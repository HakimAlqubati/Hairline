<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class BookingsStatusChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '180px';

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('admin.charts.bookings_status');
    }

    protected function getData(): array
    {
        $statuses = [
            'confirmed' => ['label' => __('admin.statuses.confirmed'), 'color' => '#3B82F6'],
            'completed' => ['label' => __('admin.statuses.completed'), 'color' => '#10B981'],
            'pending' => ['label' => __('admin.statuses.pending'), 'color' => '#F59E0B'],
            'in_progress' => ['label' => __('admin.statuses.in_progress'), 'color' => '#8B5CF6'],
            'cancelled' => ['label' => __('admin.statuses.cancelled'), 'color' => '#EF4444'],
        ];

        $counts = Booking::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($statuses as $statusKey => $meta) {
            $total = (int) ($counts[$statusKey] ?? 0);
            if ($total > 0 || empty($counts)) {
                $labels[] = $meta['label'];
                $data[] = $total;
                $colors[] = $meta['color'];
            }
        }

        if (empty($data) || array_sum($data) === 0) {
            $labels = [__('admin.statuses.confirmed'), __('admin.statuses.pending')];
            $data = [1, 0];
            $colors = ['#3B82F6', '#F59E0B'];
        }

        return [
            'datasets' => [
                [
                    'label' => __('admin.charts.bookings_count'),
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 2,
                    'borderColor' => '#FFFFFF',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'boxWidth' => 12,
                        'padding' => 10,
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
