<?php
return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
        'api_token' => [
            'driver' => 'token',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ],
    ],

    'tables_mapping' => [
        'oauth_access_tokens'           => 'oauth2_access_tokens',
        'oauth_auth_codes'              => 'oauth2_auth_codes',
        'oauth_clients'                 => 'oauth2_clients',
        'oauth_personal_access_clients' => 'oauth2_personal_access_clients',
        'oauth_refresh_tokens'          => 'oauth2_refresh_tokens',
    ],
];