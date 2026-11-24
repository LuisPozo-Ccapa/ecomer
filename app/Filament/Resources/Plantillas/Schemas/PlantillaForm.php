<?php

namespace App\Filament\Resources\Plantillas\Schemas;

use App\Filament\Forms\Components\IconPicker;
use App\Filament\Forms\Components\LinkPicker;
use App\Models\Categoria;
use App\Models\Plantilla;
use App\Models\Pregunta;
use App\Models\Seccion;
use App\Models\Site;
use App\Models\Subcategoria;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PlantillaForm
{
    public static function configure(Schema $schema): Schema
    {
        $record = $schema->getRecord();
        $respuestasSchema = ($record instanceof Plantilla && $record->exists)
            ? self::respuestasFields($record)
            : [];

        return $schema
            ->components([
                Tabs::make('PlantillaTabs')
                    ->persistTabInQueryString(false)
                    ->tabs([
                        Tab::make('Estructura & Preguntas')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Tabs::make('SubTabsEstructura')
                                    ->persistTabInQueryString(false)
                                    ->tabs([
                                        Tab::make('General & Estilos')
                                            ->icon('heroicon-o-cog')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        Section::make('General')
                                                            ->icon('heroicon-o-cog')
                                                            ->schema([
                                                                Grid::make(2)->schema([
                                                                    TextInput::make('nombre')
                                                                        ->required()
                                                                        ->maxLength(255),
                                                                    TextInput::make('slug')
                                                                        ->required()
                                                                        ->unique(ignoreRecord: true)
                                                                        ->maxLength(255),
                                                                    Select::make('tipo')
                                                                        ->options([
                                                                            'ecommerce' => 'Ecommerce',
                                                                            'landing_page' => 'Landing Page',
                                                                            'anuncio' => 'Anuncio / Promoción',
                                                                        ])
                                                                        ->default('ecommerce')
                                                                        ->required(),
                                                                    Toggle::make('activa')
                                                                        ->default(true),
                                                                    TextInput::make('estilos.direccion')
                                                                        ->label('Dirección de la tienda / negocio')
                                                                        ->placeholder('Ej: Av. Gran Chimú N°680, San Juan de Lurigancho')
                                                                        ->prefixIcon('heroicon-m-map-pin')
                                                                        ->helperText('Dirección física visible en pie de página, cabecera y sección de contacto.')
                                                                        ->columnSpan(2),
                                                                    TextInput::make('estilos.mapa_url')
                                                                        ->label('Enlace o Ubicación de Google Maps (Opcional)')
                                                                        ->placeholder('https://maps.google.com/?q=... o enlace de compartir')
                                                                        ->prefixIcon('heroicon-m-globe-alt')
                                                                        ->helperText('Opcional. Si se deja en blanco, el mapa se generará automáticamente a partir de la dirección.')
                                                                        ->columnSpan(2),
                                                                ]),
                                                                Textarea::make('descripcion')
                                                                    ->rows(2),
                                                                FileUpload::make('imagen')
                                                                    ->webp5Mb(fn (Get $get, ?Model $record) => 'plantillas/'.(Str::slug($get('slug') ?? $record?->slug) ?: 'general'), 'public')
                                                                    ->orientImagesFromExif(false)
                                                                    ->uploadingMessage('Subiendo imagen...')
                                                                    ->deletable(true)
                                                                    ->openable()
                                                                    ->downloadable(),
                                                            ])
                                                            ->columnSpan(1),

                                                        Section::make('Estilos globales')
                                                            ->icon('heroicon-o-paint-brush')
                                                            ->schema([
                                                                Grid::make(2)->schema([
                                                                    ColorPicker::make('estilos.color_primario')
                                                                        ->label('Color primario'),
                                                                    ColorPicker::make('estilos.color_secundario')
                                                                        ->label('Color secundario'),
                                                                    ColorPicker::make('estilos.color_acento')
                                                                        ->label('Color de acento / Cards destacadas')
                                                                        ->helperText('Color para tarjetas destacadas (Llámanos, Asesor WhatsApp y badges). Si se deja vacío, tomará el color primario.')
                                                                        ->nullable(),
                                                                    Select::make('estilos.tipografia_titulos')
                                                                        ->label('Tipografía para títulos')
                                                                        ->options(self::fuentes())
                                                                        ->searchable(),
                                                                    Select::make('estilos.tipografia_texto')
                                                                        ->label('Tipografía para texto')
                                                                        ->options(self::fuentes())
                                                                        ->searchable(),
                                                                    TextInput::make('estilos.radio_bordes')
                                                                        ->label('Radio de bordes')
                                                                        ->placeholder('0.5rem')
                                                                        ->helperText('Ejemplo: 0.5rem, 8px, 4px'),
                                                                    TextInput::make('estilos.espaciado')
                                                                        ->label('Espaciado general')
                                                                        ->placeholder('1rem')
                                                                        ->helperText('Ejemplo: 1rem, 16px, 8px'),
                                                                ]),
                                                            ])
                                                            ->columnSpan(1),
                                                    ])
                                                    ->columnSpanFull(),

                                                Section::make('Almacenes')
                                                    ->icon('heroicon-o-shopping-bag')
                                                    ->description('Asocia los almacenes de la plantilla y configura el subtítulo, título e ícono por defecto para la sección de Productos / Almacenes.')
                                                    ->collapsible()
                                                    ->schema([
                                                        CheckboxList::make('tiendas')
                                                            ->label('Almacenes asociados')
                                                            ->relationship('tiendas', 'nombre')
                                                            ->columns(3)
                                                            ->searchable()
                                                            ->bulkToggleable(),

                                                        Grid::make(4)->schema([
                                                            TextInput::make('estilos.seccion_productos.sub_titulo')
                                                                ->label('Subtítulo de Productos / Almacén')
                                                                ->placeholder('Ej: Catálogo Completo'),
                                                            TextInput::make('estilos.seccion_productos.titulo')
                                                                ->label('Título de Productos / Almacén')
                                                                ->placeholder('Ej: Nuestras Tortas y Creaciones'),
                                                            IconPicker::make('estilos.seccion_productos.icono')
                                                                ->label('Ícono de Productos / Almacén'),
                                                            TextInput::make('estilos.seccion_productos.orden')
                                                                ->label('Orden en el Menú')
                                                                ->numeric()
                                                                ->placeholder('Ej: 2'),
                                                        ]),
                                                    ])
                                                    ->columnSpanFull(),

                                                Section::make('Servicios')
                                                    ->icon('heroicon-o-wrench-screwdriver')
                                                    ->collapsible()
                                                    ->schema([
                                                        Grid::make(2)->schema([
                                                            CheckboxList::make('servicios')
                                                                ->label('Servicios asociados')
                                                                ->relationship('servicios', 'nombre')
                                                                ->columns(3)
                                                                ->searchable()
                                                                ->bulkToggleable(),
                                                            TextInput::make('estilos.seccion_servicios.orden')
                                                                ->label('Orden en el Menú')
                                                                ->numeric()
                                                                ->placeholder('Ej: 3'),
                                                        ]),
                                                    ])
                                                    ->columnSpanFull(),

                                                Section::make('Marcas')
                                                    ->icon('heroicon-o-tag')
                                                    ->collapsible()
                                                    ->schema([
                                                        Grid::make(2)->schema([
                                                            CheckboxList::make('marcas')
                                                                ->label('Marcas asociadas')
                                                                ->relationship('marcas', 'titulo')
                                                                ->columns(3)
                                                                ->searchable()
                                                                ->bulkToggleable(),
                                                            TextInput::make('estilos.seccion_marcas.orden')
                                                                ->label('Orden en el Menú')
                                                                ->numeric()
                                                                ->placeholder('Ej: 5'),
                                                        ]),
                                                    ])
                                                    ->columnSpanFull(),

                                                Section::make('Catálogo de Productos')
                                                    ->icon('heroicon-o-book-open')
                                                    ->description('Configura la disponibilidad del catálogo por defecto para la plantilla.')
                                                    ->collapsible()
                                                    ->schema([
                                                        Toggle::make('estilos.catalogo.activo')
                                                            ->label('Activar Catálogo en la Navegación')
                                                            ->helperText('Muestra el enlace de Catálogo en el menú superior, menú móvil y footer.')
                                                            ->default(false)
                                                            ->live(),

                                                        Grid::make(3)->schema([
                                                            TextInput::make('estilos.catalogo.titulo')
                                                                ->label('Título en el Menú')
                                                                ->placeholder('Ej: Catálogo, Ver Catálogo, Catálogo PDF')
                                                                ->default('Catálogo')
                                                                ->visible(fn (Get $get) => (bool) $get('estilos.catalogo.activo')),

                                                            TextInput::make('estilos.catalogo.enlace')
                                                                ->label('Enlace Externo o PDF (Opcional)')
                                                                ->placeholder('https://ejemplo.com/catalogo.pdf')
                                                                ->helperText('Si ingresas un enlace, el botón abrirá este archivo/URL. Si lo dejas vacío, cargará el catálogo dinámico de productos.')
                                                                ->visible(fn (Get $get) => (bool) $get('estilos.catalogo.activo'))
                                                                ->live(onBlur: true),

                                                            TextInput::make('estilos.catalogo.orden')
                                                                ->label('Orden en el Menú')
                                                                ->numeric()
                                                                ->placeholder('Ej: 4')
                                                                ->visible(fn (Get $get) => (bool) $get('estilos.catalogo.activo')),
                                                        ]),

                                                        Section::make('Filtro de Productos para el Catálogo')
                                                            ->description('Aplica cuando no se proporciona enlace externo.')
                                                            ->visible(fn (Get $get) => (bool) $get('estilos.catalogo.activo') && blank($get('estilos.catalogo.enlace')))
                                                            ->schema([
                                                                Select::make('estilos.catalogo.tipo_filtro')
                                                                    ->label('Incluir en el catálogo')
                                                                    ->options([
                                                                        'todos' => 'Todos los productos',
                                                                        'categoria' => 'Por Categoría',
                                                                        'subcategoria' => 'Por Subcategoría',
                                                                    ])
                                                                    ->default('todos')
                                                                    ->live(),

                                                                MultiSelect::make('estilos.catalogo.categorias')
                                                                    ->label('Categorías a incluir')
                                                                    ->options(fn () => Categoria::pluck('nombre', 'id'))
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->visible(fn (Get $get) => $get('estilos.catalogo.tipo_filtro') === 'categoria'),

                                                                MultiSelect::make('estilos.catalogo.subcategorias')
                                                                    ->label('Subcategorías a incluir')
                                                                    ->options(fn () => Subcategoria::with('categoria')->get()->mapWithKeys(fn ($sub) => [$sub->id => ($sub->categoria ? $sub->categoria->nombre.' > ' : '').$sub->nombre]))
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->visible(fn (Get $get) => $get('estilos.catalogo.tipo_filtro') === 'subcategoria'),
                                                            ]),
                                                    ])
                                                    ->columnSpanFull(),

                                                Section::make('Datos de Contacto Globales')
                                                    ->icon('heroicon-o-phone')
                                                    ->description('Define números de WhatsApp, correos electrónicos y horarios por defecto para la plantilla.')
                                                    ->collapsible()
                                                    ->schema([
                                                        Repeater::make('estilos.acciones_nav')
                                                            ->label('WhatsApp, Correos y Contacto')
                                                            ->defaultItems(0)
                                                            ->schema([
                                                                Grid::make(3)->schema([
                                                                    Select::make('icono')
                                                                        ->label('Tipo / Ícono')
                                                                        ->options([
                                                                            'FaWhatsapp' => 'WhatsApp',
                                                                            'FaEnvelope' => 'Correo Electrónico',
                                                                            'FaBriefcase' => 'Correo de Trabajo / Maletín',
                                                                            'FaPhone' => 'Teléfono Fijo / Móvil',
                                                                            'FaClock' => 'Horario de Atención',
                                                                            'FaLocationDot' => 'Ubicación / Dirección',
                                                                        ])
                                                                        ->default('FaWhatsapp')
                                                                        ->required(),
                                                                    TextInput::make('Label')
                                                                        ->label('Etiqueta')
                                                                        ->placeholder('Ej. WhatsApp Ventas, Correo, Horario')
                                                                        ->required(),
                                                                    Textarea::make('texto')
                                                                        ->label('Número(s) o Correo(s)')
                                                                        ->placeholder("Ej. 987654321 o correo@gmail.com\n(Múltiples contactos ej: edward 916628409\nluciana 916354345)")
                                                                        ->helperText('Puedes poner nombre y número por línea para diferenciarlos en el WhatsApp flotante.')
                                                                        ->rows(2)
                                                                        ->required(),
                                                                ]),
                                                            ])
                                                            ->collapsible()
                                                            ->itemLabel(fn (array $state): ?string => ($state['Label'] ?? '') ? ($state['Label'].': '.($state['texto'] ?? '')) : null)
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->columnSpanFull(),

                                                self::redesSocialesSection(),

                                                self::metodosPagoSection(),
                                            ]),

                                        Tab::make('Secciones & Preguntas')
                                            ->icon('heroicon-o-squares-2x2')
                                            ->schema([
                                                Repeater::make('secciones')
                                                    ->relationship()
                                                    ->label('Secciones de la plantilla')
                                                    ->reorderableWithDragAndDrop()
                                                    ->collapsible()
                                                    ->collapsed()
                                                    ->itemLabel(fn (array $state): ?string => isset($state['nombre']) && filled($state['nombre']) ? "Sección: {$state['nombre']} (".($state['slug'] ?? 'sin-slug').')' : 'Nueva Sección')
                                                    ->grid(['default' => 1])
                                                    ->schema([
                                                        Grid::make(4)->schema([
                                                            TextInput::make('nombre')
                                                                ->required()
                                                                ->maxLength(255),
                                                            TextInput::make('slug')
                                                                ->required()
                                                                ->maxLength(255),
                                                            TextInput::make('orden')
                                                                ->numeric()
                                                                ->default(0),
                                                            Toggle::make('activa')
                                                                ->default(true),
                                                        ]),
                                                        Repeater::make('preguntas')
                                                            ->relationship()
                                                            ->label('Preguntas')
                                                            ->reorderableWithDragAndDrop()
                                                            ->collapsible()
                                                            ->collapsed()
                                                            ->itemLabel(fn (array $state): ?string => isset($state['label']) && filled($state['label']) ? "Pregunta: {$state['label']} [".($state['tipo'] ?? 'texto').']' : 'Nueva Pregunta')
                                                            ->schema([
                                                                Grid::make(7)->schema([
                                                                    TextInput::make('label')
                                                                        ->required()
                                                                        ->maxLength(255)
                                                                        ->placeholder('portada, titulo1, subtitulo...'),
                                                                    Select::make('tipo')
                                                                        ->options(self::tiposPregunta())
                                                                        ->required()
                                                                        ->live(onBlur: true)
                                                                        ->default('texto'),
                                                                    Select::make('estructura')
                                                                        ->options([
                                                                            'objeto' => 'Objeto (Valor único)',
                                                                            'array' => 'Array (Múltiple/Repetible)',
                                                                        ])
                                                                        ->default('objeto')
                                                                        ->live(onBlur: true)
                                                                        ->required(),
                                                                    TextInput::make('max_items')
                                                                        ->numeric()
                                                                        ->label('Límite')
                                                                        ->placeholder('Infinito')
                                                                        ->visible(fn (Get $get): bool => $get('estructura') === 'array'),
                                                                    TextInput::make('orden')
                                                                        ->numeric()
                                                                        ->default(0),
                                                                    Toggle::make('requerida')
                                                                        ->default(false),
                                                                    Textarea::make('ayuda')
                                                                        ->rows(1)
                                                                        ->columnSpanFull(),
                                                                ]),
                                                                Repeater::make('children')
                                                                    ->relationship()
                                                                    ->label('Campos del Conjunto (Plantilla Base)')
                                                                    ->visible(fn (Get $get): bool => $get('tipo') === 'grupo')
                                                                    ->schema([
                                                                        Grid::make(6)->schema([
                                                                            TextInput::make('label')
                                                                                ->required()
                                                                                ->maxLength(255),
                                                                            Select::make('tipo')
                                                                                ->options(self::tiposPregunta())
                                                                                ->required()
                                                                                ->live(onBlur: true)
                                                                                ->default('texto'),
                                                                            Select::make('estructura')
                                                                                ->options([
                                                                                    'objeto' => 'Objeto',
                                                                                    'array' => 'Array',
                                                                                ])
                                                                                ->default('objeto')
                                                                                ->live(onBlur: true)
                                                                                ->required(),
                                                                            TextInput::make('max_items')
                                                                                ->numeric()
                                                                                ->label('Límite')
                                                                                ->placeholder('Infinito')
                                                                                ->visible(fn (Get $get): bool => $get('estructura') === 'array'),
                                                                            TextInput::make('orden')
                                                                                ->numeric()
                                                                                ->default(0),
                                                                            Toggle::make('requerida')
                                                                                ->default(false),
                                                                            TextInput::make('ayuda')
                                                                                ->placeholder('Texto de ayuda')
                                                                                ->columnSpan(1),
                                                                        ]),

                                                                        // ── Sub-campos cuando el hijo es también tipo grupo ──
                                                                        Repeater::make('children')
                                                                            ->relationship()
                                                                            ->label('Sub-campos del grupo hijo')
                                                                            ->visible(fn (Get $get): bool => $get('tipo') === 'grupo')
                                                                            ->schema([
                                                                                Grid::make(6)->schema([
                                                                                    TextInput::make('label')
                                                                                        ->required()
                                                                                        ->maxLength(255),
                                                                                    Select::make('tipo')
                                                                                        ->options(self::tiposPregunta())
                                                                                        ->required()
                                                                                        ->live(onBlur: true)
                                                                                        ->default('texto'),
                                                                                    Select::make('estructura')
                                                                                        ->options([
                                                                                            'objeto' => 'Objeto',
                                                                                            'array' => 'Array',
                                                                                        ])
                                                                                        ->default('objeto')
                                                                                        ->live(onBlur: true)
                                                                                        ->required(),
                                                                                    TextInput::make('max_items')
                                                                                        ->numeric()
                                                                                        ->label('Límite')
                                                                                        ->placeholder('Infinito')
                                                                                        ->visible(fn (Get $get): bool => $get('estructura') === 'array'),
                                                                                    TextInput::make('orden')
                                                                                        ->numeric()
                                                                                        ->default(0),
                                                                                    Toggle::make('requerida')
                                                                                        ->default(false),
                                                                                    TextInput::make('ayuda')
                                                                                        ->placeholder('Texto de ayuda')
                                                                                        ->columnSpan(1),
                                                                                ]),
                                                                            ])
                                                                            ->itemLabel(fn (array $state): ?string => isset($state['label']) ? "Sub-campo: {$state['label']}" : null)
                                                                            ->reorderableWithDragAndDrop()
                                                                            ->collapsible()
                                                                            ->collapsed()
                                                                            ->addActionLabel('+ Agregar sub-campo')
                                                                            ->columns(1),
                                                                    ])
                                                                    ->itemLabel(fn (array $state): ?string => isset($state['label']) ? "Campo: {$state['label']}" : null)
                                                                    ->reorderableWithDragAndDrop()
                                                                    ->collapsible()
                                                                    ->collapsed()
                                                                    ->columns(1),
                                                            ])
                                                            ->columns(1),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Respuestas por Defecto')
                            ->icon('heroicon-o-document-text')
                            ->visible(fn (?Plantilla $record): bool => filled($record?->id))
                            ->schema($respuestasSchema),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @return array<int, Component>
     */
    public static function respuestasFields(Plantilla $plantilla): array
    {
        if (! $plantilla->relationLoaded('secciones')) {
            $plantilla->load(['secciones.preguntas' => fn ($q) => $q->orderBy('orden')->with('children.children.children')]);
        }

        $secciones = $plantilla->secciones->sortBy('orden');

        if ($secciones->isEmpty()) {
            return [];
        }

        // Definición de metadatos, títulos amigables y orden para los bloques de la Página de Inicio (Home)
        $inicioMetadata = [
            'hero' => [
                'prioridad' => 1,
                'titulo' => 'Portada Principal / Hero Slider',
                'descripcion' => 'Carrusel interactivo de bienvenida con imágenes adaptadas para PC, Tablet y Celular.',
                'icono' => 'heroicon-o-sparkles',
            ],
            'inicio' => [
                'prioridad' => 1,
                'titulo' => 'Portada Principal / Hero Slider',
                'descripcion' => 'Carrusel interactivo de bienvenida con imágenes adaptadas para PC, Tablet y Celular.',
                'icono' => 'heroicon-o-sparkles',
            ],
            'galeria' => [
                'prioridad' => 2,
                'titulo' => 'Galería / Mosaico Visual',
                'descripcion' => 'Mosaico visual de fotos o banners secundarios destacados.',
                'icono' => 'heroicon-o-photo',
            ],
            'marcas' => [
                'prioridad' => 3,
                'titulo' => 'Marcas Aliadas',
                'descripcion' => 'Carrusel de logotipos de fabricantes y marcas asociadas.',
                'icono' => 'heroicon-o-tag',
            ],
            'liquidacion' => [
                'prioridad' => 4,
                'titulo' => 'Liquidación (Filtro de Productos en Remate)',
                'descripcion' => 'Configuración de la etiqueta/filtro para listar productos en liquidación en la tienda y menú.',
                'icono' => 'heroicon-o-receipt-percent',
            ],
            'ofertas' => [
                'prioridad' => 5,
                'titulo' => 'Ofertas Destacadas (Banner Promocional)',
                'descripcion' => 'Banner gráfico y categoría vinculada para la sección de ofertas en la página de inicio.',
                'icono' => 'heroicon-o-fire',
            ],
            'categoria' => [
                'prioridad' => 6,
                'titulo' => 'Categorías y Productos en Inicio',
                'descripcion' => 'Vitrinas de categorías o productos destacados en la portada.',
                'icono' => 'heroicon-o-shopping-bag',
            ],
            'tortasdestacadas' => [
                'prioridad' => 6,
                'titulo' => 'Productos Destacados',
                'descripcion' => 'Selección de productos estrella para la portada.',
                'icono' => 'heroicon-o-star',
            ],
            'productos' => [
                'prioridad' => 6,
                'titulo' => 'Vitrina de Productos',
                'descripcion' => 'Listado o grid de productos visibles en la portada.',
                'icono' => 'heroicon-o-shopping-bag',
            ],
            'banerproducto' => [
                'prioridad' => 7,
                'titulo' => 'Banner Intermedio de Productos',
                'descripcion' => 'Banner publicitario intercalado entre los bloques de productos de la tienda.',
                'icono' => 'heroicon-o-photo',
            ],
            'sectores' => [
                'prioridad' => 8,
                'titulo' => 'Sectores Atendidos',
                'descripcion' => 'Tarjetas informativas de sectores e industrias (Minería, Construcción, Comercio, etc.).',
                'icono' => 'heroicon-o-building-office-2',
            ],
            'sobrenosotros' => [
                'prioridad' => 9,
                'titulo' => 'Sobre Nosotros (Resumen en Inicio)',
                'descripcion' => 'Resumen institucional, imagen destacada, métricas clave y enlace a la página completa.',
                'icono' => 'heroicon-o-user-group',
            ],
            'elegirnos' => [
                'prioridad' => 10,
                'titulo' => '¿Por Qué Elegirnos?',
                'descripcion' => 'Beneficios principales, fortalezas y valores agregados del negocio.',
                'icono' => 'heroicon-o-check-badge',
            ],
            'comocomprar' => [
                'prioridad' => 11,
                'titulo' => 'Cómo Comprar / Guía de Cotización',
                'descripcion' => 'Pasos explicativos para comprar o solicitar cotizaciones en la tienda.',
                'icono' => 'heroicon-o-shopping-cart',
            ],
            'sedes' => [
                'prioridad' => 12,
                'titulo' => 'Sedes y Puntos de Atención',
                'descripcion' => 'Locales físicos, direcciones, horarios de atención y mapas en el inicio.',
                'icono' => 'heroicon-o-map-pin',
            ],
            'faq' => [
                'prioridad' => 13,
                'titulo' => 'Preguntas Frecuentes (FAQ)',
                'descripcion' => 'Acordeón de dudas habituales y respuestas para los clientes.',
                'icono' => 'heroicon-o-question-mark-circle',
            ],
            'tiktok' => [
                'prioridad' => 14,
                'titulo' => 'Videos de TikTok',
                'descripcion' => 'Feed multimedia interactivo con IDs numéricos de videos de TikTok.',
                'icono' => 'heroicon-o-video-camera',
            ],
            'banerpie' => [
                'prioridad' => 15,
                'titulo' => 'Banner Promocional Inferior',
                'descripcion' => 'Banner de ancho completo de llamada a la acción previo al pie de página.',
                'icono' => 'heroicon-o-megaphone',
            ],
        ];

        // Metadatos para Páginas Secundarias independientes
        $secundariasMetadata = [
            'nosotros' => [
                'titulo' => 'Página: Nosotros',
                'descripcion' => 'Página institucional completa: Portada, Historia cronológica, Misión, Visión, Valores y CTA.',
                'icono' => 'heroicon-o-information-circle',
            ],
            'servicios' => [
                'titulo' => 'Página: Servicios',
                'descripcion' => 'Página completa de catálogo de servicios especializados, proceso y cotización.',
                'icono' => 'heroicon-o-wrench-screwdriver',
            ],
            'contacto' => [
                'titulo' => 'Página: Contacto',
                'descripcion' => 'Página completa de contacto, sedes, canales de atención y formulario.',
                'icono' => 'heroicon-o-envelope',
            ],
        ];

        $bloquesInicio = [];
        $tabsSecundarias = [];
        $camposNav = [];
        $bloquesOtras = [];

        foreach ($secciones as $seccion) {
            $fields = self::camposDeSeccion($seccion);
            if (empty($fields)) {
                continue;
            }

            $slugNorm = self::normalizarSlug($seccion->slug);
            $nombreNorm = self::normalizarSlug($seccion->nombre);

            // 1. Cabecera y Navegación (Nav)
            if (in_array($slugNorm, ['nav', 'navfooter']) || in_array($nombreNorm, ['nav', 'navfooter', 'cabecera'])) {
                $camposNav[] = Section::make('Barra Superior de Anuncios y Accesos Rápidos')
                    ->description('Personaliza el mensaje promocional superior (ej. "Hasta un 15% de descuento") y los accesos directos.')
                    ->icon('heroicon-o-bars-3')
                    ->schema($fields)
                    ->columns(2);
                continue;
            }

            // 2. Páginas Secundarias (Nosotros completa, Servicios completa, Contacto completa)
            if ($slugNorm === 'nosotros' && $nombreNorm !== 'sobrenosotros') {
                $meta = $secundariasMetadata['nosotros'];
                $tabsSecundarias[] = Tab::make($meta['titulo'])
                    ->icon($meta['icono'])
                    ->schema([
                        Section::make($meta['titulo'])
                            ->description($meta['descripcion'])
                            ->schema($fields)
                            ->columns(2),
                    ]);
                continue;
            }

            if (in_array($slugNorm, ['servicios', 'servicio']) || in_array($nombreNorm, ['servicios', 'servicio'])) {
                $meta = $secundariasMetadata['servicios'];
                $tabsSecundarias[] = Tab::make($meta['titulo'])
                    ->icon($meta['icono'])
                    ->schema([
                        Section::make($meta['titulo'])
                            ->description($meta['descripcion'])
                            ->schema($fields)
                            ->columns(2),
                    ]);
                continue;
            }

            if (in_array($slugNorm, ['contacto', 'contactos']) || in_array($nombreNorm, ['contacto', 'contactos'])) {
                $meta = $secundariasMetadata['contacto'];
                $tabsSecundarias[] = Tab::make($meta['titulo'])
                    ->icon($meta['icono'])
                    ->schema([
                        Section::make($meta['titulo'])
                            ->description($meta['descripcion'])
                            ->schema($fields)
                            ->columns(2),
                    ]);
                continue;
            }

            // 3. Bloques de Página de Inicio (Home)
            $matchedKey = null;
            foreach ($inicioMetadata as $key => $meta) {
                if ($slugNorm === $key || $nombreNorm === $key || str_contains($slugNorm, $key) || str_contains($nombreNorm, $key)) {
                    $matchedKey = $key;
                    break;
                }
            }

            if ($matchedKey) {
                $meta = $inicioMetadata[$matchedKey];
                $bloquesInicio[] = [
                    'prioridad' => $meta['prioridad'],
                    'titulo' => $meta['titulo'],
                    'descripcion' => $meta['descripcion'],
                    'icono' => $meta['icono'],
                    'fields' => $fields,
                ];
                continue;
            }

            // 4. Otras Secciones (adicionales o personalizadas)
            $bloquesOtras[] = Section::make($seccion->nombre)
                ->description("Sección adicional: {$seccion->slug}")
                ->icon('heroicon-o-document-text')
                ->collapsible()
                ->collapsed(true)
                ->schema($fields)
                ->columns(2);
        }

        // Ordenar los bloques de inicio según el flujo natural de la tienda
        usort($bloquesInicio, fn ($a, $b) => $a['prioridad'] <=> $b['prioridad']);

        $componentesInicio = [];
        foreach ($bloquesInicio as $idx => $bloque) {
            $num = $idx + 1;
            $componentesInicio[] = Section::make("{$num}. {$bloque['titulo']}")
                ->description($bloque['descripcion'])
                ->icon($bloque['icono'])
                ->collapsible()
                ->collapsed($idx > 0) // El primer bloque abierto, los demás colapsados
                ->schema($bloque['fields'])
                ->columns(2);
        }

        // Ensamblar las Pestañas Principales del CMS
        $tabsPrincipales = [];

        if (! empty($componentesInicio)) {
            $tabsPrincipales[] = Tab::make('Página de Inicio')
                ->icon('heroicon-o-home')
                ->badge(count($componentesInicio) . ' bloques')
                ->schema($componentesInicio);
        }

        if (! empty($tabsSecundarias)) {
            $tabsPrincipales[] = Tab::make('Páginas Secundarias')
                ->icon('heroicon-o-document-duplicate')
                ->badge(count($tabsSecundarias) . ' páginas')
                ->schema([
                    Tabs::make('SubTabsPaginasSecundarias')
                        ->persistTabInQueryString(false)
                        ->tabs($tabsSecundarias)
                        ->columnSpanFull(),
                ]);
        }

        if (! empty($camposNav)) {
            $tabsPrincipales[] = Tab::make('Cabecera & Navegación')
                ->icon('heroicon-o-bars-3')
                ->schema($camposNav);
        }

        if (! empty($bloquesOtras)) {
            $tabsPrincipales[] = Tab::make('Otras Secciones')
                ->icon('heroicon-o-squares-plus')
                ->badge(count($bloquesOtras))
                ->schema($bloquesOtras);
        }

        if (empty($tabsPrincipales)) {
            return [];
        }

        return [
            Tabs::make('SubTabsSeccionesRespuestas')
                ->persistTabInQueryString(false)
                ->tabs($tabsPrincipales)
                ->columnSpanFull(),
        ];
    }

    /**
     * Extrae y construye recursivamente los campos de preguntas de una sección.
     *
     * @return array<int, Component>
     */
    private static function camposDeSeccion(Seccion $seccion): array
    {
        $fields = [];
        $preguntas = $seccion->preguntas->whereNull('parent_id')->sortBy('orden');

        foreach ($preguntas as $pregunta) {
            $campo = self::campoRespuesta($pregunta);
            if (is_array($campo)) {
                array_push($fields, ...$campo);
            } else {
                $fields[] = $campo;
            }
        }

        return $fields;
    }

    /**
     * Normaliza un slug o nombre eliminando tildes, espacios y caracteres especiales.
     */
    private static function normalizarSlug(?string $slug): string
    {
        if (blank($slug)) {
            return '';
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', (string) $slug);
        $clean = $clean !== false ? $clean : (string) $slug;

        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $clean) ?? '');
    }

    /**
     * @return Component|array<int, Component>
     */
    private static function campoRespuesta(Pregunta $pregunta): Component|array
    {
        $statePath = "respuestas.{$pregunta->id}.valor";
        $linkPath = "respuestas.{$pregunta->id}.enlace";

        $contentDirectory = function (Get $get, ?Model $record) {
            $rawSlug = $get('slug') ?? $record?->slug;
            $slug = $rawSlug ? Str::slug($rawSlug) : null;

            if ($record instanceof Site || $get('plantilla_id') !== null) {
                $folder = $slug ?: ($record?->id ? "site-{$record->id}" : 'general');

                return "sites/{$folder}/contenido";
            }
            $folder = $slug ?: ($record?->id ? "plantilla-{$record->id}" : 'general');

            return "plantillas/{$folder}/contenido";
        };

        // Construir el campo principal
        $field = match ($pregunta->tipo) {
            'area' => Textarea::make($statePath)
                ->rows(3)
                ->placeholder('Escribe el contenido aquí...'),

            // imagen con estructura array → FileUpload múltiple con límite y diseño grid compacto
            'imagen' => $pregunta->estructura === 'array'
                ? FileUpload::make($statePath)
                    ->multiple()
                    ->panelLayout('grid')
                    ->maxFiles($pregunta->max_items ?? null)
                    ->webp5Mb($contentDirectory, 'public')
                    ->orientImagesFromExif(false)
                    ->uploadingMessage('Subiendo imágenes...')
                    ->deletable(true)
                    ->openable()
                    ->downloadable()
                    ->helperText('Puedes subir múltiples imágenes'.($pregunta->max_items ? " (máx. {$pregunta->max_items})" : '').'. Formato WebP, máx. 2 MB c/u.')
                : FileUpload::make($statePath)
                    ->webp5Mb($contentDirectory, 'public')
                    ->orientImagesFromExif(false)
                    ->uploadingMessage('Subiendo imagen...')
                    ->deletable(true)
                    ->openable()
                    ->helperText('Máximo 2MB. Se convertirá automáticamente a WebP.'),

            'galeria' => FileUpload::make($statePath)
                ->multiple()
                ->panelLayout('grid')
                ->webp5Mb($contentDirectory, 'public')
                ->orientImagesFromExif(false)
                ->uploadingMessage('Subiendo imágenes...')
                ->deletable(true)
                ->openable()
                ->downloadable()
                ->helperText('Puedes subir múltiples imágenes (Máximo 2MB cada una, formato WebP)'),

            'color' => ColorPicker::make($statePath)
                ->helperText('Selecciona un color'),

            'icono' => IconPicker::make($statePath),

            'enlace' => TextInput::make($statePath)
                ->placeholder('https://ejemplo.com'),

            'grupo' => Repeater::make($statePath)
                ->schema(function () use ($pregunta, $contentDirectory) {
                    $children = self::asegurarSubcamposResponsive($pregunta);
                    if ($children->isEmpty() && in_array(strtolower($pregunta->label), ['imagenes', 'imagen', 'portada_imagenes', 'hero_imagenes'])) {
                        return self::camposFallbackResponsive($contentDirectory);
                    }

                    return self::construirEsquemaSubcampos($children, $contentDirectory);
                })
                ->itemLabel(fn (array $state): ?string => self::generarItemLabel($state))
                ->defaultItems($pregunta->estructura === 'objeto' ? 1 : 0)
                ->minItems(($pregunta->estructura === 'objeto' && $pregunta->requerida) ? 1 : 0)
                ->maxItems($pregunta->estructura === 'objeto' ? 1 : $pregunta->max_items)
                ->validationMessages([
                    'max_items' => 'El campo :attribute no puede tener más de :max elementos.',
                    'min_items' => 'El campo :attribute debe tener al menos :min elementos.',
                ])
                ->addable(true)
                ->deletable(true)
                ->reorderable($pregunta->estructura === 'array')
                ->addActionLabel('+ Agregar '.$pregunta->label)
                ->collapsible()
                ->collapseAllAction(fn ($action) => $action->label('Colapsar todos'))
                ->expandAllAction(fn ($action) => $action->label('Expandir todos')),

            default => $pregunta->estructura === 'array'
                ? TagsInput::make($statePath)
                    ->placeholder('Escribe y presiona Enter para agregar...')
                    ->helperText('Puedes agregar múltiples elementos de texto.')
                    ->required((bool) $pregunta->requerida)
                : TextInput::make($statePath)
                    ->placeholder('Ingresa el valor...'),
        };

        // Aplicar propiedades comunes
        $field = $field
            ->label($pregunta->label)
            ->helperText($pregunta->ayuda)
            ->required((bool) $pregunta->requerida);

        if ($pregunta->tipo === 'enlace' || ($pregunta->tipo === 'grupo' && $pregunta->estructura === 'array')) {
            return $field;
        }

        // Filtros internos, tags o códigos numéricos no requieren botón de enlace
        $labelNorm = strtolower($pregunta->label);
        if (in_array($labelNorm, ['filtro_producto', 'filtro', 'idvideo', 'id_video', 'numero'])) {
            return $field;
        }

        // Colocar campo de texto y LinkPicker en la misma fila horizontal (8 cols input, 4 cols botón)
        return Grid::make(['default' => 1, 'md' => 12])
            ->schema([
                $field->columnSpan(['default' => 12, 'md' => 8]),
                LinkPicker::make($linkPath)->columnSpan(['default' => 12, 'md' => 4]),
            ])
            ->columnSpanFull();
    }

    public static function tiposPregunta(): array
    {
        return [
            'texto' => 'Texto corto',
            'area' => 'Texto largo (Área)',
            'imagen' => 'Imagen',
            'galeria' => 'Galería de imágenes',
            'color' => 'Selector de color',
            'icono' => 'Ícono (react-icons)',
            'enlace' => 'Enlace (URL)',
            'grupo' => 'Grupo (Conjunto de campos)',
        ];
    }

    public static function fuentes(): array
    {
        return [
            'Inter' => 'Inter',
            'Roboto' => 'Roboto',
            'Montserrat' => 'Montserrat',
            'Poppins' => 'Poppins',
            'Playfair Display' => 'Playfair Display',
            'Lora' => 'Lora',
            'Merriweather' => 'Merriweather',
            'Outfit' => 'Outfit',
            'Plus Jakarta Sans' => 'Plus Jakarta Sans',
        ];
    }

    public static function getIconOptions(): array
    {
        return [
            'Redes Sociales y Contacto' => [
                'FaWhatsapp' => 'FaWhatsapp (WhatsApp)',
                'FaInstagram' => 'FaInstagram (Instagram)',
                'FaFacebook' => 'FaFacebook (Facebook)',
                'FaTiktok' => 'FaTiktok (TikTok)',
                'FaYoutube' => 'FaYoutube (YouTube)',
                'FaXTwitter' => 'FaXTwitter (Twitter / X)',
                'FaEnvelope' => 'FaEnvelope (Correo / Email)',
                'FaPhone' => 'FaPhone (Teléfono)',
                'FaLocationDot' => 'FaLocationDot (Ubicación / Mapa)',
                'FaGlobe' => 'FaGlobe (Sitio Web / Global)',
                'FaHouseFlag' => 'FaHouseFlag (Tienda / Sede)',
                'FaStore' => 'FaStore (Local / Tienda)',
                'TbBrandWhatsapp' => 'TbBrandWhatsapp (WhatsApp Tabler)',
                'HiOutlineEnvelope' => 'HiOutlineEnvelope (Correo Heroicons)',
                'HiOutlinePhone' => 'HiOutlinePhone (Teléfono Heroicons)',
            ],
            'Comida, Pastelería y Repostería' => [
                'HiOutlineCake' => 'HiOutlineCake (Pastel / Torta)',
                'PiCakeBold' => 'PiCakeBold (Torta Phosphor)',
                'TbCake' => 'TbCake (Torta Cumpleaños)',
                'FaCookieBite' => 'FaCookieBite (Galleta)',
                'FaCookie' => 'FaCookie (Galleta Entera)',
                'FaIceCream' => 'FaIceCream (Helado)',
                'FaBreadSlice' => 'FaBreadSlice (Pan)',
                'FaCoffee' => 'FaCoffee (Café)',
                'FaMugHot' => 'FaMugHot (Taza Caliente)',
                'FaUtensils' => 'FaUtensils (Cubiertos / Menú)',
                'FaBurger' => 'FaBurger (Hamburguesa)',
                'FaPizzaSlice' => 'FaPizzaSlice (Pizza)',
                'FaBowlFood' => 'FaBowlFood (Plato de Comida)',
                'FaAppleWhole' => 'FaAppleWhole (Manzana / Saludable)',
                'FaWineGlass' => 'FaWineGlass (Copa / Vino)',
                'TbChefHat' => 'TbChefHat (Gorro de Chef)',
                'MdOutlineCake' => 'MdOutlineCake (Torta Material)',
                'MdOutlineFastfood' => 'MdOutlineFastfood (Comida Rápida)',
            ],
            'E-commerce, Compras y Ofertas' => [
                'FaShoppingCart' => 'FaShoppingCart (Carrito de Compras)',
                'FaShoppingBag' => 'FaShoppingBag (Bolsa de Compras)',
                'HiOutlineShoppingBag' => 'HiOutlineShoppingBag (Bolsa Heroicons)',
                'TbShoppingBag' => 'TbShoppingBag (Bolsa Tabler)',
                'FaTag' => 'FaTag (Etiqueta / Precio)',
                'FaTags' => 'FaTags (Etiquetas)',
                'FaCreditCard' => 'FaCreditCard (Tarjeta de Crédito)',
                'FaMoneyBillWave' => 'FaMoneyBillWave (Dinero / Efectivo)',
                'FaGift' => 'FaGift (Regalo / Promoción)',
                'FaReceipt' => 'FaReceipt (Recibo / Factura)',
                'FaPercent' => 'FaPercent (Descuento %)',
                'TbDiscount' => 'TbDiscount (Descuento Tabler)',
                'FaBoxOpen' => 'FaBoxOpen (Caja Abierta)',
                'FaCoins' => 'FaCoins (Monedas / Puntos)',
                'PiStorefrontBold' => 'PiStorefrontBold (Fachada Tienda)',
                'MdOutlinePayments' => 'MdOutlinePayments (Pagos)',
                'MdOutlineDiscount' => 'MdOutlineDiscount (Descuento Material)',
                'HiOutlineTag' => 'HiOutlineTag (Etiqueta Heroicons)',
                'HiOutlineGift' => 'HiOutlineGift (Regalo Heroicons)',
                'PiHandbagBold' => 'PiHandbagBold (Bolso / Compra)',
            ],
            'Envíos, Logística y Horarios' => [
                'TbTruckDelivery' => 'TbTruckDelivery (Delivery / Envío)',
                'FaTruck' => 'FaTruck (Camión de Envío)',
                'FaTruckFast' => 'FaTruckFast (Envío Rápido Express)',
                'MdOutlineLocalShipping' => 'MdOutlineLocalShipping (Envío Local)',
                'FaClock' => 'FaClock (Tiempo / Horario)',
                'FaCalendarDays' => 'FaCalendarDays (Calendario / Fechas)',
                'FaShieldHalved' => 'FaShieldHalved (Seguridad / Garantía)',
                'MdOutlineVerified' => 'MdOutlineVerified (Verificado)',
                'TbRosetteDiscountCheck' => 'TbRosetteDiscountCheck (Garantía Calidad)',
                'FaBox' => 'FaBox (Paquete)',
                'TbPackage' => 'TbPackage (Paquete Tabler)',
                'FaRoute' => 'FaRoute (Ruta / En Tránsito)',
                'FaMapPin' => 'FaMapPin (Punto Mapa)',
                'HiOutlineShieldCheck' => 'HiOutlineShieldCheck (Escudo Seguro)',
                'HiOutlineClock' => 'HiOutlineClock (Horario Heroicons)',
            ],
            'Calidad, Badges y Reacciones' => [
                'PiPaintBrushHouseholdBold' => 'PiPaintBrushHouseholdBold (Diseño / Personalizado)',
                'FaStar' => 'FaStar (Estrella Rellena)',
                'FaRegStar' => 'FaRegStar (Estrella Borde)',
                'FaHeart' => 'FaHeart (Corazón Relleno)',
                'FaRegHeart' => 'FaRegHeart (Corazón Borde)',
                'FaAward' => 'FaAward (Premio / Reconocimiento)',
                'FaMedal' => 'FaMedal (Medalla)',
                'FaCircleCheck' => 'FaCircleCheck (Check / Correcto)',
                'FaThumbsUp' => 'FaThumbsUp (Me Gusta / Recomendado)',
                'FaCrown' => 'FaCrown (Premium / VIP)',
                'TbSparkles' => 'TbSparkles (Especial / Brillo)',
                'HiOutlineSparkles' => 'HiOutlineSparkles (Brillo Heroicons)',
                'FaGem' => 'FaGem (Joya / Calidad)',
                'FaCertificate' => 'FaCertificate (Certificado)',
                'FaWrench' => 'FaWrench (Servicio / Soporte)',
                'FaFire' => 'FaFire (Popular / En Tendencia)',
            ],
            'Interfaz y Navegación' => [
                'FaMagnifyingGlass' => 'FaMagnifyingGlass (Buscar / Lupa)',
                'FaUser' => 'FaUser (Usuario / Cuenta)',
                'FaRegUser' => 'FaRegUser (Usuario Borde)',
                'FaBars' => 'FaBars (Menú Hamburguesa)',
                'FaXmark' => 'FaXmark (Cerrar / X)',
                'FaChevronDown' => 'FaChevronDown (Flecha Abajo)',
                'FaChevronRight' => 'FaChevronRight (Flecha Derecha)',
                'FaChevronLeft' => 'FaChevronLeft (Flecha Izquierda)',
                'FaChevronUp' => 'FaChevronUp (Flecha Arriba)',
                'FaArrowRight' => 'FaArrowRight (Flecha Continuar)',
                'FaPlus' => 'FaPlus (Agregar / Más)',
                'FaMinus' => 'FaMinus (Quitar / Menos)',
                'FaTrash' => 'FaTrash (Eliminar / Basura)',
                'FaPenToSquare' => 'FaPenToSquare (Editar)',
                'FaFilter' => 'FaFilter (Filtrar)',
                'FaShareNodes' => 'FaShareNodes (Compartir)',
                'FaCircleInfo' => 'FaCircleInfo (Información)',
                'FaCircleQuestion' => 'FaCircleQuestion (Pregunta / Ayuda)',
                'FaEye' => 'FaEye (Ver / Previsualizar)',
                'FaLock' => 'FaLock (Bloqueado / Candado)',
            ],
            'Negocios y Soporte' => [
                'FaChartLine' => 'FaChartLine (Crecimiento / Ventas)',
                'FaBriefcase' => 'FaBriefcase (Portafolio / Negocios)',
                'FaBuilding' => 'FaBuilding (Empresa / Edificio)',
                'FaHandshake' => 'FaHandshake (Trato / Alianza)',
                'FaUsers' => 'FaUsers (Equipo / Clientes)',
                'FaBullhorn' => 'FaBullhorn (Anuncios / Novedades)',
                'FaHeadphones' => 'FaHeadphones (Audífonos / Soporte)',
                'MdOutlineSupportAgent' => 'MdOutlineSupportAgent (Agente de Soporte)',
            ],
        ];
    }

    public static function redesSocialesSection(): Section
    {
        return Section::make('Redes Sociales')
            ->icon('heroicon-o-share')
            ->description('Enlaces a los perfiles oficiales de redes sociales para el pie de página, cabecera y barra lateral.')
            ->collapsible()
            ->schema([
                Toggle::make('estilos.barra_lateral_redes.activo')
                    ->label('Mostrar barra lateral flotante de redes sociales (Borde Izquierdo)')
                    ->helperText('Muestra una barra fija en el borde izquierdo con acceso directo a Instagram, Facebook, WhatsApp y TikTok consumiendo el color principal.')
                    ->default(true),
                Grid::make(3)->schema([
                    TextInput::make('estilos.redes_sociales.facebook')
                        ->label('Facebook')
                        ->prefixIcon('heroicon-m-globe-alt')
                        ->placeholder('https://facebook.com/tu-pagina'),
                    TextInput::make('estilos.redes_sociales.instagram')
                        ->label('Instagram')
                        ->prefixIcon('heroicon-m-camera')
                        ->placeholder('https://instagram.com/tu-usuario'),
                    TextInput::make('estilos.redes_sociales.tiktok')
                        ->label('TikTok')
                        ->prefixIcon('heroicon-m-video-camera')
                        ->placeholder('https://tiktok.com/@tu-usuario'),
                    TextInput::make('estilos.redes_sociales.whatsapp')
                        ->label('WhatsApp (Número o Enlace)')
                        ->prefixIcon('heroicon-m-chat-bubble-left-right')
                        ->placeholder('916628409 o https://wa.me/...'),
                    TextInput::make('estilos.redes_sociales.youtube')
                        ->label('YouTube')
                        ->prefixIcon('heroicon-m-play-circle')
                        ->placeholder('https://youtube.com/@tu-canal'),
                    TextInput::make('estilos.redes_sociales.twitter')
                        ->label('X (Twitter)')
                        ->prefixIcon('heroicon-m-hashtag')
                        ->placeholder('https://x.com/tu-usuario'),
                ]),
            ])
            ->columnSpanFull();
    }

    public static function metodosPagoSection(): Section
    {
        return Section::make('Métodos de Pago y Carrito de Compras')
            ->icon('heroicon-o-credit-card')
            ->description('Configura los métodos de pago mostrados en el carrito lateral (Transferencia y YAPE QR) y las insignias del pie de página.')
            ->collapsible()
            ->schema([
                CheckboxList::make('estilos.metodos_pago')
                    ->label('Insignias de pago aceptadas (Pie de página)')
                    ->options([
                        'visa' => 'Visa',
                        'mastercard' => 'Mastercard',
                        'amex' => 'American Express',
                        'diners' => 'Diners Club',
                        'discover' => 'Discover',
                        'paypal' => 'PayPal',
                        'apple_pay' => 'Apple Pay',
                        'google_pay' => 'Google Pay',
                        'amazon_pay' => 'Amazon Pay',
                        'shop_pay' => 'Shop Pay',
                        'yape' => 'Yape',
                        'plin' => 'Plin',
                        'efectivo' => 'Pago Contraentrega / Efectivo',
                        'transferencia' => 'Transferencia Bancaria',
                    ])
                    ->columns([
                        'default' => 2,
                        'sm' => 3,
                        'md' => 4,
                        'lg' => 6,
                    ])
                    ->default(['visa', 'mastercard', 'amex', 'paypal', 'apple_pay', 'google_pay', 'yape', 'plin']),

                Section::make('Método 1: Transferencia / Depósito Bancario en Carrito')
                    ->description('Muestra tarjetas oscuras con número de cuenta corriente, CCI y banco para que el cliente deposite.')
                    ->collapsible()
                    ->schema([
                        Toggle::make('estilos.metodos_pago_carrito.transferencia.activo')
                            ->label('Habilitar Transferencia Bancaria en el Carrito')
                            ->helperText('Si se desactiva o no tiene cuentas, no se mostrará esta opción en el carrito.')
                            ->default(true),

                        Grid::make(2)->schema([
                            TextInput::make('estilos.metodos_pago_carrito.transferencia.titulo')
                                ->label('Título de la opción')
                                ->default('Transferencia / Deposito Bancario')
                                ->placeholder('Ej. Transferencia / Deposito Bancario'),

                            TextInput::make('estilos.metodos_pago_carrito.transferencia.descripcion')
                                ->label('Subtítulo o Instrucción')
                                ->default('Realiza tu pago directamente desde tu cuenta bancaria,')
                                ->placeholder('Ej. Realiza tu pago directamente desde tu cuenta bancaria,'),
                        ]),

                        Repeater::make('estilos.cuentas_pago')
                            ->label('Cuentas Bancarias Disponibles')
                            ->defaultItems(0)
                            ->helperText('Agrega las cuentas donde tus clientes pueden depositar (ej. BCP, Interbank, BBVA).')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('label')
                                        ->label('Tipo de cuenta')
                                        ->placeholder('Ej. Cuenta corriente / Cuenta interbancaria')
                                        ->required(),
                                    TextInput::make('metodo_pago')
                                        ->label('Banco / Moneda')
                                        ->placeholder('Ej. BCP en nuevo soles / Interbank')
                                        ->required(),
                                    TextInput::make('info')
                                        ->label('Número de Cuenta / CCI')
                                        ->placeholder('Ej. 1917358439064')
                                        ->required(),
                                ]),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => (filled($state['metodo_pago'] ?? null) || filled($state['label'] ?? null)) ? (($state['label'] ?? '').' | '.($state['metodo_pago'] ?? '').' — '.($state['info'] ?? '')) : null)
                            ->columnSpanFull(),
                    ]),

                Section::make('Método 2: Pago con QR YAPE en Carrito')
                    ->description('Muestra el código QR de Yape, el número telefónico y las instrucciones para enviar el comprobante.')
                    ->collapsible()
                    ->schema([
                        Toggle::make('estilos.metodos_pago_carrito.yape.activo')
                            ->label('Habilitar Pago con QR YAPE en el Carrito')
                            ->helperText('Si se desactiva o no tiene celular/QR, no se mostrará esta opción en el carrito.')
                            ->default(true),

                        Grid::make(2)->schema([
                            TextInput::make('estilos.metodos_pago_carrito.yape.titulo')
                                ->label('Título de la opción')
                                ->default('Pago con QR YAPE')
                                ->placeholder('Ej. Pago con QR YAPE'),

                            TextInput::make('estilos.metodos_pago_carrito.yape.numero')
                                ->label('Número de celular YAPE')
                                ->placeholder('Ej. 990096126 o 990 096 126')
                                ->helperText('Número editable según el celular del negocio.')
                                ->prefixIcon('heroicon-m-phone'),

                            TextInput::make('estilos.metodos_pago_carrito.yape.titular')
                                ->label('Nombre del titular de la cuenta YAPE (Opcional)')
                                ->placeholder('Ej. Mi Empresa S.A.C.'),

                            TextInput::make('estilos.metodos_pago_carrito.yape.instruccion')
                                ->label('Instrucción junto al QR')
                                ->default('Ingrese a YAPE y escanee este QR')
                                ->placeholder('Ej. Ingrese a YAPE y escanee este QR'),

                            FileUpload::make('estilos.metodos_pago_carrito.yape.qr_imagen')
                                ->label('Imagen del Código QR de YAPE')
                                ->image()
                                ->disk('public')
                                ->directory('sites/pagos/qr')
                                ->visibility('public')
                                ->openable()
                                ->downloadable()
                                ->columnSpanFull(),

                            TextInput::make('estilos.metodos_pago_carrito.yape.mensaje_voucher')
                                ->label('Texto para confirmación por WhatsApp')
                                ->default('Enviar captura del depósito por WhatsApp al {numero}')
                                ->placeholder('Enviar captura del depósito por WhatsApp al {numero}')
                                ->helperText('Usa {numero} para reemplazarlo automáticamente por el celular configurado arriba.')
                                ->columnSpanFull(),
                        ]),
                    ]),
            ])
            ->columnSpanFull();
    }

    /**
     * Asegura o genera los sub-campos responsive (Imagen_pc, Imagen_tablet, Imagen_cel)
     * para preguntas de tipo grupo dedicadas a imágenes.
     */
    public static function asegurarSubcamposResponsive(Pregunta $pregunta): mixed
    {
        $children = $pregunta->children;
        if ((! $children || $children->isEmpty()) && isset($pregunta->id)) {
            $children = Pregunta::where('parent_id', $pregunta->id)->orderBy('orden')->get();
        }

        if (($children->isEmpty() || ! $children) && in_array(strtolower($pregunta->label), ['imagenes', 'imagen', 'portada_imagenes', 'hero_imagenes'])) {
            if (isset($pregunta->id) && $pregunta->id) {
                $subCampos = [
                    ['label' => 'Imagen_pc', 'orden' => 1, 'ayuda' => 'Imagen para computadoras de escritorio (Recomendado 1920x800 o proporción panorámica).'],
                    ['label' => 'Imagen_tablet', 'orden' => 2, 'ayuda' => 'Imagen para tablets (Recomendado 1024x600 o proporción estándar).'],
                    ['label' => 'Imagen_cel', 'orden' => 3, 'ayuda' => 'Imagen para celulares (Recomendado 768x800 o proporción vertical).'],
                ];

                foreach ($subCampos as $sc) {
                    Pregunta::firstOrCreate(
                        [
                            'parent_id' => $pregunta->id,
                            'label'     => $sc['label'],
                        ],
                        [
                            'plantilla_id' => $pregunta->plantilla_id,
                            'seccion_id'   => $pregunta->seccion_id,
                            'tipo'         => 'imagen',
                            'estructura'   => 'objeto',
                            'orden'        => $sc['orden'],
                            'requerida'    => false,
                            'ayuda'        => $sc['ayuda'],
                        ]
                    );
                }

                $children = Pregunta::where('parent_id', $pregunta->id)->orderBy('orden')->get();
            }
        }

        return $children ?? collect();
    }

    /**
     * @return array<int, Component>
     */
    public static function camposFallbackResponsive(mixed $contentDirectory): array
    {
        return [
            Grid::make(['default' => 1, 'md' => 3])
                ->schema([
                    FileUpload::make('Imagen_pc')
                        ->label('🖥️ Desktop / PC')
                        ->webp5Mb($contentDirectory, 'public')
                        ->orientImagesFromExif(false)
                        ->uploadingMessage('Subiendo imagen...')
                        ->deletable(true)
                        ->openable()
                        ->downloadable()
                        ->helperText('Panorámica 1920x800 px (WebP).'),

                    FileUpload::make('Imagen_tablet')
                        ->label('📱 Tablet')
                        ->webp5Mb($contentDirectory, 'public')
                        ->orientImagesFromExif(false)
                        ->uploadingMessage('Subiendo imagen...')
                        ->deletable(true)
                        ->openable()
                        ->downloadable()
                        ->helperText('Estándar 1024x600 px (WebP).'),

                    FileUpload::make('Imagen_cel')
                        ->label('📲 Celular / Móvil')
                        ->webp5Mb($contentDirectory, 'public')
                        ->orientImagesFromExif(false)
                        ->uploadingMessage('Subiendo imagen...')
                        ->deletable(true)
                        ->openable()
                        ->downloadable()
                        ->helperText('Vertical 768x800 px (WebP).'),
                ])
                ->columnSpanFull(),
        ];
    }

    /**
     * Construye un esquema de alta densidad para subcampos de un grupo o repetidor,
     * agrupando imágenes responsivas (PC, tablet, celular) en un triplete horizontal (Grid 3 cols).
     *
     * @return array<int, Component>
     */
    private static function construirEsquemaSubcampos(Collection $children, mixed $contentDirectory): array
    {
        $responsiveLabels = ['imagen_pc', 'imagen_tablet', 'imagen_cel'];

        $responsiveChildren = $children->filter(fn (Pregunta $c) => in_array(strtolower($c->label), $responsiveLabels))->sortBy('orden');
        $otherChildren = $children->reject(fn (Pregunta $c) => in_array(strtolower($c->label), $responsiveLabels))->sortBy('orden');

        $components = [];

        // 1. Triplete Responsivo en Grilla de 3 columnas (Desktop / Tablet / Celular)
        if ($responsiveChildren->isNotEmpty()) {
            $responsiveFields = $responsiveChildren->map(function (Pregunta $child) use ($contentDirectory) {
                $childPath = $child->label;
                $label = match (strtolower($child->label)) {
                    'imagen_pc' => '🖥️ Desktop / PC',
                    'imagen_tablet' => '📱 Tablet',
                    'imagen_cel' => '📲 Celular / Móvil',
                    default => $child->label,
                };

                $field = FileUpload::make($childPath)
                    ->label($label)
                    ->webp5Mb($contentDirectory, 'public')
                    ->orientImagesFromExif(false)
                    ->uploadingMessage('Subiendo imagen...')
                    ->deletable(true)
                    ->openable()
                    ->downloadable()
                    ->helperText(match (strtolower($child->label)) {
                        'imagen_pc' => 'Panorámica 1920x800',
                        'imagen_tablet' => 'Estándar 1024x600',
                        'imagen_cel' => 'Vertical 768x800',
                        default => 'Formato WebP',
                    });

                if ($child->ayuda) {
                    $field->helperText($child->ayuda);
                }

                return $field->required((bool) $child->requerida);
            })->all();

            $components[] = Grid::make(['default' => 1, 'md' => count($responsiveFields)])
                ->schema($responsiveFields)
                ->columnSpanFull();
        }

        // 2. Mapear los demás campos (textos, áreas, grupos anidados, etc.)
        $normalFields = [];
        foreach ($otherChildren as $child) {
            $childPath = $child->label;

            $subField = match ($child->tipo) {
                'area' => Textarea::make($childPath)->rows(2),
                'imagen' => $child->estructura === 'array'
                    ? FileUpload::make($childPath)->multiple()->panelLayout('grid')->maxFiles($child->max_items ?? null)->webp5Mb($contentDirectory, 'public')->orientImagesFromExif(false)->uploadingMessage('Subiendo imágenes...')->deletable(true)->openable()->downloadable()
                    : FileUpload::make($childPath)->webp5Mb($contentDirectory, 'public')->orientImagesFromExif(false)->uploadingMessage('Subiendo imagen...')->deletable(true)->openable()->downloadable(),
                'galeria' => FileUpload::make($childPath)->multiple()->panelLayout('grid')->webp5Mb($contentDirectory, 'public')->orientImagesFromExif(false)->uploadingMessage('Subiendo imágenes...')->deletable(true)->openable()->downloadable(),
                'color' => ColorPicker::make($childPath),
                'icono' => IconPicker::make($childPath),
                'enlace' => TextInput::make($childPath)->placeholder('https://ejemplo.com'),

                // Sub-grupo anidado
                'grupo' => Repeater::make($childPath)
                    ->schema(function () use ($child, $contentDirectory) {
                        $grandChildren = self::asegurarSubcamposResponsive($child);
                        if ($grandChildren->isEmpty() && in_array(strtolower($child->label), ['imagenes', 'imagen', 'portada_imagenes', 'hero_imagenes'])) {
                            return self::camposFallbackResponsive($contentDirectory);
                        }

                        return self::construirEsquemaSubcampos($grandChildren, $contentDirectory);
                    })
                    ->label(match ($child->label) {
                        'Imagenes' => 'Imágenes de Portada / Banner',
                        default => $child->label,
                    })
                    ->itemLabel(fn (array $state): ?string => self::generarItemLabel($state))
                    ->defaultItems($child->estructura === 'objeto' ? 1 : 0)
                    ->minItems(($child->estructura === 'objeto' && $child->requerida) ? 1 : 0)
                    ->maxItems($child->estructura === 'objeto' ? 1 : $child->max_items)
                    ->collapsible()
                    ->reorderable($child->estructura === 'array')
                    ->addActionLabel('+ Agregar '.$child->label),

                default => $child->estructura === 'array' ? TagsInput::make($childPath) : TextInput::make($childPath),
            };

            if ($child->tipo !== 'grupo') {
                $subField->label($child->label);
            }
            if ($child->ayuda) {
                $subField->helperText($child->ayuda);
            }
            $subField->required((bool) $child->requerida);

            $normalFields[] = $subField;
        }

        if (! empty($normalFields)) {
            $components[] = Grid::make(['default' => 1, 'md' => count($normalFields) > 1 ? 2 : 1])
                ->schema($normalFields)
                ->columnSpanFull();
        }

        return $components;
    }

    /**
     * Genera un resumen inteligente para cabeceras de repeaters colapsados.
     */
    private static function generarItemLabel(array $state): ?string
    {
        $title = $state['Titulo'] ?? $state['titulo'] ?? $state['Label'] ?? $state['label'] ?? $state['nombre'] ?? $state['Nombre'] ?? $state['pregunta'] ?? $state['texto'] ?? $state['Etiqueta'] ?? $state['Numero'] ?? null;
        if ($title) {
            return Str::limit((string) $title, 55);
        }

        if (! empty($state['Imagen_pc'])) {
            $file = is_array($state['Imagen_pc']) ? array_values($state['Imagen_pc'])[0] : $state['Imagen_pc'];

            return 'Slide ('.basename((string) $file).')';
        }

        if (! empty($state['IdVideo'])) {
            return 'TikTok ID: '.$state['IdVideo'];
        }

        if (! empty($state['imagen'])) {
            $file = is_array($state['imagen']) ? array_values($state['imagen'])[0] : $state['imagen'];

            return 'Elemento ('.basename((string) $file).')';
        }

        return null;
    }
}
