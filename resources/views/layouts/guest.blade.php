<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
             * { font-family: 'Inter', sans-serif; }
            .animate-float { animation: float 6s ease-in-out infinite; }
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0px); }
            }
        </style>
    </head>
    <body class="font-sans text-slate-900 antialiased h-full">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            
            <!-- Background Blobs -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
                <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-500/10 rounded-full blur-[100px] animate-float"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-cyan-500/10 rounded-full blur-[100px] animate-float" style="animation-delay: 2s"></div>
            </div>

            <div class="relative z-10 flex flex-col items-center mb-6">
                <a href="/" class="flex items-center gap-3 mb-2">
                     <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
                    </div>
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Aisat Guidance Portal</h1>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-widest">Administrator Access</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white shadow-xl border border-slate-200 rounded-3xl overflow-hidden relative z-10">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center relative z-10">
                 <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    &copy; {{ date('Y') }} Aisat Guidance Counseling
                </p>
            </div>
        </div>
    </body>
</html>
