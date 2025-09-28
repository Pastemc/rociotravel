<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\Api\DetallePasajeController;
use App\Http\Controllers\Api\PasajeController;
use App\Http\Controllers\Api\HistorialVentaController; // CORREGIDO: Usar el del namespace Api

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// RUTA PRINCIPAL
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ========== RUTAS API MANUALES ==========
Route::prefix('api')->group(function () {
    
    // RUTA DE PRUEBA PRINCIPAL
    Route::get('test-clientes', function () {
        return response()->json([
            'success' => true,
            'message' => 'API funcionando correctamente',
            'timestamp' => now(),
            'routes' => [
                // CLIENTES
                'GET /api/clientes' => 'Listar clientes',
                'POST /api/clientes' => 'Crear cliente',
                'GET /api/clientes/buscar' => 'Buscar clientes',
                'GET /api/clientes/buscar-por-documento/{documento}' => 'Auto-llenado de cliente', // NUEVO
                
                // DETALLES DE PASAJE
                'GET /api/detalle-pasajes' => 'Listar detalles',
                'POST /api/detalle-pasajes' => 'Crear detalle',
                
                // PASAJES
                'GET /api/pasajes' => 'Listar pasajes',
                'POST /api/pasajes' => 'Crear pasaje',
                'POST /api/pasajes/generar-pdf-tiempo-real' => 'Generar PDF sin guardar',
                'POST /api/pasajes/generar-ticket-venta' => 'Generar ticket de venta',
                
                // HISTORIAL DE VENTAS - NUEVO
                'GET /api/historial-ventas' => 'Listar historial de ventas',
                'GET /api/historial-ventas/buscar' => 'Buscar en historial',
                'PUT /api/historial-ventas/{id}/anular' => 'Anular venta',
                'GET /api/historial-ventas/{id}/pdf' => 'Descargar PDF',
                'GET /api/historial-ventas/{id}/ticket' => 'Imprimir ticket',
            ]
        ]);
    });

    // ========== AUTENTICACIÓN ==========
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/verify', [AuthController::class, 'verify']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    
    // ========== USUARIOS ==========
    Route::apiResource('admin-users', AdminUserController::class);
    
    // ========== CLIENTES - MEJORADO PARA AUTO-LLENADO ==========
    // Rutas específicas ANTES de las rutas con parámetros
    Route::get('clientes/buscar', [ClienteController::class, 'buscar']);
    Route::get('clientes/buscar-por-documento/{numero_documento}', [ClienteController::class, 'buscarPorDocumento']); // NUEVA RUTA PARA AUTO-LLENADO
    Route::get('clientes/hoy', [ClienteController::class, 'hoy']);
    
    // Rutas CRUD estándar
    Route::get('clientes', [ClienteController::class, 'index']);
    Route::post('clientes', [ClienteController::class, 'store']);
    Route::get('clientes/{id}', [ClienteController::class, 'show']);
    Route::put('clientes/{id}', [ClienteController::class, 'update']);
    Route::delete('clientes/{id}', [ClienteController::class, 'destroy']);
    
    // Rutas adicionales para funcionalidades extra
    Route::get('clientes/{id}/estadisticas', [ClienteController::class, 'estadisticas']);
    Route::patch('clientes/{id}/toggle-activo', [ClienteController::class, 'toggleActivo']);

    // ========== DETALLE PASAJES ==========
    Route::get('detalle-pasajes', [DetallePasajeController::class, 'index']);
    Route::post('detalle-pasajes', [DetallePasajeController::class, 'store']);
    Route::get('detalle-pasajes/{id}', [DetallePasajeController::class, 'show']);
    Route::put('detalle-pasajes/{id}', [DetallePasajeController::class, 'update']);
    Route::delete('detalle-pasajes/{id}', [DetallePasajeController::class, 'destroy']);

    // ========== PASAJES ==========
    Route::get('pasajes', [PasajeController::class, 'index']);
    Route::post('pasajes', [PasajeController::class, 'store']);
    Route::get('pasajes/{id}', [PasajeController::class, 'show']);
    Route::put('pasajes/{id}', [PasajeController::class, 'update']);
    Route::delete('pasajes/{id}', [PasajeController::class, 'destroy']);
    Route::get('pasajes/codigo/{codigo}', [PasajeController::class, 'getByCode']);
    Route::get('pasajes/{id}/pdf', [PasajeController::class, 'generatePDF']);
    
    // RUTAS PARA PDF Y TICKET EN TIEMPO REAL
    Route::post('pasajes/generar-pdf-tiempo-real', [PasajeController::class, 'generarPdfTiempoReal']);
    Route::post('pasajes/generar-ticket-venta', [PasajeController::class, 'generarTicketVenta']);

    // ========== HISTORIAL DE VENTAS - NUEVO SISTEMA ==========
    
    // Rutas principales del historial
    Route::get('historial-ventas', [HistorialVentaController::class, 'index']);
    Route::post('historial-ventas', [HistorialVentaController::class, 'store']);
    
    // Búsquedas y filtros
    Route::get('historial-ventas/buscar', [HistorialVentaController::class, 'buscar']);
    Route::get('historial-ventas/buscar-cliente', [HistorialVentaController::class, 'buscarPorCliente']);
    Route::get('historial-ventas/recientes', [HistorialVentaController::class, 'recientes']);
    
    // Estadísticas y reportes
    Route::get('historial-ventas/estadisticas', [HistorialVentaController::class, 'estadisticas']);
    Route::get('historial-ventas/exportar', [HistorialVentaController::class, 'exportarExcel']);
    
    // Operaciones específicas por ID
    Route::get('historial-ventas/{id}', [HistorialVentaController::class, 'show']);
    Route::put('historial-ventas/{id}/anular', [HistorialVentaController::class, 'anular']);
    Route::put('historial-ventas/{id}/completar', [HistorialVentaController::class, 'completar']);
    Route::get('historial-ventas/{id}/pdf', [HistorialVentaController::class, 'generarPDF']);
    Route::get('historial-ventas/{id}/ticket', [HistorialVentaController::class, 'generarTicket']);
    Route::delete('historial-ventas/{id}', [HistorialVentaController::class, 'destroy']);
});

// ========== RUTAS WEB PARA NAVEGACIÓN DIRECTA ==========

// Rutas para acceso directo a PDFs y tickets (sin /api/)
Route::prefix('pasajes')->name('pasajes.')->group(function () {
    Route::get('/detalles', function () {
        return view('welcome'); // Vue Router se encargará de la ruta
    })->name('detalles');
    
    Route::get('/pdf/{id}', [PasajeController::class, 'generatePDF'])->name('pdf');
    Route::get('/ticket/{id}', [PasajeController::class, 'generarTicketVenta'])->name('ticket');
});

// ========== RUTAS WEB PARA HISTORIAL DE VENTAS - NUEVO ==========
Route::prefix('historial-ventas')->name('historial-ventas.')->group(function () {
    
    // Página principal del historial (Vue Router manejará la interfaz)
    Route::get('/', function () {
        return view('welcome'); // Vue Router se encarga de /historial-ventas
    })->name('index');
    
    // Ver detalle específico de venta (Vue Router + parámetro)
    Route::get('/{id}', function ($id) {
        return view('welcome'); // Vue Router manejará los detalles
    })->name('show')->where('id', '[0-9]+');
    
    // Descargar PDF directamente (sin Vue Router)
    Route::get('/{id}/pdf', [HistorialVentaController::class, 'generarPDF'])
        ->name('pdf')
        ->where('id', '[0-9]+');
    
    // Imprimir ticket directamente (sin Vue Router)
    Route::get('/{id}/ticket', [HistorialVentaController::class, 'generarTicket'])
        ->name('ticket')
        ->where('id', '[0-9]+');
    
    // Exportar Excel directamente
    Route::get('/exportar', [HistorialVentaController::class, 'exportarExcel'])
        ->name('exportar');
});

// ========== RUTAS LEGACY PARA COMPATIBILIDAD ==========
Route::prefix('nota-ventas')->name('nota-ventas.')->group(function () {
    // Redireccionar rutas antiguas al nuevo sistema
    Route::get('/', function () {
        return redirect('/historial-ventas');
    });
    
    Route::get('/{id}', function ($id) {
        return redirect("/historial-ventas/{$id}");
    })->where('id', '[0-9]+');
});

// Más redirecciones de compatibilidad
Route::get('/ver-nota-venta', function () {
    return redirect('/historial-ventas');
});

Route::get('/notas-venta', function () {
    return redirect('/historial-ventas');
});

// ========== RUTAS DEL SISTEMA PRINCIPAL ==========

// Rutas para el flujo principal de venta
Route::get('/datos-cliente', function () {
    return view('welcome'); // Vue Router maneja esta ruta
})->name('datos-cliente');

Route::get('/agregar-detalles-pasaje', function () {
    return view('welcome'); // Vue Router maneja esta ruta
})->name('agregar-detalles-pasaje');

Route::get('/agregar-detalle', function () {
    return view('welcome'); // Vue Router maneja esta ruta
})->name('agregar-detalle');

Route::get('/detalles-pasaje', function () {
    return view('welcome'); // Vue Router maneja esta ruta
})->name('detalles-pasaje');

// ========== RUTAS ADMINISTRATIVAS ==========

Route::get('/usuarios', function () {
    return view('welcome'); // Vue Router maneja esta ruta
})->name('usuarios');

Route::get('/reportes', function () {
    return view('welcome'); // Vue Router maneja esta ruta
})->name('reportes');

Route::get('/configuracion', function () {
    return view('welcome'); // Vue Router maneja esta ruta
})->name('configuracion');

// ========== RUTAS DE TESTING Y DESARROLLO ==========

// TEST AUTO-LLENADO DE CLIENTES - NUEVO
Route::get('/test-auto-llenado/{documento?}', function ($documento = '12345678') {
    try {
        $cliente = \App\Models\Cliente::where('numero_documento', $documento)->first();
        
        return response()->json([
            'success' => true,
            'message' => 'Test de auto-llenado funcionando desde WEB.PHP',
            'documento_buscado' => $documento,
            'cliente_encontrado' => $cliente ? true : false,
            'datos_cliente' => $cliente ? [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'contacto' => $cliente->contacto,
                'nacionalidad' => $cliente->nacionalidad,
                'created_at' => $cliente->created_at
            ] : null,
            'url_endpoint_api' => "/api/clientes/buscar-por-documento/{$documento}",
            'url_endpoint_web' => "/api/clientes/buscar-por-documento/{$documento}",
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

Route::get('/test-sistema', function () {
    try {
        // Verificar que todos los modelos existan
        $tests = [
            'clientes' => \App\Models\Cliente::count(),
            'detalle_pasajes' => \App\Models\DetallePasaje::count(),
            'pasajes' => \App\Models\Pasaje::count(),
            'historial_ventas' => \App\Models\HistorialVenta::count(), // NUEVO
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Sistema funcionando correctamente',
            'counts' => $tests,
            'routes_web' => [
                '/' => 'Página principal',
                '/datos-cliente' => 'Registro de clientes',
                '/agregar-detalles-pasaje' => 'Agregar detalles',
                '/detalles-pasaje' => 'Finalizar pasaje',
                '/historial-ventas' => 'Historial de ventas', // NUEVO
            ],
            'auto_llenado_test' => [
                'endpoint' => '/api/clientes/buscar-por-documento/{numero_documento}',
                'test_url' => '/test-auto-llenado/12345678',
                'descripcion' => 'Funcionalidad de auto-llenado de cliente por documento'
            ],
            'timestamp' => now()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error en el sistema: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// Ruta de prueba específica para historial
Route::get('/test-historial', function () {
    try {
        // Verificar que el modelo HistorialVenta funcione
        $count = \App\Models\HistorialVenta::count();
        $recientes = \App\Models\HistorialVenta::recientes(7)->count();
        $activas = \App\Models\HistorialVenta::activas()->count();
        
        return response()->json([
            'success' => true,
            'message' => 'Historial de Ventas funcionando correctamente',
            'estadisticas' => [
                'total' => $count,
                'recientes_7_dias' => $recientes,
                'activas' => $activas,
                'anuladas' => $count - $activas
            ],
            'rutas_disponibles' => [
                'GET /historial-ventas' => 'Ver historial',
                'GET /historial-ventas/buscar' => 'Buscar ventas',
                'POST /historial-ventas/{id}/anular' => 'Anular venta',
                'GET /historial-ventas/{id}/pdf' => 'Descargar PDF',
                'GET /historial-ventas/{id}/ticket' => 'Imprimir ticket',
            ],
            'timestamp' => now()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error en historial: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// TEST ESPECÍFICO PARA CLIENTES Y AUTO-LLENADO
Route::get('/test-clientes-completo', function () {
    try {
        $totalClientes = \App\Models\Cliente::count();
        $clientesActivos = \App\Models\Cliente::where('activo', true)->count();
        $ultimoCliente = \App\Models\Cliente::latest()->first();
        
        // Probar algunos documentos de ejemplo
        $documentosPrueba = ['12345678', '87654321', '11111111'];
        $resultadosPrueba = [];
        
        foreach ($documentosPrueba as $doc) {
            $cliente = \App\Models\Cliente::where('numero_documento', $doc)->first();
            $resultadosPrueba[$doc] = $cliente ? [
                'encontrado' => true,
                'nombre' => $cliente->nombre,
                'nacionalidad' => $cliente->nacionalidad
            ] : ['encontrado' => false];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Test completo de clientes funcionando correctamente',
            'estadisticas' => [
                'total_clientes' => $totalClientes,
                'clientes_activos' => $clientesActivos,
                'clientes_inactivos' => $totalClientes - $clientesActivos
            ],
            'ultimo_cliente' => $ultimoCliente ? [
                'id' => $ultimoCliente->id,
                'nombre' => $ultimoCliente->nombre,
                'documento' => $ultimoCliente->numero_documento,
                'fecha_registro' => $ultimoCliente->created_at
            ] : null,
            'test_auto_llenado' => $resultadosPrueba,
            'endpoints_disponibles' => [
                'GET /api/clientes' => 'Listar todos los clientes',
                'POST /api/clientes' => 'Crear nuevo cliente',
                'GET /api/clientes/buscar-por-documento/{doc}' => 'Auto-llenado por documento',
                'GET /api/clientes/buscar?q={query}' => 'Búsqueda general',
                'GET /api/clientes/{id}/estadisticas' => 'Estadísticas del cliente'
            ],
            'timestamp' => now()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error en test de clientes: ' . $e->getMessage(),
            'timestamp' => now()
        ], 500);
    }
});

// ========== RUTA CATCH-ALL PARA VUE ROUTER (DEBE IR AL FINAL) ==========
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');