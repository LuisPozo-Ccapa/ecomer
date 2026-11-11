<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DeployWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_rechaza_peticion_sin_token(): void
    {
        Config::set('app.deploy_token', 'secreto-super-seguro-123');

        $response = $this->postJson('/api/deploy/run-tasks');

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acceso denegado: Token inválido o no configurado.',
            ]);
    }

    public function test_rechaza_peticion_con_token_invalido(): void
    {
        Config::set('app.deploy_token', 'secreto-super-seguro-123');

        $response = $this->postJson('/api/deploy/run-tasks', [], [
            'X-Deploy-Token' => 'token-falso',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Acceso denegado: Token inválido o no configurado.',
            ]);
    }

    public function test_ejecuta_tareas_con_token_valido(): void
    {
        $token = 'secreto-super-seguro-123';
        Config::set('app.deploy_token', $token);

        $response = $this->postJson('/api/deploy/run-tasks', [], [
            'X-Deploy-Token' => $token,
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'status',
                'timestamp',
                'results' => [
                    'migrate',
                    'optimize:clear',
                    'config:cache',
                    'route:cache',
                    'view:cache',
                    'storage:link',
                ],
            ]);
    }

    public function test_aplica_rate_limiting_en_deploy_webhook(): void
    {
        $token = 'secreto-super-seguro-123';
        Config::set('app.deploy_token', $token);

        // El middleware es throttle:5,1 (máximo 5 intentos por minuto)
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/deploy/run-tasks', [], ['X-Deploy-Token' => 'token-invalido']);
        }

        // El sexto intento debe ser bloqueado con 429 Too Many Requests
        $response = $this->postJson('/api/deploy/run-tasks', [], ['X-Deploy-Token' => 'token-invalido']);
        $response->assertStatus(429);
    }
}

