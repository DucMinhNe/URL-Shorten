<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name')->comment('Internal label');
            $table->string('description')->nullable();
            $table->enum('type', ['bonus_credit', 'payout_fee_waiver', 'rate_boost', 'welcome_bonus']);
            $table->unsignedInteger('value')->comment('VND amount or percent depending on type');
            $table->enum('value_unit', ['vnd', 'percent'])->default('vnd');
            $table->unsignedInteger('max_redemptions')->nullable()->comment('null = unlimited');
            $table->unsignedInteger('max_per_user')->default(1);
            $table->unsignedInteger('redeemed_count')->default(0);
            $table->unsignedInteger('min_balance_required')->default(0);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'valid_from', 'valid_until']);
        });

        Schema::create('promo_code_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('value_applied');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('redeemed_at')->useCurrent();

            $table->unique(['promo_code_id', 'user_id'], 'idx_promo_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_code_redemptions');
        Schema::dropIfExists('promo_codes');
    }
};
