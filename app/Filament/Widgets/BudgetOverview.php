<?php

namespace App\Filament\Widgets;

use App\Enums\BudgetType;
use App\Models\Budget;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class BudgetOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {

        $start_date = $this->filters['startDate'] ?? null;
        $end_date = $this->filters['endDate'] ?? null;

        $start_date = $start_date ? Carbon::parse($start_date) : now()->startOfMonth();
        $end_date = $end_date ? Carbon::parse($end_date) : now();

        $budgets = Budget::query()
            ->with(['transactions' => function ($query) use ($start_date, $end_date) {
                $query->whereDate('date', '>=', $start_date)
                    ->whereDate('date', '<=', $end_date);
            }])
            ->get();

        $stats = [];
        $diff_days = $start_date->diffInDays($end_date);

        foreach ($budgets as $budget) {
            $budget_amount = $this->calculateBudgetAmount($budget, $start_date, $end_date);
            $spent = $this->calculateSpent($budget);
            $percentage = $budget_amount > 0 ? ($spent / $budget_amount) * 100 : 0;
            $remaining = $budget_amount - $spent;

            $color = $this->getColorForPercentage($percentage);
            $icon = $this->getIconPercentage($percentage);
            $chart_data = $this->generateChartData($percentage);

            $stat = Stat::make(
                $budget->name,
                Number::percentage($percentage, 1)
            )
                ->description($this->formatBudgetDescription($budget, $spent, $budget_amount, $remaining))
                ->descriptionIcon($icon)
                ->color($color)
                ->chart($chart_data);

            $stats[] = $stat;
        }

        return $stats;
    }

    private function formatBudgetDescription(Budget $budget, float $spent, float $budget_amount, float $remaining): string
    {
        $type_label = $budget->type == BudgetType::Rollover ? '🔄' : '🔁';
        $currency = filament()->auth()->user()->currency;
        $locale = filament()->auth()->user()->locale;

        $spent_formatted = Number::currency($spent, $currency, $locale);
        $budget_formatted = Number::currency($budget_amount, $currency, $locale);
        $remaining_formatted = Number::currency($remaining, $currency, $locale);

        return "{$type_label} Spent: {$spent_formatted} / {$budget_formatted} * {$remaining_formatted} left";
    }

    private function getColorForPercentage(float $percentage): string
    {
        if ($percentage >= 100) {
            return 'danger';
        }

        if ($percentage >= 75) {
            return 'warning';
        }

        return 'success';
    }

    private function getIconPercentage(float $percentage): string
    {

        if ($percentage >= 100) {
            return 'heroicon-m-exclamation-triangle';
        }

        if ($percentage >= 75) {
            return 'heroicon-m-arrow-trending-up';
        }

        return 'heroicon-m-check-circle';
    }

    private function generateChartData(float $percentage): array
    {

        $percentage = min($percentage, 100);

        $filled_bars = (int) round($percentage / 10);
        $empty_bars = 10 - $filled_bars;

        $data = [];
        for ($i = 0; $i < $filled_bars; $i++) {
            $data[] = 100;
        }
        for ($i = 0; $i < $empty_bars; $i++) {
            $data[] = 10;
        }

        return $data;
    }

    private function calculateMonthsDifference(Carbon $start_date, Carbon $end_date): int
    {
        $diff = $start_date->copy()->startOfMonth()->diffInMonths($end_date->copy()->startOfMonth());

        return (int) $diff + 1;
    }

    private function calculatePreviousUnspent(Budget $budget, Carbon $start_date): float
    {

        $created_at = $budget->created_at;
        $start_of_period = $start_date->copy()->startOfMonth();

        if ($created_at >= $start_of_period) {
            return 0;
        }

        $previous_start_date = $created_at->copy()->startOfMonth();
        $previous_end_date = $start_of_period->copy()->subDay();

        $months_before_period = $this->calculateMonthsDifference($previous_start_date, $previous_end_date);
        $total_previous_budget = (float) $budget->amount * $months_before_period;

        $previous_spent = $budget->transactions()
            ->whereDate('date', '>=', $previous_start_date)
            ->whereDate('date', '<=', $previous_end_date)
            ->where('amount', '<', 0)
            ->sum('amount');

        $previous_spent = abs($previous_spent);

        return max(0, $total_previous_budget - $previous_spent);
    }

    private function calculateSpent(Budget $budget): float
    {
        return $budget->transactions
            ->filter(fn ($transaction) => $transaction->amount < 0)
            ->sum(fn ($transaction) => abs($transaction->amount));
    }

    private function calculateBudgetAmount(Budget $budget, Carbon $start_date, Carbon $end_date): float
    {

        $months_diff = $this->calculateMonthsDifference($start_date, $end_date);
        if ($budget->type === BudgetType::Reset) {
            $multiplier = max(1, $months_diff);

            return (float) $budget->amount * $multiplier;
        }

        $total_budget = (float) $budget->amount * $months_diff;
        $previous_unspent = $this->calculatePreviousUnspent($budget, $start_date);

        return $total_budget + $previous_unspent;
    }
}
