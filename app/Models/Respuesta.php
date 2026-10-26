<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['site_id', 'plantilla_id', 'pregunta_id', 'valor', 'enlace'])]
class Respuesta extends Model
{
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function plantilla(): BelongsTo
    {
        return $this->belongsTo(Plantilla::class);
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Respuesta $respuesta) {
            $valor = $respuesta->valor;
            $decoded = null;

            if (is_string($valor)) {
                $decoded = json_decode($valor, true);
            } elseif (is_array($valor)) {
                $decoded = $valor;
            }

            if (is_array($decoded)) {
                $isTikTok = false;
                foreach ($decoded as $item) {
                    if (is_array($item) && \App\Services\TikTokCoverService::extractVideoId($item) !== null) {
                        $isTikTok = true;
                        break;
                    }
                }

                if ($isTikTok) {
                    $previousVal = $respuesta->getOriginal('valor');
                    $previousDecoded = null;
                    if (is_string($previousVal)) {
                        $previousDecoded = json_decode($previousVal, true);
                    } elseif (is_array($previousVal)) {
                        $previousDecoded = $previousVal;
                    }

                    $synced = \App\Services\TikTokCoverService::syncVideosCovers($decoded, $previousDecoded);
                    $respuesta->valor = json_encode($synced);
                }
            }
        });

        static::deleting(function (Respuesta $respuesta) {
            $valor = $respuesta->valor;
            $decoded = is_string($valor) ? json_decode($valor, true) : (is_array($valor) ? $valor : null);

            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    $id = \App\Services\TikTokCoverService::extractVideoId($item);
                    if ($id) {
                        \App\Services\TikTokCoverService::deleteCover($id);
                    }
                }
            }
        });
    }
}
