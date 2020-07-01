<?php
/**
 * GH configuration
 */

return [

    'es_location_autocomplete' => [
        'server'             => [
            // Should NOT BE BEGINNING HTTP/HTTPS.
            'host'   => env('es_host', 'localhost'),
            'port'   => 443,
            'scheme' => 'https',
        ],
        'location_index'     => 'locations',
        'autocomplete_field' => 'location_suggest',

    ],
    'search'                   => ['non_featured_properties_per_page_in_percentage' => 25],
    'firebase'                 => [
        'api_key'    => 'AIzaSyCAXhqJhHO6qQZaxE7SIMq0e5Yqnkh2O14',
        'domain_url' => 'https://g-h.app?',
    ],
    'properly'                 => ['host_payout_based_on_checkout_start_date' => '2019-11-01'],

    'user_status'              => [
        'not_found'    => 'NOT_FOUND',
        'verified'     => 'VERIFIED',
        'not_verified' => 'NOT_VERIFIED',
    ],

    'login_otp_type' => 0,
    'forgot_password_otp_type' => 1,
];
