<?php
/**
 * A simple user reset password event class.
 */

namespace App\Events;

/**
 * Class UserResetPassword. An event class which is fired when user forgot password.
 */
class UserResetPassword extends Event
{

    /**
     * To email
     *
     * @var string $to_email
     */
    public $to_email;

    /**
     * Reset Password Token
     *
     * @var string $token
     */
    public $token;

    /**
     * Dial Code
     *
     * @var string $dial_code
     */
    public $dial_code;

    /**
     * Contact
     *
     * @var string $contact
     */
    public $contact;

    /**
     * Verification Code
     *
     * @var string $verification_code
     */
    public $verification_code;

     /**
      * Role of User
      *
      * @var string $role
      */
    public $role;


    /**
     * Create a new event instance.
     *
     * @param string $to_email          Email id of user.
     * @param string $token             Reset Password link token.
     * @param string $dial_code         Dial Code.
     * @param string $contact           Contact.
     * @param string $verification_code Sms Verification Code.
     * @param string $role              Role Of User.
     *
     * @return void
     */
    public function __construct(string $to_email, string $token, string $dial_code='', string $contact='', string $verification_code='', string $role='')
    {
        $this->to_email          = $to_email;
        $this->token             = $token;
        $this->dial_code         = $dial_code;
        $this->contact           = $contact;
        $this->verification_code = $verification_code;
        $this->role              = $role;

    }//end __construct()


}//end class
