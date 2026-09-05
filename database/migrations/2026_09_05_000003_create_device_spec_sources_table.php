<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_spec_sources', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('device_spec_id')->constrained()->cascadeOnDelete();
            $table->foreignId('data_source_id')->constrained()->cascadeOnDelete();
            $table->text('source_value')->nullable();
            $table->string('source_url')->nullable();
            $table->string('verification_status')->default('unverified');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();

            $table->index(['device_spec_id', 'verification_status']);
            $table->index(['data_source_id', 'verification_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_spec_sources');
    }
};
