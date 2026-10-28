<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $t) {
            $t->id();
            $t->string('business_name');
            $t->string('business_type')->nullable();
            $t->string('tagline')->nullable();
            $t->text('description')->nullable();
            $t->string('logo_path')->nullable();
            $t->string('favicon_path')->nullable();
            $t->string('hero_image_path')->nullable();
            $t->string('primary_color', 7)->default('#2f4a3a');
            $t->string('secondary_color', 7)->default('#b8895a');
            $t->string('contact_email')->nullable();
            $t->string('contact_phone')->nullable();
            $t->string('whatsapp_number')->nullable();
            $t->text('address')->nullable();
            $t->string('maps_url')->nullable();
            $t->string('instagram_url')->nullable();
            $t->string('facebook_url')->nullable();
            $t->string('website_title')->nullable();
            $t->text('meta_description')->nullable();
            $t->boolean('is_demo')->default(false);
            $t->timestamps();
        });

        Schema::create('facilities', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->string('icon')->nullable();
            $t->string('image_path')->nullable();
            $t->string('status', 20)->default('published')->index();
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('accommodations', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->unsignedInteger('capacity')->default(2);
            $t->string('bed_description')->nullable();
            $t->unsignedBigInteger('price_per_night')->nullable();
            $t->string('featured_image')->nullable();
            $t->string('status', 20)->default('published')->index();
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('accommodation_facility', function (Blueprint $t) {
            $t->id();
            $t->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $t->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $t->unique(['accommodation_id', 'facility_id']);
        });

        Schema::create('accommodation_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('galleries', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('image_path')->nullable();
            $t->string('category')->nullable()->index();
            $t->string('alt_text')->nullable();
            $t->string('status', 20)->default('published')->index();
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('packages', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->string('package_type', 40)->default('gathering');
            $t->unsignedInteger('capacity')->nullable();
            $t->unsignedBigInteger('price')->nullable();
            $t->string('featured_image')->nullable();
            $t->text('terms')->nullable();
            $t->string('status', 20)->default('published')->index();
            $t->timestamps();
        });

        Schema::create('package_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('package_id')->constrained()->cascadeOnDelete();
            $t->string('label');
            $t->unsignedInteger('sort_order')->default(0);
        });

        Schema::create('reservations', function (Blueprint $t) {
            $t->id();
            $t->string('reservation_code')->unique();
            $t->string('guest_name');
            $t->string('phone');
            $t->string('email')->nullable();
            $t->foreignId('accommodation_id')->nullable()->constrained()->nullOnDelete();
            $t->date('check_in')->index();
            $t->date('check_out');
            $t->unsignedInteger('guest_count');
            $t->text('special_requests')->nullable();
            $t->string('status', 20)->default('pending')->index();
            $t->text('admin_notes')->nullable();
            $t->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('subject')->nullable();
            $t->text('message');
            $t->string('status', 20)->default('unread')->index();
            $t->timestamps();
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->longText('content')->nullable();
            $t->string('featured_image')->nullable();
            $t->string('meta_title')->nullable();
            $t->text('meta_description')->nullable();
            $t->string('status', 20)->default('draft')->index();
            $t->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('action', 40);
            $t->string('entity_type')->nullable();
            $t->unsignedBigInteger('entity_id')->nullable();
            $t->text('description')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['activity_logs', 'pages', 'contact_messages', 'reservations', 'package_items', 'packages', 'galleries', 'accommodation_images', 'accommodation_facility', 'accommodations', 'facilities', 'business_settings'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
