import { notFound } from "next/navigation";
import { RES } from "@/lib/resources";
import { need } from "@/lib/admin";
import ResFormPage from "@/components/ResFormPage";
export const dynamic = "force-dynamic";
export default async function Create({ params }: { params: Promise<{ res: string }> }) {
  const R = RES[(await params).res];
  if (!R) notFound();
  await need(R.level);
  return <ResFormPage R={R} />;
}
