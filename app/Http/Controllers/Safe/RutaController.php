<?php

namespace App\Http\Controllers\Safe;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Models\Mensajero;
use App\Models\Afiliado;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    public function index()
    {
        $rutas = Ruta::with('mensajero')->withCount('afiliados')->latest()->paginate(10);
        return view('safe.rutas.index', compact('rutas'));
    }

    public function create()
    {
        $mensajeros = Mensajero::where('estado', 'Activo')->get();
        
        // Afiliados que aún NO están en ninguna ruta abierta
        $afiliados_libres = Afiliado::whereDoesntHave('rutas', function($q) {
            $q->where('estado', '!=', 'Cerrada');
        })->get();

        return view('safe.rutas.create', compact('mensajeros', 'afiliados_libres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mensajero_id' => 'required|exists:mensajeros,id',
            'nombre_ruta' => 'required|string',
            'fecha_programada' => 'required|date',
            'afiliados' => 'required|array|min:1',
        ]);

        $ruta = Ruta::create($request->only('mensajero_id', 'nombre_ruta', 'fecha_programada', 'notas'));
        
        // Asignar afiliados a la ruta con orden
        foreach ($request->afiliados as $index => $afiliado_id) {
            $ruta->afiliados()->attach($afiliado_id, ['orden_entrega' => $index + 1]);
        }

        return redirect()->route('safe.rutas.index')->with('success', 'Ruta creada y despachada correctamente.');
    }

    public function show(Ruta $ruta)
    {
        $ruta->load('afiliados', 'mensajero');
        return view('safe.rutas.show', compact('ruta'));
    }

    public function updateProgress(Request $request, Ruta $ruta, Afiliado $afiliado)
    {
        // Registro de entrega por el mensajero
        $ruta->afiliados()->updateExistingPivot($afiliado->id, [
            'entregado' => true,
            'fecha_entrega_real' => now(),
            'observacion_entrega' => $request->notas
        ]);

        return back()->with('success', 'Estado de entrega actualizado.');
    }
}
