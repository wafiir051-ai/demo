CREATE TABLE "accommodation_facility" (
	"accommodation_id" integer,
	"facility_id" integer,
	CONSTRAINT "accommodation_facility_pkey" PRIMARY KEY("accommodation_id","facility_id")
);
--> statement-breakpoint
CREATE TABLE "accommodation_images" (
	"id" serial PRIMARY KEY,
	"accommodation_id" integer NOT NULL,
	"path" text NOT NULL,
	"sort_order" integer DEFAULT 0 NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "accommodations" (
	"id" serial PRIMARY KEY,
	"name" text NOT NULL,
	"slug" text NOT NULL UNIQUE,
	"description" text,
	"capacity" integer DEFAULT 2 NOT NULL,
	"bed_description" text,
	"price_per_night" bigint,
	"featured_image" text,
	"status" varchar(20) DEFAULT 'published' NOT NULL,
	"sort_order" integer DEFAULT 0 NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "activity_logs" (
	"id" serial PRIMARY KEY,
	"user_id" text,
	"user_name" text,
	"action" varchar(40) NOT NULL,
	"entity_type" text,
	"entity_id" integer,
	"description" text,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "business_settings" (
	"id" serial PRIMARY KEY,
	"business_name" text NOT NULL,
	"business_type" text,
	"tagline" text,
	"description" text,
	"logo_path" text,
	"favicon_path" text,
	"hero_image_path" text,
	"primary_color" varchar(7) DEFAULT '#2f4a3a' NOT NULL,
	"secondary_color" varchar(7) DEFAULT '#b8895a' NOT NULL,
	"contact_email" text,
	"contact_phone" text,
	"whatsapp_number" text,
	"address" text,
	"maps_url" text,
	"instagram_url" text,
	"facebook_url" text,
	"website_title" text,
	"meta_description" text,
	"is_demo" boolean DEFAULT false NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "contact_messages" (
	"id" serial PRIMARY KEY,
	"name" text NOT NULL,
	"email" text,
	"phone" text,
	"subject" text,
	"message" text NOT NULL,
	"status" varchar(20) DEFAULT 'unread' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "facilities" (
	"id" serial PRIMARY KEY,
	"name" text NOT NULL,
	"slug" text NOT NULL UNIQUE,
	"description" text,
	"icon" text,
	"image_path" text,
	"status" varchar(20) DEFAULT 'published' NOT NULL,
	"sort_order" integer DEFAULT 0 NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "galleries" (
	"id" serial PRIMARY KEY,
	"title" text NOT NULL,
	"description" text,
	"image_path" text,
	"category" text,
	"alt_text" text,
	"status" varchar(20) DEFAULT 'published' NOT NULL,
	"sort_order" integer DEFAULT 0 NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "package_items" (
	"id" serial PRIMARY KEY,
	"package_id" integer NOT NULL,
	"label" text NOT NULL,
	"sort_order" integer DEFAULT 0 NOT NULL
);
--> statement-breakpoint
CREATE TABLE "packages" (
	"id" serial PRIMARY KEY,
	"name" text NOT NULL,
	"slug" text NOT NULL UNIQUE,
	"description" text,
	"package_type" varchar(40) DEFAULT 'gathering' NOT NULL,
	"capacity" integer,
	"price" bigint,
	"featured_image" text,
	"terms" text,
	"status" varchar(20) DEFAULT 'published' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "pages" (
	"id" serial PRIMARY KEY,
	"title" text NOT NULL,
	"slug" text NOT NULL UNIQUE,
	"content" text,
	"featured_image" text,
	"meta_title" text,
	"meta_description" text,
	"status" varchar(20) DEFAULT 'draft' NOT NULL,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE TABLE "reservations" (
	"id" serial PRIMARY KEY,
	"reservation_code" text NOT NULL UNIQUE,
	"guest_name" text NOT NULL,
	"phone" text NOT NULL,
	"email" text,
	"accommodation_id" integer,
	"check_in" date NOT NULL,
	"check_out" date NOT NULL,
	"guest_count" integer NOT NULL,
	"special_requests" text,
	"status" varchar(20) DEFAULT 'pending' NOT NULL,
	"admin_notes" text,
	"created_at" timestamp with time zone DEFAULT now() NOT NULL,
	"updated_at" timestamp with time zone DEFAULT now() NOT NULL
);
--> statement-breakpoint
CREATE INDEX "accommodations_status_idx" ON "accommodations" ("status");--> statement-breakpoint
CREATE INDEX "contact_messages_status_idx" ON "contact_messages" ("status");--> statement-breakpoint
CREATE INDEX "facilities_status_idx" ON "facilities" ("status");--> statement-breakpoint
CREATE INDEX "galleries_status_idx" ON "galleries" ("status");--> statement-breakpoint
CREATE INDEX "galleries_category_idx" ON "galleries" ("category");--> statement-breakpoint
CREATE INDEX "packages_status_idx" ON "packages" ("status");--> statement-breakpoint
CREATE INDEX "pages_status_idx" ON "pages" ("status");--> statement-breakpoint
CREATE INDEX "reservations_status_idx" ON "reservations" ("status");--> statement-breakpoint
CREATE INDEX "reservations_check_in_idx" ON "reservations" ("check_in");--> statement-breakpoint
ALTER TABLE "accommodation_facility" ADD CONSTRAINT "accommodation_facility_accommodation_id_accommodations_id_fkey" FOREIGN KEY ("accommodation_id") REFERENCES "accommodations"("id") ON DELETE CASCADE;--> statement-breakpoint
ALTER TABLE "accommodation_facility" ADD CONSTRAINT "accommodation_facility_facility_id_facilities_id_fkey" FOREIGN KEY ("facility_id") REFERENCES "facilities"("id") ON DELETE CASCADE;--> statement-breakpoint
ALTER TABLE "accommodation_images" ADD CONSTRAINT "accommodation_images_accommodation_id_accommodations_id_fkey" FOREIGN KEY ("accommodation_id") REFERENCES "accommodations"("id") ON DELETE CASCADE;--> statement-breakpoint
ALTER TABLE "package_items" ADD CONSTRAINT "package_items_package_id_packages_id_fkey" FOREIGN KEY ("package_id") REFERENCES "packages"("id") ON DELETE CASCADE;--> statement-breakpoint
ALTER TABLE "reservations" ADD CONSTRAINT "reservations_accommodation_id_accommodations_id_fkey" FOREIGN KEY ("accommodation_id") REFERENCES "accommodations"("id") ON DELETE SET NULL;