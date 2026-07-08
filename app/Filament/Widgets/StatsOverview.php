<?php

namespace App\Filament\Widgets;

use App\Models\BankAccount;
use App\Models\Transaction;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Override;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    #[Override]
    protected function getColumns(): int|array|null
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 4,
        ];
    }

    protected function getStats(): array
    {

        $start_date = $this->filters['startDate'] ?? null;
        $end_date = $this->filters['endDate'] ?? null;

        $user = filament()->auth()->user();

        $accounts = BankAccount::select('id', 'name', 'balance')->get();
        $transactions = Transaction::query()
            ->when($start_date, fn($query) => $query->whereDate('date', '>=', $start_date))
            ->when($end_date, fn($query) => $query->whereDate('date', '<=', $end_date))
            ->get();

        $total_balance = Number::currency($this->calculateTotalBalance($accounts), $user->currency, $user->locale);
        $total_expenses = Number::currency(self::calculateExpenses($transactions), $user->currency, $user->locale);
        $total_income = Number::currency(self::calculateIncomes($transactions), $user->currency, $user->locale);
        $total_cash_flow = Number::currency(self::calculateCashFlow($transactions), $user->currency, $user->locale);
        $stats = [];



        $stats[] = Stat::make('Total Balance', $total_balance)
            ->description('Current account balance')
            ->descriptionIcon(Heroicon::OutlinedWallet, IconPosition::Before)
            ->color($total_balance >= 0 ? 'success' : 'danger')
            ->icon(Heroicon::OutlinedBanknotes);

        $stats[] = Stat::make('Total Expenses', $total_expenses)
            ->description('Money spent')
            ->descriptionIcon(Heroicon::OutlinedArrowTrendingDown, IconPosition::Before)
            ->color('danger')
            ->icon(Heroicon::OutlinedArrowUpCircle);

        $stats[] = Stat::make('Total Income', $total_income)
            ->description('Money received')
            ->descriptionIcon(Heroicon::OutlinedArrowTrendingUp, IconPosition::Before)
            ->color('success')
            ->icon(Heroicon::OutlinedArrowDownCircle);

        $stats[] = Stat::make('CashFlow', $total_cash_flow)
            ->description($total_cash_flow >= 0 ? 'Positive cash flow' : 'Negative cash flow')
            ->descriptionIcon(
                $total_cash_flow >= 0
                    ? Heroicon::OutlinedArrowTrendingUp
                    : Heroicon::OutlinedArrowTrendingDown,
                IconPosition::Before
            )
            ->color($total_cash_flow >= 0 ? 'success' : 'danger')
            ->icon(Heroicon::OutlinedScale);

        foreach ($accounts as $account) {
            $stats[] = Stat::make($account->name, Number::currency($account->balance, $user->currency, $user->locale));
        }
        return $stats;
    }

    protected static function calculateTotalBalance(Collection $accounts): float
    {
        $total_balance = 0;
        foreach ($accounts as $account) {
            $total_balance += $account->balance;
        }

        return $total_balance;
    }

    protected static function calculateExpenses(Collection $transactions): float
    {
        $total_expenses = 0;
        foreach ($transactions as $expense) {
            if ($expense->amount < 0) {
                $total_expenses += abs($expense->amount);
            }
        }

        return $total_expenses;
    }

    protected static function calculateIncomes(Collection $transactions): float
    {
        $total_incomes = 0;
        foreach ($transactions as $income) {
            if ($income->amount > 0) {
                $total_incomes = $income->amount;
            }
        }

        return $total_incomes;
    }

    protected static function calculateCashFlow(Collection $transactions): float
    {
        return self::calculateIncomes($transactions) - self::calculateExpenses($transactions);
    }
}
