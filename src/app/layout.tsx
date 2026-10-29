import "./globals.css";
import { getBiz } from "@/lib/site";
export const dynamic = "force-dynamic";
export async function generateMetadata() { const b = getBiz(); return { title: b.business_name, description: b.description.slice(0, 155) }; }
export default function RootLayout({ children }: { children: React.ReactNode }) {
  const b = getBiz();
  return (
    <html lang="id" style={{ "--primary": b.primary_color, "--secondary": b.secondary_color } as React.CSSProperties}>
      <body>{children}</body>
    </html>
  );
}
