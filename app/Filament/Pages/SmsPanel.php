<?php

namespace App\Filament\Pages;

use App\Services\Sms\IppanelEdgeService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Throwable;
use UnitEnum;

class SmsPanel extends Page
{
    protected string $view = 'filament.pages.sms-panel';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'پیامک';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'کنسول پیامک';

    public array $credit = [];

    public array $numbers = [];

    public ?string $serviceError = null;

    public function mount(): void
    {
        try {
            $service = app(IppanelEdgeService::class);
            $service->assertConfigured();
            $this->credit = $service->credit();
            $this->numbers = array_slice($service->numbers(), 0, 20);
        } catch (Throwable $e) {
            $this->serviceError = $e->getMessage();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTest')
                ->label('ارسال پیامک تستی')
                ->schema([
                    TextInput::make('phone')->label('موبایل')->required(),
                    Textarea::make('message')->label('متن')->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        app(IppanelEdgeService::class)->sendWebservice($data['message'], [$data['phone']]);
                        Notification::make()->success()->title('پیامک ارسال شد.')->send();
                    } catch (Throwable $e) {
                        Notification::make()->danger()->title('خطا: '.$e->getMessage())->send();
                    }
                }),
            Action::make('sendPattern')
                ->label('ارسال پترن')
                ->schema([
                    TextInput::make('code')->label('کد پترن')->required(),
                    TextInput::make('phone')->label('موبایل')->required(),
                    Textarea::make('params')
                        ->label('پارامترها (JSON)')
                        ->default('{}')
                        ->helperText('مثلا: {"name":"علی","code":"1234"}'),
                ])
                ->action(function (array $data): void {
                    try {
                        $params = json_decode($data['params'] ?? '{}', true) ?? [];
                        app(IppanelEdgeService::class)->sendPattern($data['code'], $data['phone'], $params);
                        Notification::make()->success()->title('پترن ارسال شد.')->send();
                    } catch (Throwable $e) {
                        Notification::make()->danger()->title('خطا: '.$e->getMessage())->send();
                    }
                }),
        ];
    }
}
