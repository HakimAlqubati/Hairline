@props([
    'salonIdentity' => [],
    'stats' => [],
    'aboutUs' => [],
    'workingHours' => [],
    'location' => [],
    'contactLinks' => [],
    'isActive' => false,
])

@php
    $mapUrl = (!empty($location['google_maps_url']) && !str_contains($location['google_maps_url'], '24.7677'))
        ? $location['google_maps_url']
        : 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps';

    $mapIframe = $location['google_maps_iframe'] ?? null;
    if ($mapIframe && preg_match('/src=["\']([^"\']+)["\']/', $mapIframe, $matches)) {
        $mapIframe = $matches[1];
    }
    if (!$mapIframe) {
        $mapIframe = 'https://maps.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&hl=ar&z=16&output=embed';
    }

    $whatsappUrl = $contactLinks['whatsapp'] ?? 'https://wa.me/966548857705';
    $phoneDisplay = $contactLinks['whatsapp_display'] ?? ($contactLinks['phone'] ?? '054 885 7705');
    $phoneRaw = $contactLinks['phone'] ?? preg_replace('/[^0-9+]/', '', $phoneDisplay);
    $instagramUrl = $contactLinks['instagram'] ?? 'https://www.instagram.com/Hairline.5';
    $instagramHandle = $contactLinks['instagram_handle'] ?? '@Hairline.5';
    $snapchatUrl = $contactLinks['snapchat'] ?? 'https://www.snapchat.com/add/Hairline.5';
    $snapchatHandle = $contactLinks['snapchat_handle'] ?? '@Hairline.5';
    $tiktokUrl = $contactLinks['tiktok'] ?? 'https://www.tiktok.com/@Hairline.TikTok';
    $tiktokHandle = $contactLinks['tiktok_handle'] ?? '@Hairline.TikTok';
@endphp

<section id="aboutTabView" class="{{ $isActive ? '' : 'hidden' }} flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 md:pt-8 pb-10 space-y-5 animate-step">
    {{-- About Us Main Card --}}
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-card border border-gold-soft/40 space-y-5 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <span class="px-3 py-1 rounded-full bg-gold-light text-gold-dark text-xs font-bold">التعريف بالصالون ({{ $aboutUs['title'] ?? 'من نحن' }})</span>
        </div>

        <h2 class="text-base sm:text-lg md:text-xl font-bold text-sage-dark">{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} | {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }} {{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}</h2>

        {{-- Intro paragraph --}}
        <p class="text-sm text-gray-600 leading-relaxed">
            {{ $aboutUs['intro_paragraph'] ?? '' }}
        </p>

        {{-- Description paragraph --}}
        <p class="text-sm text-gray-600 leading-relaxed">
            {{ $aboutUs['description_paragraph'] ?? '' }}
        </p>

        {{-- Belief paragraph --}}
        <p class="text-sm text-gray-600 leading-relaxed">
            {{ $aboutUs['belief_paragraph'] ?? '' }}
        </p>

    </div>

    {{-- Founder's Message Card --}}
    @if(!empty($aboutUs['founder_message_paragraphs']))
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-card border border-gold-soft/40 space-y-4 max-w-4xl mx-auto">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-sage-soft text-sage-dark text-xs font-bold">{{ $aboutUs['founder_message_title'] ?? 'كلمة المؤسسة' }}</span>
            </div>

            @foreach($aboutUs['founder_message_paragraphs'] as $paragraph)
                <p class="text-sm text-gray-600 leading-relaxed">{{ $paragraph }}</p>
            @endforeach

            {{-- Founder Signature --}}
            @if(!empty($aboutUs['founder_signature']))
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-sm font-bold text-sage-dark">{{ $aboutUs['founder_signature']['name_ar'] }}</p>
                    <p class="text-xs text-gold-dark font-medium">{{ $aboutUs['founder_signature']['title_ar'] }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Permanent Contact Information, Links, Phone & Map Card --}}
    <div id="aboutContactSection" class="bg-white rounded-3xl p-6 md:p-8 shadow-card border border-gold-soft/40 space-y-6 max-w-4xl mx-auto">
        <h3 class="text-lg sm:text-xl font-bold text-sage-dark">للتواصل والاستفسار</h3>

        {{-- Working Hours & Direct Phone / WhatsApp --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-1">
            {{-- Working Hours --}}
            <div class="flex items-start gap-3.5">
                <span class="w-11 h-11 rounded-2xl bg-primary-light/70 text-primary-dark flex items-center justify-center shrink-0 shadow-2xs">
                    <i class="fa-regular fa-clock text-base"></i>
                </span>
                <div>
                    <h4 class="font-extrabold text-sm sm:text-base text-sage-dark">أوقات العمل الرسمية</h4>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1 leading-relaxed">
                        {{ $workingHours['hours_ar'] ?? 'يومياً من الساعة 2:00 ظهراً حتى 10:00 مساءً' }}
                    </p>
                </div>
            </div>

            {{-- Phone & Direct Call / WhatsApp --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h4 class="font-extrabold text-sm sm:text-base text-sage-dark">(للتواصل)</h4>
                    <p class="text-xs sm:text-sm font-bold text-primary-dark dir-ltr text-right mt-0.5" dir="ltr">{{ $phoneDisplay }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ $whatsappUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-[#25D366] hover:bg-[#1EBE5D] text-white font-bold text-xs transition-all duration-200 flex items-center justify-center gap-1.5 shadow-xs shadow-[#25D366]/20">
                        <i class="fa-brands fa-whatsapp text-sm text-white"></i>
                        <span>واتساب</span>
                    </a>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) ($phoneRaw ?: '0548857705')) }}"
                       class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-dark text-white font-bold text-xs transition-all duration-200 flex items-center justify-center gap-1.5 shadow-xs shadow-primary/20">
                        <i class="fa-solid fa-phone text-xs text-white"></i>
                        <span>اتصال</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Subtle Soft Divider --}}
        <div class="h-px w-full bg-gradient-to-l from-transparent via-gray-200/60 to-transparent"></div>

        {{-- Social & Contact Links (Without Title as Requested) --}}
        <div class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="{{ $instagramUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2.5 rounded-2xl hover:bg-gold-light/40 transition-colors flex items-center justify-between gap-3 group">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-2xl bg-primary-light/70 text-primary-dark group-hover:bg-primary group-hover:text-white transition flex items-center justify-center shrink-0">
                            <i class="fa-brands fa-instagram text-base"></i>
                        </span>
                        <div class="min-w-0">
                            <span class="block text-xs sm:text-sm font-extrabold text-sage-dark group-hover:text-primary-dark transition-colors">إنستغرام</span>
                            <span class="block text-[11px] text-gray-500 truncate mt-0.5" dir="ltr">{{ $instagramHandle }}</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-gray-400 group-hover:text-primary transition-colors"></i>
                </a>

                <a href="{{ $snapchatUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2.5 rounded-2xl hover:bg-gold-light/40 transition-colors flex items-center justify-between gap-3 group">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-2xl bg-primary-light/70 text-primary-dark group-hover:bg-primary group-hover:text-white transition flex items-center justify-center shrink-0">
                            <i class="fa-brands fa-snapchat text-base"></i>
                        </span>
                        <div class="min-w-0">
                            <span class="block text-xs sm:text-sm font-extrabold text-sage-dark group-hover:text-primary-dark transition-colors">سناب شات</span>
                            <span class="block text-[11px] text-gray-500 truncate mt-0.5" dir="ltr">{{ $snapchatHandle }}</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-gray-400 group-hover:text-primary transition-colors"></i>
                </a>

                <a href="{{ $tiktokUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="p-2.5 rounded-2xl hover:bg-gold-light/40 transition-colors flex items-center justify-between gap-3 group">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-2xl bg-primary-light/70 text-primary-dark group-hover:bg-primary group-hover:text-white transition flex items-center justify-center shrink-0">
                            <i class="fa-brands fa-tiktok text-sm"></i>
                        </span>
                        <div class="min-w-0">
                            <span class="block text-xs sm:text-sm font-extrabold text-sage-dark group-hover:text-primary-dark transition-colors">تيك توك</span>
                            <span class="block text-[11px] text-gray-500 truncate mt-0.5" dir="ltr">{{ $tiktokHandle }}</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-gray-400 group-hover:text-primary transition-colors"></i>
                </a>
            </div>
        </div>

        {{-- Subtle Soft Divider --}}
        <div class="h-px w-full bg-gradient-to-l from-transparent via-gray-200/60 to-transparent"></div>

        {{-- Location & Embedded Google Map --}}
        <div class="space-y-3.5 pt-1">
            <div class="flex items-start gap-3.5">
                <span class="w-11 h-11 rounded-2xl bg-primary-light/70 text-primary-dark flex items-center justify-center shrink-0 shadow-2xs">
                    <i class="fa-solid fa-location-dot text-base"></i>
                </span>
                <div>
                    <h4 class="font-extrabold text-sm sm:text-base text-sage-dark">العنوان</h4>
                    <p class="text-xs sm:text-sm text-gray-600 font-medium leading-relaxed mt-0.5">
                        {{ $location['full_address_ar'] ?? 'الرياض - حي المصيف، مخرج 5' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ $mapUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   title="انقري لفتح الموقع في تطبيق الخرائط"
                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 hover:bg-primary text-primary hover:text-white text-xs font-bold transition-all duration-200 cursor-pointer">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    <span>فتح في خرائط جوجل</span>
                </a>
            </div>

            <div class="relative w-full h-64 sm:h-80 rounded-2xl overflow-hidden border border-gold-soft/40 shadow-xs bg-neutral-100 group">
                <iframe
                    src="{{ $mapIframe }}"
                    class="w-full h-full border-0 select-auto"
                    loading="lazy"
                    allowfullscreen=""
                    referrerpolicy="no-referrer-when-downgrade"
                    title="موقع صالون هيرلاين">
                </iframe>
            </div>
        </div>
    </div>
</section>
