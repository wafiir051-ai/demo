import { ERR, OK } from "@/lib/admin";
export default function Flash({ ok, err }: { ok?: string; err?: string }) {
  return <>{ok && OK[ok] && <p className="alert ok">{OK[ok]}</p>}{err && ERR[err] && <p className="alert err">{ERR[err]}</p>}</>;
}
