<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} | {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }} {{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}</title>

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- iOS Safari Fullscreen / Web App Capable Meta Tags & Manifest -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#211C18">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Frontend Data Bridge (Available synchronously before ES Modules evaluate) -->
    <script>
        window.__HAIRLINE_DATA__ = {
            routes: {
                availability: "{{ route('booking.availability') }}",
                verifyPhone: "{{ route('booking.verify_phone') }}",
                guestConfirm: "{{ route('booking.guest_confirm') }}",
                googleRedirect: "{{ route('booking.google_redirect') }}",
                googleRegister: "{{ route('booking.google_register') }}",
                profileUpdate: "{{ route('profile.update') }}",
                profileLogout: "{{ route('profile.logout') }}",
                suggestions: "{{ route('services.suggestions') }}"
            },
            googleClientId: {!! json_encode(config('services.google.client_id')) !!},
            csrfToken: "{{ csrf_token() }}",
            initialView: "{{ $initialView ?? 'home' }}",
            heroSlides: {!! json_encode($heroSlides, JSON_UNESCAPED_UNICODE) !!},
            walkInServices: {!! json_encode($walkInServices, JSON_UNESCAPED_UNICODE) !!},
            specialists: {!! json_encode($specialistsData, JSON_UNESCAPED_UNICODE) !!},
            bookableCategories: {!! json_encode($bookableCategories, JSON_UNESCAPED_UNICODE) !!},
            bookingDates: {!! json_encode($bookingDates ?? [], JSON_UNESCAPED_UNICODE) !!},
            customer: {!! json_encode($currentCustomer ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!},
            bookings: {!! json_encode($initialBookings ?? [], JSON_UNESCAPED_UNICODE) !!},
            salonIdentity: {!! json_encode($salonIdentity, JSON_UNESCAPED_UNICODE) !!},
            workingHours: {!! json_encode($workingHours ?? [], JSON_UNESCAPED_UNICODE) !!},
            location: {!! json_encode($location ?? [], JSON_UNESCAPED_UNICODE) !!}
        };
    </script>

    <!-- Compiled Production Assets via Vite (Tailwind CSS v4 & Modular JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts: Tajawal, Montserrat & Cormorant Garamond -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600&family=Montserrat:wght@300;400;500;600;700&family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Official Service Menu Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/services-menu.css') }}">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Identity Services (OAuth 2.0 Popup) -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <!-- Dynamic Brand Theme Tokens Driven by Database / Service Provider -->
    <style>
        :root {
            --color-primary: {{ $brandPrimaryColor }};
            --color-gold: {{ $brandPrimaryColor }};
        }
    </style>
</head>

<body class="min-h-screen bg-[#F0EBE3] md:bg-[#FAF8F5] flex justify-center md:block selection:bg-sage-dark selection:text-white">

    <!-- Responsive App Canvas (Phone frame on mobile, full-screen website layout on desktop) -->
    <div id="appContainer" class="w-full max-w-[480px] md:max-w-none min-h-screen bg-surface md:bg-transparent relative flex flex-col shadow-2xl md:shadow-none overflow-x-hidden pb-28 md:pb-16 transition-all duration-300">

        <!-- 1. TOP HEADER COMPONENT (Edge-to-edge full screen with contained inner content) -->
        <x-header :salonIdentity="$salonIdentity" :workingHours="$workingHours" />

        <!-- 2. MAIN HOME VIEW (Contained in luxury max-w-7xl centered container) -->
        <main id="mainHomeView" class="{{ ($initialView ?? 'home') === 'home' ? '' : 'hidden' }} flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 md:pt-8 space-y-5 md:space-y-8">
            <!-- Main Service Rows (Strictly 2 Wide Rows) -->
            <x-service-rows />

            <!-- Hero Promotional Slider -->
            <x-hero-slider :heroSlides="$heroSlides" />
        </main>

        <!-- 3. DYNAMIC TAB VIEWS -->
        <x-tabs.services :menuPages="$menuPages ?? []" :isActive="($initialView ?? 'home') === 'services'" />
        <x-tabs.specialists :specialists="$specialistsData ?? []" :isActive="($initialView ?? 'home') === 'specialists'" />
        <x-tabs.booking :bookableCategories="$bookableCategories ?? []" :isActive="($initialView ?? 'home') === 'booking'" />
        <x-tabs.about :salonIdentity="$salonIdentity" :stats="$stats" :aboutUs="$aboutUs" :workingHours="$workingHours ?? []" :location="$location ?? []" :contactLinks="$contactLinks ?? []" :isActive="($initialView ?? 'home') === 'about'" />
        <x-tabs.account />
        <x-tabs.contact :salonIdentity="$salonIdentity" :workingHours="$workingHours ?? []" :location="$location ?? []" :contactLinks="$contactLinks ?? []" :isActive="($initialView ?? 'home') === 'contact'" />
        <x-tabs.privacy :salonIdentity="$salonIdentity" :isActive="($initialView ?? 'home') === 'privacy'" />

        <!-- 4. BOTTOM NAVIGATION BAR -->
        <x-bottom-nav />

        <!-- 5. SIDEBAR DRAWER (3 Items) -->
        <x-sidebar-drawer :salonIdentity="$salonIdentity" />

        <!-- 6. UNIVERSAL BOTTOM SHEET MODAL -->
        <x-bottom-sheet />

        <!-- 7. TOAST NOTIFICATION BANNER -->
        <x-toast />

        <!-- 8. IOS SAFARI FULLSCREEN / PWA MODAL -->
        <x-ios-fullscreen-modal />

        <!-- 9. DESKTOP FLOATING BOOKING CONCIERGE BUTTON -->
        <div id="desktopFloatingBookingContainer" class="hidden md:flex fixed bottom-8 left-8 z-40 items-center">
            <button id="desktopFloatingBookingBtn"
                    onclick="toggleDesktopBooking()"
                    aria-label="للحجز"
                    title="للحجز"
                    class="group flex items-center gap-2.5 px-5 py-3 rounded-full bg-primary hover:bg-primary-dark text-white shadow-fab border-2 border-white/80 hover:border-white hover:shadow-2xl active:scale-95 transition-all duration-300 cursor-pointer">
                
                <!-- Glowing Pulse Indicator -->
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-white shadow-xs"></span>
                </span>

                <i class="fa-regular fa-calendar-check text-lg text-white group-hover:rotate-12 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-black tracking-wide">للحجز</span>
            </button>
        </div>

        <!-- 10. FLOATING WHATSAPP BUTTON (Bottom-Right of Website) -->
        <div id="floatingWhatsappContainer"
             class="fixed bottom-24 md:bottom-8 right-4 sm:right-6 md:right-8 z-40 flex items-center transition-all duration-300 ease-out">
            <a id="floatingWhatsappBtn"
               href="{{ $contactLinks['whatsapp'] ?? 'https://wa.me/966548857705' }}"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="تواصلي معنا عبر واتساب"
               title="تواصلي معنا عبر واتساب"
               class="group relative w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-[#25D366] hover:bg-[#20BD5A] text-white shadow-fab border-2 border-white/90 flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-300 cursor-pointer">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#25D366] opacity-25 pointer-events-none"></span>
                <i class="fa-brands fa-whatsapp text-2xl sm:text-[28px] leading-none drop-shadow-sm group-hover:rotate-6 transition-transform"></i>
            </a>
        </div>

        <!-- 11. FLOATING CONFIRM SERVICE BUTTON (Bottom-Right of Website, Hidden Until Service Selected) -->
        <div id="floatingBookingConfirmContainer"
             class="fixed bottom-24 md:bottom-8 right-4 sm:right-6 md:right-8 z-50 hidden opacity-0 translate-y-4 scale-95 pointer-events-none transition-all duration-300 ease-out">
            <button type="button"
                    id="floatingBookingConfirmBtn"
                    onclick="proceedToSpecialistStep()"
                    aria-label="تأكيد الحجز والانتقال لاختيار الأخصائية"
                    class="group flex items-center gap-3 px-5 py-3.5 rounded-full bg-primary hover:bg-primary-dark text-white shadow-fab border-2 border-white/85 hover:border-white active:scale-95 transition-all duration-300 cursor-pointer">
                <span class="w-8 h-8 rounded-full bg-white/20 border border-white/40 flex items-center justify-center text-white shrink-0 group-hover:bg-white group-hover:text-primary-dark transition-colors">
                    <i class="fa-solid fa-check text-xs"></i>
                </span>
                <div class="text-right">
                    <span class="block text-xs sm:text-sm font-black tracking-wide leading-tight">
                        تأكيد الحجز واختيار الأخصائية
                    </span>
                    <span id="floatingSelectedServiceText" class="block text-[11px] font-bold text-white/90 truncate max-w-[190px] sm:max-w-[260px]">
                    </span>
                </div>
                <i class="fa-solid fa-arrow-left text-xs text-white group-hover:-translate-x-1 transition-transform mr-1"></i>
            </button>
        </div>

    </div>
</body>
</html>
