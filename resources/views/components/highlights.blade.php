<section class="bg-white rounded-2xl p-4 shadow-card border border-gold-soft/40">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <span class="w-2 h-5 rounded-full bg-gold"></span>
            <h3 class="font-extrabold text-sm text-sage-dark">الأكثر طلباً للحجز هذا الأسبوع</h3>
        </div>
        <button onclick="openBookingFlowWithCategory('hot_treatments')" class="text-xs font-bold text-gold-dark hover:underline flex items-center gap-1">
            <span>احجزي الآن</span>
            <span>‹</span>
        </button>
    </div>

    <div class="grid grid-cols-2 gap-2.5">
        <div onclick="openBookingFlowWithCategory('hot_treatments')" 
             class="p-3 rounded-xl bg-sage-soft/70 border border-sage-light hover:border-gold cursor-pointer transition flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-sage-dark text-gold flex items-center justify-center shrink-0">
                <i class="fa-solid fa-fire-flame-curved text-base"></i>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-sage-dark">المعالجات الحارة</h4>
                <p class="text-[11px] text-gray-500">بروتين • كافيار • كولاجين</p>
            </div>
        </div>

        <div onclick="openBookingFlowWithCategory('cold_restore')" 
             class="p-3 rounded-xl bg-gold-light/60 border border-gold-soft/60 hover:border-gold cursor-pointer transition flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-gold text-white flex items-center justify-center shrink-0">
                <i class="fa-solid fa-wand-magic-sparkles text-base"></i>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-sage-dark">الترميم البارد</h4>
                <p class="text-[11px] text-gray-500">مورفوسيس • ترندي • كيرلي</p>
            </div>
        </div>
    </div>
</section>
