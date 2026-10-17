<div class="flex flex-col flex-1 animate-view">

    @include('livewire.partials.page-header', ['title' => 'ملفي وحجوزاتي', 'backRoute' => 'home'])

    <div class="flex-1 overflow-y-auto">

        @if(!$isLoggedIn && empty($bookingHistory))
            {{-- ══ LOGIN FORM ══ --}}
            <div class="p-5 space-y-5">

                {{-- Brand Card --}}
                <div class="bg-white rounded-3xl p-7 text-center shadow-sm border border-[#EAEAEA]">
                    <div class="w-16 h-16 rounded-full bg-[#E2EFEA] text-[#4A5D54] mx-auto mb-4 flex items-center justify-center font-bold text-3xl">
                        H
                    </div>
                    <h2 class="text-xl font-bold text-[#232B28] mb-1">تسجيل الدخول</h2>
                    <p class="text-sm text-[#5C6B64]">ادخلي كود عميلتكِ أو رقم الجوال لعرض حجوزاتكِ</p>
                </div>

                {{-- Login Input --}}
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-[#EAEAEA] space-y-4">
                    <div>
                        <label class="block text-base font-medium text-[#232B28] mb-1.5">كود العميلة أو رقم الجوال</label>
                        <input wire:model="loginIdentifier"
                               type="text" dir="ltr" placeholder="HL-1001 أو 05XXXXXXXX"
                               class="w-full px-4 py-3.5 rounded-2xl bg-[#FAF8F5] border {{ $loginError ? 'border-red-400' : 'border-[#E2DDD5]' }} text-lg font-medium text-right focus:outline-none focus:border-[#4A5D54] focus:bg-white transition-all">
                    </div>

                    @if($loginError)
                        <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium">
                            {{ $loginError }}
                        </div>
                    @endif

                    <button wire:click="login" wire:loading.attr="disabled"
                            class="w-full py-4 rounded-2xl bg-[#4A5D54] hover:bg-[#36453E] text-white font-bold text-xl shadow-md transition-all disabled:opacity-60">
                        <span wire:loading.remove>دخول</span>
                        <span wire:loading>جاري البحث...</span>
                    </button>

                    {{-- Demo hint --}}
                    <div class="bg-[#F5EFE6] rounded-2xl p-4 border border-[#C5A373]/30">
                        <p class="text-sm font-semibold text-[#8C7048] mb-1">حسابات تجريبية للمعاينة:</p>
                        <p class="text-sm text-[#5C6B64]">كود العميلة: <span class="font-bold font-en-brand">HL-1001</span> أو <span class="font-bold font-en-brand">HL-2002</span></p>
                    </div>
                </div>

                {{-- New Booking CTA --}}
                <a href="{{ route('booking') }}" wire:navigate
                   class="w-full py-4 rounded-2xl bg-white border-2 border-[#4A5D54] text-[#4A5D54] font-bold text-xl hover:bg-[#E2EFEA] transition-all flex items-center justify-center gap-2">
                    <span>+ احجزي موعداً جديداً</span>
                </a>
            </div>

        @elseif(!$isLoggedIn && !empty($bookingHistory))
            {{-- ══ SESSION BOOKINGS (No login required) ══ --}}
            <div class="p-5 space-y-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-[#EAEAEA] text-center">
                    <p class="text-lg font-bold text-[#232B28]">حجوزاتكِ الأخيرة</p>
                    <p class="text-sm text-[#5C6B64]">سجّلي دخولكِ لعرض سجل حجوزاتكِ الكامل</p>
                </div>
                @foreach($bookingHistory as $booking)
                    @include('livewire.partials.booking-card', ['booking' => $booking])
                @endforeach
                <a href="{{ route('booking') }}" wire:navigate
                   class="w-full py-4 rounded-2xl bg-[#4A5D54] text-white font-bold text-base transition-all flex items-center justify-center">
                    + حجز موعد جديد
                </a>
            </div>

        @else
            {{-- ══ LOGGED IN PROFILE ══ --}}
            <div class="p-5 space-y-4">

                {{-- Customer Card --}}
                <div class="bg-white rounded-3xl p-5 shadow-sm border border-[#EAEAEA] flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-[#E2EFEA] flex items-center justify-center text-[#4A5D54] font-bold text-xl">
                            {{ mb_substr($customer['name'], 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#232B28]">{{ $customer['name'] }}</h3>
                            <p class="text-sm text-[#5C6B64]">{{ $customer['phone'] }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-[#F5EFE6]"
                                      style="color: {{ $customer['tier_color'] }}">
                                    {{ $customer['tier'] }} Member
                                </span>
                                <span class="text-xs text-[#8C7048]">{{ number_format($customer['points']) }} نقطة</span>
                            </div>
                        </div>
                    </div>
                    <button wire:click="logout"
                            class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors">
                        خروج
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="grid grid-cols-2 gap-2">
                    <button wire:click="switchTab('bookings')"
                            class="py-3 rounded-2xl font-bold text-base transition-all {{ $activeTab === 'bookings' ? 'bg-[#4A5D54] text-white' : 'bg-white text-[#5C6B64] border border-[#EAEAEA]' }}">
                        الحجوزات
                    </button>
                    <button wire:click="switchTab('invoices')"
                            class="py-3 rounded-2xl font-bold text-base transition-all {{ $activeTab === 'invoices' ? 'bg-[#4A5D54] text-white' : 'bg-white text-[#5C6B64] border border-[#EAEAEA]' }}">
                        الفواتير
                    </button>
                </div>

                {{-- Bookings Tab --}}
                @if($activeTab === 'bookings')
                    @forelse($bookingHistory as $booking)
                        @include('livewire.partials.booking-card', ['booking' => $booking])
                    @empty
                        <div class="text-center py-8 bg-white rounded-2xl border border-[#EAEAEA]">
                            <p class="text-lg font-bold text-[#232B28]">لا توجد حجوزات بعد</p>
                            <p class="text-sm text-[#5C6B64]">احجزي موعدكِ الأول الآن</p>
                        </div>
                    @endforelse
                @endif

                {{-- Invoices Tab --}}
                @if($activeTab === 'invoices')
                    @forelse($invoices as $invoice)
                        <div class="bg-white rounded-2xl p-4 border border-[#EAEAEA] shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="font-en-brand font-bold text-[#4A5D54]">{{ $invoice['invoice_no'] }}</p>
                                    <p class="text-sm text-[#5C6B64]">{{ $invoice['date'] }} — {{ $invoice['ref_code'] }}</p>
                                </div>
                                <span class="text-xs font-bold {{ $invoice['paid'] ? 'bg-[#E2EFEA] text-[#4A5D54]' : 'bg-amber-100 text-amber-700' }} px-2.5 py-1 rounded-full">
                                    {{ $invoice['paid'] ? 'مدفوعة' : 'قيد الانتظار' }}
                                </span>
                            </div>
                            @foreach($invoice['items'] as $item)
                                <div class="flex justify-between text-sm py-1.5 border-b border-[#EAEAEA]">
                                    <span class="text-[#232B28]">{{ $item['name'] }}</span>
                                    <span class="font-bold text-[#4A5D54]">{{ $item['price'] }} SAR</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between font-bold text-base pt-2.5">
                                <span class="text-[#232B28]">الإجمالي</span>
                                <span class="text-[#4A5D54]">{{ $invoice['total'] }} SAR</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 bg-white rounded-2xl border border-[#EAEAEA]">
                            <p class="text-base text-[#5C6B64]">لا توجد فواتير مسجلة بعد</p>
                        </div>
                    @endforelse
                @endif

                <a href="{{ route('booking') }}" wire:navigate
                   class="w-full py-4 rounded-2xl bg-[#4A5D54] text-white font-bold text-base transition-all flex items-center justify-center">
                    + حجز موعد جديد
                </a>
            </div>
        @endif

    </div>

    @include('livewire.partials.bottom-nav', ['active' => 'profile'])
</div>
