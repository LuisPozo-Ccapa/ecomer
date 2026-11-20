<?php

namespace Tests\Feature;

use App\Models\Moneda;
use App\Models\Plantilla;
use App\Models\Producto;
use App\Models\Seccion;
use App\Models\Site;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMultiTenantTest extends TestCase
{
    use RefreshDatabase;

    private Site $site;
    private Tienda $tienda;
    private Producto $producto;

    protected function setUp(): void
    {
        parent::setUp();

        $moneda = Moneda::firstOrCreate(
            ['codigo' => 'PEN'],
            ['nombre' => 'Soles', 'simbolo' => 'S/', 'activa' => true]
        );

        $plantilla = Plantilla::firstOrCreate(
            ['slug' => 'ecomer'],
            ['tipo' => 'ecommerce', 'nombre' => 'Ecomer', 'descripcion' => 'Plantilla Ecomer', 'activa' => true]
        );

        Seccion::firstOrCreate(
            ['plantilla_id' => $plantilla->id, 'slug' => 'Hero'],
            ['nombre' => 'Inicio', 'orden' => 1, 'activa' => true]
        );

        Seccion::firstOrCreate(
            ['plantilla_id' => $plantilla->id, 'slug' => 'nosotros'],
            ['nombre' => 'Nosotros', 'orden' => 2, 'activa' => true]
        );

        Seccion::firstOrCreate(
            ['plantilla_id' => $plantilla->id, 'slug' => 'contacto'],
            ['nombre' => 'Contacto', 'orden' => 3, 'activa' => true]
        );

        $user = User::factory()->create();

        $this->tienda = Tienda::create([
            'slug'      => 'tienda-magnus',
            'nombre'    => 'Tienda Magnus',
            'estado'    => 'activo',
            'moneda_id' => $moneda->id,
        ]);

        $this->site = Site::create([
            'user_id'      => $user->id,
            'plantilla_id' => $plantilla->id,
            'tienda_id'    => $this->tienda->id,
            'moneda_id'    => $moneda->id,
            'nombre'       => 'Magnus Tech Peru',
            'slug'         => 'magnustechperu',
            'estado'       => 'publicado',
            'imagen'       => 'sites/magnus/logo.webp',
            'estilos'      => [
                'direccion'       => 'Av. República de Panamá 1234, Lima',
                'redes_sociales'  => [
                    'facebook'  => 'https://facebook.com/magnustechperu',
                    'instagram' => 'https://instagram.com/magnustechperu',
                ],
                'acciones_nav'    => [
                    ['texto' => '+51 987654321', 'icono' => 'FaWhatsapp'],
                ],
            ],
        ]);

        $this->site->tiendas()->attach($this->tienda->id);

        $this->producto = Producto::create([
            'nombre'            => 'Taladro Percutor Industrial 750W',
            'slug'              => 'taladro-percutor-industrial-750w',
            'descripcion_corta' => 'Taladro de alto rendimiento para concreto y metal.',
            'descripcion'       => 'Taladro profesional con velocidad variable y reversa.',
            'precio'            => 299.90,
            'precio_oferta'     => 249.90,
            'stock'             => 15,
            'sku'               => 'TAL-750W',
            'activo'            => true,
            'imagenes'          => ['productos/taladro-1.webp'],
        ]);

        $this->producto->tiendas()->attach($this->tienda->id);
    }

    public function test_robots_txt_es_dinamico_y_no_contiene_marcas_ajenas(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');

        $content = $response->getContent();

        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Disallow: /admin', $content);
        $this->assertStringContainsString('Disallow: /livewire/', $content);
        $this->assertStringContainsString('Sitemap: ' . url('/sitemap.xml'), $content);
        $this->assertStringNotContainsString('lismercorp.com', $content);
    }

    public function test_sitemap_xml_incluye_secciones_y_productos_del_tenant_sin_parametros_de_filtro(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $content = $response->getContent();

        // Debe contener la raíz y secciones activas
        $this->assertStringContainsString('<loc>' . url('/') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('/nosotros') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('/contacto') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . url('/productos') . '</loc>', $content);

        // Debe contener el producto individual
        $this->assertStringContainsString(
            '<loc>' . url('/productos?producto=taladro-percutor-industrial-750w') . '</loc>',
            $content
        );

        // NO debe contener URLs con filtros como ?liquidaciones=1
        $this->assertStringNotContainsString('liquidaciones=1', $content);
    }

    public function test_ruta_limpia_de_producto_redirige_301_a_url_canonica(): void
    {
        $response = $this->get('/producto/taladro-percutor-industrial-750w');

        $response->assertStatus(301);
        $response->assertRedirect('/productos?producto=taladro-percutor-industrial-750w');
    }

    public function test_pagina_principal_renderiza_canonical_y_schema_store(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $content = $response->getContent();

        // Etiqueta canónica
        $this->assertStringContainsString('<link rel="canonical" href="' . url('/') . '">', $content);

        // Open Graph
        $this->assertStringContainsString('<meta property="og:title" content="Magnus Tech Peru — Inicio">', $content);
        $this->assertStringContainsString('<meta property="og:type" content="website">', $content);

        // Schema JSON-LD de Store
        $this->assertStringContainsString('"@type": "Store"', $content);
        $this->assertStringContainsString('"name": "Magnus Tech Peru"', $content);
        $this->assertStringContainsString('"telephone": "+51987654321"', $content);
        $this->assertStringContainsString('https://facebook.com/magnustechperu', $content);
    }

    public function test_detalle_de_producto_renderiza_metadatos_og_y_schema_product(): void
    {
        $response = $this->get('/productos?producto=taladro-percutor-industrial-750w');

        $response->assertStatus(200);

        $content = $response->getContent();

        // Título de producto individual en el servidor
        $this->assertStringContainsString('Taladro Percutor Industrial 750W | Magnus Tech Peru', $content);

        // Canónica de producto
        $this->assertStringContainsString(
            '<link rel="canonical" href="' . url('/productos?producto=taladro-percutor-industrial-750w') . '">',
            $content
        );

        // Open Graph de producto
        $this->assertStringContainsString('<meta property="og:type" content="product">', $content);
        $this->assertStringContainsString('productos/taladro-1.webp', $content);

        // Schema JSON-LD de Product y Offer
        $this->assertStringContainsString('"@type": "Product"', $content);
        $this->assertStringContainsString('"name": "Taladro Percutor Industrial 750W"', $content);
        $this->assertStringContainsString('"@type": "Offer"', $content);
        $this->assertStringContainsString('"price": "249.90"', $content);
        $this->assertStringContainsString('"priceCurrency": "PEN"', $content);
        $this->assertStringContainsString('https://schema.org/InStock', $content);
    }

    public function test_configuraciones_opcionales_filament_seo_se_renderizan_correctamente(): void
    {
        $this->site->update([
            'estilos' => array_merge($this->site->estilos ?? [], [
                'seo' => [
                    'og_image'            => 'sites/seo/banner-redes-custom.webp',
                    'google_verification' => 'google-site-verification=abc123token456',
                ],
            ]),
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertStringContainsString('<meta name="google-site-verification" content="abc123token456">', $content);
        $this->assertStringContainsString('sites/seo/banner-redes-custom.webp', $content);
    }
}
