<?php

namespace App\Filament\Resources\AbsenMasukResource\Pages;

use App\Filament\Resources\AbsenMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsenMasuk extends EditRecord
{
    protected static string $resource = AbsenMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
