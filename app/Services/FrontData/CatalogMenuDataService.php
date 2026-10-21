<?php

declare(strict_types=1);

namespace App\Services\FrontData;

use App\Services\Cache\HairlineCacheService;
use Illuminate\Support\Facades\Cache;

/**
 * Provides structured catalog menu pages data for Hairline Beauty Lounge.
 *
 * Exact match to the official 8-page salon menu:
 * - Page 1: خدمات الشعر (HAIR SERVICES)
 * - Page 2: معالجات الشعر (HAIR TREATMENTS)
 * - Page 3: جلسات العناية بالشعر (HAIR CARE SESSIONS)
 * - Page 4: الاستشوار (BLOW DRY STYLES)
 * - Page 5: المناكير و البادكير (MANICURES AND PEDICURES)
 * - Page 6: خدمات البشرة + الرموش و الحواجب (SKIN SERVICES + EYELASHES AND EYEBROWS)
 * - Page 7: خدمات المساج + الحلاوة و الواكس (MASSAGE SERVICES + HOT & COLD WAX)
 * - Page 8: أطوال الشعر (HAIR LENGTHS)
 */
class CatalogMenuDataService
{
    /**
     * Get all 8 official menu pages (Cached).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMenuPages(): array
    {
        return Cache::rememberForever(HairlineCacheService::KEY_CATALOG_MENU_PAGES, function () {
            return [
                $this->getPage1HairServices(),
                $this->getPage2HairTreatments(),
                $this->getPage3HairCare(),
                $this->getPage4BlowDry(),
                $this->getPage5Nails(),
                $this->getPage6SkinAndBrows(),
                $this->getPage7MassageAndWax(),
                $this->getPage8HairLengths(),
            ];
        });
    }

    /**
     * Page 1: خدمات الشعر | HAIR SERVICES
     *
     * @return array<string, mixed>
     */
    public function getPage1HairServices(): array
    {
        return [
            'page_number' => 1,
            'title_ar' => 'خدمات الشعر',
            'title_en' => 'HAIR SERVICES',
            'layout_type' => 'groups',
            'groups' => [
                [
                    'label_ar' => 'التساريح',
                    'label_en' => 'HAIRDO STYLES',
                    'container_class' => '',
                    'items' => [
                        [
                            'ar_name' => 'تسريحة مع تركيب طرحة عروس',
                            'en_name' => 'HAIRSTYLE BRIDAL VEIL STYLING',
                            'price' => '400 SR',
                        ],
                        [
                            'ar_name' => 'تسريحة تثبيت - رفعة - ريترو - قصير جداً',
                            'en_name' => 'HAIRDO - RETRO- V. SHORT',
                            'price' => '200 SR',
                        ],
                        [
                            'ar_name' => 'تسريحة تثبيت - رفعة - ريترو - قصير',
                            'en_name' => 'HAIRDO- RETRO - SHORT',
                            'price' => '250 SR',
                        ],
                        [
                            'ar_name' => 'تسريحة تثبيت - رفعه - ريترو - وسط',
                            'en_name' => 'HAIRDO- RETRO - MEDIUM',
                            'price' => '350 SR',
                        ],
                        [
                            'ar_name' => 'تسريحة تثبيت - رفعة- ريترو - طويل',
                            'en_name' => 'HAIRDO - RETRO - LONG',
                            'price' => '400 SR',
                        ],
                    ],
                ],
                [
                    'label_ar' => 'الجـــذور',
                    'label_en' => 'HAIR ROOTS',
                    'container_class' => '',
                    'items' => [
                        [
                            'ar_name' => 'صبغة الجذور',
                            'en_name' => 'ROOTS COLOING',
                            'price' => '100 SR',
                        ],
                    ],
                ],
                [
                    'label_ar' => 'الصبغــات',
                    'label_en' => 'HAIR COLORING',
                    'container_class' => '',
                    'items' => [
                        [
                            'ar_name' => 'صبغة لون كامل',
                            'en_name' => 'FULL HAIR COLOR',
                            'price' => '350 - 1400 SR',
                        ],
                        [
                            'ar_name' => 'خصل (اومبري -سومبري -بلياج)',
                            'en_name' => 'HIGHLIGHTS (OMBRE - SOMBRE - BALAYAGE)',
                            'price' => '750 - 1800 SR',
                            'extra_class' => 'mb-3',
                        ],
                        [
                            'ar_name' => 'رينســاج قصيــر جــداً',
                            'en_name' => 'RANCAGE V. SHOR',
                            'price' => '110 SR',
                        ],
                        [
                            'ar_name' => 'رينســاج قصيــر',
                            'en_name' => 'RANCAGE SHOR',
                            'price' => '195 SR',
                        ],
                        [
                            'ar_name' => 'رينســاج وســط',
                            'en_name' => 'RANCAGE MEDIUM',
                            'price' => '290 SR',
                        ],
                        [
                            'ar_name' => 'رينســاج طويــل',
                            'en_name' => 'RANCAGE LONG',
                            'price' => '395 SR',
                        ],
                        [
                            'ar_name' => 'رينســاج طويــل جــداً',
                            'en_name' => 'RANCAGE V. LONG',
                            'price' => '470 SR',
                        ],
                        [
                            'ar_name' => 'صبغة بدون امونيا',
                            'en_name' => 'AMMONIA-FREE',
                            'price' => '+35 SR',
                        ],
                    ],
                ],
                [
                    'label_ar' => 'قص الشعر',
                    'label_en' => 'HAIR CUT',
                    'container_class' => 'mb-0',
                    'items' => [
                        [
                            'ar_name' => 'قص الشعر كامل',
                            'en_name' => 'FULL HAIRCUT',
                            'price' => '80 SR',
                        ],
                        [
                            'ar_name' => 'قص الأطراف',
                            'en_name' => 'HAIR ENDS CUT',
                            'price' => '60 SR',
                        ],
                        [
                            'ar_name' => 'قص الأطراف بالماكينة',
                            'en_name' => 'MACHINE HAIR ENDS CUT',
                            'price' => '48 SR',
                        ],
                        [
                            'ar_name' => 'قص أو ترتيب البوي',
                            'en_name' => 'BOY CUT',
                            'price' => '95 SR',
                        ],
                        [
                            'ar_name' => 'قص الغرة',
                            'en_name' => 'BANGS CUT',
                            'price' => '25 SR',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Page 2: معالجات الشعر | HAIR TREATMENTS
     *
     * @return array<string, mixed>
     */
    public function getPage2HairTreatments(): array
    {
        return [
            'page_number' => 2,
            'title_ar' => 'معالجات الشعر',
            'title_en' => 'HAIR TREATMENTS',
            'layout_type' => 'subsections',
            'subsections' => [
                [
                    'title_ar' => 'المعالجات الحارة',
                    'title_en' => 'HOT TREATMENTS',
                    'type' => 'simple_rows',
                    'container_class' => 'mb-4',
                    'items' => [
                        [
                            'ar_name' => 'البروتين بالتمليس الحراري',
                            'en_name' => 'PROTIEN BY STRAIGHTENER MACHINE',
                            'price' => '750-1800 SR',
                        ],
                        [
                            'ar_name' => 'البروتين بدون التمليس الحراري',
                            'en_name' => 'PROTIEN WITHOUT STRAIGHTENER MACHINE',
                            'price' => '560-1300 SR',
                        ],
                        [
                            'ar_name' => 'بروتين الجذور',
                            'en_name' => 'ROOTS PROTIEN',
                            'price' => '520 SR',
                        ],
                    ],
                ],
                [
                    'title_ar' => 'المعالجات الباردة',
                    'title_en' => 'COLD TREATMENTS',
                    'type' => 'multi_length',
                    'blocks' => [
                        [
                            'ar_name' => 'جلسة فيلر المورفوسيس (لترميم الشعر)',
                            'en_name' => 'FILLER MORPHSIS SESSION',
                            'container_class' => '',
                            'lengths' => [
                                ['ar' => 'قصير', 'en' => 'SHORT', 'price' => '195 SR'],
                                ['ar' => 'وسط', 'en' => 'MEDIUM', 'price' => '230 SR'],
                                ['ar' => 'طويل', 'en' => 'LONG', 'price' => '288 SR'],
                                ['ar' => 'طويل جداً', 'en' => 'V. LONG', 'price' => '335 SR'],
                            ],
                        ],
                        [
                            'ar_name' => 'جلسة الترندي (لترميم الشعر)',
                            'en_name' => 'TRENDY SESSION FOR HAIR RECOVERY',
                            'container_class' => '',
                            'lengths' => [
                                ['ar' => 'قصير جداً', 'en' => 'V. SHORT', 'price' => '310 SR'],
                                ['ar' => 'قصير', 'en' => 'SHORT', 'price' => '340 SR'],
                                ['ar' => 'وسط', 'en' => 'MEDIUM', 'price' => '380 SR'],
                                ['ar' => 'طويل', 'en' => 'LONG', 'price' => '410 SR'],
                                ['ar' => 'طويل جداً', 'en' => 'V. LONG', 'price' => '780 SR'],
                            ],
                        ],
                        [
                            'ar_name' => 'جلسة العناية بالشعر الكيرلي',
                            'en_name' => 'CURLY HAIR TREATMENT',
                            'container_class' => 'mb-0',
                            'lengths' => [
                                ['ar' => 'قصير', 'en' => 'SHORT', 'price' => '380 SR'],
                                ['ar' => 'وسط', 'en' => 'MEDIUM', 'price' => '400 SR'],
                                ['ar' => 'طويل', 'en' => 'LONG', 'price' => '515 SR'],
                                ['ar' => 'طويل جداً', 'en' => 'V. LONG', 'price' => '879 SR'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Page 3: جلسات العناية بالشعر | HAIR CARE SESSIONS
     *
     * @return array<string, mixed>
     */
    public function getPage3HairCare(): array
    {
        return [
            'page_number' => 3,
            'title_ar' => 'جلسات العناية بالشعر',
            'title_en' => 'HAIR CARE SESSIONS',
            'layout_type' => 'hair_care',
            'top_items' => [
                [
                    'ar_name' => 'جلسة تنظيف فروة الرأس العادية',
                    'en_name' => 'REGULAR SCALP CLEANING SESSION',
                    'price' => '215 SR',
                ],
                [
                    'ar_name' => 'جلسة تنظيف فروة الرأس العميقة',
                    'en_name' => 'DEEP SCALP CLEANING SESSION',
                    'price' => '600 SR',
                ],
                [
                    'ar_name' => 'غسيل الشعر',
                    'en_name' => 'HAIR WASH',
                    'price' => '25 SR',
                ],
                [
                    'ar_name' => 'غسيل بشامبو الرنساج / غسيل الحنة',
                    'en_name' => 'WASHING BY RENCAGE SHAMPOO / WASHING THE HENNA SESSION',
                    'price' => '50 SR',
                ],
            ],
            'multi_length_blocks' => [
                [
                    'ar_name' => 'جلسة الصبار الطبيعي',
                    'en_name' => 'OLIVERA SESSION',
                    'container_class' => '',
                    'lengths' => [
                        ['ar' => 'قصير', 'en' => 'SHORT', 'price' => '130 SR'],
                        ['ar' => 'وسط', 'en' => 'MEDIUM', 'price' => '160 SR'],
                        ['ar' => 'طويل', 'en' => 'LONG', 'price' => '190 SR'],
                        ['ar' => 'طويل جداً', 'en' => 'V. LONG', 'price' => '220 SR'],
                    ],
                ],
                [
                    'ar_name' => 'جلسة الحنة مع السدر (لكامل الشعر)',
                    'en_name' => 'HENNA MIXED WITH SIDR (FULL HAIR)',
                    'container_class' => '',
                    'lengths' => [
                        ['ar' => 'قصير', 'en' => 'SHORT', 'price' => '150 SR'],
                        ['ar' => 'وسط', 'en' => 'MEDIUM', 'price' => '180 SR'],
                        ['ar' => 'طويل', 'en' => 'LONG', 'price' => '200 SR'],
                        ['ar' => 'طويل جداً', 'en' => 'V. LONG', 'price' => '240 SR'],
                    ],
                ],
                [
                    'ar_name' => 'جلسة الحنة (لكامل الشعر)',
                    'en_name' => 'HENNA (FULL HAIR)',
                    'container_class' => '',
                    'lengths' => [
                        ['ar' => 'قصير', 'en' => 'SHORT', 'price' => '130 SR'],
                        ['ar' => 'وسط', 'en' => 'MEDIUM', 'price' => '160 SR'],
                        ['ar' => 'طويل', 'en' => 'LONG', 'price' => '190 SR'],
                        ['ar' => 'طويل جداً', 'en' => 'V.LONG', 'price' => '220 SR'],
                    ],
                ],
            ],
            'bottom_items' => [
                [
                    'ar_name' => 'الوسمة للجذور',
                    'en_name' => 'WASMA FOR THE ROOTS',
                    'price' => '80 SR',
                ],
            ],
        ];
    }

    /**
     * Page 4: الاستشوار | BLOW DRY STYLES
     *
     * @return array<string, mixed>
     */
    public function getPage4BlowDry(): array
    {
        return [
            'page_number' => 4,
            'title_ar' => 'الاستشوار',
            'title_en' => 'BLOW DRY STYLES',
            'title_class' => 'mb-10',
            'layout_type' => 'blow_dry',
            'blocks' => [
                [
                    'ar_name' => 'استشوار عادي',
                    'en_name' => 'STRAIGHT BLOW DRY',
                    'ar_title_class' => 'text-2xl',
                    'en_title_class' => 'text-xs mt-1',
                    'gap_class' => 'gap-3',
                    'container_class' => '',
                    'lengths' => [
                        ['ar' => 'قصير جداً', 'en' => 'V. SHORT', 'price' => '70 SR', 'ar_class' => 'text-base'],
                        ['ar' => 'قصير', 'en' => 'SHORT', 'price' => '110 SR', 'ar_class' => 'text-base'],
                        ['ar' => 'وسط', 'en' => 'MEDIUM', 'price' => '140 SR', 'ar_class' => 'text-base'],
                        ['ar' => 'طويل', 'en' => 'LONG', 'price' => '180 SR', 'ar_class' => 'text-base'],
                        ['ar' => 'طويل جداً', 'en' => 'V. LONG', 'price' => '210 SR', 'ar_class' => 'text-base'],
                    ],
                ],
                [
                    'ar_name' => 'استشوار ويفي',
                    'en_name' => 'Wavy Blowdry',
                    'ar_title_class' => 'text-2xl',
                    'en_title_class' => 'text-xs mt-1 !normal-case',
                    'gap_class' => 'gap-3',
                    'container_class' => '',
                    'lengths' => [
                        ['ar' => 'قصير', 'en' => 'SHORT', 'price' => '140 SR', 'ar_class' => 'text-base'],
                        ['ar' => 'وسط', 'en' => 'MEDIUM', 'price' => '180 SR', 'ar_class' => 'text-base'],
                        ['ar' => 'طويل', 'en' => 'LONG', 'price' => '230 SR', 'ar_class' => 'text-base'],
                        ['ar' => 'طويل جداً', 'en' => 'V. LONG', 'price' => '300 SR', 'ar_class' => 'text-base'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Page 5: المناكير و البادكير | MANICURES AND PEDICURES
     *
     * @return array<string, mixed>
     */
    public function getPage5Nails(): array
    {
        return [
            'page_number' => 5,
            'title_ar' => 'المناكير و البادكير',
            'title_en' => 'MANICURES AND PEDICURES',
            'layout_type' => 'simple_list',
            'items' => [
                [
                    'ar_name' => 'جلسة لاكجري سبا',
                    'ar_sub' => '(مناكير + بادكير + بارفين + يدين ورجلين + مساج اقدام واكتاف)',
                    'en_name' => 'LUXURY SPA SESSION',
                    'price' => '280 SR',
                ],
                [
                    'ar_name' => 'جلسة ريلاكس سبا',
                    'ar_sub' => '(مناكير + بادكير + مساج اقدام واكتاف)',
                    'en_name' => 'RELAX SPA SESSION',
                    'price' => '230 SR',
                ],
                [
                    'ar_name' => 'جلسة كلاسيك سبا',
                    'ar_sub' => '(مناكير + بادكير)',
                    'en_name' => 'CLASSIC SPA SESSION',
                    'price' => '180 SR',
                ],
                [
                    'ar_name' => 'مناكير (تنظيف يدين)',
                    'en_name' => 'MANICURE (HAND CLEANING)',
                    'price' => '70 SR',
                ],
                [
                    'ar_name' => 'بادكير (تنظيف اقدام)',
                    'en_name' => 'PEDICURE (FOOT CLEANING)',
                    'price' => '110 SR',
                ],
                [
                    'ar_name' => 'بادكير + مساج اقدام',
                    'en_name' => 'PEDICURE + FOOT MASSAGE',
                    'price' => '120 SR',
                ],
                [
                    'ar_name' => 'بارفين يدين',
                    'ar_sub' => '(شمع البارفين لترطيب اليدين)',
                    'en_name' => 'PARAFFIN HAND',
                    'price' => '25 SR',
                ],
                [
                    'ar_name' => 'بارفين أقدام',
                    'ar_sub' => '(شمع البارفين لترطيب القدمين)',
                    'en_name' => 'PARAFFIN FEET',
                    'price' => '35 SR',
                ],
                [
                    'ar_name' => 'برد و قص (يدين)',
                    'en_name' => 'TOUCH UP (HANDS)',
                    'price' => '25 SR',
                ],
                [
                    'ar_name' => 'برد و قص (قدمين)',
                    'en_name' => 'TOUCH UP (FEET)',
                    'price' => '35 SR',
                ],
                [
                    'ar_name' => 'لون عادي',
                    'en_name' => 'NAIL COLORING',
                    'price' => '20 SR',
                ],
                [
                    'ar_name' => 'لون فرنش',
                    'en_name' => 'FRENCH COLOR',
                    'price' => '50 SR',
                ],
                [
                    'ar_name' => 'لون آرت',
                    'en_name' => 'ART COLOR',
                    'price' => '50 SR',
                ],
                [
                    'ar_name' => 'تركيب أظافر مع لون',
                    'en_name' => 'NAIL EXTENSIONS WITH COLOR',
                    'price' => '80 SR',
                ],
            ],
        ];
    }

    /**
     * Page 6: خدمات البشرة + الرموش و الحواجب | SKIN SERVICES + EYELASHES AND EYEBROWS
     *
     * @return array<string, mixed>
     */
    public function getPage6SkinAndBrows(): array
    {
        return [
            'page_number' => 6,
            'layout_type' => 'two_sections',
            'sections' => [
                [
                    'title_ar' => 'خدمات البشرة',
                    'title_en' => 'SKIN SERVICES',
                    'title_classes' => '!mt-1 !mb-3',
                    'container_class' => 'mb-4',
                    'items' => [
                        [
                            'ar_name' => 'تنظيف البشرة',
                            'en_name' => 'FACIAL',
                            'price' => '250 SR',
                        ],
                        [
                            'ar_name' => 'جلسة تقشير البشرة بماسك الصبار',
                            'en_name' => 'ALOE VERA FACIAL SCRUB',
                            'price' => '125 SR',
                        ],
                        [
                            'ar_name' => 'فيشل الأنف',
                            'en_name' => 'NOSE FACIAL',
                            'price' => '57 SR',
                        ],
                        [
                            'ar_name' => 'تنظيف الوجه بالواكس / فتله',
                            'en_name' => 'FACE WAXING OR THREADING',
                            'price' => '80 SR',
                        ],
                        [
                            'ar_name' => 'تنظيف الشنب بالواكس / فتله',
                            'en_name' => 'UPPER LIP WAXING OR THREADING',
                            'price' => '30 SR',
                        ],
                    ],
                ],
                [
                    'title_ar' => 'الرموش و الحواجب',
                    'title_en' => 'EYELASHES AND EYEBROWS',
                    'title_classes' => '!mt-2 !mb-3',
                    'container_class' => '',
                    'items' => [
                        [
                            'ar_name' => 'مكياج كامل',
                            'en_name' => 'FULL MAKEUP',
                            'price' => '300 SR',
                        ],
                        [
                            'ar_name' => 'مكياج عيون',
                            'en_name' => 'EYES MAKEUP',
                            'price' => '250 SR',
                        ],
                        [
                            'ar_name' => 'الرموش الدائمة',
                            'en_name' => 'PERMANENT EYELASHES',
                            'price' => '500 SR',
                        ],
                        [
                            'ar_name' => 'الرموش الكاملة الإسبوعية',
                            'en_name' => 'WEEKLY EYELASHES EXTENSIONS',
                            'price' => '35 SR',
                        ],
                        [
                            'ar_name' => 'الرموش بالحبة الإسبوعية',
                            'en_name' => 'SINGLE EYELASHES EXTENSIONS',
                            'price' => '80 SR',
                        ],
                        [
                            'ar_name' => 'برم الرموش',
                            'en_name' => 'EYELASHES CURLING',
                            'price' => '350 SR',
                        ],
                        [
                            'ar_name' => 'رفع الحواجب',
                            'en_name' => 'EYEBROW LIFTING',
                            'price' => '350 SR',
                        ],
                        [
                            'ar_name' => 'تشقير الحواجب',
                            'en_name' => 'EYEBROW BLEACHING',
                            'price' => '40 SR',
                        ],
                        [
                            'ar_name' => 'صبغة الحواجب',
                            'en_name' => 'EYEBROWS COLORING',
                            'price' => '40 SR',
                        ],
                        [
                            'ar_name' => 'تشقير الحواجب + صبغة الحواجب',
                            'en_name' => 'EYEBROW BLEACHING + COLORING',
                            'price' => '80 SR',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Page 7: خدمات المساج + الحلاوة و الواكس | MASSAGE SERVICES + HOT & COLD WAX
     *
     * @return array<string, mixed>
     */
    public function getPage7MassageAndWax(): array
    {
        return [
            'page_number' => 7,
            'layout_type' => 'two_sections',
            'sections' => [
                [
                    'title_ar' => 'خدمات المساج',
                    'title_en' => 'MASSAGE SERVICES',
                    'title_classes' => '!mt-1 !mb-4',
                    'container_class' => 'mb-6 space-y-1',
                    'items' => [
                        [
                            'ar_name' => 'مساج الوجه بطريقة الشد للأعلى',
                            'en_name' => 'UPWARD LIFTING FACIAL MASSAGE',
                            'price' => '180 SR',
                        ],
                        [
                            'ar_name' => 'مساج الأكتاف والرقبة',
                            'en_name' => 'SHOULDER AND NECK MASSAGE',
                            'price' => '75 SR',
                        ],
                        [
                            'ar_name' => 'مساج الظهر والرقبة',
                            'en_name' => 'BACK AND NECK MASSAGE',
                            'price' => '120 SR',
                        ],
                        [
                            'ar_name' => 'مساج الرأس والرقبة',
                            'en_name' => 'HEAD AND NECK MASSAGE',
                            'price' => '75 SR',
                        ],
                        [
                            'ar_name' => 'مساج اليدين كاملة',
                            'en_name' => 'FULL HAND MASSAGE',
                            'price' => '85 SR',
                        ],
                        [
                            'ar_name' => 'مساج الرجلين كاملة',
                            'en_name' => 'FULL FOOT MASSAGE',
                            'price' => '97 SR',
                        ],
                        [
                            'ar_name' => 'مساج الأقدام',
                            'en_name' => 'FOOT MASSAGE',
                            'price' => '40 SR',
                        ],
                    ],
                ],
                [
                    'title_ar' => 'الحلاوة و الواكس',
                    'title_en' => 'HOT & COLD WAX',
                    'title_classes' => '!mt-4 !mb-4',
                    'container_class' => 'space-y-1',
                    'items' => [
                        [
                            'ar_name' => 'نصف اليدين',
                            'en_name' => 'HALF HANDS',
                            'price' => '50 SR',
                        ],
                        [
                            'ar_name' => 'كامل اليدين',
                            'en_name' => 'FULL HANDS',
                            'price' => '70 SR',
                        ],
                        [
                            'ar_name' => 'نصف الرجلين',
                            'en_name' => 'HALF LEGS',
                            'price' => '60 SR',
                        ],
                        [
                            'ar_name' => 'كامل الرجلين',
                            'en_name' => 'FULL LEGS',
                            'price' => '115 SR',
                        ],
                        [
                            'ar_name' => 'أندر أرم',
                            'en_name' => 'UNDER ARMS',
                            'price' => '57 SR',
                        ],
                        [
                            'ar_name' => 'بكج حلاوة / واكس',
                            'en_name' => 'WAX PACKAGE',
                            'price' => '220 SR',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Page 8: أطوال الشعر | HAIR LENGTHS
     *
     * @return array<string, mixed>
     */
    public function getPage8HairLengths(): array
    {
        return [
            'page_number' => 8,
            'title_ar' => 'أطــــوال الشــــعر',
            'title_en' => 'HAIR LENGTHS',
            'layout_type' => 'hair_lengths_guide',
        ];
    }
}
