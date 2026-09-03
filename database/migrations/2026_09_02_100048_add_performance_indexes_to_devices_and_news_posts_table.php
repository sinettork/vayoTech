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
        Schema::table('devices', function (Blueprint $table) {
            $table->index('release_date');
        });

        Schema::table('news_posts', function (Blueprint $table) {
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex(['release_date']);
        });

        Schema::table('news_posts', function (Blueprint $table) {
            $table->dropIndex(['published_at']);
        });
    }
};
