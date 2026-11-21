<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Site;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $scheme = (request()->secure() || request()->header('X-Forwarded-Proto') === 'https') ? 'https' : 'http';
        $host = request()->getHost();
        $port = request()->getPort();
        $portStr = ($port && ! in_array($port, [80, 443])) ? ":{$port}" : '';

        $baseUrl = "{$scheme}://{$host}{$portStr}";
        $urls = [];

        // 1. Identificar el sitio correspondiente al dominio actual o primer sitio publicado
        $site = Site::where('estado', 'publicado')
            ->where(function ($q) use ($host) {
                $q->where('slug', $host)
                  ->orWhere('dominio', $host);
            })
            ->with(['plantilla.secciones', 'servicios.servicios', 'marcas', 'tiendas'])
            ->first();

        if (! $site) {
            $site = Site::where('estado', 'publicado')
                ->with(['plantilla.secciones', 'servicios.servicios', 'marcas', 'tiendas'])
                ->orderBy('id')
                ->first();
        }

        if (! $site) {
            return response(view('sitemap.xml', ['urls' => []])->render(), 200, [
                'Content-Type' => 'application/xml; charset=utf-8',
            ]);
        }

        $siteUrl = "{$baseUrl}";
        $siteLastMod = $site->updated_at ? $site->updated_at->toIso8601String() : now()->toIso8601String();

        // 2. Inicio del sitio
        $urls[] = [
            'loc'        => $this->formatUrl($siteUrl),
            'lastmod'    => $siteLastMod,
            'changefreq' => 'daily',
            'priority'   => '1.0',
        ];

        // 3. Secciones dinámicas de la plantilla del sitio (nosotros, contacto, etc.)
        if ($site->plantilla && $site->plantilla->secciones) {
            foreach ($site->plantilla->secciones as $seccion) {
                if (! $seccion->activa || strtolower($seccion->slug) === 'nav' || strtolower($seccion->slug) === 'inicio' || strtolower($seccion->slug) === 'hero') {
                    continue;
                }
                $urls[] = [
                    'loc'        => $this->formatUrl("{$siteUrl}/{$seccion->slug}"),
                    'lastmod'    => $seccion->updated_at ? $seccion->updated_at->toIso8601String() : $siteLastMod,
                    'changefreq' => 'weekly',
                    'priority'   => '0.8',
                ];
            }
        }

        // 4. Servicios (si el sitio tiene servicios activos)
        if ($site->servicios && $site->servicios->where('activo', true)->isNotEmpty()) {
            $urls[] = [
                'loc'        => $this->formatUrl("{$siteUrl}/servicios"),
                'lastmod'    => $siteLastMod,
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ];
        }

        // 5. Productos y Catálogo Comercial
        $tiendaIds = $site->tiendas->pluck('id')->all();
        if (empty($tiendaIds) && $site->tienda_id) {
            $tiendaIds = [$site->tienda_id];
        }
        if (empty($tiendaIds) && $site->plantilla) {
            $tiendaIds = $site->plantilla->tiendas->pluck('id')->all();
        }

        if (! empty($tiendaIds)) {
            $urls[] = [
                'loc'        => $this->formatUrl("{$siteUrl}/productos"),
                'lastmod'    => $siteLastMod,
                'changefreq' => 'daily',
                'priority'   => '0.9',
            ];

            // Indexar productos individuales activos de las tiendas del sitio con soporte de imagen para Google
            $productos = Producto::whereHas('tiendas', fn ($q) => $q->whereIn('tiendas.id', $tiendaIds))
                ->where('activo', true)
                ->orderBy('updated_at', 'desc')
                ->get(['id', 'slug', 'nombre', 'imagen', 'imagenes', 'updated_at']);

            foreach ($productos as $producto) {
                $prodSlug = $producto->slug ?: $producto->id;
                $imgLoc = null;
                if (! empty($producto->imagenes) && is_array($producto->imagenes) && ! empty($producto->imagenes[0])) {
                    $imgLoc = filter_var($producto->imagenes[0], FILTER_VALIDATE_URL) ? $producto->imagenes[0] : asset('storage/' . ltrim($producto->imagenes[0], '/'));
                } elseif (! empty($producto->imagen)) {
                    $imgLoc = filter_var($producto->imagen, FILTER_VALIDATE_URL) ? $producto->imagen : asset('storage/' . ltrim($producto->imagen, '/'));
                }

                $entry = [
                    'loc'        => $this->formatUrl("{$siteUrl}/productos?producto=" . rawurlencode((string) $prodSlug)),
                    'lastmod'    => $producto->updated_at ? $producto->updated_at->toIso8601String() : $siteLastMod,
                    'changefreq' => 'weekly',
                    'priority'   => '0.8',
                ];

                if ($imgLoc) {
                    $entry['image'] = [
                        'loc'   => $this->formatUrl($imgLoc),
                        'title' => htmlspecialchars((string) $producto->nombre, ENT_XML1, 'UTF-8'),
                    ];
                }

                $urls[] = $entry;
            }
        }

        // 6. Marcas (si el sitio tiene marcas registradas)
        $marcas = $site->marcas->where('activa', true);
        if ($marcas->isEmpty() && $site->plantilla) {
            $marcas = $site->plantilla->marcas->where('activa', true);
        }
        if ($marcas->isNotEmpty()) {
            $urls[] = [
                'loc'        => $this->formatUrl("{$siteUrl}/marcas"),
                'lastmod'    => $siteLastMod,
                'changefreq' => 'monthly',
                'priority'   => '0.7',
            ];
        }

        // 7. Catálogo PDF / Virtual (si está activo en sus estilos)
        $estilos = Site::mergeEstilos($site->plantilla->estilos ?? [], $site->estilos ?? []);
        if (! empty($estilos['catalogo']['activo'])) {
            $urls[] = [
                'loc'        => $this->formatUrl("{$siteUrl}/catalogo"),
                'lastmod'    => $siteLastMod,
                'changefreq' => 'weekly',
                'priority'   => '0.7',
            ];
        }

        // 8. Páginas legales y Libro de Reclamaciones
        $legalPages = ['terminos', 'privacidad', 'envios', 'devoluciones', 'libro-de-reclamaciones'];
        foreach ($legalPages as $legalSlug) {
            $urls[] = [
                'loc'        => $this->formatUrl("{$siteUrl}/{$legalSlug}"),
                'lastmod'    => $siteLastMod,
                'changefreq' => 'monthly',
                'priority'   => '0.5',
            ];
        }

        // Desduplicar por 'loc'
        $uniqueUrls = collect($urls)->unique('loc')->values()->all();

        $content = view('sitemap.xml', ['urls' => $uniqueUrls])->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    /**
     * Format and encode URL strictly according to XML Sitemap standard.
     */
    private function formatUrl(string $url): string
    {
        $parsed = parse_url($url);
        if (! $parsed) {
            return htmlspecialchars($url, ENT_QUOTES | ENT_XML1, 'UTF-8');
        }

        $scheme = $parsed['scheme'] ?? (request()->secure() ? 'https' : 'http');
        $host = $parsed['host'] ?? request()->getHost();
        $port = isset($parsed['port']) ? ":{$parsed['port']}" : '';

        $path = $parsed['path'] ?? '';
        $pathSegments = array_map(fn ($seg) => rawurlencode(rawurldecode($seg)), explode('/', $path));
        $encodedPath = implode('/', $pathSegments);

        $queryStr = '';
        if (! empty($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
            $queryStr = '?'.http_build_query($queryParams);
        }

        $fullUrl = "{$scheme}://{$host}{$port}{$encodedPath}{$queryStr}";

        return htmlspecialchars($fullUrl, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
