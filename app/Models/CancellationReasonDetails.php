<?php
/**
 * CancellationReasonDetails contain all data related to request/trip cancellation reasons.
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CancellationReasonDetails
 */
class CancellationReasonDetails extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'cancellation_reason_details';


    /**
     * Get cancellation reasons for requests/trips
     *
     * @param integer $booking_status Booking status.
     *
     * @return array
     */
    public static function getCancellationReasons(int $booking_status)
    {
        /*
         * reason_cat = 1 for trips.
            * reason_cat = 0 for request.
         */

        $cancellation_reasons = self::select('id', 'reason_title')->where('reason_cat', '=', ($booking_status >= 1) ? 1 : 0)->get()->toArray();

        return $cancellation_reasons;

    }//end getCancellationReasons()


}//end class
