export function showBottomSheet() {
    const overlay = document.getElementById('sheetOverlay');
    const sheet = document.getElementById('bottomSheet');
    if (!overlay || !sheet) return;

    overlay.classList.remove('hidden');
    requestAnimationFrame(() => {
        overlay.classList.remove('opacity-0');
        sheet.classList.remove('translate-y-full');
        sheet.classList.remove('md:scale-75', 'md:opacity-0', 'md:pointer-events-none');
        sheet.classList.add('md:scale-100', 'md:opacity-100', 'md:pointer-events-auto');
    });
}

export function closeBottomSheet() {
    const overlay = document.getElementById('sheetOverlay');
    const sheet = document.getElementById('bottomSheet');
    if (!overlay || !sheet) return;

    overlay.classList.add('opacity-0');
    sheet.classList.add('translate-y-full');
    sheet.classList.remove('md:scale-100', 'md:opacity-100', 'md:pointer-events-auto');
    sheet.classList.add('md:scale-75', 'md:opacity-0', 'md:pointer-events-none');
    setTimeout(() => {
        overlay.classList.add('hidden');
    }, 350);
}

export function openToast(msg) {
    const toast = document.getElementById('toastBanner');
    const text = document.getElementById('toastText');
    if (!toast || !text) return;

    text.textContent = msg;
    toast.classList.remove('hidden');
    toast.classList.add('flex');
    setTimeout(() => {
        hideToast();
    }, 3500);
}

export function hideToast() {
    const toast = document.getElementById('toastBanner');
    if (!toast) return;

    toast.classList.add('hidden');
    toast.classList.remove('flex');
}

export function openAllServicesModal() {
    closeBottomSheet();
    if (typeof window.switchTab === 'function') {
        window.switchTab('services');
    }
}

export function isStandaloneApp() {
    return window.navigator.standalone === true || window.matchMedia('(display-mode: standalone)').matches;
}

export function isFullscreenActive() {
    return isStandaloneApp() || !!(
        document.fullscreenElement ||
        document.webkitFullscreenElement ||
        document.mozFullScreenElement ||
        document.msFullscreenElement
    );
}

export function syncFullscreenUI() {
    const btn = document.getElementById('fullscreenBtn');
    const icon = document.getElementById('fullscreenIcon');
    if (!btn || !icon) return;

    const active = isFullscreenActive();
    if (active) {
        icon.className = 'fa-solid fa-compress text-xs sm:text-sm text-gold-dark';
        btn.setAttribute('title', 'إنهاء ملء الشاشة');
        btn.setAttribute('aria-label', 'إنهاء ملء الشاشة');
        btn.classList.add('bg-gold-light', 'text-gold-dark', 'ring-2', 'ring-gold/40');
        btn.classList.remove('bg-white', 'text-sage-dark');
    } else {
        icon.className = 'fa-solid fa-expand text-xs sm:text-sm text-sage-dark';
        btn.setAttribute('title', 'ملء الشاشة');
        btn.setAttribute('aria-label', 'ملء الشاشة');
        btn.classList.remove('bg-gold-light', 'text-gold-dark', 'ring-2', 'ring-gold/40');
        btn.classList.add('bg-white', 'text-sage-dark');
    }
}

export function toggleFullscreen() {
    try {
        if (isStandaloneApp()) {
            openToast('أنتِ تتصفحين الصالون في وضع ملء الشاشة الكامل 📲✨');
            return;
        }

        const docEl = document.documentElement;
        const requestMethod = docEl.requestFullscreen ||
                              docEl.webkitRequestFullscreen ||
                              docEl.mozRequestFullScreen ||
                              docEl.msRequestFullscreen;

        const exitMethod = document.exitFullscreen ||
                           document.webkitExitFullscreen ||
                           document.mozCancelFullScreen ||
                           document.msExitFullscreen;

        if (requestMethod) {
            if (!isFullscreenActive()) {
                const promise = requestMethod.call(docEl);
                if (promise && typeof promise.then === 'function') {
                    promise.then(() => {
                        syncFullscreenUI();
                        openToast('تم تفعيل وضع ملء الشاشة');
                    }).catch(() => {
                        handleIosOrFallbackFullscreen();
                    });
                } else {
                    syncFullscreenUI();
                    openToast('تم تفعيل وضع ملء الشاشة');
                }
            } else {
                if (exitMethod) {
                    const promise = exitMethod.call(document);
                    if (promise && typeof promise.then === 'function') {
                        promise.then(() => {
                            syncFullscreenUI();
                            openToast('تم الخروج من وضع ملء الشاشة');
                        }).catch(() => {
                            handleIosOrFallbackFullscreen();
                        });
                    } else {
                        syncFullscreenUI();
                        openToast('تم الخروج من وضع ملء الشاشة');
                    }
                }
            }
            return;
        }

        handleIosOrFallbackFullscreen();
    } catch (err) {
        console.warn('Fullscreen not supported or blocked, opening iOS guide:', err);
        handleIosOrFallbackFullscreen();
    }
}

export function handleIosOrFallbackFullscreen() {
    openIosFullscreenModal();
}

export function openIosFullscreenModal() {
    const modal = document.getElementById('iosFullscreenModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    void modal.offsetWidth;
    modal.classList.remove('opacity-0');
}

export function closeIosFullscreenModal() {
    const modal = document.getElementById('iosFullscreenModal');
    if (!modal) return;
    modal.classList.add('opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
