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
    
        Schema::table('mess_user', function (Blueprint $table) {
            $table->string('nid', 100)->nullable();
            $table->string('nid_front')->nullable();
            $table->string('nid_back')->nullable();
            $table->string('emergency_contact_phone', 100)->nullable();
            $table->decimal('advance_amount', 10, 2)->nullable();
            $table->string('month', 100)->nullable();
            $table->string('joining_date')->nullable();
            $table->decimal('room_rent', 10, 2)->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       

        Schema::table('mess_user', function (Blueprint $table) {
            $table->dropColumn(['nid', 'nid_front', 'nid_back', 'address', 'emergency_contact_phone', 'advance_amount', 'month', 'joining_date', 'room_rent']);
        });
    }
};
