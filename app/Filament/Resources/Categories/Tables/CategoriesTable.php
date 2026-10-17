<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('تصویر')
                    ->circular(),
                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),
                TextColumn::make('parent.title')
                    ->label('والد')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('products_count')
                    ->label('تعداد کالا')
                    ->counts('products')
                    ->sortable(),
                TextColumn::make('sort')
                    ->label('ترتیب')
                    ->sortable()
                    ->toggleable(),
                ToggleColumn::make('is_important')
                    ->label('مهم'),
                ToggleColumn::make('status')
                    ->label('فعال'),
                TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
