@props([
    'bookableCategories' => [],
    'isActive' => false,
])

<section id="bookingTabView" class="{{ $isActive ? '' : 'hidden' }} flex-1 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 md:pt-8 pb-16 space-y-6 animate-booking-page">

    <!-- Page Header & Stepper -->
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <button type="button"
                        id="bookingPageBackBtn"
                        onclick="handleBookingBack()"
                        aria-label="العودة للخطوة السابقة"
                        class="hidden w-10 h-10 rounded-full bg-white text-primary-dark border border-primary-soft/70 shadow-2xs flex items-center justify-center hover:bg-primary hover:text-white transition cursor-pointer shrink-0">
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </button>
                <div>
                    <h2 id="bookingPageTitle" class="text-lg sm:text-2xl font-black text-sage-dark">الخدمات المتاحة للحجز</h2>
                    <p id="bookingPageSubtitle" class="hidden text-xs sm:text-sm text-gray-500 mt-0.5"></p>
                </div>
            </div>

            <!-- Collapse / Expand All Toggle (Visible in Step 1) -->
            <button type="button"
                    id="toggleAllTreeBtn"
                    onclick="toggleAllBookingCategories()"
                    class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold text-primary-dark bg-white/90 hover:bg-primary hover:text-white border border-primary-soft/70 transition cursor-pointer shrink-0">
                <span id="toggleAllTreeText">طي الكل</span>
            </button>
        </div>

        <!-- 3-Step Progress Bar -->
        <div id="bookingStepper" class="pt-2">
            <div class="grid grid-cols-3 gap-2 sm:gap-3 text-center">
                <div id="stepIndicator1" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-primary mb-1.5 transition-colors duration-300"></div>
                    <span class="text-xs font-extrabold text-primary-dark">1. اختيار الخدمة</span>
                </div>
                <div id="stepIndicator2" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-primary-soft/40 mb-1.5 transition-colors duration-300"></div>
                    <span class="text-xs font-medium text-gray-400">2. اختيار الأخصائية</span>
                </div>
                <div id="stepIndicator3" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-primary-soft/40 mb-1.5 transition-colors duration-300"></div>
                    <span class="text-xs font-medium text-gray-400">3. تحديد الموعد</span>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 1: Simplified Collapsible Tree View (Server-Rendered from Cache, Open by Default) -->
    <div id="bookingStep1Container" class="space-y-5">

        @forelse($bookableCategories as $cat)
            <div id="booking-cat-{{ $cat['id'] }}"
                 data-category-tree="{{ $cat['id'] }}"
                 data-category-title="{{ $cat['title'] }}"
                 class="booking-tree-category">

                <!-- Main Service Category Node (Parent Node - Collapsible, Open by Default) -->
                <button type="button"
                        id="tree-btn-{{ $cat['id'] }}"
                        onclick="toggleBookingTreeCategory('{{ $cat['id'] }}')"
                        aria-expanded="true"
                        aria-controls="tree-panel-{{ $cat['id'] }}"
                        class="w-full py-2.5 px-3 rounded-xl bg-primary-light/50 hover:bg-primary-light/85 flex items-center justify-between gap-3 text-right group cursor-pointer select-none border border-primary-soft/60 hover:border-primary transition-colors">
                    <div class="flex items-center gap-2 min-w-0">
                        <span id="tree-chevron-{{ $cat['id'] }}"
                              class="w-5 h-5 rounded-full flex items-center justify-center text-primary-dark transition-transform duration-300">
                            <i class="fa-solid fa-chevron-down text-[11px]"></i>
                        </span>
                        <h3 class="font-extrabold text-base sm:text-lg text-sage-dark group-hover:text-primary-dark transition-colors truncate">
                            {{ $cat['title'] }}
                        </h3>
                    </div>

                    <span class="booking-cat-count text-xs font-bold text-primary-dark shrink-0"
                          data-total-count="{{ count($cat['subServices'] ?? []) }}">
                        {{ count($cat['subServices'] ?? []) }} خدمات
                    </span>
                </button>

                <!-- Sub-Services Tree Branches (Open by Default, Borderless Minimal Rows with Checkbox) -->
                <div id="tree-panel-{{ $cat['id'] }}"
                     class="booking-tree-panel grid grid-rows-[1fr] opacity-100 transition-all duration-300 ease-out">
                    <div class="overflow-hidden">
                        <ul class="relative mr-3 sm:mr-3.5 pr-4 sm:pr-6 pt-1.5 pb-1 border-r border-gold-soft/80 space-y-0.5">
                            @foreach($cat['subServices'] ?? [] as $sub)
                                <li class="booking-service-item relative group/item">
                                    <!-- Horizontal Tree Branch Connector -->
                                    <span class="absolute -right-4 sm:-right-6 top-1/2 -translate-y-1/2 w-3 sm:w-4 h-px bg-gold-soft/80 group-hover/item:bg-gold transition-colors"></span>

                                    <label for="srv-check-{{ $sub['id'] }}"
                                           id="srv-row-{{ $sub['id'] }}"
                                           class="booking-tree-row flex items-center justify-between gap-3 py-2 px-2 rounded-lg cursor-pointer select-none transition-colors duration-150 hover:bg-gold-light/25">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <!-- Custom Minimal Checkbox -->
                                            <div class="relative flex items-center shrink-0">
                                                <input type="checkbox"
                                                       id="srv-check-{{ $sub['id'] }}"
                                                       name="booking_service_checkbox"
                                                       value="{{ $sub['id'] }}"
                                                       data-category-id="{{ $cat['id'] }}"
                                                       onchange="handleServiceCheckboxChange(this, '{{ $sub['id'] }}', '{{ $cat['id'] }}')"
                                                       class="peer sr-only booking-service-checkbox" />
                                                <span class="w-5 h-5 rounded-md border border-primary-soft bg-white flex items-center justify-center transition-all duration-200 peer-checked:border-primary peer-checked:bg-primary peer-checked:[&>i]:scale-100 group-hover/item:border-primary">
                                                    <i class="fa-solid fa-check text-[11px] text-white scale-0 transition-transform duration-150"></i>
                                                </span>
                                            </div>

                                            <!-- Sub-Service Text Info (No Borders / No Boxes / No Description / No Icons) -->
                                            <div class="min-w-0">
                                                <span class="block font-bold text-sm sm:text-[15px] text-sage-dark group-hover/item:text-primary-dark transition-colors leading-snug">
                                                    {{ $sub['name'] }}
                                                </span>
                                                <p class="booking-price-hint text-[11px] text-gray-400 font-medium mt-0.5" data-price-hint="{{ $sub['price_hint'] ?? '' }}">
                                                    {{ $sub['price_hint'] ?? 'بحسب الأطوال والكثافة' }}
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
    <div id="bookingStep2Container" class="hidden space-y-5 pb-24 md:pb-28 animate-step">
        <div id="bookingStep2Body" class="space-y-5">
            <!-- Populated dynamically when user clicks the floating confirm button -->
        </div>
        <div id="bookingStep2Footer" class="empty:hidden"></div>
    </div>

    <!-- STEP 3: Shift-Based Time Selection (Rendered Dynamically Inside the Page) -->
    <div id="bookingStep3Container" class="hidden space-y-5 pb-24 md:pb-28 animate-step">
        <div id="bookingStep3Body" class="space-y-5">
            <!-- Populated dynamically when user confirms the specialist -->
        </div>
        <div id="bookingStep3Footer" class="empty:hidden"></div>
    </div>

    <!-- STEP 4: Final Booking Confirmation Receipt (Rendered Dynamically Inside the Page) -->
    <div id="bookingStep4Container" class="hidden animate-step">
        <div class="bg-white rounded-3xl p-6 shadow-card border border-gold-soft/50 text-center space-y-4 animate-step max-w-xl mx-auto">
            <div class="w-16 h-16 rounded-full bg-sage-light border-2 border-primary mx-auto flex items-center justify-center text-primary text-2xl shadow-sm">
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

            <div class="bg-surface rounded-2xl p-4 border border-gold-soft text-right space-y-3">
                <div class="pb-2.5 border-b border-gray-200/60">
                    <span class="text-xs text-gray-500 font-bold block mb-1">اسم العميلة:</span>
                    <span class="text-sm font-extrabold text-sage-dark"></span>
                </div>
                <div class="pb-2.5 border-b border-gray-200/60">
                    <span class="text-xs text-gray-500 font-bold block mb-1">الخدمة المختارة:</span>
                    <span class="text-sm font-extrabold text-sage-dark"></span>
                </div>
                <div class="pb-2.5 border-b border-gray-200/60">
                    <span class="text-xs text-gray-500 font-bold block mb-1">الأخصائية:</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-extrabold text-sage-dark"></span>
                    </div>
                </div>
                <div class="pt-0.5">
                    <span class="text-xs text-gray-500 font-bold block mb-1">الموعد:</span>
                    <span class="text-sm font-extrabold text-sage-dark"></span>
                </div>
            </div>

            <div class="pt-1">
                <a id="bookingReceiptStaticWhatsAppBtn"
                   href="{{ $contactLinks['whatsapp'] ?? 'https://wa.me/966548857705' }}"
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="w-full py-3.5 px-4 rounded-2xl bg-[#25D366] hover:bg-[#20BD5A] text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2.5 shadow-md hover:shadow-lg active:scale-98 transition-all cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                    <span>إرسال تفاصيل الحجز إلى واتساب الصالون</span>
                </a>
             
            </div>

            <div class="flex gap-2.5 pt-1">
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
