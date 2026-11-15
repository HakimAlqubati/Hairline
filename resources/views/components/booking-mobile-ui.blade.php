{{-- Mobile Immersive Booking Bottom Summary Bar, Half-Screen Expanded Sheet & Close Confirmation Popup --}}

<!-- 1. Mobile Sticky Bottom Summary Bar (Full-width, 50/50 Split, Frosted Glass) -->
<div id="mobileBookingBottomBar"
     class="md:hidden fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] z-30 hidden translate-y-full opacity-0 pointer-events-none transition-all duration-300 ease-out">
    <div class="bg-white/70 backdrop-blur-md border-t border-gold-soft/50 px-3.5 pt-2 pb-[calc(0.5rem+env(safe-area-inset-bottom,0px))]">
        <div class="grid grid-cols-2 gap-2.5 items-stretch">
            <!-- Right Half: Selected Services Count + Up Arrow (Opens Half-Screen Sheet) -->
            <button type="button"
                    id="mobileBookingSummaryToggleBtn"
                    onclick="toggleMobileBookingSummarySheet(true)"
                    aria-expanded="false"
                    aria-controls="mobileBookingSummarySheet"
                    class="w-full h-11 min-w-0 flex items-center justify-center gap-2 px-3 rounded-xl bg-white/85 hover:bg-primary-light/70 border border-primary-soft/70 text-center font-extrabold text-xs sm:text-sm text-sage-dark transition cursor-pointer active:scale-[0.98]">
                <span id="mobileBottomSummaryCount" class="truncate">
                    0 خدمات
                </span>
                <span class="w-5 h-5 rounded-full bg-primary-light text-primary-dark flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-chevron-up text-[9px]"></i>
                </span>
            </button>

            <!-- Left Half: Primary Action Button ("التالي" / "تأكيد الحجز") -->
            <button type="button"
                    id="mobileBookingActionBtn"
                    onclick="handleFloatingBookingConfirm()"
                    class="w-full h-11 min-w-0 px-3 rounded-xl border border-transparent bg-primary hover:bg-primary-dark text-white font-extrabold text-xs sm:text-sm active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer disabled:bg-gray-100/90 disabled:text-gray-400 disabled:cursor-not-allowed">
                <span id="mobileBookingActionBtnText" class="truncate">التالي</span>
                <i id="mobileBookingActionBtnIcon" class="fa-solid fa-arrow-left text-xs shrink-0"></i>
            </button>
        </div>
    </div>
</div>

<!-- 2. Mobile Expanded Summary Bottom Sheet (Covers ~Half Screen) -->
<div id="mobileBookingSummaryBackdrop"
     onclick="toggleMobileBookingSummarySheet(false)"
     class="md:hidden fixed inset-0 bg-black/50 backdrop-blur-[2px] z-40 hidden opacity-0 transition-opacity duration-300"></div>

<div id="mobileBookingSummarySheet"
     role="dialog"
     aria-modal="true"
     aria-labelledby="mobileSheetTitle"
     class="md:hidden fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] h-[54dvh] max-h-[68dvh] min-h-[300px] z-50 bg-white rounded-t-[28px] shadow-2xl border-t border-gold-soft/80 flex flex-col overflow-hidden hidden translate-y-full transition-transform duration-300 ease-out">

    <!-- Drag Handle & Fixed Header -->
    <div class="shrink-0 px-5 pt-3 pb-3.5 bg-surface/80 border-b border-primary-soft/50">
        <div class="w-11 h-1.5 bg-gray-300 rounded-full mx-auto mb-3"></div>
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-primary-light text-primary-dark flex items-center justify-center shrink-0">
                    <i class="fa-regular fa-calendar-check text-sm"></i>
                </span>
                <div>
                    <h3 id="mobileSheetTitle" class="text-sm sm:text-base font-black text-sage-dark leading-tight">
                        ملخص الحجز
                    </h3>
                    <span id="mobileSheetCountBadge" class="text-[11px] font-bold text-primary-dark">
                        0 خدمات
                    </span>
                </div>
            </div>

            <button type="button"
                    onclick="toggleMobileBookingSummarySheet(false)"
                    aria-label="إغلاق ملخص الحجز"
                    class="w-9 h-9 rounded-full bg-white text-gray-500 hover:bg-red-50 hover:text-red-500 border border-gray-200/80 flex items-center justify-center shadow-2xs transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Independent Scrollable Middle List -->
    <div id="mobileSheetItemsContainer" class="flex-1 min-h-0 overflow-y-auto hairline-custom-scroll p-4 space-y-2.5">
        <!-- Populated dynamically by booking-wizard.js (no prices shown) -->
    </div>

    <!-- Fixed Bottom Submit Footer -->
    <div class="shrink-0 p-4 pb-[calc(1rem+env(safe-area-inset-bottom,0px))] bg-white border-t border-primary-soft/50">
        <button type="button"
                id="mobileSheetActionBtn"
                onclick="handleMobileSheetSubmit()"
                class="w-full py-3.5 px-4 rounded-2xl bg-primary hover:bg-primary-dark text-white font-extrabold text-sm shadow-lg shadow-primary/20 active:scale-[0.985] transition-all flex items-center justify-center gap-2 cursor-pointer disabled:bg-gray-100 disabled:text-gray-400 disabled:shadow-none disabled:cursor-not-allowed">
            <span id="mobileSheetActionBtnText">التالي</span>
            <i id="mobileSheetActionBtnIcon" class="fa-solid fa-arrow-left text-xs"></i>
        </button>
    </div>
</div>

<!-- 3. Mobile Close Booking Confirmation Modal -->
<div id="bookingCloseConfirmModal"
     role="alertdialog"
     aria-modal="true"
     aria-labelledby="bookingCloseConfirmTitle"
     class="fixed inset-0 z-[65] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200 opacity-0">
    <div id="bookingCloseConfirmCard"
         class="w-full max-w-[360px] rounded-[28px] bg-white border border-gold-soft/80 shadow-2xl p-6 text-center space-y-4 transform scale-95 transition-all duration-200">
        <div class="w-14 h-14 rounded-full bg-amber-50 border border-amber-200 text-amber-600 mx-auto flex items-center justify-center">
            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
        </div>
        <div class="space-y-1">
            <h4 id="bookingCloseConfirmTitle" class="text-base sm:text-lg font-black text-sage-dark">
                إغلاق الحجز؟
            </h4>
            <p class="text-xs sm:text-[13px] text-gray-500">
                ستفقدين اختياراتك الحالية.
            </p>
        </div>
        <div class="grid grid-cols-2 gap-2.5 pt-1 items-stretch">
            <button type="button"
                    onclick="closeBookingCloseConfirmModal()"
                    class="w-full h-11 px-4 rounded-2xl border border-transparent bg-primary hover:bg-primary-dark text-white font-extrabold text-xs sm:text-sm shadow-sm transition flex items-center justify-center cursor-pointer">
                متابعة
            </button>
            <button type="button"
                    onclick="confirmCloseMobileBooking()"
                    class="w-full h-11 px-4 rounded-2xl bg-surface hover:bg-red-50 text-sage-dark hover:text-red-600 border border-gray-200 hover:border-red-200 font-extrabold text-xs sm:text-sm transition flex items-center justify-center cursor-pointer">
                إغلاق
            </button>
        </div>
    </div>
</div>
