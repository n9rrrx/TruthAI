@extends('layouts.dashboard')

@section('title', 'Scan History')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Scan History</h1>
            <p class="text-slate-500 dark:text-slate-400">View and manage your previous scans.</p>
        </div>
        <div class="flex gap-3">
            <select id="typeFilter" class="input-field px-4 py-2.5 rounded-xl outline-none text-sm cursor-pointer">
                <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>All Types</option>
                <option value="text" {{ request('type') === 'text' ? 'selected' : '' }}>Text</option>
                <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Image</option>
                <option value="url" {{ request('type') === 'url' ? 'selected' : '' }}>URL</option>
                <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
                <option value="humanize" {{ request('type') === 'humanize' ? 'selected' : '' }}>Humanize</option>
            </select>
            <select id="dateFilter" class="input-field px-4 py-2.5 rounded-xl outline-none text-sm cursor-pointer">
                <option value="7" {{ request('days', '7') === '7' ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ request('days') === '30' ? 'selected' : '' }}>Last 30 days</option>
                <option value="all" {{ request('days') === 'all' ? 'selected' : '' }}>All time</option>
            </select>
        </div>
    </div>

    <!-- History Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-white/5">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Content</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">AI Score</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Plagiarism</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($scans as $scan)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $iconColors = [
                                        'text' => 'red',
                                        'url' => 'yellow',
                                        'image' => 'green',
                                        'video' => 'purple',
                                        'humanize' => 'brand-accent'
                                    ];
                                    $color = $iconColors[$scan->type] ?? 'slate';
                                @endphp
                                <div class="w-10 h-10 rounded-lg bg-{{ $color }}-500/10 flex items-center justify-center">
                                    @if($scan->type === 'text')
                                        <svg class="w-5 h-5 text-{{ $color }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @elseif($scan->type === 'url')
                                        <svg class="w-5 h-5 text-{{ $color }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    @elseif($scan->type === 'image')
                                        <svg class="w-5 h-5 text-{{ $color }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @else
                                        <svg class="w-5 h-5 text-{{ $color }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $scan->title ?? 'Untitled Scan' }}</p>
                                    <p class="text-xs text-slate-500 truncate max-w-xs">{{ Str::limit($scan->content, 60) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-400 capitalize">{{ $scan->type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($scan->status === 'completed')
                                @if($scan->ai_score >= 70)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-500">{{ number_format($scan->ai_score, 0) }}% AI</span>
                                @elseif($scan->ai_score >= 40)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-500/10 text-yellow-600">{{ number_format($scan->ai_score, 0) }}% AI</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-500">{{ number_format($scan->ai_score, 0) }}% AI</span>
                                @endif
                            @elseif($scan->status === 'processing')
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-500/10 text-blue-500">Processing...</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-500/10 text-slate-500">{{ ucfirst($scan->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($scan->status === 'completed' && $scan->plagiarism_score !== null)
                                @if($scan->plagiarism_score >= 40)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-500">{{ number_format($scan->plagiarism_score, 0) }}%</span>
                                @elseif($scan->plagiarism_score >= 15)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-500/10 text-yellow-600">{{ number_format($scan->plagiarism_score, 0) }}%</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-500">{{ number_format($scan->plagiarism_score, 0) }}%</span>
                                @endif
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $scan->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="/dashboard/scan/{{ $scan->id }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 text-slate-400 hover:text-brand-primary transition-colors" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <button onclick="deleteScan({{ $scan->id }})" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-white/10 text-slate-400 hover:text-red-500 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="text-lg font-medium text-slate-900 dark:text-white">No scans yet</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Start by detecting AI content or humanizing text.</p>
                                <a href="/dashboard/detector" class="mt-2 px-4 py-2 rounded-xl bg-gradient-to-r from-brand-primary to-brand-accent text-white text-sm font-semibold">
                                    Start Detecting
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($scans->hasPages())
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Showing {{ $scans->firstItem() }}-{{ $scans->lastItem() }} of {{ $scans->total() }} results
            </p>
            <div class="flex gap-2">
                {{ $scans->links() }}
            </div>
        </div>
        @endif
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
                // Remove the row from the table
                window.location.reload();
            } else {
                alert('Failed to delete scan. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    // Filter functionality
    function applyFilters() {
        const typeFilter = document.getElementById('typeFilter').value;
        const dateFilter = document.getElementById('dateFilter').value;
        
        const params = new URLSearchParams();
        
        if (typeFilter && typeFilter !== 'all') {
            params.set('type', typeFilter);
        }
        
        if (dateFilter && dateFilter !== '7') {
            params.set('days', dateFilter);
        }
        
        const queryString = params.toString();
        const newUrl = window.location.pathname + (queryString ? '?' + queryString : '');
        window.location.href = newUrl;
    }

    // Add event listeners to filter dropdowns
    document.getElementById('typeFilter').addEventListener('change', applyFilters);
    document.getElementById('dateFilter').addEventListener('change', applyFilters);
</script>
@endsection