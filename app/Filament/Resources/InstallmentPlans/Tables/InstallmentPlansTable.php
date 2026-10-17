<?php

namespace App\Filament\Resources\InstallmentPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InstallmentPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),
                TextColumn::make('down_payment_percent')
                    ->label('پیش‌پرداخت')
                    ->suffix('٪'),
                TextColumn::make('months')
                    ->label('اقساط')
                    ->suffix(' ماه'),
                TextColumn::make('fee_percent')
                    ->label('کارمزد')
                    ->suffix('٪'),
                ToggleColumn::make('status')
                    ->label('فعال'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
