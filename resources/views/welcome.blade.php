<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>DISCAN | Correduría de Seguros Médicos y Servicios de Salud Premium</title>
    <meta name="description" content="DISCAN es su firma de corretaje y asesoría de seguros médicos de confianza. Diseñamos planes colectivos corporativos, seguros familiares e internacionales con las principales ARS del país, respaldados por la tecnología exclusiva SysSAFE.">
    <meta name="keywords" content="seguros medicos, broker de seguros, corredores de seguros, planes de salud, ars humano, ars universal, safesure, discan, seguros colectivos, seguro internacional">
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
            background-color: #fafbfc;
            color: #0f172a;
        }
        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: 'Outfit', sans-serif;
        }
        .brand-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e40af 50%, #0284c7 100%);
        }
        .text-brand-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-glass {
            background: rgba(250, 251, 252, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
        }
        .premium-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.02);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .premium-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.08);
            border-color: rgba(37, 99, 235, 0.15);
        }
        .ars-logo-card {
            filter: grayscale(100%);
            opacity: 0.65;
            transition: all 0.3s ease;
        }
        .ars-logo-card:hover {
            filter: grayscale(0%);
            opacity: 1;
            transform: scale(1.03);
        }
        /* Custom smooth scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="antialiased selection:bg-blue-500/10 selection:text-blue-600 overflow-x-hidden min-h-screen flex flex-col justify-between" x-data="{ activePlan: 'corporate' }">

    <!-- Global Commercial Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 nav-glass transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20 transform group-hover:rotate-6 transition-transform duration-300">
                    <i class="ph-bold ph-shield-plus text-2xl"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-black tracking-tight text-slate-800 leading-none">DISCAN</span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-600 mt-1 leading-none">Corredores de Seguros</span>
                </div>
            </a>

            <!-- Commercial Menu Links -->
            <nav class="hidden lg:flex items-center gap-8">
                <a href="#inicio" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-blue-600 transition-colors">Inicio</a>
                <a href="#asesor" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-blue-600 transition-colors">Asesor de Salud</a>
                <a href="#servicios" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-blue-600 transition-colors">Planes y Servicios</a>
                <a href="#nosotros" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-blue-600 transition-colors">Quiénes Somos</a>
                <a href="#ventaja" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-blue-600 transition-colors">Ventaja SysSAFE</a>
                <a href="#faq" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-blue-600 transition-colors">Preguntas Frecuentes</a>
            </nav>

            <!-- Corporate & Quote CTA -->
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold uppercase tracking-wider shadow-md shadow-blue-600/10 transition-all flex items-center gap-2">
                            <i class="ph-bold ph-layout text-sm"></i>
                            <span>Portal de Control</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex text-xs font-extrabold uppercase tracking-wider text-slate-600 hover:text-blue-600 transition-colors px-3 py-2">
                            Acceso Personal
                        </a>
                        <a href="{{ route('login') }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-extrabold uppercase tracking-wider shadow-sm transition-all flex items-center gap-1.5">
                            <i class="ph-bold ph-sign-in text-sm"></i>
                            <span>Ingresar</span>
                        </a>
                    @endauth
                @endif
                <a href="#contacto" class="hidden md:inline-flex px-5 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all border border-blue-100">
                    Cotizar Ahora
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section: Commercial Value Proposition -->
    <section id="inicio" class="pt-36 pb-20 px-6 bg-gradient-to-b from-blue-50/50 via-white to-slate-50/50 relative overflow-hidden">
        <!-- Floating shapes -->
        <div class="absolute top-1/4 left-0 w-80 h-80 bg-blue-300/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/3 right-0 w-96 h-96 bg-cyan-200/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Info Block -->
            <div class="lg:col-span-7 space-y-8 text-left">
                <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-blue-50 border border-blue-100/80 text-[10px] font-black uppercase tracking-widest text-blue-600">
                    <i class="ph-bold ph-shield-check text-xs"></i> Firma Registrada de Corretaje de Salud
                </div>
                
                <h1 class="text-4xl md:text-6xl font-black tracking-tight text-slate-900 leading-tight">
                    Optimizamos su Seguro Médico Corporativo <span class="text-brand-gradient">A Costo Cero.</span>
                </h1>
                
                <p class="text-sm md:text-base text-slate-500 font-medium leading-relaxed max-w-2xl">
                    En **DISCAN** no vendemos seguros; somos sus asesores y defensores ante las ARS. Comparamos, diseñamos y negociamos pólizas médicas de salud colectiva, familiares y de lujo internacional para garantizarle la máxima cobertura al menor costo del mercado. **Nuestra asesoría técnica es 100% gratuita para usted.**
                </p>

                <!-- Dynamic Features Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl">
                    <div class="flex items-center gap-3 bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="ph-bold ph-money-wavy text-base"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Asesoría e Intermediación Gratuitas</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="ph-bold ph-handshake text-base"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Principales ARS del País Aliadas</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center shrink-0">
                            <i class="ph-bold ph-rocket text-base"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Envío de Carnets con SysSAFE</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <i class="ph-bold ph-headset text-base"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Soporte Médico & Reclamaciones 24/7</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="#contacto" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transition-all flex items-center gap-2">
                        <span>SOLICITAR COTIZACIÓN SIN COSTO</span>
                        <i class="ph-bold ph-arrow-right text-sm"></i>
                    </a>
                    <a href="#asesor" class="px-8 py-4 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-2xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2">
                        <span>PROBAR ASESOR DIGITAL</span>
                        <i class="ph-bold ph-sparkle text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Right Visual Interactive Advisor Card -->
            <div class="lg:col-span-5" id="asesor">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full blur-xl pointer-events-none"></div>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-blue-600 block">ASISTENTE VIRTUAL</span>
                            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Recomendador de Planes</h3>
                            <p class="text-xs text-slate-400 font-medium">Seleccione su perfil para analizar su cobertura ideal.</p>
                        </div>

                        <!-- Alpine Tabs selector -->
                        <div class="grid grid-cols-3 gap-1 bg-slate-100 p-1.5 rounded-xl">
                            <button @click="activePlan = 'corporate'" :class="activePlan === 'corporate' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="py-2 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all">
                                🏢 Empresa
                            </button>
                            <button @click="activePlan = 'family'" :class="activePlan === 'family' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="py-2 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all">
                                👨‍👩‍👧 Familiar
                            </button>
                            <button @click="activePlan = 'global'" :class="activePlan === 'global' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800'" class="py-2 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all">
                                ✈️ Global
                            </button>
                        </div>

                        <!-- Dynamic Content Panel -->
                        <div class="space-y-4 min-h-[180px] flex flex-col justify-between py-2">
                            
                            <!-- Corporate Info -->
                            <div x-show="activePlan === 'corporate'" class="space-y-3">
                                <h4 class="text-sm font-extrabold text-slate-800">Seguros Colectivos (Nóminas de 5+ Empleados)</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Diseño a la medida de planes médicos corporativos. Deducción de impuestos de nómina, tarifas grupales preferenciales, y ampliación de límites en tratamientos catastróficos.
                                </p>
                                <div class="grid grid-cols-2 gap-2 text-[10px] font-bold text-slate-600">
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Beneficios Fiscales</div>
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Cobertura Dental Amplia</div>
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Gestor Ejecutivo Dedicado</div>
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Logística SysSAFE Gratis</div>
                                </div>
                            </div>

                            <!-- Family Info -->
                            <div x-show="activePlan === 'family'" class="space-y-3" style="display: none;">
                                <h4 class="text-sm font-extrabold text-slate-800">Planes Médicos Familiares e Individuales</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Cobertura integral para el bienestar de su hogar. Consultas de especialidades, internamiento clínico en las redes más importantes del país y seguro dental incluido.
                                </p>
                                <div class="grid grid-cols-2 gap-2 text-[10px] font-bold text-slate-600">
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Libre Elección Clínica</div>
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Cobertura en Vacunas</div>
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Farmacia de Alta Gama</div>
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Reembolsos Ágiles</div>
                                </div>
                            </div>

                            <!-- Global Info -->
                            <div x-show="activePlan === 'global'" class="space-y-3" style="display: none;">
                                <h4 class="text-sm font-extrabold text-slate-800">Seguros Internacionales de Lujo</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Acceso exclusivo a los mejores hospitales del mundo (como el Baptist Health de Miami o la Clínica Universidad de Navarra). Cobertura catastrófica de millones de dólares.
                                </p>
                                <div class="grid grid-cols-2 gap-2 text-[10px] font-bold text-slate-600">
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Cobertura Global USD</div>
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Ambulancia Aérea</div>
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Segunda Opinión Global</div>
                                    <div class="flex items-center gap-1.5"><i class="ph-bold ph-check text-emerald-500"></i> Red Premium en USA</div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100">
                                <a href="#contacto" 
                                   @click="
                                     let select = document.getElementById('tipo_plan');
                                     if(activePlan === 'corporate') select.value = 'colectivo';
                                     if(activePlan === 'family') select.value = 'familiar';
                                     if(activePlan === 'global') select.value = 'internacional';
                                   "
                                   class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-black uppercase tracking-wider text-center transition-all block">
                                    Cotizar este perfil de salud
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Credibility Banner: Acredited ARS Allies -->
    <section class="py-12 bg-white border-t border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-center text-[10px] font-black uppercase tracking-widest text-slate-400 mb-8">
                Intermediarios oficiales y autorizados con todas las ARS líderes
            </p>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-8 items-center text-center">
                <div class="ars-logo-card flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS Humano</span>
                    <span class="text-[8px] font-black uppercase text-blue-500 tracking-widest">Red Platino</span>
                </div>
                <div class="ars-logo-card flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS Universal</span>
                    <span class="text-[8px] font-black uppercase text-blue-500 tracking-widest">Cobertura Total</span>
                </div>
                <div class="ars-logo-card flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">MAPFRE Salud</span>
                    <span class="text-[8px] font-black uppercase text-blue-500 tracking-widest">Alianza Global</span>
                </div>
                <div class="ars-logo-card flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS Reservas</span>
                    <span class="text-[8px] font-black uppercase text-blue-500 tracking-widest">Red Preferencial</span>
                </div>
                <div class="ars-logo-card flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS Monumental</span>
                    <span class="text-[8px] font-black uppercase text-blue-500 tracking-widest">Seguridad Médica</span>
                </div>
                <div class="ars-logo-card flex flex-col items-center">
                    <span class="text-lg font-black text-slate-700 tracking-tight">ARS CMD</span>
                    <span class="text-[8px] font-black uppercase text-blue-500 tracking-widest">Gremio Exclusivo</span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section (Quiénes Somos, Misión, Visión, Valores) -->
    <section id="nosotros" class="py-24 px-6 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <!-- Left Copwriting: Quiénes Somos -->
            <div class="lg:col-span-7 space-y-6">
                <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-full">QUIÉNES SOMOS</span>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 uppercase tracking-tight leading-tight">
                    Una Firma de Corretaje Médica Construida sobre el Servicio de Excelencia
                </h2>
                <p class="text-sm text-slate-500 leading-relaxed font-medium">
                    **DISCAN** nació con la firme convicción de que los seguros de salud no deben ser complejos ni burocráticos. Nos posicionamos como una empresa de servicios de intermediación integral que funciona como un puente de confianza entre el afiliado y las principales Administradoras de Riesgos de Salud (ARS).
                </p>
                <p class="text-sm text-slate-500 leading-relaxed font-medium">
                    Asesoramos de forma imparcial y transparente: analizamos los planes médicos, gestionamos reclamos médicos complejos, negociamos tarifas colectivas óptimas y proveemos soporte en momentos de emergencia médica. Todo ello con un equipo dedicado que garantiza respuestas inmediatas y empatía real.
                </p>

                <!-- Mission and Vision Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                    <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-2">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="ph-bold ph-compass text-lg"></i>
                        </div>
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Nuestra Misión</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Proteger la salud y estabilidad financiera de empresas y familias dominicanas, entregando intermediación de seguros médica transparente, ágil y a costo cero.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-sm space-y-2">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="ph-bold ph-eye text-lg"></i>
                        </div>
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Nuestra Visión</h4>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Ser la corredora de seguros de salud preferida del país, reconocida por nuestra innovación tecnológica logística y un estándar de servicio al cliente inigualable.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Corporate Values -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white p-8 rounded-[2.5rem] shadow-xl space-y-6 relative overflow-hidden">
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-600/10 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <h3 class="text-lg font-black uppercase tracking-wider">Nuestros Valores</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-600/20 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/10">
                                <i class="ph-bold ph-hand-eye text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold uppercase tracking-wide text-blue-300">Transparencia Radical</h4>
                                <p class="text-[11px] text-slate-300 mt-0.5">Mostramos siempre las coberturas reales, exclusiones y costos sin letra chica.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-600/20 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/10">
                                <i class="ph-bold ph-lightning text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold uppercase tracking-wide text-blue-300">Agilidad e Innovación</h4>
                                <p class="text-[11px] text-slate-300 mt-0.5">Optimizamos los tiempos de respuesta y procesos mediante tecnología integrada.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-600/20 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/10">
                                <i class="ph-bold ph-heart-beat text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold uppercase tracking-wide text-blue-300">Empatía y Servicio</h4>
                                <p class="text-[11px] text-slate-300 mt-0.5">Comprendemos que la salud es un tema sensible. Brindamos soporte humano de verdad.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Services Section -->
    <section id="servicios" class="py-24 px-6 bg-white border-t border-b border-slate-100">
        <div class="max-w-7xl mx-auto space-y-16">
            
            <div class="text-center max-w-xl mx-auto space-y-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-full">PLANES & COBERTURAS</span>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Soluciones de Salud para Cada Necesidad</h2>
                <p class="text-sm text-slate-400 font-medium">Analizamos y adaptamos carteras de seguros médicos con cobertura local e internacional adaptados a sus requerimientos específicos.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                <!-- Service 1: Corporativo -->
                <div class="premium-card p-8 rounded-3xl space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/50">
                            <i class="ph-bold ph-buildings text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Planes Médicos Colectivos</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Diseñamos planes de salud corporativos premium para sus colaboradores. Reducimos la siniestralidad, negociamos coberturas de maternidad extendida, subsidio por incapacidad y redes dentales ampliadas.
                        </p>
                    </div>
                    <a href="#contacto" @click="document.getElementById('tipo_plan').value = 'colectivo';" class="text-xs font-black uppercase tracking-wider text-blue-600 flex items-center gap-1.5 hover:underline pt-2">
                        <span>Ver propuesta corporativa</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- Service 2: Familiar -->
                <div class="premium-card p-8 rounded-3xl space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100/50">
                            <i class="ph-bold ph-users text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Planes Familiares & Locales</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Aseguramos la salud de su familia ante imprevistos o emergencias médicas. Acceso preferencial a consultas pediátricas, red nacional de clínicas, laboratorios clínicos prestigiosos y farmacias.
                        </p>
                    </div>
                    <a href="#contacto" @click="document.getElementById('tipo_plan').value = 'familiar';" class="text-xs font-black uppercase tracking-wider text-indigo-600 flex items-center gap-1.5 hover:underline pt-2">
                        <span>Explorar planes familiares</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- Service 3: Internacional -->
                <div class="premium-card p-8 rounded-3xl space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center border border-cyan-100/50">
                            <i class="ph-bold ph-globe text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Planes Globales de Lujo</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Acceso médico de élite sin fronteras. Tratamientos oncológicos y cardiovasculares complejos en los principales centros médicos de Estados Unidos y Europa. Cobertura en dólares de hasta USD $5M.
                        </p>
                    </div>
                    <a href="#contacto" @click="document.getElementById('tipo_plan').value = 'internacional';" class="text-xs font-black uppercase tracking-wider text-cyan-600 flex items-center gap-1.5 hover:underline pt-2">
                        <span>Ver cobertura global</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <!-- Service 4: Vida & Salud Preventiva -->
                <div class="premium-card p-8 rounded-3xl space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100/50">
                            <i class="ph-bold ph-heartbeat text-2xl"></i>
                        </div>
                        <h3 class="text-base font-extrabold uppercase text-slate-900 tracking-tight">Vida & Salud Preventiva</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Protegemos la estabilidad financiera de su familia en momentos difíciles. Seguros de vida colectivos e individuales, programas de bienestar preventivo y cobertura complementaria de accidentes.
                        </p>
                    </div>
                    <a href="#contacto" class="text-xs font-black uppercase tracking-wider text-emerald-600 flex items-center gap-1.5 hover:underline pt-2">
                        <span>Saber más de vida</span> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

            </div>

        </div>
    </section>

    <!-- Dynamic Differentiator: The SysSAFE Logistics Advantage -->
    <section id="ventaja" class="py-24 px-6 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <!-- Left description -->
            <div class="lg:col-span-6 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-[10px] font-black uppercase tracking-widest text-blue-400">
                    <i class="ph-bold ph-sparkle text-xs"></i> Ventaja Tecnológica Exclusiva
                </div>
                
                <h2 class="text-3xl md:text-4xl font-black text-white uppercase tracking-tight leading-tight">
                    Nuestra Logística Exclusiva: Pólizas y Carnets en sus Manos
                </h2>
                
                <p class="text-sm text-slate-400 leading-relaxed">
                    Uno de los mayores dolores de cabeza con los corredores de seguros tradicionales es la larga espera y el descontrol en la entrega física de sus carnets y contratos. 
                </p>
                <p class="text-sm text-slate-400 leading-relaxed">
                    En **DISCAN** solucionamos este desafío integrando de forma exclusiva **SysSAFE**, nuestro avanzado motor de logística y despacho digital de afiliados. Gracias a este módulo logístico, garantizamos auditoría e inmutabilidad en cada entrega.
                </p>

                <!-- Key logistic features -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="flex items-start gap-3 bg-slate-950/40 p-4 rounded-2xl border border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/10">
                            <i class="ph-bold ph-truck text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">Distribución Express</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Rutas logísticas automatizadas y mensajeros dedicados para entrega en oficinas o residencias.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 bg-slate-950/40 p-4 rounded-2xl border border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/10">
                            <i class="ph-bold ph-signature text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">Acuse Físico Auditado</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Cierre del ciclo con firma digitalizada, garantizando que el expediente llegó a manos de su colaborador.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 bg-slate-950/40 p-4 rounded-2xl border border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/10">
                            <i class="ph-bold ph-arrow-counter-clockwise text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">Sincronización en Tiempo Real</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Recursos Humanos y Directores Administrativos pueden monitorear el estatus logístico desde el portal.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 bg-slate-950/40 p-4 rounded-2xl border border-slate-800">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center shrink-0 border border-blue-500/10">
                            <i class="ph-bold ph-warning-octagon text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">Control de SLA Riguroso</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">Semáforo digital integrado de 20 días que alerta de forma predictiva cualquier retraso.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Graphic card / Mockup representation of SysSAFE -->
            <div class="lg:col-span-6 relative">
                <div class="bg-slate-950 p-8 rounded-[2.5rem] border border-slate-800 shadow-2xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-ping"></span>
                            <span class="text-xs font-black uppercase text-slate-200 tracking-wider">SysSAFE Logistics System</span>
                        </div>
                        <span class="text-[9px] font-mono text-slate-500 font-bold uppercase">Tecnología de Intermediación</span>
                    </div>

                    <!-- Interactive visualization list -->
                    <div class="space-y-5 relative before:absolute before:top-4 before:bottom-4 before:left-5 before:border-l before:border-slate-800 before:border-dashed">
                        
                        <div class="flex items-center gap-4 relative">
                            <div class="w-10 h-10 rounded-full bg-blue-950 border border-blue-800 text-blue-400 flex items-center justify-center z-10 shrink-0">
                                <i class="ph-bold ph-database text-sm"></i>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-200">1. Sincronización e Ingesta Remota</h5>
                                <p class="text-[10px] text-slate-400">Traspasos autorizados por la ARS se ingresan al sistema al instante.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 relative">
                            <div class="w-10 h-10 rounded-full bg-blue-950 border border-blue-800 text-blue-400 flex items-center justify-center z-10 shrink-0">
                                <i class="ph-bold ph-package text-sm"></i>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-200">2. Impresión y Empaque de Expedientes</h5>
                                <p class="text-[10px] text-slate-400">Emisión física de carnets con semáforo SLA de 20 días activos.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 relative">
                            <div class="w-10 h-10 rounded-full bg-emerald-950 border border-emerald-800 text-emerald-400 flex items-center justify-center z-10 shrink-0">
                                <i class="ph-bold ph-check-square text-sm"></i>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-200">3. Acuse de Recibo Físico Auditado</h5>
                                <p class="text-[10px] text-slate-400">Firma física de entrega y cierre inmutable en base de datos.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Multi-Step Interactive Quote Form Section -->
    <section id="contacto" class="py-24 px-6 bg-slate-50 relative overflow-hidden" x-data="{ 
        step: 1, 
        profile: 'empresa', 
        name: '', 
        phone: '', 
        email: '', 
        company: '', 
        arsInterest: 'cualquiera',
        timePreference: 'inmediato',
        submitForm() {
            Swal.fire({
                title: '¡Solicitud Recibida!',
                text: 'Su solicitud de cotización comercial ha sido procesada con éxito. Un corredor de seguros de DISCAN se pondrá en contacto con usted por ' + (this.timePreference === 'whatsapp' ? 'WhatsApp' : 'teléfono') + ' a la brevedad.',
                icon: 'success',
                confirmButtonColor: '#2563eb'
            });
            this.step = 1;
            this.name = '';
            this.phone = '';
            this.email = '';
            this.company = '';
            this.arsInterest = 'cualquiera';
            this.timePreference = 'inmediato';
        }
    }">
        <div class="max-w-4xl mx-auto space-y-12">
            
            <div class="text-center max-w-xl mx-auto space-y-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-full">COTIZACIÓN EXPRESS</span>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Diseñe su Propuesta de Salud</h2>
                <p class="text-sm text-slate-400 font-medium">Complete este rápido asistente comercial interactivo y obtenga una comparativa de las mejores ARS sin costo.</p>
            </div>

            <!-- Steps Progress Bar -->
            <div class="flex items-center justify-center gap-3 max-w-md mx-auto">
                <div class="flex items-center gap-1.5">
                    <span :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'" class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-all">1</span>
                    <span class="text-[10px] font-bold text-slate-700">Perfil</span>
                </div>
                <div class="w-12 h-0.5 bg-slate-200 relative"><div :class="step >= 2 ? 'w-full' : 'w-0'" class="absolute inset-0 bg-blue-600 transition-all duration-300"></div></div>
                <div class="flex items-center gap-1.5">
                    <span :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'" class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-all">2</span>
                    <span class="text-[10px] font-bold text-slate-700">Contacto</span>
                </div>
                <div class="w-12 h-0.5 bg-slate-200 relative"><div :class="step >= 3 ? 'w-full' : 'w-0'" class="absolute inset-0 bg-blue-600 transition-all duration-300"></div></div>
                <div class="flex items-center gap-1.5">
                    <span :class="step >= 3 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'" class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-all">3</span>
                    <span class="text-[10px] font-bold text-slate-700">Preferencias</span>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white p-8 md:p-12 rounded-[2.5rem] border border-slate-200/80 shadow-xl max-w-2xl mx-auto">
                <form @submit.preventDefault="submitForm()">
                    
                    <!-- STEP 1: Plan Profile Selection -->
                    <div x-show="step === 1" class="space-y-6">
                        <div class="text-center space-y-1">
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">¿Qué tipo de cobertura de salud necesita?</h3>
                            <p class="text-xs text-slate-400">Seleccione el perfil comercial que mejor se adapte a su caso.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Option Corporate -->
                            <div @click="profile = 'empresa'" :class="profile === 'empresa' ? 'border-blue-600 bg-blue-50/20 ring-2 ring-blue-600/10' : 'border-slate-200'" class="p-5 rounded-2xl border-2 cursor-pointer hover:border-blue-400 transition-all space-y-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i class="ph-bold ph-buildings text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Mi Empresa</h4>
                                    <p class="text-[10px] text-slate-400 mt-1">Seguro médico colectivo para nóminas y empleados.</p>
                                </div>
                            </div>

                            <!-- Option Family -->
                            <div @click="profile = 'familiar'" :class="profile === 'familiar' ? 'border-blue-600 bg-blue-50/20 ring-2 ring-blue-600/10' : 'border-slate-200'" class="p-5 rounded-2xl border-2 cursor-pointer hover:border-blue-400 transition-all space-y-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <i class="ph-bold ph-users text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Mi Familia</h4>
                                    <p class="text-[10px] text-slate-400 mt-1">Planes médicos nacionales familiares.</p>
                                </div>
                            </div>

                            <!-- Option International -->
                            <div @click="profile = 'internacional'" :class="profile === 'internacional' ? 'border-blue-600 bg-blue-50/20 ring-2 ring-blue-600/10' : 'border-slate-200'" class="p-5 rounded-2xl border-2 cursor-pointer hover:border-blue-400 transition-all space-y-3">
                                <div class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                                    <i class="ph-bold ph-globe text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">Internacional</h4>
                                    <p class="text-[10px] text-slate-400 mt-1">Planes globales catastróficos y de lujo.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-slate-100">
                            <button type="button" @click="step = 2" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-1.5">
                                <span>Continuar</span>
                                <i class="ph-bold ph-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: Contact Information -->
                    <div x-show="step === 2" class="space-y-6" style="display: none;">
                        <div class="text-center space-y-1">
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Déjenos sus Datos de Contacto</h3>
                            <p class="text-xs text-slate-400">Esta información se utilizará únicamente para enviarle su propuesta.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label for="form_nombre" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Nombre Completo</label>
                                <input type="text" id="form_nombre" x-model="name" placeholder="Ej. Juan Pérez" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-0 rounded-xl text-xs font-medium transition-all">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label for="form_telefono" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Teléfono de Contacto (con WhatsApp)</label>
                                    <input type="tel" id="form_telefono" x-model="phone" placeholder="Ej. 809-555-0199" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-0 rounded-xl text-xs font-medium transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label for="form_email" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Correo Electrónico</label>
                                    <input type="email" id="form_email" x-model="email" placeholder="Ej. juan@ejemplo.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-0 rounded-xl text-xs font-medium transition-all">
                                </div>
                            </div>

                            <!-- Conditionally show company field if profile is Empresa -->
                            <div class="space-y-1" x-show="profile === 'empresa'">
                                <label for="form_empresa" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Nombre de su Empresa</label>
                                <input type="text" id="form_empresa" x-model="company" placeholder="Ej. DISCAN SRL" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-0 rounded-xl text-xs font-medium transition-all">
                            </div>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-slate-100">
                            <button type="button" @click="step = 1" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-black uppercase tracking-wider transition-all">
                                Atrás
                            </button>
                            <button type="button" @click="if(name && phone && email) { step = 3; } else { Swal.fire('Error', 'Por favor complete todos los campos.', 'error'); }" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-1.5">
                                <span>Continuar</span>
                                <i class="ph-bold ph-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: Broker Preferences & ARS -->
                    <div x-show="step === 3" class="space-y-6" style="display: none;">
                        <div class="text-center space-y-1">
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-tight">Preferencias de Asesoría</h3>
                            <p class="text-xs text-slate-400">Personalice las aseguradoras y formas de contacto de su interés.</p>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label for="tipo_plan" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Tipo de Seguro Requerido (Confirmar)</label>
                                <select id="tipo_plan" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-0 rounded-xl text-xs text-slate-700 font-medium transition-all">
                                    <option value="colectivo" :selected="profile === 'empresa'">Plan Médico Colectivo (Para mi Empresa)</option>
                                    <option value="familiar" :selected="profile === 'familiar'">Plan Médico Familiar / Individual</option>
                                    <option value="internacional" :selected="profile === 'internacional'">Plan de Salud Internacional Premium</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="form_ars" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">¿Tiene alguna ARS de interés preferente?</label>
                                <select id="form_ars" x-model="arsInterest" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-600 focus:ring-0 rounded-xl text-xs text-slate-700 font-medium transition-all">
                                    <option value="cualquiera">No tengo preferencia (Quiero comparar todas)</option>
                                    <option value="humano">ARS Humano</option>
                                    <option value="universal">ARS Universal</option>
                                    <option value="reservas">ARS Reservas</option>
                                    <option value="monumental">ARS Monumental</option>
                                    <option value="cmd">ARS CMD</option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="form_metodo" class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Preferencia de Contacto</label>
                                <div class="grid grid-cols-2 gap-4 pt-1">
                                    <div @click="timePreference = 'whatsapp'" :class="timePreference === 'whatsapp' ? 'border-blue-600 bg-blue-50/20' : 'border-slate-200'" class="p-3.5 border rounded-xl flex items-center gap-2 cursor-pointer transition-all">
                                        <i class="ph-bold ph-whatsapp-logo text-xl text-emerald-500"></i>
                                        <span class="text-xs font-bold text-slate-700">WhatsApp</span>
                                    </div>
                                    <div @click="timePreference = 'llamada'" :class="timePreference === 'llamada' ? 'border-blue-600 bg-blue-50/20' : 'border-slate-200'" class="p-3.5 border rounded-xl flex items-center gap-2 cursor-pointer transition-all">
                                        <i class="ph-bold ph-phone text-xl text-blue-500"></i>
                                        <span class="text-xs font-bold text-slate-700">Llamada</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between pt-4 border-t border-slate-100">
                            <button type="button" @click="step = 2" class="px-5 py-3 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-black uppercase tracking-wider transition-all">
                                Atrás
                            </button>
                            <button type="submit" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md shadow-blue-600/10 transition-all flex items-center gap-1.5">
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
    <section id="faq" class="py-24 px-6 bg-white border-t border-b border-slate-100" x-data="{ activeFaq: null }">
        <div class="max-w-4xl mx-auto space-y-12">
            
            <div class="text-center max-w-xl mx-auto space-y-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-3 py-1 rounded-full">PREGUNTAS FRECUENTES</span>
                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Preguntas Frecuentes de Corretaje</h2>
                <p class="text-sm text-slate-400 font-medium">Respuestas rápidas para aclarar cómo funciona la asesoría de seguros a través de un corredor oficial.</p>
            </div>

            <div class="space-y-4 max-w-3xl mx-auto">
                <!-- FAQ Item 1 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                    <button @click="activeFaq === 1 ? activeFaq = null : activeFaq = 1" class="w-full p-6 text-left flex items-center justify-between gap-4 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wide">¿La intermediación y asesoría de DISCAN tiene algún costo adicional para mí?</span>
                        <i :class="activeFaq === 1 ? 'ph-bold ph-caret-up' : 'ph-bold ph-caret-down'" class="text-slate-500 text-base transition-transform duration-300"></i>
                    </button>
                    <div x-show="activeFaq === 1" x-transition.duration.300ms class="p-6 bg-white border-t border-slate-100 text-xs text-slate-500 leading-relaxed font-medium">
                        **No, en absoluto.** Por ley, las Administradoras de Riesgos de Salud (ARS) pagan las comisiones de corretaje directo a las firmas autorizadas. El costo de su póliza médica será exactamente el mismo (o menor debido a nuestras negociaciones colectivas) que si contratara directamente con la aseguradora. La ventaja es que con DISCAN obtiene un equipo de expertos que defiende sus derechos y realiza toda la gestión logística sin costo.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                    <button @click="activeFaq === 2 ? activeFaq = null : activeFaq = 2" class="w-full p-6 text-left flex items-center justify-between gap-4 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wide">Si mi empresa ya cuenta con un seguro de salud directo, ¿puedo nombrar a DISCAN como mi broker?</span>
                        <i :class="activeFaq === 2 ? 'ph-bold ph-caret-up' : 'ph-bold ph-caret-down'" class="text-slate-500 text-base transition-transform duration-300"></i>
                    </button>
                    <div x-show="activeFaq === 2" x-transition.duration.300ms class="p-6 bg-white border-t border-slate-100 text-xs text-slate-500 leading-relaxed font-medium">
                        **Sí, por supuesto.** Nombrar a DISCAN como su corredor es un trámite sumamente sencillo que solo requiere la firma de una carta de designación oficial. Sus coberturas, tarifas y red médica con la ARS actual permanecerán exactamente iguales, pero su departamento de Recursos Humanos ganará un aliado estratégico para manejar reclamos, exclusiones y todo el despacho express automatizado con **SysSAFE**.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                    <button @click="activeFaq === 3 ? activeFaq = null : activeFaq = 3" class="w-full p-6 text-left flex items-center justify-between gap-4 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wide">¿Con qué Administradoras de Riesgos de Salud (ARS) trabaja DISCAN?</span>
                        <i :class="activeFaq === 3 ? 'ph-bold ph-caret-up' : 'ph-bold ph-caret-down'" class="text-slate-500 text-base transition-transform duration-300"></i>
                    </button>
                    <div x-show="activeFaq === 3" x-transition.duration.300ms class="p-6 bg-white border-t border-slate-100 text-xs text-slate-500 leading-relaxed font-medium">
                        Trabajamos en estrecha colaboración con todas las ARS autorizadas en la República Dominicana, incluyendo **ARS Humano, ARS Universal, MAPFRE Salud, ARS Reservas, ARS Monumental, ARS Futuro y ARS CMD (Colegio Médico Dominicano)**. Esto nos permite comparar de forma objetiva todo el mercado y armar propuestas comparativas transparentes.
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                    <button @click="activeFaq === 4 ? activeFaq = null : activeFaq = 4" class="w-full p-6 text-left flex items-center justify-between gap-4 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <span class="text-xs font-extrabold uppercase text-slate-800 tracking-wide">¿Cómo funciona exactamente el sistema SysSAFE para la entrega de mis carnets?</span>
                        <i :class="activeFaq === 4 ? 'ph-bold ph-caret-up' : 'ph-bold ph-caret-down'" class="text-slate-500 text-base transition-transform duration-300"></i>
                    </button>
                    <div x-show="activeFaq === 4" x-transition.duration.300ms class="p-6 bg-white border-t border-slate-100 text-xs text-slate-500 leading-relaxed font-medium">
                        **SysSAFE** es nuestra plataforma patentada de control logístico. Cuando la ARS emite sus carnets físicos o contratos, el sistema genera de forma automática un código de barra de rastreo único, asignando el expediente a una ruta óptima de mensajería. Usted podrá ver en vivo si sus carnets están "Impresos", "En Ruta" o "Completados", y cada entrega finaliza obligatoriamente con una firma digital en dispositivo móvil que constituye el **acuse de recibo conforme e inmutable**.
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer: Commercial & Contact -->
    <footer class="py-16 px-6 bg-slate-950 text-slate-400 border-t border-slate-900 relative">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 text-sm pb-12 border-b border-slate-900">
            
            <!-- Logo area -->
            <div class="space-y-4">
                <a href="#" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                        <i class="ph-bold ph-shield-plus text-xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-black tracking-tight text-white leading-none">DISCAN</span>
                        <span class="text-[8px] font-black uppercase tracking-widest text-blue-500 mt-1 leading-none">Corredores de Seguros</span>
                    </div>
                </a>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Firma registrada y autorizada de corretaje de seguros médicos colectivos, familiares e internacionales. Brindamos servicios de intermediación médica de excelencia.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4">
                <h4 class="text-xs font-black uppercase tracking-widest text-white">Planes de Salud</h4>
                <ul class="space-y-2.5 text-xs text-slate-400 font-medium">
                    <li><a href="#servicios" class="hover:text-white transition-colors">Seguros Médicos Corporativos</a></li>
                    <li><a href="#servicios" class="hover:text-white transition-colors">Seguros Médicos Familiares</a></li>
                    <li><a href="#servicios" class="hover:text-white transition-colors">Seguros Internacionales de Lujo</a></li>
                    <li><a href="#ventaja" class="hover:text-white transition-colors">Logística SysSAFE Carnets</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="space-y-4">
                <h4 class="text-xs font-black uppercase tracking-widest text-white">Contacto y Soporte</h4>
                <ul class="space-y-2.5 text-xs text-slate-400 font-medium">
                    <li class="flex items-center gap-2">
                        <i class="ph-bold ph-phone text-blue-500"></i>
                        <span>(809) 555-0199</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="ph-bold ph-envelope text-blue-500"></i>
                        <span>info@discan.cloud</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="ph-bold ph-map-pin text-blue-500"></i>
                        <span>Av. Winston Churchill, Santo Domingo, R.D.</span>
                    </li>
                </ul>
            </div>

            <!-- Portal Dashboard Direct Access -->
            <div class="space-y-4">
                <h4 class="text-xs font-black uppercase tracking-widest text-white">Portal Autorizado</h4>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Acceso exclusivo para promotores, mensajeros, personal logístico de SAFESURE y auditores de ARS CMD.
                </p>
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex px-4 py-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                        Ingresar al Portal
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex px-4 py-2.5 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all">
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
                <span class="font-mono text-slate-700">v3.6.0</span>
            </div>
        </div>
    </footer>

    <!-- SweetAlert2 (Para el feedback interactivo del formulario) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
