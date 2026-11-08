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
    if (state.bookingState.step === 3) {
        if (state.availabilityAbortController) {
            state.availabilityAbortController.abort();
        }
        state.bookingState.step = 2;
        renderBookingStep();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else if (state.bookingState.step === 2) {
        if (state.availabilityAbortController) {
            state.availabilityAbortController.abort();
        }
        state.bookingState.step = 1;
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
        confirmFinalBooking();
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
        if (state.bookingState.step === 1) {
            const count = Array.isArray(state.bookingState.selectedSubServices) ? state.bookingState.selectedSubServices.length : 0;
            if (count > 0) {
                shouldShow = true;
                titleText = "تأكيد الحجز واختيار الأخصائية";
                subtitleText = count === 1
                    ? state.bookingState.selectedSubServices[0].name
                    : `متابعة الحجز (${count} خدمات مختارة)`;
                iconClass = "fa-solid fa-check text-xs";
            }
        } else if (state.bookingState.step === 2) {
            const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
                ? state.bookingState.selectedSubServices
                : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
            if (subServices.length > 1) {
                const allAssigned = subServices.every(s => Boolean(state.bookingState.selectedSpecialistsByService?.[s.id]));
                if (allAssigned) {
                    shouldShow = true;
                    titleText = "تأكيد الأخصائيات واختيار الوقت";
                    subtitleText = `تم تحديد أخصائيات (${subServices.length}) خدمات`;
                    iconClass = "fa-solid fa-users text-xs";
                }
            } else if (state.bookingState.selectedSpecialist || (subServices[0] && state.bookingState.selectedSpecialistsByService?.[subServices[0].id])) {
                shouldShow = true;
                const sp = state.bookingState.selectedSpecialist || state.bookingState.selectedSpecialistsByService?.[subServices[0].id];
                const spNameAr = (sp.name || '').split('|')[0].trim();
                titleText = "تأكيد الأخصائية واختيار الوقت";
                subtitleText = `الأخصائية: ${spNameAr}`;
                iconClass = "fa-solid fa-user-check text-xs";
            }
        } else if (state.bookingState.step === 3) {
            const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
                ? state.bookingState.selectedSubServices
                : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
            const isLoadingSlots = Boolean(state.bookingState.availability?.loading);
            const isDayOff = Boolean(state.bookingState.availability && state.bookingState.availability.is_day_off);
            if (!isLoadingSlots && !isDayOff && state.bookingState.selectedTime) {
                shouldShow = true;
                const spNameAr = subServices.length > 1
                    ? `${subServices.length} خدمات`
                    : ((state.bookingState.selectedSpecialist?.name || '').split('|')[0].trim());
                titleText = `تأكيد الموعد (${state.bookingState.selectedTime})`;
                subtitleText = `${state.bookingState.selectedDate || 'الموعد'} • ${spNameAr}`;
                iconClass = "fa-solid fa-calendar-check text-xs";
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
    for (const sub of (state.bookingState.selectedSubServices || [])) {
        if (Array.isArray(sub.specialists) && sub.specialists.length === 1 && !state.bookingState.selectedSpecialistsByService[sub.id]) {
            const spId = sub.specialists[0];
            if (specialistsData[spId]) {
                state.bookingState.selectedSpecialistsByService[sub.id] = specialistsData[spId];
            }
        }
    }
    if (count === 1 && state.bookingState.selectedSubServices[0]) {
        const singleSub = state.bookingState.selectedSubServices[0];
        state.bookingState.selectedSpecialist = state.bookingState.selectedSpecialistsByService[singleSub.id] || null;
    }
    state.bookingState.selectedTime = null;
    state.bookingState.availability = { loading: false, is_day_off: false, shift_label: "", slots: [] };
    state.bookingState.step = 2;
    renderBookingStep();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

export function proceedToTimeStep() {
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    if (subServices.length === 0) return;

    if (subServices.length > 1) {
        const unassigned = subServices.filter(s => !state.bookingState.selectedSpecialistsByService?.[s.id]);
        if (unassigned.length > 0) {
            return;
        }
        const firstSub = subServices[0];
        state.bookingState.selectedSpecialist = state.bookingState.selectedSpecialistsByService[firstSub.id];
    } else {
        const sub = subServices[0];
        if (!state.bookingState.selectedSpecialist) {
            state.bookingState.selectedSpecialist = state.bookingState.selectedSpecialistsByService?.[sub.id] || null;
        }
        if (!state.bookingState.selectedSpecialist) return;
        state.bookingState.selectedSpecialistsByService[sub.id] = state.bookingState.selectedSpecialist;
    }

    state.bookingState.step = 3;
    state.bookingState.selectedTime = null;
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
        const sub = subServices[0];
        state.bookingState.selectedSubService = sub;

        if (pageTitle) pageTitle.textContent = subServices.length > 1 ? "الخطوة 2: اختيار الأخصائية لكل خدمة" : "الخطوة 2: اختيار الأخصائية";
        if (pageSubtitle) {
            pageSubtitle.textContent = subServices.length > 1 
                ? "يرجى تحديد الأخصائية المفضلة لكل خدمة من الخدمات المختارة"
                : "يرجى اختيار الأخصائية المناسبة لمتابعة الحجز";
            pageSubtitle.classList.remove('hidden');
        }
        if (step2Container) step2Container.classList.remove('hidden');

        if (!state.bookingState.selectedSpecialistsByService) {
            state.bookingState.selectedSpecialistsByService = {};
        }

        // Auto-assign any service that only has 1 qualified specialist
        for (const s of subServices) {
            if (Array.isArray(s.specialists) && s.specialists.length === 1 && !state.bookingState.selectedSpecialistsByService[s.id]) {
                const singleSpId = s.specialists[0];
                if (specialistsData[singleSpId]) {
                    state.bookingState.selectedSpecialistsByService[s.id] = specialistsData[singleSpId];
                }
            }
        }

        if (step2Body) {
            if (subServices.length > 1) {
                // Multi-service: Per-service specialist picker
                step2Body.innerHTML = `
                    <div class="space-y-6">
                        <div class="p-4 rounded-2xl bg-gold-light/60 border border-gold/30 text-center space-y-1">
                            <span class="text-[11px] font-bold text-gold-dark block">
                                تحديد الأخصائية لكل خدمة (${subServices.length} خدمات مختارة)
                            </span>
                            <p class="text-xs sm:text-sm text-sage-dark font-extrabold leading-relaxed">
                                كل خدمة يتم تقديمها بواسطة أخصائياتها المؤهلات، اختاري الأخصائية لكل خدمة
                            </p>
                        </div>

                        <div class="space-y-5">
                            ${subServices.map((serviceItem, sIdx) => {
                                const qualifiedSpIds = serviceItem.specialists || [];
                                const qualifiedSpecialists = qualifiedSpIds.map(id => specialistsData[id]).filter(Boolean);
                                const currentAssigned = state.bookingState.selectedSpecialistsByService?.[serviceItem.id];

                                return `
                                    <div class="bg-white rounded-3xl p-5 border border-primary-soft/80 shadow-card space-y-3.5">
                                        <div class="flex items-center justify-between gap-3 pb-3 border-b border-primary-soft/40 flex-wrap">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="w-6 h-6 rounded-full bg-primary/10 text-primary font-black text-xs flex items-center justify-center shrink-0">
                                                    ${sIdx + 1}
                                                </span>
                                                <h4 class="font-black text-base text-sage-dark truncate">${serviceItem.name}</h4>
                                            </div>
                                            <div>
                                                ${currentAssigned ? `
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sage-light text-sage-dark text-xs font-extrabold border border-primary/20">
                                                        <i class="fa-solid fa-check text-primary text-[10px]"></i>
                                                        <span>تم الاختيار: ${(currentAssigned.name || '').split('|')[0].trim()}</span>
                                                    </span>
                                                ` : `
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200/60 animate-pulse">
                                                        <i class="fa-solid fa-hand-pointer text-[10px]"></i>
                                                        <span>يرجى اختيار أخصائية</span>
                                                    </span>
                                                `}
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-1">
                                            ${qualifiedSpecialists.map(sp => {
                                                const nameParts = (sp.name || '').split('|').map(s => s.trim());
                                                const nameAr = nameParts[0] || (sp.name || '');
                                                const nameEn = nameParts[1] || '';
                                                const isSelected = currentAssigned && currentAssigned.id === sp.id;
                                                return `
                                                    <div onclick="chooseSpecialistForService('${serviceItem.id}', '${sp.id}')"
                                                         role="button"
                                                         tabindex="0"
                                                         onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); chooseSpecialistForService('${serviceItem.id}', '${sp.id}'); }"
                                                         class="flex items-center justify-between p-3.5 rounded-2xl border-2 transition-all duration-200 cursor-pointer select-none ${
                                                             isSelected
                                                             ? 'bg-gold-light/40 border-primary ring-2 ring-primary/20 shadow-xs'
                                                             : 'bg-surface border-gray-200/80 hover:border-primary/60 hover:bg-white'
                                                         }">
                                                        <div class="flex items-center gap-3 min-w-0">
                                                            <div class="w-12 h-12 rounded-full overflow-hidden border-2 shrink-0 ${isSelected ? 'border-primary shadow-xs' : 'border-gray-200'} bg-white flex items-center justify-center">
                                                                ${sp.has_custom_avatar && sp.avatar ? `
                                                                    <img src="${sp.avatar}" alt="${nameAr}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(nameAr)}&background=FFFFFF&color=C29E6D&bold=true&size=256';" />
                                                                ` : `
                                                                    <span class="text-sm font-black font-serif text-primary">${sp.initials || 'HL'}</span>
                                                                `}
                                                            </div>
                                                            <div class="min-w-0 text-right">
                                                                <h5 class="text-xs sm:text-sm font-extrabold ${isSelected ? 'text-primary-dark' : 'text-sage-dark'} truncate">${nameAr}</h5>
                                                                <p class="text-[11px] font-bold text-gray-500 truncate">${sp.role || ''}</p>
                                                            </div>
                                                        </div>
                                                        <div class="shrink-0 mr-2">
                                                            ${isSelected ? `
                                                                <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center shadow-xs">
                                                                    <i class="fa-solid fa-check text-[10px]"></i>
                                                                </div>
                                                            ` : `
                                                                <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center text-transparent hover:border-primary">
                                                                    <i class="fa-solid fa-check text-[9px]"></i>
                                                                </div>
                                                            `}
                                                        </div>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            } else {
                // Single service: classic specialist cards grid
                const qualifiedSpIds = sub.specialists || [];
                const linkedSpecialists = qualifiedSpIds.map(id => specialistsData[id]).filter(Boolean);
                if (!state.bookingState.selectedSpecialist && state.bookingState.selectedSpecialistsByService?.[sub.id]) {
                    state.bookingState.selectedSpecialist = state.bookingState.selectedSpecialistsByService[sub.id];
                }
                const servicesTitleText = sub.name;

                step2Body.innerHTML = `
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-gold-light/60 border border-gold/30 text-center space-y-1">
                            <span class="text-[11px] font-bold text-gold-dark block">
                                ${state.bookingState.selectedCategory?.title || 'الخدمة المختارة'}
                            </span>
                            <h4 class="font-black text-base sm:text-lg text-sage-dark text-center leading-relaxed">
                                ${servicesTitleText}
                            </h4>
                        </div>

                        <div class="space-y-3">
                            <div class="${linkedSpecialists.length === 1 ? 'w-full max-w-sm sm:max-w-md mx-auto' : (linkedSpecialists.length === 2 ? 'grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-3xl mx-auto' : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5')}">
                                ${linkedSpecialists.map(sp => {
                                    const nameParts = (sp.name || '').split('|').map(s => s.trim());
                                    const nameAr = nameParts[0] || (sp.name || '');
                                    const nameEn = nameParts[1] || '';
                                    const isSelected = state.bookingState.selectedSpecialist && state.bookingState.selectedSpecialist.id === sp.id;
                                    return `
                                        <article onclick="chooseSpecialist('${sp.id}')" 
                                                 role="button"
                                                 tabindex="0"
                                                 onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); chooseSpecialist('${sp.id}'); }"
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
                                                            <span>${isSelected ? 'الأخصائية المحددة للحجز' : 'اختيار هذه الأخصائية'}</span>
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

        if (subServices.length > 1) {
            const unassigned = subServices.filter(s => !state.bookingState.selectedSpecialistsByService?.[s.id]);
            if (unassigned.length > 0) {
                state.bookingState.step = 2;
                renderBookingStep();
                return;
            }
        } else {
            if (!state.bookingState.selectedSpecialist && state.bookingState.selectedSpecialistsByService?.[subServices[0].id]) {
                state.bookingState.selectedSpecialist = state.bookingState.selectedSpecialistsByService[subServices[0].id];
            }
            if (!state.bookingState.selectedSpecialist) {
                state.bookingState.step = 2;
                renderBookingStep();
                return;
            }
        }

        if (pageTitle) pageTitle.textContent = "الخطوة 3: تحديد اليوم والوقت";
        if (pageSubtitle) {
            pageSubtitle.textContent = "اختاري اليوم والوقت المناسب لزيارتكِ";
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

        const sp = state.bookingState.selectedSpecialist || (subServices[0] ? state.bookingState.selectedSpecialistsByService?.[subServices[0].id] : null);
        const nameParts = ((sp && sp.name) || '').split('|').map(s => s.trim());
        const spNameAr = nameParts[0] || (sp ? sp.name : '');
        const spNameEn = nameParts[1] || '';
        const servicesTitleText = subServices.map(s => s.name).join(' • ');

        if (step3Body) {
            let headerCardHtml = '';
            if (subServices.length > 1) {
                headerCardHtml = `
                    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-primary-soft/60 shadow-xs space-y-3">
                        <div class="flex items-center justify-between border-b border-primary-soft/40 pb-2.5">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-primary text-sm"></i>
                                <span class="text-xs sm:text-sm font-black text-sage-dark">الخدمات المختارة والأخصائيات (${subServices.length}):</span>
                            </div>
                            <button type="button" 
                                    onclick="handleBookingBack()" 
                                    class="shrink-0 px-3 py-1.5 rounded-xl border border-primary-soft text-primary-dark text-xs font-bold hover:bg-primary hover:text-white transition cursor-pointer flex items-center gap-1.5">
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                <span>تعديل الأخصائيات</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                            ${subServices.map(s => {
                                const assignedSp = state.bookingState.selectedSpecialistsByService?.[s.id] || state.bookingState.selectedSpecialist;
                                const assignedName = assignedSp ? (assignedSp.name || '').split('|')[0].trim() : 'غير محدد';
                                return `
                                    <div class="p-3 rounded-xl bg-surface border border-gray-100 flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <span class="font-extrabold text-xs text-sage-dark block truncate">${s.name}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-primary-dark text-xs font-black shrink-0">
                                            <i class="fa-regular fa-user text-[11px] text-gold-dark"></i>
                                            <span>${assignedName}</span>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                        <p class="text-[11px] text-gray-500 font-semibold pt-1">
                            <i class="fa-solid fa-circle-info text-primary text-[10px] ml-1"></i>
                            الأوقات المتاحة بالأسفل محسوبة لضمان توفر كل أخصائية لخدمتها بالترتيب دون أي تعارض زمني.
                        </p>
                    </div>
                `;
            } else {
                headerCardHtml = `
                    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-primary-soft/60 shadow-xs flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full overflow-hidden border-2 border-primary shrink-0 bg-surface flex items-center justify-center">
                                ${sp && sp.has_custom_avatar && sp.avatar ? `
                                    <img src="${sp.avatar}" alt="${spNameAr}" class="w-full h-full object-cover" />
                                ` : `
                                    <span class="text-lg font-black font-serif text-primary">${sp?.initials || 'HL'}</span>
                                `}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-bold text-gray-400">الأخصائية المختارة:</span>
                                    <span class="font-black text-sm sm:text-base text-sage-dark">${spNameAr}</span>
                                    ${spNameEn ? `<span class="text-[11px] font-serif text-primary-dark tracking-wide uppercase">${spNameEn}</span>` : ''}
                                </div>
                                <p class="text-xs font-bold text-primary truncate mt-0.5">${servicesTitleText}</p>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="handleBookingBack()" 
                                class="shrink-0 px-3 py-1.5 rounded-xl border border-primary-soft text-primary-dark text-xs font-bold hover:bg-primary hover:text-white transition cursor-pointer flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            <span>تغيير الأخصائية</span>
                        </button>
                    </div>
                `;
            }

            step3Body.innerHTML = `
                <div class="space-y-5">
                    ${headerCardHtml}

                    <!-- Date & Time Picker Section -->
                    <div class="space-y-4 pt-1">
                        <div class="flex items-center justify-between gap-2">
                            <label class="block text-xs sm:text-sm font-extrabold text-sage-dark">
                                اختاري اليوم المناسب:
                            </label>
                            ${isLoadingSlots ? `
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-gold-light/50 text-gold-dark text-[10px] font-bold border border-gold-soft/50 animate-pulse">
                                    <i class="fa-solid fa-spinner fa-spin text-[10px]"></i>
                                    <span>جاري تحديث الأوقات...</span>
                                </span>
                            ` : (shiftLabel ? `
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-sage-soft text-sage-dark text-[10px] font-bold border border-gold-soft/50">
                                    <i class="fa-regular fa-calendar-check text-gold-dark"></i>
                                    <span>${shiftLabel}</span>
                                </span>
                            ` : '')}
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
                                ${subServices.length > 1 ? 'اختاري وقت بدء أول خدمة:' : `اختاري الوقت المتاح مع ${spNameAr}:`}
                            </label>
                            ${isLoadingSlots ? `
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 animate-pulse" aria-label="جاري تحميل الأوقات">
                                    ${Array.from({ length: 8 }).map(() => `
                                        <div class="py-2.5 px-2 rounded-xl bg-gray-100 border border-gray-200/60 flex items-center justify-center">
                                            <div class="h-3.5 w-12 bg-gray-200/90 rounded"></div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : (isDayOff ? `
                                <div class="p-4 rounded-2xl bg-red-50/70 border border-red-200 text-center space-y-1">
                                    <p class="text-xs font-extrabold text-red-700">الأخصائية في يوم إجازة في هذا التاريخ</p>
                                    <p class="text-[11px] text-red-600">يرجى اختيار يوم آخر أو الضغط على "تغيير الأخصائية" لاختيار أخصائية أخرى.</p>
                                </div>
                            ` : (shiftSlots.length === 0 ? `
                                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 text-center">
                                    <p class="text-xs font-bold text-gray-500">لا توجد مواعيد متاحة في هذا التاريخ تناسب جدول الأخصائيات المحددة.</p>
                                </div>
                            ` : `
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                    ${shiftSlots.map(slotObj => {
                                        const slotTime = slotObj.time;
                                        const isBooked = Boolean(slotObj.is_booked || !slotObj.is_available);
                                        const isSelectedSlot = !isBooked && state.bookingState.selectedTime === slotTime;

                                        if (isBooked) {
                                            return `
                                                <button type="button"
                                                        disabled
                                                        title="هذا الموعد محجوز مسبقاً"
                                                        class="py-2 px-1.5 rounded-xl text-[11px] font-bold border border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed flex flex-col items-center justify-center gap-0.5 opacity-75">
                                                    <span class="line-through">${slotTime}</span>
                                                    <span class="text-[9px] font-extrabold text-red-500 bg-red-50 px-1.5 py-0.2 rounded">محجوز</span>
                                                </button>
                                            `;
                                        }

                                        return `
                                            <button type="button"
                                                    onclick="selectBookingTime('${slotTime}')" 
                                                    class="py-2.5 rounded-xl text-xs font-bold border transition cursor-pointer ${
                                                        isSelectedSlot 
                                                        ? 'bg-primary text-white border-primary shadow-xs' 
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

export function chooseSpecialist(spId) {
    const sp = specialistsData[spId];
    if (!sp) return;
    if (state.bookingState.selectedSpecialist && state.bookingState.selectedSpecialist.id === sp.id && !state.bookingState.availability.loading) {
        return;
    }
    state.bookingState.selectedSpecialist = sp;
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    if (subServices.length === 1) {
        if (!state.bookingState.selectedSpecialistsByService) {
            state.bookingState.selectedSpecialistsByService = {};
        }
        state.bookingState.selectedSpecialistsByService[subServices[0].id] = sp;
    }
    state.bookingState.selectedTime = null;
    state.bookingState.availability = {
        loading: true,
        is_day_off: false,
        shift_label: "",
        slots: []
    };
    fetchSpecialistAvailability();
    renderBookingStep();
}

export function chooseSpecialistForService(serviceId, spId) {
    const sp = specialistsData[spId];
    if (!sp) return;
    if (!state.bookingState.selectedSpecialistsByService) {
        state.bookingState.selectedSpecialistsByService = {};
    }
    state.bookingState.selectedSpecialistsByService[serviceId] = sp;
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    if (subServices.length <= 1) {
        state.bookingState.selectedSpecialist = sp;
    }
    renderBookingStep();
    syncFloatingConfirmButton();
}

export function selectBookingDate(dateStr, dateIso = '') {
    if (state.bookingState.selectedDate === dateStr && (!dateIso || state.bookingState.selectedDateIso === dateIso) && !state.bookingState.availability.loading) {
        return;
    }
    state.bookingState.selectedDate = dateStr;
    if (dateIso) {
        state.bookingState.selectedDateIso = dateIso;
    }
    state.bookingState.selectedTime = null;
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
    state.bookingState.selectedTime = timeStr;
    renderBookingStep();
}

export async function fetchSpecialistAvailability() {
    const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
        ? state.bookingState.selectedSubServices
        : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
    if (subServices.length === 0) return;

    if (state.availabilityAbortController) {
        state.availabilityAbortController.abort();
    }
    state.availabilityAbortController = new AbortController();
    const currentSignal = state.availabilityAbortController.signal;

    state.bookingState.availability.loading = true;
    const requestedDate = state.bookingState.selectedDateIso || state.bookingState.selectedDate || '';

    let params;
    if (subServices.length > 1) {
        const items = subServices.map(s => ({
            service_id: s.id,
            specialist_id: state.bookingState.selectedSpecialistsByService?.[s.id]?.id || state.bookingState.selectedSpecialist?.id
        }));
        params = new URLSearchParams({
            items: JSON.stringify(items),
            booking_date: requestedDate,
            service_id: subServices[0].id,
            specialist_id: items[0].specialist_id || ''
        });
    } else {
        const sp = state.bookingState.selectedSpecialist || state.bookingState.selectedSpecialistsByService?.[subServices[0].id];
        if (!sp) return;
        params = new URLSearchParams({
            specialist_id: sp.id,
            service_id: subServices[0].id,
            booking_date: requestedDate
        });
    }

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
            const currentStillAvailable = availableList.some(s => s.time === state.bookingState.selectedTime);

            if (!currentStillAvailable) {
                state.bookingState.selectedTime = availableList.length > 0 ? availableList[0].time : null;
            }

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
    if (subServices.length === 0 || !state.bookingState.selectedTime) return;

    if (subServices.length > 1) {
        const unassigned = subServices.filter(s => !state.bookingState.selectedSpecialistsByService?.[s.id]);
        if (unassigned.length > 0) return;
    } else if (!state.bookingState.selectedSpecialist && !state.bookingState.selectedSpecialistsByService?.[subServices[0].id]) {
        return;
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
