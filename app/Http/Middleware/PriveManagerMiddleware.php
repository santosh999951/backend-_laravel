<?php
/**
 * PriveManagerMiddleware contain all functions to check login user is prive owner or not.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request as Request;

use Closure;
use App\Libraries\ApiResponse;
use App\Libraries\v1_6\UserService;

/**
 * Class PriveManagerMiddleware
 */
class PriveManagerMiddleware
{


    /**
     * Function check if request has vaid Prive Owner.
     *
     * @param \Illuminate\Http\Request $request Request object.
     * @param \Closure                 $next    Closure.
     *
     * @return json Return json that user is authenticated or not.
     */
    public function handle(Request $request, Closure $next)
    {
        $user_service = new UserService;

        if (empty($user_service->isPriveManager(\Auth::user())) === true) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Authorization failed. Please login with prive manager user authentication.', 'Authorization');
        }

        return $next($request);

    }//end handle()


}//end class
