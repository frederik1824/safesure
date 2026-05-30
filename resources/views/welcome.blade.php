<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>DISCAN.cloud | Plataforma de Logística Avanzada y Gestión de Expedientes</title>
    <meta name="description" content="Portal empresarial unificado de logística avanzada, sincronización en la nube (Nexus Engine) y control de expedientes físicos para ARS CMD y SAFESURE.">
    <meta name="keywords" content="logistica, seguros, carnet, cmd, safesure, firebase, discan">
    <meta name="author" content="Discan Cloud Team">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Styles & Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #030712; /* Extreme dark */
            color: #f3f4f6;
        }
        h1, h2, h3, h4, .font-display {
            font-family: 'Outfit', sans-serif;
        }
        .text-glow {
            text-shadow: 0 0 40px rgba(79, 70, 229, 0.4);
        }
        .glass-header {
            background: rgba(3, 7, 18, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.03);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-6px);
            border-color: rgba(99, 102, 241, 0.25);
            background: rgba(17, 24, 39, 0.8);
            box-shadow: 0 20px 40px -15px rgba(99, 102, 241, 0.15);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        }
        .glow-sphere-1 {
            background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
        }
        .glow-sphere-2 {
            background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, rgba(0, 0, 0, 0) 70%);
        }
    </style>
</head>
<body class="antialiased selection:bg-indigo-500/30 selection:text-indigo-200 overflow-x-hidden min-h-screen flex flex-col justify-between">

    <!-- Decorative Glow Spheres -->
    <div class="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] glow-sphere-1 pointer-events-none rounded-full blur-3xl"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[700px] h-[700px] glow-sphere-2 pointer-events-none rounded-full blur-3xl"></div>

    <!-- Navigation Header -->
    <header class="fixed top-0 left-0 right-0 z-50 glass-header">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl gradient-bg flex items-center justify-center text-white shadow-lg shadow-indigo-600/30">
                    <i class="ph-bold ph-squares-four text-xl"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-black tracking-tight text-white leading-none">DISCAN<span class="text-indigo-400">.cloud</span></span>
                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-400 mt-1 leading-none">Enterprise Ecosystem</span>
                </div>
            </a>

            <!-- Right-side Auth Buttons -->
            <div>
                @if (Route::has('login'))
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-600/20 transition-all">
                                Ir al Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-300 hover:text-white uppercase tracking-wider transition-all">
                                Iniciar Sesión
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                    Registrarse
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Hero Section -->
    <main class="flex-1 flex items-center pt-32 pb-20 px-6 relative z-10">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center w-full">
            
            <!-- Left Info Area -->
            <div class="lg:col-span-7 space-y-8 text-left">
                <!-- Premium Dynamic Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-xs font-black uppercase tracking-widest text-indigo-400">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                    </span>
                    Nexus Logistics Engine v2.4 Active
                </div>

                <div class="space-y-4">
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight text-white leading-tight text-glow">
                        Control Logístico Inteligente para Procesos Críticos
                    </h1>
                    <p class="text-base md:text-lg text-slate-400 font-medium max-w-xl">
                        El ecosistema empresarial unificado de <span class="text-white font-bold">DISCAN</span>. Diseñado para la administración de expedientes, traspasos automatizados, geolocalización de carteras y despacho de soportes físicos para <span class="text-indigo-400 font-bold">ARS CMD</span> y <span class="text-cyan-400 font-bold">SAFESURE</span>.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-sm font-bold uppercase tracking-widest shadow-xl shadow-indigo-600/30 transition-all flex items-center gap-2.5">
                            <span>Ingresar al Portal</span>
                            <i class="ph-bold ph-arrow-right text-base"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-sm font-bold uppercase tracking-widest shadow-xl shadow-indigo-600/30 transition-all flex items-center gap-2.5">
                            <span>Ingresar al Portal</span>
                            <i class="ph-bold ph-arrow-right text-base"></i>
                        </a>
                    @endauth
                    <a href="#apps" class="px-8 py-4 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 rounded-2xl text-sm font-bold uppercase tracking-widest transition-all">
                        Explorar Aplicaciones
                    </a>
                </div>
            </div>

            <!-- Right Visual Image Mockup / Connected Stats -->
            <div class="lg:col-span-5 relative">
                <div class="bg-gradient-to-tr from-indigo-500/10 to-cyan-500/10 p-8 rounded-[2.5rem] border border-white/5 relative overflow-hidden">
                    
                    <!-- Decorative blur -->
                    <div class="absolute -top-10 -left-10 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl"></div>

                    <!-- Mini status dashboard panel -->
                    <div class="space-y-6 relative z-10">
                        <div class="flex items-center justify-between border-b border-white/5 pb-4">
                            <span class="text-xs font-black uppercase text-indigo-400 tracking-wider">Centro de Sincronización</span>
                            <span class="text-[10px] font-mono text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded border border-emerald-400/15">Sincronizado</span>
                        </div>

                        <!-- Real-time simulation metrics -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-900/50 p-4 rounded-2xl border border-white/5">
                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block">Eficiencia SLA</span>
                                <span class="text-2xl font-black text-white block mt-1 font-mono">98.4%</span>
                            </div>
                            <div class="bg-slate-900/50 p-4 rounded-2xl border border-white/5">
                                <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block">Traspasos Hoy</span>
                                <span class="text-2xl font-black text-cyan-400 block mt-1 font-mono">+1,248</span>
                            </div>
                        </div>

                        <!-- Logistics Status Bar -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                <span>Progreso de Distribución</span>
                                <span class="text-indigo-400 font-mono">86%</span>
                            </div>
                            <div class="w-full bg-slate-900 h-2.5 rounded-full overflow-hidden border border-white/5">
                                <div class="bg-gradient-to-r from-indigo-500 to-cyan-500 h-full rounded-full" style="width: 86%;"></div>
                            </div>
                        </div>

                        <!-- Cloud Telemetry Item -->
                        <div class="flex items-center gap-3 p-3 bg-indigo-950/20 border border-indigo-500/15 rounded-xl text-xs text-indigo-200">
                            <i class="ph-bold ph-database text-lg text-indigo-400 animate-pulse"></i>
                            <span class="font-medium">Cruce dinámico con base de datos en nube activo</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Apps & Connected Modules Grid Section -->
    <section id="apps" class="py-20 px-6 bg-slate-950/50 border-t border-white/5 relative z-10">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <div class="text-center max-w-xl mx-auto space-y-3">
                <h2 class="text-3xl font-black text-white uppercase tracking-tight">Módulos Conectados del Ecosistema</h2>
                <p class="text-sm text-slate-400 font-medium">Aplicaciones empresariales especializadas e integradas para la gestión de flota y control documental.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- App 1: SysSAFE -->
                <div class="glass-card p-6 rounded-[2rem] flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center border border-amber-500/20">
                            <i class="ph-bold ph-truck text-2xl"></i>
                        </div>
                        <div class="space-y-1.5">
                            <h3 class="text-base font-bold text-white uppercase tracking-tight">SysSAFE Logística</h3>
                            <p class="text-xs text-slate-400 font-medium leading-relaxed">
                                Control de despachos, mensajería inteligente, asignación de rutas regionales y liquidaciones de costos de entrega en tiempo real.
                            </p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black uppercase text-amber-500 tracking-widest flex items-center gap-1">
                        Logística flotas <i class="ph ph-arrow-right"></i>
                    </span>
                </div>

                <!-- App 2: Nexus Sync -->
                <div class="glass-card p-6 rounded-[2rem] flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-cyan-500 flex items-center justify-center border border-cyan-500/20">
                            <i class="ph-bold ph-arrows-clockwise text-2xl"></i>
                        </div>
                        <div class="space-y-1.5">
                            <h3 class="text-base font-bold text-white uppercase tracking-tight">Nexus Sync Engine</h3>
                            <p class="text-xs text-slate-400 font-medium leading-relaxed">
                                Sincronización instantánea de traspasos con Firebase Firestore. Control de checkpoints, telemetría e inmutabilidad estricta.
                            </p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black uppercase text-cyan-500 tracking-widest flex items-center gap-1">
                        Sincronización nube <i class="ph ph-arrow-right"></i>
                    </span>
                </div>

                <!-- App 3: Executive Analytics -->
                <div class="glass-card p-6 rounded-[2rem] flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-500 flex items-center justify-center border border-purple-500/20">
                            <i class="ph-bold ph-chart-line text-2xl"></i>
                        </div>
                        <div class="space-y-1.5">
                            <h3 class="text-base font-bold text-white uppercase tracking-tight">Telemetría & SLAs</h3>
                            <p class="text-xs text-slate-400 font-medium leading-relaxed">
                                Cuadros de mando ejecutivos con semáforos de cumplimiento SLA (alertas y críticas), análisis de densidad geográfica y mapas de calor.
                            </p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black uppercase text-purple-500 tracking-widest flex items-center gap-1">
                        Analítica avanzada <i class="ph ph-arrow-right"></i>
                    </span>
                </div>

                <!-- App 4: Audit & Support -->
                <div class="glass-card p-6 rounded-[2rem] flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center border border-indigo-500/20">
                            <i class="ph-bold ph-newspaper text-2xl"></i>
                        </div>
                        <div class="space-y-1.5">
                            <h3 class="text-base font-bold text-white uppercase tracking-tight">Auditoría Documental</h3>
                            <p class="text-xs text-slate-400 font-medium leading-relaxed">
                                Gestión de evidencias físicas firmadas (acuses de recibo y formularios) e impresión automatizada de Reportes del Día oficiales.
                            </p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black uppercase text-indigo-500 tracking-widest flex items-center gap-1">
                        Control físico <i class="ph ph-arrow-right"></i>
                    </span>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 px-6 bg-slate-950 border-t border-white/5 relative z-10">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-bold text-slate-500">
            <div class="flex items-center gap-2">
                <span>© 2026 DISCAN.cloud & Safesure Enterprise.</span>
                <span class="hidden md:inline text-slate-700">|</span>
                <span class="text-slate-400">Todos los derechos reservados.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-white transition-colors">Términos de Servicio</a>
                <a href="#" class="hover:text-white transition-colors">Privacidad de Datos</a>
                <a href="#" class="hover:text-white transition-colors font-mono">v3.4.0</a>
            </div>
        </div>
    </footer>

</body>
</html>
