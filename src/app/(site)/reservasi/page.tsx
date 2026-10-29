import { db } from "@/lib/db";
import Form from "./form";
export const dynamic = "force-dynamic";
export default async function Page({ searchParams }: { searchParams: Promise<{ acc?: string }> }) {
  const { acc } = await searchParams;
  const rooms = db.prepare("select id,name from accommodations where status='published'").all() as any[];
  return <div><h1 className="mb-4 text-2xl font-bold">Reservasi</h1><Form rooms={rooms} acc={acc} /></div>;
}
