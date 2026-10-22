<div id="sheetOverlay" 
     onclick="closeBottomSheet()" 
     class="fixed inset-0 bg-black/55 backdrop-blur-xs z-40 hidden opacity-0 drawer-backdrop">
</div>

<div id="bottomSheet" 
     class="fixed z-50 flex flex-col overflow-hidden bg-white shadow-2xl bottom-sheet border border-gray-100/90
            bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] max-h-[88vh] rounded-t-[32px] translate-y-full
            md:bottom-28 md:left-8 md:translate-x-0 md:translate-y-0 md:w-[460px] md:max-h-[82vh] md:rounded-3xl 
            md:origin-bottom-left md:scale-75 md:opacity-0 md:pointer-events-none md:shadow-fab transition-all duration-300">
    
    <!-- Drag Handle (Mobile Only) & Top Sheet Bar -->
    <div class="pt-3 pb-3 px-5 bg-sage-light/60 border-b border-gold-soft/40 shrink-0">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3 md:hidden"></div>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <button id="sheetBackBtn" 
                        onclick="handleSheetBack()" 
                        class="hidden w-9 h-9 rounded-full bg-white text-sage-dark shadow-xs flex items-center justify-center hover:bg-sage-dark hover:text-white transition cursor-pointer">
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </button>
                <div>
                    <h3 id="sheetTitle" class="text-base sm:text-lg font-extrabold text-sage-dark">الخدمات المتاحة للحجز</h3>
                    <p id="sheetSubtitle" class="text-xs text-gray-500">خطوات بسيطة لتأكيد موعدكِ</p>
                </div>
            </div>

            <button onclick="closeBottomSheet()" 
                    aria-label="إغلاق النافذة"
                    class="w-9 h-9 rounded-full bg-white text-gray-500 hover:bg-red-50 hover:text-red-500 flex items-center justify-center shadow-xs transition cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- 2-Step Progress Bar (Shown only during Booking Flow) -->
        <div id="bookingStepper" class="mt-4 pt-3 border-t border-white/60 hidden">
            <div class="grid grid-cols-2 gap-3 text-center">
                <div id="stepIndicator1" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-sage-dark mb-1.5 transition-colors"></div>
                    <span class="text-[11px] font-bold text-sage-dark">1. اختيار الخدمة</span>
                </div>
                <div id="stepIndicator2" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-gray-200 mb-1.5 transition-colors"></div>
                    <span class="text-[11px] font-medium text-gray-400">2. اختيار الأخصائية</span>
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

<!-- ==================== PHONE VERIFICATION & GOOGLE REGISTRATION POPUP MODAL ==================== -->
<div id="bookingVerificationModal"
     class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/65 backdrop-blur-xs transition-opacity duration-300 opacity-0">
    
    <div id="bookingVerificationCard"
         class="relative w-full max-w-[410px] rounded-[28px] bg-white shadow-2xl border border-gold-soft/70 overflow-hidden transform scale-95 transition-all duration-300">
        
        <!-- Top Gold Shimmer Bar -->
        <div class="h-1.5 w-full bg-gradient-to-r from-sage-dark via-gold to-sage-dark"></div>

        <!-- Close Popup Button -->
        <button type="button"
                onclick="closePhoneVerificationModal()"
                aria-label="إغلاق نافذة التحقق"
                class="absolute top-4 left-4 w-8 h-8 rounded-full bg-surface text-gray-500 hover:bg-red-50 hover:text-red-500 flex items-center justify-center border border-gray-200/70 transition cursor-pointer z-10">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>

        <!-- VIEW 1: SAUDI PHONE NUMBER INPUT & VERIFICATION -->
        <div id="phoneVerifyStep" class="p-6 space-y-4">
            <div class="flex flex-col items-center text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-sage-soft border border-gold-soft flex items-center justify-center text-sage-dark shadow-xs">
                    <i class="fa-solid fa-mobile-screen-button text-2xl text-gold-dark"></i>
                </div>
                <h4 class="text-lg font-black text-sage-dark">تأكيد رقم الجوال</h4>
                <p class="text-xs text-gray-500 leading-relaxed max-w-[290px]">
                    أدخلي رقم جوالكِ السعودي للتحقق من حسابكِ وإتمام حجز الموعد مباشرة
                </p>
            </div>

            <!-- Selected Service & Specialist Mini Summary -->
            <div id="popupBookingSummary" class="p-3 rounded-2xl bg-surface border border-gold-soft/60 text-xs space-y-1">
                <!-- Populated dynamically -->
            </div>

            <form id="phoneVerifyForm" onsubmit="submitPhoneVerification(event)" class="space-y-3.5" novalidate>
                <div>
                    <label for="bookingPhoneInput" class="block text-xs font-extrabold text-sage-dark mb-1.5">
                        رقم الجوال السعودي <span class="text-red-500">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <input id="bookingPhoneInput"
                               type="tel"
                               inputmode="tel"
                               dir="ltr"
                               maxlength="14"
                               placeholder="05XXXXXXXX"
                               autocomplete="tel"
                               oninput="handlePhoneInputValidation(this)"
                               class="w-full py-3.5 pr-4 pl-20 rounded-2xl bg-surface border border-gray-200 focus:border-sage-dark focus:bg-white focus:outline-none text-left font-bold text-sm text-sage-dark tracking-wider transition" />
                        <span class="absolute left-3 px-2.5 py-1 rounded-xl bg-sage-light/80 text-sage-dark font-extrabold text-[11px] border border-gold-soft/50 select-none" dir="ltr">
                            🇸🇦 +966
                        </span>
                    </div>
                    <p id="phoneVerifyError" class="hidden text-[11px] font-bold text-red-600 mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span id="phoneVerifyErrorText">يرجى إدخال رقم جوال سعودي صحيح (مثال: 0551234567).</span>
                    </p>
                    <p class="text-[11px] text-gray-400 mt-1">
                        يقبل الصيغة المحلية <span dir="ltr" class="font-semibold">05XXXXXXXX</span> أو الدولية <span dir="ltr" class="font-semibold">+9665XXXXXXXX</span>
                    </p>
                </div>

                <button type="submit"
                        id="phoneVerifySubmitBtn"
                        class="w-full py-3.5 rounded-2xl bg-sage-dark hover:bg-sage-deeper text-white font-extrabold text-sm shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-shield-check text-gold"></i>
                    <span>تحقق وتأكيد الحجز</span>
                </button>
            </form>
        </div>

        <!-- VIEW 2: GOOGLE REGISTRATION SCREEN (CLICKABLE LUXURY BUTTON ONLY - NO INPUT FIELDS) -->
        <div id="googleRegisterStep" class="hidden p-6 space-y-5">
            <div class="flex flex-col items-center text-center space-y-2.5">
                <!-- Luxury Google Emblem Container -->
                <div class="relative">
                    <div class="w-18 h-18 p-4 rounded-3xl bg-gradient-to-b from-white to-surface border-2 border-gold-soft shadow-md flex items-center justify-center">
                        <!-- Official 4-Color Google G SVG -->
                        <svg class="w-10 h-10" viewBox="0 0 48 48" aria-hidden="true">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-gold text-white text-[11px] flex items-center justify-center shadow-xs border-2 border-white">
                        <i class="fa-solid fa-sparkles"></i>
                    </span>
                </div>

                <span class="px-3 py-0.5 rounded-full bg-gold-light text-gold-dark text-[11px] font-extrabold border border-gold/30">
                    عميلة جديدة في هيرلاين
                </span>
                <h4 class="text-lg font-black text-sage-dark">التسجيل السريع عبر Google</h4>
                <p class="text-xs text-gray-500 leading-relaxed max-w-[310px]">
                    رقم الجوال <span id="googleStepPhoneBadge" dir="ltr" class="font-extrabold text-sage-dark bg-sage-soft px-2 py-0.5 rounded-md border border-gold-soft/50"></span> غير مسجل لدينا مسبقاً، انقري على الزر أدناه للتسجيل المباشر عبر حساب Google وتأكيد حجزكِ فوراً
                </p>
            </div>

            <!-- Luxury Clickable Google OAuth Button Card -->
            <div class="space-y-3">
                <button type="button"
                        id="googleOAuthBtn"
                        onclick="startGoogleSignInProcess()"
                        class="w-full p-4 rounded-2xl bg-gradient-to-l from-sage-dark via-sage-deeper to-sage-dark hover:from-sage-deeper hover:to-black text-white shadow-xl hover:shadow-2xl border-2 border-gold/50 hover:border-gold active:scale-[0.985] transition-all duration-300 flex items-center justify-between gap-3 group cursor-pointer">
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
                            <span id="googleOAuthBtnTitle" class="block font-extrabold text-sm sm:text-base text-white">
                                المتابعة باستخدام حساب Google
                            </span>
                            <span class="block text-[11px] text-gold-light/85 font-medium mt-0.5">
                                تسجيل بضغطة واحدة وتأكيد الحجز باسم صاحبة الإيميل
                            </span>
                        </div>
                    </div>

                    <div class="w-8 h-8 rounded-full bg-white/10 border border-gold/40 flex items-center justify-center text-gold group-hover:bg-gold group-hover:text-sage-dark transition-all shrink-0">
                        <span class="text-base font-bold leading-none -mt-0.5">‹</span>
                    </div>
                </button>

                <p id="googleRegisterError" class="hidden text-[11px] font-bold text-red-600 text-center flex items-center justify-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span id="googleRegisterErrorText">تعذر إتمام التسجيل عبر Google، يرجى المحاولة مرة أخرى.</span>
                </p>

                <!-- Security & Speed Reassurance Badges -->
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <div class="p-2.5 rounded-xl bg-surface border border-gold-soft/50 flex items-center justify-center gap-1.5 text-[11px] font-bold text-sage-dark">
                        <i class="fa-solid fa-lock text-gold-dark text-xs"></i>
                        <span>ربط آمن ومشفر 100%</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-surface border border-gold-soft/50 flex items-center justify-center gap-1.5 text-[11px] font-bold text-sage-dark">
                        <i class="fa-solid fa-bolt text-gold-dark text-xs"></i>
                        <span>تأكيد فوري للموعد</span>
                    </div>
                </div>

                <button type="button"
                        onclick="switchPopupToPhoneStep()"
                        class="w-full py-2 text-center text-xs font-bold text-gray-500 hover:text-sage-dark transition cursor-pointer">
                    تغيير رقم الجوال
                </button>
            </div>
        </div>
    </div>
</div>
