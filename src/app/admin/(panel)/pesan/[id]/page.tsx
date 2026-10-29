import { Fragment } from "react";
import { notFound } from "next/navigation";
import { db } from "@/lib/db";
import { MSG, need, waktu } from "@/lib/admin";
import { setMsgStatus } from "../../../actions";
import Flash from "@/components/Flash";
export const dynamic = "force-dynamic";
export default async function Show({ params, searchParams }: { params: Promise<{ id: string }>; searchParams: Promise<{ ok?: string }> }) {
  await need(1);
  const { id } = await params;
  const { ok } = await searchParams;
  const m = db.prepare("select * from contact_messages where id=?").get(Number(id)) as any;
  if (!m) notFound();
  if (m.status === "unread") { db.prepare("update contact_messages set status='read' where id=?").run(m.id); m.status = "read"; }
  return (<>
    <div className="page-head"><h1>{m.subject || "Pesan dari " + m.name}</h1></div>
    <Flash ok={ok} />
    <section className="card">
      <dl className="dl"><dt>Nama</dt><dd>{m.name}</dd><dt>Email</dt><dd>{m.email || "—"}</dd><dt>Telepon</dt><dd>{m.phone || "—"}</dd><dt>Diterima</dt><dd>{waktu(m.created_at)}</dd></dl>
      <p className="msgbody">{String(m.message ?? "").split(/\r?\n/).map((l, i) => <Fragment key={i}>{i > 0 && <br />}{l}</Fragment>)}</p>
      <form className="filters" action={setMsgStatus}><input type="hidden" name="id" value={m.id} />
        <select name="status" aria-label="Status" defaultValue={m.status}>{Object.entries(MSG).map(([k, v]) => <option key={k} value={k}>{v}</option>)}</select>
        <button className="btn sm">Ubah status</button></form>
    </section>
  </>);
}
