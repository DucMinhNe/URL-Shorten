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
        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('placement', ['top','side','bottom']);
            $table->enum('type', ['banner_image','html','iframe']);
            $table->text('content');
            $table->string('target_url', 500)->nullable();
            $table->unsignedInteger('weight')->default(1);
            $table->enum('status', ['active','paused'])->default('active');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->unsignedInteger('impressions')->default(0);
            $table->unsignedInteger('clicks_count')->default(0);
            $table->timestamps();
            $table->index(['status','placement','weight']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_campaigns');
    }
};
