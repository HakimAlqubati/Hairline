@props([
    'salonIdentity' => [],
])

<div id="sidebarOverlay" 
     onclick="toggleSidebar(false)" 
     class="fixed inset-0 bg-black/50 backdrop-blur-xs z-40 hidden opacity-0 drawer-backdrop">
</div>

<aside id="sidebarDrawer" 
       aria-label="القائمة الجانبية"
       class="fixed top-0 left-0 h-full w-72 max-w-[82vw] bg-white z-50 shadow-2xl -translate-x-full sidebar-drawer flex flex-col justify-between rounded-r-3xl overflow-hidden">
    
    <!-- Drawer Top Header with Official Logo -->
    <div>
        <div class="bg-sage-light p-6 border-b border-gold-soft/50 relative">
            <button onclick="toggleSidebar(false)" 
                    aria-label="إغلاق القائمة"
                    class="w-9 h-9 rounded-full bg-white text-sage-dark shadow-sm flex items-center justify-center hover:bg-sage-dark hover:text-white transition mb-4">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-white border-2 border-gold shadow-xs flex items-center justify-center relative overflow-hidden shrink-0 p-1">
                    <img src="{{ asset('images/logo.png') }}"
                         alt="{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }}"
                         class="w-full h-full object-contain">
                </div>
                <div>
                    <h3 class="font-extrabold text-sage-dark text-base">{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} | {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}</h3>
                    <p class="text-[10px] tracking-[0.2em] text-gold-dark font-semibold uppercase">{{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}</p>
                </div>
            </div>
        </div>

        <!-- Strictly the 3 Required Navigation Items -->
        <div class="p-4 space-y-2.5 mt-2">
            <p class="px-3 text-[11px] font-bold text-gray-400 uppercase">القائمة التعريفية</p>

            <!-- Item 1: التعريف بالصالون (من نحن) -->
            <button onclick="openSidebarSection('about')" 
                    class="w-full p-3.5 rounded-2xl bg-surface hover:bg-sage-light/60 border border-transparent hover:border-gold/40 flex items-center justify-between transition group text-right">
                <div>
                    <span class="block text-sm font-extrabold text-sage-dark">التعريف بالصالون</span>
                    <span class="text-[11px] text-gray-500">(من نحن)</span>
                </div>
                <span class="text-lg text-gold-dark font-bold">‹</span>
            </button>

            <!-- Item 2: الأخصائيات -->
            <button onclick="openSidebarSection('specialists')" 
                    class="w-full p-3.5 rounded-2xl bg-surface hover:bg-sage-light/60 border border-transparent hover:border-gold/40 flex items-center justify-between transition group text-right">
                <div>
                    <span class="block text-sm font-extrabold text-sage-dark">الأخصائيات</span>
                    <span class="text-[11px] text-gray-500">نخبة خبيرات الشعر والجمال</span>
                </div>
                <span class="text-lg text-gold-dark font-bold">‹</span>
            </button>

            <!-- Item 3: للتواصل والاستفسار -->
            <button onclick="openSidebarSection('contact')" 
                    class="w-full p-3.5 rounded-2xl bg-surface hover:bg-sage-light/60 border border-transparent hover:border-gold/40 flex items-center justify-between transition group text-right">
                <div>
                    <span class="block text-sm font-extrabold text-sage-dark">للتواصل والاستفسار</span>
                    <span class="text-[11px] text-gray-500">(اتصل بنا)</span>
                </div>
                <span class="text-lg text-gold-dark font-bold">‹</span>
            </button>

            <!-- Item 4: سياسة الخصوصية -->
            <button onclick="openSidebarSection('privacy')" 
                    class="w-full p-3.5 rounded-2xl bg-surface hover:bg-sage-light/60 border border-transparent hover:border-gold/40 flex items-center justify-between transition group text-right">
                <div>
                    <span class="block text-sm font-extrabold text-sage-dark">سياسة الخصوصية</span>
                    <span class="text-[11px] text-gray-500">(حماية وسرية بياناتكِ)</span>
                </div>
                <span class="text-lg text-gold-dark font-bold">‹</span>
            </button>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div class="p-5 bg-gold-light/50 border-t border-gold-soft/40 text-center">
        <p class="text-xs font-bold text-sage-dark">أناقتكِ تتجدد في كل زيارة</p>
        <p class="text-[11px] text-gold-dark mt-0.5">{{ $salonIdentity['name_en'] ?? 'HAIRLINE' }} {{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }} ©</p>
    </div>
</aside>
