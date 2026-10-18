@props([
    'salonIdentity' => [],
    'workingHours' => [],
])

<header class="bg-sage-light pt-5 pb-6 px-5 rounded-b-[28px] shadow-sm relative z-20 border-b border-white/50">
    <!-- Subtle decorative pattern overlay -->
    <div class="absolute inset-0 opacity-20 pointer-events-none rounded-b-[28px] bg-[radial-gradient(#1F3A2E_1px,transparent_1px)] [background-size:16px_16px]"></div>

    <div class="relative flex items-center justify-between">
        <!-- Right Side: Official Salon Logo + Name & Subtitle -->
        <div class="flex items-center gap-3.5 cursor-pointer" onclick="switchTab('home')">
            <!-- Circular Luxury Logo with Gold Border -->
            <div class="w-14 h-14 rounded-full bg-white border-2 border-gold shadow-md flex items-center justify-center relative overflow-hidden shrink-0 p-1.5">
                <img src="{{ asset('images/logo.png') }}"
                     alt="{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} - {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}"
                     class="w-full h-full object-contain">
            </div>

            <!-- Salon Title & Subtitle -->
            <div class="flex flex-col">
                <div class="flex items-center gap-1.5">
                    <h1 class="text-lg font-extrabold text-sage-dark tracking-tight">{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }}</h1>
                    <span class="text-gold font-light text-base">|</span>
                    <span class="text-sm font-bold tracking-wider text-sage-dark font-serif">{{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}</span>
                </div>
                <span class="text-[11px] font-semibold tracking-[0.22em] text-gold-dark uppercase mt-0.5">
                    {{ $salonIdentity['tagline_en'] ?? 'BEAUTY LOUNGE' }}
                </span>
            </div>
        </div>

        <!-- Left Side: Quick Action Circular Icons + Sidebar Menu Button -->
        <div class="flex items-center gap-2">
            <!-- Quick Working Hours / Status Icon -->
            <button onclick="openToast('الصالون مفتوح الآن • نستقبلكن {{ $workingHours['hours_ar'] ?? 'من 10:00 ص حتى 10:00 م' }}')" 
                    title="أوقات العمل"
                    class="w-10 h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-gold-light hover:text-gold-dark transition active:scale-95 relative">
                <span class="w-2 h-2 rounded-full bg-emerald-500 absolute top-2 right-2 ring-2 ring-white"></span>
                <i class="fa-regular fa-bell text-sm"></i>
            </button>

            <!-- Quick Location / Contact Icon -->
            <button onclick="openSidebarSection('contact')" 
                    title="موقع الصالون والتواصل"
                    class="w-10 h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-gold-light hover:text-gold-dark transition active:scale-95">
                <i class="fa-solid fa-location-dot text-sm text-gold-dark"></i>
            </button>

            <!-- Far-Left Sidebar Menu Button (Three Dots) -->
            <button id="menuBtn" 
                    onclick="toggleSidebar(true)" 
                    aria-label="القائمة الجانبية"
                    class="w-10 h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-sage-dark hover:text-white transition active:scale-95 border border-gold/20">
                <i class="fa-solid fa-ellipsis-vertical text-base"></i>
            </button>
        </div>
    </div>
</header>
