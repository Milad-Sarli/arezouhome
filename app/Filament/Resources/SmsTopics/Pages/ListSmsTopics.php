<?php

namespace App\Filament\Resources\SmsTopics\Pages;

use App\Filament\Resources\SmsTopics\SmsTopicResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSmsTopics extends ListRecords
{
    protected static string $resource = SmsTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
