"use client";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState } from "react";
const links = [["/", "Beranda"], ["/akomodasi", "Akomodasi"], ["/fasilitas", "Fasilitas"], ["/paket", "Paket & Acara"], ["/galeri", "Galeri"], ["/tentang", "Tentang"], ["/kontak", "Kontak"]];
export default function Nav({ name }: { name: string }) {
  const path = usePathname();
  const [open, setOpen] = useState(false);
  const on = (h: string) => ((h === "/" ? path === "/" : path.startsWith(h)) ? "on" : "");
  return (
    <header className={`nav${open ? " open" : ""}`}>
      <div className="wrap navin">
        <Link className="logo" href="/">{name}</Link>
        <button className="burger" type="button" aria-label="Buka menu" aria-expanded={open} onClick={() => setOpen(!open)}>☰</button>
        <nav className="links" aria-label="Menu utama" onClick={() => setOpen(false)}>
          {links.map(([h, t]) => <Link key={h} href={h} className={on(h)}>{t}</Link>)}
          <Link className="btn" href="/reservasi">Reservasi</Link>
        </nav>
      </div>
    </header>
  );
}
