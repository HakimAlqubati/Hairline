<section aria-label="أقسام الخدمات الرئيسية" class="space-y-3.5">
    
    <!-- Row 1: جميع الخدمات (زيارة فورية) -->
    <button onclick="switchTab('services')" 
            class="w-full relative overflow-hidden bg-white rounded-2xl p-4 sm:p-5 shadow-card border border-gold-soft/50 hover:border-primary hover:shadow-lg hover:shadow-primary/10 transition-all duration-300 active:scale-[0.985] cursor-pointer text-center group">
        <!-- Top subtle accent shimmer line -->
        <div class="absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-primary/30 to-transparent group-hover:via-primary transition-all duration-300"></div>

        <div class="flex flex-col items-center justify-center gap-1.5">
            <div class="flex items-center justify-center gap-2">
                <span class="text-base sm:text-lg font-extrabold text-sage-dark group-hover:text-primary transition-colors">
                    جميع الخدمات
                </span>
                <!-- Explanatory Badge: (زيارة فورية) -->
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-light text-primary-dark border border-primary/20 badge-pulse">
                    <i class="fa-solid fa-sparkles text-[10px]"></i>
                    (زيارة فورية)
                </span>
            </div>
            
            <p class="text-xs text-gray-500 group-hover:text-primary-dark transition-colors font-medium flex items-center justify-center gap-1.5">
                <span>انقري للاطلاع على القائمة والأسعار</span>
                <span class="text-xs transition-transform duration-300 group-hover:-translate-x-1">‹</span>
            </p>
        </div>
    </button>

    <!-- Row 2: الخدمات المتاحة للحجز -->
    <button onclick="openBookingFlow()" 
            class="w-full relative overflow-hidden bg-white rounded-2xl p-4 sm:p-5 shadow-card border border-gold-soft/50 hover:border-sage-dark hover:shadow-lg hover:shadow-sage-dark/10 transition-all duration-300 active:scale-[0.985] cursor-pointer text-center group">
        <!-- Top subtle accent shimmer line -->
        <div class="absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-sage-medium/30 to-transparent group-hover:via-sage-dark transition-all duration-300"></div>

        <div class="flex flex-col items-center justify-center gap-1">
            <span class="text-base sm:text-lg font-extrabold text-sage-dark group-hover:text-sage-deeper transition-colors">
                الخدمات المتاحة للحجز
            </span>
            <span class="text-xs font-medium text-gray-500">
                اختاري الخدمة والأخصائية المفضلة لديكِ
            </span>
            <div class="mt-1.5 inline-flex items-center gap-1.5 text-xs font-bold text-primary-dark bg-primary-light/80 group-hover:bg-primary group-hover:text-white px-3.5 py-1 rounded-full border border-primary/25 transition-all duration-300">
                <span>احجزي موعدكِ الآن</span>
                <span class="text-xs transition-transform duration-300 group-hover:-translate-x-1">‹</span>
            </div>
        </div>
    </button>

</section>
