<section class="bg-white rounded-2xl p-4 shadow-card border border-gold-soft/40">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <span class="w-2 h-5 rounded-full bg-gold"></span>
            <h3 class="font-extrabold text-sm text-sage-dark">الأكثر طلباً للحجز هذا الأسبوع</h3>
        </div>
        <button onclick="openBookingFlow()" class="text-xs font-bold text-gold-dark hover:underline flex items-center gap-1">
            <span>عرض الكل</span>
            <span>‹</span>
        </button>
    </div>

    <div class="grid grid-cols-2 gap-2.5">
        <!-- 1. معالجات الشعر -->
        <div onclick="openBookingFlowWithCategory('hair_treatments')" 
             class="p-3 rounded-xl bg-sage-soft/70 border border-sage-light hover:border-gold cursor-pointer transition flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-sage-dark text-gold flex items-center justify-center shrink-0">
                <i class="fa-solid fa-fire-flame-curved text-base"></i>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-sage-dark">معالجات الشعر</h4>
                <p class="text-[11px] text-gray-500">بروتين تمليس • فيلر مورفوسيس</p>
            </div>
        </div>

        <!-- 2. خدمات الشعر -->
        <div onclick="openBookingFlowWithCategory('hair_services')" 
             class="p-3 rounded-xl bg-gold-light/60 border border-gold-soft/60 hover:border-gold cursor-pointer transition flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-gold text-white flex items-center justify-center shrink-0">
                <i class="fa-solid fa-scissors text-base"></i>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-sage-dark">خدمات الشعر</h4>
                <p class="text-[11px] text-gray-500">تساريح • صبغات • قص كامل</p>
            </div>
        </div>

        <!-- 3. الاستشوار والتصفيف -->
        <div onclick="openBookingFlowWithCategory('blowdry')" 
             class="p-3 rounded-xl bg-gold-light/60 border border-gold-soft/60 hover:border-gold cursor-pointer transition flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-gold text-white flex items-center justify-center shrink-0">
                <i class="fa-solid fa-wind text-base"></i>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-sage-dark">الاستشوار والتصفيف</h4>
                <p class="text-[11px] text-gray-500">استشوار عادي • ويفي مموج</p>
            </div>
        </div>

        <!-- 4. المناكير والبادكير -->
        <div onclick="openBookingFlowWithCategory('nails')" 
             class="p-3 rounded-xl bg-sage-soft/70 border border-sage-light hover:border-gold cursor-pointer transition flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-sage-dark text-gold flex items-center justify-center shrink-0">
                <i class="fa-solid fa-hand-sparkles text-base"></i>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-sage-dark">المناكير والبادكير</h4>
                <p class="text-[11px] text-gray-500">جلسات سبا • عناية باليدين</p>
            </div>
        </div>
    </div>
</section>
