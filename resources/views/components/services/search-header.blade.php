{{-- Minimal Search Bar (Sticky at top of services menu) --}}
<header class="no-print sticky top-0 z-20 bg-[#fdfbf7]/90 backdrop-blur-md border-b border-[#e5dec9] py-2.5 px-4">
    <div class="max-w-md mx-auto relative">
        <input
            id="serviceSearch"
            type="text"
            placeholder="ابحث عن خدمة أو سعر..."
            class="w-full bg-white border border-[#d5c7ad] rounded-full py-1.5 pr-4 pl-8 text-sm text-[#7e7c78] placeholder-[#b39d76] focus:outline-none focus:border-[#b7a077] shadow-xs"
        />
        <button id="clearSearchBtn" class="hidden absolute left-3 top-1/2 -translate-y-1/2 text-[#b39d76] hover:text-[#7e7c78] text-xs font-bold cursor-pointer" title="مسح البحث">✕</button>
    </div>
</header>
