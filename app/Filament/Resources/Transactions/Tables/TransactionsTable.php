<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Enums\TransactionType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money(filament()->auth()->user()->currency)
                    ->sortable()
                    ->badge()
                    ->color(fn($state): string => $state >= 0 ? 'success' : 'danger')
                    ->icon(fn($state): string => $state >= 0 ? 'heroicon-o-arrow-up' : 'heroicon-o-arrow-down'),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('bankAccount.name')
                    ->searchable(true),
                TextColumn::make('category.name')
                    ->searchable(true),
                TextColumn::make('budget.name')
                    ->searchable(true),
                TextColumn::make('note')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Transaction Type')
                    ->options(TransactionType::class)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === TransactionType::Income->value,
                                fn(Builder $query): Builder => $query->where('amount', '>=', 0),
                            )
                            ->when(
                                $data['value'] === TransactionType::Expense->value,
                                fn(Builder $query): Builder => $query->where('amount', '<', 0),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
