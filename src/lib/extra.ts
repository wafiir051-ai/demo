import type { DatabaseSync } from "node:sqlite";
const cols = (d: DatabaseSync, t: string) => (d.prepare(`pragma table_info(${t})`).all() as any[]).map(c => c.name as string);
// Hanya menulis bila kolom benar-benar belum ada, supaya proses paralel (next build) tidak saling mengunci.
const add = (d: DatabaseSync, t: string, c: string, type: string) => {
  if (cols(d, t).includes(c)) return;
  try { d.exec(`alter table ${t} add column ${c} ${type}`); } catch { /* sudah dibuat proses lain */ }
};
// Tabel dan kolom tambahan agar setara skema Laravel. Aman dijalankan berulang.
export function ensureExtra(d: DatabaseSync) {
  d.exec("pragma busy_timeout = 10000");
  d.exec(`create table if not exists contact_messages(id integer primary key, name text, email text, phone text, subject text, message text, status text default 'unread', created_at text default current_timestamp);
  create table if not exists activity_logs(id integer primary key, user_id integer, action text, description text, created_at text default current_timestamp);
  create table if not exists accommodation_images(id integer primary key, accommodation_id integer, path text, created_at text default current_timestamp);`);
  add(d, "reservations", "admin_notes", "text");
  add(d, "users", "is_active", "integer default 1");
  add(d, "accommodations", "featured_image", "text");
  add(d, "facilities", "image_path", "text");
  add(d, "galleries", "image_path", "text");
  add(d, "galleries", "description", "text");
  add(d, "packages", "featured_image", "text");
  add(d, "pages", "featured_image", "text");
  add(d, "pages", "meta_title", "text");
  add(d, "pages", "meta_description", "text");
  add(d, "pages", "updated_at", "text");
  add(d, "activity_logs", "entity_type", "text");
  add(d, "activity_logs", "entity_id", "integer");
  for (const c of ["facebook_url", "website_title", "meta_description", "logo_path", "favicon_path", "hero_image_path"]) add(d, "business_settings", c, "text");
  if (d.prepare("select 1 from pages where updated_at is null").get()) d.exec("update pages set updated_at=current_timestamp where updated_at is null");
  if (!d.prepare("select 1 from contact_messages").get()) {
    const m = d.prepare("insert into contact_messages(name,email,phone,subject,message,status) values(?,?,?,?,?,?)");
    m.run("Pengunjung Contoh", "contoh@contoh.test", null, "Tanya paket gathering", "[Data demo] Apakah tersedia untuk 25 orang?", "unread");
    m.run("Pengunjung Contoh 2", null, "0800-1111-2222", "Jam check-in", "[Data demo] Jam check-in berapa?", "read");
  }
  if (!d.prepare("select 1 from activity_logs").get())
    d.prepare("insert into activity_logs(user_id,action,description) values(1,'seed','Data demo dibuat oleh seeder')").run();
}
export function logActivity(d: DatabaseSync, userId: number, action: string, description: string, entityType: string | null = null, entityId: number | null = null) {
  d.prepare("insert into activity_logs(user_id,action,description,entity_type,entity_id) values(?,?,?,?,?)").run(userId, action, description, entityType, entityId);
}
