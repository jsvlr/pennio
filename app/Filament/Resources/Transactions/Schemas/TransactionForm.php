<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Enums\TransactionType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required(),

                Group::make([
                    ToggleButtons::make('type')
                        ->required()
                        ->options(TransactionType::class)
                        ->default(TransactionType::Expense)
                        ->inline()
                        ->afterStateHydrated(function (ToggleButtons $component, $state, $record) {
                            if ($record && $record->amount !== null) {
                                $component->state($record->amount >= 0 ? TransactionType::Income : TransactionType::Expense);
                            }
                        })
                        ->live(),

                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->prefix(fn (Get $get) => $get('type') === TransactionType::Income ? '+' : '-')
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            if ($state !== null) {
                                $component->state(abs($state));
                            }
                        })
                        ->dehydrateStateUsing(function ($state, Get $get) {
                            $amount = abs((float) $state);

                            return $get('type') === TransactionType::Income ? $amount : -$amount;
                        }),

                ])->columns(2),

                TextInput::make('description')
                    ->required(),

                Select::make('bank_account_id')
                    ->relationship(
                        'bankAccount',
                        'name'
                    ),

                Select::make('category_id')
                    ->relationship(
                        'category',
                        'name'
                    ),
                Select::make('budget_id')
                    ->relationship(
                        'budget',
                        'name'
                    ),

                Textarea::make('note')
                    ->columnSpanFull(),
            ]);
    }
}
