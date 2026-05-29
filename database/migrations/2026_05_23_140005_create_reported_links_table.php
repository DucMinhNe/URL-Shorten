<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reported_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('short_link_id')->constrained('short_links')->cascadeOnDelete();
            $table->foreignId('reporter_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reporter_email')->nullable();
            $table->string('reporter_ip', 45)->nullable();
            $table->enum('reason', ['spam', 'malware', 'phishing', 'inappropriate', 'copyright', 'scam', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'confirmed', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->enum('action_taken', ['none', 'warned', 'disabled_link', 'blacklisted_domain', 'banned_user'])->nullable();
            $table->timestamps();

            $table->index(['status', 'reason']);
            $table->index('short_link_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reported_links');
    }
};
