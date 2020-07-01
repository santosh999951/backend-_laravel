<?php
/**
 * DeviceMiddleware contain all functions to check device unique id is coming or not.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request as Request;

use Closure;
use App\Libraries\ApiResponse;
use App\Models\MobileAppDevice;

/**
 * Class DeviceMiddleware
 */
class DeviceMiddleware
{


    /**
     * Function check if request has vaid device unique id.
     *
     * @param \Illuminate\Http\Request $request Request object.
     * @param \Closure                 $next    Closure.
     *
     * @return \Illuminate\Http\JsonResponse Return json that user is authenticated or not.
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty($request->headers->get('device-unique-id')) === true) {
            return ApiResponse::validationFailed(['device-unique-id' => 'The device unique id header field is required.']);
        } else {
            // Get device unique id data.
            $row = MobileAppDevice::getDeviceByDeviceUniqueId($request->headers->get('device-unique-id'));
            if (empty($row) === true) {
                return ApiResponse::validationFailed(['device-unique-id' => 'The device unique id header field is invalid.']);
            }
        }

        return $next($request);


    }//end handle()


}//end class
