import { cookies } from "next/headers";
import { createHmac } from "node:crypto";
import { db, hash } from "./db";

const SECRET = process.env.SESSION_SECRET ?? "dev-secret-ganti-di-produksi";
const sign = (v: string) => createHmac("sha256", SECRET).update(v).digest("hex");

export type User = { id: number; name: string; email: string; role: string };

export async function login(email: string, pw: string) {
  const u = db.prepare("select * from users where email=?").get(email) as any;
  if (!u || u.is_active === 0 || u.password !== hash(pw)) return false;
  (await cookies()).set("sid", `${u.id}.${sign(String(u.id))}`, { httpOnly: true, sameSite: "lax", path: "/" });
  return true;
}
export async function currentUser(): Promise<User | null> {
  const v = (await cookies()).get("sid")?.value;
  if (!v) return null;
  const [id, sig] = v.split(".");
  if (sig !== sign(id)) return null;
  const u = db.prepare("select id,name,email,role,is_active from users where id=?").get(Number(id)) as any;
  if (!u || u.is_active === 0) return null;
  return { id: u.id, name: u.name, email: u.email, role: u.role };
}
export async function logout() { (await cookies()).delete("sid"); }
