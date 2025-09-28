<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetallePasaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DetallePasajeController extends Controller
{
    /**
     * Listar todos los detalles de pasajes
     */
    public function index()
    {
        try {
            $detalles = DetallePasaje::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $detalles,
                'message' => 'Detalles de pasajes obtenidos correctamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles de pasajes: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Crear un nuevo detalle de pasaje
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'destino' => 'required|string|max:100',
                'ruta' => 'required|string|max:150',
                'descripcion' => 'required|string|max:200', // NUEVO
                'cantidad' => 'required|integer|min:1',
                'precio_unitario' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0' // NUEVO
            ], [
                'destino.required' => 'El destino es obligatorio',
                'ruta.required' => 'La ruta es obligatoria',
                'descripcion.required' => 'La descripción es obligatoria',
                'cantidad.required' => 'La cantidad es obligatoria',
                'cantidad.min' => 'La cantidad debe ser mayor a 0',
                'precio_unitario.required' => 'El precio unitario es obligatorio',
                'precio_unitario.min' => 'El precio unitario debe ser mayor o igual a 0',
                'subtotal.required' => 'El subtotal es obligatorio'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear el detalle con todos los campos
            $detalle = DetallePasaje::create([
                'destino' => strtoupper($request->destino),
                'ruta' => strtoupper($request->ruta),
                'descripcion' => strtoupper($request->descripcion), // NUEVO
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'subtotal' => $request->subtotal // NUEVO
            ]);

            return response()->json([
                'success' => true,
                'data' => $detalle,
                'message' => 'Detalle de pasaje creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear detalle de pasaje: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Mostrar un detalle de pasaje específico
     */
    public function show($id)
    {
        try {
            $detalle = DetallePasaje::find($id);

            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detalle de pasaje no encontrado',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $detalle,
                'message' => 'Detalle de pasaje encontrado'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalle de pasaje: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Actualizar un detalle de pasaje
     */
    public function update(Request $request, $id)
    {
        try {
            $detalle = DetallePasaje::find($id);

            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detalle de pasaje no encontrado',
                    'data' => null
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'destino' => 'sometimes|string|max:100',
                'ruta' => 'sometimes|string|max:150',
                'descripcion' => 'sometimes|string|max:200', // NUEVO
                'cantidad' => 'sometimes|integer|min:1',
                'precio_unitario' => 'sometimes|numeric|min:0',
                'subtotal' => 'sometimes|numeric|min:0' // NUEVO
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $detalle->update([
                'destino' => strtoupper($request->destino ?? $detalle->destino),
                'ruta' => strtoupper($request->ruta ?? $detalle->ruta),
                'descripcion' => strtoupper($request->descripcion ?? $detalle->descripcion), // NUEVO
                'cantidad' => $request->cantidad ?? $detalle->cantidad,
                'precio_unitario' => $request->precio_unitario ?? $detalle->precio_unitario,
                'subtotal' => $request->subtotal ?? $detalle->subtotal // NUEVO
            ]);

            return response()->json([
                'success' => true,
                'data' => $detalle->fresh(),
                'message' => 'Detalle de pasaje actualizado exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar detalle de pasaje: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Eliminar un detalle de pasaje
     */
    public function destroy($id)
    {
        try {
            $detalle = DetallePasaje::find($id);

            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detalle de pasaje no encontrado',
                    'data' => null
                ], 404);
            }

            $detalle->delete();

            return response()->json([
                'success' => true,
                'message' => 'Detalle de pasaje eliminado exitosamente',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar detalle de pasaje: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}