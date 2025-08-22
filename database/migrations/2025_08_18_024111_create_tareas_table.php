<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planta_id')->constrained()->onDelete('cascade');
            $table->enum('tipo', ['riego', 'fertilizacion', 'poda', 'trasplante', 'otro']);
            $table->integer('frecuencia_dias');
            $table->text('descripcion')->nullable();
            $table->date('proxima_fecha');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tareas');
    }
};