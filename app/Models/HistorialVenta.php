<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialVenta extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'historial_ventas';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        // Referencias
        'pasaje_id',
        
        // Datos del cliente (copiados de Pasaje)
        'nombre_cliente',
        'documento_cliente',
        'contacto_cliente',
        'nacionalidad_cliente',
        
        // Datos del pasaje (copiados de Pasaje)
        'cantidad',
        'descripcion',
        'precio_unitario',
        'subtotal',
        'destino',
        'ruta',
        
        // Datos del viaje (copiados de Pasaje)
        'embarcacion',
        'puerto_embarque',
        'hora_embarque',
        'hora_salida',
        
        // Datos de pago (copiados de Pasaje)
        'medio_pago',
        'pago_mixto',
        'detalles_pago',
        
        // Estado y observaciones (específicos del historial)
        'estado',
        'nota',
        'motivo_anulacion'
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

    // Mutadores para convertir texto a mayúsculas automáticamente (igual que Pasaje)
    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = $value ? strtoupper($value) : null;
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
        $this->attributes['embarcacion'] = $value ? strtoupper($value) : null;
    }

    public function setPuertoEmbarqueAttribute($value)
    {
        $this->attributes['puerto_embarque'] = $value ? strtoupper($value) : null;
    }

    // Mutadores para campos del cliente (igual que Pasaje)
    public function setNombreClienteAttribute($value)
    {
        $this->attributes['nombre_cliente'] = $value ? strtoupper($value) : null;
    }

    public function setDocumentoClienteAttribute($value)
    {
        $this->attributes['documento_cliente'] = $value ? strtoupper($value) : null;
    }

    public function setContactoClienteAttribute($value)
    {
        $this->attributes['contacto_cliente'] = $value ? strtoupper($value) : null;
    }

    public function setNacionalidadClienteAttribute($value)
    {
        $this->attributes['nacionalidad_cliente'] = $value ? strtoupper($value) : 'PERUANA';
    }

    // Accessor para obtener el total calculado
    public function getTotalAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }

    // RELACIONES
    
    /**
     * Relación con el pasaje original
     */
    public function pasaje()
    {
        return $this->belongsTo(Pasaje::class, 'pasaje_id');
    }

    // SCOPES PARA CONSULTAS COMUNES

    /**
     * Scope para ventas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para ventas anuladas
     */
    public function scopeAnuladas($query)
    {
        return $query->where('estado', 'anulado');
    }

    /**
     * Scope para ventas completadas
     */
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completado');
    }

    /**
     * Scope para filtrar por fecha específica
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('created_at', $fecha);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para filtrar por cliente
     */
    public function scopeByCliente($query, $nombreCliente)
    {
        return $query->where('nombre_cliente', 'LIKE', '%' . strtoupper($nombreCliente) . '%');
    }

    /**
     * Scope para filtrar por documento
     */
    public function scopeByDocumento($query, $documento)
    {
        return $query->where('documento_cliente', 'LIKE', '%' . strtoupper($documento) . '%');
    }

    /**
     * Scope para filtrar por embarcación
     */
    public function scopeByEmbarcacion($query, $embarcacion)
    {
        return $query->where('embarcacion', 'LIKE', '%' . strtoupper($embarcacion) . '%');
    }

    /**
     * Scope para filtrar por medio de pago
     */
    public function scopeByMedioPago($query, $medioPago)
    {
        return $query->where('medio_pago', $medioPago);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeByEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para ventas de hoy
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    /**
     * Scope para ventas de esta semana
     */
    public function scopeEstaSemana($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope para ventas de este mes
     */
    public function scopeEsteMes($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    // MÉTODOS ESPECÍFICOS DEL HISTORIAL

    /**
     * Método para anular una venta
     */
    public function anular($motivo)
    {
        $this->update([
            'estado' => 'anulado',
            'motivo_anulacion' => $motivo
        ]);
        
        return $this;
    }

    /**
     * Método para completar una venta
     */
    public function completar()
    {
        $this->update(['estado' => 'completado']);
        return $this;
    }

    /**
     * Verificar si la venta está activa
     */
    public function estaActiva()
    {
        return $this->estado === 'activo';
    }

    /**
     * Verificar si la venta está anulada
     */
    public function estaAnulada()
    {
        return $this->estado === 'anulado';
    }

    /**
     * Verificar si la venta está completada
     */
    public function estaCompletada()
    {
        return $this->estado === 'completado';
    }

    // ACCESSORS PARA INFORMACIÓN FORMATEADA

    /**
     * Obtener información completa del cliente
     */
    public function getInformacionClienteAttribute()
    {
        return [
            'nombre' => $this->nombre_cliente,
            'documento' => $this->documento_cliente,
            'contacto' => $this->contacto_cliente,
            'nacionalidad' => $this->nacionalidad_cliente
        ];
    }

    /**
     * Obtener el nombre corto del cliente
     */
    public function getNombreCortoClienteAttribute()
    {
        if (empty($this->nombre_cliente)) return 'Sin nombre';
        
        $nombres = explode(' ', $this->nombre_cliente);
        return count($nombres) >= 2 ? $nombres[0] . ' ' . $nombres[1] : $this->nombre_cliente;
    }

    /**
     * Obtener información completa del viaje
     */
    public function getInformacionViajeAttribute()
    {
        return [
            'embarcacion' => $this->embarcacion,
            'puerto_embarque' => $this->puerto_embarque,
            'hora_embarque' => $this->hora_embarque,
            'hora_salida' => $this->hora_salida,
            'destino' => $this->destino,
            'ruta' => $this->ruta
        ];
    }

    /**
     * Obtener información completa del pago
     */
    public function getInformacionPagoAttribute()
    {
        return [
            'medio_pago' => $this->medio_pago,
            'pago_mixto' => $this->pago_mixto,
            'detalles_pago' => $this->detalles_pago,
            'subtotal' => $this->subtotal,
            'total' => $this->total
        ];
    }

    /**
     * Generar número de ticket para esta venta
     */
    public function generarNumeroTicket()
    {
        return 'HV-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener el estado con formato
     */
    public function getEstadoFormateadoAttribute()
    {
        $estados = [
            'activo' => 'Activo',
            'completado' => 'Completado',
            'anulado' => 'Anulado',
            'pendiente' => 'Pendiente'
        ];

        return $estados[$this->estado] ?? 'Desconocido';
    }

    /**
     * Obtener el medio de pago con formato
     */
    public function getMedioPagoFormateadoAttribute()
    {
        $medios = [
            'efectivo' => 'Efectivo',
            'yape' => 'Yape',
            'plin' => 'Plin',
            'transferencia' => 'Transferencia',
            'tarjeta' => 'Tarjeta'
        ];

        return $medios[$this->medio_pago] ?? 'Otros';
    }

    // MÉTODOS ESTÁTICOS PARA ESTADÍSTICAS

    /**
     * Obtener total de ventas activas
     */
    public static function totalVentasActivas()
    {
        return self::activas()->count();
    }

    /**
     * Obtener total de ingresos activos
     */
    public static function totalIngresosActivos()
    {
        return self::activas()->sum('subtotal');
    }

    /**
     * Obtener ventas por estado
     */
    public static function ventasPorEstado()
    {
        return self::selectRaw('estado, COUNT(*) as total, SUM(subtotal) as ingresos')
            ->groupBy('estado')
            ->get();
    }

    /**
     * Obtener ventas por medio de pago
     */
    public static function ventasPorMedioPago()
    {
        return self::selectRaw('medio_pago, COUNT(*) as total, SUM(subtotal) as ingresos')
            ->where('estado', '!=', 'anulado')
            ->groupBy('medio_pago')
            ->get();
    }

    /**
     * Obtener embarcaciones más populares
     */
    public static function embarcacionesPopulares($limite = 5)
    {
        return self::selectRaw('embarcacion, COUNT(*) as total_viajes, SUM(subtotal) as ingresos')
            ->where('estado', '!=', 'anulado')
            ->groupBy('embarcacion')
            ->orderBy('total_viajes', 'desc')
            ->limit($limite)
            ->get();
    }

    // VALIDACIONES

    /**
     * Validar si los datos del cliente están completos
     */
    public function clienteCompleto()
    {
        return !empty($this->nombre_cliente) && !empty($this->documento_cliente);
    }

    /**
     * Validar si los datos del viaje están completos
     */
    public function viajeCompleto()
    {
        return !empty($this->embarcacion) && 
               !empty($this->puerto_embarque) && 
               !empty($this->hora_embarque) && 
               !empty($this->hora_salida);
    }

    /**
     * Validar si la venta puede ser anulada
     */
    public function puedeSerAnulada()
    {
        return in_array($this->estado, ['activo', 'pendiente']);
    }
}