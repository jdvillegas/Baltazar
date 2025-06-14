<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero eliminamos el constraint actual
        DB::statement('PRAGMA foreign_keys=off');
        Schema::table('case_models', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Luego agregamos el nuevo enum con el estado anulado
        Schema::table('case_models', function (Blueprint $table) {
            $table->enum('status', ['pendiente', 'en_proceso', 'completado', 'cancelado', 'anulado'])->default('pendiente');
        });
        DB::statement('PRAGMA foreign_keys=on');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero eliminamos el constraint actual
        DB::statement('PRAGMA foreign_keys=off');
        Schema::table('case_models', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Luego restauramos el enum original
        Schema::table('case_models', function (Blueprint $table) {
            $table->enum('status', ['pendiente', 'en_proceso', 'completado', 'cancelado'])->default('pendiente');
        });
        DB::statement('PRAGMA foreign_keys=on');
    }
};
