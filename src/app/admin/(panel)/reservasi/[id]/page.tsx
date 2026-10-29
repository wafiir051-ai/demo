import Link from "next/link";
import { notFound } from "next/navigation";
import { db } from "@/lib/db";
import { setStatus } from "@/app/actions";
import { STATUS } from "@/lib/status";
export const dynamic = "force-dynamic";
export default async function Show({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const r = db.prepare("select r.*, a.name acc from reservations r left join accommodations a on a.id=r.accommodation_id where r.id=?").get(Number(id)) as any;
  if (!r) notFound();
  return (<>
    <div className="page-head"><h1>Reservasi {r.code}</h1><Link className="btn ghost" href="/admin/reservasi">← Kembali</Link></div>
    <div className="grid2">
      <section className="card"><h2>Data tamu</h2><dl className="dl">
        <dt>Nama</dt><dd>{r.guest_name}</dd><dt>Telepon</dt><dd>{r.phone}</dd><dt>Email</dt><dd>{r.email || "-"}</dd>
        <dt>Akomodasi</dt><dd>{r.acc ?? "-"}</dd><dt>Check-in</dt><dd>{r.check_in}</dd><dt>Check-out</dt><dd>{r.check_out}</dd>
        <dt>Jumlah tamu</dt><dd>{r.guest_count}</dd><dt>Permintaan</dt><dd>{r.notes || "-"}</dd></dl></section>
      <section className="card"><h2>Status</h2><p><span className={`badge s-${r.status}`}>{STATUS[r.status]}</span></p>
        <form className="form" action={setStatus}><input type="hidden" name="id" value={r.id} />
          <label>Ubah status<select name="status" defaultValue={r.status}>{Object.entries(STATUS).map(([k, v]) => <option key={k} value={k}>{v}</option>)}</select></label>
          <label>Catatan admin<textarea name="admin_notes" rows={3} defaultValue={r.admin_notes ?? ""} /></label>
          <div className="formacts"><button className="btn">Simpan</button></div></form></section>
    </div>
  </>);
}
