@props([
    'salonIdentity' => [],
])

<div id="sidebarOverlay" 
     onclick="toggleSidebar(false)" 
     aria-hidden="true"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden opacity-0 drawer-backdrop">
</div>

<aside id="sidebarDrawer" 
       role="dialog"
       aria-modal="true"
       aria-label="القائمة الجانبية"
       class="fixed top-0 left-0 h-full w-72 sm:w-80 max-w-[84vw] bg-white z-50 shadow-2xl -translate-x-full sidebar-drawer flex flex-col justify-between rounded-r-[2rem] border-r border-primary/20 overflow-hidden select-none">
    
    <!-- Drawer Top Header & Navigation Body -->
    <div class="flex-1 flex flex-col min-h-0 overflow-y-auto no-scrollbar bg-white">
        <!-- Drawer Top Header with Official Logo (Matches Main Header) -->
        <div class="bg-primary p-6 border-b border-primary-dark/20 relative overflow-hidden shrink-0 text-white">
            <!-- Subtle decorative pattern overlay (exact match to header) -->
            <div class="absolute inset-0 opacity-25 pointer-events-none bg-[radial-gradient(rgba(255,255,255,0.45)_1.2px,transparent_1.2px)] [background-size:16px_16px]"></div>

            <button id="sidebarCloseBtn"
                    onclick="toggleSidebar(false)" 
                    aria-label="إغلاق القائمة"
                    class="drawer-close-btn w-9 h-9 rounded-full bg-white/20 hover:bg-white text-white hover:text-primary shadow-xs border border-white/30 flex items-center justify-center active:scale-90 transition-all cursor-pointer mb-5 backdrop-blur-xs relative z-10">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- Brand: Cutout White Vector Logo + English Typography (Matching Header 1:1) -->
            <div class="drawer-brand flex items-center gap-3 relative z-10 cursor-pointer" onclick="toggleSidebar(false); switchTab('home')">
                <div class="w-11 h-11 sm:w-12 sm:h-12 shrink-0 flex items-center justify-center text-white drop-shadow-xs transition-transform hover:scale-105">
                    <img src="{{ asset('images/logo-white.svg') }}"
                         alt="{{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}"
                         class="w-full h-full object-contain"
                         loading="eager">
                </div>
                <div class="flex flex-col min-w-0 justify-center">
                    <h3 class="font-bold text-white text-lg tracking-widest font-serif leading-tight drop-shadow-sm">{{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}</h3>
                    <p class="text-[9.5px] tracking-[0.26em] text-white/85 font-semibold uppercase mt-0.5 drop-shadow-xs">{{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation Items (White Background with Brand Gold Texts & Borders) -->
        <div class="p-4 space-y-3 mt-2 flex-1 bg-white">
            <p class="drawer-section-label px-3 text-[11px] font-bold text-primary/75 uppercase tracking-widest">القائمة التعريفية</p>

            <!-- Item 1: الأخصائيات -->
            <button onclick="openSidebarSection('specialists')" 
                    class="drawer-item drawer-item-1 w-full p-4 rounded-2xl bg-white hover:bg-primary/5 border border-primary/25 hover:border-primary shadow-[0_4px_16px_rgba(194,158,109,0.08)] flex items-center justify-between transition-all group text-right cursor-pointer active:scale-[0.98]">
                <div>
                    <span class="block text-sm sm:text-base font-black text-primary transition-colors">الأخصائيات</span>
                    <span class="text-[11px] font-medium text-primary/75">نخبة خبيرات الشعر والجمال</span>
                </div>
                <span class="w-8 h-8 rounded-full bg-primary/10 group-hover:bg-primary border border-primary/25 flex items-center justify-center text-primary group-hover:text-white text-base font-bold shadow-xs transition-all group-hover:-translate-x-1">‹</span>
            </button>

            <!-- Item 2: للتواصل والاستفسار -->
            <button onclick="openSidebarSection('contact')" 
                    class="drawer-item drawer-item-2 w-full p-4 rounded-2xl bg-white hover:bg-primary/5 border border-primary/25 hover:border-primary shadow-[0_4px_16px_rgba(194,158,109,0.08)] flex items-center justify-between transition-all group text-right cursor-pointer active:scale-[0.98]">
                <div>
                    <span class="block text-sm sm:text-base font-black text-primary transition-colors">للتواصل والاستفسار</span>
                    <span class="text-[11px] font-medium text-primary/75">(اتصل بنا)</span>
                </div>
                <span class="w-8 h-8 rounded-full bg-primary/10 group-hover:bg-primary border border-primary/25 flex items-center justify-center text-primary group-hover:text-white text-base font-bold shadow-xs transition-all group-hover:-translate-x-1">‹</span>
            </button>

            <!-- Item 3: سياسة الخصوصية -->
            <button onclick="openSidebarSection('privacy')" 
                    class="drawer-item drawer-item-3 w-full p-4 rounded-2xl bg-white hover:bg-primary/5 border border-primary/25 hover:border-primary shadow-[0_4px_16px_rgba(194,158,109,0.08)] flex items-center justify-between transition-all group text-right cursor-pointer active:scale-[0.98]">
                <div>
                    <span class="block text-sm sm:text-base font-black text-primary transition-colors">سياسة الخصوصية</span>
                    <span class="text-[11px] font-medium text-primary/75">(حماية وسرية بياناتكِ)</span>
                </div>
                <span class="w-8 h-8 rounded-full bg-primary/10 group-hover:bg-primary border border-primary/25 flex items-center justify-center text-primary group-hover:text-white text-base font-bold shadow-xs transition-all group-hover:-translate-x-1">‹</span>
            </button>
        </div>
    </div>

    <!-- Drawer Footer (Clean White Background & Gold Signature Text) -->
    <div class="drawer-footer p-5 bg-white border-t border-primary/15 text-center shrink-0">
        <p class="text-xs font-bold text-primary">هيرلاين لأن الجمال إحساس</p>
        <p class="text-[10px] text-primary/75 font-semibold mt-1 tracking-widest uppercase">HAIRLINE BEAUTY SALON ©</p>
    </div>
</aside>
