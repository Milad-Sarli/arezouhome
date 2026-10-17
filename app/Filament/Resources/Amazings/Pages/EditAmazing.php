<?php

namespace App\Filament\Resources\Amazings\Pages;

use App\Filament\Resources\Amazings\AmazingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAmazing extends EditRecord
{
    protected static string $resource = AmazingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
