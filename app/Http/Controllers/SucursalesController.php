<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Categoria;
use App\Models\Sucursal;
use App\Models\Sucursal_Articulo;
use App\Models\Tipo;
use App\Models\Sucursal_Categoria;
use Illuminate\Http\Request;

class SucursalesController extends Controller
{
    public function sucursales_listar()
    {
        $sucursales = Sucursal::all();

        // Retornar una vista con las sucursales
        return view('layouts.admin.sucursales.listar', compact('sucursales'));
    }

    public function sucursales_ver($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        // Obtén TODAS las categorías asociadas (sin filtrar por padre/hijo)
        $categoriasAsociadas = $sucursal->categorias()->pluck('categorias.id')->toArray();
        $categoriasPadre = Categoria::whereNull('categoria_id')
            ->with(['categorias_hijosRecursive' => function ($query) {
                $query->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();
        return view('layouts.admin.sucursales.ver', compact('sucursal', 'categoriasPadre', 'categoriasAsociadas'));
    }

    public function sucursales_productos($id)
    {
        // Productos (solo se usan si ya no hay subcategorías)
        $sucursales_productos = Sucursal_Articulo::with([
            'sucursales_categorias.sucursal',
            'sucursales_categorias.categoria',
            'producto'
        ])
            ->where('sucursales_categorias_id', $id)
            ->whereNull('articulo_id')
            ->get();

        $sucursalCategoria = Sucursal_Categoria::with(['categoria', 'sucursal'])->find($id);
        $categoriaPadre = $sucursalCategoria?->categoria;
        $sucursal = $sucursalCategoria?->sucursal;

        $subcategorias = collect();

        if ($categoriaPadre && $sucursal) {
            // Get the IDs of the direct child categories
            $childCategoryIds = Categoria::where('categoria_id', $categoriaPadre->id)->pluck('id');

            // Get the Sucursal_Categoria records for those children in the current sucursal
            $subcategorias = Sucursal_Categoria::with('categoria')
                ->where('sucursal_id', $sucursal->id)
                ->whereIn('categoria_id', $childCategoryIds)
                ->get();
        }

        $rutaNavegacion = [];
        if ($categoriaPadre) {
            $referenciaActual = $categoriaPadre;
            while ($referenciaActual) {
                array_unshift($rutaNavegacion, $referenciaActual);
                $referenciaActual = $referenciaActual->categoria_padre;
            }
        }

        $tipos = Tipo::get();

        // No es necesario el if/else, la vista puede manejar la lógica
        return view('layouts.admin.sucursales.productos.ver', compact(
            'sucursales_productos',
            'id',
            'tipos',
            'subcategorias',
            'sucursalCategoria',
            'categoriaPadre',
            'rutaNavegacion'
        ));
    }

    public function sucursales_registrar(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'latitud' => 'required|numeric',
                'longitud' => 'required|numeric',
                'horarios' => 'required|array',
                'horarios.*.hora_inicio' => 'nullable|date_format:H:i',
                'horarios.*.hora_fin' => 'nullable|date_format:H:i',
                'horarios.*.cerrado' => 'nullable|boolean',
            ]);

            // Crear la sucursal
            $sucursal = Sucursal::create([
                'nombre' => $request->nombre,
                'direccion' => $request->direccion,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
            ]);

            // Crear los horarios para cada día
            foreach ($request->horarios as $dia => $horario) {
                $cerrado = isset($horario['cerrado']) && $horario['cerrado'] == '1';

                $sucursal->horarios()->create([
                    'dia_semana' => $dia,
                    'hora_inicio' => $cerrado ? null : $horario['hora_inicio'],
                    'hora_fin' => $cerrado ? null : $horario['hora_fin'],
                    'cerrado' => $cerrado,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sucursal y horarios registrados exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sucursales_editar(Request $request)
    {
        try {
            $sucursal = Sucursal::findOrFail($request->id);
            $sucursal->update([
                'nombre' => $request->nombre,
                'direccion' => $request->direccion,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
            ]);

            // Actualizar horarios si los envió
            if ($request->has('horarios')) {
                foreach ($request->horarios as $dia => $horario) {
                    $cerrado = isset($horario['cerrado']) && $horario['cerrado'] == '1';
                    $sucursal->horarios()->updateOrCreate(
                        ['dia_semana' => $dia],
                        [
                            'hora_inicio' => $cerrado ? null : $horario['hora_inicio'],
                            'hora_fin' => $cerrado ? null : $horario['hora_fin'],
                            'cerrado' => $cerrado,
                        ]
                    );
                }
            }

            return response()->json(['success' => true, 'message' => 'Sucursal actualizada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function editarHorarios(Request $request)
    {
        $sucursal = Sucursal::find($request->id);

        if (!$sucursal) {
            return response()->json([
                'success' => false,
                'message' => 'Sucursal no encontrada.'
            ]);
        }

        $horariosInput = $request->input('horarios', []);

        foreach ($horariosInput as $dia => $h) {
            $cerrado = isset($h['cerrado']) && $h['cerrado'] == '1';

            $sucursal->horarios()->updateOrCreate(
                ['dia_semana' => $dia], // Condición para buscar
                [
                    'hora_inicio' => $cerrado ? null : $h['hora_inicio'] ?? null,
                    'hora_fin' => $cerrado ? null : $h['hora_fin'] ?? null,
                    'cerrado' => $cerrado,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Horarios actualizados correctamente.'
        ]);
    }

    public function sucursales_eliminar(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'id' => 'required|integer|exists:sucursales,id',
        ]);

        // Buscar la sucursal por ID
        $sucursal = Sucursal::findOrFail($request->id);

        // Eliminar la sucursal
        $sucursal->delete();

        // Retornar una respuesta JSON
        return response()->json([
            'success' => true,
            'message' => 'Sucursal eliminada exitosamente.',
        ]);
    }

    public function sucursales_productos_articulos($id)
    {
        $sucursal_articulo = Sucursal_Articulo::with('articulo')->findOrFail($id);

        $sucursal_articulos = Sucursal_Articulo::with([
            'articulo.posiciones',
            'articulo.catalogos.tipo',
            'articulo.catalogos.especificacion',
            'sucursales_categorias.categoria'
        ])
            ->where('sucursales_categorias_id', $sucursal_articulo->sucursales_categorias_id)
            ->where('producto_id', $sucursal_articulo->producto_id)
            ->whereNotNull('articulo_id')
            ->get();

        $agrupados = $sucursal_articulos->groupBy(function ($item) {
            // Corregido: Agrupar por el código del artículo, no del sucursal_articulo
            return $item->articulo->codigo . '|' . $item->sucursales_categorias_id . '|' . $item->producto_id;
        });

        $sucursal_articulos_agrupados = $agrupados->map(function ($grupo) {
            $primerRegistro = $grupo->first();
            return [
                'codigo' => $primerRegistro->articulo->codigo, // Corregido: Usar el código del artículo
                'sucursales_categorias_id' => $primerRegistro->sucursales_categorias_id,
                'producto_id' => $primerRegistro->producto_id,
                'articulos' => $grupo->map(function ($sucursal_articulo) {
                    return [
                        'id' => $sucursal_articulo->id,
                        'precio' => $sucursal_articulo->precio,
                        'stock' => $sucursal_articulo->stock,
                        'descuento_habilitado' => $sucursal_articulo->descuento_habilitado,
                        'descuento_porcentaje' => $sucursal_articulo->descuento_porcentaje,
                        'estado' => $sucursal_articulo->estado,
                        'fecha_vencimiento' => $sucursal_articulo->fecha_vencimiento,
                        'articulo' => $sucursal_articulo->articulo,
                        // Corregido: Pasar la colección completa de imágenes
                        'imagenes' => $sucursal_articulo->articulo->posiciones,
                        'catalogos' => $sucursal_articulo->articulo->catalogos,
                        'categoria_tipo' => $sucursal_articulo->sucursales_categorias->categoria->tipo,
                    ];
                })->values(),
            ];
        })->values();

        // Solo pasa los tipos del producto (sin filtrar)
        $productoTipos = $sucursal_articulo->producto->producto_tipos->load('tipo.especificaciones');

        return view('layouts.admin.sucursales.productos.listar', compact(
            'sucursal_articulos_agrupados',
            'sucursal_articulo',
            'productoTipos' // sin "Disponibles"
        ));
    }

    public function sucursales_productos_articulos_ver($id)
    {
        $sucursal_articulo = Sucursal_Articulo::where('id', $id)->first();
        return view('layouts.admin.sucursales.productos.articulo', compact('sucursal_articulo'));
    }

    public function asociarCategorias(Request $request, $sucursalId)
    {
        $sucursal = Sucursal::findOrFail($sucursalId);
        $categoriasSeleccionadas = $request->input('categorias', []);

        // Validar que todas las subcategorías seleccionadas tengan su padre seleccionado
        foreach ($categoriasSeleccionadas as $categoriaId) {
            $categoria = Categoria::find($categoriaId);
            if ($categoria->categoria_id && !in_array($categoria->categoria_id, $categoriasSeleccionadas)) {
                return response()->json([
                    'success' => false,
                    'message' => "Debes seleccionar también la categoría padre: {$categoria->categoria_padre->nombre}."
                ], 422);
            }
        }

        $sucursal->categorias()->sync($categoriasSeleccionadas);
        return response()->json([
            'success' => true,
            'message' => 'Categorías asociadas correctamente'
        ]);
    }
}
