-- Custom SQL migration file, put your code below! --
-- Data demo (setara DatabaseSeeder Laravel). Ganti/hapus lewat dashboard admin.
INSERT INTO "business_settings" ("business_name", "business_type", "tagline", "description", "primary_color", "secondary_color", "contact_email", "contact_phone", "whatsapp_number", "address", "maps_url", "instagram_url", "website_title", "meta_description", "is_demo")
VALUES ('Villa Bukit Pinus (Demo)', 'Villa & Penginapan', 'Menginap tenang di antara pepohonan pinus',
  E'Ini adalah data demo. Ganti seluruh teks ini lewat Dashboard > Pengaturan Bisnis.\n\nVilla Bukit Pinus adalah contoh bisnis penginapan untuk menguji sistem Goding Hospitality.',
  '#2f4a3a', '#b8895a', 'halo@contoh.test', '0812-0000-0000', '0812-0000-0000', 'Jl. Contoh No. 1, Kota Contoh (alamat demo)', 'https://maps.google.com',
  'https://instagram.com/goding.id', 'Villa Bukit Pinus (Demo) — Penginapan', 'Situs demo penginapan yang dibuat dengan Goding Hospitality.', true);
--> statement-breakpoint
INSERT INTO "facilities" ("name", "slug", "icon", "description", "status", "sort_order") VALUES
  ('Kolam Renang', 'kolam-renang', '🏊', 'Kolam renang pribadi untuk tamu (contoh data demo).', 'published', 0),
  ('Wi-Fi Cepat', 'wi-fi-cepat', '📶', 'Akses internet di seluruh area.', 'published', 1),
  ('Dapur Lengkap', 'dapur-lengkap', '🍳', 'Peralatan masak dan makan tersedia.', 'published', 2),
  ('Area BBQ', 'area-bbq', '🔥', 'Area panggang di taman.', 'published', 3),
  ('Parkir Luas', 'parkir-luas', '🚗', 'Parkir untuk beberapa kendaraan.', 'published', 4),
  ('Taman & Gazebo', 'taman-gazebo', '🌲', 'Ruang santai terbuka.', 'published', 5);
--> statement-breakpoint
INSERT INTO "accommodations" ("name", "slug", "capacity", "bed_description", "price_per_night", "description", "status", "sort_order") VALUES
  ('Villa Keluarga Pinus', 'villa-keluarga-pinus', 8, '3 kamar tidur: 1 king, 2 twin', 1800000, '[Data demo] Villa luas untuk keluarga atau rombongan kecil.', 'published', 0),
  ('Cottage Pasangan', 'cottage-pasangan', 2, '1 kamar tidur: 1 queen', 750000, '[Data demo] Cottage hangat untuk berdua.', 'published', 1),
  ('Kamar Standar Taman', 'kamar-standar-taman', 3, '1 kamar tidur: 1 double + 1 single', 500000, '[Data demo] Kamar sederhana menghadap taman.', 'published', 2);
--> statement-breakpoint
INSERT INTO "accommodation_facility" ("accommodation_id", "facility_id")
SELECT a.id, f.id FROM "accommodations" a JOIN "facilities" f ON
  (a.slug = 'villa-keluarga-pinus' AND f.slug IN ('kolam-renang', 'wi-fi-cepat', 'dapur-lengkap', 'area-bbq'))
  OR (a.slug = 'cottage-pasangan' AND f.slug IN ('kolam-renang', 'wi-fi-cepat', 'parkir-luas', 'taman-gazebo'))
  OR (a.slug = 'kamar-standar-taman' AND f.slug IN ('wi-fi-cepat', 'area-bbq', 'parkir-luas', 'taman-gazebo'));
--> statement-breakpoint
INSERT INTO "packages" ("name", "slug", "package_type", "capacity", "price", "description", "terms", "status") VALUES
  ('Paket Gathering Keluarga', 'paket-gathering-keluarga', 'gathering', 30, 4500000, '[Data demo] Paket untuk acara keluarga besar.', 'Pemesanan minimal 7 hari sebelumnya (contoh).', 'published'),
  ('Paket Akhir Pekan', 'paket-akhir-pekan', 'stay', 4, 2200000, '[Data demo] Menginap 2 malam.', NULL, 'published');
--> statement-breakpoint
INSERT INTO "package_items" ("package_id", "label", "sort_order")
SELECT p.id, v.label, v.ord FROM "packages" p JOIN (VALUES
  ('paket-gathering-keluarga', 'Sewa area gathering 6 jam', 0), ('paket-gathering-keluarga', 'Area BBQ', 1),
  ('paket-gathering-keluarga', 'Welcome drink', 2), ('paket-gathering-keluarga', 'Parkir', 3),
  ('paket-akhir-pekan', '2 malam menginap', 0), ('paket-akhir-pekan', 'Sarapan', 1),
  ('paket-akhir-pekan', 'Late check-out (sesuai ketersediaan)', 2)
) AS v(slug, label, ord) ON p.slug = v.slug;
--> statement-breakpoint
INSERT INTO "galleries" ("title", "category", "alt_text", "status", "sort_order") VALUES
  ('Teras villa', 'Kamar', 'Teras villa (foto placeholder demo)', 'published', 0),
  ('Kolam renang', 'Fasilitas', 'Kolam renang (foto placeholder demo)', 'published', 1),
  ('Taman pagi hari', 'Lingkungan', 'Taman pagi hari (foto placeholder demo)', 'published', 2),
  ('Ruang keluarga', 'Kamar', 'Ruang keluarga (foto placeholder demo)', 'published', 3),
  ('Area BBQ', 'Fasilitas', 'Area BBQ (foto placeholder demo)', 'published', 4),
  ('Jalur setapak pinus', 'Lingkungan', 'Jalur setapak pinus (foto placeholder demo)', 'published', 5);
--> statement-breakpoint
INSERT INTO "pages" ("title", "slug", "status", "content") VALUES
  ('Tentang Kami', 'tentang', 'published', E'## Tentang\n\n[Data demo] Tuliskan kisah dan nilai bisnis Anda di sini. Halaman ini bisa diedit di **Dashboard > Halaman Website** dengan Markdown.'),
  ('Kebijakan Pemesanan', 'kebijakan-pemesanan', 'published', E'- [Data demo] Permintaan reservasi bukan konfirmasi pemesanan.\n- Tim kami akan menghubungi Anda untuk konfirmasi.');
--> statement-breakpoint
INSERT INTO "reservations" ("reservation_code", "guest_name", "phone", "accommodation_id", "check_in", "check_out", "guest_count", "status", "special_requests")
SELECT v.code, v.name, v.phone, a.id, CURRENT_DATE + v.d, CURRENT_DATE + v.d + 2, 2, v.status, 'Data demo'
FROM (VALUES
  ('GDG-DEMO-0001', 'Tamu Contoh A', '0800-0000-0000', 'pending', 5, 'villa-keluarga-pinus'),
  ('GDG-DEMO-0002', 'Tamu Contoh B', '0800-0000-0001', 'confirmed', 12, 'cottage-pasangan'),
  ('GDG-DEMO-0003', 'Tamu Contoh C', '0800-0000-0002', 'completed', -10, 'kamar-standar-taman')
) AS v(code, name, phone, status, d, slug) JOIN "accommodations" a ON a.slug = v.slug;
--> statement-breakpoint
INSERT INTO "contact_messages" ("name", "email", "phone", "subject", "message", "status") VALUES
  ('Pengunjung Contoh', 'contoh@contoh.test', NULL, 'Tanya paket gathering', '[Data demo] Apakah tersedia untuk 25 orang?', 'unread'),
  ('Pengunjung Contoh 2', NULL, '0800-1111-2222', 'Jam check-in', '[Data demo] Jam check-in berapa?', 'read');
--> statement-breakpoint
INSERT INTO "activity_logs" ("action", "entity_type", "description") VALUES ('seed', 'System', 'Data demo dibuat oleh migrasi');
