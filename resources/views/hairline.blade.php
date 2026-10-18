<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>هيرلاين | HAIRLINE BEAUTY LOUNGE</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Tajawal & Cormorant Garamond for English luxury accents -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sage: {
                            light: '#DCE7E0',      /* Light Sage Green for Header */
                            soft: '#EDF3EF',       /* Ultra soft sage tint */
                            medium: '#8FA998',     /* Muted sage accent */
                            dark: '#1F3A2E',       /* Dark Forest/Sage Green for primary buttons */
                            deeper: '#162920'      /* Hover dark forest */
                        },
                        gold: {
                            light: '#F6EFE2',      /* Light Beige/Gold background badge */
                            soft: '#E6D5B8',       /* Soft Beige border */
                            DEFAULT: '#C5A059',    /* Classic Luxury Gold */
                            dark: '#A3803B'        /* Deep Gold for text/icons */
                        },
                        surface: '#F7F8F7'
                    },
                    fontFamily: {
                        sans: ['Tajawal', 'sans-serif'],
                        serif: ['Cormorant Garamond', 'serif']
                    },
                    boxShadow: {
                        'card': '0 6px 24px -4px rgba(31, 58, 46, 0.06)',
                        'floating': '0 -4px 25px rgba(31, 58, 46, 0.08)',
                        'fab': '0 10px 25px -3px rgba(31, 58, 46, 0.45)'
                    }
                }
            }
        }
    </script>

    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            box-sizing: border-box;
        }
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #E8EFEA;
            color: #1F3A2E;
            overflow-x: hidden;
        }
        /* Hide scrollbars for smooth horizontal pills */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* Smooth Drawer & Bottom Sheet Transitions */
        .drawer-backdrop {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-drawer {
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .bottom-sheet {
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        /* Subtle pulse on gold badge */
        @keyframes subtlePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }
        .badge-pulse {
            animation: subtlePulse 3s infinite ease-in-out;
        }
        /* Fade in animation for dynamic steps */
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
    </style>
</head>

<body class="min-h-screen flex justify-center bg-[#E4ECE7] selection:bg-sage-dark selection:text-white">

    <!-- Mobile App Container (Responsive: full screen on mobile, sleek phone frame on desktop) -->
    <div class="w-full max-w-[480px] min-h-screen bg-surface relative flex flex-col shadow-2xl overflow-hidden pb-28">

        <!-- ==================== 1. TOP HEADER ==================== -->
        <header class="bg-sage-light pt-5 pb-6 px-5 rounded-b-[28px] shadow-sm relative z-20 border-b border-white/50">
            <!-- Subtle decorative pattern overlay -->
            <div class="
                absolute inset-0 opacity-20 pointer-events-none rounded-b-[28px]
                bg-[radial-gradient(#1F3A2E_1px,transparent_1px)] [background-size:16px_16px]
            "></div>

            <div class="relative flex items-center justify-between">
                <!-- Right Side: Circular Logo + Salon Name & Subtitle -->
                <div class="flex items-center gap-3.5 cursor-pointer" onclick="switchTab('home')">
                    <!-- Circular Luxury Logo with Gold Border & Initial/Monogram -->
                    <div class="w-14 h-14 rounded-full bg-sage-dark border-2 border-gold shadow-md flex flex-col items-center justify-center relative overflow-hidden shrink-0">
                        <div class="absolute inset-0.5 rounded-full border border-gold/30 pointer-events-none"></div>
                        <!-- Minimalist Hair & Crown SVG Emblem -->
                        <svg class="w-7 h-7 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-1.5 2-4 3.5-6 3.5 0 5.5 2.5 9.5 6 11.5 3.5-2 6-6 6-11.5-2 0-4.5-1.5-6-3.5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 10.5c1.5 1 3.5 1 5 0" />
                            <circle cx="12" cy="7" r="0.8" fill="currentColor" />
                        </svg>
                        <span class="text-[8px] tracking-[0.2em] text-gold-light font-serif uppercase mt-0.5">HL</span>
                    </div>

                    <!-- Salon Title & Subtitle -->
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <h1 class="text-lg font-extrabold text-sage-dark tracking-tight">هيرلاين</h1>
                            <span class="text-gold font-light text-base">|</span>
                            <span class="text-sm font-bold tracking-wider text-sage-dark font-serif">HAIRLINE</span>
                        </div>
                        <span class="text-[11px] font-semibold tracking-[0.22em] text-gold-dark uppercase mt-0.5">
                            BEAUTY LOUNGE
                        </span>
                    </div>
                </div>

                <!-- Left Side: Elegant White Circular Icons + Far-Left Sidebar Menu Button -->
                <div class="flex items-center gap-2">
                    <!-- Quick Working Hours / Status Icon -->
                    <button onclick="openToast('الصالون مفتوح الآن • نستقبلكن من 10:00 ص حتى 10:00 م')" 
                            title="أوقات العمل"
                            class="w-10 h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-gold-light hover:text-gold-dark transition active:scale-95 relative">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 absolute top-2 right-2 ring-2 ring-white"></span>
                        <i class="fa-regular fa-bell text-sm"></i>
                    </button>

                    <!-- Quick Location / Contact Icon -->
                    <button onclick="openSidebarSection('contact')" 
                            title="موقع الصالون والتواصل"
                            class="w-10 h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-gold-light hover:text-gold-dark transition active:scale-95">
                        <i class="fa-solid fa-location-dot text-sm text-gold-dark"></i>
                    </button>

                    <!-- Far-Left Sidebar Menu Button (Three Dots / Menu Icon) -->
                    <button id="menuBtn" 
                            onclick="toggleSidebar(true)" 
                            aria-label="القائمة الجانبية"
                            class="w-10 h-10 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-sage-dark hover:text-white transition active:scale-95 border border-gold/20">
                        <i class="fa-solid fa-ellipsis-vertical text-base"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- ==================== MAIN CONTENT AREA ==================== -->
        <main id="mainHomeView" class="flex-1 px-4 pt-5 space-y-5">

            <!-- 2. MAIN SECTIONS (Strictly Two Wide White Rows) -->
            <section aria-label="أقسام الخدمات الرئيسية" class="space-y-3.5">
                
                <!-- Row 1: جميع الخدمات (زيارة فورية) -->
                <button onclick="openAllServicesModal()" 
                        class="w-full bg-white rounded-2xl p-4 shadow-card border border-gold-soft/40 flex items-center justify-between group hover:border-gold transition-all duration-200 active:scale-[0.99] text-right">
                    <div class="flex items-center gap-3.5">
                        <!-- Colored Small Dot on the Right -->
                        <span class="w-3 h-3 rounded-full bg-gold shadow-sm ring-4 ring-gold-light shrink-0"></span>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-base sm:text-lg font-extrabold text-sage-dark group-hover:text-sage-deeper">
                                جميع الخدمات
                            </span>
                            <!-- Explanatory Badge: (زيارة فورية) -->
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gold-light text-gold-dark border border-gold/30 badge-pulse">
                                <i class="fa-solid fa-sparkles text-[10px]"></i>
                                (زيارة فورية)
                            </span>
                        </div>
                    </div>

                    <!-- Circular Arrow Button (‹) on the Left -->
                    <div class="w-9 h-9 rounded-full bg-sage-soft text-sage-dark group-hover:bg-sage-dark group-hover:text-white flex items-center justify-center transition-colors shrink-0">
                        <span class="text-xl font-bold leading-none -mt-0.5">‹</span>
                    </div>
                </button>

                <!-- Row 2: الخدمات المتاحة للحجز -->
                <button onclick="openBookingFlow()" 
                        class="w-full bg-white rounded-2xl p-4 shadow-card border border-gold-soft/40 flex items-center justify-between group hover:border-sage-dark/40 transition-all duration-200 active:scale-[0.99] text-right">
                    <div class="flex items-center gap-3.5">
                        <!-- Colored Small Dot on the Right -->
                        <span class="w-3 h-3 rounded-full bg-sage-dark shadow-sm ring-4 ring-sage-light shrink-0"></span>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2.5">
                            <span class="text-base sm:text-lg font-extrabold text-sage-dark">
                                الخدمات المتاحة للحجز
                            </span>
                            <span class="text-xs font-medium text-gray-500">
                                اختاري الخدمة والأخصائية المفضلة لديكِ
                            </span>
                        </div>
                    </div>

                    <!-- Circular Arrow Button (‹) on the Left -->
                    <div class="w-9 h-9 rounded-full bg-gold-light text-gold-dark group-hover:bg-sage-dark group-hover:text-white flex items-center justify-center transition-colors shrink-0">
                        <span class="text-xl font-bold leading-none -mt-0.5">‹</span>
                    </div>
                </button>

            </section>

            <!-- 3. HERO SLIDER (Promotional Banner) -->
            <section aria-label="البانر الإعلاني" class="relative">
                <div id="heroSliderContainer" class="relative w-full h-64 sm:h-72 rounded-3xl overflow-hidden shadow-lg border border-white">
                    
                    <!-- Dynamic Slide Image -->
                    <img id="heroSlideImg" 
                         src="{{ asset('images/salon/reception-desk.jpg') }}" 
                         alt="هيرلاين بيوتي لاونج - أناقتك تتجدد"
                         onerror="this.src='https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80'"
                         class="w-full h-full object-cover object-center transition-all duration-700 transform scale-100">

                    <!-- Dark Gradient Overlay from Bottom for Text Legibility -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#11221A] via-[#1F3A2E]/45 to-transparent"></div>

                    <!-- Banner Content -->
                    <div class="absolute inset-0 p-6 flex flex-col justify-end">
                        <div class="flex items-end justify-between gap-4">
                            <!-- Right Side Text Content -->
                            <div class="space-y-2">
                                <!-- Translucent Badge: HAIRLINE BEAUTY LOUNGE -->
                                <div class="inline-block px-3.5 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-gold-light text-[11px] font-semibold tracking-[0.18em] uppercase">
                                    HAIRLINE BEAUTY LOUNGE
                                </div>
                                <!-- Large Prominent White Title: أناقتكِ تتجدد -->
                                <h2 id="heroSlideTitle" class="text-3xl sm:text-4xl font-black text-white tracking-tight drop-shadow-sm">
                                    أناقتكِ تتجدد
                                </h2>
                                <p id="heroSlideSubtitle" class="text-xs sm:text-sm text-gray-200 font-medium max-w-[260px]">
                                    أحدث تقنيات المعالجات الحارة والعناية الفائقة بالشعر على أيدي نخبة من الأخصائيات
                                </p>
                            </div>

                            <!-- Left Side Slider Navigation Indicators (Gold & White Line/Dots) -->
                            <div class="flex items-center gap-1.5 pb-1 shrink-0" id="sliderIndicators">
                                <button onclick="setHeroSlide(0)" aria-label="الشريحة 1" class="h-2 w-7 rounded-full bg-gold transition-all duration-300"></button>
                                <button onclick="setHeroSlide(1)" aria-label="الشريحة 2" class="h-2 w-2 rounded-full bg-white/70 hover:bg-white transition-all duration-300"></button>
                                <button onclick="setHeroSlide(2)" aria-label="الشريحة 3" class="h-2 w-2 rounded-full bg-white/70 hover:bg-white transition-all duration-300"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Highlight Card under Banner for Seamless Mobile Experience -->
            <section class="bg-white rounded-2xl p-4 shadow-card border border-gold-soft/40">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-5 rounded-full bg-gold"></span>
                        <h3 class="font-extrabold text-sm text-sage-dark">الأكثر طلباً للحجز هذا الأسبوع</h3>
                    </div>
                    <button onclick="openBookingFlowWithCategory('hot_treatments')" class="text-xs font-bold text-gold-dark hover:underline flex items-center gap-1">
                        <span>احجزي الآن</span>
                        <span>‹</span>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div onclick="openBookingFlowWithCategory('hot_treatments')" 
                         class="p-3 rounded-xl bg-sage-soft/70 border border-sage-light hover:border-gold cursor-pointer transition flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-sage-dark text-gold flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-fire-flame-curved text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-extrabold text-sage-dark">المعالجات الحارة</h4>
                            <p class="text-[11px] text-gray-500">بروتين • كافيار • كولاجين</p>
                        </div>
                    </div>

                    <div onclick="openBookingFlowWithCategory('cold_restore')" 
                         class="p-3 rounded-xl bg-gold-light/60 border border-gold-soft/60 hover:border-gold cursor-pointer transition flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gold text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-wand-magic-sparkles text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-extrabold text-sage-dark">الترميم البارد</h4>
                            <p class="text-[11px] text-gray-500">أولابليكس • ترطيب عميق</p>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- ==================== DYNAMIC TAB VIEWS (About & Account) ==================== -->
        <!-- "عن هيرلاين" View -->
        <section id="aboutTabView" class="hidden flex-1 px-4 pt-5 space-y-4 animate-step">
            <div class="bg-white rounded-3xl p-6 shadow-card border border-gold-soft/40 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full bg-gold-light text-gold-dark text-xs font-bold">من نحن</span>
                    <span class="text-xs font-serif tracking-widest text-gray-400">EST. BEAUTY LOUNGE</span>
                </div>
                <h2 class="text-2xl font-black text-sage-dark">هيرلاين | HAIRLINE BEAUTY LOUNGE</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    وجهة الجمال الأولى المصممة خصيصاً للمرأة العصرية الباحثة عن الفخامة والعناية المتكاملة. نتميز في <strong class="text-sage-dark">هيرلاين</strong> بتقديم أحدث المعالجات الحارة والباردة للشعر باستخدام أجود المستحضرات العالمية الآمنة، تحت إشراف نخبة من الأخصائيات المعتمدات.
                </p>
                <div class="grid grid-cols-3 gap-2 pt-2 text-center">
                    <div class="p-3 rounded-2xl bg-sage-soft">
                        <span class="block text-lg font-black text-sage-dark">+12</span>
                        <span class="text-[11px] text-gray-600 font-medium">أخصائية محترفة</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-gold-light">
                        <span class="block text-lg font-black text-gold-dark">100%</span>
                        <span class="text-[11px] text-gray-600 font-medium">منتجات أصلية</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-sage-soft">
                        <span class="block text-lg font-black text-sage-dark">4.9 ★</span>
                        <span class="text-[11px] text-gray-600 font-medium">تقييم العميلات</span>
                    </div>
                </div>
                <div class="pt-3 border-t border-gray-100 flex gap-2">
                    <button onclick="openSidebarSection('specialists')" class="flex-1 py-3 rounded-xl bg-sage-dark text-white text-xs font-bold hover:bg-sage-deeper transition">
                        تعرفي على الأخصائيات
                    </button>
                    <button onclick="openSidebarSection('contact')" class="flex-1 py-3 rounded-xl bg-gold-light text-gold-dark text-xs font-bold hover:bg-gold hover:text-white transition">
                        معلومات التواصل
                    </button>
                </div>
            </div>
        </section>

        <!-- "حسابي" View -->
        <section id="accountTabView" class="hidden flex-1 px-4 pt-5 space-y-4 animate-step">
            <!-- Member Profile Card -->
            <div class="bg-white rounded-3xl p-5 shadow-card border border-gold-soft/40 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-14 h-14 rounded-full bg-gold-light border-2 border-gold flex items-center justify-center text-gold-dark text-xl font-bold">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-base text-sage-dark">أهلاً بكِ، عميلتنا المميزة</h3>
                            <span class="px-2 py-0.5 rounded-full bg-gold text-white text-[10px] font-bold">VIP</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">عضوية هيرلاين الذهبية • 240 نقطة ولاء</p>
                    </div>
                </div>
            </div>

            <!-- My Bookings List -->
            <div class="bg-white rounded-3xl p-5 shadow-card border border-gold-soft/40 space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="font-extrabold text-sm text-sage-dark flex items-center gap-2">
                        <i class="fa-regular fa-calendar-check text-gold-dark"></i>
                        <span>حجوزاتي المؤكدة</span>
                    </h4>
                    <button onclick="openBookingFlow()" class="text-xs font-bold text-sage-dark bg-sage-soft px-3 py-1 rounded-full hover:bg-sage-dark hover:text-white transition">
                        + حجز جديد
                    </button>
                </div>
                <div id="myBookingsContainer" class="space-y-2.5">
                    <!-- Populated via JS -->
                </div>
            </div>
        </section>

        <!-- ==================== 6. FIXED BOTTOM NAVIGATION BAR ==================== -->
        <nav aria-label="شريط التنقل السفلي" class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white border-t border-gray-100 shadow-floating px-3 py-2 z-30 rounded-t-[26px]">
            <div class="grid grid-cols-5 items-center text-center">
                
                <!-- 1. الرئيسية (Far Right) -->
                <button onclick="switchTab('home')" id="nav-home" class="nav-item flex flex-col items-center justify-center py-1 text-sage-dark font-bold transition">
                    <i class="fa-solid fa-house text-lg mb-1"></i>
                    <span class="text-[11px]">الرئيسية</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gold mt-0.5 nav-dot"></span>
                </button>

                <!-- 2. الخدمات -->
                <button onclick="openAllServicesModal()" id="nav-services" class="nav-item flex flex-col items-center justify-center py-1 text-gray-400 hover:text-sage-dark font-medium transition">
                    <i class="fa-solid fa-spa text-lg mb-1"></i>
                    <span class="text-[11px]">الخدمات</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-transparent mt-0.5 nav-dot"></span>
                </button>

                <!-- 3. احجزي (Central Large Prominent Circular Button in Dark Forest Green) -->
                <div class="flex flex-col items-center justify-center relative">
                    <button onclick="openBookingFlow()" 
                            aria-label="احجزي موعدك"
                            class="-mt-8 w-16 h-16 rounded-full bg-sage-dark text-white shadow-fab border-4 border-white flex flex-col items-center justify-center hover:bg-sage-deeper active:scale-95 transition-all group">
                        <i class="fa-regular fa-calendar-check text-xl text-gold group-hover:scale-110 transition-transform"></i>
                    </button>
                    <span class="text-[11px] font-extrabold text-sage-dark mt-1">احجزي</span>
                </div>

                <!-- 4. عن هيرلاين -->
                <button onclick="switchTab('about')" id="nav-about" class="nav-item flex flex-col items-center justify-center py-1 text-gray-400 hover:text-sage-dark font-medium transition">
                    <i class="fa-solid fa-gem text-lg mb-1"></i>
                    <span class="text-[11px]">عن هيرلاين</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-transparent mt-0.5 nav-dot"></span>
                </button>

                <!-- 5. حسابي (Far Left) -->
                <button onclick="switchTab('account')" id="nav-account" class="nav-item flex flex-col items-center justify-center py-1 text-gray-400 hover:text-sage-dark font-medium transition relative">
                    <i class="fa-regular fa-user text-lg mb-1"></i>
                    <span class="text-[11px]">حسابي</span>
                    <span id="bookingCountBadge" class="hidden absolute top-0.5 left-6 w-4 h-4 rounded-full bg-gold text-white text-[10px] font-bold flex items-center justify-center">1</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-transparent mt-0.5 nav-dot"></span>
                </button>

            </div>
        </nav>

        <!-- ==================== SIDEBAR / DRAWER (3 ITEMS ONLY) ==================== -->
        <div id="sidebarOverlay" 
             onclick="toggleSidebar(false)" 
             class="fixed inset-0 bg-black/50 backdrop-blur-xs z-40 hidden opacity-0 drawer-backdrop">
        </div>

        <aside id="sidebarDrawer" 
               aria-label="القائمة الجانبية"
               class="fixed top-0 left-0 h-full w-72 max-w-[82vw] bg-white z-50 shadow-2xl -translate-x-full sidebar-drawer flex flex-col justify-between rounded-r-3xl overflow-hidden">
            
            <!-- Drawer Top Header -->
            <div>
                <div class="bg-sage-light p-6 border-b border-gold-soft/50 relative">
                    <button onclick="toggleSidebar(false)" 
                            aria-label="إغلاق القائمة"
                            class="w-9 h-9 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-sage-dark hover:text-white transition mb-4">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-sage-dark border-2 border-gold flex items-center justify-center text-gold font-serif font-bold text-sm">
                            HL
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sage-dark text-base">هيرلاين | HAIRLINE</h3>
                            <p class="text-[10px] tracking-[0.2em] text-gold-dark font-semibold uppercase">BEAUTY LOUNGE</p>
                        </div>
                    </div>
                </div>

                <!-- Strictly the 3 Required Navigation Items -->
                <div class="p-4 space-y-2.5 mt-2">
                    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase">القائمة التعريفية</p>

                    <!-- Item 1: التعريف بالصالون (من نحن) -->
                    <button onclick="openSidebarSection('about')" 
                            class="w-full p-3.5 rounded-2xl bg-surface hover:bg-sage-light/60 border border-transparent hover:border-gold/40 flex items-center justify-between transition group text-right">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-xs text-sage-dark group-hover:bg-sage-dark group-hover:text-gold flex items-center justify-center transition">
                                <i class="fa-solid fa-crown text-sm"></i>
                            </div>
                            <div>
                                <span class="block text-sm font-extrabold text-sage-dark">التعريف بالصالون</span>
                                <span class="text-[11px] text-gray-500">(من نحن)</span>
                            </div>
                        </div>
                        <span class="text-lg text-gold-dark font-bold">‹</span>
                    </button>

                    <!-- Item 2: الأخصائيات -->
                    <button onclick="openSidebarSection('specialists')" 
                            class="w-full p-3.5 rounded-2xl bg-surface hover:bg-sage-light/60 border border-transparent hover:border-gold/40 flex items-center justify-between transition group text-right">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-xs text-sage-dark group-hover:bg-sage-dark group-hover:text-gold flex items-center justify-center transition">
                                <i class="fa-solid fa-user-check text-sm"></i>
                            </div>
                            <div>
                                <span class="block text-sm font-extrabold text-sage-dark">الأخصائيات</span>
                                <span class="text-[11px] text-gray-500">نخبة خبيرات الشعر والجمال</span>
                            </div>
                        </div>
                        <span class="text-lg text-gold-dark font-bold">‹</span>
                    </button>

                    <!-- Item 3: التواصل (اتصل بنا) -->
                    <button onclick="openSidebarSection('contact')" 
                            class="w-full p-3.5 rounded-2xl bg-surface hover:bg-sage-light/60 border border-transparent hover:border-gold/40 flex items-center justify-between transition group text-right">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-xs text-sage-dark group-hover:bg-sage-dark group-hover:text-gold flex items-center justify-center transition">
                                <i class="fa-solid fa-phone-volume text-sm"></i>
                            </div>
                            <div>
                                <span class="block text-sm font-extrabold text-sage-dark">التواصل</span>
                                <span class="text-[11px] text-gray-500">(اتصل بنا)</span>
                            </div>
                        </div>
                        <span class="text-lg text-gold-dark font-bold">‹</span>
                    </button>
                </div>
            </div>

            <!-- Drawer Footer -->
            <div class="p-5 bg-gold-light/50 border-t border-gold-soft/40 text-center">
                <p class="text-xs font-bold text-sage-dark">أناقتكِ تتجدد في كل زيارة</p>
                <p class="text-[11px] text-gold-dark mt-0.5">HAIRLINE BEAUTY LOUNGE ©</p>
            </div>
        </aside>

        <!-- ==================== UNIVERSAL INTERACTIVE BOTTOM SHEET MODAL ==================== -->
        <div id="sheetOverlay" 
             onclick="closeBottomSheet()" 
             class="fixed inset-0 bg-black/55 backdrop-blur-xs z-40 hidden opacity-0 drawer-backdrop">
        </div>

        <div id="bottomSheet" 
             class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] max-h-[88vh] bg-white z-50 rounded-t-[32px] shadow-2xl translate-y-full bottom-sheet flex flex-col overflow-hidden">
            
            <!-- Drag Handle & Top Sheet Bar -->
            <div class="pt-3 pb-3 px-5 bg-sage-light/60 border-b border-gold-soft/40 shrink-0">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <button id="sheetBackBtn" 
                                onclick="handleSheetBack()" 
                                class="hidden w-9 h-9 rounded-full bg-white text-sage-dark shadow-xs flex items-center justify-center hover:bg-sage-dark hover:text-white transition">
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </button>
                        <div>
                            <h3 id="sheetTitle" class="text-base sm:text-lg font-extrabold text-sage-dark">الخدمات المتاحة للحجز</h3>
                            <p id="sheetSubtitle" class="text-xs text-gray-500">خطوات بسيطة لتأكيد موعدكِ</p>
                        </div>
                    </div>

                    <button onclick="closeBottomSheet()" 
                            aria-label="إغلاق النافذة"
                            class="w-9 h-9 rounded-full bg-white text-gray-500 hover:bg-red-50 hover:text-red-500 flex items-center justify-center shadow-xs transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- 3-Step Progress Bar (Shown only during Booking Flow) -->
                <div id="bookingStepper" class="mt-4 pt-3 border-t border-white/60 hidden">
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div id="stepIndicator1" class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full bg-sage-dark mb-1.5 transition-colors"></div>
                            <span class="text-[11px] font-bold text-sage-dark">1. القسم الرئيسي</span>
                        </div>
                        <div id="stepIndicator2" class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full bg-gray-200 mb-1.5 transition-colors"></div>
                            <span class="text-[11px] font-medium text-gray-400">2. الصنف الدقيق</span>
                        </div>
                        <div id="stepIndicator3" class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full bg-gray-200 mb-1.5 transition-colors"></div>
                            <span class="text-[11px] font-medium text-gray-400">3. الأخصائية</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Scrollable Body Inside Bottom Sheet -->
            <div id="sheetBody" class="p-5 overflow-y-auto flex-1 space-y-4">
                <!-- Content injected dynamically by JS -->
            </div>

            <!-- Optional Sticky Footer inside Bottom Sheet -->
            <div id="sheetFooter" class="p-4 bg-white border-t border-gray-100 hidden shrink-0">
                <!-- Action Button injected dynamically by JS -->
            </div>
        </div>

        <!-- Custom Toast Notification Banner (No alert() used) -->
        <div id="toastBanner" class="fixed top-4 left-1/2 -translate-x-1/2 w-[90%] max-w-[420px] bg-sage-dark text-white px-4 py-3 rounded-2xl shadow-xl border border-gold/40 z-50 hidden items-center justify-between gap-3 transition-all">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-gold text-base"></i>
                <span id="toastText" class="text-xs font-bold">تمت العملية بنجاح</span>
            </div>
            <button onclick="hideToast()" class="text-xs text-gold-light hover:text-white">إغلاق</button>
        </div>

    </div>

    <!-- ==================== JAVASCRIPT DATA & INTERACTIVE LOGIC ==================== -->
    <script>
        /* -------------------------------------------------------------------------- */
        /* 1. REALISTIC SALON DATA (Arabic Mock Data for Hairline Beauty Lounge)      */
        /* -------------------------------------------------------------------------- */

        // Hero Banner Slides
        const heroSlides = [
            {
                img: "{{ asset('images/salon/reception-desk.jpg') }}",
                fallback: "https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80",
                title: "أناقتكِ تتجدد",
                subtitle: "أحدث تقنيات المعالجات الحارة والعناية الفائقة بالشعر على أيدي نخبة من الأخصائيات"
            },
            {
                img: "{{ asset('images/salon/hair-styling-chairs.jpg') }}",
                fallback: "https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=1000&q=80",
                title: "لمسة حريرية فاخرة",
                subtitle: "استعيدي حيوية ولمعان شعركِ مع جلسات الكافيار الملكي والبروتين البرازيلي المعالج"
            },
            {
                img: "{{ asset('images/salon/salon-stations-tv.jpg') }}",
                fallback: "https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=1000&q=80",
                title: "جمالكِ يستحق التميز",
                subtitle: "أجواء هادئة وراقية مصممة لراحتكِ وخصوصيتكِ في هيرلاين بيوتي لاونج"
            }
        ];

        // Walk-In Services (جميع الخدمات - زيارة فورية - للاطلاع فقط دون ربطها بالأخصائيات)
        const walkInServicesData = [
            {
                category: "الاستشوار والتصفيف الفوري",
                items: [
                    { name: "استشوار ناعم كلاسيكي", duration: "45 دقيقة", price: "80 ر.س", tag: "زيارة فورية" },
                    { name: "ويفي عريض / ريترو", duration: "60 دقيقة", price: "120 ر.س", tag: "زيارة فورية" },
                    { name: "غسيل شعر مع مساج فروة الرأس بالزيوت العطرية", duration: "25 دقيقة", price: "50 ر.س", tag: "زيارة فورية" }
                ]
            },
            {
                category: "العناية بالأظافر واليدين (سبا فوري)",
                items: [
                    { name: "مانيكير وباديكير سبا هيرلاين الملكي", duration: "60 دقيقة", price: "160 ر.س", tag: "زيارة فورية" },
                    { name: "لون جل شيلك مع تقوية الأظافر", duration: "40 دقيقة", price: "110 ر.س", tag: "زيارة فورية" },
                    { name: "بارافين علاجي لترطيب اليدين والقدمين", duration: "30 دقيقة", price: "90 ر.س", tag: "زيارة فورية" }
                ]
            },
            {
                category: "القص السريع والعناية بالملامح",
                items: [
                    { name: "قص أطراف الشعر المتقصفة (ترتيب صحي)", duration: "30 دقيقة", price: "75 ر.س", tag: "زيارة فورية" },
                    { name: "قص غرة أمامية وتدرج حول الوجه", duration: "20 دقيقة", price: "55 ر.س", tag: "زيارة فورية" },
                    { name: "تشقير وتحديد الحواجب بالخيط", duration: "25 دقيقة", price: "70 ر.س", tag: "زيارة فورية" }
                ]
            },
            {
                category: "ماسكات النضارة السريعة للشعر",
                items: [
                    { name: "ماسك الأرغان المغربي الفوري للمعان", duration: "35 دقيقة", price: "130 ر.س", tag: "زيارة فورية" },
                    { name: "جلسة بخار الأوزون المنعش للشعر", duration: "30 دقيقة", price: "95 ر.س", tag: "زيارة فورية" }
                ]
            }
        ];

        // Specialists Directory (الأخصائيات)
        const specialistsData = {
            "sp_noura": {
                id: "sp_noura",
                name: "الأخصائية نورة العتيبي",
                role: "خبيرة أولى في المعالجات الحارة وفرد الشعر",
                experience: "خبرة 9 سنوات",
                rating: "4.9",
                reviews: 142,
                avatar: "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=300&q=80",
                bio: "معتمدة دولياً في تطبيق البروتين البرازيلي العلاجي وبوتكس الشعر الحراري للشعر المصبوغ والحساس.",
                availableSlots: ["11:30 ص", "02:00 م", "05:30 م", "08:00 م"]
            },
            "sp_reem": {
                id: "sp_reem",
                name: "الأخصائية ريم الحربي",
                role: "أخصائية معالجات الكافيار والكولاجين الذهبي",
                experience: "خبرة 7 سنوات",
                rating: "4.8",
                reviews: 98,
                avatar: "https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=300&q=80",
                bio: "متخصصة في إعادة بناء ألياف الشعر التالف بالمعالجات الحارة والباردة وتقنيات النانو سيراميك.",
                availableSlots: ["12:00 م", "03:30 م", "06:30 م", "08:30 م"]
            },
            "sp_layla": {
                id: "sp_layla",
                name: "الأخصائية ليلى المنصور",
                role: "خبيرة الكيراتين الحيوي والترميم العميق",
                experience: "خبرة 8 سنوات",
                rating: "5.0",
                reviews: 165,
                avatar: "https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?auto=format&fit=crop&w=300&q=80",
                bio: "حاصلة على شهادة التميز في معالجات الشعر الكيرلي والأفرو وتنعيمه مع الحفاظ على كثافته الطبيعية.",
                availableSlots: ["01:00 م", "04:00 م", "07:00 م"]
            },
            "sp_dana": {
                id: "sp_dana",
                name: "الأخصائية دانة السبيعي",
                role: "ماستر كلرست وأخصائية صبغات بالياج",
                experience: "خبرة 10 سنوات",
                rating: "4.9",
                reviews: 210,
                avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=300&q=80",
                bio: "خبيرة دمج الألوان والصبغات الخالية من الأمونيا مع حماية روابط الشعر بتقنية أولابليكس.",
                availableSlots: ["11:00 ص", "02:30 م", "06:00 م"]
            },
            "sp_sara": {
                id: "sp_sara",
                name: "الأخصائية سارة الفيصل",
                role: "أخصائية ديتوكس الفروة والقص الاحترافي",
                experience: "خبرة 6 سنوات",
                rating: "4.8",
                reviews: 87,
                avatar: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80",
                bio: "متخصصة في تشخيص فروة الرأس بالميكروسكوب وجلسات الترميم البارد والقص الفرنسي الحديث.",
                availableSlots: ["12:30 م", "04:30 م", "07:30 م", "09:00 م"]
            }
        };

        // Bookable Categories & Sub-services (الخدمات المتاحة للحجز - 3 خطوات)
        const bookableCategories = [
            {
                id: "hot_treatments",
                title: "المعالجات الحارة",
                badge: "الأكثر طلباً",
                icon: "fa-fire-flame-curved",
                description: "جلسات فرد وتنعيم علاجي بالحرارة الآمنة تمنح شعركِ انسيابية ولمعاناً يدوم طويلاً",
                subServices: [
                    {
                        id: "hot_brazilian_protein",
                        name: "معالج البروتين البرازيلي الملكي (خالي من الفورمالين)",
                        desc: "ينعم الشعر بنسبة 95% ويغذي الألياف بالكيراتين الطبيعي وزيت الأرغان",
                        duration: "150 دقيقة",
                        price: "650 ر.س",
                        specialists: ["sp_noura", "sp_layla"]
                    },
                    {
                        id: "hot_thermal_botox",
                        name: "بوتكس الشعر الحراري المكثف",
                        desc: "مثالي للشعر المجهد والمصبوغ، يعالج النفشة والتقصف ويمنح كثافة حريرية",
                        duration: "120 دقيقة",
                        price: "550 ر.س",
                        specialists: ["sp_noura", "sp_reem"]
                    },
                    {
                        id: "hot_royal_caviar",
                        name: "معالج الكافيار الأسود الحراري الفاخر",
                        desc: "غني بأحماض أوميغا ومضادات الأكسدة لإعادة إحياء الشعر الجاف جداً",
                        duration: "135 دقيقة",
                        price: "720 ر.س",
                        specialists: ["sp_reem", "sp_layla"]
                    },
                    {
                        id: "hot_gold_collagen",
                        name: "معالج الكولاجين الذهبي الحراري (24K Gold)",
                        desc: "يرمم الروابط المتكسرة ويغلف الشعرة بطبقة حماية لامعة ضد الرطوبة",
                        duration: "140 دقيقة",
                        price: "680 ر.س",
                        specialists: ["sp_noura", "sp_reem", "sp_layla"]
                    },
                    {
                        id: "hot_bio_keratin",
                        name: "الكيراتين الحيوي الأمريكي للشعر الكيرلي والأفرو",
                        desc: "معالجة حرارية عميقة تفك التشابك وتمنح مرونة فائقة للشعر الكثيف",
                        duration: "180 دقيقة",
                        price: "790 ر.س",
                        specialists: ["sp_layla", "sp_noura"]
                    }
                ]
            },
            {
                id: "cold_restore",
                title: "المعالجات الباردة والترميم",
                badge: "علاج مكثف",
                icon: "fa-snowflake",
                description: "جلسات ترميم عميقة بدون حرارة لإصلاح الشعر المتضرر من السحب والصبغات",
                subServices: [
                    {
                        id: "cold_olaplex",
                        name: "جلسة أولابليكس (Olaplex) الكاملة لبناء الروابط",
                        desc: "ترميم جزيئي من 4 مراحل يعيد قوة ومرونة الشعر المتهالك",
                        duration: "75 دقيقة",
                        price: "380 ر.س",
                        specialists: ["sp_dana", "sp_sara"]
                    },
                    {
                        id: "cold_amino_filler",
                        name: "فيلر الأحماض الأمينية البارد لتكثيف الشعر",
                        desc: "يملأ الفراغات المسامية داخل الشعرة ويزيد من سماكتها وحيويتها",
                        duration: "60 دقيقة",
                        price: "340 ر.س",
                        specialists: ["sp_reem", "sp_sara"]
                    },
                    {
                        id: "cold_ice_cryo",
                        name: "المعالج الثلجي (Cryotherapy) بجهاز النانو البارد",
                        desc: "يغلق مسام الشعرة ويحبس الرطوبة والفيتامينات داخلها بدرجة حرارة تحت الصفر",
                        duration: "90 دقيقة",
                        price: "490 ر.س",
                        specialists: ["sp_reem", "sp_layla"]
                    }
                ]
            },
            {
                id: "hair_coloring",
                title: "الصبغات وتفتيح الشعر",
                badge: "ألوان عصرية",
                icon: "fa-palette",
                description: "تقنيات بالياج، أومبريه، وصبغات عضوية آمنة مع حماية فائقة للشعر",
                subServices: [
                    {
                        id: "color_balayage",
                        name: "تخصيل بالياج فرنسي مع تونر لمعان",
                        desc: "تموجات لونية طبيعية متدرجة تمنح إشراقة جذابة دون الإضرار بالجذور",
                        duration: "180 دقيقة",
                        price: "750 ر.س",
                        specialists: ["sp_dana"]
                    },
                    {
                        id: "color_full_ammonia_free",
                        name: "صبغة شعر كامل (خالية من الأمونيا) مع أمبولة حماية",
                        desc: "تغطية مثالية للشيب ولون غني يدوم طويلاً مع نعومة فائقة",
                        duration: "110 دقيقة",
                        price: "450 ر.س",
                        specialists: ["sp_dana", "sp_noura"]
                    }
                ]
            },
            {
                id: "scalp_detox",
                title: "العناية الفائقة بفروة الرأس والقص",
                badge: "سبا علاجي",
                icon: "fa-leaf",
                description: "جلسات ديتوكس لتنقية الفروة وتحفيز النمو مع قصات احترافية",
                subServices: [
                    {
                        id: "scalp_japanese_spa",
                        name: "هيدرا سبا وديتوكس الفروة الياباني مع الفحص المجهري",
                        desc: "تنظيف عميق للمسام، إزالة القشرة والترسبات، وتنشيط الدورة الدموية",
                        duration: "70 دقيقة",
                        price: "390 ر.س",
                        specialists: ["sp_sara"]
                    },
                    {
                        id: "cut_signature_restyle",
                        name: "قص نيو لوك احترافي + سشوار وتصفيف هيرلاين",
                        desc: "استشارة مظهر كاملة وقصة تناسب ملامح وجهكِ وكثافة شعركِ",
                        duration: "60 دقيقة",
                        price: "220 ر.س",
                        specialists: ["sp_sara", "sp_dana"]
                    }
                ]
            }
        ];

        /* -------------------------------------------------------------------------- */
        /* 2. STATE MANAGEMENT                                                        */
        /* -------------------------------------------------------------------------- */
        let currentSlideIndex = 0;
        let slideInterval = null;

        let bookingState = {
            step: 1, // 1: Category, 2: Sub-service, 3: Specialist & Time, 4: Success
            selectedCategory: null,
            selectedSubService: null,
            selectedSpecialist: null,
            selectedDate: "غداً - السبت",
            selectedTime: null
        };

        // Initial sample confirmed booking in "حسابي"
        let confirmedBookings = [
            {
                id: "HL-8492",
                categoryTitle: "المعالجات الحارة",
                serviceName: "معالج الكولاجين الذهبي الحراري (24K Gold)",
                specialistName: "الأخصائية نورة العتيبي",
                date: "الأحد القادم",
                time: "05:30 م",
                price: "680 ر.س"
            }
        ];

        /* -------------------------------------------------------------------------- */
        /* 3. HERO SLIDER LOGIC                                                       */
        /* -------------------------------------------------------------------------- */
        function setHeroSlide(index) {
            currentSlideIndex = index;
            const slide = heroSlides[index];
            const imgEl = document.getElementById('heroSlideImg');
            const titleEl = document.getElementById('heroSlideTitle');
            const subtitleEl = document.getElementById('heroSlideSubtitle');
            const indicators = document.getElementById('sliderIndicators').children;

            if (imgEl && titleEl && subtitleEl) {
                imgEl.style.opacity = '0.7';
                setTimeout(() => {
                    imgEl.src = slide.img;
                    imgEl.onerror = () => { imgEl.src = slide.fallback || 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80'; };
                    titleEl.textContent = slide.title;
                    subtitleEl.textContent = slide.subtitle;
                    imgEl.style.opacity = '1';
                }, 150);
            }

            // Update indicators (Gold bar for active, white dot for inactive)
            for (let i = 0; i < indicators.length; i++) {
                if (i === index) {
                    indicators[i].className = "h-2 w-7 rounded-full bg-gold transition-all duration-300";
                } else {
                    indicators[i].className = "h-2 w-2 rounded-full bg-white/70 hover:bg-white transition-all duration-300";
                }
            }
        }

        function startHeroSlider() {
            slideInterval = setInterval(() => {
                const next = (currentSlideIndex + 1) % heroSlides.length;
                setHeroSlide(next);
            }, 5000);
        }

        /* -------------------------------------------------------------------------- */
        /* 4. SIDEBAR & BOTTOM NAVIGATION LOGIC                                       */
        /* -------------------------------------------------------------------------- */
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

        function updateUrl(path) {
            if (window.history && window.history.pushState) {
                window.history.pushState(null, '', path);
            }
        }

        function switchTab(tabName) {
            const homeView = document.getElementById('mainHomeView');
            const aboutView = document.getElementById('aboutTabView');
            const accountView = document.getElementById('accountTabView');

            homeView.classList.add('hidden');
            aboutView.classList.add('hidden');
            accountView.classList.add('hidden');

            if (tabName === 'home') {
                homeView.classList.remove('hidden');
                updateUrl('/');
            }
            if (tabName === 'about') {
                aboutView.classList.remove('hidden');
                updateUrl('/about');
            }
            if (tabName === 'account') {
                renderMyBookings();
                accountView.classList.remove('hidden');
                updateUrl('/profile');
            }

            // Update bottom nav active state
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

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function openSidebarSection(sectionType) {
            toggleSidebar(false);
            const sheetTitle = document.getElementById('sheetTitle');
            const sheetSubtitle = document.getElementById('sheetSubtitle');
            const sheetBackBtn = document.getElementById('sheetBackBtn');
            const bookingStepper = document.getElementById('bookingStepper');
            const sheetBody = document.getElementById('sheetBody');
            const sheetFooter = document.getElementById('sheetFooter');

            sheetBackBtn.classList.add('hidden');
            bookingStepper.classList.add('hidden');
            sheetFooter.classList.add('hidden');

            if (sectionType === 'about') {
                sheetTitle.textContent = "التعريف بالصالون (من نحن)";
                sheetSubtitle.textContent = "هيرلاين | HAIRLINE BEAUTY LOUNGE";
                sheetBody.innerHTML = `
                    <div class="space-y-4 animate-step">
                        <div class="relative h-44 rounded-2xl overflow-hidden">
                            <img src="{{ asset('images/salon/salon-screen-display.jpg') }}" 
                                 alt="صالون هيرلاين" 
                                 onerror="this.src='https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&w=900&q=80'"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-sage-dark/90 to-transparent flex items-end p-4">
                                <span class="text-white font-bold text-sm">الفخامة والعناية المتخصصة تحت سقف واحد</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            تأسس صالون <strong class="text-sage-dark">هيرلاين بيوتي لاونج</strong> ليكون الملاذ الأول لكل سيدة تبحث عن التميز في العناية بشعرها وجمالها. نعتمد في كافة جلساتنا على تشخيص دقيق لطبيعة الشعر قبل البدء بأي معالج حراري أو بارد.
                        </p>
                        <div class="bg-sage-soft p-4 rounded-2xl border border-sage-light space-y-2">
                            <h4 class="font-extrabold text-xs text-sage-dark flex items-center gap-2">
                                <i class="fa-solid fa-star text-gold"></i>
                                <span>لماذا تختارين هيرلاين؟</span>
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1.5 pr-4 list-disc">
                                <li>منتجات برازيلية وأوروبية أصلية 100% آمنة وخالية من الفورمالين.</li>
                                <li>فريق أخصائيات معتمدات بخبرات تتجاوز 10 سنوات.</li>
                                <li>أقسام مجهزة بأعلى معايير التعقيم والخصوصية التامة.</li>
                            </ul>
                        </div>
                    </div>
                `;
            } else if (sectionType === 'specialists') {
                sheetTitle.textContent = "الأخصائيات";
                sheetSubtitle.textContent = "نخبة خبيرات الشعر والمعالجات في هيرلاين";
                const allSpecs = Object.values(specialistsData);
                sheetBody.innerHTML = `
                    <div class="space-y-3 animate-step">
                        ${allSpecs.map(sp => `
                            <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <img src="${sp.avatar}" 
                                             alt="${sp.name}"
                                             onerror="this.src='https://placehold.co/120x120/1F3A2E/F6EFE2?text=HL'"
                                             class="w-14 h-14 rounded-full object-cover border-2 border-gold">
                                        <div>
                                            <h4 class="font-extrabold text-sm text-sage-dark">${sp.name}</h4>
                                            <p class="text-xs text-gold-dark font-bold">${sp.role}</p>
                                            <span class="inline-block mt-1 text-[11px] text-gray-500 bg-white px-2 py-0.5 rounded-full border border-gray-200">
                                                ${sp.experience} • ★ ${sp.rating}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed">${sp.bio}</p>
                                <button onclick="openBookingFlow()" class="w-full py-2.5 rounded-xl bg-sage-dark text-white text-xs font-bold hover:bg-sage-deeper transition">
                                    احجزي موعداً مع ${sp.name.split(' ')[1]}
                                </button>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else if (sectionType === 'contact') {
                sheetTitle.textContent = "التواصل (اتصل بنا)";
                sheetSubtitle.textContent = "نسعد بخدمتكِ والرد على استفساراتكِ";
                sheetBody.innerHTML = `
                    <div class="space-y-3.5 animate-step">
                        <div class="p-4 rounded-2xl bg-sage-soft border border-sage-light flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-sage-dark text-gold flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-clock text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-sage-dark">أوقات العمل الرسمية</h4>
                                <p class="text-xs text-gray-600 mt-0.5">يومياً من الساعة 10:00 صباحاً حتى 10:00 مساءً</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-gold-light text-gold-dark flex items-center justify-center">
                                    <i class="fa-solid fa-phone text-base"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-sm text-sage-dark">الرقم الموحد للحجوزات</h4>
                                    <p class="text-xs text-gray-500 dir-ltr text-right">054 885 7705</p>
                                </div>
                            </div>
                            <a href="tel:0548857705" class="px-3 py-1.5 rounded-xl bg-sage-dark text-white text-xs font-bold">
                                اتصال
                            </a>
                        </div>

                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i class="fa-brands fa-whatsapp text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-sm text-sage-dark">خدمة عميلات واتساب</h4>
                                    <p class="text-xs text-gray-500">استشارة مجانية للشعر قبل الحجز</p>
                                </div>
                            </div>
                            <a href="https://wa.me/966548857705" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                                مراسلة
                            </a>
                        </div>

                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gold-light text-gold-dark flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-map-location-dot text-base"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-sage-dark">العنوان</h4>
                                <p class="text-xs text-gray-600">الرياض - حي الملقا، طريق أنس بن مالك (هيرلاين بيوتي لاونج)</p>
                            </div>
                        </div>
                    </div>
                `;
            }

            showBottomSheet();
        }

        /* -------------------------------------------------------------------------- */
        /* 5. ALL SERVICES (WALK-IN CATALOG - زيارة فورية للاطلاع فقط)                 */
        /* -------------------------------------------------------------------------- */
        function openAllServicesModal() {
            updateUrl('/services');
            const sheetTitle = document.getElementById('sheetTitle');
            const sheetSubtitle = document.getElementById('sheetSubtitle');
            const sheetBackBtn = document.getElementById('sheetBackBtn');
            const bookingStepper = document.getElementById('bookingStepper');
            const sheetBody = document.getElementById('sheetBody');
            const sheetFooter = document.getElementById('sheetFooter');

            sheetTitle.textContent = "جميع الخدمات (زيارة فورية)";
            sheetSubtitle.textContent = "قائمة الخدمات العامة المتاحة بالحضور المباشر دون اشتراط حجز أخصائية";
            sheetBackBtn.classList.add('hidden');
            bookingStepper.classList.add('hidden');

            sheetBody.innerHTML = `
                <div class="space-y-5 animate-step">
                    <!-- Informative Banner -->
                    <div class="p-3.5 rounded-2xl bg-gold-light/80 border border-gold/40 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gold text-white flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-circle-info text-sm"></i>
                        </div>
                        <p class="text-xs text-sage-dark font-medium leading-relaxed">
                            هذه القائمة مخصصة <strong class="font-extrabold">للاطلاع والزيارة الفورية المباشرة</strong>. أما إذا كنتِ ترغبين بحجز موعد محدد مع اختيار الأخصائية (مثل المعالجات الحارة)، يمكنكِ الانتقال إلى قسم الحجز.
                        </p>
                    </div>

                    ${walkInServicesData.map(group => `
                        <div class="space-y-2.5">
                            <h4 class="font-extrabold text-sm text-sage-dark flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gold"></span>
                                <span>${group.category}</span>
                            </h4>
                            <div class="space-y-2">
                                ${group.items.map(item => `
                                    <div class="p-3.5 rounded-2xl bg-surface border border-gray-100 flex items-center justify-between">
                                        <div>
                                            <h5 class="font-bold text-xs sm:text-sm text-sage-dark">${item.name}</h5>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[11px] text-gray-500">
                                                    <i class="fa-regular fa-clock ml-1 text-gold-dark"></i>${item.duration}
                                                </span>
                                                <span class="px-2 py-0.5 rounded-full bg-sage-soft text-sage-dark text-[10px] font-bold">
                                                    ${item.tag}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="font-extrabold text-sm text-gold-dark bg-gold-light px-3 py-1.5 rounded-xl border border-gold/20 shrink-0">
                                            ${item.price}
                                        </span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;

            sheetFooter.classList.remove('hidden');
            sheetFooter.innerHTML = `
                <button onclick="openBookingFlow()" class="w-full py-3.5 rounded-2xl bg-sage-dark text-white font-extrabold text-sm hover:bg-sage-deeper transition flex items-center justify-center gap-2 shadow-md">
                    <i class="fa-regular fa-calendar-check text-gold"></i>
                    <span>الانتقال إلى الخدمات المتاحة للحجز (مع الأخصائيات)</span>
                </button>
            `;

            showBottomSheet();
        }

        /* -------------------------------------------------------------------------- */
        /* 6. INTERACTIVE 3-STEP BOOKING FLOW (الخدمات المتاحة للحجز)                  */
        /* -------------------------------------------------------------------------- */
        function openBookingFlow() {
            updateUrl('/booking');
            bookingState = {
                step: 1,
                selectedCategory: null,
                selectedSubService: null,
                selectedSpecialist: null,
                selectedDate: "غداً - السبت",
                selectedTime: null
            };
            renderBookingStep();
            showBottomSheet();
        }

        function openBookingFlowWithCategory(categoryId) {
            updateUrl('/booking');
            const cat = bookableCategories.find(c => c.id === categoryId);
            if (!cat) {
                openBookingFlow();
                return;
            }
            bookingState = {
                step: 2,
                selectedCategory: cat,
                selectedSubService: null,
                selectedSpecialist: null,
                selectedDate: "غداً - السبت",
                selectedTime: null
            };
            renderBookingStep();
            showBottomSheet();
        }

        function handleSheetBack() {
            if (bookingState.step === 3) {
                bookingState.step = 2;
                bookingState.selectedSpecialist = null;
                bookingState.selectedTime = null;
                renderBookingStep();
            } else if (bookingState.step === 2) {
                bookingState.step = 1;
                bookingState.selectedCategory = null;
                bookingState.selectedSubService = null;
                renderBookingStep();
            }
        }

        function updateStepperUI(step) {
            const bookingStepper = document.getElementById('bookingStepper');
            const sheetBackBtn = document.getElementById('sheetBackBtn');

            if (step >= 1 && step <= 3) {
                bookingStepper.classList.remove('hidden');
            } else {
                bookingStepper.classList.add('hidden');
            }

            if (step === 2 || step === 3) {
                sheetBackBtn.classList.remove('hidden');
            } else {
                sheetBackBtn.classList.add('hidden');
            }

            for (let i = 1; i <= 3; i++) {
                const el = document.getElementById(`stepIndicator${i}`);
                if (!el) continue;
                const bar = el.children[0];
                const label = el.children[1];

                if (i < step) {
                    bar.className = "w-full h-1.5 rounded-full bg-gold mb-1.5 transition-colors";
                    label.className = "text-[11px] font-bold text-gold-dark";
                } else if (i === step) {
                    bar.className = "w-full h-1.5 rounded-full bg-sage-dark mb-1.5 transition-colors";
                    label.className = "text-[11px] font-extrabold text-sage-dark";
                } else {
                    bar.className = "w-full h-1.5 rounded-full bg-gray-200 mb-1.5 transition-colors";
                    label.className = "text-[11px] font-medium text-gray-400";
                }
            }
        }

        function renderBookingStep() {
            const sheetTitle = document.getElementById('sheetTitle');
            const sheetSubtitle = document.getElementById('sheetSubtitle');
            const sheetBody = document.getElementById('sheetBody');
            const sheetFooter = document.getElementById('sheetFooter');

            updateStepperUI(bookingState.step);

            // ---------------- STEP 1: اختيار القسم الرئيسي ----------------
            if (bookingState.step === 1) {
                sheetTitle.textContent = "الخطوة 1: اختيار القسم الرئيسي";
                sheetSubtitle.textContent = "اختاري القسم لعرض الأصناف الدقيقة والأخصائيات";
                sheetFooter.classList.add('hidden');

                sheetBody.innerHTML = `
                    <div class="space-y-3 animate-step">
                        ${bookableCategories.map(cat => `
                            <button onclick="selectCategory('${cat.id}')" 
                                    class="w-full p-4 rounded-2xl bg-surface hover:bg-sage-soft/70 border border-gold-soft/50 hover:border-sage-dark transition-all flex items-center justify-between text-right group">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-12 h-12 rounded-2xl bg-sage-dark text-gold group-hover:scale-105 transition-transform flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="fa-solid ${cat.icon} text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-extrabold text-base text-sage-dark">${cat.title}</h4>
                                            <span class="px-2 py-0.5 rounded-full bg-gold-light text-gold-dark text-[10px] font-bold border border-gold/30">
                                                ${cat.badge}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">${cat.description}</p>
                                        <span class="inline-block mt-2 text-[11px] font-bold text-sage-dark">
                                            ${cat.subServices.length} أصناف متاحة للحجز
                                        </span>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-white text-sage-dark group-hover:bg-sage-dark group-hover:text-white flex items-center justify-center shrink-0 shadow-xs transition">
                                    <span class="text-lg font-bold leading-none -mt-0.5">‹</span>
                                </div>
                            </button>
                        `).join('')}
                    </div>
                `;
            }

            // ---------------- STEP 2: تحديد الصنف الدقيق ----------------
            else if (bookingState.step === 2) {
                const cat = bookingState.selectedCategory;
                sheetTitle.textContent = `الخطوة 2: ${cat.title}`;
                sheetSubtitle.textContent = "حددي الخدمة الدقيقة التي ترغبين بحجزها";
                sheetFooter.classList.add('hidden');

                sheetBody.innerHTML = `
                    <div class="space-y-3 animate-step">
                        <div class="px-3.5 py-2.5 rounded-xl bg-sage-soft border border-sage-light flex items-center justify-between">
                            <span class="text-xs font-bold text-sage-dark">القسم المختار: ${cat.title}</span>
                            <button onclick="handleSheetBack()" class="text-xs font-bold text-gold-dark underline">تغيير القسم</button>
                        </div>

                        ${cat.subServices.map(sub => `
                            <button onclick="selectSubService('${sub.id}')" 
                                    class="w-full p-4 rounded-2xl bg-surface hover:bg-gold-light/40 border border-gray-200 hover:border-gold transition-all text-right flex flex-col gap-2.5 group">
                                <div class="flex items-start justify-between gap-2 w-full">
                                    <h4 class="font-extrabold text-sm sm:text-base text-sage-dark group-hover:text-sage-deeper">
                                        ${sub.name}
                                    </h4>
                                    <span class="px-3 py-1 rounded-xl bg-sage-dark text-gold-light font-extrabold text-xs shrink-0">
                                        ${sub.price}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 leading-relaxed">${sub.desc}</p>
                                <div class="pt-2 border-t border-gray-200/70 flex items-center justify-between w-full text-[11px]">
                                    <span class="text-gray-500 font-medium">
                                        <i class="fa-regular fa-clock ml-1 text-gold-dark"></i>المدة: ${sub.duration}
                                    </span>
                                    <span class="font-extrabold text-sage-dark flex items-center gap-1">
                                        <span>اختيار الأخصائية (${sub.specialists.length} متاحات)</span>
                                        <span class="text-base leading-none">‹</span>
                                    </span>
                                </div>
                            </button>
                        `).join('')}
                    </div>
                `;
            }

            // ---------------- STEP 3: اختيار الأخصائية وتأكيد الحجز ----------------
            else if (bookingState.step === 3) {
                const sub = bookingState.selectedSubService;
                sheetTitle.textContent = "الخطوة 3: اختيار الأخصائية";
                sheetSubtitle.textContent = "الأخصائيات المؤهلات لتقديم هذه الخدمة تحديداً";

                const linkedSpecialists = sub.specialists.map(id => specialistsData[id]).filter(Boolean);
                if (!bookingState.selectedSpecialist && linkedSpecialists.length > 0) {
                    bookingState.selectedSpecialist = linkedSpecialists[0];
                    bookingState.selectedTime = linkedSpecialists[0].availableSlots[0];
                }

                const datesList = ["اليوم - الجمعة", "غداً - السبت", "الأحد القادم", "الإثنين القادم"];

                sheetBody.innerHTML = `
                    <div class="space-y-4 animate-step">
                        <!-- Summary of Chosen Service -->
                        <div class="p-3.5 rounded-2xl bg-gold-light/70 border border-gold/40 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-gold-dark block">${bookingState.selectedCategory.title}</span>
                                <h4 class="font-extrabold text-xs sm:text-sm text-sage-dark mt-0.5">${sub.name}</h4>
                            </div>
                            <div class="text-left shrink-0">
                                <span class="font-black text-sm text-sage-dark block">${sub.price}</span>
                                <span class="text-[10px] text-gray-500">${sub.duration}</span>
                            </div>
                        </div>

                        <!-- Qualified Specialists List -->
                        <div class="space-y-2.5">
                            <label class="block text-xs font-extrabold text-sage-dark">
                                اختاري الأخصائية المفضلة لديكِ (${linkedSpecialists.length} أخصائيات معتمدات لهذه الخدمة):
                            </label>
                            ${linkedSpecialists.map(sp => {
                                const isSelected = bookingState.selectedSpecialist && bookingState.selectedSpecialist.id === sp.id;
                                return `
                                    <div onclick="chooseSpecialist('${sp.id}')" 
                                         class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between ${
                                             isSelected 
                                             ? 'bg-sage-soft/90 border-sage-dark shadow-xs' 
                                             : 'bg-surface border-transparent hover:border-gold-soft'
                                         }">
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <img src="${sp.avatar}" 
                                                     alt="${sp.name}"
                                                     onerror="this.src='https://placehold.co/100x100/1F3A2E/F6EFE2?text=HL'"
                                                     class="w-13 h-13 rounded-full object-cover border-2 ${isSelected ? 'border-sage-dark' : 'border-gold'}">
                                                ${isSelected ? `
                                                    <span class="w-5 h-5 rounded-full bg-sage-dark text-gold text-[10px] flex items-center justify-center absolute -bottom-1 -right-1 ring-2 ring-white">
                                                        <i class="fa-solid fa-check"></i>
                                                    </span>
                                                ` : ''}
                                            </div>
                                            <div>
                                                <h5 class="font-extrabold text-sm text-sage-dark">${sp.name}</h5>
                                                <p class="text-[11px] text-gray-600">${sp.role}</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] font-bold text-gold-dark bg-white px-2 py-0.5 rounded-md border border-gold/20">
                                                        ★ ${sp.rating} (${sp.reviews} تقييم)
                                                    </span>
                                                    <span class="text-[10px] text-gray-500">${sp.experience}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center ${
                                            isSelected ? 'border-sage-dark bg-sage-dark text-white' : 'border-gray-300'
                                        }">
                                            ${isSelected ? '<i class="fa-solid fa-check text-[10px]"></i>' : ''}
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>

                        <!-- Date & Time Picker for Selected Specialist -->
                        <div class="space-y-3 pt-2 border-t border-gray-100">
                            <label class="block text-xs font-extrabold text-sage-dark">حددي اليوم والوقت المتاح مع ${bookingState.selectedSpecialist.name}:</label>
                            
                            <!-- Days Pills -->
                            <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                                ${datesList.map(d => `
                                    <button onclick="selectBookingDate('${d}')" 
                                            class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 border transition ${
                                                bookingState.selectedDate === d 
                                                ? 'bg-sage-dark text-white border-sage-dark' 
                                                : 'bg-surface text-gray-600 border-gray-200 hover:border-gold'
                                            }">
                                        ${d}
                                    </button>
                                `).join('')}
                            </div>

                            <!-- Time Slots Grid -->
                            <div class="grid grid-cols-4 gap-2">
                                ${bookingState.selectedSpecialist.availableSlots.map(slot => `
                                    <button onclick="selectBookingTime('${slot}')" 
                                            class="py-2 rounded-xl text-xs font-bold border transition ${
                                                bookingState.selectedTime === slot 
                                                ? 'bg-gold text-white border-gold shadow-xs' 
                                                : 'bg-white text-sage-dark border-gray-200 hover:border-sage-dark'
                                            }">
                                        ${slot}
                                    </button>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                `;

                sheetFooter.classList.remove('hidden');
                sheetFooter.innerHTML = `
                    <button onclick="confirmFinalBooking()" 
                            class="w-full py-4 rounded-2xl bg-sage-dark hover:bg-sage-deeper text-white font-extrabold text-sm shadow-lg transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check text-gold"></i>
                        <span>تأكيد الحجز مع ${bookingState.selectedSpecialist.name}</span>
                    </button>
                `;
            }

            // ---------------- STEP 4: شاشة تأكيد الحجز الاحتفالية ----------------
            else if (bookingState.step === 4) {
                sheetTitle.textContent = "تم تأكيد حجزكِ بنجاح";
                sheetSubtitle.textContent = "نتطلع لاستقبالكِ في هيرلاين بيوتي لاونج";
                sheetFooter.classList.add('hidden');

                const lastBooking = confirmedBookings[0];

                sheetBody.innerHTML = `
                    <div class="text-center py-4 space-y-4 animate-step">
                        <div class="w-20 h-20 rounded-full bg-sage-light border-4 border-gold mx-auto flex items-center justify-center text-sage-dark text-3xl shadow-md">
                            <i class="fa-solid fa-check text-sage-dark"></i>
                        </div>

                        <div>
                            <span class="px-3 py-1 rounded-full bg-gold-light text-gold-dark text-xs font-bold">
                                رقم الحجز #${lastBooking.id}
                            </span>
                            <h3 class="text-xl font-black text-sage-dark mt-2">موعدكِ مؤكد يا جميلة!</h3>
                            <p class="text-xs text-gray-500 mt-1">تم إرسال تفاصيل الموعد إلى حسابكِ</p>
                        </div>

                        <!-- Receipt Card -->
                        <div class="bg-surface rounded-2xl p-4 border border-gold-soft text-right space-y-2.5 text-xs">
                            <div class="flex justify-between py-1 border-b border-gray-200/60">
                                <span class="text-gray-500">القسم الرئيسي:</span>
                                <span class="font-bold text-sage-dark">${lastBooking.categoryTitle}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-200/60">
                                <span class="text-gray-500">الخدمة المختارة:</span>
                                <span class="font-extrabold text-sage-dark max-w-[200px] text-left">${lastBooking.serviceName}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-200/60">
                                <span class="text-gray-500">الأخصائية:</span>
                                <span class="font-extrabold text-gold-dark">${lastBooking.specialistName}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-200/60">
                                <span class="text-gray-500">الموعد:</span>
                                <span class="font-bold text-sage-dark">${lastBooking.date} • الساعة ${lastBooking.time}</span>
                            </div>
                            <div class="flex justify-between pt-1">
                                <span class="text-gray-500 font-bold">التكلفة التقديرية:</span>
                                <span class="font-black text-sm text-sage-dark">${lastBooking.price}</span>
                            </div>
                        </div>

                        <div class="flex gap-2.5 pt-2">
                            <button onclick="closeBottomSheet(); switchTab('account');" 
                                    class="flex-1 py-3.5 rounded-2xl bg-sage-dark text-white font-extrabold text-xs hover:bg-sage-deeper transition">
                                عرض في حجوزاتي
                            </button>
                            <button onclick="closeBottomSheet()" 
                                    class="flex-1 py-3.5 rounded-2xl bg-gold-light text-gold-dark font-extrabold text-xs hover:bg-gold hover:text-white transition">
                                العودة للرئيسية
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        function selectCategory(catId) {
            const found = bookableCategories.find(c => c.id === catId);
            if (!found) return;
            bookingState.selectedCategory = found;
            bookingState.step = 2;
            renderBookingStep();
        }

        function selectSubService(subId) {
            if (!bookingState.selectedCategory) return;
            const found = bookingState.selectedCategory.subServices.find(s => s.id === subId);
            if (!found) return;
            bookingState.selectedSubService = found;
            bookingState.selectedSpecialist = null;
            bookingState.selectedTime = null;
            bookingState.step = 3;
            renderBookingStep();
        }

        function chooseSpecialist(spId) {
            const sp = specialistsData[spId];
            if (!sp) return;
            bookingState.selectedSpecialist = sp;
            bookingState.selectedTime = sp.availableSlots[0];
            renderBookingStep();
        }

        function selectBookingDate(dateStr) {
            bookingState.selectedDate = dateStr;
            renderBookingStep();
        }

        function selectBookingTime(timeStr) {
            bookingState.selectedTime = timeStr;
            renderBookingStep();
        }

        function confirmFinalBooking() {
            if (!bookingState.selectedCategory || !bookingState.selectedSubService || !bookingState.selectedSpecialist) return;

            const newBooking = {
                id: "HL-" + Math.floor(1000 + Math.random() * 9000),
                categoryTitle: bookingState.selectedCategory.title,
                serviceName: bookingState.selectedSubService.name,
                specialistName: bookingState.selectedSpecialist.name,
                date: bookingState.selectedDate,
                time: bookingState.selectedTime || bookingState.selectedSpecialist.availableSlots[0],
                price: bookingState.selectedSubService.price
            };

            confirmedBookings.unshift(newBooking);
            updateBookingBadge();
            bookingState.step = 4;
            renderBookingStep();
        }

        function updateBookingBadge() {
            const badge = document.getElementById('bookingCountBadge');
            if (!badge) return;
            badge.textContent = confirmedBookings.length;
            badge.classList.remove('hidden');
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
                            مؤكد • #${b.id}
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
            });
        }

        function closeBottomSheet() {
            const overlay = document.getElementById('sheetOverlay');
            const sheet = document.getElementById('bottomSheet');
            overlay.classList.add('opacity-0');
            sheet.classList.add('translate-y-full');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 350);
            updateUrl('/');
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

        // Initialize on Window Load
        window.onload = function () {
            startHeroSlider();
            updateBookingBadge();
            renderMyBookings();

            const initial = "{{ $initialView ?? 'home' }}";
            if (initial === 'about') {
                switchTab('about');
            } else if (initial === 'account') {
                switchTab('account');
            } else if (initial === 'services') {
                openAllServicesModal();
            } else if (initial === 'booking') {
                openBookingFlow();
            } else {
                switchTab('home');
            }
        };
    </script>
</body>
</html>
