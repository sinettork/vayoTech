<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_specs', function (Blueprint $table): void {
            $table->foreignId('spec_definition_id')
                ->nullable()
                ->after('device_id')
                ->constrained('spec_definitions')
                ->nullOnDelete();

            $table->decimal('numeric_value', 12, 3)->nullable()->after('spec_value');
            $table->boolean('boolean_value')->nullable()->after('numeric_value');

            $table->index(['spec_definition_id', 'numeric_value']);
            $table->index(['spec_definition_id', 'boolean_value']);
        });
    }

    public function down(): void
    {
        Schema::table('device_specs', function (Blueprint $table): void {
            $table->dropForeign(['spec_definition_id']);
            $table->dropIndex(['spec_definition_id', 'numeric_value']);
            $table->dropIndex(['spec_definition_id', 'boolean_value']);
            $table->dropColumn(['spec_definition_id', 'numeric_value', 'boolean_value']);
        });
    }
};
