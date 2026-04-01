<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::with(['roles', 'responsable'])->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $responsables = \App\Models\Responsable::all();
        return view('users.create', compact('roles', 'responsables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_name' => 'required|exists:roles,name',
            'responsable_id' => 'nullable|exists:responsables,id',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'responsable_id' => $request->responsable_id,
        ]);

        $user->assignRole($request->role_name);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(\App\Models\User $usuario)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $responsables = \App\Models\Responsable::all();
        return view('users.edit', compact('usuario', 'roles', 'responsables'));
    }

    public function update(Request $request, \App\Models\User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$usuario->id,
            'role_name' => 'required|exists:roles,name',
            'responsable_id' => 'nullable|exists:responsables,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'responsable_id' => $request->responsable_id,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'confirmed|min:8']);
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $usuario->update($data);
        $usuario->syncRoles([$request->role_name]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(\App\Models\User $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }
}
