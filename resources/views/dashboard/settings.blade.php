@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')
<div class="space-y-6 max-w-4xl">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Settings</h1>
        <p class="text-slate-500 dark:text-slate-400">Manage your account and preferences.</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-400 p-4 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 p-4 rounded-xl">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Section -->
    <form action="{{ route('settings.profile') }}" method="POST" class="card rounded-2xl p-6">
        @csrf
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Profile</h2>
        
        <div class="flex items-start gap-6 mb-6">
            <div class="relative">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-accent flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h3 class="font-semibold text-slate-900 dark:text-white">{{ $user->name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-primary">
                    @if($user->provider === 'google')
                        Google Account
                    @else
                        Free Plan
                    @endif
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field w-full p-3 rounded-xl outline-none bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 focus:border-brand-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field w-full p-3 rounded-xl outline-none bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 focus:border-brand-primary">
            </div>
        </div>

        <button type="submit" class="mt-4 bg-brand-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-brand-primary/90 transition-colors">
            Save Changes
        </button>
    </form>

    <!-- Password Section -->
    <form action="{{ route('settings.password') }}" method="POST" class="card rounded-2xl p-6">
        @csrf
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Change Password</h2>
        
        @if($user->provider === 'google' && !$user->password)
            <p class="text-slate-500 dark:text-slate-400 mb-4">You signed up with Google. Set a password to enable email login.</p>
        @endif

        <div class="space-y-4 max-w-md">
            @if($user->password)
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Current Password</label>
                    <input type="password" name="current_password" placeholder="Enter current password" class="input-field w-full p-3 rounded-xl outline-none bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 focus:border-brand-primary">
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">New Password</label>
                <input type="password" name="password" placeholder="Enter new password" class="input-field w-full p-3 rounded-xl outline-none bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 focus:border-brand-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Confirm new password" class="input-field w-full p-3 rounded-xl outline-none bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 focus:border-brand-primary">
            </div>
        </div>

        <button type="submit" class="mt-4 bg-brand-primary text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-brand-primary/90 transition-colors">
            {{ $user->password ? 'Update Password' : 'Set Password' }}
        </button>
    </form>

    <!-- Account Stats -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Account Statistics</h2>
        
        <div class="grid md:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl text-center">
                <p class="text-3xl font-bold text-brand-primary">{{ $user->scans()->count() }}</p>
                <p class="text-sm text-slate-500">Total Scans</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl text-center">
                <p class="text-3xl font-bold text-brand-primary">{{ $user->today_scans_count }}</p>
                <p class="text-sm text-slate-500">Today's Scans</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl text-center">
                <p class="text-3xl font-bold text-brand-primary">{{ $user->created_at->diffForHumans(null, true) }}</p>
                <p class="text-sm text-slate-500">Member Since</p>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card rounded-2xl p-6 border-2 border-red-500/20">
        <h2 class="text-lg font-bold text-red-500 mb-2">Danger Zone</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">These actions are irreversible. Please be careful.</p>
        
        <div class="flex gap-3">
            <form action="{{ route('settings.delete-data') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all your scan data? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 rounded-xl border border-red-500/30 text-red-500 font-medium hover:bg-red-500/10 transition-colors">
                    Delete All Data
                </button>
            </form>
            <form action="{{ route('settings.delete-account') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone and all your data will be permanently deleted.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600 transition-colors">
                    Delete Account
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
