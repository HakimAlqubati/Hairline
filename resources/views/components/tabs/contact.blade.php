@props([
    'isActive' => false,
    'workingHours' => [],
    'location' => [],
    'contactLinks' => [],
    'salonIdentity' => [],
])

@php
    $mapIframe = $location['google_maps_iframe'] ?? null;
    if ($mapIframe && preg_match('/src=["\']([^"\']+)["\']/', $mapIframe, $matches)) {
        $mapIframe = $matches[1];
    }
    if (!$mapIframe) {
        $mapIframe = 'https://maps.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&hl=ar&z=16&output=embed';
    }

    $phone = $contactLinks['phone'] ?? '0548857705';
    $phoneClean = preg_replace('/[^0-9]/', '', $phone);
    $whatsapp = $contactLinks['whatsapp'] ?? 'https://wa.me/966548857705';
    $address = $location['full_address_ar'] ?? 'الرياض - حي المصيف، مخرج 5';
    $mapsUrl = (!empty($location['google_maps_url']) && !str_contains($location['google_maps_url'], '24.7677')) 
        ? $location['google_maps_url'] 
        : 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps';
@endphp

<section id="contactTabView" class="{{ $isActive ? '' : 'hidden' }} flex-1 w-full max-w-7xl 2xl:max-w-[1400px] mx-auto px-3.5 sm:px-6 lg:px-8 pt-2 sm:pt-3 md:pt-4 pb-3 md:pb-4 animate-step text-right">

    <!-- Main Content Container: Clean Modern Layout Fitting Viewport Without Scroll -->
    <div class="bg-white rounded-2xl sm:rounded-3xl p-3.5 sm:p-4 md:p-5 shadow-card border border-primary-soft/60 space-y-3">
        
        <!-- Controls & Location Strip Above The Map -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
            
            <!-- 1. موقع الصالون (Replaced Interactive Map Label) -->
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-8 h-8 rounded-lg bg-primary-light text-primary-dark flex items-center justify-center shrink-0 shadow-2xs">
                    <i class="fa-solid fa-location-dot text-sm"></i>
                </span>
                <div class="min-w-0">
                    <span class="text-[11px] font-bold text-gray-400 block leading-tight">موقع الصالون</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-sage-dark truncate leading-tight" title="{{ $address }}">
                        {{ $address }}
                    </h3>
                </div>
            </div>

            <!-- 2. Action Buttons (Smaller Size) & Social Links (Directly Above Map) -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 shrink-0 self-end md:self-auto">
                <!-- Small WhatsApp Button -->
                <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer"
                   title="محادثة واتساب مباشرة"
                   class="group inline-flex items-center justify-center gap-1.5 py-1.5 w-32 rounded-lg bg-transparent border border-primary text-primary-dark hover:text-white hover:bg-primary font-bold text-xs shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-sm text-primary group-hover:text-white transition-colors duration-200"></i>
                    <span>واتساب</span>
                </a>

                <!-- Small Call Button (Same Width, No "اتصال" Word) -->
                <a href="tel:{{ $phoneClean }}"
                   title="اتصال مباشر: {{ $phone }}"
                   class="group inline-flex items-center justify-center gap-1.5 py-1.5 w-32 rounded-lg bg-transparent border border-primary text-primary-dark hover:text-white hover:bg-primary font-bold text-xs shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer">
                    <i class="fa-solid fa-phone text-xs text-primary group-hover:text-white transition-colors duration-200"></i>
                    <span dir="ltr" class="font-mono text-xs">{{ $phone }}</span>
                </a>

                <!-- Subtle Divider -->
                <span class="h-4 w-px bg-gray-200 hidden sm:inline-block"></span>

                <!-- Social Media Channel Icons -->
                <div class="flex items-center gap-1.5">
                    <a href="{{ $contactLinks['instagram'] ?? 'https://www.instagram.com/Hairline.5' }}" target="_blank" rel="noopener noreferrer"
                       title="Instagram"
                       class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-surface hover:bg-primary-light text-sage-dark hover:text-primary-dark border border-gray-200 flex items-center justify-center transition shadow-2xs hover:scale-105 cursor-pointer">
                        <i class="fa-brands fa-instagram text-xs sm:text-sm"></i>
                    </a>
                    <a href="{{ $contactLinks['snapchat'] ?? 'https://www.snapchat.com/add/Hairline.5' }}" target="_blank" rel="noopener noreferrer"
                       title="Snapchat"
                       class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-surface hover:bg-primary-light text-sage-dark hover:text-primary-dark border border-gray-200 flex items-center justify-center transition shadow-2xs hover:scale-105 cursor-pointer">
                        <i class="fa-brands fa-snapchat text-xs sm:text-sm"></i>
                    </a>
                    <a href="{{ $contactLinks['tiktok'] ?? 'https://www.tiktok.com/@Hairline.TikTok' }}" target="_blank" rel="noopener noreferrer"
                       title="TikTok"
                       class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-surface hover:bg-primary-light text-sage-dark hover:text-primary-dark border border-gray-200 flex items-center justify-center transition shadow-2xs hover:scale-105 cursor-pointer">
                        <i class="fa-brands fa-tiktok text-[11px] sm:text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Full-Width Interactive Map Frame -->
        <div class="relative w-full h-[360px] sm:h-[400px] md:h-[430px] lg:h-[460px] xl:h-[480px] rounded-xl sm:rounded-2xl overflow-hidden border border-primary-soft/80 shadow-xs bg-neutral-100 group">
            <iframe 
                src="{{ $mapIframe }}"
                class="w-full h-full border-0 select-auto" 
                loading="lazy"
                allowfullscreen=""
                referrerpolicy="no-referrer-when-downgrade"
                title="موقع صالون هيرلاين">
            </iframe>

            <!-- Floating Subtle Location Badge -->
            <div class="pointer-events-none absolute bottom-3 right-3 z-10 flex items-center gap-2">
                <div class="px-3 py-1.5 rounded-xl bg-white/95 backdrop-blur-md text-[11px] font-extrabold text-sage-dark shadow-md border border-gray-200/60 flex items-center gap-1.5 pointer-events-auto">
                    <i class="fa-solid fa-location-dot text-primary text-xs"></i>
                    <span>حي المصيف • مخرج 5</span>
                </div>
            </div>
        </div>

    </div>

</section>
