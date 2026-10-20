{{-- Vertical Guilloche Wave Lines (Server Rendered SVG) --}}
<svg class="wave-bg-layer" viewBox="0 0 840 1188" preserveAspectRatio="none" aria-hidden="true">
    @for ($i = 0; $i < 24; $i++)
        @php
            // Right vertical ribbon wave lines
            $xTopR = 840 - ($i * 8.5);
            $c1xR = 670 + ($i * 4.5);
            $c1yR = 320 + ($i * 12);
            $c2xR = 890 - ($i * 10);
            $c2yR = 760 - ($i * 10);
            $xBotR = 620 + ($i * 9.5);

            // Left vertical ribbon wave lines
            $xTopL = -25 + ($i * 9.2);
            $c1xL = 210 - ($i * 6.5);
            $c1yL = 290 + ($i * 11);
            $c2xL = -40 + ($i * 10.5);
            $c2yL = 750 - ($i * 9);
            $xBotL = 240 - ($i * 9.5);
        @endphp
        <path d="M {{ $xTopR }} -20 C {{ $c1xR }} {{ $c1yR }}, {{ $c2xR }} {{ $c2yR }}, {{ $xBotR }} 1220" fill="none" stroke="#d5c6aa" stroke-width="0.7" opacity="0.52"/>
        <path d="M {{ $xTopL }} -20 C {{ $c1xL }} {{ $c1yL }}, {{ $c2xL }} {{ $c2yL }}, {{ $xBotL }} 1220" fill="none" stroke="#d5c6aa" stroke-width="0.75" opacity="0.62"/>
    @endfor
</svg>
