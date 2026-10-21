@props([
    'salonIdentity' => [],
    'stats' => [],
    'aboutUs' => [],
])

<section id="aboutTabView" class="hidden flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 md:pt-8 space-y-4 animate-step">
    {{-- About Us Main Card --}}
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-card border border-gold-soft/40 space-y-5 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <span class="px-3 py-1 rounded-full bg-gold-light text-gold-dark text-xs font-bold">{{ $aboutUs['title'] ?? 'من نحن' }}</span>
            <span class="text-xs font-serif tracking-widest text-gray-400">EST. {{ $aboutUs['founding_year'] ?? '2020' }}</span>
        </div>

        <h2 class="text-2xl font-black text-sage-dark">{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} | {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }} {{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}</h2>

        {{-- Intro paragraph --}}
        <p class="text-sm text-gray-600 leading-relaxed">
            {{ $aboutUs['intro_paragraph'] ?? '' }}
        </p>

        {{-- Description paragraph --}}
        <p class="text-sm text-gray-600 leading-relaxed">
            {{ $aboutUs['description_paragraph'] ?? '' }}
        </p>

        {{-- Belief paragraph --}}
        <p class="text-sm text-gray-600 leading-relaxed font-medium text-sage-dark">
            {{ $aboutUs['belief_paragraph'] ?? '' }}
        </p>

        {{-- Stats Grid from Static Service --}}
        <div class="grid grid-cols-2 gap-3 pt-2 text-center">
            @foreach($stats as $st)
                <div class="p-3.5 rounded-2xl {{ $st['bg'] ?? 'bg-sage-soft' }}">
                    <span class="block text-lg font-black {{ $st['text'] ?? 'text-sage-dark' }}">{{ $st['value'] }}</span>
                    <span class="text-xs text-gray-600 font-medium">{{ $st['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Founder's Message Card --}}
    @if(!empty($aboutUs['founder_message_paragraphs']))
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-card border border-gold-soft/40 space-y-4 max-w-4xl mx-auto">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-sage-soft text-sage-dark text-xs font-bold">{{ $aboutUs['founder_message_title'] ?? 'كلمة المؤسسة' }}</span>
            </div>

            @foreach($aboutUs['founder_message_paragraphs'] as $paragraph)
                <p class="text-sm text-gray-600 leading-relaxed">{{ $paragraph }}</p>
            @endforeach

            {{-- Founder Signature --}}
            @if(!empty($aboutUs['founder_signature']))
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-sm font-black text-sage-dark">{{ $aboutUs['founder_signature']['name_ar'] }}</p>
                    <p class="text-xs text-gold-dark font-medium">{{ $aboutUs['founder_signature']['title_en'] }} | {{ $aboutUs['founder_signature']['title_ar'] }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Action Buttons --}}
    <div class="max-w-4xl mx-auto">
        <div class="pt-1 flex gap-2">
            <button onclick="openSidebarSection('specialists')" class="flex-1 py-3 rounded-xl bg-sage-dark text-white text-xs font-bold hover:bg-sage-deeper transition">
                تعرفي على الأخصائيات
            </button>
            <button onclick="openSidebarSection('contact')" class="flex-1 py-3 rounded-xl bg-gold-light text-gold-dark text-xs font-bold hover:bg-gold hover:text-white transition">
                معلومات التواصل
            </button>
        </div>
    </div>
</section>
