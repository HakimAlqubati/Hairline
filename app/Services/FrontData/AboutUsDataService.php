<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Models\FounderStory;
use App\Services\Cache\HairlineCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * Provides database-backed and cached data for the "About Us" (من نحن) section.
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
        return Cache::rememberForever(HairlineCacheService::KEY_ABOUT_US, function () {
            $story = FounderStory::first();

            $rawParagraphs = $story?->getTranslation('founder_message_paragraphs', 'ar') ?? '';
            $paragraphs = [];
            if ($rawParagraphs) {
                $decoded = json_decode($rawParagraphs, true);
                $paragraphs = is_array($decoded) ? $decoded : [$rawParagraphs];
            }

            if (empty($paragraphs)) {
                $paragraphs = [
                    'من يوم تأسيس هيرلاين، كان طموحي أقدم أكثر من مجرد خدمات صالون، كنت أبغى أصنع مساحة خاصة لكل سيدة تحس فيها بالاهتمام بنفسها والراحة.',
                    'أؤمن بأن الجمال يتجاوز المظهر، هو انعكاس للشعور بالثقة بالنفس والرضا الداخلي، وكل امرأه تستحق وقت لنفسها يعزز إحساسها بجمالها وقوتها وتميزها.',
                    'ومن هنا جاء هير لاين عشان يكون جزء من رحلتي ورحلتكم بالاهتمام بالجمال والمظهر وتعزيز الثقة الداخلية وتقديم خدمة تستحقها كل امرأه بمختلف الأعمار.',
                ];
            }

            $founderNameAr = $story?->getTranslation('founder_name', 'ar') ?: 'سارة السيف';
            $founderTitleAr = $story?->getTranslation('founder_title', 'ar') ?: 'مؤسسة هير لاين';
            $founderTitleEn = $story?->getTranslation('founder_title', 'en') ?: 'Founder of Hairline';

            return [
                'title' => $story?->getTranslation('title', 'ar') ?: 'من نحن',
                'founding_year' => $story?->founding_year ?: 2020,
                'founder_name_ar' => $founderNameAr,
                'founder_name_en' => $story?->getTranslation('founder_name', 'en') ?: 'Sara Al-Saif',
                'founder_title_ar' => $founderTitleAr,
                'founder_title_en' => $founderTitleEn,
                'intro_paragraph' => $story?->getTranslation('intro_text', 'ar') ?: 'تأسس هير لاين عام 2020 برؤية وشغف من مؤسسته سارة السيف، ليكون وجهتكِ المفضلة للجمال والعناية.',
                'description_paragraph' => $story?->getTranslation('description_text', 'ar') ?: 'هير لاين هو مساحتكِ الخاصة للدلال والتجدد، نهتم بأدق التفاصيل ونقدم لكِ خدمات مميزة في عالم العناية بالشعر والبشرة والأظافر، هدفنا أن تعيشي أحلى تجربة من أول زيارة.',
                'belief_paragraph' => $story?->getTranslation('belief_text', 'ar') ?: 'نؤمن إن الجمال الحقيقي يبدأ من إحساسكِ الجيد بنفسكِ وأن الاهتمام بذاتكِ ليس مجرد رفاهية، بل جزء من حياة كل سيدة، ومن هنا، نحرص على تقديم تجربة عناية متكاملة تمنحكِ لحظات تستحقينها.',
                'founder_message_title' => $story?->getTranslation('founder_message_title', 'ar') ?: 'كلمة المؤسسة',
                'founder_message_paragraphs' => $paragraphs,
                'founder_signature' => [
                    'name_ar' => $founderNameAr,
                    'title_ar' => $founderTitleAr,
                    'title_en' => $founderTitleEn,
                ],
            ];
        });
    }
}
