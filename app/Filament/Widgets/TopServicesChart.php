<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\BookingItem;
use App\Models\Service;
use Filament\Widgets\ChartWidget;

class TopServicesChart extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '180px';

    protected int|string|array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('admin.charts.top_services');
    }

    protected function getData(): array
    {
        $topItems = BookingItem::query()
            ->selectRaw('service_id, count(*) as total')
            ->groupBy('service_id')
            ->orderByDesc('total')
            ->with('service')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];

        foreach ($topItems as $item) {
            if ($item->service) {
                $labels[] = $item->service->name;
                $data[] = (int) $item->total;
            }
        }

        // If no booking items yet, show top featured services as baseline
        if (empty($data)) {
            $services = Service::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(5)
                ->get();

            foreach ($services as $service) {
                $labels[] = $service->name;
                $data[] = 0;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => __('admin.charts.requests_count'),
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(244, 63, 94, 0.85)',
                        'rgba(139, 92, 246, 0.85)',
                        'rgba(59, 130, 246, 0.85)',
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                'x' => [
                    'ticks' => [
                        'maxRotation' => 20,
                        'minRotation' => 0,
                    ],
                ],
            ],
        ];
    }
}
