"use server";
import { redirect } from "next/navigation";
import { revalidatePath } from "next/cache";
import { randomBytes } from "node:crypto";
import { db } from "@/lib/db";
import { currentUser, login, logout } from "@/lib/auth";
import { logActivity } from "@/lib/extra";

const s = (f: FormData, k: string) => String(f.get(k) ?? "").trim();

export async function reserve(_: unknown, f: FormData) {
  const name = s(f, "guest_name"), phone = s(f, "phone"), cin = s(f, "check_in"), cout = s(f, "check_out");
  const count = Number(s(f, "guest_count")), acc = Number(s(f, "accommodation_id")) || null;
  if (!name || !phone || !cin || !cout || !count) return { error: "Lengkapi semua kolom wajib." };
  if (cout <= cin) return { error: "Tanggal check-out harus setelah check-in." };
  const code = "GDG-" + new Date().toISOString().slice(2, 10).replace(/-/g, "") + "-" + randomBytes(3).toString("hex").slice(0, 5).toUpperCase();
  db.prepare("insert into reservations(code,guest_name,phone,email,accommodation_id,check_in,check_out,guest_count,notes) values(?,?,?,?,?,?,?,?,?)")
    .run(code, name, phone, s(f, "email"), acc, cin, cout, count, s(f, "notes"));
  redirect(`/reservasi/berhasil/${code}`);
}

export async function doLogin(f: FormData) {
  const ok = await login(s(f, "email"), s(f, "password"));
  redirect(ok ? "/admin" : "/admin/login?error=1");
}
export async function doLogout() { await logout(); redirect("/admin/login"); }

export async function setStatus(f: FormData) {
  const user = await currentUser();
  if (!user) redirect("/admin/login");
  const status = s(f, "status");
  if (!["pending", "confirmed", "rejected", "cancelled", "completed"].includes(status)) return;
  const id = Number(s(f, "id"));
  db.prepare("update reservations set status=? where id=?").run(status, id);
  if (f.has("admin_notes")) db.prepare("update reservations set admin_notes=? where id=?").run(s(f, "admin_notes"), id);
  logActivity(db, user.id, "update", `Reservasi #${id} diperbarui: status ${status}`);
  revalidatePath("/admin");
}
