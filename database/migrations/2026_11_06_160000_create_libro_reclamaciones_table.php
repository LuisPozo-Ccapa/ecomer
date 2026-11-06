<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('libro_reclamaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('codigo')->unique(); // ej: REC-2026-0001
            $table->enum('tipo', ['reclamo', 'queja'])->default('reclamo');

            // 1. Identificación del Consumidor Reclamante
            $table->string('tipo_documento', 20)->default('DNI'); // DNI, CE, RUC, Pasaporte
            $table->string('numero_documento', 30);
            $table->string('nombres', 150);
            $table->string('apellidos', 150)->nullable();
            $table->string('razon_social', 200)->nullable();
            $table->string('email', 150);
            $table->string('telefono', 50);
            $table->string('departamento', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('direccion', 255);

            // Menor de edad y apoderado (opcional)
            $table->boolean('es_menor_edad')->default(false);
            $table->string('apoderado_nombres', 200)->nullable();
            $table->string('apoderado_tipo_documento', 20)->nullable();
            $table->string('apoderado_numero_documento', 30)->nullable();
            $table->string('apoderado_email', 150)->nullable();
            $table->string('apoderado_telefono', 50)->nullable();

            // 2. Identificación del Bien Contratado
            $table->enum('tipo_bien', ['producto', 'servicio'])->default('producto');
            $table->decimal('monto_reclamado', 10, 2)->nullable();
            $table->string('moneda', 10)->default('PEN');
            $table->string('numero_pedido', 100)->nullable();
            $table->text('descripcion_bien');

            // 3. Detalle de la Reclamación
            $table->text('detalle'); // Hechos que motivan la queja o reclamo
            $table->text('pedido'); // Solicitud concreta del consumidor

            // 4. Estado y Gestión Administrativa
            $table->enum('estado', ['pendiente', 'en_proceso', 'atendido'])->default('pendiente');
            $table->date('fecha_limite')->nullable(); // Plazo legal (15 días hábiles)
            $table->text('respuesta_empresa')->nullable();
            $table->dateTime('fecha_respuesta')->nullable();

            // Auditoría técnica
            $table->string('ip_origen', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro_reclamaciones');
    }
};

