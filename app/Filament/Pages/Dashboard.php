<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BudgetOverview;
use App\Filament\Widgets\StatsOverview;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Override;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Squares2x2;

    #[Override]
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            BudgetOverview::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filters')
                    ->columnSpanFull()
                    ->components([
                        Actions::make([
                            Action::make('Last Week')
                                ->action(function (Set $set) {
                                    $set('startDate', now()->subWeek());
                                    $set('endDate', now());
                                }),
                            Action::make('Last Month')
                                ->action(function (Set $set) {
                                    $set('startDate', now()->subMonth());
                                    $set('endDate', now());
                                }),
                            Action::make('Last Year')
                                ->action(function (Set $set) {
                                    $set('startDate', now()->subYear());
                                    $set('endDate', now());
                                }),
                            Action::make('Reset')
                                ->color('danger')
                                ->icon(Heroicon::XMark)
                                ->action(function (Set $set) {
                                    $set('startDate', null);
                                    $set('endDate', null);
                                }),
                        ]),
                    ]),
                Section::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        DatePicker::make('startDate')
                            ->live()
                            ->maxDate(fn (Get $get) => $get('endDate') ?? now())
                            ->default(now()->subWeek()),
                        DatePicker::make('endDate')
                            ->live()
                            ->minDate(fn (Get $get) => $get('startDate') ?? null)
                            ->maxDate(now())
                            ->default(now()),

                    ]),

            ]);
    }
}
