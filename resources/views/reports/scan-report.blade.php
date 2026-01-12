<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TruthAI Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.6;
            padding: 40px;
        }
        .header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 2px solid #00C0C2;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #00C0C2;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #64748b;
            font-size: 14px;
        }
        .score-box {
            text-align: center;
            padding: 30px;
            margin: 30px 0;
            border-radius: 12px;
        }
        .score-box.ai {
            background: #fef2f2;
            border: 2px solid #ef4444;
        }
        .score-box.mixed {
            background: #fefce8;
            border: 2px solid #eab308;
        }
        .score-box.human {
            background: #f0fdf4;
            border: 2px solid #22c55e;
        }
        .score-value {
            font-size: 64px;
            font-weight: bold;
        }
        .score-box.ai .score-value { color: #ef4444; }
        .score-box.mixed .score-value { color: #eab308; }
        .score-box.human .score-value { color: #22c55e; }
        .score-label {
            font-size: 18px;
            font-weight: 600;
            margin-top: 10px;
        }
        .section {
            margin: 25px 0;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-label {
            color: #64748b;
        }
        .info-value {
            font-weight: 600;
            color: #1e293b;
        }
        .content-preview {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            font-size: 13px;
            color: #475569;
            word-wrap: break-word;
        }
        .provider-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 15px;
            background: #f8fafc;
            margin-bottom: 8px;
            border-radius: 6px;
        }
        .footer {
            text-align: center;
            padding-top: 30px;
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge.text { background: #dbeafe; color: #1d4ed8; }
        .badge.image { background: #f3e8ff; color: #7c3aed; }
        .badge.video { background: #fce7f3; color: #be185d; }
        .badge.url { background: #fef3c7; color: #b45309; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🔍 TruthAI</div>
        <div class="subtitle">AI Content Detection Report</div>
    </div>

    @php
        $scoreClass = $scan->ai_score >= 70 ? 'ai' : ($scan->ai_score >= 40 ? 'mixed' : 'human');
        $verdictText = $scan->ai_score >= 70 ? 'AI Detected' : ($scan->ai_score >= 40 ? 'Mixed Content' : 'Human Content');
    @endphp

    <div class="score-box {{ $scoreClass }}">
        <div class="score-value">{{ round($scan->ai_score) }}%</div>
        <div class="score-label">{{ $verdictText }}</div>
    </div>

    <div class="section">
        <div class="section-title">Report Details</div>
        <div class="info-row">
            <span class="info-label">Scan ID</span>
            <span class="info-value">#{{ $scan->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date</span>
            <span class="info-value">{{ $scan->created_at->format('F j, Y - g:i A') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Content Type</span>
            <span class="info-value">
                <span class="badge {{ $scan->type }}">{{ ucfirst($scan->type) }}</span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">AI Score</span>
            <span class="info-value">{{ round($scan->ai_score) }}%</span>
        </div>
        <div class="info-row">
            <span class="info-label">Human Score</span>
            <span class="info-value">{{ round($scan->human_score) }}%</span>
        </div>
        <div class="info-row">
            <span class="info-label">Verdict</span>
            <span class="info-value">{{ $scan->verdict }}</span>
        </div>
    </div>

    @if($scan->results && count($scan->results) > 0)
    <div class="section">
        <div class="section-title">Detection Sources</div>
        @foreach($scan->results as $result)
        <div class="provider-row">
            <span class="info-label">{{ $result->provider_name }}</span>
            <span class="info-value">{{ round($result->ai_score) }}%</span>
        </div>
        @endforeach
    </div>
    @endif

    <div class="section">
        <div class="section-title">Content Analyzed</div>
        <div class="content-preview">
            {{ Str::limit($scan->content, 500) }}
        </div>
    </div>

    <div class="footer">
        <p>Generated by TruthAI • {{ now()->format('F j, Y') }}</p>
        <p style="margin-top: 5px;">This report is for informational purposes only.</p>
    </div>
</body>
</html>
