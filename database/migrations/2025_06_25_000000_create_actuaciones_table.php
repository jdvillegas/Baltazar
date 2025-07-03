<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actuaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_model_id')->constrained()->onDelete('cascade');
            $table->string('idRegActuacion');
            $table->string('llaveProceso');
            $table->string('fechaRegistro');
            $table->string('fechaInicial');
            $table->string('fechaFinal');
            $table->string('fechaActuacion');
            $table->integer('consActuacion');
            $table->boolean('conDocumento')->default(false);
            $table->string('codRegla');
            $table->integer('cant');
            $table->text('anotacion')->nullable();
            $table->string('actuacion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('actuaciones');
    }
};
