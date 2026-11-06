<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibroReclamacion extends Model
{
    protected $table = 'libro_reclamaciones';

    protected $fillable = [
        'site_id',
        'codigo',
        'tipo',
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellidos',
        'razon_social',
        'email',
        'telefono',
        'departamento',
        'provincia',
        'distrito',
        'direccion',
        'es_menor_edad',
        'apoderado_nombres',
        'apoderado_tipo_documento',
        'apoderado_numero_documento',
        'apoderado_email',
        'apoderado_telefono',
        'tipo_bien',
        'monto_reclamado',
        'moneda',
        'numero_pedido',
        'descripcion_bien',
        'detalle',
        'pedido',
        'estado',
        'fecha_limite',
        'respuesta_empresa',
        'fecha_respuesta',
        'ip_origen',
        'user_agent',
    ];

    protected $casts = [
        'es_menor_edad' => 'boolean',
        'monto_reclamado' => 'decimal:2',
        'fecha_limite' => 'date',
        'fecha_respuesta' => 'datetime',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Genera un código correlativo único según el tipo (REC-YYYY-0001 o QUE-YYYY-0001)
     */
    public static function generarSiguienteCodigo(int $siteId, string $tipo = 'reclamo'): string
    {
        $year = Carbon::now()->year;
        $prefijo = strtolower($tipo) === 'queja' ? 'QUE' : 'REC';

        $ultimoRegistro = self::where('site_id', $siteId)
            ->where('codigo', 'like', "{$prefijo}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $numero = 1;
        if ($ultimoRegistro && preg_match("/{$prefijo}-{$year}-(\d+)/", $ultimoRegistro->codigo, $matches)) {
            $numero = (int) $matches[1] + 1;
        }

        return sprintf('%s-%d-%04d', $prefijo, $year, $numero);
    }

    /**
     * Calcula la fecha límite de respuesta excluyendo sábados y domingos (15 días hábiles legales)
     */
    public static function calcularFechaLimite(int $diasHabiles = 15): Carbon
    {
        $fecha = Carbon::now();
        $agregados = 0;

        while ($agregados < $diasHabiles) {
            $fecha->addDay();
            if (! $fecha->isWeekend()) {
                $agregados++;
            }
        }

        return $fecha;
    }

    /**
     * Nombre completo o razón social del reclamante
     */
    public function getNombreCompletoAttribute(): string
    {
        if ($this->tipo_documento === 'RUC' && ! empty($this->razon_social)) {
            return $this->razon_social;
        }

        return trim("{$this->nombres} {$this->apellidos}");
    }
}

