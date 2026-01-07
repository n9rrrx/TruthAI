@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Settings</h1>
        <p class="text-slate-500 dark:text-slate-400">Manage your account and preferences.</p>
    </div>

    <!-- Profile Section -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Profile</h2>
        
        <div class="flex items-start gap-6 mb-6">
            <div class="relative">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-accent flex items-center justify-center text-white text-2xl font-bold">J</div>
                <button class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-white dark:bg-brand-card border border-slate-200 dark:border-white/10 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:text-brand-primary transition-colors shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </button>
            </div>
            <div>
                <h3 class="font-semibold text-slate-900 dark:text-white">John Doe</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">john@example.com</p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-primary">Pro Plan</span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                <input type="text" value="John Doe" class="input-field w-full p-3 rounded-xl outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                <input type="email" value="john@example.com" class="input-field w-full p-3 rounded-xl outline-none">
            </div>
        </div>

        <button class="mt-4 bg-brand-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-brand-primary/90 transition-colors">
            Save Changes
        </button>
    </div>

    <!-- Password Section -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Change Password</h2>
        
        <div class="space-y-4 max-w-md">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Current Password</label>
                <input type="password" placeholder="Enter current password" class="input-field w-full p-3 rounded-xl outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">New Password</label>
                <input type="password" placeholder="Enter new password" class="input-field w-full p-3 rounded-xl outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Confirm Password</label>
                <input type="password" placeholder="Confirm new password" class="input-field w-full p-3 rounded-xl outline-none">
            </div>
        </div>

        <button class="mt-4 bg-brand-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-brand-primary/90 transition-colors">
            Update Password
        </button>
    </div>

    <!-- Notifications -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Notifications</h2>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <div>
                    <p class="font-medium text-slate-900 dark:text-white">Email Notifications</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Receive scan results via email</p>
                </div>
                <button class="w-12 h-6 rounded-full bg-brand-primary relative transition-colors">
                    <span class="absolute right-1 top-1 w-4 h-4 rounded-full bg-white transition-transform"></span>
                </button>
            </div>
            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <div>
                    <p class="font-medium text-slate-900 dark:text-white">Weekly Reports</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Get weekly summary of your scans</p>
                </div>
                <button class="w-12 h-6 rounded-full bg-slate-300 dark:bg-white/20 relative transition-colors">
                    <span class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white transition-transform"></span>
                </button>
            </div>
            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <div>
                    <p class="font-medium text-slate-900 dark:text-white">Product Updates</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">News about new features</p>
                </div>
                <button class="w-12 h-6 rounded-full bg-brand-primary relative transition-colors">
                    <span class="absolute right-1 top-1 w-4 h-4 rounded-full bg-white transition-transform"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- API Keys -->
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">API Keys</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Manage your API access keys</p>
            </div>
            <button class="bg-brand-primary text-white font-semibold px-4 py-2 rounded-xl hover:bg-brand-primary/90 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Key
            </button>
        </div>
        
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-brand-primary/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </div>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">Production Key</p>
                        <p class="text-xs text-slate-500 font-mono">sk_live_**********************xyz</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="p-2 rounded-lg hover:bg-slate-200 dark:hover:bg-white/10 text-slate-400 hover:text-brand-primary transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    <button class="p-2 rounded-lg hover:bg-slate-200 dark:hover:bg-white/10 text-slate-400 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card rounded-2xl p-6 border-red-500/20">
        <h2 class="text-lg font-bold text-red-500 mb-2">Danger Zone</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">These actions are irreversible. Please be careful.</p>
        
        <div class="flex gap-3">
            <button class="px-4 py-2.5 rounded-xl border border-red-500/30 text-red-500 font-medium hover:bg-red-500/10 transition-colors">
                Delete All Data
            </button>
            <button class="px-4 py-2.5 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600 transition-colors">
                Delete Account
            </button>
        </div>
    </div>
</div>
@endsection
