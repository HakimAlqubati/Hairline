<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('salon_settings')) {
            $row = DB::table('salon_settings')->first();
            if ($row) {
                $name = json_decode((string) $row->name, true) ?: [];
                $tagline = json_decode((string) $row->tagline, true) ?: [];
                $desc1 = json_decode((string) $row->description_line1, true) ?: [];
                $desc2 = json_decode((string) $row->description_line2, true) ?: [];
                $address = json_decode((string) $row->address, true) ?: [];

                $now = now();
                $transfers = [
                    'site_name_ar' => $name['ar'] ?? 'هيرلاين',
                    'site_name_en' => $name['en'] ?? 'HAIRLINE',
                    'site_tagline_ar' => $tagline['ar'] ?? 'صالون هيرلاين النسائي',
                    'site_tagline_en' => $tagline['en'] ?? 'BEAUTY SALON',
                    'description_line1' => $desc1['ar'] ?? 'وجهتكِ الفاخرة المتكاملة للعناية بالشعر والأظافر والجمال،',
                    'description_line2' => $desc2['ar'] ?? 'بأيدي خبيرات محترفات وأرقى المنتجات العالمية في أجواء ملكية.',
                    'contact_address' => $address['ar'] ?? 'الرياض - حي المصيف، مخرج 5، شارع زهير بن أبي سلمى',
                    'contact_phone' => $row->phone ?? '0548857705',
                    'whatsapp_number' => $row->whatsapp ?? '966548857705',
                    'contact_email' => $row->email ?? 'info@hairlinesalon.sa',
                    'latitude' => (string) ($row->latitude ?? 24.7677),
                    'longitude' => (string) ($row->longitude ?? 46.6896),
                    'google_maps_url' => $row->google_maps_url ?? 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps',
                    'tax_number' => $row->tax_number ?? '300998877660003',
                    'vat_percentage' => (string) ($row->vat_percentage ?? 15),
                    'stats' => $row->stats ?? json_encode([
                        ['value' => '+12', 'label' => 'أخصائية محترفة', 'bg' => 'bg-sage-soft', 'text' => 'text-sage-dark'],
                        ['value' => '100%', 'label' => 'منتجات أصلية', 'bg' => 'bg-gold-light', 'text' => 'text-gold-dark'],
                    ], JSON_UNESCAPED_UNICODE),
                ];

                if (! empty($row->logo_path)) {
                    $transfers['site_logo'] = $row->logo_path;
                }

                $upserts = [];
                foreach ($transfers as $k => $v) {
                    $upserts[] = [
                        'key' => $k,
                        'value' => (string) $v,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                DB::table('settings')->upsert($upserts, ['key'], ['value', 'updated_at']);
            }

            Schema::dropIfExists('salon_settings');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('salon_settings', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('tagline')->nullable();
            $table->json('description_line1')->nullable();
            $table->json('description_line2')->nullable();
            $table->json('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('email')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('google_maps_url')->nullable();
            $table->string('tax_number')->nullable();
            $table->decimal('vat_percentage', 5, 2)->default(15.00);
            $table->json('stats')->nullable();
            $table->timestamps();
        });
    }
};
