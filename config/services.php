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

    'restaurants_api' => [
        'key' => env('RESTAURANTS_API_KEY'),
        'host' => env('RESTAURANTS_API_HOST'),
        'base_url' => 'https://worldwide-restaurants.p.rapidapi.com',
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
    // config/services.php
    'countries' => [
        'base_url' => env('REST_COUNTRIES_BASE_URL', 'https://api.restcountries.com'),
        'api_key' => env('REST_COUNTRIES_API_KEY'),
    ],
    'weatherapi' => [
        'base_url' => env('WEATHER_BASE_URL', 'https://api.weatherapi.com/v1'),
        'key' => env('WEATHER_API_KEY'),
    ],
    'rapidapi' => [
        'key' => env('RAPIDAPI_KEY'),
        'host' => env('RAPIDAPI_HOST', 'https://booking-com15.p.rapidapi.com/api/v1'),
    ],
    'hotels' => [

        'base_url' => env('HOTELS_API_URL'),

        'key' => env('HOTELS_API_KEY'),

        'host' => env('HOTELS_API_HOST'),

        'locale' => env('HOTELS_API_LOCALE', 'en_GB'),

        'domain' => env('HOTELS_API_DOMAIN', 'GB'),

        'cache_ttl' => 60 * 60 * 24,

    ],
    'flights' => [
    'base_url' => env('FLIGHTS_API_BASE_URL'),
    'key'      => env('FLIGHTS_API_KEY'),
    'host'     => env('FLIGHTS_API_HOST'),
    'locale'   => env('FLIGHTS_API_LOCALE', 'en_GB'),
    'domain'   => env('FLIGHTS_API_DOMAIN', 'GB'),
],
'tripgo' => [
    'key' => env('TRIPGO_API_KEY'),
],
'tripgo' => [
    'key' => env('TRIPGO_API_KEY'),
],
];
