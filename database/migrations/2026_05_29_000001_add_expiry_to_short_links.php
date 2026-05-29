<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('short_links', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('status');
            $table->unsignedInteger('max_clicks')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('short_links', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'max_clicks']);
        });
    }
};
