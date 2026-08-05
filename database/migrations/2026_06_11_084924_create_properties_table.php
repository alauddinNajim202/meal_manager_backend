<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();

            $table->string('for_whom')->nullable()->comment('family, male, female, any');
            $table->integer('month_id')->default(1);
            $table->boolean('is_available_immediately')->default(false);

            $table->decimal('rent_amount', 10, 2);
            $table->integer('advance_month')->nullable();
            $table->decimal('service_charge', 10, 2)->nullable();
            $table->boolean('is_negotiable')->default(false);
            $table->enum('rent_type', ['monthly', 'yearly'])->default('monthly');

            $table->integer('beds')->nullable();
            $table->integer('baths')->nullable();
            $table->integer('balconies')->nullable();
            $table->string('floor_no')->nullable();
            $table->integer('size_sqft')->nullable();

            $table->foreignId('division_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('district_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('upazila_id')->nullable()->constrained()->onDelete('set null');
            $table->string('area')->nullable();
            $table->text('address')->nullable();
            $table->string('map_link')->nullable();

            $table->boolean('gas_bill_included')->default(false);
            $table->boolean('electricity_bill_included')->default(false);
            $table->boolean('water_bill_included')->default(false);
            $table->decimal('market_distance_km', 4, 1)->nullable();

            $table->string('contact_name')->nullable();
            $table->string('contact_type')->nullable()->comment('owner, broker, caretaker');
            $table->string('contact_mobile_number')->nullable();
            $table->string('contact_whatsapp_number')->nullable();
            $table->boolean('hide_contact_number')->default(false);

            $table->longText('description');
            $table->text('special_terms')->nullable();

            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');


            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
