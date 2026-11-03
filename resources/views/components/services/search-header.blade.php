@props([
    'context' => 'services',
    'inputId' => 'serviceSearch',
    'clearBtnId' => 'clearSearchBtn',
    'submitBtnId' => 'submitSearchBtn',
    'statusId' => 'servicesSearchStatus',
    'countId' => 'servicesSearchCount',
    'queryTextId' => 'servicesSearchQuery',
    'placeholder' => 'ابحثي عن خدمة، قسم، أو سعر...',
    'sticky' => true,
])

{{-- Unified Search Bar (Used in both Services Menu and Booking Step 1) --}}
<div class="no-print {{ $sticky ? 'sticky top-0 z-20 bg-[#fdfbf7]/95 backdrop-blur-md border-b border-[#e5dec9] py-3 px-4' : 'w-full' }}">
    <div class="{{ $sticky ? 'max-w-xl mx-auto' : 'w-full' }} space-y-2 relative">
        <form onsubmit="executeUnifiedServiceSearch('{{ $context }}', event)" class="relative flex items-center gap-2" role="search" novalidate>
            <div class="relative flex-1">
                <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[#b39d76] flex items-center">
                    <i id="{{ $context }}SearchIcon" class="fa-solid fa-magnifying-glass text-xs sm:text-sm"></i>
                    <i id="{{ $context }}SearchSpinner" class="hidden fa-solid fa-circle-notch fa-spin text-xs sm:text-sm text-primary"></i>
                </span>

                <input
                    id="{{ $inputId }}"
                    type="text"
                    inputmode="search"
                    enterkeyhint="search"
                    autocomplete="off"
                    placeholder="{{ $placeholder }}"
                    oninput="handleUnifiedSearchInput('{{ $context }}')"
                    onfocus="handleSearchFocus('{{ $context }}')"
                    class="w-full bg-white border border-primary-soft/90 focus:border-primary rounded-full py-2.5 pr-9 pl-9 text-xs sm:text-sm font-bold text-sage-dark placeholder-[#b39d76] focus:outline-none focus:ring-2 focus:ring-primary/15 shadow-xs transition"
                />

                <button
                    type="button"
                    id="{{ $clearBtnId }}"
                    onclick="clearUnifiedServiceSearch('{{ $context }}')"
                    class="hidden absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-surface hover:bg-red-50 text-gray-400 hover:text-red-500 text-[11px] font-bold flex items-center justify-center transition cursor-pointer"
                    title="مسح البحث وإظهار الكل"
                    aria-label="مسح البحث">
                    ✕
                </button>
            </div>

            <button
                type="submit"
                id="{{ $submitBtnId }}"
                class="px-4 sm:px-5 py-2.5 rounded-full bg-primary hover:bg-primary-dark text-white text-xs sm:text-sm font-extrabold shadow-xs hover:shadow-md active:scale-95 transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                <span>بحث</span>
            </button>
        </form>

        @if($context === 'services')
            {{-- Background Backdrop Dimmer --}}
            <div id="servicesSuggestionsBackdrop"
                 onclick="hideServicesSuggestions()"
                 class="hidden fixed inset-0 z-30 bg-black/25 backdrop-blur-[1px] transition-opacity"></div>

            {{-- AJAX Live Suggestions Dropdown (Up to 10 Services) --}}
            <div id="servicesSuggestionsDropdown"
                 style="background-color: #FFFFFF !important; opacity: 1 !important; box-shadow: 0 20px 45px -10px rgba(33, 28, 24, 0.22), 0 0 0 1px rgba(194, 158, 109, 0.3) !important;"
                 class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl border border-gold-soft/90 overflow-hidden z-50 transition-all">
                <div id="servicesSuggestionsList" class="p-1.5 space-y-1 max-h-[380px] overflow-y-auto hairline-custom-scroll pl-3"></div>
            </div>
        @endif

        {{-- Active Filter Status Pill --}}
        <div id="{{ $statusId }}" class="hidden items-center justify-between gap-2 px-3.5 py-1.5 rounded-xl bg-primary-light/65 border border-primary-soft/80 text-xs">
            <div class="flex items-center gap-1.5 text-sage-dark font-bold min-w-0 truncate">
                <i class="fa-solid fa-filter text-primary-dark text-[11px] shrink-0"></i>
                <span class="truncate">نتائج البحث عن «<span id="{{ $queryTextId }}" class="text-primary-dark font-extrabold"></span>»:</span>
                <span class="px-2 py-0.5 rounded-full bg-white text-primary-dark font-black text-[11px] border border-primary-soft/60 shrink-0">
                    <span id="{{ $countId }}">0</span> خدمة
                </span>
            </div>
        </div>
    </div>
</div>
