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
                        class="hidden w-10 h-10 rounded-full bg-white text-sage-dark border border-gold-soft/70 shadow-2xs flex items-center justify-center hover:bg-sage-dark hover:text-white transition cursor-pointer shrink-0">
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </button>
                <div>
                    <h2 id="bookingPageTitle" class="text-lg sm:text-2xl font-black text-sage-dark">الخدمات المتاحة للحجز</h2>
                    <p id="bookingPageSubtitle" class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        اختاري الخدمة المطلوبة من القائمة الشجرية أدناه للمتابعة لاختيار الأخصائية
                    </p>
                </div>
            </div>

            <!-- Collapse / Expand All Toggle (Visible in Step 1) -->
            <button type="button"
                    id="toggleAllTreeBtn"
                    onclick="toggleAllBookingCategories()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold text-sage-dark bg-white/80 hover:bg-gold-light/60 border border-gold-soft/60 transition cursor-pointer shrink-0">
                <i id="toggleAllTreeIcon" class="fa-solid fa-angles-up text-[10px] text-gold-dark"></i>
                <span id="toggleAllTreeText">طي الكل</span>
            </button>
        </div>

        <!-- 2-Step Progress Bar -->
        <div id="bookingStepper" class="pt-2">
            <div class="grid grid-cols-2 gap-3 text-center">
                <div id="stepIndicator1" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-sage-dark mb-1.5 transition-colors duration-300"></div>
                    <span class="text-xs font-extrabold text-sage-dark">1. اختيار الخدمة</span>
                </div>
                <div id="stepIndicator2" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-gray-200 mb-1.5 transition-colors duration-300"></div>
                    <span class="text-xs font-medium text-gray-400">2. اختيار الأخصائية</span>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 1: Simplified Collapsible Tree View (Server-Rendered from Cache, Open by Default) -->
    <div id="bookingStep1Container" class="space-y-5">
        @forelse($bookableCategories as $cat)
            <div id="booking-cat-{{ $cat['id'] }}"
                 data-category-tree="{{ $cat['id'] }}"
                 class="booking-tree-category">

                <!-- Main Service Category Node (Parent Node - Collapsible, Open by Default) -->
                <button type="button"
                        id="tree-btn-{{ $cat['id'] }}"
                        onclick="toggleBookingTreeCategory('{{ $cat['id'] }}')"
                        aria-expanded="true"
                        aria-controls="tree-panel-{{ $cat['id'] }}"
                        class="w-full py-2.5 px-1 flex items-center justify-between gap-3 text-right group cursor-pointer select-none border-b border-gold-soft/50 hover:border-gold transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span id="tree-chevron-{{ $cat['id'] }}"
                              class="w-6 h-6 rounded-full flex items-center justify-center text-gold-dark group-hover:bg-gold-light/60 transition-transform duration-300">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </span>
                        <i class="fa-solid {{ $cat['icon'] ?? 'fa-scissors' }} text-gold-dark text-sm shrink-0"></i>
                        <div class="flex flex-wrap items-baseline gap-2 min-w-0">
                            <h3 class="font-extrabold text-base sm:text-lg text-sage-dark group-hover:text-black transition-colors">
                                {{ $cat['title'] }}
                            </h3>
                            @if(!empty($cat['description']))
                                <span class="text-[11px] text-gray-400 font-medium hidden sm:inline">
                                    ({{ $cat['description'] }})
                                </span>
                            @endif
                        </div>
                    </div>

                    <span class="text-xs font-bold text-gold-dark shrink-0">
                        {{ count($cat['subServices'] ?? []) }} خدمات
                    </span>
                </button>

                <!-- Sub-Services Tree Branches (Open by Default, Borderless Minimal Rows with Checkbox) -->
                <div id="tree-panel-{{ $cat['id'] }}"
                     class="booking-tree-panel grid grid-rows-[1fr] opacity-100 transition-all duration-300 ease-out">
                    <div class="overflow-hidden">
                        <ul class="relative mr-3.5 sm:mr-4 pr-4 sm:pr-6 pt-2 pb-1 border-r border-gold-soft/80 space-y-1">
                            @foreach($cat['subServices'] ?? [] as $sub)
                                <li class="relative group/item">
                                    <!-- Horizontal Tree Branch Connector -->
                                    <span class="absolute -right-4 sm:-right-6 top-5 w-3 sm:w-4 h-px bg-gold-soft/80 group-hover/item:bg-gold transition-colors"></span>

                                    <label for="srv-check-{{ $sub['id'] }}"
                                           id="srv-row-{{ $sub['id'] }}"
                                           class="booking-tree-row flex items-start justify-between gap-3 py-2 px-2 rounded-lg cursor-pointer select-none transition-colors duration-150 hover:bg-gold-light/25">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <!-- Custom Minimal Checkbox -->
                                            <div class="relative flex items-center pt-0.5 shrink-0">
                                                <input type="checkbox"
                                                       id="srv-check-{{ $sub['id'] }}"
                                                       name="booking_service_checkbox"
                                                       value="{{ $sub['id'] }}"
                                                       data-category-id="{{ $cat['id'] }}"
                                                       onchange="handleServiceCheckboxChange(this, '{{ $sub['id'] }}', '{{ $cat['id'] }}')"
                                                       class="peer sr-only booking-service-checkbox" />
                                                <span class="w-5 h-5 rounded-md border border-gray-400 bg-transparent flex items-center justify-center transition-all duration-200 peer-checked:border-sage-dark peer-checked:bg-sage-dark peer-checked:[&>i]:scale-100 group-hover/item:border-sage-dark">
                                                    <i class="fa-solid fa-check text-[11px] text-gold scale-0 transition-transform duration-150"></i>
                                                </span>
                                            </div>

                                            <!-- Sub-Service Text Info (No Borders / No Boxes) -->
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="font-bold text-sm sm:text-[15px] text-sage-dark group-hover/item:text-black transition-colors leading-snug">
                                                        {{ $sub['name'] }}
                                                    </span>
                                                </div>
                                                @if(!empty($sub['desc']))
                                                    <p class="text-xs text-gray-500 leading-relaxed mt-0.5">
                                                        {{ $sub['desc'] }}
                                                    </p>
                                                @endif
                                                <div class="flex flex-wrap items-center gap-3 mt-1 text-[11px] text-gray-400 font-medium">
                                                    <span>
                                                        <i class="fa-regular fa-clock ml-1 text-gold-dark"></i>المدة: {{ $sub['duration'] }}
                                                    </span>
                                                    <span>•</span>
                                                    <span class="text-gold-dark font-semibold">
                                                        {{ count($sub['specialists'] ?? []) }} أخصائيات متاحات
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Price (Minimal Text, No Box) -->
                                        <span class="font-extrabold text-xs sm:text-sm text-sage-dark shrink-0 pt-0.5 whitespace-nowrap">
                                            {{ $sub['price'] }}
                                        </span>
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

    <!-- STEP 2: Specialist & Shift-Based Time Selection (Rendered Dynamically Inside the Page) -->
    <div id="bookingStep2Container" class="hidden space-y-5 animate-step">
        <div id="bookingStep2Body" class="space-y-5">
            <!-- Populated dynamically when user clicks the floating confirm button -->
        </div>
        <div id="bookingStep2Footer" class="pt-2">
            <!-- Final booking confirmation button -->
        </div>
    </div>

    <!-- STEP 3: Final Booking Confirmation Receipt (Rendered Dynamically Inside the Page) -->
    <div id="bookingStep3Container" class="hidden animate-step">
        <!-- Populated dynamically after phone verification / Google registration -->
    </div>

</section>
