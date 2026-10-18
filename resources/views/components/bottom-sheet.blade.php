<div id="sheetOverlay" 
     onclick="closeBottomSheet()" 
     class="fixed inset-0 bg-black/55 backdrop-blur-xs z-40 hidden opacity-0 drawer-backdrop">
</div>

<div id="bottomSheet" 
     class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] max-h-[88vh] bg-white z-50 rounded-t-[32px] shadow-2xl translate-y-full bottom-sheet flex flex-col overflow-hidden">
    
    <!-- Drag Handle & Top Sheet Bar -->
    <div class="pt-3 pb-3 px-5 bg-sage-light/60 border-b border-gold-soft/40 shrink-0">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-3"></div>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <button id="sheetBackBtn" 
                        onclick="handleSheetBack()" 
                        class="hidden w-9 h-9 rounded-full bg-white text-sage-dark shadow-xs flex items-center justify-center hover:bg-sage-dark hover:text-white transition">
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </button>
                <div>
                    <h3 id="sheetTitle" class="text-base sm:text-lg font-extrabold text-sage-dark">الخدمات المتاحة للحجز</h3>
                    <p id="sheetSubtitle" class="text-xs text-gray-500">خطوات بسيطة لتأكيد موعدكِ</p>
                </div>
            </div>

            <button onclick="closeBottomSheet()" 
                    aria-label="إغلاق النافذة"
                    class="w-9 h-9 rounded-full bg-white text-gray-500 hover:bg-red-50 hover:text-red-500 flex items-center justify-center shadow-xs transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- 3-Step Progress Bar (Shown only during Booking Flow) -->
        <div id="bookingStepper" class="mt-4 pt-3 border-t border-white/60 hidden">
            <div class="grid grid-cols-3 gap-2 text-center">
                <div id="stepIndicator1" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-sage-dark mb-1.5 transition-colors"></div>
                    <span class="text-[11px] font-bold text-sage-dark">1. القسم الرئيسي</span>
                </div>
                <div id="stepIndicator2" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-gray-200 mb-1.5 transition-colors"></div>
                    <span class="text-[11px] font-medium text-gray-400">2. الصنف الدقيق</span>
                </div>
                <div id="stepIndicator3" class="flex flex-col items-center">
                    <div class="w-full h-1.5 rounded-full bg-gray-200 mb-1.5 transition-colors"></div>
                    <span class="text-[11px] font-medium text-gray-400">3. الأخصائية</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Scrollable Body Inside Bottom Sheet -->
    <div id="sheetBody" class="p-5 overflow-y-auto flex-1 space-y-4">
        <!-- Content injected dynamically by JS -->
    </div>

    <!-- Optional Sticky Footer inside Bottom Sheet -->
    <div id="sheetFooter" class="p-4 bg-white border-t border-gray-100 hidden shrink-0">
        <!-- Action Button injected dynamically by JS -->
    </div>
</div>
