<?php

namespace App\Filament\Resources\ProductAttrs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductAttrsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('کاربرد')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'variant' ? 'تنوع' : 'مشخصات فنی'),
                TextColumn::make('category.title')
                    ->label('دسته')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('values.value')
                    ->label('مقادیر')
                    ->badge(),
                ToggleColumn::make('is_important')
                    ->label('مهم'),
                ToggleColumn::make('is_filterable')
                    ->label('فیلتر'),
                ToggleColumn::make('status')
                    ->label('فعال'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('کاربرد')
                    ->options(['spec' => 'مشخصات فنی', 'variant' => 'تنوع']),
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
