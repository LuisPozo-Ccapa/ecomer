<?php

namespace App\Filament\Resources\LibroReclamaciones\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LibroReclamacionesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Código copiado al portapapeles'),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'reclamo' => 'danger',
                        'queja' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                TextColumn::make('nombre_completo')
                    ->label('Consumidor')
                    ->searchable(['nombres', 'apellidos', 'razon_social'])
                    ->description(fn ($record) => "{$record->tipo_documento}: {$record->numero_documento}"),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->copyable(),

                TextColumn::make('tipo_bien')
                    ->label('Bien')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                TextColumn::make('monto_reclamado')
                    ->label('Monto')
                    ->money(fn ($record) => $record->moneda ?? 'PEN'),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'en_proceso' => 'info',
                        'atendido' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pendiente' => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'atendido' => 'Atendido',
                        default => ucfirst($state),
                    }),

                TextColumn::make('fecha_limite')
                    ->label('Fecha Límite')
                    ->date('d/m/Y')
                    ->description(fn ($record) => $record->created_at ? 'Reg: ' . $record->created_at->format('d/m/Y') : null)
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'reclamo' => 'Reclamo',
                        'queja' => 'Queja',
                    ]),
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'atendido' => 'Atendido',
                    ]),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(function ($record): ?string {
                        if (! $record->telefono) {
                            return null;
                        }
                        $clean = preg_replace('/[^0-9]/', '', $record->telefono);
                        if (! str_starts_with($clean, '51') && strlen($clean) === 9) {
                            $clean = '51' . $clean;
                        }
                        $cliente = $record->nombres ? ($record->nombres . ' ' . $record->apellidos) : ($record->razon_social ?: 'Estimado/a consumidor');
                        $msg = urlencode("Hola {$cliente}, le escribimos de " . request()->getHost() . " respecto a su Hoja de Reclamación N° {$record->codigo}.");
                        return "https://wa.me/{$clean}?text={$msg}";
                    }, shouldOpenInNewTab: true),
                EditAction::make()->label('Gestionar'),
            ]);
    }
}

