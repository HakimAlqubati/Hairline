<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\Setting;
use App\Services\Cache\HairlineCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * Provides database-backed and cached data for the "About Us" (من نحن) section
 * fetched directly from the system Settings model.
 */
class AboutUsDataService
{
    /**
     * @return array{
     *     title: string,
     *     founding_year: int,
     *     founder_name_ar: string,
     *     founder_name_en: string,
     *     founder_title_ar: string,
     *     founder_title_en: string,
     *     intro_paragraph: string,
     *     description_paragraph: string,
     *     belief_paragraph: string,
     *     founder_message_title: string,
     *     founder_message_paragraphs: string[],
     *     founder_signature: array{name_ar: string, title_ar: string, title_en: string}
     * }
     */
    public function getAboutUsContent(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_ABOUT_US, function (): array {
            $settings = Setting::whereIn('key', [
                'about_title',
                'about_founding_year',
                'about_intro',
                'about_description',
                'about_belief',
                'founder_message_title',
                'founder_message_p1',
                'founder_message_p2',
                'founder_message_p3',
                'founder_name',
                'founder_title',
            ])->pluck('value', 'key')->toArray();

            $p1 = $settings['founder_message_p1'] ?? 'من يوم تأسيس هيرلاين، كان طموحي أقدم أكثر من مجرد خدمات صالون، كنت أبغى أصنع مساحة خاصة لكل سيدة تحس فيها بالاهتمام بنفسها والراحة.';
            $p2 = $settings['founder_message_p2'] ?? 'أؤمن بأن الجمال يتجاوز المظهر، هو انعكاس للشعور بالثقة بالنفس والرضا الداخلي، وكل امرأة تستحق وقت لنفسها يعزز إحساسها بجمالها وقوتها وتميزها.';
            $p3 = $settings['founder_message_p3'] ?? 'ومن هنا جاء هيرلاين علشان يكون جزء من رحلتي ورحلتكم بالاهتمام بالجمال والمظهر وتعزيز الثقة الداخلية وتقديم خدمة تستحقها كل امرأة بمختلف الأعمار.';

            $paragraphs = array_values(array_filter([$p1, $p2, $p3]));

            $founderName = $settings['founder_name'] ?? 'سارة السيف';
            $founderTitle = $settings['founder_title'] ?? 'Founder | مؤسسة هيرلاين';

            return [
                'title' => $settings['about_title'] ?? 'من نحن',
                'founding_year' => (int) ($settings['about_founding_year'] ?? 2020),
                'founder_name_ar' => $founderName,
                'founder_name_en' => 'Sara Alsaif',
                'founder_title_ar' => $founderTitle,
                'founder_title_en' => $founderTitle,
                'intro_paragraph' => $settings['about_intro'] ?? 'تأسس هيرلاين عام 2020 برؤية وشغف من مؤسسته سارة السيف، ليكون وجهتك المفضلة للجمال والعناية.',
                'description_paragraph' => $settings['about_description'] ?? 'هيرلاين هو مساحتك الخاصة للدلال والتجدد، نهتم بأدق التفاصيل ونقدم لك خدمات مميزة في عالم العناية بالشعر والبشرة والأظافر، هدفنا أن تعيشي أحلى تجربة من أول زيارة.',
                'belief_paragraph' => $settings['about_belief'] ?? 'نؤمن إن الجمال الحقيقي يبدأ من إحساسك الجيد بنفسك وأن الاهتمام بذاتك ليس مجرد رفاهية، بل جزء من حياة كل سيدة، ومن هنا، نحرص على تقديم تجربة عناية متكاملة تمنحك لحظات تستحقينها.',
                'founder_message_title' => $settings['founder_message_title'] ?? 'كلمة المؤسسة',
                'founder_message_paragraphs' => $paragraphs,
                'founder_signature' => [
                    'name_ar' => $founderName,
                    'title_ar' => $founderTitle,
                    'title_en' => $founderTitle,
                ],
            ];
        });
    }
}
