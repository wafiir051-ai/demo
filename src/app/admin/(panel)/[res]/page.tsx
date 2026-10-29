import Link from "next/link";
import { notFound } from "next/navigation";
import { db, rupiah } from "@/lib/db";
import { RES } from "@/lib/resources";
import { need, PUB, waktu } from "@/lib/admin";
import { deleteRes } from "../../actions";
import ConfirmButton from "@/components/ConfirmButton";
import Flash from "@/components/Flash";
import Pager from "@/components/Pager";
export const dynamic = "force-dynamic";
const PER = 10;
const NOIMG = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='80' height='60'><rect width='80' height='60' fill='%23e5e5e0'/></svg>";
export default async function List({ params, searchParams }: { params: Promise<{ res: string }>; searchParams: Promise<{ q?: string; status?: string; p?: string; ok?: string; err?: string }> }) {
  const { res } = await params;
  const R = RES[res];
  if (!R) notFound();
  await need(R.level);
  const { q = "", status = "", p, ok, err } = await searchParams;
  const where: string[] = [], args: string[] = [];
  if (q.trim()) { where.push("(" + R.search.map(c => `${c} like ?`).join(" or ") + ")"); R.search.forEach(() => args.push(`%${q.trim()}%`)); }
  if (R.status && (status === "published" || status === "draft")) { where.push("status=?"); args.push(status); }
  const w = where.length ? "where " + where.join(" and ") : "";
  const total = (db.prepare(`select count(*) c from ${R.table} ${w}`).get(...args) as any).c as number;
  const pages = Math.max(1, Math.ceil(total / PER)), page = Math.min(pages, Math.max(1, Number(p) || 1));
  const rows = db.prepare(`select * from ${R.table} ${w} order by ${R.order[0]} ${R.order[1]}, id ${R.order[1]} limit ? offset ?`).all(...args, PER, (page - 1) * PER) as any[];
  return (<>
    <div className="page-head"><h1>{R.title}</h1><Link className="btn" href={`/admin/${R.key}/tambah`}>+ Tambah</Link></div>
    <Flash ok={ok} err={err} />
    <form className="filters" method="get">
      <input type="search" name="q" defaultValue={q} placeholder="Cari..." aria-label="Cari" />
      {R.status && <select name="status" defaultValue={status} aria-label="Filter status"><option value="">Semua status</option><option value="published">Dipublikasikan</option><option value="draft">Draft</option></select>}
      <button className="btn sm">Terapkan</button>
    </form>
    <div className="table-wrap"><table className="tbl"><thead><tr>{R.columns.map(c => <th key={c.key}>{c.label}</th>)}<th>Aksi</th></tr></thead>
      <tbody>{rows.map(r => <tr key={r.id}>
        {R.columns.map(c => { const v = r[c.key]; return <td key={c.key} data-label={c.label}>
          {c.type === "image" ? <img className="thumb" src={v ? `/uploads/${v}` : NOIMG} alt="" loading="lazy" />
            : c.type === "money" ? rupiah(v)
            : c.type === "status" ? <span className={`badge s-${v}`}>{PUB[v] ?? v}</span>
            : c.type === "bool" ? <span className={`badge ${v ? "s-published" : "s-draft"}`}>{v ? "Ya" : "Tidak"}</span>
            : c.type === "datetime" ? waktu(v)
            : c.map ? c.map[v] ?? v : String(v ?? "").length > 60 ? String(v).slice(0, 60) + "…" : v ?? ""}
        </td>; })}
        <td data-label="Aksi" className="acts">
          <Link className="btn sm ghost" href={`/admin/${R.key}/${r.id}/edit`}>Edit</Link>
          <form action={deleteRes}><input type="hidden" name="res" value={R.key} /><input type="hidden" name="id" value={r.id} />
            <ConfirmButton className="btn sm danger" text="Hapus data ini? Tindakan tidak bisa dibatalkan.">Hapus</ConfirmButton></form>
        </td></tr>)}
        {!rows.length && <tr><td colSpan={R.columns.length + 1} className="empty">Belum ada data.</td></tr>}</tbody></table></div>
    <Pager base={`/admin/${R.key}`} page={page} pages={pages} params={{ q, status }} />
  </>);
}
