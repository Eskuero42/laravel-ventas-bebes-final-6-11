<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Slider;
use App\Models\Sucursal;
use App\Models\Sucursal_Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrincipalController extends Controller
{
    private function obtenerTodasLasSubcategoriasIds($categoriaId)
    {
        $subcategorias = Categoria::where('categoria_id', $categoriaId)->get();
        $ids = [];

        foreach ($subcategorias as $subcategoria) {
            $ids[] = $subcategoria->id;
            $ids = array_merge($ids, $this->obtenerTodasLasSubcategoriasIds($subcategoria->id));
        }

        return $ids;
    }

    public function principal()
    {
        Log::info('PrincipalController@principal method called', ['sucursal_id' => request('sucursal_id')]);
        try {
            $sucursalesConCategorias = Sucursal::whereHas('sucursal_categorias.categoria', function ($query) {
                $query->whereNull('categoria_id');
            })->with(['sucursal_categorias' => function ($query) {
                $query->whereHas('categoria', function ($q) {
                    $q->whereNull('categoria_id');
                })->with('categoria');
            }])->get();

            $sucursalId = request('sucursal_id');

            $sliders = Slider::where('estado', 'activo')
                ->when($sucursalId, function ($query, $sucursalId) {
                    return $query->where('sucursal_id', $sucursalId);
                })
                ->get();
            $iconos = Slider::where('tipo', 'icono')->get();

            $parentCategoriesQuery = Categoria::whereNull('categoria_id');
            if ($sucursalId) {
                $parentCategoriesQuery->whereHas('sucursales_categorias', function ($q) use ($sucursalId) {
                    $q->where('sucursal_id', $sucursalId);
                });
            }
            $randomParentCategories = $parentCategoriesQuery->inRandomOrder()->take(4)->get();

            foreach ($randomParentCategories as $parent) {
                $directProductIdsQuery = DB::table('sucursales_articulos')
                    ->join('sucursales_categorias', 'sucursales_articulos.sucursales_categorias_id', '=', 'sucursales_categorias.id')
                    ->where('sucursales_categorias.categoria_id', $parent->id)
                    ->when($sucursalId, function ($query) use ($sucursalId) {
                        return $query->where('sucursales_categorias.sucursal_id', $sucursalId);
                    })
                    ->distinct()
                    ->pluck('sucursales_articulos.producto_id');

                $parent->productos_directos = Producto::whereIn('id', $directProductIdsQuery)
                    ->inRandomOrder()
                    ->take(4)
                    ->get();

                $subCategoriesQuery = Categoria::where('categoria_id', $parent->id);
                if ($sucursalId) {
                    $subCategoriesQuery->whereHas('sucursales_categorias', function ($q) use ($sucursalId) {
                        $q->where('sucursal_id', $sucursalId);
                    });
                }
                $parent->subcategorias = $subCategoriesQuery->inRandomOrder()->take(4)->get();

                foreach ($parent->subcategorias as $sub) {
                    $allChildIdsOfSub = array_merge([$sub->id], $this->obtenerTodasLasSubcategoriasIds($sub->id));

                    $productIdsQuery = DB::table('sucursales_articulos')
                        ->join('sucursales_categorias', 'sucursales_articulos.sucursales_categorias_id', '=', 'sucursales_categorias.id')
                        ->whereIn('sucursales_categorias.categoria_id', $allChildIdsOfSub)
                        ->when($sucursalId, function ($query) use ($sucursalId) {
                            return $query->where('sucursales_categorias.sucursal_id', $sucursalId);
                        })
                        ->distinct()
                        ->pluck('sucursales_articulos.producto_id');

                    $sub->productos = Producto::whereIn('id', $productIdsQuery)
                        ->inRandomOrder()
                        ->take(4)
                        ->get();
                }
            }

            return view('layouts.users.principal', compact('sucursalesConCategorias', 'sliders', 'iconos', 'randomParentCategories'));
        } catch (\Exception $e) {
            return abort(404, "Algo salió mal! " . $e->getMessage());
        }
    }

    public function login()
    {
        try {

            return view('layouts.users.login.login');
        } catch (\Exception $e) {
            return abort(404, "Algo salio mal!");
        }
    }

    public function index()
    {
        try {

            return view('layouts.admin.dashboard');
        } catch (\Exception $e) {
            return abort(404, "Algo salio mal!");
        }
    }

    public function productos()
    {
        try {

            return view('layouts.users.productos');
        } catch (\Exception $e) {
            return abort(404, "Algo salio mal!");
        }
    }

    public function u_categoriasListar($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            $sucursalId = request('sucursal_id');

            // obtener todos los IDs de las categorias (actual + subcategorías)
            $idsCategorias = array_merge([(int)$id], $this->obtenerTodasLasSubcategoriasIds($id));

            // obtener productos de las categorias filtradas
            $productoIds = DB::table('sucursales_articulos')
                ->join('sucursales_categorias', 'sucursales_articulos.sucursales_categorias_id', '=', 'sucursales_categorias.id')
                ->whereIn('sucursales_categorias.categoria_id', $idsCategorias)
                ->when($sucursalId, function ($query) use ($sucursalId) {
                    return $query->where('sucursales_categorias.sucursal_id', $sucursalId);
                })
                ->pluck('sucursales_articulos.producto_id')
                ->unique();

            // cargar productos
            $productos = Producto::whereIn('id', $productoIds)->get();

            //obtener colores de los artículos
            $allProductIds = $productos->pluck('id');
            $allArticulos = DB::table('sucursales_articulos')
                ->join('sucursales_categorias', 'sucursales_articulos.sucursales_categorias_id', '=', 'sucursales_categorias.id')
                ->whereIn('sucursales_articulos.producto_id', $allProductIds)
                ->whereIn('sucursales_categorias.categoria_id', $idsCategorias)
                ->when($sucursalId, function ($query) use ($sucursalId) {
                    return $query->where('sucursales_categorias.sucursal_id', $sucursalId);
                })
                ->select('sucursales_articulos.producto_id', 'sucursales_articulos.articulo_id')
                ->get();

            $tipoColorId = DB::table('tipos')->where('nombre', 'Colores')->value('id');
            $articuloColors = collect([]);

            if ($tipoColorId) {
                $allArticuloIdsFlat = $allArticulos->pluck('articulo_id')->unique()->filter();
                if ($allArticuloIdsFlat->isNotEmpty()) {
                    $articuloColors = DB::table('catalogos')
                        ->join('especificaciones', 'catalogos.especificacion_id', '=', 'especificaciones.id')
                        ->where('catalogos.tipo_id', $tipoColorId)
                        ->whereIn('catalogos.articulo_id', $allArticuloIdsFlat)
                        ->select('catalogos.articulo_id', 'especificaciones.descripcion')
                        ->get()
                        ->keyBy('articulo_id');
                }
            }

            foreach ($productos as $producto) {
                $productoArticuloIds = $allArticulos->where('producto_id', $producto->id)->pluck('articulo_id');
                $colores = collect([]);
                if ($productoArticuloIds->isNotEmpty()) {
                    foreach($productoArticuloIds as $articuloId) {
                        if (isset($articuloColors[$articuloId])) {
                            $colores->push($articuloColors[$articuloId]);
                        }
                    }
                }
                $producto->colores_articulo = $colores->unique('descripcion');
            }

            return view('layouts.users.categorias.listar', compact('categoria', 'productos'));
        } catch (\Exception $e) {
            abort(404, "Algo salió mal! " . $e->getMessage());
        }
    }

    public function u_productosver($id)
    {
        $producto = Producto::with('caracteristicas')->findOrFail($id);

        // cargar todos los articulos de sucursal para este producto
        $todos_los_articulos = Sucursal_Articulo::where('producto_id', $id)
            ->whereHas('articulo')
            ->with(['articulo.posiciones', 'articulo.catalogos.especificacion.tipo'])
            ->get();

        if ($todos_los_articulos->isEmpty()) {
            abort(404, 'Este producto no tiene artículos disponibles.');
        }

        $articulo_seleccionado = $todos_los_articulos->first();

        // calcular precio
        $precio_base = $articulo_seleccionado->articulo->precio;
        $precio_final = $precio_base;
        $descuento_aplicado = 0;
        $es_porcentaje = false;

        if ($articulo_seleccionado->descuento_habilitado == 1) {
            if ($articulo_seleccionado->descuento_porcentaje == 1) {
                $descuento_aplicado = ($precio_base * $articulo_seleccionado->descuento) / 100;
                $precio_final = $precio_base - $descuento_aplicado;
                $es_porcentaje = true;
            } else {
                $descuento_aplicado = $articulo_seleccionado->descuento;
                $precio_final = $precio_base - $descuento_aplicado;
            }
        }

        // obtener tallas
        $tipoTallasId = DB::table('tipos')->where('nombre', 'Tallas')->value('id');
        $tallas = collect([]);
        if ($tipoTallasId) {
            $tallas = DB::table('especificaciones')
                ->join('catalogos', 'especificaciones.id', '=', 'catalogos.especificacion_id')
                ->where('especificaciones.tipo_id', $tipoTallasId)
                ->where('catalogos.articulo_id', $articulo_seleccionado->articulo_id)
                ->select('especificaciones.id', 'especificaciones.descripcion')
                ->distinct()->get();
        }

        // obtener capacidades
        $tipoCapacidadesId = DB::table('tipos')->where('nombre', 'Capacidades')->value('id');
        $capacidades = collect([]);
        if ($tipoCapacidadesId) {
            $capacidades = DB::table('especificaciones')
                ->join('catalogos', 'especificaciones.id', '=', 'catalogos.especificacion_id')
                ->where('especificaciones.tipo_id', $tipoCapacidadesId)
                ->where('catalogos.articulo_id', $articulo_seleccionado->articulo_id)
                ->select('especificaciones.id', 'especificaciones.descripcion')
                ->distinct()->get();
        }

        return view('layouts.users.productos.ver', compact(
            'producto',
            'todos_los_articulos',
            'articulo_seleccionado',
            'precio_base',
            'precio_final',
            'descuento_aplicado',
            'es_porcentaje',
            'tallas',
            'capacidades'
        ));
    }
}