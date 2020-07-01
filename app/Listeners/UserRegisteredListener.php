<?php
/**
 * Listener for the UserRegistered event.
 */

namespace App\Listeners;

use App\Events\UserRegistered;

use App\Libraries\v1_6\UserService;

/**
 * Class UserRegisteredListener for handling UserRegistered event.
 */
class UserRegisteredListener extends Listener
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
     * @param UserRegistered $event Event.
     *
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        // Things to do when user registers like sending welcome mails, notification.
        // Send registeration mails.
        $this->user_service->sendUserRegistrationEmails($event->user, $event->source, $event->password);

    }//end handle()


}//end class
