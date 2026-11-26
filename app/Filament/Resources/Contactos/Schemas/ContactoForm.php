<?php

namespace App\Filament\Resources\Contactos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Seguimiento Comercial')
                    ->description('Estado de atención de esta solicitud o cotización web.')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('folio')
                                ->label('Folio de Cotización')
                                ->disabled()
                                ->dehydrated(false),

                            Select::make('estado')
                                ->label('Estado')
                                ->options([
                                    'nuevo' => '🟡 Nuevo (Sin contactar)',
                                    'leido' => '🔵 Visto / En revisión',
                                    'en_proceso' => '🟣 En negociación',
                                    'respondido' => '🟢 Atendido / Cotizado',
                                ])
                                ->required()
                                ->native(false),

                            Toggle::make('correo_enviado')
                                ->label('Notificación por Correo')
                                ->disabled()
                                ->dehydrated(false),
                        ]),

                        TextInput::make('error_envio')
                            ->label('Detalle de Envío SMTP')
                            ->disabled()
                            ->columnSpanFull()
                            ->visible(fn ($record) => ! empty($record?->error_envio)),
                    ]),

                Section::make('Datos del Prospecto / Solicitante')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nombre')
                                ->label('Nombre Completo')
                                ->disabled(),

                            TextInput::make('empresa')
                                ->label('Empresa')
                                ->disabled(),

                            TextInput::make('telefono')
                                ->label('Teléfono / WhatsApp')
                                ->disabled(),

                            TextInput::make('email')
                                ->label('Correo Electrónico')
                                ->disabled(),

                            TextInput::make('sector')
                                ->label('Sector Comercial')
                                ->disabled(),

                            TextInput::make('producto')
                                ->label('Producto o Servicio de Interés')
                                ->disabled(),
                        ]),

                        Textarea::make('mensaje')
                            ->label('Requerimiento / Mensaje del Cliente')
                            ->disabled()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

