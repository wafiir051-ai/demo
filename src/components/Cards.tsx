import Link from "next/link";
import { rupiah } from "@/lib/db";
import { img } from "@/lib/media";
import { PACKAGE_TYPES } from "@/lib/resources";
export function AccCard({ a }: { a: any }) {
  const href = `/reservasi?acc=${a.id}`;
  return (
    <article className="acc">
      <Link href={href} className="accimg"><img src={img(a.featured_image, a.name)} alt={a.name} loading="lazy" /></Link>
      <div className="accbody">
        <h3>{a.name}</h3>
        <p className="meta">Hingga {a.capacity} tamu{a.bed_description ? ` · ${a.bed_description}` : ""}</p>
        <p>{a.description}</p>
        <div className="accfoot"><span className="price">{a.price_per_night ? rupiah(a.price_per_night) + " / malam" : "Hubungi kami"}</span>
          <Link className="btn sm" href={href}>Reservasi</Link></div>
      </div>
    </article>
  );
}
export function PkgCard({ p, items }: { p: any; items: any[] }) {
  return (
    <article className="pkg">
      <img src={img(p.featured_image, p.name)} alt={p.name} loading="lazy" />
      <div className="pkbody">
        <h3>{p.name}</h3>
        <p className="meta">{PACKAGE_TYPES[p.package_type] ?? p.package_type}{p.capacity ? ` · hingga ${p.capacity} orang` : ""}</p>
        <p>{p.description}</p>
        <ul className="ticks">{items.map(i => <li key={i.id}>{i.label}</li>)}</ul>
        <p className="price lg">{rupiah(p.price)}</p>
        {p.terms && <p className="terms">{p.terms}</p>}
        <Link className="btn" href="/reservasi">Reservasi</Link>
      </div>
    </article>
  );
}
export function FacGrid({ rows }: { rows: any[] }) {
  return <div className="facgrid">{rows.map(f => <div key={f.id} className="fac"><span className="ico" aria-hidden="true">{f.icon || "✦"}</span><h3>{f.name}</h3><p>{f.description}</p></div>)}</div>;
}
export function Gallery({ rows }: { rows: any[] }) {
  return <div className="gal">{rows.map(g => <a key={g.id} href={img(g.image_path, g.title)} target="_blank"><img src={img(g.image_path, g.title)} alt={g.alt_text || g.title} loading="lazy" /></a>)}</div>;
}
