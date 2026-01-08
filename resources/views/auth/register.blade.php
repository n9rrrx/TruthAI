<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | TruthAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            dark: '#01090D',
                            darker: '#010608',
                            card: '#021318',
                            light: '#ECF6F9',
                            primary: '#00C0C2',
                            primaryGlow: '#00E0E3',
                            accent: '#00AEB1',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        #matrix-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.12;
        }

        html:not(.dark) #matrix-canvas {
            opacity: 0.3;
            mix-blend-mode: multiply;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .dark .glass-panel {
            background: rgba(2, 19, 24, 0.85);
            border: 1px solid rgba(0, 192, 194, 0.1);
        }

        .input-field {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #00C0C2;
            box-shadow: 0 0 0 3px rgba(0, 192, 194, 0.1);
        }

        .dark .input-field {
            background: rgba(1, 9, 13, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .dark .input-field:focus {
            border-color: #00C0C2;
            box-shadow: 0 0 0 3px rgba(0, 192, 194, 0.2);
        }

        .dark .input-field::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .social-btn {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .social-btn:hover {
            background: #f8fafc;
            border-color: #00C0C2;
        }

        .dark .social-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .dark .social-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #00C0C2;
        }

        body::-webkit-scrollbar { width: 8px; }
        body::-webkit-scrollbar-track { background: #01090D; }
        body::-webkit-scrollbar-thumb { background: #00C0C2; border-radius: 4px; }
    </style>
</head>
<body class="antialiased bg-brand-light dark:bg-brand-dark min-h-screen">

    <canvas id="matrix-canvas"></canvas>

    <div class="min-h-screen flex relative z-10">
        <!-- Left Side - Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 md:px-16 lg:px-24 py-12">
            <div class="max-w-md mx-auto w-full">
                <!-- Logo -->
                <div class="flex items-center gap-2 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-primary to-brand-accent flex items-center justify-center text-white font-bold shadow-lg shadow-brand-primary/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="font-bold text-xl bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-slate-400">
                        TruthAI
                    </span>
                </div>

                <!-- Heading -->
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    Create Your Account
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mb-8">
                    Join thousands detecting AI content with precision.
                </p>

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter your name" class="input-field w-full px-4 py-3 rounded-xl text-slate-800 dark:text-white outline-none" required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" class="input-field w-full px-4 py-3 rounded-xl text-slate-800 dark:text-white outline-none" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Create a password" class="input-field w-full px-4 py-3 rounded-xl text-slate-800 dark:text-white outline-none pr-12" required>
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand-primary transition-colors">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm your password" class="input-field w-full px-4 py-3 rounded-xl text-slate-800 dark:text-white outline-none" required>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-brand-primary/30 hover:shadow-brand-primary/50 transition-all hover:scale-[1.02]">
                        Sign Up
                    </button>
                </form>

                <!-- Terms -->
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-4 text-center">
                    By signing up, you agree to our 
                    <a href="#" class="text-brand-primary hover:underline">Terms of Service</a> and 
                    <a href="#" class="text-brand-primary hover:underline">Privacy Policy</a>.
                </p>

                <!-- Divider -->
                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
                    <span class="text-sm text-slate-400">or continue with</span>
                    <div class="flex-1 h-px bg-slate-200 dark:bg-white/10"></div>
                </div>

                <!-- Social Buttons -->
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('auth.google') }}" class="social-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-medium text-slate-700 dark:text-white">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Google
                    </a>
                    <button class="social-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-medium text-slate-700 dark:text-white opacity-50 cursor-not-allowed" disabled>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                        Apple
                    </button>
                </div>

                <!-- Login Link -->
                <p class="text-center text-slate-600 dark:text-slate-400 mt-8">
                    Already have an account? 
                    <a href="/login" class="text-brand-primary font-semibold hover:underline">Sign In</a>
                </p>
            </div>
        </div>

        <!-- Right Side - Branding -->
        <div class="hidden lg:flex w-1/2 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-primary/20 to-brand-accent/30 dark:from-brand-primary/10 dark:to-brand-accent/20"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/80 to-transparent"></div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-20 right-20 w-72 h-72 bg-brand-primary/30 rounded-full blur-[80px]"></div>
            <div class="absolute bottom-40 left-20 w-96 h-96 bg-brand-accent/20 rounded-full blur-[100px]"></div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-end p-16">
                <div class="glass-panel rounded-3xl p-8 max-w-lg">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">
                        Detect AI with Precision
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 mb-6">
                        Join our community of educators, researchers, and professionals who trust TruthAI to identify AI-generated content across text, images, and videos.
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 border-2 border-white dark:border-brand-dark flex items-center justify-center text-white text-sm font-bold">J</div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 border-2 border-white dark:border-brand-dark flex items-center justify-center text-white text-sm font-bold">S</div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-red-500 border-2 border-white dark:border-brand-dark flex items-center justify-center text-white text-sm font-bold">A</div>
                            <div class="w-10 h-10 rounded-full bg-brand-primary border-2 border-white dark:border-brand-dark flex items-center justify-center text-white text-sm font-bold">+</div>
                        </div>
                        <span class="text-slate-600 dark:text-slate-300 text-sm">
                            <strong class="text-slate-900 dark:text-white">150K+</strong> users trust us
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Toggle (floating) -->
    <button onclick="toggleTheme()" class="fixed top-6 right-6 z-50 w-10 h-10 flex items-center justify-center rounded-full glass-panel shadow-lg text-slate-600 dark:text-slate-300 hover:scale-110 transition-transform">
        <svg id="sun-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        <svg id="moon-icon" class="w-5 h-5 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
    </button>

    <script>
        const html = document.documentElement;
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');

        function toggleTheme() {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
                localStorage.setItem('theme', 'dark');
            }
        }

        if (localStorage.getItem('theme') === 'light') {
            html.classList.remove('dark');
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

        // Matrix Rain
        const canvas = document.getElementById('matrix-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()アイウエオカキクケコ';
        const fontSize = 14;
        const columns = canvas.width / fontSize;
        const drops = [];

        for (let i = 0; i < columns; i++) {
            drops[i] = Math.random() * -100;
        }

        function draw() {
            const isDark = html.classList.contains('dark');
            ctx.fillStyle = isDark ? 'rgba(1, 9, 13, 0.05)' : 'rgba(236, 246, 249, 0.08)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = isDark ? '#00C0C2' : '#00AEB1';
            ctx.font = fontSize + 'px monospace';

            for (let i = 0; i < drops.length; i++) {
                const text = chars.charAt(Math.floor(Math.random() * chars.length));
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);

                if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
            requestAnimationFrame(draw);
        }

        draw();

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    </script>
</body>
</html>
