<?php

namespace App\Filament\Resources\LibroReclamaciones\Pages;

use App\Filament\Resources\LibroReclamaciones\LibroReclamacionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLibroReclamacion extends EditRecord
{
    protected static string $resource = LibroReclamacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

