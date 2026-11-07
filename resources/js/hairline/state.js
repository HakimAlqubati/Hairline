/**
 * Shared Application State & Data Bridge
 * Connects Laravel Blade payload (window.__HAIRLINE_DATA__) to ES Modules.
 */

const rawData = window.__HAIRLINE_DATA__ || {};

export const routes = rawData.routes || {};
export const csrfToken = rawData.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
export const heroSlides = Array.isArray(rawData.heroSlides) ? rawData.heroSlides : [];
export const walkInServicesData = rawData.walkInServices || [];
export const specialistsData = rawData.specialists || {};
export const bookableCategories = Array.isArray(rawData.bookableCategories) ? rawData.bookableCategories : [];
export const bookingDates = Array.isArray(rawData.bookingDates) ? rawData.bookingDates : [];
export const salonIdentity = rawData.salonIdentity || {};
export const workingHours = rawData.workingHours || {};
export const location = rawData.location || {};

export const SAUDI_PHONE_REGEX = /^(?:\+966|00966|966|0)?5[0-9]{8}$/;

export const defaultBookingDateObj = bookingDates[1] || bookingDates[0] || { display_label: "غداً - السبت", date_iso: "" };

export const state = {
    currentSlideIndex: 0,
    slideInterval: null,
    currentActiveTab: rawData.initialView || 'home',
    floatingConfirmHideTimeout: null,
    availabilityAbortController: null,
    currentSpecialistModalIndex: 0,
    isSpecialistModalClosing: false,
    serviceSuggestionsAbortController: null,
    serviceSuggestionsDebounceTimer: null,
    selectedSuggestionIndex: -1,
    currentCustomer: rawData.customer || null,
    confirmedBookings: Array.isArray(rawData.bookings) ? [...rawData.bookings] : [],
    bookingState: {
        step: 1, // 1: Services, 2: Specialist & Time, 3: Confirmation
        selectedCategory: null,
        selectedSubService: null,
        selectedSubServices: [],
        selectedSpecialist: null,
        selectedDate: defaultBookingDateObj.display_label,
        selectedDateIso: defaultBookingDateObj.date_iso,
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
    }
};

export const specialistsListArray = Object.values(specialistsData);

export const unifiedSearchConfig = {
    services: {
        inputId: 'serviceSearch',
        clearBtnId: 'clearSearchBtn',
        submitBtnId: 'submitSearchBtn',
        statusId: 'servicesSearchStatus',
        countId: 'servicesSearchCount',
        queryTextId: 'servicesSearchQuery',
        emptyStateId: 'servicesSearchEmptyState',
        emptyQueryId: 'servicesEmptyQueryText',
        resultsContainerId: 'servicesSearchResultsContainer',
        resultsListId: 'servicesSearchResultsList',
        pagesContainerId: 'pagesContainer'
    },
    booking: {
        inputId: 'bookingServiceSearch',
        clearBtnId: 'bookingClearSearchBtn',
        submitBtnId: 'bookingSubmitSearchBtn',
        statusId: 'bookingSearchStatus',
        countId: 'bookingSearchCount',
        queryTextId: 'bookingSearchQuery',
        emptyStateId: 'bookingSearchEmptyState',
        emptyQueryId: 'bookingEmptyQueryText'
    }
};
