@props([
    'group' => [],
])

@php
    $containerClass = $group['container_class'] ?? '';
@endphp

<div class="category-group {{ $containerClass }}">
    <div class="category-side-label">
        <span class="ar">{{ $group['label_ar'] ?? '' }}</span>
        <span class="en">{{ $group['label_en'] ?? '' }}</span>
    </div>
    <div>
        @foreach($group['items'] ?? [] as $item)
            <x-services.service-row :item="$item" />
        @endforeach
    </div>
</div>
