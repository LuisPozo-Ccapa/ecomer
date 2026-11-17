<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeployWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rechaza_peticion_sin_token_valido(): void
    {
        $response = $this->postJson('/api/deploy/run-tasks', [], [
            'X-Deploy-Token' => 'token_invalido_falso',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['status' => 'error']);
    }

    public function test_webhook_ejecuta_tareas_y_auto_bootstrap_con_token_valido(): void
    {
        config(['app.deploy_token' => 'token_de_prueba_seguro_12345']);

        $response = $this->postJson('/api/deploy/run-tasks', [], [
            'X-Deploy-Token' => 'token_de_prueba_seguro_12345',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'results' => [
                'migrate',
                'store:bootstrap',
                'optimize:clear',
                'config:cache',
                'route:cache',
                'view:cache',
                'storage:link',
            ],
        ]);
    }
}
