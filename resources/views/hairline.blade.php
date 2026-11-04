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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

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

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#F6EFE2',
                            soft: '#E6D5B8',
                            DEFAULT: '{{ $brandPrimaryColor }}',
                            dark: '#A3803B'
                        },
                        sage: {
                            light: '#EAE4DC',
                            soft: '#F6F3ED',
                            medium: '#94887D',
                            dark: '#211C18',
                            deeper: '#161311'
                        },
                        gold: {
                            light: '#F6EFE2',
                            soft: '#E6D5B8',
                            DEFAULT: '{{ $brandPrimaryColor }}',
                            dark: '#A3803B'
                        },
                        surface: '#FAF8F5'
                    },
                    fontFamily: {
                        sans: ['Tajawal', 'sans-serif'],
                        serif: ['Cormorant Garamond', 'serif']
                    },
                    boxShadow: {
                        'card': '0 6px 24px -4px rgba(33, 28, 24, 0.06)',
                        'floating': '0 -4px 25px rgba(33, 28, 24, 0.08)',
                        'fab': '0 10px 25px -3px rgba(33, 28, 24, 0.45)'
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --color-primary: {{ $brandPrimaryColor }};
            --color-gold: {{ $brandPrimaryColor }};
        }
        * {
            -webkit-tap-highlight-color: transparent;
            box-sizing: border-box;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #F6F3ED;
            color: #211C18;
            overflow-x: hidden;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .drawer-backdrop {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-drawer {
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .bottom-sheet {
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1), scale 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes subtlePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }
        .badge-pulse {
            animation: subtlePulse 3s infinite ease-in-out;
        }
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-step {
            animation: fadeSlideUp 0.3s ease-out forwards;
        }
        @keyframes bookingPageReveal {
            0% {
                opacity: 0;
                transform: translateY(18px) scale(0.99);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .animate-booking-page {
            animation: bookingPageReveal 0.42s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        body.simulated-fullscreen {
            background-color: #FAF8F5 !important;
        }
        body.simulated-fullscreen #appContainer {
            max-width: 100% !important;
            box-shadow: none !important;
        }
        .hairline-custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .hairline-custom-scroll::-webkit-scrollbar-track {
            background: #FAF8F5;
            border-radius: 9999px;
        }
        .hairline-custom-scroll::-webkit-scrollbar-thumb {
            background: #C29E6D;
            border-radius: 9999px;
        }
        .hairline-custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #A3803B;
        }
        .hairline-custom-scroll {
            scrollbar-width: thin;
            scrollbar-color: #C29E6D #FAF8F5;
        }
        .specialist-card {
            transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                        border-color 0.35s ease;
            will-change: transform;
        }
        .specialist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -12px rgba(194, 158, 109, 0.28), 0 8px 20px -6px rgba(33, 28, 24, 0.08);
        }
        .specialist-portrait-img {
            transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                        filter 0.45s ease;
            transform-origin: center bottom;
            will-change: transform;
        }
        .specialist-card:hover .specialist-portrait-img {
            transform: scale(1.07) translateY(-3px);
            filter: drop-shadow(0 12px 20px rgba(33, 28, 24, 0.12));
        }
        .specialist-arch-bg {
            transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                        border-color 0.4s ease,
                        background-color 0.4s ease;
        }
        .specialist-card:hover .specialist-arch-bg {
            transform: scale(1.03);
            border-color: rgba(194, 158, 109, 0.45);
            background-color: rgba(255, 255, 255, 0.92);
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
        <x-tabs.services :menuPages="$menuPages ?? []" />
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

    <!-- ==================== JAVASCRIPT DATA & LOGIC ==================== -->
    <script>
        /* -------------------------------------------------------------------------- */
        /* STATIC DATA FROM PHP SERVICE CLASSES (PDF Catalog, Salon Info, Specialists)*/
        /* -------------------------------------------------------------------------- */
        const heroSlides = {!! json_encode($heroSlides, JSON_UNESCAPED_UNICODE) !!};
        const walkInServicesData = {!! json_encode($walkInServices, JSON_UNESCAPED_UNICODE) !!};
        const specialistsData = {!! json_encode($specialistsData, JSON_UNESCAPED_UNICODE) !!};
        const bookableCategories = {!! json_encode($bookableCategories, JSON_UNESCAPED_UNICODE) !!};
        const bookingDates = {!! json_encode($bookingDates ?? [], JSON_UNESCAPED_UNICODE) !!};

        /* -------------------------------------------------------------------------- */
        /* STATE MANAGEMENT                                                           */
        /* -------------------------------------------------------------------------- */
        let currentSlideIndex = 0;
        let slideInterval = null;
        let currentActiveTab = "{{ $initialView ?? 'home' }}";
        let floatingConfirmHideTimeout = null;
        let availabilityAbortController = null;

        const defaultBookingDateObj = bookingDates[1] || bookingDates[0] || { display_label: "غداً - السبت", date_iso: "" };

        let bookingState = {
            step: 1, // 1: Services, 2: Specialist & Time, 3: Confirmation
            selectedCategory: null,
            selectedSubService: null,
            selectedSubServices: [],
            selectedSpecialist: null,
            selectedDate: defaultBookingDateObj.display_label,
            selectedDateIso: defaultBookingDateObj.date_iso,
            selectedTime: null,
            availability: {
                loading: false,
                is_day_off: false,
                shift_label: "",
                slots: []
            },
            verifiedPhone: "",
            phoneChecked: false,
            confirmedCustomerName: "",
            confirmedCustomerEmail: ""
        };

        // Saudi Mobile Regex: accepts 05XXXXXXXX, +9665XXXXXXXX, 009665XXXXXXXX, 9665XXXXXXXX, or 5XXXXXXXX
        const SAUDI_PHONE_REGEX = /^(?:\+966|00966|966|0)?5[0-9]{8}$/;

        // Logged-in customer & confirmed bookings backed directly by Database
        let currentCustomer = {!! json_encode($currentCustomer ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
        const dbInitialBookings = {!! json_encode($initialBookings ?? [], JSON_UNESCAPED_UNICODE) !!};
        let confirmedBookings = Array.isArray(dbInitialBookings) ? dbInitialBookings : [];

        /* -------------------------------------------------------------------------- */
        /* HERO SLIDER LOGIC                                                          */
        /* -------------------------------------------------------------------------- */
        function setHeroSlide(index) {
            currentSlideIndex = index;
            const slide = heroSlides[index];
            const imgEl = document.getElementById('heroSlideImg');
            const titleEl = document.getElementById('heroSlideTitle');
            const subtitleEl = document.getElementById('heroSlideSubtitle');
            const indicators = document.getElementById('sliderIndicators')?.children;

            if (imgEl && titleEl && subtitleEl && slide) {
                imgEl.style.opacity = '0.7';
                setTimeout(() => {
                    imgEl.src = slide.img;
                    imgEl.onerror = () => { imgEl.src = slide.fallback || 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80'; };
                    titleEl.textContent = slide.title;
                    subtitleEl.textContent = slide.subtitle;
                    imgEl.style.opacity = '1';
                }, 150);
            }

            if (indicators) {
                for (let i = 0; i < indicators.length; i++) {
                    if (i === index) {
                        indicators[i].className = "h-2 w-7 rounded-full bg-gold transition-all duration-300";
                    } else {
                        indicators[i].className = "h-2 w-2 rounded-full bg-white/70 hover:bg-white transition-all duration-300";
                    }
                }
            }
        }

        function startHeroSlider() {
            if (slideInterval) clearInterval(slideInterval);
            slideInterval = setInterval(() => {
                if (heroSlides && heroSlides.length > 0) {
                    const next = (currentSlideIndex + 1) % heroSlides.length;
                    setHeroSlide(next);
                }
            }, 5000);
        }

        /* -------------------------------------------------------------------------- */
        /* URL & NAVIGATION LOGIC                                                     */
        /* -------------------------------------------------------------------------- */
        function updateUrl(path) {
            if (window.history && window.history.pushState) {
                window.history.pushState(null, '', path);
            }
        }

        function toggleSidebar(show) {
            const overlay = document.getElementById('sidebarOverlay');
            const drawer = document.getElementById('sidebarDrawer');
            if (show) {
                overlay.classList.remove('hidden');
                requestAnimationFrame(() => {
                    overlay.classList.remove('opacity-0');
                    drawer.classList.remove('-translate-x-full');
                });
            } else {
                overlay.classList.add('opacity-0');
                drawer.classList.add('-translate-x-full');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }
        }

        function switchTab(tabName) {
            currentActiveTab = tabName;

            const homeView = document.getElementById('mainHomeView');
            const aboutView = document.getElementById('aboutTabView');
            const accountView = document.getElementById('accountTabView');
            const servicesView = document.getElementById('servicesTabView');
            const specialistsView = document.getElementById('specialistsTabView');
            const bookingView = document.getElementById('bookingTabView');
            const contactView = document.getElementById('contactTabView');
            const privacyView = document.getElementById('privacyTabView');
            const desktopFabContainer = document.getElementById('desktopFloatingBookingContainer');

            if (homeView) homeView.classList.add('hidden');
            if (aboutView) aboutView.classList.add('hidden');
            if (accountView) accountView.classList.add('hidden');
            if (servicesView) servicesView.classList.add('hidden');
            if (specialistsView) specialistsView.classList.add('hidden');
            if (bookingView) bookingView.classList.add('hidden');
            if (contactView) contactView.classList.add('hidden');
            if (privacyView) privacyView.classList.add('hidden');

            if (tabName === 'home') {
                if (homeView) homeView.classList.remove('hidden');
                updateUrl('/');
            }
            if (tabName === 'services') {
                if (servicesView) servicesView.classList.remove('hidden');
                updateUrl('/services');
            }
            if (tabName === 'specialists') {
                if (specialistsView) {
                    specialistsView.classList.remove('hidden');
                    specialistsView.classList.remove('animate-step');
                    void specialistsView.offsetWidth;
                    specialistsView.classList.add('animate-step');
                }
                updateUrl('/specialists');
            }
            if (tabName === 'booking') {
                if (bookingView) {
                    bookingView.classList.remove('hidden');
                    bookingView.classList.remove('animate-booking-page');
                    void bookingView.offsetWidth;
                    bookingView.classList.add('animate-booking-page');
                }
                updateUrl('/booking');
            }
            if (tabName === 'about') {
                if (aboutView) aboutView.classList.remove('hidden');
                updateUrl('/about');
            }
            if (tabName === 'account') {
                renderCustomerProfile();
                renderMyBookings();
                if (accountView) accountView.classList.remove('hidden');
                updateUrl('/profile');
            }
            if (tabName === 'contact') {
                if (contactView) {
                    contactView.classList.remove('hidden');
                    contactView.classList.remove('animate-step');
                    void contactView.offsetWidth;
                    contactView.classList.add('animate-step');
                }
                updateUrl('/contact');
            }
            if (tabName === 'privacy') {
                if (privacyView) {
                    privacyView.classList.remove('hidden');
                    privacyView.classList.remove('animate-step');
                    void privacyView.offsetWidth;
                    privacyView.classList.add('animate-step');
                }
                updateUrl('/privacy');
            }

            if (desktopFabContainer) {
                desktopFabContainer.classList.toggle('md:hidden', tabName === 'booking');
            }

            syncFloatingConfirmButton();

            // Update bottom nav active state (Mobile)
            const navIds = ['home', 'services', 'about', 'account'];
            navIds.forEach(id => {
                const btn = document.getElementById(`nav-${id}`);
                if (!btn) return;
                const dot = btn.querySelector('.nav-dot');
                if (id === tabName) {
                    btn.classList.remove('text-gray-400', 'font-medium');
                    btn.classList.add('text-sage-dark', 'font-bold');
                    if (dot) dot.classList.replace('bg-transparent', 'bg-gold');
                } else {
                    btn.classList.add('text-gray-400', 'font-medium');
                    btn.classList.remove('text-sage-dark', 'font-bold');
                    if (dot) dot.classList.replace('bg-gold', 'bg-transparent');
                }
            });

            // Update desktop top navbar active state
            const desktopNavIds = ['home', 'services', 'specialists', 'about', 'account'];
            desktopNavIds.forEach(id => {
                const dBtn = document.getElementById(`desktop-nav-${id}`);
                if (!dBtn) return;
                const dIndicator = dBtn.querySelector('.desktop-nav-indicator');
                if (id === tabName) {
                    dBtn.classList.add('text-white', 'font-black');
                    dBtn.classList.remove('text-white/75', 'font-medium');
                    if (dIndicator) {
                        dIndicator.classList.remove('opacity-0', 'scale-x-0');
                        dIndicator.classList.add('opacity-100', 'scale-x-100');
                    }
                } else {
                    dBtn.classList.remove('text-white', 'font-black');
                    dBtn.classList.add('text-white/75', 'font-medium');
                    if (dIndicator) {
                        dIndicator.classList.remove('opacity-100', 'scale-x-100');
                        dIndicator.classList.add('opacity-0', 'scale-x-0');
                    }
                }
            });

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        /* -------------------------------------------------------------------------- */
        /* SPECIALISTS STANDALONE PAGE & INTERACTIVE FLIP/DROP MODAL                  */
        /* -------------------------------------------------------------------------- */
        const specialistsListArray = Object.values(specialistsData);
        let currentSpecialistModalIndex = 0;
        let isSpecialistModalClosing = false;

        function getSpecialistServiceCategories(specialistId) {
            if (!Array.isArray(bookableCategories)) return [];
            const matchedTitles = [];
            bookableCategories.forEach(cat => {
                const hasSpec = Array.isArray(cat.subServices) && cat.subServices.some(
                    sub => Array.isArray(sub.specialists) && sub.specialists.includes(specialistId)
                );
                if (hasSpec && cat.title) {
                    matchedTitles.push(cat.title);
                }
            });
            return matchedTitles;
        }

        function populateSpecialistModal(index, direction = 0) {
            const total = specialistsListArray.length;
            if (total === 0) return;

            currentSpecialistModalIndex = ((index % total) + total) % total;
            const sp = specialistsListArray[currentSpecialistModalIndex];
            if (!sp) return;

            const counterEl = document.getElementById('specialistModalCounter');
            const imgEl = document.getElementById('specialistModalImg');
            const initialsWrapper = document.getElementById('specialistModalInitialsWrapper');
            const initialsText = document.getElementById('specialistModalInitials');
            const nameEl = document.getElementById('specialistModalName');
            const roleEl = document.getElementById('specialistModalRole');
            const expEl = document.getElementById('specialistModalExperience');
            const bioEl = document.getElementById('specialistModalBio');
            const servicesEl = document.getElementById('specialistModalServices');
            const servicesWrapper = document.getElementById('specialistModalServicesWrapper');
            const animatedBody = document.getElementById('specialistModalAnimatedBody');

            if (counterEl) {
                counterEl.textContent = `${currentSpecialistModalIndex + 1} / ${total}`;
            }

            if (sp.has_custom_avatar && sp.avatar) {
                if (imgEl) {
                    imgEl.src = sp.avatar;
                    imgEl.alt = sp.name || '';
                    imgEl.classList.remove('hidden');
                }
                if (initialsWrapper) initialsWrapper.classList.add('hidden');
            } else {
                if (imgEl) imgEl.classList.add('hidden');
                if (initialsWrapper) {
                    initialsWrapper.classList.remove('hidden');
                    if (initialsText) initialsText.textContent = sp.initials || 'HL';
                }
            }

            if (nameEl) nameEl.textContent = sp.name || '';
            if (roleEl) roleEl.textContent = sp.role || '';
            if (expEl) expEl.textContent = sp.experience || '';
            if (bioEl) bioEl.textContent = sp.bio || '';
            if (servicesWrapper) servicesWrapper.classList.add('hidden');

            // Highlight active thumbnail
            for (let i = 0; i < total; i++) {
                const thumbBtn = document.getElementById(`specialist-modal-thumb-${i}`);
                if (!thumbBtn) continue;
                if (i === currentSpecialistModalIndex) {
                    thumbBtn.className = 'w-10 h-10 rounded-full overflow-hidden border-2 border-primary ring-2 ring-primary/40 scale-110 shadow-sm transition-all duration-200 cursor-pointer bg-surface shrink-0';
                } else {
                    thumbBtn.className = 'w-9 h-9 rounded-full overflow-hidden border-2 border-primary-soft opacity-60 hover:opacity-100 hover:border-primary transition-all duration-200 cursor-pointer bg-surface shrink-0';
                }
            }

            // Directional slide animation when flipping between specialists
            if (animatedBody && direction !== 0) {
                animatedBody.classList.remove('modal-slide-right', 'modal-slide-left');
                void animatedBody.offsetWidth;
                animatedBody.classList.add(direction > 0 ? 'modal-slide-left' : 'modal-slide-right');
            }

            // Keep the active specialist card in view on the underlying page so the close animation drops right onto it
            const activeCard = document.getElementById(`specialist-card-${currentSpecialistModalIndex}`);
            if (activeCard && direction !== 0) {
                activeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        function openSpecialistModal(index) {
            const modal = document.getElementById('specialistDetailModal');
            const backdrop = document.getElementById('specialistModalBackdrop');
            const modalCard = document.getElementById('specialistModalCard');
            const prevBtn = document.getElementById('specialistModalPrevBtn');
            const nextBtn = document.getElementById('specialistModalNextBtn');
            if (!modal || !modalCard) return;

            isSpecialistModalClosing = false;
            populateSpecialistModal(index, 0);

            // Clear any previous landing animation classes on cards
            document.querySelectorAll('.specialist-card-landed').forEach(el => {
                el.classList.remove('specialist-card-landed');
            });

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            if (prevBtn) {
                prevBtn.style.opacity = '1';
                prevBtn.style.transform = '';
            }
            if (nextBtn) {
                nextBtn.style.opacity = '1';
                nextBtn.style.transform = '';
            }

            const sourceCard = document.getElementById(`specialist-card-${currentSpecialistModalIndex}`);
            modalCard.style.transition = 'none';
            modalCard.style.transform = 'translate3d(0, 0, 0) scale(1)';
            modalCard.style.opacity = '1';

            const modalRect = modalCard.getBoundingClientRect();

            if (sourceCard && modalRect.width > 0) {
                const sourceRect = sourceCard.getBoundingClientRect();
                if (sourceRect.width > 0) {
                    const deltaX = (sourceRect.left + sourceRect.width / 2) - (modalRect.left + modalRect.width / 2);
                    const deltaY = (sourceRect.top + sourceRect.height / 2) - (modalRect.top + modalRect.height / 2);
                    const scale = Math.max(0.25, Math.min(0.85, sourceRect.width / modalRect.width));

                    if (typeof modalCard.animate === 'function') {
                        modalCard.animate([
                            {
                                transform: `translate3d(${deltaX}px, ${deltaY}px, 0) scale(${scale})`,
                                opacity: 0.25
                            },
                            {
                                transform: 'translate3d(0, 0, 0) scale(1)',
                                opacity: 1
                            }
                        ], {
                            duration: 420,
                            easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                            fill: 'forwards'
                        });
                    }
                }
            }

            requestAnimationFrame(() => {
                if (backdrop) backdrop.classList.remove('opacity-0');
            });
        }

        function navigateSpecialistModal(step, event) {
            if (event) event.stopPropagation();
            if (isSpecialistModalClosing) return;
            populateSpecialistModal(currentSpecialistModalIndex + step, step);
        }

        function selectSpecialistInModal(targetIndex) {
            if (isSpecialistModalClosing || targetIndex === currentSpecialistModalIndex) return;
            const dir = targetIndex > currentSpecialistModalIndex ? 1 : -1;
            populateSpecialistModal(targetIndex, dir);
        }

        function closeSpecialistModal(afterCloseCallback = null) {
            const modal = document.getElementById('specialistDetailModal');
            const backdrop = document.getElementById('specialistModalBackdrop');
            const modalCard = document.getElementById('specialistModalCard');
            const prevBtn = document.getElementById('specialistModalPrevBtn');
            const nextBtn = document.getElementById('specialistModalNextBtn');

            if (!modal || !modalCard || modal.classList.contains('hidden') || isSpecialistModalClosing) {
                if (typeof afterCloseCallback === 'function') afterCloseCallback();
                return;
            }

            isSpecialistModalClosing = true;

            const targetIndex = currentSpecialistModalIndex;
            const targetCard = document.getElementById(`specialist-card-${targetIndex}`);

            // Ensure target specialist card is in the viewport so the modal visibly drops onto it
            if (targetCard) {
                const initialRect = targetCard.getBoundingClientRect();
                if (initialRect.top < 60 || initialRect.bottom > window.innerHeight - 40) {
                    targetCard.scrollIntoView({ behavior: 'instant', block: 'center' });
                }
            }

            if (backdrop) backdrop.classList.add('opacity-0');
            if (prevBtn) {
                prevBtn.style.opacity = '0';
                prevBtn.style.transform = 'translateY(-50%) scale(0.75)';
            }
            if (nextBtn) {
                nextBtn.style.opacity = '0';
                nextBtn.style.transform = 'translateY(-50%) scale(0.75)';
            }

            const modalRect = modalCard.getBoundingClientRect();
            const targetRect = targetCard ? targetCard.getBoundingClientRect() : null;

            let deltaX = 0;
            let deltaY = 140;
            let targetScale = 0.35;

            if (targetRect && targetRect.width > 0 && modalRect.width > 0) {
                const modalCenterX = modalRect.left + modalRect.width / 2;
                const modalCenterY = modalRect.top + modalRect.height / 2;
                const targetCenterX = targetRect.left + targetRect.width / 2;
                const targetCenterY = targetRect.top + targetRect.height / 2;

                deltaX = targetCenterX - modalCenterX;
                deltaY = targetCenterY - modalCenterY;

                const scaleW = targetRect.width / modalRect.width;
                const scaleH = targetRect.height / modalRect.height;
                targetScale = Math.max(0.2, Math.min(0.8, Math.min(scaleW, scaleH)));
            }

            const finishClose = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modalCard.style.transform = '';
                modalCard.style.opacity = '';
                modalCard.style.transition = '';
                isSpecialistModalClosing = false;

                if (typeof afterCloseCallback === 'function') {
                    afterCloseCallback();
                }
            };

            // Trigger landing pulse on the active specialist card right as the modal drops onto it
            if (targetCard) {
                setTimeout(() => {
                    targetCard.classList.remove('specialist-card-landed');
                    void targetCard.offsetWidth;
                    targetCard.classList.add('specialist-card-landed');
                }, 280);
            }

            if (typeof modalCard.animate === 'function') {
                const dropAnimation = modalCard.animate([
                    {
                        transform: 'translate3d(0, 0, 0) scale(1)',
                        opacity: 1,
                        offset: 0
                    },
                    {
                        transform: `translate3d(${deltaX * 0.15}px, -14px, 0) scale(0.95)`,
                        opacity: 0.96,
                        offset: 0.18
                    },
                    {
                        transform: `translate3d(${deltaX}px, ${deltaY + 10}px, 0) scale(${targetScale * 0.96})`,
                        opacity: 0.55,
                        offset: 0.84
                    },
                    {
                        transform: `translate3d(${deltaX}px, ${deltaY}px, 0) scale(${targetScale})`,
                        opacity: 0,
                        offset: 1
                    }
                ], {
                    duration: 460,
                    easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                    fill: 'forwards'
                });

                dropAnimation.onfinish = () => {
                    dropAnimation.cancel();
                    finishClose();
                };
            } else {
                modalCard.style.transition = 'transform 0.44s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.4s ease';
                modalCard.style.transform = `translate3d(${deltaX}px, ${deltaY}px, 0) scale(${targetScale})`;
                modalCard.style.opacity = '0';
                setTimeout(finishClose, 450);
            }
        }

        function bookWithSpecialistFromModal() {
            closeSpecialistModal(() => {
                openBookingFlow();
            });
        }

        // Keyboard (Left/Right/Escape) & Touch Swipe Support for Specialist Modal
        document.addEventListener('keydown', function (e) {
            const modal = document.getElementById('specialistDetailModal');
            if (!modal || modal.classList.contains('hidden')) return;

            if (e.key === 'Escape') {
                e.preventDefault();
                closeSpecialistModal();
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                navigateSpecialistModal(1);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                navigateSpecialistModal(-1);
            }
        });

        (function initSpecialistModalTouchSwipe() {
            let touchStartX = 0;
            let touchEndX = 0;

            document.addEventListener('touchstart', function (e) {
                const modal = document.getElementById('specialistDetailModal');
                if (!modal || modal.classList.contains('hidden')) return;
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            document.addEventListener('touchend', function (e) {
                const modal = document.getElementById('specialistDetailModal');
                if (!modal || modal.classList.contains('hidden')) return;
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchEndX - touchStartX;
                if (Math.abs(diff) > 55) {
                    if (diff > 0) {
                        navigateSpecialistModal(1);
                    } else {
                        navigateSpecialistModal(-1);
                    }
                }
            }, { passive: true });
        })();

        /* -------------------------------------------------------------------------- */
        /* SIDEBAR SECTIONS                                                           */
        /* -------------------------------------------------------------------------- */
        function openSidebarSection(sectionType) {
            toggleSidebar(false);

            if (sectionType === 'specialists') {
                closeBottomSheet();
                switchTab('specialists');
                return;
            }

            if (sectionType === 'contact') {
                closeBottomSheet();
                switchTab('contact');
                return;
            }

            if (sectionType === 'privacy') {
                closeBottomSheet();
                switchTab('privacy');
                return;
            }

            const sheetTitle = document.getElementById('sheetTitle');
            const sheetSubtitle = document.getElementById('sheetSubtitle');
            const sheetBackBtn = document.getElementById('sheetBackBtn');
            const sheetBody = document.getElementById('sheetBody');
            const sheetFooter = document.getElementById('sheetFooter');

            if (sheetBackBtn) sheetBackBtn.classList.add('hidden');
            if (sheetFooter) sheetFooter.classList.add('hidden');

            if (sectionType === 'about') {
                sheetTitle.textContent = "التعريف بالصالون (من نحن)";
                sheetSubtitle.textContent = "{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} | {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }} {{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}";
                sheetBody.innerHTML = `
                    <div class="space-y-4 animate-step">
                        <div class="relative h-44 rounded-2xl overflow-hidden shadow-xs border border-gray-100">
                            <img src="{{ asset('images/salon/salon-screen-display.jpg') }}" 
                                 alt="صالون هيرلاين" 
                                 onerror="this.src='https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&w=900&q=80'"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-sage-dark/90 to-transparent flex items-end p-4">
                                <span class="text-white font-bold text-sm">الفخامة والعناية المتخصصة بأرقى المعايير</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            تأسس صالون <strong class="text-sage-dark">هيرلاين</strong> ليكون الملاذ الأول لكل سيدة تبحث عن التميز في العناية بشعرها وجمالها في حي المصيف بالرياض (مخرج 5). نعتمد في كافة جلساتنا على تشخيص دقيق لطبيعة الشعر وأحدث المعالجات المعتمدة في قائمة خدماتنا الرسمية.
                        </p>
                        <div class="bg-sage-soft p-4 rounded-2xl border border-sage-light space-y-2">
                            <h4 class="font-extrabold text-xs text-sage-dark flex items-center gap-2">
                                <i class="fa-solid fa-star text-gold"></i>
                                <span>لماذا تختارين هيرلاين؟</span>
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1.5 pr-4 list-disc">
                                <li>منتجات أصلية 100% آمنة معتمدة من هيئة الغذاء والدواء.</li>
                                <li>فريق أخصائيات معتمدات بخبرات تتجاوز 10 سنوات.</li>
                                <li>أقسام مجهزة بأعلى معايير التعقيم والخصوصية التامة.</li>
                                <li>أسعار واضحة ومعتمدة شاملة ضريبة القيمة المضافة 15%.</li>
                            </ul>
                        </div>
                    </div>
                `;
            } else if (sectionType === 'contact') {
                sheetTitle.textContent = "للتواصل والاستفسار";
                sheetSubtitle.textContent = "نسعد بخدمتكِ والرد على استفساراتكِ";
                sheetBody.innerHTML = `
                    <div class="space-y-3.5 animate-step">
                        <div class="p-4 rounded-2xl bg-sage-soft border border-sage-light">
                            <h4 class="font-extrabold text-sm text-sage-dark">أوقات العمل الرسمية</h4>
                            <p class="text-xs text-gray-600 mt-1">{{ $workingHours['hours_ar'] ?? 'يومياً من الساعة 2:00 ظهراً حتى 10:00 مساءً' }}</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h4 class="font-extrabold text-sm text-sage-dark">التواصل والحجوزات</h4>
                                <p class="text-xs text-gray-500 dir-ltr text-right mt-0.5">0548857705</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="https://wa.me/966548857705" target="_blank" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-[#25D366] hover:bg-[#1EBE5D] text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs shadow-[#25D366]/25">
                                    <i class="fa-brands fa-whatsapp text-sm text-white"></i>
                                    <span>واتساب</span>
                                </a>
                                <a href="tel:0548857705" class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-dark text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs shadow-primary/25">
                                    <i class="fa-solid fa-phone text-xs text-white"></i>
                                    <span>اتصال</span>
                                </a>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-extrabold text-sm text-sage-dark">العنوان وموقع الصالون</h4>
                                    <p class="text-xs text-gray-700 font-medium leading-relaxed mt-0.5">{{ $location['full_address_ar'] ?? 'الرياض - حي المصيف، مخرج 5' }}</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full bg-gold-light text-gold-dark text-[10px] font-bold border border-gold/30 shrink-0">مخرج 5</span>
                            </div>

                            <!-- Clickable Map Frame -->
                            <a href="{{ (!empty($location['google_maps_url']) && !str_contains($location['google_maps_url'], '24.7677')) ? $location['google_maps_url'] : 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps' }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               title="انقري لفتح الموقع في تطبيق الخرائط"
                               class="relative block w-full h-44 rounded-xl overflow-hidden border border-gold-soft shadow-xs group cursor-pointer">
                                <iframe 
                                    src="https://maps.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&hl=ar&z=16&output=embed"
                                    class="w-full h-full border-0 pointer-events-none filter saturate-150 brightness-95" 
                                    loading="lazy" 
                                    allowfullscreen="" 
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="موقع صالون هيرلاين">
                                </iframe>
                                
                                <!-- Floating Click-to-Open Badge -->
                                <div class="absolute inset-0 bg-sage-dark/10 group-hover:bg-sage-dark/25 transition-all flex items-end p-2.5">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/95 backdrop-blur-xs text-xs font-bold text-sage-dark group-hover:bg-gold group-hover:text-white transition shadow-sm border border-gray-200/60">
                                        <i class="fa-solid fa-location-dot text-gold group-hover:text-white text-xs"></i>
                                        <span>فتح في خرائط جوجل (Google Maps)</span>
                                        <span class="text-[11px]">↗</span>
                                    </span>
                                </div>
                            </a>

                        </div>
                    </div>
                `;
            } else if (sectionType === 'privacy') {
                sheetTitle.textContent = "سياسة الخصوصية";
                sheetSubtitle.textContent = "التزامنا التام بخصوصيتكِ وسلامة وأمان بياناتكِ";
                sheetBody.innerHTML = `
                    <div class="space-y-3.5 animate-step">
                        <!-- Card 1: خصوصية الجلسات وبيئة الصالون -->
                        <div class="p-4 rounded-2xl bg-sage-soft border border-sage-light flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-sage-dark text-gold flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-shield-halved text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-sage-dark">خصوصية المكان والزيارة</h4>
                                <p class="text-xs text-gray-600 leading-relaxed mt-1">
                                    نضمن لكِ بيئة نسائية خاصة ومحمية بالكامل 100%. تم تصميم كافة أقسام صالون هيرلاين وغرف المعالجات لتوفير أقصى درجات الراحة والاستقلالية والسكينة لكافة عميلاتنا.
                                </p>
                            </div>
                        </div>

                        <!-- Card 2: سياسة حظر التصوير -->
                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-gold-light text-gold-dark flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-camera-slash text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-sage-dark">حظر التصوير الصارم</h4>
                                <p class="text-xs text-gray-600 leading-relaxed mt-1">
                                    يُمنع منعاً باتاً التقاط الصور أو تسجيل مقاطع الفيديو داخل أروقة وأقسام الصالون احتراماً لخصوصية العميلات. أي توثيق لنتائج الخدمات لا يتم إلا بطلب وموافقة صريحة من العميلة وبما يحفظ هويتها.
                                </p>
                            </div>
                        </div>

                        <!-- Card 3: سرية بيانات الحجز والعميلات -->
                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-sage-light text-sage-dark flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-user-lock text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-sage-dark">سرية البيانات الشخصية</h4>
                                <p class="text-xs text-gray-600 leading-relaxed mt-1">
                                    بياناتكِ المسجلة (الاسم، رقم الجوال، وسجل الحجوزات) مشفرة ومحمية وتُستخدم فقط لتأكيد مواعيدكِ وإرسال تذكيرات الحجز، ولا يتم مشاركتها أو بيعها لأي جهة خارجية إطلاقاً.
                                </p>
                            </div>
                        </div>

                        <!-- Card 4: أمان المعاملات المالية والدفع -->
                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-gold-light text-gold-dark flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-credit-card text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-sage-dark">أمان المدفوعات</h4>
                                <p class="text-xs text-gray-600 leading-relaxed mt-1">
                                    تتم كافة عمليات الحجز والدفع الإلكتروني عبر بوابات دفع بنكية معتمدة ومشفّرة بمعايير الأمان العالمية، دون تخزين أي بيانات للبطاقات البنكية أو أرقام سرية لدينا.
                                </p>
                            </div>
                        </div>

                        <!-- Card 5: الدعم والاستفسار -->
                        <div class="p-3.5 rounded-xl bg-gold-light/40 border border-gold-soft/40 flex items-center justify-between text-xs">
                            <span class="text-gray-600 font-medium">لديكِ استفسار حول سياسة الخصوصية؟</span>
                            <button onclick="openSidebarSection('contact')" class="font-bold text-gold-dark hover:underline flex items-center gap-1">
                                <span>تواصلي معنا</span>
                                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                `;
            }

            showBottomSheet();
        }

        /* -------------------------------------------------------------------------- */
        /* WALK-IN SERVICES CATALOG (من ملف الـ PDF)                                   */
        /* -------------------------------------------------------------------------- */
        function openAllServicesModal() {
            closeBottomSheet();
            switchTab('services');
        }

        /* -------------------------------------------------------------------------- */
        /* 2-STEP BOOKING FLOW + PHONE REGEX POPUP & GOOGLE REGISTRATION (DB-BACKED)  */
        /* -------------------------------------------------------------------------- */
        function openBookingFlow() {
            if (availabilityAbortController) {
                availabilityAbortController.abort();
            }
            const initialDateObj = bookingDates[1] || bookingDates[0] || { display_label: "غداً - السبت", date_iso: "" };
            bookingState = {
                step: 1,
                selectedCategory: null,
                selectedSubService: null,
                selectedSubServices: [],
                selectedSpecialist: null,
                selectedDate: initialDateObj.display_label,
                selectedDateIso: initialDateObj.date_iso,
                selectedTime: null,
                availability: {
                    loading: false,
                    is_day_off: false,
                    shift_label: "",
                    slots: []
                },
                verifiedPhone: "",
                phoneChecked: false,
                confirmedCustomerName: "",
                confirmedCustomerEmail: ""
            };

            clearAllServiceCheckboxes();
            clearUnifiedServiceSearch('booking', false);
            expandAllBookingCategories();
            switchTab('booking');
            renderBookingStep();
        }

        function openBookingFlowWithCategory(categoryId) {
            const aliasMap = {
                'hair_treatments': 'hot_treatments',
                'hair_services': 'color_services',
                'blowdry': 'hairstyles',
                'nails': 'color_services'
            };
            const targetId = aliasMap[categoryId] || categoryId;
            openBookingFlow();
            setTimeout(() => {
                const sectionEl = document.getElementById(`booking-cat-${targetId}`) || document.getElementById(`booking-cat-${categoryId}`);
                if (sectionEl) {
                    sectionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 250);
        }

        function handleBookingBack() {
            if (bookingState.step === 2) {
                if (availabilityAbortController) {
                    availabilityAbortController.abort();
                }
                bookingState.step = 1;
                bookingState.selectedSpecialist = null;
                bookingState.selectedTime = null;
                bookingState.availability = {
                    loading: false,
                    is_day_off: false,
                    shift_label: "",
                    slots: []
                };
                renderBookingStep();
            }
        }

        function handleSheetBack() {
            handleBookingBack();
        }

        function toggleBookingTreeCategory(catId) {
            const btn = document.getElementById(`tree-btn-${catId}`);
            const panel = document.getElementById(`tree-panel-${catId}`);
            const chevron = document.getElementById(`tree-chevron-${catId}`);
            if (!btn || !panel) return;

            const isExpanded = btn.getAttribute('aria-expanded') === 'true';
            setCategoryExpandedState(catId, !isExpanded);
            syncToggleAllTreeButton();
        }

        function setCategoryExpandedState(catId, expand) {
            const btn = document.getElementById(`tree-btn-${catId}`);
            const panel = document.getElementById(`tree-panel-${catId}`);
            const chevron = document.getElementById(`tree-chevron-${catId}`);
            if (!btn || !panel) return;

            btn.setAttribute('aria-expanded', expand ? 'true' : 'false');
            if (expand) {
                panel.classList.remove('grid-rows-[0fr]', 'opacity-0');
                panel.classList.add('grid-rows-[1fr]', 'opacity-100');
                if (chevron) chevron.classList.remove('rotate-90');
            } else {
                panel.classList.remove('grid-rows-[1fr]', 'opacity-100');
                panel.classList.add('grid-rows-[0fr]', 'opacity-0');
                if (chevron) chevron.classList.add('rotate-90');
            }
        }

        function expandAllBookingCategories() {
            document.querySelectorAll('[data-category-tree]').forEach(el => {
                const catId = el.getAttribute('data-category-tree');
                if (catId) setCategoryExpandedState(catId, true);
            });
            syncToggleAllTreeButton();
        }

        function toggleAllBookingCategories() {
            const visibleCategories = Array.from(document.querySelectorAll('[data-category-tree]:not(.hidden)'));
            const anyExpanded = visibleCategories.some(el => {
                const catId = el.getAttribute('data-category-tree');
                const btn = catId ? document.getElementById(`tree-btn-${catId}`) : null;
                return btn && btn.getAttribute('aria-expanded') === 'true';
            });
            const targetState = !anyExpanded;

            visibleCategories.forEach(el => {
                const catId = el.getAttribute('data-category-tree');
                if (catId) setCategoryExpandedState(catId, targetState);
            });
            syncToggleAllTreeButton();
        }

        function syncToggleAllTreeButton() {
            const visibleCategories = Array.from(document.querySelectorAll('[data-category-tree]:not(.hidden)'));
            const anyExpanded = visibleCategories.some(el => {
                const catId = el.getAttribute('data-category-tree');
                const btn = catId ? document.getElementById(`tree-btn-${catId}`) : null;
                return btn && btn.getAttribute('aria-expanded') === 'true';
            });
            const text = document.getElementById('toggleAllTreeText');

            if (text) {
                text.textContent = anyExpanded ? 'طي الكل' : 'فتح الكل';
            }
        }

        function clearAllServiceCheckboxes() {
            document.querySelectorAll('.booking-service-checkbox').forEach(cb => {
                cb.checked = false;
            });
            document.querySelectorAll('.booking-tree-row').forEach(row => {
                row.classList.remove('bg-gold-light/40');
            });
            bookingState.selectedSubServices = [];
            bookingState.selectedSubService = null;
            bookingState.selectedCategory = null;
            syncFloatingConfirmButton();
        }

        function findCategoryAndSubService(subId, catId = null) {
            let foundCategory = catId ? bookableCategories.find(c => c.id === catId) : bookingState.selectedCategory;
            let foundSub = foundCategory ? foundCategory.subServices.find(s => s.id === subId) : null;

            if (!foundSub) {
                for (const cat of bookableCategories) {
                    const candidate = cat.subServices.find(s => s.id === subId);
                    if (candidate) {
                        foundCategory = cat;
                        foundSub = candidate;
                        break;
                    }
                }
            }

            return { foundCategory, foundSub };
        }

        function handleServiceCheckboxChange(checkboxEl, subId, catId = null) {
            if (!checkboxEl) return;
            if (!Array.isArray(bookingState.selectedSubServices)) {
                bookingState.selectedSubServices = [];
            }

            const activeRow = document.getElementById(`srv-row-${subId}`);

            if (checkboxEl.checked) {
                if (activeRow) {
                    activeRow.classList.add('bg-gold-light/40');
                }
                const { foundCategory, foundSub } = findCategoryAndSubService(subId, catId);
                if (foundSub && !bookingState.selectedSubServices.some(s => s.id === foundSub.id)) {
                    bookingState.selectedSubServices.push(foundSub);
                }
                if (!bookingState.selectedCategory && foundCategory) {
                    bookingState.selectedCategory = foundCategory;
                }
                bookingState.selectedSubService = bookingState.selectedSubServices[0] || null;
            } else {
                if (activeRow) {
                    activeRow.classList.remove('bg-gold-light/40');
                }
                bookingState.selectedSubServices = bookingState.selectedSubServices.filter(s => s.id !== subId);
                bookingState.selectedSubService = bookingState.selectedSubServices[0] || null;
                if (bookingState.selectedSubServices.length === 0) {
                    bookingState.selectedCategory = null;
                }
            }

            syncFloatingConfirmButton();
        }

        function syncFloatingConfirmButton() {
            const container = document.getElementById('floatingBookingConfirmContainer');
            const labelEl = document.getElementById('floatingSelectedServiceText');
            const whatsappContainer = document.getElementById('floatingWhatsappContainer');
            if (!container) return;

            const count = Array.isArray(bookingState.selectedSubServices) ? bookingState.selectedSubServices.length : 0;
            const shouldShow = (
                currentActiveTab === 'booking' &&
                bookingState.step === 1 &&
                count > 0
            );

            if (floatingConfirmHideTimeout) {
                clearTimeout(floatingConfirmHideTimeout);
                floatingConfirmHideTimeout = null;
            }

            if (whatsappContainer) {
                whatsappContainer.classList.toggle('-translate-y-16', shouldShow);
            }

            if (shouldShow) {
                if (labelEl) {
                    if (count === 1) {
                        labelEl.textContent = `${bookingState.selectedSubServices[0].name}`;
                    } else {
                        labelEl.textContent = `متابعة الحجز (${count} خدمات مختارة)`;
                    }
                }
                container.classList.remove('hidden');
                requestAnimationFrame(() => {
                    container.classList.remove('opacity-0', 'translate-y-4', 'scale-95', 'pointer-events-none');
                    container.classList.add('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
                });
            } else {
                container.classList.remove('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
                container.classList.add('opacity-0', 'translate-y-4', 'scale-95', 'pointer-events-none');
                floatingConfirmHideTimeout = setTimeout(() => {
                    container.classList.add('hidden');
                }, 280);
            }
        }

        function proceedToSpecialistStep() {
            const count = Array.isArray(bookingState.selectedSubServices) ? bookingState.selectedSubServices.length : 0;
            if (count === 0 && !bookingState.selectedSubService) return;
            if (!bookingState.selectedSubService && count > 0) {
                bookingState.selectedSubService = bookingState.selectedSubServices[0];
            }
            bookingState.selectedSpecialist = null;
            bookingState.selectedTime = null;
            bookingState.availability = { loading: true, is_day_off: false, shift_label: "", slots: [] };
            bookingState.step = 2;
            renderBookingStep();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateStepperUI(step) {
            const bookingStepper = document.getElementById('bookingStepper');
            const backBtn = document.getElementById('bookingPageBackBtn');
            const toggleAllBtn = document.getElementById('toggleAllTreeBtn');

            if (bookingStepper) {
                if (step === 1 || step === 2) {
                    bookingStepper.classList.remove('hidden');
                } else {
                    bookingStepper.classList.add('hidden');
                }
            }

            if (backBtn) {
                if (step === 2) {
                    backBtn.classList.remove('hidden');
                } else {
                    backBtn.classList.add('hidden');
                }
            }

            if (toggleAllBtn) {
                if (step === 1) {
                    toggleAllBtn.classList.remove('hidden');
                } else {
                    toggleAllBtn.classList.add('hidden');
                }
            }

            for (let i = 1; i <= 2; i++) {
                const el = document.getElementById(`stepIndicator${i}`);
                if (!el) continue;
                const bar = el.children[0];
                const label = el.children[1];

                if (i < step) {
                    bar.className = "w-full h-1.5 rounded-full bg-primary mb-1.5 transition-colors duration-300";
                    label.className = "text-xs font-bold text-primary-dark";
                } else if (i === step) {
                    bar.className = "w-full h-1.5 rounded-full bg-primary mb-1.5 transition-colors duration-300";
                    label.className = "text-xs font-extrabold text-primary-dark";
                } else {
                    bar.className = "w-full h-1.5 rounded-full bg-primary-soft/40 mb-1.5 transition-colors duration-300";
                    label.className = "text-xs font-medium text-gray-400";
                }
            }
        }

        function renderBookingStep() {
            const pageTitle = document.getElementById('bookingPageTitle');
            const pageSubtitle = document.getElementById('bookingPageSubtitle');
            const step1Container = document.getElementById('bookingStep1Container');
            const step2Container = document.getElementById('bookingStep2Container');
            const step2Body = document.getElementById('bookingStep2Body');
            const step2Footer = document.getElementById('bookingStep2Footer');
            const step3Container = document.getElementById('bookingStep3Container');

            updateStepperUI(bookingState.step);
            syncFloatingConfirmButton();

            if (step1Container) step1Container.classList.add('hidden');
            if (step2Container) step2Container.classList.add('hidden');
            if (step3Container) step3Container.classList.add('hidden');

            // STEP 1: عرض الشجرة المبسطة للخدمات المتاحة للحجز (مفتوحة افتراضياً مع Checkbox)
            if (bookingState.step === 1) {
                if (pageTitle) pageTitle.textContent = "الخدمات المتاحة للحجز";
                if (pageSubtitle) {
                    pageSubtitle.textContent = "";
                    pageSubtitle.classList.add('hidden');
                }
                if (step1Container) step1Container.classList.remove('hidden');
            }

            // STEP 2: اختيار الأخصائية وتحديد الوقت (مرتبط بشيفتات قاعدة البيانات والتحقق من المواعيد المحجوزة)
            else if (bookingState.step === 2) {
                const subServices = (Array.isArray(bookingState.selectedSubServices) && bookingState.selectedSubServices.length > 0)
                    ? bookingState.selectedSubServices
                    : (bookingState.selectedSubService ? [bookingState.selectedSubService] : []);
                if (subServices.length === 0) return;
                const sub = subServices[0];
                bookingState.selectedSubService = sub;

                if (pageTitle) pageTitle.textContent = "الخطوة 2: اختيار الأخصائية";
                if (pageSubtitle) {
                    pageSubtitle.textContent = "";
                    pageSubtitle.classList.add('hidden');
                }
                if (step2Container) step2Container.classList.remove('hidden');

                const allSpecialistLists = subServices.map(s => s.specialists || []);
                const commonSlugs = allSpecialistLists.length > 1
                    ? allSpecialistLists.reduce((a, b) => a.filter(c => b.includes(c)))
                    : (allSpecialistLists[0] || []);
                const finalSpecialistSlugs = commonSlugs.length > 0 ? commonSlugs : [...new Set(allSpecialistLists.flat())];
                const linkedSpecialists = finalSpecialistSlugs.map(id => specialistsData[id]).filter(Boolean);

                if (!bookingState.selectedSpecialist && linkedSpecialists.length > 0) {
                    bookingState.selectedSpecialist = linkedSpecialists[0];
                    bookingState.selectedTime = null;
                    bookingState.availability = {
                        loading: true,
                        is_day_off: false,
                        shift_label: "",
                        slots: []
                    };
                    fetchSpecialistAvailability();
                }

                const datesList = (Array.isArray(bookingDates) && bookingDates.length > 0)
                    ? bookingDates
                    : [
                        { display_label: "اليوم", date_iso: "" },
                        { display_label: "غداً - السبت", date_iso: "" }
                    ];

                const isLoadingSlots = Boolean(bookingState.availability?.loading);
                const shiftSlots = Array.isArray(bookingState.availability?.slots) ? bookingState.availability.slots : [];
                const isDayOff = Boolean(bookingState.availability && bookingState.availability.is_day_off);
                const shiftLabel = bookingState.availability?.shift_label || "";

                if (step2Body) {
                    const servicesTitleText = subServices.map(s => s.name).join(' • ');
                    step2Body.innerHTML = `
                        <div class="space-y-4">
                            <!-- Summary Card: Centered, No Price, No Duration -->
                            <div class="p-4 rounded-2xl bg-gold-light/60 border border-gold/30 text-center space-y-1">
                                <span class="text-[11px] font-bold text-gold-dark block">
                                    ${subServices.length > 1 ? `الخدمات المختارة (${subServices.length})` : (bookingState.selectedCategory?.title || 'الخدمة المختارة')}
                                </span>
                                <h4 class="font-black text-base sm:text-lg text-sage-dark text-center leading-relaxed">
                                    ${servicesTitleText}
                                </h4>
                            </div>

                            <!-- Specialists List (Large Luxury Cards Styled Like Specialists Page) -->
                            <div class="space-y-3">
                                <div class="${linkedSpecialists.length === 1 ? 'w-full max-w-sm sm:max-w-md mx-auto' : (linkedSpecialists.length === 2 ? 'grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-3xl mx-auto' : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5')}">
                                    ${linkedSpecialists.map(sp => {
                                        const nameParts = (sp.name || '').split('|').map(s => s.trim());
                                        const nameAr = nameParts[0] || (sp.name || '');
                                        const nameEn = nameParts[1] || '';
                                        const isSelected = bookingState.selectedSpecialist && bookingState.selectedSpecialist.id === sp.id;
                                        return `
                                            <article onclick="chooseSpecialist('${sp.id}')" 
                                                     role="button"
                                                     tabindex="0"
                                                     onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); chooseSpecialist('${sp.id}'); }"
                                                     aria-label="اختيار الأخصائية ${nameAr}"
                                                     class="specialist-card group relative rounded-[28px] bg-white border-2 transition-all duration-300 cursor-pointer select-none overflow-hidden flex flex-col shadow-card ${
                                                         isSelected 
                                                         ? 'border-primary ring-4 ring-primary/20 shadow-xl' 
                                                         : 'border-primary-soft/60 hover:border-primary hover:shadow-lg'
                                                     }">

                                                <!-- Top Portrait Showcase Area (Large Image with Arch Background) -->
                                                <div class="relative w-full h-[280px] sm:h-[310px] bg-gradient-to-b from-[#FAF8F5] via-[#F6EFE2]/75 to-[#E6D5B8]/50 pt-5 px-4 flex items-end justify-center overflow-hidden">
                                                    <!-- Decorative Luxury Studio Arch Behind Specialist -->
                                                    <div class="specialist-arch-bg absolute inset-x-5 top-6 bottom-0 rounded-t-[140px] bg-white/80 border border-primary/20 shadow-inner pointer-events-none"></div>

                                                    <!-- Experience Badge (Top Right) -->
                                                    ${sp.experience ? `
                                                        <div class="absolute top-3.5 right-3.5 z-20">
                                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/95 backdrop-blur-md border border-primary/35 text-primary-dark text-[11px] font-extrabold shadow-xs">
                                                                <i class="fa-solid fa-medal text-primary text-[10px]"></i>
                                                                <span>${sp.experience}</span>
                                                            </span>
                                                        </div>
                                                    ` : ''}

                                                    <!-- Selection Status Badge (Top Left) -->
                                                    <div class="absolute top-3.5 left-3.5 z-20">
                                                        ${isSelected ? `
                                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary text-white text-[11px] font-black shadow-md">
                                                                <i class="fa-solid fa-check text-[11px]"></i>
                                                              
                                                            </span>
                                                        ` : `
                                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/95 backdrop-blur-md border border-primary-soft/80 text-gray-600 text-[11px] font-bold shadow-2xs group-hover:border-primary group-hover:text-primary transition-colors">
                                                                <i class="fa-regular fa-circle text-[10px]"></i>
                                                                <span>اختيار</span>
                                                            </span>
                                                        `}
                                                    </div>

                                                    <!-- Large Specialist Portrait Image or Initials Badge -->
                                                    ${sp.has_custom_avatar && sp.avatar ? `
                                                        <img src="${sp.avatar}" 
                                                             alt="${sp.name}"
                                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(nameAr)}&background=FFFFFF&color=C29E6D&bold=true&size=512';"
                                                             class="specialist-portrait-img relative z-10 w-full h-full object-contain object-bottom ${isSelected ? 'scale-[1.04]' : ''}" />
                                                    ` : `
                                                        <div class="relative z-10 my-auto pb-4 flex flex-col items-center justify-center">
                                                            <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full bg-white border-4 border-primary/30 shadow-xl flex items-center justify-center specialist-portrait-img transition-transform duration-500">
                                                                <span class="text-4xl sm:text-5xl font-black font-serif text-primary tracking-widest select-none">
                                                                    ${sp.initials || 'HL'}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    `}

                                                    <!-- Bottom Primary Accent Line -->
                                                    <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-primary-light via-primary to-primary-light ${isSelected ? 'opacity-100' : 'opacity-70 group-hover:opacity-100'} transition-opacity"></div>
                                                </div>

                                                <!-- Card Body: Name, Role, Bio & Selection State -->
                                                <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between bg-white space-y-3.5 text-right">
                                                    <div class="space-y-2.5">
                                                        <!-- Specialist Name (AR + EN) -->
                                                        <div class="flex items-baseline justify-between gap-2">
                                                            <h4 class="text-lg sm:text-xl font-black ${isSelected ? 'text-primary-dark' : 'text-sage-dark group-hover:text-primary-dark'} transition-colors leading-tight">
                                                                ${nameAr}
                                                            </h4>
                                                            ${nameEn ? `
                                                                <span class="text-xs sm:text-sm font-serif font-bold tracking-wider text-primary-dark uppercase shrink-0">
                                                                    ${nameEn}
                                                                </span>
                                                            ` : ''}
                                                        </div>

                                                        <!-- Role / Specialty -->
                                                        <p class="text-xs sm:text-[13px] font-extrabold text-primary-dark leading-relaxed">
                                                            ${sp.role || ''}
                                                        </p>

                                                        <!-- Subtle Divider -->
                                                        <div class="h-px w-full bg-gradient-to-l from-primary-soft/70 via-primary-soft/30 to-transparent"></div>

                                                        <!-- Bio Description -->
                                                        ${sp.bio ? `
                                                            <p class="text-xs text-gray-600 leading-relaxed line-clamp-3">
                                                                ${sp.bio}
                                                            </p>
                                                        ` : ''}
                                                    </div>

                                                    ${linkedSpecialists.length > 1 ? `
                                                        <div class="pt-1">
                                                            <div class="w-full py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 ${
                                                                isSelected 
                                                                ? 'bg-primary text-white shadow-xs' 
                                                                : 'bg-primary-light/50 text-primary-dark group-hover:bg-primary group-hover:text-white'
                                                            }">
                                                                <i class="fa-solid ${isSelected ? 'fa-circle-check' : 'fa-hand-pointer'} text-[11px]"></i>
                                                                <span>${isSelected ? 'الأخصائية المحددة للحجز' : 'اختيار هذه الأخصائية'}</span>
                                                            </div>
                                                        </div>
                                                    ` : ''}
                                                </div>
                                            </article>
                                        `;
                                    }).join('')}
                                </div>
                            </div>

                            <!-- Date & Shift-Based Time Picker -->
                            <div class="space-y-3 pt-3 border-t border-gray-200/70">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="block text-xs sm:text-sm font-extrabold text-sage-dark">
                                        الوقت واليوم المتاح مع ${bookingState.selectedSpecialist.name}:
                                    </label>
                                    ${isLoadingSlots ? `
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-gold-light/50 text-gold-dark text-[10px] font-bold border border-gold-soft/50 animate-pulse">
                                            <i class="fa-solid fa-spinner fa-spin text-[10px]"></i>
                                            <span>جاري تحديث الأوقات...</span>
                                        </span>
                                    ` : (shiftLabel ? `
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-sage-soft text-sage-dark text-[10px] font-bold border border-gold-soft/50">
                                            <i class="fa-regular fa-calendar-check text-gold-dark"></i>
                                            <span>${shiftLabel}</span>
                                        </span>
                                    ` : '')}
                                </div>
                                
                                <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                                    ${datesList.map(d => {
                                        const label = d.display_label || d;
                                        const iso = d.date_iso || '';
                                        const isActiveDate = bookingState.selectedDate === label || (iso && bookingState.selectedDateIso === iso);
                                        return `
                                            <button type="button"
                                                    onclick="selectBookingDate('${label}', '${iso}')" 
                                                    class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 border transition cursor-pointer ${
                                                        isActiveDate 
                                                        ? 'bg-primary text-white border-primary shadow-xs' 
                                                        : 'bg-surface text-gray-600 border-gray-200 hover:border-primary'
                                                    }">
                                                ${label}
                                            </button>
                                        `;
                                    }).join('')}
                                </div>

                                ${isLoadingSlots ? `
                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 animate-pulse" aria-label="جاري تحميل الأوقات">
                                        ${Array.from({ length: 8 }).map(() => `
                                            <div class="py-2.5 px-2 rounded-xl bg-gray-100 border border-gray-200/60 flex items-center justify-center">
                                                <div class="h-3.5 w-12 bg-gray-200/90 rounded"></div>
                                            </div>
                                        `).join('')}
                                    </div>
                                ` : (isDayOff ? `
                                    <div class="p-4 rounded-2xl bg-red-50/70 border border-red-200 text-center space-y-1">
                                        <p class="text-xs font-extrabold text-red-700">الأخصائية في يوم إجازة في هذا التاريخ</p>
                                        <p class="text-[11px] text-red-600">يرجى اختيار يوم آخر أو أخصائية أخرى لإتمام الحجز.</p>
                                    </div>
                                ` : (shiftSlots.length === 0 ? `
                                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 text-center">
                                        <p class="text-xs font-bold text-gray-500">لا توجد مواعيد متاحة في هذا التاريخ.</p>
                                    </div>
                                ` : `
                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                        ${shiftSlots.map(slotObj => {
                                            const slotTime = slotObj.time;
                                            const isBooked = Boolean(slotObj.is_booked || !slotObj.is_available);
                                            const isSelectedSlot = !isBooked && bookingState.selectedTime === slotTime;

                                            if (isBooked) {
                                                return `
                                                    <button type="button"
                                                            disabled
                                                            title="هذا الموعد محجوز مسبقاً"
                                                            class="py-2 px-1.5 rounded-xl text-[11px] font-bold border border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed flex flex-col items-center justify-center gap-0.5 opacity-75">
                                                        <span class="line-through">${slotTime}</span>
                                                        <span class="text-[9px] font-extrabold text-red-500 bg-red-50 px-1.5 py-0.2 rounded">محجوز</span>
                                                    </button>
                                                `;
                                            }

                                            return `
                                                <button type="button"
                                                        onclick="selectBookingTime('${slotTime}')" 
                                                        class="py-2.5 rounded-xl text-xs font-bold border transition cursor-pointer ${
                                                            isSelectedSlot 
                                                            ? 'bg-primary text-white border-primary shadow-xs' 
                                                            : 'bg-white text-sage-dark border-gray-200 hover:border-primary'
                                                        }">
                                                    ${slotTime}
                                                </button>
                                            `;
                                        }).join('')}
                                    </div>
                                `))}
                            </div>
                        </div>
                    `;
                }

                const canConfirm = !isLoadingSlots && Boolean(bookingState.selectedTime) && !isDayOff;
                if (step2Footer) {
                    step2Footer.innerHTML = `
                        <button onclick="confirmFinalBooking()" 
                                ${canConfirm ? '' : 'disabled'}
                                class="w-full py-4 rounded-2xl ${canConfirm ? 'bg-primary hover:bg-primary-dark shadow-lg shadow-primary/25 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'} text-white font-extrabold text-sm transition flex items-center justify-center gap-2">
                            ${isLoadingSlots ? `
                                <i class="fa-solid fa-spinner fa-spin text-white"></i>
                                <span>جاري جلب الأوقات المتاحة...</span>
                            ` : `
                                <i class="fa-solid fa-circle-check text-white"></i>
                                <span>${canConfirm ? `تأكيد الحجز مع ${bookingState.selectedSpecialist.name}` : (isDayOff ? 'يرجى اختيار أخصائية أو يوم آخر' : 'يرجى اختيار وقت متاح للحجز')}</span>
                            `}
                        </button>
                    `;
                }
            }

            // STEP 3: شاشة التأكيد النهائية (بعد التحقق من الجوال أو التسجيل عبر Google وحفظ الحجز في الداتابيز)
            else if (bookingState.step === 3 || bookingState.step === 4) {
                const lastBooking = confirmedBookings[0];
                const customerName = bookingState.confirmedCustomerName || lastBooking?.customerName || currentCustomer?.name || "";
                if (pageTitle) pageTitle.textContent = "تم تأكيد حجزك بنجاح";
                if (pageSubtitle) pageSubtitle.textContent = "رقم الحجز: " + (lastBooking?.id || '');
                if (step3Container) {
                    step3Container.classList.remove('hidden');
                    step3Container.innerHTML = `
                        <div class="bg-white rounded-3xl p-6 shadow-card border border-gold-soft/50 text-center space-y-4 animate-step max-w-xl mx-auto">
                            <div class="w-16 h-16 rounded-full bg-sage-light border-2 border-primary mx-auto flex items-center justify-center text-primary text-2xl shadow-sm">
                                <i class="fa-solid fa-check"></i>
                            </div>

                            <div>
                                <h3 class="text-xl font-black text-sage-dark">تم تأكيد حجزك بنجاح</h3>
                                <p class="text-sm font-extrabold text-primary-dark mt-1">
                                    باسم: <span class="text-sage-dark">${customerName}</span>
                                </p>
                                <div class="mt-2 inline-block px-4 py-1.5 rounded-full bg-gold-light text-gold-dark text-xs font-black">
                                    رقم الحجز: ${lastBooking.id}
                                </div>
                            </div>

                            <!-- Receipt Card -->
                            <div class="bg-surface rounded-2xl p-4 border border-gold-soft text-right space-y-3">
                                <div class="pb-2.5 border-b border-gray-200/60">
                                    <span class="text-xs text-gray-500 font-bold block mb-1">اسم العميلة:</span>
                                    <span class="text-sm font-extrabold text-sage-dark">${customerName}</span>
                                </div>
                                <div class="pb-2.5 border-b border-gray-200/60">
                                    <span class="text-xs text-gray-500 font-bold block mb-1">الخدمة المختارة:</span>
                                    <span class="text-sm font-extrabold text-sage-dark">${lastBooking.serviceName}</span>
                                </div>
                                <div class="pb-2.5 border-b border-gray-200/60">
                                    <span class="text-xs text-gray-500 font-bold block mb-1">الأخصائية:</span>
                                    <div class="flex items-center gap-2">
                                        ${bookingState.selectedSpecialist?.avatar ? `
                                            <img src="${bookingState.selectedSpecialist.avatar}" 
                                                 alt="${lastBooking.specialistName}" 
                                                 class="w-7 h-7 rounded-full object-cover border border-primary/40 shrink-0 shadow-2xs" 
                                                 onerror="this.classList.add('hidden')" />
                                        ` : ''}
                                        <span class="text-sm font-extrabold text-sage-dark">${lastBooking.specialistName}</span>
                                    </div>
                                </div>
                                <div class="pt-0.5">
                                    <span class="text-xs text-gray-500 font-bold block mb-1">الموعد:</span>
                                    <span class="text-sm font-extrabold text-sage-dark">${lastBooking.date} • الساعة ${lastBooking.time}</span>
                                </div>
                            </div>

                            <div class="flex gap-2.5 pt-2">
                                <button onclick="switchTab('account');" 
                                        class="flex-1 py-3.5 rounded-2xl bg-primary text-white font-extrabold text-xs hover:bg-primary-dark transition cursor-pointer">
                                      حجوزاتي
                                </button>
                                <button onclick="switchTab('home');" 
                                        class="flex-1 py-3.5 rounded-2xl bg-gold-light text-gold-dark font-extrabold text-xs hover:bg-gold hover:text-white transition cursor-pointer">
                                    العودة للرئيسية
                                </button>
                            </div>
                        </div>
                    `;
                }
            }
        }

        function selectCategory(catId) {
            const found = bookableCategories.find(c => c.id === catId);
            if (!found) return;
            bookingState.selectedCategory = found;
            bookingState.step = 1;
            renderBookingStep();
        }

        function selectSubService(subId, catId = null) {
            const cb = document.getElementById(`srv-check-${subId}`);
            if (cb) {
                cb.checked = true;
                handleServiceCheckboxChange(cb, subId, catId);
            } else {
                const { foundCategory, foundSub } = findCategoryAndSubService(subId, catId);
                if (!foundCategory || !foundSub) return;
                bookingState.selectedCategory = foundCategory;
                bookingState.selectedSubService = foundSub;
                syncFloatingConfirmButton();
            }
        }

        function chooseSpecialist(spId) {
            const sp = specialistsData[spId];
            if (!sp) return;
            if (bookingState.selectedSpecialist && bookingState.selectedSpecialist.id === sp.id && !bookingState.availability.loading) {
                return;
            }
            bookingState.selectedSpecialist = sp;
            bookingState.selectedTime = null;
            bookingState.availability = {
                loading: true,
                is_day_off: false,
                shift_label: "",
                slots: []
            };
            fetchSpecialistAvailability();
            renderBookingStep();
        }

        function selectBookingDate(dateStr, dateIso = '') {
            if (bookingState.selectedDate === dateStr && (!dateIso || bookingState.selectedDateIso === dateIso) && !bookingState.availability.loading) {
                return;
            }
            bookingState.selectedDate = dateStr;
            if (dateIso) {
                bookingState.selectedDateIso = dateIso;
            }
            bookingState.selectedTime = null;
            bookingState.availability = {
                loading: true,
                is_day_off: false,
                shift_label: "",
                slots: []
            };
            fetchSpecialistAvailability();
            renderBookingStep();
        }

        function selectBookingTime(timeStr) {
            bookingState.selectedTime = timeStr;
            renderBookingStep();
        }

        /**
         * Queries the backend SpecialistAvailabilityService via AJAX to load shift-based slots
         * and real-time database booking conflicts without duplicating logic in the frontend.
         */
        async function fetchSpecialistAvailability() {
            if (!bookingState.selectedSpecialist) return;

            if (availabilityAbortController) {
                availabilityAbortController.abort();
            }
            availabilityAbortController = new AbortController();
            const currentSignal = availabilityAbortController.signal;

            bookingState.availability.loading = true;
            const requestedSpecialistId = bookingState.selectedSpecialist.id;
            const requestedDate = bookingState.selectedDateIso || bookingState.selectedDate || '';

            const params = new URLSearchParams({
                specialist_id: requestedSpecialistId,
                service_id: bookingState.selectedSubService?.id || '',
                booking_date: requestedDate
            });

            try {
                const response = await fetch("{{ route('booking.availability') }}?" + params.toString(), {
                    headers: { 'Accept': 'application/json' },
                    signal: currentSignal
                });
                const data = await response.json();

                // If user switched specialist or date while fetch was in-flight, discard this response
                if (bookingState.selectedSpecialist?.id !== requestedSpecialistId) {
                    return;
                }

                if (response.ok && data.success) {
                    bookingState.availability = {
                        loading: false,
                        is_day_off: Boolean(data.is_day_off),
                        shift_label: data.shift_label || '',
                        slots: Array.isArray(data.slots) ? data.slots : []
                    };

                    const availableList = bookingState.availability.slots.filter(s => s.is_available && !s.is_booked);
                    const currentStillAvailable = availableList.some(s => s.time === bookingState.selectedTime);

                    if (!currentStillAvailable) {
                        bookingState.selectedTime = availableList.length > 0 ? availableList[0].time : null;
                    }

                    if (bookingState.step === 2) {
                        renderBookingStep();
                    }
                } else {
                    bookingState.availability.loading = false;
                    if (bookingState.step === 2) {
                        renderBookingStep();
                    }
                }
            } catch (err) {
                if (err.name === 'AbortError') {
                    return; // Request intentionally aborted due to a newer selection
                }
                bookingState.availability.loading = false;
                if (bookingState.step === 2) {
                    renderBookingStep();
                }
            }
        }

        /**
         * Opens the Phone Verification Popup instead of confirming the booking directly.
         */
        function confirmFinalBooking() {
            if (!bookingState.selectedCategory || !bookingState.selectedSubService || !bookingState.selectedSpecialist || !bookingState.selectedTime) return;
            openPhoneVerificationModal();
        }

        function normalizeDigitsJs(str) {
            const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            const western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            let out = String(str || '').trim();
            for (let i = 0; i < eastern.length; i++) {
                out = out.split(eastern[i]).join(western[i]);
            }
            return out.replace(/[\s\-\(\)]+/g, '');
        }

        function openPhoneVerificationModal() {
            const modal = document.getElementById('bookingVerificationModal');
            const card = document.getElementById('bookingVerificationCard');
            const summaryEl = document.getElementById('popupBookingSummary');
            const phoneInput = document.getElementById('bookingPhoneInput');
            const errorEl = document.getElementById('phoneVerifyError');
            const loggedInNotice = document.getElementById('loggedInCustomerNotice');
            const guestContainer = document.getElementById('guestPhoneInputContainer');
            const loggedInName = document.getElementById('loggedInCustomerNameDisplay');
            const loggedInPhone = document.getElementById('loggedInPhoneDisplay');
            const loggedInAvatar = document.getElementById('loggedInCustomerAvatarDisplay');
            const loggedInAvatarFallback = document.getElementById('loggedInCustomerAvatarFallback');
            const btnText = document.getElementById('phoneVerifySubmitBtnText');
            const btnIcon = document.getElementById('phoneVerifySubmitBtnIcon');
            const nameContainer = document.getElementById('guestNameInputContainer');
            const returningGreeting = document.getElementById('returningCustomerGreeting');

            if (!modal || !card) return;

            bookingState.phoneChecked = false;
            if (nameContainer) nameContainer.classList.add('hidden');
            if (returningGreeting) returningGreeting.classList.add('hidden');

            const timeSlot = bookingState.selectedTime || bookingState.selectedSpecialist?.availableSlots?.[0];
            const subServices = (Array.isArray(bookingState.selectedSubServices) && bookingState.selectedSubServices.length > 0)
                ? bookingState.selectedSubServices
                : (bookingState.selectedSubService ? [bookingState.selectedSubService] : []);
            const servicesTitle = subServices.map(s => s.name).join(' + ');

            if (summaryEl) {
                summaryEl.innerHTML = `
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full bg-primary shrink-0"></span>
                            <span class="font-extrabold text-sm text-sage-dark truncate">${servicesTitle}</span>
                        </div>
                    </div>
                    <div class="border-t border-dashed border-gray-200/80"></div>
                    <div class="grid grid-cols-2 gap-2 text-[11px] text-gray-500 font-medium">
                        <div class="flex items-center gap-2 min-w-0">
                            ${bookingState.selectedSpecialist?.avatar ? `
                                <img src="${bookingState.selectedSpecialist.avatar}"
                                     alt="${bookingState.selectedSpecialist.name}"
                                     onerror="this.onerror=null; this.classList.add('hidden'); if(this.nextElementSibling) this.nextElementSibling.classList.remove('hidden');"
                                     class="w-6 h-6 rounded-full object-cover shrink-0 border border-primary/40 shadow-2xs" />
                                <i class="fa-regular fa-user text-primary-dark text-xs shrink-0 hidden"></i>
                            ` : `
                                <i class="fa-regular fa-user text-primary-dark text-xs shrink-0"></i>
                            `}
                            <span class="truncate font-bold text-sage-dark">${bookingState.selectedSpecialist.name}</span>
                        </div>
                        <div class="flex items-center gap-1.5 justify-end text-left min-w-0">
                            <i class="fa-regular fa-calendar-check text-gold-dark text-[11px] shrink-0"></i>
                            <span class="truncate font-semibold text-sage-dark">${bookingState.selectedDate} • ${timeSlot}</span>
                        </div>
                    </div>
                `;
            }

            // Check if customer is already logged in
            if (currentCustomer && currentCustomer.id) {
                if (loggedInNotice) loggedInNotice.classList.remove('hidden');
                if (guestContainer) guestContainer.classList.add('hidden');
                if (loggedInName) loggedInName.textContent = currentCustomer.name || 'عميلتنا العزيزة';
                if (loggedInPhone) loggedInPhone.value = currentCustomer.phone || '';

                const customerAvatarSrc = currentCustomer.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(currentCustomer.name || 'HL')}&background=FFFFFF&color=C29E6D&bold=true`;
                if (loggedInAvatar) {
                    loggedInAvatar.src = customerAvatarSrc;
                    loggedInAvatar.alt = currentCustomer.name || 'صورة الحساب';
                    loggedInAvatar.classList.remove('hidden');
                }
                if (loggedInAvatarFallback) {
                    loggedInAvatarFallback.classList.add('hidden');
                }

                if (btnText) btnText.textContent = 'تأكيد الحجز';
                if (btnIcon) btnIcon.className = 'fa-solid fa-circle-check text-white';
            } else {
                if (loggedInNotice) loggedInNotice.classList.add('hidden');
                if (guestContainer) guestContainer.classList.remove('hidden');
                if (loggedInAvatar) {
                    loggedInAvatar.src = '';
                    loggedInAvatar.classList.add('hidden');
                }
                if (loggedInAvatarFallback) {
                    loggedInAvatarFallback.classList.remove('hidden');
                }
                if (btnText) btnText.textContent = 'تأكيد الحجز';
                if (btnIcon) btnIcon.className = 'fa-solid fa-circle-check text-white';
            }

            switchPopupToPhoneStep();
            if (errorEl) errorEl.classList.add('hidden');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
                if (phoneInput && (!currentCustomer || !currentCustomer.id)) phoneInput.focus();
            });
        }

        function closePhoneVerificationModal() {
            const modal = document.getElementById('bookingVerificationModal');
            const card = document.getElementById('bookingVerificationCard');
            if (!modal || !card) return;

            modal.classList.add('opacity-0');
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 250);
        }

        function switchPopupToPhoneStep() {
            const phoneStep = document.getElementById('phoneVerifyStep');
            const googleStep = document.getElementById('googleRegisterStep');
            if (phoneStep) phoneStep.classList.remove('hidden');
            if (googleStep) googleStep.classList.add('hidden');
        }

        function switchPopupToGoogleStep(phone) {
            const phoneStep = document.getElementById('phoneVerifyStep');
            const googleStep = document.getElementById('googleRegisterStep');
            const badge = document.getElementById('googleStepPhoneBadge');
            const errorEl = document.getElementById('googleRegisterError');

            if (badge) badge.textContent = phone;
            if (errorEl) errorEl.classList.add('hidden');
            if (phoneStep) phoneStep.classList.add('hidden');
            if (googleStep) googleStep.classList.remove('hidden');
        }

        function handlePhoneInputValidation(inputEl) {
            const errorEl = document.getElementById('phoneVerifyError');
            const val = normalizeDigitsJs(inputEl.value);
            if (val.length === 0 || SAUDI_PHONE_REGEX.test(val)) {
                if (errorEl) errorEl.classList.add('hidden');
                inputEl.classList.remove('border-red-500');
            }
        }

        const googleOAuthClientId = @json(config('services.google.client_id'));

        // Listen for OAuth popup callback messages
        window.addEventListener('message', function (event) {
            if (!event.data || event.data.type !== 'HAIRLINE_GOOGLE_AUTH_RESULT') return;
            const payload = event.data.payload;
            const btn = document.getElementById('googleOAuthBtn');
            const profileBtn = document.getElementById('profileGoogleOAuthBtn');
            const btnTitle = document.getElementById('googleOAuthBtnTitle');
            const profileBtnTitle = document.getElementById('profileGoogleOAuthBtnTitle');
            const errorEl = document.getElementById('googleRegisterError');
            const profileErrorEl = document.getElementById('profileGoogleRegisterError');
            const errorText = document.getElementById('googleRegisterErrorText');
            const profileErrorText = document.getElementById('profileGoogleRegisterErrorText');

            if (btn) btn.disabled = false;
            if (profileBtn) profileBtn.disabled = false;
            if (btnTitle) btnTitle.textContent = 'المتابعة باستخدام حساب Google';
            if (profileBtnTitle) profileBtnTitle.textContent = 'المتابعة باستخدام حساب Google';

            if (payload && payload.status === 'confirmed') {
                finalizeConfirmedBookingFromServer(payload);
            } else {
                const msg = payload?.message || 'تعذر إتمام التسجيل عبر Google، يرجى المحاولة مرة أخرى.';
                if (errorText) errorText.textContent = msg;
                if (errorEl) errorEl.classList.remove('hidden');
                if (profileErrorText) profileErrorText.textContent = msg;
                if (profileErrorEl) profileErrorEl.classList.remove('hidden');
            }
        });

        async function submitPhoneVerification(event) {
            if (event) event.preventDefault();
            const phoneInput = document.getElementById('bookingPhoneInput');
            const errorEl = document.getElementById('phoneVerifyError');
            const errorText = document.getElementById('phoneVerifyErrorText');
            const submitBtn = document.getElementById('phoneVerifySubmitBtn');
            const btnText = document.getElementById('phoneVerifySubmitBtnText');
            const nameContainer = document.getElementById('guestNameInputContainer');
            const nameInput = document.getElementById('guestNameInput');
            const returningGreeting = document.getElementById('returningCustomerGreeting');
            const returningName = document.getElementById('returningCustomerName');

            const isCustomerLoggedIn = Boolean(currentCustomer && currentCustomer.id && currentCustomer.phone);
            const rawPhone = isCustomerLoggedIn
                ? normalizeDigitsJs(currentCustomer.phone)
                : normalizeDigitsJs(phoneInput ? phoneInput.value : '');

            if (!isCustomerLoggedIn && !SAUDI_PHONE_REGEX.test(rawPhone)) {
                if (errorText) {
                    errorText.textContent = 'يرجى إدخال رقم جوال سعودي صحيح يبدأ بـ 05 ويتكون من 10 أرقام (مثال: 0551234567).';
                }
                if (errorEl) errorEl.classList.remove('hidden');
                if (phoneInput) phoneInput.classList.add('border-red-500');
                return;
            }

            if (errorEl) errorEl.classList.add('hidden');
            if (phoneInput) phoneInput.classList.remove('border-red-500');

            const timeSlot = bookingState.selectedTime;
            if (!timeSlot) {
                if (errorText) errorText.textContent = 'يرجى اختيار وقت متاح للحجز أولاً.';
                if (errorEl) errorEl.classList.remove('hidden');
                return;
            }
            const bookingDateVal = bookingState.selectedDateIso || bookingState.selectedDate;
            const subServices = (Array.isArray(bookingState.selectedSubServices) && bookingState.selectedSubServices.length > 0)
                ? bookingState.selectedSubServices
                : (bookingState.selectedSubService ? [bookingState.selectedSubService] : []);
            const serviceIds = subServices.map(s => s.id);
            const primaryServiceId = subServices[0]?.id || bookingState.selectedSubService?.id;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

            // CASE 1: Customer already logged in
            if (isCustomerLoggedIn) {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري تأكيد حجزكِ باسم ${currentCustomer.name}...</span>`;
                }
                try {
                    const response = await fetch("{{ route('booking.verify_phone') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            phone: rawPhone,
                            service_ids: serviceIds,
                            service_id: primaryServiceId,
                            specialist_id: bookingState.selectedSpecialist.id,
                            booking_date: bookingDateVal,
                            booking_time: timeSlot
                        })
                    });
                    const data = await response.json();
                    if (!response.ok || data.status === 'validation_error' || data.status === 'slot_unavailable') {
                        if (errorText) errorText.textContent = data.message || 'تأكدي من صحة البيانات المدخلة.';
                        if (errorEl) errorEl.classList.remove('hidden');
                        fetchSpecialistAvailability();
                        return;
                    }
                    if (data.status === 'confirmed') {
                        finalizeConfirmedBookingFromServer(data);
                    }
                } catch (err) {
                    if (errorText) errorText.textContent = 'حدث خطأ أثناء الاتصال بالخادم، يرجى المحاولة مرة أخرى.';
                    if (errorEl) errorEl.classList.remove('hidden');
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                }
                return;
            }

            // CASE 2: Guest booking - Step A: Check if phone exists
            if (!bookingState.phoneChecked) {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري فحص الرقم...</span>`;
                }
                try {
                    const response = await fetch("{{ route('booking.verify_phone') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            phone: rawPhone
                        })
                    });
                    const data = await response.json();
                    bookingState.verifiedPhone = data.phone || rawPhone;
                    bookingState.phoneChecked = true;

                    if (data.status === 'customer_found') {
                        if (returningGreeting) returningGreeting.classList.remove('hidden');
                        if (returningName) returningName.textContent = data.customer_name || 'عميلتنا العزيزة';
                        if (nameContainer) nameContainer.classList.add('hidden');
                        if (btnText) btnText.textContent = `تأكيد الحجز فوراً كـ زائر (${data.customer_name})`;
                    } else {
                        if (nameContainer) nameContainer.classList.remove('hidden');
                        if (returningGreeting) returningGreeting.classList.add('hidden');
                        if (btnText) btnText.textContent = 'المتابعة كـ زائر وتأكيد الحجز';
                        if (nameInput) setTimeout(() => nameInput.focus(), 150);
                    }
                } catch (err) {
                    if (errorText) errorText.textContent = 'حدث خطأ أثناء فحص رقم الجوال.';
                    if (errorEl) errorEl.classList.remove('hidden');
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                }
                return;
            }

            // CASE 2: Guest booking - Step B: Confirm booking as guest!
            const guestName = nameInput ? nameInput.value.trim() : '';
            const isNameRequired = nameContainer && !nameContainer.classList.contains('hidden');
            if (isNameRequired && guestName.length < 2) {
                if (errorText) errorText.textContent = 'يرجى كتابة اسمكِ الكريم لإتمام الحجز كـ زائر.';
                if (errorEl) errorEl.classList.remove('hidden');
                if (nameInput) nameInput.focus();
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري تأكيد حجزكِ كـ زائر...</span>`;
            }

            try {
                const response = await fetch("{{ route('booking.guest_confirm') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        phone: bookingState.verifiedPhone || rawPhone,
                        name: guestName,
                        service_ids: serviceIds,
                        service_id: primaryServiceId,
                        specialist_id: bookingState.selectedSpecialist.id,
                        booking_date: bookingDateVal,
                        booking_time: timeSlot
                    })
                });

                const data = await response.json();

                if (!response.ok || data.status === 'validation_error' || data.status === 'slot_unavailable') {
                    if (errorText) errorText.textContent = data.message || 'تأكدي من صحة البيانات المدخلة.';
                    if (errorEl) errorEl.classList.remove('hidden');
                    fetchSpecialistAvailability();
                    return;
                }

                if (data.status === 'confirmed') {
                    finalizeConfirmedBookingFromServer(data);
                }
            } catch (err) {
                if (errorText) errorText.textContent = 'حدث خطأ أثناء حفظ الحجز، يرجى المحاولة مرة أخرى.';
                if (errorEl) errorEl.classList.remove('hidden');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        }

        function startGoogleSignInProcess() {
            const btn = document.getElementById('googleOAuthBtn');
            const profileBtn = document.getElementById('profileGoogleOAuthBtn');
            const btnTitle = document.getElementById('googleOAuthBtnTitle');
            const profileBtnTitle = document.getElementById('profileGoogleOAuthBtnTitle');
            const errorEl = document.getElementById('googleRegisterError');
            const profileErrorEl = document.getElementById('profileGoogleRegisterError');
            const errorText = document.getElementById('googleRegisterErrorText');
            const profileErrorText = document.getElementById('profileGoogleRegisterErrorText');

            if (errorEl) errorEl.classList.add('hidden');
            if (profileErrorEl) profileErrorEl.classList.add('hidden');
            if (btnTitle) btnTitle.textContent = 'جاري الاتصال بحساب Google...';
            if (profileBtnTitle) profileBtnTitle.textContent = 'جاري الاتصال بحساب Google...';

            const resetBtnState = (errorMsg) => {
                if (btnTitle) btnTitle.textContent = 'المتابعة باستخدام حساب Google';
                if (profileBtnTitle) profileBtnTitle.textContent = 'المتابعة باستخدام حساب Google';
                if (errorMsg) {
                    if (errorText) errorText.textContent = errorMsg;
                    if (errorEl) errorEl.classList.remove('hidden');
                    if (profileErrorText) profileErrorText.textContent = errorMsg;
                    if (profileErrorEl) profileErrorEl.classList.remove('hidden');
                }
            };

            // 1. If Google Identity Services (OAuth2 Code Client) is loaded, launch the official Google Popup
            if (window.google && window.google.accounts && window.google.accounts.oauth2 && googleOAuthClientId) {
                try {
                    const codeClient = window.google.accounts.oauth2.initCodeClient({
                        client_id: googleOAuthClientId,
                        scope: 'openid email profile',
                        ux_mode: 'popup',
                        callback: function (response) {
                            if (response && response.code) {
                                completeGoogleBookingWithOAuth({ code: response.code });
                            } else {
                                resetBtnState('تم إلغاء عملية تسجيل الدخول عبر Google.');
                            }
                        },
                        error_callback: function () {
                            openGoogleOAuthRedirectPopup();
                        }
                    });
                    codeClient.requestCode();
                    return;
                } catch (e) {
                    openGoogleOAuthRedirectPopup();
                    return;
                }
            }

            // 2. Fallback to standard OAuth 2.0 Popup window (/auth/google/redirect -> /auth/google/callback)
            openGoogleOAuthRedirectPopup();
        }

        function openGoogleOAuthRedirectPopup() {
            const timeSlot = bookingState.selectedTime || '';
            const bookingDateVal = bookingState.selectedDateIso || bookingState.selectedDate || '';
            const params = new URLSearchParams({
                phone: bookingState.verifiedPhone || '',
                service_id: bookingState.selectedSubService?.id || '',
                specialist_id: bookingState.selectedSpecialist?.id || '',
                booking_date: bookingDateVal,
                booking_time: timeSlot
            });

            const width = 520;
            const height = 640;
            const left = Math.max(0, (window.screen.width - width) / 2);
            const top = Math.max(0, (window.screen.height - height) / 2);

            const popup = window.open(
                "{{ route('booking.google_redirect') }}?" + params.toString(),
                "HairlineGoogleOAuth",
                `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,status=yes`
            );

            if (!popup) {
                const msg = 'يرجى السماح بالنوافذ المنبثقة (Popups) لإتمام تسجيل الدخول عبر Google.';
                const btnTitle = document.getElementById('googleOAuthBtnTitle');
                const profileBtnTitle = document.getElementById('profileGoogleOAuthBtnTitle');
                const errorEl = document.getElementById('googleRegisterError');
                const profileErrorEl = document.getElementById('profileGoogleRegisterError');
                const errorText = document.getElementById('googleRegisterErrorText');
                const profileErrorText = document.getElementById('profileGoogleRegisterErrorText');
                if (btnTitle) btnTitle.textContent = 'المتابعة باستخدام حساب Google';
                if (profileBtnTitle) profileBtnTitle.textContent = 'المتابعة باستخدام حساب Google';
                if (errorText) errorText.textContent = msg;
                if (errorEl) errorEl.classList.remove('hidden');
                if (profileErrorText) profileErrorText.textContent = msg;
                if (profileErrorEl) profileErrorEl.classList.remove('hidden');
            }
        }

        async function completeGoogleBookingWithOAuth(oauthPayload) {
            const btn = document.getElementById('googleOAuthBtn');
            const profileBtn = document.getElementById('profileGoogleOAuthBtn');
            const btnTitle = document.getElementById('googleOAuthBtnTitle');
            const profileBtnTitle = document.getElementById('profileGoogleOAuthBtnTitle');
            const errorEl = document.getElementById('googleRegisterError');
            const profileErrorEl = document.getElementById('profileGoogleRegisterError');
            const errorText = document.getElementById('googleRegisterErrorText');
            const profileErrorText = document.getElementById('profileGoogleRegisterErrorText');

            if (errorEl) errorEl.classList.add('hidden');
            if (profileErrorEl) profileErrorEl.classList.add('hidden');
            if (btn) btn.disabled = true;
            if (profileBtn) profileBtn.disabled = true;
            if (btnTitle) btnTitle.textContent = 'جاري التحقق...';
            if (profileBtnTitle) profileBtnTitle.textContent = 'جاري التحقق...';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const timeSlot = bookingState.selectedTime || '';
                const bookingDateVal = bookingState.selectedDateIso || bookingState.selectedDate || '';

                const response = await fetch("{{ route('booking.google_register') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(Object.assign({
                        phone: bookingState.verifiedPhone || '',
                        service_ids: (bookingState.selectedSubServices || []).map(s => s.id),
                        service_id: bookingState.selectedSubService?.id || (bookingState.selectedSubServices?.[0]?.id) || '',
                        specialist_id: bookingState.selectedSpecialist?.id || '',
                        booking_date: bookingDateVal,
                        booking_time: timeSlot
                    }, oauthPayload))
                });

                const data = await response.json();

                if (!response.ok || data.status === 'validation_error' || data.status === 'slot_unavailable') {
                    const msg = data.message || 'تعذر التحقق من حساب Google.';
                    if (errorText) errorText.textContent = msg;
                    if (errorEl) errorEl.classList.remove('hidden');
                    if (profileErrorText) profileErrorText.textContent = msg;
                    if (profileErrorEl) profileErrorEl.classList.remove('hidden');
                    return;
                }

                if (data.status === 'confirmed') {
                    finalizeConfirmedBookingFromServer(data);
                }
            } catch (err) {
                const msg = 'حدث خطأ أثناء الاتصال بالخادم، يرجى المحاولة مرة أخرى.';
                if (errorText) errorText.textContent = msg;
                if (errorEl) errorEl.classList.remove('hidden');
                if (profileErrorText) profileErrorText.textContent = msg;
                if (profileErrorEl) profileErrorEl.classList.remove('hidden');
            } finally {
                if (btn) btn.disabled = false;
                if (profileBtn) profileBtn.disabled = false;
                if (btnTitle) btnTitle.textContent = 'المتابعة باستخدام حساب Google';
                if (profileBtnTitle) profileBtnTitle.textContent = 'المتابعة باستخدام حساب Google';
            }
        }

        function finalizeConfirmedBookingFromServer(data) {
            closePhoneVerificationModal();

            if (data.customer) {
                currentCustomer = data.customer;
            }

            const customerName = data.customer?.name || currentCustomer?.name || '';
            bookingState.confirmedCustomerName = customerName;
            bookingState.confirmedCustomerEmail = data.customer?.email || '';

            if (Array.isArray(data.customer_bookings)) {
                confirmedBookings = data.customer_bookings;
            } else if (data.booking) {
                confirmedBookings.unshift(data.booking);
            }

            renderCustomerProfile();
            renderMyBookings();
            updateBookingBadge();

            if (data.booking) {
                bookingState.step = 3;
                renderBookingStep();
                openToast( 'تم تأكيد الحجز بنجاح');
            } else {
                openToast(`مرحباً بكِ ${customerName}، تم تسجيل دخولكِ بنجاح ✨`);
            }
        }

        function updateBookingBadge() {
            const badge = document.getElementById('bookingCountBadge');
            if (badge) {
                badge.textContent = confirmedBookings.length;
                badge.classList.toggle('hidden', confirmedBookings.length === 0);
            }
            const desktopBadge = document.getElementById('desktopBookingCountBadge');
            if (desktopBadge) {
                desktopBadge.textContent = confirmedBookings.length;
                desktopBadge.classList.toggle('hidden', confirmedBookings.length === 0);
            }
        }

        function toggleDesktopBooking() {
            openBookingFlow();
        }

        function renderCustomerProfile() {
            const container = document.getElementById('customerProfileCardContainer');
            const bookingsCard = document.getElementById('myBookingsSectionCard');
            if (!container) return;

            if (!currentCustomer) {
                if (bookingsCard) bookingsCard.classList.add('hidden');
                container.innerHTML = `
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-card border border-gold-soft/40 text-center space-y-6">
                        <div class="flex flex-col items-center space-y-2">
                            <h3 class="text-lg sm:text-xl font-black text-sage-dark">لا يوجد لديكِ ملف شخصي حالياً</h3>
                            <p class="text-xs sm:text-sm text-gray-500 leading-relaxed max-w-md mx-auto">
                                لم يتم تسجيل الدخول بعد. يمكنكِ التسجيل السريع والمتابعة عبر حساب Google للوصول إلى بياناتكِ ومتابعة حجوزاتكِ بكل سهولة
                            </p>
                        </div>

                        <!-- Luxury Google OAuth Button (Same style and function as booking confirmation) -->
                        <div class="max-w-md mx-auto space-y-3">
                            <button type="button"
                                    id="profileGoogleOAuthBtn"
                                    onclick="startGoogleSignInProcess()"
                                    class="w-full p-4 rounded-2xl bg-primary hover:bg-primary-dark text-white shadow-xl shadow-primary/25 hover:shadow-2xl border-2 border-white/80 hover:border-white active:scale-[0.985] transition-all duration-300 flex items-center justify-between gap-3 group cursor-pointer">
                                <div class="flex items-center gap-3.5 text-right">
                                    <div class="w-11 h-11 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        <svg class="w-6 h-6" viewBox="0 0 48 48" aria-hidden="true">
                                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span id="profileGoogleOAuthBtnTitle" class="block font-extrabold text-sm sm:text-base text-white">
                                            المتابعة باستخدام حساب Google
                                        </span>
                                        <span class="block text-[11px] text-white/90 font-medium mt-0.5">
                                            تسجيل بضغطة واحدة بحساب Google
                                        </span>
                                    </div>
                                </div>

                                <div class="w-8 h-8 rounded-full bg-white/20 border border-white/40 flex items-center justify-center text-white group-hover:bg-white group-hover:text-primary-dark transition-all shrink-0">
                                    <span class="text-base font-bold leading-none -mt-0.5">‹</span>
                                </div>
                            </button>

                            <p id="profileGoogleRegisterError" class="hidden text-[11px] font-bold text-red-600 text-center flex items-center justify-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span id="profileGoogleRegisterErrorText">تعذر إتمام التسجيل عبر Google، يرجى المحاولة مرة أخرى.</span>
                            </p>
                        </div>
                    </div>
                `;
                return;
            }

            if (bookingsCard) bookingsCard.classList.remove('hidden');

            const avatarSrc = currentCustomer.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(currentCustomer.name)}&background=211C18&color=C5A373&bold=true`;

            container.innerHTML = `
                <div class="bg-white rounded-3xl p-5 shadow-card border border-gold-soft/40 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <img src="${avatarSrc}"
                             alt="${currentCustomer.name}"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(currentCustomer.name)}&background=211C18&color=C5A373&bold=true';"
                             class="w-15 h-15 rounded-full object-cover border-2 border-gold shadow-sm shrink-0"
                             style="width: 60px; height: 60px;" />
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-base sm:text-lg text-sage-dark truncate">${currentCustomer.name}</h3>
                            <p class="text-xs text-gray-500 font-semibold mt-0.5 flex items-center gap-1.5" dir="ltr">
                                <i class="fa-solid fa-phone text-[10px] text-gold-dark"></i>
                                <span>${currentCustomer.phone || ''}</span>
                            </p>
                        </div>
                    </div>

                    <button type="button"
                            onclick="openEditProfileModal()"
                            class="px-4 py-2.5 rounded-2xl bg-surface hover:bg-primary text-sage-dark hover:text-white border border-gold-soft/80 hover:border-primary text-xs font-extrabold transition-all duration-200 flex items-center gap-2 shadow-2xs cursor-pointer shrink-0">
                        <i class="fa-regular fa-pen-to-square text-gold-dark"></i>
                        <span>تعديل الملف</span>
                    </button>
                </div>
            `;
        }

        function openEditProfileModal() {
            if (!currentCustomer) return;

            const modal = document.getElementById('editProfileModal');
            const card = document.getElementById('editProfileCard');
            const nameInput = document.getElementById('editProfileNameInput');
            const phoneInput = document.getElementById('editProfilePhoneInput');
            const avatarPreview = document.getElementById('editProfileAvatarPreview');
            const avatarInput = document.getElementById('editProfileAvatarInput');
            const errorEl = document.getElementById('editProfileError');

            if (!modal || !card) return;

            if (nameInput) nameInput.value = currentCustomer.name || '';
            if (phoneInput) phoneInput.value = currentCustomer.phone || '';
            if (avatarInput) avatarInput.value = '';
            if (avatarPreview) {
                avatarPreview.src = currentCustomer.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(currentCustomer.name)}&background=211C18&color=C5A373&bold=true`;
            }
            if (errorEl) errorEl.classList.add('hidden');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('scale-90', 'translate-y-4');
                card.classList.add('scale-100', 'translate-y-0');
                if (nameInput) nameInput.focus();
            });
        }

        function closeEditProfileModal() {
            const modal = document.getElementById('editProfileModal');
            const card = document.getElementById('editProfileCard');
            if (!modal || !card) return;

            modal.classList.add('opacity-0');
            card.classList.remove('scale-100', 'translate-y-0');
            card.classList.add('scale-90', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 280);
        }

        function previewProfileAvatar(inputEl) {
            if (!inputEl.files || !inputEl.files[0]) return;
            const file = inputEl.files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById('editProfileAvatarPreview');
                if (preview && e.target?.result) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }

        async function submitEditProfile(event) {
            if (event) event.preventDefault();
            if (!currentCustomer) return;

            const nameInput = document.getElementById('editProfileNameInput');
            const phoneInput = document.getElementById('editProfilePhoneInput');
            const avatarInput = document.getElementById('editProfileAvatarInput');
            const errorEl = document.getElementById('editProfileError');
            const errorText = document.getElementById('editProfileErrorText');
            const submitBtn = document.getElementById('editProfileSubmitBtn');

            const newName = (nameInput ? nameInput.value : '').trim();
            const rawPhone = normalizeDigitsJs(phoneInput ? phoneInput.value : '');

            if (newName.length < 2) {
                if (errorText) errorText.textContent = 'يرجى إدخال الاسم الكامل (حرفين على الأقل).';
                if (errorEl) errorEl.classList.remove('hidden');
                return;
            }

            if (!SAUDI_PHONE_REGEX.test(rawPhone)) {
                if (errorText) errorText.textContent = 'يرجى إدخال رقم جوال سعودي صحيح (مثال: 0551234567).';
                if (errorEl) errorEl.classList.remove('hidden');
                return;
            }

            if (errorEl) errorEl.classList.add('hidden');

            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري الحفظ...</span>`;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const formData = new FormData();
                formData.append('customer_id', String(currentCustomer.id));
                formData.append('name', newName);
                formData.append('phone', rawPhone);
                if (avatarInput && avatarInput.files && avatarInput.files[0]) {
                    formData.append('avatar', avatarInput.files[0]);
                }

                const response = await fetch("{{ route('profile.update') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    if (errorText) errorText.textContent = data.message || 'تعذر حفظ التعديلات، يرجى المحاولة مرة أخرى.';
                    if (errorEl) errorEl.classList.remove('hidden');
                    return;
                }

                currentCustomer = data.customer;
                if (Array.isArray(data.customer_bookings)) {
                    confirmedBookings = data.customer_bookings;
                }

                renderCustomerProfile();
                renderMyBookings();
                closeEditProfileModal();
                openToast('تم تحديث ملفكِ الشخصي بنجاح ✨');
            } catch (err) {
                if (errorText) errorText.textContent = 'حدث خطأ أثناء الاتصال بالخادم، يرجى المحاولة مرة أخرى.';
                if (errorEl) errorEl.classList.remove('hidden');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }
        }

        async function handleCustomerLogout() {
            const logoutBtn = document.getElementById('editProfileLogoutBtn');
            const originalHtml = logoutBtn ? logoutBtn.innerHTML : '';
            if (logoutBtn) {
                logoutBtn.disabled = true;
                logoutBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i><span>جاري تسجيل الخروج...</span>`;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                await fetch("{{ route('profile.logout') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            } catch (err) {
                // Ignore network errors on logout to ensure local logout always succeeds
            } finally {
                currentCustomer = null;
                confirmedBookings = [];
                bookingState.verifiedPhone = "";
                bookingState.confirmedCustomerName = "";
                bookingState.confirmedCustomerEmail = "";

                closeEditProfileModal();
                renderCustomerProfile();
                renderMyBookings();
                updateBookingBadge();

                if (logoutBtn) {
                    logoutBtn.disabled = false;
                    logoutBtn.innerHTML = originalHtml;
                }

                openToast('تم تسجيل الخروج بنجاح ✨');
            }
        }

        function renderMyBookings() {
            const container = document.getElementById('myBookingsContainer');
            if (!container) return;

            if (confirmedBookings.length === 0) {
                container.innerHTML = `<p class="text-xs text-gray-400 text-center py-4">لا توجد حجوزات حالية.</p>`;
                return;
            }

            container.innerHTML = confirmedBookings.map(b => `
                <div class="p-4 rounded-2xl bg-surface border border-gold-soft/60 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full bg-sage-light text-sage-dark text-[10px] font-extrabold">
                            مؤكد • #${b.id}${b.customerName ? ` • ${b.customerName}` : ''}
                        </span>
                        <span class="text-xs font-black text-gold-dark">${b.price}</span>
                    </div>
                    <h5 class="font-extrabold text-sm text-sage-dark">${b.serviceName}</h5>
                    <div class="flex flex-wrap items-center justify-between text-xs text-gray-600 pt-1 border-t border-gray-200/60">
                        <span><i class="fa-regular fa-user text-gold-dark ml-1"></i>${b.specialistName}</span>
                        <span><i class="fa-regular fa-clock text-gold-dark ml-1"></i>${b.date} (${b.time})</span>
                    </div>
                </div>
            `).join('');
        }

        function showBottomSheet() {
            const overlay = document.getElementById('sheetOverlay');
            const sheet = document.getElementById('bottomSheet');
            overlay.classList.remove('hidden');
            requestAnimationFrame(() => {
                overlay.classList.remove('opacity-0');
                sheet.classList.remove('translate-y-full');
                sheet.classList.remove('md:scale-75', 'md:opacity-0', 'md:pointer-events-none');
                sheet.classList.add('md:scale-100', 'md:opacity-100', 'md:pointer-events-auto');
            });
        }

        function closeBottomSheet() {
            const overlay = document.getElementById('sheetOverlay');
            const sheet = document.getElementById('bottomSheet');
            overlay.classList.add('opacity-0');
            sheet.classList.add('translate-y-full');
            sheet.classList.remove('md:scale-100', 'md:opacity-100', 'md:pointer-events-auto');
            sheet.classList.add('md:scale-75', 'md:opacity-0', 'md:pointer-events-none');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 350);
        }

        function openToast(msg) {
            const toast = document.getElementById('toastBanner');
            const text = document.getElementById('toastText');
            text.textContent = msg;
            toast.classList.remove('hidden');
            toast.classList.add('flex');
            setTimeout(() => {
                hideToast();
            }, 3500);
        }

        function hideToast() {
            const toast = document.getElementById('toastBanner');
            toast.classList.add('hidden');
            toast.classList.remove('flex');
        }

        /* -------------------------------------------------------------------------- */
        /* FULLSCREEN TOGGLE & STATE SYNCHRONIZATION (Cross-Browser + iOS Safari)    */
        /* -------------------------------------------------------------------------- */
        function isStandaloneApp() {
            return window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;
        }

        function isFullscreenActive() {
            return isStandaloneApp() || !!(
                document.fullscreenElement ||
                document.webkitFullscreenElement ||
                document.mozFullScreenElement ||
                document.msFullscreenElement
            );
        }

        function syncFullscreenUI() {
            const btn = document.getElementById('fullscreenBtn');
            const icon = document.getElementById('fullscreenIcon');
            if (!btn || !icon) return;

            const active = isFullscreenActive();
            if (active) {
                icon.className = 'fa-solid fa-compress text-xs sm:text-sm text-gold-dark';
                btn.setAttribute('title', 'إنهاء ملء الشاشة');
                btn.setAttribute('aria-label', 'إنهاء ملء الشاشة');
                btn.classList.add('bg-gold-light', 'text-gold-dark', 'ring-2', 'ring-gold/40');
                btn.classList.remove('bg-white', 'text-sage-dark');
            } else {
                icon.className = 'fa-solid fa-expand text-xs sm:text-sm text-sage-dark';
                btn.setAttribute('title', 'ملء الشاشة');
                btn.setAttribute('aria-label', 'ملء الشاشة');
                btn.classList.remove('bg-gold-light', 'text-gold-dark', 'ring-2', 'ring-gold/40');
                btn.classList.add('bg-white', 'text-sage-dark');
            }
        }

        function toggleFullscreen() {
            try {
                // If running in standalone PWA mode on iOS (Added to Home Screen)
                if (isStandaloneApp()) {
                    openToast('أنتِ تتصفحين الصالون في وضع ملء الشاشة الكامل 📲✨');
                    return;
                }

                // Native Fullscreen API (Android, Windows, Mac, iPadOS, Linux)
                const docEl = document.documentElement;
                const requestMethod = docEl.requestFullscreen ||
                                      docEl.webkitRequestFullscreen ||
                                      docEl.mozRequestFullScreen ||
                                      docEl.msRequestFullscreen;

                const exitMethod = document.exitFullscreen ||
                                   document.webkitExitFullscreen ||
                                   document.mozCancelFullScreen ||
                                   document.msExitFullscreen;

                // If native Fullscreen API is supported by the device browser
                if (requestMethod) {
                    if (!isFullscreenActive()) {
                        const promise = requestMethod.call(docEl);
                        if (promise && typeof promise.then === 'function') {
                            promise.then(() => {
                                syncFullscreenUI();
                                openToast('تم تفعيل وضع ملء الشاشة');
                            }).catch(() => {
                                handleIosOrFallbackFullscreen();
                            });
                        } else {
                            syncFullscreenUI();
                            openToast('تم تفعيل وضع ملء الشاشة');
                        }
                    } else {
                        if (exitMethod) {
                            const promise = exitMethod.call(document);
                            if (promise && typeof promise.then === 'function') {
                                promise.then(() => {
                                    syncFullscreenUI();
                                    openToast('تم الخروج من وضع ملء الشاشة');
                                }).catch(() => {
                                    handleIosOrFallbackFullscreen();
                                });
                            } else {
                                syncFullscreenUI();
                                openToast('تم الخروج من وضع ملء الشاشة');
                            }
                        }
                    }
                    return;
                }

                // If device is iOS Safari (iPhone) where Apple blocks requestFullscreen API
                handleIosOrFallbackFullscreen();
            } catch (err) {
                console.warn('Fullscreen not supported or blocked, opening iOS guide:', err);
                handleIosOrFallbackFullscreen();
            }
        }

        function handleIosOrFallbackFullscreen() {
            openIosFullscreenModal();
        }

        function openIosFullscreenModal() {
            const modal = document.getElementById('iosFullscreenModal');
            if (!modal) return;
            modal.classList.remove('hidden');
            void modal.offsetWidth; // Force layout recalculation for smooth iOS transition
            modal.classList.remove('opacity-0');
        }

        function closeIosFullscreenModal() {
            const modal = document.getElementById('iosFullscreenModal');
            if (!modal) return;
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        /* -------------------------------------------------------------------------- */
        /* UNIFIED SERVICE SEARCH ENGINE (SERVICES CATALOG + BOOKING TREE VIEW)       */
        /* -------------------------------------------------------------------------- */
        const unifiedSearchConfig = {
            services: {
                inputId: 'serviceSearch',
                clearBtnId: 'clearSearchBtn',
                statusId: 'servicesSearchStatus',
                countId: 'servicesSearchCount',
                queryTextId: 'servicesSearchQuery',
                emptyStateId: 'servicesSearchEmptyState',
                emptyQueryId: 'servicesEmptyQueryText'
            },
            booking: {
                inputId: 'bookingServiceSearch',
                clearBtnId: 'clearBookingSearchBtn',
                statusId: 'bookingSearchStatus',
                countId: 'bookingSearchCount',
                queryTextId: 'bookingSearchQuery',
                emptyStateId: 'bookingSearchEmptyState',
                emptyQueryId: 'bookingEmptyQueryText'
            }
        };

        function normalizeSearchText(str) {
            if (!str) return '';
            const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            const western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            let out = String(str).toLowerCase();
            for (let i = 0; i < eastern.length; i++) {
                out = out.split(eastern[i]).join(western[i]);
            }
            return out
                .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
                .replace(/[أإآٱ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/ى/g, 'ي')
                .replace(/ؤ/g, 'و')
                .replace(/ئ/g, 'ي')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function matchesSearchTokens(haystackText, normalizedQuery) {
            if (!normalizedQuery) return true;
            const normalizedHaystack = normalizeSearchText(haystackText);
            const tokens = normalizedQuery.split(' ').filter(Boolean);
            return tokens.every(token => normalizedHaystack.includes(token));
        }

        let serviceSuggestionsAbortController = null;
        let serviceSuggestionsDebounceTimer = null;

        function setServicesSearchLoading(isLoading) {
            const spinner = document.getElementById('servicesSearchSpinner');
            const icon = document.getElementById('servicesSearchIcon');
            if (spinner && icon) {
                spinner.classList.toggle('hidden', !isLoading);
                icon.classList.toggle('hidden', isLoading);
            }
        }

        function hideServicesSuggestions() {
            setServicesSearchLoading(false);
            const dropdown = document.getElementById('servicesSuggestionsDropdown');
            const backdrop = document.getElementById('servicesSuggestionsBackdrop');
            if (dropdown) dropdown.classList.add('hidden');
            if (backdrop) backdrop.classList.add('hidden');
        }

        function handleSearchFocus(context) {
            if (context === 'services') {
                const inputEl = document.getElementById('serviceSearch');
                if (inputEl && inputEl.value.trim().length > 0) {
                    fetchServiceAjaxSuggestions(inputEl.value.trim());
                }
            }
        }

        function handleUnifiedSearchInput(context) {
            const cfg = unifiedSearchConfig[context];
            if (!cfg) return;

            const inputEl = document.getElementById(cfg.inputId);
            const clearBtn = document.getElementById(cfg.clearBtnId);
            if (!inputEl) return;

            const trimmedVal = inputEl.value.trim();
            const hasText = trimmedVal.length > 0;
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', !hasText);
            }

            if (context === 'services') {
                if (hasText) {
                    fetchServiceAjaxSuggestions(trimmedVal);
                } else {
                    hideServicesSuggestions();
                    executeUnifiedServiceSearch(context);
                }
                return;
            }

            // Automatically restore full list when user empties the search input
            if (!hasText) {
                executeUnifiedServiceSearch(context);
            }
        }

        function fetchServiceAjaxSuggestions(query) {
            const dropdown = document.getElementById('servicesSuggestionsDropdown');
            const backdrop = document.getElementById('servicesSuggestionsBackdrop');
            const listEl = document.getElementById('servicesSuggestionsList');
            if (!dropdown || !listEl) return;

            if (serviceSuggestionsDebounceTimer) {
                clearTimeout(serviceSuggestionsDebounceTimer);
            }

            const cleanQuery = (query || '').trim();
            if (cleanQuery.length === 0) {
                hideServicesSuggestions();
                listEl.innerHTML = '';
                return;
            }

            // Show active loader inside search input and dropdown
            setServicesSearchLoading(true);
            listEl.innerHTML = `
                <div class="py-4 px-4 text-center text-xs text-gray-400 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-notch fa-spin text-primary text-sm"></i>
                    <span class="font-medium text-sage-dark">جاري البحث عن الخدمات...</span>
                </div>
            `;
            dropdown.classList.remove('hidden');
            if (backdrop) backdrop.classList.remove('hidden');

            serviceSuggestionsDebounceTimer = setTimeout(() => {
                if (serviceSuggestionsAbortController) {
                    serviceSuggestionsAbortController.abort();
                }
                serviceSuggestionsAbortController = new AbortController();

                fetch(`/services/suggestions?q=${encodeURIComponent(cleanQuery)}&limit=10`, {
                    signal: serviceSuggestionsAbortController.signal,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    setServicesSearchLoading(false);

                    if (!data.success || !Array.isArray(data.suggestions) || data.suggestions.length === 0) {
                        renderNoServiceMatchNotice(cleanQuery);
                        return;
                    }

                    renderServiceSuggestions(data.suggestions, cleanQuery);
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        setServicesSearchLoading(false);
                        renderNoServiceMatchNotice(cleanQuery);
                    }
                });
            }, 100);
        }

        function renderNoServiceMatchNotice(query) {
            const dropdown = document.getElementById('servicesSuggestionsDropdown');
            const backdrop = document.getElementById('servicesSuggestionsBackdrop');
            const listEl = document.getElementById('servicesSuggestionsList');
            if (!dropdown || !listEl) return;

            const safeQuery = (query || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');

            listEl.innerHTML = `
                <div class="py-3 px-4 text-center select-none">
                    <p class="text-xs font-bold text-sage-dark">لا توجد خدمات مطابقة لـ «<span class="text-primary-dark font-extrabold">${safeQuery}</span>»</p>
                </div>
            `;
            dropdown.classList.remove('hidden');
            if (backdrop) backdrop.classList.remove('hidden');
        }

        function renderServiceSuggestions(suggestions, query) {
            const dropdown = document.getElementById('servicesSuggestionsDropdown');
            const backdrop = document.getElementById('servicesSuggestionsBackdrop');
            const listEl = document.getElementById('servicesSuggestionsList');
            if (!dropdown || !listEl) return;

            listEl.innerHTML = suggestions.map((item, index) => {
                const highlightedName = highlightSearchMatch(item.name, query);
                const safeName = (item.name || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
                return `
                    <button type="button"
                            onmousedown="selectServiceSuggestion('${safeName}')"
                            class="service-suggestion-item w-full text-right p-3 rounded-xl hover:bg-[#FAF6F0] focus:bg-[#FAF6F0] border border-transparent hover:border-gold-soft/60 flex items-center justify-between gap-3 transition-all cursor-pointer group outline-none"
                            data-index="${index}">
                        <div class="min-w-0 flex items-center gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary shrink-0 transition-colors"></span>
                            <div class="min-w-0">
                                ${item.category ? `<span class="text-[11px] font-bold text-gold-dark block truncate mb-0.5">${item.category}</span>` : ''}
                                <div class="font-extrabold text-sm text-sage-dark group-hover:text-primary-dark transition-colors truncate">
                                    ${highlightedName}
                                </div>
                                ${item.name_en ? `<span class="text-[10px] font-serif tracking-wider text-gray-400 block uppercase truncate mt-0.5">${item.name_en}</span>` : ''}
                            </div>
                        </div>
                        <div class="shrink-0 flex items-center gap-2 mr-2">
                            <span class="px-3 py-1 rounded-full bg-gold-light/70 text-gold-dark font-extrabold text-xs border border-gold/30 shadow-2xs whitespace-nowrap">
                                ${item.price_display}
                            </span>
                            <i class="fa-solid fa-chevron-left text-[11px] text-gray-300 group-hover:text-primary transition-transform group-hover:-translate-x-1 shrink-0"></i>
                        </div>
                    </button>
                `;
            }).join('');

            dropdown.classList.remove('hidden');
            if (backdrop) backdrop.classList.remove('hidden');
        }

        function highlightSearchMatch(text, query) {
            if (!query) return text;
            const normalizedText = normalizeSearchText(text);
            const normalizedQuery = normalizeSearchText(query);
            const idx = normalizedText.indexOf(normalizedQuery);
            if (idx === -1) return text;

            const before = text.substring(0, idx);
            const match = text.substring(idx, idx + query.length);
            const after = text.substring(idx + query.length);
            return `${before}<mark class="bg-primary/20 text-primary-dark font-black rounded px-1">${match}</mark>${after}`;
        }

        function selectServiceSuggestion(serviceName) {
            hideServicesSuggestions();

            const cfg = unifiedSearchConfig['services'];
            const inputEl = document.getElementById(cfg.inputId);

            if (inputEl) {
                inputEl.value = serviceName;
            }

            // Immediately execute search based on the selected service
            executeUnifiedServiceSearch('services');
        }

        function executeUnifiedServiceSearch(context, event = null) {
            if (event) {
                event.preventDefault();
            }

            if (context === 'services') {
                hideServicesSuggestions();
            }

            const cfg = unifiedSearchConfig[context];
            if (!cfg) return;

            const inputEl = document.getElementById(cfg.inputId);
            const clearBtn = document.getElementById(cfg.clearBtnId);
            if (!inputEl) return;

            const rawQuery = inputEl.value.trim();
            const normalizedQuery = normalizeSearchText(rawQuery);

            if (clearBtn) {
                clearBtn.classList.toggle('hidden', rawQuery.length === 0);
            }

            if (event && document.activeElement === inputEl) {
                inputEl.blur();
            }

            if (context === 'services') {
                filterServicesCatalogView(rawQuery, normalizedQuery);
            } else if (context === 'booking') {
                filterBookingTreeView(rawQuery, normalizedQuery);
            }
        }

        function clearUnifiedServiceSearch(context, focusInput = false) {
            const cfg = unifiedSearchConfig[context];
            if (!cfg) return;

            if (context === 'services') {
                hideServicesSuggestions();
            }

            const inputEl = document.getElementById(cfg.inputId);
            const clearBtn = document.getElementById(cfg.clearBtnId);

            if (inputEl) {
                inputEl.value = '';
                if (focusInput) inputEl.focus();
            }
            if (clearBtn) {
                clearBtn.classList.add('hidden');
            }

            if (context === 'services') {
                filterServicesCatalogView('', '');
            } else if (context === 'booking') {
                filterBookingTreeView('', '');
            }
        }

        function updateSearchFeedbackUI(context, rawQuery, normalizedQuery, matchedCount) {
            const cfg = unifiedSearchConfig[context];
            if (!cfg) return;

            const statusEl = document.getElementById(cfg.statusId);
            const countEl = document.getElementById(cfg.countId);
            const queryTextEl = document.getElementById(cfg.queryTextId);
            const emptyStateEl = document.getElementById(cfg.emptyStateId);
            const emptyQueryEl = document.getElementById(cfg.emptyQueryId);

            if (!normalizedQuery) {
                if (statusEl) {
                    statusEl.classList.add('hidden');
                    statusEl.classList.remove('flex');
                }
                if (emptyStateEl) {
                    emptyStateEl.classList.add('hidden');
                }
                return;
            }

            if (queryTextEl) queryTextEl.textContent = rawQuery;
            if (countEl) countEl.textContent = String(matchedCount);
            if (statusEl) {
                statusEl.classList.remove('hidden');
                statusEl.classList.add('flex');
            }

            if (emptyQueryEl) emptyQueryEl.textContent = rawQuery;
            if (emptyStateEl) {
                emptyStateEl.classList.toggle('hidden', matchedCount > 0);
            }
        }

        function filterServicesCatalogView(rawQuery, normalizedQuery) {
            const pagesContainer = document.getElementById('pagesContainer');
            const resultsContainer = document.getElementById('servicesSearchResultsContainer');
            const resultsList = document.getElementById('servicesSearchResultsList');

            if (!normalizedQuery) {
                if (pagesContainer) pagesContainer.classList.remove('hidden');
                if (resultsContainer) resultsContainer.classList.add('hidden');
                if (resultsList) resultsList.innerHTML = '';
                updateSearchFeedbackUI('services', '', '', 0);
                return;
            }

            const pages = document.querySelectorAll('#servicesTabView .menu-page');
            const matchedItems = [];

            pages.forEach(page => {
                const pageNum = page.getAttribute('data-page');
                const mainTitleEl = page.querySelector(':scope > .page-content > .section-main-title');
                const pageTitleText = mainTitleEl ? mainTitleEl.querySelector('.ar-title')?.textContent?.trim() || mainTitleEl.textContent?.trim() : '';

                // 1. Multi-length service blocks
                page.querySelectorAll('.multi-length-block').forEach(block => {
                    const subGroup = block.closest('.service-subsection-group');
                    const subTitleText = subGroup?.querySelector('.subsection-title')?.textContent?.trim() || '';
                    const blockTitleText = block.querySelector('.multi-length-title')?.textContent?.trim() || '';
                    const categoryLabel = [pageTitleText, subTitleText, blockTitleText].filter(Boolean).join(' • ');

                    block.querySelectorAll('.length-row-item.service-row').forEach(row => {
                        const arName = row.querySelector('.ar-name')?.textContent?.trim() || row.textContent?.trim();
                        const enName = row.querySelector('.en-name')?.textContent?.trim() || '';
                        const price = row.querySelector('.price-badge')?.textContent?.trim() || '';
                        const combinedText = `${categoryLabel} ${arName} ${enName} ${price}`;

                        if (matchesSearchTokens(combinedText, normalizedQuery)) {
                            matchedItems.push({
                                arName,
                                enName,
                                category: categoryLabel,
                                price,
                            });
                        }
                    });
                });

                // 2. Standard service rows
                page.querySelectorAll('.service-row:not(.length-row-item)').forEach(row => {
                    const subGroup = row.closest('.service-subsection-group');
                    const subTitleText = subGroup?.querySelector('.subsection-title')?.textContent?.trim() || '';
                    const catGroup = row.closest('.category-group');
                    const catLabelText = catGroup?.querySelector('.category-side-label')?.textContent?.trim() || '';
                    const categoryLabel = [pageTitleText, catLabelText, subTitleText].filter(Boolean).join(' • ');

                    const arName = row.querySelector('.ar-name')?.textContent?.trim() || row.textContent?.trim();
                    const enName = row.querySelector('.en-name')?.textContent?.trim() || '';
                    const price = row.querySelector('.price-badge')?.textContent?.trim() || '';
                    const combinedText = `${categoryLabel} ${arName} ${enName} ${price}`;

                    if (matchesSearchTokens(combinedText, normalizedQuery)) {
                        matchedItems.push({
                            arName,
                            enName,
                            category: categoryLabel,
                            price,
                        });
                    }
                });
            });

            const totalMatchedServices = matchedItems.length;

            if (totalMatchedServices > 0) {
                if (pagesContainer) pagesContainer.classList.add('hidden');
                if (resultsContainer) resultsContainer.classList.remove('hidden');
                if (resultsList) {
                    resultsList.innerHTML = matchedItems.map(item => `
                        <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-primary-soft/80 shadow-xs flex items-center justify-between gap-3 hover:border-primary transition">
                            <div class="min-w-0">
                                ${item.category ? `<span class="text-[11px] font-bold text-gold-dark block truncate mb-0.5">${item.category}</span>` : ''}
                                <h4 class="font-extrabold text-sm sm:text-[15px] text-sage-dark leading-snug">${item.arName}</h4>
                                ${item.enName ? `<span class="text-[11px] font-serif tracking-wider text-gray-400 block uppercase mt-0.5">${item.enName}</span>` : ''}
                            </div>
                            ${item.price ? `
                                <div class="shrink-0 text-left">
                                    <span class="inline-block px-3 py-1 rounded-full bg-gold-light/60 text-gold-dark font-extrabold text-xs sm:text-sm border border-gold/25 shadow-2xs">
                                        ${item.price}
                                    </span>
                                </div>
                            ` : ''}
                        </div>
                    `).join('');
                }
            } else {
                if (pagesContainer) pagesContainer.classList.remove('hidden');
                if (resultsContainer) resultsContainer.classList.add('hidden');
                if (resultsList) resultsList.innerHTML = '';
            }

            updateSearchFeedbackUI('services', rawQuery, normalizedQuery, totalMatchedServices);
        }

        function filterBookingTreeView(rawQuery, normalizedQuery) {
            const categories = document.querySelectorAll('#bookingStep1Container .booking-tree-category');

            if (!normalizedQuery) {
                categories.forEach(catEl => {
                    catEl.classList.remove('hidden');
                    const countBadge = catEl.querySelector('.booking-cat-count');
                    if (countBadge) {
                        const total = countBadge.getAttribute('data-total-count') || '0';
                        countBadge.textContent = `${total} خدمات`;
                    }
                });
                document.querySelectorAll('#bookingStep1Container .booking-service-item').forEach(li => {
                    li.classList.remove('hidden');
                });
                updateSearchFeedbackUI('booking', '', '', 0);
                expandAllBookingCategories();
                return;
            }

            let totalMatchedBookingServices = 0;

            categories.forEach(catEl => {
                const catId = catEl.getAttribute('data-category-tree');
                const catTitle = catEl.getAttribute('data-category-title') || '';
                const items = catEl.querySelectorAll('.booking-service-item');
                let catMatches = 0;

                items.forEach(li => {
                    const combinedText = `${catTitle} ${li.textContent}`;
                    if (matchesSearchTokens(combinedText, normalizedQuery)) {
                        li.classList.remove('hidden');
                        catMatches++;
                        totalMatchedBookingServices++;
                    } else {
                        li.classList.add('hidden');
                    }
                });

                const countBadge = catEl.querySelector('.booking-cat-count');
                if (catMatches > 0) {
                    catEl.classList.remove('hidden');
                    if (catId) setCategoryExpandedState(catId, true);
                    if (countBadge) {
                        countBadge.textContent = `${catMatches} خدمات`;
                    }
                } else {
                    catEl.classList.add('hidden');
                }
            });

            updateSearchFeedbackUI('booking', rawQuery, normalizedQuery, totalMatchedBookingServices);
            syncToggleAllTreeButton();
        }

        function initServicesSearch() {
            // Close suggestions dropdown when clicking outside
            document.addEventListener('click', function (e) {
                const dropdown = document.getElementById('servicesSuggestionsDropdown');
                const searchForm = document.getElementById('serviceSearch')?.closest('form');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    if (!searchForm || !searchForm.contains(e.target)) {
                        hideServicesSuggestions();
                    }
                }
            });

            // Close suggestions dropdown when pressing Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    hideServicesSuggestions();
                }
            });
        }

        // Initialize on Window Load
        window.onload = function () {
            startHeroSlider();
            renderCustomerProfile();
            updateBookingBadge();
            renderMyBookings();
            initServicesSearch();

            // Listen to native fullscreen change events (e.g. user presses ESC or browser controls)
            ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(evt => {
                document.addEventListener(evt, syncFullscreenUI);
            });
            syncFullscreenUI();

            const initial = "{{ $initialView ?? 'home' }}";
            if (initial === 'about') {
                switchTab('about');
            } else if (initial === 'account') {
                switchTab('account');
            } else if (initial === 'services') {
                switchTab('services');
            } else if (initial === 'specialists') {
                switchTab('specialists');
            } else if (initial === 'booking') {
                openBookingFlow();
            } else if (initial === 'contact') {
                switchTab('contact');
            } else if (initial === 'privacy') {
                switchTab('privacy');
            } else {
                switchTab('home');
            }
        };
    </script>
</body>
</html>
