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
            // 1. Crear o recuperar la sección 'Sedes' para la página de inicio
            $seccion = Seccion::updateOrCreate(
                [
                    'plantilla_id' => $plantilla->id,
                    'slug'         => 'sedes',
                ],
                [
                    'nombre' => 'Sedes',
                    'orden'  => 11,
                    'activa' => false,
                ]
            );

            // 2. Pregunta: subtitulo
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
                    'ayuda'      => 'Etiqueta superior en mayúsculas (Ej: NUESTRAS SEDES)',
                ]
            );

            // 3. Pregunta: titulo
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
                    'ayuda'      => 'Título principal (Ej: Encuentra la sede más cercana a tu operación)',
                ]
            );

            // 4. Pregunta: sedes (Repeater dinámico de sedes)
            $pSedes = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'sedes',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'array',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Colección de sucursales o puntos de venta físicos',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSedes->id,
                    'label'      => 'titulo',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => true,
                    'ayuda'      => 'Nombre de la sede (Ej: Sede Central, Punto de venta "Villón")',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSedes->id,
                    'label'      => 'direccion',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => true,
                    'ayuda'      => 'Dirección de la sede (Ej: Jr. García Villón 536, Lima – Perú)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSedes->id,
                    'label'      => 'horarios',
                ],
                [
                    'tipo'       => 'area',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Horarios de atención',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSedes->id,
                    'label'      => 'imagen',
                ],
                [
                    'tipo'       => 'imagen',
                    'estructura' => 'objeto',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Fotografía de la fachada o equipo en tienda',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSedes->id,
                    'label'      => 'mapa',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 5,
                    'requerida'  => false,
                    'ayuda'      => 'Dirección o URL de Google Maps para el mapa embebido',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pSedes->id,
                    'label'      => 'boton_enlace',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 6,
                    'requerida'  => false,
                    'ayuda'      => 'Enlace directo de Google Maps (¿Cómo llegar?)',
                ]
            );

            // 5. Pregunta: banner_callout_texto
            $pBannerTexto = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'banner_callout_texto',
                ],
                [
                    'tipo'       => 'area',
                    'estructura' => 'objeto',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Texto del banner inferior de atención nacional',
                ]
            );

            // 6. Pregunta: banner_callout_enlace_texto
            $pBannerEnlaceTexto = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'banner_callout_enlace_texto',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 5,
                    'requerida'  => false,
                    'ayuda'      => 'Texto del enlace del banner (Ej: Atención en provincias ›)',
                ]
            );

            // 7. Pregunta: banner_callout_enlace
            $pBannerEnlace = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'banner_callout_enlace',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 6,
                    'requerida'  => false,
                    'ayuda'      => 'Enlace de WhatsApp o contacto para provincias',
                ]
            );

            // 8. Cargar datos iniciales de referencia para los sitios existentes (ej: Lismercorp)
            $sites = Site::where('plantilla_id', $plantilla->id)->get();
            foreach ($sites as $site) {
                $existing = Respuesta::where('site_id', $site->id)->where('pregunta_id', $pTitulo->id)->first();
                if (! $existing) {
                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pSubtitulo->id],
                        ['valor' => 'NUESTRAS SEDES']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pTitulo->id],
                        ['valor' => 'Encuentra la sede más cercana a tu operación']
                    );

                    $defaultSedes = [
                        (string) Str::uuid() => [
                            'titulo'       => 'Sede Central',
                            'direccion'    => 'Calle Ignacio Álvarez Thomas 1594, Lima',
                            'horarios'     => "Lunes a viernes de 8:00 a. m. a 5:30 p. m.\nSábados de 8:00 a. m. a 1:00 p. m.",
                            'imagen'       => null,
                            'mapa'         => 'Calle Ignacio Álvarez Thomas 1594, Lima, Perú',
                            'boton_enlace' => 'https://maps.google.com/?q=Calle+Ignacio+Alvarez+Thomas+1594+Lima',
                        ],
                        (string) Str::uuid() => [
                            'titulo'       => 'Punto de venta "Villón"',
                            'direccion'    => 'Jr. García Villón 536, Lima – Perú',
                            'horarios'     => "Lunes a viernes de 8:00 a. m. a 5:30 p. m.\nSábados de 8:00 a. m. a 1:00 p. m.",
                            'imagen'       => null,
                            'mapa'         => 'Jr. García Villón 536, Lima, Perú',
                            'boton_enlace' => 'https://maps.google.com/?q=Jr+Garcia+Villon+536+Lima',
                        ],
                        (string) Str::uuid() => [
                            'titulo'       => 'Punto de venta "Acoprom"',
                            'direccion'    => 'Av. Argentina 523, Galería Acoprom, Stand I-15, Lima – Perú',
                            'horarios'     => "Lunes a viernes de 8:00 a. m. a 5:30 p. m.\nSábados de 8:00 a. m. a 1:00 p. m.",
                            'imagen'       => null,
                            'mapa'         => 'Av. Argentina 523, Galería Acoprom, Lima, Perú',
                            'boton_enlace' => 'https://maps.google.com/?q=Av+Argentina+523+Lima',
                        ],
                    ];

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pSedes->id],
                        ['valor' => json_encode($defaultSedes)]
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBannerTexto->id],
                        ['valor' => '¿No estás en Lima? Te atendemos por llamada o videollamada en todo el Perú, con la misma confianza que una visita presencial.']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBannerEnlaceTexto->id],
                        ['valor' => 'Atención en provincias ›']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBannerEnlace->id],
                        ['valor' => 'https://wa.me/51986350124']
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
        $secciones = Seccion::where('slug', 'sedes')->get();
        foreach ($secciones as $seccion) {
            $preguntasIds = Pregunta::where('seccion_id', $seccion->id)->pluck('id');
            Respuesta::whereIn('pregunta_id', $preguntasIds)->delete();
            Pregunta::where('seccion_id', $seccion->id)->delete();
            $seccion->delete();
        }
    }
};

