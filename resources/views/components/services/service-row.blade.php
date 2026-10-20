@props([
    'item' => [],
])

@php
    $extraClass = $item['extra_class'] ?? '';
@endphp

<div class="service-row {{ $extraClass }}">
    <div class="service-info">
        <span class="ar-name">{{ $item['ar_name'] ?? '' }}</span>
        @if(!empty($item['ar_sub']))
            <span class="ar-sub">{{ $item['ar_sub'] }}</span>
        @endif
        @if(!empty($item['en_name']))
            <span class="en-name">{{ $item['en_name'] }}</span>
        @endif
    </div>
    <span class="price-badge">{{ $item['price'] ?? '' }}</span>
</div>
