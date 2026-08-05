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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('file')->nullable();
            $table->longText('description')->nullable();
            $table->longText('credintials')->nullable();
            $table->longText('technologies')->nullable();
            $table->longText('features')->nullable();
            $table->longText('note')->nullable();
            $table->string('frontend')->nullable();
            $table->string('backend')->nullable();
            $table->string('github')->nullable();
            $table->json('metadata')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
