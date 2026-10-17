<div class="flex flex-col flex-1 animate-view">

    {{-- ===== HEADER ===== --}}
    @include('livewire.partials.page-header', ['title' => 'عن هيرلاين', 'backRoute' => 'home'])

    {{-- ===== SCROLLABLE CONTENT ===== --}}
    <div class="flex-1 overflow-y-auto pb-4">
        <div class="p-5 space-y-4">

            {{-- Brand Identity Card --}}
            <div class="bg-white rounded-3xl p-7 text-center shadow-sm border border-[#EAEAEA]">
                <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-[#FAF8F5] flex items-center justify-center border border-[#C5A373]/40 shadow-inner">
                    @include('livewire.partials.hairline-logo', ['size' => 'lg'])
                </div>
                <h2 class="font-en-brand text-2xl font-semibold text-[#4A5D54]">HAIRLINE</h2>
                <p class="font-en-sub text-[10px] text-[#C5A373] font-semibold mb-4">BEAUTY LOUNGE</p>
                <p class="text-lg font-medium text-[#232B28] leading-relaxed">
                    {{ $identity['description_line1'] }}<br>
                    {{ $identity['description_line2'] }}
                </p>
            </div>

            {{-- Working Hours --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EAEAEA] flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#E2EFEA] text-[#4A5D54] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-[#232B28]">أوقات العمل الرسمية</h3>
                        <p class="text-base text-[#5C6B64] mt-0.5">{{ $workingHours['days'] }}: {{ $workingHours['hours_ar'] }}</p>
                    </div>
                </div>
                <span class="{{ $workingHours['is_open_now'] ? 'bg-[#E2EFEA] text-[#4A5D54]' : 'bg-red-50 text-red-600' }} text-xs font-bold px-3 py-1.5 rounded-full">
                    {{ $workingHours['is_open_now'] ? 'مفتوح' : 'مغلق' }}
                </span>
            </div>

            {{-- Location Card --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EAEAEA] flex items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#F5EFE6] text-[#A88453] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-[#232B28]">موقع الصالون</h3>
                        <p class="text-base text-[#5C6B64] mt-0.5">{{ $location['full_address_ar'] }}</p>
                    </div>
                </div>
                <button wire:click="openLocationSheet"
                        class="shrink-0 bg-[#FAF8F5] hover:bg-[#E2EFEA] text-[#4A5D54] border border-[#C5A373]/40 font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors">
                    الوصول السريع
                </button>
            </div>

            {{-- Competitive Advantages Swiper --}}
            <div>
                <h3 class="text-lg font-bold text-[#232B28] mb-3 px-1">لماذا هيرلاين؟</h3>
                <div wire:ignore x-data="{ swiper: null }"
                     x-init="swiper = new Swiper($el.querySelector('.swiper'), {
                         slidesPerView: 1.2,
                         spaceBetween: 12,
                         centeredSlides: false,
                         freeMode: true,
                         breakpoints: { 400: { slidesPerView: 1.6 }, 520: { slidesPerView: 2 } }
                     })">
                    <div class="swiper overflow-visible">
                        <div class="swiper-wrapper">
                            @foreach($advantages as $adv)
                                <div class="swiper-slide">
                                    <div class="bg-white rounded-2xl p-4 border border-[#EAEAEA] shadow-sm h-full">
                                        <div class="w-10 h-10 rounded-xl bg-[#E2EFEA] text-[#4A5D54] flex items-center justify-center mb-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <h4 class="font-bold text-base text-[#232B28] mb-1">{{ $adv['title'] }}</h4>
                                        <p class="text-sm text-[#5C6B64] leading-relaxed">{{ $adv['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Links --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EAEAEA]">
                <h3 class="text-lg font-bold text-[#232B28] mb-3">تواصلي معنا</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ $contactLinks['whatsapp'] }}" target="_blank"
                       class="flex items-center gap-2.5 bg-[#E8F5E9] text-green-700 px-4 py-3 rounded-xl font-semibold text-sm transition-colors hover:bg-green-100">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        واتساب
                    </a>
                    <a href="{{ $contactLinks['instagram'] }}" target="_blank"
                       class="flex items-center gap-2.5 bg-[#FCE4EC] text-pink-700 px-4 py-3 rounded-xl font-semibold text-sm transition-colors hover:bg-pink-100">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        انستقرام
                    </a>
                </div>
            </div>

            {{-- Navigation Buttons --}}
            <div class="grid grid-cols-2 gap-3.5 pt-2">
                <a href="{{ route('services') }}" wire:navigate
                   class="py-4 px-5 rounded-2xl bg-white border-2 border-[#4A5D54] text-[#4A5D54] font-bold text-lg hover:bg-[#E2EFEA] transition-all flex items-center justify-center">
                    تصفح الخدمات
                </a>
                <a href="{{ route('booking') }}" wire:navigate
                   class="py-4 px-5 rounded-2xl bg-[#4A5D54] text-white font-bold text-lg hover:bg-[#36453E] shadow-md transition-all flex items-center justify-center">
                    احجزي موعدكِ
                </a>
            </div>

        </div>
    </div>

    {{-- ===== LOCATION BOTTOM SHEET ===== --}}
    @if($showLocationSheet)
        <div class="fixed inset-0 z-[80]" x-data="{ open: true }">
            <div class="bottom-sheet-backdrop" @click="$wire.closeLocationSheet()"></div>
            <div class="bottom-sheet-panel"
                 x-transition:enter="transition-transform duration-380"
                 x-transition:enter-start="translate-y-full"
                 x-transition:enter-end="translate-y-0">
                <div class="bottom-sheet-handle"></div>
                <div class="p-6 pb-8">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-[#232B28]">موقع صالون هيرلاين</h3>
                        <button wire:click="closeLocationSheet"
                                class="w-9 h-9 rounded-full bg-[#FAF8F5] text-[#232B28] font-bold flex items-center justify-center text-lg">✕</button>
                    </div>

                    <div class="rounded-2xl bg-[#E2EFEA] p-5 border border-[#4A5D54]/15 text-center mb-4">
                        <p class="text-lg font-bold text-[#4A5D54] mb-1">HAIRLINE BEAUTY LOUNGE</p>
                        <p class="text-base text-[#232B28]">{{ $location['full_address_ar'] }}</p>
                        <p class="text-sm text-[#5C6B64] mt-1">{{ $location['parking_note'] }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ $location['google_maps_url'] }}" target="_blank" rel="noopener noreferrer"
                           class="py-3.5 px-4 rounded-2xl bg-[#4A5D54] text-white font-bold text-base text-center hover:bg-[#36453E] transition-colors">
                            فتح في خرائط جوجل
                        </a>
                        <button wire:click="closeLocationSheet"
                                class="py-3.5 px-4 rounded-2xl bg-[#FAF8F5] text-[#232B28] font-bold text-base border border-[#EAEAEA]">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ===== BOTTOM NAV ===== --}}
    @include('livewire.partials.bottom-nav', ['active' => 'about'])

</div>
