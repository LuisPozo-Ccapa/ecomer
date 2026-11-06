<?php

namespace App\Filament\Resources\LibroReclamaciones\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LibroReclamacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // GESTIÓN Y RESPUESTA DE LA EMPRESA
                Section::make('Gestión y Respuesta al Consumidor')
                    ->description('Registre la atención y respuesta formal emitida al cliente dentro de los 15 días hábiles.')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('estado')
                                ->label('Estado del Requerimiento')
                                ->options([
                                    'pendiente' => '🟡 Pendiente de Atención',
                                    'en_proceso' => '🔵 En Proceso de Evaluación',
                                    'atendido' => '🟢 Atendido / Respondido',
                                ])
                                ->required()
                                ->native(false),

                            DatePicker::make('fecha_limite')
                                ->label('Fecha Límite Legal (15 días)')
                                ->disabled()
                                ->dehydrated(false),

                            DateTimePicker::make('fecha_respuesta')
                                ->label('Fecha de Respuesta Emitida')
                                ->native(false),
                        ]),

                        Textarea::make('respuesta_empresa')
                            ->label('Respuesta Formal Emitida por la Empresa')
                            ->placeholder('Detalle la solución, comunicación o respuesta remitida formalmente al consumidor...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // HOJA DE RECLAMACIÓN REGISTRADA (DATOS DEL CLIENTE)
                Section::make('Información del Reclamo / Queja')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('codigo')
                                ->label('Código Oficial')
                                ->disabled()
                                ->dehydrated(false),

                            TextInput::make('tipo')
                                ->label('Tipo de Solicitud')
                                ->formatStateUsing(fn ($state) => strtoupper($state))
                                ->disabled()
                                ->dehydrated(false),

                            TextInput::make('created_at')
                                ->label('Fecha y Hora de Registro')
                                ->formatStateUsing(fn ($record) => $record?->created_at?->format('d/m/Y H:i:s'))
                                ->disabled()
                                ->dehydrated(false),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('tipo_documento')
                                ->label('Tipo de Documento')
                                ->disabled(),

                            TextInput::make('numero_documento')
                                ->label('N° de Documento')
                                ->disabled(),

                            TextInput::make('nombre_completo')
                                ->label('Consumidor / Razón Social')
                                ->disabled()
                                ->dehydrated(false),

                            TextInput::make('email')
                                ->label('Correo Notificado')
                                ->disabled(),

                            TextInput::make('telefono')
                                ->label('Teléfono de Contacto')
                                ->disabled(),

                            TextInput::make('direccion')
                                ->label('Domicilio')
                                ->disabled(),
                        ]),

                        Toggle::make('es_menor_edad')
                            ->label('El consumidor es menor de edad')
                            ->disabled(),

                        Grid::make(2)->schema([
                            TextInput::make('tipo_bien')
                                ->label('Tipo de Bien Contratado')
                                ->formatStateUsing(fn ($state) => ucfirst($state))
                                ->disabled(),

                            TextInput::make('monto_reclamado')
                                ->label('Monto Reclamado')
                                ->formatStateUsing(fn ($record) => $record ? ($record->moneda === 'USD' ? '$ ' : 'S/ ') . number_format($record->monto_reclamado ?? 0, 2) : '-')
                                ->disabled()
                                ->dehydrated(false),

                            TextInput::make('numero_pedido')
                                ->label('N° de Pedido / Comprobante')
                                ->disabled(),

                            Textarea::make('descripcion_bien')
                                ->label('Descripción del Producto o Servicio')
                                ->disabled()
                                ->columnSpanFull(),
                        ]),

                        Textarea::make('detalle')
                            ->label('Hechos y Detalle de la Reclamación')
                            ->rows(4)
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('pedido')
                            ->label('Pedido Concreto del Consumidor')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}

