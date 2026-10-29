import { notFound } from "next/navigation";
import { RES } from "@/lib/resources";
import { need } from "@/lib/admin";
import ResFormPage from "@/components/ResFormPage";
export const dynamic = "force-dynamic";
export default async function Edit({ params }: { params: Promise<{ res: string; id: string }> }) {
  const { res, id } = await params;
  const R = RES[res];
  if (!R || !Number(id)) notFound();
  await need(R.level);
  return <ResFormPage R={R} id={Number(id)} />;
}
