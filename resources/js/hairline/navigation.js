import { state, salonIdentity, workingHours, location } from './state.js';
import { showBottomSheet, closeBottomSheet } from './ui-modals.js';
import { renderCustomerProfile, renderMyBookings } from './profile.js';
import { syncFloatingConfirmButton } from './booking-wizard.js';

export function updateUrl(path) {
    if (window.history && window.history.pushState) {
        window.history.pushState(null, '', path);
    }
}

let sidebarCloseTimer = null;
let isSidebarActive = false;

export function toggleSidebar(show) {
    const overlay = document.getElementById('sidebarOverlay');
    const drawer = document.getElementById('sidebarDrawer');
    if (!overlay || !drawer) return;

    if (sidebarCloseTimer) {
        clearTimeout(sidebarCloseTimer);
        sidebarCloseTimer = null;
    }

    if (show) {
        isSidebarActive = true;

        // Prevent body background scroll on mobile/desktop while open
        document.body.classList.add('overflow-hidden');

        // Reveal elements in DOM
        overlay.classList.remove('hidden');
        drawer.classList.remove('hidden');
        drawer.setAttribute('aria-hidden', 'false');

        // Smooth transition trigger without forced layout thrashing
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                overlay.classList.add('is-active');
                drawer.classList.add('sidebar-open');
            });
        });
    } else {
        isSidebarActive = false;

        // Restore body scroll
        document.body.classList.remove('overflow-hidden');

        drawer.setAttribute('aria-hidden', 'true');
        drawer.classList.remove('sidebar-open');
        overlay.classList.remove('is-active');

        // Allow reverse animation (240ms) to complete before hiding from DOM
        sidebarCloseTimer = setTimeout(() => {
            if (!isSidebarActive) {
                overlay.classList.add('hidden');
                drawer.classList.add('hidden');
            }
        }, 260);
    }
}

export function initSidebarListeners() {
    const drawer = document.getElementById('sidebarDrawer');
    if (!drawer || drawer.dataset.sidebarInitialized === 'true') return;
    drawer.dataset.sidebarInitialized = 'true';

    // Keyboard support: Escape closes sidebar
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isSidebarActive) {
            toggleSidebar(false);
        }
    });

    // Touch gesture: swipe left on drawer to dismiss
    let touchStartX = 0;
    let touchStartY = 0;
    let trackingSwipe = false;

    drawer.addEventListener('touchstart', (e) => {
        if (!e.touches || e.touches.length !== 1) return;
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        trackingSwipe = true;
    }, { passive: true });

    drawer.addEventListener('touchmove', (e) => {
        if (!trackingSwipe || !e.touches || e.touches.length !== 1) return;
        const currentX = e.touches[0].clientX;
        const currentY = e.touches[0].clientY;
        const diffX = currentX - touchStartX;
        const diffY = currentY - touchStartY;

        // If swiping left (negative diffX) beyond 45px threshold and horizontal movement exceeds vertical
        if (diffX < -45 && Math.abs(diffX) > Math.abs(diffY)) {
            trackingSwipe = false;
            toggleSidebar(false);
        }
    }, { passive: true });

    drawer.addEventListener('touchend', () => {
        trackingSwipe = false;
    }, { passive: true });
}

export function switchTab(tabName) {
    state.currentActiveTab = tabName;

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

export function openSidebarSection(sectionType) {
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

    const brandNameAr = salonIdentity.name_ar || 'هيرلاين';
    const brandNameEn = salonIdentity.name_en || 'HAIRLINE';
    const brandTaglineEn = salonIdentity.tagline_en || 'BEAUTY SALON';
    const hoursText = workingHours.hours_ar || 'من 2:00 ظهراً حتى 10:00 مساءً';
    const addressText = location.full_address_ar || 'الرياض - حي المصيف، مخرج 5';
    const mapsUrl = (location.google_maps_url && !location.google_maps_url.includes('24.7677'))
        ? location.google_maps_url
        : 'https://www.google.com/maps?q=%D9%87%D9%8A%D8%B1%D9%84%D8%A7%D9%8A%D9%86,+Ext.5%D8%8C+%D8%B2%D9%87%D9%8A%D8%B1+%D8%A8%D9%86+%D8%A7%D8%A8%D9%8A+%D8%B3%D9%84%D9%85%D9%89%D8%8C+%D8%A7%D9%84%D9%85%D8%B5%D9%8A%D9%81%D8%8C+%D8%A7%D9%84%D8%B1%D9%8A%D8%A7%D8%B6+12468&ftid=0x3e2efda2254ccfcb:0x9b01f2c00151ffe1&entry=gps';

    if (sectionType === 'about') {
        sheetTitle.textContent = "التعريف بالصالون (من نحن)";
        sheetSubtitle.textContent = `${brandNameAr} | ${brandNameEn} ${brandTaglineEn}`;
        sheetBody.innerHTML = `
            <div class="space-y-4 animate-step">
                <div class="relative h-44 rounded-2xl overflow-hidden shadow-xs border border-gray-100">
                    <img src="/images/salon/salon-screen-display.jpg" 
                         alt="صالون هيرلاين" 
                         onerror="this.src='https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&w=900&q=80'"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-sage-dark/90 to-transparent flex items-end p-4">
                        <span class="text-white font-bold text-sm">الفخامة والعناية المتخصصة بأرقى المعايير</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">
                    تأسس صالون <strong class="text-sage-dark">${brandNameAr}</strong> ليكون الملاذ الأول لكل سيدة تبحث عن التميز في العناية بشعرها وجمالها في حي المصيف بالرياض (مخرج 5). نعتمد في كافة جلساتنا على تشخيص دقيق لطبيعة الشعر وأحدث المعالجات المعتمدة في قائمة خدماتنا الرسمية.
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
                    <p class="text-xs text-gray-600 mt-1">${hoursText}</p>
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
                    <div class="flex items-center gap-3">
                        <div>
                            <h4 class="font-extrabold text-sm text-sage-dark">العنوان</h4>
                            <p class="text-xs text-gray-700 font-medium leading-relaxed mt-0.5">${addressText}</p>
                        </div>
                    </div>

                    <!-- Clickable Map Frame -->
                    <a href="${mapsUrl}" 
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

                <div class="p-3.5 rounded-xl bg-gold-light/40 border border-gold-soft/40 flex items-center justify-between text-xs">
                    <span class="text-gray-600 font-medium">لديكِ استفسار حول سياسة الخصوصية؟</span>
                    <button onclick="openSidebarSection('contact')" class="font-bold text-gold-dark hover:underline flex items-center gap-1 cursor-pointer">
                        <span>تواصلي معنا</span>
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    </button>
                </div>
            </div>
        `;
    }

    showBottomSheet();
}

export function updateBookingBadge() {
    const badge = document.getElementById('bookingCountBadge');
    if (badge) {
        badge.textContent = state.confirmedBookings.length;
        badge.classList.toggle('hidden', state.confirmedBookings.length === 0);
    }
    const desktopBadge = document.getElementById('desktopBookingCountBadge');
    if (desktopBadge) {
        desktopBadge.textContent = state.confirmedBookings.length;
        desktopBadge.classList.toggle('hidden', state.confirmedBookings.length === 0);
    }
}

export function toggleDesktopBooking() {
    // Uses openBookingFlow which will be bound on window or imported
    if (typeof window.openBookingFlow === 'function') {
        window.openBookingFlow();
    }
}
