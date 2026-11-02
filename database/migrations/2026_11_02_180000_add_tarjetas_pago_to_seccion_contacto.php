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

            // 1. Pregunta Tarjetas_Pago repetible (array)
            $tarjetasPago = Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $parentId,
                    'label'      => 'Tarjetas_Pago',
                ],
                [
                    'tipo'       => 'grupo',
                    'estructura' => 'array',
                    'orden'      => 8,
                    'requerida'  => false,
                    'ayuda'      => 'Tarjetas de métodos de pago en la barra inferior (Efectivo, Transferencia, Pago Fácil, etc.)',
                ]
            );

            // 2. Subpreguntas de cada Tarjeta de Pago
            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $tarjetasPago->id,
                    'label'      => 'Icono',
                ],
                [
                    'tipo'       => 'icono',
                    'estructura' => 'objeto',
                    'orden'      => 1,
                    'requerida'  => false,
                    'ayuda'      => 'Ícono vectorial (ej. FaMoneyBillWave, FaBuildingColumns, FaMobileScreenButton, FaCreditCard)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $tarjetasPago->id,
                    'label'      => 'Imagen',
                ],
                [
                    'tipo'       => 'imagen',
                    'estructura' => 'objeto',
                    'orden'      => 2,
                    'requerida'  => false,
                    'ayuda'      => 'Opcional: Sube tu propia imagen o logo (ej. logo Yape, BCP)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $tarjetasPago->id,
                    'label'      => 'Titulo',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 3,
                    'requerida'  => false,
                    'ayuda'      => 'Título del método de pago (ej. Efectivo, Transferencia Bancaria, Pago Fácil)',
                ]
            );

            Pregunta::updateOrCreate(
                [
                    'seccion_id' => $seccion->id,
                    'parent_id'  => $tarjetasPago->id,
                    'label'      => 'Descripcion',
                ],
                [
                    'tipo'       => 'texto',
                    'estructura' => 'objeto',
                    'orden'      => 4,
                    'requerida'  => false,
                    'ayuda'      => 'Detalle explicativo (ej. Paga en nuestra oficina al momento de la compra)',
                ]
            );

            // 3. Inicializar Tarjetas_Pago en respuestas existentes para que aparezcan visibles de inmediato en el panel
            if ($preguntaContacto) {
                $respuestas = Respuesta::where('pregunta_id', $preguntaContacto->id)->get();
                foreach ($respuestas as $resp) {
                    $val = is_string($resp->valor) ? json_decode($resp->valor, true) : $resp->valor;
                    if (is_array($val)) {
                        $updated = false;
                        foreach ($val as $uuid => &$item) {
                            if (! isset($item['Tarjetas_Pago']) || empty($item['Tarjetas_Pago'])) {
                                $item['Tarjetas_Pago'] = [
                                    (string) Str::uuid() => [
                                        'Icono'       => 'FaMoneyBillWave',
                                        'Titulo'      => 'Efectivo',
                                        'Descripcion' => 'Pago en nuestra oficina al momento de la compra',
                                    ],
                                    (string) Str::uuid() => [
                                        'Icono'       => 'FaBuildingColumns',
                                        'Titulo'      => 'Transferencia Bancaria',
                                        'Descripcion' => 'Transferencias desde cualquier entidad bancaria.',
                                    ],
                                    (string) Str::uuid() => [
                                        'Icono'       => 'FaMobileScreenButton',
                                        'Titulo'      => 'Pago Fácil',
                                        'Descripcion' => 'Pago rápido y seguro desde tu aplicativo.',
                                    ],
                                ];
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

            $tarjetasPago = Pregunta::where('seccion_id', $seccion->id)->where('label', 'Tarjetas_Pago')->first();
            if ($tarjetasPago) {
                Pregunta::where('parent_id', $tarjetasPago->id)->delete();
                $tarjetasPago->delete();
            }
        }
    }
};

