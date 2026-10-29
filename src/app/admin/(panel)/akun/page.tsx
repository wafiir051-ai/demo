import { need, plain } from "@/lib/admin";
import { ACCOUNT_FIELDS } from "@/lib/resources";
import { saveAkun } from "../../actions";
import ResForm from "@/components/ResForm";
import Flash from "@/components/Flash";
export const dynamic = "force-dynamic";
export default async function Akun({ searchParams }: { searchParams: Promise<{ ok?: string }> }) {
  const u = await need(1);
  const { ok } = await searchParams;
  return (<>
    <div className="page-head"><h1>Pengaturan Akun</h1></div>
    <Flash ok={ok} />
    <ResForm action={saveAkun} fields={ACCOUNT_FIELDS} row={plain({ id: u.id, name: u.name, email: u.email })} cancel="/admin" />
  </>);
}
