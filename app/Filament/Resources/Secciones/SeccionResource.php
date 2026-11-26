<?php

namespace App\Filament\Resources\Secciones;

use App\Filament\Resources\Secciones\Pages\ListSecciones;
use App\Filament\Resources\Sites\Pages\EditSite;
use App\Models\Seccion;
use App\Models\Site;
use BackedEnum;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use UnitEnum;

class SeccionResource extends Resource
{
    protected static ?string $model = Seccion::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static int $globalSearchResultsLimit = 8;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Secciones Web';

    protected static ?string $modelLabel = 'Sección Web';

    protected static ?string $pluralModelLabel = 'Secciones Web';

    protected static string|UnitEnum|null $navigationGroup = 'CMS & Diseño Web';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'slug'];
    }

    public static function getGlobalSearchResults(string $search): Collection
    {
        $cleanSearch = trim($search);
        if (mb_strlen($cleanSearch) < 2) {
            return collect();
        }

        $cleanSearchLower = mb_strtolower($cleanSearch);
        $normSearch = mb_strtolower(str_replace(['_', ' ', '-'], '', $cleanSearch));

        // Obtener secciones de plantillas asociadas a sitios publicados
        $secciones = Seccion::query()
            ->with(['plantilla.sites' => fn ($q) => $q->where('estado', 'publicado')])
            ->whereHas('plantilla.sites', fn ($q) => $q->where('estado', 'publicado'))
            ->get();

        if ($secciones->isEmpty()) {
            $secciones = Seccion::with('plantilla.sites')->get();
        }

        $results = collect();

        foreach ($secciones as $seccion) {
            $slug = (string) $seccion->slug;
            $nombre = (string) $seccion->nombre;
            $canonical = self::normalizeCanonicalSlug($slug, $nombre);
            $friendlyTitle = self::getFriendlyTitle($slug, $nombre);
            $ubicacion = self::getUbicacionLabel($slug, $nombre);

            $searchableText = mb_strtolower("{$slug} {$nombre} {$canonical} {$friendlyTitle} {$ubicacion}");
            $normText = mb_strtolower(str_replace(['_', ' ', '-'], '', $searchableText));

            if (str_contains($searchableText, $cleanSearchLower) || str_contains($normText, $normSearch)) {
                $site = $seccion->plantilla?->sites->first() ?? Site::first();
                if (! $site) {
                    continue;
                }

                $url = EditSite::getUrl(['record' => $site, 'seccion' => $canonical]);

                $results->push(new GlobalSearchResult(
                    title: $friendlyTitle,
                    url: $url,
                    details: array_filter([
                        'Ubicación' => $ubicacion,
                        'Tienda' => $site->nombre,
                        'Plantilla' => $seccion->plantilla?->nombre,
                    ]),
                ));
            }
        }

        return $results->unique('url')->take(static::$globalSearchResultsLimit);
    }

    public static function normalizeCanonicalSlug(string $slug, ?string $nombre = null): string
    {
        $slugNorm = strtolower(str_replace(['_', ' ', '-'], '', $slug));
        $nombreNorm = strtolower(str_replace(['_', ' ', '-'], '', $nombre ?? ''));

        return match (true) {
            $slugNorm === 'nosotros' && $nombreNorm !== 'sobrenosotros' => 'nosotros',
            in_array($slugNorm, ['servicios', 'servicio']) || in_array($nombreNorm, ['servicios', 'servicio']) => 'servicios',
            in_array($slugNorm, ['contacto', 'contactos']) || in_array($nombreNorm, ['contacto', 'contactos']) => 'contacto',
            $slugNorm === 'hero' || $slugNorm === 'inicio' || $nombreNorm === 'hero' || $nombreNorm === 'inicio' => 'hero',
            str_contains($slugNorm, 'galeria') || str_contains($nombreNorm, 'galeria') => 'galeria',
            str_contains($slugNorm, 'marca') || str_contains($nombreNorm, 'marca') => 'marcas',
            str_contains($slugNorm, 'liquidacion') || str_contains($nombreNorm, 'liquidacion') => 'liquidacion',
            str_contains($slugNorm, 'oferta') || str_contains($nombreNorm, 'oferta') => 'ofertas',
            str_contains($slugNorm, 'categoria') || str_contains($nombreNorm, 'categoria') => 'categoria',
            str_contains($slugNorm, 'tortasdestacada') || str_contains($nombreNorm, 'tortasdestacada') => 'tortasdestacadas',
            $slugNorm === 'productos' || $nombreNorm === 'productos' => 'productos',
            str_contains($slugNorm, 'banerproducto') || str_contains($nombreNorm, 'banerproducto') => 'banerproducto',
            str_contains($slugNorm, 'sector') || str_contains($nombreNorm, 'sector') => 'sectores',
            str_contains($slugNorm, 'sobrenosotros') || str_contains($nombreNorm, 'sobrenosotros') => 'sobrenosotros',
            str_contains($slugNorm, 'elegirnos') || str_contains($nombreNorm, 'elegirnos') => 'elegirnos',
            str_contains($slugNorm, 'comocomprar') || str_contains($nombreNorm, 'comocomprar') => 'comocomprar',
            str_contains($slugNorm, 'sede') || str_contains($nombreNorm, 'sede') => 'sedes',
            str_contains($slugNorm, 'faq') || str_contains($nombreNorm, 'faq') => 'faq',
            str_contains($slugNorm, 'tiktok') || str_contains($nombreNorm, 'tiktok') => 'tiktok',
            str_contains($slugNorm, 'banerpie') || str_contains($nombreNorm, 'banerpie') => 'banerpie',
            $slugNorm === 'nav' || $nombreNorm === 'nav' || $nombreNorm === 'navegacion' => 'nav',
            default => strtolower(str_replace(['_', ' '], '-', $slug)),
        };
    }

    public static function getFriendlyTitle(string $slug, ?string $nombre = null): string
    {
        $key = self::normalizeCanonicalSlug($slug, $nombre);

        $titles = [
            'hero' => 'Portada Principal / Hero Slider',
            'galeria' => 'Galería / Mosaico Visual',
            'marcas' => 'Marcas Aliadas',
            'liquidacion' => 'Liquidación (Remate)',
            'ofertas' => 'Ofertas Destacadas',
            'categoria' => 'Categorías y Productos en Inicio',
            'tortasdestacadas' => 'Productos Destacados',
            'productos' => 'Vitrina de Productos',
            'banerproducto' => 'Banner Intermedio de Productos',
            'sectores' => 'Sectores Atendidos',
            'sobrenosotros' => 'Sobre Nosotros (Resumen en Inicio)',
            'elegirnos' => '¿Por Qué Elegirnos?',
            'comocomprar' => 'Cómo Comprar / Cotización',
            'sedes' => 'Sedes y Puntos de Atención',
            'faq' => 'Preguntas Frecuentes (FAQ)',
            'tiktok' => 'Videos de TikTok',
            'banerpie' => 'Banner Promocional Inferior',
            'nosotros' => 'Página: Nosotros',
            'servicios' => 'Página: Servicios',
            'contacto' => 'Página: Contacto',
            'nav' => 'Cabecera & Navegación',
        ];

        return $titles[$key] ?? (ucfirst($nombre ?: $slug));
    }

    public static function getUbicacionLabel(string $slug, ?string $nombre = null): string
    {
        $key = self::normalizeCanonicalSlug($slug, $nombre);

        if (in_array($key, ['nosotros', 'servicios', 'contacto'])) {
            return 'Página Secundaria';
        }

        if ($key === 'nav') {
            return 'Cabecera & Menú';
        }

        return 'Página de Inicio';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo_descriptivo')
                    ->label('Sección / Bloque')
                    ->state(fn (Seccion $record): string => self::getFriendlyTitle($record->slug, $record->nombre))
                    ->description(fn (Seccion $record): string => "Identificador: {$record->slug}")
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('nombre', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    })
                    ->weight('medium')
                    ->icon(fn (Seccion $record): string => match (self::getUbicacionLabel($record->slug, $record->nombre)) {
                        'Página Secundaria' => 'heroicon-o-document-duplicate',
                        'Cabecera & Menú' => 'heroicon-o-bars-3',
                        default => 'heroicon-o-home',
                    }),

                TextColumn::make('ubicacion')
                    ->label('Ubicación')
                    ->badge()
                    ->state(fn (Seccion $record): string => self::getUbicacionLabel($record->slug, $record->nombre))
                    ->color(fn (string $state): string => match ($state) {
                        'Página Secundaria' => 'info',
                        'Cabecera & Menú' => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('plantilla.nombre')
                    ->label('Plantilla Base')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                IconColumn::make('activa')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('orden')
            ->actions([
                \Filament\Tables\Actions\Action::make('editar_en_tienda')
                    ->label('Editar en Tienda')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('primary')
                    ->button()
                    ->size('sm')
                    ->url(function (Seccion $record): ?string {
                        $site = Site::where('plantilla_id', $record->plantilla_id)->first() ?? Site::first();
                        if (! $site) {
                            return null;
                        }

                        $canonical = self::normalizeCanonicalSlug($record->slug, $record->nombre);

                        return EditSite::getUrl(['record' => $site, 'seccion' => $canonical]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSecciones::route('/'),
        ];
    }
}
