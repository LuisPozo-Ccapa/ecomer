<?php

use App\Models\Plantilla;
use App\Models\Pregunta;
use App\Models\Seccion;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $jsonPath = database_path('data/plantilla_ecomer_secciones.json');
        if (! file_exists($jsonPath)) {
            return;
        }

        $seccionesJson = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($seccionesJson)) {
            return;
        }

        // Obtener todas las plantillas tipo ecommerce o ecomer
        $plantillas = Plantilla::where('tipo', 'ecommerce')
            ->orWhere('slug', 'ecomer')
            ->orWhere('nombre', 'like', '%ecomer%')
            ->orWhere('nombre', 'like', '%lismer%')
            ->get();

        foreach ($plantillas as $plantilla) {
            if ($plantilla->slug === 'tortas') {
                continue;
            }

            foreach ($seccionesJson as $secData) {
                $seccion = Seccion::firstOrCreate(
                    [
                        'plantilla_id' => $plantilla->id,
                        'slug'         => $secData['slug'],
                    ],
                    [
                        'nombre' => $secData['nombre'],
                        'orden'  => $secData['orden'] ?? 1,
                        'activa' => $secData['activa'] ?? false,
                    ]
                );

                if (! empty($secData['preguntas']) && is_array($secData['preguntas'])) {
                    $this->syncPreguntasRecursivas((int) $seccion->id, $secData['preguntas'], null);
                }
            }
        }
    }

    private function syncPreguntasRecursivas(int $seccionId, array $preguntas, ?int $parentId = null): void
    {
        foreach ($preguntas as $p) {
            $pregunta = Pregunta::firstOrCreate(
                [
                    'seccion_id' => $seccionId,
                    'label'      => $p['label'],
                    'parent_id'  => $parentId,
                ],
                [
                    'tipo'       => $p['tipo'] ?? 'texto',
                    'estructura' => $p['estructura'] ?? 'objeto',
                    'orden'      => $p['orden'] ?? 0,
                    'requerida'  => $p['requerida'] ?? false,
                    'ayuda'      => $p['ayuda'] ?? null,
                    'opciones'   => $p['opciones'] ?? null,
                ]
            );

            if ($pregunta->tipo !== ($p['tipo'] ?? 'texto') || $pregunta->estructura !== ($p['estructura'] ?? 'objeto')) {
                $pregunta->update([
                    'tipo'       => $p['tipo'] ?? 'texto',
                    'estructura' => $p['estructura'] ?? 'objeto',
                ]);
            }

            if (! empty($p['hijos']) && is_array($p['hijos'])) {
                $this->syncPreguntasRecursivas($seccionId, $p['hijos'], (int) $pregunta->id);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Operación aditiva segura, no se eliminan datos
    }
};
