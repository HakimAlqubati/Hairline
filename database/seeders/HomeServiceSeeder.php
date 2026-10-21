<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class HomeServiceSeeder extends Seeder
{
    public function run(): void
    {
        $districtsByZone = [
            'شمال الرياض' => [
                ['id' => 'd_malqa',    'name' => 'حي الملقا',     'zone' => 'شمال الرياض', 'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_nakheel',  'name' => 'حي النخيل',     'zone' => 'شمال الرياض', 'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_yasmin',   'name' => 'حي الياسمين',   'zone' => 'شمال الرياض', 'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_qurtuba',  'name' => 'حي قرطبة',      'zone' => 'شمال الرياض', 'is_available' => true,  'extra_fee' => 0],
            ],
            'شرق الرياض' => [
                ['id' => 'd_rawdah',   'name' => 'حي الروضة',     'zone' => 'شرق الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_zahra',    'name' => 'حي الزهراء',    'zone' => 'شرق الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_mansura',  'name' => 'حي المنصورة',   'zone' => 'شرق الرياض',  'is_available' => true,  'extra_fee' => 15],
                ['id' => 'd_khaleej',  'name' => 'حي الخليج',     'zone' => 'شرق الرياض',  'is_available' => true,  'extra_fee' => 15],
            ],
            'غرب الرياض' => [
                ['id' => 'd_sulayman', 'name' => 'حي السلطان',    'zone' => 'غرب الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_izdihar',  'name' => 'حي الإزدهار',   'zone' => 'غرب الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_rabwa',    'name' => 'حي الربوة',     'zone' => 'غرب الرياض',  'is_available' => true,  'extra_fee' => 0],
                ['id' => 'd_hittin',   'name' => 'حي حطين',       'zone' => 'غرب الرياض',  'is_available' => false, 'extra_fee' => 0],
            ],
        ];

        foreach ($districtsByZone as $zoneName => $districtList) {
            foreach ($districtList as $d) {
                District::updateOrCreate(
                    ['slug' => $d['id']],
                    [
                        'name' => [
                            'ar' => $d['name'],
                            'en' => ucfirst(str_replace('d_', '', $d['id'])),
                        ],
                        'zone' => [
                            'ar' => $zoneName,
                            'en' => 'Riyadh Zone',
                        ],
                        'extra_fee' => (float) ($d['extra_fee'] ?? 0),
                        'is_available' => (bool) ($d['is_available'] ?? true),
                    ]
                );
            }
        }
    }
}
