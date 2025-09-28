<?php
// ========== 1. MIGRATION - database/migrations/xxxx_create_clientes_table.php ==========

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_documento', 20)->unique();
            $table->string('nombre', 255);
            $table->string('contacto', 20)->nullable();
            $table->string('nacionalidad', 50)->default('PERUANA');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices
            $table->index('numero_documento');
            $table->index('nombre');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};