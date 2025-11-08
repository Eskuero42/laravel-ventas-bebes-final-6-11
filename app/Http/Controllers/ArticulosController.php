<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Articulo;
use App\Models\Sucursal_Articulo;
use App\Models\Posicion;
use App\Models\Catalogo;
use App\Models\Producto;
use Intervention\Image\Facades\Image;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArticulosController extends Controller
{
    public function articulosRegistrar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'producto_id' => 'required|exists:productos,id',
            'sucursales_categorias_id' => 'required|exists:sucursales_categorias,id',
            'stock' => 'required|integer|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha_vencimiento' => 'nullable|date',
            'precio_radio' => 'required|in:nuevo,actual',
            'precio_nuevo' => 'nullable|numeric|min:0',
            'precio_actual' => 'required|numeric|min:0',
            'especificaciones' => 'nullable|array',
            'imagen' => 'nullable|array', // Permitir sin imágenes
            'imagen.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        DB::beginTransaction();

        try {
            // Obtener el producto
            $producto = Producto::findOrFail($request->producto_id);

            // Generar código secuencial más robusto
            $maxSequence = Articulo::where('codigo', 'like', $producto->codigo . '-%')
                ->selectRaw('CAST(SUBSTRING_INDEX(codigo, "-", -1) AS UNSIGNED) as sequence_num')
                ->orderByDesc('sequence_num')
                ->first();

            $nuevoCodigo = ($maxSequence ? $maxSequence->sequence_num : 0) + 1;
            $codigoCompleto = $producto->codigo . '-' . str_pad($nuevoCodigo, 4, '0', STR_PAD_LEFT);

            // Determinar precio según selección
            $precio = $request->precio_radio === 'nuevo'
                ? $request->precio_nuevo
                : $request->precio_actual;

            // Crear el artículo
            $articulo = Articulo::create([
                'codigo' => $codigoCompleto,
                'nombre' => $request->nombre,
                'precio' => $precio,
                'stock' => $request->stock,
            ]);

            // Crear relación Sucursal_Articulo (funcionalidad del primer método)
            Sucursal_Articulo::create([
                'precio' => $precio,
                'stock' => $request->stock,
                'descuento' => $request->descuento ?? 0,
                'descuento_porcentaje' => $request->descuento_porcentaje ?? 0,
                'descuento_habilitado' => ($request->descuento > 0 || $request->descuento_porcentaje > 0),
                'estado' => 'vigente',
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'sucursales_categorias_id' => $request->sucursales_categorias_id,
                'producto_id' => $request->producto_id,
                'articulo_id' => $articulo->id,
            ]);

            // Guardar especificaciones
            if ($request->has('especificaciones')) {
                foreach ($request->especificaciones as $tipo_id => $especificacion_ids) { // $especificacion_ids is now an array
                    if (is_array($especificacion_ids)) { // Ensure it's an array
                        foreach ($especificacion_ids as $especificacion_id) { // Iterate over each selected ID
                            if (!empty($especificacion_id)) {
                                Catalogo::create([
                                    'articulo_id' => $articulo->id,
                                    'tipo_id' => $tipo_id,
                                    'especificacion_id' => $especificacion_id,
                                ]);
                            }
                        }
                    } elseif (!empty($especificacion_ids)) { // Handle single selection case if it somehow occurs
                        Catalogo::create([
                            'articulo_id' => $articulo->id,
                            'tipo_id' => $tipo_id,
                            'especificacion_id' => $especificacion_ids,
                        ]);
                    }
                }
            }

            // Guardar imágenes
            if ($request->hasFile('imagen')) {
                foreach ($request->file('imagen') as $file) {
                    if ($file && $file->isValid()) {
                        $fileName = uniqid('articulo_') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                        $destinationPath = public_path('archivos/articulos');
                        $file->move($destinationPath, $fileName);

                        Posicion::create([
                            'imagen' => 'archivos/articulos/' . $fileName,
                            'articulo_id' => $articulo->id,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Artículo registrado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar artículo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function articuloSucursalRegistrar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'producto_id' => 'required|exists:productos,id',
            'sucursales_categorias_id' => 'required|exists:sucursales_categorias,id',
            'stock' => 'required|integer|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha_vencimiento' => 'nullable|date',
            'precio_radio' => 'required|in:nuevo,actual',
            'precio_nuevo' => 'nullable|numeric|min:0',
            'precio_actual' => 'required|numeric|min:0',
            'especificaciones' => 'nullable|array',
            'imagen' => 'nullable|array',
            'imagen.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        DB::beginTransaction();

        try {
            $producto = Producto::findOrFail($request->producto_id);
            $maxSequence = Articulo::where('codigo', 'like', $producto->codigo . '-%')
                ->selectRaw('CAST(SUBSTRING_INDEX(codigo, "-", -1) AS UNSIGNED) as sequence_num')
                ->orderByDesc('sequence_num')
                ->first();

            $nuevoCodigo = ($maxSequence ? $maxSequence->sequence_num : 0) + 1;
            $codigoCompleto = $producto->codigo . '-' . str_pad($nuevoCodigo, 4, '0', STR_PAD_LEFT);

            $precio = $request->precio_radio === 'nuevo'
                ? $request->precio_nuevo
                : $request->precio_actual;

            $articulo = Articulo::create([
                'codigo' => $codigoCompleto,
                'nombre' => $request->nombre,
                'precio' => $precio,
                'stock' => $request->stock,
            ]);

            Sucursal_Articulo::create([
                'precio' => $precio,
                'stock' => $request->stock,
                'descuento' => $request->descuento ?? 0,
                'descuento_porcentaje' => $request->descuento_porcentaje ?? 0,
                'descuento_habilitado' => ($request->descuento > 0 || $request->descuento_porcentaje > 0),
                'estado' => 'vigente',
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'sucursales_categorias_id' => $request->sucursales_categorias_id,
                'producto_id' => $request->producto_id,
                'articulo_id' => $articulo->id,
            ]);

            if ($request->has('especificaciones')) {
                foreach ($request->especificaciones as $tipo_id => $especificacion_values) {
                    $values = is_array($especificacion_values) ? $especificacion_values : [$especificacion_values];
                    foreach ($values as $especificacion_id) {
                        if (!empty($especificacion_id)) {
                            Catalogo::create([
                                'articulo_id' => $articulo->id,
                                'tipo_id' => $tipo_id,
                                'especificacion_id' => $especificacion_id,
                            ]);
                        }
                    }
                }
            }

            if ($request->hasFile('imagen')) {
                foreach ($request->file('imagen') as $file) {
                    if ($file && $file->isValid()) {
                        $fileName = uniqid('articulo_') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                        $destinationPath = public_path('archivos/articulos');
                        $file->move($destinationPath, $fileName);

                        Posicion::create([
                            'imagen' => 'archivos/articulos/' . $fileName,
                            'articulo_id' => $articulo->id,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Artículo registrado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar artículo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function articuloSucursalDuplicar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'producto_id' => 'required|exists:productos,id',
            'sucursales_categorias_id' => 'required|exists:sucursales_categorias,id',
            'stock' => 'required|integer|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha_vencimiento' => 'nullable|date',
            'precio_radio' => 'required|in:nuevo,actual',
            'precio_nuevo' => 'nullable|numeric|min:0',
            'precio_actual' => 'required|numeric|min:0',
            'especificaciones' => 'nullable|array',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
            'articulo_id' => 'required|exists:articulos,id',
        ]);

        DB::beginTransaction();

        try {
            $producto = Producto::findOrFail($request->producto_id);

            $maxSequence = Articulo::where('codigo', 'like', $producto->codigo . '-%')
                ->selectRaw('CAST(SUBSTRING_INDEX(codigo, "-", -1) AS UNSIGNED) as sequence_num')
                ->orderByDesc('sequence_num')
                ->first();

            $nuevoCodigo = ($maxSequence ? $maxSequence->sequence_num : 0) + 1;
            $codigoCompleto = $producto->codigo . '-' . str_pad($nuevoCodigo, 4, '0', STR_PAD_LEFT);

            $precio = $request->precio_radio === 'nuevo'
                ? $request->precio_nuevo
                : $request->precio_actual;

            $articulo = Articulo::create([
                'codigo' => $codigoCompleto,
                'nombre' => $request->nombre,
                'precio' => $precio,
                'stock' => $request->stock,
            ]);

            Sucursal_Articulo::create([
                'precio' => $precio,
                'stock' => $request->stock,
                'descuento' => $request->descuento ?? 0,
                'descuento_porcentaje' => $request->descuento_porcentaje ?? 0,
                'descuento_habilitado' => ($request->descuento > 0 || $request->descuento_porcentaje > 0),
                'estado' => 'vigente',
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'sucursales_categorias_id' => $request->sucursales_categorias_id,
                'producto_id' => $request->producto_id,
                'articulo_id' => $articulo->id,
            ]);

            if ($request->has('especificaciones')) {
                foreach ($request->especificaciones as $tipo_id => $especificacion_values) {
                    $values = is_array($especificacion_values) ? $especificacion_values : [$especificacion_values];
                    foreach ($values as $especificacion_id) {
                        if (!empty($especificacion_id)) {
                            Catalogo::create([
                                'articulo_id' => $articulo->id,
                                'tipo_id' => $tipo_id,
                                'especificacion_id' => $especificacion_id,
                            ]);
                        }
                    }
                }
            }

            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $file) {
                    if ($file && $file->isValid()) {
                        $fileName = uniqid('articulo_') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                        $destinationPath = public_path('archivos/articulos');
                        $file->move($destinationPath, $fileName);

                        Posicion::create([
                            'imagen' => 'archivos/articulos/' . $fileName,
                            'articulo_id' => $articulo->id,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Artículo duplicado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al duplicar artículo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function eliminarArticulo(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = json_decode($request->ids);

        if (!is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'IDs inválidos.'], 400);
        }

        DB::beginTransaction();
        try {
            $articulos = Articulo::whereIn('id', $ids)->get();

            foreach ($articulos as $articulo) {

                $posiciones = Posicion::where('articulo_id', $articulo->id)->get();
                foreach ($posiciones as $posicion) {
                    if (File::exists(public_path($posicion->imagen))) {
                        File::delete(public_path($posicion->imagen));
                    }
                }

                Posicion::where('articulo_id', $articulo->id)->delete();
                Catalogo::where('articulo_id', $articulo->id)->delete();
                Sucursal_Articulo::where('articulo_id', $articulo->id)->delete();

                $articulo->delete();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Artículos eliminados correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar artículos: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ocurrió un error en el servidor.'], 500);
        }
    }

    public function actualizarArticulo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'articulo_id' => 'required|exists:articulos,id',
            'sucursal_articulo_id' => 'required|exists:sucursales_articulos,id',
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha_vencimiento' => 'nullable|date',
            'nuevas_imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'deleted_images' => 'nullable|json',
            'especificaciones.*.*' => 'nullable|exists:especificaciones,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $articulo = Articulo::findOrFail($request->articulo_id);
            $sucursalArticulo = Sucursal_Articulo::findOrFail($request->sucursal_articulo_id);

            $articulo->nombre = $request->nombre;
            // $articulo->codigo = $request->codigo;
            $articulo->save();

            $sucursalArticulo->precio = $request->precio;
            $sucursalArticulo->stock = $request->stock;
            $sucursalArticulo->descuento = $request->descuento ?? 0;
            $sucursalArticulo->descuento_porcentaje = $request->descuento_porcentaje ?? 0;
            $sucursalArticulo->fecha_vencimiento = $request->fecha_vencimiento;
            $sucursalArticulo->save();

            if ($request->has('deleted_images')) {
                $deletedImageIds = json_decode($request->deleted_images);
                foreach ($deletedImageIds as $imageId) {
                    $posicion = Posicion::find($imageId);
                    if ($posicion && $posicion->articulo_id == $articulo->id) {
                        Storage::disk('public')->delete($posicion->imagen);
                        $posicion->delete();
                    }
                }
            }

            if ($request->hasFile('nuevas_imagenes')) {
                foreach ($request->file('nuevas_imagenes') as $file) {
                    if ($file && $file->isValid()) {
                        $fileName = uniqid('articulo_') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                        $destinationPath = public_path('archivos/articulos');
                        $file->move($destinationPath, $fileName);

                        Posicion::create([
                            'imagen' => 'archivos/articulos/' . $fileName,
                            'articulo_id' => $articulo->id,
                        ]);
                    }
                }
            }

            Catalogo::where('articulo_id', $articulo->id)->delete();

            if ($request->has('especificaciones')) {
                foreach ($request->especificaciones as $tipoId => $especificacionIds) {
                    if (is_array($especificacionIds)) {
                        foreach ($especificacionIds as $especificacionId) {
                            Catalogo::create([
                                'articulo_id' => $articulo->id,
                                'tipo_id' => $tipoId,
                                'especificacion_id' => $especificacionId,
                            ]);
                        }
                    } else {
                        Catalogo::create([
                            'articulo_id' => $articulo->id,
                            'tipo_id' => $tipoId,
                            'especificacion_id' => $especificacionIds,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Artículo actualizado exitosamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar artículo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar el artículo: ' . $e->getMessage()], 500);
        }
    }

    public function ajustarStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sucursal_articulo_id' => 'required|exists:sucursales_articulos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $sucursalArticulo = Sucursal_Articulo::findOrFail($request->sucursal_articulo_id);

            if ($request->action === 'add') {
                $sucursalArticulo->increment('stock', $request->cantidad);
                $message = 'Stock añadido correctamente.';
            } else {
                if ($sucursalArticulo->stock < $request->cantidad) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'No se puede restar más stock del existente.'], 400);
                }
                $sucursalArticulo->decrement('stock', $request->cantidad);
                $message = 'Stock restado correctamente.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al ajustar stock: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al ajustar el stock: ' . $e->getMessage()], 500);
        }
    }
}
