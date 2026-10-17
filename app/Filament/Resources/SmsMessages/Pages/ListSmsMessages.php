<?php

namespace App\Filament\Resources\SmsMessages\Pages;

use App\Filament\Resources\SmsMessages\SmsMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSmsMessages extends ListRecords
{
    protected static string $resource = SmsMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
