<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 16)->nullable()->unique()->after('preferred_locale');
            $table->foreignId('referred_by')->nullable()->after('referral_code')->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('referral_earned')->default(0)->after('referred_by');
            $table->boolean('is_premium')->default(false)->after('is_admin');
            $table->timestamp('premium_until')->nullable()->after('is_premium');
        });

        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by');
            $table->dropColumn(['referral_code', 'referral_earned', 'is_premium', 'premium_until']);
        });
    }
};
