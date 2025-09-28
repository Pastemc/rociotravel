<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pasajes', function (Blueprint $table) {
            $table->id();
            
            // Datos del pasaje
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->decimal('precio_unitario', 8, 2);
            $table->decimal('subtotal', 8, 2);
            $table->string('destino')->nullable();
            $table->string('ruta')->nullable();
            
            // Datos del viaje
            $table->string('embarcacion');
            $table->string('puerto_embarque');
            $table->string('hora_embarque');
            $table->string('hora_salida');
            
            // Datos de pago
            $table->enum('medio_pago', ['efectivo', 'yape', 'plin'])->default('efectivo');
            $table->boolean('pago_mixto')->default(false);
            $table->text('detalles_pago')->nullable();
            
            // Datos del cliente
            $table->string('nombre_cliente');
            $table->string('documento_cliente');
            $table->string('contacto_cliente')->nullable();
            $table->string('nacionalidad_cliente')->default('PERUANA');
            
            // Observaciones
            $table->text('nota')->nullable();
            
            // Estado para el historial
            $table->enum('estado', ['activo', 'completado', 'anulado'])->default('activo');
            $table->text('motivo_anulacion')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasajes');
    }
};