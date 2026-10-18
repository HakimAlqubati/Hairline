<section aria-label="أقسام الخدمات الرئيسية" class="space-y-3.5">
    
    <!-- Row 1: جميع الخدمات (زيارة فورية) -->
    <button onclick="openAllServicesModal()" 
            class="w-full bg-white rounded-2xl p-4 shadow-card border border-gold-soft/40 flex items-center justify-between group hover:border-gold transition-all duration-200 active:scale-[0.99] text-right">
        <div class="flex items-center gap-3.5">
            <!-- Colored Small Dot on the Right -->
            <span class="w-3 h-3 rounded-full bg-gold shadow-sm ring-4 ring-gold-light shrink-0"></span>
            
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-base sm:text-lg font-extrabold text-sage-dark group-hover:text-sage-deeper">
                    جميع الخدمات
                </span>
                <!-- Explanatory Badge: (زيارة فورية) -->
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gold-light text-gold-dark border border-gold/30 badge-pulse">
                    <i class="fa-solid fa-sparkles text-[10px]"></i>
                    (زيارة فورية)
                </span>
            </div>
        </div>

        <!-- Circular Arrow Button (‹) on the Left -->
        <div class="w-9 h-9 rounded-full bg-sage-soft text-sage-dark group-hover:bg-sage-dark group-hover:text-white flex items-center justify-center transition-colors shrink-0">
            <span class="text-xl font-bold leading-none -mt-0.5">‹</span>
        </div>
    </button>

    <!-- Row 2: الخدمات المتاحة للحجز -->
    <button onclick="openBookingFlow()" 
            class="w-full bg-white rounded-2xl p-4 shadow-card border border-gold-soft/40 flex items-center justify-between group hover:border-sage-dark/40 transition-all duration-200 active:scale-[0.99] text-right">
        <div class="flex items-center gap-3.5">
            <!-- Colored Small Dot on the Right -->
            <span class="w-3 h-3 rounded-full bg-sage-dark shadow-sm ring-4 ring-sage-light shrink-0"></span>
            
            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2.5">
                <span class="text-base sm:text-lg font-extrabold text-sage-dark">
                    الخدمات المتاحة للحجز
                </span>
                <span class="text-xs font-medium text-gray-500">
                    اختاري الخدمة والأخصائية المفضلة لديكِ
                </span>
            </div>
        </div>

        <!-- Circular Arrow Button (‹) on the Left -->
        <div class="w-9 h-9 rounded-full bg-gold-light text-gold-dark group-hover:bg-sage-dark group-hover:text-white flex items-center justify-center transition-colors shrink-0">
            <span class="text-xl font-bold leading-none -mt-0.5">‹</span>
        </div>
    </button>

</section>
