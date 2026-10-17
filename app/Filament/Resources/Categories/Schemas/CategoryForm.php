<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('نامک (slug)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('parent_id')
                    ->label('دسته والد')
                    ->options(fn () => Category::whereNull('parent_id')->pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('sort')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                FileUpload::make('image')
                    ->label('تصویر')
                    ->image()
                    ->imageEditor()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)
                    ->directory('categories'),
                FileUpload::make('banner')
                    ->label('بنر')
                    ->image()
                    ->imageEditor()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)
                    ->directory('categories/banners')
                    ->helperText('بنر عریض بالای صفحه دسته'),
                Toggle::make('is_important')
                    ->label('مهم (نمایش در صفحه اصلی)'),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),
            ]);
    }
}
