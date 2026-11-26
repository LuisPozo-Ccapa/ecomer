<?php

namespace App\Filament\Resources\Secciones\Pages;

use App\Filament\Resources\Secciones\SeccionResource;
use Filament\Resources\Pages\ListRecords;

class ListSecciones extends ListRecords
{
    protected static string $resource = SeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
