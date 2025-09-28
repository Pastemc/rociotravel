<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HistorialVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class HistorialVentaController extends Controller
{
    /**
     * Obtener todo el historial de ventas
     */
    public function index(Request $request)
    {
        try {
            Log::info('Solicitando historial de ventas completo');

            $query = HistorialVenta::query();

            // Filtros opcionales
            if ($request->has('estado') && $request->estado) {
                $query->byEstado($request->estado);
            }

            if ($request->has('fecha_desde') && $request->has('fecha_hasta')) {
                $query->porRangoFechas(
                    Carbon::parse($request->fecha_desde)->startOfDay(),
                    Carbon::parse($request->fecha_hasta)->endOfDay()
                );
            }

            if ($request->has('embarcacion') && $request->embarcacion) {
                $query->byEmbarcacion($request->embarcacion);
            }

            // Ordenar por fecha de creación (más recientes primero)
            $ventas = $query->orderBy('created_at', 'desc')->get();

            Log::info('Historial de ventas obtenido exitosamente', [
                'total_registros' => $ventas->count(),
                'filtros' => $request->only(['estado', 'fecha_desde', 'fecha_hasta', 'embarcacion'])
            ]);

            return response()->json([
                'success' => true,
                'data' => $ventas,
                'total' => $ventas->count(),
                'total_activas' => $ventas->where('estado', 'activo')->count(),
                'total_anuladas' => $ventas->where('estado', 'anulado')->count(),
                'ingresos_totales' => $ventas->where('estado', '!=', 'anulado')->sum('subtotal'),
                'message' => 'Historial de ventas obtenido correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@index: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Buscar ventas con filtros específicos
     */
    public function buscar(Request $request)
    {
        try {
            Log::info('Buscando ventas con filtros', $request->all());

            $query = HistorialVenta::query();

            // Filtro por fechas
            if ($request->has('fecha_inicio') && $request->fecha_inicio) {
                $query->whereDate('created_at', '>=', $request->fecha_inicio);
            }

            if ($request->has('fecha_fin') && $request->fecha_fin) {
                $query->whereDate('created_at', '<=', $request->fecha_fin);
            }

            // Filtro por documento
            if ($request->has('numero_doc') && $request->numero_doc) {
                $query->byDocumento($request->numero_doc);
            }

            // Filtro por nombre del cliente
            if ($request->has('nombre_cliente') && $request->nombre_cliente) {
                $query->byCliente($request->nombre_cliente);
            }

            // Filtro por embarcación
            if ($request->has('embarcacion') && $request->embarcacion) {
                $query->byEmbarcacion($request->embarcacion);
            }

            // Filtro por estado
            if ($request->has('estado') && $request->estado) {
                $query->byEstado($request->estado);
            }

            // Filtro por medio de pago
            if ($request->has('medio_pago') && $request->medio_pago) {
                $query->byMedioPago($request->medio_pago);
            }

            // Búsqueda general por texto
            if ($request->has('buscar') && $request->buscar) {
                $textoBusqueda = $request->buscar;
                $query->where(function($q) use ($textoBusqueda) {
                    $q->where('nombre_cliente', 'LIKE', "%{$textoBusqueda}%")
                      ->orWhere('documento_cliente', 'LIKE', "%{$textoBusqueda}%")
                      ->orWhere('descripcion', 'LIKE', "%{$textoBusqueda}%")
                      ->orWhere('embarcacion', 'LIKE', "%{$textoBusqueda}%")
                      ->orWhere('destino', 'LIKE', "%{$textoBusqueda}%");
                });
            }

            // Ordenamiento
            $ordenPor = $request->get('orden_por', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            $query->orderBy($ordenPor, $direccion);

            $ventas = $query->get();

            Log::info('Búsqueda de ventas completada', [
                'resultados_encontrados' => $ventas->count(),
                'filtros_aplicados' => $request->all()
            ]);

            return response()->json([
                'success' => true,
                'data' => $ventas,
                'total' => $ventas->count(),
                'filtros_aplicados' => $request->only([
                    'fecha_inicio', 'fecha_fin', 'numero_doc', 'nombre_cliente', 
                    'embarcacion', 'estado', 'medio_pago', 'buscar'
                ]),
                'message' => 'Búsqueda completada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@buscar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Crear nueva venta en el historial (método opcional)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // Datos del cliente (requeridos)
                'nombre_cliente' => 'required|string|max:255|min:2',
                'documento_cliente' => 'required|string|max:50|min:8',
                'contacto_cliente' => 'nullable|string|max:50',
                'nacionalidad_cliente' => 'required|string|max:100|min:2',
                
                // Datos del pasaje (requeridos)
                'cantidad' => 'required|integer|min:1|max:999',
                'descripcion' => 'required|string|max:255',
                'precio_unitario' => 'required|numeric|min:0|max:9999.99',
                'subtotal' => 'required|numeric|min:0|max:999999.99',
                'destino' => 'nullable|string|max:100',
                'ruta' => 'nullable|string|max:255',
                
                // Datos del viaje (requeridos)
                'embarcacion' => 'required|string|max:100',
                'puerto_embarque' => 'required|string|max:255',
                'hora_embarque' => 'required|string|max:20',
                'hora_salida' => 'required|string|max:20',
                
                // Datos de pago (requeridos)
                'medio_pago' => 'required|string|in:efectivo,yape,plin,transferencia,tarjeta',
                'pago_mixto' => 'sometimes|boolean',
                'detalles_pago' => 'nullable|string|max:255',
                
                // Estado y observaciones
                'estado' => 'sometimes|string|in:activo,completado,anulado,pendiente',
                'nota' => 'nullable|string|max:500',
                'pasaje_id' => 'nullable|integer|exists:pasajes,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validar que el subtotal coincida con el cálculo
            $subtotalCalculado = $request->cantidad * $request->precio_unitario;
            if (abs($subtotalCalculado - $request->subtotal) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'El subtotal no coincide con la cantidad x precio unitario',
                    'errors' => ['subtotal' => ['Subtotal incorrecto']]
                ], 422);
            }

            $venta = HistorialVenta::create([
                // Referencias
                'pasaje_id' => $request->pasaje_id,
                
                // Datos del cliente
                'nombre_cliente' => $request->nombre_cliente,
                'documento_cliente' => $request->documento_cliente,
                'contacto_cliente' => $request->contacto_cliente,
                'nacionalidad_cliente' => $request->nacionalidad_cliente ?? 'PERUANA',
                
                // Datos del pasaje
                'cantidad' => $request->cantidad,
                'descripcion' => $request->descripcion,
                'precio_unitario' => $request->precio_unitario,
                'subtotal' => $request->subtotal,
                'destino' => $request->destino,
                'ruta' => $request->ruta,
                
                // Datos del viaje
                'embarcacion' => $request->embarcacion,
                'puerto_embarque' => $request->puerto_embarque,
                'hora_embarque' => $request->hora_embarque,
                'hora_salida' => $request->hora_salida,
                
                // Datos de pago
                'medio_pago' => $request->medio_pago,
                'pago_mixto' => $request->pago_mixto ?? false,
                'detalles_pago' => $request->detalles_pago,
                
                // Estado y observaciones
                'estado' => $request->estado ?? 'activo',
                'nota' => $request->nota
            ]);

            Log::info('Venta creada directamente en historial', [
                'venta_id' => $venta->id,
                'cliente' => $venta->nombre_cliente,
                'subtotal' => $venta->subtotal
            ]);

            return response()->json([
                'success' => true,
                'data' => $venta,
                'message' => 'Venta creada exitosamente en el historial'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@store: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la venta: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Mostrar una venta específica
     */
    public function show($id)
    {
        try {
            $venta = HistorialVenta::with('pasaje')->find($id);

            if (!$venta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada',
                    'data' => null
                ], 404);
            }

            Log::info('Venta consultada', ['venta_id' => $id]);

            return response()->json([
                'success' => true,
                'data' => $venta,
                'message' => 'Venta encontrada'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@show: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la venta: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Anular una venta
     */
    public function anular(Request $request, $id)
    {
        try {
            $venta = HistorialVenta::find($id);

            if (!$venta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada',
                    'data' => null
                ], 404);
            }

            if ($venta->estaAnulada()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venta ya está anulada',
                    'data' => $venta
                ], 400);
            }

            if (!$venta->puedeSerAnulada()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venta no puede ser anulada en su estado actual',
                    'data' => $venta
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'motivo_anulacion' => 'required|string|min:10|max:500'
            ], [
                'motivo_anulacion.required' => 'El motivo de anulación es obligatorio',
                'motivo_anulacion.min' => 'El motivo debe tener al menos 10 caracteres'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motivo de anulación requerido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $venta->anular($request->motivo_anulacion);

            Log::info('Venta anulada exitosamente', [
                'venta_id' => $venta->id,
                'cliente' => $venta->nombre_cliente,
                'motivo' => $request->motivo_anulacion,
                'usuario' => auth()->user()->id ?? 'sistema'
            ]);

            return response()->json([
                'success' => true,
                'data' => $venta->fresh(),
                'message' => 'Venta anulada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@anular: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al anular la venta: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Generar ticket de una venta
     */
    public function generarTicket($id)
    {
        try {
            $venta = HistorialVenta::find($id);

            if (!$venta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            if ($venta->estaAnulada()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede generar ticket de una venta anulada'
                ], 400);
            }

            // Preparar datos para el ticket
            $datosTicket = [
                // Información de la empresa
                'empresa' => 'ROCÍO TRAVEL',
                'direccion' => 'Calle. Pevas N° 366 - IQUITOS',
                'contacto' => '+51901978379',
                'yape' => '989667653',
                
                // Datos del ticket
                'numero_ticket' => $venta->generarNumeroTicket(),
                'fecha_emision' => Carbon::now()->format('d/m/Y'),
                'hora_emision' => Carbon::now()->format('H:i:s'),
                
                // Datos del cliente
                'cliente' => [
                    'nombre' => $venta->nombre_cliente,
                    'documento' => $venta->documento_cliente,
                    'contacto' => $venta->contacto_cliente ?? '',
                    'nacionalidad' => $venta->nacionalidad_cliente ?? 'PERUANA'
                ],
                
                // Datos del pasaje
                'cantidad' => $venta->cantidad,
                'descripcion' => $venta->descripcion,
                'precio_unitario' => number_format($venta->precio_unitario, 2),
                'subtotal' => number_format($venta->subtotal, 2),
                'total' => number_format($venta->total, 2),
                
                // Datos del viaje
                'fecha_viaje' => $venta->created_at->format('d/m/Y'),
                'embarcacion' => $venta->embarcacion,
                'puerto_embarque' => $venta->puerto_embarque,
                'hora_embarque' => $venta->hora_embarque,
                'hora_salida' => $venta->hora_salida,
                'destino' => $venta->destino ?? $venta->descripcion,
                'medio_pago' => strtoupper($venta->medio_pago),
                'nota' => $venta->nota ?? '',
                
                // Información adicional
                'fecha_impresion' => Carbon::now()->format('d/m/Y H:i:s'),
                'operador' => 'ROCÍO TRAVEL',
                'estado' => $venta->estado_formateado
            ];

            // Generar HTML para el ticket
            $html = view('ticket.historial-venta', $datosTicket)->render();
            
            Log::info('Ticket generado para venta del historial', [
                'venta_id' => $venta->id,
                'numero_ticket' => $datosTicket['numero_ticket']
            ]);
            
            return response($html)->header('Content-Type', 'text/html');

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@generarTicket: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar historial a Excel
     */
    public function exportarExcel(Request $request)
    {
        try {
            Log::info('Iniciando exportación a Excel del historial de ventas');

            $datos = $request->get('datos', []);

            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay datos para exportar'
                ], 400);
            }

            // Preparar datos para Excel
            $datosExcel = [];
            foreach ($datos as $item) {
                $datosExcel[] = [
                    'Fecha' => $item['Fecha'] ?? 'N/A',
                    'Cliente' => $item['Cliente'] ?? 'N/A',
                    'DNI' => $item['DNI'] ?? 'N/A',
                    'Contacto' => $item['Contacto'] ?? 'N/A',
                    'Nacionalidad' => $item['Nacionalidad'] ?? 'PERUANA',
                    'Cantidad' => $item['Cantidad'] ?? 1,
                    'Descripción' => $item['Descripción'] ?? 'N/A',
                    'Precio Unitario' => $item['Precio Unitario'] ?? '0.00',
                    'Subtotal' => $item['Subtotal'] ?? '0.00',
                    'Destino' => $item['Destino'] ?? 'N/A',
                    'Ruta' => $item['Ruta'] ?? 'N/A',
                    'Embarcación' => $item['Embarcación'] ?? 'N/A',
                    'Puerto Embarque' => $item['Puerto Embarque'] ?? 'N/A',
                    'Hora Embarque' => $item['Hora Embarque'] ?? 'N/A',
                    'Hora Salida' => $item['Hora Salida'] ?? 'N/A',
                    'Medio de Pago' => $item['Medio de Pago'] ?? 'N/A',
                    'Pago Mixto' => $item['Pago Mixto'] ?? 'No',
                    'Detalles Pago' => $item['Detalles Pago'] ?? 'N/A',
                    'Estado' => $item['Estado'] ?? 'Activo',
                    'Notas' => $item['Notas'] ?? 'N/A'
                ];
            }

            // Crear contenido CSV (simple pero efectivo)
            $csvContent = '';
            
            // Encabezados
            $headers = array_keys($datosExcel[0]);
            $csvContent .= implode(',', $headers) . "\n";
            
            // Datos
            foreach ($datosExcel as $fila) {
                $valores = array_map(function($valor) {
                    return '"' . str_replace('"', '""', $valor) . '"';
                }, array_values($fila));
                $csvContent .= implode(',', $valores) . "\n";
            }

            // Convertir a formato Excel (usando BOM para UTF-8)
            $csvContent = "\xEF\xBB\xBF" . $csvContent;

            Log::info('Excel generado exitosamente', [
                'total_registros' => count($datosExcel)
            ]);

            return response($csvContent)
                ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="historial-ventas-' . date('Y-m-d') . '.csv"')
                ->header('Cache-Control', 'max-age=0');

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@exportarExcel: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del historial
     */
    public function estadisticas(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
            $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());

            $query = HistorialVenta::porRangoFechas($fechaInicio, $fechaFin);

            $estadisticas = [
                'resumen' => [
                    'total_ventas' => $query->count(),
                    'ventas_activas' => $query->activas()->count(),
                    'ventas_anuladas' => $query->anuladas()->count(),
                    'ventas_completadas' => $query->completadas()->count(),
                    'ingresos_totales' => $query->where('estado', '!=', 'anulado')->sum('subtotal'),
                    'ingresos_promedio' => $query->where('estado', '!=', 'anulado')->avg('subtotal'),
                ],
                'por_estado' => HistorialVenta::ventasPorEstado(),
                'por_medio_pago' => HistorialVenta::ventasPorMedioPago(),
                'embarcaciones_populares' => HistorialVenta::embarcacionesPopulares(10),
                'ventas_por_dia' => $query->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad, SUM(subtotal) as total')
                    ->where('estado', '!=', 'anulado')
                    ->groupBy('fecha')
                    ->orderBy('fecha')
                    ->get(),
                'rango_fechas' => [
                    'inicio' => $fechaInicio,
                    'fin' => $fechaFin
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas obtenidas correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@estadisticas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Completar una venta
     */
    public function completar($id)
    {
        try {
            $venta = HistorialVenta::find($id);

            if (!$venta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            if ($venta->estaAnulada()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede completar una venta anulada'
                ], 400);
            }

            $venta->completar();

            Log::info('Venta completada', ['venta_id' => $venta->id]);

            return response()->json([
                'success' => true,
                'data' => $venta->fresh(),
                'message' => 'Venta completada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@completar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al completar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen rápido
     */
    public function resumen()
    {
        try {
            $resumen = [
                'hoy' => [
                    'ventas' => HistorialVenta::hoy()->count(),
                    'ingresos' => HistorialVenta::hoy()->where('estado', '!=', 'anulado')->sum('subtotal'),
                    'anuladas' => HistorialVenta::hoy()->anuladas()->count()
                ],
                'esta_semana' => [
                    'ventas' => HistorialVenta::estaSemana()->count(),
                    'ingresos' => HistorialVenta::estaSemana()->where('estado', '!=', 'anulado')->sum('subtotal')
                ],
                'este_mes' => [
                    'ventas' => HistorialVenta::esteMes()->count(),
                    'ingresos' => HistorialVenta::esteMes()->where('estado', '!=', 'anulado')->sum('subtotal')
                ],
                'totales' => [
                    'ventas_activas' => HistorialVenta::totalVentasActivas(),
                    'ingresos_activos' => HistorialVenta::totalIngresosActivos()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $resumen,
                'message' => 'Resumen obtenido correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en HistorialVentaController@resumen: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen: ' . $e->getMessage()
            ], 500);
        }
    }
}