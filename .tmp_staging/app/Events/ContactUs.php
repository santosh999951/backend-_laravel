<?php
/**
 * A simple contactus event class.
 */

namespace App\Events;

/**
 * Class ContactUs. An event class which is fired when user enquiry in properly.
 */
class ContactUs extends Event
{

    /**
     * Message
     *
     * @var string $message
     */
    public $message;

    /**
     * Subject
     *
     * @var string subject.
     */
    public $subject;


    /**
     * Create a new event instance.
     *
     * @param string $subject Subject oF Email.
     * @param string $message Message.
     *
     * @return void
     */
    public function __construct(string $subject, string $message)
    {
        $this->subject = $subject;
        $this->message = $message;

    }//end __construct()


}//end class
