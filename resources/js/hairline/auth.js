import { state, SAUDI_PHONE_REGEX, routes, csrfToken } from './state.js';
import { openToast } from './ui-modals.js';
import { renderCustomerProfile, renderMyBookings } from './profile.js';
import { updateBookingBadge } from './navigation.js';
import { fetchSpecialistAvailability, renderBookingStep } from './booking-wizard.js';

export function normalizeDigitsJs(str) {
    const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    const western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    let out = String(str || '').trim();
    for (let i = 0; i < eastern.length; i++) {
        out = out.split(eastern[i]).join(western[i]);
    }
    return out.replace(/[\s\-\(\)]+/g, '');
}

export function openPhoneVerificationModal() {
    const modal = document.getElementById('bookingVerificationModal');
    const card = document.getElementById('bookingVerificationCard');
    const summaryEl = document.getElementById('popupBookingSummary');
    const phoneInput = document.getElementById('bookingPhoneInput');
    const errorEl = document.getElementById('phoneVerifyError');
    const loggedInNotice = document.getElementById('loggedInCustomerNotice');
    const guestContainer = document.getElementById('guestPhoneInputContainer');
    const loggedInName = document.getElementById('loggedInCustomerNameDisplay');
    const loggedInPhone = document.getElementById('loggedInPhoneDisplay');
    const loggedInAvatar = document.getElementById('loggedInCustomerAvatarDisplay');
    const loggedInAvatarFallback = document.getElementById('loggedInCustomerAvatarFallback');
    const btnText = document.getElementById('phoneVerifySubmitBtnText');
    const btnIcon = document.getElementById('phoneVerifySubmitBtnIcon');
    const nameContainer = document.getElementById('guestNameInputContainer');
    const returningGreeting = document.getElementById('returningCustomerGreeting');

    if (!modal || !card) return;

    state.bookingState.phoneChecked = false;
    if (nameContainer) nameContainer.classList.add('hidden');
    if (returningGreeting) returningGreeting.classList.add('hidden');

    const timeSlot = state.bookingState.selectedTime || state.bookingState.selectedSpecialist?.availableSlots?.[0];
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    const servicesTitle = subServices.map(s => s.name).join(' + ');
    const hasMultiple = subServices.length > 1;

    const guestsCount = state.bookingState.guestsCount || parseInt(document.getElementById('bookingGuestsCountInput')?.value || '1', 10) || 1;
    const customerNotes = state.bookingState.customerNotes || document.getElementById('bookingCustomerNotesInput')?.value || '';

    if (summaryEl) {
        let servicesHtml = '';
        if (hasMultiple) {
            servicesHtml = `
                <div class="space-y-1.5 py-1 text-right">
                    ${subServices.map(s => {
                        const sp = state.bookingState.selectedSpecialistsByService?.[s.id] || state.bookingState.selectedSpecialist;
                        const spName = sp ? (sp.name || '').split('|')[0].trim() : '';
                        return `
                            <div class="flex items-center justify-between text-xs bg-gray-50/70 p-2 rounded-xl border border-gray-100">
                                <span class="font-bold text-sage-dark">${s.name}</span>
                                <span class="text-primary font-black"><i class="fa-regular fa-user text-[10px] ml-1 text-gold-dark"></i>${spName}</span>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        }

        summaryEl.innerHTML = `
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2 h-2 rounded-full bg-primary shrink-0"></span>
                    <span class="font-extrabold text-sm text-sage-dark truncate">${servicesTitle}</span>
                </div>
            </div>
            ${hasMultiple ? servicesHtml : ''}
            <div class="border-t border-dashed border-gray-200/80"></div>
            <div class="grid grid-cols-2 gap-2 text-[11px] text-gray-500 font-medium">
                <div class="flex items-center gap-2 min-w-0">
                    ${!hasMultiple && state.bookingState.selectedSpecialist?.avatar ? `
                        <img src="${state.bookingState.selectedSpecialist.avatar}"
                             alt="${state.bookingState.selectedSpecialist.name}"
                             onerror="this.onerror=null; this.classList.add('hidden'); if(this.nextElementSibling) this.nextElementSibling.classList.remove('hidden');"
                             class="w-6 h-6 rounded-full object-cover shrink-0 border border-primary/40 shadow-2xs" />
                        <i class="fa-regular fa-user text-primary-dark text-xs shrink-0 hidden"></i>
                    ` : `
                        <i class="fa-regular fa-user text-primary-dark text-xs shrink-0"></i>
                    `}
                    <span class="truncate font-bold text-sage-dark">
                        ${hasMultiple 
                            ? subServices.map(s => {
                                const sp = state.bookingState.selectedSpecialistsByService?.[s.id] || state.bookingState.selectedSpecialist;
                                return sp ? (sp.name || '').split('|')[0].trim() : '';
                            }).filter(Boolean).filter((v, i, a) => a.indexOf(v) === i).join(' + ')
                            : (state.bookingState.selectedSpecialist?.name || '')}
                    </span>
                </div>
                <div class="flex items-center gap-1.5 justify-end text-left min-w-0">
                    <i class="fa-regular fa-calendar-check text-gold-dark text-[11px] shrink-0"></i>
                    <span class="truncate font-semibold text-sage-dark">${state.bookingState.selectedDate} • ${timeSlot}</span>
                </div>
            </div>
            ${guestsCount > 1 ? `
                <div class="text-[11px] text-gray-500 font-medium flex items-center gap-1.5 pt-1 border-t border-dashed border-gray-200/80">
                    <i class="fa-solid fa-users text-primary text-[10px]"></i>
                    <span>عدد الأشخاص: <strong class="text-sage-dark font-extrabold">${guestsCount} أشخاص</strong></span>
                </div>
            ` : ''}
            ${customerNotes ? `
                <div class="text-[11px] text-gray-500 font-medium flex items-start gap-1.5 pt-1 border-t border-dashed border-gray-200/80">
                    <i class="fa-solid fa-comment-dots text-primary text-[10px] mt-0.5 shrink-0"></i>
                    <span class="truncate">ملاحظاتكِ: <span class="text-sage-dark font-semibold">${customerNotes}</span></span>
                </div>
            ` : ''}
        `;
    }

    if (state.currentCustomer && state.currentCustomer.id) {
        if (loggedInNotice) loggedInNotice.classList.remove('hidden');
        if (guestContainer) guestContainer.classList.add('hidden');
        if (loggedInName) loggedInName.textContent = state.currentCustomer.name || 'عميلتنا العزيزة';
        if (loggedInPhone) loggedInPhone.value = state.currentCustomer.phone || '';

        const customerAvatarSrc = state.currentCustomer.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(state.currentCustomer.name || 'HL')}&background=FFFFFF&color=C29E6D&bold=true`;
        if (loggedInAvatar) {
            loggedInAvatar.src = customerAvatarSrc;
            loggedInAvatar.alt = state.currentCustomer.name || 'صورة الحساب';
            loggedInAvatar.classList.remove('hidden');
        }
        if (loggedInAvatarFallback) {
            loggedInAvatarFallback.classList.add('hidden');
        }

        if (btnText) btnText.textContent = 'تأكيد الحجز';
        if (btnIcon) btnIcon.className = 'fa-solid fa-circle-check text-white';
    } else {
        if (loggedInNotice) loggedInNotice.classList.add('hidden');
        if (guestContainer) guestContainer.classList.remove('hidden');
        if (loggedInAvatar) {
            loggedInAvatar.src = '';
            loggedInAvatar.classList.add('hidden');
        }
        if (loggedInAvatarFallback) {
            loggedInAvatarFallback.classList.remove('hidden');
        }
        if (btnText) btnText.textContent = 'تأكيد الحجز';
        if (btnIcon) btnIcon.className = 'fa-solid fa-circle-check text-white';
    }

    switchPopupToPhoneStep();
    if (errorEl) errorEl.classList.add('hidden');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
        if (phoneInput && (!state.currentCustomer || !state.currentCustomer.id)) phoneInput.focus();
    });
}

export function closePhoneVerificationModal() {
    const modal = document.getElementById('bookingVerificationModal');
    const card = document.getElementById('bookingVerificationCard');
    if (!modal || !card) return;

    modal.classList.add('opacity-0');
    card.classList.remove('scale-100');
    card.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 250);
}

export function switchPopupToPhoneStep() {
    const phoneStep = document.getElementById('phoneVerifyStep');
    const googleStep = document.getElementById('googleRegisterStep');
    if (phoneStep) phoneStep.classList.remove('hidden');
    if (googleStep) googleStep.classList.add('hidden');
}

export function switchPopupToGoogleStep(phone) {
    const phoneStep = document.getElementById('phoneVerifyStep');
    const googleStep = document.getElementById('googleRegisterStep');
    const badge = document.getElementById('googleStepPhoneBadge');
    const errorEl = document.getElementById('googleRegisterError');

    if (badge) badge.textContent = phone;
    if (errorEl) errorEl.classList.add('hidden');
    if (phoneStep) phoneStep.classList.add('hidden');
    if (googleStep) googleStep.classList.remove('hidden');
}

export function handlePhoneInputValidation(inputEl) {
    if (!inputEl) return;
    const errorEl = document.getElementById('phoneVerifyError');
    let val = normalizeDigitsJs(inputEl.value).replace(/\D/g, '');

    // Normalize international prefix if pasted (e.g. 009665... or 9665...) to standard 05...
    if (val.startsWith('009665')) {
        val = '0' + val.slice(5);
    } else if (val.startsWith('9665')) {
        val = '0' + val.slice(3);
    }

    // Strict Saudi mobile length: maximum 10 digits (05XXXXXXXX) - prevents any extra digits
    if (val.length > 10) {
        val = val.slice(0, 10);
    }

    if (inputEl.value !== val) {
        inputEl.value = val;
    }

    if (val.length === 0 || SAUDI_PHONE_REGEX.test(val)) {
        if (errorEl) errorEl.classList.add('hidden');
        inputEl.classList.remove('border-red-500');
    }
}

export async function submitPhoneVerification(event) {
    if (event) event.preventDefault();
    const phoneInput = document.getElementById('bookingPhoneInput');
    const errorEl = document.getElementById('phoneVerifyError');
    const errorText = document.getElementById('phoneVerifyErrorText');
    const submitBtn = document.getElementById('phoneVerifySubmitBtn');
    const btnText = document.getElementById('phoneVerifySubmitBtnText');
    const nameContainer = document.getElementById('guestNameInputContainer');
    const nameInput = document.getElementById('guestNameInput');
    const returningGreeting = document.getElementById('returningCustomerGreeting');
    const returningName = document.getElementById('returningCustomerName');

    const isCustomerLoggedIn = Boolean(state.currentCustomer && state.currentCustomer.id && state.currentCustomer.phone);
    const rawPhone = isCustomerLoggedIn
        ? normalizeDigitsJs(state.currentCustomer.phone)
        : normalizeDigitsJs(phoneInput ? phoneInput.value : '');

    if (!isCustomerLoggedIn && !SAUDI_PHONE_REGEX.test(rawPhone)) {
        if (errorText) {
            errorText.textContent = 'يرجى إدخال رقم جوال سعودي صحيح.';
        }
        if (errorEl) errorEl.classList.remove('hidden');
        if (phoneInput) phoneInput.classList.add('border-red-500');
        return;
    }

    if (errorEl) errorEl.classList.add('hidden');
    if (phoneInput) phoneInput.classList.remove('border-red-500');

    const timeSlot = state.bookingState.selectedTime;
    if (!timeSlot) {
        if (errorText) errorText.textContent = 'يرجى اختيار وقت متاح للحجز أولاً.';
        if (errorEl) errorEl.classList.remove('hidden');
        return;
    }
    const bookingDateVal = state.bookingState.selectedDateIso || state.bookingState.selectedDate;
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    const serviceIds = subServices.map(s => s.id);
    const primaryServiceId = subServices[0]?.id || state.bookingState.selectedSubService?.id;
    const items = subServices.map(s => ({
        service_id: s.id,
        specialist_id: state.bookingState.selectedSpecialistsByService?.[s.id]?.id || state.bookingState.selectedSpecialist?.id
    }));

    const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
    const verifyPhoneUrl = routes.verifyPhone || '/booking/verify-phone';
    const guestConfirmUrl = routes.guestConfirm || '/booking/guest-confirm';
    const guestsCount = state.bookingState.guestsCount || parseInt(document.getElementById('bookingGuestsCountInput')?.value || '1', 10) || 1;
    const customerNotes = state.bookingState.customerNotes || document.getElementById('bookingCustomerNotesInput')?.value || '';

    // CASE 1: Customer already logged in
    if (isCustomerLoggedIn) {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري تأكيد حجزكِ باسم ${state.currentCustomer.name}...</span>`;
        }
        try {
            const response = await fetch(verifyPhoneUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    phone: rawPhone,
                    service_ids: serviceIds,
                    service_id: primaryServiceId,
                    specialist_id: items[0]?.specialist_id || state.bookingState.selectedSpecialist?.id,
                    items: items,
                    booking_date: bookingDateVal,
                    booking_time: timeSlot,
                    guests_count: guestsCount,
                    customer_notes: customerNotes
                })
            });
            const data = await response.json();
            if (!response.ok || data.status === 'validation_error' || data.status === 'slot_unavailable') {
                if (errorText) errorText.textContent = data.message || 'تأكدي من صحة البيانات المدخلة.';
                if (errorEl) errorEl.classList.remove('hidden');
                fetchSpecialistAvailability();
                return;
            }
            if (data.status === 'confirmed') {
                finalizeConfirmedBookingFromServer(data);
            }
        } catch (err) {
            if (errorText) errorText.textContent = 'حدث خطأ أثناء الاتصال بالخادم، يرجى المحاولة مرة أخرى.';
            if (errorEl) errorEl.classList.remove('hidden');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }
        return;
    }

    // CASE 2: Guest booking - Step A: Check if phone exists
    if (!state.bookingState.phoneChecked) {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري فحص الرقم...</span>`;
        }
        try {
            const response = await fetch(verifyPhoneUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    phone: rawPhone
                })
            });
            const data = await response.json();
            state.bookingState.verifiedPhone = data.phone || rawPhone;
            state.bookingState.phoneChecked = true;

            if (data.status === 'customer_found') {
                if (returningGreeting) returningGreeting.classList.remove('hidden');
                if (returningName) returningName.textContent = data.customer_name || 'عميلتنا العزيزة';
                if (nameContainer) nameContainer.classList.add('hidden');
                if (btnText) btnText.textContent = `تأكيد الحجز فوراً كـ زائر (${data.customer_name})`;
            } else {
                if (nameContainer) nameContainer.classList.remove('hidden');
                if (returningGreeting) returningGreeting.classList.add('hidden');
                if (btnText) btnText.textContent = 'المتابعة كـ زائر وتأكيد الحجز';
                if (nameInput) setTimeout(() => nameInput.focus(), 150);
            }
        } catch (err) {
            if (errorText) errorText.textContent = 'حدث خطأ أثناء فحص رقم الجوال.';
            if (errorEl) errorEl.classList.remove('hidden');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }
        return;
    }

    // CASE 2: Guest booking - Step B: Confirm booking as guest!
    const guestName = nameInput ? nameInput.value.trim() : '';
    const isNameRequired = nameContainer && !nameContainer.classList.contains('hidden');
    if (isNameRequired && guestName.length < 2) {
        if (errorText) errorText.textContent = 'يرجى كتابة اسمكِ الكريم لإتمام الحجز كـ زائر.';
        if (errorEl) errorEl.classList.remove('hidden');
        if (nameInput) nameInput.focus();
        return;
    }

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري تأكيد حجزكِ كـ زائر...</span>`;
    }

    try {
        const response = await fetch(guestConfirmUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                phone: state.bookingState.verifiedPhone || rawPhone,
                name: guestName,
                service_ids: serviceIds,
                service_id: primaryServiceId,
                specialist_id: items[0]?.specialist_id || state.bookingState.selectedSpecialist?.id,
                items: items,
                booking_date: bookingDateVal,
                booking_time: timeSlot,
                guests_count: guestsCount,
                customer_notes: customerNotes
            })
        });

        const data = await response.json();

        if (!response.ok || data.status === 'validation_error' || data.status === 'slot_unavailable') {
            if (errorText) errorText.textContent = data.message || 'تأكدي من صحة البيانات المدخلة.';
            if (errorEl) errorEl.classList.remove('hidden');
            fetchSpecialistAvailability();
            return;
        }

        if (data.status === 'confirmed') {
            finalizeConfirmedBookingFromServer(data);
        }
    } catch (err) {
        if (errorText) errorText.textContent = 'حدث خطأ أثناء حفظ الحجز، يرجى المحاولة مرة أخرى.';
        if (errorEl) errorEl.classList.remove('hidden');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }
    }
}

export function startGoogleSignInProcess(context = null) {
    const errorEl = document.getElementById('googleRegisterError');
    const profileErrorEl = document.getElementById('profileGoogleRegisterError');
    const btnTitle = document.getElementById('googleOAuthBtnTitle');
    const profileBtnTitle = document.getElementById('profileGoogleOAuthBtnTitle');
    const errorText = document.getElementById('googleRegisterErrorText');
    const profileErrorText = document.getElementById('profileGoogleRegisterErrorText');

    if (errorEl) errorEl.classList.add('hidden');
    if (profileErrorEl) profileErrorEl.classList.add('hidden');
    if (btnTitle) btnTitle.textContent = 'جاري الاتصال بحساب Google...';
    if (profileBtnTitle) profileBtnTitle.textContent = 'جاري الاتصال بحساب Google...';

    const googleClientId = window.__HAIRLINE_DATA__?.googleClientId || null;
    const isProfile = (context === 'profile' || state.currentActiveTab === 'account');

    const resetBtnState = (errorMsg) => {
        if (btnTitle) btnTitle.textContent = 'المتابعة باستخدام حساب Google';
        if (profileBtnTitle) profileBtnTitle.textContent = 'المتابعة باستخدام حساب Google';
        if (errorMsg) {
            if (errorText) errorText.textContent = errorMsg;
            if (errorEl) errorEl.classList.remove('hidden');
            if (profileErrorText) profileErrorText.textContent = errorMsg;
            if (profileErrorEl) profileErrorEl.classList.remove('hidden');
        }
    };

    if (window.google && window.google.accounts && window.google.accounts.oauth2 && googleClientId) {
        try {
            const codeClient = window.google.accounts.oauth2.initCodeClient({
                client_id: googleClientId,
                scope: 'openid email profile',
                ux_mode: 'popup',
                callback: function (response) {
                    if (response && response.code) {
                        completeGoogleBookingWithOAuth({ code: response.code }, isProfile);
                    } else {
                        resetBtnState('تم إلغاء عملية تسجيل الدخول عبر Google.');
                    }
                },
                error_callback: function () {
                    openGoogleOAuthRedirectPopup(isProfile);
                }
            });
            codeClient.requestCode();
            return;
        } catch (e) {
            openGoogleOAuthRedirectPopup(isProfile);
            return;
        }
    }

    openGoogleOAuthRedirectPopup(isProfile);
}

export function openGoogleOAuthRedirectPopup(isProfileOnly = false) {
    const isProfile = isProfileOnly || state.currentActiveTab === 'account';
    const timeSlot = isProfile ? '' : (state.bookingState.selectedTime || '');
    const bookingDateVal = isProfile ? '' : (state.bookingState.selectedDateIso || state.bookingState.selectedDate || '');
    const guestsCount = state.bookingState.guestsCount || parseInt(document.getElementById('bookingGuestsCountInput')?.value || '1', 10) || 1;
    const customerNotes = state.bookingState.customerNotes || document.getElementById('bookingCustomerNotesInput')?.value || '';

    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    const items = subServices.map(s => ({
        service_id: s.id,
        specialist_id: state.bookingState.selectedSpecialistsByService?.[s.id]?.id || state.bookingState.selectedSpecialist?.id
    }));

    const params = new URLSearchParams(isProfile ? {
        is_profile: '1'
    } : {
        is_profile: '0',
        phone: state.bookingState.verifiedPhone || '',
        service_id: state.bookingState.selectedSubService?.id || '',
        specialist_id: items[0]?.specialist_id || state.bookingState.selectedSpecialist?.id || '',
        items: JSON.stringify(items),
        booking_date: bookingDateVal,
        booking_time: timeSlot,
        guests_count: guestsCount,
        customer_notes: customerNotes
    });

    const width = 520;
    const height = 640;
    const left = Math.max(0, (window.screen.width - width) / 2);
    const top = Math.max(0, (window.screen.height - height) / 2);

    const redirectUrl = routes.googleRedirect || '/auth/google/redirect';

    const popup = window.open(
        `${redirectUrl}?${params.toString()}`,
        "HairlineGoogleOAuth",
        `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,status=yes`
    );

    if (!popup) {
        const msg = 'يرجى السماح بالنوافذ المنبثقة (Popups) لإتمام تسجيل الدخول عبر Google.';
        const btnTitle = document.getElementById('googleOAuthBtnTitle');
        const profileBtnTitle = document.getElementById('profileGoogleOAuthBtnTitle');
        const errorEl = document.getElementById('googleRegisterError');
        const profileErrorEl = document.getElementById('profileGoogleRegisterError');
        const errorText = document.getElementById('googleRegisterErrorText');
        const profileErrorText = document.getElementById('profileGoogleRegisterErrorText');
        if (btnTitle) btnTitle.textContent = 'المتابعة باستخدام حساب Google';
        if (profileBtnTitle) profileBtnTitle.textContent = 'المتابعة باستخدام حساب Google';
        if (errorText) errorText.textContent = msg;
        if (errorEl) errorEl.classList.remove('hidden');
        if (profileErrorText) profileErrorText.textContent = msg;
        if (profileErrorEl) profileErrorEl.classList.remove('hidden');
    }
}

export async function completeGoogleBookingWithOAuth(oauthPayload, isProfileOnly = false) {
    const btn = document.getElementById('googleOAuthBtn');
    const profileBtn = document.getElementById('profileGoogleOAuthBtn');
    const btnTitle = document.getElementById('googleOAuthBtnTitle');
    const profileBtnTitle = document.getElementById('profileGoogleOAuthBtnTitle');
    const errorEl = document.getElementById('googleRegisterError');
    const profileErrorEl = document.getElementById('profileGoogleRegisterError');
    const errorText = document.getElementById('googleRegisterErrorText');
    const profileErrorText = document.getElementById('profileGoogleRegisterErrorText');

    if (errorEl) errorEl.classList.add('hidden');
    if (profileErrorEl) profileErrorEl.classList.add('hidden');
    if (btn) btn.disabled = true;
    if (profileBtn) profileBtn.disabled = true;
    if (btnTitle) btnTitle.textContent = 'جاري التحقق...';
    if (profileBtnTitle) profileBtnTitle.textContent = 'جاري التحقق...';

    try {
        const isProfile = isProfileOnly || state.currentActiveTab === 'account';
        const timeSlot = isProfile ? '' : (state.bookingState.selectedTime || '');
        const bookingDateVal = isProfile ? '' : (state.bookingState.selectedDateIso || state.bookingState.selectedDate || '');
        const registerUrl = routes.googleRegister || '/booking/google-register';
        const guestsCount = state.bookingState.guestsCount || parseInt(document.getElementById('bookingGuestsCountInput')?.value || '1', 10) || 1;
        const customerNotes = state.bookingState.customerNotes || document.getElementById('bookingCustomerNotesInput')?.value || '';

        const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
            ? state.bookingState.selectedSubServices
            : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
        const items = subServices.map(s => ({
            service_id: s.id,
            specialist_id: state.bookingState.selectedSpecialistsByService?.[s.id]?.id || state.bookingState.selectedSpecialist?.id
        }));

        const payloadData = isProfile
            ? Object.assign({ is_profile: true }, oauthPayload)
            : Object.assign({
                phone: state.bookingState.verifiedPhone || '',
                service_ids: (state.bookingState.selectedSubServices || []).map(s => s.id),
                service_id: state.bookingState.selectedSubService?.id || (state.bookingState.selectedSubServices?.[0]?.id) || '',
                specialist_id: items[0]?.specialist_id || state.bookingState.selectedSpecialist?.id || '',
                items: items,
                booking_date: bookingDateVal,
                booking_time: timeSlot,
                guests_count: guestsCount,
                customer_notes: customerNotes
            }, oauthPayload);

        const response = await fetch(registerUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payloadData)
        });

        const data = await response.json();

        if (!response.ok || data.status === 'validation_error' || data.status === 'slot_unavailable') {
            const msg = data.message || 'تعذر التحقق من حساب Google.';
            if (errorText) errorText.textContent = msg;
            if (errorEl) errorEl.classList.remove('hidden');
            if (profileErrorText) profileErrorText.textContent = msg;
            if (profileErrorEl) profileErrorEl.classList.remove('hidden');
            return;
        }

        if (data.status === 'confirmed') {
            finalizeConfirmedBookingFromServer(data);
        }
    } catch (err) {
        const msg = 'حدث خطأ أثناء الاتصال بالخادم، يرجى المحاولة مرة أخرى.';
        if (errorText) errorText.textContent = msg;
        if (errorEl) errorEl.classList.remove('hidden');
        if (profileErrorText) profileErrorText.textContent = msg;
        if (profileErrorEl) profileErrorEl.classList.remove('hidden');
    } finally {
        if (btn) btn.disabled = false;
        if (profileBtn) profileBtn.disabled = false;
        if (btnTitle) btnTitle.textContent = 'المتابعة باستخدام حساب Google';
        if (profileBtnTitle) profileBtnTitle.textContent = 'المتابعة باستخدام حساب Google';
    }
}

export function finalizeConfirmedBookingFromServer(data) {
    closePhoneVerificationModal();

    if (data.customer) {
        state.currentCustomer = data.customer;
    }

    const customerName = data.customer?.name || state.currentCustomer?.name || '';
    state.bookingState.confirmedCustomerName = customerName;
    state.bookingState.confirmedCustomerEmail = data.customer?.email || '';

    if (Array.isArray(data.customer_bookings)) {
        state.confirmedBookings = data.customer_bookings;
    } else if (data.booking) {
        state.confirmedBookings.unshift(data.booking);
    }

    renderCustomerProfile();
    renderMyBookings();
    updateBookingBadge();

    if (data.booking) {
        state.bookingState.step = 4;
        renderBookingStep();
        openToast('تم تأكيد الحجز بنجاح');
    } else {
        openToast(`مرحباً بكِ ${customerName}، تم تسجيل دخولكِ بنجاح ✨`);
    }
}

export function initAuthMessageListener() {
    window.addEventListener('message', function (event) {
        if (!event.data || event.data.type !== 'HAIRLINE_GOOGLE_AUTH_RESULT') return;
        const payload = event.data.payload;
        const btn = document.getElementById('googleOAuthBtn');
        const profileBtn = document.getElementById('profileGoogleOAuthBtn');
        const btnTitle = document.getElementById('googleOAuthBtnTitle');
        const profileBtnTitle = document.getElementById('profileGoogleOAuthBtnTitle');
        const errorEl = document.getElementById('googleRegisterError');
        const profileErrorEl = document.getElementById('profileGoogleRegisterError');
        const errorText = document.getElementById('googleRegisterErrorText');
        const profileErrorText = document.getElementById('profileGoogleRegisterErrorText');

        if (btn) btn.disabled = false;
        if (profileBtn) profileBtn.disabled = false;
        if (btnTitle) btnTitle.textContent = 'المتابعة باستخدام حساب Google';
        if (profileBtnTitle) profileBtnTitle.textContent = 'المتابعة باستخدام حساب Google';

        if (payload && payload.status === 'confirmed') {
            finalizeConfirmedBookingFromServer(payload);
        } else {
            const msg = payload?.message || 'تعذر إتمام التسجيل عبر Google، يرجى المحاولة مرة أخرى.';
            if (errorText) errorText.textContent = msg;
            if (errorEl) errorEl.classList.remove('hidden');
            if (profileErrorText) profileErrorText.textContent = msg;
            if (profileErrorEl) profileErrorEl.classList.remove('hidden');
        }
    });
}
