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
        Schema::create('ad_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('short_link_id')->constrained()->cascadeOnDelete();
            $table->foreignId('click_id')->nullable()->constrained()->nullOnDelete();
            $table->string('impression_token', 64)->index();
            $table->string('ip_address', 45);
            $table->boolean('was_clicked')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['ad_campaign_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_impressions');
    }
};
