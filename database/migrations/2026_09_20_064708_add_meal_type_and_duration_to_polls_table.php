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
        Schema::table('polls', function (Blueprint $table) {
            $table->string('meal_type')->nullable()->after('title');
            $table->integer('duration_hours')->nullable()->after('meal_type');
            $table->timestamp('expires_at')->nullable()->after('duration_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn(['meal_type', 'duration_hours', 'expires_at']);
        });
    }
};
