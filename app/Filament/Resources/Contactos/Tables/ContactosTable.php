<?php

namespace App\Filament\Resources\Contactos\Tables;

use App\Models\Contacto;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ContactosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')
                    ->label('Folio')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('empresa')
                    ->label('Empresa')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-phone'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('producto')
                    ->label('Interés / Producto')
                    ->searchable()
                    ->placeholder('General')
                    ->limit(30),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nuevo' => 'warning',
                        'leido' => 'info',
                        'en_proceso' => 'primary',
                        'respondido' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'nuevo' => 'Nuevo',
                        'leido' => 'Leído',
                        'en_proceso' => 'En proceso',
                        'respondido' => 'Atendido',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                IconColumn::make('correo_enviado')
                    ->label('SMTP')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (Contacto $record): ?string => $record->error_envio ?: ($record->correo_enviado ? 'Enviado por correo' : 'No enviado')),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'nuevo' => 'Nuevo',
                        'leido' => 'Leído',
                        'en_proceso' => 'En proceso',
                        'respondido' => 'Atendido',
                    ]),
                TernaryFilter::make('correo_enviado')
                    ->label('Estado de Correo')
                    ->trueLabel('Enviados')
                    ->falseLabel('Fallidos / No enviados'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(function (Contacto $record): ?string {
                        if (! $record->telefono) {
                            return null;
                        }
                        $clean = preg_replace('/[^0-9]/', '', $record->telefono);
                        if (! str_starts_with($clean, '51') && strlen($clean) === 9) {
                            $clean = '51' . $clean;
                        }
                        $cliente = $record->nombre ?: 'Estimado/a';
                        $msg = urlencode("Hola {$cliente}, le escribimos de " . request()->getHost() . " en respuesta a su consulta (Folio: {$record->folio}).");
                        return "https://wa.me/{$clean}?text={$msg}";
                    }, shouldOpenInNewTab: true),
                EditAction::make()->label('Atender'),
                DeleteAction::make()->label('Archivar'),
                RestoreAction::make()->label('Restaurar'),
            ]);
    }
}

