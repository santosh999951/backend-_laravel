<?php
/**
 * A simple user sms event class.
 */

namespace App\Events;

/**
 * Class UserLoginUrlSMS. An event class which is fired when new user is registered on properly dashboard.
 */
class UserLoginUrlSms extends Event
{

    /**
     * Login Url
     *
     * @var string $login_url.
     */
    public $login_url;

    /**
     * Dial Code
     *
     * @var string $dial_code.
     */
    public $dial_code;

    /**
     * Contact number
     *
     * @var string $contact.
     */
    public $contact;


    /**
     * Create a new event instance.
     *
     * @param string $login_url New user login url.
     * @param string $dial_code Dial Code.
     * @param string $contact   Contact number.
     *
     * @return void
     */
    public function __construct(string $login_url, string $dial_code, string $contact)
    {
        $this->login_url = $login_url;
        $this->dial_code = $dial_code;
        $this->contact   = $contact;

    }//end __construct()


}//end class
