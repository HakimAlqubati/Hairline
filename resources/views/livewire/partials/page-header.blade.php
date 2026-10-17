{{--
    Reusable Page Header Partial
    @param string $title
    @param string $backRoute   — named route for back button
    @param bool   $showBag     — show booking bag icon (default: true)
--}}
<header class="sticky top-0 z-40 px-5 py-4 flex items-center justify-between border-b border-[#D5E3DD] bg-white">

    {{-- Back Button + Page Title --}}
    <div class="flex items-center gap-3">
        <a href="{{ route($backRoute ?? 'home') }}" wire:navigate
           class="w-10 h-10 rounded-full bg-[#F7F4EF] hover:bg-[#E2EFEA] text-[#4A5D54] flex items-center justify-center transition-colors"
           aria-label="رجوع">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-[#232B28]">{{ $title }}</h1>
    </div>

    {{-- Home Logo (always visible) --}}
    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
        <div class="w-9 h-9 rounded-full bg-[#E2EFEA] flex items-center justify-center border border-[#C5A373]/30">
            @include('livewire.partials.hairline-logo', ['size' => 'sm'])
        </div>
    </a>

</header>
