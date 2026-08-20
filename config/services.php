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

    'biteship' => [
        'api_key' => env('BITESHIP_API_KEY', ''),
        'origin_contact_name' => env('BITESHIP_ORIGIN_CONTACT_NAME', 'Bagus'),
        'origin_contact_phone' => env('BITESHIP_ORIGIN_CONTACT_PHONE', '6285280028167'),
        'origin_address' => env('BITESHIP_ORIGIN_ADDRESS', 'jl. Kalisuren, kantor WayKopi Greenwood, Tajurhalang, Bogor, Jawa Barat, 16320, Indonesia'),
        'origin_note' => env('BITESHIP_ORIGIN_NOTE', 'Dekat gerbang utama Way Kopi Roastery'),
        'origin_area_id' => env('BITESHIP_ORIGIN_AREA_ID', 'IDNP9IDNC74IDND6752IDZ16320'),
        'origin_postal_code' => env('BITESHIP_ORIGIN_POSTAL_CODE', '16320'),
        'origin_latitude' => (float) env('BITESHIP_ORIGIN_LATITUDE', -6.4714015),
        'origin_longitude' => (float) env('BITESHIP_ORIGIN_LONGITUDE', 106.7453021),
        'webhook_secret' => env('BITESHIP_WEBHOOK_SECRET', ''),
        'auto_create_order' => (bool) env('BITESHIP_AUTO_CREATE_ORDER', true),
    ],

    'xendit' => [
        'secret_key' => env('XENDIT_SECRET_KEY', ''),
        'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', ''),
    ],

    'waha' => [
        'api_url' => env('WAHA_API_URL', 'http://localhost:3000'),
        'api_key' => env('WAHA_API_KEY', ''),
        'session' => env('WAHA_SESSION', 'default'),
        'admin_phone' => env('ADMIN_WA_PHONE', '6281234567890'),
    ],

];
