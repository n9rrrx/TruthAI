@extends('layouts.dashboard')

@section('title', 'AI Detector')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">AI Content Detector</h1>
        <p class="text-slate-500 dark:text-slate-400">Multi-provider AI detection with weighted consensus for maximum accuracy.</p>
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
    </div>

    <!-- Main Content -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Input Panel -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Input Content</h2>
                <span id="char-count" class="text-sm text-slate-500">0 characters</span>
            </div>
            
            <!-- Text Input -->
            <div id="input-text" class="detection-input">
                <textarea id="content-input" class="input-field w-full h-80 p-4 rounded-xl resize-none outline-none" placeholder="Paste your text here to analyze for AI-generated content... (minimum 50 characters)" oninput="updateCharCount()"></textarea>
            </div>

            <!-- URL Input -->
            <div id="input-url" class="detection-input hidden">
                <input type="url" id="url-input" class="input-field w-full p-4 rounded-xl outline-none mb-4" placeholder="Enter URL to analyze (e.g., https://example.com/article)">
                <div class="h-64 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-xl flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        <p class="text-slate-400 dark:text-slate-500">URL content will be extracted</p>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div id="error-message" class="hidden mt-4 bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                <p class="text-red-500 text-sm"></p>
            </div>

            <!-- Scan Button -->
            <button id="scan-btn" onclick="runDetection()" class="w-full mt-4 bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-primary/20 hover:shadow-brand-primary/40 transition-all hover:scale-[1.01] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                <svg id="scan-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <svg id="loading-icon" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span id="scan-text">Analyze Content</span>
            </button>
        </div>

        <!-- Results Panel -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Analysis Results</h2>
            
            <!-- Empty State -->
            <div id="results-empty" class="flex flex-col items-center justify-center h-80">
                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </div>
                <p class="text-lg font-medium text-slate-900 dark:text-white">Ready to Analyze</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 text-center max-w-xs">Paste your text and click analyze to detect AI-generated content.</p>
            </div>

            <!-- Results Content (hidden initially) -->
            <div id="results-content" class="hidden">
                <!-- Score Display -->
                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-40 h-40">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="none" class="text-slate-200 dark:text-white/10"></circle>
                            <circle id="score-circle" cx="80" cy="80" r="70" stroke="url(#gradient)" stroke-width="12" fill="none" stroke-dasharray="440" stroke-dashoffset="440" stroke-linecap="round" style="transition: stroke-dashoffset 1s ease-out;"></circle>
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop id="gradient-start" offset="0%" stop-color="#22c55e"></stop>
                                    <stop id="gradient-end" offset="100%" stop-color="#16a34a"></stop>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span id="score-value" class="text-4xl font-bold text-slate-900 dark:text-white">0%</span>
                            <span id="score-label" class="text-sm font-medium">AI Score</span>
                        </div>
                    </div>
                </div>

                <!-- Verdict -->
                <div id="verdict-box" class="rounded-xl p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div id="verdict-icon" class="w-10 h-10 rounded-full flex items-center justify-center"></div>
                        <div>
                            <p id="verdict-title" class="font-bold"></p>
                            <p id="verdict-desc" class="text-sm opacity-80"></p>
                        </div>
                    </div>
                </div>

                <!-- Word Count -->
                <div class="flex items-center justify-between mb-4 text-sm">
                    <span class="text-slate-500">Word Count</span>
                    <span id="word-count" class="font-semibold text-slate-900 dark:text-white">0</span>
                </div>

                <!-- Detection Sources -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-3">Detection Sources</h3>
                    <div id="provider-results" class="space-y-2">
                        <!-- Dynamic provider results -->
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <a href="/dashboard/humanizer" id="humanize-btn" class="flex-1 bg-brand-primary text-white font-semibold py-3 rounded-xl text-center hover:bg-brand-primary/90 transition-colors hidden">
                        Humanize Text
                    </a>
                    <button onclick="resetDetector()" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        Scan Again
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentType = 'text';

    function setDetectionType(type) {
        currentType = type;
        
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

    function updateCharCount() {
        const text = document.getElementById('content-input').value;
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

    async function runDetection() {
        hideError();
        
        const content = currentType === 'text' 
            ? document.getElementById('content-input').value 
            : document.getElementById('url-input').value;

        // Different validation for URL vs text
        if (currentType === 'url') {
            if (!content || !content.match(/^https?:\/\/.+/)) {
                showError('Please enter a valid URL (starting with http:// or https://)');
                return;
            }
        } else {
            if (content.length < 50) {
                showError('Please enter at least 50 characters for accurate detection.');
                return;
            }
        }

        // Show loading state
        const btn = document.getElementById('scan-btn');
        btn.disabled = true;
        document.getElementById('scan-icon').classList.add('hidden');
        document.getElementById('loading-icon').classList.remove('hidden');
        document.getElementById('scan-text').textContent = 'Analyzing...';

        try {
            const response = await fetch('/dashboard/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    content: content,
                    type: currentType,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Detection failed');
            }

            displayResults(data.scan);

        } catch (error) {
            showError(error.message);
        } finally {
            // Reset button
            btn.disabled = false;
            document.getElementById('scan-icon').classList.remove('hidden');
            document.getElementById('loading-icon').classList.add('hidden');
            document.getElementById('scan-text').textContent = 'Analyze Content';
        }
    }

    function displayResults(scan) {
        // Hide empty state, show results
        document.getElementById('results-empty').classList.add('hidden');
        document.getElementById('results-content').classList.remove('hidden');

        const aiScore = Math.round(scan.ai_score);
        
        // Animate score circle
        const circle = document.getElementById('score-circle');
        const dashOffset = 440 - (440 * aiScore / 100);
        circle.style.strokeDashoffset = dashOffset;

        // Update score value
        document.getElementById('score-value').textContent = aiScore + '%';
        document.getElementById('word-count').textContent = scan.word_count;

        // Set colors based on score
        const gradientStart = document.getElementById('gradient-start');
        const gradientEnd = document.getElementById('gradient-end');
        const scoreLabel = document.getElementById('score-label');
        
        if (aiScore >= 70) {
            gradientStart.setAttribute('stop-color', '#ef4444');
            gradientEnd.setAttribute('stop-color', '#f97316');
            scoreLabel.className = 'text-sm font-medium text-red-500';
            scoreLabel.textContent = 'AI Detected';
            setVerdict('ai_generated', aiScore);
        } else if (aiScore >= 40) {
            gradientStart.setAttribute('stop-color', '#eab308');
            gradientEnd.setAttribute('stop-color', '#f59e0b');
            scoreLabel.className = 'text-sm font-medium text-yellow-500';
            scoreLabel.textContent = 'Mixed Content';
            setVerdict('mixed', aiScore);
        } else {
            gradientStart.setAttribute('stop-color', '#22c55e');
            gradientEnd.setAttribute('stop-color', '#16a34a');
            scoreLabel.className = 'text-sm font-medium text-green-500';
            scoreLabel.textContent = 'Human Written';
            setVerdict('human', aiScore);
        }

        // Show humanize button if AI detected
        if (aiScore >= 40) {
            document.getElementById('humanize-btn').classList.remove('hidden');
        } else {
            document.getElementById('humanize-btn').classList.add('hidden');
        }

        // Display provider results
        const providerDiv = document.getElementById('provider-results');
        providerDiv.innerHTML = scan.results.map(result => {
            const scoreColor = result.ai_score >= 70 ? 'text-red-500' : result.ai_score >= 40 ? 'text-yellow-500' : 'text-green-500';
            return `
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-white/5 rounded-lg">
                    <span class="text-sm text-slate-700 dark:text-slate-300">${result.provider_name}</span>
                    <span class="text-sm font-bold ${scoreColor}">${Math.round(result.ai_score)}%</span>
                </div>
            `;
        }).join('');
    }

    function setVerdict(type, score) {
        const box = document.getElementById('verdict-box');
        const icon = document.getElementById('verdict-icon');
        const title = document.getElementById('verdict-title');
        const desc = document.getElementById('verdict-desc');

        if (type === 'ai_generated') {
            box.className = 'rounded-xl p-4 mb-6 bg-red-500/10 border border-red-500/20';
            icon.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-red-500/20';
            icon.innerHTML = '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
            title.className = 'font-bold text-red-600 dark:text-red-400';
            title.textContent = 'Likely AI Generated';
            desc.className = 'text-sm text-red-500/80';
            desc.textContent = 'High probability of AI-written content';
        } else if (type === 'mixed') {
            box.className = 'rounded-xl p-4 mb-6 bg-yellow-500/10 border border-yellow-500/20';
            icon.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-yellow-500/20';
            icon.innerHTML = '<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            title.className = 'font-bold text-yellow-600 dark:text-yellow-400';
            title.textContent = 'Mixed Content';
            desc.className = 'text-sm text-yellow-500/80';
            desc.textContent = 'Contains both AI and human-written elements';
        } else {
            box.className = 'rounded-xl p-4 mb-6 bg-green-500/10 border border-green-500/20';
            icon.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-green-500/20';
            icon.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            title.className = 'font-bold text-green-600 dark:text-green-400';
            title.textContent = 'Likely Human Written';
            desc.className = 'text-sm text-green-500/80';
            desc.textContent = 'Content appears to be written by a human';
        }
    }

    function resetDetector() {
        document.getElementById('content-input').value = '';
        document.getElementById('url-input').value = '';
        document.getElementById('results-content').classList.add('hidden');
        document.getElementById('results-empty').classList.remove('hidden');
        document.getElementById('score-circle').style.strokeDashoffset = '440';
        updateCharCount();
    }
</script>
@endsection
