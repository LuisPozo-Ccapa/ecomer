<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('tiendas')) {
            Schema::create('tiendas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('slug')->unique();
                $table->text('descripcion')->nullable();
                $table->string('estado')->default('activo');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('plantilla_tienda')) {
            Schema::create('plantilla_tienda', function (Blueprint $table) {
                $table->foreignId('plantilla_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tienda_id')->constrained()->cascadeOnDelete();
                $table->unique(['plantilla_id', 'tienda_id']);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('productos') && ! Schema::hasColumn('productos', 'tienda_id')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->foreignId('tienda_id')->nullable()->constrained('tiendas')->nullOnDelete();
            });
        }

        // Si producto_tienda fue creada antes de que existiera tiendas (instalación en base de datos limpia), vincular la clave foránea ahora:
        if (Schema::hasTable('producto_tienda') && DB::getDriverName() !== 'sqlite') {
            try {
                Schema::table('producto_tienda', function (Blueprint $table) {
                    $table->foreign('tienda_id')->references('id')->on('tiendas')->cascadeOnDelete();
                });
            } catch (\Throwable $e) {
                // Clave foránea ya vinculada
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['tienda_id']);
            $table->dropColumn('tienda_id');
        });

        Schema::dropIfExists('plantilla_tienda');
        Schema::dropIfExists('tiendas');
    }
};
