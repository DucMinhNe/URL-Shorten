<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 16)->default('slate');
            $table->string('icon', 64)->nullable();
            $table->string('description')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('is_featured');
        });

        Schema::create('short_link_tag', function (Blueprint $table) {
            $table->foreignId('short_link_id')->constrained('short_links')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['short_link_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('short_link_tag');
        Schema::dropIfExists('tags');
    }
};
