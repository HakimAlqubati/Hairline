<?php

declare(strict_types=1);

return [
    'brand' => [
        'name' => 'صالون هيرلاين',
        'subname' => 'لوحة الإدارة المتطورة',
    ],

    'nav' => [
        'bookings' => 'إدارة الحجوزات والمواعيد',
        'services_catalog' => 'كتالوج الخدمات والأسعار',
        'specialists_team' => 'فريق العمل والأخصائيات',
        'customers_loyalty' => 'العملاء وبرنامج الولاء',
        'website_content' => 'إدارة محتوى الموقع والواجهة',
        'financials_logistics' => 'المالية والتغطية الجغرافية',
        'system_settings' => 'إعدادات النظام والمشرفين',
    ],

    'resources' => [
        // Bookings & Operations
        'booking' => 'حجز',
        'bookings' => 'الحجوزات',
        'booking_item' => 'بند خدمة بالحجز',
        'booking_items' => 'بنود الخدمات بالحجز',
        'specialist_shift' => 'مناوبة أخصائية',
        'specialist_shifts' => 'مناوبات الأخصائيات',
        'specialist_unavailability' => 'فترة غياب أو إجازة',
        'specialist_unavailabilities' => 'فترات الغياب والإجازات',

        // Services & Catalog
        'service' => 'خدمة',
        'services' => 'الخدمات',
        'service_category' => 'تصنيف خدمات',
        'service_categories' => 'تصنيفات الخدمات',
        'service_group' => 'مجموعة خدمات',
        'service_groups' => 'مجموعات الخدمات',
        'hair_length' => 'مستوى طول الشعر',
        'hair_lengths' => 'مستويات طول الشعر',
        'pricing_tier' => 'تسعيرة حسب الطول',
        'pricing_tiers' => 'تسعيرات أطوال الشعر',
        'catalog_page' => 'صفحة كتالوج',
        'catalog_pages' => 'صفحات الكتالوج',

        // Staff & Specialists
        'specialist' => 'أخصائية',
        'specialists' => 'الأخصائيات',

        // Customers & Loyalty
        'customer' => 'عميلة',
        'customers' => 'العميلات',
        'customer_address' => 'عنوان خدمة منزلية',
        'customer_addresses' => 'عناوين الخدمة المنزلية',
        'membership_tier' => 'مستوى عضوية',
        'membership_tiers' => 'مستويات العضوية',
        'loyalty_point_transaction' => 'حركة نقاط ولاء',
        'loyalty_point_transactions' => 'حركات نقاط الولاء',

        // CMS & Website
        'hero_slide' => 'شريحة رئيسية',
        'hero_slides' => 'شرائح الواجهة الرئيسية',
        'salon_gallery' => 'صورة بالمعرض',
        'salon_galleries' => 'معرض الأعمال',
        'founder_story' => 'قصة المؤسس',
        'founder_stories' => 'قصص المؤسسين',
        'cancellation_policy' => 'سياسة إلغاء',
        'cancellation_policies' => 'سياسات الإلغاء',
        'salon_working_hour' => 'ساعات عمل يومية',
        'salon_working_hours' => 'أوقات العمل الأسبوعية',

        // Financials & Logistics
        'payment' => 'معاملة مالية',
        'payments' => 'المدفوعات',
        'payment_method' => 'طريقة دفع',
        'payment_methods' => 'طرق الدفع',
        'district' => 'حي مخدوم',
        'districts' => 'أحياء التغطية ورسوم التوصيل',

        // System
        'salon_setting' => 'إعدادات الصالون',
        'salon_settings' => 'الإعدادات العامة للصالون',
        'user' => 'مشرف نظام',
        'users' => 'المشرفون والمدراء',
    ],

    'sections' => [
        'general_information' => 'المعلومات العامة',
        'service_details' => 'تفاصيل الخدمة والتصنيف',
        'pricing_and_duration' => 'الأسعار والوقت المقدر',
        'media' => 'الوسائط والصور',
        'booking_info' => 'بيانات الحجز والموعد',
        'customer_info' => 'بيانات العميلة والتواصل',
        'financial_summary' => 'الملخص المالي والضرائب',
        'status_and_notes' => 'الحالة وملاحظات الإدارة',
        'specialist_profile' => 'الملف الشخصي والخبرات',
        'work_schedule' => 'جدول العمل والمناوبات',
        'contact_and_social' => 'بيانات التواصل وحسابات التواصل الاجتماعي',
        'location_and_tax' => 'الموقع الجغرافي والبيانات الضريبية',
        'membership_rewards' => 'المكافآت وامتيازات العضوية',
        'security' => 'بيانات الدخول والأمان',
    ],

    'fields' => [
        // Common
        'id' => 'المعرف',
        'name' => 'الاسم',
        'slug' => 'الرابط الدائم (Slug)',
        'title' => 'العنوان',
        'subtitle' => 'العنوان الفرعي',
        'description' => 'الوصف التفصيلي',
        'notes' => 'ملاحظات',
        'phone' => 'رقم الهاتف',
        'email' => 'البريد الإلكتروني',
        'avatar' => 'الصورة الشخصية',
        'image' => 'الصورة',
        'sort_order' => 'الترتيب',
        'is_active' => 'مفعل',
        'is_featured' => 'مميز في الواجهة',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'آخر تحديث',

        // Services & Catalog
        'category' => 'التصنيف الرئيسي',
        'service_group' => 'مجموعة الخدمات',
        'hair_length' => 'طول الشعر',
        'base_price' => 'السعر الأساسي',
        'price' => 'السعر',
        'duration_minutes' => 'المدة المقدرة',
        'extra_duration_minutes' => 'مدة إضافية',
        'custom_label' => 'المسمى المخصص',
        'price_display_override' => 'نص السعر البديل (مثال: يبدأ من)',
        'inclusions' => 'المشتملات والمزايا',
        'icon' => 'الأيقونة',
        'container_class' => 'فئة التصميم (CSS Class)',
        'type' => 'النوع',
        'page_number' => 'رقم الصفحة',
        'layout_type' => 'نوع التخطيط',
        'meta' => 'البيانات الوصفية (Meta)',

        // Specialists
        'role' => 'المسمى الوظيفي',
        'experience' => 'سنوات الخبرة',
        'bio' => 'نبذة تعريفية',
        'is_home_specialist' => 'متاحة للخدمة المنزلية',
        'day_of_week' => 'يوم الأسبوع',
        'start_time' => 'وقت البدء',
        'end_time' => 'وقت الانتهاء',
        'is_day_off' => 'يوم عطلة / إجازة',
        'start_datetime' => 'تاريخ ووقت البدء',
        'end_datetime' => 'تاريخ ووقت الانتهاء',
        'reason' => 'السبب',

        // Customers & Loyalty
        'customer_code' => 'كود العميلة',
        'membership_tier' => 'مستوى العضوية',
        'total_points' => 'إجمالي النقاط',
        'min_spend' => 'الحد الأدنى للإنفاق',
        'discount_percentage' => 'نسبة الخصم %',
        'points_multiplier' => 'مضاعف النقاط',
        'badge_color' => 'لون الشارة',
        'points_change' => 'حركة النقاط (+/-)',
        'balance_after' => 'الرصيد بعد الحركة',

        // Addresses & Districts
        'district' => 'الحي',
        'zone' => 'المنطقة',
        'street_name' => 'اسم الشارع',
        'building_number' => 'رقم المبنى',
        'floor_number' => 'رقم الطابق',
        'latitude' => 'خط العرض',
        'longitude' => 'خط الطول',
        'is_default' => 'العنوان الافتراضي',
        'delivery_fee' => 'رسوم التوصيل',
        'estimated_transit_minutes' => 'وقت الوصول المقدر',

        // Bookings
        'booking_number' => 'رقم الحجز',
        'customer' => 'العميلة',
        'customer_address' => 'عنوان الخدمة المنزلية',
        'booking_type' => 'نوع الحجز',
        'booking_date' => 'تاريخ الموعد',
        'overall_start_time' => 'وقت البدء العام',
        'overall_end_time' => 'وقت الانتهاء العام',
        'status' => 'حالة الحجز',
        'subtotal_amount' => 'المجموع الفرعي',
        'discount_amount' => 'قيمة الخصم',
        'vat_amount' => 'ضريبة القيمة المضافة',
        'total_amount' => 'الإجمالي النهائي',
        'deposit_required' => 'العربون المطلوب',
        'deposit_paid' => 'العربون المدفوع',
        'payment_status' => 'حالة الدفع',
        'admin_notes' => 'ملاحظات الإدارة',

        // Booking Items
        'service' => 'الخدمة',
        'specialist' => 'الأخصائية',
        'unit_price' => 'سعر الوحدة',
        'scheduled_start_time' => 'الوقت المجدول للبدء',
        'scheduled_end_time' => 'الوقت المجدول للانتهاء',

        // Payments
        'booking' => 'الحجز التابع له',
        'payment_method' => 'طريقة الدفع',
        'transaction_reference' => 'رقم العملية المرجعي',
        'amount' => 'المبلغ',
        'payment_type' => 'نوع الدفعة',
        'paid_at' => 'تاريخ ووقت السداد',
        'gateway_response' => 'رد بوابة الدفع',
        'code' => 'الرمز التعريفي',
        'is_online' => 'دفع إلكتروني',
        'instructions' => 'تعليمات الدفع',

        // CMS
        'badge_text' => 'نص الشارة',
        'fallback_url' => 'الرابط البديل',
        'tag' => 'الوسم',
        'aspect_ratio' => 'نسبة أبعاد الصورة',
        'founder_name' => 'اسم المؤسس',
        'founder_title' => 'المسمى الرسمي',
        'intro_text' => 'المقدمة',
        'description_text' => 'النص التعريفي',
        'vision_quote' => 'اقتباس الرؤية',
        'experience_badge' => 'وسام سنوات الخبرة',
        'signature_image_path' => 'صورة التوقيع',
        'portrait_image_path' => 'الصورة الشخصية',
        'policy_text' => 'نص السياسة والشروط',
        'free_cancellation_hours' => 'مهلة الإلغاء المجاني (بالساعات)',
        'partial_refund_hours' => 'مهلة الاسترجاع الجزئي (بالساعات)',
        'partial_refund_percentage' => 'نسبة الاسترجاع الجزئي %',
        'no_show_penalty_percentage' => 'غرامة عدم الحضور %',
        'day_label' => 'اسم اليوم',
        'open_time' => 'ساعة الفتح',
        'close_time' => 'ساعة الإغلاق',
        'is_closed' => 'مغلق بالكامل (عطلة)',

        // Settings
        'tagline' => 'الشعار اللفظي (Slogan)',
        'description_line1' => 'وصف الصالون (السطر الأول)',
        'description_line2' => 'وصف الصالون (السطر الثاني)',
        'address' => 'العنوان الفعلي',
        'logo_path' => 'شعار الصالون (Logo)',
        'whatsapp' => 'واتساب',
        'instagram' => 'إنستغرام',
        'tiktok' => 'تيك توك',
        'snapchat' => 'سناب شات',
        'google_maps_url' => 'رابط خرائط جوجل',
        'tax_number' => 'الرقم الضريبي',
        'vat_percentage' => 'نسبة الضريبة المضافة %',
        'stats' => 'الإحصائيات والأرقام الترويجية',

        // Users
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
    ],

    'statuses' => [
        // Bookings
        'pending' => 'قيد الانتظار',
        'confirmed' => 'مؤكد',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتمل بنجاح',
        'cancelled' => 'ملغي',
        'no_show' => 'لم تحضر (No-Show)',

        // Payments
        'unpaid' => 'غير مدفوع',
        'partially_paid' => 'مدفوع جزئياً',
        'fully_paid' => 'مدفوع بالكامل',
        'refunded' => 'مسترجع',
        'success' => 'عملية ناجحة',
        'failed' => 'عملية فاشلة',

        // Common
        'active' => 'نشط',
        'inactive' => 'غير نشط',
    ],

    'options' => [
        'booking_type_salon' => 'داخل الصالون',
        'booking_type_home' => 'خدمة منزلية',
        'payment_type_deposit' => 'عربون مبدئي',
        'payment_type_final' => 'دفعة نهائية',
        'payment_type_refund' => 'استرجاع مالي',
        'days' => [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ],
    ],

    'filters' => [
        'category' => 'حسب التصنيف',
        'status' => 'حسب الحالة',
        'booking_type' => 'حسب نوع الحجز',
        'payment_status' => 'حسب حالة الدفع',
        'active_status' => 'الحسابات المفعلة',
        'featured_status' => 'العناصر المميزة',
        'specialist' => 'حسب الأخصائية',
        'district' => 'حسب الحي',
        'date' => 'حسب التاريخ',
    ],

    'actions' => [
        'create' => 'إضافة جديد',
        'edit' => 'تعديل',
        'view' => 'عرض التفاصيل',
        'delete' => 'حذف',
        'save' => 'حفظ البيانات',
        'cancel' => 'إلغاء',
        'confirm' => 'تأكيد',
    ],

    'units' => [
        'minutes' => 'دقيقة',
        'currency' => 'ر.س',
        'points' => 'نقطة',
        'percentage' => '%',
        'hours' => 'ساعة',
    ],

    'helpers' => [
        'price_display_override' => 'نص مخصص يظهر في بطاقة الخدمة بدلاً من السعر الرقمي المباشر، مثل: "يبدأ من 150 ر.س"',
        'phone_format' => 'يرجى كتابة رقم الهاتف بصيغة دولية مثل: 9665xxxxxxxx',
        'slug_auto' => 'يتم توليده تلقائياً من الاسم للاستخدام في روابط المتصفح',
    ],
];
