import { db } from "@/lib/db";
import { FacGrid } from "@/components/Cards";
export const dynamic = "force-dynamic";
export default function Page() {
  const rows = db.prepare("select * from facilities where status='published' order by sort_order").all() as any[];
  return (<><div className="pagehead"><div className="wrap"><p className="eyebrow">Kenyamanan</p><h1>Fasilitas</h1></div></div><section className="sec wrap"><FacGrid rows={rows} /></section></>);
}
