<?php

declare(strict_types=1);

namespace App\Services\FrontData;

/**
 * Provides static data for the "About Us" (من نحن) section.
 *
 * All content is returned as typed arrays, ready for replacement
 * with a database-backed model in the future.
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
        return [
            'title' => 'من نحن',
            'founding_year' => 2020,

            // Founder info
            'founder_name_ar' => 'سارة السيف',
            'founder_name_en' => 'Sara Al-Saif',
            'founder_title_ar' => 'مؤسسة هير لاين',
            'founder_title_en' => 'Founder of Hairline',

            // About intro paragraph
            'intro_paragraph' => 'تأسس هير لاين عام 2020 برؤية وشغف من مؤسسته سارة السيف، ليكون وجهتكِ المفضلة للجمال والعناية.',

            // Description paragraph
            'description_paragraph' => 'هير لاين هو مساحتكِ الخاصة للدلال والتجدد، نهتم بأدق التفاصيل ونقدم لكِ خدمات مميزة في عالم العناية بالشعر والبشرة والأظافر، هدفنا أن تعيشي أحلى تجربة من أول زيارة.',

            // Belief paragraph
            'belief_paragraph' => 'نؤمن إن الجمال الحقيقي يبدأ من إحساسكِ الجيد بنفسكِ وأن الاهتمام بذاتكِ ليس مجرد رفاهية، بل جزء من حياة كل سيدة، ومن هنا، نحرص على تقديم تجربة عناية متكاملة تمنحكِ لحظات تستحقينها.',

            // Founder's message
            'founder_message_title' => 'كلمة المؤسسة',
            'founder_message_paragraphs' => [
                'من يوم تأسيس هيرلاين، كان طموحي أقدم أكثر من مجرد خدمات صالون، كنت أبغى أصنع مساحة خاصة لكل سيدة تحس فيها بالاهتمام بنفسها والراحة.',
                'أؤمن بأن الجمال يتجاوز المظهر، هو انعكاس للشعور بالثقة بالنفس والرضا الداخلي، وكل امرأه تستحق وقت لنفسها يعزز إحساسها بجمالها وقوتها وتميزها.',
                'ومن هنا جاء هير لاين عشان يكون جزء من رحلتي ورحلتكم بالاهتمام بالجمال والمظهر وتعزيز الثقة الداخلية وتقديم خدمة تستحقها كل امرأه بمختلف الأعمار.',
            ],

            // Founder signature
            'founder_signature' => [
                'name_ar' => 'سارة السيف',
                'title_ar' => 'مؤسسة هير لاين',
                'title_en' => 'Founder of Hairline',
            ],
        ];
    }
}
