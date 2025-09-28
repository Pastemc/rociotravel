<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historial_ventas', function (Blueprint $table) {
            $table->id();
            
            // Referencias
            $table->unsignedBigInteger('pasaje_id')->nullable(); // Referencia al pasaje original
            
            // Datos del cliente
            $table->string('nombre_cliente');
            $table->string('documento_cliente', 50);
            $table->string('contacto_cliente', 100)->nullable();
            $table->string('nacionalidad_cliente', 100)->default('PERUANA');
            
            // Datos del pasaje
            $table->integer('cantidad')->default(1);
            $table->string('descripcion');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->string('destino')->nullable();
            $table->string('ruta')->nullable();
            
            // Datos del viaje
            $table->string('embarcacion');
            $table->string('puerto_embarque');
            $table->string('hora_embarque', 50);
            $table->string('hora_salida', 50);
            
            // Datos de pago
            $table->string('medio_pago', 50);
            $table->boolean('pago_mixto')->default(false);
            $table->text('detalles_pago')->nullable();
            
            // Estado y observaciones
            $table->enum('estado', ['activo', 'completado', 'anulado', 'pendiente'])->default('activo');
            $table->text('nota')->nullable();
            $table->text('motivo_anulacion')->nullable();
            
            $table->timestamps();
            
            // Índices para búsquedas
            $table->index(['documento_cliente']);
            $table->index(['nombre_cliente']);
            $table->index(['estado']);
            $table->index(['created_at']);
            $table->index(['embarcacion']);
            
            // Clave foránea (opcional)
            $table->foreign('pasaje_id')->references('id')->on('pasajes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('historial_ventas');
    }
};
