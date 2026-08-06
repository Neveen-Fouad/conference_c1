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


'hotels_api' => [
    'key' => env('HOTELS_API_KEY'),
    'host' => env('HOTELS_API_HOST'),
    'base_url' => 'https://hotels-com-provider.p.rapidapi.com',
],
'restaurants_api' => [
    'key' => env('RESTAURANTS_API_KEY'),
    'host' => env('RESTAURANTS_API_HOST'),
    'base_url' => 'https://tripadvisor16.p.rapidapi.com/api/v1/restaurant',
],
'flights_api' => [
    'key' => env('FLIGHTS_API_KEY'),
    'host' => env('FLIGHTS_API_HOST', 'sky-scrapper.p.rapidapi.com'),
    'base_url' => 'https://sky-scrapper.p.rapidapi.com/api',
],
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

];
