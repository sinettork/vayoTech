<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $definitions = [
            ['Network', 'network_technology', 'Network technology', 'text', null, true, 10],
            ['Launch', 'launch_announced', 'Announced', 'text', null, false, 10],
            ['Launch', 'launch_status', 'Status', 'text', null, true, 20],
            ['Body', 'body_dimensions', 'Dimensions', 'text', null, false, 10],
            ['Body', 'body_weight', 'Weight', 'decimal', 'g', true, 20],
            ['Body', 'body_build', 'Build', 'text', null, false, 30],
            ['Body', 'body_ip_rating', 'Water and dust resistance', 'text', null, true, 40],
            ['Body', 'body_headphone_jack', 'Headphone jack', 'boolean', null, true, 50],
            ['Display', 'display_type', 'Display type', 'text', null, true, 10],
            ['Display', 'display_size', 'Display size', 'decimal', 'in', true, 20],
            ['Display', 'display_resolution', 'Resolution', 'text', null, true, 30],
            ['Display', 'display_refresh_rate', 'Refresh rate', 'integer', 'Hz', true, 40],
            ['Display', 'display_brightness', 'Brightness', 'integer', 'nits', true, 50],
            ['Display', 'display_hdr', 'HDR', 'boolean', null, true, 60],
            ['Platform', 'platform_os', 'Operating system', 'text', null, true, 10],
            ['Platform', 'platform_chipset', 'Chipset', 'text', null, true, 20],
            ['Platform', 'platform_cpu', 'CPU', 'text', null, false, 30],
            ['Platform', 'platform_gpu', 'GPU', 'text', null, false, 40],
            ['Memory', 'memory_ram', 'RAM', 'integer', 'GB', true, 10],
            ['Memory', 'memory_storage', 'Internal storage', 'integer', 'GB', true, 20],
            ['Memory', 'memory_storage_type', 'Storage technology', 'text', null, true, 30],
            ['Memory', 'memory_card_slot', 'Memory card slot', 'boolean', null, true, 40],
            ['Main Camera', 'main_camera_setup', 'Main camera setup', 'text', null, false, 10],
            ['Main Camera', 'main_camera_video', 'Main camera video', 'text', null, false, 20],
            ['Selfie Camera', 'selfie_camera_setup', 'Selfie camera setup', 'text', null, false, 10],
            ['Selfie Camera', 'selfie_camera_video', 'Selfie camera video', 'text', null, false, 20],
            ['Sound', 'sound_loudspeaker', 'Loudspeaker', 'text', null, false, 10],
            ['Sound', 'sound_headphone_out', '3.5 mm headphone output', 'boolean', null, true, 20],
            ['Comms', 'comms_wifi', 'Wi-Fi', 'text', null, true, 10],
            ['Comms', 'comms_bluetooth', 'Bluetooth', 'text', null, false, 20],
            ['Comms', 'comms_nfc', 'NFC', 'boolean', null, true, 30],
            ['Comms', 'comms_usb', 'USB', 'text', null, false, 40],
            ['Features', 'features_sensors', 'Sensors', 'text', null, false, 10],
            ['Battery', 'battery_type', 'Battery type', 'text', null, false, 10],
            ['Battery', 'battery_capacity', 'Battery capacity', 'integer', 'mAh', true, 20],
            ['Battery', 'battery_charging', 'Charging', 'text', null, true, 30],
            ['Battery', 'battery_wireless', 'Wireless charging', 'boolean', null, true, 40],
        ];

        foreach ($definitions as [$category, $key, $label, $valueType, $unit, $filterable, $sortOrder]) {
            DB::table('spec_definitions')->updateOrInsert(
                ['category' => $category, 'key' => $key],
                [
                    'label' => $label,
                    'value_type' => $valueType,
                    'unit' => $unit,
                    'filterable' => $filterable,
                    'sort_order' => $sortOrder,
                    'active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('spec_definitions')->whereIn('key', [
            'network_technology', 'launch_announced', 'launch_status',
            'body_dimensions', 'body_weight', 'body_build', 'body_ip_rating', 'body_headphone_jack',
            'display_type', 'display_size', 'display_resolution', 'display_refresh_rate', 'display_brightness', 'display_hdr',
            'platform_os', 'platform_chipset', 'platform_cpu', 'platform_gpu',
            'memory_ram', 'memory_storage', 'memory_storage_type', 'memory_card_slot',
            'main_camera_setup', 'main_camera_video', 'selfie_camera_setup', 'selfie_camera_video',
            'sound_loudspeaker', 'sound_headphone_out',
            'comms_wifi', 'comms_bluetooth', 'comms_nfc', 'comms_usb',
            'features_sensors', 'battery_type', 'battery_capacity', 'battery_charging', 'battery_wireless',
        ])->delete();
    }
};
