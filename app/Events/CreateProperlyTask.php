<?php
/**
 * A simple Task Created event class.
 */

namespace App\Events;


/**
 * Class CreateProperlyTask. An event class which is fired when Task Created.
 */
class CreateProperlyTask extends Event
{

    /**
     * array.
     *
     * @var array $recipient_id
     */
    public $recipient_id;

    /**
     * ProperlyTask object.
     *
     * @var ProperlyTask $booking_request
     */
    public $booking_request;

    /**
     * Create a new event instance.
     *
     * @param array        $recipient_id        recipient_id.        
     *
     * @return void
     */
    public function __construct( array $properly_task, array $recipient_id)
    {
        $this->properly_task   = $properly_task;
        $this->recipient_id    = $recipient_id;

    }//end __construct()


}//end class
