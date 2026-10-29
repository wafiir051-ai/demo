import { db } from "@/lib/db";
import { PkgCard } from "@/components/Cards";
export const dynamic = "force-dynamic";
export default function Page() {
  const rows = db.prepare("select * from packages where status='published'").all() as any[];
  const items = (id: number) => db.prepare("select * from package_items where package_id=? order by sort_order").all(id) as any[];
  return (<><div className="pagehead"><div className="wrap"><p className="eyebrow">Acara & penawaran</p><h1>Paket & Acara</h1></div></div><section className="sec wrap"><div className="pkgrid">{rows.map(p => <PkgCard key={p.id} p={p} items={items(p.id)} />)}</div></section></>);
}
