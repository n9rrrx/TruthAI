<?php

require __DIR__ . '/vendor/autoload.php';

$url = 'https://www.dawn.com/news/1866287/pakistan-indonesia-to-set-up-jtc-to-boost-trade';

echo "Fetching: $url\n\n";

$html = @file_get_contents($url, false, stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
        'timeout' => 15,
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]));

if (!$html) {
    echo "Failed to fetch URL!\n";
    exit(1);
}

echo "HTML length: " . strlen($html) . " bytes\n";

// Check for article tag
if (preg_match('/<article[^>]*>(.*?)<\/article>/is', $html, $articleMatch)) {
    echo "Found <article> tag: " . strlen($articleMatch[1]) . " bytes\n";
} else {
    echo "NO <article> tag found\n";
}

// Check for main tag
if (preg_match('/<main[^>]*>(.*?)<\/main>/is', $html, $mainMatch)) {
    echo "Found <main> tag: " . strlen($mainMatch[1]) . " bytes\n";
} else {
    echo "NO <main> tag found\n";
}

// Check for paragraphs
preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $paragraphs);
echo "Paragraphs found: " . count($paragraphs[1]) . "\n";

// Show content from first 5 paragraphs
echo "\n--- First 5 paragraphs ---\n";
for ($i = 0; $i < min(5, count($paragraphs[1])); $i++) {
    $text = strip_tags($paragraphs[1][$i]);
    $text = preg_replace('/\s+/', ' ', trim($text));
    echo ($i+1) . ". " . substr($text, 0, 100) . "...\n";
}

// Check what classes exist in Dawn.com (useful for targeting)
echo "\n--- Looking for Dawn.com article class patterns ---\n";
preg_match_all('/class="([^"]*story[^"]*)"/', $html, $storyClasses);
print_r(array_unique($storyClasses[1]));
