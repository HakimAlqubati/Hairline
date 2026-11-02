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
        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table): void {
                $table->string('key')->primary();
                $table->longText('value')->nullable();
                $table->timestamps();
            });

            $defaults = [
                'site_name_ar' => 'هيرلاين',
                'site_name_en' => 'HAIRLINE',
                'site_tagline_ar' => 'صالون هيرلاين النسائي',
                'site_tagline_en' => 'BEAUTY SALON',
                'site_logo' => '/images/logo.png',
                'tax_number' => '300998877660003',
                'vat_percentage' => '15',
                'deposit_percentage' => '30',
                'currency' => 'SAR',
                'default_working_hours' => 'يومياً من الساعة 2:00 ظهراً حتى 10:00 مساءً',
                'contact_phone' => '0548857705',
                'whatsapp_number' => '966548857705',
                'contact_email' => 'info@hairlinesalon.sa',
                'contact_address' => 'الرياض - حي المصيف، مخرج 5، شارع زهير بن أبي سلمى',
                'google_maps_url' => 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps',
                'instagram_url' => 'https://www.instagram.com/hairline.sa',
                'instagram_handle' => '@hairline.sa',
                'snapchat_url' => 'https://www.snapchat.com/add/hairline.sa',
                'snapchat_handle' => '@hairline.sa',
                'tiktok_url' => 'https://www.tiktok.com/@hairline.sa',
                'tiktok_handle' => '@hairline.sa',
                'about_title' => 'من نحن',
                'about_founding_year' => '2020',
                'about_intro' => 'تأسس هيرلاين عام 2020 برؤية وشغف من مؤسسته سارة السيف، ليكون وجهتك المفضلة للجمال والعناية.',
                'about_description' => 'هيرلاين هو مساحتك الخاصة للدلال والتجدد، نهتم بأدق التفاصيل ونقدم لك خدمات مميزة في عالم العناية بالشعر والبشرة والأظافر، هدفنا أن تعيشي أحلى تجربة من أول زيارة.',
                'about_belief' => 'نؤمن إن الجمال الحقيقي يبدأ من إحساسك الجيد بنفسك وأن الاهتمام بذاتك ليس مجرد رفاهية، بل جزء من حياة كل سيدة، ومن هنا، نحرص على تقديم تجربة عناية متكاملة تمنحك لحظات تستحقينها.',
                'founder_message_title' => 'كلمة المؤسسة',
                'founder_message_p1' => 'من يوم تأسيس هيرلاين، كان طموحي أقدم أكثر من مجرد خدمات صالون، كنت أبغى أصنع مساحة خاصة لكل سيدة تحس فيها بالاهتمام بنفسها والراحة.',
                'founder_message_p2' => 'أؤمن بأن الجمال يتجاوز المظهر، هو انعكاس للشعور بالثقة بالنفس والرضا الداخلي، وكل امرأة تستحق وقت لنفسها يعزز إحساسها بجمالها وقوتها وتميزها.',
                'founder_message_p3' => 'ومن هنا جاء هيرلاين علشان يكون جزء من رحلتي ورحلتكم بالاهتمام بالجمال والمظهر وتعزيز الثقة الداخلية وتقديم خدمة تستحقها كل امرأة بمختلف الأعمار.',
                'founder_name' => 'سارة السيف',
                'founder_title' => 'Founder | مؤسسة هيرلاين',
                'meta_title' => 'هيرلاين | صالون هيرلاين النسائي للتجميل والعناية بالرياض',
                'meta_description' => 'وجهتكِ الفاخرة المتكاملة للعناية بالشعر والأظافر والجمال في الرياض بأيدي خبيرات محترفات.',
                'meta_keywords' => 'صالون نسائي, هيرلاين, معالجات شعر, صبغات, تساريح, مساج, مناكير, الرياض, حي المصيف',
            ];

            $now = now();
            $rows = [];
            foreach ($defaults as $key => $value) {
                $rows[] = [
                    'key' => $key,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('settings')->insert($rows);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
