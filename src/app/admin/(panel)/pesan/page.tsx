import Link from "next/link";
import { db } from "@/lib/db";
import { need, MSG, waktuPendek } from "@/lib/admin";
import { deleteMsg } from "../../actions";
import ConfirmButton from "@/components/ConfirmButton";
import Flash from "@/components/Flash";
import Pager from "@/components/Pager";
export const dynamic = "force-dynamic";
const PER = 12;
export default async function Inbox({ searchParams }: { searchParams: Promise<{ q?: string; status?: string; p?: string; ok?: string }> }) {
  await need(1);
  const { q = "", status = "", p, ok } = await searchParams;
  const like = `%${q.trim()}%`, st = status in MSG ? status : "";
  const cond = "(name like ? or subject like ? or message like ? or email like ?) and (?='' or status=?)";
  const a = [like, like, like, like, st, st];
  const total = (db.prepare(`select count(*) c from contact_messages where ${cond}`).get(...a) as any).c as number;
  const pages = Math.max(1, Math.ceil(total / PER)), page = Math.min(pages, Math.max(1, Number(p) || 1));
  const rows = db.prepare(`select * from contact_messages where ${cond} order by id desc limit ? offset ?`).all(...a, PER, (page - 1) * PER) as any[];
  const cut = (t: string) => (t.length > 50 ? t.slice(0, 50) + "..." : t);
  return (<>
    <div className="page-head"><h1>Pesan Masuk</h1></div>
    <Flash ok={ok} />
    <form className="filters" method="get">
      <input type="search" name="q" defaultValue={q} placeholder="Cari pesan..." aria-label="Cari" />
      <select name="status" defaultValue={st} aria-label="Status"><option value="">Semua status</option>{Object.entries(MSG).map(([k, v]) => <option key={k} value={k}>{v}</option>)}</select>
      <button className="btn sm">Filter</button>
    </form>
    <div className="table-wrap"><table className="tbl"><thead><tr><th>Pengirim</th><th>Subjek</th><th>Status</th><th>Diterima</th><th>Aksi</th></tr></thead>
      <tbody>{rows.map(m => <tr key={m.id}>
        <td data-label="Pengirim">{m.name}<br /><small>{m.email || m.phone}</small></td>
        <td data-label="Subjek">{m.subject || cut(m.message ?? "")}</td>
        <td data-label="Status"><span className={`badge s-${m.status}`}>{MSG[m.status]}</span></td>
        <td data-label="Diterima">{waktuPendek(m.created_at)}</td>
        <td data-label="Aksi" className="acts"><Link className="btn sm ghost" href={`/admin/pesan/${m.id}`}>Buka</Link>
          <form action={deleteMsg}><input type="hidden" name="id" value={m.id} /><ConfirmButton className="btn sm danger" text="Hapus pesan ini?">Hapus</ConfirmButton></form></td></tr>)}
        {!rows.length && <tr><td colSpan={5} className="empty">Tidak ada pesan.</td></tr>}</tbody></table></div>
    <Pager base="/admin/pesan" page={page} pages={pages} params={{ q, status: st }} />
  </>);
}
