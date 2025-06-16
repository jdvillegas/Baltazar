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
        Schema::table('case_models', function (Blueprint $table) {
            $table->string('departamento')->after('description');
            $table->string('despacho')->after('departamento');
            $table->boolean('es_privado')->default(false)->after('despacho');
            $table->dateTime('fecha_proceso')->nullable()->after('es_privado');
            $table->dateTime('fecha_ultima_actuacion')->nullable()->after('fecha_proceso');
            $table->unsignedBigInteger('id_conexion')->nullable()->after('fecha_ultima_actuacion');
            $table->unsignedBigInteger('id_proceso')->after('id_conexion');
            $table->string('llave_proceso')->after('id_proceso');
            $table->text('sujetos_procesales')->after('llave_proceso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_models', function (Blueprint $table) {
            $table->dropColumn([
                'departamento',
                'despacho',
                'es_privado',
                'fecha_proceso',
                'fecha_ultima_actuacion',
                'id_conexion',
                'id_proceso',
                'llave_proceso',
                'sujetos_procesales'
            ]);
        });
    }
};
