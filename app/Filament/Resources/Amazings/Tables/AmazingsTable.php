<?php

namespace App\Filament\Resources\Amazings\Tables;

use App\Filament\Support\MoneyComponents;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AmazingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('کالا')
                    ->searchable(),
                TextColumn::make('percent')
                    ->label('درصد')
                    ->suffix('٪')
                    ->sortable(),
                MoneyComponents::tomanColumn('price_rial', 'قیمت ویژه'),
                TextColumn::make('end_at')
                    ->label('پایان')
                    ->jalaliDateTime()
                    ->sortable(),
                ToggleColumn::make('status')
                    ->label('فعال'),
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
