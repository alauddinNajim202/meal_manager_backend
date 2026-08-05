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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nid_number')->nullable()->after('phone');
            $table->date('dob')->nullable()->after('nid_number');
            $table->string('profession')->nullable()->after('dob');
            $table->string('religion')->nullable()->after('profession');
            $table->string('gender')->nullable()->after('religion');
            $table->text('address')->nullable()->after('profession');
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete()->after('address');
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete()->after('division_id');
            $table->foreignId('upazila_id')->nullable()->constrained('upazilas')->nullOnDelete()->after('district_id');
            $table->string('whatsapp_number')->nullable()->after('address');
            $table->string('referred_by_id')->nullable()->after('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['upazila_id']);
            $table->dropColumn([
                'nid_number',
                'dob',
                'profession',
                'religion',
                'gender',
                'address',
                'division_id',
                'district_id',
                'upazila_id',
            ]);
        });
    }
};
