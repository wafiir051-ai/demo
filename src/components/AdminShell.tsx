"use client";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState } from "react";
type Item = { href: string; label: string };
export default function AdminShell({ name, user, role, menu, logout, children }: { name: string; user: string; role: string; menu: Item[]; logout: () => Promise<void>; children: React.ReactNode }) {
  const path = usePathname();
  const [open, setOpen] = useState(false);
  const on = (h: string) => (h === "/admin" ? path === "/admin" : path.startsWith(h));
  const crumb = menu.find(m => on(m.href))?.label ?? "Dashboard";
  return (
    <div className="shell">
      <aside className={`side${open ? " open" : ""}`}>
        <div className="brand">{name}<small>Admin Dashboard</small></div>
        <nav aria-label="Menu admin" onClick={() => setOpen(false)}>
          {menu.map(m => <Link key={m.href} href={m.href} className={on(m.href) ? "on" : ""}>{m.label}</Link>)}
        </nav>
        <a className="viewsite" href="/" target="_blank" rel="noopener">Lihat website ↗</a>
      </aside>
      <div className="main">
        <header className="top">
          <button className="burger" type="button" aria-label="Buka menu" onClick={() => setOpen(!open)}>☰</button>
          <nav className="crumb" aria-label="Breadcrumb"><Link href="/admin">Admin</Link> / <span>{crumb}</span></nav>
          <form className="hsearch" method="get" action="/admin/reservasi" role="search">
            <input type="search" name="q" placeholder="Cari reservasi (kode/nama/telepon)" aria-label="Cari reservasi" />
          </form>
          <div className="who"><span>{user} · {role}</span><form action={logout}><button className="btn sm ghost">Keluar</button></form></div>
        </header>
        <main className="content">{children}</main>
      </div>
    </div>
  );
}
