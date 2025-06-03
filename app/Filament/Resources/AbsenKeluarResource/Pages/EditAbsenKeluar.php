<?php

namespace App\Filament\Resources\AbsenKeluarResource\Pages;

use App\Filament\Resources\AbsenKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsenKeluar extends EditRecord
{
    protected static string $resource = AbsenKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
