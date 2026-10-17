<?php

namespace App\Filament\Resources\Blogs\Tables;

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

class BlogsTable
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
                    ->searchable()
                    ->limit(50),
                TextColumn::make('category.title')
                    ->label('دسته')
                    ->badge(),
                TextColumn::make('publish_at')
                    ->label('انتشار')
                    ->jalaliDateTime()
                    ->sortable(),
                TextColumn::make('views')
                    ->label('بازدید')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_featured')
                    ->label('ویژه'),
                ToggleColumn::make('status')
                    ->label('منتشر'),
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
