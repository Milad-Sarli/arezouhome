<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('موبایل')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('نقش‌ها')
                    ->badge(),
                TextColumn::make('orders_count')
                    ->label('سفارش‌ها')
                    ->counts('orders')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('فعال'),
                TextColumn::make('created_at')
                    ->label('عضویت')
                    ->jalaliDate()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
