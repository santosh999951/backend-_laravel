<?php
/**
 * A simple user email verified event class.
 */

namespace App\Events;

use App\Models\User;

/**
 * Class UserEmailVerified. An event class which is fired when user verify their email id.
 */
class UserEmailVerified extends Event
{

    /**
     * To email
     *
     * @var string $to_email
     */
    public $to_email;

    /**
     * Username
     *
     * @var string $user_name
     */
    public $user_name;

    /**
     * Confirmation_code
     *
     * @var string $confirmation_code
     */
    public $confirmation_code;

    /**
     * Verification link
     *
     * @var string $verification_link
     */
    public $verification_link;


    /**
     * Create a new event instance.
     *
     * @param string $to_email          Email id of user.
     * @param string $user_name         Name of user.
     * @param string $confirmation_code Mail Verification Code.
     * @param string $verification_link Mail Verification Link.
     *
     * @return void
     */
    public function __construct(string $to_email, string $user_name, string $confirmation_code, string $verification_link)
    {
        $this->to_email          = $to_email;
        $this->user_name         = $user_name;
        $this->confirmation_code = $confirmation_code;
        $this->verification_link = $verification_link;

    }//end __construct()


}//end class
