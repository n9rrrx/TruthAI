<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show dashboard home with stats
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get stats
        $totalScans = $user->scans()->count();
        $todayScans = $user->scans()->whereDate('created_at', today())->count();
        $aiDetected = $user->scans()->where('ai_score', '>=', 70)->count();
        $humanized = $user->scans()->where('type', 'humanize')->count();
        
        // Calculate AI detection rate
        $aiRate = $totalScans > 0 ? round(($aiDetected / $totalScans) * 100) : 0;
        
        // Get recent scans (last 5)
        $recentScans = $user->scans()
            ->latest()
            ->take(5)
            ->get();
        
        // Scans by type
        $textScans = $user->scans()->where('type', 'text')->count();
        $imageScans = $user->scans()->where('type', 'image')->count();
        $videoScans = $user->scans()->where('type', 'video')->count();
        $urlScans = $user->scans()->where('type', 'url')->count();
        
        return view('dashboard.index', compact(
            'totalScans', 
            'todayScans',
            'aiDetected', 
            'humanized',
            'aiRate',
            'recentScans',
            'textScans',
            'imageScans',
            'videoScans',
            'urlScans'
        ));
    }
}
