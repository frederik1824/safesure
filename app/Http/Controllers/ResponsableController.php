<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use Illuminate\Http\Request;

class ResponsableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $responsables = Responsable::with('user')->orderBy('id', 'desc')->paginate(10);
        return view('responsables.index', compact('responsables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('responsables.form', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_entrega' => 'nullable|numeric|min:0',
            'user_id' => 'nullable|exists:users,id',
        ]);
        $validated['activo'] = $request->has('activo');

        Responsable::create($validated);
        return redirect()->route('responsables.index')->with('success', 'Responsable creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $responsable = Responsable::findOrFail($id);
        return view('responsables.show', compact('responsable'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $responsable = Responsable::findOrFail($id);
        $users = \App\Models\User::orderBy('name')->get();
        return view('responsables.form', compact('responsable', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $responsable = Responsable::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_entrega' => 'nullable|numeric|min:0',
            'user_id' => 'nullable|exists:users,id',
        ]);
        $validated['activo'] = $request->has('activo');

        $responsable->update($validated);
        return redirect()->route('responsables.index')->with('success', 'Responsable actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $responsable = Responsable::findOrFail($id);
        $responsable->delete();
        return redirect()->route('responsables.index')->with('success', 'Responsable eliminado exitosamente.');
    }
}
