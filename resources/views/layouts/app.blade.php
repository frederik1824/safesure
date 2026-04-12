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
    <link rel="icon" type="image/png" href="{{ asset('images/logo_safe.png') }}">

    <title>{{ config('app.name', 'ARS CMD Dashboard') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
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
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS (CDN for prototype, should be compiled via Vite in prod) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        @php
            $isGestora = auth()->check() && auth()->user()->isGestora();
            $brandPrimary = $isGestora ? "#01579b" : "#00346f"; // Dark Blue for SAFE, Deep Navy for CMD
            $brandSecondary = $isGestora ? "#0288d1" : "#0060ac";
        @endphp
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-container": "#ffa77e",
                        "on-surface": "#191c1d",
                        "surface": "#f8f9fa",
                        "primary": "{{ $brandPrimary }}",
                        "secondary": "{{ $brandSecondary }}",
                        "surface-container-low": "#f3f4f5",
                        "error": "#ba1a1a",
                        "surface-container-high": "#e7e8e9",
                        "outline-variant": "#c2c6d3",
                        "on-surface-variant": "#424751"
                    },
                    fontFamily: {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #191c1d;
        }
        h1, h2, h3, .font-headline {
            font-family: 'Manrope', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e1e3e4;
            border-radius: 10px;
        }

        /* --- Transitions & Animations --- */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .page-transition {
            animation: fadeIn 0.4s ease-out forwards;
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
    <body class="bg-surface text-on-surface" 
          data-success="{{ session('success') }}" 
          data-error="{{ session('error') ?? ($errors->any() ? 'Existen errores de validación en el formulario.' : '') }}">

    <!-- SideNavBar Component -->
    <aside class="h-screen w-80 fixed left-0 top-0 border-r border-white/5 bg-[#0a0f1d] flex flex-col py-8 z-50 shadow-2xl overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 -right-24 w-32 h-64 bg-secondary/10 rounded-full blur-3xl pointer-events-none"></div>
        
        @php /** @var \App\Models\User $user */ $user = Auth::user(); @endphp
        <div class="px-8 mb-10 w-full relative z-10">
            <div class="flex items-center gap-4">
                @if(auth()->user()->isCmd())
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-700 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-500/40 ring-1 ring-white/20">
                        <i class="ph-fill ph-shield-check text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black tracking-tighter text-white leading-none">ARS CMD</h1>
                        <p class="text-[0.65rem] font-bold uppercase tracking-[0.3em] text-blue-400 mt-1">ID Platform</p>
                    </div>
                @else
                    <div class="relative w-full h-14 bg-white/95 backdrop-blur-sm rounded-2xl p-2.5 flex items-center justify-center shadow-lg border border-white/10 group/logo overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-white to-slate-100 opacity-50"></div>
                        <img src="{{ asset('images/logo_safe.png') }}" alt="Seguros Safe Logo" class="h-full w-auto object-contain relative z-10 transition-transform duration-500 group-hover/logo:scale-110">
                    </div>
                @endif
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar" x-data="{ 
            activeGroup: '{{ 
                request()->routeIs('import.*', 'afiliados.cmd', 'afiliados.otros', 'afiliados.salida_inmediata') ? 'admision' : (
                request()->routeIs('afiliados.index', 'lotes.*', 'cierre.*', 'mensajeros.*', 'rutas.*', 'despachos.*') ? 'logistica' : (
                request()->routeIs('evidencias.*', 'liquidacion.*', 'pagos.*') ? 'gestion' : (
                request()->routeIs('reportes.*') ? 'reportes' : (
                request()->routeIs('empresas.*', 'proveedores.*', 'catalogo.*', 'admin.audit.index', 'usuarios.*') ? 'sistema' : ''
            )))) }}' 
        }">
            @php $isGestora = auth()->user()->isGestora(); @endphp
            <!-- Navigation Links -->
            <a class="{{ request()->routeIs('dashboard') ? 'flex items-center gap-4 px-6 py-4 text-white font-black bg-white/10 border-l-[4px] border-primary shadow-2xl rounded-r-2xl transition-all relative overflow-hidden' : 'flex items-center gap-4 px-6 py-4 text-slate-400 hover:text-white hover:bg-white/5 rounded-r-2xl transition-all group/link' }} mb-6 mt-0.5" href="{{ route('dashboard') }}">
                @if(request()->routeIs('dashboard'))
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/20 via-transparent to-transparent"></div>
                @endif
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-slate-800/50 text-slate-500 group-hover/link:bg-slate-700 group-hover/link:text-white' }}">
                    <i class="ph ph-squares-four text-lg"></i>
                </div>
                <span class="text-[0.75rem] tracking-[0.15em] uppercase font-black relative z-10">Dashboard</span>
            </a>

            <!-- ADMISIÓN -->
            <div class="space-y-1">
                <button @click="activeGroup = activeGroup === 'admision' ? '' : 'admision'" 
                        class="w-full flex items-center justify-between px-6 py-4 rounded-2xl transition-all duration-300 group {{ request()->routeIs('import.*', 'afiliados.cmd', 'afiliados.otros', 'afiliados.salida_inmediata') ? 'bg-white/5' : 'hover:bg-white/5' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-800/50 text-slate-500 group-hover:bg-slate-700 group-hover:text-white transition-all shadow-sm" :class="activeGroup === 'admision' ? 'bg-blue-500/20 text-blue-400' : ''">
                            <i class="ph ph-tray text-lg"></i>
                        </div>
                        <span class="text-[0.7rem] tracking-[0.15em] uppercase font-black transition-colors" :class="activeGroup === 'admision' ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'">Admisión</span>
                    </div>
                    <i class="ph ph-caret-down text-xs transition-transform duration-300 text-slate-600" :class="activeGroup === 'admision' ? 'rotate-180 text-white' : ''"></i>
                </button>
                <div x-show="activeGroup === 'admision'" x-collapse class="pl-6 pr-2 space-y-1">
                    @can('manage_affiliates')
                    <x-nav-link route="import.index" icon="ph ph-upload-simple" label="Importar" />
                    @unless($isGestora)
                        <x-nav-link route="afiliados.cmd" icon="ph ph-shield-star" label="Afiliados CMD" />
                    @endunless
                    <x-nav-link route="afiliados.otros" icon="ph ph-buildings" label="{{ $isGestora ? 'Mis Afiliados' : 'Extra Empresa' }}" />
                    <a class="{{ request()->routeIs('afiliados.salida_inmediata') ? 'flex items-center justify-between px-4 py-3 text-white font-black bg-white/10 border-l-[3px] border-primary shadow-inner rounded-r-xl relative overflow-hidden' : 'flex items-center justify-between px-4 py-3 text-slate-400 hover:text-white hover:bg-white/5 rounded-r-xl transition-all group/link' }} mt-0.5" href="{{ route('afiliados.salida_inmediata') }}">
                        <div class="flex items-center gap-3 relative z-10">
                            <i class="ph ph-user-check text-lg {{ request()->routeIs('afiliados.salida_inmediata') ? 'text-primary' : 'group-hover/link:text-white text-slate-500' }}"></i>
                            <span class="text-[0.65rem] tracking-wider uppercase font-extrabold">Salida Inmediata</span>
                        </div>
                        @php
                            $countSalida = \App\Models\Afiliado::whereHas('empresaModel', function($q) {
                                $q->where('es_verificada', true);
                            })->whereNull('fecha_entrega_safesure')->count();
                        @endphp
                        @if($countSalida > 0)
                            <span class="bg-primary text-white text-[0.6rem] font-black px-2 py-0.5 rounded-full shadow-lg shadow-primary/40 relative z-10">{{ $countSalida }}</span>
                        @endif
                    </a>
                    @endcan
                </div>
            </div>

            <!-- LOGÍSTICA & DESPACHO -->
            @canany(['manage_logistics', 'manage_closures'])
            <div class="space-y-1">
                <button @click="activeGroup = activeGroup === 'logistica' ? '' : 'logistica'" 
                        class="w-full flex items-center justify-between px-6 py-4 rounded-2xl transition-all duration-300 group {{ request()->routeIs('afiliados.index', 'lotes.*', 'cierre.*', 'mensajeros.*', 'rutas.*', 'despachos.*') ? 'bg-white/5' : 'hover:bg-white/5' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-800/50 text-slate-500 group-hover:bg-slate-700 group-hover:text-white transition-all" :class="activeGroup === 'logistica' ? 'bg-emerald-500/20 text-emerald-400' : ''">
                            <i class="ph ph-truck text-lg"></i>
                        </div>
                        <span class="text-[0.7rem] tracking-[0.15em] uppercase font-black transition-colors" :class="activeGroup === 'logistica' ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'">Logística</span>
                    </div>
                    <i class="ph ph-caret-down text-xs transition-transform duration-300 text-slate-600" :class="activeGroup === 'logistica' ? 'rotate-180 text-white' : ''"></i>
                </button>
                <div x-show="activeGroup === 'logistica'" x-collapse class="pl-6 pr-2 space-y-1">
                    @can('manage_logistics')
                    <x-nav-link route="logistica.dashboard" icon="ph ph-squares-four" label="Monitor Logístico" />
                    @unless($isGestora)
                        <x-nav-link route="afiliados.index" icon="ph ph-address-book" label="Asignaciones" query="asignacion=pendiente" />
                    @endunless
                    <x-nav-link route="lotes.index" icon="ph ph-check-square-offset" label="Control Lotes" />
                    <x-nav-link route="despachos.index" icon="ph ph-rocket-launch" label="Despachos" />
                    <x-nav-link route="mensajeros.index" icon="ph ph-person-simple-run" label="Mensajeros" />
                    <x-nav-link route="rutas.index" icon="ph ph-map-trifold" label="Gestión Rutas" />
                    @endcan
                    @can('manage_closures')
                    <x-nav-link route="cierre.index" icon="ph ph-folder-lock" label="Cierre Físico" />
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- GESTIÓN & PAGOS -->
            @canany(['manage_evidencias', 'manage_liquidations'])
            <div class="space-y-1">
                <button @click="activeGroup = activeGroup === 'gestion' ? '' : 'gestion'" 
                        class="w-full flex items-center justify-between px-6 py-4 rounded-2xl transition-all duration-300 group {{ request()->routeIs('evidencias.*', 'liquidacion.*', 'pagos.*') ? 'bg-white/5' : 'hover:bg-white/5' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-800/50 text-slate-500 group-hover:bg-slate-700 group-hover:text-white transition-all shadow-sm" :class="activeGroup === 'gestion' ? 'bg-blue-400/20 text-blue-300' : ''">
                            <i class="ph ph-receipt text-lg"></i>
                        </div>
                        <span class="text-[0.7rem] tracking-[0.15em] uppercase font-black transition-colors" :class="activeGroup === 'gestion' ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'">Gestión</span>
                    </div>
                    <i class="ph ph-caret-down text-xs transition-transform duration-300 text-slate-600" :class="activeGroup === 'gestion' ? 'rotate-180 text-white' : ''"></i>
                </button>
                <div x-show="activeGroup === 'gestion'" x-collapse class="pl-6 pr-2 space-y-1">
                    @can('manage_evidencias')
                    <x-nav-link route="evidencias.index" icon="ph ph-files" label="Expedientes" />
                    @endcan
                    @can('manage_liquidations')
                    <x-nav-link route="liquidacion.index" icon="ph ph-money" label="Liquidaciones" />
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- REPORTES -->
            @can('view_reports')
            <div class="space-y-1">
                <button @click="activeGroup = activeGroup === 'reportes' ? '' : 'reportes'" 
                        :class="activeGroup === 'reportes' ? 'text-white' : 'text-slate-500'"
                        class="w-full flex items-center justify-between px-6 py-3 hover:bg-white/5 rounded-xl transition-colors group">
                    <div class="flex items-center gap-4">
                        <i class="ph ph-chart-pie-slice text-[22px] group-hover:text-blue-400 transition-colors"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-white transition-colors">Reportes</span>
                    </div>
                    <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'reportes' ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="activeGroup === 'reportes'" x-collapse class="pl-4 space-y-1">
                    <x-nav-link route="reportes.index" icon="ph ph-chart-line-up" label="Estadísticas" />
                    <x-nav-link route="reportes.supervision" icon="ph ph-eye" label="Supervisión" />
                    <x-nav-link route="reportes.heatmap" icon="ph ph-globe-hemisphere-west" label="Mapa Global" />
                    <x-nav-link route="reportes.sla_alerts" icon="ph ph-bell-ringing" label="Alertas SLA" />
                    <x-nav-link route="reportes.comparativa" icon="ph ph-scales" label="Comparativa" />
                </div>
            </div>
            @endcan

            <!-- SISTEMA -->
            @canany(['manage_companies', 'access_admin_panel', 'manage_users'])
            <div class="space-y-1">
                <button @click="activeGroup = activeGroup === 'sistema' ? '' : 'sistema'" 
                        class="w-full flex items-center justify-between px-6 py-4 rounded-2xl transition-all duration-300 group {{ request()->routeIs('empresas.*', 'proveedores.*', 'catalogo.*', 'admin.audit.index', 'usuarios.*') ? 'bg-white/5' : 'hover:bg-white/5' }}">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-slate-800/50 text-slate-500 group-hover:bg-slate-700 group-hover:text-white transition-all shadow-sm" :class="activeGroup === 'sistema' ? 'bg-slate-500/20 text-slate-300' : ''">
                            <i class="ph ph-gear-six text-lg"></i>
                        </div>
                        <span class="text-[0.7rem] tracking-[0.15em] uppercase font-black transition-colors" :class="activeGroup === 'sistema' ? 'text-white' : 'text-slate-500 group-hover:text-slate-300'">Sistema</span>
                    </div>
                    <i class="ph ph-caret-down text-xs transition-transform duration-300 text-slate-600" :class="activeGroup === 'sistema' ? 'rotate-180 text-white' : ''"></i>
                </button>
                <div x-show="activeGroup === 'sistema'" x-collapse class="pl-6 pr-2 space-y-1">
                    @can('manage_companies')
                    <x-nav-link route="empresas.index" icon="ph ph-briefcase" label="Empresas" />
                    @endcan
                    @can('access_admin_panel')
                    <x-nav-link route="proveedores.index" icon="ph ph-package" label="Proveedores" />
                    <x-nav-link route="catalogo.index" icon="ph ph-tag" label="Catálogos" />
                    <x-nav-link route="admin.sync.index" icon="ph ph-arrows-clockwise" label="Sincronizar Firebase" />
                    <x-nav-link route="admin.audit.index" icon="ph ph-clock-counter-clockwise" label="Auditoría" />
                    @endcan
                    @can('manage_users')
                    <x-nav-link route="usuarios.index" icon="ph ph-users" label="Usuarios & Roles" />
                    @endcan
                </div>
            </div>
            @endcanany
        </nav>

        <div class="px-6 mt-auto pt-6 border-t border-white/5">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 p-3 bg-gradient-to-r from-white/5 to-transparent rounded-2xl hover:from-white/10 border border-white/5 transition-all group relative overflow-hidden mb-4 shadow-sm">
                <div class="relative">
                    <img src="{{ $user->avatar_url }}" class="w-11 h-11 rounded-xl object-cover border-2 border-slate-700/50 shadow-lg relative z-10" alt="Avatar">
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-[#0a0f1d] rounded-full z-20"></div>
                </div>
                <div class="overflow-hidden relative z-10">
                    <p class="text-[0.75rem] font-black text-white truncate">{{ $user->name }}</p>
                    <p class="text-[0.6rem] font-bold text-slate-500 uppercase tracking-widest mt-0.5 truncate">{{ $user->getRoleNames()->first() ?? 'Usuario' }}</p>
                </div>
                <i class="ph ph-caret-right text-slate-600 text-xs ml-auto group-hover:translate-x-1 group-hover:text-white transition-all relative z-10"></i>
            </a>
        </div>
    </aside>

    <!-- Main Canvas -->
    <main class="ml-80 min-h-screen">
        <!-- TopNavBar Component -->
        <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md h-16 px-8 flex justify-between items-center shadow-sm border-b border-slate-100">
            <div class="flex items-center gap-8">
                <div class="relative w-80 group">
                    <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-hover:text-blue-600 transition-colors"></i>
                    <input id="navbar-search" class="w-full bg-slate-50 border border-slate-200 rounded-full pl-11 pr-16 py-2.5 text-[0.875rem] font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:font-medium shadow-inner" placeholder="Buscar afiliado o empresa..." type="text" autocomplete="off"/>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 px-2 py-1 bg-white rounded-md border border-slate-200 shadow-sm pointer-events-none opacity-60 group-hover:opacity-100 transition-opacity">
                        <span class="text-[0.6rem] font-black text-slate-400">CTRL</span>
                        <span class="text-[0.6rem] font-black text-slate-400">K</span>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div id="search-results" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden hidden animate-in fade-in slide-in-from-top-2 duration-200 z-50">
                        <div class="p-3 border-b border-slate-50 bg-slate-50/50">
                            <span class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">Resultados sugeridos</span>
                        </div>
                        <div id="results-container" class="max-h-[400px] overflow-y-auto custom-scrollbar divide-y divide-slate-50">
                            <!-- JS Will fill this -->
                        </div>
                        <div id="search-empty" class="p-8 text-center hidden">
                            <i class="ph ph-user-focus text-slate-200 text-4xl mb-2"></i>
                            <p class="text-xs font-bold text-slate-400">No se encontraron coincidencias.</p>
                        </div>
                    </div>
                </div>
                <nav class="flex gap-6">
                    <a class="{{ request()->routeIs('dashboard') ? 'text-primary font-bold border-b-[3px] border-primary pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-primary border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem] flex items-center gap-2" href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                    @can('manage_affiliates')
                    @unless($isGestora)
                        <a class="{{ request()->routeIs('afiliados.cmd') ? 'text-primary font-bold border-b-[3px] border-primary pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-primary border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem]" href="{{ route('afiliados.cmd') }}">Afiliados CMD</a>
                    @endunless
                    <a class="{{ request()->routeIs('afiliados.otros') ? 'text-primary font-bold border-b-[3px] border-primary pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-primary border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem]" href="{{ route('afiliados.otros') }}">{{ $isGestora ? 'Mis Afiliados' : 'Extra Empresa' }}</a>
                    @endcan
                    @can('manage_companies')
                    <a class="{{ request()->routeIs('empresas.*') ? 'text-primary font-bold border-b-[3px] border-primary pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-primary border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem]" href="{{ route('empresas.index') }}">Empresas</a>
                    @endcan
                    @can('manage_liquidations')
                    <a class="{{ request()->routeIs('liquidacion.*') ? 'text-primary font-bold border-b-[3px] border-primary pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-primary border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem]" href="{{ route('liquidacion.index') }}">Liquidación</a>
                    @endcan
                </nav>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors rounded-full relative group">
                        <i class="ph ph-bell text-[22px] group-hover:scale-110 transition-transform"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-2 right-2 w-4 h-4 bg-red-500 rounded-full border-[2px] border-white text-[0.6rem] text-white flex items-center justify-center font-bold">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown -->
                    <div x-show="open" @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden">
                        
                        <div class="p-4 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                            <h3 class="text-sm font-black text-slate-800">Notificaciones</h3>
                            <span class="text-[0.6rem] font-bold uppercase text-slate-400">{{ $user->unreadNotifications->count() }} Pendientes</span>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @forelse($user->notifications->take(5) as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="block p-4 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0 {{ $notification->read_at ? 'opacity-60' : '' }}">
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                            @php 
                                            $ni = $notification->data['icon'] ?? 'notifications';
                                            $phIcon = 'ph-bell';
                                            if ($ni == 'task_alt') $phIcon = 'ph-check-circle';
                                            elseif ($ni == 'warning') $phIcon = 'ph-warning';
                                            elseif ($ni == 'error') $phIcon = 'ph-x-circle';
                                            @endphp
                                            <i class="ph {{ $phIcon }} text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800 mb-0.5">{{ $notification->data['title'] }}</p>
                                            <p class="text-[0.7rem] text-slate-500 leading-relaxed">{{ $notification->data['message'] }}</p>
                                            <p class="text-[0.6rem] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center flex flex-col items-center">
                                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3 text-slate-300">
                                        <i class="ph ph-bell-slash text-2xl"></i>
                                    </div>
                                    <p class="text-xs text-slate-400 font-medium">No tienes notificaciones por el momento.</p>
                                </div>
                            @endforelse
                        </div>

                        @if($user->unreadNotifications->count() > 0)
                        <div class="p-3 bg-slate-50 text-center border-t border-slate-100">
                            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[0.65rem] font-black uppercase text-blue-600 hover:text-blue-800 tracking-wider">Marcar todas como leídas</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
                
                <!-- User Professional Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 p-1 pr-4 bg-white hover:bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-full shadow-sm hover:shadow transition-all duration-200">
                        <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="User">
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-[0.75rem] font-black text-slate-800 leading-tight">{{ $user->name }}</span>
                            <span class="text-[0.6rem] font-bold text-blue-600 uppercase tracking-tighter">{{ $user->getRoleNames()->first() ?? 'Usuario' }}</span>
                        </div>
                        <i class="ph ph-caret-down text-slate-400 text-sm transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50">
                        
                        <div class="p-4 bg-slate-50/50 border-b border-slate-100">
                            <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-1">Sesión activa</p>
                            <p class="text-[0.8rem] font-bold text-slate-800 truncate">{{ $user->email }}</p>
                        </div>

                        <div class="p-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-colors group">
                                <i class="ph ph-user-circle text-lg opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                <span class="font-bold">Mi Perfil</span>
                            </a>
                        </div>

                        <div class="p-2 border-t border-slate-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm text-slate-600 hover:bg-rose-50 hover:text-rose-600 rounded-xl transition-colors group text-left">
                                    <i class="ph ph-sign-out text-lg opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                    <span class="font-bold">Cerrar Sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Header (Optional Slot) -->
        @if (isset($header))
            <div class="px-8 py-6 bg-white border-b border-slate-100 mb-6">
                {{ $header }}
            </div>
        @endif

        <!-- Page Content -->
        <div class="p-10 max-w-[1600px] mx-auto space-y-10">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global Toast Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
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

        document.addEventListener("DOMContentLoaded", () => {
            const successMsg = document.body.dataset.success;
            const errorMsg = document.body.dataset.error;

            if (successMsg) {
                Toast.fire({ icon: 'success', title: successMsg });
            }
            if (errorMsg) {
                Toast.fire({ icon: 'error', title: errorMsg });
            }

            // --- Smart Search Logic ---
            const searchInput = document.getElementById('navbar-search');
            const searchResults = document.getElementById('search-results');
            const resultsContainer = document.getElementById('results-container');
            const searchEmpty = document.getElementById('search-empty');
            let searchTimeout;

            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                clearTimeout(searchTimeout);

                if (query.length < 3) {
                    searchResults.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    const quickActions = [
                        { nombre: 'Nueva Empresa', url: '{{ route("empresas.create") }}', icon: 'add_business', keywords: ['nueva', 'crear', 'empresa'] },
                        { nombre: 'Importar Excel', url: '{{ route("import.index") }}', icon: 'upload_file', keywords: ['importar', 'excel', 'subir'] },
                        { nombre: 'Ver Auditoría', url: '{{ route("admin.audit.index") }}', icon: 'history', keywords: ['auditoria', 'logs', 'historial'] },
                        { nombre: 'Reporte Supervisión', url: '{{ route("reportes.supervision") }}', icon: 'monitoring', keywords: ['reporte', 'supervision', 'graficos'] }
                    ];

                    const filteredActions = quickActions.filter(a => 
                        a.keywords.some(k => k.includes(query.toLowerCase())) || 
                        a.nombre.toLowerCase().includes(query.toLowerCase())
                    );

                    fetch(`{{ route('afiliados.search_ajax') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            searchResults.classList.remove('hidden');
                            resultsContainer.innerHTML = '';
                            
                            if (data.length === 0 && filteredActions.length === 0) {
                                searchEmpty.classList.remove('hidden');
                                resultsContainer.classList.add('hidden');
                            } else {
                                searchEmpty.classList.add('hidden');
                                resultsContainer.classList.remove('hidden');
                                
                                // Render Actions First
                                if (filteredActions.length > 0) {
                                    const actionHeader = document.createElement('div');
                                    actionHeader.className = 'p-3 bg-slate-50/50 border-b border-slate-50';
                                    actionHeader.innerHTML = '<span class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-400">Acciones Rápidas</span>';
                                    resultsContainer.appendChild(actionHeader);

                                    filteredActions.forEach(action => {
                                        const row = document.createElement('a');
                                        row.href = action.url;
                                        row.className = 'flex items-center gap-4 p-4 hover:bg-primary/5 transition-colors group cursor-pointer border-l-4 border-transparent hover:border-primary';
                                        row.innerHTML = `
                                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:rotate-12 transition-transform">
                                                <span class="material-symbols-outlined text-xl">${action.icon}</span>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-bold text-slate-800">${action.nombre}</p>
                                                <p class="text-[0.65rem] font-medium text-slate-400">Acceso directo al módulo</p>
                                            </div>
                                            <span class="material-symbols-outlined text-slate-300 text-sm group-hover:translate-x-1 transition-transform">chevron_right</span>
                                        `;
                                        resultsContainer.appendChild(row);
                                    });
                                }

                                // Render Affiliates
                                if (data.length > 0) {
                                    const affiliateHeader = document.createElement('div');
                                    affiliateHeader.className = 'p-3 bg-slate-50/50 border-y border-slate-50';
                                    affiliateHeader.innerHTML = `<span class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-400">Expedientes (${data.length})</span>`;
                                    resultsContainer.appendChild(affiliateHeader);

                                    data.forEach(item => {
                                        const row = document.createElement('a');
                                        row.href = item.url;
                                        row.className = 'flex items-center gap-4 p-4 hover:bg-slate-50 transition-colors group cursor-pointer';
                                        row.innerHTML = `
                                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                                <span class="material-symbols-outlined text-xl">person</span>
                                            </div>
                                            <div class="flex-1 overflow-hidden">
                                                <div class="flex justify-between items-center mb-0.5">
                                                    <p class="text-sm font-bold text-slate-800 truncate">${item.nombre}</p>
                                                    <span class="text-[0.6rem] font-black uppercase text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">${item.estado}</span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-[0.7rem] font-medium text-slate-400">ID: ${item.cedula}</span>
                                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                                    <span class="text-[0.7rem] font-medium text-slate-400">Póliza: ${item.poliza}</span>
                                                </div>
                                            </div>
                                        `;
                                        resultsContainer.appendChild(row);
                                    });
                                }
                            }
                        });
                }, 300);
            });

            // --- Command Palette (Ctrl+K) ---
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });

            // Close search when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    @stack('scripts')
    @livewireScripts
</body>
</html>
