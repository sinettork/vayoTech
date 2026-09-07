<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceSpec;
use App\Models\SpecDefinition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DeviceSpecSyncService
{
    /**
     * Synchronize a device's specifications using canonical definitions.
     *
     * Each submitted row must contain a valid active spec_definition_id and
     * a human-readable spec_value. Legacy category/spec_key fields are derived
     * from the definition so they cannot drift from the canonical schema.
     */
    public function sync(Device $device, array $specs): void
    {
        DB::transaction(function () use ($device, $specs): void {
            $definitionIds = collect($specs)
                ->map(fn (array $spec): int => (int) ($spec['definition_id'] ?? 0))
                ->filter()
                ->values();

            if ($definitionIds->count() !== $definitionIds->unique()->count()) {
                throw new InvalidArgumentException('Each specification definition can only be added once per device.');
            }

            $definitions = SpecDefinition::query()
                ->whereIn('id', $definitionIds)
                ->where('active', true)
                ->get()
                ->keyBy('id');

            if ($definitions->count() !== $definitionIds->unique()->count()) {
                throw new InvalidArgumentException('One or more selected specification definitions are invalid or inactive.');
            }

            $existing = $device->specs()
                ->whereIn('spec_definition_id', $definitionIds)
                ->get()
                ->keyBy('spec_definition_id');

            $keptIds = [];

            foreach (array_values($specs) as $index => $spec) {
                $definitionId = (int) $spec['definition_id'];
                $definition = $definitions->get($definitionId);
                $value = trim((string) ($spec['spec_value'] ?? ''));

                if ($value === '') {
                    throw new InvalidArgumentException("Specification value is required for {$definition->label}.");
                }

                $payload = [
                    'spec_definition_id' => $definition->id,
                    'category' => $definition->category,
                    'spec_key' => $definition->label,
                    'spec_value' => $value,
                    'numeric_value' => in_array($definition->value_type, ['integer', 'decimal'], true)
                        ? $this->numericValue($value)
                        : null,
                    'boolean_value' => $definition->value_type === 'boolean'
                        ? $this->booleanValue($value)
                        : null,
                    'sort_order' => $index + 1,
                ];

                $deviceSpec = $existing->get($definitionId);

                if ($deviceSpec) {
                    $deviceSpec->update($payload);
                } else {
                    $deviceSpec = $device->specs()->create($payload);
                }

                $keptIds[] = $deviceSpec->id;
            }

            $device->specs()
                ->when(
                    count($keptIds) > 0,
                    fn ($query) => $query->whereNotIn('id', $keptIds),
                    fn ($query) => $query
                )
                ->delete();
        });
    }

    private function numericValue(string $value): ?float
    {
        if (preg_match('/-?\d+(?:\.\d+)?/', str_replace(',', '', $value), $matches) !== 1) {
            return null;
        }

        return (float) $matches[0];
    }

    private function booleanValue(string $value): ?bool
    {
        return match (Str::lower(trim($value))) {
            'yes', 'true', '1', 'supported', 'present' => true,
            'no', 'false', '0', 'not supported', 'absent' => false,
            default => null,
        };
    }
}
