<?php

namespace App\Filament\Resources\ReturnRequests\Tables;

use App\Filament\Resources\ReturnRequests\Schemas\ReturnRequestForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReturnRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('مشتری')
                    ->searchable(),
                TextColumn::make('order.ref_code')
                    ->label('سفارش')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('کالا')
                    ->toggleable(),
                TextColumn::make('reason')
                    ->label('دلیل')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => ReturnRequestForm::returnReasons()[$state] ?? ($state ?? '—')),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ReturnRequestForm::returnStatuses()[$state] ?? $state),
                TextColumn::make('created_at')
                    ->label('ثبت')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(ReturnRequestForm::returnStatuses()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
