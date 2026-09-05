<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_data_sources', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->foreignId('data_source_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('external_url')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['data_source_id', 'external_id']);
            $table->index(['device_id', 'data_source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_data_sources');
    }
};
