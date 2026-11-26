<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->label('Borrar')
                    ->hidden(fn (User $record): bool => $record->id === auth()->id())
                    ->disabled(fn (User $record): bool => $record->sites()->exists())
                    ->tooltip(fn (User $record): ?string => $record->sites()->exists() ? 'No se puede eliminar un usuario con tiendas asignadas.' : null),
            ]);
    }
}
