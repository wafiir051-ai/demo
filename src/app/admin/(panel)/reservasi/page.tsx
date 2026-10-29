import Link from "next/link";
import { db } from "@/lib/db";
import { STATUS } from "@/lib/status";
export const dynamic = "force-dynamic";
export default async function List({ searchParams }: { searchParams: Promise<{ q?: string; status?: string }> }) {
  const { q = "", status = "" } = await searchParams;
  const like = `%${q}%`;
  const rows = db.prepare(`select r.*, a.name acc from reservations r left join accommodations a on a.id=r.accommodation_id
    where (r.code like ? or r.guest_name like ? or r.phone like ?) and (?='' or r.status=?) order by r.id desc`).all(like, like, like, status, status) as any[];
  return (<>
    <div className="page-head"><h1>Reservasi</h1></div>
    <form className="filters" method="get">
      <input type="search" name="q" defaultValue={q} placeholder="Kode / nama / telepon" />
      <select name="status" defaultValue={status}><option value="">Semua status</option>{Object.entries(STATUS).map(([k, v]) => <option key={k} value={k}>{v}</option>)}</select>
      <button className="btn sm">Filter</button>
    </form>
    <div className="table-wrap"><table className="tbl"><thead><tr><th>Kode</th><th>Tamu</th><th>Akomodasi</th><th>Check-in</th><th>Check-out</th><th>Status</th><th></th></tr></thead>
      <tbody>{rows.map(r => <tr key={r.id}>
        <td data-label="Kode">{r.code}</td><td data-label="Tamu">{r.guest_name}<br /><span className="muted small">{r.phone}</span></td><td data-label="Akomodasi">{r.acc ?? "-"}</td>
        <td data-label="Check-in">{r.check_in}</td><td data-label="Check-out">{r.check_out}</td>
        <td data-label="Status"><span className={`badge s-${r.status}`}>{STATUS[r.status]}</span></td>
        <td><Link className="btn sm ghost" href={`/admin/reservasi/${r.id}`}>Detail</Link></td></tr>)}
        {!rows.length && <tr><td className="empty" colSpan={7}>Tidak ada reservasi.</td></tr>}</tbody></table></div>
  </>);
}
