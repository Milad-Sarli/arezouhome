<?php

namespace App\Filament\Resources\SmsTopics\Pages;

use App\Filament\Resources\SmsTopics\SmsTopicResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSmsTopic extends EditRecord
{
    protected static string $resource = SmsTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
