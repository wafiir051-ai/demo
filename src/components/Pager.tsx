import Link from "next/link";
export default function Pager({ base, page, pages, params }: { base: string; page: number; pages: number; params: Record<string, string | undefined> }) {
  if (pages <= 1) return null;
  const href = (p: number) => { const q = new URLSearchParams(); for (const [k, v] of Object.entries(params)) if (v) q.set(k, v); q.set("p", String(p)); return `${base}?${q}`; };
  return (<div className="pager">
    {page > 1 ? <Link className="btn sm ghost" href={href(page - 1)}>← Sebelumnya</Link> : <span />}
    <span className="muted small">Halaman {page} dari {pages}</span>
    {page < pages ? <Link className="btn sm ghost" href={href(page + 1)}>Berikutnya →</Link> : <span />}
  </div>);
}
