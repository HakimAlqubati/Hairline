{{--
    @param string $active  — 'home' | 'services' | 'booking' | 'profile'
--}}
<nav class="bottom-nav-bar">

    {{-- Home --}}
    <a href="{{ route('home') }}" wire:navigate
       class="bottom-nav-item {{ $active === 'home' ? 'is-active' : '' }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V20.25a.75.75 0 01-.75.75H3.75a.75.75 0 01-.75-.75V9.75z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20.25V13.5h6v6.75"/>
        </svg>
        <span>الرئيسية</span>
    </a>

    {{-- Services --}}
    <a href="{{ route('services') }}" wire:navigate
       class="bottom-nav-item {{ $active === 'services' ? 'is-active' : '' }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M12 12h.01M12 16h.01"/>
        </svg>
        <span>الخدمات</span>
    </a>

    {{-- Booking (Center Action Button) --}}
    <a href="{{ route('booking') }}" wire:navigate
       class="bottom-nav-item {{ $active === 'booking' ? 'is-active' : '' }} relative">
        <div class="absolute -top-4 w-14 h-14 rounded-full bg-[#4A5D54] flex items-center justify-center shadow-lg border-4 border-[#F7F4EF] {{ $active === 'booking' ? 'bg-[#36453E]' : '' }}">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <span class="mt-5 text-[#4A5D54] font-bold">احجزي</span>
    </a>

    {{-- About --}}
    <a href="{{ route('about') }}" wire:navigate
       class="bottom-nav-item {{ $active === 'about' ? 'is-active' : '' }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>عن هيرلاين</span>
    </a>

    {{-- Profile --}}
    <a href="{{ route('profile') }}" wire:navigate
       class="bottom-nav-item {{ $active === 'profile' ? 'is-active' : '' }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <span>حسابي</span>
    </a>

</nav>
