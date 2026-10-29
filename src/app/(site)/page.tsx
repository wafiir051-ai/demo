import Link from "next/link";
import { db } from "@/lib/db";
import { getBiz, waUrl } from "@/lib/site";
import { img } from "@/lib/media";
import { AccCard, PkgCard, FacGrid, Gallery } from "@/components/Cards";
export const dynamic = "force-dynamic";
const all = (q: string) => db.prepare(q).all() as any[];
export default function Home() {
  const b = getBiz();
  const accs = all("select * from accommodations where status='published' order by sort_order limit 6");
  const facs = all("select * from facilities where status='published' order by sort_order");
  const pkgs = all("select * from packages where status='published' limit 3");
  const gal = all("select * from galleries where status='published' order by sort_order limit 6");
  const items = (id: number) => all(`select * from package_items where package_id=${id} order by sort_order`);
  const wa = waUrl(b, "Halo, saya ingin bertanya tentang " + b.business_name);
  return (<>
    <section className="hero" style={{ backgroundImage: `url("${img((b as any).hero_image_path, b.business_name)}")` }}>
      <div className="wrap heroin">
        <p className="eyebrow">{b.business_type}</p><h1>{b.tagline || b.business_name}</h1>
        <p className="lead">{b.description.slice(0, 200)}</p>
        <p><Link className="btn lg" href="/reservasi">Ajukan Reservasi</Link> <Link className="btn lg light" href="/akomodasi">Lihat Akomodasi</Link></p>
      </div>
    </section>
    <section className="sec wrap intro">
      <div><p className="eyebrow">Selamat datang</p><h2>{b.business_name}</h2></div>
      <div>{b.description.split("\n\n").map((t, i) => <p key={i}>{t}</p>)}<p><Link className="more" href="/tentang">Selengkapnya tentang kami →</Link></p></div>
    </section>
    <section className="sec alt"><div className="wrap"><div className="sechead"><p className="eyebrow">Menginap</p><h2>Akomodasi</h2></div>
      {accs.length ? <div className="accgrid">{accs.map(a => <AccCard key={a.id} a={a} />)}</div> : <p className="empty">Belum ada akomodasi yang dipublikasikan.</p>}</div></section>
    <section className="sec wrap"><div className="sechead"><p className="eyebrow">Kenyamanan</p><h2>Fasilitas</h2></div>
      {facs.length ? <FacGrid rows={facs} /> : <p className="empty">Belum ada fasilitas.</p>}</section>
    {pkgs.length > 0 && <section className="sec alt"><div className="wrap"><div className="sechead"><p className="eyebrow">Acara &amp; penawaran</p><h2>Paket Unggulan</h2></div>
      <div className="pkgrid">{pkgs.map(p => <PkgCard key={p.id} p={p} items={items(p.id)} />)}</div>
      <p className="center"><Link className="more" href="/paket">Semua paket →</Link></p></div></section>}
    {gal.length > 0 && <section className="sec wrap"><div className="sechead"><p className="eyebrow">Suasana</p><h2>Galeri</h2></div><Gallery rows={gal} />
      <p className="center"><Link className="more" href="/galeri">Lihat semua foto →</Link></p></section>}
    <section className="sec alt"><div className="wrap two contactsec">
      <div><p className="eyebrow">Lokasi &amp; kontak</p><h2>Kunjungi kami</h2><p>{b.address}</p><p>Telepon: {b.contact_phone}</p><p>Email: <a href={`mailto:${b.contact_email}`}>{b.contact_email}</a></p>
        <p><a className="btn ghost" href={b.maps_url} target="_blank" rel="noopener">Buka peta</a> {wa && <a className="btn wa" href={wa} target="_blank" rel="noopener">Chat WhatsApp</a>}</p></div>
      <div className="cta"><h2>Siap menginap?</h2><p>Kirim permintaan reservasi dan kami akan menghubungi Anda untuk konfirmasi.</p><Link className="btn lg" href="/reservasi">Ajukan Reservasi</Link></div>
    </div></section>
  </>);
}
