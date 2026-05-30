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
        Schema::create('captcha_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->string('image', 500)->nullable();   // ảnh minh hoạ tuỳ chọn
            $table->json('options');                     // [{text, correct}]
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('shown_count')->default(0);
            $table->timestamps();
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captcha_questions');
    }
};
