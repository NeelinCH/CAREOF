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
        Schema::create('recordatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_id')->constrained()->onDelete('cascade');
            $table->dateTime('fecha_envio');
            $table->boolean('enviado')->default(false);
            $table->string('tipo_recordatorio')->default('email'); // email, sms, push
            $table->json('detalles')->nullable(); // Para información adicional
            $table->timestamp('enviado_at')->nullable();
            $table->text('error_mensaje')->nullable(); // Para log de errores
            $table->integer('intentos')->default(0);
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index('fecha_envio');
            $table->index('enviado');
            $table->index(['tarea_id', 'enviado']);
            $table->index(['fecha_envio', 'enviado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recordatorios');
    }
};