<?php

namespace App\Filament\Resources\NewsletterSubscriptions;

use App\Filament\Resources\NewsletterSubscriptions\Pages\ListNewsletterSubscriptions;
use App\Filament\Resources\NewsletterSubscriptions\Tables\NewsletterSubscriptionsTable;
use App\Models\NewsletterSubscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NewsletterSubscriptionResource extends Resource
{
    protected static ?string $model = NewsletterSubscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static string|UnitEnum|null $navigationGroup = 'پیامک';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'phone';

    public static function getModelLabel(): string
    {
        return 'عضو خبرنامه';
    }

    public static function getPluralModelLabel(): string
    {
        return 'اعضای خبرنامه';
    }

    public static function table(Table $table): Table
    {
        return NewsletterSubscriptionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletterSubscriptions::route('/'),
        ];
    }
}
