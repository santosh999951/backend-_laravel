<?php
/**
 * BeforeMiddleware contain all functions to which needs to excute before the request is handled by the application.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request as Request;

use Closure;
use DB;


/**
 * Class BeforeMiddleware
 */
class BeforeMiddleware
{


    /**
     * Function to enable query logs.
     *
     * @param \Illuminate\Http\Request $request Request object.
     * @param \Closure                 $next    Closure.
     *
     * @return \Illuminate\Http\JsonResponse Return json.
     */
    public function handle(Request $request, Closure $next)
    {
        DB::enableQueryLog();

        return $next($request);

    }//end handle()


}//end class
