<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('مشتری')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('کالا')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('rating')
                    ->label('امتیاز')
                    ->suffix(' / ۵')
                    ->sortable(),
                TextColumn::make('body')
                    ->label('متن')
                    ->limit(60)
                    ->searchable(),
                ToggleColumn::make('status')
                    ->label('تأیید'),
                TextColumn::make('created_at')
                    ->label('ثبت')
                    ->jalaliDate()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([1 => 'تأیید شده', 0 => 'در انتظار']),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('toggleStatus')
                    ->label(fn ($record) => $record->status ? 'لغو تأیید' : 'تأیید')
                    ->action(fn ($record) => $record->update(['status' => ! $record->status])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
