{{--
    Hairline SVG Logo Emblem (recreated from attached logo reference)
    @param string $size — 'sm' | 'md' | 'lg'
--}}
@php
    $classes = match($size ?? 'md') {
        'sm'  => 'w-8 h-8',
        'lg'  => 'w-20 h-20',
        default => 'w-12 h-12',
    };
@endphp
<svg viewBox="0 0 100 100" class="{{ $classes }}" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="50" r="45" stroke="#C5A373" stroke-width="3.2"/>
    <!-- Flowing hair strands matching the Hairline logo -->
    <path d="M42 7 C20 22, 14 58, 35 84" stroke="#C5A373" stroke-width="2.8" stroke-linecap="round"/>
    <path d="M36 16 C22 36, 22 64, 41 79" stroke="#C5A373" stroke-width="2.5" stroke-linecap="round"/>
    <path d="M48 11 C76 20, 88 52, 74 86" stroke="#C5A373" stroke-width="2.8" stroke-linecap="round"/>
    <path d="M44 18 C70 26, 80 56, 65 91" stroke="#C5A373" stroke-width="2.8" stroke-linecap="round"/>
    <path d="M40 26 C64 32, 72 58, 56 93" stroke="#C5A373" stroke-width="2.8" stroke-linecap="round"/>
    <!-- Face contour & neck line -->
    <path d="M35 33 C44 33, 49 35, 52 36 M35 33 C36 52, 45 64, 52 67 C56 68, 60 63, 63 59 M46 65 C49 73, 49 83, 47 93"
          stroke="#C5A373" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
