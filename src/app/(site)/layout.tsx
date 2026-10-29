import "../site.css";
import Link from "next/link";
import Nav from "@/components/Nav";
import { db } from "@/lib/db";
import { getBiz, waUrl } from "@/lib/site";
export const dynamic = "force-dynamic";
export default function SiteLayout({ children }: { children: React.ReactNode }) {
  const b = getBiz();
  const pages = db.prepare("select title,slug from pages where status='published' and slug!='tentang'").all() as any[];
  const wa = waUrl(b);
  return (<>
    {b.is_demo ? <div className="demo">Mode demo: seluruh data di situs ini adalah contoh dan belum diverifikasi.</div> : null}
    <Nav name={b.business_name} />
    <main id="main">{children}</main>
    <footer className="foot">
      <div className="wrap fgrid">
        <div><strong className="flogo">{b.business_name}</strong><p>{b.tagline}</p></div>
        <div><h3>Kontak</h3><p>{b.address}</p><p>Telp: {b.contact_phone}</p><p><a href={`mailto:${b.contact_email}`}>{b.contact_email}</a></p>
          {wa && <p><a href={wa} target="_blank" rel="noopener">WhatsApp</a></p>}</div>
        <div><h3>Jelajahi</h3><p><Link href="/akomodasi">Akomodasi</Link></p><p><Link href="/paket">Paket &amp; Acara</Link></p><p><Link href="/galeri">Galeri</Link></p>
          {pages.map(p => <p key={p.slug}><Link href={`/tentang?p=${p.slug}`}>{p.title}</Link></p>)}</div>
        <div><h3>Ikuti kami</h3>{b.instagram_url && <p><a href={b.instagram_url} target="_blank" rel="noopener">Instagram</a></p>}{b.maps_url && <p><a href={b.maps_url} target="_blank" rel="noopener">Lihat di peta</a></p>}</div>
      </div>
      <div className="wrap credit">© {new Date().getFullYear()} {b.business_name} · Website oleh <a href="https://www.godingofficial.com/" target="_blank" rel="noopener">Goding</a> — <em>Build Your Digital Presence with Goding.</em> · <a href="https://instagram.com/goding.id" target="_blank" rel="noopener">@goding.id</a></div>
    </footer>
  </>);
}
