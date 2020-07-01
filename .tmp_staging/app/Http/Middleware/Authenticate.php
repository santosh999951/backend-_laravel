<?php
/**
 * Authenticate contain all functions releated touser oauth token issue api.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Libraries\ApiResponse;
use Illuminate\Http\Request as Request;

 /**
  * Class Authenticate
  */
class Authenticate
{

    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;


    /**
     * Function constructor.
     *
     * @param Auth $auth Auth Instance.
     *
     * @return void.
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;

    }//end __construct()


    /**
     * Function get called after oauth proccess a request.
     *
     * @param \Illuminate\Http\Request $request Request object.
     * @param \Closure                 $next    Closure.
     * @param string|null              $guard   Guaurd.
     * @param string                   $mode    Mode.
     *
     * @return \Illuminate\Http\JsonResponse Return data that user is authenticated or not.
     */
    public function handle(Request $request, Closure $next, string $guard=null, string $mode='full')
    {
        $headers = $request->headers->all();
        if ($this->auth->guard($guard)->guest() === true) {
            if ($mode === 'full') {
                return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Authorization failed. Please login before this operation.', 'Authorization');
            } else if ($mode === 'semi' && isset($headers['authorization']) === true && isset($headers['authorization'][0]) === true && empty($headers['authorization'][0]) === false) {
                return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Authorization failed. Please login before this operation.', 'Authorization');
            }
        }

        return $next($request);

    }//end handle()


}//end class
