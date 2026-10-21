<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['site_id', 'tienda_id', 'moneda_id', 'categoria_id', 'subcategoria_id', 'marca_id', 'nombre', 'slug', 'descripcion_corta', 'descripcion', 'precio', 'precio_oferta', 'precio_dolares', 'precio_oferta_dolares', 'precio_euros', 'precio_oferta_euros', 'cantidad', 'stock', 'sku', 'codigo_referencia', 'enlace', 'imagen', 'imagenes', 'activo', 'destacado', 'es_liquidacion', 'orden'])]
class Producto extends Model
{
    private static function normalizeStoragePath(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $raw)) {
            $parsed = parse_url($raw);
            $path = $parsed['path'] ?? $raw;
            $raw = $path;
        }

        $raw = preg_replace('#^/?storage/#', '', $raw);
        $raw = preg_replace('#^/?public/#', '', $raw);
        $raw = ltrim($raw, '/');

        return $raw !== '' ? $raw : null;
    }

    public function getImagenAttribute($value): ?string
    {
        return self::normalizeStoragePath($value);
    }

    public function setImagenAttribute($value): void
    {
        $this->attributes['imagen'] = self::normalizeStoragePath($value);
    }

    public function getImagenesAttribute($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(fn ($item) => self::normalizeStoragePath($item), $value)));
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map(fn ($item) => self::normalizeStoragePath($item), $decoded)));
            }
        }

        return [];
    }

    public function setImagenesAttribute($value): void
    {
        $normalized = is_array($value)
            ? array_values(array_filter(array_map(fn ($item) => self::normalizeStoragePath($item), $value)))
            : [];

        $this->attributes['imagenes'] = json_encode($normalized);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function tienda(): BelongsTo
    {
        return $this->belongsTo(Tienda::class);
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria(): BelongsTo
    {
        return $this->belongsTo(Subcategoria::class, 'subcategoria_id');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    public function variantes(): HasMany
    {
        return $this->hasMany(Variante::class)->orderBy('orden');
    }

    public function tiendas(): BelongsToMany
    {
        return $this->belongsToMany(Tienda::class)->withPivot('stock')->withTimestamps();
    }

    public function productoTiendas(): HasMany
    {
        return $this->hasMany(ProductoTienda::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'precio_oferta' => 'decimal:2',
            'precio_dolares' => 'decimal:2',
            'precio_oferta_dolares' => 'decimal:2',
            'precio_euros' => 'decimal:2',
            'precio_oferta_euros' => 'decimal:2',
            'imagenes' => 'array',
            'activo' => 'bool',
            'destacado' => 'bool',
            'es_liquidacion' => 'bool',
        ];
    }
}
