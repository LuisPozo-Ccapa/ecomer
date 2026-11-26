<?php

namespace App\Observers;

use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class ProductoObserver
{
    /**
     * Invalida la caché del catálogo PDF pre-renderizado para el sitio indicado.
     */
    public static function clearCatalogCache(?int $siteId = null): void
    {
        $disk = Storage::disk('public');

        if ($siteId) {
            $path = 'catalogos/catalogo_site_' . $siteId . '.pdf';
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        } else {
            if ($disk->exists('catalogos')) {
                foreach ($disk->files('catalogos') as $file) {
                    if (str_ends_with($file, '.pdf')) {
                        $disk->delete($file);
                    }
                }
            }
        }
    }

    public function saved(Producto $producto): void
    {
        self::clearCatalogCache($producto->site_id);
    }

    public function deleted(Producto $producto): void
    {
        self::clearCatalogCache($producto->site_id);
    }

    public function restored(Producto $producto): void
    {
        self::clearCatalogCache($producto->site_id);
    }

    /**
     * Se ejecuta cuando un producto es eliminado permanentemente de la base de datos (Force Delete).
     * Purga las imágenes físicas del disco de almacenamiento para optimizar la cuota del hosting.
     */
    public function forceDeleted(Producto $producto): void
    {
        self::clearCatalogCache($producto->site_id);

        $disk = Storage::disk('public');

        if (! empty($producto->imagen) && $disk->exists($producto->imagen)) {
            $disk->delete($producto->imagen);
        }

        if (is_array($producto->imagenes)) {
            foreach ($producto->imagenes as $imagen) {
                if (! empty($imagen) && $disk->exists($imagen)) {
                    $disk->delete($imagen);
                }
            }
        }
    }
}
