<?php
/**
 * AfterMiddleware contain all functions to which needs to excute after the request is handled by the application.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request as Request;
use App\Libraries\Helper;

use Closure;
use DB;
use \Auth;


/**
 * Class AfterMiddleware
 */
class AfterMiddleware
{


    /**
     * Handled an incoming Request.
     *
     * @param \Illuminate\Http\Request $request Request object.
     * @param \Closure                 $next    Closure.
     *
     * @return \Illuminate\Http\JsonResponse Return json that user is authenticated or not.
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);

    }//end handle()


    /**
     * Function to add query logs in file or queue when reponse is ready
     *
     * @param \Illuminate\Http\Request $request Request object.
     *
     * @return boolean.
     */
    public function terminate(Request $request)
    {
        $queries  = DB::getQueryLog();
        $user_id  = (empty(Auth::id()) === false) ? Auth::id() : 0;
        $admin_id = (empty($request->headers->get('admin')) === false) ? Helper::decodeAdminId($request->headers->get('admin')) : 0;

        if (DB_QUERY_LOG_METHOD === 'queue') {
            foreach (array_chunk($queries, 100, false) as $batched) {
                $data = [
                    'queries'  => $batched,
                    'user_id'  => $user_id,
                    'admin_id' => $admin_id,
                ];

                Helper::pushMessageToQueue(DB_QUERY_LOG_QUEQE, json_encode($data), TRANSCODER_AWS_REGION);
            }
        } else {
            Helper::writeDBQueriesToFile($queries, $admin_id, $user_id);
        }

        return true;

    }//end terminate()


}//end class
