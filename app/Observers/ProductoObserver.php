<?php

namespace App\Observers;

use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class ProductoObserver
{
    /**
     * Se ejecuta cuando un producto es eliminado permanentemente de la base de datos (Force Delete).
     * Purga las imágenes físicas del disco de almacenamiento para optimizar la cuota del hosting.
     */
    public function forceDeleted(Producto $producto): void
    {
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
