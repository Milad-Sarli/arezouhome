<?php

namespace App\Filament\Resources\Cities\Tables;

use App\Models\Province;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('province.name')
                    ->label('استان')
                    ->badge()
                    ->sortable(),
                ToggleColumn::make('status')
                    ->label('فعال'),
            ])
            ->filters([
                SelectFilter::make('province_id')
                    ->label('استان')
                    ->options(fn () => Province::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
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
