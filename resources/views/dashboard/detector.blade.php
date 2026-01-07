@extends('layouts.dashboard')

@section('title', 'AI Detector')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">AI Content Detector</h1>
        <p class="text-slate-500 dark:text-slate-400">Analyze text, images, URLs, or videos for AI-generated content.</p>
    </div>

    <!-- Detection Type Tabs -->
    <div class="flex flex-wrap gap-2">
        <button onclick="setDetectionType('text')" id="tab-text" class="detection-tab active px-5 py-2.5 rounded-xl font-medium transition-all bg-brand-primary text-white">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Text
            </span>
        </button>
        <button onclick="setDetectionType('url')" id="tab-url" class="detection-tab px-5 py-2.5 rounded-xl font-medium transition-all bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-white/10">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                URL
            </span>
        </button>
        <button onclick="setDetectionType('image')" id="tab-image" class="detection-tab px-5 py-2.5 rounded-xl font-medium transition-all bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-white/10">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Image
            </span>
        </button>
        <button onclick="setDetectionType('video')" id="tab-video" class="detection-tab px-5 py-2.5 rounded-xl font-medium transition-all bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-white/10">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                Video
            </span>
        </button>
    </div>

    <!-- Main Content -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Input Panel -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Input Content</h2>
            
            <!-- Text Input -->
            <div id="input-text" class="detection-input">
                <textarea class="input-field w-full h-80 p-4 rounded-xl resize-none outline-none" placeholder="Paste your text here to analyze for AI-generated content..."></textarea>
            </div>

            <!-- URL Input -->
            <div id="input-url" class="detection-input hidden">
                <input type="url" class="input-field w-full p-4 rounded-xl outline-none mb-4" placeholder="Enter URL to analyze (e.g., https://example.com/article)">
                <div class="h-64 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-xl flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        <p class="text-slate-400 dark:text-slate-500">URL preview will appear here</p>
                    </div>
                </div>
            </div>

            <!-- Image Input -->
            <div id="input-image" class="detection-input hidden">
                <div class="h-80 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-xl flex items-center justify-center cursor-pointer hover:border-brand-primary/50 transition-colors">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-slate-600 dark:text-slate-400 font-medium mb-2">Drop image here or click to upload</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">Supports JPG, PNG, GIF up to 10MB</p>
                    </div>
                </div>
            </div>

            <!-- Video Input -->
            <div id="input-video" class="detection-input hidden">
                <div class="h-80 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-xl flex items-center justify-center cursor-pointer hover:border-brand-primary/50 transition-colors">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        <p class="text-slate-600 dark:text-slate-400 font-medium mb-2">Drop video here or click to upload</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">Supports MP4, MOV, AVI up to 100MB</p>
                    </div>
                </div>
            </div>

            <!-- Scan Button -->
            <button class="w-full mt-4 bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-primary/20 hover:shadow-brand-primary/40 transition-all hover:scale-[1.01] flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Analyze Content
            </button>
        </div>

        <!-- Results Panel -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Analysis Results</h2>
            
            <!-- Score Display -->
            <div class="flex items-center justify-center mb-6">
                <div class="relative w-40 h-40">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="none" class="text-slate-200 dark:text-white/10"></circle>
                        <circle cx="80" cy="80" r="70" stroke="url(#gradient)" stroke-width="12" fill="none" stroke-dasharray="440" stroke-dashoffset="26.4" stroke-linecap="round"></circle>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#ef4444"></stop>
                                <stop offset="100%" stop-color="#f97316"></stop>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-bold text-slate-900 dark:text-white">94%</span>
                        <span class="text-sm text-red-500 font-medium">AI Detected</span>
                    </div>
                </div>
            </div>

            <!-- Verdict -->
            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold text-red-600 dark:text-red-400">Likely AI Generated</p>
                        <p class="text-sm text-red-500/80">High probability of AI-written content</p>
                    </div>
                </div>
            </div>

            <!-- Detection Sources -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-3">Detection Sources</h3>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-white/5 rounded-lg">
                        <span class="text-sm text-slate-700 dark:text-slate-300">GPTZero</span>
                        <span class="text-sm font-bold text-red-500">96%</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-white/5 rounded-lg">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Originality.ai</span>
                        <span class="text-sm font-bold text-red-500">92%</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-white/5 rounded-lg">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Copyleaks</span>
                        <span class="text-sm font-bold text-red-500">94%</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <a href="/dashboard/humanizer" class="flex-1 bg-brand-primary text-white font-semibold py-3 rounded-xl text-center hover:bg-brand-primary/90 transition-colors">
                    Humanize Text
                </a>
                <button class="px-4 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function setDetectionType(type) {
        // Reset all tabs
        document.querySelectorAll('.detection-tab').forEach(tab => {
            tab.classList.remove('bg-brand-primary', 'text-white');
            tab.classList.add('bg-slate-100', 'dark:bg-white/5', 'text-slate-600', 'dark:text-slate-400');
        });
        
        // Activate selected tab
        const activeTab = document.getElementById('tab-' + type);
        activeTab.classList.remove('bg-slate-100', 'dark:bg-white/5', 'text-slate-600', 'dark:text-slate-400');
        activeTab.classList.add('bg-brand-primary', 'text-white');
        
        // Hide all inputs
        document.querySelectorAll('.detection-input').forEach(input => {
            input.classList.add('hidden');
        });
        
        // Show selected input
        document.getElementById('input-' + type).classList.remove('hidden');
    }
</script>
@endsection
