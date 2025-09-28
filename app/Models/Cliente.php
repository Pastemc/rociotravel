<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'numero_documento',
        'nombre',
        'contacto',
        'nacionalidad',
        'activo'  // Agregar este campo
    ];

    protected $casts = [
        'activo' => 'boolean',  // Agregar este cast
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Mutators para convertir a mayúsculas
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    public function setNacionalidadAttribute($value)
    {
        $this->attributes['nacionalidad'] = strtoupper($value);
    }

    // Método para obtener historial de compras (opcional)
    public function pasajes()
    {
        return $this->hasMany(Pasaje::class, 'numero_documento', 'numero_documento');
    }

    // Accessor para obtener el total de compras
    public function getTotalComprasAttribute()
    {
        return $this->pasajes()->count();
    }

    // Accessor para obtener la última compra
    public function getUltimaCompraAttribute()
    {
        return $this->pasajes()->latest()->first();
    }
}