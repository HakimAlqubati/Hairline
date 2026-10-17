<div class="flex flex-col flex-1 animate-view">

    {{-- Header --}}
    @include('livewire.partials.page-header', ['title' => 'قائمة الخدمات المعتمدة', 'backRoute' => 'home'])

    {{-- Search Bar --}}
    <div class="bg-white px-5 py-3 border-b border-[#EAEAEA]">
        <div class="relative">
            <input wire:model.live.debounce.350ms="searchQuery"
                   type="search"
                   placeholder="ابحثي عن خدمة (قص، استشوار، بروتين، تنظيف بشرة، سبا...)"
                   class="w-full bg-[#FAF8F5] border border-[#E2DDD5] rounded-2xl px-4 py-3 pr-10 text-base focus:outline-none focus:border-[#4A5D54] focus:bg-white transition-all">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-[#8C7048]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    {{-- Section Info Bar with Hair Lengths Guide toggle --}}
    <div class="bg-white px-5 py-2.5 border-b border-[#EAEAEA] flex items-center justify-between gap-2">
        <p class="text-xs sm:text-sm text-[#5C6B64] truncate">
            @if($searchQuery)
                نتائج البحث عن: <span class="font-bold text-[#232B28]">"{{ $searchQuery }}"</span>
            @else
                الأسعار شاملة ضريبة القيمة المضافة 15%
            @endif
        </p>
        <button wire:click="toggleLengthsGuide"
                class="shrink-0 text-xs font-bold bg-[#F5EFE6] text-[#8C7048] hover:bg-[#E2EFEA] hover:text-[#4A5D54] px-3 py-1.5 rounded-full border border-[#C5A373]/30 transition-colors flex items-center gap-1">
            <span>📏 دليل أطوال الشعر</span>
        </button>
    </div>

    {{-- Hair Lengths Guide (Page 8 Collapsible Drawer) --}}
    @if($showLengthsGuide)
        <div class="bg-[#FAF8F5] border-b border-[#C5A373]/30 p-4 animate-fade-in">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-bold text-sm text-[#232B28] flex items-center gap-2">
                    <span>دليل أطوال الشعر المعتمد (HAIR LENGTHS)</span>
                </h4>
                <button wire:click="toggleLengthsGuide" class="text-xs text-[#8C7048] hover:underline font-bold">
                    إغلاق ✕
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                @foreach($lengthsGuide as $tier)
                    <div class="bg-white p-2.5 rounded-xl border border-[#EAEAEA] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold px-2 py-0.5 rounded bg-[#4A5D54] text-white text-[10px]">{{ $tier['code'] }}</span>
                            <span class="font-bold text-[#232B28]">{{ $tier['name_ar'] }}</span>
                        </div>
                        <span class="text-[#5C6B64] text-[11px]">{{ $tier['description'] }}</span>
                    </div>
                @endforeach
            </div>
            <p class="text-[10px] text-[#8C7048] mt-2 text-center">تعتمد أسعار الاستشوار، الرينساج، المعالجات، والتساريح على طول الشعر الموضح أعلاه</p>
        </div>
    @endif

    {{-- ===== ACCORDION TREE ===== --}}
    <div class="flex-1 overflow-y-auto divide-y divide-[#EAEAEA]">

        @forelse($filteredCategories as $index => $category)
            @php $isOpen = ($openCategoryId === $category['id']); @endphp

            <div>
                {{-- Level 1: Category Header --}}
                <button wire:click="toggleCategory('{{ $category['id'] }}')"
                        class="category-accordion-btn {{ $isOpen ? 'is-open' : '' }} px-5 py-4 flex items-center justify-between w-full text-right focus:outline-none">
                    <div class="flex items-center gap-3.5">
                        <span class="w-8 h-8 rounded-full {{ $isOpen ? 'bg-[#4A5D54] text-white' : 'bg-[#F5EFE6] text-[#8C7048]' }} font-bold text-sm flex items-center justify-center transition-colors">
                            {{ $index + 1 }}
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg sm:text-xl font-bold text-[#232B28]">{{ $category['title'] }}</h3>
                                @if(isset($category['title_en']))
                                    <span class="font-en-sub text-[9px] text-[#8C7048] font-semibold hidden sm:inline">
                                        {{ $category['title_en'] }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs sm:text-sm text-[#5C6B64] mt-0.5">{{ $category['subtitle'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $isOpen ? 'bg-white text-[#4A5D54]' : 'bg-[#FAF8F5] text-[#5C6B64]' }} border border-[#EAEAEA]">
                            {{ $category['badge_count'] }}
                        </span>
                        <div class="w-8 h-8 rounded-full bg-white border border-[#EAEAEA] flex items-center justify-center text-[#4A5D54] transition-transform duration-300 {{ $isOpen ? 'rotate-180' : '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </button>

                {{-- Level 2: Sub-services --}}
                <div class="sub-services-container {{ $isOpen ? 'is-open' : '' }}">
                    <div class="p-4 space-y-3">
                        @foreach($category['services'] as $service)
                            @php $isSelected = in_array($service['id'], $selectedServiceIds); @endphp
                            <div class="bg-white rounded-2xl p-4 shadow-xs border {{ $isSelected ? 'border-[#4A5D54] ring-1 ring-[#4A5D54]' : 'border-[#EAEAEA]' }} transition-all">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                                    <div class="space-y-1.5 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-base sm:text-lg font-bold text-[#232B28]">{{ $service['name'] }}</h4>
                                            @if(!empty($service['group']))
                                                <span class="text-[10px] font-semibold bg-[#FAF8F5] text-[#8C7048] px-2 py-0.5 rounded-md border border-[#EAEAEA]">
                                                    {{ $service['group'] }}
                                                </span>
                                            @endif
                                            @if(!empty($service['length_label']))
                                                <span class="text-[10px] font-bold bg-[#E2EFEA] text-[#4A5D54] px-2 py-0.5 rounded-md border border-[#4A5D54]/20">
                                                    الطول: {{ $service['length_label'] }}
                                                </span>
                                            @endif
                                        </div>
                                        @if(!empty($service['name_en']))
                                            <p class="font-en-sub text-[10px] text-[#8C7048] tracking-wider">{{ $service['name_en'] }}</p>
                                        @endif
                                        <p class="text-xs sm:text-sm text-[#5C6B64]">{{ $service['description'] }}</p>

                                        <div class="flex items-center gap-3 text-xs sm:text-sm text-[#5C6B64] pt-1">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-[#C5A373]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $service['duration'] }}
                                            </span>
                                            <span>•</span>
                                            <span class="font-bold text-base text-[#4A5D54]">
                                                @if(!empty($service['price_display']))
                                                    {{ $service['price_display'] }}
                                                @else
                                                    {{ $service['price'] }} <span class="font-en-brand text-xs">SAR</span>
                                                @endif
                                            </span>
                                            @if($service['is_popular'])
                                                <span class="bg-[#F5EFE6] text-[#8C7048] text-[11px] px-2 py-0.5 rounded-full font-medium">⭐ الأكثر طلباً</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                        {{-- Toggle Bag --}}
                                        <button wire:click="toggleService('{{ $service['id'] }}')"
                                                class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold border transition-all {{ $isSelected ? 'bg-[#E2EFEA] text-[#4A5D54] border-[#4A5D54]' : 'bg-[#FAF8F5] text-[#5C6B64] border-[#EAEAEA] hover:border-[#C5A373]' }}">
                                            {{ $isSelected ? '✓ مضافة' : '+ أضيفي' }}
                                        </button>
                                        {{-- Direct Book --}}
                                        <button wire:click="directBook('{{ $category['id'] }}', '{{ $service['id'] }}')"
                                                class="bg-[#4A5D54] hover:bg-[#36453E] text-white font-bold text-xs sm:text-sm px-4 sm:px-5 py-2 rounded-xl shadow-xs transition-all">
                                            احجزي الآن
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="p-10 text-center">
                <p class="text-lg font-bold text-[#232B28] mb-1">لا توجد نتائج</p>
                <p class="text-sm text-[#5C6B64]">جربي كلمة بحث أخرى مثل (قص، ويفي، فيلر، بدكير)</p>
            </div>
        @endforelse
    </div>

    {{-- ===== STICKY BOTTOM BAR ===== --}}
    <div class="bg-white border-t border-[#EAEAEA] px-5 py-4 flex items-center justify-between gap-4">
        <div>
            <p class="text-xs text-[#5C6B64]">الخدمات المختارة (شاملة الضريبة)</p>
            <p class="text-base font-bold text-[#4A5D54]">
                @if(count($selectedServiceIds) === 0)
                    لم يتم اختيار خدمة بعد
                @else
                    {{ $totals['count'] }} خدمة •
                    <span class="text-[#232B28]">{{ $totals['total_price'] }} SAR</span>
                @endif
            </p>
        </div>
        @if(count($selectedServiceIds) > 0)
            <button wire:click="proceedToBooking"
                    class="bg-[#4A5D54] hover:bg-[#36453E] text-white font-bold text-base px-6 py-3.5 rounded-2xl shadow-md transition-all flex items-center gap-2">
                <span>إتمام الحجز</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        @endif
    </div>

    @include('livewire.partials.bottom-nav', ['active' => 'services'])
</div>
