<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasaje extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'pasajes';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'cantidad',
        'descripcion',
        'precio_unitario',
        'subtotal',
        'destino',
        'ruta',
        'embarcacion',
        'puerto_embarque',
        'hora_embarque',
        'hora_salida',
        'medio_pago',
        'pago_mixto',
        'detalles_pago',
        'nota',
        // CAMPOS DEL CLIENTE AGREGADOS
        'nombre_cliente',
        'documento_cliente',
        'contacto_cliente',
        'nacionalidad_cliente'
    ];

    // Conversión de tipos de datos
    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'cantidad' => 'integer',
        'pago_mixto' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Mutadores para convertir texto a mayúsculas automáticamente
    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strtoupper($value);
    }

    public function setDestinoAttribute($value)
    {
        $this->attributes['destino'] = $value ? strtoupper($value) : null;
    }

    public function setRutaAttribute($value)
    {
        $this->attributes['ruta'] = $value ? strtoupper($value) : null;
    }

    public function setEmbarcacionAttribute($value)
    {
        $this->attributes['embarcacion'] = strtoupper($value);
    }

    public function setPuertoEmbarqueAttribute($value)
    {
        $this->attributes['puerto_embarque'] = strtoupper($value);
    }

    // NUEVOS MUTADORES PARA CAMPOS DEL CLIENTE
    public function setNombreClienteAttribute($value)
    {
        $this->attributes['nombre_cliente'] = strtoupper($value);
    }

    public function setDocumentoClienteAttribute($value)
    {
        $this->attributes['documento_cliente'] = strtoupper($value);
    }

    public function setContactoClienteAttribute($value)
    {
        $this->attributes['contacto_cliente'] = $value ? strtoupper($value) : null;
    }

    public function setNacionalidadClienteAttribute($value)
    {
        $this->attributes['nacionalidad_cliente'] = strtoupper($value);
    }

    // Accessor para obtener el total calculado
    public function getTotalAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }

    // Scope para filtrar por destino
    public function scopeByDestino($query, $destino)
    {
        return $query->where('destino', strtoupper($destino));
    }

    // Scope para filtrar por embarcación
    public function scopeByEmbarcacion($query, $embarcacion)
    {
        return $query->where('embarcacion', strtoupper($embarcacion));
    }

    // Scope para filtrar por medio de pago
    public function scopeByMedioPago($query, $medioPago)
    {
        return $query->where('medio_pago', $medioPago);
    }

    // Scope para filtrar por fecha
    public function scopeByFecha($query, $fecha)
    {
        return $query->whereDate('created_at', $fecha);
    }

    // NUEVOS SCOPES PARA CAMPOS DEL CLIENTE
    public function scopeByCliente($query, $nombreCliente)
    {
        return $query->where('nombre_cliente', 'LIKE', '%' . strtoupper($nombreCliente) . '%');
    }

    public function scopeByDocumento($query, $documento)
    {
        return $query->where('documento_cliente', strtoupper($documento));
    }

    public function scopeByNacionalidad($query, $nacionalidad)
    {
        return $query->where('nacionalidad_cliente', strtoupper($nacionalidad));
    }

    // Método para generar número de ticket
    public function generarNumeroTicket()
    {
        return 'CTE-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    // NUEVO: Método para obtener información completa del cliente
    public function getInformacionClienteAttribute()
    {
        return [
            'nombre' => $this->nombre_cliente,
            'documento' => $this->documento_cliente,
            'contacto' => $this->contacto_cliente,
            'nacionalidad' => $this->nacionalidad_cliente
        ];
    }

    // NUEVO: Método para validar si los datos del cliente están completos
    public function clienteCompleto()
    {
        return !empty($this->nombre_cliente) && !empty($this->documento_cliente);
    }

    // NUEVO: Método para obtener el nombre corto del cliente
    public function getNombreCortoClienteAttribute()
    {
        $nombres = explode(' ', $this->nombre_cliente);
        return count($nombres) >= 2 ? $nombres[0] . ' ' . $nombres[1] : $this->nombre_cliente;
    }
}