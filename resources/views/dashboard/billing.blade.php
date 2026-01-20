@extends('layouts.dashboard')

@section('title', 'Billing')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Billing & Subscription</h1>
        <p class="text-slate-500 dark:text-slate-400">Manage your subscription and payment methods.</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-400 p-4 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 p-4 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 p-4 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('info') }}
        </div>
    @endif

    <!-- Current Plan Card -->
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Current Plan</h2>
                <p class="text-slate-500 dark:text-slate-400">Your subscription details</p>
            </div>
            @if($isPro)
                <span class="px-4 py-2 rounded-full bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Pro Plan
                </span>
            @else
                <span class="px-4 py-2 rounded-full bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 font-semibold">
                    Free Plan
                </span>
            @endif
        </div>

        <div class="grid md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Daily Scans</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ $user->today_scans_count }}<span class="text-lg text-slate-400">/{{ number_format($user->daily_limit) }}</span>
                </p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Detection Types</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                    @if($isPro) All @else Text Only @endif
                </p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Humanizer</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">
                    @if($isPro)
                        <span class="text-green-500">Included</span>
                    @else
                        <span class="text-slate-400">Pro Only</span>
                    @endif
                </p>
            </div>
        </div>

        @if($isPro && $subscription)
            <div class="border-t border-slate-200 dark:border-white/10 pt-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            @if($subscription->onGracePeriod())
                                Subscription ends on {{ $subscription->ends_at->format('F j, Y') }}
                            @else
                                Next billing date: {{ $subscription->asStripeSubscription()->current_period_end ? date('F j, Y', $subscription->asStripeSubscription()->current_period_end) : 'N/A' }}
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('billing.portal') }}" class="text-brand-primary hover:underline font-semibold">
                        Manage Subscription →
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Pricing Plans -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Free Plan -->
        <div class="card rounded-2xl p-6 {{ !$isPro ? 'ring-2 ring-brand-primary' : '' }}">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Free</h3>
                @if(!$isPro)
                    <span class="px-3 py-1 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-semibold">Current</span>
                @endif
            </div>
            <div class="mb-4">
                <span class="text-4xl font-bold text-slate-900 dark:text-white">$0</span>
                <span class="text-slate-500 dark:text-slate-400">/month</span>
            </div>
            <ul class="space-y-3 mb-6">
                <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    100 scans/day
                </li>
                <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Text detection only
                </li>
                <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Basic support
                </li>
                <li class="flex items-center gap-2 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Image/Video detection
                </li>
                <li class="flex items-center gap-2 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Text humanization
                </li>
            </ul>
            @if(!$isPro)
                <button disabled class="w-full py-3 rounded-xl bg-slate-200 dark:bg-white/10 text-slate-500 dark:text-slate-400 font-semibold cursor-not-allowed">
                    Current Plan
                </button>
            @endif
        </div>

        <!-- Pro Plan -->
        <div class="card rounded-2xl p-6 {{ $isPro ? 'ring-2 ring-brand-primary' : 'bg-gradient-to-br from-brand-primary/5 to-brand-accent/5' }}">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    Pro
                    <span class="px-2 py-0.5 rounded bg-brand-primary text-white text-xs">Popular</span>
                </h3>
                @if($isPro)
                    <span class="px-3 py-1 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-semibold">Current</span>
                @endif
            </div>
            <div class="mb-4">
                <span class="text-4xl font-bold text-slate-900 dark:text-white">$29</span>
                <span class="text-slate-500 dark:text-slate-400">/month</span>
            </div>
            <ul class="space-y-3 mb-6">
                <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <strong>10,000 scans/day</strong>
                </li>
                <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    All detection types
                </li>
                <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Text humanization
                </li>
                <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Image & Video detection
                </li>
                <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    API access
                </li>
                <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Priority support
                </li>
            </ul>
            @if($isPro)
                <a href="{{ route('billing.portal') }}" class="block w-full py-3 rounded-xl bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300 font-semibold text-center hover:bg-slate-300 dark:hover:bg-white/20 transition-colors">
                    Manage Subscription
                </a>
            @else
                <a href="{{ route('billing.checkout') }}" class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-primary to-brand-accent text-white font-semibold hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Upgrade to Pro
                </a>
            @endif
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Frequently Asked Questions</h2>
        
        <div class="space-y-4">
            <div class="border-b border-slate-200 dark:border-white/10 pb-4">
                <h3 class="font-semibold text-slate-900 dark:text-white mb-2">Can I cancel anytime?</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Yes, you can cancel your subscription at any time. You'll continue to have Pro access until the end of your billing period.</p>
            </div>
            <div class="border-b border-slate-200 dark:border-white/10 pb-4">
                <h3 class="font-semibold text-slate-900 dark:text-white mb-2">What payment methods do you accept?</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">We accept all major credit cards, debit cards, and some local payment methods through Stripe.</p>
            </div>
            <div>
                <h3 class="font-semibold text-slate-900 dark:text-white mb-2">What happens if I exceed my daily limit?</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Free users are limited to 100 scans/day. Pro users get 10,000 scans/day. Limits reset at midnight UTC.</p>
            </div>
        </div>
    </div>
</div>
@endsection
