<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pasaje;
use App\Models\HistorialVenta; // AGREGAR ESTE IMPORT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PasajeController extends Controller
{
    /**
     * Listar todos los pasajes con filtros opcionales
     */
    public function index(Request $request)
    {
        try {
            $query = Pasaje::query();

            // Filtros opcionales
            if ($request->has('fecha_desde') && $request->has('fecha_hasta')) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->fecha_desde)->startOfDay(),
                    Carbon::parse($request->fecha_hasta)->endOfDay()
                ]);
            }

            if ($request->has('embarcacion') && $request->embarcacion) {
                $query->byEmbarcacion($request->embarcacion);
            }

            if ($request->has('cliente') && $request->cliente) {
                $query->byCliente($request->cliente);
            }

            if ($request->has('medio_pago') && $request->medio_pago) {
                $query->byMedioPago($request->medio_pago);
            }

            // Paginación
            $perPage = $request->get('per_page', 15);
            $pasajes = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $pasajes,
                'message' => 'Pasajes obtenidos correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en PasajeController@index: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los pasajes: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Crear un nuevo pasaje - MODIFICADO PARA COPIAR A HISTORIAL_VENTAS
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cantidad' => 'required|integer|min:1|max:999',
                'descripcion' => 'required|string|max:255',
                'precio_unitario' => 'required|numeric|min:0|max:9999.99',
                'subtotal' => 'required|numeric|min:0|max:999999.99',
                'destino' => 'nullable|string|max:100',
                'ruta' => 'nullable|string|max:255',
                'embarcacion' => 'required|string|max:100',
                'puerto_embarque' => 'required|string|max:255',
                'hora_embarque' => 'required|string|max:20',
                'hora_salida' => 'required|string|max:20',
                'medio_pago' => 'required|string|in:efectivo,yape,plin',
                'pago_mixto' => 'sometimes|boolean',
                'detalles_pago' => 'nullable|string|max:255',
                'nota' => 'nullable|string|max:500',
                // VALIDACIONES PARA CAMPOS DEL CLIENTE (REQUERIDOS)
                'nombre_cliente' => 'required|string|max:255|min:2',
                'documento_cliente' => 'required|string|max:50|min:8',
                'contacto_cliente' => 'nullable|string|max:50',
                'nacionalidad_cliente' => 'required|string|max:100|min:2'
            ], [
                // Mensajes personalizados
                'nombre_cliente.required' => 'El nombre del cliente es obligatorio',
                'documento_cliente.required' => 'El documento del cliente es obligatorio',
                'nacionalidad_cliente.required' => 'La nacionalidad del cliente es obligatoria',
                'cantidad.max' => 'La cantidad no puede ser mayor a 999',
                'precio_unitario.max' => 'El precio unitario no puede ser mayor a 9999.99',
                'documento_cliente.min' => 'El documento debe tener al menos 8 caracteres'
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

            // PASO 1: Crear el pasaje en la tabla pasajes
            $pasaje = Pasaje::create([
                'cantidad' => $request->cantidad,
                'descripcion' => $request->descripcion,
                'precio_unitario' => $request->precio_unitario,
                'subtotal' => $request->subtotal,
                'destino' => $request->destino,
                'ruta' => $request->ruta,
                'embarcacion' => $request->embarcacion,
                'puerto_embarque' => $request->puerto_embarque,
                'hora_embarque' => $request->hora_embarque,
                'hora_salida' => $request->hora_salida,
                'medio_pago' => $request->medio_pago,
                'pago_mixto' => $request->pago_mixto ?? false,
                'detalles_pago' => $request->detalles_pago,
                'nota' => $request->nota,
                'nombre_cliente' => $request->nombre_cliente,
                'documento_cliente' => $request->documento_cliente,
                'contacto_cliente' => $request->contacto_cliente,
                'nacionalidad_cliente' => $request->nacionalidad_cliente ?? 'PERUANA'
            ]);

            Log::info('Pasaje creado exitosamente', ['pasaje_id' => $pasaje->id]);

            // PASO 2: AUTOMÁTICAMENTE COPIAR A HISTORIAL_VENTAS
            $historialCreado = $this->copiarAHistorialVentas($pasaje);

            return response()->json([
                'success' => true,
                'data' => $pasaje,
                'historial_creado' => $historialCreado,
                'message' => 'Pasaje creado y agregado al historial exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error en PasajeController@store: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear pasaje: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * MÉTODO NUEVO: Copiar automáticamente datos de pasaje a historial_ventas
     */
    private function copiarAHistorialVentas($pasaje)
    {
        try {
            Log::info('Iniciando copia de pasaje a historial_ventas', ['pasaje_id' => $pasaje->id]);

            // Crear registro en historial_ventas con los mismos datos
            $historialVenta = HistorialVenta::create([
                // Referencias
                'pasaje_id' => $pasaje->id,
                
                // Datos del cliente
                'nombre_cliente' => $pasaje->nombre_cliente,
                'documento_cliente' => $pasaje->documento_cliente,
                'contacto_cliente' => $pasaje->contacto_cliente,
                'nacionalidad_cliente' => $pasaje->nacionalidad_cliente ?? 'PERUANA',
                
                // Datos del pasaje
                'cantidad' => $pasaje->cantidad,
                'descripcion' => $pasaje->descripcion,
                'precio_unitario' => $pasaje->precio_unitario,
                'subtotal' => $pasaje->subtotal,
                'destino' => $pasaje->destino,
                'ruta' => $pasaje->ruta,
                
                // Datos del viaje
                'embarcacion' => $pasaje->embarcacion,
                'puerto_embarque' => $pasaje->puerto_embarque,
                'hora_embarque' => $pasaje->hora_embarque,
                'hora_salida' => $pasaje->hora_salida,
                
                // Datos de pago
                'medio_pago' => $pasaje->medio_pago,
                'pago_mixto' => $pasaje->pago_mixto ?? false,
                'detalles_pago' => $pasaje->detalles_pago,
                
                // Estado y observaciones
                'estado' => 'activo', // Estado inicial
                'nota' => $pasaje->nota,
                
                // Timestamps explícitos
                'created_at' => $pasaje->created_at ?? now(),
                'updated_at' => now()
            ]);

            Log::info('Pasaje copiado exitosamente al historial de ventas', [
                'pasaje_id' => $pasaje->id,
                'historial_venta_id' => $historialVenta->id,
                'cliente' => $pasaje->nombre_cliente,
                'subtotal' => $pasaje->subtotal
            ]);

            return [
                'success' => true,
                'historial_venta_id' => $historialVenta->id,
                'message' => 'Registro creado en historial de ventas'
            ];

        } catch (\Exception $e) {
            Log::error('Error copiando pasaje al historial de ventas', [
                'pasaje_id' => $pasaje->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // NO lanzar excepción para no afectar el guardado del pasaje principal
            // Solo registrar el error y continuar
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Error al crear registro en historial de ventas'
            ];
        }
    }

    /**
     * Generar PDF en tiempo real (sin guardar en base de datos)
     */
    public function generarPdfTiempoReal(Request $request)
    {
        try {
            // Validar datos requeridos para el PDF
            $validator = Validator::make($request->all(), [
                'cantidad' => 'required|integer|min:1',
                'descripcion' => 'required|string',
                'precio_unitario' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'embarcacion' => 'required|string',
                'puerto_embarque' => 'required|string',
                'hora_embarque' => 'required|string',
                'hora_salida' => 'required|string',
                'medio_pago' => 'required|string|in:efectivo,yape,plin',
                'cliente.nombre' => 'required|string',
                'cliente.documento' => 'required|string',
                'fecha_emision' => 'sometimes|string',
                'hora_emision' => 'sometimes|string',
                'operador' => 'sometimes|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos incompletos para generar PDF',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generar número de ticket único con timestamp
            $timestamp = Carbon::now()->format('ymdHis');
            $numeroTicket = 'CTE-' . $timestamp . '-' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);

            // Preparar datos para la vista del PDF
            $datosPDF = [
                // Información de la empresa
                'empresa' => 'ROCÍO TRAVEL',
                'subtitulo' => 'VENTA DE PASAJES FLUVIALES',
                'direcciones' => [
                    'IQUITOS - YURIMAGUAS - PUCALLPA - SANTA ROSA - INTUTO - SAN LORENZO,',
                    'TROMPETEROS - PANTOJA - REQUENA - Y PUERTOS INTERMEDIOS'
                ],
                'direccion_fisica' => 'Dirección: Calle. Pevas N° 366',
                'correo' => 'Correo: travelrocio19@gmail.com',
                'contacto' => 'Contacto: +51901978379',
                'yape' => 'Yape: 989667653',
                'ubicacion' => 'IQUITOS - MAYNAS - LORETO',

                // Datos del ticket
                'numero_ticket' => $numeroTicket,
                'fecha_emision' => $request->fecha_emision ?: Carbon::now()->format('d/m/Y'),
                'hora_emision' => $request->hora_emision ?: Carbon::now()->format('h:i A'),
                'operador' => $request->operador ?: 'ROCÍO TRAVEL',
                'medio_pago' => strtoupper($request->medio_pago),

                // Información del cliente (VALIDADA)
                'cliente' => [
                    'nombre' => strtoupper($request->cliente['nombre']),
                    'documento' => strtoupper($request->cliente['documento']),
                    'contacto' => $request->cliente['contacto'] ?? '',
                    'nacionalidad' => strtoupper($request->cliente['nacionalidad'] ?? 'PERUANA')
                ],

                // Detalles del pasaje
                'pasaje' => [
                    'cantidad' => $request->cantidad,
                    'descripcion' => strtoupper($request->descripcion),
                    'precio_unitario' => number_format($request->precio_unitario, 2),
                    'subtotal' => number_format($request->subtotal, 2)
                ],

                // Total
                'total' => number_format($request->total, 2),

                // Datos del viaje
                'viaje' => [
                    'fecha_viaje' => Carbon::now()->format('d/m/Y'),
                    'embarcacion' => strtoupper($request->embarcacion),
                    'puerto_embarque' => strtoupper($request->puerto_embarque),
                    'hora_embarque' => $request->hora_embarque,
                    'hora_salida' => $request->hora_salida,
                    'destino' => strtoupper($request->destino ?? ''),
                    'ruta' => strtoupper($request->ruta ?? $request->descripcion)
                ],

                // Información de pago
                'pago' => [
                    'medio_pago' => strtoupper($request->medio_pago),
                    'pago_mixto' => $request->pago_mixto ?? false,
                    'detalles_pago' => $request->detalles_pago ?? ''
                ],

                // Nota adicional
                'nota' => $request->nota ?: '',

                // Información adicional del pie
                'politicas' => [
                    'La empresa no aceptará devoluciones una vez realizada la venta y separado el cupo;',
                    'en caso que la embarcación haya partido y Ud. no abordó,',
                    'perderá su derecho a viajar y el valor de su pasaje.'
                ],
                'equipaje' => '15Kg por pasajero',
                'cambio_boleta' => 'ESTE TICKET PUEDE SER CAMBIADO POR BOLETA DE VENTA O FACTURA'
            ];

            // Generar PDF usando la vista
            $pdf = Pdf::loadView('pdf.pasaje-ticket', $datosPDF);
            
            // Configurar el PDF
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'debugKeepTemp' => false,
                'debugPng' => false,
                'debugCss' => false,
                'logOutputFile' => storage_path('logs/dompdf.log')
            ]);

            Log::info('PDF generado exitosamente', ['ticket' => $numeroTicket]);

            // Retornar PDF como descarga
            return $pdf->download("pasaje-{$numeroTicket}.pdf");

        } catch (\Exception $e) {
            Log::error('Error en PasajeController@generarPdfTiempoReal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar ticket de venta en formato HTML para imprimir
     */
    public function generarTicketVenta(Request $request)
    {
        try {
            // Validar datos requeridos para el ticket
            $validator = Validator::make($request->all(), [
                'numero_ticket' => 'required|string',
                'fecha_emision' => 'required|string',
                'hora_emision' => 'required|string',
                'cliente' => 'required|array',
                'cliente.nombre' => 'required|string',
                'cliente.documento' => 'required|string',
                'cantidad' => 'required|integer',
                'descripcion' => 'required|string',
                'precio_unitario' => 'required|string',
                'subtotal' => 'required|string',
                'total' => 'required|string',
                'fecha_viaje' => 'required|string',
                'embarcacion' => 'required|string',
                'puerto_embarque' => 'required|string',
                'hora_embarque' => 'required|string',
                'hora_salida' => 'required|string',
                'medio_pago' => 'required|string|in:efectivo,yape,plin'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos incompletos para generar ticket',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Preparar datos para la vista del ticket
            $datosTicket = [
                // Información de la empresa
                'empresa' => 'ROCÍO TRAVEL',
                'direccion' => 'Calle. Pevas N° 366 - IQUITOS',
                'contacto' => '+51901978379',
                'yape' => '989667653',
                
                // Datos del ticket
                'numero_ticket' => $request->numero_ticket,
                'fecha_emision' => $request->fecha_emision,
                'hora_emision' => $request->hora_emision,
                
                // Datos del cliente
                'cliente' => [
                    'nombre' => strtoupper($request->cliente['nombre']),
                    'documento' => strtoupper($request->cliente['documento']),
                    'contacto' => $request->cliente['contacto'] ?? '',
                    'nacionalidad' => strtoupper($request->cliente['nacionalidad'] ?? 'PERUANA')
                ],
                
                // Datos del pasaje
                'cantidad' => $request->cantidad,
                'descripcion' => strtoupper($request->descripcion),
                'precio_unitario' => $request->precio_unitario,
                'subtotal' => $request->subtotal,
                'total' => $request->total,
                
                // Datos del viaje
                'fecha_viaje' => $request->fecha_viaje,
                'embarcacion' => strtoupper($request->embarcacion),
                'puerto_embarque' => strtoupper($request->puerto_embarque),
                'hora_embarque' => $request->hora_embarque,
                'hora_salida' => $request->hora_salida,
                'medio_pago' => strtoupper($request->medio_pago),
                'nota' => $request->nota ?? '',
                
                // Información adicional
                'fecha_impresion' => Carbon::now()->format('d/m/Y H:i:s'),
                'operador' => 'ROCÍO TRAVEL'
            ];

            // Generar HTML para el ticket usando la vista blade
            $html = view('ticket.ticket-venta', $datosTicket)->render();
            
            Log::info('Ticket de venta generado', ['numero' => $request->numero_ticket]);
            
            // Retornar HTML directamente para imprimir
            return response($html)->header('Content-Type', 'text/html');

        } catch (\Exception $e) {
            Log::error('Error en PasajeController@generarTicketVenta: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al generar ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un pasaje específico
     */
    public function show($id)
    {
        try {
            $pasaje = Pasaje::find($id);

            if (!$pasaje) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasaje no encontrado',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pasaje,
                'message' => 'Pasaje encontrado'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en PasajeController@show: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pasaje: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Actualizar un pasaje
     */
    public function update(Request $request, $id)
    {
        try {
            $pasaje = Pasaje::find($id);

            if (!$pasaje) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasaje no encontrado',
                    'data' => null
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'cantidad' => 'sometimes|integer|min:1|max:999',
                'descripcion' => 'sometimes|string|max:255',
                'precio_unitario' => 'sometimes|numeric|min:0|max:9999.99',
                'subtotal' => 'sometimes|numeric|min:0|max:999999.99',
                'destino' => 'sometimes|nullable|string|max:100',
                'ruta' => 'sometimes|nullable|string|max:255',
                'embarcacion' => 'sometimes|string|max:100',
                'puerto_embarque' => 'sometimes|string|max:255',
                'hora_embarque' => 'sometimes|string|max:20',
                'hora_salida' => 'sometimes|string|max:20',
                'medio_pago' => 'sometimes|string|in:efectivo,yape,plin',
                'pago_mixto' => 'sometimes|boolean',
                'detalles_pago' => 'sometimes|nullable|string|max:255',
                'nota' => 'sometimes|nullable|string|max:500',
                'nombre_cliente' => 'sometimes|string|max:255|min:2',
                'documento_cliente' => 'sometimes|string|max:50|min:8',
                'contacto_cliente' => 'sometimes|nullable|string|max:50',
                'nacionalidad_cliente' => 'sometimes|string|max:100|min:2'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validar subtotal si se actualiza cantidad o precio
            if ($request->has('cantidad') || $request->has('precio_unitario')) {
                $cantidad = $request->get('cantidad', $pasaje->cantidad);
                $precio = $request->get('precio_unitario', $pasaje->precio_unitario);
                $subtotalCalculado = $cantidad * $precio;
                
                if ($request->has('subtotal') && abs($subtotalCalculado - $request->subtotal) > 0.01) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El subtotal no coincide con la cantidad x precio unitario',
                        'errors' => ['subtotal' => ['Subtotal incorrecto']]
                    ], 422);
                }
                
                // Auto-calcular subtotal si no se proporciona
                if (!$request->has('subtotal')) {
                    $request->merge(['subtotal' => $subtotalCalculado]);
                }
            }

            $pasaje->update($request->only([
                'cantidad', 'descripcion', 'precio_unitario', 'subtotal',
                'destino', 'ruta', 'embarcacion', 'puerto_embarque',
                'hora_embarque', 'hora_salida', 'medio_pago', 'pago_mixto',
                'detalles_pago', 'nota', 'nombre_cliente', 'documento_cliente',
                'contacto_cliente', 'nacionalidad_cliente'
            ]));

            Log::info('Pasaje actualizado', ['pasaje_id' => $pasaje->id]);

            return response()->json([
                'success' => true,
                'data' => $pasaje->fresh(),
                'message' => 'Pasaje actualizado exitosamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en PasajeController@update: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar pasaje: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Eliminar un pasaje
     */
    public function destroy($id)
    {
        try {
            $pasaje = Pasaje::find($id);

            if (!$pasaje) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasaje no encontrado',
                    'data' => null
                ], 404);
            }

            // Guardar datos para log antes de eliminar
            $datosPasaje = $pasaje->toArray();
            
            $pasaje->delete();

            Log::info('Pasaje eliminado', ['pasaje_eliminado' => $datosPasaje]);

            return response()->json([
                'success' => true,
                'message' => 'Pasaje eliminado exitosamente',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en PasajeController@destroy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar pasaje: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * NUEVO: Obtener estadísticas de pasajes
     */
    public function estadisticas(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
            $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());

            $query = Pasaje::whereBetween('created_at', [$fechaInicio, $fechaFin]);

            $estadisticas = [
                'total_pasajes' => $query->count(),
                'total_ingresos' => $query->sum('subtotal'),
                'promedio_por_pasaje' => $query->avg('subtotal'),
                'embarcaciones_populares' => $query->select('embarcacion')
                    ->selectRaw('COUNT(*) as total')
                    ->groupBy('embarcacion')
                    ->orderBy('total', 'desc')
                    ->limit(5)
                    ->get(),
                'medios_pago' => $query->select('medio_pago')
                    ->selectRaw('COUNT(*) as total')
                    ->groupBy('medio_pago')
                    ->get(),
                'ventas_por_dia' => $query->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad, SUM(subtotal) as total')
                    ->groupBy('fecha')
                    ->orderBy('fecha')
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas obtenidas correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en PasajeController@estadisticas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * NUEVO: Buscar pasajes por múltiples criterios
     */
    public function buscar(Request $request)
    {
        try {
            $query = Pasaje::query();

            // Búsqueda por texto (nombre cliente, documento, descripción)
            if ($request->has('buscar') && $request->buscar) {
                $textoBusqueda = $request->buscar;
                $query->where(function($q) use ($textoBusqueda) {
                    $q->where('nombre_cliente', 'LIKE', "%{$textoBusqueda}%")
                      ->orWhere('documento_cliente', 'LIKE', "%{$textoBusqueda}%")
                      ->orWhere('descripcion', 'LIKE', "%{$textoBusqueda}%")
                      ->orWhere('embarcacion', 'LIKE', "%{$textoBusqueda}%");
                });
            }

            // Ordenamiento
            $ordenPor = $request->get('orden_por', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            $query->orderBy($ordenPor, $direccion);

            // Paginación
            $perPage = $request->get('per_page', 10);
            $resultados = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $resultados,
                'message' => 'Búsqueda realizada correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error en PasajeController@buscar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}