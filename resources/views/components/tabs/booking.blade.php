@props([
    'bookableCategories' => [],
    'isActive' => false,
])

<section id="bookingTabView"
    class="{{ $isActive ? '' : 'hidden' }} flex-1 w-full max-w-4xl md:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-0 md:pt-6 pb-28 md:pb-2 space-y-5 md:space-y-6 animate-booking-page">

    <!-- Mobile Immersive Top Bar (Hidden on Desktop) -->
    <div id="mobileBookingTopBar"
         class="sticky top-0 z-30 md:hidden -mx-4 px-4 py-3 bg-[#fdfbf7]/95 backdrop-blur-md border-b border-[#e5dec9] flex items-center justify-between gap-2">
        <!-- Far Right: Step Back Button (Hidden on Step 1) -->
        <button type="button"
                id="mobileBookingBackBtn"
                onclick="handleBookingBack()"
                aria-label="العودة للخطوة السابقة"
                class="invisible pointer-events-none w-10 h-10 rounded-full bg-white text-primary-dark border border-primary-soft/80 hover:bg-primary hover:text-white flex items-center justify-center shadow-2xs transition cursor-pointer shrink-0 active:scale-95">
            <i class="fa-solid fa-arrow-right text-sm"></i>
        </button>

        <!-- Center: Current Step Title -->
        <div class="flex-1 min-w-0 text-center px-1">
            <h2 id="mobileBookingTopTitleText" class="text-sm sm:text-base font-black text-sage-dark truncate">
                اختيار الخدمة
            </h2>
            <span id="mobileBookingTopSubtitleText" class="hidden block text-[11px] font-bold text-primary-dark truncate"></span>
        </div>

        <!-- Far Left: Close Screen Button (X) -->
        <button type="button"
                id="mobileBookingCloseBtn"
                onclick="requestCloseMobileBooking()"
                aria-label="إغلاق شاشة الحجز"
                class="w-10 h-10 rounded-full bg-white text-sage-dark border border-primary-soft/80 hover:bg-red-50 hover:text-red-500 hover:border-red-200 flex items-center justify-center shadow-2xs transition cursor-pointer shrink-0 active:scale-95">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <!-- Desktop 65% / 35% Split Layout (Single Column on Mobile) -->
    <div id="bookingSplitLayout" class="grid grid-cols-1 md:grid-cols-[minmax(0,65%)_minmax(0,35%)] gap-6 lg:gap-8 items-start">

        <!-- Main Column (65% on Desktop): Step Header, Stepper & Step Content -->
        <div class="min-w-0 space-y-5">

            <!-- Page Header & Stepper -->
            <div class="space-y-4">
                <div class="hidden md:flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <button type="button" id="bookingPageBackBtn" onclick="handleBookingBack()"
                            aria-label="العودة للخطوة السابقة"
                            class="hidden w-10 h-10 rounded-full bg-white text-primary-dark border border-primary-soft/70 shadow-2xs flex items-center justify-center hover:bg-primary hover:text-white transition cursor-pointer shrink-0">
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </button>
                        <div>
                            <h2 id="bookingPageTitle" class="text-lg sm:text-2xl font-black text-sage-dark">الخدمات المتاحة
                                للحجز</h2>
                            <p id="bookingPageSubtitle" class="hidden text-xs sm:text-sm text-gray-500 mt-0.5"></p>
                        </div>
                    </div>
                </div>

                <!-- 3-Step Progress Bar -->
                <div id="bookingStepper" class="pt-1 md:pt-2">
                    <div class="grid grid-cols-3 gap-2 sm:gap-3 text-center">
                        <div id="stepIndicator1" class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full bg-primary mb-1.5 transition-colors duration-300"></div>
                            <span class="text-xs font-extrabold text-primary-dark">1. اختيار الخدمة</span>
                        </div>
                        <div id="stepIndicator2" class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full bg-primary-soft/40 mb-1.5 transition-colors duration-300">
                            </div>
                            <span class="text-xs font-medium text-gray-400">2. اختيار الأخصائية</span>
                        </div>
                        <div id="stepIndicator3" class="flex flex-col items-center">
                            <div class="w-full h-1.5 rounded-full bg-primary-soft/40 mb-1.5 transition-colors duration-300">
                            </div>
                            <span class="text-xs font-medium text-gray-400">3. تحديد الموعد</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 1: Simplified Collapsible Tree View (Server-Rendered from Cache, Open by Default) -->
            <div id="bookingStep1Container" class="space-y-5">

                @forelse($bookableCategories as $cat)
                    <div id="booking-cat-{{ $cat['id'] }}" data-category-tree="{{ $cat['id'] }}"
                        data-category-title="{{ $cat['title'] }}" class="booking-tree-category">

                        <!-- Main Service Category Node (Parent Node - Collapsible, Open by Default) -->
                        <button type="button" id="tree-btn-{{ $cat['id'] }}" onclick="toggleBookingTreeCategory('{{ $cat['id'] }}')"
                            aria-expanded="true" aria-controls="tree-panel-{{ $cat['id'] }}"
                            class="w-full py-2.5 px-3 rounded-xl bg-primary-light/50 hover:bg-primary-light/85 flex items-center justify-between gap-3 text-right group cursor-pointer select-none border border-primary-soft/60 hover:border-primary transition-colors">
                            <div class="flex items-center gap-2 min-w-0">
                                <span id="tree-chevron-{{ $cat['id'] }}"
                                    class="w-5 h-5 rounded-full flex items-center justify-center text-primary-dark transition-transform duration-300">
                                    <i class="fa-solid fa-chevron-down text-[11px]"></i>
                                </span>
                                <h3
                                    class="font-extrabold text-base sm:text-lg text-sage-dark group-hover:text-primary-dark transition-colors truncate">
                                    {{ $cat['title'] }}
                                </h3>
                            </div>
                        </button>

                        <!-- Sub-Services Tree Branches (Open by Default, Borderless Minimal Rows with Checkbox) -->
                        <div id="tree-panel-{{ $cat['id'] }}"
                            class="booking-tree-panel grid grid-rows-[1fr] opacity-100 transition-all duration-300 ease-out">
                            <div class="overflow-hidden">
                                <ul
                                    class="relative mr-3 sm:mr-3.5 pr-4 sm:pr-6 pt-1.5 pb-1 border-r border-gold-soft/80 space-y-0.5">
                                    @foreach($cat['subServices'] ?? [] as $sub)
                                        <li class="booking-service-item relative group/item">
                                            <!-- Horizontal Tree Branch Connector -->
                                            <span
                                                class="absolute -right-4 sm:-right-6 top-1/2 -translate-y-1/2 w-3 sm:w-4 h-px bg-gold-soft/80 group-hover/item:bg-gold transition-colors"></span>

                                            <label for="srv-check-{{ $sub['id'] }}" id="srv-row-{{ $sub['id'] }}"
                                                class="booking-tree-row flex items-center justify-between gap-3 py-2 px-2 rounded-lg cursor-pointer select-none transition-colors duration-150 hover:bg-gold-light/25">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <!-- Custom Minimal Checkbox -->
                                                    <div class="relative flex items-center shrink-0">
                                                        <input type="checkbox" id="srv-check-{{ $sub['id'] }}"
                                                            name="booking_service_checkbox" value="{{ $sub['id'] }}"
                                                            data-category-id="{{ $cat['id'] }}"
                                                            onchange="handleServiceCheckboxChange(this, '{{ $sub['id'] }}', '{{ $cat['id'] }}')"
                                                            class="peer sr-only booking-service-checkbox" />
                                                        <span
                                                            class="w-5 h-5 rounded-md border border-primary-soft bg-white flex items-center justify-center transition-all duration-200 peer-checked:border-primary peer-checked:bg-primary peer-checked:[&>i]:scale-100 group-hover/item:border-primary">
                                                            <i
                                                                class="fa-solid fa-check text-[11px] text-white scale-0 transition-transform duration-150"></i>
                                                        </span>
                                                    </div>

                                                    <!-- Sub-Service Text Info (No Borders / No Boxes / No Description / No Icons) -->
                                                    <div class="min-w-0">
                                                        <span
                                                            class="block font-bold text-sm sm:text-[15px] text-sage-dark group-hover/item:text-primary-dark transition-colors leading-snug">
                                                            {{ $sub['name'] }}
                                                        </span>
                                                        <p class="booking-price-hint text-[11px] text-gray-400 font-medium mt-0.5"
                                                            data-price-hint="{{ $sub['price_hint'] ?? '' }}">
                                                            {{ $sub['price_hint'] ?? 'بحسب الطول والكثافة' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-8">لا توجد خدمات متاحة للحجز حالياً.</p>
                @endforelse
            </div>

            <!-- STEP 2: Specialist Selection (Rendered Dynamically Inside the Page) -->
            <div id="bookingStep2Container" class="hidden space-y-5 pb-4 animate-step">
                <div id="bookingStep2Body" class="space-y-5">
                    <!-- Populated dynamically when user advances to step 2 -->
                </div>
                <div id="bookingStep2Footer" class="empty:hidden"></div>
            </div>

            <!-- STEP 3: Shift-Based Time Selection (Rendered Dynamically Inside the Page) -->
            <div id="bookingStep3Container" class="hidden space-y-5 pb-4 animate-step">
                <div id="bookingStep3Body" class="space-y-5">
                    <!-- Populated dynamically when user confirms the specialist -->
                </div>

                <!-- Extra Booking Details: Guests Count -->
                <div id="bookingDetailsSection" class="bg-white rounded-2xl p-4 sm:p-5 border border-primary-soft/60 shadow-xs">
                    <!-- Number of Persons -->
                    <div class="flex items-center justify-between gap-4">
                        <label for="bookingGuestsCountInput"
                            class="text-xs sm:text-sm font-extrabold text-sage-dark cursor-pointer">
                            عدد الأشخاص
                        </label>
                        <div
                            class="inline-flex items-center bg-surface border border-gray-200 rounded-xl p-1 shadow-2xs shrink-0">
                            <button type="button" onclick="changeBookingGuestsCount(-1)" aria-label="تقليل"
                                class="w-8 h-8 rounded-lg bg-white hover:bg-primary hover:text-white text-sage-dark flex items-center justify-center font-bold text-xs border border-gray-200 transition cursor-pointer active:scale-95">
                                <i class="fa-solid fa-minus text-[10px]"></i>
                            </button>
                            <input type="number" id="bookingGuestsCountInput" name="guests_count" min="1" max="20" value="1"
                                oninput="handleBookingGuestsCountInput(this)"
                                class="w-12 text-center font-black text-sm text-sage-dark bg-transparent border-0 focus:ring-0 focus:outline-none" />
                            <button type="button" onclick="changeBookingGuestsCount(1)" aria-label="زيادة"
                                class="w-8 h-8 rounded-lg bg-white hover:bg-primary hover:text-white text-sage-dark flex items-center justify-center font-bold text-sm border border-gray-200 transition cursor-pointer active:scale-95">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="bookingStep3Footer" class="empty:hidden"></div>
            </div>

        </div>

        <!-- Desktop 35% Sticky Summary Sidebar (Bounded strictly to viewport height, internal scroll only) -->
        <aside id="desktopBookingSidebar" class="hidden md:block desktop-booking-sidebar" aria-label="ملخص الحجز">
            <div id="desktopBookingSummaryCard"
                 class="desktop-booking-summary-card flex flex-col overflow-hidden bg-white rounded-3xl border border-gold-soft/80 shadow-card">

                <!-- Fixed Top Header -->
                <div class="shrink-0 px-5 py-4 bg-surface/80 border-b border-primary-soft/50 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-9 h-9 rounded-xl bg-primary-light text-primary-dark flex items-center justify-center shrink-0 border border-primary-soft/50">
                            <i class="fa-regular fa-calendar-check text-sm"></i>
                        </span>
                        <h3 class="text-base font-black text-sage-dark truncate">ملخص الحجز</h3>
                    </div>

                    <span id="desktopSummaryCountBadge"
                          class="px-3 py-1 rounded-full bg-primary-light text-primary-dark text-xs font-extrabold border border-primary-soft/70 shrink-0">
                        0 خدمات
                    </span>
                </div>

                <!-- Independent Scrollable Middle List (No prices displayed) -->
                <div id="desktopSummaryItemsContainer"
                     class="flex-1 min-h-0 overflow-y-auto hairline-custom-scroll p-4 space-y-2.5">
                    <div class="h-full min-h-[200px] py-8 px-4 flex flex-col items-center justify-center text-center space-y-2.5">
                        <span class="w-12 h-12 rounded-2xl bg-primary-light/70 text-primary-dark flex items-center justify-center border border-primary-soft/50">
                            <i class="fa-regular fa-calendar-plus text-lg"></i>
                        </span>
                        <div class="space-y-1">
                            <p class="text-sm font-black text-sage-dark">لا توجد خدمات مختارة</p>
                            <p class="text-xs text-gray-400">
                                اختاري من القائمة لإضافتها هنا.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fixed Bottom Submit Button -->
                <div class="shrink-0 p-4 bg-white border-t border-primary-soft/50">
                    <button type="button"
                            id="desktopBookingSubmitBtn"
                            onclick="handleFloatingBookingConfirm()"
                            disabled
                            class="w-full py-3.5 px-4 rounded-2xl bg-primary hover:bg-primary-dark text-white font-extrabold text-sm shadow-lg shadow-primary/20 active:scale-[0.985] transition-all flex items-center justify-center gap-2 cursor-pointer disabled:bg-gray-100 disabled:text-gray-400 disabled:shadow-none disabled:cursor-not-allowed">
                        <span id="desktopBookingSubmitBtnText">اختاري خدمة</span>
                        <i id="desktopBookingSubmitBtnIcon" class="fa-solid fa-arrow-left text-xs"></i>
                    </button>
                </div>

            </div>
        </aside>

    </div>

    <!-- STEP 4: Final Booking Confirmation Receipt (Rendered Dynamically Inside the Page) -->
    <div id="bookingStep4Container" class="hidden animate-step">
        <div
            class="bg-white rounded-3xl p-6 shadow-card border border-gold-soft/50 text-center space-y-4 animate-step max-w-xl mx-auto">
            <div
                class="w-16 h-16 rounded-full bg-sage-light border-2 border-primary mx-auto flex items-center justify-center text-primary text-2xl shadow-sm">
                <i class="fa-solid fa-check"></i>
            </div>

            <div>
                <h3 class="text-xl font-black text-sage-dark">تم تأكيد حجزك بنجاح</h3>
                <p class="text-sm font-extrabold text-primary-dark mt-1">
                    باسم: <span class="text-sage-dark"></span>
                </p>
                <div class="mt-2 inline-block px-4 py-1.5 rounded-full bg-gold-light text-gold-dark text-xs font-black">
                    رقم الحجز:
                </div>
            </div>

            <!-- 2 Rows with Columns, completely borderless and box-free -->
            <div class="py-3 px-1 text-right space-y-4">
                <!-- Row 1: اسم العميلة، الخدمة المختارة، الأخصائية -->
                <div class="grid grid-cols-3 gap-2.5 sm:gap-4 items-start">
                    <div class="min-w-0">
                        <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">اسم العميلة</span>
                        <span class="text-xs sm:text-sm font-extrabold text-sage-dark block truncate"></span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">الخدمة المختارة</span>
                        <span class="text-xs sm:text-sm font-extrabold text-sage-dark block truncate"></span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">الأخصائية</span>
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="text-xs sm:text-sm font-extrabold text-sage-dark truncate"></span>
                        </div>
                    </div>
                </div>

                <!-- Row 2: الموعد، عدد الأشخاص -->
                <div class="grid grid-cols-2 gap-2.5 sm:gap-4 items-start">
                    <div class="min-w-0">
                        <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">الموعد</span>
                        <span class="text-xs sm:text-sm font-extrabold text-sage-dark block leading-snug"></span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">عدد الأشخاص</span>
                        <span class="text-xs sm:text-sm font-extrabold text-sage-dark block">1</span>
                    </div>
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
    </div>

</section>