<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Metaetiquetas de Reputación para Antivirus -->
    <meta name="description" content="Plataforma de Gestión y Logística SysSAFE Carnet - Control de Admisión y Entrega de Carnets.">
    <meta name="keywords" content="logistica, seguros, carnet, gestion, cmd, safesure">
    <meta name="author" content="SysSAFE Dev Team">
    <meta name="robots" content="index, follow">

    <!-- OpenGraph (Para redes y filtros de seguridad) -->
    <meta property="og:title" content="{{ config('app.name', 'ARS CMD Dashboard') }}">
    <meta property="og:description" content="Portal oficial de gestión de carnets y logística ARS CMD.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Favicon (Esencial para reputación) -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-web-ss.png') }}">

    <title>{{ config('app.name', 'ARS CMD Dashboard') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @livewireStyles
    
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control {
            border: none !important;
            padding: 12px 16px !important;
            border-radius: 12px !important;
            background-color: #f3f4f5 !important; /* surface-container-low */
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #191c1d !important;
            box-shadow: none !important;
        }
        .ts-wrapper.focus .ts-control {
            ring: 2px solid #00346f !important; /* primary */
        }
        .ts-dropdown {
            border-radius: 16px !important;
            margin-top: 8px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #f3f4f5 !important;
            padding: 8px !important;
        }
        .ts-dropdown .active {
            background-color: #00346f !important;
            color: #ffffff !important;
            border-radius: 8px !important;
        }
    </style>
    
    <!-- Alpine.js (Livewire 3 ya lo incluye, evitamos conflictos) -->
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    
    <!-- Styles & Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @php
        $isGestora = auth()->check() && auth()->user()->isGestora();
        $brandPrimary = $isGestora ? "#01579b" : "#00346f"; 
        $brandSecondary = $isGestora ? "#0288d1" : "#0060ac";
    @endphp

    <style>
        :root {
            --brand-primary: {{ $brandPrimary }};
            --brand-secondary: {{ $brandSecondary }};
        }
    </style>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Glassmorphism */
        .glass-panel { background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .glass-dark { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }

        /* Shadow Depth */
        .shadow-enterprise { box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 10px -2px rgba(0, 0, 0, 0.05); }
        .shadow-modal { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .page-transition { animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        .skeleton { background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%); background-size: 200% 100%; animation: shimmer 2s infinite linear; }

        /* --- Responsive Table Overhaul --- */
        @media (max-width: 1024px) {
            .responsive-table thead {
                display: none;
            }
            .responsive-table tr {
                display: block;
                background: white;
                border: 1px solid #e2e8f0;
                border-radius: 1rem;
                margin-bottom: 1rem;
                padding: 1rem;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .responsive-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 0 !important;
                border: 0 !important;
                text-align: right;
                font-size: 0.875rem;
            }
            .responsive-table td::before {
                content: attr(data-label);
                font-weight: 800;
                text-transform: uppercase;
                font-size: 0.65rem;
                color: #64748b;
                letter-spacing: 0.05em;
                margin-right: 1rem;
                text-align: left;
            }
            .responsive-table td:last-child {
                border-top: 1px solid #f1f5f9 !important;
                margin-top: 0.5rem;
                padding-top: 1rem !important;
            }
            .responsive-table .afiliado-cell {
                text-align: left;
                flex-direction: column;
                align-items: flex-start;
            }
            .responsive-table .afiliado-cell::before {
                margin-bottom: 0.5rem;
            }
        }

        /* Touch-friendly buttons */
        @media (max-width: 768px) {
            button, a.btn, .nav-link {
                min-height: 44px;
                display: flex;
                align-items: center;
            }
            input, select {
                font-size: 16px !important; /* Evita zoom automático en iOS */
                min-height: 48px;
            }
        }

        .hover-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0, 52, 111, 0.15);
        }
    </style>
</head>
    <body class="bg-[#F8FAFC] font-sans text-slate-900 antialiased selection:bg-blue-500/10 selection:text-blue-600" 
      data-success="{{ session('success') }}" 
      data-error="{{ session('error') }}"
      x-data="{ 
          sidebarOpen: false,
          notificationsOpen: false,
          userMenuOpen: false,
          commandPaletteOpen: false,
          slideOverOpen: false,
          slideOverUrl: '',
          slideOverTitle: ''
      }"
      x-init="$watch('commandPaletteOpen', value => { if(value) window.dispatchEvent(new CustomEvent('palette-focus')) })"
      @keydown.window="if ((event.ctrlKey || event.metaKey) && event.key === 'k') { event.preventDefault(); commandPaletteOpen = true; }"
      @open-slideover.window="slideOverUrl = $event.detail.url; slideOverTitle = $event.detail.title; slideOverOpen = true">

    @php /** @var \App\Models\User $user */ $user = Auth::user(); @endphp
    @php
        $activeApp = '';
        if(request()->routeIs('import.*', 'afiliados.cmd', 'afiliados.otros', 'afiliados.salida_inmediata', 'empresas.*')) {
            $activeApp = 'admision';
        } elseif(request()->routeIs('afiliados.index', 'lotes.*', 'cierre.*', 'mensajeros.*', 'rutas.*', 'despachos.*', 'logistica.dashboard')) {
            $activeApp = 'logistica';
        } elseif(request()->routeIs('evidencias.*', 'liquidacion.*', 'pagos.*')) {
            $activeApp = 'gestion';
        } elseif(request()->routeIs('reportes.*')) {
            $activeApp = 'reportes';
        } elseif(request()->routeIs('proveedores.*', 'catalogo.*', 'admin.audit.index', 'usuarios.*', 'admin.sync.*', 'traspasos.*')) {
            $activeApp = 'sistema';
        }
        $isHome = request()->routeIs('dashboard');
    @endphp

    <!-- GLOBAL TOP NAVBAR (Enterprise Level) -->
    <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 z-50 relative shadow-sm w-full">
        <div class="flex items-center gap-4">
            <!-- App Launcher Button (Grid) -->
            <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-colors" title="Centro de Aplicaciones">
                <i class="ph-bold ph-squares-four text-xl"></i>
            </a>
            
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo-web-ss.png') }}" alt="SS" class="h-5 w-auto grayscale contrast-200">
                <span class="text-sm font-display font-black text-slate-800 tracking-tight">Safesure <span class="font-normal text-slate-400">Enterprise</span></span>
            </a>
            
            @if(!$isHome && $activeApp)
            <div class="hidden md:flex items-center gap-2 ml-4 px-3 py-1 bg-slate-50 border border-slate-200 rounded-lg shadow-sm">
                <span class="w-2 h-2 rounded-full {{ $activeApp == 'logistica' ? 'bg-amber-500' : ($activeApp == 'admision' ? 'bg-blue-500' : 'bg-slate-500') }}"></span>
                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">{{ $activeApp }}</span>
            </div>
            @endif
        </div>
        
        <!-- Center: Global Search (Command Palette Trigger) -->
        <div class="hidden md:flex flex-1 max-w-xl mx-4">
            <button @click="commandPaletteOpen = true" class="w-full flex items-center justify-between pl-3 pr-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 hover:border-slate-300 transition-all text-slate-500 group">
                <div class="flex items-center gap-2">
                    <i class="ph-bold ph-magnifying-glass group-hover:text-blue-500 transition-colors"></i>
                    <span class="text-xs font-medium">Buscar expedientes, acciones o empresas...</span>
                </div>
                <div class="flex items-center gap-1">
                    <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[9px] font-bold text-slate-400 bg-white border border-slate-200 rounded">Ctrl</kbd>
                    <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[9px] font-bold text-slate-400 bg-white border border-slate-200 rounded">K</kbd>
                </div>
            </button>
        </div>

        <!-- COMMAND PALETTE OVERLAY -->
        <div x-show="commandPaletteOpen" style="display: none;" 
             class="fixed inset-0 z-[100] overflow-y-auto p-4 sm:p-6 md:p-20" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="commandPaletteOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="commandPaletteOpen = false"></div>

            <!-- Command Palette Panel -->
            <div x-show="commandPaletteOpen" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="mx-auto max-w-2xl transform divide-y divide-slate-100 overflow-hidden rounded-2xl glass-panel shadow-modal ring-1 ring-black/5 transition-all relative">
                
                <div class="relative">
                    <i class="ph-bold ph-magnifying-glass absolute left-4 top-3.5 text-xl text-slate-400"></i>
                    <input type="text" id="command-palette-input" 
                           class="h-14 w-full border-0 bg-transparent pl-11 pr-4 text-slate-900 placeholder:text-slate-400 focus:ring-0 sm:text-sm font-medium" 
                           placeholder="¿Qué necesitas hacer hoy?" role="combobox" aria-expanded="false" aria-controls="options">
                </div>

                <!-- Results -->
                <div id="command-results" class="max-h-96 scroll-py-3 overflow-y-auto p-3 hidden">
                    <h2 class="mb-2 mt-4 px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Acciones Rápidas</h2>
                    <ul class="text-sm text-slate-700" id="command-actions-container">
                        <!-- Dynamic Actions -->
                    </ul>

                    <h2 class="mb-2 mt-4 px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest hidden" id="command-expedientes-title">Expedientes</h2>
                    <ul class="text-sm text-slate-700" id="command-expedientes-container">
                        <!-- Dynamic Expedientes -->
                    </ul>
                </div>

                <!-- Empty State -->
                <div id="command-empty" class="px-6 py-14 text-center text-sm sm:px-14 hidden">
                    <i class="ph-bold ph-ghost text-4xl text-slate-300 mb-4 inline-block"></i>
                    <p class="font-bold text-slate-900">No encontramos resultados</p>
                    <p class="mt-1 text-slate-500">Prueba buscando un nombre, cédula o módulo.</p>
                </div>
                
                <!-- Initial State -->
                <div id="command-initial" class="px-6 py-10 text-center text-sm sm:px-14">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-blue-500 mb-4">
                        <i class="ph-bold ph-command text-2xl"></i>
                    </div>
                    <p class="font-bold text-slate-900">Busca en todo Safesure</p>
                    <p class="mt-1 text-slate-500">Comienza a escribir para ver expedientes o acciones rápidas.</p>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="md:hidden p-1.5 text-slate-500 hover:bg-slate-100 rounded-lg transition-all">
                <i class="ph-bold ph-list text-xl"></i>
            </button>

            <!-- Cloud Status Indicator -->
            <a href="{{ route('admin.sync.index') }}" class="hidden sm:flex items-center gap-2 px-2.5 py-1 hover:bg-slate-50 rounded-md border border-transparent hover:border-slate-200 transition-all" title="Ver estado de sincronización">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Nube OK</span>
            </a>

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-lg relative transition-colors">
                    <i class="ph-bold ph-bell text-lg"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-rose-500 rounded-full border border-white"></span>
                    @endif
                </button>

                <!-- Notifications Dropdown -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" 
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" 
                     class="absolute right-0 mt-2 w-80 origin-top-right rounded-xl glass-panel shadow-modal ring-1 ring-black ring-opacity-5 focus:outline-none z-[120] overflow-hidden" 
                     style="display: none;">
                    
                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-800">Notificaciones</h3>
                        <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ auth()->user()->unreadNotifications->count() }}</span>
                    </div>

                    <div class="max-h-96 overflow-y-auto custom-scrollbar">
                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                <p class="text-sm font-medium text-slate-900">{{ $notification->data['message'] ?? 'Nueva notificación' }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="py-10 px-6 text-center">
                                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                    <i class="ph-bold ph-bell-slash text-2xl"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sin notificaciones</p>
                            </div>
                        @endforelse
                    </div>

                    @if(auth()->user()->unreadNotifications->count() > 0)
                    <div class="p-2 bg-slate-50 border-t border-slate-100">
                        <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                            @csrf
                            <button type="submit" class="w-full py-2 text-center text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-700 transition-colors">
                                Marcar todas como leídas
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <div class="h-4 w-px bg-slate-200 mx-1 hidden sm:block"></div>

            <!-- Profile -->
            <div class="relative hidden sm:block" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="flex items-center gap-2 p-1 hover:bg-slate-50 rounded-lg transition-colors border border-transparent hover:border-slate-200">
                    <img src="{{ $user->avatar_url }}" class="w-6 h-6 rounded border border-slate-200 object-cover" alt="User">
                    <i class="ph-bold ph-caret-down text-slate-400 text-[10px]"></i>
                </button>

                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-50">
                    <div class="p-3 bg-slate-50 border-b border-slate-100">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ $user->name }}</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5 truncate">{{ $user->getRoleNames()->first() ?? 'Usuario' }}</p>
                    </div>
                    <div class="p-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">
                            <i class="ph-bold ph-user-circle text-base"></i> Perfil Personal
                        </a>
                    </div>
                    <div class="p-1 border-t border-slate-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-left">
                                <i class="ph-bold ph-sign-out text-base"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- MOBILE BACKDROP -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition opacity-ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition opacity-ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 md:hidden">
    </div>

    <!-- MAIN WRAPPER (h-screen - navbar height) -->
    <div class="flex h-[calc(100vh-3.5rem)] overflow-hidden relative bg-[#F8FAFC]">
        
        @if(!$isHome && $activeApp)
        <!-- CONTEXTUAL SIDEBAR (ENTERPRISE STYLE) -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col shadow-sm z-50 absolute md:static inset-y-0 left-0 transform transition-transform duration-300"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            
            <!-- Mobile Close -->
            <button @click="sidebarOpen = false" class="md:hidden absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-600 border border-slate-200 rounded-lg bg-white">
                <i class="ph-bold ph-x text-xl"></i>
            </button>

            @if($activeApp == 'admision')
                @include('layouts.sidebars.admision')
            @elseif($activeApp == 'logistica')
                @include('layouts.sidebars.logistica')
            @elseif($activeApp == 'sistema')
                @include('layouts.sidebars.sistema')
            @endif
        </aside>
        @endif

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto custom-scrollbar relative">
            <!-- Page Header (Optional Slot) -->
            @if (isset($header))
                <div class="px-4 md:px-8 py-6 bg-white border-b border-slate-100 shadow-sm z-10 sticky top-0">
                    {{ $header }}
                </div>
            @endif

            <div class="w-full max-w-[1600px] mx-auto p-4 md:p-8 space-y-6">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Enterprise Slide-over (Quick View) -->
    <div x-show="slideOverOpen" class="fixed inset-0 z-[110] overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background overlay -->
            <div x-show="slideOverOpen" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="slideOverOpen = false"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <!-- Slide-over panel -->
                <div x-show="slideOverOpen" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="pointer-events-auto w-screen max-w-2xl">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl border-l border-slate-200">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between sm:px-6">
                            <h2 class="text-base font-bold text-slate-800 uppercase tracking-widest flex items-center gap-2" id="slide-over-title">
                                <i class="ph-bold ph-identification-card text-blue-600 text-xl"></i>
                                <span x-text="slideOverTitle">Detalles del Expediente</span>
                            </h2>
                            <div class="ml-3 flex h-7 items-center">
                                <button type="button" @click="slideOverOpen = false" class="relative rounded-md bg-white text-slate-400 hover:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    <span class="absolute -inset-2.5"></span>
                                    <span class="sr-only">Cerrar panel</span>
                                    <i class="ph-bold ph-x text-xl"></i>
                                </button>
                            </div>
                        </div>
                        <div class="relative flex-1">
                            <!-- Show loading spinner while iframe loads -->
                            <div class="absolute inset-0 flex items-center justify-center bg-white" x-show="slideOverUrl !== ''" id="slideover-loader">
                                <div class="flex flex-col items-center gap-3">
                                    <i class="ph-bold ph-spinner-gap text-4xl text-blue-500 animate-spin"></i>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Cargando expediente...</p>
                                </div>
                            </div>
                            <template x-if="slideOverUrl">
                                <iframe :src="slideOverUrl" class="w-full h-full border-0 absolute inset-0 z-10 bg-white" onload="document.getElementById('slideover-loader').style.display = 'none';"></iframe>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GLOBAL COMMAND PALETTE (Enterprise Search) -->
    <div x-show="commandPaletteOpen" 
         class="fixed inset-0 z-[100] p-4 sm:p-6 md:p-20 overflow-y-auto" 
         role="dialog" aria-modal="true" style="display: none;"
         @keydown.escape.window="commandPaletteOpen = false">
        
        <!-- Backdrop -->
        <div x-show="commandPaletteOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="commandPaletteOpen = false"></div>

        <!-- Palette Panel -->
        <div x-show="commandPaletteOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="mx-auto max-w-2xl transform divide-y divide-slate-100 overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all relative z-10">
            
            <div class="relative">
                <i class="ph-bold ph-magnifying-glass pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-slate-400"></i>
                <input type="text" id="command-palette-input"
                       class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-slate-900 placeholder:text-slate-400 focus:ring-0 sm:text-sm font-medium" 
                       placeholder="Buscar expedientes, empresas o acciones... (Ctrl+K)" 
                       role="combobox" aria-expanded="false" aria-controls="options">
            </div>

            <!-- Initial State / Recommendations -->
            <div id="command-initial" class="p-4">
                <h2 class="mb-2 px-2 text-xs font-black uppercase tracking-widest text-slate-400">Sugerencias rápidas</h2>
                <ul class="text-sm text-slate-700">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-slate-50 group transition-colors">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <i class="ph-bold ph-squares-four text-lg"></i>
                            </div>
                            <span class="font-bold">Ir al App Center</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('afiliados.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-slate-50 group transition-colors">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <i class="ph-bold ph-users text-lg"></i>
                            </div>
                            <span class="font-bold">Ver Afiliados Recientes</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Results State -->
            <div id="command-results" class="hidden max-h-96 overflow-y-auto p-4 custom-scrollbar">
                <div class="mb-4">
                    <h2 class="mb-2 px-2 text-xs font-black uppercase tracking-widest text-slate-400">Acciones</h2>
                    <ul id="command-actions-container" class="space-y-1"></ul>
                </div>
                <div>
                    <h2 id="command-expedientes-title" class="mb-2 px-2 text-xs font-black uppercase tracking-widest text-slate-400">Expedientes encontrados</h2>
                    <ul id="command-expedientes-container" class="space-y-1"></ul>
                </div>
            </div>

            <!-- Empty State -->
            <div id="command-empty" class="hidden px-6 py-14 text-center sm:px-14">
                <i class="ph-bold ph-ghost mx-auto h-12 w-12 text-slate-300"></i>
                <p class="mt-4 text-sm font-bold text-slate-900 uppercase tracking-widest">No se encontraron resultados</p>
                <p class="mt-2 text-xs text-slate-500">Prueba buscando por nombre, cédula o el nombre de una acción.</p>
            </div>

            <!-- Footer Help -->
            <div class="flex items-center justify-between bg-slate-50 px-4 py-2.5 text-[10px] text-slate-500 font-bold uppercase tracking-tighter border-t border-slate-100">
                <div class="flex gap-4">
                    <span><kbd class="font-sans font-black text-slate-400">ESC</kbd> Cerrar</span>
                    <span><kbd class="font-sans font-black text-slate-400">ENTER</kbd> Seleccionar</span>
                </div>
                <span>Safesure Intelligent Search v2</span>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global Toast Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Global Confirmation Interceptor for Forms
        function confirmActionForm(event, title = '¿Estás seguro?', text = 'Esta acción no se puede deshacer.') {
            event.preventDefault();
            const form = event.target;
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#004a99',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function initEnterpriseUI() {
            console.log("Safesure UI Initializing...");
            const successMsg = document.body.dataset.success;
            const errorMsg = document.body.dataset.error;

            if (successMsg && successMsg !== 'null' && successMsg !== '') {
                console.log("Firing Success Toast:", successMsg);
                Toast.fire({ icon: 'success', title: successMsg });
                document.body.dataset.success = ''; 
            }
            if (errorMsg && errorMsg !== 'null' && errorMsg !== '') {
                console.log("Firing Error Toast:", errorMsg);
                Toast.fire({ icon: 'error', title: errorMsg });
                document.body.dataset.error = '';
            }
            
            // Global Slide-over handlers
            window.openQuickView = (url, title) => {
                console.log("Opening Quick View:", url);
                window.dispatchEvent(new CustomEvent('open-slideover', { detail: { url, title } }));
            };

            // --- Command Palette Logic ---
            const cpInput = document.getElementById('command-palette-input');
            const cpResults = document.getElementById('command-results');
            const cpActionsContainer = document.getElementById('command-actions-container');
            const cpExpedientesContainer = document.getElementById('command-expedientes-container');
            const cpExpedientesTitle = document.getElementById('command-expedientes-title');
            const cpEmpty = document.getElementById('command-empty');
            const cpInitial = document.getElementById('command-initial');
            let searchTimeout;

            // Auto-focus input when palette opens via Alpine event
            document.addEventListener('alpine:init', () => {
                // We can't easily use x-effect here from outside, but we can watch for the open event
            });

            // Handle focus via window events that Alpine can trigger or we can listen to
            window.addEventListener('palette-focus', () => {
                setTimeout(() => cpInput && cpInput.focus(), 100);
            });

            if(cpInput && cpInitial && cpResults && cpEmpty) {
                // Ensure we don't attach multiple listeners
                if (!cpInput.dataset.listenerAttached) {
                    cpInput.addEventListener('input', (e) => {
                        const query = e.target.value.trim();
                        clearTimeout(searchTimeout);

                        if (query.length === 0) {
                            cpInitial.classList.remove('hidden');
                            cpResults.classList.add('hidden');
                            cpEmpty.classList.add('hidden');
                            return;
                        }

                        if (query.length < 3) return;

                        searchTimeout = setTimeout(() => {
                            const quickActions = [
                                { nombre: 'Ir al Dashboard (App Center)', url: '{{ route("dashboard") }}', icon: 'squares-four', keywords: ['inicio', 'home', 'dashboard', 'apps'] },
                                { nombre: 'Nueva Empresa', url: '{{ route("empresas.create") }}', icon: 'buildings', keywords: ['nueva', 'crear', 'empresa'] },
                                { nombre: 'Importar Excel', url: '{{ route("import.index") }}', icon: 'file-xls', keywords: ['importar', 'excel', 'subir'] },
                                { nombre: 'Ver Auditoría', url: '{{ route("admin.audit.index") }}', icon: 'clipboard-text', keywords: ['auditoria', 'logs', 'historial'] },
                                { nombre: 'Reporte Supervisión', url: '{{ route("reportes.supervision") }}', icon: 'chart-line-up', keywords: ['reporte', 'supervision', 'graficos'] }
                            ];

                            const filteredActions = quickActions.filter(a => 
                                a.keywords.some(k => k.includes(query.toLowerCase())) || 
                                a.nombre.toLowerCase().includes(query.toLowerCase())
                            );

                            fetch(`{{ route('afiliados.search_ajax') }}?q=${encodeURIComponent(query)}`)
                                .then(res => res.json())
                                .then(data => {
                                    cpInitial.classList.add('hidden');
                                    
                                    if (data.length === 0 && filteredActions.length === 0) {
                                        cpEmpty.classList.remove('hidden');
                                        cpResults.classList.add('hidden');
                                    } else {
                                        cpEmpty.classList.add('hidden');
                                        cpResults.classList.remove('hidden');
                                        
                                        if (cpActionsContainer) {
                                            cpActionsContainer.innerHTML = '';
                                            if (filteredActions.length > 0) {
                                                filteredActions.forEach(action => {
                                                    const li = document.createElement('li');
                                                    li.innerHTML = `
                                                        <a href="${action.url}" class="group flex cursor-default select-none items-center rounded-xl p-3 hover:bg-slate-50 transition-colors">
                                                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-slate-100 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                                                <i class="ph-bold ph-${action.icon} text-lg"></i>
                                                            </div>
                                                            <div class="ml-4 flex-auto">
                                                                <p class="text-sm font-bold text-slate-900">${action.nombre}</p>
                                                            </div>
                                                            <i class="ph-bold ph-caret-right text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                                                        </a>`;
                                                    cpActionsContainer.appendChild(li);
                                                });
                                            }
                                        }

                                        if (cpExpedientesContainer) {
                                            cpExpedientesContainer.innerHTML = '';
                                            if (data.length > 0) {
                                                if (cpExpedientesTitle) cpExpedientesTitle.classList.remove('hidden');
                                                data.forEach(item => {
                                                    const li = document.createElement('li');
                                                    li.innerHTML = `
                                                        <a href="${item.url}" class="group flex cursor-default select-none items-center rounded-xl p-3 hover:bg-slate-50 transition-colors">
                                                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                                                <i class="ph-bold ph-user text-lg"></i>
                                                            </div>
                                                            <div class="ml-4 flex-auto">
                                                                <div class="flex items-center gap-2">
                                                                    <p class="text-sm font-bold text-slate-900">${item.nombre}</p>
                                                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-1.5 py-0.5 text-[9px] font-black uppercase text-blue-700 ring-1 ring-inset ring-blue-700/10">${item.estado}</span>
                                                                </div>
                                                                <p class="text-[10px] font-medium text-slate-500 mt-0.5">Cédula: ${item.cedula} &middot; Póliza: ${item.poliza}</p>
                                                            </div>
                                                        </a>`;
                                                    cpExpedientesContainer.appendChild(li);
                                                });
                                            } else {
                                                if (cpExpedientesTitle) cpExpedientesTitle.classList.add('hidden');
                                            }
                                        }
                                    }
                                })
                                .catch(err => console.error('Search error:', err));
                        }, 300);
                    });
                    cpInput.dataset.listenerAttached = "true";
                }
            }
        }

        document.addEventListener("DOMContentLoaded", initEnterpriseUI);
        document.addEventListener("livewire:navigated", initEnterpriseUI);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    @stack('scripts')
    @livewireScripts
</body>
</html>
