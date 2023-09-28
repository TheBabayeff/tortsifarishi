<?php

namespace App\Filament\Resources\Shop\ReasonResource\Pages;

use App\Filament\Resources\Shop\ReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReason extends EditRecord
{
    protected static string $resource = ReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
