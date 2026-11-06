/**
 * Hairline Beauty Lounge — Frontend JS Entry Point
 * Bundled and compiled with Vite 8 + ES Modules.
 */

import { state } from './hairline/state.js';
import { setHeroSlide, startHeroSlider } from './hairline/hero-slider.js';
import {
    updateUrl,
    toggleSidebar,
    initSidebarListeners,
    switchTab,
    openSidebarSection,
    updateBookingBadge,
    toggleDesktopBooking
} from './hairline/navigation.js';
import {
    getSpecialistServiceCategories,
    populateSpecialistModal,
    openSpecialistModal,
    navigateSpecialistModal,
    selectSpecialistInModal,
    closeSpecialistModal,
    bookWithSpecialistFromModal,
    initSpecialistKeyboardAndTouch
} from './hairline/specialists.js';
import {
    openBookingFlow,
    openBookingFlowWithCategory,
    handleBookingBack,
    handleSheetBack,
    toggleBookingTreeCategory,
    setCategoryExpandedState,
    expandAllBookingCategories,
    toggleAllBookingCategories,
    syncToggleAllTreeButton,
    clearAllServiceCheckboxes,
    findCategoryAndSubService,
    handleServiceCheckboxChange,
    syncFloatingConfirmButton,
    handleFloatingBookingConfirm,
    proceedToSpecialistStep,
    proceedToTimeStep,
    updateStepperUI,
    renderBookingStep,
    selectCategory,
    selectSubService,
    chooseSpecialist,
    selectBookingDate,
    selectBookingTime,
    fetchSpecialistAvailability,
    confirmFinalBooking,
    getBookingWhatsAppShareUrl
} from './hairline/booking-wizard.js';
import {
    normalizeDigitsJs,
    openPhoneVerificationModal,
    closePhoneVerificationModal,
    switchPopupToPhoneStep,
    switchPopupToGoogleStep,
    handlePhoneInputValidation,
    submitPhoneVerification,
    startGoogleSignInProcess,
    openGoogleOAuthRedirectPopup,
    completeGoogleBookingWithOAuth,
    finalizeConfirmedBookingFromServer,
    initAuthMessageListener
} from './hairline/auth.js';
import {
    renderCustomerProfile,
    openEditProfileModal,
    closeEditProfileModal,
    previewProfileAvatar,
    submitEditProfile,
    handleCustomerLogout,
    renderMyBookings
} from './hairline/profile.js';
import {
    openAllServicesModal,
    showBottomSheet,
    closeBottomSheet,
    openToast,
    hideToast,
    isStandaloneApp,
    isFullscreenActive,
    syncFullscreenUI,
    toggleFullscreen,
    handleIosOrFallbackFullscreen,
    openIosFullscreenModal,
    closeIosFullscreenModal
} from './hairline/ui-modals.js';
import {
    normalizeSearchText,
    matchesSearchTokens,
    setServicesSearchLoading,
    hideServicesSuggestions,
    handleSearchFocus,
    handleUnifiedSearchInput,
    fetchServiceAjaxSuggestions,
    renderNoServiceMatchNotice,
    renderServiceSuggestions,
    highlightSearchMatch,
    selectServiceSuggestion,
    executeUnifiedServiceSearch,
    clearUnifiedServiceSearch,
    updateSearchFeedbackUI,
    filterServicesCatalogView,
    filterBookingTreeView,
    initServicesSearch
} from './hairline/search.js';

// Retain Alpine.js helper if needed
window.swipeToClose = function (panel, closeCallback) {
    return {
        startY: 0,
        isDragging: false,
        dragOffset: 0,
        init() {
            if (!panel) return;
            panel.addEventListener('touchstart', this.onTouchStart.bind(this), { passive: true });
            panel.addEventListener('touchmove',  this.onTouchMove.bind(this),  { passive: false });
            panel.addEventListener('touchend',   this.onTouchEnd.bind(this),   { passive: true });
        },
        onTouchStart(e) {
            this.startY = e.touches[0].clientY;
            this.isDragging = true;
            this.dragOffset = 0;
            panel.style.transition = 'none';
        },
        onTouchMove(e) {
            if (!this.isDragging) return;
            const deltaY = e.touches[0].clientY - this.startY;
            if (deltaY < 0) return;
            this.dragOffset = deltaY;
            panel.style.transform = `translateX(-50%) translateY(${deltaY}px)`;
            if (deltaY > 10) e.preventDefault();
        },
        onTouchEnd() {
            this.isDragging = false;
            panel.style.transition = '';
            panel.style.transform  = '';
            if (this.dragOffset > 100) {
                closeCallback();
            }
            this.dragOffset = 0;
        },
    };
};

/**
 * Expose all functions invoked by Blade HTML inline handlers (onclick, onsubmit, oninput, etc.)
 * to window so that 100% backward compatibility is guaranteed without touching a single button in Blade!
 */
Object.assign(window, {
    // Navigation & Tabs
    updateUrl,
    toggleSidebar,
    switchTab,
    openSidebarSection,
    updateBookingBadge,
    toggleDesktopBooking,

    // Hero Slider
    setHeroSlide,
    startHeroSlider,

    // Specialists Modal
    getSpecialistServiceCategories,
    populateSpecialistModal,
    openSpecialistModal,
    navigateSpecialistModal,
    selectSpecialistInModal,
    closeSpecialistModal,
    bookWithSpecialistFromModal,

    // Booking Wizard
    openBookingFlow,
    openBookingFlowWithCategory,
    handleBookingBack,
    handleSheetBack,
    toggleBookingTreeCategory,
    setCategoryExpandedState,
    expandAllBookingCategories,
    toggleAllBookingCategories,
    syncToggleAllTreeButton,
    clearAllServiceCheckboxes,
    findCategoryAndSubService,
    handleServiceCheckboxChange,
    syncFloatingConfirmButton,
    handleFloatingBookingConfirm,
    proceedToSpecialistStep,
    proceedToTimeStep,
    updateStepperUI,
    renderBookingStep,
    selectCategory,
    selectSubService,
    chooseSpecialist,
    selectBookingDate,
    selectBookingTime,
    fetchSpecialistAvailability,
    confirmFinalBooking,
    getBookingWhatsAppShareUrl,

    // Auth & Phone Verification
    normalizeDigitsJs,
    openPhoneVerificationModal,
    closePhoneVerificationModal,
    switchPopupToPhoneStep,
    switchPopupToGoogleStep,
    handlePhoneInputValidation,
    submitPhoneVerification,
    startGoogleSignInProcess,
    openGoogleOAuthRedirectPopup,
    completeGoogleBookingWithOAuth,
    finalizeConfirmedBookingFromServer,

    // Profile & Bookings
    renderCustomerProfile,
    openEditProfileModal,
    closeEditProfileModal,
    previewProfileAvatar,
    submitEditProfile,
    handleCustomerLogout,
    renderMyBookings,

    // Modals & Fullscreen
    openAllServicesModal,
    showBottomSheet,
    closeBottomSheet,
    openToast,
    hideToast,
    isStandaloneApp,
    isFullscreenActive,
    syncFullscreenUI,
    toggleFullscreen,
    handleIosOrFallbackFullscreen,
    openIosFullscreenModal,
    closeIosFullscreenModal,

    // Search Engine
    normalizeSearchText,
    matchesSearchTokens,
    setServicesSearchLoading,
    hideServicesSuggestions,
    handleSearchFocus,
    handleUnifiedSearchInput,
    fetchServiceAjaxSuggestions,
    renderNoServiceMatchNotice,
    renderServiceSuggestions,
    highlightSearchMatch,
    selectServiceSuggestion,
    executeUnifiedServiceSearch,
    clearUnifiedServiceSearch,
    updateSearchFeedbackUI,
    filterServicesCatalogView,
    filterBookingTreeView,
    initServicesSearch
});

/**
 * Application Lifecycle Bootstrapping
 */
function initHairlineApp() {
    startHeroSlider();
    renderCustomerProfile();
    updateBookingBadge();
    renderMyBookings();
    initServicesSearch();
    initSidebarListeners();
    initSpecialistKeyboardAndTouch();
    initAuthMessageListener();

    ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(evt => {
        document.addEventListener(evt, syncFullscreenUI);
    });
    syncFullscreenUI();

    // Initial View Routing
    const initial = state.currentActiveTab || 'home';
    if (initial === 'booking') {
        openBookingFlow();
    } else {
        switchTab(initial);
    }

    // Support browser back/forward buttons
    window.addEventListener('popstate', () => {
        const path = window.location.pathname.replace(/^\/+/, '');
        if (path === 'services') switchTab('services');
        else if (path === 'specialists') switchTab('specialists');
        else if (path === 'about') switchTab('about');
        else if (path === 'profile') switchTab('account');
        else if (path === 'booking') openBookingFlow();
        else if (path === 'contact') switchTab('contact');
        else if (path === 'privacy') switchTab('privacy');
        else switchTab('home');
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHairlineApp);
} else {
    initHairlineApp();
}
