<?php
/**
 * Model containing data regarding traffic data
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

/**
 * Class Rating
 */
class Rating extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'ratings';


    /**
     * Save property rating for booking.
     *
     * @param string  $rating_data Json encoded rating string.
     * @param integer $booking_id  Booking id.
     * @param integer $property_id Property id.
     * @param integer $user_id     User id.
     *
     * @return array
     */
    public static function savePropertyRatingForBooking(string $rating_data, int $booking_id, int $property_id, int $user_id)
    {
        $rating = self::where('booking_id', '=', $booking_id)->where('from_id', '=', $user_id)->first();

        if (empty($rating) === false) {
            return [
                'status'  => 403,
                'code'    => EC_RATING_ALREADY_SUBMITTED,
                'message' => 'Rating already submitted.',
            ];
        }

        // Saving rating in ratings table.
        $rating_data = json_decode(mb_convert_encoding($rating_data, 'UTF-8'), true);

        if (count($rating_data) < 1) {
            return [
                'status'  => 400,
                'code'    => EC_VALIDATION_FAILED,
                'message' => 'The rating field is invalid.',
            ];
        }

        foreach ($rating_data as $key => $value) {
            $rating                 = new self;
            $rating->from_type      = ENTITY_TRAVELLER;
            $rating->to_type        = ENTITY_PROPERTY;
            $rating->from_id        = $user_id;
            $rating->to_id          = $property_id;
            $rating->rating_param   = $key;
            $rating->score          = $value;
            $rating->rating_disable = 1;
            $rating->booking_id     = $booking_id;

            try {
                $rating->save();
            } catch (QueryException $e) {
                return [
                    'status'  => 500,
                    'code'    => EC_SERVER_ERROR,
                    'message' => 'Rating not saved.',
                ];
            }
        }//end foreach

        return [
            'status'  => 200,
            'message' => 'Ratings successfully submitted.',
        ];

    }//end savePropertyRatingForBooking()


}//end class
