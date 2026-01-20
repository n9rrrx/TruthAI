<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | TruthAI</title>
    
    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <link rel="apple-touch-icon" href="/images/logo-icon.png">
    <meta name="theme-color" content="#00C0C2">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.stripe.com/v3/"></script>
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

        /* Walking Robot Animations */
        .floating-robot {
            animation: walk-around 20s linear infinite;
        }

        @keyframes walk-around {
            0% { 
                top: 20%; 
                right: 10%; 
            }
            25% { 
                top: 60%; 
                right: 30%; 
            }
            50% { 
                top: 70%; 
                right: 10%; 
            }
            75% { 
                top: 40%; 
                right: 25%; 
            }
            100% { 
                top: 20%; 
                right: 10%; 
            }
        }

        .floating-robot svg {
            animation: body-bob 0.5s ease-in-out infinite;
        }

        @keyframes body-bob {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .robot-leg-left {
            animation: leg-walk-left 0.5s ease-in-out infinite;
            transform-origin: center top;
        }

        .robot-leg-right {
            animation: leg-walk-right 0.5s ease-in-out infinite;
            transform-origin: center top;
        }

        @keyframes leg-walk-left {
            0%, 100% { transform: rotate(-8deg); }
            50% { transform: rotate(8deg); }
        }

        @keyframes leg-walk-right {
            0%, 100% { transform: rotate(8deg); }
            50% { transform: rotate(-8deg); }
        }

        .robot-eye {
            animation: blink 4s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 90%, 100% { opacity: 1; }
            95% { opacity: 0.3; }
        }

        .antenna-glow {
            animation: glow 2s ease-in-out infinite;
        }

        @keyframes glow {
            0%, 100% { opacity: 1; filter: drop-shadow(0 0 3px #00E0E3); }
            50% { opacity: 0.6; filter: drop-shadow(0 0 8px #00E0E3); }
        }

        .chest-bar {
            animation: pulse-bar 1.5s ease-in-out infinite;
        }

        .chest-bar.delay-1 {
            animation-delay: 0.3s;
        }

        .chest-bar.delay-2 {
            animation-delay: 0.6s;
        }

        @keyframes pulse-bar {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
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
                    <img src="/images/logo-icon.png" alt="TruthAI" class="w-10 h-10 rounded-xl shadow-lg shadow-brand-primary/20">
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
                <form method="POST" action="{{ route('register') }}" class="space-y-5" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Profile Picture Upload -->
                    <div class="flex flex-col items-center mb-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Profile Picture <span class="text-slate-400">(optional)</span></label>
                        <div class="relative group cursor-pointer" onclick="document.getElementById('avatar-input').click()">
                            <div id="avatar-preview" class="w-24 h-24 rounded-full bg-gradient-to-br from-brand-primary/20 to-brand-accent/20 dark:from-brand-primary/30 dark:to-brand-accent/30 border-2 border-dashed border-brand-primary/50 flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:border-brand-primary group-hover:scale-105">
                                <svg id="avatar-placeholder" class="w-10 h-10 text-brand-primary/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <img id="avatar-image" src="" alt="Preview" class="w-full h-full object-cover hidden">
                            </div>
                            <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        <p class="text-xs text-slate-400 mt-2">Click to upload</p>
                        @error('avatar')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

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

                    <!-- Plan Selection -->
                    <div class="pt-4 border-t border-slate-200 dark:border-white/10">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Choose Your Plan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="plan" value="free" class="hidden peer" checked onchange="toggleCardForm()">
                                <div class="peer-checked:ring-2 peer-checked:ring-brand-primary peer-checked:border-brand-primary p-4 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 transition-all hover:border-brand-primary/50">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-bold text-slate-900 dark:text-white">Free</span>
                                        <span class="text-lg font-bold text-slate-900 dark:text-white">$0</span>
                                    </div>
                                    <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                                        <li>✓ 100 scans/day</li>
                                        <li>✓ Text detection</li>
                                    </ul>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="plan" value="pro" class="hidden peer" onchange="toggleCardForm()">
                                <div class="peer-checked:ring-2 peer-checked:ring-brand-primary peer-checked:border-brand-primary p-4 rounded-xl border border-slate-200 dark:border-white/10 bg-gradient-to-br from-brand-primary/5 to-brand-accent/5 transition-all hover:border-brand-primary/50 relative overflow-hidden">
                                    <span class="absolute top-0 right-0 bg-brand-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-bl">POPULAR</span>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-bold text-slate-900 dark:text-white">Pro</span>
                                        <span class="text-lg font-bold text-slate-900 dark:text-white">$29<span class="text-xs font-normal">/mo</span></span>
                                    </div>
                                    <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                                        <li>✓ 10,000 scans/day</li>
                                        <li>✓ All features</li>
                                    </ul>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Card Payment Section (hidden by default) -->
                    <div id="card-section" class="hidden space-y-4 pt-4 border-t border-slate-200 dark:border-white/10">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Payment Information</span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Cardholder Name</label>
                            <input type="text" id="cardholder-name" placeholder="Name on card" class="input-field w-full px-4 py-3 rounded-xl text-slate-800 dark:text-white outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Card Details</label>
                            <div id="card-element" class="input-field px-4 py-3 rounded-xl">
                                <!-- Stripe Element -->
                            </div>
                            <div id="card-errors" class="text-red-500 text-sm mt-1" role="alert"></div>
                        </div>
                        
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span>Secured by Stripe. Cancel anytime.</span>
                        </div>
                    </div>

                    <!-- Hidden field for payment method -->
                    <input type="hidden" name="payment_method" id="payment-method-input">
                    
                    <!-- Error Display -->
                    <div id="payment-error" class="hidden p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm"></div>

                    <button type="submit" id="submit-btn" class="w-full bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-brand-primary/30 hover:shadow-brand-primary/50 transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                        <span id="btn-text">Sign Up</span>
                        <svg id="btn-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
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
            
            <!-- Floating Robot -->
            <div class="absolute top-1/4 right-1/4 transform -translate-y-1/2 floating-robot">
                <svg class="w-56 h-72 drop-shadow-2xl" viewBox="0 0 160 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Shadow -->
                    <ellipse cx="80" cy="185" rx="35" ry="8" fill="rgba(0, 0, 0, 0.3)" class="robot-shadow"/>
                    
                    <!-- Left Leg with Sneaker -->
                    <g class="robot-leg-left">
                        <path d="M55 165 L55 175 Q55 182 62 182 L72 182 Q78 182 78 176 L78 165" fill="#5a5a5a" stroke="#4a4a4a" stroke-width="1"/>
                        <ellipse cx="67" cy="180" rx="14" ry="6" fill="#6a6a6a"/>
                        <path d="M55 178 Q67 184 78 178" stroke="#888" stroke-width="1" fill="none"/>
                    </g>
                    
                    <!-- Right Leg with Sneaker -->
                    <g class="robot-leg-right">
                        <path d="M82 165 L82 175 Q82 182 89 182 L99 182 Q105 182 105 176 L105 165" fill="#5a5a5a" stroke="#4a4a4a" stroke-width="1"/>
                        <ellipse cx="93" cy="180" rx="14" ry="6" fill="#6a6a6a"/>
                        <path d="M82 178 Q93 184 105 178" stroke="#888" stroke-width="1" fill="none"/>
                    </g>
                    
                    <!-- Hoodie Body -->
                    <path d="M45 95 L45 165 L115 165 L115 95 Q115 85 105 82 L55 82 Q45 85 45 95" fill="#5a5a5a" stroke="#4a4a4a" stroke-width="1"/>
                    
                    <!-- Hoodie pocket -->
                    <path d="M55 130 L105 130 L105 150 Q105 155 100 155 L60 155 Q55 155 55 150 Z" fill="#4a4a4a" stroke="#3a3a3a" stroke-width="1"/>
                    
                    <!-- Hoodie strings -->
                    <line x1="70" y1="82" x2="68" y2="105" stroke="#7a7a7a" stroke-width="2" stroke-linecap="round"/>
                    <line x1="90" y1="82" x2="92" y2="105" stroke="#7a7a7a" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="68" cy="107" r="3" fill="#8a8a8a"/>
                    <circle cx="92" cy="107" r="3" fill="#8a8a8a"/>
                    
                    <!-- Arms -->
                    <path d="M45 95 L30 110 L30 140 Q30 145 35 145 L40 145 Q45 145 45 140 L45 95" fill="#5a5a5a" stroke="#4a4a4a" stroke-width="1"/>
                    <path d="M115 95 L130 110 L130 140 Q130 145 125 145 L120 145 Q115 145 115 140 L115 95" fill="#5a5a5a" stroke="#4a4a4a" stroke-width="1"/>
                    
                    <!-- Robot Hands -->
                    <circle cx="37" cy="147" r="8" fill="#2a2a2a"/>
                    <circle cx="123" cy="147" r="8" fill="#2a2a2a"/>
                    
                    <!-- Hood -->
                    <path d="M35 75 Q35 25 80 20 Q125 25 125 75 L125 90 Q125 95 115 95 L45 95 Q35 95 35 90 Z" fill="#5a5a5a" stroke="#4a4a4a" stroke-width="1"/>
                    
                    <!-- Hood inner shadow -->
                    <path d="M45 70 Q45 35 80 32 Q115 35 115 70 L115 82 L45 82 Z" fill="#3a3a3a"/>
                    
                    <!-- Robot Face (dark visor) -->
                    <rect x="50" y="45" width="60" height="40" rx="8" fill="#1a1a1a" stroke="#2a2a2a" stroke-width="1"/>
                    
                    <!-- Glowing Eyes -->
                    <rect class="robot-eye" x="58" y="52" width="12" height="18" rx="2" fill="#ff6b6b">
                        <animate attributeName="opacity" values="1;0.6;1" dur="3s" repeatCount="indefinite"/>
                    </rect>
                    <rect class="robot-eye" x="90" y="52" width="12" height="18" rx="2" fill="#ff6b6b">
                        <animate attributeName="opacity" values="1;0.6;1" dur="3s" repeatCount="indefinite"/>
                    </rect>
                    
                    <!-- Eye glow effect -->
                    <rect x="58" y="52" width="12" height="18" rx="2" fill="#ff6b6b" opacity="0.5" filter="url(#glow)"/>
                    <rect x="90" y="52" width="12" height="18" rx="2" fill="#ff6b6b" opacity="0.5" filter="url(#glow)"/>
                    
                    <!-- Kissy mouth -->
                    <ellipse cx="80" cy="76" rx="4" ry="5" fill="#444"/>
                    
                    <!-- Glow filter -->
                    <defs>
                        <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
                            <feGaussianBlur stdDeviation="4" result="coloredBlur"/>
                            <feMerge>
                                <feMergeNode in="coloredBlur"/>
                                <feMergeNode in="SourceGraphic"/>
                            </feMerge>
                        </filter>
                    </defs>
                </svg>
            </div>

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

        // Avatar preview function
        function previewAvatar(input) {
            const preview = document.getElementById('avatar-image');
            const placeholder = document.getElementById('avatar-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Toggle card form based on plan selection
        function toggleCardForm() {
            const cardSection = document.getElementById('card-section');
            const proSelected = document.querySelector('input[name="plan"][value="pro"]').checked;
            const btnText = document.getElementById('btn-text');
            
            if (proSelected) {
                cardSection.classList.remove('hidden');
                btnText.textContent = 'Sign Up & Pay $29';
                initStripe();
            } else {
                cardSection.classList.add('hidden');
                btnText.textContent = 'Sign Up';
            }
        }

        // Stripe initialization
        let stripe, elements, cardElement;
        let stripeInitialized = false;

        function initStripe() {
            if (stripeInitialized) return;
            
            stripe = Stripe('{{ config("services.stripe.key") }}');
            elements = stripe.elements();
            
            const style = {
                base: {
                    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#1e293b',
                    fontFamily: 'Inter, sans-serif',
                    fontSize: '16px',
                    '::placeholder': {
                        color: document.documentElement.classList.contains('dark') ? '#64748b' : '#94a3b8'
                    }
                },
                invalid: {
                    color: '#ef4444',
                    iconColor: '#ef4444'
                }
            };

            cardElement = elements.create('card', { style: style });
            cardElement.mount('#card-element');

            cardElement.on('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            stripeInitialized = true;
        }

        // Form submission handling
        document.querySelector('form').addEventListener('submit', async function(e) {
            const proSelected = document.querySelector('input[name="plan"][value="pro"]').checked;
            
            if (!proSelected) {
                // Free plan - normal form submission
                return true;
            }

            // Pro plan - need to create payment method first
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            const paymentError = document.getElementById('payment-error');
            const cardholderName = document.getElementById('cardholder-name').value;

            if (!cardholderName) {
                paymentError.textContent = 'Please enter the cardholder name.';
                paymentError.classList.remove('hidden');
                return;
            }

            // Disable button and show spinner
            submitBtn.disabled = true;
            btnText.textContent = 'Processing...';
            btnSpinner.classList.remove('hidden');
            paymentError.classList.add('hidden');

            try {
                // Create payment method
                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                    billing_details: {
                        name: cardholderName
                    }
                });

                if (error) {
                    throw new Error(error.message);
                }

                // Set payment method in hidden field
                document.getElementById('payment-method-input').value = paymentMethod.id;
                
                // Submit the form
                this.submit();

            } catch (error) {
                paymentError.textContent = error.message;
                paymentError.classList.remove('hidden');
                submitBtn.disabled = false;
                btnText.textContent = 'Sign Up & Pay $29';
                btnSpinner.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
