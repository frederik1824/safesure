<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso Seguro - Safesure Platform</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-web-ss.png') }}">
    
    <!-- Modern Corporate Typography -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'slate-dark': '#0B0F1A',
                        'slate-card': '#111827',
                        'ss-blue': '#3B82F6',
                        'ss-navy': '#1E293B',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'display': ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #0B0F1A;
            background-image: 
                radial-gradient(circle at 2px 2px, rgba(255,255,255,0.02) 1px, transparent 0);
            background-size: 32px 32px;
        }

        .auth-card {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-pro {
            background: #1F2937;
            border: 1px solid #374151;
            color: white;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-pro:focus {
            border-color: #3B82F6;
            background: #111827;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 antialiased">
    
    <!-- Subtle Ambient Glow -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-indigo-600/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-4xl w-full grid lg:grid-cols-2 auth-card rounded-2xl overflow-hidden relative z-10">
        
        <!-- Sidebar: Info & Branding -->
        <div class="hidden lg:flex p-12 flex-col justify-between bg-[#0F172A] border-r border-white/5 relative">
            <div class="relative z-10">
                <img src="{{ asset('images/logo-web-ss.png') }}" class="h-10 w-auto mb-16 grayscale brightness-200" alt="Safesure">
                
                <h1 class="text-4xl font-display font-bold text-white leading-tight mb-6">
                    Infraestructura <br/>
                    <span class="text-blue-500">Logística de Salud</span>
                </h1>
                
                <p class="text-slate-400 text-lg leading-relaxed max-w-xs">
                    Gestión crítica de afiliados y carnets con integridad de datos garantizada.
                </p>
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-white/5 border border-white/5 backdrop-blur-sm">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <i class="ph-bold ph-shield-check text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white">Seguridad Activa</p>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest">Cifrado de grado médico</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Area -->
        <div class="p-8 lg:p-16 flex flex-col justify-center">
            <div class="lg:hidden mb-12 flex justify-center">
                <img src="{{ asset('images/logo-web-ss.png') }}" class="h-10 w-auto" alt="Safesure">
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-display font-bold text-white mb-2">Iniciar Sesión</h2>
                <p class="text-slate-500 font-medium">Ingresa tus credenciales autorizadas</p>
            </div>

            @if(session('status'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm font-medium flex items-center gap-3">
                    <i class="ph-bold ph-check-circle"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">Correo Electrónico</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-user text-slate-500 text-lg"></i>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                            class="input-pro w-full rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium" 
                            placeholder="nombre@safesure.com">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-400 font-medium mt-1 ml-1 flex items-center gap-1">
                            <i class="ph-fill ph-warning-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-blue-500 hover:text-blue-400 transition-colors">Olvidé mi clave</a>
                        @endif
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-lock-key text-slate-500 text-lg"></i>
                        </span>
                        <input id="password" type="password" name="password" required 
                            class="input-pro w-full rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium" 
                            placeholder="••••••••••••">
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-400 font-medium mt-1 ml-1 flex items-center gap-1">
                            <i class="ph-fill ph-warning-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center px-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-0 focus:ring-offset-0 transition-all">
                        <span class="ml-2 text-xs font-medium text-slate-500 group-hover:text-slate-300 transition-colors">Recordar este dispositivo</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary w-full text-white font-bold py-4 rounded-xl shadow-lg flex items-center justify-center gap-2 active:scale-[0.99]">
                    Entrar al Panel
                    <i class="ph-bold ph-arrow-right"></i>
                </button>
            </form>

            <div class="mt-12 pt-8 border-t border-white/5 text-center">
                <p class="text-[10px] text-slate-600 font-bold uppercase tracking-[0.2em]">
                    © {{ date('Y') }} Safesure Logistics Group
                </p>
            </div>
        </div>
    </div>
</body>
</html>
