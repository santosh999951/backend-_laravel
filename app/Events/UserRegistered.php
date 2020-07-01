<?php
/**
 * A simple user registration event class.
 */

namespace App\Events;

use App\Models\User;

/**
 * Class UserRegistered. An event class which is fired when new user is registered.
 */
class UserRegistered extends Event
{

    /**
     * The user object.
     *
     * @var User $user
     */
    public $user;

    /**
     * Registration source- website/google/fb/apple
     *
     * @var integer $source
     */
    public $source;

    /**
     * Password
     *
     * @var string Password of account.
     */
    public $password;


    /**
     * Create a new event instance.
     *
     * @param User    $user     New user object.
     * @param integer $source   Website/google/facebook.
     * @param string  $password Required if source is google/facebook.
     *
     * @return void
     */
    public function __construct(User $user, int $source, string $password='')
    {
        $this->user     = $user;
        $this->source   = $source;
        $this->password = $password;

    }//end __construct()


}//end class
