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
            // 1. Crear o recuperar la sección 'Sobre Nosotros' para la página de inicio
            $seccion = Seccion::updateOrCreate(
                [
                    'plantilla_id' => $plantilla->id,
                    'slug'         => 'sobre_nosotros',
                ],
                [
                    'nombre' => 'Sobre Nosotros',
                    'orden'  => 9,
                    'activa' => false,
                ]
            );

            // 2. Pregunta: subtitulo (Etiqueta superior en mayúsculas)
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
                    'ayuda'      => 'Etiqueta superior en mayúsculas (Ej: NOSOTROS)',
                ]
            );

            // 3. Pregunta: titulo (Título principal)
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
                    'ayuda'      => 'Título principal (Ej: Jóvenes como marca, expertos en oleohidráulica)',
                ]
            );

            // 4. Pregunta: descripcion (Párrafo explicativo)
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
                    'ayuda'      => 'Párrafo explicativo de trayectoria y propuesta de valor',
                ]
            );

            // 5. Pregunta: imagen (Foto de equipo / instalaciones)
            $pImagen = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'imagen',
                ],
                [
                    'tipo'       => 'imagen',
                    'estructura' => 'objeto',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Fotografía del equipo o instalaciones',
                ]
            );

            // 6. Pregunta: badge_destacado (Cifra destacada en la card flotante)
            $pBadgeDestacado = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'badge_destacado',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 5,
                    'requerida'  => false,
                    'ayuda'      => 'Cifra grande en la tarjeta flotante (Ej: +20 años)',
                ]
            );

            // 7. Pregunta: badge_texto (Subtexto de la card flotante)
            $pBadgeTexto = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'badge_texto',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 6,
                    'requerida'  => false,
                    'ayuda'      => 'Subtexto de la tarjeta flotante (Ej: de experiencia técnica en oleohidráulica)',
                ]
            );

            // 8. Pregunta: metricas (Repeater dinámico de estadísticas)
            $pMetricas = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'metricas',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'array',
                    'orden'      => 7,
                    'requerida'  => false,
                    'ayuda'      => 'Métricas numéricas y datos de confianza (Ej: 7 años / como HPR GROUP)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pMetricas->id,
                    'label'      => 'cifra',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => true,
                    'ayuda'      => 'Número o valor destacado (Ej: 7 años, 37+ marcas, 3 sedes)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pMetricas->id,
                    'label'      => 'etiqueta',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => true,
                    'ayuda'      => 'Etiqueta o descripción de la cifra (Ej: como HPR GROUP, representadas, en Lima)',
                ]
            );

            // 9. Pregunta: boton_texto
            $pBotonTexto = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'boton_texto',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 8,
                    'requerida'  => false,
                    'ayuda'      => 'Texto del botón de acción (Ej: Conócenos)',
                ]
            );

            // 10. Pregunta: boton_enlace
            $pBotonEnlace = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'boton_enlace',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 9,
                    'requerida'  => false,
                    'ayuda'      => 'Enlace de redirección (Ej: /nosotros)',
                ]
            );

            // 11. Cargar datos iniciales de referencia para los sitios existentes (ej: Lismercorp)
            $sites = Site::where('plantilla_id', $plantilla->id)->get();
            foreach ($sites as $site) {
                $existing = Respuesta::where('site_id', $site->id)->where('pregunta_id', $pTitulo->id)->first();
                if (! $existing) {
                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pSubtitulo->id],
                        ['valor' => 'NOSOTROS']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pTitulo->id],
                        ['valor' => 'Jóvenes como marca, expertos en oleohidráulica']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pDescripcion->id],
                        ['valor' => 'HPR GROUP nació hace 7 años, pero detrás hay socios y un equipo técnico con más de dos décadas en el rubro. Combinamos ese conocimiento con un amplio stock nacional y el respaldo de las marcas que representamos para resolver tu requerimiento rápido y bien.']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pImagen->id],
                        ['valor' => null]
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBadgeDestacado->id],
                        ['valor' => '+20 años']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBadgeTexto->id],
                        ['valor' => 'de experiencia técnica en oleohidráulica']
                    );

                    $defaultMetricas = [
                        (string) Str::uuid() => [
                            'cifra'    => '7 años',
                            'etiqueta' => 'como HPR GROUP',
                        ],
                        (string) Str::uuid() => [
                            'cifra'    => '37+ marcas',
                            'etiqueta' => 'representadas',
                        ],
                        (string) Str::uuid() => [
                            'cifra'    => '3 sedes',
                            'etiqueta' => 'en Lima',
                        ],
                    ];

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pMetricas->id],
                        ['valor' => json_encode($defaultMetricas)]
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBotonTexto->id],
                        ['valor' => 'Conócenos']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBotonEnlace->id],
                        ['valor' => '/nosotros']
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
        $secciones = Seccion::where('slug', 'sobre_nosotros')->get();
        foreach ($secciones as $seccion) {
            $preguntasIds = Pregunta::where('seccion_id', $seccion->id)->pluck('id');
            Respuesta::whereIn('pregunta_id', $preguntasIds)->delete();
            Pregunta::where('seccion_id', $seccion->id)->delete();
            $seccion->delete();
        }
    }
};

