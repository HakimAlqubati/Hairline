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
    toggleMobileBookingSummarySheet(false);
    closeBookingCloseConfirmModal();
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
    state.bookingState.selectedTimesByService = {};
    state.bookingState.selectedTimesBySpecialist = {};
    state.bookingState.currentServiceIndex = 0;
    toggleMobileBookingSummarySheet(false);
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
        state.bookingState.selectedSubServices = state.bookingState.selectedSubServices.filter(s => String(s.id) !== String(subId));
        if (state.bookingState.selectedSpecialistsByService) {
            delete state.bookingState.selectedSpecialistsByService[subId];
        }
        if (state.bookingState.selectedTimesByService) {
            delete state.bookingState.selectedTimesByService[subId];
        }
        if (state.bookingState.currentServiceIndex >= state.bookingState.selectedSubServices.length) {
            state.bookingState.currentServiceIndex = Math.max(0, state.bookingState.selectedSubServices.length - 1);
        }
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

let mobileBottomBarHideTimeout = null;
let mobileSummarySheetHideTimeout = null;
let bookingCloseModalHideTimeout = null;
let isMobileSummarySheetOpen = false;
let mobileSheetTouchStartY = null;
let mobileSheetListenersInitialized = false;

function escapeBookingHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

export function formatBookingServiceCountLabel(count) {
    if (!count || count <= 0) return '0 خدمات';
    if (count === 1) return 'خدمة واحدة';
    if (count === 2) return 'خدمتان';
    if (count <= 10) return `${count} خدمات`;
    return `${count} خدمة`;
}

function renderBookingSummaryItemsHtml(subServices) {
    if (!Array.isArray(subServices) || subServices.length === 0) {
        return `
            <div id="desktopSummaryEmptyState" class="h-full min-h-[200px] py-8 px-3 flex flex-col items-center justify-center text-center space-y-2.5">
                <div class="w-12 h-12 rounded-2xl bg-surface border border-primary-soft/70 mx-auto flex items-center justify-center text-primary/70">
                    <i class="fa-regular fa-calendar-plus text-lg"></i>
                </div>
                <p class="text-xs sm:text-sm font-extrabold text-sage-dark">لا توجد خدمات مختارة</p>
                <p class="text-[11px] text-gray-400">
                    اختاري من القائمة لإضافتها هنا.
                </p>
            </div>
        `;
    }

    const selectedDateLabel = state.bookingState.selectedDate || '';

    return subServices.map((sub, idx) => {
        const subIdAttr = escapeBookingHtml(sub.id);
        const subName = escapeBookingHtml(sub.name || '');
        const assignedSp = state.bookingState.selectedSpecialistsByService?.[sub.id];
        const spNameRaw = assignedSp?.name ? assignedSp.name.split('|')[0].trim() : '';
        const spName = spNameRaw ? escapeBookingHtml(spNameRaw) : '';
        const assignedTime = state.bookingState.selectedTimesByService?.[sub.id]
            ? escapeBookingHtml(state.bookingState.selectedTimesByService[sub.id])
            : '';

        const isActiveService = state.bookingState.step >= 2 && idx === (state.bookingState.currentServiceIndex || 0);

        return `
            <div class="group rounded-2xl p-3 border transition-all ${
                isActiveService
                    ? 'bg-gold-light/30 border-primary/50 shadow-2xs'
                    : 'bg-surface/65 border-primary-soft/60 hover:border-gold-soft'
            }">
                <div class="flex items-start justify-between gap-2.5">
                    <div class="flex items-start gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-primary-light text-primary-dark text-[11px] font-black flex items-center justify-center shrink-0 mt-0.5">
                            ${idx + 1}
                        </span>
                        <div class="min-w-0 space-y-1">
                            <h4 class="text-xs sm:text-[13px] font-extrabold text-sage-dark leading-snug break-words">
                                ${subName}
                            </h4>
                            ${(spName || assignedTime) ? `
                                <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                                    ${spName ? `
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white border border-primary-soft/70 text-primary-dark font-bold">
                                            <i class="fa-regular fa-user text-[9px]"></i>
                                            <span>${spName}</span>
                                        </span>
                                    ` : ''}
                                    ${assignedTime ? `
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white border border-gold-soft text-sage-dark font-bold">
                                            <i class="fa-regular fa-clock text-[9px] text-primary"></i>
                                            <span>${selectedDateLabel ? `${escapeBookingHtml(selectedDateLabel)} • ` : ''}${assignedTime}</span>
                                        </span>
                                    ` : ''}
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <button type="button"
                            onclick="removeBookingService('${subIdAttr}')"
                            aria-label="حذف ${subName}"
                            title="حذف الخدمة"
                            class="w-7 h-7 rounded-lg bg-white text-gray-400 hover:bg-red-50 hover:text-red-500 border border-gray-200/80 hover:border-red-200 flex items-center justify-center shrink-0 transition cursor-pointer">
                        <i class="fa-regular fa-trash-can text-[11px]"></i>
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

export function removeBookingService(subId) {
    const targetId = String(subId);

    const checkbox = document.getElementById(`srv-check-${targetId}`)
        || Array.from(document.querySelectorAll('.booking-service-checkbox')).find(cb => String(cb.value) === targetId);
    if (checkbox) {
        checkbox.checked = false;
    }

    const row = document.getElementById(`srv-row-${targetId}`);
    if (row) {
        row.classList.remove('bg-gold-light/40');
    }

    const currentList = Array.isArray(state.bookingState.selectedSubServices)
        ? state.bookingState.selectedSubServices
        : [];
    const remaining = currentList.filter(s => String(s.id) !== targetId);

    state.bookingState.selectedSubServices = remaining;
    if (state.bookingState.selectedSpecialistsByService) {
        delete state.bookingState.selectedSpecialistsByService[targetId];
    }
    if (state.bookingState.selectedTimesByService) {
        delete state.bookingState.selectedTimesByService[targetId];
    }

    if (remaining.length === 0) {
        state.bookingState.selectedSubService = null;
        state.bookingState.selectedCategory = null;
        state.bookingState.selectedSpecialist = null;
        state.bookingState.activeSpecialistId = null;
        state.bookingState.selectedTime = null;
        state.bookingState.currentServiceIndex = 0;
        state.bookingState.step = 1;
        state.bookingState.availability = { loading: false, is_day_off: false, shift_label: '', slots: [] };
        toggleMobileBookingSummarySheet(false);
        renderBookingStep();
        return;
    }

    const maxIdx = remaining.length - 1;
    if ((state.bookingState.currentServiceIndex || 0) > maxIdx) {
        state.bookingState.currentServiceIndex = maxIdx;
    }
    const currentSub = remaining[state.bookingState.currentServiceIndex || 0] || remaining[0];
    state.bookingState.selectedSubService = currentSub;

    if (state.bookingState.step === 2 || state.bookingState.step === 3) {
        const assignedSp = state.bookingState.selectedSpecialistsByService?.[currentSub.id] || null;
        state.bookingState.selectedSpecialist = assignedSp;
        state.bookingState.activeSpecialistId = assignedSp?.id || null;
        state.bookingState.selectedTime = state.bookingState.selectedTimesByService?.[currentSub.id] || null;

        if (state.bookingState.step === 3) {
            if (!assignedSp) {
                state.bookingState.step = 2;
                state.bookingState.availability = { loading: false, is_day_off: false, shift_label: '', slots: [] };
            } else {
                state.bookingState.availability = { loading: true, is_day_off: false, shift_label: '', slots: [] };
                fetchSpecialistAvailability();
            }
        }
        renderBookingStep();
        return;
    }

    syncFloatingConfirmButton();
}

export function toggleMobileBookingSummarySheet(show) {
    const backdrop = document.getElementById('mobileBookingSummaryBackdrop');
    const sheet = document.getElementById('mobileBookingSummarySheet');
    const toggleBtn = document.getElementById('mobileBookingSummaryToggleBtn');
    if (!backdrop || !sheet) return;

    if (mobileSummarySheetHideTimeout) {
        clearTimeout(mobileSummarySheetHideTimeout);
        mobileSummarySheetHideTimeout = null;
    }

    const shouldOpen = typeof show === 'boolean' ? show : !isMobileSummarySheetOpen;
    isMobileSummarySheetOpen = shouldOpen;

    if (toggleBtn) {
        toggleBtn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    }

    if (shouldOpen) {
        backdrop.classList.remove('hidden');
        sheet.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            sheet.classList.remove('translate-y-full');
            sheet.classList.add('translate-y-0');
        });
    } else {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        sheet.classList.remove('translate-y-0');
        sheet.classList.add('translate-y-full');
        mobileSummarySheetHideTimeout = setTimeout(() => {
            if (!isMobileSummarySheetOpen) {
                backdrop.classList.add('hidden');
                sheet.classList.add('hidden');
            }
        }, 300);
    }
}

export function handleMobileSheetSubmit() {
    toggleMobileBookingSummarySheet(false);
    handleFloatingBookingConfirm();
}

export function requestCloseMobileBooking() {
    if (state.bookingState.step >= 4) {
        confirmCloseMobileBooking();
        return;
    }

    const modal = document.getElementById('bookingCloseConfirmModal');
    const card = document.getElementById('bookingCloseConfirmCard');
    if (!modal || !card) {
        confirmCloseMobileBooking();
        return;
    }

    if (bookingCloseModalHideTimeout) {
        clearTimeout(bookingCloseModalHideTimeout);
        bookingCloseModalHideTimeout = null;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
    });
}

export function closeBookingCloseConfirmModal() {
    const modal = document.getElementById('bookingCloseConfirmModal');
    const card = document.getElementById('bookingCloseConfirmCard');
    if (!modal || !card) return;

    if (bookingCloseModalHideTimeout) {
        clearTimeout(bookingCloseModalHideTimeout);
        bookingCloseModalHideTimeout = null;
    }

    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    card.classList.remove('scale-100');
    card.classList.add('scale-95');
    bookingCloseModalHideTimeout = setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
}

export function confirmCloseMobileBooking() {
    closeBookingCloseConfirmModal();
    toggleMobileBookingSummarySheet(false);
    if (typeof window.switchTab === 'function') {
        window.switchTab('home');
    }
}

export function initBookingMobileSheetListeners() {
    if (mobileSheetListenersInitialized) return;
    mobileSheetListenersInitialized = true;

    const sheet = document.getElementById('mobileBookingSummarySheet');
    if (sheet) {
        sheet.addEventListener('touchstart', (event) => {
            const itemsContainer = document.getElementById('mobileSheetItemsContainer');
            if (itemsContainer && itemsContainer.contains(event.target) && itemsContainer.scrollTop > 0) {
                mobileSheetTouchStartY = null;
                return;
            }
            mobileSheetTouchStartY = event.touches?.[0]?.clientY ?? null;
        }, { passive: true });

        sheet.addEventListener('touchmove', (event) => {
            if (mobileSheetTouchStartY === null) return;
            const currentY = event.touches?.[0]?.clientY ?? mobileSheetTouchStartY;
            const deltaY = currentY - mobileSheetTouchStartY;
            if (deltaY > 65) {
                mobileSheetTouchStartY = null;
                toggleMobileBookingSummarySheet(false);
            }
        }, { passive: true });

        sheet.addEventListener('touchend', () => {
            mobileSheetTouchStartY = null;
        }, { passive: true });
    }

    const closeModal = document.getElementById('bookingCloseConfirmModal');
    if (closeModal) {
        closeModal.addEventListener('click', (event) => {
            if (event.target === closeModal) {
                closeBookingCloseConfirmModal();
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const closeModalEl = document.getElementById('bookingCloseConfirmModal');
        if (closeModalEl && !closeModalEl.classList.contains('hidden')) {
            closeBookingCloseConfirmModal();
            return;
        }
        if (isMobileSummarySheetOpen) {
            toggleMobileBookingSummarySheet(false);
        }
    });
}

function syncBookingSplitAndMobileHeader(step, subServices, currentIdx) {
    const splitLayout = document.getElementById('bookingSplitLayout');
    if (splitLayout) {
        if (step >= 1 && step <= 3) {
            splitLayout.classList.remove('hidden');
        } else {
            splitLayout.classList.add('hidden');
        }
    }

    const topTitleEl = document.getElementById('mobileBookingTopTitleText');
    const topSubtitleEl = document.getElementById('mobileBookingTopSubtitleText');
    const mobileBackBtn = document.getElementById('mobileBookingBackBtn');

    if (topTitleEl) {
        if (step === 1) {
            topTitleEl.textContent = 'اختيار الخدمة';
        } else if (step === 2) {
            topTitleEl.textContent = 'اختيار الأخصائية';
        } else if (step === 3) {
            topTitleEl.textContent = 'تحديد الموعد';
        } else {
            topTitleEl.textContent = 'تأكيد الحجز';
        }
    }

    if (topSubtitleEl) {
        if ((step === 2 || step === 3) && subServices.length > 1) {
            topSubtitleEl.textContent = `الخدمة ${currentIdx + 1} من ${subServices.length}`;
            topSubtitleEl.classList.remove('hidden');
        } else {
            topSubtitleEl.textContent = '';
            topSubtitleEl.classList.add('hidden');
        }
    }

    if (mobileBackBtn) {
        if (step === 2 || step === 3) {
            mobileBackBtn.classList.remove('invisible', 'pointer-events-none');
        } else {
            mobileBackBtn.classList.add('invisible', 'pointer-events-none');
        }
    }
}

export function syncFloatingConfirmButton() {
    const container = document.getElementById('floatingBookingConfirmContainer');
    const whatsappContainer = document.getElementById('floatingWhatsappContainer');

    if (state.floatingConfirmHideTimeout) {
        clearTimeout(state.floatingConfirmHideTimeout);
        state.floatingConfirmHideTimeout = null;
    }

    if (container) {
        container.classList.remove('opacity-100', 'translate-y-0', 'scale-100', 'pointer-events-auto');
        container.classList.add('hidden', 'opacity-0', 'translate-y-4', 'scale-95', 'pointer-events-none');
    }

    if (whatsappContainer) {
        if (state.currentActiveTab === 'booking') {
            whatsappContainer.classList.add('hidden');
        } else {
            whatsappContainer.classList.remove('hidden', '-translate-y-16');
        }
    }

    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    const count = subServices.length;
    const currentIdx = state.bookingState.currentServiceIndex || 0;
    const currentSub = subServices[currentIdx] || subServices[0];
    const step = state.bookingState.step || 1;

    syncBookingSplitAndMobileHeader(step, subServices, currentIdx);

    let canProceed = false;
    let actionText = 'اختاري خدمة';
    let actionIconClass = 'fa-solid fa-arrow-left text-xs';

    if (step === 1) {
        if (count > 0) {
            canProceed = true;
            actionText = 'التالي';
            actionIconClass = 'fa-solid fa-arrow-left text-xs';
        } else {
            canProceed = false;
            actionText = 'اختاري خدمة';
            actionIconClass = 'fa-solid fa-arrow-left text-xs';
        }
    } else if (step === 2) {
        const assigned = currentSub && state.bookingState.selectedSpecialistsByService?.[currentSub.id];
        if (assigned) {
            canProceed = true;
            actionText = 'التالي';
            actionIconClass = 'fa-solid fa-arrow-left text-xs';
        } else {
            canProceed = false;
            actionText = 'اختاري الأخصائية';
            actionIconClass = 'fa-solid fa-arrow-left text-xs';
        }
    } else if (step === 3) {
        const isLoadingSlots = Boolean(state.bookingState.availability?.loading);
        const isDayOff = Boolean(state.bookingState.availability && state.bookingState.availability.is_day_off);
        const currentTime = currentSub && state.bookingState.selectedTimesByService?.[currentSub.id];

        if (!isLoadingSlots && !isDayOff && currentTime) {
            const allScheduled = count > 0 && subServices.every(s =>
                Boolean(state.bookingState.selectedSpecialistsByService?.[s.id]) &&
                Boolean(state.bookingState.selectedTimesByService?.[s.id])
            );

            if (allScheduled || count <= 1) {
                canProceed = true;
                actionText = 'تأكيد الحجز';
                actionIconClass = 'fa-solid fa-check text-xs';
            } else if (currentIdx + 1 < count) {
                canProceed = true;
                actionText = 'الخدمة التالية';
                actionIconClass = 'fa-solid fa-arrow-left text-xs';
            } else {
                canProceed = false;
                actionText = 'اختاري الموعد';
                actionIconClass = 'fa-solid fa-clock text-xs';
            }
        } else {
            canProceed = false;
            actionText = 'اختاري الموعد';
            actionIconClass = 'fa-solid fa-clock text-xs';
        }
    }

    const countLabel = formatBookingServiceCountLabel(count);
    const itemsHtml = renderBookingSummaryItemsHtml(subServices);

    // Sync Desktop 35% Summary Sidebar
    const desktopBadge = document.getElementById('desktopSummaryCountBadge');
    const desktopItemsContainer = document.getElementById('desktopSummaryItemsContainer');
    const desktopSubmitBtn = document.getElementById('desktopBookingSubmitBtn');
    const desktopSubmitText = document.getElementById('desktopBookingSubmitBtnText');
    const desktopSubmitIcon = document.getElementById('desktopBookingSubmitBtnIcon');

    if (desktopBadge) desktopBadge.textContent = countLabel;
    if (desktopItemsContainer) desktopItemsContainer.innerHTML = itemsHtml;
    if (desktopSubmitBtn) desktopSubmitBtn.disabled = !canProceed;
    if (desktopSubmitText) desktopSubmitText.textContent = actionText;
    if (desktopSubmitIcon) desktopSubmitIcon.className = actionIconClass;

    // Sync Mobile Bottom Bar & Expanded Half-Screen Sheet
    const mobileBottomBar = document.getElementById('mobileBookingBottomBar');
    const mobileBottomCount = document.getElementById('mobileBottomSummaryCount');
    const mobileActionBtn = document.getElementById('mobileBookingActionBtn');
    const mobileActionText = document.getElementById('mobileBookingActionBtnText');
    const mobileActionIcon = document.getElementById('mobileBookingActionBtnIcon');

    const mobileSheetBadge = document.getElementById('mobileSheetCountBadge');
    const mobileSheetItems = document.getElementById('mobileSheetItemsContainer');
    const mobileSheetBtn = document.getElementById('mobileSheetActionBtn');
    const mobileSheetBtnText = document.getElementById('mobileSheetActionBtnText');
    const mobileSheetBtnIcon = document.getElementById('mobileSheetActionBtnIcon');

    if (mobileBottomCount) mobileBottomCount.textContent = countLabel;
    if (mobileActionBtn) mobileActionBtn.disabled = !canProceed;
    if (mobileActionText) mobileActionText.textContent = actionText;
    if (mobileActionIcon) mobileActionIcon.className = actionIconClass;

    if (mobileSheetBadge) mobileSheetBadge.textContent = countLabel;
    if (mobileSheetItems) mobileSheetItems.innerHTML = itemsHtml;
    if (mobileSheetBtn) mobileSheetBtn.disabled = !canProceed;
    if (mobileSheetBtnText) mobileSheetBtnText.textContent = actionText;
    if (mobileSheetBtnIcon) mobileSheetBtnIcon.className = actionIconClass;

    const shouldShowMobileBar = state.currentActiveTab === 'booking' && step >= 1 && step <= 3 && count > 0;

    if (!shouldShowMobileBar && isMobileSummarySheetOpen) {
        toggleMobileBookingSummarySheet(false);
    }

    if (mobileBottomBar) {
        if (mobileBottomBarHideTimeout) {
            clearTimeout(mobileBottomBarHideTimeout);
            mobileBottomBarHideTimeout = null;
        }

        if (shouldShowMobileBar) {
            mobileBottomBar.classList.remove('hidden');
            requestAnimationFrame(() => {
                mobileBottomBar.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
                mobileBottomBar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
            });
        } else {
            mobileBottomBar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
            mobileBottomBar.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
            mobileBottomBarHideTimeout = setTimeout(() => {
                mobileBottomBar.classList.add('hidden');
            }, 300);
        }
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
    const skeletonDelays = [0, 75, 150, 225, 300];
    return `
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 select-none" role="status" aria-label="تحميل المواعيد">
            ${skeletonDelays.map((delay, idx) => `
                <div class="hairline-slot-skeleton h-[38px] px-2 rounded-xl border flex items-center justify-center"
                     style="--slot-wave-delay: ${delay}ms;"
                     aria-hidden="true">
                    <span class="block h-2.5 ${idx % 2 === 0 ? 'w-12' : 'w-14'} rounded-full bg-[#DFD3BF]/75 blur-[1.5px]"></span>
                </div>
            `).join('')}
            <span class="sr-only">جاري تحميل المواعيد...</span>
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
        if (pageTitle) {
            pageTitle.textContent = "الخدمات المتاحة للحجز";
            pageTitle.classList.remove('hidden');
        }
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
            pageTitle.classList.remove('hidden');
        }
        if (pageSubtitle) {
            pageSubtitle.textContent = "";
            pageSubtitle.classList.add('hidden');
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
            pageTitle.classList.remove('hidden');
        }
        if (pageSubtitle) {
            pageSubtitle.textContent = "";
            pageSubtitle.classList.add('hidden');
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
                                    <span class="font-black text-sm sm:text-base text-sage-dark">${activeSpNameAr}</span>
                                    ${activeSpNameEn ? `<span class="text-[11px] font-serif text-primary-dark tracking-wide uppercase">${activeSpNameEn}</span>` : ''}
                                </div>
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
                        <div>
                            <label class="block text-xs sm:text-sm font-extrabold text-sage-dark">
                                اليوم
                            </label>
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
                                الوقت
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
            step3Footer.innerHTML = '';
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
        if (pageTitle) {
            pageTitle.textContent = "";
            pageTitle.classList.add('hidden');
        }
        if (pageSubtitle) {
            pageSubtitle.textContent = "";
            pageSubtitle.classList.add('hidden');
        }
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
                                    ${lastBooking.items.map(it => {
                                        const startTime = it.startTime || it.time || '';
                                        const endTime = it.endTime || '';
                                        const timeDisplay = (startTime && endTime) ? `${startTime} - ${endTime}` : startTime;

                                        return `
                                        <div class="p-2.5 rounded-xl bg-surface border border-gray-100 flex items-center justify-between text-xs">
                                            <div class="min-w-0">
                                                <span class="font-bold text-sage-dark block truncate">${it.serviceName}</span>
                                                ${timeDisplay ? `
                                                    <span class="text-[10px] text-primary block mt-0.5">
                                                        <i class="fa-regular fa-clock text-[9px] ml-1"></i>${timeDisplay}
                                                    </span>
                                                ` : ''}
                                            </div>
                                            <div class="flex items-center gap-1.5 text-primary-dark font-extrabold shrink-0">
                                                <i class="fa-regular fa-user text-[10px] text-gold-dark"></i>
                                                <span>${it.specialistName}</span>
                                            </div>
                                        </div>
                                    `;}).join('')}
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
        b.items && b.items.length > 1 ? 'تفاصيل الخدمات والأخصائيات:\n' + b.items.map(it => {
            const startTime = it.startTime || it.time || '';
            const endTime = it.endTime || '';
            const timeDisplay = (startTime && endTime) ? `${startTime} - ${endTime}` : startTime;
            return `  • ${it.serviceName} مع الأخصائية ${it.specialistName}${timeDisplay ? ` (${timeDisplay})` : ''}`;
        }).join('\n') : null,
        guests && guests > 1 ? `عدد الأشخاص: ${guests} أشخاص` : null,
        notes ? `ملاحظات العميلة: ${notes}` : null,
        b.price ? `التكلفة التقديرية: ${b.price}` : null,
        '',
        'أرجو تأكيد موعدي، شكراً لكم.'
    ].filter(line => line !== null).join('\n');

    return `${baseUrl}?text=${encodeURIComponent(lines)}`;
}
