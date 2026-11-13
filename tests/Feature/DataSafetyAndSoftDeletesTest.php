<?php

namespace Tests\Feature;

use App\Filament\Resources\LibroReclamaciones\LibroReclamacionResource;
use App\Models\Categoria;
use App\Models\Contacto;
use App\Models\LibroReclamacion;
use App\Models\Plantilla;
use App\Models\Producto;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DataSafetyAndSoftDeletesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Plantilla $plantilla;
    protected Site $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->plantilla = Plantilla::create([
            'nombre' => 'Plantilla Base',
            'slug' => 'plantilla-base',
            'activo' => true,
        ]);
        $this->site = Site::create([
            'user_id' => $this->user->id,
            'plantilla_id' => $this->plantilla->id,
            'nombre' => 'Magnus Store',
            'slug' => 'magnus-store',
            'estado' => 'publicado',
        ]);
    }

    /**
     * Caso de Uso 1: SoftDelete de Producto permite recuperación total y no borra de raíz.
     */
    public function test_producto_soft_delete_preserva_registro_y_permite_restauracion(): void
    {
        $categoria = Categoria::create([
            'site_id' => $this->site->id,
            'nombre' => 'Textiles',
            'slug' => 'textiles',
        ]);

        $producto = Producto::create([
            'site_id' => $this->site->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Camisa Oxford',
            'slug' => 'camisa-oxford',
            'precio' => 89.90,
            'stock' => 50,
            'activo' => true,
        ]);

        // Simular borrado en el panel
        $producto->delete();

        // No debe aparecer en consultas activas
        $this->assertNull(Producto::find($producto->id));

        // Debe mantenerse en papelera
        $this->assertSoftDeleted('productos', ['id' => $producto->id]);
        $this->assertNotNull(Producto::withTrashed()->find($producto->id));

        // Restaurar
        $producto->restore();
        $this->assertNotNull(Producto::find($producto->id));
        $this->assertEquals('Camisa Oxford', Producto::find($producto->id)->nombre);
    }

    /**
     * Caso de Uso 7: Observer limpia imágenes de disco SOLO en ForceDelete, preservándolas en SoftDelete.
     */
    public function test_observer_purga_fotos_solo_en_force_delete_y_las_preserva_en_soft_delete(): void
    {
        Storage::fake('public');

        $rutaFoto = 'productos/camisa-test.webp';
        Storage::disk('public')->put($rutaFoto, 'dummy-content');

        $producto = Producto::create([
            'site_id' => $this->site->id,
            'nombre' => 'Camisa Observer Test',
            'slug' => 'camisa-observer-test',
            'precio' => 100,
            'imagen' => $rutaFoto,
        ]);

        // 1. SoftDelete -> El archivo NO se borra del disco
        $producto->delete();
        Storage::disk('public')->assertExists($rutaFoto);

        // 2. ForceDelete -> El observer purga el archivo físico
        $producto->forceDelete();
        Storage::disk('public')->assertMissing($rutaFoto);
    }

    /**
     * Caso de Uso 2: Se prohíbe eliminar a un usuario propietario de una tienda activa.
     */
    public function test_modelo_bloquea_eliminacion_de_usuario_con_sitios_activos(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('No se puede eliminar un usuario que es propietario de una tienda activa. Reasigne la tienda primero.');

        $this->user->delete();
    }

    /**
     * Caso de Uso 5: Libro de Reclamaciones no permite borrado físico bajo normativa INDECOPI.
     */
    public function test_libro_reclamaciones_prohibe_borrado_en_recurso_filament(): void
    {
        $reclamo = LibroReclamacion::create([
            'site_id' => $this->site->id,
            'codigo' => 'REC-2026-0001',
            'tipo' => 'reclamo',
            'tipo_documento' => 'DNI',
            'numero_documento' => '72345678',
            'nombres' => 'Juan',
            'apellidos' => 'Perez',
            'email' => 'juan@example.com',
            'telefono' => '999888777',
            'direccion' => 'Av. Larco 123',
            'descripcion_bien' => 'Servicio de estampado',
            'detalle' => 'Demora en la entrega',
            'pedido' => 'Entrega inmediata',
            'estado' => 'pendiente',
        ]);

        // canDelete debe ser falso
        $this->assertFalse(LibroReclamacionResource::canDelete($reclamo));
    }

    /**
     * Caso de Uso 4: Cotización web se persiste en BD con folio incluso si el envío de correo falla.
     */
    public function test_cotizacion_web_se_persiste_en_base_de_datos_incluso_si_falla_smtp(): void
    {
        // Forzar falla de correo
        Mail::shouldReceive('send')->andThrow(new \Exception('SMTP Connection timeout'));

        $response = $this->postJson('/api/contacto', [
            'site_id'  => $this->site->id,
            'nombre'   => 'Carlos Santana',
            'empresa'  => 'Corporacion Textil SAC',
            'telefono' => '987654321',
            'email'    => 'carlos@corporacion.pe',
            'sector'   => 'Textil',
            'producto' => 'Tela Algodón Pima 500m',
            'mensaje'  => 'Requiero cotización urgente por 500 metros.',
        ]);

        // Debe retornar éxito HTTP 200 para no frustrar al cliente
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $folio = $response->json('folio');
        $this->assertNotNull($folio);
        $this->assertStringStartsWith('COT-', $folio);

        // Debe estar persistido en la tabla contactos
        $this->assertDatabaseHas('contactos', [
            'folio'          => $folio,
            'site_id'        => $this->site->id,
            'nombre'         => 'Carlos Santana',
            'empresa'        => 'Corporacion Textil SAC',
            'correo_enviado' => false,
            'error_envio'    => 'SMTP Connection timeout',
        ]);
    }
}

