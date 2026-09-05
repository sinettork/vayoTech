<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spec_definitions', function (Blueprint $table): void {
            $table->id();
            $table->string('category');
            $table->string('key');
            $table->string('label');
            $table->string('value_type')->default('text');
            $table->string('unit')->nullable();
            $table->boolean('filterable')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['category', 'key']);
            $table->index(['filterable', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spec_definitions');
    }
};
