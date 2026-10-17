<?php

namespace App\Filament\Resources\SmsMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SmsMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('topic.title')
                    ->label('موضوع')
                    ->badge(),
                TextColumn::make('body')
                    ->label('متن')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'sent' ? 'ارسال شده' : 'پیش‌نویس'),
                TextColumn::make('creator.name')
                    ->label('سازنده')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->jalaliDate()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(['draft' => 'پیش‌نویس', 'sent' => 'ارسال شده']),
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
