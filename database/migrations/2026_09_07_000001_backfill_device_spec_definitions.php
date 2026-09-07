<?php

use App\Models\SpecDefinition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $definitions = SpecDefinition::query()
            ->get(['id', 'category', 'key', 'label'])
            ->keyBy(fn (SpecDefinition $definition): string => $this->lookupKey($definition->category, $definition->label));

        DB::table('device_specs')
            ->whereNull('spec_definition_id')
            ->orderBy('id')
            ->get(['id', 'category', 'spec_key', 'spec_value'])
            ->each(function (object $spec) use ($definitions): void {
                $definition = $definitions->get($this->lookupKey($spec->category, $spec->spec_key));

                if (!$definition) {
                    return;
                }

                $numericValue = null;
                if (in_array($definition->value_type, ['integer', 'decimal'], true)) {
                    if (preg_match('/-?\d+(?:\.\d+)?/', str_replace(',', '', (string) $spec->spec_value), $matches) === 1) {
                        $numericValue = (float) $matches[0];
                    }
                }

                $booleanValue = null;
                if ($definition->value_type === 'boolean') {
                    $booleanValue = match (strtolower(trim((string) $spec->spec_value))) {
                        'yes', 'true', '1', 'supported', 'present' => 1,
                        'no', 'false', '0', 'not supported', 'absent' => 0,
                        default => null,
                    };
                }

                DB::table('device_specs')
                    ->where('id', $spec->id)
                    ->update([
                        'spec_definition_id' => $definition->id,
                        'category' => $definition->category,
                        'spec_key' => $definition->label,
                        'numeric_value' => $numericValue,
                        'boolean_value' => $booleanValue,
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('device_specs')->update([
            'spec_definition_id' => null,
            'numeric_value' => null,
            'boolean_value' => null,
        ]);
    }

    private function lookupKey(string $category, string $label): string
    {
        return strtolower(trim($category)) . '|' . strtolower(trim($label));
    }
};
