<?php

namespace App\Filament\Resources\LibroReclamaciones\Pages;

use App\Filament\Resources\LibroReclamaciones\LibroReclamacionResource;
use Filament\Resources\Pages\EditRecord;

class EditLibroReclamacion extends EditRecord
{
    protected static string $resource = LibroReclamacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Borrado deshabilitado por cumplimiento legal de custodia INDECOPI (D.S. N° 011-2011-PCM)
        ];
    }
}
