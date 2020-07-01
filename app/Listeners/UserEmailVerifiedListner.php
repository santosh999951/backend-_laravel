<?php
/**
 * Listener for the UserEmailVerified event.
 */

namespace App\Listeners;

use App\Events\UserEmailVerified;

use App\Libraries\v1_6\UserService;

/**
 * Class UserEmailVerifiedListner for handling email Verified event.
 */
class UserEmailVerifiedListner extends Listener
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
     * @param UserEmailVerified $event Event.
     *
     * @return void
     */
    public function handle(UserEmailVerified $event)
    {
        // Send email verification mails.
        $this->user_service->sendUserVerificationEmail($event->to_email, $event->user_name, $event->confirmation_code, $event->verification_link);

    }//end handle()


}//end class
