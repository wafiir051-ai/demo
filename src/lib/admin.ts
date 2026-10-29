import { redirect } from "next/navigation";
import { currentUser } from "./auth";

export const LEVEL: Record<string, number> = { staff: 1, admin: 2, super_admin: 3 };
export const ROLES: Record<string, string> = { super_admin: "Super Admin", admin: "Admin", staff: "Staff" };
export const PUB: Record<string, string> = { published: "Publik", draft: "Draft" };
export const MSG: Record<string, string> = { unread: "Belum dibaca", read: "Dibaca", replied: "Sudah dibalas", archived: "Diarsipkan" };
export const OK: Record<string, string> = {
  created: "Data berhasil ditambahkan.", updated: "Perubahan berhasil disimpan.", deleted: "Data berhasil dihapus.",
  msgdeleted: "Pesan dihapus.", msgstatus: "Status pesan diperbarui.", account: "Akun diperbarui.",
  settings: "Pengaturan bisnis tersimpan dan langsung tampil di website publik.",
};
export const ERR: Record<string, string> = { self: "Anda tidak dapat menghapus akun sendiri.", lastsuper: "Harus tersisa minimal satu Super Admin aktif." };

export async function need(level: number) {
  const u = await currentUser();
  if (!u) redirect("/admin/login");
  if ((LEVEL[u.role] ?? 0) < level) redirect("/admin");
  return u;
}
export const s = (f: FormData, k: string) => String(f.get(k) ?? "").trim();
export const slugify = (v: string) => v.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "") || "item";
const parts = (v: string) => Object.fromEntries(new Intl.DateTimeFormat("id-ID", { timeZone: "Asia/Jakarta", day: "2-digit", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit", hourCycle: "h23" })
  .formatToParts(new Date(v.replace(" ", "T") + "Z")).map(x => [x.type, x.value]));
export const waktu = (v: string | null) => { if (!v) return "-"; const p = parts(v); return `${p.day} ${p.month} ${p.year} ${p.hour}:${p.minute}`; };
export const waktuPendek = (v: string | null) => { if (!v) return "-"; const p = parts(v); return `${p.day} ${p.month} ${p.hour}:${p.minute}`; };
export const plain = <T,>(o: T): T => JSON.parse(JSON.stringify(o));
