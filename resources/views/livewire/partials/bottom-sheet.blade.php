{{-- Vaul-style Bottom Sheet partial
    @param string  $wireAction  — Livewire method to close (e.g. 'closeLocationSheet')
    @param bool    $isOpen
    @param string  $title
    @param string  $slot        — Use @slot syntax or pass content via $content
--}}
<div x-data="{ open: @entangle($attributes->wire('open')).live }"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-[80]"
     @keydown.escape.window="open = false">

    {{-- Backdrop --}}
    <div class="bottom-sheet-backdrop"
         x-show="open"
         x-transition:enter="transition-opacity duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-250"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false">
    </div>

    {{-- Sheet Panel --}}
    <div class="bottom-sheet-panel"
         x-show="open"
         x-transition:enter="transition-transform duration-380 ease-[cubic-bezier(0.16,1,0.3,1)]"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition-transform duration-250 ease-in"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         @click.stop
         x-ref="panel"
         x-data="swipeToClose($refs.panel, () => { open = false; })">

        {{-- Drag Handle --}}
        <div class="bottom-sheet-handle"></div>

        {{-- Sheet Content --}}
        <div class="p-6 pb-8">
            {{-- Title Row --}}
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-xl font-bold text-[#232B28]">{{ $title }}</h3>
                <button @click="open = false"
                        class="w-9 h-9 rounded-full bg-[#FAF8F5] text-[#232B28] font-bold flex items-center justify-center text-lg hover:bg-[#E2EFEA] transition-colors">
                    ✕
                </button>
            </div>

            {{ $slot }}
        </div>
    </div>

</div>
