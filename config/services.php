<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    // AI Detection APIs
    'gptzero' => [
        'api_key' => env('GPTZERO_API_KEY'),
    ],

    'copyleaks' => [
        'email' => env('COPYLEAKS_EMAIL'),
        'api_key' => env('COPYLEAKS_API_KEY'),
    ],

    'sapling' => [
        'api_key' => env('SAPLING_API_KEY'),
    ],

    'writer' => [
        'api_key' => env('WRITER_API_KEY'),
    ],

    // AI Humanization
    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
    ],

    // Image/Video AI Detection
    'sightengine' => [
        'api_user' => env('SIGHTENGINE_API_USER'),
        'api_secret' => env('SIGHTENGINE_API_SECRET'),
    ],

    // FFmpeg for video processing
    'ffmpeg' => [
        'path' => env('FFMPEG_PATH', 'C:\\Users\\DELL5\\AppData\\Local\\Microsoft\\WinGet\\Packages\\Gyan.FFmpeg_Microsoft.Winget.Source_8wekyb3d8bbwe\\ffmpeg-8.0.1-full_build\\bin\\ffmpeg.exe'),
    ],

    // Browsershot for headless browser URL fetching
    'browsershot' => [
        'node_path' => env('NODE_PATH', 'node'),
        'npm_path' => env('NPM_PATH', 'npm'),
    ],

    // Google Custom Search for Plagiarism Detection
    'google_search' => [
        'api_key' => env('GOOGLE_SEARCH_API_KEY'),
        'engine_id' => env('GOOGLE_SEARCH_ENGINE_ID'),
    ],

    // Stripe Payment
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'pro_price_id' => env('STRIPE_PRO_PRICE_ID'),
    ],

];
