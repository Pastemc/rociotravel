<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\TramiteController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\Api\TipoDocumentoController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\DetallePasajeController;
use App\Http\Controllers\Api\PasajeController;
use App\Http\Controllers\Api\HistorialVentaController;
// NUEVOS CONTROLADORES AGREGADOS
use App\Http\Controllers\Api\EmbarcacionController;
use App\Http\Controllers\Api\PuertoEmbarqueController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// RUTAS DE AUTENTICACIÓN
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify', [AuthController::class, 'verify']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// RUTAS DE ADMINISTRACIÓN
Route::prefix('admin')->group(function () {
    Route::apiResource('users', AdminUserController::class);
});

// RUTAS PRINCIPALES DEL SISTEMA
Route::apiResource('areas', AreaController::class);
Route::apiResource('tramites', TramiteController::class);
Route::apiResource('personas', PersonaController::class);
Route::apiResource('tipos-documento', TipoDocumentoController::class);

// ========== NUEVAS RUTAS AGREGADAS ==========

// RUTAS EMBARCACIONES - PARA AUTO-LLENADO
Route::prefix('embarcaciones')->group(function () {
    Route::get('/', [EmbarcacionController::class, 'index']);
    Route::post('/', [EmbarcacionController::class, 'store']);
    Route::get('/{id}', [EmbarcacionController::class, 'show']);
    Route::put('/{id}', [EmbarcacionController::class, 'update']);
    Route::delete('/{id}', [EmbarcacionController::class, 'destroy']);
    Route::get('/buscar/{nombre}', [EmbarcacionController::class, 'buscarPorNombre']);
});

// RUTAS PUERTOS DE EMBARQUE - PARA AUTO-LLENADO
Route::prefix('puertos-embarque')->group(function () {
    Route::get('/', [PuertoEmbarqueController::class, 'index']);
    Route::post('/', [PuertoEmbarqueController::class, 'store']);
    Route::get('/{id}', [PuertoEmbarqueController::class, 'show']);
    Route::put('/{id}', [PuertoEmbarqueController::class, 'update']);
    Route::delete('/{id}', [PuertoEmbarqueController::class, 'destroy']);
    Route::get('/buscar/{nombre}', [PuertoEmbarqueController::class, 'buscarPorNombre']);
});

// ========== FIN NUEVAS RUTAS ==========

// RUTAS CLIENTES - MEJORADAS PARA AUTO-LLENADO
Route::prefix('clientes')->group(function () {
    // Rutas específicas ANTES de las rutas con parámetros
    Route::get('/buscar', [ClienteController::class, 'buscar']);
    Route::get('/buscar-por-documento/{numero_documento}', [ClienteController::class, 'buscarPorDocumento']);
    Route::get('/hoy', [ClienteController::class, 'hoy']);
    
    // Rutas CRUD estándar
    Route::get('/', [ClienteController::class, 'index']);
    Route::post('/', [ClienteController::class, 'store']);
    Route::get('/{id}', [ClienteController::class, 'show']);
    Route::put('/{id}', [ClienteController::class, 'update']);
    Route::delete('/{id}', [ClienteController::class, 'destroy']);
    
    // Rutas adicionales para funcionalidades extra
    Route::get('/{id}/estadisticas', [ClienteController::class, 'estadisticas']);
    Route::patch('/{id}/toggle-activo', [ClienteController::class, 'toggleActivo']);
});

// RUTAS DETALLES DE PASAJE
Route::prefix('detalle-pasajes')->group(function () {
    Route::get('/', [DetallePasajeController::class, 'index']);
    Route::post('/', [DetallePasajeController::class, 'store']);
    Route::get('/{id}', [DetallePasajeController::class, 'show']);
    Route::put('/{id}', [DetallePasajeController::class, 'update']);
    Route::delete('/{id}', [DetallePasajeController::class, 'destroy']);
});

// RUTAS PASAJES - ACTUALIZADA CON GENERACIÓN DE IMAGEN
Route::prefix('pasajes')->group(function () {
    Route::get('/', [PasajeController::class, 'index']);
    Route::post('/', [PasajeController::class, 'store']);
    Route::get('/{id}', [PasajeController::class, 'show']);
    Route::put('/{id}', [PasajeController::class, 'update']);
    Route::delete('/{id}', [PasajeController::class, 'destroy']);
    
    // Rutas adicionales
    Route::get('/buscar', [PasajeController::class, 'buscar']);
    Route::get('/estadisticas', [PasajeController::class, 'estadisticas']);
    Route::get('/codigo/{codigo}', [PasajeController::class, 'getByCode']);
    
    // Rutas para Ticket y PDF (mantenidas para compatibilidad)
    Route::post('/generar-pdf-tiempo-real', [PasajeController::class, 'generarPdfTiempoReal']);
    Route::post('/generar-ticket-venta', [PasajeController::class, 'generarTicketVenta']);
    
    // NUEVA RUTA PARA GENERAR IMAGEN JPG
    Route::post('/generar-imagen-tiempo-real', [PasajeController::class, 'generarImagenTiempoReal']);
    
    // ========== NUEVA RUTA PARA GUARDAR TICKET ==========
    Route::post('/guardar-ticket', [PasajeController::class, 'guardarTicket']);
});

// ========== RUTAS HISTORIAL DE VENTAS - SISTEMA PRINCIPAL ==========
// Historial de Ventas
Route::get('/historial-ventas', [HistorialVentaController::class, 'index']);
Route::get('/historial-ventas/buscar', [HistorialVentaController::class, 'buscar']);
Route::post('/historial-ventas', [HistorialVentaController::class, 'store']);
Route::get('/historial-ventas/{id}', [HistorialVentaController::class, 'show']);
Route::put('/historial-ventas/{id}/anular', [HistorialVentaController::class, 'anular']);
Route::get('/historial-ventas/{id}/ticket', [HistorialVentaController::class, 'generarTicket']);
Route::get('/historial-ventas/{id}/ticket-pdf', [HistorialVentaController::class, 'generarTicketPdf']);
Route::post('/historial-ventas/exportar-excel', [HistorialVentaController::class, 'exportarExcel']);
Route::get('/historial-ventas/estadisticas', [HistorialVentaController::class, 'estadisticas']);
Route::put('/historial-ventas/{id}/completar', [HistorialVentaController::class, 'completar']);
Route::get('/historial-ventas/resumen', [HistorialVentaController::class, 'resumen']);

// ========== RUTA ALTERNATIVA PARA GUARDAR TICKET DESDE EL NAVEGADOR ==========
Route::post('/guardar-ticket', [HistorialVentaController::class, 'guardarTicketDesdeNavegador']);

// ========== RUTAS DE TESTING ==========

// TEST BÁSICO
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now(),
        'server' => 'Laravel ' . app()->version(),
        'database_status' => \DB::connection()->getPdo() ? 'conectado' : 'desconectado'
    ]);
});

// TEST AUTO-LLENADO DE CLIENTES
Route::get('/test-auto-llenado/{documento?}', function ($documento = '12345678') {
    try {
        $cliente = \App\Models\Cliente::where('numero_documento', $documento)->first();
        
        return response()->json([
            'success' => true,
            'message' => 'Test de auto-llenado funcionando',
            'documento_buscado' => $documento,
            'cliente_encontrado' => $cliente ? true : false,
            'datos_cliente' => $cliente ? [
                'nombre' => $cliente->nombre,
                'contacto' => $cliente->contacto,
                'nacionalidad' => $cliente->nacionalidad
            ] : null,
            'url_endpoint' => "/api/clientes/buscar-por-documento/{$documento}",
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error en test de auto-llenado: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// NUEVO TEST EMBARCACIONES
Route::get('/test-embarcaciones', function () {
    try {
        // Crear embarcaciones de prueba si no existen
        $embarcaciones = ['KAORY', 'DON JULIO', 'ORIENTE 1', 'HAYDEE', 'MACHI MACHIN'];
        
        foreach ($embarcaciones as $nombre) {
            \App\Models\Embarcacion::firstOrCreate(['nombre' => $nombre]);
        }
        
        $count = \App\Models\Embarcacion::count();
        $todas = \App\Models\Embarcacion::all();
        
        return response()->json([
            'success' => true,
            'message' => 'API Embarcaciones funcionando correctamente',
            'total_embarcaciones' => $count,
            'embarcaciones' => $todas->pluck('nombre'),
            'endpoint' => '/api/embarcaciones',
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'hint' => 'Necesitas crear el modelo Embarcacion y su migración',
            'timestamp' => now()
        ], 500);
    }
});

// NUEVO TEST PUERTOS DE EMBARQUE
Route::get('/test-puertos', function () {
    try {
        // Crear puertos de prueba si no existen
        $puertos = ['CALLE PEVAS #456', 'CALLE PEVAS #408', 'PUERTO PRINCIPAL DE NAUTA', 'PUERTO LA BOCA'];
        
        foreach ($puertos as $nombre) {
            \App\Models\PuertoEmbarque::firstOrCreate(['nombre' => $nombre]);
        }
        
        $count = \App\Models\PuertoEmbarque::count();
        $todos = \App\Models\PuertoEmbarque::all();
        
        return response()->json([
            'success' => true,
            'message' => 'API Puertos de Embarque funcionando correctamente',
            'total_puertos' => $count,
            'puertos' => $todos->pluck('nombre'),
            'endpoint' => '/api/puertos-embarque',
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'hint' => 'Necesitas crear el modelo PuertoEmbarque y su migración',
            'timestamp' => now()
        ], 500);
    }
});

// TEST CLIENTES
Route::get('/test-clientes', function () {
    try {
        $count = \App\Models\Cliente::count();
        $ultimoCliente = \App\Models\Cliente::latest()->first();
        
        return response()->json([
            'success' => true,
            'message' => 'API de clientes funcionando correctamente',
            'total_clientes' => $count,
            'ultimo_cliente' => $ultimoCliente ? [
                'nombre' => $ultimoCliente->nombre,
                'documento' => $ultimoCliente->numero_documento,
                'fecha_registro' => $ultimoCliente->created_at
            ] : null,
            'endpoints_clientes' => [
                'GET /api/clientes' => 'Listar todos',
                'POST /api/clientes' => 'Crear nuevo',
                'GET /api/clientes/buscar-por-documento/{documento}' => 'Auto-llenado',
                'GET /api/clientes/buscar?q={query}' => 'Buscar general'
            ],
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error conectando a la base de datos: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// TEST DETALLES DE PASAJE
Route::get('/test-detalle-pasajes', function () {
    try {
        $count = \App\Models\DetallePasaje::count();
        $ultimo = \App\Models\DetallePasaje::latest()->first();

        return response()->json([
            'success' => true,
            'message' => 'API Detalles de Pasaje funcionando correctamente',
            'total_detalles' => $count,
            'ultimo_detalle' => $ultimo ? $ultimo->created_at : null,
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// TEST PASAJES
Route::get('/test-pasajes', function () {
    try {
        $count = \App\Models\Pasaje::count();
        $ultimo = \App\Models\Pasaje::latest()->first();

        return response()->json([
            'success' => true,
            'message' => 'API Pasajes funcionando correctamente',
            'total_pasajes' => $count,
            'ultimo_pasaje' => $ultimo ? [
                'id' => $ultimo->id,
                'descripcion' => $ultimo->descripcion,
                'subtotal' => $ultimo->subtotal,
                'fecha' => $ultimo->created_at
            ] : null,
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// TEST HISTORIAL DE VENTAS
Route::get('/test-historial-ventas', function () {
    try {
        // Verificar si la tabla existe primero
        if (!\Schema::hasTable('historial_ventas')) {
            return response()->json([
                'success' => false,
                'message' => 'La tabla historial_ventas no existe. Ejecuta php artisan migrate',
                'hint' => 'Necesitas crear la migración: php artisan make:migration create_historial_ventas_table',
                'timestamp' => now()
            ], 500);
        }

        $count = \App\Models\HistorialVenta::count();
        $ultimo = \App\Models\HistorialVenta::latest()->first();

        return response()->json([
            'success' => true,
            'message' => 'API Historial de Ventas funcionando correctamente',
            'total_ventas' => $count,
            'ultimo_registro' => $ultimo ? [
                'id' => $ultimo->id,
                'cliente' => $ultimo->nombre_cliente,
                'total' => $ultimo->subtotal,
                'estado' => $ultimo->estado,
                'fecha' => $ultimo->created_at
            ] : null,
            'endpoints_disponibles' => [
                'GET /api/historial-ventas' => 'Listar todas las ventas',
                'POST /api/historial-ventas' => 'Crear nueva venta',
                'GET /api/historial-ventas/buscar' => 'Buscar ventas con filtros',
                'PUT /api/historial-ventas/{id}/anular' => 'Anular venta',
                'POST /api/historial-ventas/{id}/generar-ticket' => 'Generar ticket',
                'GET /api/historial-ventas/{id}/visualizar-ticket' => 'Visualizar ticket guardado',
                'POST /api/pasajes/guardar-ticket' => 'Guardar ticket desde pasajes',
                'POST /api/guardar-ticket' => 'Guardar ticket desde navegador'
            ],
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'hint' => 'Posiblemente necesitas crear el modelo HistorialVenta o la migración',
            'timestamp' => now()
        ], 500);
    }
});

// HEALTH CHECK COMPLETO ACTUALIZADO
Route::get('/health', function () {
    try {
        $tablas = [
            'clientes' => \Schema::hasTable('clientes'),
            'detalles_pasaje' => \Schema::hasTable('detalles_pasaje'),
            'pasajes' => \Schema::hasTable('pasajes'),
            'historial_ventas' => \Schema::hasTable('historial_ventas'),
            'embarcaciones' => \Schema::hasTable('embarcaciones'),
            'puertos_embarque' => \Schema::hasTable('puertos_embarque')
        ];
        
        return response()->json([
            'status' => 'ok',
            'database' => \DB::connection()->getPdo() ? 'conectado' : 'desconectado',
            'tables' => $tablas,
            'models_status' => [
                'Cliente' => class_exists('\App\Models\Cliente'),
                'DetallePasaje' => class_exists('\App\Models\DetallePasaje'),
                'Pasaje' => class_exists('\App\Models\Pasaje'),
                'HistorialVenta' => class_exists('\App\Models\HistorialVenta'),
                'Embarcacion' => class_exists('\App\Models\Embarcacion'),
                'PuertoEmbarque' => class_exists('\App\Models\PuertoEmbarque')
            ],
            'controllers_status' => [
                'ClienteController' => class_exists('\App\Http\Controllers\Api\ClienteController'),
                'PasajeController' => class_exists('\App\Http\Controllers\Api\PasajeController'),
                'HistorialVentaController' => class_exists('\App\Http\Controllers\Api\HistorialVentaController'),
                'EmbarcacionController' => class_exists('\App\Http\Controllers\Api\EmbarcacionController'),
                'PuertoEmbarqueController' => class_exists('\App\Http\Controllers\Api\PuertoEmbarqueController')
            ],
            'endpoints_principales' => [
                'GET /api/clientes' => 'Gestión de clientes',
                'GET /api/clientes/buscar-por-documento/{documento}' => 'Auto-llenado de cliente',
                'GET /api/embarcaciones' => 'Gestión de embarcaciones',
                'GET /api/puertos-embarque' => 'Gestión de puertos de embarque',
                'GET /api/detalle-pasajes' => 'Detalles de pasajes',
                'GET /api/pasajes' => 'Gestión de pasajes',
                'POST /api/pasajes/generar-imagen-tiempo-real' => 'Generar imagen JPG',
                'POST /api/pasajes/guardar-ticket' => 'Guardar ticket desde pasajes',
                'GET /api/historial-ventas' => 'Historial de ventas',
                'GET /api/historial-ventas/buscar' => 'Búsqueda de ventas',
                'POST /api/historial-ventas' => 'Crear nueva venta',
                'PUT /api/historial-ventas/{id}/anular' => 'Anular venta',
                'GET /api/historial-ventas/{id}/visualizar-ticket' => 'Visualizar ticket guardado',
                'POST /api/guardar-ticket' => 'Guardar ticket desde navegador'
            ],
            'nuevas_funcionalidades' => [
                'Embarcaciones auto-llenado' => 'GET /api/embarcaciones',
                'Puertos auto-llenado' => 'GET /api/puertos-embarque',
                'Generar imagen JPG' => 'POST /api/pasajes/generar-imagen-tiempo-real',
                'Guardar ticket desde pasajes' => 'POST /api/pasajes/guardar-ticket',
                'Visualizar ticket guardado' => 'GET /api/historial-ventas/{id}/visualizar-ticket'
            ],
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// RUTA USER AUTENTICADO
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});