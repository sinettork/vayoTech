<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('ram');
            $table->string('storage');
            $table->string('storage_type')->nullable();
            $table->string('model_code')->nullable();
            $table->string('market')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['device_id', 'ram', 'storage', 'model_code', 'market'], 'device_variant_unique');
            $table->index(['device_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_variants');
    }
};
