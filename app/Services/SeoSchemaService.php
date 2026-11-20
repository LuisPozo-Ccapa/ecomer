<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Site;

class SeoSchemaService
{
    /**
     * Genera el esquema JSON-LD de tipo Store u Organization para la tienda.
     *
     * @param  Site  $site
     * @param  string  $canonicalUrl
     * @return array<string, mixed>
     */
    public static function generateStoreSchema(Site $site, string $canonicalUrl): array
    {
        $estilos = $site->estilos ?? [];
        $logoUrl = $site->imagen ? asset('storage/' . ltrim($site->imagen, '/')) : asset('/favicon.png');

        $schema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'Store',
            'name'       => $site->nombre,
            'url'        => $canonicalUrl,
            'image'      => $logoUrl,
            'priceRange' => '$$',
        ];

        // Redes sociales (sameAs)
        $sameAs = [];
        $redes = $estilos['redes_sociales'] ?? [];
        if (is_array($redes)) {
            foreach (['facebook', 'instagram', 'tiktok', 'youtube', 'twitter'] as $red) {
                if (! empty($redes[$red]) && filter_var($redes[$red], FILTER_VALIDATE_URL)) {
                    $sameAs[] = $redes[$red];
                }
            }
        }
        if (! empty($sameAs)) {
            $schema['sameAs'] = array_values(array_unique($sameAs));
        }

        // Teléfono principal extraído de las acciones de navegación
        $accionesNav = $estilos['acciones_nav'] ?? [];
        if (is_array($accionesNav)) {
            foreach ($accionesNav as $navItem) {
                if (is_array($navItem) && ! empty($navItem['texto'])) {
                    $cleanPhone = preg_replace('/[^0-9+]/', '', (string) $navItem['texto']);
                    if (! empty($cleanPhone) && strlen($cleanPhone) >= 7) {
                        $schema['telephone'] = $cleanPhone;
                        break;
                    }
                }
            }
        }

        // Dirección física
        if (! empty($estilos['direccion'])) {
            $schema['address'] = [
                '@type'          => 'PostalAddress',
                'streetAddress'  => (string) $estilos['direccion'],
                'addressCountry' => 'PE',
            ];
        }

        return $schema;
    }

    /**
     * Genera el esquema JSON-LD de tipo Product para la ficha individual de un producto.
     *
     * @param  Site  $site
     * @param  Producto  $producto
     * @param  string  $canonicalUrl
     * @return array<string, mixed>
     */
    public static function generateProductSchema(Site $site, Producto $producto, string $canonicalUrl): array
    {
        $monedaCodigo = $site->moneda?->codigo ?? 'PEN';

        // Imagen principal
        $imageUrl = null;
        if (! empty($producto->imagenes) && is_array($producto->imagenes)) {
            $first = $producto->imagenes[0] ?? null;
            if ($first) {
                $imageUrl = filter_var($first, FILTER_VALIDATE_URL) ? $first : asset('storage/' . ltrim($first, '/'));
            }
        } elseif (! empty($producto->imagen)) {
            $imageUrl = filter_var($producto->imagen, FILTER_VALIDATE_URL) ? $producto->imagen : asset('storage/' . ltrim($producto->imagen, '/'));
        }

        if (! $imageUrl && $site->imagen) {
            $imageUrl = asset('storage/' . ltrim($site->imagen, '/'));
        }

        // Precio y precio de oferta
        $precioFinal = null;
        if ($producto->precio_oferta && (float) $producto->precio_oferta > 0) {
            $precioFinal = number_format((float) $producto->precio_oferta, 2, '.', '');
        } elseif ($producto->precio && (float) $producto->precio > 0) {
            $precioFinal = number_format((float) $producto->precio, 2, '.', '');
        }

        // Disponibilidad en stock
        $hasStock = true;
        if ($producto->stock !== null) {
            $hasStock = (int) $producto->stock > 0;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Product',
            'name'     => $producto->nombre,
            'url'      => $canonicalUrl,
        ];

        if ($imageUrl) {
            $schema['image'] = $imageUrl;
        }

        $rawDesc = $producto->descripcion ?: $producto->descripcion_corta ?: "{$producto->nombre} en {$site->nombre}";
        $schema['description'] = mb_substr(strip_tags((string) $rawDesc), 0, 300);

        if (! empty($producto->sku)) {
            $schema['sku'] = (string) $producto->sku;
        }

        if (! empty($producto->marca?->titulo)) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name'  => (string) $producto->marca->titulo,
            ];
        }

        if ($precioFinal !== null) {
            $schema['offers'] = [
                '@type'         => 'Offer',
                'price'         => $precioFinal,
                'priceCurrency' => $monedaCodigo,
                'availability'  => $hasStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url'           => $canonicalUrl,
            ];
        }

        return $schema;
    }
}
