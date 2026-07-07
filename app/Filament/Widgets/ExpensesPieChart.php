<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class ExpensesPieChart extends ChartWidget
{
    protected ?string $heading = 'Expenses by Category';

    protected bool $isCollapsible = true;

    protected ?string $description = 'Expenses Pie Chart Description';

    protected function getData(): array
    {
        $start_date = $this->filter['startDate'] ?? null;
        $end_date = $this->filter['endDate'] ?? null;

        $data = Transaction::query()
            ->with('category')
            ->where('amount', '<', 0)
            ->whereNotNull('category_id')
            ->when($start_date, fn ($query) => $query->whereDate('date', '>=', $start_date))
            ->when($end_date, fn ($query) => $query->whereDate('date', '<=', $end_date))
            ->get()
            ->groupBy('category.name')
            ->map(fn ($transactions) => abs($transactions->sum('amount')));

        return [
            'datasets' => [
                [
                    'label' => 'expenses',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => [
                        '#ef4444',
                        '#f97316',
                        '#f59e0b',
                        '#84cc16',
                        '#10b981',
                        '#06b6d4',
                        '#3b82f6',
                        '#6366f1',
                        '#8b5cf6',
                        '#d946ef',
                        '#f43f5e',
                        '#64748b',
                    ],
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
