<section aria-label="أقسام الخدمات الرئيسية" class="space-y-3.5 md:space-y-0 md:grid md:grid-cols-2 md:gap-5">
    
    <!-- Row 1: جميع الخدمات (زيارة فورية) -->
    <button onclick="switchTab('services')" 
            class="w-full relative overflow-hidden bg-white rounded-2xl p-4 sm:p-5 shadow-card border border-gold-soft/50 hover:border-primary hover:shadow-lg hover:shadow-primary/10 transition-all duration-300 active:scale-[0.985] cursor-pointer text-center group">
        <!-- Top subtle accent shimmer line -->
        <div class="absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-primary/30 to-transparent group-hover:via-primary transition-all duration-300"></div>

        <div class="flex flex-col items-center justify-center gap-1">
            <span class="text-base sm:text-lg font-extrabold text-sage-dark group-hover:text-primary transition-colors">
                جميع الخدمات
            </span>
            <span class="text-xs font-medium text-gray-500">
                (زيارة فورية)
            </span>
            
            <p class="text-xs text-gray-500 group-hover:text-primary-dark transition-colors font-medium flex items-center justify-center gap-1.5">
                <span>الاطلاع على قائمة الخدمات والأسعار</span>
                <span class="text-xs transition-transform duration-300 group-hover:-translate-x-1">‹</span>
            </p>
        </div>
    </button>

    <!-- Row 2: الخدمات المتاحة للحجز -->
    <button onclick="openBookingFlow()" 
            class="w-full relative overflow-hidden bg-white rounded-2xl p-4 sm:p-5 shadow-card border border-gold-soft/50 hover:border-primary hover:shadow-lg hover:shadow-primary/10 transition-all duration-300 active:scale-[0.985] cursor-pointer text-center group">
        <!-- Top subtle accent shimmer line -->
        <div class="absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-primary/30 to-transparent group-hover:via-primary transition-all duration-300"></div>

        <div class="flex flex-col items-center justify-center gap-1">
            <span class="text-base sm:text-lg font-extrabold text-sage-dark group-hover:text-primary transition-colors">
                الخدمات المتاحة للحجز
            </span>
            <span class="text-xs font-medium text-gray-500">
                اختاري الخدمة والأخصائية المفضلة لديكِ
            </span>
            <p class="text-xs text-gray-500 group-hover:text-primary-dark transition-colors font-medium flex items-center justify-center gap-1.5">
                <span>احجزي موعدكِ الآن</span>
                <span class="text-xs transition-transform duration-300 group-hover:-translate-x-1">‹</span>
            </p>
        </div>
    </button>

</section>
