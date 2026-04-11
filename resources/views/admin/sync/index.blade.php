@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    Centro de Sincronización <span class="text-blue-600">Firebase</span>
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Gestiona la bidireccionalidad de datos con el ecosistema CMD en tiempo real.
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    <i class="fas fa-satellite-dish mr-2"></i> Firebase-First Architecture
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/30 p-4 border-l-4 border-green-400">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-md bg-red-50 dark:bg-red-900/30 p-4 border-l-4 border-red-400">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Action Cards -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Incremental Sync Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="p-6">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-sync-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Incremental (24h)</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Sincroniza solo los registros creados o modificados en las últimas 24 horas.</p>
                        <form action="{{ route('admin.sync.trigger') }}" method="POST" class="mt-6">
                            @csrf
                            <input type="hidden" name="type" value="incremental">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Ejecutar Ahora
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Verified Sync Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="p-6">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-check-double text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Empresas Verificadas</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Actualiza las empresas marcadas como "Verificadas" para corregir el dashboard.</p>
                        <form action="{{ route('admin.sync.trigger') }}" method="POST" class="mt-6">
                            @csrf
                            <input type="hidden" name="type" value="verified">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                                Sincronizar Reales
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Full Sync Card (Highly Cautionary) -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border-2 border-red-100 dark:border-red-900/30 overflow-hidden group">
                    <div class="p-6">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/50 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-database text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sincronización Total</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-balance">⚠️ Consume alta cuota de Firebase. Usar solo en casos de emergencia o configuración inicial.</p>
                        <form action="{{ route('admin.sync.trigger') }}" method="POST" class="mt-6" onsubmit="return confirm('¿Estás seguro? Esta acción consume gran parte de la cuota diaria de Google Cloud.')">
                            @csrf
                            <input type="hidden" name="type" value="full">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border-2 border-red-600 text-sm font-bold rounded-lg text-red-600 hover:bg-red-600 hover:text-white transition-all duration-300">
                                EXPORTAR TODO
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Log Table -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Historial de Operaciones</h3>
                        <span class="text-xs text-gray-500 uppercase font-semibold">Últimos 20 registros</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50 text-left">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Operación</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Iniciado por</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white uppercase">{{ $log->type }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($log->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                                    Completado
                                                </span>
                                            @elseif($log->status === 'failed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300" title="{{ $log->message }}">
                                                    Fallido
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">
                                                    En Proceso...
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $log->user->name ?? 'Sistema' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500 italic">No hay registros de sincronización recientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos Premium Adicionales */
    .glass-effect {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
</style>
@endsection
