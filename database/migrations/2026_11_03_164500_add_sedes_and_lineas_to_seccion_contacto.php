<?php

use App\Models\Plantilla;
use App\Models\Pregunta;
use App\Models\Respuesta;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $plantillas = Plantilla::whereIn('slug', ['ecomer'])
            ->orWhere('nombre', 'like', '%ecomer%')
            ->get();

        foreach ($plantillas as $plantilla) {
            $seccion = $plantilla->secciones()->where(function ($q) {
                $q->where('slug', 'contacto')->orWhere('nombre', 'contacto');
            })->first();

            if (! $seccion) {
                continue;
            }

            // Buscar la pregunta raíz 'Contacto' (tipo grupo)
            $preguntaContacto = Pregunta::where('seccion_id', $seccion->id)
                ->whereNull('parent_id')
                ->where(function ($q) {
                    $q->where('label', 'Contacto')->orWhere('label', 'contacto');
                })
                ->first();

            $parentId = $preguntaContacto ? $preguntaContacto->id : null;

            // 1. Pregunta Sedes repetible (array) bajo Contacto
            $sedes = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $parentId,
                    'label'      => 'Sedes',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'array',
                    'orden'      => 9,
                    'requerida'  => false,
                    'ayuda'      => 'Sucursales o sedes físicas de la empresa para el mapa interactivo con switch',
                ]
            );

            // Subpreguntas de cada Sede
            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $sedes->id,
                    'label'      => 'Titulo',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => false,
                    'ayuda'      => 'Nombre de la sede (Ej: Tienda Central, Sucursal Norte, Almacén Callao)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $sedes->id,
                    'label'      => 'Direccion',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => false,
                    'ayuda'      => 'Dirección completa (Ej: Jr. Huarochirí 592, CC. Plaza Ferretero)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $sedes->id,
                    'label'      => 'Referencia',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Referencia comercial para llegar (Ej: Frente a la estación Monserrate)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $sedes->id,
                    'label'      => 'Mapa',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Dirección para el mapa de Google o coordenadas (Ej: Jr. Huarochirí 592, Cercado de Lima)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $sedes->id,
                    'label'      => 'Boton_Enlace',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 5,
                    'requerida'  => false,
                    'ayuda'      => 'Enlace directo de Google Maps (Ej: https://maps.google.com/...)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $sedes->id,
                    'label'      => 'Telefono',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 6,
                    'requerida'  => false,
                    'ayuda'      => 'Teléfono directo de la sede (opcional)',
                ]
            );

            // 2. Subpregunta repetible Lineas bajo Cards (para múltiples teléfonos por departamento)
            $cardsPregunta = Pregunta::where('seccion_id', $seccion->id)
                ->where('parent_id', $parentId)
                ->where('label', 'Cards')
                ->first();

            if ($cardsPregunta) {
                $lineas = Pregunta::updateOrCreate(
                    [
                        'seccion_id' => $seccion->id,
                        'parent_id'  => $cardsPregunta->id,
                        'label'      => 'Lineas',
                    ],
                    [
                        'tipo'       => 'grupo',
                        'estructura' => 'array',
                        'orden'      => 7,
                        'requerida'  => false,
                        'ayuda'      => 'Líneas o departamentos de contacto adicionales (ej. Ventas, Soporte, Postventa)',
                    ]
                );

                Pregunta::updateOrCreate(
                    [
                        'seccion_id' => $seccion->id,
                        'parent_id'  => $lineas->id,
                        'label'      => 'Etiqueta',
                    ],
                    [
                        'tipo'       => 'texto',
                        'estructura' => 'objeto',
                        'orden'      => 1,
                        'requerida'  => false,
                        'ayuda'      => 'Nombre del departamento (Ej: Ventas, Soporte, Atención al Cliente)',
                    ]
                );

                Pregunta::updateOrCreate(
                    [
                        'seccion_id' => $seccion->id,
                        'parent_id'  => $lineas->id,
                        'label'      => 'Numero',
                    ],
                    [
                        'tipo'       => 'texto',
                        'estructura' => 'objeto',
                        'orden'      => 2,
                        'requerida'  => false,
                        'ayuda'      => 'Número telefónico (Ej: 994063340)',
                    ]
                );

                Pregunta::updateOrCreate(
                    [
                        'seccion_id' => $seccion->id,
                        'parent_id'  => $lineas->id,
                        'label'      => 'Horario',
                    ],
                    [
                        'tipo'       => 'texto',
                        'estructura' => 'objeto',
                        'orden'      => 3,
                        'requerida'  => false,
                        'ayuda'      => 'Horario de atención específico (Ej: Lun a Vie 09:00 am - 07:00 pm)',
                    ]
                );
            }

            // 3. Pre-llenar la Sede inicial para los sitios existentes que ya tengan mapa o dirección
            $sites = $plantilla->sites;
            foreach ($sites as $site) {
                $respContacto = Respuesta::where('site_id', $site->id)
                    ->where('pregunta_id', $preguntaContacto?->id)
                    ->first();

                if ($respContacto && !empty($respContacto->valor)) {
                    $val = is_string($respContacto->valor) ? json_decode($respContacto->valor, true) : $respContacto->valor;
                    if (is_array($val)) {
                        $firstKey = array_key_first($val);
                        $contactoData = is_array($val[$firstKey] ?? null) ? $val[$firstKey] : $val;

                        // Si aún no tiene Sedes, crear la Sede Principal automáticamente
                        if (empty($contactoData['Sedes'])) {
                            $mapaDir = $contactoData['Mapa'] ?? 'Jr. Huarochirí 592, Cercado de Lima';
                            $ref = $contactoData['Referencia'] ?? 'Visítanos en nuestra tienda central en Jr. Huarochirí Nº592, CC. Plaza Ferretero.';

                            $contactoData['Sedes'] = [
                                (string) Str::uuid() => [
                                    'Titulo'       => 'Tienda Central',
                                    'Direccion'    => $mapaDir,
                                    'Referencia'   => $ref,
                                    'Mapa'         => $mapaDir,
                                    'Boton_Enlace' => 'https://maps.google.com/?q=' . urlencode($mapaDir),
                                    'Telefono'     => '994063340',
                                ]
                            ];

                            if ($firstKey && is_array($val[$firstKey] ?? null)) {
                                $val[$firstKey] = $contactoData;
                            } else {
                                $val = $contactoData;
                            }

                            $respContacto->valor = $val;
                            $respContacto->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sedes = Pregunta::where('label', 'Sedes')->get();
        foreach ($sedes as $s) {
            $s->children()->delete();
            $s->delete();
        }

        $lineas = Pregunta::where('label', 'Lineas')->get();
        foreach ($lineas as $l) {
            $l->children()->delete();
            $l->delete();
        }
    }
};

