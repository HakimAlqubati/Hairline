@props([
    'salonIdentity' => [],
    'workingHours' => [],
])

<header class="bg-sage-light pt-5 pb-6 px-4 sm:px-5 rounded-b-[28px] shadow-sm relative z-20 border-b border-white/50">
    <!-- Subtle decorative pattern overlay -->
    <div class="absolute inset-0 opacity-20 pointer-events-none rounded-b-[28px] bg-[radial-gradient(#1F3A2E_1px,transparent_1px)] [background-size:16px_16px]"></div>

    <div class="relative flex items-center justify-between gap-2">
        <!-- Right Side: Official Salon Logo + Name & Subtitle -->
        <div class="flex items-center gap-2.5 sm:gap-3.5 cursor-pointer min-w-0" onclick="switchTab('home')">
            <!-- Circular Luxury Logo with Gold Border -->
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white border-2 border-gold shadow-md flex items-center justify-center relative overflow-hidden shrink-0 p-1 sm:p-1.5">
                <img src="{{ asset('images/logo.png') }}"
                     alt="{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} - {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}"
                     class="w-full h-full object-contain">
            </div>

            <!-- Salon Title & Subtitle -->
            <div class="flex flex-col min-w-0">
                <div class="flex items-center gap-1 sm:gap-1.5">
                    <h1 class="text-base sm:text-lg font-extrabold text-sage-dark tracking-tight truncate">{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }}</h1>
                    <span class="text-gold font-light text-sm sm:text-base">|</span>
                    <span class="text-xs sm:text-sm font-bold tracking-wider text-sage-dark font-serif">{{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}</span>
                </div>
                <span class="text-[10px] sm:text-[11px] font-semibold tracking-[0.2em] sm:tracking-[0.22em] text-gold-dark uppercase mt-0.5">
                    {{ $salonIdentity['tagline_en'] ?? 'BEAUTY LOUNGE' }}
                </span>
            </div>
        </div>

        <!-- Left Side: Quick Action Circular Icons + Sidebar Menu Button -->
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <!-- 1. Fullscreen Toggle Button -->
            <button id="fullscreenBtn" 
                    onclick="toggleFullscreen()" 
                    title="ملء الشاشة"
                    aria-label="ملء الشاشة"
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-gold-light hover:text-gold-dark transition active:scale-95">
                <i id="fullscreenIcon" class="fa-solid fa-expand text-xs sm:text-sm"></i>
            </button>

            <!-- 2. Quick Working Hours / Status Icon -->
            <button onclick="openToast('الصالون مفتوح الآن • نستقبلكن {{ $workingHours['hours_ar'] ?? 'من 10:00 ص حتى 10:00 م' }}')" 
                    title="أوقات العمل"
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-gold-light hover:text-gold-dark transition active:scale-95 relative">
                <span class="w-2 h-2 rounded-full bg-emerald-500 absolute top-1.5 right-1.5 sm:top-2 sm:right-2 ring-2 ring-white"></span>
                <i class="fa-regular fa-bell text-xs sm:text-sm"></i>
            </button>

            <!-- 3. Quick Location / Contact Icon -->
            <button onclick="openSidebarSection('contact')" 
                    title="موقع الصالون والتواصل"
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-gold-light hover:text-gold-dark transition active:scale-95">
                <i class="fa-solid fa-location-dot text-xs sm:text-sm text-gold-dark"></i>
            </button>

            <!-- 4. Far-Left Sidebar Menu Button (Three Dots) -->
            <button id="menuBtn" 
                    onclick="toggleSidebar(true)" 
                    aria-label="القائمة الجانبية"
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-sage-dark hover:text-white transition active:scale-95 border border-gold/20">
                <i class="fa-solid fa-ellipsis-vertical text-xs sm:text-base"></i>
            </button>
        </div>
    </div>
</header>
