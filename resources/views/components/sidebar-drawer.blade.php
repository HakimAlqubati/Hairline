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
       class="fixed top-0 left-0 h-full w-72 sm:w-80 max-w-[84vw] bg-white z-50 shadow-2xl -translate-x-full sidebar-drawer flex flex-col justify-between rounded-r-[2rem] border-r border-gold-soft/40 overflow-hidden select-none">
    
    <!-- Drawer Top Header & Navigation Body -->
    <div class="flex-1 flex flex-col min-h-0 overflow-y-auto no-scrollbar">
        <!-- Drawer Top Header with Official Logo -->
        <div class="bg-gradient-to-b from-[#F2ECE3] to-sage-light p-6 border-b border-gold-soft/50 relative overflow-hidden shrink-0">
            <!-- Decorative subtle glow in header -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gold/10 rounded-full blur-2xl pointer-events-none"></div>

            <button id="sidebarCloseBtn"
                    onclick="toggleSidebar(false)" 
                    aria-label="إغلاق القائمة"
                    class="drawer-close-btn w-9 h-9 rounded-full bg-white text-sage-dark shadow-xs border border-gold-soft/50 flex items-center justify-center hover:bg-sage-dark hover:text-white hover:border-transparent active:scale-90 transition-all cursor-pointer mb-4">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <div class="drawer-brand flex items-center gap-3 relative">
                <div class="w-12 h-12 rounded-full bg-white border-2 border-gold shadow-xs flex items-center justify-center relative overflow-hidden shrink-0 p-1">
                    <img src="{{ asset('images/logo.png') }}"
                         alt="{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }}"
                         class="w-full h-full object-contain">
                </div>
                <div>
                    <h3 class="font-extrabold text-sage-dark text-base tracking-tight">{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} | {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}</h3>
                    <p class="text-[10px] tracking-[0.2em] text-gold-dark font-semibold uppercase">{{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation Items -->
        <div class="p-4 space-y-2.5 mt-2 flex-1">
            <p class="drawer-section-label px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">القائمة التعريفية</p>

            <!-- Item 1: الأخصائيات -->
            <button onclick="openSidebarSection('specialists')" 
                    class="drawer-item drawer-item-1 w-full p-3.5 rounded-2xl bg-surface hover:bg-white border border-[#EFEAE2] hover:border-gold/50 shadow-[0_2px_8px_rgba(33,28,24,0.03)] hover:shadow-md flex items-center justify-between transition-all group text-right cursor-pointer active:scale-[0.98]">
                <div>
                    <span class="block text-sm font-extrabold text-sage-dark group-hover:text-primary transition-colors">الأخصائيات</span>
                    <span class="text-[11px] text-gray-500">نخبة خبيرات الشعر والجمال</span>
                </div>
                <span class="w-7 h-7 rounded-full bg-white group-hover:bg-gold/10 border border-gold-soft/40 flex items-center justify-center text-gold-dark group-hover:text-primary text-base font-bold shadow-xs transition-all group-hover:-translate-x-1">‹</span>
            </button>

            <!-- Item 2: للتواصل والاستفسار -->
            <button onclick="openSidebarSection('contact')" 
                    class="drawer-item drawer-item-2 w-full p-3.5 rounded-2xl bg-surface hover:bg-white border border-[#EFEAE2] hover:border-gold/50 shadow-[0_2px_8px_rgba(33,28,24,0.03)] hover:shadow-md flex items-center justify-between transition-all group text-right cursor-pointer active:scale-[0.98]">
                <div>
                    <span class="block text-sm font-extrabold text-sage-dark group-hover:text-primary transition-colors">للتواصل والاستفسار</span>
                    <span class="text-[11px] text-gray-500">(اتصل بنا)</span>
                </div>
                <span class="w-7 h-7 rounded-full bg-white group-hover:bg-gold/10 border border-gold-soft/40 flex items-center justify-center text-gold-dark group-hover:text-primary text-base font-bold shadow-xs transition-all group-hover:-translate-x-1">‹</span>
            </button>

            <!-- Item 3: سياسة الخصوصية -->
            <button onclick="openSidebarSection('privacy')" 
                    class="drawer-item drawer-item-3 w-full p-3.5 rounded-2xl bg-surface hover:bg-white border border-[#EFEAE2] hover:border-gold/50 shadow-[0_2px_8px_rgba(33,28,24,0.03)] hover:shadow-md flex items-center justify-between transition-all group text-right cursor-pointer active:scale-[0.98]">
                <div>
                    <span class="block text-sm font-extrabold text-sage-dark group-hover:text-primary transition-colors">سياسة الخصوصية</span>
                    <span class="text-[11px] text-gray-500">(حماية وسرية بياناتكِ)</span>
                </div>
                <span class="w-7 h-7 rounded-full bg-white group-hover:bg-gold/10 border border-gold-soft/40 flex items-center justify-center text-gold-dark group-hover:text-primary text-base font-bold shadow-xs transition-all group-hover:-translate-x-1">‹</span>
            </button>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="drawer-footer p-5 bg-gradient-to-t from-gold-light/60 to-gold-light/30 border-t border-gold-soft/40 text-center shrink-0">
        <p class="text-xs font-bold text-sage-dark">هيرلاين لأن الجمال إحساس</p>
        <p class="text-[11px] text-gold-dark font-medium mt-0.5 tracking-wider">HAIRLINE BEAUTY SALON ©</p>
    </div>
</aside>
