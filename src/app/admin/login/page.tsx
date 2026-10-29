import { doLogin } from "@/app/actions";
import { getBiz } from "@/lib/site";
export const dynamic = "force-dynamic";
export default async function Login({ searchParams }: { searchParams: Promise<{ error?: string }> }) {
  const { error } = await searchParams;
  const b = getBiz();
  return (
    <div className="loginbody">
      <form className="logincard" action={doLogin}>
        <h1>{b.business_name}</h1>
        <p className="muted">Masuk ke dashboard admin</p>
        {error && <div className="alert err" role="alert">Email atau password salah.</div>}
        <label>Email<input type="email" name="email" required autoFocus autoComplete="username" /></label>
        <label>Password<input type="password" name="password" required autoComplete="current-password" /></label>
        <button className="btn" type="submit">Masuk</button>
        <p className="muted small">Dibuat oleh Goding — Build Your Digital Presence with Goding.</p>
      </form>
    </div>
  );
}
