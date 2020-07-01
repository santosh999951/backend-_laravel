<?php
/**
 * Listener for the Contact Us event.
 */

namespace App\Listeners;

use App\Events\ContactUs;

use App\Libraries\v1_6\UserService;

/**
 * Class ContactUsListener for handling contact us event.
 */
class ContactUsListener extends Listener
{

    /**
     * User service.
     *
     * @var UserService $user_service User Service.
     */
    protected $user_service;


    /**
     * Initialize the object.
     *
     * @param UserService $user_service User Service.
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;

    }//end __construct()


    /**
     * Handle the event.
     *
     * @param ContactUs $event Event.
     *
     * @return void
     */
    public function handle(ContactUs $event)
    {
         // Send email to support mails.
        $this->user_service->sendProperlySupportEmail($event->subject, $event->message);

    }//end handle()


}//end class
