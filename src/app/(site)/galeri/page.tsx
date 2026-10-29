import { db } from "@/lib/db";
import { Gallery } from "@/components/Cards";
export const dynamic = "force-dynamic";
export default function Page() {
  const rows = db.prepare("select * from galleries where status='published' order by sort_order").all() as any[];
  return (<><div className="pagehead"><div className="wrap"><p className="eyebrow">Suasana</p><h1>Galeri</h1></div></div><section className="sec wrap"><Gallery rows={rows} /></section></>);
}
