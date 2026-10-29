"use server";
import { redirect, notFound } from "next/navigation";
import { revalidatePath } from "next/cache";
import { db, hash } from "@/lib/db";
import { logActivity } from "@/lib/extra";
import { ACCOUNT_FIELDS, RES, SETTINGS_FIELDS } from "@/lib/resources";
import { MSG, need, s, slugify } from "@/lib/admin";
import { validate } from "@/lib/validate";
import { checkImage, deleteMedia, saveImage } from "@/lib/uploads";

type St = { errors: string[]; values: Record<string, any> } | null;
const fail = (errors: string[], values: Record<string, any>): St => ({ errors, values });
const nowUtc = () => new Date().toISOString().slice(0, 19).replace("T", " ");
const otherSuper = (id: number) => !!db.prepare("select 1 from users where role='super_admin' and is_active=1 and id<>?").get(id);

export async function saveRes(_: St, f: FormData): Promise<St> {
  const R = RES[s(f, "res")];
  if (!R) redirect("/admin");
  const u = await need(R.level);
  const id = Number(s(f, "id")) || 0, isNew = !id;
  const old = isNew ? null : (db.prepare(`select * from ${R.table} where id=?`).get(id) as any);
  if (!isNew && !old) notFound();

  const v = validate(f, R.fields, { table: R.table, id, isNew });
  const errors = [...v.errors], values = { ...v.values };
  let facIds: number[] = [], removeIds: number[] = [], gallery: File[] = [], itemsText = "";

  if (R.key === "akomodasi") {
    facIds = f.getAll("facility_ids").map(Number).filter(Number.isInteger);
    values.facility_ids = facIds;
    if (facIds.some(x => !db.prepare("select 1 from facilities where id=?").get(x))) errors.push("Fasilitas tidak valid.");
    removeIds = f.getAll("remove_images").map(Number).filter(Number.isInteger);
    gallery = f.getAll("gallery_images").filter((x): x is File => x instanceof File && x.size > 0);
    if (gallery.length > 10) errors.push("Galeri foto tambahan maksimal 10 file.");
    for (const g of gallery) { const e = checkImage(g, "Galeri foto tambahan"); if (e) { errors.push(e); break; } }
  }
  if (R.key === "paket") {
    itemsText = String(f.get("items_text") ?? "").trim();
    values.items_text = itemsText;
    if (itemsText.length > 3000) errors.push("Daftar isi paket maksimal 3000 karakter.");
  }
  if (R.key === "pengguna" && old) {
    const role = String(v.data.role), active = v.data.is_active === 1;
    if (old.id === u.id && (role !== old.role || !active)) errors.push("Anda tidak dapat mengubah role atau menonaktifkan akun Anda sendiri.");
    else if (old.role === "super_admin" && (role !== "super_admin" || !active) && !otherSuper(old.id)) errors.push("Harus tersisa minimal satu Super Admin aktif.");
  }
  if (errors.length) return fail(errors, values);

  const row: Record<string, any> = {};
  for (const fl of R.fields) {
    const n = fl.name;
    if (fl.type === "slug") continue;
    if (fl.type === "image") {
      const file = v.files[n];
      if (file) { await deleteMedia(old?.[n]); row[n] = await saveImage(file, R.folder); }
      continue;
    }
    if (fl.type === "password") { if (v.data[n]) row[n] = hash(String(v.data[n])); continue; }
    row[n] = v.data[n];
  }
  if (R.slugFrom) {
    let sl = (v.data.slug as string | null) || null;
    if (!sl && !old?.slug) {
      const base = slugify(String(v.data[R.slugFrom] ?? "")); let i = 2; sl = base;
      while (db.prepare(`select 1 from ${R.table} where slug=? and id<>?`).get(sl, id)) sl = `${base}-${i++}`;
    }
    if (sl) row.slug = sl;
  }
  if (R.table === "pages") row.updated_at = nowUtc();

  const cols = Object.keys(row), args = Object.values(row);
  let rid = id;
  if (isNew) rid = Number(db.prepare(`insert into ${R.table}(${cols.join(",")}) values(${cols.map(() => "?").join(",")})`).run(...args).lastInsertRowid);
  else db.prepare(`update ${R.table} set ${cols.map(c => c + "=?").join(",")} where id=?`).run(...args, id);

  if (R.key === "akomodasi") {
    db.prepare("delete from accommodation_facility where accommodation_id=?").run(rid);
    const ins = db.prepare("insert into accommodation_facility(accommodation_id,facility_id) values(?,?)");
    for (const x of new Set(facIds)) ins.run(rid, x);
    for (const img of db.prepare("select * from accommodation_images where accommodation_id=?").all(rid) as any[])
      if (removeIds.includes(img.id)) { await deleteMedia(img.path); db.prepare("delete from accommodation_images where id=?").run(img.id); }
    for (const g of gallery) db.prepare("insert into accommodation_images(accommodation_id,path) values(?,?)").run(rid, await saveImage(g, "accommodations"));
  }
  if (R.key === "paket") {
    db.prepare("delete from package_items where package_id=?").run(rid);
    itemsText.split(/\r?\n/).map(x => x.trim()).filter(Boolean).forEach((x, i) => db.prepare("insert into package_items(package_id,label,sort_order) values(?,?,?)").run(rid, x.slice(0, 190), i));
  }
  const label = row[R.label] ?? old?.[R.label] ?? `#${rid}`;
  logActivity(db, u.id, isNew ? "create" : "update", `${isNew ? "Membuat" : "Mengubah"} ${R.title.toLowerCase()}: ${label}`, R.entity, rid);
  revalidatePath("/admin");
  redirect(`/admin/${R.key}?ok=${isNew ? "created" : "updated"}`);
}

export async function deleteRes(f: FormData) {
  const R = RES[s(f, "res")];
  if (!R) redirect("/admin");
  const u = await need(R.level);
  const id = Number(s(f, "id"));
  const item = db.prepare(`select * from ${R.table} where id=?`).get(id) as any;
  if (!item) redirect(`/admin/${R.key}`);
  if (R.key === "pengguna") {
    if (item.id === u.id) redirect("/admin/pengguna?err=self");
    if (item.role === "super_admin" && !otherSuper(item.id)) redirect("/admin/pengguna?err=lastsuper");
  }
  for (const fl of R.fields) if (fl.type === "image") await deleteMedia(item[fl.name]);
  if (R.key === "akomodasi") {
    for (const img of db.prepare("select * from accommodation_images where accommodation_id=?").all(id) as any[]) await deleteMedia(img.path);
    db.prepare("delete from accommodation_images where accommodation_id=?").run(id);
    db.prepare("delete from accommodation_facility where accommodation_id=?").run(id);
  }
  if (R.key === "fasilitas") db.prepare("delete from accommodation_facility where facility_id=?").run(id);
  if (R.key === "paket") db.prepare("delete from package_items where package_id=?").run(id);
  logActivity(db, u.id, "delete", `Menghapus ${R.title.toLowerCase()}: ${item[R.label] ?? "#" + id}`, R.entity, id);
  db.prepare(`delete from ${R.table} where id=?`).run(id);
  revalidatePath("/admin");
  redirect(`/admin/${R.key}?ok=deleted`);
}

export async function setMsgStatus(f: FormData) {
  const u = await need(1);
  const id = Number(s(f, "id")), st = s(f, "status");
  if (!(st in MSG)) redirect(`/admin/pesan/${id}`);
  db.prepare("update contact_messages set status=? where id=?").run(st, id);
  logActivity(db, u.id, "update", `Mengubah status pesan #${id} menjadi ${st}`, "ContactMessage", id);
  revalidatePath("/admin");
  redirect(`/admin/pesan/${id}?ok=msgstatus`);
}

export async function deleteMsg(f: FormData) {
  const u = await need(1);
  const id = Number(s(f, "id"));
  logActivity(db, u.id, "delete", `Menghapus pesan #${id}`, "ContactMessage", id);
  db.prepare("delete from contact_messages where id=?").run(id);
  revalidatePath("/admin");
  redirect("/admin/pesan?ok=msgdeleted");
}

export async function saveBiz(_: St, f: FormData): Promise<St> {
  const u = await need(2);
  const v = validate(f, SETTINGS_FIELDS);
  if (v.errors.length) return fail(v.errors, v.values);
  const b = (db.prepare("select * from business_settings order by id limit 1").get() ?? {}) as any;
  const row: Record<string, any> = {};
  for (const fl of SETTINGS_FIELDS) {
    if (fl.type === "image") {
      const file = v.files[fl.name];
      if (file) { await deleteMedia(b[fl.col!]); row[fl.col!] = await saveImage(file, "branding"); }
    } else row[fl.name] = v.data[fl.name];
  }
  const cols = Object.keys(row);
  db.prepare(`update business_settings set ${cols.map(c => c + "=?").join(",")} where id=?`).run(...Object.values(row), b.id);
  logActivity(db, u.id, "update", "Mengubah pengaturan bisnis", "BusinessSetting", b.id);
  revalidatePath("/", "layout");
  redirect("/admin/pengaturan?ok=settings");
}

export async function saveAkun(_: St, f: FormData): Promise<St> {
  const u = await need(1);
  const v = validate(f, ACCOUNT_FIELDS);
  const errors = [...v.errors];
  const cur = String(f.get("current_password") ?? ""), pw = String(f.get("password") ?? "");
  if (pw && !cur) errors.push("Password saat ini wajib diisi bila mengganti password.");
  if (cur) { const r = db.prepare("select password from users where id=?").get(u.id) as any; if (r.password !== hash(cur)) errors.push("Password saat ini salah."); }
  if (pw && pw !== String(f.get("password_confirmation") ?? "")) errors.push("Konfirmasi password tidak sama.");
  if (errors.length) return fail(errors, v.values);
  db.prepare("update users set name=? where id=?").run(v.data.name, u.id);
  if (pw) db.prepare("update users set password=? where id=?").run(hash(pw), u.id);
  logActivity(db, u.id, "update", "Memperbarui akun sendiri" + (pw ? " (password diganti)" : ""), "User", u.id);
  revalidatePath("/admin");
  redirect("/admin/akun?ok=account");
}
