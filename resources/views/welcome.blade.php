<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruthAI | Premium AI Analytics</title>
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
                    },
                    animation: {
                        'float': 'float 10s ease-in-out infinite',
                        'float-delayed': 'float 12s ease-in-out infinite reverse',
                        'pulse-slow': 'pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0) scale(1)' },
                            '50%': { transform: 'translateY(-20px) scale(1.05)' },
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
            z-index: 1;
            opacity: 0.15;
            transition: opacity 0.3s ease;
        }

        html:not(.dark) #matrix-canvas {
            opacity: 0.5;
            mix-blend-mode: multiply;
        }

        .bg-noise {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }

        .bg-dots {
            background-image: radial-gradient(#333 1px, transparent 1px);
            background-size: 32px 32px;
            mask-image: radial-gradient(circle at center, black 40%, transparent 100%);
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 100%);
        }

        html:not(.dark) .bg-dots {
            background-image: radial-gradient(#94a3b8 1px, transparent 1px);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .dark .glass-panel {
            background: rgba(2, 19, 24, 0.6);
            border: 1px solid rgba(0, 192, 194, 0.1);
        }

        .gauge-ring {
            background: conic-gradient(var(--tw-gradient-from) var(--value), var(--tw-gradient-to) 0);
        }

        textarea::-webkit-scrollbar { width: 6px; }
        textarea::-webkit-scrollbar-track { background: transparent; }
        textarea::-webkit-scrollbar-thumb { background: #475569; border-radius: 10px; }

        body::-webkit-scrollbar { width: 8px; }
        body::-webkit-scrollbar-track { background: #01090D; }
        body::-webkit-scrollbar-thumb { background: #00C0C2; border-radius: 4px; }

        .feature-card {
            position: relative;
            overflow: hidden;
            z-index: 0;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 192, 194, 0.1), transparent);
            transition: left 0.5s;
            z-index: -1;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        #light-backdrop {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            transition: opacity 0.5s ease;
            background: linear-gradient(180deg, rgba(236,246,249,0.6) 0%, rgba(220,240,244,0.7) 50%, rgba(200,230,235,0.75) 100%);
        }

        /* Typing cursor animation */
        .typing-cursor {
            display: inline-block;
            width: 3px;
            height: 1em;
            background: linear-gradient(to bottom, #00C0C2, #00AEB1);
            margin-left: 4px;
            animation: blink 0.8s infinite;
            vertical-align: text-bottom;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        /* Stats counter gradient */
        .stat-number {
            background: linear-gradient(135deg, #00C0C2 0%, #00AEB1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glow border effect */
        .glow-border {
            position: relative;
        }

        .glow-border::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: inherit;
            background: linear-gradient(45deg, #00C0C2, #00AEB1, #00C0C2);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .glow-border:hover::after {
            opacity: 0.6;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Scan line effect */
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00C0C2, transparent);
            animation: scanLine 3s linear infinite;
            opacity: 0.5;
        }

        @keyframes scanLine {
            0% { top: 0; }
            100% { top: 100%; }
        }

        /* Badge pulse */
        .badge-pulse {
            animation: badgePulse 2s ease-in-out infinite;
        }

        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(0, 192, 194, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(0, 192, 194, 0); }
        }

        /* Smooth reveal animation */
        .reveal-up {
            opacity: 0;
            transform: translateY(30px);
            animation: revealUp 0.8s ease forwards;
        }

        @keyframes revealUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .reveal-delay-1 { animation-delay: 0.1s; }
        .reveal-delay-2 { animation-delay: 0.2s; }
        .reveal-delay-3 { animation-delay: 0.3s; }
        .reveal-delay-4 { animation-delay: 0.4s; }

        /* Testimonial card hover */
        .testimonial-card {
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="antialiased bg-brand-light text-slate-900 dark:bg-brand-dark dark:text-slate-100 transition-colors duration-300 relative overflow-x-hidden">

    <canvas id="matrix-canvas"></canvas>
    <div id="light-backdrop" class="opacity-0 dark:hidden"></div>
    <div class="bg-noise"></div>

    <div class="fixed top-[-10%] left-[-10%] w-[500px] h-[500px] bg-brand-primary/20 dark:bg-brand-primary/10 rounded-full blur-[100px] animate-float" style="z-index: 0;"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-brand-accent/20 dark:bg-brand-accent/10 rounded-full blur-[120px] animate-float-delayed" style="z-index: 0;"></div>

    <div class="fixed inset-0 bg-dots opacity-40 pointer-events-none" style="z-index: 0;"></div>

    <header class="fixed top-6 left-0 right-0 z-50 flex justify-center px-4">
        <nav class="glass-panel rounded-full px-6 py-3 flex items-center justify-between gap-12 shadow-lg shadow-black/5 dark:shadow-black/20 transition-all duration-300 hover:scale-[1.01] max-w-6xl w-full">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-primary to-brand-accent flex items-center justify-center text-white font-bold shadow-lg shadow-brand-primary/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="font-bold text-lg tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-slate-400">
                    TruthAI
                </span>
            </div>

            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-500 dark:text-slate-400">
                <a href="#features" class="hover:text-brand-primary transition-colors">Features</a>
                <a href="#pricing" class="hover:text-brand-primary transition-colors">Pricing</a>
                <a href="#faq" class="hover:text-brand-primary transition-colors">FAQ</a>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-600 dark:text-slate-300 transition-colors ring-1 ring-slate-200 dark:ring-white/10">
                    <svg id="sun-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg id="moon-icon" class="w-4 h-4 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                <a href="#detector" id="get-started-button" class="hidden sm:block bg-slate-900 dark:bg-white text-white dark:text-black text-xs font-bold px-4 py-2.5 rounded-full hover:opacity-90 transition-opacity">
                    Get Started
                </a>
            </div>
        </nav>
    </header>

    <main class="min-h-screen pt-32 pb-20 px-4 flex flex-col items-center justify-center relative z-10">

        <div class="text-center mb-12 max-w-2xl mx-auto space-y-4 reveal-up">
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-slate-900 via-slate-700 to-slate-900 dark:from-white dark:via-slate-200 dark:to-slate-500">
                    Truth in a World of
                </span>
                <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-primary to-brand-accent">
                    <span id="typed-text"></span><span class="typing-cursor"></span>
                </span>
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-lg reveal-up reveal-delay-2">
                Detect AI generation with 99.2% accuracy. Humanize text to bypass filters.
                All in one decentralized, secure dashboard.
            </p>
        </div>

        <!-- Stats Section -->
        <div class="flex flex-wrap justify-center gap-8 md:gap-16 mb-16 reveal-up reveal-delay-3">
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold stat-number" data-target="99.2">0%</div>
                <div class="text-sm text-slate-500 dark:text-slate-400">Accuracy Rate</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold stat-number" data-target="2.5">0M+</div>
                <div class="text-sm text-slate-500 dark:text-slate-400">Scans Performed</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold stat-number" data-target="150">0K+</div>
                <div class="text-sm text-slate-500 dark:text-slate-400">Happy Users</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-bold stat-number" data-target="50">0ms</div>
                <div class="text-sm text-slate-500 dark:text-slate-400">Avg Response</div>
            </div>
        </div>

        <div id="detector" class="w-full max-w-6xl glass-panel rounded-3xl p-1 overflow-hidden shadow-2xl shadow-brand-primary/5 transition-all duration-500 mb-20 glow-border relative">
            <div class="scan-line"></div>
            <div class="flex flex-col lg:flex-row min-h-[650px]">

                <div class="w-full lg:w-1/2 p-6 md:p-8 flex flex-col border-b lg:border-b-0 lg:border-r border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-black/20">

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex p-1 bg-slate-200 dark:bg-white/5 rounded-xl">
                            <button class="px-6 py-2 rounded-lg bg-white dark:bg-brand-primary text-slate-900 dark:text-black shadow-sm text-sm font-semibold transition-all">Detector</button>
                            <button class="px-6 py-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-sm font-medium transition-all">Humanizer</button>
                        </div>
                        <span class="text-xs text-slate-400 font-mono">v2.6.0</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <button class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 hover:border-brand-primary/50 dark:hover:border-brand-primary/50 transition-all group">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            <span class="text-xs font-medium text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white">Link</span>
                        </button>
                        <button class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 hover:border-brand-primary/50 dark:hover:border-brand-primary/50 transition-all group">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-medium text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white">Image</span>
                        </button>
                        <button class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/5 hover:border-brand-primary/50 dark:hover:border-brand-primary/50 transition-all group">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-medium text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white">Video</span>
                        </button>
                    </div>

                    <div class="relative flex-grow group">
                        <textarea
                            class="w-full h-full bg-transparent border-0 focus:ring-0 text-slate-600 dark:text-slate-300 text-sm md:text-base leading-relaxed placeholder-slate-400 resize-none p-0 transition-colors"
                            placeholder="Paste your content here to begin analysis..."
                            spellcheck="false"
                        >To be honest, the rapid advancement of artificial intelligence has sparked a global debate concerning the ethical implications of automation. While proponents argue that AI can significantly enhance productivity and solve complex problems, critics raise valid concerns regarding job displacement and the erosion of human privacy. Furthermore, the lack of transparency in algorithmic decision-making poses a significant threat to democratic institutions.</textarea>

                        <div class="absolute -inset-4 bg-brand-primary/5 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity -z-10 blur-md"></div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-white/10 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <div class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></div>
                            <span>Systems Online</span>
                        </div>
                        <button class="relative overflow-hidden group bg-slate-900 dark:bg-brand-primary text-white dark:text-black font-bold py-3 px-8 rounded-xl shadow-lg transition-all hover:scale-[1.02]">
                            <span class="relative z-10 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Scan Content
                            </span>
                            <div class="absolute inset-0 bg-white/10 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 ease-in-out"></div>
                        </button>
                    </div>
                </div>

                <div class="w-full lg:w-1/2 bg-white/60 dark:bg-[#010a0e]/60 p-6 md:p-8 flex flex-col relative overflow-hidden backdrop-blur-sm">

                    <div class="absolute top-0 right-0 w-64 h-64 bg-brand-primary/5 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="flex items-start justify-between mb-8 relative z-10">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Analysis Verdict</h3>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-2xl font-bold text-red-500">Likely AI Generated</h2>
                                <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-500 text-xs font-bold border border-red-500/20">HIGH RISK</span>
                            </div>
                        </div>

                        <div class="relative w-20 h-20 rounded-full gauge-ring flex items-center justify-center shadow-lg shadow-red-500/20" style="--value: 94%; --tw-gradient-from: #ef4444; --tw-gradient-to: #334155;">
                            <div class="w-16 h-16 bg-white dark:bg-[#010a0e] rounded-full flex items-center justify-center">
                                <span class="text-xl font-bold text-slate-800 dark:text-white">94%</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow overflow-y-auto pr-2 mb-6 custom-scrollbar relative z-10">
                        <div class="p-4 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/5 text-sm leading-7 text-slate-700 dark:text-slate-300">
                            <p>
                                <span class="bg-red-500/20 text-red-700 dark:text-red-300 border-b-2 border-red-500 pb-0.5 rounded-sm px-0.5 transition-colors hover:bg-red-500/30 cursor-help" title="99% AI Probability">To be honest, the rapid advancement of artificial intelligence has sparked a global debate concerning the ethical implications of automation.</span>
                                <span class="bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 border-b-2 border-yellow-500 pb-0.5 rounded-sm px-0.5 transition-colors hover:bg-yellow-500/30 cursor-help" title="65% AI Probability">While proponents argue that AI can significantly enhance productivity and solve complex problems,</span>
                                critics raise valid concerns regarding job displacement and the erosion of human privacy.
                                <span class="bg-red-500/20 text-red-700 dark:text-red-300 border-b-2 border-red-500 pb-0.5 rounded-sm px-0.5 transition-colors hover:bg-red-500/30 cursor-help" title="95% AI Probability">Furthermore, the lack of transparency in algorithmic decision-making poses a significant threat to democratic institutions.</span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-auto relative z-10 p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 dark:from-[#021318] dark:to-[#01090D] border border-slate-700 dark:border-brand-primary/20 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4 group hover:border-brand-primary/40 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-brand-primary/20 flex items-center justify-center text-brand-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-sm">Bypass Detection?</h4>
                                <p class="text-slate-400 text-xs">Rewrite content to lower AI score.</p>
                            </div>
                        </div>
                        <button class="w-full sm:w-auto px-5 py-2.5 bg-white text-black font-bold text-sm rounded-lg hover:bg-brand-primary hover:text-white transition-colors shadow-lg shadow-white/5">
                            Humanize Text →
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <section id="features" class="w-full max-w-6xl mb-20 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-slate-400">
                    Powerful Detection Tools
                </h2>
                <p class="text-slate-500 dark:text-slate-400">
                    Everything you need to identify and humanize AI-generated content
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="feature-card glass-panel rounded-2xl p-6 hover:border-brand-primary/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-brand-primary/10 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-slate-800 dark:text-white">Text Detection</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Analyze essays and documents with sentence-level AI probability scoring</p>
                </div>

                <div class="feature-card glass-panel rounded-2xl p-6 hover:border-brand-primary/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-brand-accent/10 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-slate-800 dark:text-white">Image Analysis</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Detect AI-generated images using advanced GAN signatures</p>
                </div>

                <div class="feature-card glass-panel rounded-2xl p-6 hover:border-brand-primary/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-slate-800 dark:text-white">Video Detection</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Identify deepfakes with frame-by-frame analysis</p>
                </div>

                <div class="feature-card glass-panel rounded-2xl p-6 hover:border-brand-primary/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-slate-800 dark:text-white">URL Scanning</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Analyze entire websites by pasting the URL</p>
                </div>

                <div class="feature-card glass-panel rounded-2xl p-6 hover:border-brand-primary/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-brand-primary/10 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-slate-800 dark:text-white">Text Humanizer</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Rewrite AI content to bypass detectors</p>
                </div>

                <div class="feature-card glass-panel rounded-2xl p-6 hover:border-brand-primary/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-brand-accent/10 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-slate-800 dark:text-white">API Access</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Integrate detection into your applications</p>
                </div>
            </div>
        </section>

        <section id="pricing" class="w-full max-w-6xl mb-20 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-slate-400">
                    Simple, Transparent Pricing
                </h2>
                <p class="text-slate-500 dark:text-slate-400">Choose the plan that's right for you</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="glass-panel rounded-2xl p-8 hover:scale-105 transition-all">
                    <div class="text-sm font-semibold text-brand-primary mb-2">FREE</div>
                    <div class="text-4xl font-bold mb-1 text-slate-800 dark:text-white">$0</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mb-6">per month</div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">100 scans/day</span>
                        </li>
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">Text detection only</span>
                        </li>
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">Basic support</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 rounded-xl border-2 border-slate-300 dark:border-white/20 text-slate-700 dark:text-slate-200 font-semibold hover:border-brand-primary transition-colors">Get Started</button>
                </div>

                <div class="glass-panel rounded-2xl p-8 hover:scale-105 transition-all border-2 border-brand-primary">
                    <div class="text-sm font-semibold text-brand-primary mb-2">PRO</div>
                    <div class="text-4xl font-bold mb-1 text-slate-800 dark:text-white">$29</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mb-6">per month</div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">10,000 scans/day</span>
                        </li>
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">All detection types</span>
                        </li>
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">Text humanization</span>
                        </li>
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">API access</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 rounded-xl bg-brand-primary text-white font-semibold hover:opacity-90 transition-opacity">Upgrade to Pro</button>
                </div>

                <div class="glass-panel rounded-2xl p-8 hover:scale-105 transition-all">
                    <div class="text-sm font-semibold text-brand-accent mb-2">ENTERPRISE</div>
                    <div class="text-4xl font-bold mb-1 text-slate-800 dark:text-white">Custom</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mb-6">contact us</div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">Unlimited scans</span>
                        </li>
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">Custom API limits</span>
                        </li>
                        <li class="flex items-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-brand-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-600 dark:text-slate-300">Dedicated support</span>
                        </li>
                    </ul>
                    <button class="w-full py-3 rounded-xl border-2 border-slate-300 dark:border-white/20 text-slate-700 dark:text-slate-200 font-semibold hover:border-brand-primary transition-colors">Contact Sales</button>
                </div>
            </div>
        </section>

        <section id="faq" class="w-full max-w-4xl mb-20 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-slate-400">
                    Frequently Asked Questions
                </h2>
            </div>

            <div class="space-y-4">
                <div class="glass-panel rounded-xl p-6">
                    <h3 class="font-bold text-lg mb-2 text-slate-800 dark:text-white">How accurate is the AI detection?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Our 2026 engine achieves 99.2% accuracy across multiple AI models including GPT-4, Claude, and other LLMs.</p>
                </div>
                <div class="glass-panel rounded-xl p-6">
                    <h3 class="font-bold text-lg mb-2 text-slate-800 dark:text-white">Does the humanizer really work?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Yes! Our humanizer rewrites content to bypass AI detectors while maintaining the original meaning. Success rate is over 95%.</p>
                </div>
                <div class="glass-panel rounded-xl p-6">
                    <h3 class="font-bold text-lg mb-2 text-slate-800 dark:text-white">Is my data secure?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Absolutely. We use end-to-end encryption and don't store your content. All analysis is done in real-time.</p>
                </div>
            </div>
        </section>

    </main>

    <footer class="py-8 px-4 border-t border-slate-200 dark:border-white/10 relative z-10">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-brand-primary to-brand-accent flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <span class="font-bold text-slate-800 dark:text-white">TruthAI</span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">© 2026 TruthAI. All rights reserved.</p>
            <div class="flex items-center gap-6 text-sm text-slate-500 dark:text-slate-400">
                <a href="#" class="hover:text-brand-primary transition-colors">Privacy</a>
                <a href="#" class="hover:text-brand-primary transition-colors">Terms</a>
                <a href="#" class="hover:text-brand-primary transition-colors">Contact</a>
            </div>
        </div>
    </footer>

    <script>
        const html = document.documentElement;
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        const lightBackdrop = document.getElementById('light-backdrop');

        function toggleTheme() {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
                localStorage.setItem('theme', 'light');
                lightBackdrop.classList.remove('opacity-0');
                lightBackdrop.classList.add('opacity-100');
                updateMatrixColor('#00AEB1');
                // Clear canvas for light theme
                if (typeof ctx !== 'undefined') {
                    ctx.fillStyle = 'rgba(236, 246, 249, 1)';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                }
            } else {
                html.classList.add('dark');
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
                localStorage.setItem('theme', 'dark');
                lightBackdrop.classList.add('opacity-0');
                lightBackdrop.classList.remove('opacity-100');
                updateMatrixColor('#00C0C2');
                // Clear canvas for dark theme
                if (typeof ctx !== 'undefined') {
                    ctx.fillStyle = 'rgba(1, 9, 13, 1)';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                }
            }
        }

        if (localStorage.getItem('theme') === 'light') {
            html.classList.remove('dark');
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
            lightBackdrop.classList.remove('opacity-0');
            lightBackdrop.classList.add('opacity-100');
        }

        const canvas = document.getElementById('matrix-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()アイウエオカキクケコ';
        const fontSize = 14;
        const columns = canvas.width / fontSize;
        const drops = [];
        let matrixColor = '#00C0C2';

        for (let i = 0; i < columns; i++) {
            drops[i] = Math.random() * -100;
        }

        function updateMatrixColor(color) {
            matrixColor = color;
        }

        let lastTime = 0;
        const fps = 30;
        const nextFrame = 1000 / fps;

        function draw(timestamp) {
            if (timestamp - lastTime < nextFrame) {
                requestAnimationFrame(draw);
                return;
            }
            lastTime = timestamp;
            const isDark = html.classList.contains('dark');
            // Use different fade colors for each theme
            ctx.fillStyle = isDark ? 'rgba(1, 9, 13, 0.05)' : 'rgba(236, 246, 249, 0.08)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = matrixColor;
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

        requestAnimationFrame(draw);

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        document.getElementById('get-started-button').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('detector').scrollIntoView({ behavior: 'smooth' });
        });

        // Typing Animation
        const typedTextElement = document.getElementById('typed-text');
        const textOptions = ['Artificial Content', 'AI-Generated Text', 'Deepfake Videos', 'Synthetic Images', 'Machine Writing'];
        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let typingSpeed = 100;

        function typeText() {
            const currentText = textOptions[textIndex];

            if (isDeleting) {
                typedTextElement.textContent = currentText.substring(0, charIndex - 1);
                charIndex--;
                typingSpeed = 50;
            } else {
                typedTextElement.textContent = currentText.substring(0, charIndex + 1);
                charIndex++;
                typingSpeed = 100;
            }

            if (!isDeleting && charIndex === currentText.length) {
                typingSpeed = 2000;
                isDeleting = true;
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                textIndex = (textIndex + 1) % textOptions.length;
                typingSpeed = 500;
            }

            setTimeout(typeText, typingSpeed);
        }

        setTimeout(typeText, 1000);

        // Stats Counter Animation
        function animateCounters() {
            const statNumbers = document.querySelectorAll('.stat-number');

            statNumbers.forEach(stat => {
                const target = parseFloat(stat.getAttribute('data-target'));
                const suffix = stat.textContent.replace(/[0-9.]/g, '');
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;

                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        stat.textContent = (target < 10 ? current.toFixed(1) : Math.floor(current)) + suffix;
                        requestAnimationFrame(updateCounter);
                    } else {
                        stat.textContent = (target % 1 !== 0 ? target.toFixed(1) : target) + suffix;
                    }
                };

                updateCounter();
            });
        }

        // Run stats animation on load
        setTimeout(animateCounters, 500);
    </script>
</body>
</html>
