<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} | Portal Entry</title>

        <!-- Scripts & Styles -->
        @vite('resources/css/app.css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
        
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

            :root {
                --primary: 79, 70, 229; /* Indigo 600 */
            }

            * {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
            }

            .animate-float {
                animation: float 6s ease-in-out infinite;
            }

            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0px); }
            }
        </style>
    </head>
    <body class="h-full bg-slate-50 text-slate-900 antialiased flex flex-col">
        
        <!-- Header -->
        <header class="h-20 flex items-center justify-between px-8 bg-white border-b border-slate-200 sticky top-0 z-50">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="fa-solid fa-graduation-cap text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-slate-900 leading-none">Aisat Guidance</h1>
                    <span class="text-[10px] uppercase tracking-wider text-indigo-600 font-bold">Portal Entry</span>
                </div>
            </div>

            @if (Route::has('login'))
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-sm font-bold transition-all">
                            <i class="fa-solid fa-gauge-high mr-2"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="font-bold text-slate-600 hover:text-indigo-600 transition-colors text-sm">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 transition-all">
                                Get Started <i class="fa-solid fa-arrow-right ml-2"></i>
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto w-full p-8 space-y-12">

            <!-- Hero Section -->
            <div class="relative overflow-hidden bg-white border border-slate-200 rounded-3xl p-12 shadow-xl mb-12">
                <div class="relative z-10 max-w-3xl">
                    <div class="mb-4"></div>

                    <h1 class="text-5xl md:text-6xl font-black text-slate-900 tracking-tight leading-tight mb-6">
                        Welcome to <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-cyan-500">AISAT College Dasmariñas</span>
                    </h1>
                    
                    <!-- Enrollment Indication (Text only, no button) -->
                    <div class="inline-block bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-8">
                        <p class="text-indigo-800 font-bold text-lg flex items-center gap-2">
                            <i class="fa-solid fa-school-flag"></i>
                            Enroll Here at AISAT College Dasmariñas
                        </p>
                        <p class="text-indigo-600 text-sm mt-1">Admissions are open. Visit our campus to enroll.</p>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold shadow-lg flex items-center gap-3 hover:scale-105 transition-transform">
                                <i class="fa-solid fa-gauge-high"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-500/30 flex items-center gap-3 hover:scale-105 transition-transform hover:bg-indigo-500">
                                <i class="fa-solid fa-right-to-bracket"></i> Student Portal Login
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Abstract Visuals -->
                <div class="absolute top-0 right-0 w-1/2 h-full hidden md:block opacity-50 pointer-events-none">
                     <div class="absolute top-1/2 right-24 w-64 h-64 bg-indigo-400/20 rounded-full blur-3xl animate-float"></div>
                     <div class="absolute top-24 right-48 w-48 h-48 bg-cyan-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s"></div>
                </div>
            </div>
            
            <!-- About AISAT Section -->
            <div class="mb-16 text-center max-w-4xl mx-auto">
                <h2 class="text-3xl font-black text-slate-900 mb-4">About AISAT</h2>
                <p class="text-lg text-slate-600 leading-relaxed">
                    AISAT College Dasmariñas is dedicated to providing quality education in technology, business, and sciences. We cultivate an environment of excellence, preparing students for successful careers in their chosen fields through practical training and academic rigor.
                </p>
            </div>

            <!-- Features Grid (Programs) -->
            <div class="space-y-4 mb-12">
                <h3 class="text-xl font-bold text-slate-900 px-2 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-book-open-reader text-indigo-500"></i> Academic Programs
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- 1. Computer Science -->
                    <div class="group bg-white border border-slate-200 p-6 rounded-2xl hover:border-indigo-300 hover:shadow-md transition-all cursor-default">
                        <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-code text-indigo-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">Computer Science</h3>
                        <p class="text-sm text-slate-500 font-medium">Software Development, Programming, and IT Solutions.</p>
                    </div>

                    <!-- 2. Tourism -->
                    <div class="group bg-white border border-slate-200 p-6 rounded-2xl hover:border-indigo-300 hover:shadow-md transition-all cursor-default">
                        <div class="w-12 h-12 bg-cyan-50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                             <i class="fa-solid fa-plane-up text-cyan-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">Tourism</h3>
                        <p class="text-sm text-slate-500 font-medium">Travel Management, Hospitality, and Global Tourism.</p>
                    </div>

                    <!-- 3. Office Administration -->
                    <div class="group bg-white border border-slate-200 p-6 rounded-2xl hover:border-indigo-300 hover:shadow-md transition-all cursor-default">
                         <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                             <i class="fa-solid fa-briefcase text-rose-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">Office Administration</h3>
                        <p class="text-sm text-slate-500 font-medium">Corporate Management, Administrative Skills, and Operations.</p>
                    </div>

                    <!-- 4. Accounting -->
                    <div class="group bg-white border border-slate-200 p-6 rounded-2xl hover:border-indigo-300 hover:shadow-md transition-all cursor-default">
                        <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                             <i class="fa-solid fa-calculator text-amber-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">Accounting</h3>
                        <p class="text-sm text-slate-500 font-medium">Financial Management, Bookkeeping, and Business Analysis.</p>
                    </div>
                    
                    <!-- 5. Criminology -->
                    <div class="group bg-white border border-slate-200 p-6 rounded-2xl hover:border-indigo-300 hover:shadow-md transition-all cursor-default">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                             <i class="fa-solid fa-scale-balanced text-emerald-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">Criminology</h3>
                        <p class="text-sm text-slate-500 font-medium">Law Enforcement, Criminal Justice, and Public Safety.</p>
                    </div>
                    
                    <!-- Senior High School -->
                     <div class="group bg-white border border-slate-200 p-6 rounded-2xl hover:border-indigo-300 hover:shadow-md transition-all cursor-default">
                        <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                             <i class="fa-solid fa-user-graduate text-purple-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-indigo-600 transition-colors">Senior High School</h3>
                        <p class="text-sm text-slate-500 font-medium">Grade 11 & 12 Academic and Technical-Vocational Tracks.</p>
                    </div>
                </div>
            </div>



            <!-- ATTENDANCE TAP SECTION -->
            <div class="bg-indigo-900 rounded-[3rem] p-12 text-center text-white relative overflow-hidden shadow-2xl shadow-indigo-500/40">
                <div class="relative z-10 max-w-xl mx-auto">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 rounded-3xl mb-6 backdrop-blur-md border border-white/20 animate-pulse">
                        <i class="fa-solid fa-id-card text-4xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-black mb-4 uppercase tracking-tighter">Portal Entry Scanner</h2>
                    <p class="text-indigo-200 font-medium mb-8">Please tap your RFID card on the scanner to record your entry or exit.</p>
                    
                    <div id="tap-status" class="hidden animate-bounce bg-white/20 backdrop-blur-xl border border-white/30 rounded-2xl p-6 mb-8 text-white font-black uppercase tracking-widest text-sm">
                        Processing card...
                    </div>

                    <!-- Hidden Input for Scanner -->
                    <input type="text" id="rfid_input" class="opacity-0 absolute" autofocus autocomplete="off">
                    
                    <div class="flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-indigo-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Scanner Ready & Active
                    </div>
                </div>

                <!-- Decorative circles -->
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/5 rounded-full"></div>
                <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-2xl"></div>
            </div>

        </main>

        <script>
            const rfidInput = document.getElementById('rfid_input');
            const statusBox = document.getElementById('tap-status');

            // Keep input focused at all times for the scanner
            document.addEventListener('click', () => rfidInput.focus());
            
            rfidInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const rfid = this.value;
                    if (rfid.length < 3) return; // Prevent accidental short taps

                    processTap(rfid);
                    this.value = '';
                }
            });

            async function processTap(rfid) {
                statusBox.classList.remove('hidden');
                statusBox.textContent = 'Processing Card: ' + rfid + '...';
                statusBox.className = 'animate-bounce bg-white/20 backdrop-blur-xl border border-white/30 rounded-2xl p-6 mb-8 text-white font-black uppercase tracking-widest text-sm';

                try {
                    const response = await fetch('{{ route("attendance.tap") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ rfid_uid: rfid })
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        statusBox.textContent = data.message + ': ' + data.attendance.student_name;
                        statusBox.className = 'bg-emerald-500/20 backdrop-blur-xl border border-emerald-400 rounded-2xl p-6 mb-8 text-emerald-100 font-black uppercase tracking-widest text-sm';
                    } else if (data.status === 'error') {
                        statusBox.textContent = data.message;
                        statusBox.className = 'bg-rose-500/20 backdrop-blur-xl border border-rose-400 rounded-2xl p-6 mb-8 text-rose-100 font-black uppercase tracking-widest text-sm';
                    } else {
                        statusBox.textContent = data.message;
                        statusBox.className = 'bg-amber-500/20 backdrop-blur-xl border border-amber-400 rounded-2xl p-6 mb-8 text-amber-100 font-black uppercase tracking-widest text-sm';
                    }
                } catch (error) {
                    statusBox.textContent = 'Connection Error. Please try again.';
                    statusBox.className = 'bg-rose-500/20 backdrop-blur-xl border border-rose-400 rounded-2xl p-6 mb-8 text-rose-100 font-black uppercase tracking-widest text-sm';
                }

                // Hide status after 5 seconds
                setTimeout(() => {
                    statusBox.classList.add('hidden');
                }, 5000);
            }
        </script>

        <!-- Footer -->
        <footer class="p-8 border-t border-slate-200 bg-white mt-auto">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-center md:text-left">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        &copy; {{ date('Y') }} Aisat Guidance Counseling
                    </p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        General Aguinaldo Highway, Dasmariñas City, Cavite
                    </p>
                </div>
                <div class="flex items-center gap-4 text-slate-400">
                    <a href="https://facebook.com/aisatcollegedasmaph" class="hover:text-indigo-600 transition-colors"><i class="fa-brands fa-facebook text-lg"></i></a>
                    <a href="#" class="hover:text-indigo-600 transition-colors"><i class="fa-solid fa-envelope text-lg"></i></a>
                    <a href="#" class="hover:text-indigo-600 transition-colors"><i class="fa-solid fa-phone text-lg"></i></a>
                </div>
            </div>
        </footer>

    </body>
</html>
