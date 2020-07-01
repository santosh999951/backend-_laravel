<?php
/**
 * A wallet updation event class.
 */

namespace App\Events;

use App\Models\{User, WalletTransaction};

/**
 * Class WalletUpdation. An event class which is fired when users wallet got updated.
 */
class WalletUpdation extends Event
{

    /**
     * Wallet Tranaction.
     *
     * @var WalletTransaction $wallet_transaction
     */
    public $wallet_transaction;

    /**
     * The user object.
     *
     * @var User $user
     */
    public $user;

    /**
     * Referal User Name.
     *
     * @var string $referal_user_name
     */
    public $referal_user_name;

     /**
      *  Property Link.
      *
      * @var string $property_link
      */
    public $property_link;

     /**
      * Property Title.
      *
      * @var string $property_title
      */
    public $property_title;


    /**
     * Create a new event instance.
     *
     * @param WalletTransaction $wallet_transaction Wallet Transaction.
     * @param User              $user               New user object.
     * @param string            $referal_user_name  Referal User name.
     * @param string            $property_link      Property Link.
     * @param string            $property_title     Property Title.
     *
     * @return void
     */
    public function __construct(WalletTransaction $wallet_transaction, User $user, string $referal_user_name='', string $property_link='', string $property_title='')
    {
        $this->wallet_transaction = $wallet_transaction;
        $this->user               = $user;
        $this->referal_user_name  = $referal_user_name;
        $this->property_link      = $property_link;
        $this->property_title     = $property_title;

    }//end __construct()


}//end class
