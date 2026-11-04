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

<section id="contactTabView" class="{{ $isActive ? '' : 'hidden' }} flex-1 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 md:pt-8 pb-16 space-y-6 animate-step text-right">

    <!-- Header Section -->
    <div class="space-y-1.5 pb-2 border-b border-primary-soft/50">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-light/70 text-primary-dark text-xs font-bold">
            <i class="fa-solid fa-headset text-[11px]"></i>
            <span>خدمة العملاء والحجوزات</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-sage-dark tracking-tight">للتواصل والاستفسار</h2>
    </div>

    <!-- Main Content Container: Clean Modern Layout Without Rigid Boxes -->
    <div class="bg-white rounded-[32px] p-6 sm:p-8 md:p-10 shadow-card border border-primary-soft/60 space-y-8">
        
        <!-- Row 1: Direct Contact Buttons -->
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer"
               class="w-full sm:flex-1 py-4 px-6 rounded-2xl bg-[#25D366] hover:bg-[#1EBE5D] text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md shadow-[#25D366]/20 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                <i class="fa-brands fa-whatsapp text-lg"></i>
                <span>محادثة واتساب مباشرة</span>
            </a>

            <a href="tel:{{ $phoneClean }}"
               class="w-full sm:flex-1 py-4 px-6 rounded-2xl bg-primary hover:bg-primary-dark text-white font-bold text-sm flex items-center justify-center gap-2.5 shadow-md shadow-primary/25 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                <i class="fa-solid fa-phone text-sm"></i>
                <span dir="ltr">{{ $phone }}</span>
                <span>اتصال مباشر</span>
            </a>
        </div>

        <!-- Row 2: Working Hours & Location Info (Clean Typography) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
            <!-- Working Hours -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-primary-light text-primary-dark flex items-center justify-center shrink-0 shadow-2xs">
                    <i class="fa-regular fa-clock text-lg"></i>
                </div>
                <div class="space-y-1 min-w-0">
                    <span class="text-xs font-medium text-gray-400 block">أوقات العمل الرسمية</span>
                    <h3 class="text-base sm:text-lg font-bold text-sage-dark">
                        {{ $workingHours['hours_ar'] ?? 'من 2:00 ظهراً حتى 10:00 مساءً' }}
                    </h3>
                </div>
            </div>

            <!-- Salon Location -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-primary-light text-primary-dark flex items-center justify-center shrink-0 shadow-2xs">
                    <i class="fa-solid fa-location-dot text-lg"></i>
                </div>
                <div class="space-y-1 min-w-0">
                    <span class="text-xs font-medium text-gray-400 block">موقع الصالون</span>
                    <h3 class="text-base sm:text-lg font-bold text-sage-dark leading-snug">
                        {{ $address }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Row 3: Interactive Map Frame -->
        <div class="space-y-2 pt-2">
            <span class="text-xs font-medium text-gray-400 block">خريطة الوصول للصالون</span>
            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
               title="انقري لفتح الموقع في تطبيق الخرائط"
               class="relative block w-full h-64 sm:h-72 rounded-2xl overflow-hidden border border-primary-soft shadow-xs group cursor-pointer">
                <iframe 
                    src="{{ $mapIframe }}"
                    class="w-full h-full border-0 pointer-events-none filter saturate-150 brightness-95" 
                    loading="lazy">
                </iframe>
                <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-sage-dark/80 via-sage-dark/40 to-transparent flex items-center justify-between text-white text-xs font-bold">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-diamond-turn-right text-primary"></i>
                        <span>انقري لفتح الإتجاهات في Google Maps</span>
                    </span>
                    <span class="px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-xs text-[11px]">مخرج 5</span>
                </div>
            </a>
        </div>

        <!-- Row 4: Social Media Channels -->
        <div class="pt-6 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
            <div class="space-y-0.5">
                <span class="text-xs font-bold text-sage-dark block">تابعي حساباتنا الرسمية</span>
                <p class="text-[11px] text-gray-400">شاهدي أحدث جلسات العناية وأعمال الأخصائيات يومياً</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ $contactLinks['instagram'] ?? 'https://www.instagram.com/Hairline.5' }}" target="_blank" rel="noopener noreferrer"
                   title="Instagram"
                   class="w-10 h-10 rounded-full bg-surface hover:bg-primary-light text-sage-dark hover:text-primary-dark border border-gray-200 flex items-center justify-center transition shadow-2xs hover:scale-105 cursor-pointer">
                    <i class="fa-brands fa-instagram text-base"></i>
                </a>
                <a href="{{ $contactLinks['snapchat'] ?? 'https://www.snapchat.com/add/Hairline.5' }}" target="_blank" rel="noopener noreferrer"
                   title="Snapchat"
                   class="w-10 h-10 rounded-full bg-surface hover:bg-primary-light text-sage-dark hover:text-primary-dark border border-gray-200 flex items-center justify-center transition shadow-2xs hover:scale-105 cursor-pointer">
                    <i class="fa-brands fa-snapchat text-base"></i>
                </a>
                <a href="{{ $contactLinks['tiktok'] ?? 'https://www.tiktok.com/@Hairline.TikTok' }}" target="_blank" rel="noopener noreferrer"
                   title="TikTok"
                   class="w-10 h-10 rounded-full bg-surface hover:bg-primary-light text-sage-dark hover:text-primary-dark border border-gray-200 flex items-center justify-center transition shadow-2xs hover:scale-105 cursor-pointer">
                    <i class="fa-brands fa-tiktok text-sm"></i>
                </a>
            </div>
        </div>

    </div>

</section>
