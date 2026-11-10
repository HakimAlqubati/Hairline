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
    const nameInput = document.getElementById('guestNameInput');
    const nameContainer = document.getElementById('guestNameInputContainer');
    const errorEl = document.getElementById('phoneVerifyError');
    const loggedInNotice = document.getElementById('loggedInCustomerNotice');
    const guestContainer = document.getElementById('guestPhoneInputContainer');
    const loggedInName = document.getElementById('loggedInCustomerNameDisplay');
    const loggedInPhone = document.getElementById('loggedInPhoneDisplay');
    const loggedInAvatar = document.getElementById('loggedInCustomerAvatarDisplay');
    const loggedInAvatarFallback = document.getElementById('loggedInCustomerAvatarFallback');
    const btnText = document.getElementById('phoneVerifySubmitBtnText');
    const btnIcon = document.getElementById('phoneVerifySubmitBtnIcon');

    if (!modal || !card) return;

    state.bookingState.phoneChecked = false;
    if (nameContainer) nameContainer.classList.remove('hidden');

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
        if (btnIcon) btnIcon.className = 'fa-solid fa-circle-check text-white text-base';
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
        if (btnText) btnText.textContent = 'إرسال رمز التحقق وتأكيد البيانات';
        if (btnIcon) btnIcon.className = 'fa-solid fa-paper-plane text-white text-sm';
    }

    switchPopupToPhoneStep();
    if (errorEl) errorEl.classList.add('hidden');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
        if (!state.currentCustomer || !state.currentCustomer.id) {
            if (nameInput && !nameInput.value.trim()) {
                nameInput.focus();
            } else if (phoneInput) {
                phoneInput.focus();
            }
        }
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
    const otpStep = document.getElementById('smsOtpVerifyStep');
    const googleStep = document.getElementById('googleRegisterStep');
    if (phoneStep) phoneStep.classList.remove('hidden');
    if (otpStep) otpStep.classList.add('hidden');
    if (googleStep) googleStep.classList.add('hidden');
}

export function switchPopupToOtpStep(otpData = {}) {
    const phoneStep = document.getElementById('phoneVerifyStep');
    const otpStep = document.getElementById('smsOtpVerifyStep');
    const googleStep = document.getElementById('googleRegisterStep');
    const nameBadge = document.getElementById('otpStepCustomerNameBadge');
    const phoneBadge = document.getElementById('otpStepPhoneBadge');
    const simBanner = document.getElementById('otpSimulationBanner');
    const simCodeText = document.getElementById('otpSimulatedCodeText');
    const otpError = document.getElementById('otpVerifyError');

    if (nameBadge) nameBadge.textContent = otpData.name || state.bookingState.pendingCustomerName || '';
    if (phoneBadge) phoneBadge.textContent = otpData.phone || state.bookingState.verifiedPhone || '';

    if (simBanner) {
        if (otpData.is_simulation === false) {
            simBanner.classList.add('hidden');
        } else {
            simBanner.classList.remove('hidden');
            if (simCodeText) {
                simCodeText.textContent = otpData.simulated_code || '1100';
            }
        }
    }

    if (otpError) otpError.classList.add('hidden');
    clearOtpDigitBoxes();

    if (phoneStep) phoneStep.classList.add('hidden');
    if (googleStep) googleStep.classList.add('hidden');
    if (otpStep) otpStep.classList.remove('hidden');

    setTimeout(() => {
        const firstBox = document.getElementById('otpDigit0');
        if (firstBox) firstBox.focus();
    }, 100);
}

export function switchPopupToGoogleStep(phone) {
    const phoneStep = document.getElementById('phoneVerifyStep');
    const otpStep = document.getElementById('smsOtpVerifyStep');
    const googleStep = document.getElementById('googleRegisterStep');
    const badge = document.getElementById('googleStepPhoneBadge');
    const errorEl = document.getElementById('googleRegisterError');

    if (badge) badge.textContent = phone;
    if (errorEl) errorEl.classList.add('hidden');
    if (phoneStep) phoneStep.classList.add('hidden');
    if (otpStep) otpStep.classList.add('hidden');
    if (googleStep) googleStep.classList.remove('hidden');
}

export function handleCustomerNameInputValidation(inputEl) {
    if (!inputEl) return;
    const errorEl = document.getElementById('phoneVerifyError');
    if (inputEl.value.trim().length >= 2) {
        inputEl.classList.remove('border-red-500');
        if (errorEl) errorEl.classList.add('hidden');
    }
}

export function handlePhoneInputValidation(inputEl) {
    if (!inputEl) return;
    const errorEl = document.getElementById('phoneVerifyError');
    let val = normalizeDigitsJs(inputEl.value).replace(/\D/g, '');

    if (val.startsWith('009665')) {
        val = '0' + val.slice(5);
    } else if (val.startsWith('9665')) {
        val = '0' + val.slice(3);
    }

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

export function clearOtpDigitBoxes() {
    for (let i = 0; i < 4; i++) {
        const box = document.getElementById(`otpDigit${i}`);
        if (box) {
            box.value = '';
            box.classList.remove('border-red-500');
        }
    }
}

export function getEnteredOtpCode() {
    let code = '';
    for (let i = 0; i < 4; i++) {
        const box = document.getElementById(`otpDigit${i}`);
        if (box) {
            code += normalizeDigitsJs(box.value).replace(/\D/g, '').slice(0, 1);
        }
    }
    return code;
}

export function handleOtpDigitInput(inputEl, index) {
    if (!inputEl) return;
    const otpError = document.getElementById('otpVerifyError');
    if (otpError) otpError.classList.add('hidden');

    const cleaned = normalizeDigitsJs(inputEl.value).replace(/\D/g, '');

    if (cleaned.length > 1) {
        // Handle multi-digit autocomplete or fast typing
        for (let i = 0; i < cleaned.length && (index + i) < 4; i++) {
            const box = document.getElementById(`otpDigit${index + i}`);
            if (box) {
                box.value = cleaned[i];
                box.classList.remove('border-red-500');
            }
        }
        const nextFocusIndex = Math.min(3, index + cleaned.length);
        const nextBox = document.getElementById(`otpDigit${nextFocusIndex}`);
        if (nextBox) nextBox.focus();
        return;
    }

    inputEl.value = cleaned;
    inputEl.classList.remove('border-red-500');

    if (cleaned.length === 1 && index < 3) {
        const nextBox = document.getElementById(`otpDigit${index + 1}`);
        if (nextBox) nextBox.focus();
    }
}

export function handleOtpDigitKeydown(event, index) {
    const currentBox = document.getElementById(`otpDigit${index}`);
    if (!currentBox) return;

    if (event.key === 'Backspace') {
        if (!currentBox.value && index > 0) {
            event.preventDefault();
            const prevBox = document.getElementById(`otpDigit${index - 1}`);
            if (prevBox) {
                prevBox.value = '';
                prevBox.focus();
            }
        }
    } else if (event.key === 'ArrowLeft' && index > 0) {
        event.preventDefault();
        const prevBox = document.getElementById(`otpDigit${index - 1}`);
        if (prevBox) prevBox.focus();
    } else if (event.key === 'ArrowRight' && index < 3) {
        event.preventDefault();
        const nextBox = document.getElementById(`otpDigit${index + 1}`);
        if (nextBox) nextBox.focus();
    }
}

export function handleOtpPaste(event) {
    if (!event.clipboardData) return;
    event.preventDefault();
    const pasted = normalizeDigitsJs(event.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 4);
    if (!pasted) return;

    for (let i = 0; i < 4; i++) {
        const box = document.getElementById(`otpDigit${i}`);
        if (box) {
            box.value = pasted[i] || '';
            box.classList.remove('border-red-500');
        }
    }

    const focusIdx = Math.min(3, Math.max(0, pasted.length - 1));
    const targetBox = document.getElementById(`otpDigit${focusIdx}`);
    if (targetBox) targetBox.focus();
}

function buildBookingRequestPayload() {
    const timeSlot = state.bookingState.selectedTime;
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
    const guestsCount = state.bookingState.guestsCount || parseInt(document.getElementById('bookingGuestsCountInput')?.value || '1', 10) || 1;
    const customerNotes = state.bookingState.customerNotes || document.getElementById('bookingCustomerNotesInput')?.value || '';

    return {
        timeSlot,
        bookingDateVal,
        serviceIds,
        primaryServiceId,
        items,
        guestsCount,
        customerNotes
    };
}

export async function submitPhoneVerification(event) {
    if (event) event.preventDefault();
    const phoneInput = document.getElementById('bookingPhoneInput');
    const nameInput = document.getElementById('guestNameInput');
    const errorEl = document.getElementById('phoneVerifyError');
    const errorText = document.getElementById('phoneVerifyErrorText');
    const submitBtn = document.getElementById('phoneVerifySubmitBtn');

    const isCustomerLoggedIn = Boolean(state.currentCustomer && state.currentCustomer.id && state.currentCustomer.phone);
    const rawPhone = isCustomerLoggedIn
        ? normalizeDigitsJs(state.currentCustomer.phone)
        : normalizeDigitsJs(phoneInput ? phoneInput.value : '');
    const customerName = isCustomerLoggedIn
        ? (state.currentCustomer.name || '')
        : (nameInput ? nameInput.value.trim() : '');

    if (!isCustomerLoggedIn) {
        if (customerName.length < 2) {
            if (errorText) {
                errorText.textContent = 'يرجى إدخال اسمكِ الكريم (حرفين على الأقل) للمتابعة.';
            }
            if (errorEl) errorEl.classList.remove('hidden');
            if (nameInput) {
                nameInput.classList.add('border-red-500');
                nameInput.focus();
            }
            return;
        }

        if (!SAUDI_PHONE_REGEX.test(rawPhone)) {
            if (errorText) {
                errorText.textContent = 'يرجى إدخال رقم جوال سعودي صحيح.';
            }
            if (errorEl) errorEl.classList.remove('hidden');
            if (phoneInput) {
                phoneInput.classList.add('border-red-500');
                phoneInput.focus();
            }
            return;
        }
    }

    if (errorEl) errorEl.classList.add('hidden');
    if (phoneInput) phoneInput.classList.remove('border-red-500');
    if (nameInput) nameInput.classList.remove('border-red-500');

    const {
        timeSlot,
        bookingDateVal,
        serviceIds,
        primaryServiceId,
        items,
        guestsCount,
        customerNotes
    } = buildBookingRequestPayload();

    if (!timeSlot) {
        if (errorText) errorText.textContent = 'يرجى اختيار وقت متاح للحجز أولاً.';
        if (errorEl) errorEl.classList.remove('hidden');
        return;
    }

    const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
    const verifyPhoneUrl = routes.verifyPhone || '/booking/verify-phone';
    const sendOtpUrl = routes.sendOtp || '/booking/send-otp';

    // CASE 1: Customer already logged in -> Confirm booking directly
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

    // CASE 2: Unauthenticated Customer -> Send 4-Digit SMS OTP (Simulated 1100 in dev)
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري إرسال رمز التحقق (SMS)...</span>`;
    }

    try {
        const response = await fetch(sendOtpUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                name: customerName,
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

        if (data.status === 'otp_sent') {
            state.bookingState.verifiedPhone = data.phone || rawPhone;
            state.bookingState.pendingCustomerName = data.name || customerName;
            state.bookingState.phoneChecked = true;
            switchPopupToOtpStep(data);
            openToast('تم إرسال رمز التحقق المكون من 4 أرقام إلى جوالكِ');
        }
    } catch (err) {
        if (errorText) errorText.textContent = 'حدث خطأ أثناء إرسال رمز التحقق، يرجى المحاولة مرة أخرى.';
        if (errorEl) errorEl.classList.remove('hidden');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }
    }
}

export async function submitSmsOtpVerification(event) {
    if (event) event.preventDefault();

    const otpError = document.getElementById('otpVerifyError');
    const otpErrorText = document.getElementById('otpVerifyErrorText');
    const submitBtn = document.getElementById('otpVerifySubmitBtn');
    const nameInput = document.getElementById('guestNameInput');
    const phoneInput = document.getElementById('bookingPhoneInput');

    const otpCode = getEnteredOtpCode();
    if (otpCode.length !== 4) {
        if (otpErrorText) otpErrorText.textContent = 'يرجى إدخال رمز التحقق المكون من 4 أرقام كاملاً.';
        if (otpError) otpError.classList.remove('hidden');
        for (let i = 0; i < 4; i++) {
            const box = document.getElementById(`otpDigit${i}`);
            if (box && !box.value) {
                box.classList.add('border-red-500');
                box.focus();
                break;
            }
        }
        return;
    }

    if (otpError) otpError.classList.add('hidden');

    const customerName = state.bookingState.pendingCustomerName || (nameInput ? nameInput.value.trim() : '');
    const customerPhone = state.bookingState.verifiedPhone || normalizeDigitsJs(phoneInput ? phoneInput.value : '');

    const {
        timeSlot,
        bookingDateVal,
        serviceIds,
        primaryServiceId,
        items,
        guestsCount,
        customerNotes
    } = buildBookingRequestPayload();

    const verifyOtpUrl = routes.verifyOtp || '/booking/verify-otp';
    const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري التحقق من الرمز وتسجيل الحجز...</span>`;
    }

    try {
        const response = await fetch(verifyOtpUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                name: customerName,
                phone: customerPhone,
                otp_code: otpCode,
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

        if (!response.ok || data.status === 'validation_error' || data.status === 'invalid_code' || data.status === 'otp_expired' || data.status === 'max_attempts_exceeded' || data.status === 'slot_unavailable') {
            if (otpErrorText) otpErrorText.textContent = data.message || 'رمز التحقق غير صحيح، يرجى المحاولة مرة أخرى.';
            if (otpError) otpError.classList.remove('hidden');
            for (let i = 0; i < 4; i++) {
                const box = document.getElementById(`otpDigit${i}`);
                if (box) box.classList.add('border-red-500');
            }
            if (data.status === 'slot_unavailable') {
                fetchSpecialistAvailability();
            }
            return;
        }

        if (data.status === 'confirmed') {
            finalizeConfirmedBookingFromServer(data);
        }
    } catch (err) {
        if (otpErrorText) otpErrorText.textContent = 'حدث خطأ أثناء الاتصال بالخادم، يرجى المحاولة مرة أخرى.';
        if (otpError) otpError.classList.remove('hidden');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }
    }
}

export async function resendBookingSmsOtp() {
    const resendBtn = document.getElementById('otpResendBtn');
    const resendText = document.getElementById('otpResendBtnText');
    const otpError = document.getElementById('otpVerifyError');
    const otpErrorText = document.getElementById('otpVerifyErrorText');

    if (otpError) otpError.classList.add('hidden');
    if (resendBtn) resendBtn.disabled = true;
    if (resendText) resendText.textContent = 'جاري الإرسال...';

    try {
        await submitPhoneVerification();
    } catch (e) {
        if (otpErrorText) otpErrorText.textContent = 'تعذر إعادة إرسال الرمز حالياً.';
        if (otpError) otpError.classList.remove('hidden');
    } finally {
        if (resendBtn) resendBtn.disabled = false;
        if (resendText) resendText.textContent = 'إعادة إرسال الرمز';
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
        openToast('تم تأكيد الرمز وتسجيل الحجز بنجاح');
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
