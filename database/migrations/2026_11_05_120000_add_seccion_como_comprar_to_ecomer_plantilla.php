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
            // 1. Crear o recuperar la sección 'Cómo Comprar' para la página de inicio
            $seccion = Seccion::updateOrCreate(
                [
                    'plantilla_id' => $plantilla->id,
                    'slug'         => 'como_comprar',
                ],
                [
                    'nombre' => 'Cómo Comprar',
                    'orden'  => 10,
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
                    'ayuda'      => 'Etiqueta superior en mayúsculas (Ej: CÓMO COMPRAR)',
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
                    'ayuda'      => 'Título principal (Ej: Cómo solicitar tu cotización)',
                ]
            );

            // 4. Pregunta: pasos (Repeater de 4 pasos)
            $pPasos = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'pasos',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'array',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Lista de pasos de cotización y compra',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pPasos->id,
                    'label'      => 'numero',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => true,
                    'ayuda'      => 'Número o identificador del paso (Ej: 1, 2, 3, 4)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pPasos->id,
                    'label'      => 'titulo',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => true,
                    'ayuda'      => 'Título del paso (Ej: Solicita, Recibe tu cotización)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pPasos->id,
                    'label'      => 'descripcion',
                ],
                [
                    'tipo'       => 'area',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Descripción breve del paso',
                ]
            );

            // 5. Pregunta: imagen
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
                    'ayuda'      => 'Fotografía de atención al cliente / equipo de cotizaciones',
                ]
            );

            // 6. Pregunta: banner_texto
            $pBannerTexto = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'banner_texto',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 5,
                    'requerida'  => false,
                    'ayuda'      => 'Texto del banner inferior (Ej: ¿Compras por volumen para revender? Consúltanos por condiciones mayoristas.)',
                ]
            );

            // 7. Pregunta: banner_boton_texto
            $pBannerBotonTexto = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'banner_boton_texto',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 6,
                    'requerida'  => false,
                    'ayuda'      => 'Texto del botón en el banner (Ej: WhatsApp (+51) 986 350 124)',
                ]
            );

            // 8. Pregunta: banner_boton_enlace
            $pBannerBotonEnlace = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'banner_boton_enlace',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 7,
                    'requerida'  => false,
                    'ayuda'      => 'Enlace del botón (Ej: https://wa.me/51986350124)',
                ]
            );

            // 9. Cargar datos iniciales de referencia para los sitios existentes (ej: Lismercorp)
            $sites = Site::where('plantilla_id', $plantilla->id)->get();
            foreach ($sites as $site) {
                $existing = Respuesta::where('site_id', $site->id)->where('pregunta_id', $pTitulo->id)->first();
                if (! $existing) {
                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pSubtitulo->id],
                        ['valor' => 'CÓMO COMPRAR']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pTitulo->id],
                        ['valor' => 'Cómo solicitar tu cotización']
                    );

                    $defaultPasos = [
                        (string) Str::uuid() => [
                            'numero'      => '1',
                            'titulo'      => 'Solicita',
                            'descripcion' => 'Desde el catálogo (carrito de cotización), el buscador o por WhatsApp.',
                        ],
                        (string) Str::uuid() => [
                            'numero'      => '2',
                            'titulo'      => 'Recibe tu cotización',
                            'descripcion' => 'Con producto confirmado, precio y plazo reales.',
                        ],
                        (string) Str::uuid() => [
                            'numero'      => '3',
                            'titulo'      => 'Aprueba el pedido',
                            'descripcion' => 'Confirmas y coordinamos el despacho.',
                        ],
                        (string) Str::uuid() => [
                            'numero'      => '4',
                            'titulo'      => 'Recibe y factura',
                            'descripcion' => 'Entregamos y emitimos tu comprobante (DNI/CE persona natural · RUC persona jurídica).',
                        ],
                    ];

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pPasos->id],
                        ['valor' => json_encode($defaultPasos)]
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pImagen->id],
                        ['valor' => null]
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBannerTexto->id],
                        ['valor' => '¿Compras por volumen para revender? Consúltanos por condiciones mayoristas.']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBannerBotonTexto->id],
                        ['valor' => 'WhatsApp (+51) 986 350 124']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pBannerBotonEnlace->id],
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
        $secciones = Seccion::where('slug', 'como_comprar')->get();
        foreach ($secciones as $seccion) {
            $preguntasIds = Pregunta::where('seccion_id', $seccion->id)->pluck('id');
            Respuesta::whereIn('pregunta_id', $preguntasIds)->delete();
            Pregunta::where('seccion_id', $seccion->id)->delete();
            $seccion->delete();
        }
    }
};

