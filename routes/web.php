<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\PersonaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\Api\DetallePasajeController;
use App\Http\Controllers\Api\PasajeController;
use App\Http\Controllers\Api\HistorialVentaController;
// NUEVOS CONTROLADORES AGREGADOS
use App\Http\Controllers\Api\EmbarcacionController;
use App\Http\Controllers\Api\PuertoEmbarqueController;

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
                'GET /api/clientes/buscar-por-documento/{documento}' => 'Auto-llenado de cliente',
                
                // EMBARCACIONES - NUEVO
                'GET /api/embarcaciones' => 'Listar embarcaciones',
                'POST /api/embarcaciones' => 'Crear embarcación',
                
                // PUERTOS DE EMBARQUE - NUEVO
                'GET /api/puertos-embarque' => 'Listar puertos',
                'POST /api/puertos-embarque' => 'Crear puerto',
                
                // DETALLES DE PASAJE
                'GET /api/detalle-pasajes' => 'Listar detalles',
                'POST /api/detalle-pasajes' => 'Crear detalle',
                
                // PASAJES
                'GET /api/pasajes' => 'Listar pasajes',
                'POST /api/pasajes' => 'Crear pasaje',
                'POST /api/pasajes/generar-imagen-tiempo-real' => 'Generar imagen JPG',
                'POST /api/pasajes/generar-ticket-venta' => 'Generar ticket de venta',
                'POST /api/pasajes/guardar-ticket' => 'Guardar ticket en BD', // NUEVA RUTA AGREGADA
                
                // HISTORIAL DE VENTAS
                'GET /api/historial-ventas' => 'Listar historial de ventas',
                'GET /api/historial-ventas/buscar' => 'Buscar en historial',
                'PUT /api/historial-ventas/{id}/anular' => 'Anular venta',
                'GET /api/historial-ventas/{id}/pdf' => 'Descargar PDF',
                'GET /api/historial-ventas/{id}/ticket' => 'Imprimir ticket',
                'GET /api/historial-ventas/{id}/visualizar-ticket' => 'Visualizar ticket guardado', // NUEVA RUTA AGREGADA
            ]
        ]);
    });

    // ========== AUTENTICACIÓN ==========
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/verify', [AuthController::class, 'verify']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    
    // ========== USUARIOS ==========
    Route::apiResource('admin-users', AdminUserController::class);
    
    // ========== EMBARCACIONES - NUEVO SISTEMA ==========
    Route::get('embarcaciones', [EmbarcacionController::class, 'index']);
    Route::post('embarcaciones', [EmbarcacionController::class, 'store']);
    Route::get('embarcaciones/{id}', [EmbarcacionController::class, 'show']);
    Route::put('embarcaciones/{id}', [EmbarcacionController::class, 'update']);
    Route::delete('embarcaciones/{id}', [EmbarcacionController::class, 'destroy']);
    Route::get('embarcaciones/buscar/{nombre}', [EmbarcacionController::class, 'buscarPorNombre']);
    
    // ========== PUERTOS DE EMBARQUE - NUEVO SISTEMA ==========
    Route::get('puertos-embarque', [PuertoEmbarqueController::class, 'index']);
    Route::post('puertos-embarque', [PuertoEmbarqueController::class, 'store']);
    Route::get('puertos-embarque/{id}', [PuertoEmbarqueController::class, 'show']);
    Route::put('puertos-embarque/{id}', [PuertoEmbarqueController::class, 'update']);
    Route::delete('puertos-embarque/{id}', [PuertoEmbarqueController::class, 'destroy']);
    Route::get('puertos-embarque/buscar/{nombre}', [PuertoEmbarqueController::class, 'buscarPorNombre']);
    
    // ========== CLIENTES - MEJORADO PARA AUTO-LLENADO ==========
    // Rutas específicas ANTES de las rutas con parámetros
    Route::get('clientes/buscar', [ClienteController::class, 'buscar']);
    Route::get('clientes/buscar-por-documento/{numero_documento}', [ClienteController::class, 'buscarPorDocumento']);
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

    // ========== PASAJES - ACTUALIZADO CON IMAGEN JPG ==========
    Route::get('pasajes', [PasajeController::class, 'index']);
    Route::post('pasajes', [PasajeController::class, 'store']);
    Route::get('pasajes/{id}', [PasajeController::class, 'show']);
    Route::put('pasajes/{id}', [PasajeController::class, 'update']);
    Route::delete('pasajes/{id}', [PasajeController::class, 'destroy']);
    Route::get('pasajes/codigo/{codigo}', [PasajeController::class, 'getByCode']);
    Route::get('pasajes/{id}/pdf', [PasajeController::class, 'generatePDF']);
    
    // RUTAS PARA GENERACIÓN DE ARCHIVOS
    Route::post('pasajes/generar-pdf-tiempo-real', [PasajeController::class, 'generarPdfTiempoReal']);
    Route::post('pasajes/generar-ticket-venta', [PasajeController::class, 'generarTicketVenta']);
    // NUEVA RUTA PARA GENERAR IMAGEN JPG
     Route::post('/pasajes/generar-imagen-tiempo-real', [PasajeController::class, 'generarImagenTiempoReal']);
    
    // ========== NUEVA RUTA PARA GUARDAR TICKET ==========
    Route::post('pasajes/guardar-ticket', [PasajeController::class, 'guardarTicket']);

    // ========== HISTORIAL DE VENTAS - SISTEMA PRINCIPAL ==========
    
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
    
    // ========== NUEVA RUTA PARA VISUALIZAR TICKET GUARDADO ==========
    Route::get('historial-ventas/{id}/visualizar-ticket', [HistorialVentaController::class, 'visualizarTicket']);
    
    Route::delete('historial-ventas/{id}', [HistorialVentaController::class, 'destroy']);
});

// ========== RUTAS WEB PARA NAVEGACIÓN DIRECTA ==========

// Rutas para acceso directo a PDFs, tickets e imágenes (sin /api/)
Route::prefix('pasajes')->name('pasajes.')->group(function () {
    Route::get('/detalles', function () {
        return view('welcome'); // Vue Router se encargará de la ruta
    })->name('detalles');
    
    Route::get('/pdf/{id}', [PasajeController::class, 'generatePDF'])->name('pdf');
    Route::get('/ticket/{id}', [PasajeController::class, 'generarTicketVenta'])->name('ticket');
    // NUEVA RUTA PARA GENERAR IMAGEN DIRECTA
    Route::post('/imagen', [PasajeController::class, 'generarImagenTiempoReal'])->name('imagen');
});

// ========== NUEVA RUTA WEB PARA GENERAR IMAGEN DIRECTA ==========
Route::post('/generar-imagen-pasaje', [PasajeController::class, 'generarImagenTiempoReal'])->name('generar-imagen-pasaje');

// RUTA TEMPORAL PARA IMAGEN JPG (FUNCIONAL Y DESCARGABLE)
Route::post('/imagen-boleta', function(Request $request) {
    try {
        $datos = $request->all();
        
        $datosBoleta = [
            'empresa' => 'ROCÍO TRAVEL',
            'numero_boleta' => 'BOL-' . time(),
            'tipo_documento' => 'BOLETA DE VENTA',
            'fecha_emision' => date('d/m/Y'),
            'hora_emision' => date('H:i'),
            'operador' => 'ROCÍO TRAVEL',
            'cliente' => $datos['cliente'] ?? ['nombre' => 'Cliente', 'documento' => 'N/A', 'contacto' => '', 'nacionalidad' => 'PERUANA'],
            'descripcion' => $datos['descripcion'] ?? 'Pasaje',
            'cantidad' => $datos['cantidad'] ?? 1,
            'precio_unitario' => $datos['precio_unitario'] ?? 0,
            'subtotal' => $datos['subtotal'] ?? 0,
            'total' => $datos['total'] ?? 0,
            'embarcacion' => $datos['embarcacion'] ?? 'N/A',
            'puerto_embarque' => $datos['puerto_embarque'] ?? 'N/A',
            'hora_embarque' => $datos['hora_embarque'] ?? 'N/A',
            'hora_salida' => $datos['hora_salida'] ?? 'N/A',
            'fecha_viaje' => date('d/m/Y'),
            'destino' => $datos['destino'] ?? '',
            'ruta' => $datos['ruta'] ?? $datos['descripcion'] ?? '',
            'medio_pago' => $datos['pago_mixto'] ? 'PAGO MIXTO' : ($datos['medio_pago'] ?? 'Efectivo'),
            'pago_mixto' => $datos['pago_mixto'] ?? false,
            'detalles_pago' => $datos['detalles_pago'] ?? '',
            'nota' => $datos['nota'] ?? '',
            'politicas' => [
                'La empresa no aceptará devoluciones una vez realizada la venta y separado el cupo;',
                'en caso que la embarcación haya partido y Ud. no abordó,',
                'perderá su derecho a viajar y el valor de su pasaje.'
            ],
            'equipaje' => '15Kg por pasajero',
            'cambio_boleta' => 'ESTE TICKET PUEDE SER CAMBIADO POR BOLETA DE VENTA O FACTURA',
            'fecha_impresion' => date('d/m/Y H:i:s'),
            'direccion_fisica' => 'Dirección: Calle. Pevas N° 366',
            'correo' => 'Correo: travelrocio19@gmail.com',
            'contacto' => 'Contacto: +51901978379',
            'yape' => 'Yape: 989667653',
            'ubicacion' => 'IQUITOS - MAYNAS - LORETO'
        ];
        
        $html = view('images.pasaje-ticket', $datosBoleta)->render();
        
        return response($html, 200)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('X-Generated-Type', 'html-boleta')
            ->header('X-Boleta-Number', $datosBoleta['numero_boleta']);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error generando imagen: ' . $e->getMessage(),
            'debug' => [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]
        ], 500);
    }
});

// AGREGAMOS TAMBIÉN EL IMPORT DE REQUEST
use Illuminate\Http\Request;

// ========== RUTAS WEB PARA EMBARCACIONES Y PUERTOS ==========
Route::prefix('embarcaciones')->name('embarcaciones.')->group(function () {
    Route::get('/', function () {
        return view('welcome'); // Vue Router maneja la interfaz
    })->name('index');
    
    Route::get('/agregar', function () {
        return view('welcome'); // Vue Router maneja el formulario
    })->name('agregar');
});

Route::prefix('puertos-embarque')->name('puertos-embarque.')->group(function () {
    Route::get('/', function () {
        return view('welcome'); // Vue Router maneja la interfaz
    })->name('index');
    
    Route::get('/agregar', function () {
        return view('welcome'); // Vue Router maneja el formulario
    })->name('agregar');
});

// ========== RUTAS WEB PARA HISTORIAL DE VENTAS ==========
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

// NUEVA RUTA PARA AGREGAR/ACTUALIZAR DETALLES
Route::get('/agregar-actualizar-detalles', function () {
    return view('welcome'); // Vue Router maneja esta ruta
})->name('agregar-actualizar-detalles');

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

// TEST AUTO-LLENADO DE CLIENTES
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

// NUEVO TEST EMBARCACIONES
Route::get('/test-embarcaciones', function () {
    try {
        // Verificar si la tabla existe
        if (!\Schema::hasTable('embarcaciones')) {
            return response()->json([
                'success' => false,
                'message' => 'Tabla embarcaciones no existe. Ejecuta: php artisan make:migration create_embarcaciones_table',
                'hint' => 'Migración necesaria para embarcaciones',
                'timestamp' => now()
            ], 500);
        }

        $count = \App\Models\Embarcacion::count();
        $todas = \App\Models\Embarcacion::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Sistema de embarcaciones funcionando correctamente',
            'total_embarcaciones' => $count,
            'embarcaciones' => $todas->pluck('nombre'),
            'endpoints' => [
                'GET /api/embarcaciones' => 'Listar todas',
                'POST /api/embarcaciones' => 'Crear nueva',
                'GET /api/embarcaciones/{id}' => 'Ver específica',
                'PUT /api/embarcaciones/{id}' => 'Actualizar',
                'DELETE /api/embarcaciones/{id}' => 'Eliminar'
            ],
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error en embarcaciones: ' . $e->getMessage(),
            'hint' => 'Necesitas crear el modelo Embarcacion y su migración',
            'timestamp' => now()
        ], 500);
    }
});

// NUEVO TEST PUERTOS DE EMBARQUE
Route::get('/test-puertos', function () {
    try {
        // Verificar si la tabla existe
        if (!\Schema::hasTable('puertos_embarque')) {
            return response()->json([
                'success' => false,
                'message' => 'Tabla puertos_embarque no existe. Ejecuta: php artisan make:migration create_puertos_embarque_table',
                'hint' => 'Migración necesaria para puertos de embarque',
                'timestamp' => now()
            ], 500);
        }

        $count = \App\Models\PuertoEmbarque::count();
        $todos = \App\Models\PuertoEmbarque::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Sistema de puertos de embarque funcionando correctamente',
            'total_puertos' => $count,
            'puertos' => $todos->pluck('nombre'),
            'endpoints' => [
                'GET /api/puertos-embarque' => 'Listar todos',
                'POST /api/puertos-embarque' => 'Crear nuevo',
                'GET /api/puertos-embarque/{id}' => 'Ver específico',
                'PUT /api/puertos-embarque/{id}' => 'Actualizar',
                'DELETE /api/puertos-embarque/{id}' => 'Eliminar'
            ],
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error en puertos: ' . $e->getMessage(),
            'hint' => 'Necesitas crear el modelo PuertoEmbarque y su migración',
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
            'historial_ventas' => \App\Models\HistorialVenta::count(),
            'embarcaciones' => class_exists('\App\Models\Embarcacion') ? \App\Models\Embarcacion::count() : 'Modelo no existe',
            'puertos_embarque' => class_exists('\App\Models\PuertoEmbarque') ? \App\Models\PuertoEmbarque::count() : 'Modelo no existe'
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Sistema funcionando correctamente',
            'counts' => $tests,
            'routes_web' => [
                '/' => 'Página principal',
                '/datos-cliente' => 'Registro de clientes',
                '/agregar-detalles-pasaje' => 'Agregar detalles',
                '/agregar-actualizar-detalles' => 'Agregar/Actualizar embarcaciones y puertos',
                '/detalles-pasaje' => 'Finalizar pasaje',
                '/historial-ventas' => 'Historial de ventas',
                '/embarcaciones' => 'Gestión de embarcaciones',
                '/puertos-embarque' => 'Gestión de puertos'
            ],
            'nuevas_funcionalidades' => [
                'auto_llenado_embarcaciones' => '/api/embarcaciones',
                'auto_llenado_puertos' => '/api/puertos-embarque',
                'generar_imagen_jpg' => '/api/pasajes/generar-imagen-tiempo-real',
                'generar_imagen_directa' => '/generar-imagen-pasaje',
                'guardar_ticket' => '/api/pasajes/guardar-ticket',
                'visualizar_ticket' => '/api/historial-ventas/{id}/visualizar-ticket'
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

// TEST ESPECÍFICO PARA GENERACIÓN DE IMAGEN
Route::get('/test-imagen', function () {
    return response()->json([
        'success' => true,
        'message' => 'Test para generación de imagen JPG',
        'endpoints_imagen' => [
            'POST /api/pasajes/generar-imagen-tiempo-real' => 'Generar imagen desde API',
            'POST /generar-imagen-pasaje' => 'Generar imagen directa',
            'POST /pasajes/imagen' => 'Generar imagen con nombre'
        ],
        'vista_blade' => 'resources/views/images/pasaje-ticket.blade.php',
        'proceso' => [
            '1' => 'Vue.js envía datos a la ruta',
            '2' => 'Controlador procesa datos y renderiza vista blade',
            '3' => 'HTML se convierte a imagen JPG',
            '4' => 'Imagen se devuelve como base64 o blob',
            '5' => 'Vue.js descarga la imagen automáticamente'
        ],
        'datos_necesarios' => [
            'cliente' => 'Información del cliente',
            'pasaje' => 'Detalles del pasaje',
            'embarcacion' => 'Embarcación seleccionada',
            'puerto_embarque' => 'Puerto de embarque',
            'medio_pago' => 'Método de pago'
        ],
        'timestamp' => now()
    ]);
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
                'GET /historial-ventas/{id}/visualizar-ticket' => 'Visualizar ticket guardado',
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