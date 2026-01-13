<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | TruthAI</title>
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
        .sidebar-link {
            transition: all 0.2s ease;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(0, 192, 194, 0.1);
            color: #00C0C2;
        }
        .sidebar-link.active {
            border-left: 3px solid #00C0C2;
        }
        .sidebar-collapsed .sidebar-link.active {
            border-left: none;
        }

        /* Tooltip for collapsed sidebar */
        .sidebar-link {
            position: relative;
        }
        .sidebar-collapsed .sidebar-link::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 8px;
            padding: 6px 12px;
            background: #01090D;
            color: white;
            font-size: 12px;
            font-weight: 500;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .sidebar-collapsed .sidebar-link:hover::after {
            opacity: 1;
            visibility: visible;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .dark .glass-panel {
            background: rgba(2, 19, 24, 0.6);
            border: 1px solid rgba(0, 192, 194, 0.1);
        }

        .card {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .dark .card {
            background: rgba(2, 19, 24, 0.8);
            border: 1px solid rgba(0, 192, 194, 0.1);
        }

        body::-webkit-scrollbar { width: 8px; }
        body::-webkit-scrollbar-track { background: #01090D; }
        body::-webkit-scrollbar-thumb { background: #00C0C2; border-radius: 4px; }

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

        #sidebar { transition: width 0.3s ease; }
        #main-content { transition: margin-left 0.3s ease; }

        /* Smooth transitions for sidebar elements */
        #header-expanded, #header-collapsed, .nav-text, #usage-card {
            transition: opacity 0.25s ease;
        }
        
        /* Default: expanded visible, collapsed hidden */
        #header-expanded {
            display: flex;
        }
        #header-collapsed {
            display: none;
        }
        
        /* Collapsed state: swap visibility */
        .sidebar-collapsed #header-expanded {
            display: none;
        }
        .sidebar-collapsed #header-collapsed {
            display: flex;
            justify-content: center;
        }
        
        /* Fade nav text and usage card */
        .sidebar-collapsed .nav-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
            transition: opacity 0.15s ease, width 0.25s ease;
        }
        .nav-text {
            transition: opacity 0.15s ease 0.1s, width 0.25s ease;
        }
        
        /* Usage card smooth transition */
        #usage-card {
            transition: opacity 0.3s ease 0.15s, visibility 0.3s ease 0.15s;
        }
        .sidebar-collapsed #usage-card {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.15s ease, visibility 0.15s ease;
        }
    </style>
    @yield('styles')
</head>
<body class="antialiased bg-slate-100 dark:bg-brand-dark min-h-screen">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white dark:bg-brand-darker border-r border-slate-200 dark:border-white/5 z-40 hidden lg:block">
            <!-- Header -->
            <div class="p-4 border-b border-slate-200 dark:border-white/5">
                <!-- Expanded Header -->
                <div id="header-expanded" class="flex items-center justify-between">
                    <a href="/dashboard" class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-primary to-brand-accent flex items-center justify-center text-white shadow-lg shadow-brand-primary/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span id="logo-text" class="font-bold text-lg text-slate-900 dark:text-white">TruthAI</span>
                    </a>
                    <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 text-slate-500 dark:text-slate-400 hover:text-brand-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
                <!-- Collapsed Header -->
                <div id="header-collapsed">
                    <div class="relative group">
                        <a href="/dashboard" class="flex w-9 h-9 rounded-xl bg-gradient-to-br from-brand-primary to-brand-accent items-center justify-center text-white shadow-lg shadow-brand-primary/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </a>
                        <button onclick="toggleSidebar()" class="absolute inset-0 w-9 h-9 rounded-xl bg-brand-primary flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1">
                <a href="/dashboard" data-tooltip="Dashboard" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 @if(request()->is('dashboard')) active @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="nav-text font-medium">Dashboard</span>
                </a>
                <a href="/dashboard/detector" data-tooltip="Detector" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 @if(request()->is('dashboard/detector')) active @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="nav-text font-medium">Detector</span>
                </a>
                <a href="/dashboard/humanizer" data-tooltip="Humanizer" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 @if(request()->is('dashboard/humanizer')) active @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    <span class="nav-text font-medium">Humanizer</span>
                </a>
                <a href="/dashboard/history" data-tooltip="History" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 @if(request()->is('dashboard/history')) active @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="nav-text font-medium">History</span>
                </a>

                <div class="pt-4 mt-4 border-t border-slate-200 dark:border-white/5">
                    <a href="/dashboard/settings" data-tooltip="Settings" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 dark:text-slate-400 @if(request()->is('dashboard/settings')) active @endif">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="nav-text font-medium">Settings</span>
                    </a>
                </div>
            </nav>

            <!-- Usage Stats -->
            <div id="usage-card" class="absolute bottom-0 left-0 right-0 p-4">
                <div class="card rounded-xl p-4">
                    @php
                        $todayScans = auth()->user()->today_scans_count;
                        $dailyLimit = 100;
                        $percentage = min(($todayScans / $dailyLimit) * 100, 100);
                    @endphp
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400">Scans Today</span>
                        <span class="text-sm font-bold text-brand-primary">{{ $todayScans }}/{{ $dailyLimit }}</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-white/10 rounded-full h-2">
                        <div class="bg-gradient-to-r from-brand-primary to-brand-accent h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-500 mt-2">Upgrade for unlimited scans</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div id="main-content" class="flex-1 lg:ml-64">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 bg-white/80 dark:bg-brand-dark/80 backdrop-blur-xl border-b border-slate-200 dark:border-white/5">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/5">
                        <svg class="w-6 h-6 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    <!-- Search -->
                    <div class="hidden md:flex flex-1 max-w-md">
                        <div class="relative w-full">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" placeholder="Search scans..." class="input-field w-full pl-10 pr-4 py-2.5 rounded-xl outline-none">
                        </div>
                    </div>

                    <!-- Right Actions -->
                    <div class="flex items-center gap-4">
                        <!-- Theme Toggle -->
                        <button onclick="toggleTheme()" class="p-2.5 rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 transition-colors">
                            <svg id="sun-icon" class="w-5 h-5 text-slate-600 dark:text-slate-300 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <svg id="moon-icon" class="w-5 h-5 text-slate-600 dark:text-slate-300 block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </button>

                        <!-- Notifications -->
                        <div class="relative">
                            <button onclick="toggleNotifications()" class="relative p-2.5 rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 transition-colors">
                                <svg class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                <span id="notification-badge" class="hidden absolute top-1.5 right-1.5 w-2 h-2 bg-brand-primary rounded-full"></span>
                            </button>

                            <!-- Notifications Dropdown -->
                            <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-brand-darker rounded-xl shadow-lg border border-slate-200 dark:border-white/10 z-50 overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-white/10">
                                    <h3 class="font-semibold text-slate-900 dark:text-white">Notifications</h3>
                                    <button onclick="markAllNotificationsRead()" class="text-xs text-brand-primary hover:underline">Mark all read</button>
                                </div>
                                
                                <div id="notifications-list" class="max-h-80 overflow-y-auto">
                                    <div class="p-4 text-center text-slate-500 dark:text-slate-400">
                                        <p>Loading...</p>
                                    </div>
                                </div>
                                
                                <div class="px-4 py-3 border-t border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                                    <button onclick="clearAllNotifications()" class="w-full text-xs text-slate-500 hover:text-red-500 transition-colors">Clear all notifications</button>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button onclick="toggleProfileMenu()" class="flex items-center gap-3 pl-4 border-l border-slate-200 dark:border-white/10 cursor-pointer">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-primary to-brand-accent flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500">Free Plan</p>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="profile-menu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-brand-darker rounded-xl shadow-lg border border-slate-200 dark:border-white/10 py-2 z-50">
                                <a href="/dashboard/settings" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Settings
                                </a>
                                <div class="border-t border-slate-200 dark:border-white/10 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 w-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleMobileSidebar()"></div>

    <script>
        const html = document.documentElement;
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const headerExpanded = document.getElementById('header-expanded');
        const headerCollapsed = document.getElementById('header-collapsed');
        const logoText = document.getElementById('logo-text');
        const usageCard = document.getElementById('usage-card');
        const navTexts = document.querySelectorAll('.nav-text');

        let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

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

        function toggleSidebar() {
            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            applySidebarState();
        }

        function applySidebarState() {
            if (sidebarCollapsed) {
                sidebar.classList.add('sidebar-collapsed');
                sidebar.style.width = '80px';
                mainContent.style.marginLeft = '80px';
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.style.width = '256px';
                mainContent.style.marginLeft = '256px';
            }
        }

        function toggleMobileSidebar() {
            const mobileOverlay = document.getElementById('mobile-overlay');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                mobileOverlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('hidden');
                mobileOverlay.classList.add('hidden');
            }
        }

        // Initialize theme
        if (localStorage.getItem('theme') === 'light') {
            html.classList.remove('dark');
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
        }

        // Apply sidebar state on desktop
        if (window.innerWidth >= 1024) {
            applySidebarState();
        }

        // Profile dropdown toggle
        function toggleProfileMenu() {
            const menu = document.getElementById('profile-menu');
            menu.classList.toggle('hidden');
        }

        // Close profile menu when clicking outside
        document.addEventListener('click', function(e) {
            const profileMenu = document.getElementById('profile-menu');
            const profileButton = e.target.closest('[onclick="toggleProfileMenu()"]');
            if (!profileButton && profileMenu && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
            
            // Also close notifications dropdown when clicking outside
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            const notificationsButton = e.target.closest('[onclick="toggleNotifications()"]');
            if (!notificationsButton && notificationsDropdown && !notificationsDropdown.contains(e.target)) {
                notificationsDropdown.classList.add('hidden');
            }
        });

        // Notifications System
        function toggleNotifications() {
            const dropdown = document.getElementById('notifications-dropdown');
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                fetchNotifications();
            }
        }

        function fetchNotifications() {
            fetch('/dashboard/notifications')
                .then(res => res.json())
                .then(data => {
                    renderNotifications(data.notifications);
                    updateNotificationBadge(data.unread_count);
                })
                .catch(err => {
                    console.error('Failed to fetch notifications:', err);
                    document.getElementById('notifications-list').innerHTML = 
                        '<div class="p-4 text-center text-slate-500">Failed to load notifications</div>';
                });
        }

        function renderNotifications(notifications) {
            const container = document.getElementById('notifications-list');
            
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="p-6 text-center text-slate-500 dark:text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-2 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p>No notifications</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = notifications.map(n => `
                <div class="flex gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-white/5 cursor-pointer border-b border-slate-100 dark:border-white/5 last:border-0 ${n.is_read ? 'opacity-70' : ''}"
                     onclick="markNotificationRead(${n.id}, '${n.link || ''}')">
                    <span class="text-2xl flex-shrink-0">${n.icon || '🔔'}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 dark:text-white truncate">${n.title}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">${n.message}</p>
                        <p class="text-xs text-slate-400 mt-1">${n.time_ago}</p>
                    </div>
                    ${!n.is_read ? '<span class="w-2 h-2 bg-brand-primary rounded-full flex-shrink-0 mt-1.5"></span>' : ''}
                </div>
            `).join('');
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notification-badge');
            if (count > 0) {
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        function markNotificationRead(id, link) {
            fetch(`/dashboard/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                if (link) {
                    window.location.href = link;
                } else {
                    fetchNotifications();
                }
            });
        }

        function markAllNotificationsRead() {
            fetch('/dashboard/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                }
            }).then(() => fetchNotifications());
        }

        function clearAllNotifications() {
            if (confirm('Are you sure you want to clear all notifications?')) {
                fetch('/dashboard/notifications', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Content-Type': 'application/json'
                    }
                }).then(() => fetchNotifications());
            }
        }

        // Load notification badge on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/dashboard/notifications')
                .then(res => res.json())
                .then(data => updateNotificationBadge(data.unread_count))
                .catch(() => {});
        });
    </script>
    @yield('scripts')
</body>
</html>
