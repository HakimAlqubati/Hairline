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
     *     rating: string,
     *     reviews: int,
     *     avatar: string,
     *     bio: string,
     *     availableSlots: array<int, string>
     * }>
     */
    public function getAllSpecialists(): array
    {
        return [
            'sp_noura' => [
                'id' => 'sp_noura',
                'name' => 'الأخصائية نورة العتيبي',
                'role' => 'خبيرة أولى في المعالجات الحارة وفرد الشعر',
                'experience' => 'خبرة 9 سنوات',
                'rating' => '4.9',
                'reviews' => 142,
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=300&q=80',
                'bio' => 'معتمدة دولياً في تطبيق البروتين البرازيلي العلاجي وبوتكس الشعر الحراري للشعر المصبوغ والحساس.',
                'availableSlots' => ['11:30 ص', '02:00 م', '05:30 م', '08:00 م'],
            ],
            'sp_reem' => [
                'id' => 'sp_reem',
                'name' => 'الأخصائية ريم الحربي',
                'role' => 'أخصائية معالجات الكافيار والكولاجين الذهبي',
                'experience' => 'خبرة 7 سنوات',
                'rating' => '4.8',
                'reviews' => 98,
                'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=300&q=80',
                'bio' => 'متخصصة في إعادة بناء ألياف الشعر التالف بالمعالجات الحارة والباردة وتقنيات النانو سيراميك.',
                'availableSlots' => ['12:00 م', '03:30 م', '06:30 م', '08:30 م'],
            ],
            'sp_layla' => [
                'id' => 'sp_layla',
                'name' => 'الأخصائية ليلى المنصور',
                'role' => 'خبيرة الكيراتين الحيوي والترميم العميق',
                'experience' => 'خبرة 8 سنوات',
                'rating' => '5.0',
                'reviews' => 165,
                'avatar' => 'https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?auto=format&fit=crop&w=300&q=80',
                'bio' => 'حاصلة على شهادة التميز في معالجات الشعر الكيرلي والأفرو وتنعيمه مع الحفاظ على كثافته الطبيعية.',
                'availableSlots' => ['01:00 م', '04:00 م', '07:00 م'],
            ],
            'sp_dana' => [
                'id' => 'sp_dana',
                'name' => 'الأخصائية دانة السبيعي',
                'role' => 'ماستر كلرست وأخصائية صبغات بالياج',
                'experience' => 'خبرة 10 سنوات',
                'rating' => '4.9',
                'reviews' => 210,
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=300&q=80',
                'bio' => 'خبيرة دمج الألوان والصبغات الخالية من الأمونيا مع حماية روابط الشعر بتقنية أولابليكس.',
                'availableSlots' => ['11:00 ص', '02:30 م', '06:00 م'],
            ],
            'sp_sara' => [
                'id' => 'sp_sara',
                'name' => 'الأخصائية سارة الفيصل',
                'role' => 'أخصائية ديتوكس الفروة والقص الاحترافي',
                'experience' => 'خبرة 6 سنوات',
                'rating' => '4.8',
                'reviews' => 87,
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80',
                'bio' => 'متخصصة في تشخيص فروة الرأس بالميكروسكوب وجلسات الترميم البارد والقص الفرنسي الحديث.',
                'availableSlots' => ['12:30 م', '04:30 م', '07:30 م', '09:00 م'],
            ],
        ];
    }

    /**
     * @return ?array{
     *     id: string,
     *     name: string,
     *     role: string,
     *     experience: string,
     *     rating: string,
     *     reviews: int,
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
