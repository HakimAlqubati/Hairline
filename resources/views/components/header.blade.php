@props([
    'salonIdentity' => [],
    'workingHours' => [],
])

<style>
    /* Drop shadow following the tapered contour */
    .header-tapered-wrapper {
        filter: drop-shadow(0 10px 22px rgba(31, 58, 46, 0.14));
    }

    /* Mobile: 20° angle slant from top corners with soft rounded bottom vertices */
    .header-tapered-shape {
        clip-path: polygon(
            0% 0%,
            100% 0%,
            calc(100% - 6px) calc(100% - 18px),
            calc(100% - 10px) calc(100% - 8px),
            calc(100% - 14px) calc(100% - 2px),
            calc(100% - 18px) 100%,
            calc(18px) 100%,
            calc(14px) calc(100% - 2px),
            calc(10px) calc(100% - 8px),
            calc(6px) calc(100% - 18px)
        );
    }

    /* Tablet (sm/md): proportional 20° angle slant */
    @media (min-width: 640px) {
        .header-tapered-shape {
            clip-path: polygon(
                0% 0%,
                100% 0%,
                calc(100% - 10px) calc(100% - 26px),
                calc(100% - 16px) calc(100% - 12px),
                calc(100% - 22px) calc(100% - 3px),
                calc(100% - 28px) 100%,
                calc(28px) 100%,
                calc(22px) calc(100% - 3px),
                calc(16px) calc(100% - 12px),
                calc(10px) calc(100% - 26px)
            );
        }
    }    /* Desktop (lg/xl/2xl): subtle luxury 20° angle descent from top corners with smooth rounded bottom corners */
    @media (min-width: 1024px) {
        .header-tapered-shape {
            clip-path: polygon(
                0% 0%,
                100% 0%,
                calc(100% - 15px) calc(100% - 38px),
                calc(100% - 21px) calc(100% - 18px),
                calc(100% - 27px) calc(100% - 7px),
                calc(100% - 33px) calc(100% - 1px),
                calc(100% - 40px) 100%,
                calc(40px) 100%,
                calc(33px) calc(100% - 1px),
                calc(27px) calc(100% - 7px),
                calc(21px) calc(100% - 18px),
                calc(15px) calc(100% - 38px)
            );
        }
    }

    /* Smart Scroll Show/Hide Animation */
    #mainHeader {
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), 
                    filter 0.35s ease, 
                    opacity 0.25s ease;
        will-change: transform;
    }

    /* Hidden on Scroll Down */
    #mainHeader.header-hidden {
        transform: translateY(-115%);
        pointer-events: none;
        opacity: 0.95;
    }

    /* Shown on Scroll Up & Resting at Top */
    #mainHeader.header-visible {
        transform: translateY(0);
        pointer-events: auto;
        opacity: 1;
    }

    /* Floating elevation when scrolled away from top */
    #mainHeader.header-floating {
        filter: drop-shadow(0 14px 28px rgba(31, 58, 46, 0.22));
    }

    /* Fullscreen mode adaptation */
    body.simulated-fullscreen #headerPlaceholder,
    body.simulated-fullscreen #mainHeader {
        max-width: 100% !important;
    }

    /* Header Action Circular Buttons (Guaranteed 1:1 Aspect Ratio & Comfortable Touch Target) */
    .header-action-btn {
        width: 38px;
        height: 38px;
        min-width: 38px;
        min-height: 38px;
        flex-shrink: 0;
        aspect-ratio: 1 / 1;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    @media (max-width: 374px) {
        .header-action-btn {
            width: 35px;
            height: 35px;
            min-width: 35px;
            min-height: 35px;
        }
    }

    @media (min-width: 640px) {
        .header-action-btn {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
        }
    }
</style>

<!-- Placeholder container to preserve layout flow and prevent layout shift (CLS) -->
<div id="headerPlaceholder" class="w-full relative h-[76px] sm:h-[82px] md:h-[92px]">
    <header id="mainHeader" class="fixed top-0 left-0 right-0 mx-auto max-w-[480px] md:max-w-none w-full z-30 header-tapered-wrapper header-visible">
        <!-- Outer Slanted Layer with Luxury Glowing Border Accent -->
        <div class="w-full header-tapered-shape bg-white/30 pb-[1.5px] px-[1px] transition-all">
            <!-- Inner Slanted Main Header -->
            <div class="w-full header-tapered-shape bg-primary pt-4 pb-5 md:py-4 relative transition-all overflow-hidden">
                <!-- Subtle decorative pattern overlay -->
                <div class="absolute inset-0 opacity-25 pointer-events-none bg-[radial-gradient(rgba(255,255,255,0.45)_1.2px,transparent_1.2px)] [background-size:16px_16px]"></div>

                <div class="max-w-7xl mx-auto px-3.5 sm:px-6 md:px-14 lg:px-20 relative flex items-center justify-between gap-2 sm:gap-4 w-full">
                    <!-- Right Side: Official Salon Logo + Name & Subtitle -->
                    <div class="flex items-center gap-2 sm:gap-3.5 cursor-pointer min-w-0" onclick="switchTab('home')">
                        <!-- Circular Luxury Logo with White Background & Border -->
                        <div class="w-11 h-11 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-full bg-white border-2 border-white/90 shadow-md ring-2 ring-black/5 flex items-center justify-center relative overflow-hidden shrink-0 p-1 sm:p-1.5 transition-transform hover:scale-105">
                            <img src="{{ asset('images/logo.png') }}"
                                 alt="{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} - {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}"
                                 class="w-full h-full object-contain">
                        </div>

                        <!-- Salon Title & Subtitle -->
                        <div class="flex flex-col min-w-0">
                            <div class="flex items-center gap-1 sm:gap-1.5">
                                <h1 class="text-sm sm:text-lg md:text-xl font-black text-white tracking-tight truncate drop-shadow-sm">{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }}</h1>
                                <span class="text-white/60 font-light text-xs sm:text-base">|</span>
                                <span class="text-[11px] sm:text-sm md:text-base font-bold tracking-wider text-white/95 font-serif drop-shadow-sm">{{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}</span>
                            </div>
                            <span class="text-[9px] sm:text-[11px] font-bold tracking-[0.2em] sm:tracking-[0.24em] text-white/80 uppercase mt-0.5 drop-shadow-xs">
                                {{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}
                            </span>
                        </div>
                    </div>

                    <!-- Middle: Desktop Navigation Bar (Visible on md: and above) -->
                    <nav aria-label="شريط التنقل العلوي" class="hidden md:flex items-center gap-1 lg:gap-3 bg-black/10 px-3 py-1.5 rounded-full border border-white/15 backdrop-blur-xs">
                        <!-- 1. الرئيسية -->
                        <button onclick="switchTab('home')" id="desktop-nav-home" 
                                class="desktop-nav-item px-4 py-1.5 rounded-full text-xs lg:text-sm font-black text-white hover:bg-white/20 transition-all flex flex-col items-center relative cursor-pointer">
                            <span>الرئيسية</span>
                            <span class="desktop-nav-indicator w-5 h-0.5 rounded-full bg-gold-light mt-0.5 transition-all duration-300"></span>
                        </button>

                        <!-- 2. دليل الخدمات -->
                        <button onclick="switchTab('services')" id="desktop-nav-services" 
                                class="desktop-nav-item px-4 py-1.5 rounded-full text-xs lg:text-sm font-medium text-white/75 hover:text-white hover:bg-white/20 transition-all flex flex-col items-center relative cursor-pointer">
                            <span>دليل الخدمات</span>
                            <span class="desktop-nav-indicator w-5 h-0.5 rounded-full bg-gold-light mt-0.5 opacity-0 scale-x-0 transition-all duration-300"></span>
                        </button>

                        <!-- 3. عن هيرلاين -->
                        <button onclick="switchTab('about')" id="desktop-nav-about" 
                                class="desktop-nav-item px-4 py-1.5 rounded-full text-xs lg:text-sm font-medium text-white/75 hover:text-white hover:bg-white/20 transition-all flex flex-col items-center relative cursor-pointer">
                            <span>عن هيرلاين</span>
                            <span class="desktop-nav-indicator w-5 h-0.5 rounded-full bg-gold-light mt-0.5 opacity-0 scale-x-0 transition-all duration-300"></span>
                        </button>

                        <!-- 4. حسابي / حجوزاتي -->
                        <button onclick="switchTab('account')" id="desktop-nav-account" 
                                class="desktop-nav-item px-4 py-1.5 rounded-full text-xs lg:text-sm font-medium text-white/75 hover:text-white hover:bg-white/20 transition-all flex flex-col items-center relative cursor-pointer">
                            <div class="flex items-center gap-1.5">
                                <span>حسابي</span>
                                <span id="desktopBookingCountBadge" class="hidden px-1.5 py-0.5 rounded-full bg-gold text-white text-[10px] font-black">1</span>
                            </div>
                            <span class="desktop-nav-indicator w-5 h-0.5 rounded-full bg-gold-light mt-0.5 opacity-0 scale-x-0 transition-all duration-300"></span>
                        </button>
                    </nav>

                    <!-- Left Side: Quick Actions & Status -->
                    <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                        <!-- Desktop Working Hours Pill -->
                        <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs">
                            <span class="w-2 h-2 rounded-full bg-gold-light ring-2 ring-white/40"></span>
                            <span class="font-medium text-white/90">{{ $workingHours['hours_ar'] ?? 'من 2:00 ظهراً حتى 10:00 مساءً' }}</span>
                        </div>

                        <!-- 1. Fullscreen Toggle Button (Mobile Only) -->
                        <button id="fullscreenBtn" 
                                onclick="toggleFullscreen()" 
                                title="ملء الشاشة"
                                aria-label="ملء الشاشة"
                                class="header-action-btn md:hidden bg-white text-sage-dark shadow-md shadow-black/10 hover:bg-white/95 hover:text-primary transition active:scale-95 cursor-pointer">
                            <i id="fullscreenIcon" class="fa-solid fa-expand text-[13px] sm:text-sm"></i>
                        </button>

                        <!-- 2. Quick Working Hours / Status Icon -->
                        <button onclick="openToast('الصالون مفتوح الآن • نستقبلكن {{ $workingHours['hours_ar'] ?? 'من 2:00 ظهراً حتى 10:00 مساءً' }}')" 
                                title="أوقات العمل"
                                aria-label="أوقات العمل"
                                class="header-action-btn bg-white text-sage-dark shadow-md shadow-black/10 hover:bg-white/95 hover:text-primary transition active:scale-95 cursor-pointer">
                            <span class="w-2.5 h-2.5 rounded-full bg-gold absolute top-1.5 right-1.5 sm:top-2 sm:right-2 ring-2 ring-white"></span>
                            <i class="fa-regular fa-bell text-[13px] sm:text-sm"></i>
                        </button>

                        <!-- 3. Quick Location / Contact Icon -->
                        <button onclick="openSidebarSection('contact')" 
                                title="موقع الصالون والتواصل"
                                aria-label="موقع الصالون والتواصل"
                                class="header-action-btn bg-white text-primary shadow-md shadow-black/10 hover:bg-white/95 hover:opacity-80 transition active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-location-dot text-[13px] sm:text-sm text-primary"></i>
                        </button>

                        <!-- 4. Far-Left Sidebar Menu Button (Mobile Only) -->
                        <button id="menuBtn" 
                                onclick="toggleSidebar(true)" 
                                title="القائمة الجانبية"
                                aria-label="القائمة الجانبية"
                                class="header-action-btn md:hidden bg-white text-sage-dark shadow-md shadow-black/10 hover:bg-gray-900 hover:text-white transition active:scale-95 cursor-pointer border border-white/60">
                            <i class="fa-solid fa-ellipsis-vertical text-[13px] sm:text-base"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>

<script>
    (function() {
        let lastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
        let isHeaderHidden = false;
        let ticking = false;
        const scrollThreshold = 75; // Distance from top before hide-on-down triggers
        const deltaThreshold = 5;   // Ignore micro-scrolls < 5px to prevent twitching

        function updateHeaderScroll() {
            const header = document.getElementById('mainHeader');
            if (!header) return;

            const currentScrollY = Math.max(0, window.pageYOffset || document.documentElement.scrollTop || 0);
            const scrollDelta = currentScrollY - lastScrollY;

            // 1. At or near top: always show header in natural resting state
            if (currentScrollY <= 15) {
                if (isHeaderHidden) {
                    header.classList.remove('header-hidden');
                    header.classList.add('header-visible');
                    isHeaderHidden = false;
                }
                header.classList.remove('header-floating');
                lastScrollY = currentScrollY;
                return;
            }

            // 2. Add luxury floating elevation shadow when scrolled down
            header.classList.add('header-floating');

            // 3. Skip micro-movements to avoid jitter
            if (Math.abs(scrollDelta) < deltaThreshold) {
                return;
            }

            // 4. Scrolling DOWN -> Hide header with smooth ease-out
            if (scrollDelta > 0 && currentScrollY > scrollThreshold) {
                if (!isHeaderHidden) {
                    header.classList.remove('header-visible');
                    header.classList.add('header-hidden');
                    isHeaderHidden = true;
                }
            } 
            // 5. Scrolling UP -> Reveal header with beautiful spring ease-out
            else if (scrollDelta < 0) {
                if (isHeaderHidden) {
                    header.classList.remove('header-hidden');
                    header.classList.add('header-visible');
                    isHeaderHidden = false;
                }
            }

            lastScrollY = currentScrollY;
        }

        // 60fps / 120fps smooth passive listener
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    updateHeaderScroll();
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        // Synchronize placeholder height to avoid any CLS layout shifts
        function syncPlaceholderHeight() {
            const placeholder = document.getElementById('headerPlaceholder');
            const header = document.getElementById('mainHeader');
            if (placeholder && header) {
                placeholder.style.height = header.offsetHeight + 'px';
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncPlaceholderHeight);
        } else {
            syncPlaceholderHeight();
        }
        window.addEventListener('resize', syncPlaceholderHeight, { passive: true });
    })();
</script>
