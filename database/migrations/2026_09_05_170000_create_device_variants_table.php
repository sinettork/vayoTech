<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();

            // Bounded lengths keep the composite unique index below
            // MySQL's 3072-byte utf8mb4 index limit.
            $table->string('ram', 50);
            $table->string('storage', 50);
            $table->string('storage_type', 50)->nullable();
            $table->string('model_code', 100)->nullable();
            $table->string('market', 50)->nullable();

            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(
                ['device_id', 'ram', 'storage', 'model_code', 'market'],
                'device_variant_unique'
            );

            $table->index(['device_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_variants');
    }
};
