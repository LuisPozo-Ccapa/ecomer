<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contactos';

    protected $fillable = [
        'folio',
        'site_id',
        'nombre',
        'empresa',
        'telefono',
        'email',
        'sector',
        'producto',
        'mensaje',
        'sitio_nombre',
        'estado',
        'correo_enviado',
        'error_envio',
    ];

    protected $casts = [
        'correo_enviado' => 'boolean',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Genera un identificador único correlativo / folio de cotización.
     */
    public static function generarFolio(): string
    {
        return 'COT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }
}
