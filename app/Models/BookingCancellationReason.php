<?php
/**
 * Admin Model containing all functions related to admin table
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
 /**
  * Class BookingCancellationReason
  */
class BookingCancellationReason extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'booking_cancellation_reason';


    /**
     * Get cancellation reasons for requests/trips
     *
     * @param array $params Params.
     *
     * @return void
     */
    public static function saveBookingCancellationReason(array $params)
    {
        $cancelation          = new BookingCancellationReason;
        $cancelation->user_id = $params['user_id'];
        $cancelation->booking_request_id = $params['booking_request_id'];
        $cancelation->reason_id          = $params['reason_id'];
        $cancelation->comments           = $params['reason_details'];
        ;
        $cancelation->save();

    }//end saveBookingCancellationReason()


}//end class
