<div class="flex flex-col flex-1 animate-view"
     x-data="{
        activePhoto: null,
        photoTitle: '',
        photoSubtitle: '',
        openLightbox(url, title, subtitle) {
            this.activePhoto = url;
            this.photoTitle = title;
            this.photoSubtitle = subtitle;
        },
        closeLightbox() {
            this.activePhoto = null;
        }
     }">

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
                    <span class="text-xs font-semibold bg-[#E2EFEA] text-[#4A5D54] px-3 py-1 rounded-full border border-[#4A5D54]/15">
                        {{ count($categories) }} أقسام معتمدة
                    </span>
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

    {{-- ===== HERO PORTRAIT SECTION (تصوير صالون هيرلاين الحقيقي) ===== --}}
    <section class="relative min-h-[400px] sm:min-h-[460px] flex flex-col justify-end overflow-hidden" style="background-color: #E2EFEA;">

        {{-- Real Salon Image Slides --}}
        <div wire:ignore x-data="{
                currentSlide: 0,
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

            {{-- Slide 1: Reception Desk --}}
            <img src="{{ asset('images/salon/reception-desk.jpg') }}"
                 alt="منطقة الاستقبال الفاخرة بصالون هيرلاين"
                 :class="currentSlide === 0 ? 'opacity-100 scale-100' : 'opacity-0 scale-105'"
                 class="hero-slide absolute inset-0 w-full h-full object-cover object-center transition-all duration-700">

            {{-- Slide 2: Hair Styling Chairs & Floral Wall --}}
            <img src="{{ asset('images/salon/hair-styling-chairs.jpg') }}"
                 alt="محطات تصفيف الشعر وجدار الورود في صالون هيرلاين"
                 :class="currentSlide === 1 ? 'opacity-100 scale-100' : 'opacity-0 scale-105'"
                 class="hero-slide absolute inset-0 w-full h-full object-cover object-center transition-all duration-700">

            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-[#232B28]/95 via-[#232B28]/40 to-transparent"></div>

            {{-- Hero Content --}}
            <div class="relative z-10 p-6 sm:p-7 flex items-end justify-between">
                <div>
                    <span class="inline-block font-en-sub text-[10px] text-[#E2EFEA] uppercase bg-white/15 backdrop-blur-md px-3 py-1 rounded-full mb-2.5 border border-white/20">
                        HAIRLINE BEAUTY LOUNGE
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight" x-text="slogans[currentSlide]">
                        {{ $salonIdentity['hero_slogans'][0] }}
                    </h1>
                    <p class="text-xs text-white/85 mt-1 font-medium">صالون نسائي متكامل فاخر • حي الملقا، الرياض</p>
                </div>

                {{-- Slide Dots --}}
                <div class="flex items-center gap-2 bg-black/35 backdrop-blur-md px-3.5 py-2 rounded-full border border-white/20">
                    <button @click="switchTo(0)"
                            :class="currentSlide === 0 ? 'w-6 bg-[#C5A373]' : 'w-2 bg-white/60'"
                            class="h-2 rounded-full transition-all duration-300"
                            aria-label="صورة الاستقبال"></button>
                    <button @click="switchTo(1)"
                            :class="currentSlide === 1 ? 'w-6 bg-[#C5A373]' : 'w-2 bg-white/60'"
                            class="h-2 rounded-full transition-all duration-300"
                            aria-label="صورة التصفيف"></button>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== SALON AMBIENCE & PHOTO GALLERY (معرض صور الصالون بتنسيق متناسق) ===== --}}
    <section class="bg-[#FAF8F5] py-7 px-5 border-t border-[#EAEAEA] space-y-5">

        {{-- Section Header --}}
        <div class="flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-[#8C7048] uppercase tracking-wider block font-en-brand">SALON TOUR</span>
                <h2 class="text-xl font-bold text-[#232B28]">أجواء وفخامة صالون هيرلاين</h2>
            </div>
            <span class="text-xs font-semibold bg-[#E2EFEA] text-[#4A5D54] px-3 py-1 rounded-full border border-[#4A5D54]/15">
                تصوير حي من الصالون
            </span>
        </div>

        {{-- ══ BENTO GALLERY: Balanced Sizes & Proportions ══ --}}
        <div class="space-y-3.5">

            {{-- 1. Main Wide Hero Photo: Reception Desk --}}
            <div @click="openLightbox('{{ asset('images/salon/reception-desk.jpg') }}', 'الاستقبال الملكي', 'شعار هيرلاين المذهب ومنطقة الضيافة وخدمة العملاء')"
                 class="group relative w-full aspect-[16/9] sm:aspect-[21/9] rounded-3xl overflow-hidden cursor-pointer shadow-sm border border-[#EAEAEA] bg-white transition-all hover:shadow-md">
                <img src="{{ asset('images/salon/reception-desk.jpg') }}"
                     alt="منطقة الاستقبال والترحيب الفاخرة في صالون هيرلاين"
                     class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
                <div class="absolute bottom-4 right-4 left-4 flex items-end justify-between text-white">
                    <div>
                        <span class="inline-block text-[10px] font-bold uppercase bg-[#C5A373] text-white px-2.5 py-0.5 rounded-full mb-1">
                            منطقة الاستقبال
                        </span>
                        <h3 class="text-base sm:text-lg font-bold">الاستقبال الملكي والترحيب</h3>
                        <p class="text-xs text-white/80 line-clamp-1">كاونتر مجهز بأحدث أنظمة الدفع وشعار هيرلاين الذهبي الفاخر</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 text-white group-hover:bg-[#C5A373] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 2. Balanced 2-Column Row (Vertical Portrait + Two Stacked Landscapes) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">

                {{-- Left/First Column: Tall Portrait of Hair Styling Chairs & Floral Wall --}}
                <div @click="openLightbox('{{ asset('images/salon/hair-styling-chairs.jpg') }}', 'محطات التصفيف الفاخرة', 'مرايا بإضاءة نهارية سينمائية وجدار الورود الطبيعية')"
                     class="group relative w-full aspect-[4/5] rounded-3xl overflow-hidden cursor-pointer shadow-sm border border-[#EAEAEA] bg-white transition-all hover:shadow-md">
                    <img src="{{ asset('images/salon/hair-styling-chairs.jpg') }}"
                         alt="كراسي ومحطات التصفيف وجدار الورود"
                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-4 right-4 left-4 text-white">
                        <span class="inline-block text-[10px] font-bold uppercase bg-[#4A5D54] text-white px-2.5 py-0.5 rounded-full mb-1">
                            تصفيف الشعر
                        </span>
                        <h3 class="text-base font-bold">محطات التصفيف وجدار الورود</h3>
                        <p class="text-xs text-white/80 line-clamp-1">كراسي إيطالية مريحة وإضاءة سينمائية احترافية</p>
                    </div>
                </div>

                {{-- Right/Second Column: Two Harmonized Landscape Cards --}}
                <div class="grid grid-cols-1 gap-3.5">

                    {{-- Stack 1: Styling stations & screen --}}
                    <div @click="openLightbox('{{ asset('images/salon/salon-stations-tv.jpg') }}', 'أجواء اللوج الفندقية', 'شاشات عرض الموديلات وأحدث صيحات التجميل والعناية')"
                         class="group relative w-full aspect-[16/10] rounded-2xl overflow-hidden cursor-pointer shadow-sm border border-[#EAEAEA] bg-white transition-all hover:shadow-md">
                        <img src="{{ asset('images/salon/salon-stations-tv.jpg') }}"
                             alt="محطات الصالون وشاشات العرض المودرن"
                             class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 right-3 left-3 text-white">
                            <span class="inline-block text-[10px] font-bold uppercase bg-white/25 backdrop-blur-md text-white px-2 py-0.5 rounded-full mb-1">
                                الصالون الداخلي
                            </span>
                            <h4 class="text-sm font-bold">أجواء اللوج الفندقية</h4>
                        </div>
                    </div>

                    {{-- Stack 2: Screen display & private seating --}}
                    <div @click="openLightbox('{{ asset('images/salon/salon-screen-display.jpg') }}', 'ركن الجمال والخصوصية', 'فواصل خصوصية تامة وخدمة متفردة لكل عميلة')"
                         class="group relative w-full aspect-[16/10] rounded-2xl overflow-hidden cursor-pointer shadow-sm border border-[#EAEAEA] bg-white transition-all hover:shadow-md">
                        <img src="{{ asset('images/salon/salon-screen-display.jpg') }}"
                             alt="شاشات العرض وركن الخصوصية"
                             class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 right-3 left-3 text-white">
                            <span class="inline-block text-[10px] font-bold uppercase bg-white/25 backdrop-blur-md text-white px-2 py-0.5 rounded-full mb-1">
                                خصوصية تامة
                            </span>
                            <h4 class="text-sm font-bold">ركن الجمال والخصوصية</h4>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 3. Bottom Feature: Salon Business Cards & Contact Privilege Card --}}
            <div class="bg-white rounded-3xl p-4 sm:p-5 border border-[#C5A373]/30 shadow-xs flex flex-col sm:flex-row items-center gap-4">
                {{-- Square/Proportional Image of Business Cards --}}
                <div @click="openLightbox('{{ asset('images/salon/business-cards.jpg') }}', 'بطاقات وهوية هيرلاين', 'خدمة الحجز الفوري والاستفسار المباشر 0548857705')"
                     class="w-full sm:w-44 aspect-[4/3] sm:aspect-square rounded-2xl overflow-hidden cursor-pointer relative group shrink-0 border border-[#EAEAEA]">
                    <img src="{{ asset('images/salon/business-cards.jpg') }}"
                         alt="بطاقات صالون هيرلاين الفاخرة"
                         class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors"></div>
                    <span class="absolute bottom-2 right-2 text-[9px] font-bold bg-white/90 text-[#4A5D54] px-2 py-0.5 rounded-md shadow-2xs">
                        تكبير الصورة 🔍
                    </span>
                </div>
                {{-- Detail Content --}}
                <div class="flex-1 space-y-2 text-right w-full">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#C5A373]"></span>
                        <h4 class="font-bold text-base text-[#232B28]">صالون هيرلاين النسائي • Hairline Beauty Lounge</h4>
                    </div>
                    <p class="text-xs text-[#5C6B64] leading-relaxed">
                        نستقبلكِ يومياً في أجواء راقية ومصممة خصيصاً لراحتكِ وجمالكِ. جميع خدمات الشعر، الأظافر، البشرة، والمساج تقدم بأرقى المعايير.
                    </p>
                    <div class="flex flex-wrap items-center gap-3 pt-1">
                        <a href="tel:0548857705"
                           class="inline-flex items-center gap-1.5 text-xs font-bold bg-[#E2EFEA] text-[#4A5D54] px-3.5 py-2 rounded-xl border border-[#4A5D54]/20 hover:bg-[#4A5D54] hover:text-white transition-colors">
                            <span>📞</span> 0548857705
                        </a>
                        <a href="{{ route('booking') }}" wire:navigate
                           class="inline-flex items-center gap-1.5 text-xs font-bold bg-[#4A5D54] text-white px-4 py-2 rounded-xl hover:bg-[#36453E] transition-colors shadow-2xs">
                            احجزي موعدكِ الآن
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ===== SERVICES LANDING SHOWCASE (مطابقة للملف المعتمد) ===== --}}
    <section class="bg-white py-6 px-5 border-t border-[#EAEAEA] space-y-6">

        {{-- Section Header --}}
        <div class="flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-[#8C7048] uppercase tracking-wider block font-en-brand">OUR MENU</span>
                <h2 class="text-xl font-bold text-[#232B28]">أقسام الخدمات المعتمدة</h2>
            </div>
            <a href="{{ route('services') }}" wire:navigate
               class="text-xs font-bold text-[#4A5D54] hover:text-[#C5A373] flex items-center gap-1 transition-colors">
                <span>عرض الكل</span>
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
        </div>

        {{-- Horizontal Categories Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($categories as $cat)
                <a href="{{ route('services', ['cat' => $cat['id']]) }}" wire:navigate
                   class="group p-4 rounded-2xl bg-[#FAF8F5] hover:bg-[#E2EFEA]/60 border border-[#EAEAEA] hover:border-[#4A5D54]/30 transition-all flex items-center justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-white shadow-xs border border-[#EAEAEA] flex items-center justify-center text-[#4A5D54] group-hover:scale-105 group-hover:bg-[#4A5D54] group-hover:text-white transition-all">
                            @if($cat['icon'] === 'scissors')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/>
                                    <line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/>
                                </svg>
                            @elseif($cat['icon'] === 'sparkles')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            @elseif($cat['icon'] === 'leaf')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 01-9-9c0-4.97 4.03-9 9-9 4.14 0 7.64 2.81 8.68 6.64.1.37-.17.72-.55.72H16c-2.21 0-4 1.79-4 4v4.13c0 .38-.35.65-.72.55A8.97 8.97 0 0112 21z"/>
                                </svg>
                            @elseif($cat['icon'] === 'wind')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.59 4.59A2 2 0 1111 8H2m10.59 11.41A2 2 0 1014 16H2m15.73-8.27A2.5 2.5 0 1119.5 12H2"/>
                                </svg>
                            @elseif($cat['icon'] === 'hand')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/>
                                </svg>
                            @elseif($cat['icon'] === 'face')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-[#232B28] group-hover:text-[#4A5D54] transition-colors">{{ $cat['title'] }}</h3>
                            <p class="text-xs text-[#5C6B64] line-clamp-1">{{ $cat['subtitle'] }}</p>
                        </div>
                    </div>
                    <div class="text-left shrink-0">
                        <span class="text-[11px] font-semibold bg-white text-[#8C7048] px-2.5 py-1 rounded-full border border-[#EAEAEA] shadow-2xs block">
                            {{ $cat['badge_count'] }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- ===== HAIR LENGTHS GUIDE TEASER (Page 8 in Menu) ===== --}}
        <div x-data="{ openGuide: false }" class="rounded-2xl bg-[#F5EFE6]/70 border border-[#C5A373]/30 p-4">
            <div class="flex items-center justify-between cursor-pointer" @click="openGuide = !openGuide">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-[#C5A373]/20 text-[#8C7048] flex items-center justify-center font-bold text-sm">
                        📏
                    </span>
                    <div>
                        <h4 class="font-bold text-sm text-[#232B28]">دليل أطوال الشعر المعتمد بالصالون</h4>
                        <p class="text-xs text-[#5C6B64]">قصير جداً، قصير، وسط، طويل، طويل جداً</p>
                    </div>
                </div>
                <button type="button" class="text-xs font-bold text-[#8C7048] hover:underline flex items-center gap-1">
                    <span x-text="openGuide ? 'إخفاء' : 'عرض الدليل'"></span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="openGuide ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            <div x-show="openGuide" x-collapse class="mt-3 pt-3 border-t border-[#C5A373]/20 space-y-2">
                @foreach($lengthsGuide as $tier)
                    <div class="flex items-center justify-between text-xs bg-white/80 p-2.5 rounded-xl border border-[#EAEAEA]">
                        <div class="flex items-center gap-2">
                            <span class="font-bold px-2 py-0.5 rounded bg-[#4A5D54] text-white text-[10px]">{{ $tier['code'] }}</span>
                            <span class="font-bold text-[#232B28]">{{ $tier['name_ar'] }}</span>
                        </div>
                        <span class="text-[#5C6B64]">{{ $tier['description'] }}</span>
                    </div>
                @endforeach
                <p class="text-[10px] text-[#8C7048] text-center pt-1 font-medium">تُحدد أسعار الاستشوار، الرينساج، المعالجات، والتساريح وفق طول الشعر أعلاه</p>
            </div>
        </div>

        {{-- ===== POPULAR SERVICES DIRECT BOOKING (الأكثر طلباً) ===== --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-base text-[#232B28] flex items-center gap-2">
                    <span class="text-amber-500">⭐</span>
                    <span>الخدمات الأكثر طلباً</span>
                </h3>
                <span class="text-xs text-[#8C7048] font-semibold">حجز فوري</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($featuredServices as $srv)
                    <div class="p-3.5 rounded-2xl bg-white border border-[#EAEAEA] hover:border-[#4A5D54] shadow-xs flex flex-col justify-between gap-3 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="font-bold text-sm text-[#232B28]">{{ $srv['name'] }}</h4>
                                <span class="font-bold text-sm text-[#4A5D54] shrink-0">
                                    {{ $srv['price_display'] ?? ($srv['price'] . ' SAR') }}
                                </span>
                            </div>
                            <p class="text-xs text-[#5C6B64] mt-1 line-clamp-2">{{ $srv['description'] }}</p>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-[#FAF8F5]">
                            <span class="text-[11px] text-[#8C7048] flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-[#C5A373]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $srv['duration'] }}
                            </span>
                            <a href="{{ route('services', ['cat' => $srv['category_id']]) }}" wire:navigate
                               class="text-xs font-bold text-white bg-[#4A5D54] hover:bg-[#36453E] px-3.5 py-1.5 rounded-xl transition-all shadow-2xs">
                                احجزي الآن
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===== OFFICIAL SALON FOOTER GUARANTEE ===== --}}
        <div class="bg-[#FAF8F5] rounded-2xl p-4 border border-[#EAEAEA] text-center space-y-2">
            <p class="text-xs font-bold text-[#4A5D54]">
                صالون هيرلاين النسائي • HAIRLINE BEAUTY LOUNGE
            </p>
            <p class="text-[11px] text-[#5C6B64]">
                جميع الأسعار معتمدة وشاملة ضريبة القيمة المضافة 15% (VAT)
            </p>
            <div class="flex items-center justify-center gap-4 pt-1 text-xs font-semibold text-[#8C7048]">
                <a href="tel:0548857705" class="hover:underline flex items-center gap-1">
                    <span>📞</span> 0548857705
                </a>
                <span>•</span>
                <span class="font-en-brand">@hairline.5</span>
            </div>
        </div>

    </section>

    {{-- ===== LIGHTBOX MODAL (تكبير الصور بدقة عالية) ===== --}}
    <div x-show="activePhoto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.away="closeLightbox()"
         @keydown.escape.window="closeLightbox()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md"
         style="display: none;">

        <div class="relative max-w-2xl w-full bg-[#232B28] rounded-3xl overflow-hidden shadow-2xl border border-white/10"
             @click.stop>
            {{-- Close Button --}}
            <button @click="closeLightbox()"
                    class="absolute top-4 left-4 z-10 w-10 h-10 rounded-full bg-black/60 hover:bg-black text-white flex items-center justify-center transition-colors">
                ✕
            </button>

            {{-- Full Image Container --}}
            <div class="max-h-[70vh] overflow-hidden flex items-center justify-center bg-black/40">
                <img :src="activePhoto"
                     :alt="photoTitle"
                     class="max-h-[70vh] w-auto max-w-full object-contain">
            </div>

            {{-- Caption --}}
            <div class="p-5 text-right bg-[#232B28] border-t border-white/10 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white" x-text="photoTitle"></h3>
                    <p class="text-xs text-[#E2EFEA]/80 mt-0.5" x-text="photoSubtitle"></p>
                </div>
                <a href="{{ route('booking') }}" wire:navigate
                   class="bg-[#C5A373] hover:bg-[#A88453] text-[#232B28] font-bold text-xs px-4 py-2 rounded-xl transition-colors shrink-0">
                    احجزي موعدكِ
                </a>
            </div>
        </div>
    </div>

    {{-- ===== BOTTOM NAVIGATION BAR ===== --}}
    @include('livewire.partials.bottom-nav', ['active' => 'home'])

</div>
