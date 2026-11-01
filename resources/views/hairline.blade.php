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

        const defaultBookingDateObj = bookingDates[1] || bookingDates[0] || { display_label: "غداً - السبت", date_iso: "" };

        let bookingState = {
            step: 1, // 1: Services (Main Category + Sub-Services directly underneath), 2: Specialist & Time, 3: Confirmation
            selectedCategory: null,
            selectedSubService: null,
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
            confirmedCustomerName: "",
            confirmedCustomerEmail: ""
        };

        // Saudi Mobile Regex: accepts 05XXXXXXXX, +9665XXXXXXXX, 009665XXXXXXXX, 9665XXXXXXXX, or 5XXXXXXXX
        const SAUDI_PHONE_REGEX = /^(?:\+966|00966|966|0)?5[0-9]{8}$/;

        // Logged-in customer & confirmed bookings backed directly by Database
        let currentCustomer = {!! json_encode($currentCustomer ?? null, JSON_UNESCAPED_UNICODE) !!};
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
            const desktopFabContainer = document.getElementById('desktopFloatingBookingContainer');

            if (homeView) homeView.classList.add('hidden');
            if (aboutView) aboutView.classList.add('hidden');
            if (accountView) accountView.classList.add('hidden');
            if (servicesView) servicesView.classList.add('hidden');
            if (specialistsView) specialistsView.classList.add('hidden');
            if (bookingView) bookingView.classList.add('hidden');

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

            if (imgEl) {
                imgEl.src = sp.avatar || '';
                imgEl.alt = sp.name || '';
                imgEl.onerror = function () {
                    this.onerror = null;
                    this.src = 'https://placehold.co/400x500/FAF8F5/C29E6D?text=HL';
                };
            }

            if (nameEl) nameEl.textContent = sp.name || '';
            if (roleEl) roleEl.textContent = sp.role || '';
            if (expEl) expEl.textContent = sp.experience || '';
            if (bioEl) bioEl.textContent = sp.bio || '';

            if (servicesEl) {
                const cats = getSpecialistServiceCategories(sp.id);
                if (cats.length > 0) {
                    if (servicesWrapper) servicesWrapper.classList.remove('hidden');
                    servicesEl.innerHTML = cats.map(title => `
                        <span class="px-3 py-1 rounded-full bg-primary-light/85 text-primary-dark border border-primary/30 text-xs font-extrabold">
                            ${title}
                        </span>
                    `).join('');
                } else if (servicesWrapper) {
                    servicesWrapper.classList.add('hidden');
                }
            }

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
            const initialDateObj = bookingDates[1] || bookingDates[0] || { display_label: "غداً - السبت", date_iso: "" };
            bookingState = {
                step: 1,
                selectedCategory: null,
                selectedSubService: null,
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
                confirmedCustomerName: "",
                confirmedCustomerEmail: ""
            };

            clearAllServiceCheckboxes();
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
                bookingState.step = 1;
                bookingState.selectedSpecialist = null;
                bookingState.selectedTime = null;
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
            const buttons = Array.from(document.querySelectorAll('[id^="tree-btn-"]'));
            const anyExpanded = buttons.some(btn => btn.getAttribute('aria-expanded') === 'true');
            const targetState = !anyExpanded;

            document.querySelectorAll('[data-category-tree]').forEach(el => {
                const catId = el.getAttribute('data-category-tree');
                if (catId) setCategoryExpandedState(catId, targetState);
            });
            syncToggleAllTreeButton();
        }

        function syncToggleAllTreeButton() {
            const buttons = Array.from(document.querySelectorAll('[id^="tree-btn-"]'));
            const anyExpanded = buttons.some(btn => btn.getAttribute('aria-expanded') === 'true');
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

            if (checkboxEl.checked) {
                document.querySelectorAll('.booking-service-checkbox').forEach(cb => {
                    if (cb !== checkboxEl) {
                        cb.checked = false;
                    }
                });
                document.querySelectorAll('.booking-tree-row').forEach(row => {
                    row.classList.remove('bg-gold-light/40');
                });

                const activeRow = document.getElementById(`srv-row-${subId}`);
                if (activeRow) {
                    activeRow.classList.add('bg-gold-light/40');
                }

                const { foundCategory, foundSub } = findCategoryAndSubService(subId, catId);
                bookingState.selectedCategory = foundCategory || null;
                bookingState.selectedSubService = foundSub || null;
            } else {
                const activeRow = document.getElementById(`srv-row-${subId}`);
                if (activeRow) {
                    activeRow.classList.remove('bg-gold-light/40');
                }
                bookingState.selectedCategory = null;
                bookingState.selectedSubService = null;
            }

            syncFloatingConfirmButton();
        }

        function syncFloatingConfirmButton() {
            const container = document.getElementById('floatingBookingConfirmContainer');
            const labelEl = document.getElementById('floatingSelectedServiceText');
            const whatsappContainer = document.getElementById('floatingWhatsappContainer');
            if (!container) return;

            const shouldShow = (
                currentActiveTab === 'booking' &&
                bookingState.step === 1 &&
                Boolean(bookingState.selectedSubService)
            );

            if (floatingConfirmHideTimeout) {
                clearTimeout(floatingConfirmHideTimeout);
                floatingConfirmHideTimeout = null;
            }

            if (whatsappContainer) {
                whatsappContainer.classList.toggle('-translate-y-16', shouldShow);
            }

            if (shouldShow) {
                if (labelEl && bookingState.selectedSubService) {
                    labelEl.textContent = `${bookingState.selectedSubService.name} • ${bookingState.selectedSubService.price}`;
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
            if (!bookingState.selectedCategory || !bookingState.selectedSubService) return;
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
                const sub = bookingState.selectedSubService;
                if (!sub) return;

                if (pageTitle) pageTitle.textContent = "الخطوة 2: اختيار الأخصائية";
                if (pageSubtitle) {
                    pageSubtitle.textContent = "";
                    pageSubtitle.classList.add('hidden');
                }
                if (step2Container) step2Container.classList.remove('hidden');

                const linkedSpecialists = sub.specialists.map(id => specialistsData[id]).filter(Boolean);
                if (!bookingState.selectedSpecialist && linkedSpecialists.length > 0) {
                    bookingState.selectedSpecialist = linkedSpecialists[0];
                    bookingState.selectedTime = linkedSpecialists[0].availableSlots[0] || null;
                    fetchSpecialistAvailability();
                }

                const datesList = (Array.isArray(bookingDates) && bookingDates.length > 0)
                    ? bookingDates
                    : [
                        { display_label: "اليوم", date_iso: "" },
                        { display_label: "غداً - السبت", date_iso: "" }
                    ];

                const shiftSlots = (bookingState.availability && Array.isArray(bookingState.availability.slots) && bookingState.availability.slots.length > 0)
                    ? bookingState.availability.slots
                    : (bookingState.selectedSpecialist?.availableSlots || []).map(t => ({
                        time: t,
                        is_available: true,
                        is_booked: false
                    }));

                const isDayOff = Boolean(bookingState.availability && bookingState.availability.is_day_off);
                const shiftLabel = bookingState.availability?.shift_label || "";

                if (step2Body) {
                    step2Body.innerHTML = `
                        <div class="space-y-4 animate-step">
                            <!-- Summary Card -->
                            <div class="p-4 rounded-2xl bg-gold-light/70 border border-gold/40 flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-bold text-gold-dark block">${bookingState.selectedCategory.title}</span>
                                    <h4 class="font-extrabold text-sm sm:text-base text-sage-dark mt-0.5">${sub.name}</h4>
                                </div>
                                <div class="text-left shrink-0">
                                    <span class="font-black text-sm sm:text-base text-sage-dark block">${sub.price}</span>
                                    <span class="text-[11px] text-gray-500">${sub.duration}</span>
                                </div>
                            </div>

                            <!-- Specialists List -->
                            <div class="space-y-2.5">
                                <label class="block text-xs sm:text-sm font-extrabold text-sage-dark">
                                    اختاري الأخصائية (${linkedSpecialists.length} أخصائيات معتمدات):
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    ${linkedSpecialists.map(sp => {
                                        const isSelected = bookingState.selectedSpecialist && bookingState.selectedSpecialist.id === sp.id;
                                        return `
                                            <div onclick="chooseSpecialist('${sp.id}')" 
                                                 class="p-3.5 rounded-2xl border transition-all duration-200 cursor-pointer flex items-center justify-between gap-3 ${
                                                     isSelected 
                                                     ? 'bg-primary-light/70 border-primary shadow-xs ring-1 ring-primary/20' 
                                                     : 'bg-white border-gray-200/80 hover:border-primary-soft hover:bg-surface'
                                                 }">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="relative w-11 h-11 shrink-0" style="width: 44px; height: 44px;">
                                                        <img src="${sp.avatar}" 
                                                             alt="${sp.name}"
                                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(sp.name)}&background=211C18&color=C29E6D&bold=true&size=96';"
                                                             class="w-11 h-11 rounded-full object-cover border ${isSelected ? 'border-primary ring-2 ring-primary/20' : 'border-gold/30'} shrink-0 shadow-2xs"
                                                             style="width: 44px; height: 44px; min-width: 44px; min-height: 44px;">
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h5 class="font-extrabold text-sm text-sage-dark truncate leading-tight">${sp.name}</h5>
                                                        <p class="text-[11px] text-gray-500 truncate mt-0.5 font-medium">${sp.role}</p>
                                                        <div class="flex items-center gap-1.5 mt-1">
                                                            <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-gold-dark bg-gold-light/60 px-2 py-0.5 rounded-md border border-gold/25">
                                                                <i class="fa-regular fa-clock text-[9px]"></i>
                                                                ${sp.experience}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="w-6 h-6 rounded-full border-2 shrink-0 flex items-center justify-center transition-all ${
                                                    isSelected 
                                                    ? 'border-primary bg-primary text-white shadow-xs' 
                                                    : 'border-gray-300 bg-white'
                                                }">
                                                    ${isSelected ? '<i class="fa-solid fa-check text-[10px]"></i>' : ''}
                                                </div>
                                            </div>
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
                                    ${shiftLabel ? `
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-sage-soft text-sage-dark text-[10px] font-bold border border-gold-soft/50">
                                            <i class="fa-regular fa-calendar-check text-gold-dark"></i>
                                            <span>${shiftLabel}</span>
                                        </span>
                                    ` : ''}
                                </div>
                                
                                <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                                    ${datesList.map(d => {
                                        const label = d.display_label || d;
                                        const iso = d.date_iso || '';
                                        const isActiveDate = bookingState.selectedDate === label || (iso && bookingState.selectedDateIso === iso);
                                        return `
                                            <button onclick="selectBookingDate('${label}', '${iso}')" 
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

                                ${isDayOff ? `
                                    <div class="p-4 rounded-2xl bg-red-50/70 border border-red-200 text-center space-y-1">
                                        <p class="text-xs font-extrabold text-red-700">الأخصائية في يوم إجازة في هذا التاريخ</p>
                                        <p class="text-[11px] text-red-600">يرجى اختيار يوم آخر أو أخصائية أخرى لإتمام الحجز.</p>
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
                                `}
                            </div>
                        </div>
                    `;
                }

                const canConfirm = Boolean(bookingState.selectedTime) && !isDayOff;
                if (step2Footer) {
                    step2Footer.innerHTML = `
                        <button onclick="confirmFinalBooking()" 
                                ${canConfirm ? '' : 'disabled'}
                                class="w-full py-4 rounded-2xl ${canConfirm ? 'bg-primary hover:bg-primary-dark shadow-lg shadow-primary/25 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'} text-white font-extrabold text-sm transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-check text-white"></i>
                            <span>${canConfirm ? `تأكيد الحجز مع ${bookingState.selectedSpecialist.name}` : 'يرجى اختيار وقت متاح للحجز'}</span>
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
                                    <span class="text-sm font-extrabold text-sage-dark">${lastBooking.specialistName}</span>
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
            bookingState.selectedSpecialist = sp;
            bookingState.selectedTime = sp.availableSlots[0] || null;
            fetchSpecialistAvailability();
            renderBookingStep();
        }

        function selectBookingDate(dateStr, dateIso = '') {
            bookingState.selectedDate = dateStr;
            if (dateIso) {
                bookingState.selectedDateIso = dateIso;
            }
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

            bookingState.availability.loading = true;
            const params = new URLSearchParams({
                specialist_id: bookingState.selectedSpecialist.id,
                service_id: bookingState.selectedSubService?.id || '',
                booking_date: bookingState.selectedDateIso || bookingState.selectedDate || ''
            });

            try {
                const response = await fetch("{{ route('booking.availability') }}?" + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
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
                }
            } catch (err) {
                bookingState.availability.loading = false;
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

            if (!modal || !card) return;

            const timeSlot = bookingState.selectedTime || bookingState.selectedSpecialist.availableSlots[0];
            if (summaryEl) {
                summaryEl.innerHTML = `
                    <div class="flex items-center justify-between font-extrabold text-sage-dark">
                        <span>${bookingState.selectedSubService.name}</span>
                        <span class="text-gold-dark">${bookingState.selectedSubService.price}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-gray-500">
                        <span>${bookingState.selectedSpecialist.name}</span>
                        <span>${bookingState.selectedDate} • ${timeSlot}</span>
                    </div>
                `;
            }

            switchPopupToPhoneStep();
            if (errorEl) errorEl.classList.add('hidden');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
                if (phoneInput) phoneInput.focus();
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

            const rawPhone = normalizeDigitsJs(phoneInput ? phoneInput.value : '');

            if (!SAUDI_PHONE_REGEX.test(rawPhone)) {
                if (errorText) {
                    errorText.textContent = 'يرجى إدخال رقم جوال سعودي صحيح يبدأ بـ 05 ويتكون من 10 أرقام (مثال: 0551234567).';
                }
                if (errorEl) errorEl.classList.remove('hidden');
                if (phoneInput) phoneInput.classList.add('border-red-500');
                return;
            }

            if (errorEl) errorEl.classList.add('hidden');
            if (phoneInput) phoneInput.classList.remove('border-red-500');

            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري التحقق من الرقم...</span>`;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const timeSlot = bookingState.selectedTime || bookingState.selectedSpecialist.availableSlots[0];

                const response = await fetch("{{ route('booking.verify_phone') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        phone: rawPhone,
                        service_id: bookingState.selectedSubService.id,
                        specialist_id: bookingState.selectedSpecialist.id,
                        booking_date: bookingState.selectedDateIso || bookingState.selectedDate,
                        booking_time: timeSlot
                    })
                });

                const data = await response.json();

                if (!response.ok || data.status === 'validation_error') {
                    if (errorText) errorText.textContent = data.message || 'تأكدي من صحة رقم الجوال المدخل.';
                    if (errorEl) errorEl.classList.remove('hidden');
                    fetchSpecialistAvailability();
                    return;
                }

                // Case 1: Phone exists in customers table -> Booking created in DB, show confirmation with customer's name
                if (data.exists && data.status === 'confirmed') {
                    finalizeConfirmedBookingFromServer(data);
                }
                // Case 2: Phone does NOT exist in customers table -> Show Google Registration Screen
                else {
                    bookingState.verifiedPhone = data.phone || rawPhone;
                    switchPopupToGoogleStep(bookingState.verifiedPhone);
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
            const timeSlot = bookingState.selectedTime || bookingState.selectedSpecialist?.availableSlots?.[0] || '';
            const params = new URLSearchParams({
                phone: bookingState.verifiedPhone || '',
                service_id: bookingState.selectedSubService?.id || '',
                specialist_id: bookingState.selectedSpecialist?.id || '',
                booking_date: bookingState.selectedDate || '',
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
                const timeSlot = bookingState.selectedTime || bookingState.selectedSpecialist?.availableSlots?.[0] || '';

                const response = await fetch("{{ route('booking.google_register') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(Object.assign({
                        phone: bookingState.verifiedPhone || '',
                        service_id: bookingState.selectedSubService?.id || '',
                        specialist_id: bookingState.selectedSpecialist?.id || '',
                        booking_date: bookingState.selectedDate || '',
                        booking_time: timeSlot
                    }, oauthPayload))
                });

                const data = await response.json();

                if (!response.ok || data.status === 'validation_error') {
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
                openToast(`تم تأكيد الحجز بنجاح باسم ${customerName} ✨`);
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

        // Instant Search & Highlight across all services in servicesTabView
        function initServicesSearch() {
            const searchInput = document.getElementById('serviceSearch');
            const clearBtn = document.getElementById('clearSearchBtn');
            if (!searchInput) return;

            searchInput.addEventListener('input', () => {
                const query = searchInput.value.trim().toLowerCase();
                if (clearBtn) clearBtn.classList.toggle('hidden', query.length === 0);

                const rows = document.querySelectorAll('#servicesTabView .service-row');
                let firstMatch = null;

                rows.forEach(row => {
                    row.classList.remove('search-highlight');
                    if (query.length >= 2 && row.textContent.toLowerCase().includes(query)) {
                        row.classList.add('search-highlight');
                        if (!firstMatch) firstMatch = row;
                    }
                });

                if (firstMatch) {
                    firstMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    searchInput.value = '';
                    clearBtn.classList.add('hidden');
                    document.querySelectorAll('#servicesTabView .service-row').forEach(r => r.classList.remove('search-highlight'));
                });
            }
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
            } else {
                switchTab('home');
            }
        };
    </script>
</body>
</html>
