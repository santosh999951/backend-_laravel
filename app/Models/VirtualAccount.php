<?php
/**
 * VirtualAccount Model containing all functions related to virtual account table
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VirtualAccount
 */
class VirtualAccount extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'virtual_accounts';


    /**
     * Get Booking virtual accounts.
     *
     * @param integer $booking_request_id Booking Request Id.
     *
     * @return object
     */
    public function getBookingVirtualAccountDetails(int $booking_request_id)
    {
        return self::where('booking_request_id', $booking_request_id)->first();

    }//end getBookingVirtualAccountDetails()


    /**
     * Save Booking virtual accounts.
     *
     * @param integer $booking_request_id       Booking Request Id.
     * @param string  $virtual_account_id       Virtual account id.
     * @param array   $virtual_account_response Virtual Account response.
     *
     * @return object
     */
    public function saveVirtualAccount(int $booking_request_id, string $virtual_account_id, array $virtual_account_response)
    {
        $virtual_account               = new self;
        $virtual_account->va_id        = $virtual_account_id;
        $virtual_account->json_details = json_encode($virtual_account_response, true);
        $virtual_account->booking_request_id = $booking_request_id;

        if ($virtual_account->save() === false) {
            return (object) [];
        }

        return $virtual_account;

    }//end saveVirtualAccount()


}//end class
