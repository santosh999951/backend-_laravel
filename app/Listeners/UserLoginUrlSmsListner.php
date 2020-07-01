<?php
/**
 * Listener for the UserLoginUrlSMS event.
 */

namespace App\Listeners;

use App\Events\UserLoginUrlSms;

use App\Libraries\v1_6\UserService;

/**
 * Class UserLoginUrlSMSListener for handling UserSMS event.
 */
class UserLoginUrlSmsListner extends Listener
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
     * @param UserLoginUrlSms $event Event.
     *
     * @return void
     */
    public function handle(UserLoginUrlSms $event)
    {
        // Things to do when user registers like sending sms with otp to login.
        $this->user_service->sendLoginUrlwithSms($event->login_url, $event->dial_code, $event->contact);

    }//end handle()


}//end class
