import { db } from "@/lib/db";
import { need, waktu } from "@/lib/admin";
import Pager from "@/components/Pager";
export const dynamic = "force-dynamic";
const PER = 25;
export default async function Logs({ searchParams }: { searchParams: Promise<{ q?: string; p?: string }> }) {
  await need(2);
  const { q = "", p } = await searchParams;
  const like = `%${q.trim()}%`;
  const cond = "(l.description like ? or l.action like ? or l.entity_type like ?)";
  const total = (db.prepare(`select count(*) c from activity_logs l where ${cond}`).get(like, like, like) as any).c as number;
  const pages = Math.max(1, Math.ceil(total / PER)), page = Math.min(pages, Math.max(1, Number(p) || 1));
  const rows = db.prepare(`select l.*, u.name uname from activity_logs l left join users u on u.id=l.user_id where ${cond} order by l.id desc limit ? offset ?`).all(like, like, like, PER, (page - 1) * PER) as any[];
  return (<>
    <div className="page-head"><h1>Activity Log</h1></div>
    <form className="filters" method="get"><input type="search" name="q" defaultValue={q} placeholder="Cari aktivitas..." aria-label="Cari" /><button className="btn sm">Cari</button></form>
    <div className="table-wrap"><table className="tbl"><thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Keterangan</th></tr></thead>
      <tbody>{rows.map(l => <tr key={l.id}><td data-label="Waktu">{waktu(l.created_at)}</td><td data-label="Pengguna">{l.uname ?? "Sistem"}</td>
        <td data-label="Aksi"><span className="badge">{l.action}</span></td><td data-label="Keterangan">{l.description}</td></tr>)}
        {!rows.length && <tr><td colSpan={4} className="empty">Belum ada aktivitas.</td></tr>}</tbody></table></div>
    <Pager base="/admin/log" page={page} pages={pages} params={{ q }} />
  </>);
}
