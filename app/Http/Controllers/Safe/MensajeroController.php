<?php

namespace App\Http\Controllers\Safe;

use App\Http\Controllers\Controller;
use App\Models\Mensajero;
use App\Models\User;
use Illuminate\Http\Request;

class MensajeroController extends Controller
{
    public function index()
    {
        $mensajeros = Mensajero::with('user')->get();
        return view('safe.mensajeros.index', compact('mensajeros'));
    }

    public function create()
    {
        $users = User::all();
        return view('safe.mensajeros.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'telefono' => 'nullable|string',
            'vehiculo_placa' => 'nullable|string',
        ]);

        Mensajero::create($request->all());

        return redirect()->route('safe.mensajeros.index')->with('success', 'Mensajero registrado.');
    }

    public function edit($id)
    {
        $mensajero = Mensajero::findOrFail($id);
        $users = User::all();
        return view('safe.mensajeros.edit', compact('mensajero', 'users'));
    }

    public function update(Request $request, $id)
    {
        $mensajero = Mensajero::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'telefono' => 'nullable|string',
            'vehiculo_placa' => 'nullable|string',
        ]);

        $mensajero->update($request->all());

        return redirect()->route('safe.mensajeros.index')->with('success', 'Mensajero actualizado.');
    }

    public function destroy($id)
    {
        $mensajero = Mensajero::findOrFail($id);
        $mensajero->delete();
        return redirect()->route('safe.mensajeros.index')->with('success', 'Mensajero eliminado.');
    }
}
