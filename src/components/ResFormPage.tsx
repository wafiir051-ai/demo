import { notFound } from "next/navigation";
import { db } from "@/lib/db";
import { plain } from "@/lib/admin";
import type { Res } from "@/lib/resources";
import { saveRes } from "@/app/admin/actions";
import ResForm from "./ResForm";

export default function ResFormPage({ R, id }: { R: Res; id?: number }) {
  const item = id ? (db.prepare(`select * from ${R.table} where id=?`).get(id) as any) : {};
  if (!item) notFound();
  const { password: _pw, ...row } = item;
  const extras: any = {};
  if (R.key === "akomodasi") {
    extras.facilities = db.prepare("select id,name,icon from facilities order by sort_order, id").all();
    extras.selected = id ? (db.prepare("select facility_id v from accommodation_facility where accommodation_id=?").all(id) as any[]).map(x => x.v) : [];
    extras.images = id ? db.prepare("select id,path from accommodation_images where accommodation_id=? order by id").all(id) : [];
  }
  if (R.key === "paket") extras.itemsText = id ? (db.prepare("select label from package_items where package_id=? order by sort_order, id").all(id) as any[]).map(x => x.label).join("\n") : "";
  return (<>
    <div className="page-head"><h1>{id ? "Edit" : "Tambah"} {R.title}</h1></div>
    <ResForm action={saveRes} fields={R.fields} row={plain(row)} hidden={{ res: R.key, id: id ? String(id) : "" }} extras={plain(extras)} cancel={`/admin/${R.key}`} />
  </>);
}
