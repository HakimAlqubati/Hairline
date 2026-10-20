<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
    <meta name="theme-color" content="#1F3A2E">
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
                            light: '#DCE7E0',
                            soft: '#EDF3EF',
                            medium: '#8FA998',
                            dark: '#1F3A2E',
                            deeper: '#162920'
                        },
                        gold: {
                            light: '#F6EFE2',
                            soft: '#E6D5B8',
                            DEFAULT: '{{ $brandPrimaryColor }}',
                            dark: '#A3803B'
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
            background-color: #E8EFEA;
            color: #1F3A2E;
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
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
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
        body.simulated-fullscreen {
            background-color: #F7F8F7 !important;
        }
        body.simulated-fullscreen #appContainer {
            max-width: 100% !important;
            box-shadow: none !important;
        }
    </style>
</head>

<body class="min-h-screen flex justify-center bg-[#E4ECE7] selection:bg-sage-dark selection:text-white">

    <!-- Mobile App Container (Responsive: full screen on mobile, sleek phone frame on desktop) -->
    <div id="appContainer" class="w-full max-w-[480px] min-h-screen bg-surface relative flex flex-col shadow-2xl overflow-hidden pb-28">

        <!-- 1. TOP HEADER COMPONENT -->
        <x-header :salonIdentity="$salonIdentity" :workingHours="$workingHours" />

        <!-- 2. MAIN HOME VIEW -->
        <main id="mainHomeView" class="flex-1 px-4 pt-5 space-y-5">
            <!-- Main Service Rows (Strictly 2 Wide Rows) -->
            <x-service-rows />

            <!-- Hero Promotional Slider -->
            <x-hero-slider :heroSlides="$heroSlides" />
        </main>

        <!-- 3. DYNAMIC TAB VIEWS -->
        <x-tabs.services :menuPages="$menuPages ?? []" />
        <x-tabs.about :salonIdentity="$salonIdentity" :stats="$stats" />
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

        /* -------------------------------------------------------------------------- */
        /* STATE MANAGEMENT                                                           */
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

        // Confirmed bookings in "حسابي"
        let confirmedBookings = [
            {
                id: "482",
                categoryTitle: "المعالجات الحارة",
                serviceName: "البروتين بالتمليس الحراري",
                specialistName: "الأخصائية نورة العتيبي",
                date: "الأحد القادم",
                time: "05:30 م",
                price: "750 ر.س"
            }
        ];

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
            const homeView = document.getElementById('mainHomeView');
            const aboutView = document.getElementById('aboutTabView');
            const accountView = document.getElementById('accountTabView');
            const servicesView = document.getElementById('servicesTabView');

            if (homeView) homeView.classList.add('hidden');
            if (aboutView) aboutView.classList.add('hidden');
            if (accountView) accountView.classList.add('hidden');
            if (servicesView) servicesView.classList.add('hidden');

            if (tabName === 'home') {
                if (homeView) homeView.classList.remove('hidden');
                updateUrl('/');
            }
            if (tabName === 'services') {
                if (servicesView) servicesView.classList.remove('hidden');
                updateUrl('/services');
            }
            if (tabName === 'about') {
                if (aboutView) aboutView.classList.remove('hidden');
                updateUrl('/about');
            }
            if (tabName === 'account') {
                renderMyBookings();
                if (accountView) accountView.classList.remove('hidden');
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

        /* -------------------------------------------------------------------------- */
        /* SIDEBAR SECTIONS                                                           */
        /* -------------------------------------------------------------------------- */
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
                                                ${sp.experience}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed">${sp.bio}</p>
                            </div>
                        `).join('')}
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

                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex items-center justify-between">
                            <div>
                                <h4 class="font-extrabold text-sm text-sage-dark">الرقم الموحد للحجوزات</h4>
                                <p class="text-xs text-gray-500 dir-ltr text-right mt-0.5">0548857705</p>
                            </div>
                            <a href="tel:0548857705" class="px-4 py-2 rounded-xl bg-sage-dark text-white text-xs font-bold hover:bg-sage-deeper transition">
                                اتصال
                            </a>
                        </div>

                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 flex items-center justify-between">
                            <div>
                                <h4 class="font-extrabold text-sm text-sage-dark">خدمة عميلات واتساب</h4>
                                <p class="text-xs text-gray-500 mt-0.5">استشارة مجانية للشعر قبل الحجز</p>
                            </div>
                            <a href="https://wa.me/966548857705" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition">
                                مراسلة
                            </a>
                        </div>

                        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/50 space-y-2">
                            <h4 class="font-extrabold text-sm text-sage-dark">العنوان</h4>
                            <p class="text-xs text-gray-700 font-medium leading-relaxed">{{ $location['full_address_ar'] ?? 'الرياض - حي المصيف، مخرج 5' }}</p>
                            <a href="{{ $location['google_maps_url'] ?? 'https://maps.google.com/?q=Al+Maseef+Riyadh+Exit+5' }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-gold-dark hover:underline pt-1">
                                <span>عرض الموقع على خرائط جوجل (Google Maps)</span>
                                <span class="text-[10px]">↗</span>
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
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
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
        /* 3-STEP BOOKING FLOW (من ملف الـ PDF)                                        */
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

            // STEP 1: اختيار القسم
            if (bookingState.step === 1) {
                sheetTitle.textContent = "الخطوة 1: اختيار القسم الرئيسي";
                sheetSubtitle.textContent = "اختاري القسم لعرض الأصناف الدقيقة والأخصائيات";
                sheetFooter.classList.add('hidden');

                sheetBody.innerHTML = `
                    <div class="space-y-3 animate-step">
                        ${bookableCategories.map(cat => `
                            <button onclick="selectCategory('${cat.id}')" 
                                    class="w-full p-4 rounded-2xl bg-surface hover:bg-sage-soft/70 border border-gold-soft/50 hover:border-sage-dark transition-all flex items-center justify-between text-right group">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-extrabold text-base text-sage-dark">${cat.title}</h4>
                                        <span class="px-2 py-0.5 rounded-full bg-gold-light text-gold-dark text-[10px] font-bold border border-gold/30">
                                            ${cat.badge}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed">${cat.description}</p>
                                    <span class="inline-block pt-1 text-[11px] font-bold text-sage-dark">
                                        ${cat.subServices.length} أصناف معتمدة للحجز
                                    </span>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-white text-sage-dark group-hover:bg-sage-dark group-hover:text-white flex items-center justify-center shrink-0 shadow-xs transition mr-2">
                                    <span class="text-lg font-bold leading-none -mt-0.5">‹</span>
                                </div>
                            </button>
                        `).join('')}
                    </div>
                `;
            }

            // STEP 2: تحديد الخدمة الدقيقة
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

            // STEP 3: اختيار الأخصائية وتحديد الوقت
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
                        <!-- Summary Card -->
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

                        <!-- Specialists List -->
                        <div class="space-y-2.5">
                            <label class="block text-xs font-extrabold text-sage-dark">
                                اختاري الأخصائية (${linkedSpecialists.length} أخصائيات معتمدات):
                            </label>
                            ${linkedSpecialists.map(sp => {
                                const isSelected = bookingState.selectedSpecialist && bookingState.selectedSpecialist.id === sp.id;
                                return `
                                    <div onclick="chooseSpecialist('${sp.id}')" 
                                         class="p-3.5 rounded-2xl border transition-all duration-200 cursor-pointer flex items-center justify-between gap-3 ${
                                             isSelected 
                                             ? 'bg-sage-soft/90 border-sage-dark shadow-xs ring-1 ring-sage-dark/20' 
                                             : 'bg-white border-gray-200/80 hover:border-gold-soft hover:bg-surface'
                                         }">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="relative w-11 h-11 shrink-0" style="width: 44px; height: 44px;">
                                                <img src="${sp.avatar}" 
                                                     alt="${sp.name}"
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(sp.name)}&background=1F3A2E&color=C29E6D&bold=true&size=96';"
                                                     class="w-11 h-11 rounded-full object-cover border ${isSelected ? 'border-sage-dark ring-2 ring-sage-dark/15' : 'border-gold/30'} shrink-0 shadow-2xs"
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
                                            ? 'border-sage-dark bg-sage-dark text-white shadow-xs' 
                                            : 'border-gray-300 bg-white'
                                        }">
                                            ${isSelected ? '<i class="fa-solid fa-check text-[10px]"></i>' : ''}
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>

                        <!-- Date & Time Picker -->
                        <div class="space-y-3 pt-2 border-t border-gray-100">
                            <label class="block text-xs font-extrabold text-sage-dark">الوقت واليوم المتاح مع ${bookingState.selectedSpecialist.name}:</label>
                            
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

            // STEP 4: شاشة التأكيد
            else if (bookingState.step === 4) {
                const lastBooking = confirmedBookings[0];
                sheetTitle.textContent = "تم تأكيد حجزك بنجاح";
                sheetSubtitle.textContent = "رقم الحجز: " + lastBooking.id;
                sheetFooter.classList.add('hidden');

                sheetBody.innerHTML = `
                    <div class="text-center py-4 space-y-4 animate-step">
                        <div class="w-16 h-16 rounded-full bg-sage-light border-2 border-primary mx-auto flex items-center justify-center text-primary text-2xl shadow-sm">
                            <i class="fa-solid fa-check"></i>
                        </div>

                        <div>
                            <h3 class="text-xl font-black text-sage-dark">تم تأكيد حجزك بنجاح</h3>
                            <div class="mt-2 inline-block px-4 py-1.5 rounded-full bg-gold-light text-gold-dark text-xs font-black">
                                رقم الحجز: ${lastBooking.id}
                            </div>
                        </div>

                        <!-- Receipt Card: فقط العناصر الثلاثة المطلوبة بدون تفاصيل أخرى -->
                        <div class="bg-surface rounded-2xl p-4 border border-gold-soft text-right space-y-3">
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
                            <button onclick="closeBottomSheet(); switchTab('account');" 
                                    class="flex-1 py-3.5 rounded-2xl bg-sage-dark text-white font-extrabold text-xs hover:bg-sage-deeper transition">
                                  حجوزاتي
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
                id: String(Math.floor(100 + Math.random() * 900)),
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
            } else if (initial === 'booking') {
                openBookingFlow();
            } else {
                switchTab('home');
            }
        };
    </script>
</body>
</html>
