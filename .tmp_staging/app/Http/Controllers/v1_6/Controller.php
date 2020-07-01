<?php
/**
 * Controller containing common methods
 */

namespace App\Http\Controllers\v1_6;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use \Auth;

/**
 * Class Controller
 */
class Controller extends BaseController
{


    /**
     * Get device unique id from headers
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return string Device unique id
     */
    protected function getDeviceUniqueId(Request $request)
    {
        return $request->headers->get('device-unique-id');

    }//end getDeviceUniqueId()


    /**
     * Get device type from headers
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return string Device source
     */
    protected function getDeviceType(Request $request)
    {
        // phpcs:ignore
        $source = (empty($request->headers->get('device-type')) === false && in_array($request->headers->get('device-type'), ['android', 'ios']) === true) ? 'app' : ((empty($request->headers->get('device-type')) === false && $request->headers->get('device-type') === 'website') ? 'website' : 'web');
        return $source;

    }//end getDeviceType()


    /**
     * Get App expiry version check from headers
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return array App Expiry check
     */
    protected function getAppExpiryCheck(Request $request)
    {
        $device_type = (empty($request->headers->get('device-type')) === false && in_array($request->headers->get('device-type'), ['android', 'ios']) === true) ? $request->headers->get('device-type') : '';
        $app_version = (empty($request->headers->get('app-version')) === false) ? $request->headers->get('app-version') : '';

        if (empty($device_type) === true || empty($app_version) === true) {
            return [];
        }

        $split_app_version = array_map('intval', explode('.', $app_version));
        if (count($split_app_version) !== 3 || $split_app_version[0] < 1) {
            return [];
        }

        $min_version    = ANDROID_MIN_VERSION;
        $latest_version = ANDROID_LATEST_VERSION;

        if ($device_type === 'ios') {
            $min_version    = IOS_MIN_VERSION;
            $latest_version = IOS_LATEST_VERSION;
        }

        $response = [];

        if ($app_version >= $min_version) {
            if ($app_version === $latest_version) {
                return [];
            } else if ($app_version < $latest_version) {
                $response['required']       = 0;
                $response['min_version']    = '';
                $response['latest_version'] = $latest_version;
                $response['text']           = LATEST_VERSION_TEXT;
                if ($device_type === 'android') {
                    $response['version_code'] = ANDROID_LATEST_VERSION_CODE;
                }
            } else {
                return [];
            }
        } else {
            $response['required']       = 1;
            $response['min_version']    = $min_version;
            $response['latest_version'] = '';
            $response['text']           = MIN_VERSION_TEXT;
            if ($device_type === 'android') {
                $response['version_code'] = ANDROID_MIN_VERSION_CODE;
            }
        }//end if

        return $response;

    }//end getAppExpiryCheck()


    /**
     * Is user logged in
     *
     * @return boolean check user login
     */
    protected function isUserLoggedIn()
    {
        return Auth::check();

    }//end isUserLoggedIn()


    /**
     * Get user data fetched from access token
     *
     * @return array user id
     */
    protected function getAuthUserData()
    {
        return [
            'user_id' => (int) Auth::user()->id,
        ];

    }//end getAuthUserData()


     /**
      * Get user  fetched from access token
      *
      * @return object user
      */
    protected function getAuthUser()
    {
          return Auth::user();

    }//end getAuthUser()


    /**
     * Validate request params
     *
     * @param \Illuminate\Http\Request $request Http request object.
     * @param array                    $rules   Validation rules for variable.
     *
     * @return array |boolean  Error message
     */
    protected function areParamsValid(Request $request, array $rules)
    {
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails() !== false) {
            $error_messages = $validator->errors()->messages();

            // Create error message by using key and value.
            foreach ($error_messages as $key => $value) {
                $error_messages[$key] = $value[0];
            }

            return $error_messages;
        }

        return true;

    }//end areParamsValid()


}//end class
