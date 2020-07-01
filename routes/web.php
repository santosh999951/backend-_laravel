<?php

/*
    |--------------------------------------------------------------------------
    | Application Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register all of the routes for an application.
    | It is a breeze. Simply tell Lumen the URIs it should respond to
    | and give it the Closure to call when that URI is requested.
    |
*/

require __DIR__.'/../constants/constants.php';

use Illuminate\Http\Request;

// v1.6 routes
$router->group(
    [
        'prefix'    => 'v1.6',
        'namespace' => '\App\Http\Controllers\v1_6',
    ],
    function () use ($router) {
        include __DIR__.'/../constants/v1_6.php';
        include __DIR__.'/v1_6.php';
    }
);

// v1.7 routes
$router->group(
    [
        'prefix'    => 'v1.7',
        'namespace' => '\App\Http\Controllers\v1_7',
    ],
    function () use ($router) {
        include __DIR__.'/v1_7.php';
    }
);
