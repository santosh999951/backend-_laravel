<?php
/**
 * Listener for the Wallet Updation event.
 */

namespace App\Listeners;

use App\Events\WalletUpdation;

use App\Libraries\v1_6\UserService;

/**
 * Class WalletUpdationListener for handling WalletUpdation event.
 */
class WalletUpdationListener extends Listener
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
     * @param WalletUpdation $event Event.
     *
     * @return void
     */
    public function handle(WalletUpdation $event)
    {
        $wallet_transaction = $event->wallet_transaction;

        // Things to do when wallet money added or updated.
        if ((int) $wallet_transaction->event === TRIP_AND_REVIEW) {
            $this->user_service->sendWalletUpdationForTripAndReviewSms($event->wallet_transaction, $event->user);

            $this->user_service->sendWalletUpdationForTripAndReviewEmail($event->wallet_transaction, $event->user, $event->property_title, $event->property_link);
        } else if ((int) $wallet_transaction->event === REFERRAL_BONUS) {
             $this->user_service->sendWalletUpdationForReferalBonusSms($event->wallet_transaction, $event->user);

             $this->user_service->sendWalletUpdationForReferalBonusEmail($event->wallet_transaction, $event->user);
        } else if ((int) $wallet_transaction->event === FRIEND_REFERRAL_BONUS) {
              $this->user_service->sendWalletUpdationForFriendReferalBonusSms($event->wallet_transaction, $event->user);

             $this->user_service->sendWalletUpdationForFriendReferalBonusEmail($event->wallet_transaction, $event->user, $event->referal_user_name);
        } else if ((int) $wallet_transaction->event === REFERRAL_FIRST_BOOKING_BONUS) {
            $this->user_service->sendWalletUpdationForFirstBookingBonusSms($event->wallet_transaction, $event->user);

             $this->user_service->sendWalletUpdationForReferalFirstBookingBonusEmail($event->wallet_transaction, $event->user, $event->referal_user_name);
        } else if ((int) $wallet_transaction->event === APPLY_WALLET_MONEY) {
            $this->user_service->sendWalletUpdationForApplyWalletSms($event->wallet_transaction, $event->user);

             $this->user_service->sendWalletUpdationForApplyWalletMoneyEmail($event->wallet_transaction, $event->user, $event->property_title, $event->property_link);
        }//end if

    }//end handle()


}//end class
