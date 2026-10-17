<?php

namespace App\Filament\Resources\Blogs\Schemas;

use App\Models\BlogCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class BlogForm
{
    public static function faSlug(string $title): string
    {
        $slug = preg_replace('/[\s_]+/u', '-', trim($title));
        $slug = trim($slug, '-');

        return mb_substr($slug, 0, 200);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('blog')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('محتوا')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('عنوان')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                if (filled($get('slug'))) {
                                                    return;
                                                }

                                                if (filled($state)) {
                                                    $set('slug', self::faSlug($state));
                                                }
                                            }),
                                        TextInput::make('slug')
                                            ->label('نامک (در آدرس)')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('فارسی با خط‌تیره، مثلا: راهنمای-خرید-یخچال'),
                                        Select::make('blog_category_id')
                                            ->label('دسته')
                                            ->options(fn () => BlogCategory::pluck('title', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->nullable(),
                                    ]),
                                RichEditor::make('description')
                                    ->label('متن مقاله')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('blog/content')
                                    ->columnSpanFull(),
                                Textarea::make('excerpt')
                                    ->label('خلاصه (اختیاری — خودکار ساخته می‌شود)')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->columnSpanFull(),
                                TagsInput::make('keywords')
                                    ->label('کلمات کلیدی')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('تصویر شاخص')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('تصویر شاخص')
                                    ->image()
                                    ->imageEditor()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->directory('blog')
                                    ->helperText('حداکثر ۵ مگابایت — سایزهای 320 تا 1280 خودکار ساخته می‌شوند')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('سئو')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('عنوان سئو (اختیاری — پیش‌فرض عنوان مقاله)')
                                    ->maxLength(70)
                                    ->helperText('حدود ۵۰ تا ۶۰ نویسه بهترین نمایش در گوگل را دارد')
                                    ->columnSpanFull(),
                                Textarea::make('meta_description')
                                    ->label('توضیح متا')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->helperText('حدود ۱۵۰ تا ۱۶۰ نویسه')
                                    ->columnSpanFull(),
                                TextInput::make('canonical_url')
                                    ->label('آدرس canonical (اختیاری)')
                                    ->url()
                                    ->nullable()
                                    ->helperText('فقط اگر همین مطلب با آدرس دیگری هم منتشر شده')
                                    ->columnSpanFull(),
                                Placeholder::make('serp_preview')
                                    ->label('پیش‌نمایش گوگل (نمونه)')
                                    ->content(
                                        new HtmlString(
                                            '<div dir="rtl" style="font-family:inherit">'
                                            .'<div style="color:#1a0dab;font-size:18px;line-height:1.4">عنوان مقاله | فروشگاه آرزو</div>'
                                            .'<div style="color:#006621;font-size:13px">arezouhome.test › blog › …</div>'
                                            .'<div style="color:#545454;font-size:13px;line-height:1.6">توضیح متای مقاله اینجا نمایش داده می‌شود…</div>'
                                            .'</div>'
                                        )
                                    )
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('انتشار')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        DateTimePicker::make('publish_at')
                                            ->label('زمان انتشار (شمسی)')
                                            ->jalali()
                                            ->nullable()
                                            ->helperText('خالی = پیش‌نویس بدون زمان‌بندی'),
                                        Toggle::make('status')
                                            ->label('منتشر شود'),
                                        Toggle::make('is_featured')
                                            ->label('ویژه (نمایش در صفحه اصلی)'),
                                        Placeholder::make('views_info')
                                            ->label('بازدید')
                                            ->content('شمارش بازدید پس از انتشار آغاز می‌شود'),
                                    ])
                                    ->columns(2),
                            ]),
                    ]),
            ]);
    }
}
