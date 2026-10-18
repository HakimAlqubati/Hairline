@props([
    'salonIdentity' => [],
    'stats' => [],
])

<section id="aboutTabView" class="hidden flex-1 px-4 pt-5 space-y-4 animate-step">
    <div class="bg-white rounded-3xl p-6 shadow-card border border-gold-soft/40 space-y-4">
        <div class="flex items-center justify-between">
            <span class="px-3 py-1 rounded-full bg-gold-light text-gold-dark text-xs font-bold">من نحن</span>
            <span class="text-xs font-serif tracking-widest text-gray-400">EST. BEAUTY LOUNGE</span>
        </div>
        <h2 class="text-2xl font-black text-sage-dark">{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} | {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }} {{ $salonIdentity['tagline_en'] ?? 'BEAUTY LOUNGE' }}</h2>
        <p class="text-sm text-gray-600 leading-relaxed">
            وجهة الجمال الأولى المصممة خصيصاً للمرأة العصرية الباحثة عن الفخامة والعناية المتكاملة. نتميز في <strong class="text-sage-dark">{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }}</strong> بتقديم أحدث المعالجات الحارة والباردة للشعر، الاستشوار، قص الشعر، الصبغات، المناكير والبادكير، والمساج باستخدام أجود المستحضرات العالمية الآمنة، تحت إشراف نخبة من الأخصائيات المعتمدات.
        </p>

        <!-- Stats Grid from Static Service -->
        <div class="grid grid-cols-3 gap-2 pt-2 text-center">
            @foreach($stats as $st)
                <div class="p-3 rounded-2xl {{ $st['bg'] ?? 'bg-sage-soft' }}">
                    <span class="block text-lg font-black {{ $st['text'] ?? 'text-sage-dark' }}">{{ $st['value'] }}</span>
                    <span class="text-[11px] text-gray-600 font-medium">{{ $st['label'] }}</span>
                </div>
            @endforeach
        </div>

        <div class="pt-3 border-t border-gray-100 flex gap-2">
            <button onclick="openSidebarSection('specialists')" class="flex-1 py-3 rounded-xl bg-sage-dark text-white text-xs font-bold hover:bg-sage-deeper transition">
                تعرفي على الأخصائيات
            </button>
            <button onclick="openSidebarSection('contact')" class="flex-1 py-3 rounded-xl bg-gold-light text-gold-dark text-xs font-bold hover:bg-gold hover:text-white transition">
                معلومات التواصل
            </button>
        </div>
    </div>
</section>
