<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (User $record): bool => $record->id === auth()->id())
                ->disabled(fn (User $record): bool => $record->sites()->exists())
                ->tooltip(fn (User $record): ?string => $record->sites()->exists() ? 'No se puede eliminar un usuario con tiendas asignadas.' : null),
        ];
    }
}
