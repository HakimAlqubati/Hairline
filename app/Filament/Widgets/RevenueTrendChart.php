<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueTrendChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected ?string $maxHeight = '180px';

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('admin.charts.revenue_trend');
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(function (int $monthsAgo) {
            return Carbon::now()->subMonths($monthsAgo);
        });

        $labels = $months->map(fn (Carbon $date) => $date->translatedFormat('M Y'))->toArray();

        $data = $months->map(function (Carbon $date) {
            return (float) Payment::query()
                ->where('status', 'success')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
        })->toArray();

        // If no payment records found, fallback to bookings total_amount
        if (array_sum($data) === 0.0) {
            $data = $months->map(function (Carbon $date) {
                return (float) Booking::query()
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->whereYear('booking_date', $date->year)
                    ->whereMonth('booking_date', $date->month)
                    ->sum('total_amount');
            })->toArray();
        }

        return [
            'datasets' => [
                [
                    'label' => __('admin.charts.revenue_label'),
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#E11D48',
                    'backgroundColor' => 'rgba(225, 29, 72, 0.12)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
