<section id="accountTabView" class="hidden flex-1 px-4 pt-5 space-y-4 animate-step">
    <!-- Member Profile Card -->
    <div class="bg-white rounded-3xl p-5 shadow-card border border-gold-soft/40 flex items-center justify-between">
        <div class="flex items-center gap-3.5">
            <div class="w-14 h-14 rounded-full bg-gold-light border-2 border-gold flex items-center justify-center text-gold-dark text-xl font-bold">
                <i class="fa-regular fa-user"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-extrabold text-base text-sage-dark">أهلاً بكِ، عميلتنا المميزة</h3>
                    <span class="px-2 py-0.5 rounded-full bg-gold text-white text-[10px] font-bold">VIP</span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">عضوية هيرلاين الذهبية • 240 نقطة ولاء</p>
            </div>
        </div>
    </div>

    <!-- My Bookings List -->
    <div class="bg-white rounded-3xl p-5 shadow-card border border-gold-soft/40 space-y-3">
        <div class="flex items-center justify-between">
            <h4 class="font-extrabold text-sm text-sage-dark flex items-center gap-2">
                <i class="fa-regular fa-calendar-check text-gold-dark"></i>
                <span>حجوزاتي المؤكدة</span>
            </h4>
            <button onclick="openBookingFlow()" class="text-xs font-bold text-sage-dark bg-sage-soft px-3 py-1 rounded-full hover:bg-sage-dark hover:text-white transition">
                + حجز جديد
            </button>
        </div>
        <div id="myBookingsContainer" class="space-y-2.5">
            <!-- Populated via JS -->
        </div>
    </div>
</section>
