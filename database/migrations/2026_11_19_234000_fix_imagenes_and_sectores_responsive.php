<?php

use App\Models\Plantilla;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Seccion;
use App\Models\Site;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Asegurar preguntas responsive (Imagen_pc, Imagen_tablet, Imagen_cel) en todos los grupos 'Imagenes'
        $gruposImagenes = Pregunta::where('tipo', 'grupo')
            ->whereIn('label', ['Imagenes', 'imagenes', 'portada_imagenes', 'hero_imagenes'])
            ->get();

        $subCamposResponsive = [
            ['label' => 'Imagen_pc', 'orden' => 1, 'ayuda' => 'Imagen para computadoras de escritorio (Recomendado 1920x800 o proporción panorámica).'],
            ['label' => 'Imagen_tablet', 'orden' => 2, 'ayuda' => 'Imagen para tablets (Recomendado 1024x600 o proporción estándar).'],
            ['label' => 'Imagen_cel', 'orden' => 3, 'ayuda' => 'Imagen para celulares (Recomendado 768x800 o proporción vertical).'],
        ];

        foreach ($gruposImagenes as $grupo) {
            $existingChildren = Pregunta::where('parent_id', $grupo->id)->count();
            if ($existingChildren === 0) {
                foreach ($subCamposResponsive as $sc) {
                    Pregunta::firstOrCreate(
                        [
                            'parent_id' => $grupo->id,
                            'label'     => $sc['label'],
                        ],
                        [
                            'plantilla_id' => $grupo->plantilla_id,
                            'seccion_id'   => $grupo->seccion_id,
                            'tipo'         => 'imagen',
                            'estructura'   => 'objeto',
                            'orden'        => $sc['orden'],
                            'requerida'    => false,
                            'ayuda'        => $sc['ayuda'],
                        ]
                    );
                }
            }
        }

        // 2. Hacer opcional la imagen de la sección sectores (para permitir eliminarla o usar fallback de ícono)
        $preguntasImagenSectores = Pregunta::where('label', 'imagen')
            ->whereHas('parent', fn ($q) => $q->where('label', 'sectores'))
            ->get();

        foreach ($preguntasImagenSectores as $p) {
            $p->update(['requerida' => false]);
        }

        // 3. Asegurar sub-pregunta 'icono' en grupo 'sectores' para fallback visual
        $gruposSectores = Pregunta::where('label', 'sectores')
            ->where('tipo', 'grupo')
            ->get();

        foreach ($gruposSectores as $gs) {
            Pregunta::firstOrCreate(
                [
                    'parent_id' => $gs->id,
                    'label'     => 'icono',
                ],
                [
                    'plantilla_id' => $gs->plantilla_id,
                    'seccion_id'   => $gs->seccion_id,
                    'tipo'         => 'icono',
                    'estructura'   => 'objeto',
                    'orden'        => 7,
                    'requerida'    => false,
                    'ayuda'        => 'Ícono decorativo para la tarjeta cuando no se asigne imagen (Ej. FaIndustry, FaWrench, FaTruck)',
                ]
            );
        }

        // 4. Limpiar respuestas existentes: desenrollar UUIDs en arrays de imágenes y limpiar banners heredados
        $respuestas = Respuesta::whereNotNull('valor')->get();
        foreach ($respuestas as $resp) {
            $valor = $resp->valor;
            if (! is_string($valor)) {
                continue;
            }

            $decoded = json_decode($valor, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                continue;
            }

            $modified = false;
            $site = $resp->site_id ? Site::find($resp->site_id) : null;
            $isNotLismer = $site && ! in_array(strtolower($site->slug), ['lismercorp', 'lismer']);

            $normalizer = function (&$arr) use (&$normalizer, &$modified, $isNotLismer) {
                if (! is_array($arr)) {
                    return;
                }

                // Desenrollar clave UUID en sub-campo de imagen
                if (isset($arr['imagen']) && is_array($arr['imagen']) && count($arr['imagen']) === 1 && ! array_is_list($arr['imagen'])) {
                    $arr['imagen'] = reset($arr['imagen']);
                    $modified = true;
                }

                // Si no es el sitio oficial de Lismer Corp y tiene una imagen heredada de lismercorp en sectores, limpiarla a null
                if ($isNotLismer && isset($arr['imagen']) && is_string($arr['imagen']) && str_contains($arr['imagen'], 'sites/lismercorp/')) {
                    $arr['imagen'] = null;
                    $modified = true;
                }

                foreach ($arr as $k => &$v) {
                    if (is_array($v)) {
                        $normalizer($v);
                    }
                }
            };

            $normalizer($decoded);

            if ($modified) {
                $resp->update(['valor' => json_encode($decoded)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructivo
    }
};
