<?php

namespace App\Filament\Resources\Tickets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsTable
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
                TextColumn::make('subject')
                    ->label('موضوع')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('department')
                    ->label('دپارتمان')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sales' => 'فروش',
                        'warranty' => 'گارانتی',
                        default => 'پشتیبانی',
                    }),
                TextColumn::make('priority')
                    ->label('اولویت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'low' => 'کم',
                        'high' => 'زیاد',
                        default => 'متوسط',
                    }),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'answered' => 'پاسخ داده شده',
                        'closed' => 'بسته',
                        default => 'باز',
                    }),
                TextColumn::make('messages_count')
                    ->label('پیام‌ها')
                    ->counts('messages'),
                TextColumn::make('updated_at')
                    ->label('آخرین فعالیت')
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(['open' => 'باز', 'answered' => 'پاسخ داده شده', 'closed' => 'بسته']),
                SelectFilter::make('department')
                    ->label('دپارتمان')
                    ->options(['support' => 'پشتیبانی', 'sales' => 'فروش', 'warranty' => 'گارانتی']),
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
