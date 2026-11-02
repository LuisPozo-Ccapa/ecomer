<?php

use App\Models\Plantilla;
use App\Models\Pregunta;
use Illuminate\Database\Migrations\Migration;

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

            // 1. Pregunta Cards repetible (array)
            $cards = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $parentId,
                    'label'      => 'Cards',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'array',
                    'orden'      => 6,
                    'requerida'  => false,
                    'ayuda'      => 'Tarjetas de contacto superiores (Llámanos, Escríbenos, Visítanos, etc.)',
                ]
            );

            // 2. Subpreguntas de cada Card (Opción C: Híbrido Icono + Imagen)
            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $cards->id,
                    'label'      => 'Icono',
                ],
                [
                    'tipo'       => 'icono',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => false,
                    'ayuda'      => 'Selecciona un ícono vectorial (ej. FaPhone, FaEnvelope, FaLocationDot)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $cards->id,
                    'label'      => 'Imagen',
                ],
                [
                    'tipo'       => 'imagen',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => false,
                    'ayuda'      => 'Opcional: Sube tu propia imagen o logo en formato PNG/SVG/WebP',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $cards->id,
                    'label'      => 'Titulo',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Título de la tarjeta (Ej: Llámanos, Escríbenos, Visítanos)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $cards->id,
                    'label'      => 'Descripcion',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Horario o descripción corta (Ej: Lun a Vie 09:00 am - 07:00 pm, Prol. Huánuco 1676...)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $cards->id,
                    'label'      => 'Boton_Texto',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 5,
                    'requerida'  => false,
                    'ayuda'      => 'Texto del botón o número (Ej: +51 984 150 060, Enviar correo, Ver en Maps)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $cards->id,
                    'label'      => 'Boton_Enlace',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 6,
                    'requerida'  => false,
                    'ayuda'      => 'Enlace del botón (Ej: tel:+51984150060, mailto:info@correo.com, https://maps.google.com/...)',
                ]
            );

            // 3. Campo adicional: Referencia_Comercial (para la tarjeta de ubicación)
            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $parentId,
                    'label'      => 'Referencia',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 7,
                    'requerida'  => false,
                    'ayuda'      => 'Referencia comercial para la tienda física (Ej: Visítanos en nuestra tienda central en Gamarra)',
                ]
            );

            // 4. Inicializar Cards y Referencia en respuestas existentes de la pregunta raíz para que aparezcan visibles de inmediato
            if ($preguntaContacto) {
                $respuestas = \App\Models\Respuesta::where('pregunta_id', $preguntaContacto->id)->get();
                foreach ($respuestas as $resp) {
                    $val = is_string($resp->valor) ? json_decode($resp->valor, true) : $resp->valor;
                    if (is_array($val)) {
                        $updated = false;
                        foreach ($val as $uuid => &$item) {
                            if (! isset($item['Cards']) || empty($item['Cards'])) {
                                $item['Cards'] = [
                                    (string) \Illuminate\Support\Str::uuid() => [
                                        'Icono'        => 'FaPhone',
                                        'Titulo'       => 'Llámanos',
                                        'Descripcion'  => 'Lun a Vie (09:00 am - 07:00 pm)',
                                        'Boton_Texto'  => '994063340',
                                        'Boton_Enlace' => 'tel:994063340',
                                    ],
                                    (string) \Illuminate\Support\Str::uuid() => [
                                        'Icono'        => 'FaEnvelope',
                                        'Titulo'       => 'Escríbenos',
                                        'Descripcion'  => 'Atención rápida para ventas corporativas',
                                        'Boton_Texto'  => 'Enviar correo',
                                        'Boton_Enlace' => 'mailto:lismercorp1@gmail.com',
                                    ],
                                    (string) \Illuminate\Support\Str::uuid() => [
                                        'Icono'        => 'FaLocationDot',
                                        'Titulo'       => 'Visítanos',
                                        'Descripcion'  => 'Tienda: Jr. Huarochirí Nº592, CC. Plaza Ferretero',
                                        'Boton_Texto'  => 'Ver en Maps',
                                        'Boton_Enlace' => 'https://maps.google.com/maps?q=Jr.+Huarochir%C3%AD+592%2C+Cercado+de+Lima',
                                    ],
                                ];
                                $updated = true;
                            }
                            if (! isset($item['Referencia']) || empty($item['Referencia'])) {
                                $item['Referencia'] = 'Visítanos en nuestra tienda central en Jr. Huarochirí Nº592, CC. Plaza Ferretero.';
                                $updated = true;
                            }
                        }
                        if ($updated) {
                            $resp->valor = json_encode($val, JSON_UNESCAPED_UNICODE);
                            $resp->save();
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

            $cards = Pregunta::where('seccion_id', $seccion->id)->where('label', 'Cards')->first();
            if ($cards) {
                Pregunta::where('parent_id', $cards->id)->delete();
                $cards->delete();
            }

            Pregunta::where('seccion_id', $seccion->id)->where('label', 'Referencia')->delete();
        }
    }
};

