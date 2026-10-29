import { db } from "@/lib/db";
export const dynamic = "force-dynamic";
export default async function Page({ searchParams }: { searchParams: Promise<{ p?: string }> }) {
  const { p } = await searchParams;
  const pg = db.prepare("select * from pages where slug=? and status='published'").get(p || "tentang") as any;
  if (!pg) return <div className="sec wrap empty">Halaman tidak ditemukan.</div>;
  const blocks = String(pg.content).split("\n").filter(Boolean);
  return (<><div className="pagehead"><div className="wrap"><h1>{pg.title}</h1></div></div>
    <section className="sec wrap narrow prose">{blocks.map((l, i) => l.startsWith("## ") ? <h2 key={i}>{l.slice(3)}</h2> : l.startsWith("- ") ? <ul key={i} className="ticks"><li>{l.slice(2)}</li></ul> : <p key={i}>{l}</p>)}</section></>);
}
