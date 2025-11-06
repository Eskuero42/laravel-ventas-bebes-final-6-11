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
                foreach ($request->especificaciones as $tipo_id => $especificacion_ids) {
                    if (is_array($especificacion_ids)) {
                        foreach ($especificacion_ids as $especificacion_id) {
                            if (!empty($especificacion_id)) {
                                Catalogo::create([
                                    'articulo_id' => $articulo->id,
                                    'tipo_id' => $tipo_id,
                                    'especificacion_id' => $especificacion_id,
                                ]);
                            }
                        }
                    } elseif (!empty($especificacion_ids)) {
                        Catalogo::create([
                            'articulo_id' => $articulo->id,
                            'tipo_id' => $tipo_id,
                            'especificacion_id' => $especificacion_ids,
                        ]);
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
            'articulo_id' => 'required|exists:articulos,id',
            'especificaciones' => 'nullable|array',
        ]);

        // 1. Obtener el artículo original
        $articuloOriginal = Articulo::findOrFail($request->articulo_id);

        // 2. Obtener el sucursal_articulo asociado
        $sucursalArticulo = Sucursal_Articulo::where('articulo_id', $articuloOriginal->id)->first();

        if (!$sucursalArticulo) {
            return response()->json(['success' => false, 'message' => 'Sucursal Articulo no encontrado'], 404);
        }

        // 3. Obtener el código base del artículo (ej: VM-001)
        $codigoBase = $sucursalArticulo->codigo; // Ej: "VM-001"

        // 4. Obtener el siguiente número secuencial para el catálogo
        $ultimoCatalogo = Catalogo::where('sucursales_articulos_id', $sucursalArticulo->id)
            ->orderBy('codigo', 'desc')
            ->first();

        if ($ultimoCatalogo) {
            $partes = explode('-', $ultimoCatalogo->codigo);
            $numeroSecuencia = (int) end($partes);
            $siguienteNumero = $numeroSecuencia + 1;
        } else {
            $siguienteNumero = 1;
        }

        $codigoCatalogo = $codigoBase . '-' . str_pad($siguienteNumero, 3, '0', STR_PAD_LEFT); // Ej: VM-001-002

        $catalogosCreados = [];

        // 5. Crear catálogos según especificaciones seleccionadas → ¡todos con el MISMO código!
        if ($request->has('especificaciones')) {
            foreach ($request->especificaciones as $tipo_id => $especificaciones) {
                if (!is_array($especificaciones)) {
                    $especificaciones = [$especificaciones];
                }

                foreach ($especificaciones as $especificacion_id) {
                    if (empty($especificacion_id)) continue;

                    $catalogo = Catalogo::create([
                        'sucursales_articulos_id' => $sucursalArticulo->id,
                        'codigo' => $codigoCatalogo, // ← ¡Mismo código para todos!
                        'principal' => false,
                        'tipo_id' => $tipo_id,
                        'especificacion_id' => $especificacion_id,
                    ]);

                    $catalogosCreados[] = $catalogo;
                }
            }
        }

        // 6. Si no hay especificaciones, crear un catálogo genérico
        if (empty($catalogosCreados)) {
            $catalogo = Catalogo::create([
                'sucursales_articulos_id' => $sucursalArticulo->id,
                'codigo' => $codigoCatalogo, // ← Mismo código
                'principal' => true,
            ]);

            $catalogosCreados[] = $catalogo;
        }

        // 7. Marcar el PRIMER catálogo como principal, los demás como no principales
        if (!empty($catalogosCreados)) {
            $catalogosCreados[0]->update(['principal' => true]);

            for ($i = 1; $i < count($catalogosCreados); $i++) {
                $catalogosCreados[$i]->update(['principal' => false]);
            }

            $catalogoPrincipal = $catalogosCreados[0];
        } else {
            $catalogoPrincipal = null;
        }

        // 8. Guardar imágenes en el catálogo principal (el que tiene principal = 1)
        if ($request->hasFile('imagen') && $catalogoPrincipal) {
            foreach ($request->file('imagen') as $img) {
                $imageName = time() . '_' . uniqid() . '_' . $img->getClientOriginalName();
                $imagePath = 'archivos/articulos/' . $imageName;

                $image = Image::make($img->getRealPath());
                $image->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $image->save(public_path($imagePath));

                Posicion::create([
                    'imagen' => $imagePath,
                    'catalogo_id' => $catalogoPrincipal->id, // ← Correcto: catálogo con principal = 1
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Catálogos duplicados exitosamente.',
            'codigo_catalogo' => $codigoCatalogo,
            'catalogos_creados' => count($catalogosCreados),
        ]);
    }
}
