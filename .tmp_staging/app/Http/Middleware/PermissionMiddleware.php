<?php
/**
 * Permission contain all functions releated to user permissions.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request as Request;

use Closure;
use Spatie\Permission\Exceptions\UnauthorizedException;
use App\Libraries\ApiResponse;

/**
 * Class PermissionMiddleware
 */
class PermissionMiddleware
{


    /**
     * Function check if user has permission to access model.
     *
     * @param \Illuminate\Http\Request $request Request object.
     * @param \Closure                 $next    Closure.
     *
     * @return \Illuminate\Http\JsonResponse Return json that user is authenticated or not.
     */
    // phpcs:ignore
    public function handle(Request $request, Closure $next, $permission)
    {
        // phpcs:ignore
        if (app('auth')->guest()) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Authorization failed. Please login before this operation.', 'Authorization');
        }

        // phpcs:ignore
        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        foreach ($permissions as $permission) {
            // phpcs:ignore
            if (app('auth')->user()->can($permission)) {
                return $next($request);
            }
        }

        return ApiResponse::forbiddenError(EC_FORBIDDEN, 'You do not have the permission to perform this task.', 'permission-denied');

    }//end handle()


}//end class
