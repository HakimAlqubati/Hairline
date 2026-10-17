<div class="flex flex-col flex-1 animate-view">

    {{-- Header --}}
    @include('livewire.partials.page-header', ['title' => 'الحجز', 'backRoute' => 'home'])

    @if($isConfirmed)
        {{-- ===== BOOKING CONFIRMATION TICKET ===== --}}
        <div class="flex-1 overflow-y-auto p-6 flex flex-col justify-center animate-view">
            <div class="bg-white rounded-3xl p-7 shadow-md border border-[#C5A373]/40 text-center relative overflow-hidden">

                {{-- Success Icon --}}
                <div class="w-16 h-16 rounded-full bg-[#E2EFEA] text-[#4A5D54] mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-[#232B28] mb-1">تم تأكيد حجزكِ بنجاح</h2>
                <p class="text-base text-[#5C6B64] mb-5">نسعد باستقبالكِ في صالون هيرلاين النسائي</p>

                {{-- Reference Badge --}}
                <div class="bg-[#FAF8F5] border border-dashed border-[#C5A373] rounded-2xl p-4 mb-5 flex items-center justify-between">
                    <div class="text-right">
                        <span class="text-xs text-[#5C6B64] block">رقم الحجز المرجعي</span>
                        <span class="font-en-brand text-xl font-bold text-[#4A5D54]">{{ $confirmedBooking['ref_code'] }}</span>
                    </div>
                    <button wire:click="copyRefCode"
                            x-data
                            @copy-to-clipboard.window="navigator.clipboard.writeText($event.detail.text).then(() => { $el.textContent = 'تم النسخ ✓'; setTimeout(() => { $el.textContent = 'نسخ الرقم'; }, 2000); })"
                            class="bg-white hover:bg-[#E2EFEA] text-[#4A5D54] border border-[#EAEAEA] text-xs font-bold px-3.5 py-2 rounded-xl transition-colors">
                        نسخ الرقم
                    </button>
                </div>

                {{-- Appointment Details --}}
                <div class="text-right space-y-3 text-base border-t border-b border-[#EAEAEA] py-4 mb-6">
                    <div class="flex justify-between">
                        <span class="text-[#5C6B64]">الاسم:</span>
                        <span class="font-bold text-[#232B28]">{{ $confirmedBooking['client_name'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#5C6B64]">الموعد:</span>
                        <span class="font-bold text-[#4A5D54]">{{ $confirmedBooking['date_label'] }} • {{ $confirmedBooking['time'] }}</span>
                    </div>
                    @if($confirmedBooking['booking_type'] === 'home' && $confirmedBooking['district_name'])
                        <div class="flex justify-between">
                            <span class="text-[#5C6B64]">الحي:</span>
                            <span class="font-bold text-[#232B28]">{{ $confirmedBooking['district_name'] }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-[#5C6B64] block mb-1">الخدمات المحجوزة:</span>
                        @foreach($confirmedBooking['services'] as $srv)
                            <div class="flex justify-between text-sm bg-[#FAF8F5] px-3 py-2 rounded-xl mb-1">
                                <span class="font-medium text-[#232B28]">• {{ $srv['name'] }}</span>
                                <span class="font-bold text-[#4A5D54]">{{ $srv['price'] }} SAR</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between pt-2 border-t border-[#EAEAEA]">
                        <span class="font-bold text-[#232B28]">الإجمالي:</span>
                        <span class="font-bold text-xl text-[#4A5D54]">{{ $confirmedBooking['totals']['total_price'] }} SAR</span>
                    </div>
                </div>

                {{-- CTA Buttons --}}
                <div class="grid grid-cols-2 gap-3">
                    <button wire:click="resetBooking"
                            class="py-3.5 px-4 rounded-2xl bg-[#FAF8F5] hover:bg-[#E2EFEA] text-[#4A5D54] font-bold text-base border border-[#EAEAEA] transition-colors">
                        حجز موعد آخر
                    </button>
                    <a href="{{ route('home') }}" wire:navigate
                       class="py-3.5 px-4 rounded-2xl bg-[#4A5D54] hover:bg-[#36453E] text-white font-bold text-base transition-colors text-center">
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>

    @else
        {{-- ===== BOOKING FORM ===== --}}
        <div class="flex-1 overflow-y-auto">
            <div class="p-5 space-y-5 pb-4">

                {{-- ══ STEP 1: Service Selection ══ --}}
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-[#EAEAEA]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-[#232B28] flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#4A5D54] text-white text-xs flex items-center justify-center">1</span>
                            <span>الخدمة المطلوبة</span>
                        </h3>
                        <a href="{{ route('services') }}" wire:navigate class="text-sm font-semibold text-[#8C7048] hover:underline">
                            + تصفح كل الخدمات
                        </a>
                    </div>

                    {{-- Category Tabs Horizontal Scroll --}}
                    <div wire:ignore x-data="{ swiper: null }"
                         x-init="swiper = new Swiper($el.querySelector('.swiper'), { slidesPerView: 'auto', spaceBetween: 8, freeMode: true })">
                        <div class="swiper overflow-visible mb-4">
                            <div class="swiper-wrapper">
                                @foreach($categories as $cat)
                                    <div class="swiper-slide !w-auto">
                                        <button wire:click="selectCategory('{{ $cat['id'] }}')"
                                                class="shrink-0 px-4 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ $activeCategoryId === $cat['id'] ? 'bg-[#4A5D54] text-white shadow-sm' : 'bg-[#FAF8F5] text-[#5C6B64] border border-[#EAEAEA] hover:border-[#C5A373]' }}">
                                            {{ $cat['title'] }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Service Picker List --}}
                    <div class="space-y-2 max-h-52 overflow-y-auto">
                        @foreach($activeServices as $srv)
                            @php $isSelected = in_array($srv['id'], $selectedServiceIds); @endphp
                            <div wire:click="toggleService('{{ $srv['id'] }}')"
                                 class="p-3.5 rounded-2xl border cursor-pointer transition-all flex items-center justify-between {{ $isSelected ? 'bg-[#E2EFEA]/70 border-[#4A5D54]' : 'bg-[#FAF8F5] border-[#EAEAEA] hover:border-[#C5A373]' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center {{ $isSelected ? 'bg-[#4A5D54] border-[#4A5D54] text-white' : 'bg-white border-[#C5A373]' }}">
                                        @if($isSelected) ✓ @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="text-sm sm:text-base font-bold text-[#232B28]">{{ $srv['name'] }}</p>
                                            @if(!empty($srv['length_label']))
                                                <span class="text-[10px] font-bold bg-[#E2EFEA] text-[#4A5D54] px-1.5 py-0.5 rounded border border-[#4A5D54]/20">{{ $srv['length_label'] }}</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-[#5C6B64]">{{ $srv['duration'] }}</p>
                                    </div>
                                </div>
                                <span class="font-bold text-sm sm:text-base text-[#4A5D54] shrink-0">
                                    {{ $srv['price_display'] ?? ($srv['price'] . ' SAR') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Selected Chips --}}
                    @if(count($selectedServiceIds) > 0)
                        <div class="mt-4 pt-3 border-t border-[#EAEAEA]">
                            <p class="text-xs font-semibold text-[#5C6B64] mb-2">الخدمات المحددة ({{ count($selectedServiceIds) }}):</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedServiceIds as $srvId)
                                    @php
                                        $found = null;
                                        foreach($categories as $cat) {
                                            foreach($cat['services'] as $s) {
                                                if ($s['id'] === $srvId) { $found = $s; break 2; }
                                            }
                                        }
                                    @endphp
                                    @if($found)
                                        <span class="inline-flex items-center gap-2 bg-[#4A5D54] text-white text-sm font-medium px-3.5 py-1.5 rounded-full">
                                            <span>{{ $found['name'] }} ({{ $found['price'] }} SAR)</span>
                                            <button wire:click="toggleService('{{ $srvId }}')" class="hover:text-[#C5A373] font-bold">✕</button>
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="mt-4 pt-3 border-t border-[#EAEAEA] text-sm text-[#8C7048] bg-[#F5EFE6] px-3.5 py-2.5 rounded-xl border border-[#C5A373]/30">
                            فضلاً اختاري خدمة واحدة على الأقل من القائمة أعلاه.
                        </p>
                    @endif
                </div>

                {{-- ══ STEP 2: Booking Type (Salon / Home) ══ --}}
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-[#EAEAEA]">
                    <h3 class="text-lg font-bold text-[#232B28] flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 rounded-full bg-[#4A5D54] text-white text-xs flex items-center justify-center">2</span>
                        <span>نوع الخدمة</span>
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="$set('bookingType', 'salon')"
                                class="py-4 rounded-2xl border-2 text-base font-bold transition-all {{ $bookingType === 'salon' ? 'bg-[#4A5D54] text-white border-[#4A5D54] shadow-md' : 'bg-white text-[#5C6B64] border-[#EAEAEA] hover:border-[#C5A373]' }}">
                            🏪 في الصالون
                        </button>
                        <button wire:click="$set('bookingType', 'home')"
                                class="py-4 rounded-2xl border-2 text-base font-bold transition-all {{ $bookingType === 'home' ? 'bg-[#4A5D54] text-white border-[#4A5D54] shadow-md' : 'bg-white text-[#5C6B64] border-[#EAEAEA] hover:border-[#C5A373]' }}">
                            🏠 خدمة منزلية
                        </button>
                    </div>

                    {{-- District picker for home service --}}
                    @if($bookingType === 'home')
                        <div class="mt-4">
                            <button wire:click="$set('showDistrictSheet', true)"
                                    class="w-full text-right px-4 py-3.5 rounded-2xl border {{ $selectedDistrictId ? 'border-[#4A5D54] bg-[#E2EFEA]/50' : 'border-dashed border-[#C5A373]' }} text-base font-medium flex items-center justify-between transition-colors">
                                <span class="{{ $selectedDistrictId ? 'text-[#232B28] font-bold' : 'text-[#8C7048]' }}">
                                    @if($selectedDistrictId)
                                        @foreach($districtGroups as $zone => $districts)
                                            @foreach($districts as $d)
                                                @if($d['id'] === $selectedDistrictId) {{ $d['name'] }} @endif
                                            @endforeach
                                        @endforeach
                                    @else
                                        اختاري حيّكِ ← (12 حياً مغطى)
                                    @endif
                                </span>
                                <svg class="w-5 h-5 text-[#8C7048]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

                {{-- ══ STEP 3: Specialist ══ --}}
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-[#EAEAEA]">
                    <h3 class="text-lg font-bold text-[#232B28] flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 rounded-full bg-[#4A5D54] text-white text-xs flex items-center justify-center">3</span>
                        <span>اختاري الخبيرة</span>
                    </h3>
                    <div wire:ignore x-data="{ swiper: null }"
                         x-init="swiper = new Swiper($el.querySelector('.swiper'), { slidesPerView: 'auto', spaceBetween: 10, freeMode: true })">
                        <div class="swiper overflow-visible">
                            <div class="swiper-wrapper">
                                @foreach($specialists as $specialist)
                                    @if($bookingType === 'home' && !$specialist['is_home_specialist']) @continue @endif
                                    @php $isActive = ($selectedSpecialistId === $specialist['id']); @endphp
                                    <div class="swiper-slide !w-auto">
                                        <button wire:click="selectSpecialist('{{ $specialist['id'] }}')"
                                                class="flex flex-col items-center gap-2 p-3 rounded-2xl border-2 transition-all {{ $isActive ? 'border-[#4A5D54] bg-[#E2EFEA]/60' : 'border-[#EAEAEA] bg-white hover:border-[#C5A373]' }}">
                                            <div class="w-12 h-12 rounded-full bg-[#E2EFEA] flex items-center justify-center text-[#4A5D54] font-bold text-lg">
                                                {{ $specialist['avatar_initials'] }}
                                            </div>
                                            <div class="text-center">
                                                <p class="text-sm font-bold text-[#232B28] whitespace-nowrap">{{ $specialist['name'] }}</p>
                                                <p class="text-xs text-[#5C6B64]">{{ $specialist['speciality'] }}</p>
                                            </div>
                                            @if($isActive)
                                                <span class="text-xs font-bold text-[#4A5D54] bg-white px-2 py-0.5 rounded-full">✓ مختارة</span>
                                            @endif
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ STEP 4: Date ══ --}}
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-[#EAEAEA]">
                    <h3 class="text-lg font-bold text-[#232B28] flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 rounded-full bg-[#4A5D54] text-white text-xs flex items-center justify-center">4</span>
                        <span>اختاري اليوم والتاريخ</span>
                    </h3>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2.5">
                        @foreach($availableDates as $date)
                            <button wire:click="selectDate('{{ $date['id'] }}')"
                                    class="choice-pill {{ $selectedDateId === $date['id'] ? 'is-selected' : '' }} py-3 px-2 rounded-2xl text-center">
                                <span class="block text-base font-bold">{{ $date['day_name'] }}</span>
                                <span class="block text-xs opacity-80 mt-0.5">{{ $date['date_label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- ══ STEP 5: Time Slot ══ --}}
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-[#EAEAEA]">
                    <h3 class="text-lg font-bold text-[#232B28] flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 rounded-full bg-[#4A5D54] text-white text-xs flex items-center justify-center">5</span>
                        <span>اختاري الوقت المتاح</span>
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @foreach($timeSlots as $slot)
                            <button wire:click="{{ $slot['is_available'] ? "selectTime('{$slot['time']}')" : '' }}"
                                    class="choice-pill {{ $selectedTime === $slot['time'] ? 'is-selected' : '' }} {{ !$slot['is_available'] ? 'is-booked' : '' }} py-3 px-3 rounded-2xl text-base font-bold text-center">
                                {{ $slot['time'] }}
                                @if(!$slot['is_available'])
                                    <span class="block text-xs mt-0.5 font-normal">محجوز</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- ══ STEP 6: Client Info ══ --}}
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-[#EAEAEA] space-y-4">
                    <h3 class="text-lg font-bold text-[#232B28] flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-[#4A5D54] text-white text-xs flex items-center justify-center">6</span>
                        <span>بيانات التواصل</span>
                    </h3>

                    <div>
                        <label class="block text-base font-medium text-[#232B28] mb-1.5">الاسم الكريم</label>
                        <input wire:model="clientName"
                               type="text" placeholder="مثال: سارة العتيبي"
                               class="w-full px-4 py-3.5 rounded-2xl bg-[#FAF8F5] border {{ isset($validationErrors['client_name']) ? 'border-red-400' : 'border-[#E2DDD5]' }} text-lg font-medium focus:outline-none focus:border-[#4A5D54] focus:bg-white transition-all">
                        @error('clientName') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        @if(isset($validationErrors['client_name']))
                            <p class="text-red-600 text-xs mt-1">{{ $validationErrors['client_name'] }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-base font-medium text-[#232B28] mb-1.5">رقم الجوال</label>
                        <input wire:model="clientPhone"
                               type="tel" dir="ltr" placeholder="05XXXXXXXX" maxlength="10"
                               class="w-full px-4 py-3.5 rounded-2xl bg-[#FAF8F5] border {{ isset($validationErrors['client_phone']) ? 'border-red-400' : 'border-[#E2DDD5]' }} text-lg font-medium text-right focus:outline-none focus:border-[#4A5D54] focus:bg-white transition-all">
                        @if(isset($validationErrors['client_phone']))
                            <p class="text-red-600 text-xs mt-1">{{ $validationErrors['client_phone'] }}</p>
                        @endif
                    </div>

                    {{-- Payment Option --}}
                    <div>
                        <label class="block text-base font-medium text-[#232B28] mb-2">طريقة الدفع</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($paymentOptions as $payment)
                                <button wire:click="$set('selectedPaymentId', '{{ $payment['id'] }}')"
                                        class="px-3 py-2.5 rounded-xl border text-sm font-bold transition-all text-right {{ $selectedPaymentId === $payment['id'] ? 'bg-[#E2EFEA] border-[#4A5D54] text-[#4A5D54]' : 'bg-[#FAF8F5] border-[#EAEAEA] text-[#5C6B64] hover:border-[#C5A373]' }}">
                                    {{ $payment['name_ar'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Cancellation Policy Link --}}
                    <button wire:click="$set('showCancellationSheet', true)"
                            class="text-sm font-semibold text-[#8C7048] underline w-full text-right">
                        📋 اطلعي على سياسة الإلغاء والعربون
                    </button>

                    {{-- Validation Errors Summary --}}
                    @if(isset($validationErrors['services']))
                        <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
                            {{ $validationErrors['services'] }}
                        </div>
                    @endif
                    @if(isset($validationErrors['time']))
                        <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
                            {{ $validationErrors['time'] }}
                        </div>
                    @endif
                </div>

                {{-- ══ STEP 7: Total & Confirm ══ --}}
                <div class="bg-[#E2EFEA] rounded-3xl p-5 border border-[#4A5D54]/20 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="text-sm font-medium text-[#4A5D54] block">الإجمالي التقديري</span>
                            <span class="text-xs text-[#5C6B64]">{{ $totals['total_duration_label'] ?? '0 دقيقة' }}</span>
                        </div>
                        <div class="text-left">
                            <span class="text-2xl font-extrabold text-[#232B28]">{{ $totals['total_price'] }}</span>
                            <span class="font-en-brand text-sm font-bold text-[#4A5D54] mr-1">SAR</span>
                        </div>
                    </div>

                    @if($totals['total_price'] > 0)
                        <div class="bg-white/60 rounded-2xl px-4 py-3 mb-4 flex items-center justify-between">
                            <span class="text-sm text-[#5C6B64]">{{ $deposit['deposit_label_ar'] }}</span>
                            <span class="text-base font-bold text-[#8C7048]">{{ $deposit['deposit_amount'] }} SAR</span>
                        </div>
                    @endif

                    <button wire:click="submitBooking" wire:loading.attr="disabled"
                            class="w-full py-4 rounded-2xl bg-[#4A5D54] hover:bg-[#36453E] text-white font-bold text-xl shadow-lg transition-all flex items-center justify-center gap-3 disabled:opacity-60">
                        <span wire:loading.remove wire:target="submitBooking">تأكيد الحجز</span>
                        <span wire:loading wire:target="submitBooking">جاري التأكيد...</span>
                        <svg wire:loading.remove wire:target="submitBooking" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ===== DISTRICT BOTTOM SHEET ===== --}}
    @if($showDistrictSheet)
        <div class="fixed inset-0 z-[80]">
            <div class="bottom-sheet-backdrop" wire:click="$set('showDistrictSheet', false)"></div>
            <div class="bottom-sheet-panel" style="max-height:80dvh">
                <div class="bottom-sheet-handle"></div>
                <div class="p-6 pb-8 overflow-y-auto max-h-[75dvh]">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-[#232B28]">اختاري حيّكِ</h3>
                        <button wire:click="$set('showDistrictSheet', false)"
                                class="w-9 h-9 rounded-full bg-[#FAF8F5] font-bold text-[#232B28] flex items-center justify-center text-lg">✕</button>
                    </div>
                    @foreach($districtGroups as $zone => $districts)
                        <div class="mb-5">
                            <p class="text-sm font-semibold text-[#8C7048] mb-2 px-1">{{ $zone }}</p>
                            <div class="space-y-2">
                                @foreach($districts as $district)
                                    <button wire:click="selectDistrict('{{ $district['id'] }}')"
                                            @class([
                                                'w-full text-right p-4 rounded-2xl border transition-all flex items-center justify-between',
                                                'border-[#4A5D54] bg-[#E2EFEA]' => $selectedDistrictId === $district['id'],
                                                'border-[#EAEAEA] bg-white hover:border-[#C5A373]' => $selectedDistrictId !== $district['id'] && $district['is_available'],
                                                'border-[#EAEAEA] bg-[#FAF8F5] opacity-50 cursor-not-allowed' => !$district['is_available'],
                                            ])
                                            @disabled(!$district['is_available'])>
                                        <div>
                                            <span class="font-bold text-[#232B28]">{{ $district['name'] }}</span>
                                            @if($district['extra_fee'] > 0)
                                                <span class="text-xs text-[#8C7048] mr-2">+{{ $district['extra_fee'] }} SAR</span>
                                            @endif
                                        </div>
                                        @if(!$district['is_available'])
                                            <span class="text-xs text-red-400 font-medium">غير متاح</span>
                                        @elseif($selectedDistrictId === $district['id'])
                                            <span class="text-[#4A5D54] font-bold">✓</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ===== CANCELLATION POLICY BOTTOM SHEET ===== --}}
    @if($showCancellationSheet)
        <div class="fixed inset-0 z-[80]">
            <div class="bottom-sheet-backdrop" wire:click="$set('showCancellationSheet', false)"></div>
            <div class="bottom-sheet-panel">
                <div class="bottom-sheet-handle"></div>
                <div class="p-6 pb-8">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-[#232B28]">سياسة الإلغاء والعربون</h3>
                        <button wire:click="$set('showCancellationSheet', false)"
                                class="w-9 h-9 rounded-full bg-[#FAF8F5] font-bold text-[#232B28] flex items-center justify-center text-lg">✕</button>
                    </div>

                    <div class="bg-[#FAF8F5] rounded-2xl p-4 border border-[#C5A373]/30 mb-4">
                        <p class="text-base text-[#232B28] leading-relaxed">{{ $cancellationPolicy['policy_ar'] }}</p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-3 border-b border-[#EAEAEA]">
                            <span class="text-[#5C6B64]">الإلغاء المجاني قبل:</span>
                            <span class="font-bold text-[#4A5D54]">{{ $cancellationPolicy['free_cancellation_hours'] }} ساعة</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-[#EAEAEA]">
                            <span class="text-[#5C6B64]">استرداد جزئي ({{ $cancellationPolicy['partial_refund_percentage'] }}٪):</span>
                            <span class="font-bold text-amber-600">{{ $cancellationPolicy['partial_refund_hours'] }}-{{ $cancellationPolicy['free_cancellation_hours'] }} ساعة</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-[#5C6B64]">فقدان العربون كاملاً:</span>
                            <span class="font-bold text-red-600">أقل من {{ $cancellationPolicy['partial_refund_hours'] }} ساعات</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @include('livewire.partials.bottom-nav', ['active' => 'booking'])
</div>
