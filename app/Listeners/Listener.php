<?php
/**
 * Base listener class.
 */

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class Listener. This is the base class for all out listeners.
 */
class Listener implements ShouldQueue
{

    /**
     * The common event queue for all listeners.
     *
     * @var string Events queue.
     */
    public $queue = EVENT_QUEUE;


}//end class
