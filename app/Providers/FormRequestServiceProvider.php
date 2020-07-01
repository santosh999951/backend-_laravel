<?php
/**
 * FormRequestServiceProvider containing Form Request Validation related Method.
 */

// phpcs:disable
namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use App\Http\Requests\FormRequest;

class FormRequestServiceProvider extends ServiceProvider
{


    public function register()
    {

    }//end register()


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving(
            FormRequest::class,
            function ($request, $app) {
                $this->initializeRequest($request, $app['request']);
            }
        );

        $this->app->afterResolving(
            FormRequest::class,
            function ($resolved) {
                $resolved->validateResolved();
            }
        );

    }//end boot()


    /**
     * Initialize the form request with data from the given request.
     *
     * @param  \App\Http\Requests\FormRequest $form
     * @param  \Illuminate\Http\Request      $current
     * @return void
     */
    protected function initializeRequest(FormRequest $form, Request $current)
    {
        $files = $current->files->all();

        $files = is_array($files) ? array_filter($files) : $files;

        $form->initialize(
            $current->query->all(),
            $current->request->all(),
            $current->attributes->all(),
            $current->cookies->all(),
            $files,
            $current->server->all(),
            $current->getContent()
        );

        $form->setContainer($this->app);

    }//end initializeRequest()


}//end class

// phpcs:enable
