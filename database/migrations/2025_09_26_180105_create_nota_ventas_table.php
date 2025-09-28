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
        Schema::create('nota_ventas', function (Blueprint $table) {
            $table->id();
            
            // Datos de la nota de venta
            $table->string('numero_emision')->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['activa', 'cancelada', 'anulada'])->default('activa');
            
            // Datos del cliente (desnormalizados para historial)
            $table->string('cliente_nombre');
            $table->string('cliente_documento');
            $table->string('cliente_contacto')->nullable();
            $table->string('cliente_nacionalidad')->default('PERUANA');
            
            // Datos del pasaje (desnormalizados para historial)
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->string('ruta');
            $table->string('destino')->nullable();
            $table->decimal('precio_unitario', 8, 2);
            $table->decimal('subtotal', 8, 2);
            $table->decimal('total', 8, 2);
            
            // Datos del viaje
            $table->string('embarcacion');
            $table->string('puerto_embarque');
            $table->string('hora_embarque');
            $table->string('hora_salida');
            $table->enum('medio_pago', ['efectivo', 'yape', 'plin']);
            $table->boolean('pago_mixto')->default(false);
            $table->string('detalles_pago')->nullable();
            $table->text('nota')->nullable();
            
            // Datos de auditoría
            $table->string('operador')->default('ROCÍO TRAVEL');
            $table->timestamp('fecha_emision');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            // Referencia al pasaje original (si existe)
            $table->unsignedBigInteger('pasaje_id')->nullable();
            $table->foreign('pasaje_id')->references('id')->on('pasajes')->onDelete('set null');
            
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index(['cliente_documento']);
            $table->index(['numero_emision']);
            $table->index(['estado']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_ventas');
    }
};