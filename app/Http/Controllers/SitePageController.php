<?php

namespace App\Http\Controllers;

use App\Http\Resources\v1\ProductoResource;
use App\Models\Marca;
use App\Models\Plantilla;
use App\Models\Pregunta;
use App\Models\Producto;
use App\Models\Respuesta;
use App\Models\Seccion;
use App\Models\Servicio;
use App\Models\Servicios;
use App\Models\Site;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SitePageController extends Controller
{
    public function redirectToFirst(?string $param1 = null, ?string $param2 = null): RedirectResponse
    {
        $siteModel = $this->findSite();
        $primeraSeccion = $siteModel->plantilla?->secciones
            ->where('activa', true)
            ->reject(fn ($s) => strtolower($s->slug) === 'nav')
            ->first();

        $seccionSlug = $primeraSeccion ? $primeraSeccion->slug : 'Inicio';

        return Redirect::to("/{$seccionSlug}");
    }

    public function show(string $param1, ?string $param2 = null, ?string $param3 = null): Response|RedirectResponse
    {
        // Si viene solo un parámetro (ej. /Inicio, /productos, etc.)
        if ($param2 === null && $param3 === null) {
            $dominio = '';
            $siteSlug = null;
            $seccionSlug = $param1;
        } elseif ($param3 !== null) {
            $dominio = $param1;
            $siteSlug = $param2;
            $seccionSlug = $param3;
        } else {
            // Caso de 2 parámetros: si el primer segmento es el slug del sitio (ej. /lismercorp/Inicio), redirigir a /Inicio
            $site = $this->findSite($param1);
            if ($site && strcasecmp($param1, (string) $site->slug) === 0) {
                return Redirect::to("/{$param2}");
            }
            $dominio = $param1;
            $siteSlug = null;
            $seccionSlug = $param2;
        }

        $site = $this->findSite($dominio, $siteSlug);
        $estilos = Site::mergeEstilos($site->plantilla->estilos ?? [], $site->estilos ?? []);
        $visibilidad = $estilos['secciones_visibilidad'] ?? [];

        $seccionesNav = $site->plantilla->secciones
            ->where('activa', true)
            ->reject(function ($s) use ($visibilidad) {
                $slugNorm = strtolower(str_replace(['_', ' '], '-', $s->slug));
                if (in_array($slugNorm, ['nav', 'sectores', 'sobre-nosotros', 'sobre_nosotros', 'como-comprar', 'como_comprar', 'comocomprar', 'cotizacion', 'sedes', 'sucursales', 'puntos-de-venta', 'puntos_venta', 'faq', 'faqs', 'preguntas-frecuentes', 'preguntas_frecuentes', 'libro-de-reclamaciones', 'libro-reclamaciones', 'reclamaciones'])) {
                    return true;
                }

                $nombreNorm = strtolower(str_replace(['_', ' ', '-'], '', $s->nombre));
                $cleanSlug = strtolower(str_replace(['_', ' ', '-'], '', $s->slug));

                $visKey = match (true) {
                    $cleanSlug === 'nosotros' || $nombreNorm === 'nosotros' => 'nosotros',
                    in_array($cleanSlug, ['servicios', 'servicio']) || in_array($nombreNorm, ['servicios', 'servicio']) => 'servicios',
                    in_array($cleanSlug, ['contacto', 'contactos']) || in_array($nombreNorm, ['contacto', 'contactos']) => 'contacto',
                    default => $slugNorm,
                };

                if (isset($visibilidad[$visKey]) && ! (bool) $visibilidad[$visKey]) {
                    return true;
                }
                if (isset($visibilidad[$slugNorm]) && ! (bool) $visibilidad[$slugNorm]) {
                    return true;
                }

                return false;
            });

        $targetSlug = strtolower(str_replace(['_', ' '], '-', $seccionSlug));
        $seccion = $seccionesNav->first(function ($s) use ($targetSlug) {
            $sSlug = strtolower(str_replace(['_', ' '], '-', $s->slug));
            $sNombre = strtolower(str_replace(['_', ' '], '-', $s->nombre));
            if ($targetSlug === 'inicio' || $targetSlug === 'hero') {
                return in_array($sSlug, ['inicio', 'hero']) || in_array($sNombre, ['inicio', 'hero']);
            }

            return $sSlug === $targetSlug || $sNombre === $targetSlug || str_contains($sSlug, $targetSlug) || str_contains($targetSlug, $sSlug);
        });

        // Si se intentó ingresar directamente a una página secundaria que está oculta por switch, redirigir a inicio
        if ($seccion) {
            $seccionSlugNorm = strtolower(str_replace(['_', ' ', '-'], '', $seccion->slug));
            $nombreNorm = strtolower(str_replace(['_', ' ', '-'], '', $seccion->nombre));
            $visKey = match (true) {
                $seccionSlugNorm === 'nosotros' || $nombreNorm === 'nosotros' => 'nosotros',
                in_array($seccionSlugNorm, ['servicios', 'servicio']) || in_array($nombreNorm, ['servicios', 'servicio']) => 'servicios',
                in_array($seccionSlugNorm, ['contacto', 'contactos']) || in_array($nombreNorm, ['contacto', 'contactos']) => 'contacto',
                default => null,
            };
            if ($visKey && isset($visibilidad[$visKey]) && ! (bool) $visibilidad[$visKey]) {
                $homeUrl = $siteSlug ? "/{$dominio}/{$siteSlug}" : ($dominio ? "/{$dominio}" : '/');
                return Redirect::to($homeUrl);
            }
        }

        $siteRespuestas = $site->respuestas->keyBy('pregunta_id')->all();
        $plantillaRespuestas = Cache::remember(
            "plantilla_respuestas_{$site->plantilla_id}",
            300,
            fn () => Respuesta::where('plantilla_id', $site->plantilla_id)->get()->keyBy('pregunta_id')->all()
        );
        $respuestas = $siteRespuestas + $plantillaRespuestas;

        // Pre-cargar el contenido de TODAS las secciones respetando el switch de visibilidad
        $seccionesData = [];
        foreach ($site->plantilla->secciones as $s) {
            $key = strtolower(str_replace(['_', ' '], '-', $s->slug));
            $cleanKey = strtolower(str_replace(['_', ' ', '-'], '', $s->slug));
            $nombreNorm = strtolower(str_replace(['_', ' ', '-'], '', $s->nombre));

            $visKey = match (true) {
                $cleanKey === 'nosotros' || $nombreNorm === 'nosotros' => 'nosotros',
                in_array($cleanKey, ['servicios', 'servicio']) || in_array($nombreNorm, ['servicios', 'servicio']) => 'servicios',
                in_array($cleanKey, ['contacto', 'contactos']) || in_array($nombreNorm, ['contacto', 'contactos']) => 'contacto',
                $cleanKey === 'hero' || $cleanKey === 'inicio' || $nombreNorm === 'hero' || $nombreNorm === 'inicio' => 'hero',
                str_contains($cleanKey, 'galeria') || str_contains($nombreNorm, 'galeria') => 'galeria',
                str_contains($cleanKey, 'marca') || str_contains($nombreNorm, 'marca') => 'marcas',
                str_contains($cleanKey, 'liquidacion') || str_contains($nombreNorm, 'liquidacion') => 'liquidacion',
                str_contains($cleanKey, 'oferta') || str_contains($nombreNorm, 'oferta') => 'ofertas',
                str_contains($cleanKey, 'categoria') || str_contains($nombreNorm, 'categoria') => 'categoria',
                str_contains($cleanKey, 'tortasdestacada') || str_contains($nombreNorm, 'tortasdestacada') => 'tortasdestacadas',
                $cleanKey === 'productos' || $nombreNorm === 'productos' => 'productos',
                str_contains($cleanKey, 'banerproducto') || str_contains($nombreNorm, 'banerproducto') => 'banerproducto',
                str_contains($cleanKey, 'sector') || str_contains($nombreNorm, 'sector') => 'sectores',
                str_contains($cleanKey, 'sobrenosotros') || str_contains($nombreNorm, 'sobrenosotros') => 'sobrenosotros',
                str_contains($cleanKey, 'elegirnos') || str_contains($nombreNorm, 'elegirnos') => 'elegirnos',
                str_contains($cleanKey, 'comocomprar') || str_contains($nombreNorm, 'comocomprar') => 'comocomprar',
                str_contains($cleanKey, 'sede') || str_contains($nombreNorm, 'sede') => 'sedes',
                str_contains($cleanKey, 'faq') || str_contains($nombreNorm, 'faq') => 'faq',
                str_contains($cleanKey, 'tiktok') || str_contains($cleanKey, 'tik-tok') || str_contains($nombreNorm, 'tiktok') => 'tiktok',
                str_contains($cleanKey, 'banerpie') || str_contains($nombreNorm, 'banerpie') => 'banerpie',
                default => $cleanKey,
            };

            $isActiva = true;
            if (isset($visibilidad[$visKey]) && ! (bool) $visibilidad[$visKey]) {
                $isActiva = false;
            } elseif (isset($visibilidad[$cleanKey]) && ! (bool) $visibilidad[$cleanKey]) {
                $isActiva = false;
            } elseif (isset($visibilidad[$key]) && ! (bool) $visibilidad[$key]) {
                $isActiva = false;
            }

            $seccionesData[$key] = [
                'slug' => $s->slug,
                'nombre' => $s->nombre,
                'activa' => $isActiva,
                'contenido' => $isActiva ? self::formatearPreguntas($s->preguntas, $respuestas) : [],
            ];
        }

        $legalSlugsMap = [
            'terminos' => ['type' => 'terminos', 'nombre' => 'Términos y Condiciones', 'slug' => 'terminos-y-condiciones'],
            'terminos-y-condiciones' => ['type' => 'terminos', 'nombre' => 'Términos y Condiciones', 'slug' => 'terminos-y-condiciones'],
            'privacidad' => ['type' => 'privacidad', 'nombre' => 'Política de Privacidad', 'slug' => 'politica-de-privacidad'],
            'politica-privacidad' => ['type' => 'privacidad', 'nombre' => 'Política de Privacidad', 'slug' => 'politica-de-privacidad'],
            'politica-de-privacidad' => ['type' => 'privacidad', 'nombre' => 'Política de Privacidad', 'slug' => 'politica-de-privacidad'],
            'envios' => ['type' => 'envios', 'nombre' => 'Política de Envíos', 'slug' => 'politica-de-envios'],
            'politica-envios' => ['type' => 'envios', 'nombre' => 'Política de Envíos', 'slug' => 'politica-de-envios'],
            'politica-de-envios' => ['type' => 'envios', 'nombre' => 'Política de Envíos', 'slug' => 'politica-de-envios'],
            'devoluciones' => ['type' => 'devoluciones', 'nombre' => 'Políticas de Devolución', 'slug' => 'politica-de-devolucion'],
            'politica-devolucion' => ['type' => 'devoluciones', 'nombre' => 'Políticas de Devolución', 'slug' => 'politica-de-devolucion'],
            'politica-devoluciones' => ['type' => 'devoluciones', 'nombre' => 'Políticas de Devolución', 'slug' => 'politica-de-devolucion'],
            'politica-de-devolucion' => ['type' => 'devoluciones', 'nombre' => 'Políticas de Devolución', 'slug' => 'politica-de-devolucion'],
        ];

        if (array_key_exists($targetSlug, $legalSlugsMap)) {
            $info = $legalSlugsMap[$targetSlug];
            $seccionActiva = [
                'slug' => $info['slug'],
                'nombre' => $info['nombre'],
                'contenido' => [],
                'is_legal' => true,
                'legal_type' => $info['type'],
            ];
        } elseif (in_array($targetSlug, ['libro-de-reclamaciones', 'libro-reclamaciones', 'reclamaciones'])) {
            $seccionActiva = [
                'slug' => 'libro-de-reclamaciones',
                'nombre' => 'Libro de Reclamaciones',
                'contenido' => [],
                'is_libro_reclamaciones' => true,
            ];
        } elseif (! $seccion && in_array($targetSlug, ['inicio', 'hero', 'productos', 'tienda', 'tiendas', 'servicios', 'servicio', 'nosotros', 'sobre-nosotros', 'contacto', 'contactos'])) {
            $canonicalSlug = in_array($targetSlug, ['productos', 'tienda', 'tiendas'])
                ? 'productos'
                : (in_array($targetSlug, ['servicios', 'servicio'])
                    ? 'servicios'
                    : (in_array($targetSlug, ['nosotros', 'sobre-nosotros'])
                        ? 'nosotros'
                        : (in_array($targetSlug, ['contacto', 'contactos']) ? 'contacto' : 'inicio')));

            // Si la página secundaria está oculta por switch, redirigir a inicio para no mostrar vista huérfana
            if ($canonicalSlug !== 'inicio' && isset($visibilidad[$canonicalSlug]) && ! (bool) $visibilidad[$canonicalSlug]) {
                $homeUrl = $siteSlug ? "/{$dominio}/{$siteSlug}" : ($dominio ? "/{$dominio}" : '/');
                return Redirect::to($homeUrl);
            }

            $seccionActiva = [
                'slug' => $canonicalSlug,
                'nombre' => ucfirst($canonicalSlug),
                'contenido' => $seccionesData[$canonicalSlug]['contenido'] ?? $seccionesData['productos']['contenido'] ?? $seccionesData['tiendas']['contenido'] ?? [],
            ];
        } else {
            $seccion = $seccion ?? $seccionesNav->first();
            abort_unless($seccion instanceof Seccion, 404);
            $seccionActiva = $seccionesData[strtolower(str_replace(['_', ' '], '-', $seccion->slug))] ?? [
                'slug' => $seccion->slug,
                'nombre' => $seccion->nombre,
                'contenido' => self::formatearPreguntas($seccion->preguntas, $respuestas),
            ];
        }

        $tiendaIds = $site->tiendas->pluck('id')->all();
        if (empty($tiendaIds) && $site->tienda_id) {
            $tiendaIds = [$site->tienda_id];
        }
        if (empty($tiendaIds) && $site->plantilla) {
            $tiendaIds = $site->plantilla->tiendas->pluck('id')->all();
        }

        $productosQuery = Producto::with(['categoria', 'subcategoria', 'marca', 'variantes', 'tiendas.moneda'])
            ->where('activo', true);

        if (! empty($tiendaIds)) {
            $productosQuery->whereHas('tiendas', fn ($sub) => $sub->whereIn('tiendas.id', $tiendaIds));
        } else {
            $productosQuery->whereRaw('1 = 0');
        }

        $productos = $productosQuery->orderBy('orden')->orderBy('nombre')->get();
        $productosDestacados = $productos->where('destacado', true)->values();
        if ($productosDestacados->isEmpty() && $productos->isNotEmpty()) {
            $productosDestacados = $productos->take(12)->values();
        }

        // Serializar servicios del sitio para el frontend
        $serviciosSitio = $site->servicios
            ->where('activo', true)
            ->map(fn (Servicios $grupo) => [
                'id' => $grupo->id,
                'nombre' => $grupo->nombre,
                'activo' => $grupo->activo,
                'servicios' => $grupo->servicios
                    ->where('activo', true)
                    ->values()
                    ->map(fn (Servicio $s) => [
                        'id' => $s->id,
                        'titulo' => $s->titulo,
                        'subtitulo' => $s->subtitulo,
                        'descripcion' => $s->descripcion,
                        'descripcioncorta' => $s->descripcioncorta,
                        'lista' => $s->lista_array,
                        'icono' => $s->icono,
                        'imagen' => $s->imagen
                            ? asset('storage/'.$s->imagen)
                            : null,
                        'boton' => $s->boton,
                        'url' => $s->url,
                        'orden' => $s->orden,
                    ])
                    ->toArray(),
            ])
            ->values()
            ->toArray();

        // Serializar marcas del sitio para el frontend
        $marcas = $site->marcas->where('activa', true);
        if ($marcas->isEmpty() && $site->plantilla) {
            $marcas = $site->plantilla->marcas->where('activa', true);
        }

        $marcasSitio = $marcas
            ->values()
            ->map(fn (Marca $m) => [
                'id' => $m->id,
                'titulo' => $m->titulo,
                'slug' => $m->slug,
                'imagen' => $m->imagen ? asset('storage/'.$m->imagen) : null,
                'descripcion' => $m->descripcion,
            ])
            ->toArray();

        $seccionesArray = $seccionesNav
            ->map(fn (Seccion $s): array => [
                'slug' => $s->slug,
                'nombre' => $s->nombre,
                'orden' => $s->orden !== null ? (int) $s->orden : 99,
            ])
            ->values()
            ->toArray();

        $prodOrden = isset($estilos['seccion_productos']['orden']) && $estilos['seccion_productos']['orden'] !== '' && $estilos['seccion_productos']['orden'] !== null
            ? (int) $estilos['seccion_productos']['orden']
            : 3;

        $hasProductosInNav = collect($seccionesArray)->contains(
            fn ($s) => in_array(strtolower($s['slug'] ?? ''), ['productos', 'tienda', 'tiendas'])
        );
        $prodVisible = ! isset($visibilidad['productos']) || (bool) $visibilidad['productos'];
        if ($prodVisible && ! empty($tiendaIds) && ! $hasProductosInNav) {
            $seccionesArray[] = [
                'slug' => 'productos',
                'nombre' => 'Productos',
                'orden' => $prodOrden,
            ];
        } elseif (! $prodVisible) {
            $seccionesArray = array_values(array_filter($seccionesArray, fn ($s) => ! in_array(strtolower($s['slug'] ?? ''), ['productos', 'tienda', 'tiendas'])));
        } else {
            foreach ($seccionesArray as &$sec) {
                if (in_array(strtolower($sec['slug'] ?? ''), ['productos', 'tienda', 'tiendas'])) {
                    $sec['orden'] = $prodOrden;
                }
            }
            unset($sec);
        }

        $servOrden = isset($estilos['seccion_servicios']['orden']) && $estilos['seccion_servicios']['orden'] !== '' && $estilos['seccion_servicios']['orden'] !== null
            ? (int) $estilos['seccion_servicios']['orden']
            : 6;

        $hasServiciosInNav = collect($seccionesArray)->contains(
            fn ($s) => in_array(strtolower($s['slug'] ?? ''), ['servicios', 'servicio'])
        );
        $servVisible = ! isset($visibilidad['servicios']) || (bool) $visibilidad['servicios'];
        if ($servVisible && ! empty($serviciosSitio) && ! $hasServiciosInNav) {
            $seccionesArray[] = [
                'slug' => 'servicios',
                'nombre' => 'Servicios',
                'orden' => $servOrden,
            ];
        } elseif (! $servVisible) {
            $seccionesArray = array_values(array_filter($seccionesArray, fn ($s) => ! in_array(strtolower($s['slug'] ?? ''), ['servicios', 'servicio'])));
        } else {
            foreach ($seccionesArray as &$sec) {
                if (in_array(strtolower($sec['slug'] ?? ''), ['servicios', 'servicio'])) {
                    $sec['orden'] = $servOrden;
                }
            }
            unset($sec);
        }

        $catalogoConfig = $estilos['catalogo'] ?? [];
        if (! empty($catalogoConfig['activo'])) {
            $catOrden = isset($catalogoConfig['orden']) && $catalogoConfig['orden'] !== ''
                ? (int) $catalogoConfig['orden']
                : 98;
            $catTitulo = ! empty($catalogoConfig['titulo']) ? $catalogoConfig['titulo'] : 'Catálogo';

            $hasCatalogoInNav = collect($seccionesArray)->contains(
                fn ($s) => strtolower($s['slug'] ?? '') === 'catalogo'
            );
            if (! $hasCatalogoInNav) {
                $seccionesArray[] = [
                    'slug' => 'catalogo',
                    'nombre' => $catTitulo,
                    'orden' => $catOrden,
                    'isCatalogo' => true,
                ];
            } else {
                foreach ($seccionesArray as &$sec) {
                    if (strtolower($sec['slug'] ?? '') === 'catalogo') {
                        $sec['isCatalogo'] = true;
                        $sec['orden'] = $catOrden;
                    }
                }
                unset($sec);
            }
        }

        $marcasOrden = isset($estilos['seccion_marcas']['orden']) && $estilos['seccion_marcas']['orden'] !== '' && $estilos['seccion_marcas']['orden'] !== null
            ? (int) $estilos['seccion_marcas']['orden']
            : 4;

        $hasMarcasInNav = collect($seccionesArray)->contains(
            fn ($s) => in_array(strtolower($s['slug'] ?? ''), ['marcas', 'marca'])
        );
        $marcasVisible = ! isset($visibilidad['marcas']) || (bool) $visibilidad['marcas'];
        if ($marcasVisible && ! empty($marcasSitio) && count($marcasSitio) > 0 && ! $hasMarcasInNav) {
            $seccionesArray[] = [
                'slug' => 'marcas',
                'nombre' => 'Marcas',
                'orden' => $marcasOrden,
            ];
        } elseif (! $marcasVisible) {
            $seccionesArray = array_values(array_filter($seccionesArray, fn ($s) => ! in_array(strtolower($s['slug'] ?? ''), ['marcas', 'marca'])));
        } else {
            foreach ($seccionesArray as &$sec) {
                if (in_array(strtolower($sec['slug'] ?? ''), ['marcas', 'marca'])) {
                    $sec['orden'] = $marcasOrden;
                }
            }
            unset($sec);
        }

        foreach ($seccionesArray as &$sec) {
            $slugLower = strtolower($sec['slug'] ?? '');
            if (in_array($slugLower, ['contacto', 'contactos']) && ($sec['orden'] === 5 || $sec['orden'] === 8 || empty($sec['orden']))) {
                $sec['orden'] = 7;
            }
        }
        unset($sec);

        usort($seccionesArray, fn ($a, $b) => ($a['orden'] ?? 99) <=> ($b['orden'] ?? 99));

        // ─── SEO: calcular título, descripción, imagen y esquema en el servidor ───
        $seoCustomTitle       = $estilos['seo']['title'] ?? null;
        $seoCustomDescription = $estilos['seo']['description'] ?? null;
        $seoCustomKeywords    = $estilos['seo']['keywords'] ?? null;

        $paramProd = request()->query('producto');
        $productoActivo = null;
        if (! empty($paramProd)) {
            $paramProdStr = (string) $paramProd;
            $productoActivo = $productos->first(function ($p) use ($paramProdStr) {
                return (string) $p->id === $paramProdStr
                    || (string) $p->slug === $paramProdStr
                    || strcasecmp((string) $p->nombre, $paramProdStr) === 0;
            });
        }

        $scheme = (request()->secure() || request()->header('X-Forwarded-Proto') === 'https') ? 'https' : 'http';
        $host = request()->getHost();
        $port = request()->getPort();
        $portStr = ($port && ! in_array($port, [80, 443])) ? ":{$port}" : '';
        $currentBaseUrl = "{$scheme}://{$host}{$portStr}";

        if ($productoActivo) {
            $seoTitle = "{$productoActivo->nombre} | {$site->nombre}";
            $rawDescription = $productoActivo->descripcion
                ?: $productoActivo->descripcion_corta
                ?: "Comprar {$productoActivo->nombre} en {$site->nombre}. Envíos y garantía.";
            $seoDescription = mb_substr(strip_tags((string) $rawDescription), 0, 160);

            $seoImage = null;
            if (! empty($productoActivo->imagenes) && is_array($productoActivo->imagenes)) {
                $first = $productoActivo->imagenes[0] ?? null;
                if ($first) {
                    $seoImage = filter_var($first, FILTER_VALIDATE_URL) ? $first : asset('storage/' . ltrim($first, '/'));
                }
            } elseif (! empty($productoActivo->imagen)) {
                $seoImage = filter_var($productoActivo->imagen, FILTER_VALIDATE_URL) ? $productoActivo->imagen : asset('storage/' . ltrim($productoActivo->imagen, '/'));
            }
            if (! $seoImage && $site->imagen) {
                $seoImage = asset('storage/' . ltrim($site->imagen, '/'));
            }

            $canonicalUrl = "{$currentBaseUrl}/productos?producto=" . rawurlencode((string) ($productoActivo->slug ?: $productoActivo->id));
            $productSchema = \App\Services\SeoSchemaService::generateProductSchema($site, $productoActivo, $canonicalUrl);

            $breadcrumbItems = [
                ['name' => 'Inicio', 'url' => "{$currentBaseUrl}"],
                ['name' => 'Productos', 'url' => "{$currentBaseUrl}/productos"],
            ];
            if ($productoActivo->categoria) {
                $breadcrumbItems[] = [
                    'name' => $productoActivo->categoria->nombre,
                    'url'  => "{$currentBaseUrl}/productos?categoria=" . rawurlencode((string) ($productoActivo->categoria->slug ?: $productoActivo->categoria->nombre)),
                ];
            }
            $breadcrumbItems[] = [
                'name' => $productoActivo->nombre,
                'url'  => $canonicalUrl,
            ];
            $breadcrumbSchema = \App\Services\SeoSchemaService::generateBreadcrumbSchema($breadcrumbItems);

            $schemaJsonLd = \App\Services\SeoSchemaService::wrapInGraph([$productSchema, $breadcrumbSchema]);
            $seoType = 'product';
        } else {
            $seoTitle = $seoCustomTitle
                ?: "{$site->nombre} — " . ($seccionActiva['nombre'] ?? 'Inicio');

            $rawDescription = $seoCustomDescription
                ?: ($seccionActiva['descripcion'] ?? null)
                ?: "Bienvenido a {$site->nombre}. Descubre nuestros productos, ofertas y servicios con la mejor calidad y garantía.";

            $seoDescription = mb_substr(strip_tags((string) $rawDescription), 0, 160);

            $customOgImage = ! empty($estilos['seo']['og_image'])
                ? (filter_var($estilos['seo']['og_image'], FILTER_VALIDATE_URL) ? $estilos['seo']['og_image'] : asset('storage/' . ltrim($estilos['seo']['og_image'], '/')))
                : null;

            $seoImage = $customOgImage ?: ($site->imagen ? asset('storage/' . ltrim($site->imagen, '/')) : asset('/favicon.png'));

            $cleanSlug = strtolower($seccionActiva['slug'] ?? 'inicio');
            if (in_array($cleanSlug, ['inicio', 'hero', 'home'])) {
                $canonicalUrl = "{$currentBaseUrl}";
                $schemaJsonLd = \App\Services\SeoSchemaService::generateStoreSchema($site, $canonicalUrl);
            } else {
                $canonicalUrl = "{$currentBaseUrl}/{$seccionActiva['slug']}";
                $storeSchema = \App\Services\SeoSchemaService::generateStoreSchema($site, $canonicalUrl);
                $breadcrumbSchema = \App\Services\SeoSchemaService::generateBreadcrumbSchema([
                    ['name' => 'Inicio', 'url' => "{$currentBaseUrl}"],
                    ['name' => $seccionActiva['nombre'] ?? ucfirst($cleanSlug), 'url' => $canonicalUrl],
                ]);
                $schemaJsonLd = \App\Services\SeoSchemaService::wrapInGraph([$storeSchema, $breadcrumbSchema]);
            }
            $seoType = 'website';
        }

        $googleVerification = $estilos['seo']['google_verification'] ?? null;
        if (! empty($googleVerification)) {
            if (preg_match('/content=[\'"]([^\'"]+)[\'"]/i', $googleVerification, $matches)) {
                $googleVerification = $matches[1];
            } else {
                $googleVerification = preg_replace('/^google-site-verification[:=\s]*/i', '', trim($googleVerification));
            }
        }

        // ─── LCP Preload: primer slide del hero para acelerar la carga en móvil y desktop ───
        $heroPreload = null;
        $heroSecData = $seccionesData['hero'] ?? $seccionesData['inicio'] ?? null;
        if ($heroSecData && ! empty($heroSecData['contenido'])) {
            foreach ($heroSecData['contenido'] as $cItem) {
                if (in_array(strtolower($cItem['label'] ?? ''), ['imagenes', 'imagen', 'img_seccion1'])) {
                    $rawVal = $cItem['valor'] ?? null;
                    $firstSlide = is_array($rawVal) ? ($rawVal[0] ?? null) : $rawVal;
                    if ($firstSlide) {
                        $extractUrl = function ($v) {
                            if (is_string($v)) {
                                return trim($v);
                            }
                            if (is_array($v)) {
                                return is_string($v[0] ?? null) ? trim($v[0]) : null;
                            }
                            return null;
                        };
                        if (is_array($firstSlide)) {
                            $pc = $extractUrl($firstSlide['Imagen_pc'] ?? $firstSlide['imagen_pc'] ?? $firstSlide['pc'] ?? $firstSlide['imagen'] ?? null);
                            $cel = $extractUrl($firstSlide['Imagen_cel'] ?? $firstSlide['imagen_cel'] ?? $firstSlide['cel'] ?? null);
                            $heroPreload = [
                                'pc' => $pc,
                                'cel' => $cel ?: $pc,
                            ];
                        } elseif (is_string($firstSlide)) {
                            $heroPreload = [
                                'pc' => trim($firstSlide),
                                'cel' => trim($firstSlide),
                            ];
                        }
                    }
                    break;
                }
            }
        }

        Inertia::share([
            '_seo' => [
                'title'              => $seoTitle,
                'description'        => $seoDescription,
                'keywords'           => $seoCustomKeywords,
                'image'              => $seoImage,
                'canonical'          => $canonicalUrl,
                'type'               => $seoType,
                'googleVerification' => $googleVerification,
                'schemaJsonLd'       => $schemaJsonLd,
            ],
            '_heroPreload' => $heroPreload,
        ]);
        // ─────────────────────────────────────────────────────────────────────────────────

        return Inertia::render(self::paginaPlantilla($site->plantilla), [
            'site' => [
                'id' => $site->id,
                'nombre' => $site->nombre,
                'slug' => $site->slug,
                'imagen' => $site->imagen ? asset('storage/'.$site->imagen) : null,
                'plantilla_id' => $site->plantilla_id,
            ],
            'dominio' => $dominio,
            'siteSlug' => $siteSlug,
            'tieneTienda' => ! empty($tiendaIds),
            'secciones' => $seccionesArray,
            'seccionActiva' => $seccionActiva,
            'seccionesData' => $seccionesData,
            'productos' => ProductoResource::collection($productos)->resolve(),
            'productosDestacados' => ProductoResource::collection($productosDestacados)->resolve(),
            'estilos' => $estilos,
            'serviciosSitio' => $serviciosSitio,
            'marcasSitio' => $marcasSitio,
        ]);
    }

    private function findSite(?string $dominioOrSlug = null, ?string $siteSlug = null): Site
    {
        $base = Site::query()->where('estado', 'publicado');
        $slug = $siteSlug ?: $dominioOrSlug;

        if ($slug !== null && $slug !== '') {
            $site = (clone $base)
                ->with(['plantilla.secciones.preguntas', 'plantilla.marcas', 'respuestas', 'servicios.servicios', 'marcas', 'tiendas'])
                ->where('slug', $slug)
                ->first();

            if ($site) {
                return $site;
            }
        }

        $site = $base
            ->with(['plantilla.secciones.preguntas', 'plantilla.marcas', 'respuestas', 'servicios.servicios', 'marcas', 'tiendas'])
            ->orderBy('id')
            ->first();

        if (! $site) {
            abort(404);
        }

        return $site;
    }

    public static function paginaPlantilla(Plantilla|string $plantillaOrTipo): string
    {
        if ($plantillaOrTipo instanceof Plantilla) {
            $studlySlug = Str::studly($plantillaOrTipo->slug);

            // 1. Si coincide con una plantilla estándar compilada en Vite, resolver directamente
            //    (Garantiza funcionamiento en producción cuando resources/js/ no se transfiere al cPanel)
            if (in_array($studlySlug, ['Ecomer', 'Tortas', 'Corporativa', 'Anuncio'])) {
                return "sites/{$studlySlug}/Index";
            }

            // 2. Si existe físicamente en desarrollo/local
            if (file_exists(resource_path("js/pages/sites/{$studlySlug}/Index.jsx"))) {
                return "sites/{$studlySlug}/Index";
            }

            if ($plantillaOrTipo->slug === 'plantilla-corporativa') {
                return 'sites/Corporativa/Index';
            }

            $tipo = $plantillaOrTipo->tipo;
        } else {
            $tipo = $plantillaOrTipo;
        }

        return match ($tipo) {
            'ecommerce' => 'sites/Ecomer/Index',
            'landing_page' => 'sites/Corporativa/Index',
            'anuncio' => 'sites/Anuncio/Index',
            default => 'sites/Ecomer/Index',
        };
    }

    public static function formatearPreguntas($preguntas, $respuestas): array
    {
        return $preguntas->whereNull('parent_id')->map(function (Pregunta $pregunta) use ($preguntas, $respuestas): array {
            $respuesta = $respuestas[$pregunta->id] ?? null;

            $children = ($pregunta->relationLoaded('children') && $pregunta->children->isNotEmpty())
                ? $pregunta->children
                : ($preguntas->where('parent_id', $pregunta->id)->isNotEmpty()
                    ? $preguntas->where('parent_id', $pregunta->id)
                    : $pregunta->children);

            $data = [
                'label' => $pregunta->label,
                'tipo' => $pregunta->tipo,
                'estructura' => $pregunta->estructura ?? 'objeto',
                'max_items' => $pregunta->max_items,
                'valor' => self::valorPublico($pregunta->tipo, $respuesta?->valor, $children, $preguntas),
                'enlace' => $respuesta?->enlace,
            ];

            if ($pregunta->tipo === 'grupo') {
                $data['plantilla_campos'] = $children->map(function ($child) {
                    return [
                        'label' => $child->label,
                        'tipo' => $child->tipo,
                        'valor' => null,
                    ];
                })->values()->toArray();
            }

            return $data;
        })->values()->toArray();
    }

    public static function valorPublico(string $tipo, mixed $valor, $children = null, $allPreguntas = null): mixed
    {
        if (is_null($valor)) {
            return null;
        }

        if (is_string($valor) && is_array($decoded = json_decode($valor, true))) {
            $valor = $decoded;
        }

        if ($tipo === 'grupo' && is_array($valor)) {
            $childrenMap = ($children && $children->isNotEmpty()) ? $children->keyBy('label') : collect();

            // Si viene con claves asociativas (ej. UUIDs de Filament), convertir a lista indexada [0, 1, ...]
            if (! empty($valor) && ! array_is_list($valor)) {
                $valor = array_values($valor);
            }

            return array_values(array_map(function ($item) use ($childrenMap, $allPreguntas) {
                if (! is_array($item)) {
                    return $item;
                }

                // Normalizar si viene en formato antiguo [[{"label": "x", "valor": "y"}]]
                if (isset($item[0]) && is_array($item[0]) && isset($item[0]['label'])) {
                    $normalized = [];
                    foreach ($item as $campo) {
                        if (isset($campo['label'])) {
                            $normalized[$campo['label']] = $campo['valor'] ?? null;
                        }
                    }
                    $item = $normalized;
                }

                // Formatear cada campo del objeto descartando sub-campos eliminados de la plantilla
                $formattedItem = [];
                foreach ($item as $key => $val) {
                    $childPregunta = $childrenMap[$key] ?? null;
                    if ($childrenMap->isNotEmpty() && ! $childPregunta) {
                        continue;
                    }

                    $childTipo = $childPregunta ? $childPregunta->tipo : null;

                    if (! $childTipo) {
                        $lowerKey = strtolower((string) $key);
                        if (str_contains($lowerKey, 'imagen') || str_contains($lowerKey, 'foto') || str_contains($lowerKey, 'logo') || str_contains($lowerKey, 'img')) {
                            $childTipo = 'imagen';
                        } else {
                            $childTipo = 'texto';
                        }
                    }

                    $subChildren = null;
                    if ($childPregunta) {
                        if ($allPreguntas && $allPreguntas->isNotEmpty()) {
                            $subChildren = $allPreguntas->where('parent_id', $childPregunta->id);
                        }
                        if ((! $subChildren || $subChildren->isEmpty()) && $childPregunta->relationLoaded('children') && $childPregunta->children->isNotEmpty()) {
                            $subChildren = $childPregunta->children;
                        }
                    }

                    $formattedItem[$key] = self::valorPublico($childTipo, $val, $subChildren, $allPreguntas);
                }

                return $formattedItem;
            }, $valor));
        }

        if ($tipo === 'imagen' || $tipo === 'galeria') {
            if (empty($valor)) {
                return null;
            }

            if ($tipo === 'imagen' && is_array($valor)) {
                $first = reset($valor);
                if (is_string($first)) {
                    $valor = $first;
                }
            }

            $formatUrl = function ($ruta) {
                if (empty($ruta) || ! is_string($ruta)) {
                    return $ruta;
                }
                if (str_starts_with($ruta, 'http://') || str_starts_with($ruta, 'https://')) {
                    if (str_contains($ruta, '127.0.0.1') || str_contains($ruta, 'localhost')) {
                        $cleanPath = preg_replace('/^https?:\/\/[^\/]+\/(storage\/)?/', '', $ruta);
                        if (request()->hasHeader('Host')) {
                            return request()->schemeAndHttpHost().'/storage/'.ltrim($cleanPath, '/');
                        }
                    }

                    return $ruta;
                }

                $cleanPath = ltrim($ruta, '/');
                if (request()->hasHeader('Host')) {
                    return request()->schemeAndHttpHost().'/storage/'.$cleanPath;
                }

                return Storage::disk('public')->url($cleanPath);
            };

            return is_array($valor)
                ? array_values(array_map($formatUrl, $valor))
                : $formatUrl((string) $valor);
        }

        return $valor;
    }

    public function descargarCatalogo(?string $param1 = null, ?string $param2 = null)
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $dominio = $param1;
        $siteSlug = $param2;

        $site = $this->findSite($dominio, $siteSlug);

        $estilos = Site::mergeEstilos($site->plantilla->estilos ?? [], $site->estilos ?? []);
        $catalogoConfig = $estilos['catalogo'] ?? [];

        if (! empty($catalogoConfig['enlace'])) {
            $enlace = trim($catalogoConfig['enlace']);
            if (str_starts_with($enlace, 'http://') || str_starts_with($enlace, 'https://')) {
                return redirect()->away($enlace);
            }
            $cleanPath = preg_replace('/^\/?storage\//', '', $enlace);
            if (Storage::disk('public')->exists($cleanPath)) {
                return response()->download(Storage::disk('public')->path($cleanPath));
            }
        }

        $tiendaIds = $site->tiendas->pluck('id')->all();
        if (empty($tiendaIds) && $site->tienda_id) {
            $tiendaIds = [$site->tienda_id];
        }
        if (empty($tiendaIds) && $site->plantilla) {
            $tiendaIds = $site->plantilla->tiendas->pluck('id')->all();
        }

        $productosQuery = Producto::with(['categoria', 'subcategoria'])
            ->where('activo', true);

        if (! empty($tiendaIds)) {
            $productosQuery->whereHas('tiendas', fn ($q) => $q->whereIn('tiendas.id', $tiendaIds));
        }

        $tipoFiltro = $catalogoConfig['tipo_filtro'] ?? 'todos';
        if ($tipoFiltro === 'categoria' && ! empty($catalogoConfig['categorias'])) {
            $catIds = (array) $catalogoConfig['categorias'];
            $productosQuery->whereIn('categoria_id', $catIds);
        } elseif ($tipoFiltro === 'subcategoria' && ! empty($catalogoConfig['subcategorias'])) {
            $subCatIds = (array) $catalogoConfig['subcategorias'];
            $productosQuery->whereIn('subcategoria_id', $subCatIds);
        }

        $productos = $productosQuery->orderBy('orden')->orderBy('nombre')->get();
        $titulo = $catalogoConfig['titulo'] ?? 'Catálogo de Productos';
        $colorPrimario = $estilos['color_primario'] ?? '#F72F46';
        $filename = Str::slug($titulo).'.pdf';
        $cacheRelPath = 'catalogos/catalogo_site_' . $site->id . '.pdf';

        // Fase 3 (Cache-Aside): Si el PDF pre-renderizado ya existe en disco, servirlo estáticamente (< 50ms, 0 MB RAM)
        if (Storage::disk('public')->exists($cacheRelPath)) {
            return response()->download(Storage::disk('public')->path($cacheRelPath), $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // Compilar con DomPDF una sola vez y persistir en disco
        $pdf = Pdf::loadView('pdf.catalogo', [
            'site' => $site,
            'titulo' => $titulo,
            'productos' => $productos,
            'colorPrimario' => $colorPrimario,
            'estilos' => $estilos,
        ])->setOption('isGdEnabled', false)->setOption('isRemoteEnabled', true);

        if (! Storage::disk('public')->exists('catalogos')) {
            Storage::disk('public')->makeDirectory('catalogos');
        }

        Storage::disk('public')->put($cacheRelPath, $pdf->output());

        return response()->download(Storage::disk('public')->path($cacheRelPath), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
