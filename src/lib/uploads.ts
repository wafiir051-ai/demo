import fs from "node:fs/promises";
import path from "node:path";
import { randomBytes } from "node:crypto";

const ROOT = (process.env.VERCEL ? "/tmp/uploads" : path.join(process.cwd(), "uploads"));
export const EXT_MIME: Record<string, string> = { jpg: "image/jpeg", jpeg: "image/jpeg", png: "image/png", webp: "image/webp", ico: "image/x-icon" };
const MIME_EXT: Record<string, string> = { "image/jpeg": "jpg", "image/png": "png", "image/webp": "webp", "image/x-icon": "ico", "image/vnd.microsoft.icon": "ico" };
export const IMG = ["jpg", "png", "webp"];

export function checkImage(file: File, name: string, mimes = IMG, maxKb = 3072): string | null {
  const ext = MIME_EXT[file.type];
  if (!ext || !mimes.includes(ext)) return `${name} harus berupa gambar (${mimes.join(", ")}).`;
  if (file.size > maxKb * 1024) return `${name} maksimal ${Math.round(maxKb / 1024)} MB.`;
  return null;
}
export async function saveImage(file: File, folder: string): Promise<string> {
  const rel = `${folder}/${randomBytes(12).toString("hex")}.${MIME_EXT[file.type] ?? "jpg"}`;
  await fs.mkdir(path.join(ROOT, folder), { recursive: true });
  await fs.writeFile(path.join(ROOT, rel), Buffer.from(await file.arrayBuffer()));
  return rel;
}
export async function deleteMedia(rel?: string | null) {
  if (!rel) return;
  const p = path.resolve(ROOT, rel);
  if (p.startsWith(ROOT + path.sep)) await fs.unlink(p).catch(() => {});
}
export async function readMedia(parts: string[]) {
  const p = path.resolve(ROOT, ...parts);
  if (!p.startsWith(ROOT + path.sep)) return null;
  try { return { buf: await fs.readFile(p), type: EXT_MIME[path.extname(p).slice(1).toLowerCase()] ?? "application/octet-stream" }; } catch { return null; }
}
