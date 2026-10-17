{{--
    Booking card partial — used in both Profile and post-booking session display.
    @param array $booking
--}}
@php
    $isUpcoming  = ($booking['status'] ?? 'confirmed') === 'upcoming';
    $isConfirmed = ($booking['status'] ?? 'confirmed') === 'confirmed';
    $isCompleted = ($booking['status'] ?? '') === 'completed';

    $statusLabel = $booking['status_label_ar']
        ?? ($isConfirmed ? 'مؤكد' : ($isCompleted ? 'مكتمل' : 'قادم'));

    $statusClass = $isCompleted
        ? 'bg-gray-100 text-gray-600'
        : ($isUpcoming || $isConfirmed ? 'bg-[#E2EFEA] text-[#4A5D54]' : 'bg-gray-100 text-gray-600');

    $refCode  = $booking['ref_code'] ?? ($booking['ref_code'] ?? '#HL-????');
    $dateText = $booking['date_label'] ?? '';
    $time     = $booking['time'] ?? '';
    $total    = $booking['totals']['total_price'] ?? $booking['total_sar'] ?? 0;
    $name     = $booking['client_name'] ?? '';
    $services = $booking['services'] ?? [];
@endphp
<div class="bg-[#FAF8F5] rounded-2xl p-4 border border-[#C5A373]/40 shadow-xs space-y-2">
    <div class="flex items-center justify-between">
        <span class="font-en-brand font-bold text-base text-[#4A5D54]">{{ $refCode }}</span>
        <span class="text-xs font-bold {{ $statusClass }} px-2.5 py-1 rounded-full">{{ $statusLabel }}</span>
    </div>

    <p class="text-base font-bold text-[#232B28]">{{ $dateText }}{{ $time ? ' — ' . $time : '' }}</p>

    @if(!empty($name))
        <p class="text-sm text-[#5C6B64]">{{ $name }}</p>
    @endif

    <p class="text-sm text-[#5C6B64]">
        @if(is_array($services) && count($services) > 0)
            @if(isset($services[0]['name']))
                {{ collect($services)->pluck('name')->join(' ، ') }}
            @elseif(is_string($services[0]))
                {{ implode(' ، ', $services) }}
            @endif
        @endif
    </p>

    <div class="pt-2 border-t border-[#EAEAEA] flex justify-between items-center text-sm">
        <span class="text-[#5C6B64]">
            {{ ($booking['booking_type'] ?? 'salon') === 'home' ? '🏠 خدمة منزلية' : '🏪 في الصالون' }}
        </span>
        <span class="font-bold text-[#4A5D54]">{{ $total }} SAR</span>
    </div>
</div>
