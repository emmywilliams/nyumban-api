<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Property;
use Carbon\Carbon;

class PropertiesChart extends ChartWidget
{
    protected ?string $heading = 'Properties Growth';

    // protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $data = collect(range(0, 6))->map(function ($daysAgo) {
            return Property::whereDate(
                'created_at',
                Carbon::now()->subDays($daysAgo)
            )->count();
        })->reverse();

        return [
            'datasets' => [
                [
                    'label' => 'Properties',
                    'data' => $data,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => ['6d', '5d', '4d', '3d', '2d', '1d', 'Today'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
