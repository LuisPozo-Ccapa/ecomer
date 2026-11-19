<?php

namespace Tests\Feature;

use App\Filament\Resources\Plantillas\Schemas\PlantillaForm;
use App\Filament\Resources\Sites\Pages\EditSite;
use App\Http\Controllers\SitePageController;
use App\Models\Plantilla;
use App\Models\Pregunta;
use App\Models\Respuesta;
use App\Models\Seccion;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsImagenesAndSectoresTest extends TestCase
{
    use RefreshDatabase;

    public function test_normalizar_valores_para_formulario_unwraps_uuid_image_arrays(): void
    {
        $input = [
            'item1' => [
                'titulo' => 'Sector Minería',
                'imagen' => [
                    '0985d045-0ab8-4c89-91c2-9a9ff711d6fc' => 'sites/lismercorp/contenido/test.webp',
                ],
            ],
            'galeria' => [
                'img1.webp',
                'img2.webp',
            ],
        ];

        $normalized = EditSite::normalizarValoresParaFormulario($input);

        $this->assertSame('sites/lismercorp/contenido/test.webp', $normalized['item1']['imagen']);
        $this->assertSame(['img1.webp', 'img2.webp'], $normalized['galeria']);
    }

    public function test_normalizar_valores_para_guardar_cleans_uuid_arrays(): void
    {
        $input = [
            'item1' => [
                'titulo' => 'Sector Minería',
                'imagen' => [
                    '0985d045-0ab8-4c89-91c2-9a9ff711d6fc' => 'sites/siesquen/contenido/banner.webp',
                ],
            ],
        ];

        $cleaned = EditSite::normalizarValoresParaGuardar($input);

        $this->assertSame('sites/siesquen/contenido/banner.webp', $cleaned['item1']['imagen']);
    }

    public function test_asegurar_subcampos_responsive_creates_pc_tablet_cel_when_empty(): void
    {
        $plantilla = Plantilla::create([
            'nombre' => 'Test Plantilla',
            'slug' => 'test-plantilla',
            'tipo' => 'ecommerce',
        ]);

        $seccion = Seccion::create([
            'plantilla_id' => $plantilla->id,
            'nombre' => 'Servicios',
            'slug' => 'servicios',
            'orden' => 1,
            'activa' => true,
        ]);

        $hero = Pregunta::create([
            'plantilla_id' => $plantilla->id,
            'seccion_id' => $seccion->id,
            'label' => 'Hero',
            'tipo' => 'grupo',
            'estructura' => 'objeto',
            'orden' => 1,
        ]);

        $imagenes = Pregunta::create([
            'plantilla_id' => $plantilla->id,
            'seccion_id' => $seccion->id,
            'parent_id' => $hero->id,
            'label' => 'Imagenes',
            'tipo' => 'grupo',
            'estructura' => 'objeto',
            'orden' => 1,
        ]);

        // Inicialmente tiene 0 hijos
        $this->assertSame(0, Pregunta::where('parent_id', $imagenes->id)->count());

        // Al invocar asegurarSubcamposResponsive
        $children = PlantillaForm::asegurarSubcamposResponsive($imagenes);

        $this->assertCount(3, $children);
        $labels = $children->pluck('label')->all();
        $this->assertContains('Imagen_pc', $labels);
        $this->assertContains('Imagen_tablet', $labels);
        $this->assertContains('Imagen_cel', $labels);
    }

    public function test_valor_publico_unwraps_single_image_uuid_array_to_url(): void
    {
        $uuidArray = [
            '0985d045-0ab8-4c89-91c2-9a9ff711d6fc' => 'sites/lismercorp/contenido/banner.webp',
        ];

        $url = SitePageController::valorPublico('imagen', $uuidArray);
        $this->assertIsString($url);
        $this->assertStringContainsString('sites/lismercorp/contenido/banner.webp', $url);

        $nullUrl = SitePageController::valorPublico('imagen', null);
        $this->assertNull($nullUrl);

        $emptyUrl = SitePageController::valorPublico('imagen', '');
        $this->assertNull($emptyUrl);
    }

    public function test_guardar_respuestas_allows_null_deletion_without_reversion(): void
    {
        $user = \App\Models\User::factory()->create();

        $plantilla = Plantilla::create([
            'nombre' => 'Plantilla Base',
            'slug' => 'plantilla-base',
            'tipo' => 'ecommerce',
        ]);

        $site = Site::create([
            'user_id' => $user->id,
            'plantilla_id' => $plantilla->id,
            'nombre' => 'Ferreteria Siesquen',
            'slug' => 'ferreteriasiesquen',
        ]);

        $seccion = Seccion::create([
            'plantilla_id' => $plantilla->id,
            'nombre' => 'Sectores',
            'slug' => 'sectores',
            'orden' => 1,
            'activa' => true,
        ]);

        $preguntaSectores = Pregunta::create([
            'plantilla_id' => $plantilla->id,
            'seccion_id' => $seccion->id,
            'label' => 'sectores',
            'tipo' => 'grupo',
            'estructura' => 'array',
            'orden' => 1,
        ]);

        // Simular respuesta existente previa en BD con la imagen de Lismer Corp
        $existing = Respuesta::create([
            'site_id' => $site->id,
            'pregunta_id' => $preguntaSectores->id,
            'valor' => json_encode([
                'card1' => [
                    'titulo' => 'Sector Industrial',
                    'imagen' => 'sites/lismercorp/contenido/banner.webp',
                ],
            ]),
        ]);

        // Ahora el usuario en el panel borra la imagen o vacía los sectores
        // Enviar petición con la imagen borrada (imagen: null)
        $nuevosValores = [
            'card1' => [
                'titulo' => 'Sector Industrial',
                'imagen' => null,
            ],
        ];

        $cleaned = EditSite::normalizarValoresParaGuardar($nuevosValores);
        $existing->update([
            'valor' => json_encode($cleaned),
        ]);

        $reloaded = Respuesta::find($existing->id);
        $decoded = json_decode($reloaded->valor, true);

        // Debe mantenerse null, no restaurar la imagen previa
        $this->assertNull($decoded['card1']['imagen']);

        // Ahora el usuario borra todas las tarjetas (envía array vacío que se convierte a null)
        $existing->update([
            'valor' => null,
        ]);

        $reloadedAfterClear = Respuesta::find($existing->id);
        $this->assertNull($reloadedAfterClear->valor);
    }

    public function test_guardar_respuestas_top_level_image_uuid_array_does_not_throw_type_error(): void
    {
        $rawValor = [
            '93dc41ef-ccfe-44d0-b12f-515e7c1dd724' => 'sites/siesquen/contenido/ejHhwYfI3dzhp483er9Nr6pPMS3i5aeslQZ6P83y.webp',
        ];

        // Ejecutar el pipeline exacto de guardarRespuestas
        $valor = $rawValor;
        if (is_array($valor)) {
            $valor = EditSite::normalizarValoresParaGuardar($valor);
            if (is_array($valor)) {
                $valorClean = array_filter($valor);
                if (empty($valorClean)) {
                    $valor = null;
                } else {
                    $valor = json_encode($valor);
                }
            } elseif (is_string($valor) && trim($valor) === '') {
                $valor = null;
            }
        }

        // El resultado debe ser un string con la ruta limpia y NO lanzar TypeError
        $this->assertSame('sites/siesquen/contenido/ejHhwYfI3dzhp483er9Nr6pPMS3i5aeslQZ6P83y.webp', $valor);
    }
}
