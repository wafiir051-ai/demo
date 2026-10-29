import { readMedia } from "@/lib/uploads";
export async function GET(_: Request, { params }: { params: Promise<{ path: string[] }> }) {
  const f = await readMedia((await params).path);
  if (!f) return new Response("Not found", { status: 404 });
  return new Response(new Uint8Array(f.buf), { headers: { "Content-Type": f.type, "Cache-Control": "public, max-age=86400" } });
}
