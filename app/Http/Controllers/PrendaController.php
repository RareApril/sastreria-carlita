<?php

namespace App\Http\Controllers;

use App\Models\Prenda;
use App\Models\CategoriaVestimenta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PrendaController extends Controller
{
    public function index()
    {
        $prendas = Prenda::with('categoria')->latest()->get();
        $categorias = CategoriaVestimenta::all();

        return Inertia::render('Admin/Prenda', [
            'prendas' => $prendas,
            'categorias' => $categorias,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_vestimenta_id' => 'required',
            'codigo' => 'required|unique:prendas,codigo',
            'nombre' => 'required',
            'talla' => 'required',
            'color' => 'required',
            'precio_alquiler' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
            'marca' => 'nullable|string',
            'genero' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Guardar imagen si se sube
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $imagen->move(public_path('prendas'), $nombreImagen);
            $data['imagen'] = $nombreImagen;
        }

        Prenda::create($data);

        return redirect()->route('admin.prendas.index')->with('success', 'Prenda creada correctamente');
    }

    public function update(Request $request, $id)
    {
        $prenda = Prenda::findOrFail($id);

        $data = $request->validate([
            'categoria_vestimenta_id' => 'required',
            'codigo' => 'required|unique:prendas,codigo,' . $id,
            'nombre' => 'required',
            'talla' => 'required',
            'color' => 'required',
            'precio_alquiler' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
            'marca' => 'nullable|string',
            'genero' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($prenda->imagen && file_exists(public_path('prendas/' . $prenda->imagen))) {
                unlink(public_path('prendas/' . $prenda->imagen));
            }
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $imagen->move(public_path('prendas'), $nombreImagen);
            $data['imagen'] = $nombreImagen;
        } else {
            unset($data['imagen']);
        }

        $prenda->update($data);

        return redirect()->back()->with('success', 'Prenda actualizada correctamente');
    }

    // MÉTODO PARA ELIMINAR
    public function destroy($id)
    {
        $prenda = Prenda::findOrFail($id);

        // Eliminar imagen físicamente si existe
        if ($prenda->imagen && file_exists(public_path('prendas/' . $prenda->imagen))) {
            unlink(public_path('prendas/' . $prenda->imagen));
        }

        $prenda->delete();

        return redirect()->route('admin.prendas.index')->with('success', 'Prenda eliminada correctamente.');
    }
}
