<!-- iOS Safari Fullscreen / PWA Helper Modal -->
<div id="iosFullscreenModal" 
     onclick="closeIosFullscreenModal()"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[70] hidden opacity-0 transition-opacity duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    
    <div onclick="event.stopPropagation()"
         class="w-full max-w-[440px] bg-white rounded-t-[32px] sm:rounded-3xl p-6 shadow-2xl space-y-4 text-center border-t border-gold/30 relative">
        
        <!-- Apple & Fullscreen Icon Badge -->
        <div class="w-14 h-14 rounded-2xl bg-gold-light text-gold-dark flex items-center justify-center mx-auto text-2xl border border-gold/30 shadow-sm">
            <i class="fa-brands fa-apple"></i>
        </div>

        <div>
            <h3 class="text-base sm:text-lg font-extrabold text-sage-dark">ملء الشاشة على سفاري (آيفون)</h3>
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                أنظمة آبل تمنع أي موقع من إخفاء أشرطة المتصفح تلقائياً. للحصول على <strong>شاشة كاملة حقيقية 100%</strong> كتطبيق وبدون أي أشرطة:
            </p>
        </div>

        <!-- 2 Simple Steps -->
        <div class="p-4 rounded-2xl bg-sage-soft border border-sage-light text-right space-y-3.5 text-xs text-sage-dark font-medium">
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-sage-dark text-white text-[11px] font-bold flex items-center justify-center shrink-0">1</span>
                <span>اضغطي زر المشاركة أسفل شاشة سفاري (<i class="fa-solid fa-arrow-up-from-bracket text-gold-dark mx-1 text-sm"></i>)</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-sage-dark text-white text-[11px] font-bold flex items-center justify-center shrink-0">2</span>
                <span>اختاري <strong class="text-gold-dark">"إضافة إلى الصفحة الرئيسية"</strong> (Add to Home Screen)</span>
            </div>
        </div>

        <p class="text-[11px] text-gray-400">
            سيتم تثبيت أيقونة الصالون بشاشتك وستعمل بكامل الشاشة فوراً كأي تطبيق محمل من App Store ✨
        </p>

        <!-- Downward subtle guide for iPhone Safari bottom bar -->
        <div class="flex items-center justify-center gap-1.5 text-gold-dark text-xs font-bold pt-1 animate-bounce">
            <i class="fa-solid fa-arrow-down text-sm"></i>
            <span>زر المشاركة موجود بالشريط السفلي لسفاري</span>
            <i class="fa-solid fa-arrow-down text-sm"></i>
        </div>

        <div class="pt-2">
            <button onclick="closeIosFullscreenModal()" 
                    class="w-full py-3.5 rounded-2xl bg-sage-dark text-white font-extrabold text-sm hover:bg-sage-deeper transition active:scale-95 shadow-md">
                حسناً، فهمت
            </button>
        </div>
    </div>
</div>
