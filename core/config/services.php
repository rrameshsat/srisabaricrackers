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

    'demo' => [
        'enabled' => false,
    ],

    'shippo' => [
        'private_key' => env('SHIPPO_PRIVATE'),
        'from_address' => [
            'object_purpose' => 'PURCHASE',
            'name' => env('SHIPPO_FROM_NAME', ''),
            'company' => env('SHIPPO_FROM_COMPANY', ''),
            'street1' => env('SHIPPO_FROM_STREET1', ''),
            'city' => env('SHIPPO_FROM_CITY', ''),
            'state' => env('SHIPPO_FROM_STATE', ''),
            'zip' => env('SHIPPO_FROM_ZIP', ''),
            'country' => env('SHIPPO_FROM_COUNTRY', 'US'),
            'phone' => env('SHIPPO_FROM_PHONE', ''),
            'email' => env('SHIPPO_FROM_EMAIL', ''),
        ],
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
