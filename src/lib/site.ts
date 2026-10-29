import { db } from "./db";
export type Biz = { business_name: string; business_type: string; tagline: string; description: string; primary_color: string; secondary_color: string; contact_email: string; contact_phone: string; whatsapp_number: string; address: string; maps_url: string; instagram_url: string; is_demo: number };
export const getBiz = () => db.prepare("select * from business_settings limit 1").get() as unknown as Biz;
export function waUrl(b: Biz, text = "") {
  let n = (b.whatsapp_number || "").replace(/\D/g, "");
  if (!n) return "";
  if (n.startsWith("0")) n = "62" + n.slice(1);
  return `https://wa.me/${n}` + (text ? `?text=${encodeURIComponent(text)}` : "");
}
