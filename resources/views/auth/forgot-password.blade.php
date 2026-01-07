<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | TruthAI</title>
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

        body::-webkit-scrollbar { width: 8px; }
        body::-webkit-scrollbar-track { background: #01090D; }
        body::-webkit-scrollbar-thumb { background: #00C0C2; border-radius: 4px; }
    </style>
</head>
<body class="antialiased bg-brand-light dark:bg-brand-dark min-h-screen">

    <canvas id="matrix-canvas"></canvas>

    <div class="min-h-screen flex items-center justify-center relative z-10 px-4 py-12">
        <div class="w-full max-w-md">
            <div class="glass-panel rounded-3xl p-8 md:p-10">
                <!-- Logo -->
                <div class="flex items-center justify-center gap-2 mb-8">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-primary to-brand-accent flex items-center justify-center text-white font-bold shadow-lg shadow-brand-primary/20">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <!-- Heading -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">
                        Forgot Password?
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400">
                        No worries! Enter your email and we'll send you reset instructions.
                    </p>
                </div>

                <!-- Form -->
                <form class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                        <input type="email" placeholder="Enter your email" class="input-field w-full px-4 py-3 rounded-xl text-slate-800 dark:text-white outline-none">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-brand-primary/30 hover:shadow-brand-primary/50 transition-all hover:scale-[1.02]">
                        Send Reset Link
                    </button>
                </form>

                <!-- Back to Login -->
                <a href="/login" class="flex items-center justify-center gap-2 text-slate-600 dark:text-slate-400 mt-6 hover:text-brand-primary transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Sign In
                </a>
            </div>

            <!-- Register Link -->
            <p class="text-center text-slate-600 dark:text-slate-400 mt-8">
                Don't have an account? 
                <a href="/register" class="text-brand-primary font-semibold hover:underline">Sign Up</a>
            </p>
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
