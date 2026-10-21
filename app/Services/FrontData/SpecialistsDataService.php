<?php

declare(strict_types=1);

namespace App\Services\FrontData;

/**
 * Provides static specialist data for Hairline Beauty Lounge.
 */
class SpecialistsDataService
{
    /**
     * @return array<string, array{
     *     id: string,
     *     name: string,
     *     role: string,
     *     experience: string,
     *     avatar: string,
     *     bio: string,
     *     availableSlots: array<int, string>
     * }>
     */
    public function getAllSpecialists(): array
    {
        return [
            'sp_karen' => [
                'id' => 'sp_karen',
                'name' => 'كارين | Karen',
                'role' => 'خبيرة قص ومعالجات وصبغات الشعر والتسريحات',
                'experience' => 'خبرة لأكثر من 15 عامًا',
                'avatar' => 'https://ui-avatars.com/api/?name=Karen&background=211C18&color=C29E6D&bold=true&size=150',
                'bio' => 'خبرة واسعة في مجال قص الشعر بمختلف أنواعه، ومعالجات الشعر، والصبغات، والتسريحات، بخبرة واسعة في تقديم إطلالات تناسب مختلف الأذواق.',
                'availableSlots' => ['11:30 ص', '02:00 م', '05:30 م', '08:00 م'],
            ],
            'sp_janet' => [
                'id' => 'sp_janet',
                'name' => 'جانيت | Janet',
                'role' => 'أخصائية خدمات الشعر والصبغات والمعالجات',
                'experience' => 'خبرة لأكثر من 13 عامًا',
                'avatar' => 'https://ui-avatars.com/api/?name=Janet&background=211C18&color=C29E6D&bold=true&size=150',
                'bio' => 'تمتع بخبرة شاملة في خدمات الشعر، وتتميز باحترافيتها في الصبغات ومعالجات الشعر، مع اهتمام بأدق التفاصيل للوصول إلى نتائج مميزة.',
                'availableSlots' => ['12:00 م', '03:30 م', '06:30 م', '08:30 م'],
            ],
            'sp_jenky' => [
                'id' => 'sp_jenky',
                'name' => 'جينكي | Jenky',
                'role' => 'أخصائية العناية بالأظافر والبشرة والشعر والاستشوار',
                'experience' => 'خبرة لأكثر من 7 سنوات',
                'avatar' => 'https://ui-avatars.com/api/?name=Jenky&background=211C18&color=C29E6D&bold=true&size=150',
                'bio' => 'متخصصة في خدمات العناية بالأظافر والبشرة والشعر، وتتميز بمهارتها في الاستشوار بمختلف أنواع الويفي، بالإضافة إلى تقديم خدمات العناية بالبشرة وتنظيفها وإزالة الشعر بمختلف التقنيات لإطلالات أنيقة ومرتبة.',
                'availableSlots' => ['01:00 م', '04:00 م', '07:00 م', '09:00 م'],
            ],
            'sp_ruth' => [
                'id' => 'sp_ruth',
                'name' => 'روث | Ruth',
                'role' => 'أخصائية العناية بالأظافر وتقنيات المساج',
                'experience' => 'خبرة لأكثر من 12 عامًا',
                'avatar' => 'https://ui-avatars.com/api/?name=Ruth&background=211C18&color=C29E6D&bold=true&size=150',
                'bio' => 'تمتع بخبرة واسعة في العناية بالأظافر والمساج ، حاصلة على شهادات دولية متخصصة في تقنيات المساج. تتميز بدقتها العالية في العناية بالأظافر، لتقدم لك تجربة متكاملة تجمع بين الجودة والاحترافية والاسترخاء.',
                'availableSlots' => ['11:00 ص', '02:30 م', '06:00 م', '08:30 م'],
            ],
            'sp_najla' => [
                'id' => 'sp_najla',
                'name' => 'نجلاء العبيد | Najla Alobaid',
                'role' => 'ميك أب أرتيست',
                'experience' => 'أكثر من 6 سنوات من الخبرة',
                'avatar' => 'https://ui-avatars.com/api/?name=Najla+Alobaid&background=211C18&color=C29E6D&bold=true&size=150',
                'bio' => 'متخصصة في المكياج وتتميز بلمساتها الاحترافية التي تبرز جمال الملامح بأسلوب مناسب لكل ملامح ولكل مناسبة.',
                'availableSlots' => ['12:30 م', '03:00 م', '05:30 م', '08:00 م'],
            ],
        ];
    }

    /**
     * @return ?array{
     *     id: string,
     *     name: string,
     *     role: string,
     *     experience: string,
     *     avatar: string,
     *     bio: string,
     *     availableSlots: array<int, string>
     * }
     */
    public function getSpecialistById(string $id): ?array
    {
        return $this->getAllSpecialists()[$id] ?? null;
    }
}
