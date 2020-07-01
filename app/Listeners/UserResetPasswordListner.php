<?php
/**
 * Listener for the UserResetPassword event.
 */

namespace App\Listeners;

use App\Events\UserResetPassword;

use App\Libraries\v1_6\UserService;

/**
 * Class UserResetPasswordListner for handling email Verified event.
 */
class UserResetPasswordListner extends Listener
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
     * @param UserResetPassword $event Event.
     *
     * @return void
     */
    public function handle(UserResetPassword $event)
    {
        if (false === empty($event->token)) {
            // Send Reset Password Link email.
            $this->user_service->sendUserResetPasswordLinkEmail($event->to_email, $event->token, $event->role);
        } else {
            // Send OTP in email.
            if (false === empty($event->verification_code)) {
                $this->user_service->sendUserResetPasswordOtpEmail($event->to_email, $event->verification_code, $event->role);
            }
        }

        // Send Reset Password Sms Otp.
        $this->user_service->sendUserResetPasswordOtpSms($event->dial_code, $event->contact, $event->verification_code);

    }//end handle()


}//end class
