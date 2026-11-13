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
                        class="hidden w-9 h-9 rounded-full bg-white text-primary-dark shadow-xs flex items-center justify-center hover:bg-primary hover:text-white transition cursor-pointer">
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
         class="relative w-full max-w-[420px] rounded-[30px] bg-white shadow-2xl border border-gold-soft/80 overflow-hidden transform scale-95 transition-all duration-300">
        
        <!-- Top Gold Shimmer Bar -->
        <div class="h-1.5 w-full bg-gradient-to-r from-primary-light via-primary to-primary-light"></div>

        <!-- Close Popup Button -->
        <button type="button"
                onclick="closePhoneVerificationModal()"
                aria-label="إغلاق نافذة التحقق"
                class="absolute top-4 left-4 w-9 h-9 rounded-full bg-surface text-gray-400 hover:text-sage-dark hover:bg-gray-100 flex items-center justify-center border border-gray-200/80 transition cursor-pointer z-10 shadow-2xs">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <!-- VIEW 1: SAUDI PHONE NUMBER INPUT & VERIFICATION -->
        <div id="phoneVerifyStep" class="p-6 sm:p-7 space-y-4">
            <!-- LOGGED-IN CUSTOMER NOTICE VIEW -->
            <div id="loggedInCustomerNotice" class="hidden space-y-3.5">
                <div class="flex flex-col items-center text-center pt-1">
                    <!-- Luxury Avatar with Verified Badge -->
                    <div class="relative mb-2.5">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-gradient-to-b from-primary-light to-surface border-2 border-primary/40 flex items-center justify-center text-primary-dark shadow-sm">
                            <img id="loggedInCustomerAvatarDisplay"
                                 src=""
                                 alt="صورة الحساب"
                                 class="w-full h-full object-cover hidden"
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent(document.getElementById('loggedInCustomerNameDisplay')?.textContent || 'HL') + '&background=211C18&color=C5A373&bold=true';" />
                            <div id="loggedInCustomerAvatarFallback" class="w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-user-check text-2xl"></i>
                            </div>
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-5 h-5 rounded-full bg-emerald-500 text-white text-[10px] flex items-center justify-center shadow-xs border-2 border-white" title="حساب موثق">
                            <i class="fa-solid fa-check text-[9px]"></i>
                        </span>
                    </div>
                    
                    <h4 id="loggedInCustomerNameDisplay" class="text-xl font-black text-sage-dark tracking-tight"></h4>
                </div>

                <!-- Verified Phone Display Card -->
                <div class="p-3.5 rounded-2xl bg-surface border border-gray-200/80 flex items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-primary-light/70 border border-primary-soft/60 flex items-center justify-center text-primary-dark shrink-0">
                            <i class="fa-solid fa-mobile-screen-button text-sm"></i>
                        </div>
                        <div class="min-w-0 text-right">
                            <span class="block text-[11px] text-gray-500 font-bold leading-tight">رقم الجوال:</span>
                            <input id="loggedInPhoneDisplay"
                                   type="text"
                                   dir="ltr"
                                   readonly
                                   tabindex="-1"
                                   class="bg-transparent border-0 p-0 text-left font-black text-sm sm:text-base text-sage-dark tracking-wider cursor-default select-all focus:outline-none w-auto max-w-[150px]" />
                        </div>
                    </div>
                  
                </div>
            </div>

            <!-- CUSTOMER REGISTRATION INPUT VIEW (NAME + PHONE VISIBLE TOGETHER, NO GUEST BOOKING) -->
            <div id="guestPhoneInputContainer" class="space-y-4">
                <div class="flex flex-col items-center text-center space-y-1.5 pt-1">
                    <div class="w-15 h-15 rounded-full bg-gradient-to-b from-primary-light to-surface border-2 border-primary/40 flex items-center justify-center text-primary-dark shadow-sm mb-0.5">
                        <i class="fa-solid fa-user-check text-xl"></i>
                    </div>
                    <h4 class="text-lg sm:text-xl font-black text-sage-dark tracking-tight">تسجيل العميلة وتأكيد الحجز</h4>
                    <p class="text-[11px] sm:text-xs text-gray-500 font-medium">
                        أدخلي اسمكِ الكريم ورقم جوالكِ لتلقي رمز التحقق عبر واتساب
                    </p>
                </div>

                <!-- Field 1: Customer Name Input (Always Visible) -->
                <div id="guestNameInputContainer" class="space-y-1.5">
                    <label for="guestNameInput" class="block text-xs font-extrabold text-sage-dark">
                        الاسم الكريم <span class="text-red-500">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <input id="guestNameInput"
                               type="text"
                               name="name"
                               autocomplete="name"
                               oninput="handleCustomerNameInputValidation(this)"
                               class="w-full py-3.5 pr-4 pl-10 rounded-2xl bg-surface border border-gray-200 focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/15 focus:outline-none font-bold text-sm text-sage-dark transition" />
                        <i class="fa-regular fa-user text-gray-400 text-xs absolute left-3.5 pointer-events-none"></i>
                    </div>
                </div>

                <!-- Field 2: Saudi Mobile Phone Input (Always Visible) -->
                <div>
                    <label for="bookingPhoneInput" class="block text-xs font-extrabold text-sage-dark mb-1.5">
                        رقم الجوال <span class="text-red-500">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <input id="bookingPhoneInput"
                               type="tel"
                               name="phone"
                               inputmode="numeric"
                               dir="ltr"
                               maxlength="10"
                               placeholder="05XXXXXXXX"
                               autocomplete="tel"
                               oninput="handlePhoneInputValidation(this)"
                               class="w-full py-3.5 pr-4 pl-22 rounded-2xl bg-surface border border-gray-200 focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/15 focus:outline-none text-left font-bold text-sm text-sage-dark tracking-wider transition" />
                        <span class="absolute left-2.5 px-2.5 py-1 rounded-xl bg-white text-sage-dark font-extrabold text-[11px] border border-gray-200 select-none shadow-2xs" dir="ltr">
                            🇸🇦 +966
                        </span>
                    </div>
                </div>
            </div>

            <!-- Selected Service & Specialist Mini Summary -->
            <div id="popupBookingSummary" class="rounded-2xl bg-gradient-to-b from-surface to-white border border-gold-soft/80 p-3.5 shadow-2xs space-y-2.5">
                <!-- Populated dynamically without price -->
            </div>

            <form id="phoneVerifyForm" onsubmit="submitPhoneVerification(event)" class="space-y-3" novalidate>
                <p id="phoneVerifyError" class="hidden text-[11px] font-bold text-red-600 mt-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span id="phoneVerifyErrorText">يرجى إدخال رقم جوال سعودي صحيح.</span>
                </p>

                <button type="submit"
                        id="phoneVerifySubmitBtn"
                        class="w-full py-4 rounded-2xl bg-primary hover:bg-primary-dark text-white font-extrabold text-sm sm:text-base shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 active:scale-[0.985] transition-all duration-200 flex items-center justify-center gap-2.5 cursor-pointer">
                    <i id="phoneVerifySubmitBtnIcon" class="fa-solid fa-circle-check text-white text-base"></i>
                    <span id="phoneVerifySubmitBtnText">تأكيد الحجز</span>
                </button>

                <!-- Optional Google Login Button -->
                <div id="optionalGoogleContainer" class="pt-2 text-center space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="h-px bg-gray-200 flex-1"></div>
                        <span class="text-[11px] text-gray-400 font-bold">أو</span>
                        <div class="h-px bg-gray-200 flex-1"></div>
                    </div>
                    <button type="button"
                            onclick="startGoogleSignInProcess()"
                            class="w-full py-3 px-4 rounded-2xl bg-surface hover:bg-gold-light/40 border border-gray-200/90 text-xs font-extrabold text-sage-dark flex items-center justify-center gap-2.5 transition cursor-pointer shadow-2xs">
                        <svg class="w-4 h-4" viewBox="0 0 48 48" aria-hidden="true">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        <span>المتابعة باستخدام Google (اختياري)</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- VIEW 1.5: 4-DIGIT SMS OTP VERIFICATION SCREEN -->
        <div id="smsOtpVerifyStep" class="hidden p-6 sm:p-7 space-y-5 animate-step">
            <div class="flex flex-col items-center text-center space-y-2">
                <div id="otpChannelIconWrapper" class="w-15 h-15 rounded-full bg-gradient-to-b from-primary-light to-surface border-2 border-primary/40 flex items-center justify-center text-primary shadow-sm">
                    <i id="otpChannelIcon" class="fa-solid fa-comment-sms text-2xl"></i>
                </div>
                <span id="otpChannelBadge" class="px-3 py-0.5 rounded-full bg-gold-light text-gold-dark text-[11px] font-extrabold border border-gold/30">
                    تأكيد رقم الجوال عبر SMS
                </span>
                <h4 class="text-lg sm:text-xl font-black text-sage-dark">أدخلي رمز التحقق (4 أرقام)</h4>
                <p class="text-xs text-gray-500 leading-relaxed max-w-[320px]">
                    تم إرسال رمز التحقق للعميلة <span id="otpStepCustomerNameBadge" class="font-extrabold text-sage-dark"></span> <span id="otpChannelText">عبر رسالة نصية SMS</span> على الرقم
                    <span id="otpStepPhoneBadge" dir="ltr" class="font-extrabold text-primary-dark bg-primary-light/60 px-2 py-0.5 rounded-md border border-primary-soft/60"></span>
                </p>

                <!-- Simulation Mode Notice Banner (Hidden) -->
                <div id="otpSimulationBanner" class="hidden w-full py-2 px-3 rounded-xl bg-amber-50/90 border border-amber-200/80 text-amber-900 text-[11px] font-bold items-center justify-center gap-1.5">
                    <i class="fa-solid fa-flask text-amber-600 text-xs"></i>
                    <span>وضع المحاكاة للتطوير — الرمز الافتراضي:</span>
                    <strong id="otpSimulatedCodeText" class="font-mono font-black text-xs text-primary-dark tracking-widest bg-white px-2 py-0.5 rounded border border-amber-200">1100</strong>
                </div>
            </div>

            <form id="smsOtpVerifyForm" onsubmit="submitSmsOtpVerification(event)" class="space-y-4" novalidate>
                <!-- 4 OTP Digit Input Boxes -->
                <div class="flex items-center justify-center gap-3" dir="ltr">
                    <input type="text"
                           id="otpDigit0"
                           data-otp-index="0"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="1"
                           autocomplete="one-time-code"
                           aria-label="الرقم الأول من رمز التحقق"
                           oninput="handleOtpDigitInput(this, 0)"
                           onkeydown="handleOtpDigitKeydown(event, 0)"
                           onpaste="handleOtpPaste(event)"
                           class="otp-digit-box w-13 h-14 sm:w-14 sm:h-15 rounded-2xl bg-surface border-2 border-gray-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/15 focus:outline-none text-center font-black text-xl text-sage-dark transition-all" />
                    <input type="text"
                           id="otpDigit1"
                           data-otp-index="1"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="1"
                           aria-label="الرقم الثاني من رمز التحقق"
                           oninput="handleOtpDigitInput(this, 1)"
                           onkeydown="handleOtpDigitKeydown(event, 1)"
                           onpaste="handleOtpPaste(event)"
                           class="otp-digit-box w-13 h-14 sm:w-14 sm:h-15 rounded-2xl bg-surface border-2 border-gray-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/15 focus:outline-none text-center font-black text-xl text-sage-dark transition-all" />
                    <input type="text"
                           id="otpDigit2"
                           data-otp-index="2"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="1"
                           aria-label="الرقم الثالث من رمز التحقق"
                           oninput="handleOtpDigitInput(this, 2)"
                           onkeydown="handleOtpDigitKeydown(event, 2)"
                           onpaste="handleOtpPaste(event)"
                           class="otp-digit-box w-13 h-14 sm:w-14 sm:h-15 rounded-2xl bg-surface border-2 border-gray-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/15 focus:outline-none text-center font-black text-xl text-sage-dark transition-all" />
                    <input type="text"
                           id="otpDigit3"
                           data-otp-index="3"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="1"
                           aria-label="الرقم الرابع من رمز التحقق"
                           oninput="handleOtpDigitInput(this, 3)"
                           onkeydown="handleOtpDigitKeydown(event, 3)"
                           onpaste="handleOtpPaste(event)"
                           class="otp-digit-box w-13 h-14 sm:w-14 sm:h-15 rounded-2xl bg-surface border-2 border-gray-200 focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/15 focus:outline-none text-center font-black text-xl text-sage-dark transition-all" />
                </div>

                <p id="otpVerifyError" class="hidden text-[11px] font-bold text-red-600 text-center flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span id="otpVerifyErrorText">رمز التحقق غير صحيح.</span>
                </p>

                <button type="submit"
                        id="otpVerifySubmitBtn"
                        class="w-full py-4 rounded-2xl bg-primary hover:bg-primary-dark text-white font-extrabold text-sm sm:text-base shadow-lg shadow-primary/25 hover:shadow-xl active:scale-[0.985] transition-all duration-200 flex items-center justify-center gap-2.5 cursor-pointer">
                    <i class="fa-solid fa-shield-check text-white text-base"></i>
                    <span id="otpVerifySubmitBtnText">تأكيد الرمز وإتمام التسجيل</span>
                </button>

                <div class="flex items-center justify-between pt-1 text-xs font-bold">
                    <button type="button"
                            id="otpResendBtn"
                            onclick="resendBookingSmsOtp()"
                            class="text-primary-dark hover:underline cursor-pointer flex items-center gap-1">
                        <i class="fa-solid fa-rotate-right text-[10px]"></i>
                        <span id="otpResendBtnText">إعادة إرسال الرمز</span>
                    </button>

                    <button type="button"
                            onclick="switchPopupToPhoneStep()"
                            class="text-gray-500 hover:text-sage-dark transition cursor-pointer flex items-center gap-1">
                        <i class="fa-solid fa-pen text-[10px]"></i>
                        <span>تعديل الاسم أو الجوال</span>
                    </button>
                </div>
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
                    التحقق عبر Google
                </span>
                <h4 class="text-lg font-black text-sage-dark">التحقق وتأكيد الحجز عبر Google</h4>
                <p class="text-xs text-gray-500 leading-relaxed max-w-[310px]">
                    رقم الجوال <span id="googleStepPhoneBadge" dir="ltr" class="font-extrabold text-sage-dark bg-sage-soft px-2 py-0.5 rounded-md border border-gold-soft/50"></span>، انقري على الزر أدناه للمتابعة بحساب Google المرتبط وتأكيد حجزكِ فوراً
                </p>
            </div>

            <!-- Luxury Clickable Google OAuth Button Card -->
            <div class="space-y-3">
                <button type="button"
                        id="googleOAuthBtn"
                        onclick="startGoogleSignInProcess()"
                        class="w-full p-4 rounded-2xl bg-primary hover:bg-primary-dark text-white shadow-xl hover:shadow-2xl border-2 border-white/60 active:scale-[0.985] transition-all duration-300 flex items-center justify-between gap-3 group cursor-pointer">
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
                            <span class="block text-[11px] text-white/90 font-medium mt-0.5">
                                تسجيل بضغطة واحدة وتأكيد الحجز باسم صاحبة الإيميل
                            </span>
                        </div>
                    </div>

                    <div class="w-8 h-8 rounded-full bg-white/20 border border-white/40 flex items-center justify-center text-white group-hover:bg-white group-hover:text-primary-dark transition-all shrink-0">
                        <span class="text-base font-bold leading-none -mt-0.5">‹</span>
                    </div>
                </button>

                <p id="googleRegisterError" class="hidden text-[11px] font-bold text-red-600 text-center flex items-center justify-center gap-1">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span id="googleRegisterErrorText">تعذر إتمام التسجيل عبر Google، يرجى المحاولة مرة أخرى.</span>
                </p>

              

                <button type="button"
                        onclick="switchPopupToPhoneStep()"
                        class="w-full py-2 text-center text-xs font-bold text-gray-500 hover:text-sage-dark transition cursor-pointer">
                    تغيير رقم الجوال
                </button>
            </div>
        </div>
    </div>
</div>
