@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Welcome back, John! 👋</h1>
            <p class="text-slate-500 dark:text-slate-400">Here's what's happening with your scans today.</p>
        </div>
        <a href="/dashboard/detector" class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-primary to-brand-accent text-white font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-primary/20 hover:shadow-brand-primary/40 transition-all hover:scale-[1.02]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Scan
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card rounded-2xl p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-brand-primary/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <span class="text-xs font-medium text-green-500 bg-green-500/10 px-2 py-1 rounded-full">+12%</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900 dark:text-white">247</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Total Scans</p>
        </div>

        <div class="card rounded-2xl p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <span class="text-xs font-medium text-red-500 bg-red-500/10 px-2 py-1 rounded-full">High</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900 dark:text-white">182</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">AI Detected</p>
        </div>

        <div class="card rounded-2xl p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-brand-accent/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <span class="text-xs font-medium text-brand-accent bg-brand-accent/10 px-2 py-1 rounded-full">+8%</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900 dark:text-white">65</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Humanized</p>
        </div>

        <div class="card rounded-2xl p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-medium text-green-500 bg-green-500/10 px-2 py-1 rounded-full">Pass</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-900 dark:text-white">99.2%</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Accuracy</p>
        </div>
    </div>

    <!-- Quick Actions & Recent -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Quick Actions</h2>
            <div class="space-y-3">
                <a href="/dashboard/detector" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-brand-primary/10 flex items-center justify-center group-hover:bg-brand-primary/20 transition-colors">
                        <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">Detect Text</p>
                        <p class="text-xs text-slate-500">Analyze text content</p>
                    </div>
                </a>
                <a href="/dashboard/detector" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center group-hover:bg-purple-500/20 transition-colors">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">Analyze Image</p>
                        <p class="text-xs text-slate-500">Detect AI images</p>
                    </div>
                </a>
                <a href="/dashboard/humanizer" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-brand-accent/10 flex items-center justify-center group-hover:bg-brand-accent/20 transition-colors">
                        <svg class="w-5 h-5 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">Humanize Text</p>
                        <p class="text-xs text-slate-500">Bypass detection</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Scans -->
        <div class="lg:col-span-2 card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Scans</h2>
                <a href="/dashboard/history" class="text-sm text-brand-primary hover:underline">View All</a>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">Essay_Analysis.txt</p>
                            <p class="text-xs text-slate-500">2 minutes ago</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-500">94% AI</span>
                </div>
                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">profile_photo.jpg</p>
                            <p class="text-xs text-slate-500">15 minutes ago</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-500">Human</span>
                </div>
                <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-white/5">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 dark:text-white">example.com/blog/...</p>
                            <p class="text-xs text-slate-500">1 hour ago</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/10 text-yellow-500">67% AI</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
