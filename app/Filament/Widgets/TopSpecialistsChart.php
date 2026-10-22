<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Specialist;
use Filament\Widgets\ChartWidget;

class TopSpecialistsChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected ?string $maxHeight = '180px';

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('admin.charts.top_specialists');
    }

    protected function getData(): array
    {
        $specialists = Specialist::query()
            ->where('is_active', true)
            ->withCount('bookingItems')
            ->orderByDesc('booking_items_count')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];

        foreach ($specialists as $specialist) {
            $labels[] = $specialist->name;
            $data[] = (int) $specialist->booking_items_count;
        }

        if (empty($labels)) {
            $labels = [__('admin.charts.no_data')];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => __('admin.charts.appointments_count'),
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(225, 29, 72, 0.75)',   // Rose
                        'rgba(147, 51, 234, 0.75)',  // Purple
                        'rgba(13, 148, 136, 0.75)',  // Teal
                        'rgba(245, 158, 11, 0.75)',  // Amber
                        'rgba(59, 130, 246, 0.75)',  // Blue
                    ],
                    'borderWidth' => 2,
                    'borderColor' => '#FFFFFF',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'boxWidth' => 12,
                        'padding' => 8,
                    ],
                ],
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
