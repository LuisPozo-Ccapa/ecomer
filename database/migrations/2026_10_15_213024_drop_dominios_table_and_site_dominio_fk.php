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
        if (Schema::hasTable('sites')) {
            Schema::table('sites', function (Blueprint $table) {
                // Drop foreign key FIRST before dropping any index
                try {
                    $table->dropForeign(['dominio_id']);
                } catch (\Throwable $e) {}

                // Drop unique index
                try {
                    $table->dropUnique(['dominio_id', 'slug']);
                } catch (\Throwable $e) {}

                // Drop column
                if (Schema::hasColumn('sites', 'dominio_id')) {
                    $table->dropColumn('dominio_id');
                }
            });
        }

        Schema::dropIfExists('dominios');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('dominios')) {
            Schema::create('dominios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('nombre')->unique();
                $table->string('estado')->default('activo');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('sites') && !Schema::hasColumn('sites', 'dominio_id')) {
            Schema::table('sites', function (Blueprint $table) {
                $table->foreignId('dominio_id')->nullable()->constrained()->nullOnDelete();
            });
        }
    }
};
