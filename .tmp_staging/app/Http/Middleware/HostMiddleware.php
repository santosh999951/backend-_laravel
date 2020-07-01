<?php
/**
 * HostMiddleware contain all functions to check login user is host or not.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request as Request;

use Closure;
use App\Libraries\ApiResponse;
use App\Models\User;

/**
 * Class DeviceMiddleware
 */
class HostMiddleware
{


    /**
     * Function check if request has vaid Host.
     *
     * @param \Illuminate\Http\Request $request Request object.
     * @param \Closure                 $next    Closure.
     *
     * @return \Illuminate\Http\JsonResponse Return json that user is authenticated or not.
     */
    public function handle(Request $request, Closure $next)
    {
        if (User::isUserHost(\Auth::user()->id) === false) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Authentication failed. Please add your property.');
        }

        return $next($request);

    }//end handle()


}//end class
