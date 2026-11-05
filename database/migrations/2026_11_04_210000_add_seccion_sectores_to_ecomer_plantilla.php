<?php

use App\Models\Plantilla;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Seccion;
use App\Models\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $plantillas = Plantilla::whereIn('slug', ['ecomer'])->get();

        foreach ($plantillas as $plantilla) {
            // 1. Crear o recuperar la sección 'Sectores'
            $seccion = Seccion::updateOrCreate(
                [
                    'plantilla_id' => $plantilla->id,
                    'slug'         => 'sectores',
                ],
                [
                    'nombre' => 'Sectores',
                    'orden'  => 8,
                    'activa' => false,
                ]
            );

            // 2. Pregunta: subtitulo (Etiqueta superior. Ej: SECTORES)
            $pSubtitulo = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'subtitulo',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => false,
                    'ayuda'      => 'Etiqueta superior en mayúsculas (Ej: SECTORES)',
                ]
            );

            // 3. Pregunta: titulo (Título principal de la sección)
            $pTitulo = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'titulo',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => false,
                    'ayuda'      => 'Título principal (Ej: Soluciones hidráulicas para tu sector)',
                ]
            );

            // 4. Pregunta: descripcion (Opcional)
            $pDescripcion = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'descripcion',
                ],
                [
                    'tipo'       => 'area',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Breve descripción general opcional de la sección',
                ]
            );

            // 5. Pregunta PADRE: sectores (Colección de tarjetas interactivas)
            $pSectores = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'sectores',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'array',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Colección de sectores con imagen, título, badge, descripción y enlace de redirección.',
                ]
            );

            // 6. Sub-preguntas de cada Sector:
            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSectores->id,
                    'label'      => 'imagen',
                ],
                [
                    'tipo'       => 'imagen',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => true,
                    'ayuda'      => 'Imagen de fondo de la tarjeta del sector',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSectores->id,
                    'label'      => 'titulo',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => true,
                    'ayuda'      => 'Título o nombre del sector (Ej: Minería y maquinaria pesada)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSectores->id,
                    'label'      => 'badge',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Insignia o etiqueta de estado superior (Ej: HPR EN OPERACIÓN)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSectores->id,
                    'label'      => 'descripcion',
                ],
                [
                    'tipo'       => 'area',
                    'estructura' => 'objeto',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Texto explicativo visible al hacer hover (en PC) o en la card (en móvil)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSectores->id,
                    'label'      => 'boton_texto',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 5,
                    'requerida'  => false,
                    'ayuda'      => 'Texto del botón o enlace (Ej: Ver soluciones)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSectores->id,
                    'label'      => 'enlace',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 6,
                    'requerida'  => false,
                    'ayuda'      => 'Ruta o URL de destino al hacer clic (Ej: /productos?sector=mineria)',
                ]
            );

            // 7. Seed inicial para los sites que usan ecomer (ej. Lismercorp) con los 4 sectores de muestra
            $sites = Site::where('plantilla_id', $plantilla->id)->get();
            foreach ($sites as $site) {
                // Solo si no tiene respuesta para pSectores todavía
                $existing = Respuesta::where('site_id', $site->id)->where('pregunta_id', $pSectores->id)->first();
                if (! $existing) {
                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pSubtitulo->id],
                        ['valor' => 'SECTORES']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pTitulo->id],
                        ['valor' => 'Soluciones hidráulicas para tu sector']
                    );

                    $defaultSectores = [
                        (string) Str::uuid() => [
                            'imagen'      => 'https://images.unsplash.com/photo-1579829366248-204fe8413f31?auto=format&fit=crop&w=1000&q=80',
                            'titulo'      => 'Minería y maquinaria pesada',
                            'badge'       => 'HPR EN OPERACIÓN',
                            'descripcion' => 'Repuestos para equipos de extracción y perforación, donde cada hora de parada cuesta.',
                            'boton_texto' => 'Ver soluciones',
                            'enlace'      => '/productos?sector=mineria',
                        ],
                        (string) Str::uuid() => [
                            'imagen'      => 'https://images.unsplash.com/photo-1592982537447-7440770cbfc9?auto=format&fit=crop&w=1000&q=80',
                            'titulo'      => 'Agrícola',
                            'badge'       => 'ALTO RENDIMIENTO EN CAMPO',
                            'descripcion' => 'Componentes para tractores, cosecha y maquinaria agrícola con máxima durabilidad.',
                            'boton_texto' => 'Ver soluciones',
                            'enlace'      => '/productos?sector=agricola',
                        ],
                        (string) Str::uuid() => [
                            'imagen'      => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=1000&q=80',
                            'titulo'      => 'Pesquero',
                            'badge'       => 'RESISTENCIA MARINA',
                            'descripcion' => 'Soluciones para sistemas hidráulicos marinos, pesca industrial y embarcaciones.',
                            'boton_texto' => 'Ver soluciones',
                            'enlace'      => '/productos?sector=pesquero',
                        ],
                        (string) Str::uuid() => [
                            'imagen'      => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1000&q=80',
                            'titulo'      => 'Industrial',
                            'badge'       => 'PRODUCCIÓN CONTINUA',
                            'descripcion' => 'Repuestos para maquinaria de producción, prensas y plantas industriales.',
                            'boton_texto' => 'Ver soluciones',
                            'enlace'      => '/productos?sector=industrial',
                        ],
                    ];

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pSectores->id],
                        ['valor' => json_encode($defaultSectores)]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $secciones = Seccion::where('slug', 'sectores')->get();
        foreach ($secciones as $seccion) {
            $preguntasIds = Pregunta::where('seccion_id', $seccion->id)->pluck('id');
            Respuesta::whereIn('pregunta_id', $preguntasIds)->delete();
            Pregunta::where('seccion_id', $seccion->id)->delete();
            $seccion->delete();
        }
    }
};
