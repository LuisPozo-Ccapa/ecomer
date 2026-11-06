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
            // 1. Crear o recuperar la sección 'Preguntas Frecuentes' (FAQ)
            $seccion = Seccion::updateOrCreate(
                [
                    'plantilla_id' => $plantilla->id,
                    'slug'         => 'faq',
                ],
                [
                    'nombre' => 'Preguntas Frecuentes',
                    'orden'  => 12,
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
                    'ayuda'      => 'Etiqueta superior en mayúsculas (Ej: FAQ)',
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
                    'ayuda'      => 'Título principal (Ej: Preguntas frecuentes)',
                ]
            );

            // 4. Pregunta: imagen (Foto de asesora o atención al cliente)
            $pImagen = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'imagen',
                ],
                [
                    'tipo'       => 'imagen',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Fotografía de atención al cliente / asesoría técnica',
                ]
            );

            // 5. Pregunta: faqs (Repeater de preguntas y respuestas)
            $pFaqs = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'faqs',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'array',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Colección de preguntas frecuentes y sus respuestas',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pFaqs->id,
                    'label'      => 'numero',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => false,
                    'ayuda'      => 'Número o identificador (Ej: 01, 02, 03)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pFaqs->id,
                    'label'      => 'pregunta',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => true,
                    'ayuda'      => 'Texto de la pregunta frecuente',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $pFaqs->id,
                    'label'      => 'respuesta',
                ],
                [
                    'tipo'       => 'area',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => true,
                    'ayuda'      => 'Explicación detallada de la respuesta',
                ]
            );

            // 6. Cargar datos iniciales de referencia para los sitios existentes (ej: Lismercorp)
            $sites = Site::where('plantilla_id', $plantilla->id)->get();
            foreach ($sites as $site) {
                $existing = Respuesta::where('site_id', $site->id)->where('pregunta_id', $pTitulo->id)->first();
                if (! $existing) {
                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pSubtitulo->id],
                        ['valor' => 'FAQ']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pTitulo->id],
                        ['valor' => 'Preguntas frecuentes']
                    );

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pImagen->id],
                        ['valor' => null]
                    );

                    $defaultFaqs = [
                        (string) Str::uuid() => [
                            'numero'    => '01',
                            'pregunta'  => '¿Tienen stock para entrega inmediata?',
                            'respuesta' => 'Sí, contamos con un amplio stock en nuestros almacenes y sedes físicas para entrega inmediata en los componentes y productos de mayor rotación.',
                        ],
                        (string) Str::uuid() => [
                            'numero'    => '02',
                            'pregunta'  => '¿Realizan envíos a todo el Perú?',
                            'respuesta' => 'Sí, realizamos despachos a nivel nacional a través de las principales agencias de transporte y logística autorizadas, o con entrega directa según tu operación.',
                        ],
                        (string) Str::uuid() => [
                            'numero'    => '03',
                            'pregunta'  => '¿Los productos que venden tienen garantía?',
                            'respuesta' => 'Todos nuestros productos y suministros cuentan con garantía directa de fábrica y el respaldo oficial de las marcas representadas.',
                        ],
                        (string) Str::uuid() => [
                            'numero'    => '04',
                            'pregunta'  => 'No estoy seguro del componente exacto que necesito. ¿Ofrecen asesoría técnica?',
                            'respuesta' => 'Por supuesto. Nuestro equipo de especialistas técnicos e ingenieros te asesora sin costo para identificar la pieza, reemplazo o solución exacta para tu maquinaria.',
                        ],
                        (string) Str::uuid() => [
                            'numero'    => '05',
                            'pregunta'  => '¿Qué métodos de pago aceptan?',
                            'respuesta' => 'Aceptamos transferencias bancarias (BCP, BBVA, Interbank), tarjetas de crédito y débito, pagos digitales (Yape, Plin) y líneas de crédito comercial para clientes corporativos.',
                        ],
                    ];

                    Respuesta::updateOrCreate(
                        ['site_id' => $site->id, 'pregunta_id' => $pFaqs->id],
                        ['valor' => json_encode($defaultFaqs)]
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
        $secciones = Seccion::where('slug', 'faq')->get();
        foreach ($secciones as $seccion) {
            $preguntasIds = Pregunta::where('seccion_id', $seccion->id)->pluck('id');
            Respuesta::whereIn('pregunta_id', $preguntasIds)->delete();
            Pregunta::where('seccion_id', $seccion->id)->delete();
            $seccion->delete();
        }
    }
};

