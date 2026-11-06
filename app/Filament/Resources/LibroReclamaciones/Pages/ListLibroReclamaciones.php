<?php

namespace App\Filament\Resources\LibroReclamaciones\Pages;

use App\Filament\Resources\LibroReclamaciones\LibroReclamacionResource;
use Filament\Resources\Pages\ListRecords;

class ListLibroReclamaciones extends ListRecords
{
    protected static string $resource = LibroReclamacionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

