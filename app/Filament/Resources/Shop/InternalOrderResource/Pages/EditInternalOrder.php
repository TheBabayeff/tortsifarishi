<?php

namespace App\Filament\Resources\Shop\InternalOrderResource\Pages;

use App\Filament\Resources\Shop\InternalOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInternalOrder extends EditRecord
{
    protected static string $resource = InternalOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
