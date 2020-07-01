<?php
/**
 * Appservice provider contains custom validation rule.
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap any application services.
     *
     * @return boolean
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Validator::extend(
            'mailorphone',
            function ($attribute, $value, $parameters, $validator) {
                // When attribute is mobile.
                // Else When attribute is email.
                if (filter_var($value, FILTER_VALIDATE_INT) !== false && (strlen($value) > 8 && strlen($value) <= 12)) {
                    return true;
                } else if (filter_var($value, FILTER_VALIDATE_EMAIL) !== false && strlen($value) <= 200) {
                    return true;
                }
            }
        );

        Validator::extendImplicit(
            'sourcevalue',
            function ($attribute, $value, $parameters, $validator) {
                // When source is phone then source type shoud be numeric.
                if ((int) $parameters[0] === PHONE_SOURCE_ID && filter_var($value, FILTER_VALIDATE_INT) !== false && (strlen($value) > 8 && strlen($value) <= 12)) {
                    return true;
                } else if ((int) $parameters[0] === EMAIL_SOURCE_ID && filter_var($value, FILTER_VALIDATE_EMAIL) !== false && strlen($value) <= 200) {
                    return true;
                } else if ((in_array((int) $parameters[0], [GOOGLE_SOURCE_ID, FACEBOOK_SOURCE_ID, APPLE_SOURCE_ID]) === true) && is_string($value) === true) {
                    return true;
                }
            }
        );

        // Required if type is numeric.
        Validator::extendImplicit(
            'required_if_type_numeric',
            // If attrinute type is numeric then dial code is required other not required.
            function ($attribute, $value, $parameters, $validator) {
                if (is_numeric($parameters[1]) === true && ($value) !== null) {
                    return true;
                } else if (is_numeric($parameters[1]) === false) {
                    return true;
                }
            }
        );

        return false;

    }//end boot()


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }//end register()


}//end class
