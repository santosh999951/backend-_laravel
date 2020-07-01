<?php

/**
 * Config File
 */

//phpcs:disable
require_once __DIR__.'/../vendor/autoload.php';

try {
   (new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
    ))->bootstrap();

} catch (Dotenv\Exception\InvalidPathException $e) {
}



/*
    |--------------------------------------------------------------------------
    | Create The Application
    |--------------------------------------------------------------------------
    |
    | Here we will load the environment and create the application instance
    | that serves as the central piece of this framework. We'll use this
    | application as an "IoC" container and router for this framework.
    |
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->withFacades();
$app->withEloquent();
class_alias('Illuminate\Support\Facades\App', 'App');
class_alias('Illuminate\Support\Facades\Mail', 'Mail');

/*
    |--------------------------------------------------------------------------
    | Custom Configure Monolog
    |--------------------------------------------------------------------------
    |
*/


 /*
     |--------------------------------------------------------------------------
     | Register Container Bindings
     |--------------------------------------------------------------------------
     |
     | Now we will register a few bindings in the service container. We will
     | register the exception handler and the console kernel. You may add
     | your own bindings here if you like or you can make another file.
     |
 */

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    'filesystem',
    function ($app) {
        return $app->loadComponent('filesystems', 'Illuminate\Filesystem\FilesystemServiceProvider', 'filesystem');
    }
);


 // include config files
 $app->configure('swagger-lume');
 $app->configure('geoip');
 $app->configure('mail');
 $app->configure('queue');
 $app->configure('auth');
 $app->configure('filesystems');
 $app->configure('dompdf');
 $app->configure('gh');
 $app->configure('permission');

 /*
     |--------------------------------------------------------------------------
     | Register Middleware
     |--------------------------------------------------------------------------
     |
     | Next, we will register the middleware with the application. These can
     | be global middleware that run before and after each request into a
     | route or middleware that'll be assigned to some specific routes.
     |
 */

$app->middleware(
    [
// App\Http\Middleware\ExampleMiddleware::class.
         App\Http\Middleware\BeforeMiddleware::class,
         App\Http\Middleware\AfterMiddleware::class,
    ]
);

$app->routeMiddleware(
    [
        'auth' => App\Http\Middleware\Authenticate::class,
        'device' => App\Http\Middleware\DeviceMiddleware::class,
        'host' => App\Http\Middleware\HostMiddleware::class,
        'prive' => App\Http\Middleware\PriveMiddleware::class,
        'prive_manager' => App\Http\Middleware\PriveManagerMiddleware::class,
        'permission' => App\Http\Middleware\PermissionMiddleware::class,
        'swagger_doc' => App\Http\Middleware\SwaggerDocMiddleware::class
    ]
);




 /*
     |--------------------------------------------------------------------------
     | Register Service Providers
     |--------------------------------------------------------------------------
     |
     | Here we will register all of the application's service providers which
     | are used to bind services into the container. Service providers are
     | totally optional, so you are not required to uncomment this line.
     |
 */

  $app->register(App\Providers\AppServiceProvider::class);
 // $app->register(App\Providers\AuthServiceProvider::class);
 $app->register(App\Providers\EventServiceProvider::class);
 /*
     Passport */
 // Finally register two service providers - original one and Lumen adapter.
 $app->register(Laravel\Passport\PassportServiceProvider::class);
 $app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
 \Dusterio\LumenPassport\LumenPassport::routes($app);
 \Dusterio\LumenPassport\LumenPassport::allowMultipleTokens();

 // Define expiry dates of tokens.
 \Laravel\Passport\Passport::tokensExpireIn(\Carbon\Carbon::now()->addSeconds(env('PASSPORT_ACCESS_TOKEN_TTL')));
 \Laravel\Passport\Passport::refreshTokensExpireIn(\Carbon\Carbon::now()->addSeconds(env('PASSPORT_REFRESH_TOKEN_TTL')));

 // Redis providers.
 $app->register(Illuminate\Redis\RedisServiceProvider::class);

 // Swagger lumen.
 $app->register(\SwaggerLume\ServiceProvider::class);

 // User agent.
 $app->register(\Jenssegers\Agent\AgentServiceProvider::class);

 // Geo ip.
 $app->register(\Torann\GeoIP\GeoIPServiceProvider::class);

 // Email.
 $app->register(\Illuminate\Mail\MailServiceProvider::class);

 // Aws.
 $app->register(Aws\Laravel\AwsServiceProvider::class);

 // Intervention image.
 $app->register(\Intervention\Image\ImageServiceProvider::class);
 $app->register('Sentry\Laravel\ServiceProvider');
 $app->register('Spatie\Tail\TailServiceProvider');
 //$app->register(\NunoMaduro\Larastan\LarastanServiceProvider::class);

 $app->register(App\Providers\FormRequestServiceProvider::class);
 $app->register(Waavi\Sanitizer\Laravel\SanitizerServiceProvider::class);
 class_alias('Waavi\Sanitizer\Laravel\Facade', 'Sanitizer');


// Register DOMPdf for Generate PDF.
$app->register(\Barryvdh\DomPDF\ServiceProvider::class);

// For permission role model.
$app->alias('cache', \Illuminate\Cache\CacheManager::class);  // if you don't have this already
$app->register(Spatie\Permission\PermissionServiceProvider::class);

 /*
     |--------------------------------------------------------------------------
     | Load The Application Routes
     |--------------------------------------------------------------------------
     |
     | Next we will include the routes file so that they can all be added to
     | the application. This will provide all of the URLs the application
     | can respond to, as well as the controllers that may handle them.
     |
 */

$app->router->group(
    ['namespace' => 'App\Http\Controllers' ],
    function ($router) {
        include __DIR__.'/../routes/web.php';
    }
);

 return $app;
