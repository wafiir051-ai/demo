import { db } from "@/lib/db";
import { need, plain } from "@/lib/admin";
import { SETTINGS_FIELDS } from "@/lib/resources";
import { saveBiz } from "../../actions";
import ResForm from "@/components/ResForm";
import Flash from "@/components/Flash";
export const dynamic = "force-dynamic";
export default async function Biz({ searchParams }: { searchParams: Promise<{ ok?: string }> }) {
  await need(2);
  const { ok } = await searchParams;
  const b = (db.prepare("select * from business_settings order by id limit 1").get() ?? {}) as any;
  const row = plain({ ...b, id: b.id ?? 1, logo: b.logo_path, favicon: b.favicon_path, hero_image: b.hero_image_path });
  return (<>
    <div className="page-head"><h1>Pengaturan Bisnis</h1></div>
    <Flash ok={ok} />
    <ResForm action={saveBiz} fields={SETTINGS_FIELDS} row={row} cancel="/admin" submit="Simpan pengaturan" />
  </>);
}
