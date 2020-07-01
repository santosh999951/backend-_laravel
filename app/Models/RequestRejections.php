<?php
/**
 * RequestRejections contain all data related to request rejection.
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RequestRejections
 */
class RequestRejections extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'request_rejections';


    /**
     * Save Rejection Reasons
     *
     * @param integer $booking_request_id Booking Request Id.
     * @param integer $rejection_id       Request Rejection Id.
     * @param string  $message            Rejection Reason Message.
     *
     * @return boolean
     */
    public function saveRejectionReason(int $booking_request_id, int $rejection_id=4, string $message='')
    {
        $request_rejection             = new self;
        $request_rejection->request_id = $booking_request_id;
        $request_rejection->reason     = $rejection_id;
        $request_rejection->message    = $message;

        if ($request_rejection->save() === true) {
            return true;
        }

        return false;

    }//end saveRejectionReason()


    /**
     * Get Rejection Reasons
     *
     * @return array
     */
    public function getRejectionReasons()
    {
        // Will fix soon by creting new table.
        return [
            [
                'id'     => 3,
                'reason' => 'Pricing issue',
            ],
            [
                'id'     => 3,
                'reason' => 'Not available',
            ],
            [
                'id'     => 3,
                'reason' => 'Other reason',
            ],
        ];

    }//end getRejectionReasons()


}//end class
