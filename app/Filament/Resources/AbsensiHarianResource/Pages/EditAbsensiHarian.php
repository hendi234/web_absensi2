<?php

namespace App\Filament\Resources\AbsensiHarianResource\Pages;

use App\Filament\Resources\AbsensiHarianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsensiHarian extends EditRecord
{
    protected static string $resource = AbsensiHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
