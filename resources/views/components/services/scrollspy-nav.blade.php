@props([
    'sections' => [],
])

@if(count($sections) > 0)
    <div id="servicesScrollspyContainer" class="max-w-[840px] mx-auto px-3 sm:px-4 pt-1 pb-2.5">
        <div class="relative flex items-center gap-2">
            {{-- Horizontal Scrollable Category Pills --}}
            <div id="servicesScrollspyPills"
                 role="tablist"
                 aria-label="أقسام الخدمات"
                 class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth py-0.5 flex-1 min-w-0">
                @foreach($sections as $index => $sec)
                    <button type="button"
                            role="tab"
                            data-scrollspy-pill="{{ $sec['id'] }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-controls="{{ $sec['id'] }}"
                            onclick="scrollToServiceSection('{{ $sec['id'] }}')"
                            class="shrink-0 h-9 px-4 rounded-full text-xs sm:text-[13px] font-bold whitespace-nowrap transition-all duration-200 cursor-pointer select-none inline-flex items-center active:scale-95 {{ $index === 0 ? 'bg-primary text-white border border-primary shadow-xs' : 'bg-white text-sage-dark border border-[#ddd3bf] hover:border-primary hover:bg-primary-light/60' }}">
                        {{ $sec['title_ar'] }}
                    </button>
                @endforeach
            </div>

            {{-- Pinned All-Categories Menu Trigger Button --}}
            <button type="button"
                    id="servicesScrollspyMenuBtn"
                    onclick="toggleServicesScrollspyMenu()"
                    aria-haspopup="menu"
                    aria-expanded="false"
                    aria-controls="servicesScrollspyMenu"
                    aria-label="عرض جميع أقسام الخدمات"
                    title="جميع أقسام الخدمات"
                    class="w-9 h-9 rounded-full shrink-0 bg-white border border-[#ddd3bf] hover:border-primary hover:bg-primary-light/60 text-sage-dark flex items-center justify-center shadow-xs transition-all duration-200 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <circle cx="16.5" cy="5.5" r="1.25" fill="currentColor" />
                    <path d="M13 5.5H3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    <circle cx="16.5" cy="10" r="1.25" fill="currentColor" />
                    <path d="M13 10H3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    <circle cx="16.5" cy="14.5" r="1.25" fill="currentColor" />
                    <path d="M13 14.5H3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                </svg>
            </button>

            {{-- Floating Categories Index Dropdown Menu --}}
            <div id="servicesScrollspyMenu"
                 role="menu"
                 aria-label="قائمة أقسام الخدمات"
                 class="hidden opacity-0 scale-95 origin-top-left absolute top-full left-0 mt-2 w-72 max-w-[calc(100vw-1.5rem)] bg-white rounded-2xl border border-gold-soft/90 shadow-[0_20px_45px_-10px_rgba(33,28,24,0.22),0_0_0_1px_rgba(194,158,109,0.18)] z-50 overflow-hidden transition-all duration-200">
                <div class="px-4 py-2.5 bg-surface/90 border-b border-[#ece4d4] flex items-center justify-between">
                    <span class="text-xs font-extrabold text-sage-dark">أقسام الخدمات</span>
                    <span class="px-2 py-0.5 rounded-full bg-primary-light text-primary-dark text-[11px] font-extrabold border border-primary-soft/70">
                        {{ count($sections) }} أقسام
                    </span>
                </div>
                <div class="p-1.5 space-y-1 max-h-[58vh] overflow-y-auto hairline-custom-scroll">
                    @foreach($sections as $index => $sec)
                        <button type="button"
                                role="menuitem"
                                data-scrollspy-menu-item="{{ $sec['id'] }}"
                                onclick="selectServiceSectionFromMenu('{{ $sec['id'] }}')"
                                class="w-full text-right px-3 py-2.5 rounded-xl flex items-center justify-between gap-3 transition-colors cursor-pointer {{ $index === 0 ? 'bg-primary-light/80 text-sage-dark' : 'hover:bg-surface text-sage-dark' }}">
                            <div class="min-w-0">
                                <span class="block text-xs sm:text-[13px] font-extrabold truncate leading-snug">{{ $sec['title_ar'] }}</span>
                                @if(!empty($sec['title_en']))
                                    <span class="block text-[10px] font-en uppercase tracking-wider text-[#b39d76] truncate mt-0.5">{{ $sec['title_en'] }}</span>
                                @endif
                            </div>
                            <span data-scrollspy-check class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center shrink-0 text-[10px] {{ $index === 0 ? '' : 'hidden' }}">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
