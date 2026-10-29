import Link from "next/link";
import { getBiz, waUrl } from "@/lib/site";
export const dynamic = "force-dynamic";
export default function Page() {
  const b = getBiz(); const wa = waUrl(b);
  return (<><div className="pagehead"><div className="wrap"><p className="eyebrow">Hubungi kami</p><h1>Kontak</h1></div></div>
    <section className="sec wrap narrow"><p>{b.address}</p><p>Telepon: {b.contact_phone}</p><p>Email: <a href={`mailto:${b.contact_email}`}>{b.contact_email}</a></p>
      <p><a className="btn ghost" href={b.maps_url} target="_blank" rel="noopener">Buka peta</a> {wa && <a className="btn wa" href={wa} target="_blank" rel="noopener">Chat WhatsApp</a>} <Link className="btn" href="/reservasi">Reservasi</Link></p></section></>);
}
