@extends('layouts.app')

@section('content')
<div class="px-8 pb-12 w-full">

    <!-- Cover Background & Header -->
    <div class="relative w-full h-48 rounded-b-3xl bg-gradient-to-r from-blue-900 via-blue-800 to-blue-600 shadow-inner overflow-hidden mb-8">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-20"></div>
        <div class="absolute -bottom-10 left-10 flex items-end">
            <!-- The avatar will be overlapping -->
        </div>
    </div>

    <!-- Main Container Layout -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 max-w-[1400px] mx-auto px-4 md:px-8 mt-[-6rem] relative z-10">
        
        <!-- Profile Sidebar Profile Card -->
        <div class="xl:col-span-3">
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 flex flex-col items-center text-center">
                <div class="relative group cursor-pointer w-32 h-32 rounded-full overflow-hidden shadow-xl border-4 border-white mb-4">
                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                    <!-- Overlay if they want to use the form to change it (it's handled in the form usually, but we can put the form logic here or just display the avatar) -->
                </div>
                <h2 class="text-xl font-black text-slate-800 tracking-tight">{{ $user->name }}</h2>
                <div class="text-sm font-bold text-blue-600 uppercase tracking-widest mt-1 mb-4">
                    {{ $user->getRoleNames()->first() ?? 'Usuario' }}
                </div>
                
                <div class="w-full h-px bg-slate-100 my-4"></div>

                <div class="w-full flex items-center gap-3 text-slate-500 mb-2">
                    <i class="ph-fill ph-envelope-simple text-blue-500"></i>
                    <span class="text-xs font-bold truncate">{{ $user->email }}</span>
                </div>
                @if($user->phone)
                <div class="w-full flex items-center gap-3 text-slate-500 mb-2">
                    <i class="ph-fill ph-phone text-blue-500"></i>
                    <span class="text-xs font-bold">{{ $user->phone }}</span>
                </div>
                @endif
                @if($user->position)
                <div class="w-full flex items-center gap-3 text-slate-500">
                    <i class="ph-fill ph-briefcase text-blue-500"></i>
                    <span class="text-xs font-bold">{{ $user->position }}</span>
                </div>
                @endif
            </div>
            
            <div class="mt-6 bg-slate-50 rounded-3xl p-6 border border-slate-200 shadow-inner">
                <h3 class="text-[0.65rem] uppercase font-black tracking-widest text-slate-400 mb-4">Navegación</h3>
                <nav class="space-y-2">
                    <a href="#info" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white shadow-sm text-blue-600 font-bold text-sm border-l-4 border-blue-600">
                        <i class="ph ph-identification-card text-lg"></i> Información
                    </a>
                    <a href="#security" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-200/50 text-slate-600 font-bold text-sm transition-colors text-slate-500">
                        <i class="ph ph-lock-key text-lg text-slate-400"></i> Seguridad
                    </a>
                </nav>
            </div>
        </div>

        <!-- Configuration Forms -->
        <div class="xl:col-span-9 space-y-8">
            <!-- INFO -->
            <div id="info" class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
                <div class="flex items-center gap-4 mb-8 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center border border-blue-100">
                        <i class="ph ph-user-circle-gear text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800">Información del Perfil</h3>
                        <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Actualiza tus datos y avatar</p>
                    </div>
                </div>
                @include('profile.partials.update-profile-information-form')
            </div>
            
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
                <div class="flex items-center gap-4 mb-8 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center border border-slate-100">
                        <i class="ph ph-shield-check text-2xl text-slate-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800">Accesos y Roles</h3>
                        <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Permisos del Sistema</p>
                    </div>
                </div>
                @include('profile.partials.security-information')
            </div>

            <!-- SECURITY -->
            <div id="security" class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8">
                <div class="flex items-center gap-4 mb-8 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center border border-amber-100">
                        <i class="ph ph-password text-2xl text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800">Actualizar Contraseña</h3>
                        <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Asegura tu cuenta</p>
                    </div>
                </div>
                @include('profile.partials.update-password-form')
            </div>

            <!-- DANGER ZONE -->
            <div class="bg-rose-50 rounded-3xl shadow-sm border border-rose-100 p-8">
                <div class="flex items-center gap-4 mb-8 pb-4 border-b border-rose-200">
                    <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center border border-red-200">
                        <i class="ph ph-warning-circle text-2xl text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-red-800">Zona de Peligro</h3>
                        <p class="text-xs font-bold text-red-400 mt-1 uppercase tracking-widest">Acciones irreversibles</p>
                    </div>
                </div>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
