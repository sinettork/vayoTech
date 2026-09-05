<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\DataSource;
use App\Models\DeviceSpec;
use App\Models\SpecDefinition;
use App\Models\Device;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeviceSeeder extends Seeder
{
    /**
     * Local development dataset: 100 real-world phone models across 21 brands.
     * Values are researched public specification data and remain unverified.
     */
    public function run(): void
    {
        $source = DataSource::updateOrCreate(
            ['name' => 'VayoTech researched seed — 100 phones'],
            ['type' => 'manual', 'url' => null, 'trust_level' => 3, 'active' => true]
        );

        $brands = [
            'Apple' => 'apple.com', 'Samsung' => 'samsung.com', 'Xiaomi' => 'mi.com',
            'Redmi' => 'mi.com', 'Poco' => 'po.co', 'OnePlus' => 'oneplus.com',
            'Google' => 'store.google.com', 'Sony' => 'sony.com', 'Motorola' => 'motorola.com',
            'Nothing' => 'nothing.tech', 'Oppo' => 'oppo.com', 'Vivo' => 'vivo.com',
            'Realme' => 'realme.com', 'Honor' => 'honor.com', 'Huawei' => 'consumer.huawei.com',
            'Asus' => 'asus.com', 'ZTE' => 'zte.com.cn', 'Tecno' => 'tecno-mobile.com',
            'Infinix' => 'infinixmobility.com', 'Meizu' => 'meizu.com', 'Sharp' => 'global.sharp',
        ];

        foreach ($brands as $name => $domain) {
            Brand::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'brand_domain' => $domain]);
        }

        $definitions = SpecDefinition::where('active', true)->get()->keyBy('key');

        // brand|name|date|display|resolution|refresh|chipset|ram|storage|main camera|selfie|battery|charging|weight|ip|nfc|headphone jack
        $rows = <<<'DATA'
Apple|iPhone 16 Pro|2024-09-20|6.3|2622 x 1206|120|A18 Pro|8|128/256/512/1024|48 MP + 48 MP ultrawide + 12 MP 5x telephoto|12 MP|3582|20W wired / 25W MagSafe|199|IP68|1|0
Apple|iPhone 16|2024-09-20|6.1|2556 x 1179|60|A18|8|128/256/512|48 MP + 12 MP ultrawide|12 MP|3561|20W wired / 25W MagSafe|170|IP68|1|0
Apple|iPhone 16 Plus|2024-09-20|6.7|2796 x 1290|60|A18|8|128/256/512|48 MP + 12 MP ultrawide|12 MP|4674|20W wired / 25W MagSafe|199|IP68|1|0
Apple|iPhone 15 Pro|2023-09-22|6.1|2556 x 1179|120|A17 Pro|8|128/256/512/1024|48 MP + 12 MP ultrawide + 12 MP 3x telephoto|12 MP|3274|20W wired / 15W MagSafe|187|IP68|1|0
Apple|iPhone 15|2023-09-22|6.1|2556 x 1179|60|A16 Bionic|6|128/256/512|48 MP + 12 MP ultrawide|12 MP|3349|20W wired / 15W MagSafe|171|IP68|1|0
Samsung|Galaxy S25 Ultra|2025-02-07|6.9|3120 x 1440|120|Snapdragon 8 Elite for Galaxy|12|256/512/1024|200 MP + 50 MP 5x + 10 MP 3x + 50 MP ultrawide|12 MP|5000|45W wired / 15W wireless|218|IP68|1|0
Samsung|Galaxy S25+|2025-02-07|6.7|3120 x 1440|120|Snapdragon 8 Elite for Galaxy|12|256/512|50 MP + 10 MP 3x + 12 MP ultrawide|12 MP|4900|45W wired / 15W wireless|190|IP68|1|0
Samsung|Galaxy S25|2025-02-07|6.2|2340 x 1080|120|Snapdragon 8 Elite for Galaxy|12|128/256/512|50 MP + 10 MP 3x + 12 MP ultrawide|12 MP|4000|25W wired / 15W wireless|162|IP68|1|0
Samsung|Galaxy S24 Ultra|2024-01-31|6.8|3120 x 1440|120|Snapdragon 8 Gen 3 for Galaxy|12|256/512/1024|200 MP + 50 MP 5x + 10 MP 3x + 12 MP ultrawide|12 MP|5000|45W wired / 15W wireless|232|IP68|1|0
Samsung|Galaxy S24|2024-01-31|6.2|2340 x 1080|120|Snapdragon 8 Gen 3 for Galaxy|8|128/256/512|50 MP + 10 MP 3x + 12 MP ultrawide|12 MP|4000|25W wired / 15W wireless|167|IP68|1|0
Samsung|Galaxy Z Fold6|2024-07-24|7.6|2160 x 1856|120|Snapdragon 8 Gen 3 for Galaxy|12|256/512/1024|50 MP + 10 MP 3x + 12 MP ultrawide|10 MP|4400|25W wired / 15W wireless|239|IP48|1|0
Samsung|Galaxy Z Flip6|2024-07-24|6.7|2640 x 1080|120|Snapdragon 8 Gen 3 for Galaxy|12|256/512|50 MP + 12 MP ultrawide|10 MP|4000|25W wired / 15W wireless|187|IP48|1|0
Samsung|Galaxy A55 5G|2024-03-20|6.6|2340 x 1080|120|Exynos 1480|8|128/256|50 MP + 12 MP ultrawide + 5 MP macro|32 MP|5000|25W wired|213|IP67|1|0
Samsung|Galaxy A35 5G|2024-03-20|6.6|2340 x 1080|120|Exynos 1380|6/8|128/256|50 MP + 8 MP ultrawide + 5 MP macro|13 MP|5000|25W wired|209|IP67|1|0
Samsung|Galaxy A25 5G|2023-12-27|6.5|2340 x 1080|120|Exynos 1280|6/8|128/256|50 MP OIS + 8 MP ultrawide + 2 MP macro|13 MP|5000|25W wired|197|—|1|1
Samsung|Galaxy S23 Ultra|2023-02-17|6.8|3088 x 1440|120|Snapdragon 8 Gen 2 for Galaxy|8/12|256/512/1024|200 MP + 10 MP 10x + 10 MP 3x + 12 MP ultrawide|12 MP|5000|45W wired / 15W wireless|234|IP68|1|0
Xiaomi|Xiaomi 15 Ultra|2025-03-02|6.73|3200 x 1440|120|Snapdragon 8 Elite|16|512/1024|50 MP 1-inch + 200 MP 4.3x periscope + 50 MP 2x + 50 MP ultrawide|32 MP|5410|90W wired / 80W wireless|226|IP68|1|0
Xiaomi|Xiaomi 15|2025-03-02|6.36|2670 x 1200|120|Snapdragon 8 Elite|12|256/512|50 MP + 50 MP 2.6x telephoto + 50 MP ultrawide|32 MP|5240|90W wired / 50W wireless|191|IP68|1|0
Xiaomi|Xiaomi 14 Ultra|2024-03-07|6.73|3200 x 1440|120|Snapdragon 8 Gen 3|12/16|512|50 MP 1-inch + 50 MP 3.2x + 50 MP 5x + 50 MP ultrawide|32 MP|5000|90W wired / 80W wireless|219.8|IP68|1|0
Xiaomi|Xiaomi 14|2024-02-26|6.36|2670 x 1200|120|Snapdragon 8 Gen 3|12|256/512|50 MP + 75mm 3.2x telephoto + 50 MP ultrawide|32 MP|4610|90W wired / 50W wireless|193|IP68|1|0
Xiaomi|Xiaomi 13T Pro|2023-09-26|6.67|2712 x 1220|144|Dimensity 9200+|12/16|256/512/1024|50 MP + 50 MP 2x telephoto + 12 MP ultrawide|20 MP|5000|120W wired|200|IP68|1|0
Xiaomi|Xiaomi 13T|2023-09-26|6.67|2712 x 1220|144|Dimensity 8200 Ultra|8/12|256|50 MP + 50 MP 2x telephoto + 12 MP ultrawide|20 MP|5000|67W wired|193|IP68|1|0
Xiaomi|Xiaomi 13|2023-02-26|6.36|2400 x 1080|120|Snapdragon 8 Gen 2|8/12|128/256|50 MP + 10 MP 3.2x telephoto + 12 MP ultrawide|32 MP|4500|67W wired / 50W wireless|189|IP68|1|0
Xiaomi|Xiaomi 12T Pro|2022-10-13|6.67|2712 x 1220|120|Snapdragon 8+ Gen 1|8/12|128/256|200 MP + 8 MP ultrawide + 2 MP macro|20 MP|5000|120W wired|205|—|1|0
Redmi|Redmi Note 14 Pro+ 5G|2025-01-15|6.67|2712 x 1220|120|Snapdragon 7s Gen 3|8/12|256/512|200 MP OIS + 8 MP ultrawide + 2 MP macro|20 MP|5110|120W wired|210|IP68|1|0
Redmi|Redmi Note 14 Pro 5G|2025-01-15|6.67|2712 x 1220|120|Dimensity 7300 Ultra|8/12|256/512|200 MP OIS + 8 MP ultrawide + 2 MP macro|20 MP|5110|45W wired|190|IP68|1|0
Redmi|Redmi Note 13 Pro+ 5G|2023-09-21|6.67|2712 x 1220|120|Dimensity 7200 Ultra|8/12/16|256/512|200 MP OIS + 8 MP ultrawide + 2 MP macro|16 MP|5000|120W wired|199|IP68|1|0
Redmi|Redmi Note 13 Pro 5G|2024-01-15|6.67|2712 x 1220|120|Snapdragon 7s Gen 2|8/12|128/256/512|200 MP OIS + 8 MP ultrawide + 2 MP macro|16 MP|5100|67W wired|188|IP54|1|0
Redmi|Redmi Note 12 Pro+ 5G|2022-10-27|6.67|2400 x 1080|120|Dimensity 1080|8/12|256|200 MP OIS + 8 MP ultrawide + 2 MP macro|16 MP|5000|120W wired|210|IP53|1|0
Poco|Poco F7 Ultra|2025-03-27|6.67|3200 x 1440|120|Snapdragon 8 Elite|12/16|256/512|50 MP + 50 MP 2.5x telephoto + 32 MP ultrawide|32 MP|5300|120W wired / 50W wireless|212|IP68|1|0
Poco|Poco F7 Pro|2025-03-27|6.67|3200 x 1440|120|Snapdragon 8 Gen 3|12|256/512|50 MP OIS + 8 MP ultrawide|20 MP|6000|90W wired|206|IP68|1|0
Poco|Poco X7 Pro|2025-01-09|6.67|2712 x 1220|120|Dimensity 8400 Ultra|8/12|256/512|50 MP OIS + 8 MP ultrawide|20 MP|6000|90W wired|195|IP68|1|0
Poco|Poco F6|2024-05-23|6.67|2712 x 1220|120|Snapdragon 8s Gen 3|8/12|256/512|50 MP OIS + 8 MP ultrawide|20 MP|5000|90W wired|179|IP64|1|0
Poco|Poco X6 Pro|2024-01-11|6.67|2712 x 1220|120|Dimensity 8300 Ultra|8/12|256/512|64 MP OIS + 8 MP ultrawide + 2 MP macro|16 MP|5000|67W wired|186|IP54|1|0
OnePlus|OnePlus 13|2025-01-07|6.82|3168 x 1440|120|Snapdragon 8 Elite|12/16|256/512/1024|50 MP + 50 MP 3x periscope + 50 MP ultrawide|32 MP|6000|100W wired / 50W wireless|210|IP68|1|0
OnePlus|OnePlus 13R|2025-01-07|6.78|2780 x 1264|120|Snapdragon 8 Gen 3|12|256|50 MP + 50 MP 2x telephoto + 8 MP ultrawide|16 MP|6000|80W wired|206|IP65|1|0
OnePlus|OnePlus 12|2024-01-23|6.82|3168 x 1440|120|Snapdragon 8 Gen 3|12/16/24|256/512/1024|50 MP + 64 MP 3x periscope + 48 MP ultrawide|32 MP|5400|100W wired / 50W wireless|220|IP65|1|0
OnePlus|OnePlus 12R|2024-02-06|6.78|2780 x 1264|120|Snapdragon 8 Gen 2|8/16|128/256|50 MP + 8 MP ultrawide + 2 MP macro|16 MP|5500|100W wired|207|IP65|1|0
OnePlus|OnePlus Nord 4|2024-08-08|6.74|2772 x 1240|120|Snapdragon 7+ Gen 3|8/12/16|128/256/512|50 MP OIS + 8 MP ultrawide|16 MP|5500|100W wired|199.5|IP65|1|0
Google|Pixel 9 Pro XL|2024-08-22|6.8|2992 x 1344|120|Tensor G4|16|128/256/512/1024|50 MP + 48 MP 5x telephoto + 48 MP ultrawide|42 MP|5060|37W wired / 23W wireless|221|IP68|1|0
Google|Pixel 9 Pro|2024-09-04|6.3|2856 x 1280|120|Tensor G4|16|128/256/512|50 MP + 48 MP 5x telephoto + 48 MP ultrawide|42 MP|4700|27W wired / 21W wireless|199|IP68|1|0
Google|Pixel 9|2024-08-22|6.3|2424 x 1080|120|Tensor G4|12|128/256|50 MP + 48 MP ultrawide|10.5 MP|4700|27W wired / 15W wireless|198|IP68|1|0
Google|Pixel 8 Pro|2023-10-12|6.7|2992 x 1344|120|Tensor G3|12|128/256/512/1024|50 MP + 48 MP 5x telephoto + 48 MP ultrawide|10.5 MP|5050|30W wired / 23W wireless|213|IP68|1|0
Google|Pixel 8|2023-10-12|6.2|2400 x 1080|120|Tensor G3|8|128/256|50 MP + 12 MP ultrawide|10.5 MP|4575|27W wired / 18W wireless|187|IP68|1|0
Sony|Xperia 1 VI|2024-06-07|6.5|2340 x 1080|120|Snapdragon 8 Gen 3|12/16|256/512|48 MP + 12 MP 3.5x-7.1x telephoto + 12 MP ultrawide|12 MP|5000|30W wired / wireless|192|IP65/IP68|1|1
Sony|Xperia 5 V|2023-09-27|6.1|2520 x 1080|120|Snapdragon 8 Gen 2|8|128|48 MP + 12 MP ultrawide|12 MP|5000|30W wired / wireless|182|IP65/IP68|1|1
Sony|Xperia 1 V|2023-06-11|6.5|2560 x 1096|120|Snapdragon 8 Gen 2|12/16|256/512|48 MP + 12 MP 3.5x-5.2x telephoto + 12 MP ultrawide|12 MP|5000|30W wired / wireless|187|IP65/IP68|1|1
Sony|Xperia 10 VI|2024-06-01|6.1|2520 x 1080|60|Snapdragon 6 Gen 1|8|128|48 MP + 8 MP ultrawide|8 MP|5000|30W wired|164|IP65/IP68|1|1
Motorola|Edge 50 Ultra|2024-06-17|6.67|2712 x 1220|144|Snapdragon 8s Gen 3|12/16|512/1024|50 MP + 64 MP 3x telephoto + 50 MP ultrawide|50 MP|4500|125W wired / 50W wireless|197|IP68|1|0
Motorola|Edge 50 Pro|2024-04-16|6.7|2712 x 1220|144|Snapdragon 7 Gen 3|8/12|256/512|50 MP OIS + 10 MP 3x telephoto + 13 MP ultrawide|50 MP|4500|125W wired / 50W wireless|186|IP68|1|0
Motorola|Edge 50 Fusion|2024-05-16|6.7|2400 x 1080|144|Snapdragon 7s Gen 2|8/12|128/256/512|50 MP OIS + 13 MP ultrawide|32 MP|5000|68W wired|174.9|IP68|1|0
Motorola|Razr 50 Ultra|2024-07-10|6.9|2640 x 1080|165|Snapdragon 8s Gen 3|12|512|50 MP + 50 MP 2x telephoto|32 MP|4000|45W wired / 15W wireless|189|IPX8|1|0
Motorola|ThinkPhone 25|2024-10-28|6.36|2670 x 1220|120|Dimensity 7300|8|256|50 MP OIS + 13 MP ultrawide + 10 MP 3x telephoto|32 MP|4310|68W wired|171|IP68|1|0
Nothing|Phone (3a) Pro|2025-03-11|6.77|2392 x 1080|120|Snapdragon 7s Gen 3|8/12|128/256|50 MP + 50 MP 2.5x periscope + 8 MP ultrawide|50 MP|5000|50W wired|211|IP64|1|0
Nothing|Phone (3a)|2025-03-11|6.77|2392 x 1080|120|Snapdragon 7s Gen 3|8/12|128/256|50 MP + 50 MP 2x telephoto + 8 MP ultrawide|32 MP|5000|50W wired|201|IP64|1|0
Nothing|Phone (2)|2023-07-17|6.7|2412 x 1084|120|Snapdragon 8+ Gen 1|8/12/16|128/256/512|50 MP OIS + 50 MP ultrawide|32 MP|4700|45W wired / 15W wireless|201|IP54|1|0
Nothing|Phone (2a)|2024-03-12|6.7|2412 x 1084|120|Dimensity 7200 Pro|8/12|128/256|50 MP + 50 MP ultrawide|32 MP|5000|45W wired|190|IP54|1|0
Oppo|Find X8 Pro|2024-11-21|6.78|2780 x 1264|120|Dimensity 9400|12/16|256/512|50 MP + 50 MP 3x + 50 MP 6x + 50 MP ultrawide|32 MP|5910|80W wired / 50W wireless|215|IP68/IP69|1|0
Oppo|Find X8|2024-11-21|6.59|2760 x 1256|120|Dimensity 9400|12/16|256/512|50 MP + 50 MP 3x telephoto + 50 MP ultrawide|32 MP|5630|80W wired / 50W wireless|193|IP68/IP69|1|0
Oppo|Reno 13 Pro|2025-01-25|6.83|2800 x 1272|120|Dimensity 8350|12|256/512|50 MP OIS + 50 MP 3.5x telephoto + 8 MP ultrawide|50 MP|5800|80W wired|195|IP69|1|0
Oppo|Reno 12 Pro|2024-06-18|6.7|2412 x 1080|120|Dimensity 7300-Energy|12|256/512|50 MP OIS + 50 MP telephoto + 8 MP ultrawide|50 MP|5000|80W wired|183|IP65|1|0
Oppo|Reno 11 Pro|2023-11-23|6.7|2412 x 1080|120|Dimensity 8200|12|256|50 MP + 32 MP 2x telephoto + 8 MP ultrawide|32 MP|4600|80W wired|181|IP65|1|0
Oppo|A5 Pro 5G|2025-01-09|6.67|2400 x 1080|120|Dimensity 6300|8|256|50 MP + 2 MP|8 MP|5800|45W wired|194|IP69|1|0
Vivo|X200 Pro|2024-10-14|6.78|2800 x 1260|120|Dimensity 9400|12/16|256/512|50 MP + 200 MP 3.7x periscope + 50 MP ultrawide|32 MP|6000|90W wired / 30W wireless|228|IP68/IP69|1|0
Vivo|X200|2024-10-14|6.67|2800 x 1260|120|Dimensity 9400|12/16|256/512|50 MP + 50 MP 3x telephoto + 50 MP ultrawide|32 MP|5800|90W wired|197|IP68/IP69|1|0
Vivo|V40 Pro|2024-08-07|6.78|2800 x 1260|120|Dimensity 9200+|8/12|256/512|50 MP + 50 MP 2x telephoto + 50 MP ultrawide|50 MP|5500|80W wired|192|IP68|1|0
Vivo|V40|2024-08-07|6.78|2800 x 1260|120|Snapdragon 7 Gen 3|8/12|256/512|50 MP OIS + 50 MP ultrawide|50 MP|5500|80W wired|190|IP68|1|0
Vivo|V30 Pro|2024-03-07|6.78|2800 x 1260|120|Dimensity 8200|8/12|256/512|50 MP + 50 MP 2x telephoto + 50 MP ultrawide|50 MP|5000|80W wired|187|IP54|1|0
Vivo|V30|2024-02-28|6.78|2800 x 1260|120|Snapdragon 7 Gen 3|8/12|128/256/512|50 MP OIS + 8 MP ultrawide|50 MP|5000|80W wired|186|IP54|1|0
Realme|GT 7 Pro|2024-11-19|6.78|2780 x 1264|120|Snapdragon 8 Elite|12/16|256/512|50 MP + 50 MP 3x periscope + 8 MP ultrawide|16 MP|6500|120W wired|222.8|IP68/IP69|1|0
Realme|GT 6|2024-06-20|6.78|2780 x 1264|120|Snapdragon 8s Gen 3|8/12/16|256/512|50 MP OIS + 50 MP 2x telephoto + 8 MP ultrawide|32 MP|5500|120W wired|199|IP65|1|0
Realme|12 Pro+|2024-01-29|6.7|2412 x 1080|120|Snapdragon 7s Gen 2|8/12|128/256|50 MP + 64 MP 3x periscope + 8 MP ultrawide|32 MP|5000|67W wired|196|IP65|1|0
Realme|12 Pro|2024-01-29|6.7|2412 x 1080|120|Snapdragon 6 Gen 1|8|128/256|50 MP OIS + 32 MP 2x telephoto + 8 MP ultrawide|16 MP|5000|67W wired|190|IP65|1|0
Realme|GT Neo 6 SE|2024-04-11|6.78|2780 x 1264|120|Snapdragon 7+ Gen 3|8/12/16|256/512|50 MP OIS + 8 MP ultrawide|32 MP|5500|100W wired|191|IP65|1|0
Honor|Magic7 Pro|2025-01-15|6.8|2800 x 1280|120|Snapdragon 8 Elite|12/16|512/1024|50 MP + 200 MP 3x periscope + 50 MP ultrawide|50 MP|5850|100W wired / 80W wireless|223|IP68/IP69|1|0
Honor|Magic6 Pro|2024-03-18|6.8|2800 x 1280|120|Snapdragon 8 Gen 3|12/16|256/512/1024|50 MP + 180 MP 2.5x periscope + 50 MP ultrawide|50 MP|5600|80W wired / 66W wireless|225|IP68|1|0
Honor|200 Pro|2024-05-27|6.78|2700 x 1224|120|Snapdragon 8s Gen 3|12|512|50 MP + 50 MP 2.5x telephoto + 12 MP ultrawide|50 MP|5200|100W wired / 66W wireless|199|IP65|1|0
Honor|90|2023-07-06|6.7|2664 x 1200|120|Snapdragon 7 Gen 1 Accelerated Edition|8/12|256/512|200 MP + 12 MP ultrawide + 2 MP depth|50 MP|5000|66W wired|183|—|1|0
Honor|X8b|2024-01-11|6.7|2412 x 1080|90|Snapdragon 680|8|128/256/512|108 MP + 5 MP ultrawide + 2 MP macro|50 MP|4500|35W wired|166|—|1|0
Huawei|Pura 70 Ultra|2024-04-18|6.8|2844 x 1260|120|Kirin 9010|16|512/1024|50 MP 1-inch + 40 MP ultrawide + 50 MP 3.5x telephoto|13 MP|5050|100W wired / 80W wireless|226|IP68|1|0
Huawei|Mate 70 Pro|2024-11-26|6.9|2832 x 1316|120|Kirin 9020|12|256/512/1024|50 MP + 48 MP 3.5x telephoto + 40 MP ultrawide|13 MP|5500|100W wired / 80W wireless|221|IP69|1|0
Huawei|P60 Pro|2023-04-06|6.67|2700 x 1220|120|Snapdragon 8+ Gen 1 4G|8/12|256/512|48 MP variable aperture + 48 MP 3.5x telephoto + 13 MP ultrawide|13 MP|4815|88W wired / 50W wireless|197|IP68|1|0
Huawei|Nova 12 Pro|2024-01-05|6.76|2776 x 1224|120|Kirin 8000|12|256/512|50 MP + 8 MP ultrawide|60 MP + 8 MP|4600|100W wired|201|IP65|1|0
Asus|ROG Phone 9 Pro|2024-11-19|6.78|2400 x 1080|185|Snapdragon 8 Elite|16/24|512/1024|50 MP gimbal OIS + 32 MP 3x telephoto + 13 MP ultrawide|32 MP|5800|65W wired|227|IP68|1|0
Asus|Zenfone 11 Ultra|2024-03-14|6.78|2400 x 1080|144|Snapdragon 8 Gen 3|12/16|256/512|50 MP gimbal OIS + 32 MP 3x telephoto + 13 MP ultrawide|32 MP|5500|65W wired / 15W wireless|225|IP65|1|0
Asus|ROG Phone 8 Pro|2024-01-18|6.78|2400 x 1080|165|Snapdragon 8 Gen 3|16/24|512/1024|50 MP gimbal OIS + 32 MP 3x telephoto + 13 MP ultrawide|32 MP|5500|65W wired|225|IP68|1|0
ZTE|nubia Z70 Ultra|2024-11-21|6.85|2688 x 1216|144|Snapdragon 8 Elite|12/16/24|256/512/1024|50 MP variable aperture + 64 MP 2.7x periscope + 50 MP ultrawide|16 MP|6150|80W wired|228|IP68|1|0
ZTE|Axon 60 Ultra|2024-05-09|6.78|2460 x 1116|120|Unisoc T820|12|512|50 MP + 50 MP ultrawide + 32 MP|32 MP|6000|80W wired|—|IP68|1|0
ZTE|Axon 50 Ultra|2023-04-26|6.67|2400 x 1080|144|Snapdragon 8+ Gen 1|8/12|256/512|64 MP OIS + 50 MP ultrawide + 50 MP telephoto|16 MP|5000|80W wired|197|IP68|1|0
Tecno|Phantom V Fold2|2024-09-13|7.85|2296 x 2000|120|Dimensity 9000+|12|512|50 MP + 50 MP 2x telephoto + 50 MP ultrawide|32 MP + 32 MP|5750|70W wired|249|IP54|1|0
Tecno|Camon 30 Premier 5G|2024-04-07|6.77|2780 x 1264|120|Dimensity 8200 Ultimate|12|512|50 MP + 50 MP 3x periscope + 50 MP ultrawide|50 MP|5000|70W wired|210|IP54|1|0
Tecno|Phantom X2 Pro|2022-12-17|6.8|2400 x 1080|120|Dimensity 9000|12|256|50 MP retractable portrait + 50 MP ultrawide + 13 MP|32 MP|5160|45W wired|201|—|1|0
Tecno|Camon 20 Pro 5G|2023-07-04|6.67|2400 x 1080|120|Dimensity 8050|8|256|64 MP OIS + 2 MP depth + 2 MP|32 MP|5000|33W wired|187.6|IP53|1|0
Infinix|Zero 40 5G|2024-08-29|6.78|2436 x 1080|144|Dimensity 8200 Ultimate|12|256/512|108 MP OIS + 50 MP ultrawide + 2 MP|50 MP|5000|45W wired|195|IP54|1|0
Infinix|GT 20 Pro|2024-05-28|6.78|2436 x 1080|144|Dimensity 8200 Ultimate|8/12|256|108 MP OIS + 2 MP + 2 MP|32 MP|5000|45W wired|194|IP54|1|0
Infinix|Note 40 Pro+ 5G|2024-03-19|6.78|2436 x 1080|120|Dimensity 7020|8|256|108 MP OIS + 2 MP + 2 MP|32 MP|4600|100W wired / 20W wireless|190|IP53|1|0
Infinix|Note 40 Pro|2024-03-19|6.78|2436 x 1080|120|Helio G99 Ultimate|8|256|108 MP OIS + 2 MP + 2 MP|32 MP|5000|45W wired / 20W wireless|190|IP54|1|0
Meizu|21|2023-12-05|6.55|2340 x 1080|120|Snapdragon 8 Gen 3|8/12|256/512|200 MP OIS + 13 MP ultrawide + 5 MP depth|32 MP|4800|80W wired|208|IP54|1|0
Meizu|21 Pro|2024-02-29|6.79|3192 x 1368|120|Snapdragon 8 Gen 3|12/16|256/512/1024|50 MP + 10 MP 3x telephoto + 13 MP ultrawide|32 MP|5050|80W wired / 50W wireless|214|IP68|1|0
Sharp|AQUOS R9|2024-07-12|6.5|2340 x 1080|240|Snapdragon 7+ Gen 3|8|128|50.3 MP OIS + 50.3 MP ultrawide|50.3 MP|5000|27W wired / wireless|195|IP68|1|0
DATA
;

        $count = 0;
        foreach (preg_split('/\r?\n/', trim($rows)) as $line) {
            if ($line === '') {
                continue;
            }

            [$brandName, $name, $date, $display, $resolution, $refresh, $chipset, $ram, $storage, $mainCamera, $selfie, $battery, $charging, $weight, $ip, $nfc, $jack] = explode('|', $line);
            $brand = Brand::where('slug', Str::slug($brandName))->firstOrFail();
            $slug = Str::slug($brandName . ' ' . $name);

            $device = Device::updateOrCreate(
                ['slug' => $slug],
                [
                    'brand_id' => $brand->id,
                    'name' => $name,
                    'release_date' => $date,
                    'status' => 'available',
                    'verification_status' => 'unverified',
                ]
            );

            $device->specs()->delete();

            $values = [
                'network_technology' => 'GSM / HSPA / LTE / 5G',
                'launch_announced' => $date,
                'launch_status' => 'Available',
                'body_dimensions' => 'See manufacturer regional variant',
                'body_weight' => $weight,
                'body_build' => $this->buildFor($brandName),
                'body_ip_rating' => $ip === '—' ? 'Not rated' : $ip,
                'body_headphone_jack' => $jack ? 'Yes' : 'No',
                'display_type' => $this->displayTypeFor($brandName, $name),
                'display_size' => $display,
                'display_resolution' => $resolution,
                'display_refresh_rate' => $refresh,
                'display_hdr' => 'Yes',
                'platform_os' => $brandName === 'Apple' ? 'iOS 18' : ($date >= '2025-01-01' ? 'Android 15' : 'Android 14'),
                'platform_chipset' => $chipset,
                'memory_ram' => $ram,
                'memory_storage' => $storage,
                'memory_storage_type' => $this->storageTypeFor($chipset),
                'memory_card_slot' => 'No',
                'main_camera_setup' => $mainCamera,
                'main_camera_video' => '4K video; 1080p video; model-dependent frame rates',
                'selfie_camera_setup' => $selfie,
                'selfie_camera_video' => '4K or 1080p video; model-dependent',
                'sound_loudspeaker' => 'Stereo speakers',
                'sound_headphone_out' => $jack ? 'Yes' : 'No',
                'comms_wifi' => 'Wi-Fi 6 / Wi-Fi 7, model and region dependent',
                'comms_bluetooth' => 'Bluetooth 5.x',
                'comms_nfc' => $nfc ? 'Yes' : 'No',
                'comms_usb' => 'USB Type-C',
                'features_sensors' => 'Fingerprint, accelerometer, gyro, proximity, compass; model-dependent',
                'battery_type' => 'Li-Ion / Li-Po',
                'battery_capacity' => $battery,
                'battery_charging' => $charging,
                'battery_wireless' => Str::contains(Str::lower($charging), ['wireless', 'magsafe']),
            ];

            $sort = 1;
            foreach ($values as $key => $value) {
                $definition = $definitions->get($key);
                if (!$definition) {
                    continue;
                }

                $text = is_bool($value) ? ($value ? 'Yes' : 'No') : (string) $value;
                $spec = DeviceSpec::create([
                    'device_id' => $device->id,
                    'spec_definition_id' => $definition->id,
                    'category' => $definition->category,
                    'spec_key' => $definition->label,
                    'spec_value' => $text,
                    'numeric_value' => in_array($definition->value_type, ['integer', 'decimal'], true) ? $this->numericValue($text) : null,
                    'boolean_value' => $definition->value_type === 'boolean' ? $this->booleanValue($text) : null,
                    'sort_order' => $sort++,
                ]);

                $spec->sources()->updateOrCreate(
                    ['data_source_id' => $source->id],
                    [
                        'source_value' => $text,
                        'source_url' => null,
                        'verification_status' => 'unverified',
                        'verified_at' => null,
                        'verified_by' => null,
                        'review_note' => 'Research seed; editorial verification pending.',
                    ]
                );
            }

            $device->dataSourceLinks()->updateOrCreate(
                ['data_source_id' => $source->id, 'external_id' => $slug],
                [
                    'external_url' => null,
                    'last_seen_at' => now(),
                    'metadata' => ['seed_version' => '100-phone-research-2026-09', 'verification' => 'unverified'],
                ]
            );

            $count++;
        }

        $this->command?->info("Seeded {$count} researched phone models across " . count($brands) . ' brands.');
    }

    private function numericValue(string $value): ?float
    {
        return preg_match('/-?\d+(?:\.\d+)?/', str_replace(',', '', $value), $m) === 1 ? (float) $m[0] : null;
    }

    private function booleanValue(string $value): ?bool
    {
        return match (Str::lower(trim($value))) {
            'yes', 'true', '1', 'supported', 'present' => true,
            'no', 'false', '0', 'not supported', 'absent' => false,
            default => null,
        };
    }

    private function buildFor(string $brand): string
    {
        return match ($brand) {
            'Apple' => 'Ceramic Shield front, glass back, aluminum/titanium frame',
            'Samsung' => 'Gorilla Glass front/back, aluminum frame',
            default => 'Glass front, glass back, aluminum frame',
        };
    }

    private function displayTypeFor(string $brand, string $name): string
    {
        if ($brand === 'Apple') {
            return 'Super Retina XDR OLED';
        }
        return Str::contains($name, ['Ultra', 'Pro', 'Magic', 'S25', 'S24']) ? 'LTPO AMOLED' : 'AMOLED';
    }

    private function storageTypeFor(string $chipset): string
    {
        return Str::contains($chipset, [
            'Snapdragon 8', 'A18', 'A17', 'Tensor G4', 'Tensor G3', 'Dimensity 9400',
            'Dimensity 9200', 'Dimensity 9000', 'Kirin 9010', 'Kirin 9020',
        ]) ? 'UFS 4.0' : 'UFS 3.1';
    }
}
