<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Sucursal_Categoria;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CategoriasController extends Controller
{
    public function categoriaslistar()
    {
        $categorias = Categoria::whereNull('categoria_id')->get();
        $sucursales = Sucursal::all();
        return view('layouts.admin.categorias.listar', compact('categorias', 'sucursales'));
    }

    public function categoriasregistrar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:detallado,no detallado',
            'descripcion' => 'required|string|max:1000',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,ico|max:2048',
            'categoria_id' => 'nullable|exists:categorias,id',
            'sucursales' => 'required|array',
            'sucursales.*' => 'exists:sucursales,id',
        ]);

        $image = $request->file('imagen');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = 'archivos/categorias/' . $imageName;

        $img = Image::make($image)
            ->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });

        $img->save(public_path($imagePath));

        $categoria = Categoria::create([
            'nombre' => $validated['nombre'],
            'tipo' => $validated['tipo'],
            'descripcion' => $validated['descripcion'],
            'imagen' => $imagePath,
            'categoria_id' => $validated['categoria_id'] ?? null,
        ]);

        foreach ($validated['sucursales'] as $sucursalId) {
            Sucursal_Categoria::create([
                'categoria_id' => $categoria->id,
                'sucursal_id' => $sucursalId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categoría registrada exitosamente.',
            'categoria' => $categoria
        ]);
    }

    public function subcategoriasregistrar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:detallado,no detallado',
            'descripcion' => 'required|string|max:1000',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,ico|max:2048',
            'categoria_id' => 'nullable|exists:categorias,id',
            'sucursal' => 'required|exists:sucursales,id',
        ]);

        // Procesar la imagen con Intervention Image
        $image = $request->file('imagen');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = 'archivos/categorias/' . $imageName; // Ruta relativa a public/

        // Redimensionar y optimizar la imagen
        $img = Image::make($image->getRealPath());
        $img->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Guardar la imagen en public/archivos/categorias/
        $img->save(public_path($imagePath));

        // Crear la categoría
        $categoria = Categoria::create([
            'nombre' => $validated['nombre'],
            'tipo' => $validated['tipo'],
            'descripcion' => $validated['descripcion'],
            'imagen' => $imagePath,
            'categoria_id' => $validated['categoria_id'] ?? null,
        ]);

        // Asociar la categoría a la sucursal
        Sucursal_Categoria::create([
            'categoria_id' => $categoria->id,
            'sucursal_id' => $validated['sucursal'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SubCategoría registrada exitosamente.'
        ]);
    }

    public function categoriaseditar(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:categorias,id',
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:detallado,no detallado',
            'descripcion' => 'required|string|max:1000',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:2048',
            'categoria_id' => 'nullable|exists:categorias,id',
            'sucursal' => 'required|exists:sucursales,id',
        ]);

        // Buscar la categoría a editar
        $categoria = Categoria::findOrFail($validated['id']);

        // Procesar la imagen si se sube una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($categoria->imagen && File::exists(public_path($categoria->imagen))) {
                File::delete(public_path($categoria->imagen));
            }

            // Procesar la nueva imagen con Intervention Image
            $image = $request->file('imagen');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'archivos/categorias/' . $imageName;

            // Redimensionar y optimizar la imagen
            $img = Image::make($image->getRealPath());
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            // Guardar la imagen en public/archivos/categorias/
            $img->save(public_path($imagePath));

            // Actualizar la ruta de la imagen en la categoría
            $categoria->imagen = $imagePath;
        }

        // Actualizar los datos de la categoría
        $categoria->nombre = $validated['nombre'];
        $categoria->tipo = $validated['tipo'];
        $categoria->descripcion = $validated['descripcion'];
        $categoria->categoria_id = $validated['categoria_id'] ?? null;
        $categoria->save();

        // Actualizar la relación con la sucursal
        $sucursalCategoria = Sucursal_Categoria::where('categoria_id', $categoria->id)->first();
        if ($sucursalCategoria) {
            $sucursalCategoria->sucursal_id = $validated['sucursal'];
            $sucursalCategoria->save();
        } else {
            Sucursal_Categoria::create([
                'categoria_id' => $categoria->id,
                'sucursal_id' => $validated['sucursal'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente.',
            'categoria' => $categoria
        ]);
    }

    /*******************USUARIO*************** */
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

    
}
