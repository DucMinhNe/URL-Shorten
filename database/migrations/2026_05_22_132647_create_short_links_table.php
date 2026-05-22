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
        Schema::create('short_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug', 32)->unique();
            $table->text('original_url');
            $table->string('title')->nullable();
            $table->string('password')->nullable();
            $table->enum('status', ['active','disabled','blocked'])->default('active');
            $table->unsignedInteger('total_clicks')->default(0);
            $table->unsignedInteger('valid_views')->default(0);
            $table->unsignedBigInteger('total_earned')->default(0);
            $table->timestamps();
            $table->index(['user_id','status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_links');
    }
};
