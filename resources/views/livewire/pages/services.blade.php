<div class="flex flex-col flex-1 animate-view">

    {{-- Header --}}
    @include('livewire.partials.page-header', ['title' => 'أقسام الخدمات', 'backRoute' => 'home'])

    {{-- Search Bar --}}
    <div class="bg-white px-5 py-3 border-b border-[#EAEAEA]">
        <div class="relative">
            <input wire:model.live.debounce.350ms="searchQuery"
                   type="search"
                   placeholder="ابحثي عن خدمة..."
                   class="w-full bg-[#FAF8F5] border border-[#E2DDD5] rounded-2xl px-4 py-3 pr-10 text-base focus:outline-none focus:border-[#4A5D54] focus:bg-white transition-all">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-[#8C7048]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    {{-- Section Info Bar --}}
    <div class="bg-white px-6 py-3 border-b border-[#EAEAEA] flex items-center justify-between">
        <p class="text-sm text-[#5C6B64]">
            @if($searchQuery)
                نتائج البحث عن: <span class="font-bold text-[#232B28]">"{{ $searchQuery }}"</span>
            @else
                اختاري القسم لعرض الخدمات والحجز الفوري
            @endif
        </p>
        <span class="text-xs font-semibold bg-[#F5EFE6] text-[#8C7048] px-3 py-1.5 rounded-full border border-[#C5A373]/30">
            الأسعار بـ SAR
        </span>
    </div>

    {{-- ===== ACCORDION TREE ===== --}}
    <div class="flex-1 overflow-y-auto divide-y divide-[#EAEAEA]">

        @forelse($filteredCategories as $index => $category)
            @php $isOpen = ($openCategoryId === $category['id']); @endphp

            <div>
                {{-- Level 1: Category Header --}}
                <button wire:click="toggleCategory('{{ $category['id'] }}')"
                        class="category-accordion-btn {{ $isOpen ? 'is-open' : '' }} px-6 py-5 flex items-center justify-between w-full text-right focus:outline-none">
                    <div class="flex items-center gap-4">
                        <span class="w-8 h-8 rounded-full {{ $isOpen ? 'bg-[#4A5D54] text-white' : 'bg-[#F5EFE6] text-[#8C7048]' }} font-bold text-sm flex items-center justify-center transition-colors">
                            {{ $index + 1 }}
                        </span>
                        <div>
                            <h3 class="text-xl font-bold text-[#232B28]">{{ $category['title'] }}</h3>
                            <p class="text-sm text-[#5C6B64] mt-0.5">{{ $category['subtitle'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $isOpen ? 'bg-white text-[#4A5D54]' : 'bg-[#FAF8F5] text-[#5C6B64]' }}">
                            {{ $category['badge_count'] }}
                        </span>
                        <div class="w-9 h-9 rounded-full bg-white border border-[#EAEAEA] flex items-center justify-center text-[#4A5D54] transition-transform duration-300 {{ $isOpen ? 'rotate-180' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
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
                                    <div class="space-y-1">
                                        <h4 class="text-lg font-bold text-[#232B28]">{{ $service['name'] }}</h4>
                                        <p class="text-sm text-[#5C6B64]">{{ $service['description'] }}</p>
                                        <div class="flex items-center gap-3 text-sm text-[#5C6B64]">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4 text-[#C5A373]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $service['duration'] }}
                                            </span>
                                            <span>•</span>
                                            <span class="font-bold text-base text-[#4A5D54]">
                                                {{ $service['price'] }}
                                                <span class="font-en-brand text-xs">SAR</span>
                                            </span>
                                            @if($service['is_popular'])
                                                <span class="bg-[#F5EFE6] text-[#8C7048] text-xs px-2 py-0.5 rounded-full font-medium">⭐ الأكثر طلباً</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                        {{-- Toggle Bag --}}
                                        <button wire:click="toggleService('{{ $service['id'] }}')"
                                                class="px-3.5 py-2.5 rounded-xl text-sm font-bold border transition-all {{ $isSelected ? 'bg-[#E2EFEA] text-[#4A5D54] border-[#4A5D54]' : 'bg-[#FAF8F5] text-[#5C6B64] border-[#EAEAEA] hover:border-[#C5A373]' }}">
                                            {{ $isSelected ? '✓ مضافة' : '+ أضيفي' }}
                                        </button>
                                        {{-- Direct Book --}}
                                        <button wire:click="directBook('{{ $category['id'] }}', '{{ $service['id'] }}')"
                                                class="bg-[#4A5D54] hover:bg-[#36453E] text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-xs transition-all">
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
                <p class="text-sm text-[#5C6B64]">جربي كلمة بحث أخرى</p>
            </div>
        @endforelse
    </div>

    {{-- ===== STICKY BOTTOM BAR ===== --}}
    <div class="bg-white border-t border-[#EAEAEA] px-5 py-4 flex items-center justify-between gap-4">
        <div>
            <p class="text-sm text-[#5C6B64]">الخدمات المختارة</p>
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
