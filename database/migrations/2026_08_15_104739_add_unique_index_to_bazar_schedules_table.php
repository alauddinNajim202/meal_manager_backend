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
        Schema::table('bazar_schedules', function (Blueprint $table) {
            $table->unique(['mess_id', 'date'], 'bazar_schedules_mess_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bazar_schedules', function (Blueprint $table) {
            $table->dropUnique('bazar_schedules_mess_date_unique');
        });
    }
};
