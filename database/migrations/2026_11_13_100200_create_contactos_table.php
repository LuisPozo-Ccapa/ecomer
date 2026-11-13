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
        if (! Schema::hasTable('contactos')) {
            Schema::create('contactos', function (Blueprint $table) {
                $table->id();
                $table->string('folio', 50)->unique()->index();
                $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
                $table->string('nombre', 255);
                $table->string('empresa', 255)->nullable();
                $table->string('telefono', 50);
                $table->string('email', 255)->nullable();
                $table->string('sector', 100)->nullable();
                $table->string('producto', 255)->nullable();
                $table->text('mensaje');
                $table->string('sitio_nombre', 255)->nullable();
                $table->string('estado', 50)->default('nuevo'); // nuevo, leido, en_proceso, respondido
                $table->boolean('correo_enviado')->default(false);
                $table->text('error_envio')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
