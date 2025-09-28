<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePasaje extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'detalles_pasaje';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'destino',
        'ruta',
        'descripcion', // AGREGAR ESTE CAMPO
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    // Conversión de tipos de datos
    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'cantidad' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Mutadores para convertir texto a mayúsculas automáticamente
    public function setDestinoAttribute($value)
    {
        $this->attributes['destino'] = strtoupper($value);
    }

    public function setRutaAttribute($value)
    {
        $this->attributes['ruta'] = strtoupper($value);
    }

    // NUEVO: Mutador para descripción
    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strtoupper($value);
    }

    // Accessor para calcular el subtotal automáticamente si no se proporciona
    public function getSubtotalAttribute($value)
    {
        // Si ya existe el valor, lo retornamos
        if ($value !== null) {
            return $value;
        }
        
        // Si no existe, lo calculamos
        return $this->cantidad * $this->precio_unitario;
    }

    // Scope para filtrar por destino
    public function scopeByDestino($query, $destino)
    {
        return $query->where('destino', strtoupper($destino));
    }

    // Scope para filtrar por ruta
    public function scopeByRuta($query, $ruta)
    {
        return $query->where('ruta', 'LIKE', '%' . strtoupper($ruta) . '%');
    }

    // NUEVO: Scope para filtrar por descripción
    public function scopeByDescripcion($query, $descripcion)
    {
        return $query->where('descripcion', 'LIKE', '%' . strtoupper($descripcion) . '%');
    }
}