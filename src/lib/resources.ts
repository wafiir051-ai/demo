import { rupiah } from "./db";

export type Field = {
  name: string; label: string; type: "text" | "email" | "textarea" | "number" | "select" | "image" | "slug" | "checkbox" | "password" | "color";
  options?: Record<string, string>; rows?: number; required?: boolean; reqNew?: boolean; def?: string | number;
  max?: number; minLen?: number; min?: number; maxv?: number; url?: boolean; unique?: boolean; pattern?: [string, string];
  col?: string; mimes?: string[]; maxKb?: number; section?: string; two?: boolean; ro?: boolean; htmlRequired?: boolean;
};
export type Col = { key: string; label: string; type?: "image" | "money" | "status" | "bool" | "datetime"; map?: Record<string, string> };
export type Res = {
  key: string; table: string; title: string; entity: string; label: string; folder: string; level: number; status?: boolean;
  slugFrom?: string; search: string[]; order: [string, "asc" | "desc"]; columns: Col[]; fields: Field[];
};

export const PACKAGE_TYPES: Record<string, string> = { gathering: "Gathering", family_event: "Acara Keluarga", corporate: "Acara Perusahaan", stay: "Paket Menginap", promo: "Promo", other: "Lainnya" };
export const USER_ROLES: Record<string, string> = { super_admin: "Super Admin", admin: "Admin", staff: "Staff" };
const PUB = { published: "Dipublikasikan", draft: "Draft (disembunyikan)" };
const status = (def: string): Field => ({ name: "status", label: "Status publikasi", type: "select", options: PUB, required: true, def });
const sort: Field = { name: "sort_order", label: "Urutan tampil (angka kecil tampil lebih dulu)", type: "number", min: 0, maxv: 9999, def: 0 };
const slug = (label = "Slug URL (kosongkan untuk otomatis)"): Field => ({ name: "slug", label, type: "slug", max: 150 });
const colStatus: Col = { key: "status", label: "Status", type: "status" };

export const RES: Record<string, Res> = {
  akomodasi: {
    key: "akomodasi", table: "accommodations", title: "Akomodasi", entity: "Accommodation", label: "name", folder: "accommodations", level: 2, status: true,
    slugFrom: "name", search: ["name", "description"], order: ["sort_order", "asc"],
    columns: [{ key: "featured_image", label: "Foto", type: "image" }, { key: "name", label: "Nama" }, { key: "capacity", label: "Kapasitas" }, { key: "price_per_night", label: "Harga/malam", type: "money" }, colStatus],
    fields: [
      { name: "name", label: "Nama akomodasi", type: "text", required: true, max: 150 },
      slug(),
      { name: "description", label: "Deskripsi", type: "textarea", max: 5000 },
      { name: "capacity", label: "Kapasitas (tamu)", type: "number", required: true, min: 1, maxv: 200 },
      { name: "bed_description", label: "Keterangan tempat tidur", type: "text", max: 255 },
      { name: "price_per_night", label: "Harga per malam (Rp, kosongkan bila tidak ditampilkan)", type: "number", min: 0 },
      { name: "featured_image", label: "Foto utama", type: "image" },
      status("published"), sort,
    ],
  },
  fasilitas: {
    key: "fasilitas", table: "facilities", title: "Fasilitas", entity: "Facility", label: "name", folder: "facilities", level: 2, status: true,
    slugFrom: "name", search: ["name", "description"], order: ["sort_order", "asc"],
    columns: [{ key: "image_path", label: "Foto", type: "image" }, { key: "name", label: "Nama" }, { key: "sort_order", label: "Urutan" }, colStatus],
    fields: [
      { name: "name", label: "Nama fasilitas", type: "text", required: true, max: 150 },
      slug(),
      { name: "description", label: "Deskripsi", type: "textarea", max: 2000 },
      { name: "icon", label: "Ikon (emoji atau simbol singkat)", type: "text", max: 8 },
      { name: "image_path", label: "Foto", type: "image" },
      status("published"), sort,
    ],
  },
  galeri: {
    key: "galeri", table: "galleries", title: "Galeri", entity: "Gallery", label: "title", folder: "galleries", level: 2, status: true,
    search: ["title", "category", "alt_text"], order: ["sort_order", "asc"],
    columns: [{ key: "image_path", label: "Foto", type: "image" }, { key: "title", label: "Judul" }, { key: "category", label: "Kategori" }, { key: "sort_order", label: "Urutan" }, colStatus],
    fields: [
      { name: "title", label: "Judul", type: "text", required: true, max: 150 },
      { name: "image_path", label: "Gambar (jpg/png/webp, maks 3 MB)", type: "image", reqNew: true },
      { name: "category", label: "Kategori (mis. Kamar, Lingkungan)", type: "text", max: 60 },
      { name: "alt_text", label: "Alt text (deskripsi gambar untuk aksesibilitas/SEO)", type: "text", max: 200 },
      { name: "description", label: "Keterangan", type: "textarea", max: 1000 },
      status("published"), sort,
    ],
  },
  paket: {
    key: "paket", table: "packages", title: "Paket dan Acara", entity: "Package", label: "name", folder: "packages", level: 2, status: true,
    slugFrom: "name", search: ["name", "description"], order: ["id", "desc"],
    columns: [{ key: "featured_image", label: "Foto", type: "image" }, { key: "name", label: "Nama" }, { key: "package_type", label: "Jenis", map: PACKAGE_TYPES }, { key: "price", label: "Harga", type: "money" }, colStatus],
    fields: [
      { name: "name", label: "Nama paket", type: "text", required: true, max: 150 },
      slug(),
      { name: "package_type", label: "Jenis paket", type: "select", options: PACKAGE_TYPES, required: true, def: "gathering" },
      { name: "description", label: "Deskripsi", type: "textarea", max: 5000 },
      { name: "capacity", label: "Kapasitas (orang)", type: "number", min: 1, maxv: 5000 },
      { name: "price", label: "Harga (Rp, kosongkan bila tidak ditampilkan)", type: "number", min: 0 },
      { name: "featured_image", label: "Foto", type: "image" },
      { name: "terms", label: "Syarat dan ketentuan", type: "textarea", max: 3000 },
      status("published"),
    ],
  },
  halaman: {
    key: "halaman", table: "pages", title: "Halaman Website", entity: "Page", label: "title", folder: "pages", level: 2, status: true,
    slugFrom: "title", search: ["title", "content"], order: ["id", "desc"],
    columns: [{ key: "title", label: "Judul" }, { key: "slug", label: "Slug" }, colStatus, { key: "updated_at", label: "Diubah", type: "datetime" }],
    fields: [
      { name: "title", label: "Judul halaman", type: "text", required: true, max: 150 },
      slug('Slug URL (kosongkan untuk otomatis; halaman "tentang" dipakai di menu Tentang)'),
      { name: "content", label: "Konten (mendukung Markdown; HTML mentah dinonaktifkan demi keamanan)", type: "textarea", rows: 14, max: 60000 },
      { name: "featured_image", label: "Gambar utama", type: "image" },
      { name: "meta_title", label: "Meta title (SEO)", type: "text", max: 160 },
      { name: "meta_description", label: "Meta description (SEO)", type: "textarea", rows: 3, max: 300 },
      status("draft"),
    ],
  },
  pengguna: {
    key: "pengguna", table: "users", title: "Pengguna", entity: "User", label: "name", folder: "users", level: 3,
    search: ["name", "email"], order: ["id", "asc"],
    columns: [{ key: "name", label: "Nama" }, { key: "email", label: "Email" }, { key: "role", label: "Role", map: USER_ROLES }, { key: "is_active", label: "Aktif", type: "bool" }],
    fields: [
      { name: "name", label: "Nama", type: "text", required: true, max: 120 },
      { name: "email", label: "Email", type: "email", required: true, max: 190, unique: true },
      { name: "role", label: "Role", type: "select", options: USER_ROLES, required: true, def: "staff" },
      { name: "is_active", label: "Akun aktif", type: "checkbox" },
      { name: "password", label: "Password (minimal 8 karakter; saat edit, isi hanya untuk reset password)", type: "password", reqNew: true, minLen: 8, max: 100 },
    ],
  },
};

const hex: [string, string] = ["^#[0-9A-Fa-f]{6}$", "Warna harus berformat hex, mis. #2f4a3a."];
export const SETTINGS_FIELDS: Field[] = [
  { name: "business_name", label: "Nama bisnis", type: "text", required: true, max: 150, section: "Identitas" },
  { name: "business_type", label: "Jenis bisnis (mis. Villa, Hotel)", type: "text", max: 100 },
  { name: "tagline", label: "Tagline (headline di hero)", type: "text", max: 200 },
  { name: "description", label: "Deskripsi bisnis", type: "textarea", max: 5000, rows: 3 },
  { name: "primary_color", label: "Warna utama", type: "color", required: true, pattern: hex, def: "#2f4a3a", section: "Tampilan", two: true },
  { name: "secondary_color", label: "Warna sekunder", type: "color", required: true, pattern: hex, def: "#b8895a", two: true },
  { name: "logo", col: "logo_path", label: "Logo", type: "image" },
  { name: "favicon", col: "favicon_path", label: "Favicon", type: "image", mimes: ["png", "jpg", "webp", "ico"], maxKb: 1024 },
  { name: "hero_image", col: "hero_image_path", label: "Foto hero", type: "image" },
  { name: "contact_email", label: "Email kontak", type: "email", max: 150, section: "Kontak" },
  { name: "contact_phone", label: "Telepon", type: "text", max: 30 },
  { name: "whatsapp_number", label: "Nomor WhatsApp (mis. 0812xxxxxxx)", type: "text", pattern: ["^[0-9+\\s\\-]{8,20}$", "Nomor WhatsApp tidak valid (8–20 karakter: angka, +, spasi, strip)."] },
  { name: "address", label: "Alamat", type: "textarea", max: 500, rows: 3 },
  { name: "maps_url", label: "Tautan Google Maps", type: "text", url: true, max: 500 },
  { name: "instagram_url", label: "URL Instagram", type: "text", url: true, max: 300 },
  { name: "facebook_url", label: "URL Facebook", type: "text", url: true, max: 300 },
  { name: "website_title", label: "Judul website (title)", type: "text", max: 160, section: "SEO dasar" },
  { name: "meta_description", label: "Meta description", type: "textarea", max: 300, rows: 3 },
  { name: "is_demo", label: 'Tampilkan label "data demo" di website publik', type: "checkbox", section: "Mode demo" },
];
export const ACCOUNT_FIELDS: Field[] = [
  { name: "email", label: "Email (hanya Super Admin yang dapat mengubah lewat menu Pengguna)", type: "email", ro: true },
  { name: "name", label: "Nama", type: "text", required: true, max: 120, htmlRequired: true },
  { name: "current_password", label: "Password saat ini", type: "password", section: "Ganti password (opsional)" },
  { name: "password", label: "Password baru (min. 8 karakter)", type: "password", minLen: 8, max: 100 },
  { name: "password_confirmation", label: "Ulangi password baru", type: "password" },
];
export { rupiah };
