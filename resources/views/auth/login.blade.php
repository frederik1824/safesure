<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso - SysSAFE Carnet</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-web-ss.png') }}">
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#00346f',
                        'accent': '#01579b',
                    },
                    fontFamily: {
                        'sans': ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .mesh-bg {
            background-color: #0c1222;
            background-image: 
                radial-gradient(at 0% 0%, hsla(220,100%,15%,1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(200,100%,20%,1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(220,100%,10%,1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(240,100%,15%,1) 0px, transparent 50%),
                radial-gradient(at 50% 50%, hsla(210,100%,25%,1) 0px, transparent 50%);
            background-attachment: fixed;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .input-glass:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #3b82f6;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="mesh-bg min-h-screen flex items-center justify-center p-6 antialiased overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-accent/20 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-5xl w-full grid lg:grid-cols-2 glass-card rounded-[3rem] shadow-[0_32px_64px_-15px_rgba(0,0,0,0.5)] overflow-hidden relative z-10">
        
        <!-- Branding Section -->
        <div class="hidden lg:flex p-16 flex-col justify-between relative overflow-hidden border-r border-white/5 bg-gradient-to-br from-white/5 to-transparent">
            <div class="relative z-10">
                <div class="mb-20">
                    <img src="{{ asset('images/logo-web-ss.png') }}" class="h-16 w-auto" alt="SafeSure Logo">
                </div>

                <div class="space-y-8">
                    <h2 class="text-5xl font-extrabold text-white leading-[1.1] tracking-tight">
                        La potencia de la <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">logística moderna</span> en tus manos.
                    </h2>
                    <p class="text-slate-400 text-xl font-medium leading-relaxed max-w-sm">
                        Plataforma inteligente de gestión de afiliados, reportes y logística en tiempo real.
                    </p>
                </div>
            </div>

            <div class="relative z-10">
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/10 inline-flex flex-col gap-4">
                    <img src="{{ asset('images/logo-web-ss.png') }}" class="h-10 w-auto object-contain" alt="SafeSure Logo">
                    <div class="h-px bg-white/10 w-full"></div>
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-3">
                            <div class="w-8 h-8 rounded-full border-2 border-slate-900 bg-blue-500"></div>
                            <div class="w-8 h-8 rounded-full border-2 border-slate-900 bg-indigo-500"></div>
                            <div class="w-8 h-8 rounded-full border-2 border-slate-900 bg-emerald-500"></div>
                        </div>
                        <p class="text-xs font-bold text-slate-300">Monitorea tus afiliados hoy</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Form Section -->
        <div class="p-10 lg:p-20 flex flex-col justify-center bg-white/[0.02]">
            <div class="mb-12 lg:hidden flex items-center justify-center">
                <img src="{{ asset('images/logo-web-ss.png') }}" class="h-12 w-auto" alt="SafeSure Logo">
            </div>

            <div class="mb-12">
                <h3 class="text-4xl font-extrabold text-white mb-3">Bienvenido</h3>
                <p class="text-slate-500 font-medium text-lg">Ingresa para acceder al panel de control.</p>
            </div>

            @if(session('status'))
                <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold flex items-center gap-3">
                    <i class="ph-bold ph-check-circle text-lg"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <div class="space-y-2 group">
                    <label for="email" class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-500 ml-4 transition-colors group-focus-within:text-blue-400">Identificación</label>
                    <div class="relative">
                        <i class="ph ph-envelope-simple absolute left-5 top-1/2 -translate-y-1/2 text-slate-500 text-xl transition-colors group-focus-within:text-blue-400"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                            class="input-glass w-full rounded-2xl pl-14 pr-6 py-4.5 text-[1rem] font-semibold outline-none ring-0 focus:ring-0" 
                            placeholder="usuario@dominio.com">
                    </div>
                    @error('email')
                        <p class="text-[0.7rem] text-rose-400 font-bold mt-2 ml-4 flex items-center gap-1">
                            <i class="ph-bold ph-warning-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2 group">
                    <div class="flex justify-between items-center px-4">
                        <label for="password" class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-500 transition-colors group-focus-within:text-blue-400">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[0.65rem] font-bold text-slate-400 hover:text-white transition-colors">¿Problemas de acceso?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <i class="ph ph-lock-key absolute left-5 top-1/2 -translate-y-1/2 text-slate-500 text-xl transition-colors group-focus-within:text-blue-400"></i>
                        <input id="password" type="password" name="password" required 
                            class="input-glass w-full rounded-2xl pl-14 pr-6 py-4.5 text-[1rem] font-semibold outline-none ring-0 focus:ring-0" 
                            placeholder="••••••••••••">
                    </div>
                    @error('password')
                        <p class="text-[0.7rem] text-rose-400 font-bold mt-2 ml-4 flex items-center gap-1">
                            <i class="ph-bold ph-warning-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex items-center justify-between px-4">
                    <label class="flex items-center cursor-pointer group/check">
                        <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-white/10 bg-white/5 text-blue-500 focus:ring-0 transition-all cursor-pointer">
                        <span class="ml-3 text-xs font-bold text-slate-500 group-hover/check:text-slate-300 transition-colors">Mantener sesión activa</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-extrabold py-5 rounded-[1.25rem] shadow-[0_20px_40px_-10px_rgba(59,130,246,0.5)] transition-all flex items-center justify-center gap-3 active:scale-[0.98] text-lg">
                    Acceder al Sistema
                    <i class="ph-bold ph-arrow-right"></i>
                </button>
            </form>

            <!-- Refined simple version footer -->
            <div class="mt-16 text-center">
                <div class="flex items-center justify-center gap-2 text-slate-600 text-[0.7rem] font-bold uppercase tracking-widest">
                    <span>SysSAFE Carnet</span>
                    <span class="w-1 h-1 bg-slate-800 rounded-full"></span>
                    <span>Plataforma Segura</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
