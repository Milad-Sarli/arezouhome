<?php

namespace App\Filament\Resources\Sliders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SliderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                TextInput::make('link')
                    ->label('لینک کلی')
                    ->nullable(),
                TextInput::make('sort')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
                Repeater::make('slides')
                    ->label('اسلایدها')
                    ->relationship()
                    ->schema([
                        FileUpload::make('image')
                            ->label('تصویر')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->directory('sliders')
                            ->helperText('تمام‌عرض — حداکثر ۵ مگابایت، سایزهای کوچک خودکار ساخته می‌شوند')
                            ->required(),
                        TextInput::make('title')
                            ->label('عنوان'),
                        TextInput::make('link')
                            ->label('لینک'),
                        TextInput::make('button_title')
                            ->label('متن دکمه'),
                        TextInput::make('button_link')
                            ->label('لینک دکمه'),
                        TextInput::make('sort')
                            ->label('ترتیب')
                            ->numeric()
                            ->default(0),
                        Toggle::make('status')
                            ->label('فعال')
                            ->default(true),
                        RichEditor::make('description')
                            ->label('توضیح')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
