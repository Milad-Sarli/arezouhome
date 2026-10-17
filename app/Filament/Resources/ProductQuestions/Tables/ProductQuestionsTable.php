<?php

namespace App\Filament\Resources\ProductQuestions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductQuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('کالا')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable(),
                IconColumn::make('is_answered')
                    ->label('پاسخ داده شده')
                    ->boolean(),
                IconColumn::make('status')
                    ->label('نمایش')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('ثبت')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('به‌روزرسانی')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
