<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>DISCAN | Asesoría, Correduría de Seguros y Servicios de Salud Premium en RD</title>
    <meta name="description" content="DISCAN es su firma experta de corretaje de seguros y servicios de salud en República Dominicana. Protegemos su empresa y familia con las mejores coberturas de salud, vida, vehículos y propiedades.">
    <meta name="keywords" content="seguros medicos, broker de seguros, corredores de seguros, planes de salud, max corredores, safesure, discan, seguros colectivos, seguro internacional, seguro de vida dominicana">
    <meta name="author" content="DISCAN Enterprise">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Styles & Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Outfit', sans-serif;
        }
        
        /* Navy & Teal Brand Colors */
        .bg-brand-navy {
            background-color: #003b8f;
        }
        .text-brand-navy {
            color: #003b8f;
        }
        .border-brand-navy {
            border-color: #003b8f;
        }
        .bg-brand-gold {
            background-color: #0d9488;
        }
        .text-brand-gold {
            color: #0d9488;
        }
        .border-brand-gold {
            border-color: #0d9488;
        }
        
        .brand-gradient-navy {
            background: linear-gradient(135deg, #002254 0%, #003b8f 50%, #004ea6 100%);
        }
        .brand-gradient-gold-text {
            background: linear-gradient(135deg, #2dd4bf 0%, #0d9488 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Glassmorphism navigation */
        .nav-glass-corporate {
            background: rgba(0, 59, 143, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 2px solid #0d9488;
        }
        
        /* Premium Card Interactions mimicking max.com.do */
        .max-style-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.05);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .max-style-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 59, 143, 0.1);
            border-color: #0d9488;
        }
        .max-style-card:hover .card-icon-container {
            background-color: #0d9488;
            color: #ffffff;
            transform: scale(1.05);
        }
        
        .service-panel-card {
            background: #ffffff;
            border-bottom: 4px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .service-panel-card:hover {
            border-bottom-color: #0d9488;
            box-shadow: 0 10px 15px -3px rgba(0, 59, 143, 0.05);
        }

        .ars-logo-gray {
            filter: grayscale(100%);
            opacity: 0.55;
            transition: all 0.3s ease;
        }
        .ars-logo-gray:hover {
            filter: grayscale(0%);
            opacity: 1;
            transform: scale(1.05);
        }
        
        /* Quick access tab borders */
        .tab-button-active {
            border-bottom: 3px solid #0d9488;
            color: #0d9488;
        }
    </style>
</head>
<body class="antialiased selection:bg-teal-500/10 selection:text-teal-600 overflow-x-hidden min-h-screen flex flex-col justify-between" x-data="{ activeServiceTab: 'empresas', showDropdown: false }">

    <!-- Global Commercial Navbar (Inspirada en MAX) -->
    <header class="fixed top-0 left-0 right-0 z-50 nav-glass-corporate shadow-md transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 group">
                <img src="{{ asset('images/discan-logo.jpg') }}" alt="DISCAN Logo" class="h-12 w-auto rounded-lg shadow-md transition-transform duration-300 group-hover:scale-105">
                <div class="flex flex-col">
                    <span class="text-xl font-display font-black tracking-tight text-white leading-none">DISCAN</span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-teal-400 mt-1 leading-none">Corredores de Seguros</span>
                </div>
            </a>

            <!-- Menu Navigation Dropdowns & Links -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="#inicio" class="text-[11px] font-black uppercase tracking-wider text-slate-200 hover:text-teal-400 transition-colors">Inicio</a>
                
                <!-- Interactive Hover Dropdown for Seguros -->
                <div class="relative" x-data="{ openMenu: false }" @mouseenter="openMenu = true" @mouseleave="openMenu = false">
                    <button class="text-[11px] font-black uppercase tracking-wider text-slate-200 hover:text-teal-400 transition-colors flex items-center gap-1">
                        <span>Nuestros Seguros</span>
                        <i class="ph-bold ph-caret-down text-[10px]"></i>
                    </button>
                    <div x-show="openMenu" x-transition class="absolute top-full left-0 w-64 bg-slate-900 border-t-2 border-teal-500 rounded-b-xl shadow-2xl py-3 px-4 space-y-2 mt-1">
                        <div class="text-[9px] font-black uppercase tracking-widest text-teal-500 pb-1 border-b border-slate-800">Líneas de Cobertura</div>
                        <a href="#seguros" class="block text-xs font-bold text-slate-300 hover:text-white py-1">Seguros para Empresas</a>
                        <a href="#seguros" class="block text-xs font-bold text-slate-300 hover:text-white py-1">Seguros de Salud Familiar</a>
                        <a href="#seguros" class="block text-xs font-bold text-slate-300 hover:text-white py-1">Seguros Internacionales</a>
                        <a href="#seguros" class="block text-xs font-bold text-slate-300 hover:text-white py-1">Seguros de Vehículos y Flotas</a>
                        <a href="#seguros" class="block text-xs font-bold text-slate-300 hover:text-white py-1">Riesgos Generales y Hogar</a>
                    </div>
                </div>

                <a href="#servicios" class="text-[11px] font-black uppercase tracking-wider text-slate-200 hover:text-teal-400 transition-colors">Servicios</a>
                <a href="#nosotros" class="text-[11px] font-black uppercase tracking-wider text-slate-200 hover:text-teal-400 transition-colors">Quiénes Somos</a>
                <a href="#ventaja" class="text-[11px] font-black uppercase tracking-wider text-slate-200 hover:text-teal-400 transition-colors">Ventaja Logística</a>
                <a href="#blog" class="text-[11px] font-black uppercase tracking-wider text-slate-200 hover:text-teal-400 transition-colors">DISCAN te Informa</a>
            </nav>

            <!-- Corporate Operations Portal & Quote CTA -->
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white rounded-lg text-xs font-black uppercase tracking-wider shadow-md shadow-teal-500/10 transition-all flex items-center gap-2">
                            <i class="ph-bold ph-layout text-sm"></i>
                            <span>Portal de Control</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex text-xs font-black uppercase tracking-wider text-slate-300 hover:text-teal-400 transition-colors px-3 py-2">
                            Acceso Personal
                        </a>
                        <a href="{{ route('login') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-black uppercase tracking-wider border border-slate-700 shadow-sm transition-all flex items-center gap-1.5">
                            <i class="ph-bold ph-sign-in text-sm"></i>
                            <span>Ingresar</span>
                        </a>
                    @endauth
                @endif
                <a href="#contacto" class="hidden md:inline-flex px-5 py-2.5 bg-teal-500 hover:bg-teal-600 text-slate-950 rounded-lg text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-teal-500/10">
                    Cotizar Póliza
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section: Presidencial / Institutional Focus (Inspirada en MAX) -->
    <section id="inicio" class="pt-36 pb-28 px-6 brand-gradient-navy text-white relative overflow-hidden">
        
        <!-- Background graphics -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-teal-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Copwriting Column -->
            <div class="lg:col-span-8 space-y-6 text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900 border border-slate-800 text-[10px] font-black uppercase tracking-widest text-teal-400">
                    <i class="ph-bold ph-award"></i> Asesoría de Seguros Oficial y Certificada en RD
                </div>
                
                <h1 class="text-4xl md:text-6xl font-black tracking-tight text-white leading-tight">
                    Evitamos Costos Operativos Innecesarios en su Empresa y Familia.
                </h1>
                
                <p class="text-sm md:text-base text-slate-300 font-medium leading-relaxed max-w-3xl">
                    En **DISCAN** nos apoyamos en la más alta administración técnica para intermediar y administrar sus seguros de salud y riesgos generales. Diseñamos planes colectivos corporativos, familiares e internacionales con las aseguradoras líderes de la República Dominicana. **Le acompañamos en todo momento: desde la cotización hasta el momento clave del siniestro.**
                </p>

                <!-- Value Indicators -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-4 max-w-3xl border-t border-slate-800">
                    <div>
                        <span class="text-3xl font-black font-mono tracking-tighter text-teal-400 block">+15,000</span>
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mt-0.5">Afiliados Protegidos</span>
                    </div>
                    <div>
                        <span class="text-3xl font-black font-mono tracking-tighter text-teal-400 block">+350</span>
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mt-0.5">Empresas Clientes</span>
                    </div>
                    <div>
                        <span class="text-3xl font-black font-mono tracking-tighter text-teal-400 block">100%</span>
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mt-0.5">Asesoría Gratuita</span>
                    </div>
                    <div>
                        <span class="text-3xl font-black font-mono tracking-tighter text-teal-400 block">24/7</span>
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mt-0.5">Gestión de Siniestros</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 pt-4">
                    <a href="#contacto" class="px-8 py-4 bg-teal-500 hover:bg-teal-600 text-slate-950 rounded-lg text-xs font-black uppercase tracking-wider shadow-lg shadow-teal-500/20 transition-all flex items-center gap-2">
                        <span>SOLICITAR COTIZACIÓN PERSONALIZADA</span>
                        <i class="ph-bold ph-calculator text-sm"></i>
                    </a>
                    <a href="#seguros" class="px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-black uppercase tracking-wider border border-slate-700 transition-all">
                        EXPLORAR SEGUROS
                    </a>
                </div>
            </div>

            <!-- Right Column Image Mockup -->
            <div class="lg:col-span-4 relative hidden lg:block">
                <div class="w-full aspect-[4/5] rounded-[2rem] border-4 border-slate-800 bg-slate-900/50 shadow-2xl relative overflow-hidden flex flex-col justify-end p-8">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent z-10"></div>
                    <div class="w-16 h-16 rounded-2xl bg-teal-500 flex items-center justify-center text-slate-950 shadow-xl mb-4 relative z-20">
                        <i class="ph-bold ph-handshake text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-black text-white relative z-20 uppercase tracking-tight">Tranquilidad Total</h3>
                    <p class="text-xs text-slate-400 mt-1 relative z-20 leading-relaxed font-medium">Asesoramos y administramos técnicamente su cartera para mitigar cualquier riesgo.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- Top Quick Service Panel (Accesos Rápidos de Servicio - Estilo max.com.do) -->
    <section class="relative -mt-10 z-30 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Service 1: Reembolsos -->
            <div class="service-panel-card p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <i class="ph-bold ph-receipt text-xl"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Solicitud de Reembolso</h4>
                    <p class="text-[10px] text-slate-400 leading-relaxed">Consulte la documentación y requisitos necesarios para radicar su reembolso médico ante la ARS.</p>
                </div>
                <a href="#contacto" class="inline-flex px-4 py-2 mt-4 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-[10px] font-black uppercase tracking-wider text-center transition-colors">
                    Solicitar
                </a>
            </div>

            <!-- Service 2: Siniestros -->
            <div class="service-panel-card p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <i class="ph-bold ph-bell-ringing text-xl"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Notificación de Siniestro</h4>
                    <p class="text-[10px] text-slate-400 leading-relaxed">Reporte de forma ágil y digital un siniestro médico, vehicular o laboral para soporte inmediato.</p>
                </div>
                <a href="#contacto" class="inline-flex px-4 py-2 mt-4 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-[10px] font-black uppercase tracking-wider text-center transition-colors">
                    Notificar
                </a>
            </div>

            <!-- Service 3: Salud 24/7 -->
            <div class="service-panel-card p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <i class="ph-bold ph-first-aid text-xl"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Línea Salud 24/7</h4>
                    <p class="text-[10px] text-slate-400 leading-relaxed">Teléfono directo de intermediación y soporte para autorizaciones médicas y urgencias clínicas.</p>
                </div>
                <a href="tel:8095550199" class="inline-flex px-4 py-2 mt-4 bg-teal-500 hover:bg-teal-600 text-slate-950 rounded-lg text-[10px] font-black uppercase tracking-wider text-center transition-colors">
                    (809) 555-0199
                </a>
            </div>

            <!-- Service 4: Riesgos Generales -->
            <div class="service-panel-card p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <i class="ph-bold ph-headset text-xl"></i>
                    </div>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Riesgos Generales</h4>
                    <p class="text-[10px] text-slate-400 leading-relaxed">Línea especializada de corretaje corporativo para pólizas de flotas, incendios y de construcción.</p>
                </div>
                <a href="tel:8095550299" class="inline-flex px-4 py-2 mt-4 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-[10px] font-black uppercase tracking-wider text-center transition-colors">
                    (809) 555-0299
                </a>
            </div>

        </div>
    </section>

    <!-- Section: Why trust DISCAN? (Pillares de Confianza - Estilo MAX) -->
    <section class="py-24 px-6 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Info Column -->
            <div class="lg:col-span-5 space-y-6">
                <span class="text-[10px] font-black uppercase tracking-widest text-teal-600 bg-teal-50 px-3 py-1 rounded-full border border-teal-200/50">RESPALDO TOTAL</span>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight leading-tight">
                    ¿Por qué confiar en DISCAN para administrar sus seguros?
                </h2>
                <p class="text-sm text-slate-500 leading-relaxed font-medium">
                    Como corredores de seguros, nos sentamos de su lado de la mesa. Analizamos objetivamente la oferta de todas las aseguradoras (ARS) para proponerle la opción ideal para su empresa o su familia, encargándonos de toda la burocracia logística.
                </p>
                <div class="pt-2">
                    <a href="#contacto" class="inline-flex px-6 py-3.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-black uppercase tracking-wider transition-all">
                        Cotizar sin compromiso
                    </a>
                </div>
            </div>

            <!-- Right Column 4-Pillar Grid -->
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-6">
                
                <!-- Pillar 1 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Acompañamiento Constante</h3>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">No le dejamos solo ante la ARS. Administramos sus reclamaciones, exclusiones y agilizamos sus autorizaciones.</p>
                </div>

                <!-- Pillar 2 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Soluciones a la Medida</h3>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">Adecuamos la póliza colectiva al presupuesto exacto y las necesidades específicas de salud de su nómina.</p>
                </div>

                <!-- Pillar 3 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Respaldo de ARS Líderes</h3>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">Intermediamos con solidez ante todas las ARS acreditadas en la República Dominicana.</p>
                </div>

                <!-- Pillar 4 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-3">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Diferenciador SysSAFE</h3>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">Entregamos contratos y carnets físicos a sus colaboradores en menos de 20 días con auditoría digital.</p>
                </div>

            </div>

        </div>
    </section>

    <!-- Our Insurance Catalog Section (Interactive 8-Segment Grid - Estilo MAX) -->
    <section id="seguros" class="py-24 px-6 bg-white border-t border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto space-y-16">
            
            <div class="text-center max-w-xl mx-auto space-y-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-teal-600 bg-teal-50 px-3 py-1 rounded-full border border-teal-200/50">NUESTROS SEGUROS</span>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Líneas de Cobertura y Servicios</h2>
                <p class="text-sm text-slate-400 font-medium">Conozca la amplia cartera de seguros que intermediamos y administramos para empresas y particulares.</p>
            </div>

            <!-- Tab Toggles (Corporate / Personal) -->
            <div class="flex justify-center gap-6 max-w-xs mx-auto border-b border-slate-200 pb-2">
                <button @click="activeServiceTab = 'empresas'" :class="activeServiceTab === 'empresas' ? 'tab-button-active' : 'text-slate-400'" class="text-xs font-black uppercase tracking-wider pb-1.5 transition-all">
                    Empresas
                </button>
                <button @click="activeServiceTab = 'personas'" :class="activeServiceTab === 'personas' ? 'tab-button-active' : 'text-slate-400'" class="text-xs font-black uppercase tracking-wider pb-1.5 transition-all">
                    Personas
                </button>
            </div>

            <!-- Grid Container: 8 Seguros -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- 1. Seguros para Empresas (Visible under both) -->
                <div class="max-style-card p-8 rounded-2xl flex flex-col justify-between" x-show="activeServiceTab === 'empresas'">
                    <div class="space-y-4">
                        <div class="card-icon-container w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center border border-slate-100 transition-all shrink-0">
                            <i class="ph-bold ph-buildings text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Salud Corporativa</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                            Planes médicos colectivos premium para sus colaboradores. Reducimos la siniestralidad y negociamos beneficios en maternidad y odontología.
                        </p>
                    </div>
                    <a href="#contacto" @click="document.getElementById('tipo_plan').value = 'colectivo';" class="text-[10px] font-black uppercase tracking-wider text-teal-600 flex items-center gap-1.5 hover:underline pt-4">
                        <span>Cotizar Colectivo</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- 2. Seguros de Salud Familiar -->
                <div class="max-style-card p-8 rounded-2xl flex flex-col justify-between" x-show="activeServiceTab === 'personas'">
                    <div class="space-y-4">
                        <div class="card-icon-container w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center border border-slate-100 transition-all shrink-0">
                            <i class="ph-bold ph-heart-beat text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Salud Familiar</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                            Aseguramos la tranquilidad de su hogar con coberturas de consulta médica, emergencias 24/7 y hospitalización en las clínicas más prestigiosas del país.
                        </p>
                    </div>
                    <a href="#contacto" @click="document.getElementById('tipo_plan').value = 'familiar';" class="text-[10px] font-black uppercase tracking-wider text-teal-600 flex items-center gap-1.5 hover:underline pt-4">
                        <span>Ver planes familiares</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- 3. Seguros Internacionales -->
                <div class="max-style-card p-8 rounded-2xl flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="card-icon-container w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center border border-slate-100 transition-all shrink-0">
                            <i class="ph-bold ph-globe-hemisphere-west text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Salud Internacional</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                            Acceso médico catastrófico de lujo global. Coberturas millonarias en los mejores centros oncológicos de EE.UU. y Europa.
                        </p>
                    </div>
                    <a href="#contacto" @click="document.getElementById('tipo_plan').value = 'internacional';" class="text-[10px] font-black uppercase tracking-wider text-teal-600 flex items-center gap-1.5 hover:underline pt-4">
                        <span>Ver cobertura global</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- 4. Vehículos y Flotas -->
                <div class="max-style-card p-8 rounded-2xl flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="card-icon-container w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center border border-slate-100 transition-all shrink-0">
                            <i class="ph-bold ph-car text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Vehículos & Flotas</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                            Protegemos la movilidad corporativa e individual. Pólizas para vehículos ejecutivos, flotas comerciales y camiones con auxilio vial express.
                        </p>
                    </div>
                    <a href="#contacto" class="text-[10px] font-black uppercase tracking-wider text-teal-600 flex items-center gap-1.5 hover:underline pt-4">
                        <span>Cotizar Vehículo</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- 5. Hogar y Propiedades -->
                <div class="max-style-card p-8 rounded-2xl flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="card-icon-container w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center border border-slate-100 transition-all shrink-0">
                            <i class="ph-bold ph-house text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Hogar & Propiedades</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                            Resguardo físico ante incendios, terremotos, robo y huracanes para su infraestructura corporativa o de residencia familiar.
                        </p>
                    </div>
                    <a href="#contacto" class="text-[10px] font-black uppercase tracking-wider text-teal-600 flex items-center gap-1.5 hover:underline pt-4">
                        <span>Saber más</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- 6. Fianzas e Inversión -->
                <div class="max-style-card p-8 rounded-2xl flex flex-col justify-between" x-show="activeServiceTab === 'empresas'">
                    <div class="space-y-4">
                        <div class="card-icon-container w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center border border-slate-100 transition-all shrink-0">
                            <i class="ph-bold ph-coins text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Fianzas e Inversión</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                            Garantías para licitaciones públicas y privadas. Fianzas de fiel cumplimiento de contrato, de vicios ocultos y anticipo.
                        </p>
                    </div>
                    <a href="#contacto" class="text-[10px] font-black uppercase tracking-wider text-teal-600 flex items-center gap-1.5 hover:underline pt-4">
                        <span>Solicitar Fianza</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- 7. Construcción y Montaje -->
                <div class="max-style-card p-8 rounded-2xl flex flex-col justify-between" x-show="activeServiceTab === 'empresas'">
                    <div class="space-y-4">
                        <div class="card-icon-container w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center border border-slate-100 transition-all shrink-0">
                            <i class="ph-bold ph-crane text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Construcción & Obras</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                            Pólizas Todo Riesgo de Construcción (CAR). Coberturas para maquinaria contratista y daños a terceros en obras civiles.
                        </p>
                    </div>
                    <a href="#contacto" class="text-[10px] font-black uppercase tracking-wider text-teal-600 flex items-center gap-1.5 hover:underline pt-4">
                        <span>Ver cobertura de obra</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- 8. Maquinarias y Equipos -->
                <div class="max-style-card p-8 rounded-2xl flex flex-col justify-between" x-show="activeServiceTab === 'empresas'">
                    <div class="space-y-4">
                        <div class="card-icon-container w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center border border-slate-100 transition-all shrink-0">
                            <i class="ph-bold ph-cpu text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Maquinarias & Equipos</h3>
                        <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                            Aseguramiento de servidores, equipos médicos e informáticos y avería de maquinaria pesada industrial.
                        </p>
                    </div>
                    <a href="#contacto" class="text-[10px] font-black uppercase tracking-wider text-teal-600 flex items-center gap-1.5 hover:underline pt-4">
                        <span>Ver detalles</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

            </div>

        </div>
    </section>

    <!-- Technological Edge: The SysSAFE Logistics Advantage Section -->
    <section id="ventaja" class="py-24 px-6 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-teal-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <!-- Left Copwriting -->
            <div class="lg:col-span-6 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-950 border border-slate-800 text-[10px] font-black uppercase tracking-widest text-teal-400">
                    <i class="ph-bold ph-lightning text-xs"></i> Tecnología Logística Exclusiva
                </div>
                
                <h2 class="text-3xl md:text-4xl font-black text-white uppercase tracking-tight leading-tight">
                    Nuestra Ventaja SysSAFE: Contratos y Carnets en sus Manos
                </h2>
                
                <p class="text-sm text-slate-400 leading-relaxed">
                    Un error común de los corredores de seguros de salud tradicionales es el descontrol en la distribución física de los contratos y carnets de los nuevos afiliados. Recursos Humanos suele pasar semanas gestionando y coordinando las entregas individuales.
                </p>
                <p class="text-sm text-slate-400 leading-relaxed">
                    En **DISCAN** resolvemos esto integrando **SysSAFE**, nuestro exclusivo sistema de distribución física express. Todos los carnets físicos y contratos se procesan de forma inmediata en nuestro motor logístico con un semáforo predictivo de SLA de 20 días.
                </p>

                <!-- 3 Pillars of Logistics -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-950 text-teal-500 flex items-center justify-center shrink-0 border border-slate-800">
                            <i class="ph-bold ph-truck text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-200">Envío Express Directo</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Rutas de mensajería optimizadas para realizar entregas físicas en la oficina corporativa u hogar.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-950 text-teal-500 flex items-center justify-center shrink-0 border border-slate-800">
                            <i class="ph-bold ph-signature text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-200">Acuse de Recibo Físico Auditado</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">El ciclo finaliza obligatoriamente con la firma digitalizada del receptor, garantizando que el carnet llegó de forma conforme.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-950 text-teal-500 flex items-center justify-center shrink-0 border border-slate-800">
                            <i class="ph-bold ph-arrow-counter-clockwise text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-200">Monitoreo en Tiempo Real</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Los departamentos de Recursos Humanos de las empresas afiliadas tienen acceso completo al portal para ver el estatus exacto del despacho.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Graphic card / Mockup representation of SysSAFE -->
            <div class="lg:col-span-6">
                <div class="bg-slate-950 p-8 rounded-[2.5rem] border border-slate-800 shadow-2xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500 animate-ping"></span>
                            <span class="text-xs font-black uppercase text-slate-200 tracking-wider">SysSAFE Control Engine</span>
                        </div>
                        <span class="text-[9px] font-mono text-slate-500 font-bold uppercase">Módulos de Auditoría</span>
                    </div>

                    <!-- Steps Timeline -->
                    <div class="space-y-6 relative before:absolute before:top-4 before:bottom-4 before:left-5 before:border-l before:border-slate-800 before:border-dashed">
                        
                        <div class="flex items-center gap-4 relative">
                            <div class="w-10 h-10 rounded-full bg-slate-900 border border-slate-800 text-teal-500 flex items-center justify-center z-10 shrink-0">
                                <i class="ph-bold ph-arrows-clockwise text-sm"></i>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-200">1. Sincronización Remota</h5>
                                <p class="text-[10px] text-slate-400">Ingesta al instante de traspasos autorizados por la ARS.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 relative">
                            <div class="w-10 h-10 rounded-full bg-slate-900 border border-slate-800 text-teal-500 flex items-center justify-center z-10 shrink-0">
                                <i class="ph-bold ph-printer text-sm"></i>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-200">2. Emisión y Embalaje</h5>
                                <p class="text-[10px] text-slate-400">Impresión física de carnets con semáforo SLA de 20 días.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 relative">
                            <div class="w-10 h-10 rounded-full bg-teal-950 border border-teal-800 text-teal-400 flex items-center justify-center z-10 shrink-0">
                                <i class="ph-bold ph-check-square-offset text-sm"></i>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-200">3. Acuse de Recibo Físico</h5>
                                <p class="text-[10px] text-slate-400">Firma digital de entrega y cierre inmutable en el sistema.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Acredited ARS Allies Slide Banner -->
    <section class="py-12 bg-white border-t border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-center text-[10px] font-black uppercase tracking-widest text-slate-400 mb-8">
                Asesores autorizados oficiales ante los principales proveedores de salud
            </p>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-8 items-center text-center">
                <div class="ars-logo-gray flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS Humano</span>
                    <span class="text-[8px] font-black uppercase text-teal-600 tracking-widest">Socio Platino</span>
                </div>
                <div class="ars-logo-gray flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS Universal</span>
                    <span class="text-[8px] font-black uppercase text-teal-600 tracking-widest">Cobertura Total</span>
                </div>
                <div class="ars-logo-gray flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">MAPFRE Salud</span>
                    <span class="text-[8px] font-black uppercase text-teal-600 tracking-widest">Seguro Global</span>
                </div>
                <div class="ars-logo-gray flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS Reservas</span>
                    <span class="text-[8px] font-black uppercase text-teal-600 tracking-widest">Red Preferida</span>
                </div>
                <div class="ars-logo-gray flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS Monumental</span>
                    <span class="text-[8px] font-black uppercase text-teal-600 tracking-widest">Red Norte</span>
                </div>
                <div class="ars-logo-gray flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS CMD</span>
                    <span class="text-[8px] font-black uppercase text-teal-600 tracking-widest">Gremio Oficial</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section: "DISCAN te Informa" (Inspirada en MAX te informa) -->
    <section id="blog" class="py-24 px-6 bg-slate-50 border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto space-y-16">
            
            <div class="text-center max-w-xl mx-auto space-y-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-teal-600 bg-teal-50 px-3 py-1 rounded-full border border-teal-200/50">DISCAN TE INFORMA</span>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Noticias y Consejos de Seguros</h2>
                <p class="text-sm text-slate-400 font-medium">Manténgase al día con las últimas regulaciones de salud y consejos de corretaje de seguros en República Dominicana.</p>
            </div>

            <!-- Blog Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Post 1 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm flex flex-col justify-between max-style-card">
                    <div>
                        <div class="h-44 bg-slate-900 relative flex items-center justify-center p-6 text-center text-white">
                            <span class="text-xs font-black uppercase tracking-widest text-teal-400 block border border-teal-500/20 px-4 py-2 rounded-xl">SALUD & COPAGO</span>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3 class="text-base font-extrabold text-slate-800 hover:text-teal-600 transition-colors">
                                Te aclaramos algunas dudas sobre el copago y las pólizas médicas
                            </h3>
                            <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                                ¿Sabe exactamente qué es el copago y qué límites tiene por ley en la República Dominicana? Analizamos los puntos clave para evitar cobros sorpresa.
                            </p>
                        </div>
                    </div>
                    <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400">Mayo, 2026</span>
                        <a href="#contacto" class="text-[10px] font-black uppercase text-teal-600 hover:underline">Leer artículo</a>
                    </div>
                </div>

                <!-- Post 2 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm flex flex-col justify-between max-style-card">
                    <div>
                        <div class="h-44 bg-slate-900 relative flex items-center justify-center p-6 text-center text-white">
                            <span class="text-xs font-black uppercase tracking-widest text-teal-400 block border border-teal-500/20 px-4 py-2 rounded-xl">CORRETAJE DE SEGUROS</span>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3 class="text-base font-extrabold text-slate-800 hover:text-teal-600 transition-colors">
                                Beneficios de contratar a un Corredor de Seguros para su Empresa
                            </h3>
                            <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                                Descubra cómo un corredor de seguros oficial le ayuda a ahorrar costes operativos innecesarios y optimizar el soporte a sus empleados, sin gastar un centavo más.
                            </p>
                        </div>
                    </div>
                    <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400">Mayo, 2026</span>
                        <a href="#contacto" class="text-[10px] font-black uppercase text-teal-600 hover:underline">Leer artículo</a>
                    </div>
                </div>

                <!-- Post 3 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm flex flex-col justify-between max-style-card">
                    <div>
                        <div class="h-44 bg-slate-900 relative flex items-center justify-center p-6 text-center text-white">
                            <span class="text-xs font-black uppercase tracking-widest text-teal-400 block border border-teal-500/20 px-4 py-2 rounded-xl">RIESGOS GENERALES</span>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3 class="text-base font-extrabold text-slate-800 hover:text-teal-600 transition-colors">
                                Cómo proteger los activos de su negocio ante incendios y catástrofes
                            </h3>
                            <p class="text-[11px] text-slate-400 leading-relaxed font-medium">
                                Las líneas aliadas y el seguro de infraestructura física son vitales para garantizar la continuidad del negocio ante terremotos, robos o huracanes en el Caribe.
                            </p>
                        </div>
                    </div>
                    <div class="p-6 pt-0 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400">Mayo, 2026</span>
                        <a href="#contacto" class="text-[10px] font-black uppercase text-teal-600 hover:underline">Leer artículo</a>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Multi-Step Interactive Quote Form (Estilo MAX) -->
    <section id="contacto" class="py-24 px-6 bg-white relative overflow-hidden" x-data="{ 
        step: 1, 
        profile: 'empresa', 
        name: '', 
        phone: '', 
        email: '', 
        company: '', 
        arsInterest: 'cualquiera',
        timePreference: 'whatsapp',
        submitForm() {
            Swal.fire({
                title: '¡Solicitud Procesada!',
                text: 'Su solicitud de cotización comparativa ha sido recibida con éxito. Un ejecutivo experto de DISCAN se pondrá en contacto con usted por ' + (this.timePreference === 'whatsapp' ? 'WhatsApp' : 'teléfono') + ' a la brevedad.',
                icon: 'success',
                confirmButtonColor: '#d97706'
            });
            this.step = 1;
            this.name = '';
            this.phone = '';
            this.email = '';
            this.company = '';
            this.arsInterest = 'cualquiera';
            this.timePreference = 'whatsapp';
        }
    }">
        <div class="max-w-4xl mx-auto space-y-12">
            
            <div class="text-center max-w-xl mx-auto space-y-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-teal-600 bg-teal-50 px-3 py-1 rounded-full border border-teal-200/50">COTIZACIÓN DE SEGUROS</span>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Solicite una Cotización Sin Compromiso</h2>
                <p class="text-sm text-slate-400 font-medium">Utilice nuestro asistente interactivo para indicarnos qué seguro requiere y analizaremos las mejores propuestas de las ARS dominicanas.</p>
            </div>

            <!-- Form Progress Steps -->
            <div class="flex items-center justify-center gap-3 max-w-md mx-auto">
                <div class="flex items-center gap-1.5">
                    <span :class="step >= 1 ? 'bg-slate-900 text-white border-teal-500' : 'bg-slate-100 text-slate-400 border-slate-200'" class="w-6 h-6 rounded-full border flex items-center justify-center text-[10px] font-bold transition-all">1</span>
                    <span class="text-[10px] font-bold text-slate-700">Riesgo</span>
                </div>
                <div class="w-12 h-0.5 bg-slate-200 relative"><div :class="step >= 2 ? 'w-full' : 'w-0'" class="absolute inset-0 bg-teal-500 transition-all duration-300"></div></div>
                <div class="flex items-center gap-1.5">
                    <span :class="step >= 2 ? 'bg-slate-900 text-white border-teal-500' : 'bg-slate-100 text-slate-400 border-slate-200'" class="w-6 h-6 rounded-full border flex items-center justify-center text-[10px] font-bold transition-all">2</span>
                    <span class="text-[10px] font-bold text-slate-700">Contacto</span>
                </div>
                <div class="w-12 h-0.5 bg-slate-200 relative"><div :class="step >= 3 ? 'w-full' : 'w-0'" class="absolute inset-0 bg-teal-500 transition-all duration-300"></div></div>
                <div class="flex items-center gap-1.5">
                    <span :class="step >= 3 ? 'bg-slate-900 text-white border-teal-500' : 'bg-slate-100 text-slate-400 border-slate-200'" class="w-6 h-6 rounded-full border flex items-center justify-center text-[10px] font-bold transition-all">3</span>
                    <span class="text-[10px] font-bold text-slate-700">Asesoría</span>
                </div>
            </div>

            <!-- Interactive Form Panel (Navy Accent) -->
            <div class="bg-white p-8 md:p-12 rounded-3xl border border-slate-200/80 shadow-2xl max-w-2xl mx-auto">
                <form @submit.preventDefault="submitForm()">
                    
                    <!-- STEP 1: Plan Profile Selection -->
                    <div x-show="step === 1" class="space-y-6">
                        <div class="text-center space-y-1">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">¿Qué tipo de cobertura de seguro necesita cotizar?</h3>
                            <p class="text-xs text-slate-400">Seleccione el perfil que mejor se adapte a sus requerimientos actuales.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Option Corporate -->
                            <div @click="profile = 'empresa'" :class="profile === 'empresa' ? 'border-teal-500 bg-teal-50/10 ring-2 ring-teal-500/10' : 'border-slate-200'" class="p-5 rounded-2xl border-2 cursor-pointer hover:border-teal-400 transition-all space-y-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center">
                                    <i class="ph-bold ph-buildings text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Mi Empresa</h4>
                                    <p class="text-[10px] text-slate-400 mt-1">Seguros colectivos, de riesgos generales, de obras o flotas.</p>
                                </div>
                            </div>

                            <!-- Option Family -->
                            <div @click="profile = 'familiar'" :class="profile === 'familiar' ? 'border-teal-500 bg-teal-50/10 ring-2 ring-teal-500/10' : 'border-slate-200'" class="p-5 rounded-2xl border-2 cursor-pointer hover:border-teal-400 transition-all space-y-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center">
                                    <i class="ph-bold ph-users text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Mi Familia</h4>
                                    <p class="text-[10px] text-slate-400 mt-1">Seguros de salud familiar locales o de hogar.</p>
                                </div>
                            </div>

                            <!-- Option International -->
                            <div @click="profile = 'internacional'" :class="profile === 'internacional' ? 'border-teal-500 bg-teal-50/10 ring-2 ring-teal-500/10' : 'border-slate-200'" class="p-5 rounded-2xl border-2 cursor-pointer hover:border-teal-400 transition-all space-y-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center">
                                    <i class="ph-bold ph-globe text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Internacional</h4>
                                    <p class="text-[10px] text-slate-400 mt-1">Planes catastróficos y médicos globales en USD.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100">
                            <button type="button" @click="step = 2" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-black uppercase tracking-wider transition-all flex items-center gap-1.5">
                                <span>Siguiente</span>
                                <i class="ph-bold ph-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Contact Information -->
                    <div x-show="step === 2" class="space-y-6" style="display: none;">
                        <div class="text-center space-y-1">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Datos de Identificación y Contacto</h3>
                            <p class="text-xs text-slate-400">Esta información es estrictamente confidencial para el análisis de sus cotizaciones.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label for="form_nombre" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Nombre Completo</label>
                                <input type="text" id="form_nombre" x-model="name" placeholder="Ej. Juan Pérez" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-0 rounded-lg text-xs font-medium transition-all">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label for="form_telefono" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Teléfono (WhatsApp)</label>
                                    <input type="tel" id="form_telefono" x-model="phone" placeholder="Ej. 809-555-0199" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-0 rounded-lg text-xs font-medium transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label for="form_email" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Correo Electrónico</label>
                                    <input type="email" id="form_email" x-model="email" placeholder="Ej. juan@ejemplo.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-0 rounded-lg text-xs font-medium transition-all">
                                </div>
                            </div>

                            <!-- Conditionally show company field if profile is Empresa -->
                            <div class="space-y-1" x-show="profile === 'empresa'">
                                <label for="form_empresa" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Nombre de la Empresa</label>
                                <input type="text" id="form_empresa" x-model="company" placeholder="Ej. DISCAN SRL" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-0 rounded-lg text-xs font-medium transition-all">
                            </div>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-slate-100">
                            <button type="button" @click="step = 1" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg text-xs font-black uppercase tracking-wider transition-all">
                                Atrás
                            </button>
                            <button type="button" @click="if(name && phone && email) { step = 3; } else { Swal.fire('Faltan Datos', 'Por favor complete todos los campos.', 'error'); }" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-black uppercase tracking-wider transition-all flex items-center gap-1.5">
                                <span>Siguiente</span>
                                <i class="ph-bold ph-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: Broker Preferences & ARS -->
                    <div x-show="step === 3" class="space-y-6" style="display: none;">
                        <div class="text-center space-y-1">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Preferencias de Asesoría y ARS</h3>
                            <p class="text-xs text-slate-400">Personalice el enfoque de análisis comercial de su seguro.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label for="tipo_plan" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Tipo de Seguro Requerido</label>
                                <select id="tipo_plan" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-0 rounded-lg text-xs text-slate-700 font-medium transition-all">
                                    <option value="colectivo" :selected="profile === 'empresa'">Plan Médico Colectivo (Para mi Empresa)</option>
                                    <option value="familiar" :selected="profile === 'familiar'">Plan Médico Familiar / Individual</option>
                                    <option value="internacional" :selected="profile === 'internacional'">Plan de Salud Internacional Premium</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="form_ars" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">¿Tiene alguna ARS o Aseguradora de Interés Preferente?</label>
                                <select id="form_ars" x-model="arsInterest" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-teal-500 focus:ring-0 rounded-lg text-xs text-slate-700 font-medium transition-all">
                                    <option value="cualquiera">No tengo preferencia (Quiero comparar todas)</option>
                                    <option value="humano">ARS Humano</option>
                                    <option value="universal">ARS Universal</option>
                                    <option value="reservas">ARS Reservas</option>
                                    <option value="monumental">ARS Monumental</option>
                                    <option value="cmd">ARS CMD</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="form_metodo" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Método de Contacto Preferido</label>
                                <div class="grid grid-cols-2 gap-4 pt-1">
                                    <div @click="timePreference = 'whatsapp'" :class="timePreference === 'whatsapp' ? 'border-teal-500 bg-teal-50/10' : 'border-slate-200'" class="p-3.5 border rounded-xl flex items-center gap-2 cursor-pointer transition-all">
                                        <i class="ph-bold ph-whatsapp-logo text-xl text-emerald-500"></i>
                                        <span class="text-xs font-bold text-slate-700">WhatsApp</span>
                                    </div>
                                    <div @click="timePreference = 'llamada'" :class="timePreference === 'llamada' ? 'border-teal-500 bg-teal-50/10' : 'border-slate-200'" class="p-3.5 border rounded-xl flex items-center gap-2 cursor-pointer transition-all">
                                        <i class="ph-bold ph-phone text-xl text-blue-500"></i>
                                        <span class="text-xs font-bold text-slate-700">Llamada</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-slate-100">
                            <button type="button" @click="step = 2" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-lg text-xs font-black uppercase tracking-wider transition-all">
                                Atrás
                            </button>
                            <button type="submit" class="px-7 py-3 bg-teal-500 hover:bg-teal-600 text-slate-950 rounded-lg text-xs font-black uppercase tracking-wider shadow-md shadow-teal-500/10 transition-all flex items-center gap-1.5">
                                <span>ENVIAR SOLICITUD</span>
                                <i class="ph-bold ph-paper-plane-right"></i>
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section class="py-24 px-6 bg-slate-50 border-t border-slate-200/50" x-data="{ activeFaq: null }">
        <div class="max-w-4xl mx-auto space-y-12">
            
            <div class="text-center max-w-xl mx-auto space-y-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-teal-600 bg-teal-50 px-3 py-1 rounded-full border border-teal-200/50">PREGUNTAS FRECUENTES</span>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Preguntas Frecuentes</h2>
                <p class="text-sm text-slate-400 font-medium">Aclare de forma transparente cómo funciona la intermediación a través de un corredor oficial de seguros médicos.</p>
            </div>

            <div class="space-y-4 max-w-3xl mx-auto">
                <!-- FAQ Item 1 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                    <button @click="activeFaq === 1 ? activeFaq = null : activeFaq = 1" class="w-full p-5 text-left flex items-center justify-between gap-4 bg-white hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wide">¿La intermediación y consultoría técnica de DISCAN tiene algún costo para el cliente?</span>
                        <i :class="activeFaq === 1 ? 'ph-bold ph-caret-up text-teal-600' : 'ph-bold ph-caret-down text-slate-500'" class="text-base transition-transform duration-300"></i>
                    </button>
                    <div x-show="activeFaq === 1" x-transition class="p-6 bg-white border-t border-slate-100 text-xs text-slate-500 leading-relaxed font-medium">
                        **No tiene absolutamente ningún costo para usted.** En la República Dominicana, la Ley de Seguros establece que las Administradoras de Riesgos de Salud (ARS) y aseguradoras pagan las comisiones directamente a los corredores y asesores autorizados. El costo de su póliza médica será el mismo (o menor por negociación grupal) que si contratara directamente, pero con la ventaja de tener un equipo experto que defiende sus derechos sin costo.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                    <button @click="activeFaq === 2 ? activeFaq = null : activeFaq = 2" class="w-full p-5 text-left flex items-center justify-between gap-4 bg-white hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wide">¿Puedo cambiar mi corredor actual por DISCAN manteniendo mi ARS actual?</span>
                        <i :class="activeFaq === 2 ? 'ph-bold ph-caret-up text-teal-600' : 'ph-bold ph-caret-down text-slate-500'" class="text-base transition-transform duration-300"></i>
                    </button>
                    <div x-show="activeFaq === 2" x-transition class="p-6 bg-white border-t border-slate-100 text-xs text-slate-500 leading-relaxed font-medium">
                        **Sí, de forma sumamente sencilla.** Solo requiere la firma de una carta de nombramiento oficial de corretaje. Su póliza médica, red de clínicas, coberturas y tarifas con su ARS actual permanecerán exactamente iguales, pero ganará el soporte técnico experto de DISCAN para sus reclamaciones y la ventaja del control logístico express **SysSAFE** para sus empleados.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                    <button @click="activeFaq === 3 ? activeFaq = null : activeFaq = 3" class="w-full p-5 text-left flex items-center justify-between gap-4 bg-white hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wide">¿Cuál es la red de cobertura nacional que ofrecen sus seguros de salud?</span>
                        <i :class="activeFaq === 3 ? 'ph-bold ph-caret-up text-teal-600' : 'ph-bold ph-caret-down text-slate-500'" class="text-base transition-transform duration-300"></i>
                    </button>
                    <div x-show="activeFaq === 3" x-transition class="p-6 bg-white border-t border-slate-100 text-xs text-slate-500 leading-relaxed font-medium">
                        Al intermediar con todas las ARS acreditadas (Humano, Universal, Mapfre, Reservas, Monumental), podemos ofrecer planes con acceso a la red médica premium del país, que incluye prestigiosos centros de salud como la **Clínica Abreu, Centro de Diagnóstico Medicina Avanzada y Telemedicina (CEDIMAT), Hospital General Plaza de la Salud, y Clínica Corazones Unidos**, entre otros.
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all shadow-sm">
                    <button @click="activeFaq === 4 ? activeFaq = null : activeFaq = 4" class="w-full p-5 text-left flex items-center justify-between gap-4 bg-white hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wide">¿Cómo ayuda SysSAFE a agilizar los procesos de Recursos Humanos?</span>
                        <i :class="activeFaq === 4 ? 'ph-bold ph-caret-up text-teal-600' : 'ph-bold ph-caret-down text-slate-500'" class="text-base transition-transform duration-300"></i>
                    </button>
                    <div x-show="activeFaq === 4" x-transition class="p-6 bg-white border-t border-slate-100 text-xs text-slate-500 leading-relaxed font-medium">
                        **SysSAFE** elimina todo el descontrol y fricción. Cuando la ARS emite los carnets físicos o contratos, nuestro sistema los clasifica por ruta inteligente, asigna un mensajero exclusivo y notifica a Recursos Humanos en tiempo real. Al momento de la entrega, se realiza una firma digital en dispositivo móvil que constituye el **acuse de recibo conforme e inmutable**, garantizando un control de entrega física al 100%.
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer: Commercial & Contact (Navy Theme - Estilo MAX) -->
    <footer class="py-16 px-6 bg-slate-950 text-slate-400 border-t border-slate-900 relative">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 text-sm pb-12 border-b border-slate-900">
            
            <!-- Logo area -->
            <div class="space-y-4">
                <a href="#" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-slate-950 font-black">
                        <i class="ph-bold ph-shield-check text-xl text-white"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-black tracking-tight text-white leading-none">DISCAN</span>
                        <span class="text-[8px] font-black uppercase tracking-widest text-teal-400 mt-1 leading-none">Corredores de Seguros</span>
                    </div>
                </a>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Firma autorizada e intermediaria oficial de seguros de salud, vida, vehículos y riesgos generales para empresas y personas en la República Dominicana.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4">
                <h4 class="text-xs font-black uppercase tracking-widest text-white">Nuestros Seguros</h4>
                <ul class="space-y-2.5 text-xs text-slate-400 font-medium">
                    <li><a href="#seguros" class="hover:text-white transition-colors">Seguros Colectivos para Empresas</a></li>
                    <li><a href="#seguros" class="hover:text-white transition-colors">Seguro de Salud Familiar</a></li>
                    <li><a href="#seguros" class="hover:text-white transition-colors">Seguro Médico Internacional</a></li>
                    <li><a href="#seguros" class="hover:text-white transition-colors">Seguro de Flotas de Motores</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="space-y-4">
                <h4 class="text-xs font-black uppercase tracking-widest text-white">Contacto y Soporte</h4>
                <ul class="space-y-2.5 text-xs text-slate-400 font-medium">
                    <li class="flex items-center gap-2">
                        <i class="ph-bold ph-phone text-teal-500"></i>
                        <span>(809) 555-0199</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="ph-bold ph-envelope text-teal-500"></i>
                        <span>info@discan.cloud</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="ph-bold ph-map-pin text-teal-500"></i>
                        <span>Av. Winston Churchill, Santo Domingo, R.D.</span>
                    </li>
                </ul>
            </div>

            <!-- Portal Dashboard Direct Access -->
            <div class="space-y-4">
                <h4 class="text-xs font-black uppercase tracking-widest text-white">Portal Autorizado</h4>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Acceso logístico exclusivo para promotores, mensajeros, personal logístico de SAFESURE y auditores de ARS CMD.
                </p>
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex px-4 py-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-white rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                        Ingresar al Portal
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex px-4 py-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-white rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                        Ingresar al Portal
                    </a>
                @endauth
            </div>

        </div>

        <div class="max-w-7xl mx-auto pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-bold">
            <div class="flex items-center gap-2">
                <span>© 2026 DISCAN. Todos los derechos reservados.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-white transition-colors">Términos de Servicio</a>
                <a href="#" class="hover:text-white transition-colors">Privacidad de Datos</a>
                <span class="font-mono text-slate-700">v3.7.0</span>
            </div>
        </div>
    </footer>

    <!-- SweetAlert2 (Para el feedback interactivo del formulario) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
