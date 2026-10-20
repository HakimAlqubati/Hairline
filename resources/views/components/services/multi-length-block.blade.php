@props([
    'block' => [],
])

@php
    $containerClass = $block['container_class'] ?? '';
    $arTitleClass = $block['ar_title_class'] ?? '';
    $enTitleClass = $block['en_title_class'] ?? '';
    $gapClass = $block['gap_class'] ?? '';
@endphp

<div class="multi-length-block {{ $containerClass }}">
    <div class="multi-length-title">
        <span class="ar-name {{ $arTitleClass }}">{{ $block['ar_name'] ?? '' }}</span>
        <span class="en-name {{ $enTitleClass }}">{{ $block['en_name'] ?? '' }}</span>
    </div>
    <div class="length-rows-list {{ $gapClass }}">
        @foreach($block['lengths'] ?? [] as $len)
            <div class="length-row-item service-row">
                <div class="length-label">
                    <span class="ar {{ $len['ar_class'] ?? '' }}">{{ $len['ar'] ?? '' }}</span>
                    <span class="en">{{ $len['en'] ?? '' }}</span>
                </div>
                <span class="price-badge">{{ $len['price'] ?? '' }}</span>
            </div>
        @endforeach
    </div>
</div>
