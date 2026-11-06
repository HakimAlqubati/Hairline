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
        confirmedCustomerEmail: ""
    };

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

    const activeRow = document.getElementById(`srv-row-${subId}`);

    if (checkboxEl.checked) {
        if (activeRow) {
            activeRow.classList.add('bg-gold-light/40');
        }
        const { foundCategory, foundSub } = findCategoryAndSubService(subId, catId);
        if (foundSub && !state.bookingState.selectedSubServices.some(s => s.id === foundSub.id)) {
            state.bookingState.selectedSubServices.push(foundSub);
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
            if (state.bookingState.selectedSpecialist) {
                shouldShow = true;
                const spNameAr = (state.bookingState.selectedSpecialist.name || '').split('|')[0].trim();
                titleText = "تأكيد الأخصائية واختيار الوقت";
                subtitleText = `الأخصائية: ${spNameAr}`;
                iconClass = "fa-solid fa-user-check text-xs";
            }
        } else if (state.bookingState.step === 3) {
            const isLoadingSlots = Boolean(state.bookingState.availability?.loading);
            const isDayOff = Boolean(state.bookingState.availability && state.bookingState.availability.is_day_off);
            if (!isLoadingSlots && !isDayOff && state.bookingState.selectedTime) {
                shouldShow = true;
                const spNameAr = (state.bookingState.selectedSpecialist?.name || '').split('|')[0].trim();
                titleText = `تأكيد الموعد (${state.bookingState.selectedTime})`;
                subtitleText = `${state.bookingState.selectedDate || 'الموعد'} مع ${spNameAr}`;
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
    state.bookingState.selectedSpecialist = null;
    state.bookingState.selectedTime = null;
    state.bookingState.availability = { loading: false, is_day_off: false, shift_label: "", slots: [] };
    state.bookingState.step = 2;
    renderBookingStep();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

export function proceedToTimeStep() {
    if (!state.bookingState.selectedSpecialist) return;
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

        if (pageTitle) pageTitle.textContent = "الخطوة 2: اختيار الأخصائية";
        if (pageSubtitle) {
            pageSubtitle.textContent = "يرجى اختيار الأخصائية المناسبة لمتابعة الحجز";
            pageSubtitle.classList.remove('hidden');
        }
        if (step2Container) step2Container.classList.remove('hidden');

        const allSpecialistLists = subServices.map(s => s.specialists || []);
        const commonSlugs = allSpecialistLists.length > 1
            ? allSpecialistLists.reduce((a, b) => a.filter(c => b.includes(c)))
            : (allSpecialistLists[0] || []);
        const finalSpecialistSlugs = commonSlugs.length > 0 ? commonSlugs : [...new Set(allSpecialistLists.flat())];
        const linkedSpecialists = finalSpecialistSlugs.map(id => specialistsData[id]).filter(Boolean);

        if (step2Body) {
            const servicesTitleText = subServices.map(s => s.name).join(' • ');
            step2Body.innerHTML = `
                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-gold-light/60 border border-gold/30 text-center space-y-1">
                        <span class="text-[11px] font-bold text-gold-dark block">
                            ${subServices.length > 1 ? `الخدمات المختارة (${subServices.length})` : (state.bookingState.selectedCategory?.title || 'الخدمة المختارة')}
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

        if (step2Footer) {
            step2Footer.innerHTML = '';
        }
    } else if (state.bookingState.step === 3) {
        const subServices = (Array.isArray(state.bookingState.selectedSubServices) && state.bookingState.selectedSubServices.length > 0)
            ? state.bookingState.selectedSubServices
            : (state.bookingState.selectedSubService ? [state.bookingState.selectedSubService] : []);
        if (subServices.length === 0 || !state.bookingState.selectedSpecialist) {
            state.bookingState.step = 2;
            renderBookingStep();
            return;
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

        const sp = state.bookingState.selectedSpecialist;
        const nameParts = (sp.name || '').split('|').map(s => s.trim());
        const spNameAr = nameParts[0] || (sp.name || '');
        const spNameEn = nameParts[1] || '';
        const servicesTitleText = subServices.map(s => s.name).join(' • ');

        if (step3Body) {
            step3Body.innerHTML = `
                <div class="space-y-5">
                    <!-- Selected Service & Specialist Header Card -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-primary-soft/60 shadow-xs flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full overflow-hidden border-2 border-primary shrink-0 bg-surface flex items-center justify-center">
                                ${sp.has_custom_avatar && sp.avatar ? `
                                    <img src="${sp.avatar}" alt="${spNameAr}" class="w-full h-full object-cover" />
                                ` : `
                                    <span class="text-lg font-black font-serif text-primary">${sp.initials || 'HL'}</span>
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
                                اختاري الوقت المتاح مع ${spNameAr}:
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
                                    <p class="text-xs font-bold text-gray-500">لا توجد مواعيد متاحة في هذا التاريخ.</p>
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
    } else if (state.bookingState.step === 4 || state.bookingState.step === 5) {
        const lastBooking = state.confirmedBookings[0];
        const customerName = state.bookingState.confirmedCustomerName || lastBooking?.customerName || state.currentCustomer?.name || "";
        if (pageTitle) pageTitle.textContent = "تم تأكيد حجزك بنجاح";
        if (pageSubtitle) pageSubtitle.textContent = "رقم الحجز: " + (lastBooking?.id || '');
        if (step4Container && lastBooking) {
            const waUrl = getBookingWhatsAppShareUrl(lastBooking, customerName);
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

                    <div class="bg-surface rounded-2xl p-4 border border-gold-soft text-right space-y-3">
                        <div class="pb-2.5 border-b border-gray-200/60">
                            <span class="text-xs text-gray-500 font-bold block mb-1">اسم العميلة:</span>
                            <span class="text-sm font-extrabold text-sage-dark">${customerName}</span>
                        </div>
                        <div class="pb-2.5 border-b border-gray-200/60">
                            <span class="text-xs text-gray-500 font-bold block mb-1">الخدمة المختارة:</span>
                            <span class="text-sm font-extrabold text-sage-dark">${lastBooking.serviceName}</span>
                        </div>
                        <div class="pb-2.5 border-b border-gray-200/60">
                            <span class="text-xs text-gray-500 font-bold block mb-1">الأخصائية:</span>
                            <div class="flex items-center gap-2">
                                ${state.bookingState.selectedSpecialist?.avatar ? `
                                    <img src="${state.bookingState.selectedSpecialist.avatar}" 
                                         alt="${lastBooking.specialistName}" 
                                         class="w-7 h-7 rounded-full object-cover border border-primary/40 shrink-0 shadow-2xs" 
                                         onerror="this.classList.add('hidden')" />
                                ` : ''}
                                <span class="text-sm font-extrabold text-sage-dark">${lastBooking.specialistName}</span>
                            </div>
                        </div>
                        <div class="pt-0.5">
                            <span class="text-xs text-gray-500 font-bold block mb-1">الموعد:</span>
                            <span class="text-sm font-extrabold text-sage-dark">${lastBooking.date} • الساعة ${lastBooking.time}</span>
                        </div>
                    </div>

                    <!-- Manual WhatsApp Share Button -->
                    <div class="pt-1">
                        <a href="${waUrl}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           id="bookingReceiptWhatsAppBtn"
                           class="w-full py-3.5 px-4 rounded-2xl bg-[#25D366] hover:bg-[#20BD5A] text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2.5 shadow-md hover:shadow-lg active:scale-98 transition-all cursor-pointer">
                            <i class="fa-brands fa-whatsapp text-xl"></i>
                            <span>إرسال تفاصيل الحجز إلى واتساب الصالون</span>
                        </a>
                         
                    </div>

                    <div class="flex gap-2.5 pt-1">
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
    if (!state.bookingState.selectedSpecialist) return;

    if (state.availabilityAbortController) {
        state.availabilityAbortController.abort();
    }
    state.availabilityAbortController = new AbortController();
    const currentSignal = state.availabilityAbortController.signal;

    state.bookingState.availability.loading = true;
    const requestedSpecialistId = state.bookingState.selectedSpecialist.id;
    const requestedDate = state.bookingState.selectedDateIso || state.bookingState.selectedDate || '';

    const params = new URLSearchParams({
        specialist_id: requestedSpecialistId,
        service_id: state.bookingState.selectedSubService?.id || '',
        booking_date: requestedDate
    });

    const endpointUrl = routes.availability ? `${routes.availability}?${params.toString()}` : `/booking/availability?${params.toString()}`;

    try {
        const response = await fetch(endpointUrl, {
            headers: { 'Accept': 'application/json' },
            signal: currentSignal
        });
        const data = await response.json();

        if (state.bookingState.selectedSpecialist?.id !== requestedSpecialistId) {
            return;
        }

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
    if (!state.bookingState.selectedCategory || !state.bookingState.selectedSubService || !state.bookingState.selectedSpecialist || !state.bookingState.selectedTime) return;
    openPhoneVerificationModal();
}

export function getBookingWhatsAppShareUrl(booking, customerName = '') {
    const rawWaUrl = window.__HAIRLINE_DATA__?.whatsappUrl || 'https://wa.me/966548857705';
    const baseUrl = rawWaUrl.split('?')[0];

    const b = booking || state.confirmedBookings[0];
    if (!b) return baseUrl;

    const name = customerName || b.customerName || state.currentCustomer?.name || 'عميلة هيرلاين';
    const phone = b.customerPhone || state.currentCustomer?.phone || '';

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
        b.price ? `التكلفة التقديرية: ${b.price}` : null,
        '',
        'أرجو تأكيد موعدي، شكراً لكم.'
    ].filter(line => line !== null).join('\n');

    return `${baseUrl}?text=${encodeURIComponent(lines)}`;
}
