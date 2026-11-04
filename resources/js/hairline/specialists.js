import { bookableCategories, specialistsListArray, state } from './state.js';

export function getSpecialistServiceCategories(specialistId) {
    if (!Array.isArray(bookableCategories)) return [];
    const matchedTitles = [];
    bookableCategories.forEach(cat => {
        const hasSpec = Array.isArray(cat.subServices) && cat.subServices.some(
            sub => Array.isArray(sub.specialists) && sub.specialists.includes(specialistId)
        );
        if (hasSpec && cat.title) {
            matchedTitles.push(cat.title);
        }
    });
    return matchedTitles;
}

export function populateSpecialistModal(index, direction = 0) {
    const total = specialistsListArray.length;
    if (total === 0) return;

    state.currentSpecialistModalIndex = ((index % total) + total) % total;
    const sp = specialistsListArray[state.currentSpecialistModalIndex];
    if (!sp) return;

    const counterEl = document.getElementById('specialistModalCounter');
    const imgEl = document.getElementById('specialistModalImg');
    const initialsWrapper = document.getElementById('specialistModalInitialsWrapper');
    const initialsText = document.getElementById('specialistModalInitials');
    const nameEl = document.getElementById('specialistModalName');
    const roleEl = document.getElementById('specialistModalRole');
    const expEl = document.getElementById('specialistModalExperience');
    const bioEl = document.getElementById('specialistModalBio');
    const servicesWrapper = document.getElementById('specialistModalServicesWrapper');
    const animatedBody = document.getElementById('specialistModalAnimatedBody');

    if (counterEl) {
        counterEl.textContent = `${state.currentSpecialistModalIndex + 1} / ${total}`;
    }

    if (sp.has_custom_avatar && sp.avatar) {
        if (imgEl) {
            imgEl.src = sp.avatar;
            imgEl.alt = sp.name || '';
            imgEl.classList.remove('hidden');
        }
        if (initialsWrapper) initialsWrapper.classList.add('hidden');
    } else {
        if (imgEl) imgEl.classList.add('hidden');
        if (initialsWrapper) {
            initialsWrapper.classList.remove('hidden');
            if (initialsText) initialsText.textContent = sp.initials || 'HL';
        }
    }

    if (nameEl) nameEl.textContent = sp.name || '';
    if (roleEl) roleEl.textContent = sp.role || '';
    if (expEl) expEl.textContent = sp.experience || '';
    if (bioEl) bioEl.innerHTML = sp.bio || '';
    if (servicesWrapper) servicesWrapper.classList.add('hidden');

    // Highlight active thumbnail
    for (let i = 0; i < total; i++) {
        const thumbBtn = document.getElementById(`specialist-modal-thumb-${i}`);
        if (!thumbBtn) continue;
        if (i === state.currentSpecialistModalIndex) {
            thumbBtn.className = 'w-10 h-10 rounded-full overflow-hidden border-2 border-primary ring-2 ring-primary/40 scale-110 shadow-sm transition-all duration-200 cursor-pointer bg-surface shrink-0';
        } else {
            thumbBtn.className = 'w-9 h-9 rounded-full overflow-hidden border-2 border-primary-soft opacity-60 hover:opacity-100 hover:border-primary transition-all duration-200 cursor-pointer bg-surface shrink-0';
        }
    }

    // Directional slide animation when flipping between specialists
    if (animatedBody && direction !== 0) {
        animatedBody.classList.remove('modal-slide-right', 'modal-slide-left');
        void animatedBody.offsetWidth;
        animatedBody.classList.add(direction > 0 ? 'modal-slide-left' : 'modal-slide-right');
    }

    const activeCard = document.getElementById(`specialist-card-${state.currentSpecialistModalIndex}`);
    if (activeCard && direction !== 0) {
        activeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

export function openSpecialistModal(index) {
    const modal = document.getElementById('specialistDetailModal');
    const backdrop = document.getElementById('specialistModalBackdrop');
    const modalCard = document.getElementById('specialistModalCard');
    const prevBtn = document.getElementById('specialistModalPrevBtn');
    const nextBtn = document.getElementById('specialistModalNextBtn');
    if (!modal || !modalCard) return;

    state.isSpecialistModalClosing = false;
    populateSpecialistModal(index, 0);

    document.querySelectorAll('.specialist-card-landed').forEach(el => {
        el.classList.remove('specialist-card-landed');
    });

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    if (prevBtn) {
        prevBtn.style.opacity = '1';
        prevBtn.style.transform = '';
    }
    if (nextBtn) {
        nextBtn.style.opacity = '1';
        nextBtn.style.transform = '';
    }

    const sourceCard = document.getElementById(`specialist-card-${state.currentSpecialistModalIndex}`);
    modalCard.style.transition = 'none';
    modalCard.style.transform = 'translate3d(0, 0, 0) scale(1)';
    modalCard.style.opacity = '1';

    const modalRect = modalCard.getBoundingClientRect();

    if (sourceCard && modalRect.width > 0) {
        const sourceRect = sourceCard.getBoundingClientRect();
        if (sourceRect.width > 0) {
            const deltaX = (sourceRect.left + sourceRect.width / 2) - (modalRect.left + modalRect.width / 2);
            const deltaY = (sourceRect.top + sourceRect.height / 2) - (modalRect.top + modalRect.height / 2);
            const scale = Math.max(0.25, Math.min(0.85, sourceRect.width / modalRect.width));

            if (typeof modalCard.animate === 'function') {
                modalCard.animate([
                    {
                        transform: `translate3d(${deltaX}px, ${deltaY}px, 0) scale(${scale})`,
                        opacity: 0.25
                    },
                    {
                        transform: 'translate3d(0, 0, 0) scale(1)',
                        opacity: 1
                    }
                ], {
                    duration: 420,
                    easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
                    fill: 'forwards'
                });
            }
        }
    }

    requestAnimationFrame(() => {
        if (backdrop) backdrop.classList.remove('opacity-0');
    });
}

export function navigateSpecialistModal(step, event) {
    if (event) event.stopPropagation();
    if (state.isSpecialistModalClosing) return;
    populateSpecialistModal(state.currentSpecialistModalIndex + step, step);
}

export function selectSpecialistInModal(targetIndex) {
    if (state.isSpecialistModalClosing || targetIndex === state.currentSpecialistModalIndex) return;
    const dir = targetIndex > state.currentSpecialistModalIndex ? 1 : -1;
    populateSpecialistModal(targetIndex, dir);
}

export function closeSpecialistModal(afterCloseCallback = null) {
    const modal = document.getElementById('specialistDetailModal');
    const backdrop = document.getElementById('specialistModalBackdrop');
    const modalCard = document.getElementById('specialistModalCard');
    const prevBtn = document.getElementById('specialistModalPrevBtn');
    const nextBtn = document.getElementById('specialistModalNextBtn');

    if (!modal || !modalCard || modal.classList.contains('hidden') || state.isSpecialistModalClosing) {
        if (typeof afterCloseCallback === 'function') afterCloseCallback();
        return;
    }

    state.isSpecialistModalClosing = true;

    const targetIndex = state.currentSpecialistModalIndex;
    const targetCard = document.getElementById(`specialist-card-${targetIndex}`);

    if (targetCard) {
        const initialRect = targetCard.getBoundingClientRect();
        if (initialRect.top < 60 || initialRect.bottom > window.innerHeight - 40) {
            targetCard.scrollIntoView({ behavior: 'instant', block: 'center' });
        }
    }

    if (backdrop) backdrop.classList.add('opacity-0');
    if (prevBtn) {
        prevBtn.style.opacity = '0';
        prevBtn.style.transform = 'translateY(-50%) scale(0.75)';
    }
    if (nextBtn) {
        nextBtn.style.opacity = '0';
        nextBtn.style.transform = 'translateY(-50%) scale(0.75)';
    }

    const modalRect = modalCard.getBoundingClientRect();
    const targetRect = targetCard ? targetCard.getBoundingClientRect() : null;

    let deltaX = 0;
    let deltaY = 140;
    let targetScale = 0.35;

    if (targetRect && targetRect.width > 0 && modalRect.width > 0) {
        const modalCenterX = modalRect.left + modalRect.width / 2;
        const modalCenterY = modalRect.top + modalRect.height / 2;
        const targetCenterX = targetRect.left + targetRect.width / 2;
        const targetCenterY = targetRect.top + targetRect.height / 2;

        deltaX = targetCenterX - modalCenterX;
        deltaY = targetCenterY - modalCenterY;

        const scaleW = targetRect.width / modalRect.width;
        const scaleH = targetRect.height / modalRect.height;
        targetScale = Math.max(0.2, Math.min(0.8, Math.min(scaleW, scaleH)));
    }

    const finishClose = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modalCard.style.transform = '';
        modalCard.style.opacity = '';
        modalCard.style.transition = '';
        state.isSpecialistModalClosing = false;

        if (typeof afterCloseCallback === 'function') {
            afterCloseCallback();
        }
    };

    if (targetCard) {
        setTimeout(() => {
            targetCard.classList.remove('specialist-card-landed');
            void targetCard.offsetWidth;
            targetCard.classList.add('specialist-card-landed');
        }, 280);
    }

    if (typeof modalCard.animate === 'function') {
        const dropAnimation = modalCard.animate([
            {
                transform: 'translate3d(0, 0, 0) scale(1)',
                opacity: 1,
                offset: 0
            },
            {
                transform: `translate3d(${deltaX * 0.15}px, -14px, 0) scale(0.95)`,
                opacity: 0.96,
                offset: 0.18
            },
            {
                transform: `translate3d(${deltaX}px, ${deltaY + 10}px, 0) scale(${targetScale * 0.96})`,
                opacity: 0.55,
                offset: 0.84
            },
            {
                transform: `translate3d(${deltaX}px, ${deltaY}px, 0) scale(${targetScale})`,
                opacity: 0,
                offset: 1
            }
        ], {
            duration: 460,
            easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
            fill: 'forwards'
        });

        dropAnimation.onfinish = () => {
            dropAnimation.cancel();
            finishClose();
        };
    } else {
        modalCard.style.transition = 'transform 0.44s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.4s ease';
        modalCard.style.transform = `translate3d(${deltaX}px, ${deltaY}px, 0) scale(${targetScale})`;
        modalCard.style.opacity = '0';
        setTimeout(finishClose, 450);
    }
}

export function bookWithSpecialistFromModal() {
    closeSpecialistModal(() => {
        if (typeof window.openBookingFlow === 'function') {
            window.openBookingFlow();
        }
    });
}

export function initSpecialistKeyboardAndTouch() {
    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('specialistDetailModal');
        if (!modal || modal.classList.contains('hidden')) return;

        if (e.key === 'Escape') {
            e.preventDefault();
            closeSpecialistModal();
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            navigateSpecialistModal(1);
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            navigateSpecialistModal(-1);
        }
    });

    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function (e) {
        const modal = document.getElementById('specialistDetailModal');
        if (!modal || modal.classList.contains('hidden')) return;
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    document.addEventListener('touchend', function (e) {
        const modal = document.getElementById('specialistDetailModal');
        if (!modal || modal.classList.contains('hidden')) return;
        touchEndX = e.changedTouches[0].screenX;
        const diff = touchEndX - touchStartX;
        if (Math.abs(diff) > 55) {
            if (diff > 0) {
                navigateSpecialistModal(1);
            } else {
                navigateSpecialistModal(-1);
            }
        }
    }, { passive: true });
}
