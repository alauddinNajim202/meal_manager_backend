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
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'manager', 'member') DEFAULT 'member'");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE mess_user MODIFY COLUMN role ENUM('owner', 'manager', 'member') DEFAULT 'member'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("UPDATE users SET role = 'manager' WHERE role = 'owner'");
            \Illuminate\Support\Facades\DB::statement("UPDATE mess_user SET role = 'manager' WHERE role = 'owner'");
            
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('manager', 'member') DEFAULT 'member'");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE mess_user MODIFY COLUMN role ENUM('manager', 'member') DEFAULT 'member'");
        }
    }
};
