<?php

namespace App\Filament\Resources\Plantillas\Pages;

use App\Filament\Resources\Plantillas\PlantillaResource;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Site;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditPlantilla extends EditRecord
{
    protected static string $resource = PlantillaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->load([
            'respuestas',
            'secciones.preguntas.children.children',
        ]);

        $allPreguntas = collect();
        foreach ($this->record->secciones as $seccion) {
            foreach ($seccion->preguntas as $pregunta) {
                $allPreguntas->push($pregunta);
                if ($pregunta->children) {
                    foreach ($pregunta->children as $child) {
                        $allPreguntas->push($child);
                        if ($child->children) {
                            foreach ($child->children as $grandChild) {
                                $allPreguntas->push($grandChild);
                            }
                        }
                    }
                }
            }
        }
        $preguntasById = $allPreguntas->keyBy('id');

        $respuestasMap = $this->record->respuestas
            ->mapWithKeys(function (Respuesta $respuesta) use ($preguntasById): array {
                $pregunta = $preguntasById->get($respuesta->pregunta_id);
                $valor = $respuesta->valor;

                if (is_string($valor)) {
                    $decoded = json_decode($valor, true);
                    if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                        $valor = $decoded;
                    }
                }

                $isRepeaterOrMultiple = $pregunta && (
                    $pregunta->tipo === 'grupo' ||
                    $pregunta->tipo === 'galeria' ||
                    $pregunta->estructura === 'array'
                );

                if ($isRepeaterOrMultiple) {
                    if (! is_array($valor)) {
                        if (is_string($valor) && trim($valor) !== '') {
                            $valor = $pregunta->tipo === 'grupo' ? [] : [trim($valor)];
                        } else {
                            $valor = [];
                        }
                    } else {
                        // Si la pregunta es texto/simple pero estructura array, asegurar que no sean objetos [object Object]
                        if ($pregunta && $pregunta->tipo !== 'grupo') {
                            $valor = array_values(array_filter(array_map(function ($item) {
                                if (is_array($item)) {
                                    return $item['nombre'] ?? $item['titulo'] ?? $item['valor'] ?? $item['label'] ?? null;
                                }

                                return is_scalar($item) ? (string) $item : null;
                            }, $valor)));
                        }
                    }
                } else {
                    // Si la estructura es objeto/valor único pero en BD se guardó un objeto o array, extraer el texto escalar limpio
                    if (is_array($valor)) {
                        $first = reset($valor);
                        if (is_array($first)) {
                            $valor = $first['nombre'] ?? $first['titulo'] ?? $first['valor'] ?? $first['label'] ?? null;
                        } else {
                            $valor = is_scalar($first) ? (string) $first : null;
                        }
                    }
                }

                return [
                    $respuesta->pregunta_id => [
                        'valor' => $valor,
                        'enlace' => $respuesta->enlace,
                        'activar_enlace' => filled($respuesta->enlace),
                    ],
                ];
            })
            ->all();

        foreach ($allPreguntas as $pregunta) {
            if (! isset($respuestasMap[$pregunta->id])) {
                $isRepeaterOrMultiple = $pregunta->tipo === 'grupo' || $pregunta->tipo === 'galeria' || $pregunta->estructura === 'array';
                $respuestasMap[$pregunta->id] = [
                    'valor' => $isRepeaterOrMultiple ? [] : null,
                    'enlace' => null,
                    'activar_enlace' => false,
                ];
            }
        }

        foreach ($respuestasMap as $pid => $item) {
            $respuestasMap[$pid]['valor'] = static::normalizarValoresParaFormulario($item['valor'] ?? null);
        }

        $data['respuestas'] = $respuestasMap;

        return $data;
    }

    public static function normalizarValoresParaFormulario(mixed $valor): mixed
    {
        if (is_null($valor)) {
            return null;
        }

        if (is_string($valor)) {
            $decoded = json_decode($valor, true);
            if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                $valor = $decoded;
            }
        }

        if (! is_array($valor)) {
            return $valor;
        }

        if (count($valor) === 1 && ! array_is_list($valor)) {
            $firstKey = (string) array_key_first($valor);
            $firstVal = reset($valor);
            if (is_string($firstVal) && (preg_match('/^[a-f0-9\-]{32,36}$/i', $firstKey) || str_contains($firstVal, '/'))) {
                return $firstVal;
            }
        }

        foreach ($valor as $k => $v) {
            $valor[$k] = static::normalizarValoresParaFormulario($v);
        }

        return $valor;
    }

    public static function normalizarValoresParaGuardar(mixed $valor): mixed
    {
        if (is_null($valor)) {
            return null;
        }

        if (! is_array($valor)) {
            return $valor;
        }

        if (count($valor) === 1 && ! array_is_list($valor)) {
            $firstKey = (string) array_key_first($valor);
            $firstVal = reset($valor);
            if (is_string($firstVal) && (preg_match('/^[a-f0-9\-]{32,36}$/i', $firstKey) || str_contains($firstVal, '/'))) {
                return $firstVal;
            }
        }

        foreach ($valor as $k => $v) {
            $valor[$k] = static::normalizarValoresParaGuardar($v);
        }

        return $valor;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['estilos']) && is_array($data['estilos'])) {
            $freshRecord = static::getModel()::find($this->record->id);
            $freshEstilos = $freshRecord?->estilos ?? [];
            if (is_string($freshEstilos)) {
                $freshEstilos = json_decode($freshEstilos, true) ?? [];
            }
            if (is_array($freshEstilos)) {
                $data['estilos'] = Site::mergeEstilos($freshEstilos, $data['estilos']);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->guardarRespuestas();
    }

    private function guardarRespuestas(): void
    {
        $respuestas = $this->data['respuestas'] ?? $this->form->getRawState()['respuestas'] ?? [];

        if (empty($respuestas)) {
            return;
        }

        $existingRespuestas = Respuesta::where('plantilla_id', $this->record->id)
            ->whereNull('site_id')
            ->get()
            ->keyBy('pregunta_id');

        $validPreguntaIds = Pregunta::whereIn('id', array_keys($respuestas))->pluck('id')->all();

        $dirtyRespuestas = [];
        foreach ($respuestas as $preguntaId => $item) {
            if (! in_array((int) $preguntaId, $validPreguntaIds, true)) {
                continue;
            }

            $valor = $item['valor'] ?? null;
            $enlace = $item['enlace'] ?? null;

            if (is_array($valor)) {
                $valor = static::normalizarValoresParaGuardar($valor);
                if (is_array($valor)) {
                    $valorClean = array_filter($valor);
                    if (empty($valorClean)) {
                        $valor = null;
                    } else {
                        $valor = json_encode($valor);
                    }
                } elseif (is_string($valor) && trim($valor) === '') {
                    $valor = null;
                }
            }

            $existing = $existingRespuestas->get((int) $preguntaId);

            // Comprobación PATCH: Si no ha cambiado nada con respecto a la BD, omitir actualización
            if ($existing && (string) $existing->valor === (string) $valor && (string) $existing->enlace === (string) $enlace) {
                continue;
            }

            $dirtyRespuestas[$preguntaId] = [
                'valor' => $valor,
                'enlace' => $enlace,
            ];
        }

        if (empty($dirtyRespuestas)) {
            return;
        }

        DB::transaction(function () use ($dirtyRespuestas) {
            foreach ($dirtyRespuestas as $preguntaId => $item) {
                Respuesta::updateOrCreate(
                    ['plantilla_id' => $this->record->id, 'pregunta_id' => $preguntaId],
                    ['site_id' => null, 'valor' => $item['valor'], 'enlace' => $item['enlace']],
                );
            }
        });
    }
}
