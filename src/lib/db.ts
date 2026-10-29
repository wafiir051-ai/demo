import { DatabaseSync } from "node:sqlite";
import path from "node:path";
import { scryptSync } from "node:crypto";
import { ensureExtra } from "./extra";

export const hash = (p: string) => scryptSync(p, "goding-salt", 32).toString("hex");
export const rupiah = (n: number | null) => (n == null ? "Hubungi kami" : "Rp " + n.toLocaleString("id-ID"));
const slug = (s: string) => s.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");

const g = globalThis as unknown as { __db?: DatabaseSync };
function open() {
  const d = new DatabaseSync(path.join(process.cwd(), "goding.db"));
  d.exec(`
  create table if not exists users(id integer primary key, name text, email text unique, password text, role text);
  create table if not exists business_settings(id integer primary key, business_name text, business_type text, tagline text, description text, primary_color text, secondary_color text, contact_email text, contact_phone text, whatsapp_number text, address text, maps_url text, instagram_url text, is_demo integer);
  create table if not exists facilities(id integer primary key, name text, slug text, icon text, description text, sort_order integer, status text default 'published');
  create table if not exists accommodations(id integer primary key, name text, slug text unique, description text, capacity integer, bed_description text, price_per_night integer, sort_order integer default 0, status text default 'published');
  create table if not exists accommodation_facility(accommodation_id integer, facility_id integer);
  create table if not exists packages(id integer primary key, name text, slug text, description text, package_type text, capacity integer, price integer, terms text, status text default 'published');
  create table if not exists package_items(id integer primary key, package_id integer, label text, sort_order integer);
  create table if not exists galleries(id integer primary key, title text, category text, alt_text text, sort_order integer, status text default 'published');
  create table if not exists pages(id integer primary key, title text, slug text unique, content text, status text default 'published');
  create table if not exists reservations(id integer primary key, code text unique, guest_name text, phone text, email text, accommodation_id integer, check_in text, check_out text, guest_count integer, notes text, status text default 'pending', created_at text default current_timestamp);
  `);
  if (!d.prepare("select 1 from users").get()) seed(d);
  ensureExtra(d);
  return d;
}
function seed(d: DatabaseSync) {
  const u = d.prepare("insert into users(name,email,password,role) values(?,?,?,?)");
  for (const [n, e, r] of [["Super Admin Demo", "superadmin@goding.test", "super_admin"], ["Admin Demo", "admin@goding.test", "admin"], ["Staf Demo", "staff@goding.test", "staff"]]) u.run(n, e, hash("password"), r);
  d.prepare("insert into business_settings(business_name,business_type,tagline,description,primary_color,secondary_color,contact_email,contact_phone,whatsapp_number,address,maps_url,instagram_url,is_demo) values(?,?,?,?,?,?,?,?,?,?,?,?,1)").run(
    "Villa Bukit Pinus (Demo)", "Villa & Penginapan", "Menginap tenang di antara pepohonan pinus",
    "Ini adalah data demo. Ganti seluruh teks ini lewat Dashboard > Pengaturan Bisnis.\n\nVilla Bukit Pinus adalah contoh bisnis penginapan untuk menguji sistem Goding Hospitality.",
    "#2f4a3a", "#b8895a", "halo@contoh.test", "0812-0000-0000", "0812-0000-0000", "Jl. Contoh No. 1, Kota Contoh (alamat demo)", "https://maps.google.com", "https://instagram.com/goding.id");
  const f = d.prepare("insert into facilities(name,slug,icon,description,sort_order) values(?,?,?,?,?)");
  [["Kolam Renang", "🏊", "Kolam renang pribadi untuk tamu (contoh data demo)."], ["Wi-Fi Cepat", "📶", "Akses internet di seluruh area."], ["Dapur Lengkap", "🍳", "Peralatan masak dan makan tersedia."], ["Area BBQ", "🔥", "Area panggang di taman."], ["Parkir Luas", "🚗", "Parkir untuk beberapa kendaraan."], ["Taman & Gazebo", "🌲", "Ruang santai terbuka."]]
    .forEach(([n, i, t], k) => f.run(n, slug(n), i, t, k));
  const a = d.prepare("insert into accommodations(name,slug,capacity,bed_description,price_per_night,description,sort_order) values(?,?,?,?,?,?,?)");
  const af = d.prepare("insert into accommodation_facility values(?,?)");
  [["Villa Keluarga Pinus", 8, "3 kamar tidur: 1 king, 2 twin", 1800000, "Villa luas untuk keluarga atau rombongan kecil."], ["Cottage Pasangan", 2, "1 kamar tidur: 1 queen", 750000, "Cottage hangat untuk berdua."], ["Kamar Standar Taman", 3, "1 kamar tidur: 1 double + 1 single", 500000, "Kamar sederhana menghadap taman."]]
    .forEach(([n, c, b, p, t], k) => { const r = a.run(n, slug(n as string), c, b, p, `[Data demo] ${t}`, k); for (let j = 0; j < 4; j++) af.run(Number(r.lastInsertRowid), ((k + j) % 6) + 1); });
  const p = d.prepare("insert into packages(name,slug,package_type,capacity,price,description,terms) values(?,?,?,?,?,?,?)");
  const pi = d.prepare("insert into package_items(package_id,label,sort_order) values(?,?,?)");
  let r = p.run("Paket Gathering Keluarga", "paket-gathering-keluarga", "gathering", 30, 4500000, "[Data demo] Paket untuk acara keluarga besar.", "Pemesanan minimal 7 hari sebelumnya (contoh).");
  ["Sewa area gathering 6 jam", "Area BBQ", "Welcome drink", "Parkir"].forEach((l, i) => pi.run(Number(r.lastInsertRowid), l, i));
  r = p.run("Paket Akhir Pekan", "paket-akhir-pekan", "stay", 4, 2200000, "[Data demo] Menginap 2 malam.", null);
  ["2 malam menginap", "Sarapan", "Late check-out (sesuai ketersediaan)"].forEach((l, i) => pi.run(Number(r.lastInsertRowid), l, i));
  const gl = d.prepare("insert into galleries(title,category,alt_text,sort_order) values(?,?,?,?)");
  [["Teras villa", "Kamar"], ["Kolam renang", "Fasilitas"], ["Taman pagi hari", "Lingkungan"], ["Ruang keluarga", "Kamar"], ["Area BBQ", "Fasilitas"], ["Jalur setapak pinus", "Lingkungan"]].forEach(([t, c], i) => gl.run(t, c, `${t} (foto placeholder demo)`, i));
  const pg = d.prepare("insert into pages(title,slug,content) values(?,?,?)");
  pg.run("Tentang Kami", "tentang", "## Tentang\n\n[Data demo] Tuliskan kisah dan nilai bisnis Anda di sini. Halaman ini bisa diedit lewat Dashboard > Halaman Website.");
  pg.run("Kebijakan Pemesanan", "kebijakan-pemesanan", "- [Data demo] Permintaan reservasi bukan konfirmasi pemesanan.\n- Tim kami akan menghubungi Anda untuk konfirmasi.");
  const rs = d.prepare("insert into reservations(code,guest_name,phone,accommodation_id,check_in,check_out,guest_count,status,notes) values(?,?,?,?,?,?,2,?,'Data demo')");
  const day = (n: number) => new Date(Date.now() + n * 864e5).toISOString().slice(0, 10);
  [["Tamu Contoh A", "pending", "GDG-DEMO-0001", 5], ["Tamu Contoh B", "confirmed", "GDG-DEMO-0002", 12], ["Tamu Contoh C", "completed", "GDG-DEMO-0003", -10]]
    .forEach(([n, s, c, k], i) => rs.run(c, n, "0800-0000-000" + i, (i % 3) + 1, day(k as number), day((k as number) + 2), s));
}
export const db = (g.__db ??= open());
