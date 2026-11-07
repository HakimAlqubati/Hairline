import { state, SAUDI_PHONE_REGEX, routes, csrfToken } from './state.js';
import { openToast } from './ui-modals.js';
import { normalizeDigitsJs } from './auth.js';
import { updateBookingBadge } from './navigation.js';

export function renderCustomerProfile() {
    const container = document.getElementById('customerProfileCardContainer');
    const bookingsCard = document.getElementById('myBookingsSectionCard');
    if (!container) return;

    if (!state.currentCustomer) {
        if (bookingsCard) bookingsCard.classList.add('hidden');
        container.innerHTML = `
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-card border border-gold-soft/40 text-center space-y-6">
                <div class="flex flex-col items-center space-y-2">
                    <h3 class="text-lg sm:text-xl font-black text-sage-dark">لا يوجد لديكِ ملف شخصي حالياً</h3>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed max-w-md mx-auto">
                        لم يتم تسجيل الدخول بعد. يمكنكِ التسجيل السريع والمتابعة عبر حساب Google للوصول إلى بياناتكِ ومتابعة حجوزاتكِ بكل سهولة
                    </p>
                </div>

                <div class="max-w-md mx-auto space-y-3">
                    <button type="button"
                            id="profileGoogleOAuthBtn"
                            onclick="startGoogleSignInProcess('profile')"
                            class="w-full p-4 rounded-2xl bg-primary hover:bg-primary-dark text-white shadow-xl shadow-primary/25 hover:shadow-2xl border-2 border-white/80 hover:border-white active:scale-[0.985] transition-all duration-300 flex items-center justify-between gap-3 group cursor-pointer">
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
                                <span id="profileGoogleOAuthBtnTitle" class="block font-extrabold text-sm sm:text-base text-white">
                                    المتابعة باستخدام حساب Google
                                </span>
                                <span class="block text-[11px] text-white/90 font-medium mt-0.5">
                                    تسجيل بضغطة واحدة بحساب Google
                                </span>
                            </div>
                        </div>

                        <div class="w-8 h-8 rounded-full bg-white/20 border border-white/40 flex items-center justify-center text-white group-hover:bg-white group-hover:text-primary-dark transition-all shrink-0">
                            <span class="text-base font-bold leading-none -mt-0.5">‹</span>
                        </div>
                    </button>

                    <p id="profileGoogleRegisterError" class="hidden text-[11px] font-bold text-red-600 text-center flex items-center justify-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span id="profileGoogleRegisterErrorText">تعذر إتمام التسجيل عبر Google، يرجى المحاولة مرة أخرى.</span>
                    </p>
                </div>
            </div>
        `;
        return;
    }

    if (bookingsCard) bookingsCard.classList.remove('hidden');

    const avatarSrc = state.currentCustomer.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(state.currentCustomer.name)}&background=211C18&color=C5A373&bold=true`;

    container.innerHTML = `
        <div class="bg-white rounded-3xl p-5 shadow-card border border-gold-soft/40 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <img src="${avatarSrc}"
                     alt="${state.currentCustomer.name}"
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(state.currentCustomer.name)}&background=211C18&color=C5A373&bold=true';"
                     class="w-15 h-15 rounded-full object-cover border-2 border-gold shadow-sm shrink-0"
                     style="width: 60px; height: 60px;" />
                <div class="min-w-0">
                    <h3 class="font-extrabold text-base sm:text-lg text-sage-dark truncate">${state.currentCustomer.name}</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-0.5 flex items-center gap-1.5" dir="ltr">
                        <i class="fa-solid ${state.currentCustomer.phone ? 'fa-phone' : 'fa-envelope'} text-[10px] text-gold-dark"></i>
                        <span>${state.currentCustomer.phone || state.currentCustomer.email || ''}</span>
                    </p>
                </div>
            </div>

            <button type="button"
                    onclick="openEditProfileModal()"
                    class="px-4 py-2.5 rounded-2xl bg-surface hover:bg-primary text-sage-dark hover:text-white border border-gold-soft/80 hover:border-primary text-xs font-extrabold transition-all duration-200 flex items-center gap-2 shadow-2xs cursor-pointer shrink-0">
                <i class="fa-regular fa-pen-to-square text-gold-dark"></i>
                <span>تعديل الملف</span>
            </button>
        </div>
    `;
}

export function openEditProfileModal() {
    if (!state.currentCustomer) return;

    const modal = document.getElementById('editProfileModal');
    const card = document.getElementById('editProfileCard');
    const nameInput = document.getElementById('editProfileNameInput');
    const phoneInput = document.getElementById('editProfilePhoneInput');
    const avatarPreview = document.getElementById('editProfileAvatarPreview');
    const avatarInput = document.getElementById('editProfileAvatarInput');
    const errorEl = document.getElementById('editProfileError');

    if (!modal || !card) return;

    if (nameInput) nameInput.value = state.currentCustomer.name || '';
    if (phoneInput) phoneInput.value = state.currentCustomer.phone || '';
    if (avatarInput) avatarInput.value = '';
    if (avatarPreview) {
        avatarPreview.src = state.currentCustomer.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(state.currentCustomer.name)}&background=211C18&color=C5A373&bold=true`;
    }
    if (errorEl) errorEl.classList.add('hidden');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        card.classList.remove('scale-90', 'translate-y-4');
        card.classList.add('scale-100', 'translate-y-0');
        if (nameInput) nameInput.focus();
    });
}

export function closeEditProfileModal() {
    const modal = document.getElementById('editProfileModal');
    const card = document.getElementById('editProfileCard');
    if (!modal || !card) return;

    modal.classList.add('opacity-0');
    card.classList.remove('scale-100', 'translate-y-0');
    card.classList.add('scale-90', 'translate-y-4');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 280);
}

export function previewProfileAvatar(inputEl) {
    if (!inputEl.files || !inputEl.files[0]) return;
    const file = inputEl.files[0];
    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('editProfileAvatarPreview');
        if (preview && e.target?.result) {
            preview.src = e.target.result;
        }
    };
    reader.readAsDataURL(file);
}

export async function submitEditProfile(event) {
    if (event) event.preventDefault();
    if (!state.currentCustomer) return;

    const nameInput = document.getElementById('editProfileNameInput');
    const phoneInput = document.getElementById('editProfilePhoneInput');
    const avatarInput = document.getElementById('editProfileAvatarInput');
    const errorEl = document.getElementById('editProfileError');
    const errorText = document.getElementById('editProfileErrorText');
    const submitBtn = document.getElementById('editProfileSubmitBtn');

    const newName = (nameInput ? nameInput.value : '').trim();
    const rawPhone = normalizeDigitsJs(phoneInput ? phoneInput.value : '');

    if (newName.length < 2) {
        if (errorText) errorText.textContent = 'يرجى إدخال الاسم الكامل (حرفين على الأقل).';
        if (errorEl) errorEl.classList.remove('hidden');
        return;
    }

    if (rawPhone !== '' && !SAUDI_PHONE_REGEX.test(rawPhone)) {
        if (errorText) errorText.textContent = 'يرجى إدخال رقم جوال سعودي صحيح.';
        if (errorEl) errorEl.classList.remove('hidden');
        return;
    }

    if (errorEl) errorEl.classList.add('hidden');

    const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-gold"></i><span>جاري الحفظ...</span>`;
    }

    try {
        const formData = new FormData();
        formData.append('customer_id', String(state.currentCustomer.id));
        formData.append('name', newName);
        formData.append('phone', rawPhone);
        if (avatarInput && avatarInput.files && avatarInput.files[0]) {
            formData.append('avatar', avatarInput.files[0]);
        }

        const updateUrl = routes.profileUpdate || '/profile/update';

        const response = await fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            if (errorText) errorText.textContent = data.message || 'تعذر حفظ التعديلات، يرجى المحاولة مرة أخرى.';
            if (errorEl) errorEl.classList.remove('hidden');
            return;
        }

        state.currentCustomer = data.customer;
        if (Array.isArray(data.customer_bookings)) {
            state.confirmedBookings = data.customer_bookings;
        }

        renderCustomerProfile();
        renderMyBookings();
        closeEditProfileModal();
        openToast('تم تحديث ملفكِ الشخصي');
    } catch (err) {
        if (errorText) errorText.textContent = 'حدث خطأ أثناء الاتصال بالخادم، يرجى المحاولة مرة أخرى.';
        if (errorEl) errorEl.classList.remove('hidden');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }
    }
}

export async function handleCustomerLogout() {
    const logoutBtn = document.getElementById('editProfileLogoutBtn');
    const originalHtml = logoutBtn ? logoutBtn.innerHTML : '';
    if (logoutBtn) {
        logoutBtn.disabled = true;
        logoutBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i><span>جاري تسجيل الخروج...</span>`;
    }

    try {
        const logoutUrl = routes.profileLogout || '/profile/logout';
        await fetch(logoutUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
    } catch (err) {
        // Ignore network errors on logout
    } finally {
        state.currentCustomer = null;
        state.confirmedBookings = [];
        state.bookingState.verifiedPhone = "";
        state.bookingState.confirmedCustomerName = "";
        state.bookingState.confirmedCustomerEmail = "";

        closeEditProfileModal();
        renderCustomerProfile();
        renderMyBookings();
        updateBookingBadge();

        if (logoutBtn) {
            logoutBtn.disabled = false;
            logoutBtn.innerHTML = originalHtml;
        }

        openToast('تم تسجيل الخروج بنجاح ✨');
    }
}

export function renderMyBookings() {
    const container = document.getElementById('myBookingsContainer');
    if (!container) return;

    if (state.confirmedBookings.length === 0) {
        container.innerHTML = `<p class="text-xs text-gray-400 text-center py-4">لا توجد حجوزات حالية.</p>`;
        return;
    }

    container.innerHTML = state.confirmedBookings.map(b => `
        <div class="p-4 rounded-2xl bg-surface border border-gold-soft/60 space-y-2">
            <div class="flex items-center justify-between">
                <span class="px-2.5 py-0.5 rounded-full bg-sage-light text-sage-dark text-[10px] font-extrabold">
                    مؤكد • #${b.id}${b.customerName ? ` • ${b.customerName}` : ''}
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
