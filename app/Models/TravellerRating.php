<?php
/**
 * Model containing data regarding traffic data
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use App\Libraries\Helper;


/**
 * Class TravellerRating
 */
class TravellerRating extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'traveller_ratings';


    /**
     * Get traveller ratings for booking.
     *
     * @param integer $booking_requests_id Booking Request id.
     * @param integer $user_id             User id.
     *
     * @return boolean True/false
     */
    public static function getRatingForRequest(int $booking_requests_id, int $user_id)
    {
        return self::where('booking_requests_id', '=', $booking_requests_id)->where('rated_by', '=', $user_id)->first();

    }//end getRatingForRequest()


    /**
     * Save property rating for booking.
     *
     * @param array   $rating_data         Rating array.
     * @param integer $booking_requests_id Bookingn Request id.
     * @param integer $property_id         Property id.
     * @param integer $user_id             User id.
     *
     * @return boolean True/false
     */
    public static function savePropertyRatingForBooking(array $rating_data, int $booking_requests_id, int $property_id, int $user_id)
    {
        // Saving rating in ratings table.
        foreach ($rating_data as $key => $value) {
            $rating               = new self;
            $rating->rated_by     = $user_id;
            $rating->property_id  = $property_id;
            $rating->rating_param = $key;
            $rating->rating       = $value;
            $rating->enabled      = 1;
            $rating->booking_requests_id = $booking_requests_id;

            try {
                $rating->save();
            } catch (QueryException $e) {
                 Helper::logError('<<<<---- Rating not saved ---->>>>.', ['Booking Id' => $booking_requests_id, 'Property Id' => $property_id, 'Traveller Id' => $user_id, 'Rating' => json_encode($rating_data)]);
                return false;
            }
        }//end foreach

        return true;

    }//end savePropertyRatingForBooking()


     /**
      * Get property review by traveller.
      *
      * @param integer $pid Property id.
      *
      * @return array
      */
    public static function getPropertyReviewByTraveller(int $pid)
    {
        //phpcs:ignore
        $query = self::selectRaw('AVG(rating) AS property_score')        ->where('enabled', 1)        ->where('property_id', $pid)        ->get()        ->toArray();

        return $query;

    }//end getPropertyReviewByTraveller()


}//end class
