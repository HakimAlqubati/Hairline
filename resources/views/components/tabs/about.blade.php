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

<section id="aboutTabView" class="{{ $isActive ? '' : 'hidden' }} flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-0 pb-0 flex flex-col animate-step">

    <!-- Unified Master Container Starting Flush From Top Without Top/Bottom Borders -->
    <div class="w-full max-w-4xl mx-auto bg-white border-x border-primary-soft/60 border-t-0 border-b-0 rounded-none shadow-none sm:shadow-xs px-6 sm:px-8 md:px-10 lg:px-12 pt-8 sm:pt-10 pb-16 space-y-10 sm:space-y-12 flex-1">
        
        <!-- ==================== SECTION 1: ABOUT US ==================== -->
        <article class="space-y-5">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-light text-primary-dark text-xs font-bold">
                    <i class="fa-solid fa-circle-info text-[11px]"></i>
                    <span>التعريف بالصالون ({{ $aboutUs['title'] ?? 'من نحن' }})</span>
                </span>
            </div>

            <h3 class="text-xl sm:text-2xl font-black text-sage-dark tracking-tight">
                {{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} | {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }} {{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}
            </h3>

            <div class="space-y-4 text-sm sm:text-[15px] text-gray-600 leading-relaxed font-normal">
                @if(!empty($aboutUs['intro_paragraph']))
                    <p>{{ $aboutUs['intro_paragraph'] }}</p>
                @endif

                @if(!empty($aboutUs['description_paragraph']))
                    <p>{{ $aboutUs['description_paragraph'] }}</p>
                @endif

                @if(!empty($aboutUs['belief_paragraph']))
                    <p>{{ $aboutUs['belief_paragraph'] }}</p>
                @endif
            </div>
        </article>

        <!-- Elegant Golden Gradient Divider -->
        <div class="h-px w-full bg-gradient-to-r from-transparent via-primary-soft to-transparent"></div>

        <!-- ==================== SECTION 2: FOUNDER'S MESSAGE ==================== -->
        @if(!empty($aboutUs['founder_message_paragraphs']))
            <article class="space-y-5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-light text-primary-dark text-xs font-bold">
                        <i class="fa-solid fa-quote-right text-[11px]"></i>
                        <span>{{ $aboutUs['founder_message_title'] ?? 'كلمة المؤسسة' }}</span>
                    </span>
                </div>

                <div class="space-y-3.5 text-sm sm:text-[15px] text-gray-600 leading-relaxed font-normal">
                    @foreach($aboutUs['founder_message_paragraphs'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                <!-- Founder Signature -->
                @if(!empty($aboutUs['founder_signature']))
                    <div class="pt-4 border-t border-gray-100/80 flex items-center justify-between">
                        <div>
                            <p class="text-base font-black text-sage-dark">{{ $aboutUs['founder_signature']['name_ar'] }}</p>
                            <p class="text-xs font-bold text-primary-dark mt-0.5">{{ $aboutUs['founder_signature']['title_ar'] }}</p>
                        </div>
                    </div>
                @endif
            </article>

            <!-- Elegant Golden Gradient Divider -->
            <div class="h-px w-full bg-gradient-to-r from-transparent via-primary-soft to-transparent"></div>
        @endif

        <!-- ==================== SECTION 3: CONTACT & INFORMATION ==================== -->
        <div id="aboutContactSection" class="space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-lg sm:text-xl font-bold text-sage-dark flex items-center gap-2">
                    <i class="fa-solid fa-headset text-primary text-base"></i>
                    <span>للتواصل والاستفسار</span>
                </h3>
            </div>

            <!-- Working Hours & Direct Call / WhatsApp -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-1">
                <!-- Working Hours -->
                <div class="flex items-start gap-3.5">
                    <span class="w-11 h-11 rounded-2xl bg-primary-light/70 text-primary-dark flex items-center justify-center shrink-0 shadow-2xs">
                        <i class="fa-regular fa-clock text-base"></i>
                    </span>
                    <div class="space-y-0.5">
                        <h4 class="font-extrabold text-sm sm:text-base text-sage-dark">أوقات العمل الرسمية</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-medium">
                            {{ $workingHours['hours_ar'] ?? 'يومياً من الساعة 2:00 ظهراً حتى 10:00 مساءً' }}
                        </p>
                    </div>
                </div>

                <!-- Phone & Direct Call / WhatsApp -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <span class="w-11 h-11 rounded-2xl bg-primary-light/70 text-primary-dark flex items-center justify-center shrink-0 shadow-2xs">
                            <i class="fa-solid fa-phone text-base"></i>
                        </span>
                        <div>
                            <h4 class="font-extrabold text-sm sm:text-base text-sage-dark">الهاتف والحجوزات</h4>
                            <p class="text-xs sm:text-sm font-bold text-primary-dark dir-ltr text-right mt-0.5" dir="ltr">{{ $phoneDisplay }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ $whatsappUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="group flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-transparent border border-primary text-primary-dark hover:text-white hover:bg-primary font-bold text-xs transition-all duration-200 flex items-center justify-center gap-1.5 shadow-2xs hover:shadow-xs hover:shadow-primary/20">
                            <i class="fa-brands fa-whatsapp text-sm text-primary group-hover:text-white transition-colors"></i>
                            <span>واتساب</span>
                        </a>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) ($phoneRaw ?: '0548857705')) }}"
                           class="group flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-transparent border border-primary text-primary-dark hover:text-white hover:bg-primary font-bold text-xs transition-all duration-200 flex items-center justify-center gap-1.5 shadow-2xs hover:shadow-xs hover:shadow-primary/20">
                            <i class="fa-solid fa-phone text-xs text-primary group-hover:text-white transition-colors"></i>
                            <span>اتصال</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Subtle Soft Divider -->
            <div class="h-px w-full bg-gradient-to-l from-transparent via-gray-200/60 to-transparent"></div>

            <!-- Social & Contact Links -->
            <div class="space-y-3">
                <h4 class="font-extrabold text-sm sm:text-base text-sage-dark">حسابات وروابط التواصل</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <a href="{{ $instagramUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="p-2.5 rounded-2xl hover:bg-primary-light/40 transition-colors flex items-center justify-between gap-3 group">
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
                       class="p-2.5 rounded-2xl hover:bg-primary-light/40 transition-colors flex items-center justify-between gap-3 group">
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
                       class="p-2.5 rounded-2xl hover:bg-primary-light/40 transition-colors flex items-center justify-between gap-3 group">
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

            <!-- Subtle Soft Divider -->
            <div class="h-px w-full bg-gradient-to-l from-transparent via-gray-200/60 to-transparent"></div>

            <!-- Location & Embedded Google Map -->
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

                <a href="{{ $mapUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   title="انقري لفتح الموقع في تطبيق الخرائط"
                   class="relative block w-full h-56 sm:h-72 rounded-2xl overflow-hidden border border-primary-soft/60 shadow-xs group cursor-pointer">
                    <iframe
                        src="{{ $mapIframe }}"
                        class="w-full h-full border-0 pointer-events-none filter saturate-150 brightness-95"
                        loading="lazy"
                        allowfullscreen=""
                        referrerpolicy="no-referrer-when-downgrade"
                        title="موقع صالون هيرلاين">
                    </iframe>

                    <div class="absolute inset-0 bg-sage-dark/10 group-hover:bg-sage-dark/25 transition-all flex items-end p-3.5">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/95 backdrop-blur-md text-xs font-bold text-sage-dark group-hover:bg-primary group-hover:text-white transition shadow-sm border border-gray-200/60">
                            <i class="fa-solid fa-location-dot text-primary group-hover:text-white text-xs"></i>
                            <span>فتح في خرائط جوجل (Google Maps)</span>
                            <span class="text-xs">↗</span>
                        </span>
                    </div>
                </a>
            </div>
        </div>

    </div>
</section>
