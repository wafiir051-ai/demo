import { pgTable, serial, text, varchar, integer, bigint, boolean, date, timestamp, primaryKey, index } from "drizzle-orm/pg-core";

const stamps = {
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow(),
};

export const businessSettings = pgTable("business_settings", {
  id: serial().primaryKey(),
  businessName: text("business_name").notNull(),
  businessType: text("business_type"),
  tagline: text(),
  description: text(),
  logoPath: text("logo_path"),
  faviconPath: text("favicon_path"),
  heroImagePath: text("hero_image_path"),
  primaryColor: varchar("primary_color", { length: 7 }).notNull().default("#2f4a3a"),
  secondaryColor: varchar("secondary_color", { length: 7 }).notNull().default("#b8895a"),
  contactEmail: text("contact_email"),
  contactPhone: text("contact_phone"),
  whatsappNumber: text("whatsapp_number"),
  address: text(),
  mapsUrl: text("maps_url"),
  instagramUrl: text("instagram_url"),
  facebookUrl: text("facebook_url"),
  websiteTitle: text("website_title"),
  metaDescription: text("meta_description"),
  isDemo: boolean("is_demo").notNull().default(false),
  ...stamps,
});

export const facilities = pgTable("facilities", {
  id: serial().primaryKey(),
  name: text().notNull(),
  slug: text().notNull().unique(),
  description: text(),
  icon: text(),
  imagePath: text("image_path"),
  status: varchar({ length: 20 }).notNull().default("published"),
  sortOrder: integer("sort_order").notNull().default(0),
  ...stamps,
}, (t) => [index("facilities_status_idx").on(t.status)]);

export const accommodations = pgTable("accommodations", {
  id: serial().primaryKey(),
  name: text().notNull(),
  slug: text().notNull().unique(),
  description: text(),
  capacity: integer().notNull().default(2),
  bedDescription: text("bed_description"),
  pricePerNight: bigint("price_per_night", { mode: "number" }),
  featuredImage: text("featured_image"),
  status: varchar({ length: 20 }).notNull().default("published"),
  sortOrder: integer("sort_order").notNull().default(0),
  ...stamps,
}, (t) => [index("accommodations_status_idx").on(t.status)]);

export const accommodationFacility = pgTable("accommodation_facility", {
  accommodationId: integer("accommodation_id").notNull().references(() => accommodations.id, { onDelete: "cascade" }),
  facilityId: integer("facility_id").notNull().references(() => facilities.id, { onDelete: "cascade" }),
}, (t) => [primaryKey({ columns: [t.accommodationId, t.facilityId] })]);

export const accommodationImages = pgTable("accommodation_images", {
  id: serial().primaryKey(),
  accommodationId: integer("accommodation_id").notNull().references(() => accommodations.id, { onDelete: "cascade" }),
  path: text().notNull(),
  sortOrder: integer("sort_order").notNull().default(0),
  ...stamps,
});

export const galleries = pgTable("galleries", {
  id: serial().primaryKey(),
  title: text().notNull(),
  description: text(),
  imagePath: text("image_path"),
  category: text(),
  altText: text("alt_text"),
  status: varchar({ length: 20 }).notNull().default("published"),
  sortOrder: integer("sort_order").notNull().default(0),
  ...stamps,
}, (t) => [index("galleries_status_idx").on(t.status), index("galleries_category_idx").on(t.category)]);

export const packages = pgTable("packages", {
  id: serial().primaryKey(),
  name: text().notNull(),
  slug: text().notNull().unique(),
  description: text(),
  packageType: varchar("package_type", { length: 40 }).notNull().default("gathering"),
  capacity: integer(),
  price: bigint({ mode: "number" }),
  featuredImage: text("featured_image"),
  terms: text(),
  status: varchar({ length: 20 }).notNull().default("published"),
  ...stamps,
}, (t) => [index("packages_status_idx").on(t.status)]);

export const packageItems = pgTable("package_items", {
  id: serial().primaryKey(),
  packageId: integer("package_id").notNull().references(() => packages.id, { onDelete: "cascade" }),
  label: text().notNull(),
  sortOrder: integer("sort_order").notNull().default(0),
});

export const reservations = pgTable("reservations", {
  id: serial().primaryKey(),
  reservationCode: text("reservation_code").notNull().unique(),
  guestName: text("guest_name").notNull(),
  phone: text().notNull(),
  email: text(),
  accommodationId: integer("accommodation_id").references(() => accommodations.id, { onDelete: "set null" }),
  checkIn: date("check_in").notNull(),
  checkOut: date("check_out").notNull(),
  guestCount: integer("guest_count").notNull(),
  specialRequests: text("special_requests"),
  status: varchar({ length: 20 }).notNull().default("pending"),
  adminNotes: text("admin_notes"),
  ...stamps,
}, (t) => [index("reservations_status_idx").on(t.status), index("reservations_check_in_idx").on(t.checkIn)]);

export const contactMessages = pgTable("contact_messages", {
  id: serial().primaryKey(),
  name: text().notNull(),
  email: text(),
  phone: text(),
  subject: text(),
  message: text().notNull(),
  status: varchar({ length: 20 }).notNull().default("unread"),
  ...stamps,
}, (t) => [index("contact_messages_status_idx").on(t.status)]);

export const pages = pgTable("pages", {
  id: serial().primaryKey(),
  title: text().notNull(),
  slug: text().notNull().unique(),
  content: text(),
  featuredImage: text("featured_image"),
  metaTitle: text("meta_title"),
  metaDescription: text("meta_description"),
  status: varchar({ length: 20 }).notNull().default("draft"),
  ...stamps,
}, (t) => [index("pages_status_idx").on(t.status)]);

export const activityLogs = pgTable("activity_logs", {
  id: serial().primaryKey(),
  // ID & nama pengguna Netlify Identity (disalin agar log tetap terbaca setelah pengguna dihapus)
  userId: text("user_id"),
  userName: text("user_name"),
  action: varchar({ length: 40 }).notNull(),
  entityType: text("entity_type"),
  entityId: integer("entity_id"),
  description: text(),
  ...stamps,
});
