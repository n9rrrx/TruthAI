@extends('layouts.dashboard')

@section('title', 'Text Humanizer')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Text Humanizer</h1>
        <p class="text-slate-500 dark:text-slate-400">Rewrite AI-generated content to bypass detection tools.</p>
    </div>

    <!-- Main Content -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Input Panel -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Original Text</h2>
                <span id="char-count" class="text-xs text-slate-400">0 characters</span>
            </div>
            
            <textarea id="original-text" class="input-field w-full h-72 p-4 rounded-xl resize-none outline-none" placeholder="Paste your AI-generated text here..." oninput="updateCharCount()"></textarea>

            <!-- Options -->
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Writing Style</label>
                    <select id="style-select" class="input-field w-full p-3 rounded-xl outline-none">
                        <option value="academic">Academic</option>
                        <option value="casual">Casual</option>
                        <option value="professional">Professional</option>
                        <option value="creative">Creative</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Humanization Level</label>
                    <div class="flex gap-2">
                        <button onclick="setLevel('light')" id="level-light" class="level-btn flex-1 py-2 px-4 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 text-sm font-medium hover:bg-slate-200 dark:hover:bg-white/10 transition-colors">Light</button>
                        <button onclick="setLevel('medium')" id="level-medium" class="level-btn flex-1 py-2 px-4 rounded-lg bg-brand-primary text-white text-sm font-medium">Medium</button>
                        <button onclick="setLevel('strong')" id="level-strong" class="level-btn flex-1 py-2 px-4 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 text-sm font-medium hover:bg-slate-200 dark:hover:bg-white/10 transition-colors">Strong</button>
                    </div>
                    <p id="level-description" class="text-xs text-slate-500 mt-2">Balanced rewriting - changes ~30% of the text</p>
                </div>
            </div>

            <!-- Error Message -->
            <div id="error-message" class="hidden mt-4 bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                <p class="text-red-500 text-sm"></p>
            </div>

            <!-- Humanize Button -->
            <button id="humanize-btn" onclick="runHumanize()" class="w-full mt-4 bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-primary/20 hover:shadow-brand-primary/40 transition-all hover:scale-[1.01] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                <svg id="humanize-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                <svg id="loading-icon" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span id="btn-text">Humanize Text</span>
            </button>
        </div>

        <!-- Output Panel -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Humanized Text</h2>
                <div class="flex gap-2">
                    <button onclick="copyText()" class="p-2 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-white/10 transition-colors" title="Copy">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    <button onclick="downloadText()" class="p-2 rounded-lg bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-white/10 transition-colors" title="Download">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </button>
                </div>
            </div>
            
            <!-- Empty State -->
            <div id="output-empty" class="h-72 flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <p class="text-lg font-medium text-slate-900 dark:text-white">Ready to Humanize</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 text-center max-w-xs">Paste your text and click humanize to rewrite it.</p>
            </div>

            <!-- Output Content (hidden initially) -->
            <div id="output-content" class="hidden">
                <div id="humanized-text" class="h-72 p-4 rounded-xl bg-slate-50 dark:bg-white/5 overflow-y-auto">
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap"></p>
                </div>

                <!-- Word Count -->
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-slate-500">Word Count</span>
                    <span id="word-count" class="font-semibold text-slate-900 dark:text-white">0</span>
                </div>

                <!-- Success Message -->
                <div class="mt-4 p-4 bg-green-500/10 border border-green-500/20 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-green-600 dark:text-green-400">Text Humanized!</p>
                            <p class="text-sm text-green-500/80">Ready to pass AI detection tools</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 flex gap-3">
                    <button onclick="regenerate()" class="flex-1 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 font-medium hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        Regenerate
                    </button>
                    <button onclick="makeMoreHuman()" class="flex-1 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 font-medium hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        Make More Human
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Tips for Better Humanization</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <div class="w-10 h-10 rounded-lg bg-brand-primary/10 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Add Personal Touch</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400">Include personal anecdotes or opinions to make text more authentic.</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <div class="w-10 h-10 rounded-lg bg-brand-accent/10 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Manual Edits</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400">Make small manual edits after humanization for best results.</p>
            </div>
            <div class="p-4 bg-slate-50 dark:bg-white/5 rounded-xl">
                <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Multiple Passes</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400">Use "Regenerate" multiple times until you find the perfect version.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentLevel = 'medium';
    let lastHumanizedText = '';

    const levelDescriptions = {
        'light': 'Minimal changes - keeps ~85% of original text',
        'medium': 'Balanced rewriting - changes ~30% of the text',
        'strong': 'Heavy rewriting - changes ~50% for maximum bypass',
    };

    function setLevel(level) {
        currentLevel = level;
        
        // Update button styles
        document.querySelectorAll('.level-btn').forEach(btn => {
            btn.classList.remove('bg-brand-primary', 'text-white');
            btn.classList.add('bg-slate-100', 'dark:bg-white/5', 'text-slate-600', 'dark:text-slate-400');
        });
        
        const activeBtn = document.getElementById('level-' + level);
        activeBtn.classList.remove('bg-slate-100', 'dark:bg-white/5', 'text-slate-600', 'dark:text-slate-400');
        activeBtn.classList.add('bg-brand-primary', 'text-white');
        
        // Update description
        document.getElementById('level-description').textContent = levelDescriptions[level];
    }

    function updateCharCount() {
        const text = document.getElementById('original-text').value;
        document.getElementById('char-count').textContent = text.length + ' characters';
    }

    function showError(message) {
        const errorDiv = document.getElementById('error-message');
        errorDiv.querySelector('p').textContent = message;
        errorDiv.classList.remove('hidden');
    }

    function hideError() {
        document.getElementById('error-message').classList.add('hidden');
    }

    async function runHumanize() {
        hideError();
        
        const content = document.getElementById('original-text').value;
        const style = document.getElementById('style-select').value;

        if (content.length < 20) {
            showError('Please enter at least 20 characters.');
            return;
        }

        setLoading(true);

        try {
            const response = await fetch('/dashboard/humanize', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    content: content,
                    level: currentLevel,
                    style: style,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Humanization failed');
            }

            displayResult(data.result);

        } catch (error) {
            showError(error.message);
        } finally {
            setLoading(false);
        }
    }

    function displayResult(result) {
        document.getElementById('output-empty').classList.add('hidden');
        document.getElementById('output-content').classList.remove('hidden');
        
        lastHumanizedText = result.humanized;
        document.getElementById('humanized-text').querySelector('p').textContent = result.humanized;
        document.getElementById('word-count').textContent = result.word_count;
    }

    async function regenerate() {
        const content = document.getElementById('original-text').value;
        const style = document.getElementById('style-select').value;

        setLoading(true);

        try {
            const response = await fetch('/dashboard/humanize/regenerate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    content: content,
                    level: currentLevel,
                    style: style,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Regeneration failed');
            }

            lastHumanizedText = data.result.humanized;
            document.getElementById('humanized-text').querySelector('p').textContent = data.result.humanized;
            document.getElementById('word-count').textContent = data.result.word_count;

        } catch (error) {
            showError(error.message);
        } finally {
            setLoading(false);
        }
    }

    function makeMoreHuman() {
        // Set to strong level and regenerate
        setLevel('strong');
        regenerate();
    }

    function setLoading(loading) {
        const btn = document.getElementById('humanize-btn');
        btn.disabled = loading;
        document.getElementById('humanize-icon').classList.toggle('hidden', loading);
        document.getElementById('loading-icon').classList.toggle('hidden', !loading);
        document.getElementById('btn-text').textContent = loading ? 'Humanizing...' : 'Humanize Text';
    }

    function copyText() {
        if (lastHumanizedText) {
            navigator.clipboard.writeText(lastHumanizedText);
            // Show brief feedback
            const btn = event.target.closest('button');
            btn.classList.add('text-green-500');
            setTimeout(() => btn.classList.remove('text-green-500'), 1000);
        }
    }

    function downloadText() {
        if (lastHumanizedText) {
            const blob = new Blob([lastHumanizedText], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'humanized-text.txt';
            a.click();
            URL.revokeObjectURL(url);
        }
    }
</script>
@endsection
