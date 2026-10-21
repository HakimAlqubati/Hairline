@props([
    'menuPages' => app(\App\Services\FrontData\CatalogMenuDataService::class)->getMenuPages(),
])

@php
    $p1 = $menuPages[0] ?? [];
    $p2 = $menuPages[1] ?? [];
    $p3 = $menuPages[2] ?? [];
    $p4 = $menuPages[3] ?? [];
    $p5 = $menuPages[4] ?? [];
    $p6 = $menuPages[5] ?? [];
    $p7 = $menuPages[6] ?? [];
    $p8 = $menuPages[7] ?? [];
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

<section id="servicesTabView" class="hidden flex-1 w-full animate-step selection:bg-[#b7a077]/30 selection:text-[#4a4336]">

    <!-- Minimal Search Bar Sticky at Top -->
    <x-services.search-header />

    <!-- Main Pages Container -->
    <main id="pagesContainer" class="py-4 px-1.5 sm:px-3 max-w-7xl mx-auto">

        <!-- ==========================================
             PAGE 1: خدمات الشعر | HAIR SERVICES
             ========================================== -->
        <article class="menu-page" data-page="1">
            <x-services.wave-bg />

            <div class="page-content">
                <div class="section-main-title">
                    <h2 class="ar-title">{{ $p1['title_ar'] ?? 'خدمات الشعر' }}</h2>
                    <p class="en-title">{{ $p1['title_en'] ?? 'HAIR SERVICES' }}</p>
                </div>

                @foreach($p1['groups'] ?? [] as $group)
                    <x-services.category-group :group="$group" />
                @endforeach
            </div>

            <x-services.page-footer />
        </article>

        <!-- ==========================================
             PAGE 2: معالجات الشعر | HAIR TREATMENTS
             ========================================== -->
        <article class="menu-page" data-page="2">
            <x-services.wave-bg />

            <div class="page-content">
                <div class="section-main-title">
                    <h2 class="ar-title">{{ $p2['title_ar'] ?? 'معالجات الشعر' }}</h2>
                    <p class="en-title">{{ $p2['title_en'] ?? 'HAIR TREATMENTS' }}</p>
                </div>

                @foreach($p2['subsections'] ?? [] as $sub)
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
                @endforeach
            </div>

            <x-services.page-footer />
        </article>

        <!-- ==========================================
             PAGE 3: جلسات العناية بالشعر | HAIR CARE SESSIONS
             ========================================== -->
        <article class="menu-page" data-page="3">
            <x-services.wave-bg />

            <div class="page-content">
                <div class="section-main-title">
                    <h2 class="ar-title">{{ $p3['title_ar'] ?? 'جلسات العناية بالشعر' }}</h2>
                    <p class="en-title">{{ $p3['title_en'] ?? 'HAIR CARE SESSIONS' }}</p>
                </div>

                <!-- Top Single-Price Sessions -->
                <div class="mb-4">
                    @foreach($p3['top_items'] ?? [] as $item)
                        <x-services.service-row :item="$item" />
                    @endforeach
                </div>

                <!-- Multi-Length Sessions -->
                @foreach($p3['multi_length_blocks'] ?? [] as $block)
                    <x-services.multi-length-block :block="$block" />
                @endforeach

                <!-- Bottom Single-Price Item -->
                <div class="mt-2">
                    @foreach($p3['bottom_items'] ?? [] as $item)
                        <x-services.service-row :item="$item" />
                    @endforeach
                </div>
            </div>

            <x-services.page-footer />
        </article>

        <!-- ==========================================
             PAGE 4: الاستشوار | BLOW DRY STYLES
             ========================================== -->
        <article class="menu-page" data-page="4">
            <x-services.wave-bg />

            <div class="page-content">
                <div class="section-main-title {{ $p4['title_class'] ?? 'mb-10' }}">
                    <h2 class="ar-title">{{ $p4['title_ar'] ?? 'الاستشوار' }}</h2>
                    <p class="en-title">{{ $p4['title_en'] ?? 'BLOW DRY STYLES' }}</p>
                </div>

                <div class="space-y-12 my-auto">
                    @foreach($p4['blocks'] ?? [] as $block)
                        <x-services.multi-length-block :block="$block" />
                    @endforeach
                </div>
            </div>

            <x-services.page-footer />
        </article>

        <!-- ==========================================
             PAGE 5: المناكير و البادكير | MANICURES AND PEDICURES
             ========================================== -->
        <article class="menu-page" data-page="5">
            <x-services.wave-bg />

            <div class="page-content">
                <div class="section-main-title">
                    <h2 class="ar-title">{{ $p5['title_ar'] ?? 'المناكير و البادكير' }}</h2>
                    <p class="en-title">{{ $p5['title_en'] ?? 'MANICURES AND PEDICURES' }}</p>
                </div>

                <div class="space-y-1">
                    @foreach($p5['items'] ?? [] as $item)
                        <x-services.service-row :item="$item" />
                    @endforeach
                </div>
            </div>

            <x-services.page-footer />
        </article>

        <!-- ==========================================
             PAGE 6: خدمات البشرة + الرموش و الحواجب
             ========================================== -->
        <article class="menu-page" data-page="6">
            <x-services.wave-bg />

            <div class="page-content">
                @foreach($p6['sections'] ?? [] as $sec)
                    <div class="section-main-title {{ $sec['title_classes'] ?? '' }}">
                        <h2 class="ar-title">{{ $sec['title_ar'] ?? '' }}</h2>
                        <p class="en-title">{{ $sec['title_en'] ?? '' }}</p>
                    </div>

                    <div class="{{ $sec['container_class'] ?? '' }}">
                        @foreach($sec['items'] ?? [] as $item)
                            <x-services.service-row :item="$item" />
                        @endforeach
                    </div>
                @endforeach
            </div>

            <x-services.page-footer />
        </article>

        <!-- ==========================================
             PAGE 7: خدمات المساج + الحلاوة و الواكس
             ========================================== -->
        <article class="menu-page" data-page="7">
            <x-services.wave-bg />

            <div class="page-content">
                @foreach($p7['sections'] ?? [] as $sec)
                    <div class="section-main-title {{ $sec['title_classes'] ?? '' }}">
                        <h2 class="ar-title">{{ $sec['title_ar'] ?? '' }}</h2>
                        <p class="en-title">{{ $sec['title_en'] ?? '' }}</p>
                    </div>

                    <div class="{{ $sec['container_class'] ?? '' }}">
                        @foreach($sec['items'] ?? [] as $item)
                            <x-services.service-row :item="$item" />
                        @endforeach
                    </div>
                @endforeach
            </div>

            <x-services.page-footer />
        </article>

        <!-- ==========================================
             PAGE 8: أطوال الشعر | HAIR LENGTHS
             ========================================== -->
        <article class="menu-page" data-page="8">
            <x-services.wave-bg />

            <div class="page-content">
                <div class="section-main-title !mt-6 !mb-4">
                    <h2 class="ar-title tracking-wide">{{ $p8['title_ar'] ?? 'أطــــوال الشــــعر' }}</h2>
                    <p class="en-title">{{ $p8['title_en'] ?? 'HAIR LENGTHS' }}</p>
                </div>

                <x-services.hair-lengths-diagram />
            </div>

            <x-services.page-footer />
        </article>

    </main>

</section>
