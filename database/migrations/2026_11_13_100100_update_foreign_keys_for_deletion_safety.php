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
        // En SQLite (usado en tests en memoria), ALTER TABLE DROP FOREIGN KEY no está soportado de forma directa.
        // En MySQL/MariaDB (producción y staging), aplicamos restrictOnDelete para blindaje físico.
        if (DB::getDriverName() !== 'sqlite') {
            if (Schema::hasTable('sites')) {
                Schema::table('sites', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->foreign('user_id')
                        ->references('id')
                        ->on('users')
                        ->restrictOnDelete();
                });
            }

            if (Schema::hasTable('libro_reclamaciones')) {
                Schema::table('libro_reclamaciones', function (Blueprint $table) {
                    $table->dropForeign(['site_id']);
                    $table->foreign('site_id')
                        ->references('id')
                        ->on('sites')
                        ->restrictOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            if (Schema::hasTable('sites')) {
                Schema::table('sites', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->foreign('user_id')
                        ->references('id')
                        ->on('users')
                        ->cascadeOnDelete();
                });
            }

            if (Schema::hasTable('libro_reclamaciones')) {
                Schema::table('libro_reclamaciones', function (Blueprint $table) {
                    $table->dropForeign(['site_id']);
                    $table->foreign('site_id')
                        ->references('id')
                        ->on('sites')
                        ->cascadeOnDelete();
                });
            }
        }
    }
};
