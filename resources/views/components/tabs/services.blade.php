@props([
    'menuPages' => app(\App\Services\FrontData\CatalogMenuDataService::class)->getMenuPages(),
    'scrollspySections' => null,
    'isActive' => false,
])

@php
    $catalogMenuService = app(\App\Services\FrontData\CatalogMenuDataService::class);
    $scrollspySections = $scrollspySections ?? $catalogMenuService->getScrollspySections($menuPages);
@endphp

<!-- Reusable SVG Definition for HAIRLINE Emblem -->
<svg class="hidden" aria-hidden="true">
    <defs>
        <g id="hairline-emblem">
            <circle cx="100" cy="100" r="78" fill="none" stroke="#b39d76" stroke-width="1.8" />
            <path d="M 78 25 C 42 45, 25 90, 38 145" fill="none" stroke="#b39d76" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M 88 23 C 50 48, 34 95, 48 156" fill="none" stroke="#b39d76" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M 96 26 C 60 52, 46 102, 62 167" fill="none" stroke="#b39d76" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M 62 66 C 72 67, 83 70, 90 73" fill="none" stroke="#b39d76" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M 63 67 C 66 95, 79 119, 95 125 C 104 128, 111 122, 116 112" fill="none" stroke="#b39d76" stroke-width="1.7" stroke-linecap="round"/>
            <path d="M 87 122 C 91 140, 91 158, 88 175" fill="none" stroke="#b39d76" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M 74 36 C 110 36, 148 64, 152 115 C 154 138, 142 160, 130 171" fill="none" stroke="#b39d76" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M 68 45 C 106 47, 140 75, 141 122 C 142 144, 129 165, 116 176" fill="none" stroke="#b39d76" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M 65 55 C 102 58, 130 85, 128 128 C 127 148, 115 167, 104 177" fill="none" stroke="#b39d76" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M 85 28 C 126 32, 162 64, 165 112 C 167 134, 158 152, 147 162" fill="none" stroke="#b39d76" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M 98 22 C 140 28, 173 62, 175 106" fill="none" stroke="#b39d76" stroke-width="1.4" stroke-linecap="round"/>
        </g>
    </defs>
</svg>

<section id="servicesTabView" class="{{ $isActive ? '' : 'hidden' }} flex-1 w-full animate-step selection:bg-[#b7a077]/30 selection:text-[#4a4336]">

    <!-- Unified Sticky Search & Scrollspy Category Navigation -->
    <div id="servicesStickyBar"
         class="no-print services-sticky-bar z-[25] bg-[#fdfbf7]/95 backdrop-blur-md border-b border-[#e5dec9] shadow-[0_6px_20px_-8px_rgba(33,28,24,0.08)]">
        <div class="max-w-xl mx-auto px-3 sm:px-4 pt-2.5 pb-1.5">
            <x-services.search-header :sticky="false" />
        </div>
        <x-services.scrollspy-nav :sections="$scrollspySections" />
    </div>

    <!-- Dedicated Minimalist Search Results List (Clean vertical list replacing complex brochure pages during search) -->
    <div id="servicesSearchResultsContainer" class="hidden max-w-3xl mx-auto my-6 px-3 sm:px-4 space-y-2.5">
        <div id="servicesSearchResultsList" class="space-y-2"></div>
    </div>

    <!-- Main Pages Container -->
    <main id="pagesContainer" class="py-4 px-1.5 sm:px-3 max-w-7xl mx-auto">

        <!-- Empty State When No Services Match Search -->
        <div id="servicesSearchEmptyState" class="hidden max-w-lg mx-auto my-8 bg-white rounded-3xl p-8 text-center border border-[#d5c7ad] shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-full bg-primary-light/70 text-primary-dark mx-auto flex items-center justify-center">
                <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </div>
            <div class="space-y-1">
                <h4 class="font-extrabold text-sm sm:text-base text-sage-dark">لا توجد خدمات مطابقة لبحثكِ</h4>
                <p class="text-xs text-gray-500">
                    لم نتمكن من العثور على خدمات تطابق «<span id="servicesEmptyQueryText" class="font-bold text-primary-dark"></span>».
                </p>
            </div>
            <button type="button"
                    onclick="clearUnifiedServiceSearch('services')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-primary hover:bg-primary-dark text-white text-xs font-extrabold transition cursor-pointer">
                <i class="fa-solid fa-rotate-right text-[11px]"></i>
                <span>عرض جميع الخدمات</span>
            </button>
        </div>

        @foreach($menuPages as $page)
            @php
                $pageNumber = (int) ($page['page_number'] ?? 0);
                $hasSections = !empty($page['sections']) && is_array($page['sections']);
            @endphp

            <article
                @if(!$hasSections && $pageNumber > 0)
                    id="service-section-{{ $pageNumber }}"
                    data-scrollspy-section="service-section-{{ $pageNumber }}"
                @endif
                class="menu-page"
                data-page="{{ $pageNumber }}"
            >
                <x-services.wave-bg />

                <div class="page-content">
                    @if($hasSections)
                        @foreach(array_values($page['sections']) as $idx => $sec)
                            <div id="service-section-{{ $pageNumber }}-{{ $idx }}"
                                 data-scrollspy-section="service-section-{{ $pageNumber }}-{{ $idx }}"
                                 class="service-section-block">
                                <div class="section-main-title {{ $sec['title_classes'] ?? '' }}">
                                    <h2 class="ar-title">{{ $sec['title_ar'] ?? '' }}</h2>
                                    <p class="en-title">{{ $sec['title_en'] ?? '' }}</p>
                                </div>

                                <div class="{{ $sec['container_class'] ?? '' }}">
                                    @foreach($sec['items'] ?? [] as $item)
                                        <x-services.service-row :item="$item" />
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @elseif($pageNumber === 8 || isset($page['lengths']))
                        <div class="section-main-title !mt-6 !mb-4">
                            <h2 class="ar-title tracking-wide">{{ $page['title_ar'] ?? 'أطــــوال الشــــعر' }}</h2>
                            <p class="en-title">{{ $page['title_en'] ?? 'HAIR LENGTHS' }}</p>
                        </div>

                        <x-services.hair-lengths-diagram />
                    @else
                        <div class="section-main-title {{ $page['title_class'] ?? (!empty($page['blocks']) ? 'mb-10' : '') }}">
                            <h2 class="ar-title">{{ $page['title_ar'] ?? '' }}</h2>
                            <p class="en-title">{{ $page['title_en'] ?? '' }}</p>
                        </div>

                        @if(!empty($page['groups']))
                            @foreach($page['groups'] as $group)
                                <x-services.category-group :group="$group" />
                            @endforeach
                        @endif

                        @if(!empty($page['subsections']))
                            @foreach($page['subsections'] as $sub)
                                <div class="service-subsection-group">
                                    <div class="subsection-title">
                                        <span class="ar">{{ $sub['title_ar'] ?? '' }}</span>
                                        <span class="en">{{ $sub['title_en'] ?? '' }}</span>
                                    </div>

                                    @if(($sub['type'] ?? '') === 'simple_rows')
                                        <div class="{{ $sub['container_class'] ?? 'mb-4' }}">
                                            @foreach($sub['items'] ?? [] as $item)
                                                <x-services.service-row :item="$item" />
                                            @endforeach
                                        </div>
                                    @elseif(($sub['type'] ?? '') === 'multi_length')
                                        @foreach($sub['blocks'] ?? [] as $block)
                                            <x-services.multi-length-block :block="$block" />
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        @if(!empty($page['top_items']) || !empty($page['multi_length_blocks']) || !empty($page['bottom_items']))
                            @if(!empty($page['top_items']))
                                <div class="mb-4">
                                    @foreach($page['top_items'] as $item)
                                        <x-services.service-row :item="$item" />
                                    @endforeach
                                </div>
                            @endif

                            @foreach($page['multi_length_blocks'] ?? [] as $block)
                                <x-services.multi-length-block :block="$block" />
                            @endforeach

                            @if(!empty($page['bottom_items']))
                                <div class="mt-2">
                                    @foreach($page['bottom_items'] as $item)
                                        <x-services.service-row :item="$item" />
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        @if(!empty($page['blocks']))
                            <div class="space-y-12 my-auto">
                                @foreach($page['blocks'] as $block)
                                    <x-services.multi-length-block :block="$block" />
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($page['items']))
                            <div class="space-y-1">
                                @foreach($page['items'] as $item)
                                    <x-services.service-row :item="$item" />
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                <x-services.page-footer />
            </article>
        @endforeach

    </main>

</section>
