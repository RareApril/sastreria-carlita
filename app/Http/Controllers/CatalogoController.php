<?php

namespace App\Http\Controllers;

use App\Models\Prenda;
use App\Models\CategoriaVestimenta;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $query = Prenda::with('categoria')->where('stock', '>', 0);

        if ($request->buscar) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                    ->orWhere('descripcion', 'like', '%' . $request->buscar . '%')
                    ->orWhere('marca', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->categoria) $query->where('categoria_vestimenta_id', $request->categoria);
        if ($request->genero && $request->genero !== 'todos') $query->where('genero', $request->genero);
        if ($request->talla) $query->where('talla', $request->talla);
        if ($request->color) $query->where('color', $request->color);
        if ($request->precio_min) $query->where('precio_alquiler', '>=', $request->precio_min);
        if ($request->precio_max) $query->where('precio_alquiler', '<=', $request->precio_max);

        switch ($request->sort) {
            case 'precio_asc': $query->orderBy('precio_alquiler', 'asc'); break;
            case 'precio_desc': $query->orderBy('precio_alquiler', 'desc'); break;
            case 'nombre': $query->orderBy('nombre', 'asc'); break;
            default: $query->orderBy('created_at', 'desc');
        }

        $prendas = $query->paginate(12);

        $categorias = CategoriaVestimenta::all();
        $tallas = Prenda::where('stock', '>', 0)->distinct()->pluck('talla')->filter()->sort()->values();
        $colores = Prenda::where('stock', '>', 0)->distinct()->pluck('color')->filter()->sort()->values();
        $precio_min = Prenda::where('stock', '>', 0)->min('precio_alquiler') ?? 0;
        $precio_max = Prenda::where('stock', '>', 0)->max('precio_alquiler') ?? 100;

        return Inertia::render('Catalogo/Index', [
            'prendas' => $prendas,
            'categorias' => $categorias,
            'tallas' => $tallas,
            'colores' => $colores,
            'precio_min' => $precio_min,
            'precio_max' => $precio_max,
            'filtros' => $request->only(['buscar', 'categoria', 'genero', 'talla', 'color', 'precio_min', 'precio_max', 'sort'])
        ]);
    }
}