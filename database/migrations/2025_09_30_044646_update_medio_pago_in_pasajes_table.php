<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Opción 1: Si es un ENUM, modificar los valores permitidos
        Schema::table('pasajes', function (Blueprint $table) {
            // Cambiar la columna medio_pago para aceptar 'mixto'
            $table->string('medio_pago', 50)->default('efectivo')->change();
        });

        // Opción 2: Si prefieres mantenerlo como ENUM (descomenta esta sección y comenta la anterior)
        /*
        Schema::table('pasajes', function (Blueprint $table) {
            $table->enum('medio_pago', [
                'efectivo', 
                'yape', 
                'plin', 
                'transferencia', 
                'tarjeta', 
                'mixto'
            ])->default('efectivo')->change();
        });
        */
    }

    public function down()
    {
        // Revertir los cambios si es necesario
        Schema::table('pasajes', function (Blueprint $table) {
            $table->enum('medio_pago', [
                'efectivo', 
                'yape', 
                'plin', 
                'transferencia', 
                'tarjeta'
                // Remover 'mixto' en el rollback
            ])->default('efectivo')->change();
        });
    }
};