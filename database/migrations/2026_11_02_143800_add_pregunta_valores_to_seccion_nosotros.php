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
        $plantillas = Plantilla::whereIn('slug', ['ecomer'])->get();

        foreach ($plantillas as $plantilla) {
            $seccion = $plantilla->secciones()->where('slug', 'nosotros')->first();
            if (! $seccion) {
                continue;
            }

            // 1. Pregunta principal 'Valores'
            $preguntaValores = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => null,
                    'label'      => 'Valores',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'objeto',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Sección de valores y lo que nos diferencia con tarjetas y números 01, 02, etc.',
                ]
            );

            // 2. Hijos de Valores: sub, Titulo, Descripcion, Cards
            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $preguntaValores->id,
                    'label'      => 'sub',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => false,
                    'ayuda'      => 'Kicker superior en mayúsculas (Ej: LO QUE NOS DIFERENCIA)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $preguntaValores->id,
                    'label'      => 'Titulo',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => false,
                    'ayuda'      => 'Título principal (Ej: Valores y compromiso con el cliente)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $preguntaValores->id,
                    'label'      => 'Descripcion',
                ],
                [
                    'tipo'       => 'area',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Párrafo explicativo introductorio',
                ]
            );

            $cards = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $preguntaValores->id,
                    'label'      => 'Cards',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'array',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Lista repetible de tarjetas de valores (01, 02, 03...)',
                ]
            );

            // 3. Sub-hijos de Cards: Icono, Titulo, Descripcion
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
                    'ayuda'      => 'Ícono representativo (ej: FaCrosshairs, FaArrowTrendUp, FaClock, FaArrowsRotate)',
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
                    'orden'      => 2,
                    'requerida'  => false,
                    'ayuda'      => 'Título del valor (Ej: CONSISTENCIA, MEJORA, PUNTUALIDAD)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $cards->id,
                    'label'      => 'Descripcion',
                ],
                [
                    'tipo'       => 'area',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Descripción breve del valor o pilar',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $plantillas = Plantilla::whereIn('slug', ['ecomer'])->get();

        foreach ($plantillas as $plantilla) {
            $seccion = $plantilla->secciones()->where('slug', 'nosotros')->first();
            if ($seccion) {
                $pregunta = Pregunta::where('seccion_id', $seccion->id)
                    ->whereNull('parent_id')
                    ->where('label', 'Valores')
                    ->first();

                if ($pregunta) {
                    $pregunta->delete();
                }
            }
        }
    }
};
