<?php
/**
 * Exception handler class
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Class Handler
 */
class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    //phpcs:ignore
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        OAuthServerException::class,
    ];


    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e General exception object.
     *
     * @return void
     */
    public function report(Exception $e)
    {
        if (true === app()->bound('sentry') && true === $this->shouldReport($e) && true === env('APP_IS_LIVE', false) && 'production' === env('APP_ENV', 'local')) {
            app('sentry')->captureException($e);
        }

        $request          = app('request');
        $headers          = $request->headers->all();
        $device_unique_id = (true === isset($headers['device-unique-id'])) ? $headers['device-unique-id'] : '';
        \Log::Error('Device unique id in Headers is'.json_encode($device_unique_id).'and exception class is '.get_class($e).' and message is '.$e->getMessage());

        if ($e instanceof OAuthServerException) {
            \Log::Error('and stack trace is '.$e->getTraceAsString());
        }

        parent::report($e);

    }//end report()


    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request Request object.
     * @param \Exception               $e       General exception object.
     *
     * @return \Illuminate\Http\Response
     */
    //phpcs:ignore
    public function render($request, Exception $e)
    {
        return parent::render($request, $e);

    }//end render()


}//end class
