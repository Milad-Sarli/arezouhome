<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Support\MoneyComponents;
use App\Models\Brand;
use App\Models\Color;
use App\Models\ProductAttr;
use App\Models\Warranty;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('مشخصات اصلی')
                        ->schema([
                            TextInput::make('name')
                                ->label('نام کالا')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('slug')
                                ->label('نامک (slug)')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                            Select::make('brand_id')
                                ->label('برند')
                                ->options(fn () => Brand::pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')->label('نام برند')->required(),
                                    TextInput::make('slug')->label('نامک')->required(),
                                ]),
                            Select::make('categories')
                                ->label('دسته‌بندی‌ها')
                                ->multiple()
                                ->relationship('categories', 'title')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('warranty_id')
                                ->label('گارانتی')
                                ->options(fn () => Warranty::pluck('title', 'id'))
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            TextInput::make('model')
                                ->label('مدل'),
                            TextInput::make('product_code')
                                ->label('کد کالا')
                                ->unique(ignoreRecord: true),
                            Toggle::make('in_stock')->label('موجود')->default(true),
                            Toggle::make('status')->label('فعال')->default(true),
                            Toggle::make('is_featured')->label('ویژه'),
                            Toggle::make('is_special')->label('پیشنهاد خاص'),
                        ])->columns(2),
                    Step::make('قیمت و موجودی')
                        ->schema([
                            MoneyComponents::tomanInput('price_rial', 'قیمت', true),
                            MoneyComponents::tomanInput('discount_rial', 'تخفیف'),
                            TextInput::make('quantity')
                                ->label('موجودی')
                                ->numeric()
                                ->default(0),
                            TextInput::make('weight_gram')
                                ->label('وزن (گرم)')
                                ->numeric()
                                ->nullable(),
                            TextInput::make('length_cm')->label('طول (cm)')->numeric()->nullable(),
                            TextInput::make('width_cm')->label('عرض (cm)')->numeric()->nullable(),
                            TextInput::make('height_cm')->label('ارتفاع (cm)')->numeric()->nullable(),
                        ])->columns(2),
                    Step::make('تنوع‌ها (رنگ)')
                        ->schema([
                            Repeater::make('variants')
                                ->label('تنوع‌ها')
                                ->relationship()
                                ->schema([
                                    Select::make('color_id')
                                        ->label('رنگ')
                                        ->options(fn () => Color::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload(),
                                    MoneyComponents::tomanInput('price_rial', 'قیمت (خالی = قیمت اصلی)'),
                                    TextInput::make('quantity')
                                        ->label('موجودی')
                                        ->numeric()
                                        ->default(0),
                                    TextInput::make('product_code')
                                        ->label('کد تنوع'),
                                    FileUpload::make('image')
                                        ->label('عکس')
                                        ->image()
                                        ->imageEditor()
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->maxSize(5120)
                                        ->directory('products/variants')
                                        ->helperText('حداکثر ۵ مگابایت'),
                                    Toggle::make('status')
                                        ->label('فعال')
                                        ->default(true),
                                ])
                                ->columns(2)
                                ->columnSpanFull(),
                        ]),
                    Step::make('مشخصات فنی')
                        ->schema([
                            Repeater::make('specs')
                                ->label('جدول مشخصات')
                                ->relationship()
                                ->schema([
                                    Select::make('product_attr_id')
                                        ->label('ویژگی')
                                        ->options(fn () => ProductAttr::where('type', 'spec')->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->distinct()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->validationMessages([
                                            'distinct' => 'این ویژگی قبلاً انتخاب شده است.',
                                        ]),
                                    TextInput::make('value')
                                        ->label('مقدار')
                                        ->required(),
                                ])
                                ->columns(2)
                                ->columnSpanFull(),
                        ]),
                    Step::make('توضیحات و سئو')
                        ->schema([
                            RichEditor::make('description')
                                ->label('توضیحات')
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsDirectory('products/content')
                                ->columnSpanFull(),
                            Textarea::make('meta_description')
                                ->label('توضیح متا')
                                ->columnSpanFull(),
                            TagsInput::make('keywords')
                                ->label('کلمات کلیدی')
                                ->columnSpanFull(),
                        ]),
                    Step::make('تصویر')
                        ->schema([
                            FileUpload::make('image')
                                ->label('تصویر اصلی')
                                ->image()
                                ->imageEditor()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(5120)
                                ->directory('products')
                                ->helperText('حداکثر ۵ مگابایت — سایزهای 320 تا 1280 خودکار ساخته می‌شوند'),
                        ]),
                    Step::make('واقعیت افزوده (AR)')
                        ->schema([
                            FileUpload::make('ar_model_url')
                                ->label('فایل 3D (GLB)')
                                ->acceptedFileTypes(['model/gltf-binary', '.glb'])
                                ->directory('products/ar')
                                ->maxSize(20480)
                                ->helperText('فایل glb تا 20MB — برای نمایش AR در اندروید'),
                            FileUpload::make('ar_ios_url')
                                ->label('فایل iOS (USDZ) - اختیاری')
                                ->acceptedFileTypes(['model/vnd.usdz+zip', '.usdz'])
                                ->directory('products/ar')
                                ->maxSize(20480)
                                ->helperText('برای Quick Look آیفون'),
                        ]),
                ])
                    ->skippable()
                    ->columnSpanFull(),
            ]);
    }
}
