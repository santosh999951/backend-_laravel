<?php
// phpcs:ignoreFile
namespace App\Listeners;

use App\Events\ExampleEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExampleListener
{


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }//end __construct()


    /**
     * Handle the event.
     *
     * @param  ExampleEvent $event
     * @return void
     */
    public function handle(ExampleEvent $event)
    {

    }//end handle()


}//end class
