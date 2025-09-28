<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    /**
     * Listar todos los clientes
     */
    public function index()
    {
        try {
            $clientes = Cliente::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $clientes,
                'message' => 'Clientes obtenidos correctamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Crear un nuevo cliente
     */
    public function store(Request $request)
    {
        try {
            // Validaciones
            $validator = Validator::make($request->all(), [
                'numero_documento' => 'required|string|max:20|unique:clientes,numero_documento',
                'nombre' => 'required|string|max:255',
                'contacto' => 'nullable|string|max:50',
                'nacionalidad' => 'nullable|string|max:50'
            ], [
                'numero_documento.required' => 'El número de documento es obligatorio',
                'numero_documento.unique' => 'Ya existe un cliente con este número de documento',
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.max' => 'El nombre no puede exceder 255 caracteres'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear el cliente
            $cliente = Cliente::create([
                'numero_documento' => $request->numero_documento,
                'nombre' => strtoupper($request->nombre),
                'contacto' => $request->contacto,
                'nacionalidad' => strtoupper($request->nacionalidad ?? 'PERUANA')
            ]);

            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Cliente creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear cliente: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Mostrar un cliente específico
     */
    public function show($id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => 'Cliente encontrado'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener cliente: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Actualizar un cliente
     */
    public function update(Request $request, $id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado',
                    'data' => null
                ], 404);
            }

            // Validaciones
            $validator = Validator::make($request->all(), [
                'numero_documento' => 'required|string|max:20|unique:clientes,numero_documento,' . $id,
                'nombre' => 'required|string|max:255',
                'contacto' => 'nullable|string|max:50',
                'nacionalidad' => 'nullable|string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar
            $cliente->update([
                'numero_documento' => $request->numero_documento,
                'nombre' => strtoupper($request->nombre),
                'contacto' => $request->contacto,
                'nacionalidad' => strtoupper($request->nacionalidad ?? 'PERUANA')
            ]);

            return response()->json([
                'success' => true,
                'data' => $cliente->fresh(),
                'message' => 'Cliente actualizado exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar cliente: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Eliminar un cliente
     */
    public function destroy($id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado',
                    'data' => null
                ], 404);
            }

            $cliente->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar cliente: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Buscar clientes por nombre o documento
     */
    public function buscar(Request $request)
    {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Ingrese al menos 2 caracteres'
                ], 200);
            }

            $clientes = Cliente::where('nombre', 'LIKE', "%{$query}%")
                             ->orWhere('numero_documento', 'LIKE', "%{$query}%")
                             ->limit(10)
                             ->get();

            return response()->json([
                'success' => true,
                'data' => $clientes,
                'message' => 'Búsqueda realizada correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Buscar cliente por número de documento para auto-llenado
     * Este método es específico para el auto-llenado del formulario Vue
     */
    public function buscarPorDocumento($numero_documento)
    {
        try {
            // Validar que el documento tenga el formato correcto
            if (strlen($numero_documento) !== 8 || !is_numeric($numero_documento)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El número de documento debe tener exactamente 8 dígitos',
                    'data' => null
                ], 400);
            }

            // Buscar cliente por número de documento
            $cliente = Cliente::where('numero_documento', $numero_documento)
                             ->where('activo', true)
                             ->first();

            if ($cliente) {
                // Obtener información adicional para el historial
                $totalCompras = 0;
                $ultimaCompra = null;

                // Si tienes relación con la tabla pasajes, descomenta estas líneas:
                // $totalCompras = $cliente->pasajes()->count();
                // $ultimaCompra = $cliente->pasajes()->latest()->first();

                $clienteData = [
                    'id' => $cliente->id,
                    'numero_documento' => $cliente->numero_documento,
                    'nombre' => $cliente->nombre,
                    'contacto' => $cliente->contacto,
                    'nacionalidad' => $cliente->nacionalidad,
                    'total_compras' => $totalCompras,
                    'ultima_compra' => $ultimaCompra,
                    'created_at' => $cliente->created_at,
                    'updated_at' => $cliente->updated_at
                ];

                return response()->json([
                    'success' => true,
                    'data' => $clienteData,
                    'message' => 'Cliente encontrado correctamente'
                ], 200);
            }

            // Cliente no encontrado
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado',
                'data' => null
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar cliente: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del cliente
     */
    public function estadisticas($id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado',
                    'data' => null
                ], 404);
            }

            $estadisticas = [
                'total_pasajes' => 0,
                'ultimo_viaje' => null,
                'destinos_visitados' => [],
                'monto_total_gastado' => 0
            ];

            // Si tienes relación con pasajes, calcula las estadísticas aquí
            // $estadisticas['total_pasajes'] = $cliente->pasajes()->count();
            // $estadisticas['ultimo_viaje'] = $cliente->pasajes()->latest()->first();
            // etc.

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas obtenidas correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Activar/Desactivar cliente
     */
    public function toggleActivo($id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado',
                    'data' => null
                ], 404);
            }

            $cliente->activo = !$cliente->activo;
            $cliente->save();

            $estado = $cliente->activo ? 'activado' : 'desactivado';

            return response()->json([
                'success' => true,
                'data' => $cliente,
                'message' => "Cliente {$estado} correctamente"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado del cliente: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}