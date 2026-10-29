import { db } from "@/lib/db";
import { AccCard } from "@/components/Cards";
export const dynamic = "force-dynamic";
export default function Page() {
  const rows = db.prepare("select * from accommodations where status='published' order by sort_order").all() as any[];
  return (<><div className="pagehead"><div className="wrap"><p className="eyebrow">Menginap</p><h1>Akomodasi</h1></div></div><section className="sec wrap"><div className="accgrid">{rows.map(a => <AccCard key={a.id} a={a} />)}</div></section></>);
}
