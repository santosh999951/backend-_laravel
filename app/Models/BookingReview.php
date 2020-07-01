<?php
/**
 * Model containing data regarding traffic data
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;


/**
 * Class BookingReview
 */
class BookingReview extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'booking_review';


    /**
     * Get Booking review.
     *
     * @param integer $booking_requests_id Booking Request id.
     * @param integer $user_id             User id.
     *
     * @return array
     */
    public static function getBookingReviewForRequest(int $booking_requests_id, int $user_id)
    {
        return self::where('booking_requests_id', '=', $booking_requests_id)->first();

    }//end getBookingReviewForRequest()


    /**
     * Save booking review.
     *
     * @param array $data Booking Review Data.
     *
     * @return boolean True/false
     */
    public static function saveBookingReview(array $data)
    {
        $booking_review_count = self::where('booking_requests_id', '=', $data['booking_request_id'])->count();
        if (empty($booking_review_count) === false) {
            return true;
        } else {
            // Saving Booking Review data.
            $booking_review = new self;
            $booking_review->booking_requests_id = $data['booking_request_id'];
            $booking_review->property_id         = $data['property_id'];
            $booking_review->booking_rating      = $data['booking_rating'];
            $booking_review->property_rating     = $data['property_rating'];
            $booking_review->booking_review      = (empty($data['booking_comment']) === false) ? $data['booking_comment'] : null;

            try {
                $booking_review->save();
            } catch (QueryException $e) {
                return false;
            }
        }

        return true;

    }//end saveBookingReview()


}//end class
