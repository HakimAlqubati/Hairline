<nav aria-label="شريط التنقل السفلي" class="md:hidden fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white border-t border-gray-100 shadow-floating px-3 py-2 z-30 rounded-t-[26px]">
    <div class="grid grid-cols-5 items-center text-center">
        
        <!-- 1. الرئيسية (Far Right) -->
        <button onclick="switchTab('home')" id="nav-home" class="nav-item flex flex-col items-center justify-center py-1 text-sage-dark font-bold transition">
            <i class="fa-solid fa-house text-lg mb-1"></i>
            <span class="text-[11px]">الرئيسية</span>
            <span class="w-1.5 h-1.5 rounded-full bg-gold mt-0.5 nav-dot"></span>
        </button>

        <!-- 2. الخدمات -->
        <button onclick="switchTab('services')" id="nav-services" class="nav-item flex flex-col items-center justify-center py-1 text-gray-400 hover:text-sage-dark font-medium transition cursor-pointer">
            <i class="fa-solid fa-spa text-lg mb-1"></i>
            <span class="text-[11px]">الخدمات</span>
            <span class="w-1.5 h-1.5 rounded-full bg-transparent mt-0.5 nav-dot"></span>
        </button>

        <!-- 3. احجزي (Central Large Prominent Circular Button in Dark Forest Green) -->
        <div class="flex flex-col items-center justify-center relative">
            <button onclick="openBookingFlow()" 
                    aria-label="احجزي موعدك"
                    class="-mt-8 w-16 h-16 rounded-full bg-sage-dark text-white shadow-fab border-4 border-white flex flex-col items-center justify-center hover:bg-sage-deeper active:scale-95 transition-all group">
                <i class="fa-regular fa-calendar-check text-xl text-gold group-hover:scale-110 transition-transform"></i>
            </button>
            <span class="text-[11px] font-extrabold text-sage-dark mt-1">احجزي</span>
        </div>

        <!-- 4. عن هيرلاين -->
        <button onclick="switchTab('about')" id="nav-about" class="nav-item flex flex-col items-center justify-center py-1 text-gray-400 hover:text-sage-dark font-medium transition">
            <i class="fa-solid fa-gem text-lg mb-1"></i>
            <span class="text-[11px]">عن هيرلاين</span>
            <span class="w-1.5 h-1.5 rounded-full bg-transparent mt-0.5 nav-dot"></span>
        </button>

        <!-- 5. حسابي (Far Left) -->
        <button onclick="switchTab('account')" id="nav-account" class="nav-item flex flex-col items-center justify-center py-1 text-gray-400 hover:text-sage-dark font-medium transition relative">
            <i class="fa-regular fa-user text-lg mb-1"></i>
            <span class="text-[11px]">حسابي</span>
            <span id="bookingCountBadge" class="hidden absolute top-0.5 left-6 w-4 h-4 rounded-full bg-gold text-white text-[10px] font-bold flex items-center justify-center">1</span>
            <span class="w-1.5 h-1.5 rounded-full bg-transparent mt-0.5 nav-dot"></span>
        </button>

    </div>
</nav>
