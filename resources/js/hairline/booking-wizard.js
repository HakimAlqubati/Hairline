import { state, bookableCategories, bookingDates, specialistsData, routes } from './state.js';
import { openPhoneVerificationModal } from './auth.js';

export function openBookingFlow() {
    if (state.availabilityAbortController) {
        state.availabilityAbortController.abort();
    }
    const initialDateObj = bookingDates[1] || bookingDates[0] || { display_label: "غداً - السبت", date_iso: "" };
    state.bookingState = {
        step: 1,
        selectedCategory: null,
        selectedSubService: null,
        selectedSubServices: [],
        selectedSpecialist: null,
        selectedSpecialistsByService: {},
        selectedDate: initialDateObj.display_label,
        selectedDateIso: initialDateObj.date_iso,
        selectedTime: null,
        selectedTimesBySpecialist: {},
        selectedTimesByService: {},
        currentServiceIndex: 0,
        activeSpecialistId: null,
        availability: {
            loading: false,
            is_day_off: false,
            shift_label: "",
            slots: []
        },
        verifiedPhone: "",
        phoneChecked: false,
        confirmedCustomerName: "",
        confirmedCustomerEmail: "",
        guestsCount: 1,
        customerNotes: ""
    };

    const guestsInput = document.getElementById('bookingGuestsCountInput');
    const badge = document.getElementById('bookingGuestsCountBadge');
    const notesInput = document.getElementById('bookingCustomerNotesInput');
    const charCount = document.getElementById('bookingNotesCharCount');
    if (guestsInput) guestsInput.value = '1';
    if (badge) badge.textContent = '1 شخص';
    if (notesInput) notesInput.value = '';
    if (charCount) charCount.textContent = '0 / 1000';

    clearAllServiceCheckboxes();
    if (typeof window.clearUnifiedServiceSearch === 'function') {
        window.clearUnifiedServiceSearch('booking', false);
    }
    expandAllBookingCategories();
    if (typeof window.switchTab === 'function') {
        window.switchTab('booking');
    }
    renderBookingStep();
}

export function openBookingFlowWithCategory(categoryId) {
    const aliasMap = {
        'hair_treatments': 'hot_treatments',
        'hair_services': 'color_services',
        'blowdry': 'hairstyles',
        'nails': 'color_services'
    };
    const targetId = aliasMap[categoryId] || categoryId;
    openBookingFlow();
    setTimeout(() => {
        const sectionEl = document.getElementById(`booking-cat-${targetId}`) || document.getElementById(`booking-cat-${categoryId}`);
        if (sectionEl) {
            sectionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 250);
}

export function handleBookingBack() {
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    const currentIdx = state.bookingState.currentServiceIndex || 0;

    if (state.availabilityAbortController) {
        state.availabilityAbortController.abort();
    }

    if (state.bookingState.step === 3) {
        state.bookingState.step = 2;
        renderBookingStep();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else if (state.bookingState.step === 2) {
        if (currentIdx > 0 && subServices.length > 1) {
            state.bookingState.currentServiceIndex = currentIdx - 1;
            const prevSub = subServices[currentIdx - 1];
            state.bookingState.selectedSpecialist = state.bookingState.selectedSpecialistsByService?.[prevSub.id] || null;
            state.bookingState.activeSpecialistId = state.bookingState.selectedSpecialist?.id || null;
            state.bookingState.step = 3;
            state.bookingState.availability = { loading: true, is_day_off: false, shift_label: "", slots: [] };
            fetchSpecialistAvailability();
            renderBookingStep();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            state.bookingState.step = 1;
            state.bookingState.currentServiceIndex = 0;
            state.bookingState.selectedSpecialist = null;
            state.bookingState.selectedTime = null;
            state.bookingState.availability = {
                loading: false,
                is_day_off: false,
                shift_label: "",
                slots: []
            };
            renderBookingStep();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
}

export function handleSheetBack() {
    handleBookingBack();
}

export function toggleBookingTreeCategory(catId) {
    const btn = document.getElementById(`tree-btn-${catId}`);
    const panel = document.getElementById(`tree-panel-${catId}`);
    if (!btn || !panel) return;

    const isExpanded = btn.getAttribute('aria-expanded') === 'true';
    setCategoryExpandedState(catId, !isExpanded);
    syncToggleAllTreeButton();
}

export function setCategoryExpandedState(catId, expand) {
    const btn = document.getElementById(`tree-btn-${catId}`);
    const panel = document.getElementById(`tree-panel-${catId}`);
    const chevron = document.getElementById(`tree-chevron-${catId}`);
    if (!btn || !panel) return;

    btn.setAttribute('aria-expanded', expand ? 'true' : 'false');
    if (expand) {
        panel.classList.remove('grid-rows-[0fr]', 'opacity-0');
        panel.classList.add('grid-rows-[1fr]', 'opacity-100');
        if (chevron) chevron.classList.remove('rotate-90');
    } else {
        panel.classList.remove('grid-rows-[1fr]', 'opacity-100');
        panel.classList.add('grid-rows-[0fr]', 'opacity-0');
        if (chevron) chevron.classList.add('rotate-90');
    }
}

export function expandAllBookingCategories() {
    document.querySelectorAll('[data-category-tree]').forEach(el => {
        const catId = el.getAttribute('data-category-tree');
        if (catId) setCategoryExpandedState(catId, true);
    });
    syncToggleAllTreeButton();
}

export function toggleAllBookingCategories() {
    const visibleCategories = Array.from(document.querySelectorAll('[data-category-tree]:not(.hidden)'));
    const anyExpanded = visibleCategories.some(el => {
        const catId = el.getAttribute('data-category-tree');
        const btn = catId ? document.getElementById(`tree-btn-${catId}`) : null;
        return btn && btn.getAttribute('aria-expanded') === 'true';
    });
    const targetState = !anyExpanded;

    visibleCategories.forEach(el => {
        const catId = el.getAttribute('data-category-tree');
        if (catId) setCategoryExpandedState(catId, targetState);
    });
    syncToggleAllTreeButton();
}

export function syncToggleAllTreeButton() {
    const visibleCategories = Array.from(document.querySelectorAll('[data-category-tree]:not(.hidden)'));
    const anyExpanded = visibleCategories.some(el => {
        const catId = el.getAttribute('data-category-tree');
        const btn = catId ? document.getElementById(`tree-btn-${catId}`) : null;
        return btn && btn.getAttribute('aria-expanded') === 'true';
    });
    const text = document.getElementById('toggleAllTreeText');

    if (text) {
        text.textContent = anyExpanded ? 'طي الكل' : 'فتح الكل';
    }
}

export function clearAllServiceCheckboxes() {
    document.querySelectorAll('.booking-service-checkbox').forEach(cb => {
        cb.checked = false;
    });
    document.querySelectorAll('.booking-tree-row').forEach(row => {
        row.classList.remove('bg-gold-light/40');
    });
    state.bookingState.selectedSubServices = [];
    state.bookingState.selectedSubService = null;
    state.bookingState.selectedCategory = null;
    state.bookingState.selectedSpecialistsByService = {};
    syncFloatingConfirmButton();
}

export function findCategoryAndSubService(subId, catId = null) {
    let foundCategory = catId ? bookableCategories.find(c => c.id === catId) : state.bookingState.selectedCategory;
    let foundSub = foundCategory ? foundCategory.subServices.find(s => s.id === subId) : null;

    if (!foundSub) {
        for (const cat of bookableCategories) {
            const candidate = cat.subServices.find(s => s.id === subId);
            if (candidate) {
                foundCategory = cat;
                foundSub = candidate;
                break;
            }
        }
    }

    return { foundCategory, foundSub };
}

export function handleServiceCheckboxChange(checkboxEl, subId, catId = null) {
    if (!checkboxEl) return;
    if (!Array.isArray(state.bookingState.selectedSubServices)) {
        state.bookingState.selectedSubServices = [];
    }
    if (!state.bookingState.selectedSpecialistsByService) {
        state.bookingState.selectedSpecialistsByService = {};
    }

    const activeRow = document.getElementById(`srv-row-${subId}`);

    if (checkboxEl.checked) {
        if (activeRow) {
            activeRow.classList.add('bg-gold-light/40');
        }
        const { foundCategory, foundSub } = findCategoryAndSubService(subId, catId);
        if (foundSub && !state.bookingState.selectedSubServices.some(s => s.id === foundSub.id)) {
            state.bookingState.selectedSubServices.push(foundSub);
            if (Array.isArray(foundSub.specialists) && foundSub.specialists.length === 1) {
                const spId = foundSub.specialists[0];
                if (specialistsData[spId]) {
                    state.bookingState.selectedSpecialistsByService[foundSub.id] = specialistsData[spId];
                }
            }
        }
        if (!state.bookingState.selectedCategory && foundCategory) {
            state.bookingState.selectedCategory = foundCategory;
        }
        state.bookingState.selectedSubService = state.bookingState.selectedSubServices[0] || null;
    } else {
        if (activeRow) {
            activeRow.classList.remove('bg-gold-light/40');
        }
        state.bookingState.selectedSubServices = state.bookingState.selectedSubServices.filter(s => s.id !== subId);
        delete state.bookingState.selectedSpecialistsByService[subId];
        state.bookingState.selectedSubService = state.bookingState.selectedSubServices[0] || null;
        if (state.bookingState.selectedSubServices.length === 0) {
            state.bookingState.selectedCategory = null;
        }
    }

    syncFloatingConfirmButton();
}

export function handleFloatingBookingConfirm() {
    if (state.bookingState.step === 1) {
        proceedToSpecialistStep();
    } else if (state.bookingState.step === 2) {
        proceedToTimeStep();
    } else if (state.bookingState.step === 3) {
        const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
            ? state.bookingState.selectedSubServices
            : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
        const currentIdx = state.bookingState.currentServiceIndex || 0;
        const currentSub = subServices[currentIdx] || subServices[0];
        const currentTime = currentSub ? state.bookingState.selectedTimesByService?.[currentSub.id] : null;

        const allScheduled = subServices.length > 0 && subServices.every(s => 
            Boolean(state.bookingState.selectedSpecialistsByService?.[s.id]) && 
            Boolean(state.bookingState.selectedTimesByService?.[s.id])
        );

        if (allScheduled || (subServices.length <= 1 && state.bookingState.selectedTime)) {
            confirmFinalBooking();
        } else if (currentTime && currentIdx + 1 < subServices.length) {
            state.bookingState.currentServiceIndex = currentIdx + 1;
            const nextSub = subServices[currentIdx + 1];
            state.bookingState.selectedSpecialist = state.bookingState.selectedSpecialistsByService?.[nextSub.id] || null;
            state.bookingState.activeSpecialistId = state.bookingState.selectedSpecialist?.id || null;
            state.bookingState.step = 2;
            state.bookingState.availability = { loading: false, is_day_off: false, shift_label: "", slots: [] };
            renderBookingStep();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            confirmFinalBooking();
        }
    }
}

export function syncFloatingConfirmButton() {
    const container = document.getElementById('floatingBookingConfirmContainer');
    const titleEl = document.getElementById('floatingBookingConfirmTitle');
    const labelEl = document.getElementById('floatingSelectedServiceText');
    const iconEl = document.getElementById('floatingBookingConfirmIcon');
    const whatsappContainer = document.getElementById('floatingWhatsappContainer');
    if (!container) return;

    let shouldShow = false;
    let titleText = "تأكيد الحجز واختيار الأخصائية";
    let subtitleText = "";
    let iconClass = "fa-solid fa-check text-xs";

    if (state.currentActiveTab === 'booking') {
        const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
            ? state.bookingState.selectedSubServices
            : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
        const currentIdx = state.bookingState.currentServiceIndex || 0;
        const currentSub = subServices[currentIdx] || subServices[0];

        if (state.bookingState.step === 1) {
            const count = subServices.length;
            if (count > 0) {
                shouldShow = true;
                titleText = "تأكيد الحجز واختيار الأخصائية";
                subtitleText = count === 1
                    ? subServices[0].name
                    : `متابعة الحجز (${count} خدمات مختارة)`;
                iconClass = "fa-solid fa-check text-xs";
            }
        } else if (state.bookingState.step === 2) {
            const assigned = currentSub && state.bookingState.selectedSpecialistsByService?.[currentSub.id];
            if (assigned) {
                shouldShow = true;
                const spNameAr = (assigned.name || '').split('|')[0].trim();
                titleText = "متابعة لتحديد الوقت";
                subtitleText = `${currentSub.name} • الأخصائية: ${spNameAr}`;
                iconClass = "fa-solid fa-user-check text-xs";
            }
        } else if (state.bookingState.step === 3) {
            const isLoadingSlots = Boolean(state.bookingState.availability?.loading);
            const isDayOff = Boolean(state.bookingState.availability && state.bookingState.availability.is_day_off);
            const currentTime = currentSub && state.bookingState.selectedTimesByService?.[currentSub.id];

            if (!isLoadingSlots && !isDayOff && currentTime) {
                const allScheduled = subServices.length > 0 && subServices.every(s => 
                    Boolean(state.bookingState.selectedSpecialistsByService?.[s.id]) && 
                    Boolean(state.bookingState.selectedTimesByService?.[s.id])
                );

                if (allScheduled || subServices.length <= 1) {
                    shouldShow = true;
                    titleText = "تأكيد الحجز ومتابعة البيانات";
                    subtitleText = subServices.length > 1 
                        ? `${state.bookingState.selectedDate || 'الموعد'} • تم جدولة جميع الخدمات (${subServices.length})`
                        : `${state.bookingState.selectedDate || 'الموعد'} • الموعد (${currentTime})`;
                    iconClass = "fa-solid fa-calendar-check text-xs";
                } else if (currentIdx + 1 < subServices.length) {
                    shouldShow = true;
                    titleText = `متابعة للخدمة (${currentIdx + 2} من ${subServices.length})`;
                    subtitleText = `${currentSub.name} (${currentTime})`;
                    iconClass = "fa-solid fa-arrow-left text-xs";
                }
            }
        }
    }

    if (state.floatingConfirmHideTimeout) {
        clearTimeout(state.floatingConfirmHideTimeout);
        state.floatingConfirmHideTimeout = null;
    }

    if (whatsappContainer) {
        whatsappContainer.classList.toggle('-translate-y-16', shouldShow);
    }

    if (shouldShow) {
        if (titleEl) titleEl.textContent = titleText;
        if (labelEl) labelEl.textContent = subtitleText;
        if (iconEl) iconEl.className = iconClass;

        container.classList.remove('hidden');
        requestAnimationFrame(() => {
            container.classList.remove('opacity-0', 'translate-y-4', 'scale-95', 'pointer-events-none');
            container.classList.add('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
        });
    } else {
        container.classList.remove('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
        container.classList.add('opacity-0', 'translate-y-4', 'scale-95', 'pointer-events-none');
        state.floatingConfirmHideTimeout = setTimeout(() => {
            container.classList.add('hidden');
        }, 280);
    }
}

export function proceedToSpecialistStep() {
    const count = Array.isArray(state.bookingState.selectedSubServices) ? state.bookingState.selectedSubServices.length : 0;
    if (count === 0 && !state.bookingState.selectedSubService) return;
    if (!state.bookingState.selectedSubService && count > 0) {
        state.bookingState.selectedSubService = state.bookingState.selectedSubServices[0];
    }
    if (!state.bookingState.selectedSpecialistsByService) {
        state.bookingState.selectedSpecialistsByService = {};
    }
    state.bookingState.currentServiceIndex = 0;

    for (const sub of (state.bookingState.selectedSubServices || [])) {
        if (Array.isArray(sub.specialists) && sub.specialists.length === 1 && !state.bookingState.selectedSpecialistsByService[sub.id]) {
            const spId = sub.specialists[0];
            if (specialistsData[spId]) {
                state.bookingState.selectedSpecialistsByService[sub.id] = specialistsData[spId];
            }
        }
    }

    const firstSub = (state.bookingState.selectedSubServices || [])[0] || state.bookingState.selectedSubService;
    if (firstSub) {
        state.bookingState.selectedSpecialist = state.bookingState.selectedSpecialistsByService[firstSub.id] || null;
        state.bookingState.activeSpecialistId = state.bookingState.selectedSpecialist?.id || null;
    }
    state.bookingState.selectedTime = null;
    state.bookingState.availability = { loading: false, is_day_off: false, shift_label: "", slots: [] };
    state.bookingState.step = 2;
    renderBookingStep();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

export function getHairlineLogoLoaderHtml() {
    return `
        <div class="hairline-slot-loader-wrapper py-10 px-4 flex flex-col items-center justify-center text-center select-none" role="status" aria-label="تحميل المواعيد">
            <div class="relative w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center">
                <svg viewBox="0 0 320 320" xmlns="http://www.w3.org/2000/svg" class="hairline-undulating-logo w-full h-full overflow-visible">
                    <defs>
                        <linearGradient id="hairlineSlotWaveGleam" x1="0%" y1="130%" x2="0%" y2="-30%">
                            <stop offset="0%" stop-color="#C29E6D" stop-opacity="0.35" />
                            <stop offset="35%" stop-color="#C29E6D" stop-opacity="0.75" />
                            <stop offset="50%" stop-color="#FFFDF9" stop-opacity="1" />
                            <stop offset="65%" stop-color="#E5C799" stop-opacity="0.9" />
                            <stop offset="100%" stop-color="#C29E6D" stop-opacity="0.35" />
                            <animate attributeName="y1" from="140%" to="-40%" dur="1.9s" repeatCount="indefinite" />
                            <animate attributeName="y2" from="80%" to="-100%" dur="1.9s" repeatCount="indefinite" />
                        </linearGradient>
                        <filter id="hairlineUndulatingWave" x="-20%" y="-20%" width="140%" height="140%">
                            <feTurbulence type="fractalNoise" baseFrequency="0.015 0.035" numOctaves="3" result="wave">
                                <animate attributeName="baseFrequency" dur="2.8s" values="0.015 0.03; 0.022 0.055; 0.018 0.04; 0.015 0.03" repeatCount="indefinite" />
                            </feTurbulence>
                            <feDisplacementMap in="SourceGraphic" in2="wave" scale="4.5" xChannelSelector="R" yChannelSelector="G" />
                        </filter>
                    </defs>
                    <path fill="url(#hairlineSlotWaveGleam)" stroke="#C29E6D" stroke-width="1.2" stroke-opacity="0.7" stroke-linejoin="round" stroke-linecap="round" filter="url(#hairlineUndulatingWave)" d="M 151.0 11.5 L 150.0 12.5 L 135.0 13.5 L 116.0 18.5 L 107.0 22.5 L 95.0 32.5 L 82.5 45.0 L 68.5 64.0 L 59.5 84.0 L 53.5 107.0 L 53.5 113.0 L 52.5 114.0 L 52.5 143.0 L 53.5 144.0 L 55.5 161.0 L 60.5 178.0 L 74.5 206.0 L 88.5 224.0 L 106.5 240.0 L 106.0 242.5 L 98.0 239.5 L 86.0 230.5 L 75.5 220.0 L 67.5 210.0 L 61.5 201.0 L 53.5 185.0 L 48.5 171.0 L 45.5 153.0 L 44.5 152.0 L 43.5 120.0 L 44.5 119.0 L 44.5 111.0 L 45.5 110.0 L 45.5 104.0 L 48.5 91.0 L 53.5 76.0 L 61.5 59.0 L 67.5 49.0 L 74.5 41.0 L 66.0 46.5 L 42.5 72.0 L 36.5 84.0 L 28.5 111.0 L 27.5 124.0 L 26.5 125.0 L 27.5 152.0 L 28.5 153.0 L 28.5 159.0 L 29.5 160.0 L 32.5 177.0 L 40.5 199.0 L 43.5 203.0 L 43.5 205.0 L 48.5 214.0 L 61.5 232.0 L 81.0 251.5 L 91.0 259.5 L 106.0 268.5 L 107.5 270.0 L 107.5 272.0 L 105.0 272.5 L 95.0 268.5 L 72.0 253.5 L 56.5 239.0 L 40.5 218.0 L 30.5 199.0 L 22.5 177.0 L 19.5 159.0 L 18.5 158.0 L 16.0 126.5 L 14.5 137.0 L 13.5 138.0 L 13.5 170.0 L 14.5 171.0 L 14.5 180.0 L 15.5 181.0 L 16.5 192.0 L 20.5 204.0 L 20.5 207.0 L 26.5 222.0 L 34.5 237.0 L 49.5 258.0 L 66.0 274.5 L 87.0 289.5 L 113.0 301.5 L 116.0 301.5 L 119.0 303.5 L 128.0 305.5 L 132.5 299.0 L 134.5 291.0 L 134.5 272.0 L 133.5 271.0 L 133.5 265.0 L 130.5 252.0 L 121.5 230.0 L 117.5 224.0 L 97.5 203.0 L 90.5 193.0 L 82.5 178.0 L 79.5 168.0 L 77.5 165.0 L 72.5 144.0 L 71.5 119.0 L 73.0 117.5 L 77.0 117.5 L 78.0 116.5 L 82.0 116.5 L 83.0 117.5 L 99.0 117.5 L 100.0 118.5 L 106.0 118.5 L 107.0 119.5 L 112.0 119.5 L 113.0 120.5 L 130.0 123.5 L 146.0 129.5 L 146.0 132.5 L 140.0 132.5 L 139.0 131.5 L 125.0 130.5 L 117.0 128.5 L 109.0 128.5 L 108.0 127.5 L 81.0 126.5 L 81.5 142.0 L 82.5 143.0 L 82.5 148.0 L 84.5 156.0 L 90.5 173.0 L 104.5 196.0 L 124.0 216.5 L 143.0 230.5 L 153.0 235.5 L 160.0 234.5 L 179.0 222.5 L 193.0 210.5 L 196.0 206.5 L 199.5 207.0 L 198.5 211.0 L 189.5 224.0 L 176.0 236.5 L 165.0 243.5 L 160.0 245.5 L 153.0 245.5 L 142.0 241.5 L 138.0 238.5 L 135.5 239.0 L 140.5 254.0 L 140.5 258.0 L 142.5 264.0 L 142.5 274.0 L 143.5 275.0 L 142.5 293.0 L 138.5 307.0 L 142.0 308.5 L 160.0 308.5 L 161.0 309.5 L 174.0 308.5 L 176.5 307.0 L 184.5 295.0 L 195.5 274.0 L 205.5 247.0 L 209.5 231.0 L 211.5 212.0 L 212.5 211.0 L 212.5 200.0 L 211.5 199.0 L 210.5 185.0 L 206.5 171.0 L 201.5 160.0 L 192.5 146.0 L 175.0 128.5 L 165.0 121.5 L 144.0 111.5 L 126.0 105.5 L 106.0 100.5 L 90.5 98.0 L 91.0 95.5 L 112.0 95.5 L 113.0 96.5 L 121.0 96.5 L 122.0 97.5 L 132.0 98.5 L 136.0 100.5 L 146.0 102.5 L 156.0 106.5 L 170.0 114.5 L 172.0 114.5 L 189.0 127.5 L 202.5 143.0 L 214.5 166.0 L 220.5 190.0 L 220.5 221.0 L 219.5 222.0 L 219.5 228.0 L 218.5 229.0 L 216.5 243.0 L 204.5 279.0 L 190.0 306.5 L 200.0 304.5 L 211.5 300.0 L 222.5 279.0 L 232.5 250.0 L 235.5 237.0 L 235.5 232.0 L 236.5 231.0 L 236.5 225.0 L 237.5 224.0 L 238.5 199.0 L 237.5 198.0 L 237.5 189.0 L 236.5 188.0 L 235.5 176.0 L 229.5 156.0 L 220.5 138.0 L 209.5 123.0 L 197.0 111.5 L 182.0 100.5 L 163.0 90.5 L 148.0 84.5 L 145.0 84.5 L 142.0 82.5 L 122.0 77.5 L 101.0 74.5 L 100.5 72.0 L 125.0 71.5 L 126.0 72.5 L 133.0 72.5 L 154.0 77.5 L 157.0 79.5 L 170.0 83.5 L 186.0 91.5 L 203.0 103.5 L 220.5 121.0 L 230.5 136.0 L 238.5 152.0 L 243.5 168.0 L 245.5 183.0 L 246.5 184.0 L 247.5 211.0 L 246.5 212.0 L 245.5 232.0 L 244.5 233.0 L 244.5 238.0 L 240.5 255.0 L 232.5 279.0 L 226.0 293.5 L 239.0 286.5 L 252.5 276.0 L 260.5 249.0 L 260.5 245.0 L 263.5 234.0 L 263.5 228.0 L 264.5 227.0 L 264.5 218.0 L 265.5 217.0 L 265.5 191.0 L 264.5 190.0 L 264.5 182.0 L 263.5 181.0 L 262.5 170.0 L 255.5 146.0 L 247.5 129.0 L 238.5 115.0 L 231.5 106.0 L 209.0 84.5 L 198.0 76.5 L 176.0 64.5 L 149.0 54.5 L 129.0 50.5 L 128.5 48.0 L 137.0 47.5 L 138.0 48.5 L 146.0 48.5 L 147.0 49.5 L 162.0 51.5 L 180.0 57.5 L 198.0 66.5 L 213.0 76.5 L 225.0 86.5 L 235.5 97.0 L 250.5 116.0 L 261.5 136.0 L 264.5 146.0 L 266.5 149.0 L 270.5 163.0 L 272.5 179.0 L 273.5 180.0 L 273.5 187.0 L 274.5 188.0 L 274.5 223.0 L 273.5 224.0 L 272.5 238.0 L 268.5 253.0 L 268.5 257.0 L 266.5 261.0 L 267.0 262.5 L 279.5 248.0 L 291.5 228.0 L 291.5 208.0 L 292.5 205.0 L 291.5 204.0 L 291.5 186.0 L 290.5 185.0 L 289.5 170.0 L 283.5 144.0 L 275.5 123.0 L 266.5 106.0 L 255.5 90.0 L 231.0 65.5 L 205.0 47.5 L 176.0 33.5 L 159.0 28.5 L 158.5 26.0 L 165.0 25.5 L 166.0 26.5 L 172.0 26.5 L 184.0 29.5 L 209.0 39.5 L 228.0 51.5 L 242.0 62.5 L 263.5 85.0 L 278.5 107.0 L 286.5 123.0 L 292.5 139.0 L 292.5 142.0 L 297.5 158.0 L 298.5 170.0 L 299.5 171.0 L 300.5 195.0 L 301.5 196.0 L 300.5 199.0 L 301.0 206.5 L 305.5 188.0 L 306.5 176.0 L 307.5 175.0 L 307.5 150.0 L 306.5 149.0 L 306.5 141.0 L 305.5 140.0 L 304.5 129.0 L 301.5 121.0 L 300.5 114.0 L 296.5 104.0 L 291.5 93.0 L 282.5 78.0 L 272.5 64.0 L 250.0 42.5 L 234.0 31.5 L 216.0 22.5 L 202.0 17.5 L 185.0 13.5 L 180.0 13.5 L 179.0 12.5 Z M 145.0 3.5 L 144.0 4.5 L 136.0 4.5 L 135.0 5.5 L 121.0 7.5 L 114.0 10.5 L 111.0 10.5 L 83.0 23.5 L 69.0 32.5 L 57.0 42.5 L 39.5 61.0 L 26.5 80.0 L 17.5 98.0 L 11.5 115.0 L 7.5 132.0 L 6.5 147.0 L 5.5 148.0 L 5.5 174.0 L 6.5 175.0 L 6.5 184.0 L 7.5 185.0 L 7.5 190.0 L 10.5 203.0 L 16.5 221.0 L 22.5 234.0 L 38.5 259.0 L 60.0 281.5 L 88.0 300.5 L 115.0 311.5 L 131.0 315.5 L 136.0 315.5 L 137.0 316.5 L 175.0 316.5 L 184.0 316.5 L 185.0 315.5 L 190.0 315.5 L 206.0 311.5 L 222.0 305.5 L 238.0 297.5 L 253.0 287.5 L 264.0 278.5 L 281.5 260.0 L 297.5 236.0 L 307.5 213.0 L 307.5 210.0 L 310.5 203.0 L 313.5 190.0 L 313.5 184.0 L 314.5 183.0 L 314.5 138.0 L 313.5 137.0 L 312.5 126.0 L 307.5 111.0 L 307.5 108.0 L 300.5 93.0 L 300.5 91.0 L 298.5 89.0 L 294.5 80.0 L 281.5 61.0 L 257.0 36.5 L 238.0 23.5 L 222.0 15.5 L 202.0 8.5 L 189.0 5.5 L 175.0 4.5 L 174.0 3.5 Z" />
                </svg>
            </div>
        </div>
    `;
}

export function goToServiceConfig(targetIdx, targetStep = 2) {
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    if (targetIdx < 0 || targetIdx >= subServices.length) return;

    if (state.availabilityAbortController) {
        state.availabilityAbortController.abort();
    }

    state.bookingState.currentServiceIndex = targetIdx;
    const targetSub = subServices[targetIdx];
    const targetSp = state.bookingState.selectedSpecialistsByService?.[targetSub.id] || null;

    state.bookingState.selectedSpecialist = targetSp;
    state.bookingState.activeSpecialistId = targetSp?.id || null;

    if (targetStep === 3 && targetSp) {
        state.bookingState.step = 3;
        state.bookingState.availability = { loading: true, is_day_off: false, shift_label: "", slots: [] };
        fetchSpecialistAvailability();
    } else {
        state.bookingState.step = 2;
        state.bookingState.availability = { loading: false, is_day_off: false, shift_label: "", slots: [] };
    }

    renderBookingStep();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

export function getDistinctBookingSpecialists() {
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);

    const specialistMap = new Map();

    for (const service of subServices) {
        const sp = state.bookingState.selectedSpecialistsByService?.[service.id] || state.bookingState.selectedSpecialist;
        if (!sp || !sp.id) continue;

        if (!specialistMap.has(sp.id)) {
            specialistMap.set(sp.id, {
                specialist: sp,
                services: [service]
            });
        } else {
            specialistMap.get(sp.id).services.push(service);
        }
    }

    return Array.from(specialistMap.values());
}

export function switchActiveBookingSpecialist(spId) {
    const sp = specialistsData[spId];
    if (!sp) return;
    if (state.bookingState.activeSpecialistId === sp.id && !state.bookingState.availability?.loading) {
        return;
    }

    state.bookingState.activeSpecialistId = sp.id;
    state.bookingState.selectedSpecialist = sp;
    state.bookingState.availability = {
        loading: true,
        is_day_off: false,
        shift_label: "",
        slots: []
    };
    fetchSpecialistAvailability();
    renderBookingStep();
}

export function proceedToTimeStep() {
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    if (subServices.length === 0) return;

    const currentIdx = state.bookingState.currentServiceIndex || 0;
    const currentSub = subServices[currentIdx] || subServices[0];

    const currentSp = state.bookingState.selectedSpecialistsByService?.[currentSub.id] || state.bookingState.selectedSpecialist;
    if (!currentSp) return;

    state.bookingState.selectedSpecialist = currentSp;
    state.bookingState.activeSpecialistId = currentSp.id;

    state.bookingState.step = 3;
    state.bookingState.availability = {
        loading: true,
        is_day_off: false,
        shift_label: "",
        slots: []
    };
    fetchSpecialistAvailability();
    renderBookingStep();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

export function updateStepperUI(step) {
    const bookingStepper = document.getElementById('bookingStepper');
    const backBtn = document.getElementById('bookingPageBackBtn');
    const toggleAllBtn = document.getElementById('toggleAllTreeBtn');

    if (bookingStepper) {
        if (step >= 1 && step <= 3) {
            bookingStepper.classList.remove('hidden');
        } else {
            bookingStepper.classList.add('hidden');
        }
    }

    if (backBtn) {
        if (step === 2 || step === 3) {
            backBtn.classList.remove('hidden');
        } else {
            backBtn.classList.add('hidden');
        }
    }

    if (toggleAllBtn) {
        if (step === 1) {
            toggleAllBtn.classList.remove('hidden');
        } else {
            toggleAllBtn.classList.add('hidden');
        }
    }

    for (let i = 1; i <= 3; i++) {
        const el = document.getElementById(`stepIndicator${i}`);
        if (!el) continue;
        const bar = el.children[0];
        const label = el.children[1];

        if (i < step) {
            bar.className = "w-full h-1.5 rounded-full bg-primary mb-1.5 transition-colors duration-300";
            label.className = "text-xs font-bold text-primary-dark";
        } else if (i === step) {
            bar.className = "w-full h-1.5 rounded-full bg-primary mb-1.5 transition-colors duration-300";
            label.className = "text-xs font-extrabold text-primary-dark";
        } else {
            bar.className = "w-full h-1.5 rounded-full bg-primary-soft/40 mb-1.5 transition-colors duration-300";
            label.className = "text-xs font-medium text-gray-400";
        }
    }
    syncFloatingConfirmButton();
}

export function renderBookingStep() {
    const pageTitle = document.getElementById('bookingPageTitle');
    const pageSubtitle = document.getElementById('bookingPageSubtitle');
    const step1Container = document.getElementById('bookingStep1Container');
    const step2Container = document.getElementById('bookingStep2Container');
    const step2Body = document.getElementById('bookingStep2Body');
    const step2Footer = document.getElementById('bookingStep2Footer');
    const step3Container = document.getElementById('bookingStep3Container');
    const step3Body = document.getElementById('bookingStep3Body');
    const step3Footer = document.getElementById('bookingStep3Footer');
    const step4Container = document.getElementById('bookingStep4Container') || step3Container;

    updateStepperUI(state.bookingState.step);
    syncFloatingConfirmButton();

    if (step1Container) step1Container.classList.add('hidden');
    if (step2Container) step2Container.classList.add('hidden');
    if (step3Container) step3Container.classList.add('hidden');
    if (step4Container && step4Container !== step3Container) step4Container.classList.add('hidden');

    if (state.bookingState.step === 1) {
        if (pageTitle) pageTitle.textContent = "الخدمات المتاحة للحجز";
        if (pageSubtitle) {
            pageSubtitle.textContent = "";
            pageSubtitle.classList.add('hidden');
        }
        if (step1Container) step1Container.classList.remove('hidden');
    } else if (state.bookingState.step === 2) {
        const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
            ? state.bookingState.selectedSubServices
            : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
        if (subServices.length === 0) return;

        const currentIdx = state.bookingState.currentServiceIndex || 0;
        const currentSub = subServices[currentIdx] || subServices[0];
        state.bookingState.selectedSubService = currentSub;

        if (pageTitle) {
            pageTitle.textContent = subServices.length > 1 
                ? `الخطوة 2: اختيار الأخصائية (${currentIdx + 1} من ${subServices.length})`
                : "الخطوة 2: اختيار الأخصائية";
        }
        if (pageSubtitle) {
            pageSubtitle.textContent = subServices.length > 1 
                ? `اختاري الأخصائية لخدمة: ${currentSub.name}`
                : "يرجى اختيار الأخصائية المناسبة لمتابعة الحجز";
            pageSubtitle.classList.remove('hidden');
        }
        if (step2Container) step2Container.classList.remove('hidden');

        if (!state.bookingState.selectedSpecialistsByService) {
            state.bookingState.selectedSpecialistsByService = {};
        }

        // Auto-assign any service that only has 1 qualified specialist
        if (Array.isArray(currentSub.specialists) && currentSub.specialists.length === 1 && !state.bookingState.selectedSpecialistsByService[currentSub.id]) {
            const singleSpId = currentSub.specialists[0];
            if (specialistsData[singleSpId]) {
                state.bookingState.selectedSpecialistsByService[currentSub.id] = specialistsData[singleSpId];
            }
        }

        const currentAssigned = state.bookingState.selectedSpecialistsByService[currentSub.id] || null;
        if (!state.bookingState.selectedSpecialist && currentAssigned) {
            state.bookingState.selectedSpecialist = currentAssigned;
        }

        const qualifiedSpIds = currentSub.specialists || [];
        const linkedSpecialists = qualifiedSpIds.map(id => specialistsData[id]).filter(Boolean);

        let subServicesChipsHtml = '';
        if (subServices.length > 1) {
            subServicesChipsHtml = `
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                    ${subServices.map((item, idx) => {
                        const assignedSp = state.bookingState.selectedSpecialistsByService?.[item.id];
                        const assignedTime = state.bookingState.selectedTimesByService?.[item.id];
                        const isCurrent = idx === currentIdx;
                        const isDone = Boolean(assignedSp && assignedTime);
                        const spNameAr = assignedSp ? (assignedSp.name || '').split('|')[0].trim() : '';

                        if (isDone && !isCurrent) {
                            return `
                                <button type="button" 
                                        onclick="goToServiceConfig(${idx}, 2)"
                                        class="px-3 py-1.5 rounded-full text-xs font-extrabold shrink-0 border transition cursor-pointer flex items-center gap-1.5 bg-sage-light text-sage-dark border-primary/30 hover:border-primary">
                                    <i class="fa-solid fa-check text-primary text-[10px]"></i>
                                    <span>${idx + 1}. ${item.name}</span>
                                    <span class="text-[10px] text-primary font-bold">(${spNameAr})</span>
                                </button>
                            `;
                        } else if (isCurrent) {
                            return `
                                <div class="px-3.5 py-1.5 rounded-full text-xs font-black shrink-0 border-2 border-primary bg-primary text-white shadow-xs flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-white/20 text-white text-[10px] flex items-center justify-center font-mono">${idx + 1}</span>
                                    <span>${item.name}</span>
                                    <span class="text-[10px] opacity-90">(اختيار الأخصائية)</span>
                                </div>
                            `;
                        } else {
                            return `
                                <button type="button"
                                        onclick="goToServiceConfig(${idx}, 2)"
                                        class="px-3 py-1.5 rounded-full text-xs font-bold shrink-0 border border-gray-200 bg-surface text-gray-500 hover:border-gray-300 transition cursor-pointer flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-gray-200 text-gray-600 text-[10px] flex items-center justify-center font-mono">${idx + 1}</span>
                                    <span>${item.name}</span>
                                </button>
                            `;
                        }
                    }).join('')}
                </div>
            `;
        }

        if (step2Body) {
            step2Body.innerHTML = `
                <div class="space-y-4">
                    ${subServicesChipsHtml}

                    <div class="p-4 rounded-2xl bg-gold-light/60 border border-gold/30 text-center space-y-1">
                        <span class="text-[11px] font-bold text-gold-dark block">
                            ${subServices.length > 1 ? `الخدمة (${currentIdx + 1} من ${subServices.length})` : (state.bookingState.selectedCategory?.title || 'الخدمة المختارة')}
                        </span>
                        <h4 class="font-black text-base sm:text-lg text-sage-dark text-center leading-relaxed">
                            ${currentSub.name}
                        </h4>
                    </div>

                    <div class="space-y-3">
                        <div class="${linkedSpecialists.length === 1 ? 'w-full max-w-sm sm:max-w-md mx-auto' : (linkedSpecialists.length === 2 ? 'grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-3xl mx-auto' : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5')}">
                            ${linkedSpecialists.map(sp => {
                                const nameParts = (sp.name || '').split('|').map(s => s.trim());
                                const nameAr = nameParts[0] || (sp.name || '');
                                const nameEn = nameParts[1] || '';
                                const isSelected = (currentAssigned && currentAssigned.id === sp.id) || (state.bookingState.selectedSpecialist && state.bookingState.selectedSpecialist.id === sp.id);
                                return `
                                    <article onclick="chooseSpecialistForCurrentService('${sp.id}')" 
                                             role="button"
                                             tabindex="0"
                                             onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); chooseSpecialistForCurrentService('${sp.id}'); }"
                                             aria-label="اختيار الأخصائية ${nameAr}"
                                             class="specialist-card group relative rounded-[28px] bg-white border-2 transition-all duration-300 cursor-pointer select-none overflow-hidden flex flex-col shadow-card ${
                                                 isSelected 
                                                 ? 'border-primary ring-4 ring-primary/20 shadow-xl' 
                                                 : 'border-primary-soft/60 hover:border-primary hover:shadow-lg'
                                             }">

                                        <div class="relative w-full h-[280px] sm:h-[310px] bg-gradient-to-b from-[#FAF8F5] via-[#F6EFE2]/75 to-[#E6D5B8]/50 pt-5 px-4 flex items-end justify-center overflow-hidden">
                                            <div class="specialist-arch-bg absolute inset-x-5 top-6 bottom-0 rounded-t-[140px] bg-white/80 border border-primary/20 shadow-inner pointer-events-none"></div>

                                            ${sp.experience ? `
                                                <div class="absolute top-3.5 right-3.5 z-20">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/95 backdrop-blur-md border border-primary/35 text-primary-dark text-[11px] font-extrabold shadow-xs">
                                                        <i class="fa-solid fa-medal text-primary text-[10px]"></i>
                                                        <span>${sp.experience}</span>
                                                    </span>
                                                </div>
                                            ` : ''}

                                            <div class="absolute top-3.5 left-3.5 z-20">
                                                ${isSelected ? `
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary text-white text-[11px] font-black shadow-md">
                                                        <i class="fa-solid fa-check text-[11px]"></i>
                                                    </span>
                                                ` : `
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/95 backdrop-blur-md border border-primary-soft/80 text-gray-600 text-[11px] font-bold shadow-2xs group-hover:border-primary group-hover:text-primary transition-colors">
                                                        <i class="fa-regular fa-circle text-[10px]"></i>
                                                        <span>اختيار</span>
                                                    </span>
                                                `}
                                            </div>

                                            ${sp.has_custom_avatar && sp.avatar ? `
                                                <img src="${sp.avatar}" 
                                                     alt="${sp.name}"
                                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(nameAr)}&background=FFFFFF&color=C29E6D&bold=true&size=512';"
                                                     class="specialist-portrait-img relative z-10 w-full h-full object-contain object-bottom ${isSelected ? 'scale-[1.04]' : ''}" />
                                            ` : `
                                                <div class="relative z-10 my-auto pb-4 flex flex-col items-center justify-center">
                                                    <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full bg-white border-4 border-primary/30 shadow-xl flex items-center justify-center specialist-portrait-img transition-transform duration-500">
                                                        <span class="text-4xl sm:text-5xl font-black font-serif text-primary tracking-widest select-none">
                                                            ${sp.initials || 'HL'}
                                                        </span>
                                                    </div>
                                                </div>
                                            `}

                                            <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-primary-light via-primary to-primary-light ${isSelected ? 'opacity-100' : 'opacity-70 group-hover:opacity-100'} transition-opacity"></div>
                                        </div>

                                        <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between bg-white space-y-3.5 text-right">
                                            <div class="space-y-2.5">
                                                <div class="flex items-baseline justify-between gap-2">
                                                    <h4 class="text-lg sm:text-xl font-black ${isSelected ? 'text-primary-dark' : 'text-sage-dark group-hover:text-primary-dark'} transition-colors leading-tight">
                                                        ${nameAr}
                                                    </h4>
                                                    ${nameEn ? `
                                                        <span class="text-xs sm:text-sm font-serif font-bold tracking-wider text-primary-dark uppercase shrink-0">
                                                            ${nameEn}
                                                        </span>
                                                    ` : ''}
                                                </div>

                                                <p class="text-xs sm:text-[13px] font-extrabold text-primary-dark leading-relaxed">
                                                    ${sp.role || ''}
                                                </p>

                                                <div class="h-px w-full bg-gradient-to-l from-primary-soft/70 via-primary-soft/30 to-transparent"></div>

                                                ${sp.bio ? `
                                                    <p class="text-xs text-gray-600 leading-relaxed line-clamp-3">
                                                        ${sp.bio}
                                                    </p>
                                                ` : ''}
                                            </div>

                                            ${linkedSpecialists.length > 1 ? `
                                                <div class="pt-1">
                                                    <div class="w-full py-2.5 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 ${
                                                        isSelected 
                                                        ? 'bg-primary text-white shadow-xs' 
                                                        : 'bg-primary-light/50 text-primary-dark group-hover:bg-primary group-hover:text-white'
                                                    }">
                                                        <i class="fa-solid ${isSelected ? 'fa-circle-check' : 'fa-hand-pointer'} text-[11px]"></i>
                                                        <span>${isSelected ? 'الأخصائية المحددة للخدمة' : 'اختيار هذه الأخصائية'}</span>
                                                    </div>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </article>
                                `;
                            }).join('')}
                        </div>
                    </div>
                </div>
            `;
        }

        if (step2Footer) {
            step2Footer.innerHTML = '';
        }
    } else if (state.bookingState.step === 3) {
        const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
            ? state.bookingState.selectedSubServices
            : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
        
        if (subServices.length === 0) {
            state.bookingState.step = 2;
            renderBookingStep();
            return;
        }

        const currentIdx = state.bookingState.currentServiceIndex || 0;
        const currentSub = subServices[currentIdx] || subServices[0];

        const activeSp = state.bookingState.selectedSpecialistsByService?.[currentSub.id] || state.bookingState.selectedSpecialist;
        if (!activeSp) {
            state.bookingState.step = 2;
            renderBookingStep();
            return;
        }
        state.bookingState.selectedSpecialist = activeSp;
        state.bookingState.activeSpecialistId = activeSp.id;

        const activeNameParts = (activeSp.name || '').split('|').map(s => s.trim());
        const activeSpNameAr = activeNameParts[0] || (activeSp.name || '');
        const activeSpNameEn = activeNameParts[1] || '';

        if (pageTitle) {
            pageTitle.textContent = subServices.length > 1 
                ? `الخطوة 3: تحديد الموعد (${currentIdx + 1} من ${subServices.length})` 
                : "الخطوة 3: تحديد الموعد";
        }
        if (pageSubtitle) {
            pageSubtitle.textContent = subServices.length > 1 
                ? `حددي اليوم والوقت لخدمة: ${currentSub.name}`
                : "اختاري اليوم والوقت المناسب لزيارتكِ";
            pageSubtitle.classList.remove('hidden');
        }
        if (step3Container) step3Container.classList.remove('hidden');

        const datesList = (Array.isArray(bookingDates) && bookingDates.length > 0)
            ? bookingDates
            : [
                { display_label: "اليوم", date_iso: "" },
                { display_label: "غداً - السبت", date_iso: "" }
            ];

        const isLoadingSlots = Boolean(state.bookingState.availability?.loading);
        const shiftSlots = Array.isArray(state.bookingState.availability?.slots) ? state.bookingState.availability.slots : [];
        const isDayOff = Boolean(state.bookingState.availability && state.bookingState.availability.is_day_off);
        const shiftLabel = state.bookingState.availability?.shift_label || "";
        const activeSelectedTime = state.bookingState.selectedTimesByService?.[currentSub.id] || null;

        // Check if all services in multi-service booking are fully scheduled
        const allScheduled = subServices.length > 0 && subServices.every(s => 
            Boolean(state.bookingState.selectedSpecialistsByService?.[s.id]) && 
            Boolean(state.bookingState.selectedTimesByService?.[s.id])
        );

        let subServicesChipsHtml = '';
        if (subServices.length > 1) {
            subServicesChipsHtml = `
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                    ${subServices.map((item, idx) => {
                        const assignedSp = state.bookingState.selectedSpecialistsByService?.[item.id];
                        const assignedTime = state.bookingState.selectedTimesByService?.[item.id];
                        const isCurrent = idx === currentIdx;
                        const isDone = Boolean(assignedSp && assignedTime);
                        const spNameAr = assignedSp ? (assignedSp.name || '').split('|')[0].trim() : '';

                        if (isDone && !isCurrent) {
                            return `
                                <button type="button" 
                                        onclick="goToServiceConfig(${idx}, 3)"
                                        class="px-3 py-1.5 rounded-full text-xs font-extrabold shrink-0 border transition cursor-pointer flex items-center gap-1.5 bg-sage-light text-sage-dark border-primary/30 hover:border-primary">
                                    <i class="fa-solid fa-check text-primary text-[10px]"></i>
                                    <span>${idx + 1}. ${item.name}</span>
                                    <span class="text-[10px] text-primary font-bold">(${assignedTime})</span>
                                </button>
                            `;
                        } else if (isCurrent) {
                            return `
                                <div class="px-3.5 py-1.5 rounded-full text-xs font-black shrink-0 border-2 border-primary bg-primary text-white shadow-xs flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-white/20 text-white text-[10px] flex items-center justify-center font-mono">${idx + 1}</span>
                                    <span>${item.name}</span>
                                    <span class="text-[10px] opacity-90">(تحديد الوقت)</span>
                                </div>
                            `;
                        } else {
                            return `
                                <button type="button"
                                        onclick="goToServiceConfig(${idx}, 2)"
                                        class="px-3 py-1.5 rounded-full text-xs font-bold shrink-0 border border-gray-200 bg-surface text-gray-500 hover:border-gray-300 transition cursor-pointer flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-gray-200 text-gray-600 text-[10px] flex items-center justify-center font-mono">${idx + 1}</span>
                                    <span>${item.name}</span>
                                </button>
                            `;
                        }
                    }).join('')}
                </div>
            `;
        }

        let scheduleSummaryHtml = '';
        if (allScheduled && subServices.length > 1) {
            scheduleSummaryHtml = `
                <div class="p-4 sm:p-5 rounded-3xl bg-gradient-to-br from-gold-light/40 via-surface to-gold-light/20 border border-gold/40 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-gold/20 pb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary text-white text-xs font-black flex items-center justify-center">
                                <i class="fa-solid fa-calendar-check text-[10px]"></i>
                            </span>
                            <span class="text-xs sm:text-sm font-black text-sage-dark">
                                جدول مواعيدكِ للخدمات المختارة (${subServices.length} خدمات):
                            </span>
                        </div>
                        <span class="text-xs font-extrabold text-primary-dark">
                            ${state.bookingState.selectedDate || ''}
                        </span>
                    </div>
                    <div class="space-y-2">
                        ${subServices.map((subItem, sIdx) => {
                            const assignedSp = state.bookingState.selectedSpecialistsByService?.[subItem.id];
                            const assignedTime = state.bookingState.selectedTimesByService?.[subItem.id];
                            const spName = assignedSp ? (assignedSp.name || '').split('|')[0].trim() : '';
                            return `
                                <div class="flex items-center justify-between p-3 rounded-2xl bg-white border border-primary-soft/50 shadow-2xs gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-5 h-5 rounded-full bg-primary/10 text-primary font-black text-[11px] flex items-center justify-center shrink-0">
                                            ${sIdx + 1}
                                        </span>
                                        <div class="min-w-0">
                                            <h5 class="text-xs sm:text-sm font-black text-sage-dark truncate">${subItem.name}</h5>
                                            <div class="flex items-center gap-2 text-[11px] font-bold text-gray-500 mt-0.5">
                                                <span class="text-primary-dark flex items-center gap-1">
                                                    <i class="fa-regular fa-user text-[10px] text-gold-dark"></i>
                                                    <span>${spName}</span>
                                                </span>
                                                <span>•</span>
                                                <span class="text-primary flex items-center gap-1 font-extrabold">
                                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                                    <span>${assignedTime}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            onclick="goToServiceConfig(${sIdx}, 2)"
                                            class="shrink-0 px-3 py-1 rounded-xl border border-primary-soft text-primary-dark hover:bg-primary hover:text-white text-xs font-bold transition cursor-pointer">
                                        تعديل
                                    </button>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;
        }

        if (step3Body) {
            step3Body.innerHTML = `
                <div class="space-y-5">
                    ${subServicesChipsHtml}

                    <!-- Header Card for Current Service Specialist -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-primary-soft/60 shadow-xs flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full overflow-hidden border-2 border-primary shrink-0 bg-surface flex items-center justify-center">
                                ${activeSp && activeSp.has_custom_avatar && activeSp.avatar ? `
                                    <img src="${activeSp.avatar}" alt="${activeSpNameAr}" class="w-full h-full object-cover" />
                                ` : `
                                    <span class="text-lg font-black font-serif text-primary">${activeSp?.initials || 'HL'}</span>
                                `}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-bold text-gray-400">
                                        ${subServices.length > 1 ? `أخصائية خدمة (${currentSub.name}):` : 'الأخصائية المختارة:'}
                                    </span>
                                    <span class="font-black text-sm sm:text-base text-sage-dark">${activeSpNameAr}</span>
                                    ${activeSpNameEn ? `<span class="text-[11px] font-serif text-primary-dark tracking-wide uppercase">${activeSpNameEn}</span>` : ''}
                                </div>
                                <p class="text-xs font-bold text-primary truncate mt-0.5">${activeSp.role || ''}</p>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="goToServiceConfig(${currentIdx}, 2)" 
                                class="shrink-0 px-3 py-1.5 rounded-xl border border-primary-soft text-primary-dark text-xs font-bold hover:bg-primary hover:text-white transition cursor-pointer flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            <span>تغيير الأخصائية</span>
                        </button>
                    </div>

                    <!-- Date & Time Picker Section -->
                    <div class="space-y-4 pt-1">
                        <div class="flex items-center justify-between gap-2">
                            <label class="block text-xs sm:text-sm font-extrabold text-sage-dark">
                                اختاري اليوم المناسب:
                            </label>
                            ${!isLoadingSlots && shiftLabel ? `
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-sage-soft text-sage-dark text-[10px] font-bold border border-gold-soft/50">
                                    <i class="fa-regular fa-calendar-check text-gold-dark"></i>
                                    <span>${shiftLabel}</span>
                                </span>
                            ` : ''}
                        </div>
                        
                        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
                            ${datesList.map(d => {
                                const label = d.display_label || d;
                                const iso = d.date_iso || '';
                                const isActiveDate = state.bookingState.selectedDate === label || (iso && state.bookingState.selectedDateIso === iso);
                                return `
                                    <button type="button"
                                            onclick="selectBookingDate('${label}', '${iso}')" 
                                            class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 border transition cursor-pointer ${
                                                isActiveDate 
                                                ? 'bg-primary text-white border-primary shadow-xs' 
                                                : 'bg-surface text-gray-600 border-gray-200 hover:border-primary'
                                            }">
                                        ${label}
                                    </button>
                                `;
                            }).join('')}
                        </div>

                        <div class="pt-2">
                            <label class="block text-xs sm:text-sm font-extrabold text-sage-dark mb-2.5">
                                ${subServices.length > 1 
                                    ? `اختاري وقت بدء خدمة (${currentSub.name}) مع (${activeSpNameAr}):`
                                    : `اختاري الوقت المتاح مع ${activeSpNameAr}:`}
                            </label>
                            ${isLoadingSlots ? getHairlineLogoLoaderHtml() : (isDayOff ? `
                                <div class="p-4 rounded-2xl bg-red-50/70 border border-red-200 text-center space-y-1">
                                    <p class="text-xs font-extrabold text-red-700">الأخصائية في يوم إجازة في هذا التاريخ</p>
                                    <p class="text-[11px] text-red-600">يرجى اختيار يوم آخر أو الضغط على "تغيير الأخصائية" لاختيار أخصائية أخرى.</p>
                                </div>
                            ` : (shiftSlots.length === 0 ? `
                                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 text-center">
                                    <p class="text-xs font-bold text-gray-500">لا توجد مواعيد متاحة لهذه الأخصائية في هذا التاريخ.</p>
                                </div>
                            ` : `
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                    ${shiftSlots.map(slotObj => {
                                        const slotTime = slotObj.time;
                                        const isAvailable = slotObj.is_available && !slotObj.is_booked;
                                        const isSelectedSlot = (slotTime === activeSelectedTime);

                                        if (!isAvailable) {
                                            return `
                                                <button type="button" 
                                                        disabled 
                                                        class="py-2.5 px-2 rounded-xl text-xs font-medium bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed opacity-60 flex items-center justify-center gap-1">
                                                    <span>${slotTime}</span>
                                                    <i class="fa-solid fa-lock text-[9px]"></i>
                                                </button>
                                            `;
                                        }

                                        return `
                                            <button type="button" 
                                                    onclick="selectBookingTime('${slotTime}')"
                                                    class="py-2.5 px-2 rounded-xl text-xs font-bold border transition cursor-pointer select-none ${
                                                        isSelectedSlot 
                                                        ? 'bg-primary text-white border-primary shadow-xs ring-2 ring-primary/20' 
                                                        : 'bg-white text-sage-dark border-gray-200 hover:border-primary'
                                                    }">
                                                ${slotTime}
                                            </button>
                                        `;
                                    }).join('')}
                                </div>
                            `))}
                        </div>
                    </div>

                    ${scheduleSummaryHtml}
                </div>
            `;
        }

        if (step3Footer) {
            if (allScheduled || (subServices.length <= 1 && state.bookingState.selectedTime)) {
                step3Footer.innerHTML = `
                    <div class="pt-2">
                        <button type="button"
                                onclick="confirmFinalBooking()"
                                class="w-full py-4 px-6 rounded-2xl bg-primary text-white font-black text-sm sm:text-base hover:bg-primary-dark shadow-md transition cursor-pointer flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check text-xs"></i>
                            <span>تأكيد الحجز ومتابعة البيانات</span>
                        </button>
                    </div>
                `;
            } else {
                step3Footer.innerHTML = '';
            }
        }

        const guestsInput = document.getElementById('bookingGuestsCountInput');
        const badge = document.getElementById('bookingGuestsCountBadge');
        const notesInput = document.getElementById('bookingCustomerNotesInput');
        const charCount = document.getElementById('bookingNotesCharCount');
        if (guestsInput && state.bookingState.guestsCount) {
            guestsInput.value = state.bookingState.guestsCount;
            if (badge) {
                const count = state.bookingState.guestsCount;
                badge.textContent = count === 1 ? '1 شخص' : (count === 2 ? 'شخصان' : `${count} أشخاص`);
            }
        }
        if (notesInput && state.bookingState.customerNotes !== undefined) {
            notesInput.value = state.bookingState.customerNotes;
            if (charCount) {
                charCount.textContent = `${state.bookingState.customerNotes.length} / 1000`;
            }
        }
    } else if (state.bookingState.step === 4 || state.bookingState.step === 5) {
        const lastBooking = state.confirmedBookings[0];
        const customerName = state.bookingState.confirmedCustomerName || lastBooking?.customerName || state.currentCustomer?.name || "";
        if (pageTitle) pageTitle.textContent = "تم تأكيد حجزك بنجاح";
        if (pageSubtitle) pageSubtitle.textContent = "رقم الحجز: " + (lastBooking?.id || '');
        if (step4Container && lastBooking) {
            const waUrl = getBookingWhatsAppShareUrl(lastBooking, customerName);
            const guestsNum = lastBooking.guests_count || lastBooking.guestsCount || state.bookingState.guestsCount || 1;
            const notesText = lastBooking.customer_notes || lastBooking.customerNotes || state.bookingState.customerNotes || '';
            const hasMultipleItems = Array.isArray(lastBooking.items) && lastBooking.items.length > 1;

            step4Container.classList.remove('hidden');
            step4Container.innerHTML = `
                <div class="bg-white rounded-3xl p-6 shadow-card border border-gold-soft/50 text-center space-y-4 animate-step max-w-xl mx-auto">
                    <div class="w-16 h-16 rounded-full bg-sage-light border-2 border-primary mx-auto flex items-center justify-center text-primary text-2xl shadow-sm">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-sage-dark">تم تأكيد حجزك بنجاح</h3>
                        <p class="text-sm font-extrabold text-primary-dark mt-1">
                            باسم: <span class="text-sage-dark">${customerName}</span>
                        </p>
                        <div class="mt-2 inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-gold-light text-gold-dark text-xs font-black">
                            <span>رقم الحجز:</span>
                            <span class="font-mono">#${lastBooking.id}</span>
                            <span class="mx-1 opacity-40">•</span>
                            <span class="text-[11px] font-bold text-amber-800 bg-amber-100/90 px-2 py-0.5 rounded-full">قيد الانتظار</span>
                        </div>
                    </div>

                    <!-- Receipt Summary Rows -->
                    <div class="py-3 px-1 text-right space-y-4">
                        <!-- Row 1: اسم العميلة، الخدمة المختارة، الأخصائية -->
                        <div class="grid grid-cols-3 gap-2.5 sm:gap-4 items-start">
                            <div class="min-w-0">
                                <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">اسم العميلة</span>
                                <span class="text-xs sm:text-sm font-extrabold text-sage-dark block truncate">${customerName}</span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">الخدمة المختارة</span>
                                <span class="text-xs sm:text-sm font-extrabold text-sage-dark block truncate">${lastBooking.serviceName}</span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">الأخصائية</span>
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="text-xs sm:text-sm font-extrabold text-sage-dark truncate">${lastBooking.specialistName}</span>
                                </div>
                            </div>
                        </div>

                        ${hasMultipleItems ? `
                            <!-- Multi-specialist breakdown table -->
                            <div class="pt-3 pb-2 border-t border-dashed border-gray-200 text-right space-y-2">
                                <span class="text-[11px] font-bold text-gray-400 block">تفاصيل جدول الخدمات والأخصائيات:</span>
                                <div class="space-y-1.5">
                                    ${lastBooking.items.map(it => `
                                        <div class="p-2.5 rounded-xl bg-surface border border-gray-100 flex items-center justify-between text-xs">
                                            <div class="min-w-0">
                                                <span class="font-bold text-sage-dark block truncate">${it.serviceName}</span>
                                                <span class="text-[10px] text-primary block mt-0.5">
                                                    <i class="fa-regular fa-clock text-[9px] ml-1"></i>${it.startTime} - ${it.endTime}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-primary-dark font-extrabold shrink-0">
                                                <i class="fa-regular fa-user text-[10px] text-gold-dark"></i>
                                                <span>${it.specialistName}</span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}

                        <!-- Row 2: الموعد، عدد الأشخاص -->
                        <div class="grid grid-cols-2 gap-2.5 sm:gap-4 items-start ${hasMultipleItems ? '' : 'border-t border-dashed border-gray-100 pt-3'}">
                            <div class="min-w-0">
                                <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">الموعد</span>
                                <span class="text-xs sm:text-sm font-extrabold text-sage-dark block leading-snug">
                                    ${lastBooking.date}
                                    <span class="text-[11px] font-bold text-primary block">${lastBooking.time}</span>
                                </span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] sm:text-xs text-gray-400 font-bold block mb-1">عدد الأشخاص</span>
                                <span class="text-xs sm:text-sm font-extrabold text-sage-dark block">${guestsNum > 1 ? `${guestsNum} أشخاص` : 'شخص واحد'}</span>
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
            `;
        }
    }
    syncFloatingConfirmButton();
}

export function selectCategory(catId) {
    const found = bookableCategories.find(c => c.id === catId);
    if (!found) return;
    state.bookingState.selectedCategory = found;
    state.bookingState.step = 1;
    renderBookingStep();
}

export function selectSubService(subId, catId = null) {
    const cb = document.getElementById(`srv-check-${subId}`);
    if (cb) {
        cb.checked = true;
        handleServiceCheckboxChange(cb, subId, catId);
    } else {
        const { foundCategory, foundSub } = findCategoryAndSubService(subId, catId);
        if (!foundCategory || !foundSub) return;
        state.bookingState.selectedCategory = foundCategory;
        state.bookingState.selectedSubService = foundSub;
        syncFloatingConfirmButton();
    }
}

export function chooseSpecialistForCurrentService(spId) {
    const sp = specialistsData[spId];
    if (!sp) return;

    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    const currentIdx = state.bookingState.currentServiceIndex || 0;
    const currentSub = subServices[currentIdx] || subServices[0];

    if (!state.bookingState.selectedSpecialistsByService) {
        state.bookingState.selectedSpecialistsByService = {};
    }
    if (currentSub) {
        state.bookingState.selectedSpecialistsByService[currentSub.id] = sp;
    }
    state.bookingState.selectedSpecialist = sp;
    state.bookingState.activeSpecialistId = sp.id;

    // Immediately advance to Step 3 (time determination) for this service
    proceedToTimeStep();
}

export function chooseSpecialist(spId) {
    chooseSpecialistForCurrentService(spId);
}

export function chooseSpecialistForService(serviceId, spId) {
    const sp = specialistsData[spId];
    if (!sp) return;
    if (!state.bookingState.selectedSpecialistsByService) {
        state.bookingState.selectedSpecialistsByService = {};
    }
    state.bookingState.selectedSpecialistsByService[serviceId] = sp;
    state.bookingState.selectedSpecialist = sp;
    state.bookingState.activeSpecialistId = sp.id;
    proceedToTimeStep();
}

export function selectBookingDate(dateStr, dateIso = '') {
    if (state.bookingState.selectedDate === dateStr && (!dateIso || state.bookingState.selectedDateIso === dateIso) && !state.bookingState.availability?.loading) {
        return;
    }
    state.bookingState.selectedDate = dateStr;
    if (dateIso) {
        state.bookingState.selectedDateIso = dateIso;
    }
    state.bookingState.availability = {
        loading: true,
        is_day_off: false,
        shift_label: "",
        slots: []
    };
    fetchSpecialistAvailability();
    renderBookingStep();
}

export function selectBookingTime(timeStr) {
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    const currentIdx = state.bookingState.currentServiceIndex || 0;
    const currentSub = subServices[currentIdx] || subServices[0];
    const activeSpId = state.bookingState.activeSpecialistId || state.bookingState.selectedSpecialist?.id;

    if (!state.bookingState.selectedTimesByService) {
        state.bookingState.selectedTimesByService = {};
    }
    if (!state.bookingState.selectedTimesBySpecialist) {
        state.bookingState.selectedTimesBySpecialist = {};
    }

    if (currentSub) {
        state.bookingState.selectedTimesByService[currentSub.id] = timeStr;
    }
    if (activeSpId) {
        state.bookingState.selectedTimesBySpecialist[activeSpId] = timeStr;
    }
    state.bookingState.selectedTime = timeStr;

    // Sequential multi-service flow: advance to the next service if any
    if (currentIdx + 1 < subServices.length) {
        state.bookingState.currentServiceIndex = currentIdx + 1;
        const nextSub = subServices[currentIdx + 1];
        state.bookingState.selectedSpecialist = state.bookingState.selectedSpecialistsByService?.[nextSub.id] || null;
        state.bookingState.activeSpecialistId = state.bookingState.selectedSpecialist?.id || null;
        state.bookingState.step = 2; // Move back to Step 2 for next service
        state.bookingState.availability = {
            loading: false,
            is_day_off: false,
            shift_label: "",
            slots: []
        };
        renderBookingStep();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        // Final service time selected!
        renderBookingStep();
        syncFloatingConfirmButton();
    }
}

export async function fetchSpecialistAvailability() {
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    if (subServices.length === 0) return;

    const currentIdx = state.bookingState.currentServiceIndex || 0;
    const currentSub = subServices[currentIdx] || subServices[0];
    const sp = state.bookingState.selectedSpecialistsByService?.[currentSub.id] || state.bookingState.selectedSpecialist;
    if (!sp) return;

    if (state.availabilityAbortController) {
        state.availabilityAbortController.abort();
    }
    state.availabilityAbortController = new AbortController();
    const currentSignal = state.availabilityAbortController.signal;

    state.bookingState.availability.loading = true;
    const requestedDate = state.bookingState.selectedDateIso || state.bookingState.selectedDate || '';

    const params = new URLSearchParams({
        specialist_id: sp.id,
        service_id: currentSub.id,
        booking_date: requestedDate
    });

    const endpointUrl = routes.availability ? `${routes.availability}?${params.toString()}` : `/booking/availability?${params.toString()}`;

    try {
        const response = await fetch(endpointUrl, {
            headers: { 'Accept': 'application/json' },
            signal: currentSignal
        });
        const data = await response.json();

        if (response.ok && data.success) {
            state.bookingState.availability = {
                loading: false,
                is_day_off: Boolean(data.is_day_off),
                shift_label: data.shift_label || '',
                slots: Array.isArray(data.slots) ? data.slots : []
            };

            const availableList = state.bookingState.availability.slots.filter(s => s.is_available && !s.is_booked);
            const currentSelectedForService = state.bookingState.selectedTimesByService?.[currentSub.id];
            const currentStillAvailable = availableList.some(s => s.time === currentSelectedForService);

            if (currentSelectedForService && !currentStillAvailable) {
                delete state.bookingState.selectedTimesByService[currentSub.id];
                if (state.bookingState.selectedTimesBySpecialist?.[sp.id] === currentSelectedForService) {
                    delete state.bookingState.selectedTimesBySpecialist[sp.id];
                }
            }

            state.bookingState.selectedTime = state.bookingState.selectedTimesByService?.[currentSub.id] || null;

            if (state.bookingState.step === 3) {
                renderBookingStep();
            }
        } else {
            state.bookingState.availability.loading = false;
            if (state.bookingState.step === 3) {
                renderBookingStep();
            }
        }
    } catch (err) {
        if (err.name === 'AbortError') {
            return;
        }
        state.bookingState.availability.loading = false;
        if (state.bookingState.step === 3) {
            renderBookingStep();
        }
    }
    syncFloatingConfirmButton();
}

export function confirmFinalBooking() {
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    if (subServices.length === 0) return;

    // Validate that every service has a specialist and an appointment time
    for (let i = 0; i < subServices.length; i++) {
        const sub = subServices[i];
        const sp = state.bookingState.selectedSpecialistsByService?.[sub.id];
        if (!sp) {
            goToServiceConfig(i, 2);
            return;
        }
        const time = state.bookingState.selectedTimesByService?.[sub.id];
        if (!time) {
            goToServiceConfig(i, 3);
            return;
        }
    }

    openPhoneVerificationModal();
}

export function changeBookingGuestsCount(delta) {
    const input = document.getElementById('bookingGuestsCountInput');
    const badge = document.getElementById('bookingGuestsCountBadge');
    let current = parseInt(input?.value || state.bookingState.guestsCount || '1', 10);
    if (isNaN(current)) current = 1;
    let next = current + delta;
    if (next < 1) next = 1;
    if (next > 20) next = 20;

    state.bookingState.guestsCount = next;
    if (input) input.value = next;
    if (badge) {
        badge.textContent = next === 1 ? '1 شخص' : (next === 2 ? 'شخصان' : `${next} أشخاص`);
    }
}

export function handleBookingGuestsCountInput(el) {
    const badge = document.getElementById('bookingGuestsCountBadge');
    let val = parseInt(el?.value || '1', 10);
    if (isNaN(val) || val < 1) val = 1;
    if (val > 20) val = 20;

    state.bookingState.guestsCount = val;
    if (badge) {
        badge.textContent = val === 1 ? '1 شخص' : (val === 2 ? 'شخصان' : `${val} أشخاص`);
    }
}

export function handleBookingNotesInput(el) {
    const charCount = document.getElementById('bookingNotesCharCount');
    const val = el?.value || '';
    state.bookingState.customerNotes = val;
    if (charCount) {
        charCount.textContent = `${val.length} / 1000`;
    }
}

export function getBookingWhatsAppShareUrl(booking, customerName = '') {
    const rawWaUrl = window.__HAIRLINE_DATA__?.whatsappUrl || 'https://wa.me/966548857705';
    const baseUrl = rawWaUrl.split('?')[0];

    const b = booking || state.confirmedBookings[0];
    if (!b) return baseUrl;

    const name = customerName || b.customerName || state.currentCustomer?.name || 'عميلة هيرلاين';
    const phone = b.customerPhone || state.currentCustomer?.phone || '';
    const guests = b.guests_count || b.guestsCount || (state.bookingState.guestsCount > 1 ? state.bookingState.guestsCount : null);
    const notes = b.customer_notes || b.customerNotes || state.bookingState.customerNotes || '';

    const lines = [
        'مرحباً صالون هيرلاين،',
        'أود مشاركة تفاصيل حجز جديد:',
        '',
        `رقم الحجز: #${b.id}`,
        `اسم العميلة: ${name}`,
        phone ? `رقم الجوال: ${phone}` : null,
        `الخدمة: ${b.serviceName}`,
        `الأخصائية: ${b.specialistName}`,
        `الموعد: ${b.date}`,
        `الوقت: ${b.time}`,
        b.items && b.items.length > 1 ? 'تفاصيل الخدمات والأخصائيات:\n' + b.items.map(it => `  • ${it.serviceName} مع الأخصائية ${it.specialistName} (${it.startTime} - ${it.endTime})`).join('\n') : null,
        guests && guests > 1 ? `عدد الأشخاص: ${guests} أشخاص` : null,
        notes ? `ملاحظات العميلة: ${notes}` : null,
        b.price ? `التكلفة التقديرية: ${b.price}` : null,
        '',
        'أرجو تأكيد موعدي، شكراً لكم.'
    ].filter(line => line !== null).join('\n');

    return `${baseUrl}?text=${encodeURIComponent(lines)}`;
}
