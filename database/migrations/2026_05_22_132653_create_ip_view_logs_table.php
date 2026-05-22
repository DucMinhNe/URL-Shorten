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
        Schema::create('ip_view_logs', function (Blueprint $table) {
            $table->foreignId('short_link_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->timestamp('viewed_at')->useCurrent();
            $table->primary(['short_link_id','ip_address','viewed_at']);
            $table->index('viewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_view_logs');
    }
};
