<div class="flex flex-col flex-1 animate-view">

    {{-- ===== TOP APP HEADER ===== --}}
    <header class="sticky top-0 z-40 px-5 py-4 flex items-center justify-between border-b border-[#D5E3DD]"
            style="background-color: #E2EFEA;">

        {{-- Brand Logo --}}
        <div class="flex items-center gap-3 text-right">
            <div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center shadow-sm border border-[#C5A373]/40">
                @include('livewire.partials.hairline-logo', ['size' => 'sm'])
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-en-brand text-lg font-semibold tracking-widest text-[#4A5D54]">HAIRLINE</span>
                    <span class="text-[#C5A373] font-light">|</span>
                    <span class="text-xl font-bold text-[#232B28]">هيرلاين</span>
                </div>
                <p class="font-en-sub text-[9px] text-[#8C7048] font-medium mt-0.5">BEAUTY LOUNGE</p>
            </div>
        </div>

        {{-- Header Action Icons --}}
        <div class="flex items-center gap-2.5">
            {{-- Profile / My Bookings --}}
            <a href="{{ route('profile') }}" wire:navigate
               class="w-11 h-11 rounded-full bg-white/85 hover:bg-white text-[#4A5D54] flex items-center justify-center shadow-sm border border-[#4A5D54]/10 transition-all"
               title="حجوزاتي وملفي">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </a>

            {{-- Booking Bag --}}
            <a href="{{ route('booking') }}" wire:navigate
               class="relative w-11 h-11 rounded-full bg-white/85 hover:bg-white text-[#4A5D54] flex items-center justify-center shadow-sm border border-[#4A5D54]/10 transition-all"
               title="احجزي موعدكِ">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </a>
        </div>
    </header>

    {{-- ===== MAIN NAVIGATION ROWS ===== --}}
    <nav aria-label="القائمة الرئيسية" class="w-full shadow-sm z-10">

        {{-- Row 1: About --}}
        <a href="{{ route('about') }}" wire:navigate class="main-nav-row group block" role="button">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-4">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#C5A373] group-hover:scale-125 transition-transform"></span>
                    <span class="main-nav-title">عن هيرلاين</span>
                </div>
                <div class="flex items-center gap-2 text-[#4A5D54]">
                    <span class="text-sm font-medium text-[#8C7048] opacity-0 group-hover:opacity-100 transition-opacity">اكتشفي</span>
                    <div class="w-10 h-10 rounded-full bg-[#F7F4EF] group-hover:bg-[#E2EFEA] flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>

        {{-- Row 2: Services --}}
        <a href="{{ route('services') }}" wire:navigate class="main-nav-row group block" role="button">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-4">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#C5A373] group-hover:scale-125 transition-transform"></span>
                    <span class="main-nav-title">الخدمات</span>
                </div>
                <div class="flex items-center gap-3 text-[#4A5D54]">
                    <span class="text-xs font-medium bg-[#E2EFEA] text-[#4A5D54] px-3 py-1 rounded-full">4 أقسام رئيسية</span>
                    <div class="w-10 h-10 rounded-full bg-[#F7F4EF] group-hover:bg-[#E2EFEA] flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>

        {{-- Row 3: Booking --}}
        <a href="{{ route('booking') }}" wire:navigate class="main-nav-row group block" role="button">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-4">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#4A5D54] group-hover:scale-125 transition-transform"></span>
                    <span class="main-nav-title">الحجز</span>
                </div>
                <div class="flex items-center gap-3 text-[#4A5D54]">
                    <span class="text-xs font-semibold bg-[#F5EFE6] text-[#8C7048] border border-[#C5A373]/30 px-3 py-1 rounded-full">حجز فوري</span>
                    <div class="w-10 h-10 rounded-full bg-[#F7F4EF] group-hover:bg-[#E2EFEA] flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
    </nav>

    {{-- ===== HERO PORTRAIT SECTION ===== --}}
    <section class="relative flex-1 min-h-[420px] flex flex-col justify-end overflow-hidden" style="background-color: #E2EFEA;">

        {{-- Portrait Image Slides — wire:ignore prevents Livewire from touching this DOM --}}
        <div wire:ignore x-data="{
                currentSlide: {{ $currentHeroSlide }},
                slogans: ['فخامة الجمال', 'أناقتكِ تتجدد'],
                autoPlay: null,
                init() {
                    this.autoPlay = setInterval(() => { this.nextSlide(); }, 6000);
                },
                nextSlide() {
                    this.currentSlide = this.currentSlide === 0 ? 1 : 0;
                },
                switchTo(idx) {
                    this.currentSlide = idx;
                    clearInterval(this.autoPlay);
                    this.autoPlay = setInterval(() => { this.nextSlide(); }, 6000);
                }
             }" class="absolute inset-0">

            {{-- Slide 1 --}}
            <img src="{{ $salonIdentity['hero_images'][0]['url'] }}"
                 onerror="this.src='{{ $salonIdentity['hero_images'][0]['fallback'] }}'"
                 alt="{{ $salonIdentity['hero_images'][0]['alt'] }}"
                 :class="currentSlide === 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-105'"
                 class="hero-slide absolute inset-0 w-full h-full object-cover object-center">

            {{-- Slide 2 --}}
            <img src="{{ $salonIdentity['hero_images'][1]['url'] }}"
                 onerror="this.src='{{ $salonIdentity['hero_images'][1]['fallback'] }}'"
                 alt="{{ $salonIdentity['hero_images'][1]['alt'] }}"
                 :class="currentSlide === 1 ? 'opacity-100 scale-100' : 'opacity-0 scale-105'"
                 class="hero-slide absolute inset-0 w-full h-full object-cover object-center">

            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-[#232B28]/85 via-[#232B28]/25 to-transparent"></div>

            {{-- Hero Content --}}
            <div class="relative z-10 p-7 flex items-end justify-between">
                <div>
                    <span class="inline-block font-en-sub text-[10px] text-[#E2EFEA] uppercase bg-white/15 backdrop-blur-md px-3 py-1 rounded-full mb-2.5 border border-white/20">
                        HAIRLINE BEAUTY LOUNGE
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight" x-text="slogans[currentSlide]">
                        {{ $salonIdentity['hero_slogans'][0] }}
                    </h1>
                </div>

                {{-- Slide Dots --}}
                <div class="flex items-center gap-2 bg-black/25 backdrop-blur-md px-3.5 py-2 rounded-full border border-white/15">
                    <button @click="switchTo(0)"
                            :class="currentSlide === 0 ? 'w-6 bg-[#C5A373]' : 'w-2 bg-white/60'"
                            class="h-2 rounded-full transition-all duration-300"
                            aria-label="صورة 1"></button>
                    <button @click="switchTo(1)"
                            :class="currentSlide === 1 ? 'w-6 bg-[#C5A373]' : 'w-2 bg-white/60'"
                            class="h-2 rounded-full transition-all duration-300"
                            aria-label="صورة 2"></button>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== BOTTOM NAVIGATION BAR ===== --}}
    @include('livewire.partials.bottom-nav', ['active' => 'home'])

</div>
