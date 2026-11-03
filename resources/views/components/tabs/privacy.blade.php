@props([
    'isActive' => false,
    'salonIdentity' => [],
])

<section id="privacyTabView" class="{{ $isActive ? '' : 'hidden' }} flex-1 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 md:pt-8 pb-16 space-y-6 animate-step text-right">

    <!-- Header Section -->
    <div class="space-y-1.5 pb-2 border-b border-primary-soft/50">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-light/70 text-primary-dark text-xs font-black">
            <i class="fa-solid fa-shield-halved text-[11px]"></i>
            <span>السرية والأمان</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-black text-sage-dark tracking-tight">سياسة الخصوصية وسرية البيانات</h2>
        <p class="text-xs sm:text-sm text-gray-500 font-medium">التزامنا الصارم بخصوصية جلساتكِ وأمان وسرية بياناتكِ المسجلة لدينا</p>
    </div>

    <!-- Main Content: Clean Luxury Grid -->
    <div class="bg-white rounded-[32px] p-6 sm:p-8 md:p-10 shadow-card border border-primary-soft/60 space-y-8">
        
        <!-- Pillar 1: Salon Privacy -->
        <div class="flex items-start gap-4 sm:gap-5">
            <div class="w-12 h-12 rounded-2xl bg-sage-dark text-gold flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                <i class="fa-solid fa-door-closed text-lg"></i>
            </div>
            <div class="space-y-1.5 min-w-0">
                <h3 class="text-base sm:text-lg font-black text-sage-dark">خصوصية المكان والبيئة النسائية</h3>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-medium">
                    نضمن لكِ في صالون هيرلاين بيئة نسائية آمنة ومحمية بالكامل 100%. تم تصميم وتجهيز كافة أقسام الصالون وغرف المعالجات لتوفير أقصى درجات الراحة والهدوء والسكينة لعميلاتنا طوال فترة الزيارة.
                </p>
            </div>
        </div>

        <div class="h-px bg-gray-100"></div>

        <!-- Pillar 2: Strict No-Photography Policy -->
        <div class="flex items-start gap-4 sm:gap-5">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                <i class="fa-solid fa-camera-slash text-lg"></i>
            </div>
            <div class="space-y-1.5 min-w-0">
                <h3 class="text-base sm:text-lg font-black text-sage-dark">حظر التصوير الصارم داخل الصالون</h3>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-medium">
                    يُمنع منعاً باتاً التقاط الصور أو تسجيل مقاطع الفيديو بكاميرات الهواتف في كافة مرافق وأقسام الصالون، صوناً لخصوصية العميلات. وأي توثيق لنتائج المعالجات أو التسريحات لا يتم إطلاقاً إلا بطلب وموافقة خطية واضحة من العميلة وبما يحفظ هويتها بالكامل.
                </p>
            </div>
        </div>

        <div class="h-px bg-gray-100"></div>

        <!-- Pillar 3: Personal Data Privacy -->
        <div class="flex items-start gap-4 sm:gap-5">
            <div class="w-12 h-12 rounded-2xl bg-primary-light text-primary-dark flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                <i class="fa-solid fa-user-shield text-lg"></i>
            </div>
            <div class="space-y-1.5 min-w-0">
                <h3 class="text-base sm:text-lg font-black text-sage-dark">حماية وسرية البيانات الشخصية</h3>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-medium">
                    بياناتكِ المسجلة (الاسم، رقم الجوال، وتفاصيل المواعيد) مشفرة ومحفوظة وفق أعلى معايير الحماية الرقمية. تُستخدم هذه البيانات حصراً لتأكيد حجوزاتكِ وإرسال تذكيرات المواعيد، ولا يتم مشاركتها أو بيعها لأي طرف ثالث تحت أي ظرف.
                </p>
            </div>
        </div>

        <div class="h-px bg-gray-100"></div>

        <!-- Pillar 4: Secure Transactions -->
        <div class="flex items-start gap-4 sm:gap-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                <i class="fa-solid fa-lock text-lg"></i>
            </div>
            <div class="space-y-1.5 min-w-0">
                <h3 class="text-base sm:text-lg font-black text-sage-dark">أمان وسلامة المدفوعات</h3>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-medium">
                    تتم كافة عمليات الدفع والحجوزات عبر بوابات دفع إلكترونية مرخصة ومعتمدة من البنك المركزي السعودي، دون تخزين أو حفظ أي معلومات للبطاقات البنكية في خوادمنا.
                </p>
            </div>
        </div>

    </div>

</section>
