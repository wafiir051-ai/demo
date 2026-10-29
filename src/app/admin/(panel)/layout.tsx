import { redirect } from "next/navigation";
import AdminShell from "@/components/AdminShell";
import { currentUser } from "@/lib/auth";
import { getBiz } from "@/lib/site";
import { doLogout } from "@/app/actions";
import { LEVEL, ROLES } from "@/lib/admin";
export const dynamic = "force-dynamic";
const ALL = [
  ["/admin", "Dashboard", 1], ["/admin/reservasi", "Reservasi", 1], ["/admin/pesan", "Pesan Masuk", 1],
  ["/admin/akomodasi", "Akomodasi", 2], ["/admin/fasilitas", "Fasilitas", 2], ["/admin/galeri", "Galeri", 2],
  ["/admin/paket", "Paket dan Acara", 2], ["/admin/halaman", "Halaman Website", 2], ["/admin/pengaturan", "Pengaturan Bisnis", 2],
  ["/admin/pengguna", "Pengguna dan Role", 3], ["/admin/log", "Activity Log", 2], ["/admin/akun", "Pengaturan Akun", 1],
] as const;
export default async function Panel({ children }: { children: React.ReactNode }) {
  const u = await currentUser();
  if (!u) redirect("/admin/login");
  const lv = LEVEL[u.role] ?? 0;
  const menu = ALL.filter(m => lv >= m[2]).map(([href, label]) => ({ href, label }));
  return <AdminShell name={getBiz().business_name} user={u.name} role={ROLES[u.role] ?? u.role} menu={menu} logout={doLogout}>{children}</AdminShell>;
}
