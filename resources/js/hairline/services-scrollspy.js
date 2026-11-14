/**
 * Sticky Scrollspy Navigation for the Services Menu
 *
 * Syncs horizontal category pills and an index dropdown menu with vertical
 * scrolling across all service sections, with full RTL horizontal auto-centering
 * and coordination with the auto-hiding top header.
 */

const ACTIVE_PILL_CLASSES = ['bg-primary', 'text-white', 'border-primary', 'shadow-xs'];
const INACTIVE_PILL_CLASSES = ['bg-white', 'text-sage-dark', 'border-[#ddd3bf]', 'hover:border-primary', 'hover:bg-primary-light/60'];

let activeSectionId = null;
let programmaticTargetId = null;
let programmaticLockTimer = null;
let scrollTicking = false;
let isMenuOpen = false;
let menuCloseTimer = null;

function isServicesCatalogVisible() {
    const tabView = document.getElementById('servicesTabView');
    const pagesContainer = document.getElementById('pagesContainer');
    if (!tabView || tabView.classList.contains('hidden')) {
        return false;
    }
    if (!pagesContainer || pagesContainer.classList.contains('hidden')) {
        return false;
    }
    return true;
}

function getStickyBarBottom() {
    const stickyBar = document.getElementById('servicesStickyBar');
    if (!stickyBar) {
        return 0;
    }
    return stickyBar.getBoundingClientRect().bottom;
}

function getSectionAnchorElement(sectionEl) {
    return sectionEl.querySelector('.section-main-title') || sectionEl;
}

function clearProgrammaticLock() {
    programmaticTargetId = null;
    if (programmaticScrollTimerExists()) {
        clearTimeout(programmaticLockTimer);
        programmaticLockTimer = null;
    }
}

function programmaticScrollTimerExists() {
    return programmaticLockTimer !== null;
}

function centerActivePillInStrip(pillEl) {
    const pillStrip = document.getElementById('servicesScrollspyPills');
    if (!pillStrip || !pillEl) {
        return;
    }

    const stripRect = pillStrip.getBoundingClientRect();
    const pillRect = pillEl.getBoundingClientRect();
    const stripCenter = stripRect.left + stripRect.width / 2;
    const pillCenter = pillRect.left + pillRect.width / 2;
    const deltaX = pillCenter - stripCenter;

    if (Math.abs(deltaX) < 4) {
        return;
    }

    pillStrip.scrollBy({
        left: deltaX,
        behavior: 'smooth',
    });
}

export function setActiveScrollspySection(sectionId, { centerPill = true } = {}) {
    if (!sectionId || sectionId === activeSectionId) {
        if (centerPill && sectionId) {
            const activePill = document.querySelector(`[data-scrollspy-pill="${sectionId}"]`);
            centerActivePillInStrip(activePill);
        }
        return;
    }

    activeSectionId = sectionId;

    const pills = document.querySelectorAll('[data-scrollspy-pill]');
    let targetPill = null;

    pills.forEach(pill => {
        const isMatch = pill.getAttribute('data-scrollspy-pill') === sectionId;
        pill.setAttribute('aria-selected', isMatch ? 'true' : 'false');

        if (isMatch) {
            pill.classList.remove(...INACTIVE_PILL_CLASSES);
            pill.classList.add(...ACTIVE_PILL_CLASSES);
            targetPill = pill;
        } else {
            pill.classList.remove(...ACTIVE_PILL_CLASSES);
            pill.classList.add(...INACTIVE_PILL_CLASSES);
        }
    });

    const menuItems = document.querySelectorAll('[data-scrollspy-menu-item]');
    menuItems.forEach(item => {
        const isMatch = item.getAttribute('data-scrollspy-menu-item') === sectionId;
        const checkBadge = item.querySelector('[data-scrollspy-check]');

        if (isMatch) {
            item.classList.add('bg-primary-light/80');
            item.classList.remove('hover:bg-surface');
            if (checkBadge) {
                checkBadge.classList.remove('hidden');
            }
        } else {
            item.classList.remove('bg-primary-light/80');
            item.classList.add('hover:bg-surface');
            if (checkBadge) {
                checkBadge.classList.add('hidden');
            }
        }
    });

    if (centerPill && targetPill) {
        centerActivePillInStrip(targetPill);
    }
}

export function scrollToServiceSection(sectionId) {
    const sectionEl = document.querySelector(`[data-scrollspy-section="${sectionId}"]`);
    if (!sectionEl) {
        return;
    }

    const anchorEl = getSectionAnchorElement(sectionEl);
    const stickyBar = document.getElementById('servicesStickyBar');
    const mainHeader = document.getElementById('mainHeader');

    const currentScrollY = Math.max(0, window.pageYOffset || document.documentElement.scrollTop || 0);
    const stickyHeight = stickyBar ? stickyBar.offsetHeight : 0;
    const headerHeight = mainHeader ? mainHeader.offsetHeight : 0;

    const anchorDocTop = anchorEl.getBoundingClientRect().top + currentScrollY;
    const roughDestinationY = Math.max(0, anchorDocTop - stickyHeight - 16);

    // Predict whether #mainHeader will be hidden or visible at destination
    const scrollingDownPastThreshold = roughDestinationY > currentScrollY && roughDestinationY > 75;
    const destinationHeaderOffset = scrollingDownPastThreshold ? 0 : headerHeight;
    const targetScrollY = Math.max(0, anchorDocTop - destinationHeaderOffset - stickyHeight - 16);

    clearProgrammaticLock();
    programmaticTargetId = sectionId;
    programmaticLockTimer = setTimeout(() => {
        programmaticTargetId = null;
        programmaticLockTimer = null;
    }, 950);

    setActiveScrollspySection(sectionId, { centerPill: true });

    window.scrollTo({
        top: targetScrollY,
        behavior: 'smooth',
    });
}

export function toggleServicesScrollspyMenu(forceState = null) {
    const menu = document.getElementById('servicesScrollspyMenu');
    const btn = document.getElementById('servicesScrollspyMenuBtn');
    if (!menu || !btn) {
        return;
    }

    const nextState = typeof forceState === 'boolean' ? forceState : !isMenuOpen;
    if (nextState === isMenuOpen) {
        return;
    }

    if (menuCloseTimer) {
        clearTimeout(menuCloseTimer);
        menuCloseTimer = null;
    }

    isMenuOpen = nextState;
    btn.setAttribute('aria-expanded', nextState ? 'true' : 'false');

    if (nextState) {
        btn.classList.add('bg-primary', 'text-white', 'border-primary');
        btn.classList.remove('bg-white', 'text-sage-dark', 'border-[#ddd3bf]');

        menu.classList.remove('hidden');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                menu.classList.remove('opacity-0', 'scale-95');
                menu.classList.add('opacity-100', 'scale-100');
            });
        });

        const activeItem = menu.querySelector(`[data-scrollspy-menu-item="${activeSectionId}"]`);
        if (activeItem) {
            activeItem.scrollIntoView({ block: 'nearest' });
        }
    } else {
        btn.classList.remove('bg-primary', 'text-white', 'border-primary');
        btn.classList.add('bg-white', 'text-sage-dark', 'border-[#ddd3bf]');

        menu.classList.remove('opacity-100', 'scale-100');
        menu.classList.add('opacity-0', 'scale-95');

        menuCloseTimer = setTimeout(() => {
            if (!isMenuOpen) {
                menu.classList.add('hidden');
            }
        }, 200);
    }
}

export function selectServiceSectionFromMenu(sectionId) {
    toggleServicesScrollspyMenu(false);
    scrollToServiceSection(sectionId);
}

export function syncServicesScrollspy() {
    if (!isServicesCatalogVisible()) {
        return;
    }

    const sections = Array.from(document.querySelectorAll('#servicesTabView [data-scrollspy-section]'));
    if (sections.length === 0) {
        return;
    }

    if (programmaticTargetId) {
        return;
    }

    const stickyBottom = getStickyBarBottom();
    const activationY = stickyBottom + 40;

    // If scrolled to the very bottom of the page, highlight the last visible section
    const scrollPos = window.pageYOffset || document.documentElement.scrollTop || 0;
    const atPageBottom = window.innerHeight + scrollPos >= document.documentElement.scrollHeight - 24;
    if (atPageBottom) {
        const lastSection = sections[sections.length - 1];
        const lastRect = lastSection.getBoundingClientRect();
        if (lastRect.top < window.innerHeight) {
            const lastId = lastSection.getAttribute('data-scrollspy-section');
            setActiveScrollspySection(lastId, { centerPill: true });
            return;
        }
    }

    let currentId = sections[0].getAttribute('data-scrollspy-section');

    for (const sec of sections) {
        const anchor = getSectionAnchorElement(sec);
        const rect = anchor.getBoundingClientRect();
        if (rect.top <= activationY) {
            currentId = sec.getAttribute('data-scrollspy-section');
        } else {
            break;
        }
    }

    setActiveScrollspySection(currentId, { centerPill: true });
}

export function initServicesScrollspy() {
    const container = document.getElementById('servicesScrollspyContainer');
    if (!container || container.dataset.scrollspyInitialized === 'true') {
        return;
    }
    container.dataset.scrollspyInitialized = 'true';

    const onScroll = () => {
        if (!scrollTicking) {
            window.requestAnimationFrame(() => {
                syncServicesScrollspy();
                scrollTicking = false;
            });
            scrollTicking = true;
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });

    // Cancel programmatic smooth-scroll lock immediately if the user manually scrolls
    ['wheel', 'touchstart', 'pointerdown'].forEach(evt => {
        window.addEventListener(evt, () => {
            if (programmaticTargetId) {
                clearProgrammaticLock();
            }
        }, { passive: true });
    });

    // Close categories dropdown on outside click
    document.addEventListener('click', event => {
        if (!isMenuOpen) {
            return;
        }
        const menu = document.getElementById('servicesScrollspyMenu');
        const btn = document.getElementById('servicesScrollspyMenuBtn');
        if (menu && btn && !menu.contains(event.target) && !btn.contains(event.target)) {
            toggleServicesScrollspyMenu(false);
        }
    });

    // Close categories dropdown on Escape key
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && isMenuOpen) {
            toggleServicesScrollspyMenu(false);
        }
    });

    syncServicesScrollspy();
}
