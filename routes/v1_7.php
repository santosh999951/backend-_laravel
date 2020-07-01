<?php
/**
 * File defining Api routes.
 **/
$router->group(
    ['namespace' => '\App\Http\Controllers\v1_6'],
    function () use ($router) {
        $router->post('device', 'DeviceController@postRegister');
    }
);
// Device unique id in headers.
$router->group(
    ['middleware' => 'device'],
    function () use ($router) {
        // Garden Api.
        $router->put('user/verify/otp', 'UserController@putVerifyOtp');
        $router->put('user/verify/forgotpassword/otp', 'UserController@putVerifyForgotOtp');
        $router->post('user', 'UserController@postRegister');

        $router->post('user/login', 'UserController@postLogin');
        $router->get('user/status', 'UserController@getUserStatus');
        $router->post('user/generate/otp', 'UserController@postGenerateOtp');

        $router->post('user/reset/password', 'UserController@postResetPasswordOtp');
        $router->put('user/reset/password', 'UserController@putResetPassword');

        $router->group(
            ['namespace' => '\App\Http\Controllers\v1_6'],
            function () use ($router) {
                $router->get('countrydetails', 'StaticController@getCountryDetails');
            }
        );
    }
);
