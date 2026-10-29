import Link from "next/link";
import { db } from "@/lib/db";
import { STATUS } from "@/lib/status";
export const dynamic = "force-dynamic";
const n = (q: string) => Object.values(db.prepare(q).get() as any)[0] as number;
const tgl = (s: string) => new Date(s.replace(" ", "T") + (s.length > 10 ? "Z" : "")).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" });
export default function Dashboard() {
  const stats: [string, number][] = [
    ["Akomodasi", n("select count(*) from accommodations")], ["Paket", n("select count(*) from packages")],
    ["Total reservasi", n("select count(*) from reservations")], ["Reservasi menunggu", n("select count(*) from reservations where status='pending'")],
    ["Reservasi dikonfirmasi", n("select count(*) from reservations where status='confirmed'")], ["Pesan belum dibaca", n("select count(*) from contact_messages where status='unread'")],
  ];
  const chart = Array.from({ length: 6 }, (_, i) => {
    const d = new Date(); d.setDate(1); d.setMonth(d.getMonth() - (5 - i));
    return { label: d.toLocaleDateString("id-ID", { month: "short", year: "numeric" }), value: n(`select count(*) from reservations where substr(created_at,1,7)='${d.toISOString().slice(0, 7)}'`) };
  });
  const max = Math.max(1, ...chart.map(c => c.value));
  const latest = db.prepare("select * from reservations order by created_at desc, id desc limit 5").all() as any[];
  const logs = db.prepare("select l.*, u.name uname from activity_logs l left join users u on u.id=l.user_id order by l.id desc limit 8").all() as any[];
  return (<>
    <div className="page-head"><h1>Dashboard</h1></div>
    <div className="stats">{stats.map(([l, v]) => <div className="stat" key={l}><strong>{v}</strong><span>{l}</span></div>)}</div>
    <div className="grid2">
      <section className="card"><h2>Reservasi 6 bulan terakhir</h2>
        <div className="bars" role="img" aria-label="Grafik jumlah reservasi per bulan">
          {chart.map(b => <div className="bar" key={b.label}><span className="v">{b.value}</span><i style={{ height: `${Math.max(4, Math.round(b.value / max * 100))}%` }} /><span className="l">{b.label}</span></div>)}
        </div></section>
      <section className="card"><h2>Reservasi terbaru</h2>
        {latest.length ? latest.map(r => <Link className="row" key={r.id} href={`/admin/reservasi/${r.id}`}><span><strong>{r.guest_name}</strong><br /><small>{r.code} · {tgl(r.check_in)}</small></span><span className={`badge s-${r.status}`}>{STATUS[r.status]}</span></Link>) : <p className="muted">Belum ada reservasi.</p>}
      </section>
    </div>
    <section className="card"><h2>Aktivitas admin terbaru</h2>
      {logs.length ? logs.map(l => <p className="logline" key={l.id}><small>{tgl(l.created_at)}</small> <strong>{l.uname ?? "Sistem"}</strong> — {l.description}</p>) : <p className="muted">Belum ada aktivitas.</p>}
    </section>
  </>);
}
