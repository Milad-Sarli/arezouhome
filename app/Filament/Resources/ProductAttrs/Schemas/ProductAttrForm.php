<?php

namespace App\Filament\Resources\ProductAttrs\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductAttrForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام ویژگی')
                    ->required()
                    ->maxLength(255)
                    ->helperText('مثلا: ظرفیت، رده مصرف انرژی، نوع موتور'),
                Select::make('type')
                    ->label('کاربرد')
                    ->options([
                        'spec' => 'مشخصات فنی',
                        'variant' => 'تنوع (واریانت)',
                    ])
                    ->default('spec')
                    ->required(),
                Select::make('category_id')
                    ->label('مخصوص دسته')
                    ->options(fn () => Category::pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('خالی = عمومی برای همه دسته‌ها'),
                TextInput::make('sort')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_important')
                    ->label('مهم (نمایش خلاصه)'),
                Toggle::make('is_filterable')
                    ->label('قابل فیلتر در فروشگاه'),
                Toggle::make('status')
                    ->label('فعال')
                    ->default(true),
                Repeater::make('values')
                    ->label('مقادیر')
                    ->relationship()
                    ->schema([
                        TextInput::make('value')
                            ->label('مقدار')
                            ->required(),
                        TextInput::make('sort')
                            ->label('ترتیب')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
