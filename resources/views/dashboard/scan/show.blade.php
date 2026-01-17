@extends('layouts.dashboard')

@section('title', 'Scan Details')

@section('content')
<div class="space-y-6">
    <!-- Header with back button -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="/dashboard/history" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 text-slate-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $scan->title ?? 'Scan Details' }}</h1>
                <p class="text-slate-500 dark:text-slate-400">{{ $scan->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('scan.export-pdf', $scan) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/20 transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export PDF
            </a>
            <button onclick="deleteScan({{ $scan->id }})" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500/20 transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Delete
            </button>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Content Card -->
            <div class="card rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Scanned Content</h2>
                <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-4 max-h-96 overflow-y-auto">
                    <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap">{{ $scan->content }}</p>
                </div>
                <div class="flex items-center gap-4 mt-4 text-sm text-slate-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        {{ $scan->word_count ?? 0 }} words
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        {{ ucfirst($scan->type) }}
                    </span>
                </div>
            </div>

            <!-- Provider Results -->
            @if($scan->results && $scan->results->count() > 0)
            <div class="card rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Detection Results by Provider</h2>
                <div class="space-y-4">
                    @foreach($scan->results as $result)
                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-medium text-slate-900 dark:text-white">{{ $result->provider_name ?? ucfirst($result->provider) }}</span>
                            @if($result->ai_score >= 70)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-500">{{ number_format($result->ai_score, 1) }}% AI</span>
                            @elseif($result->ai_score >= 40)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-500/10 text-yellow-600">{{ number_format($result->ai_score, 1) }}% AI</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-500">{{ number_format($result->ai_score, 1) }}% AI</span>
                            @endif
                        </div>
                        <div class="h-2 bg-slate-200 dark:bg-white/10 rounded-full overflow-hidden">
                            @php
                                $barColor = $result->ai_score >= 70 ? 'bg-red-500' : ($result->ai_score >= 40 ? 'bg-yellow-500' : 'bg-green-500');
                            @endphp
                            <div class="{{ $barColor }} h-full rounded-full transition-all" style="width: {{ $result->ai_score }}%"></div>
                        </div>
                        @if($result->confidence)
                        <p class="text-xs text-slate-500 mt-2">Confidence: {{ number_format($result->confidence, 1) }}%</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Plagiarism Analysis -->
            @if($scan->plagiarism_score !== null)
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Plagiarism Analysis</h2>
                    @php
                        $plagScore = $scan->plagiarism_score ?? 0;
                    @endphp
                    @if($plagScore >= 40)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-500">High Risk</span>
                    @elseif($plagScore >= 15)
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/10 text-yellow-600">Moderate</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-500">Original</span>
                    @endif
                </div>
                
                <!-- Plagiarism Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold {{ $plagScore >= 40 ? 'text-red-500' : ($plagScore >= 15 ? 'text-yellow-600' : 'text-green-500') }}">{{ number_format($plagScore, 1) }}%</p>
                        <p class="text-xs text-slate-500">Plagiarized</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-white/5 rounded-xl p-4 text-center">
                        <p class="text-2xl font-bold text-green-500">{{ number_format($scan->original_score ?? (100 - $plagScore), 1) }}%</p>
                        <p class="text-xs text-slate-500">Original</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="h-3 bg-slate-200 dark:bg-white/10 rounded-full overflow-hidden">
                        @php
                            $plagBarColor = $plagScore >= 40 ? 'bg-red-500' : ($plagScore >= 15 ? 'bg-yellow-500' : 'bg-green-500');
                        @endphp
                        <div class="{{ $plagBarColor }} h-full rounded-full transition-all" style="width: {{ $plagScore }}%"></div>
                    </div>
                </div>

                <!-- Matched Sources -->
                @if($scan->plagiarism_sources && count($scan->plagiarism_sources) > 0)
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Matched Sources</h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($scan->plagiarism_sources as $source)
                        <div class="flex items-center justify-between bg-slate-50 dark:bg-white/5 rounded-lg p-3">
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                <a href="{{ $source['url'] ?? '#' }}" target="_blank" class="text-sm text-brand-primary hover:underline truncate">{{ $source['title'] ?? $source['url'] ?? 'Unknown Source' }}</a>
                            </div>
                            @if(isset($source['match_percentage']))
                            <span class="text-xs font-semibold text-red-500 flex-shrink-0 ml-2">{{ number_format($source['match_percentage'], 0) }}%</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <p class="text-sm text-slate-500 text-center py-2">No matching sources found. Content appears to be original.</p>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Overall Score Card -->
            <div class="card rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Analysis Result</h2>
                
                <!-- Circular Score -->
                <div class="flex justify-center mb-6">
                    <div class="relative">
                        @php
                            $score = $scan->ai_score ?? 0;
                            $circumference = 2 * 3.14159 * 54;
                            $offset = $circumference - ($score / 100) * $circumference;
                            $scoreColor = $score >= 70 ? '#ef4444' : ($score >= 40 ? '#eab308' : '#22c55e');
                        @endphp
                        <svg class="w-36 h-36 transform -rotate-90">
                            <circle cx="72" cy="72" r="54" stroke="currentColor" stroke-width="12" fill="none" class="text-slate-200 dark:text-white/10"></circle>
                            <circle cx="72" cy="72" r="54" stroke="{{ $scoreColor }}" stroke-width="12" fill="none" stroke-linecap="round" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($score, 0) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Verdict -->
                <div class="text-center">
                    @if($score >= 70)
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-500/10 text-red-500 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Likely AI Generated
                        </span>
                    @elseif($score >= 40)
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-yellow-500/10 text-yellow-600 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Mixed Content
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-500/10 text-green-500 font-semibold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Likely Human Written
                        </span>
                    @endif
                </div>
            </div>

            <!-- Details Card -->
            <div class="card rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Details</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Type</span>
                        <span class="font-medium text-slate-900 dark:text-white capitalize">{{ $scan->type }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Status</span>
                        <span class="font-medium text-slate-900 dark:text-white capitalize">{{ $scan->status }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">AI Score</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ number_format($scan->ai_score ?? 0, 1) }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Human Score</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ number_format($scan->human_score ?? 0, 1) }}%</span>
                    </div>
                    @if($scan->plagiarism_score !== null)
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Plagiarism</span>
                        @php $plag = $scan->plagiarism_score ?? 0; @endphp
                        <span class="font-medium {{ $plag >= 40 ? 'text-red-500' : ($plag >= 15 ? 'text-yellow-600' : 'text-green-500') }}">{{ number_format($plag, 1) }}%</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Word Count</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $scan->word_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Scanned</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $scan->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function deleteScan(scanId) {
        if (!confirm('Are you sure you want to delete this scan? This action cannot be undone.')) {
            return;
        }
        
        fetch(`/dashboard/scan/${scanId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '/dashboard/history';
            } else {
                alert('Failed to delete scan. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
</script>
@endsection
