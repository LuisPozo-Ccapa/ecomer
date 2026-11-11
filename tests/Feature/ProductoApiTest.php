<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Plantilla;
use App\Models\Producto;
use App\Models\Site;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lista_productos_activos_y_excluye_inactivos(): void
    {
        $productoActivo = Producto::create([
            'nombre' => 'Producto Activo',
            'slug' => 'producto-activo',
            'precio' => 100.00,
            'activo' => true,
        ]);

        $productoInactivo = Producto::create([
            'nombre' => 'Producto Oculto',
            'slug' => 'producto-oculto',
            'precio' => 50.00,
            'activo' => false,
        ]);

        $response = $this->getJson('/api/v1/productos');

        $response->assertOk()
            ->assertJsonFragment(['nombre' => 'Producto Activo', 'slug' => 'producto-activo'])
            ->assertJsonMissing(['nombre' => 'Producto Oculto', 'slug' => 'producto-oculto']);
    }

    public function test_lista_solo_productos_destacados(): void
    {
        Producto::create([
            'nombre' => 'Producto Normal',
            'slug' => 'producto-normal',
            'precio' => 100.00,
            'activo' => true,
            'destacado' => false,
        ]);

        Producto::create([
            'nombre' => 'Producto Top',
            'slug' => 'producto-top',
            'precio' => 200.00,
            'activo' => true,
            'destacado' => true,
        ]);

        $response = $this->getJson('/api/v1/productos/destacados');

        $response->assertOk()
            ->assertJsonFragment(['nombre' => 'Producto Top'])
            ->assertJsonMissing(['nombre' => 'Producto Normal']);
    }

    public function test_muestra_detalle_de_un_producto_por_slug(): void
    {
        $producto = Producto::create([
            'nombre' => 'Laptop Gamer Pro',
            'slug' => 'laptop-gamer-pro',
            'precio' => 4500.00,
            'sku' => 'LAP-001',
            'activo' => true,
        ]);

        $response = $this->getJson("/api/v1/productos/{$producto->slug}");

        $response->assertOk()
            ->assertJsonPath('data.nombre', 'Laptop Gamer Pro')
            ->assertJsonPath('data.slug', 'laptop-gamer-pro')
            ->assertJsonPath('data.sku', 'LAP-001');
    }

    public function test_filtra_productos_por_tiendas_del_sitio(): void
    {
        $user = User::factory()->create();
        $plantilla = Plantilla::create(['slug' => 'ecomer', 'tipo' => 'ecommerce', 'nombre' => 'Ecomer']);
        $tiendaA = Tienda::create(['nombre' => 'Almacén Lima', 'slug' => 'almacen-lima']);
        $tiendaB = Tienda::create(['nombre' => 'Almacén Cusco', 'slug' => 'almacen-cusco']);

        $site = Site::create([
            'user_id' => $user->id,
            'plantilla_id' => $plantilla->id,
            'nombre' => 'Tienda Lima',
            'slug' => 'tienda-lima',
            'estado' => 'publicado',
        ]);
        $site->tiendas()->attach($tiendaA->id);

        $prodLima = Producto::create(['nombre' => 'Stock Lima', 'slug' => 'stock-lima', 'precio' => 10, 'activo' => true]);
        $prodLima->tiendas()->attach($tiendaA->id);

        $prodCusco = Producto::create(['nombre' => 'Stock Cusco', 'slug' => 'stock-cusco', 'precio' => 20, 'activo' => true]);
        $prodCusco->tiendas()->attach($tiendaB->id);

        $response = $this->getJson("/api/v1/sites/{$site->slug}/productos");

        $response->assertOk()
            ->assertJsonFragment(['nombre' => 'Stock Lima'])
            ->assertJsonMissing(['nombre' => 'Stock Cusco']);
    }
}

