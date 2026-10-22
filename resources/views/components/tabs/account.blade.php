<section id="accountTabView" class="hidden flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 md:pt-8 space-y-4 animate-step">
    <!-- Real Customer Profile Card (Populated dynamically via JS - No Fake Data) -->
    <div id="customerProfileCardContainer">
        <!-- Rendered by renderCustomerProfile() -->
    </div>

    <!-- My Bookings List -->
    <div class="bg-white rounded-3xl p-5 shadow-card border border-gold-soft/40 space-y-3">
        <div class="flex items-center justify-between">
            <h4 class="font-extrabold text-sm text-sage-dark flex items-center gap-2">
                <i class="fa-regular fa-calendar-check text-gold-dark"></i>
                <span>حجوزاتي المؤكدة</span>
            </h4>
            <button onclick="openBookingFlow()" class="text-xs font-bold text-sage-dark bg-sage-soft px-3.5 py-1.5 rounded-full hover:bg-sage-dark hover:text-white transition cursor-pointer">
                + حجز جديد
            </button>
        </div>
        <div id="myBookingsContainer" class="space-y-2.5">
            <!-- Populated via JS -->
        </div>
    </div>
</section>

<!-- ==================== LIGHTWEIGHT ANIMATED EDIT PROFILE POPUP MODAL ==================== -->
<div id="editProfileModal"
     onclick="if (event.target === this) closeEditProfileModal()"
     class="fixed inset-0 z-[65] hidden items-center justify-center p-4 bg-black/55 backdrop-blur-xs transition-opacity duration-300 opacity-0">
    
    <div id="editProfileCard"
         class="relative w-full max-w-[390px] rounded-[28px] bg-white shadow-2xl border border-gold-soft/70 overflow-hidden transform scale-90 translate-y-4 transition-all duration-300 ease-out">
        
        <!-- Top Gold Accent Line -->
        <div class="h-1.5 w-full bg-gradient-to-r from-sage-dark via-gold to-sage-dark"></div>

        <!-- Close Button -->
        <button type="button"
                onclick="closeEditProfileModal()"
                aria-label="إغلاق نافذة التعديل"
                class="absolute top-4 left-4 w-8 h-8 rounded-full bg-surface text-gray-500 hover:bg-red-50 hover:text-red-500 flex items-center justify-center border border-gray-200/70 transition cursor-pointer z-10">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>

        <form id="editProfileForm" onsubmit="submitEditProfile(event)" class="p-6 space-y-4" enctype="multipart/form-data" novalidate>
            <div class="text-center space-y-1">
                <h4 class="text-base sm:text-lg font-black text-sage-dark">تعديل الملف الشخصي</h4>
                <p class="text-xs text-gray-500">حدّثي اسمكِ، رقم جوالكِ، أو صورتكِ الشخصية</p>
            </div>

            <!-- Avatar Upload & Live Preview -->
            <div class="flex flex-col items-center">
                <label for="editProfileAvatarInput" class="relative group cursor-pointer">
                    <img id="editProfileAvatarPreview"
                         src="https://ui-avatars.com/api/?name=HL&background=211C18&color=C5A373&bold=true"
                         alt="الصورة الشخصية"
                         class="w-20 h-20 rounded-full object-cover border-2 border-gold shadow-md group-hover:opacity-90 transition" />
                    <span class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-sage-dark text-gold border-2 border-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-camera text-[11px]"></i>
                    </span>
                </label>
                <input id="editProfileAvatarInput"
                       type="file"
                       name="avatar"
                       accept="image/*"
                       onchange="previewProfileAvatar(this)"
                       class="hidden" />
                <button type="button"
                        onclick="document.getElementById('editProfileAvatarInput').click()"
                        class="mt-1.5 text-[11px] font-bold text-gold-dark hover:underline cursor-pointer">
                    تغيير الصورة الشخصية
                </button>
            </div>

            <!-- Name Field -->
            <div>
                <label for="editProfileNameInput" class="block text-xs font-extrabold text-sage-dark mb-1">
                    الاسم الكامل <span class="text-red-500">*</span>
                </label>
                <div class="relative flex items-center">
                    <input id="editProfileNameInput"
                           type="text"
                           name="name"
                           placeholder="الاسم الكامل"
                           class="w-full py-3 pr-3.5 pl-9 rounded-2xl bg-surface border border-gray-200 focus:border-sage-dark focus:bg-white focus:outline-none text-right font-bold text-xs sm:text-sm text-sage-dark transition" />
                    <i class="fa-regular fa-user text-gray-400 text-xs absolute left-3.5"></i>
                </div>
            </div>

            <!-- Phone Field -->
            <div>
                <label for="editProfilePhoneInput" class="block text-xs font-extrabold text-sage-dark mb-1">
                    رقم الجوال السعودي <span class="text-red-500">*</span>
                </label>
                <div class="relative flex items-center">
                    <input id="editProfilePhoneInput"
                           type="tel"
                           name="phone"
                           dir="ltr"
                           maxlength="14"
                           placeholder="05XXXXXXXX"
                           class="w-full py-3 pr-3.5 pl-9 rounded-2xl bg-surface border border-gray-200 focus:border-sage-dark focus:bg-white focus:outline-none text-left font-bold text-xs sm:text-sm text-sage-dark tracking-wider transition" />
                    <i class="fa-solid fa-phone text-gray-400 text-xs absolute left-3.5"></i>
                </div>
            </div>

            <p id="editProfileError" class="hidden text-[11px] font-bold text-red-600 flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span id="editProfileErrorText">يرجى التأكد من صحة البيانات المدخلة.</span>
            </p>

            <div class="flex items-center gap-2.5 pt-1">
                <button type="submit"
                        id="editProfileSubmitBtn"
                        class="flex-1 py-3.5 rounded-2xl bg-sage-dark hover:bg-sage-deeper text-white font-extrabold text-xs sm:text-sm shadow-lg transition flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-check text-gold"></i>
                    <span>حفظ التعديلات</span>
                </button>
                <button type="button"
                        onclick="closeEditProfileModal()"
                        class="px-4 py-3.5 rounded-2xl bg-surface hover:bg-gray-100 text-gray-600 font-bold text-xs sm:text-sm border border-gray-200 transition cursor-pointer">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>
