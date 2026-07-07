<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ExpensesPieChart;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Override;

class Charts extends Page
{
    protected string $view = 'filament.pages.charts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartPie;

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            ExpensesPieChart::class,
        ];
    }
}
