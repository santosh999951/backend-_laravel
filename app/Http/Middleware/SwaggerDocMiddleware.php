<?php
/**
 * SwaggerDocMiddleware handler to override the swagger configuration
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request as Request;

use Closure;

/**
 * Class SwaggerDocMiddleware
 */
class SwaggerDocMiddleware
{


    /**
     * Function overrides swagger configuration
     *
     * @param \Illuminate\Http\Request $request Request object.
     * @param \Closure                 $next    Closure.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        config(['swagger-lume.paths.docs' => storage_path('api-docs').'/'.$request->get('v')]);

        return $next($request);

    }//end handle()


}//end class
