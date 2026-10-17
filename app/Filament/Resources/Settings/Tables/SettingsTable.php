<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Filament\Resources\Settings\Schemas\SettingForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('تنظیم')
                    ->formatStateUsing(fn (string $state): string => SettingForm::keys()[$state] ?? $state),
                TextColumn::make('value')
                    ->label('مقدار')
                    ->limit(80),
                TextColumn::make('updated_at')
                    ->label('ویرایش')
                    ->jalaliDateTime()
                    ->sortable(),
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
