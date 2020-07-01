<?php
/**
 * BookingAvailability Model containing all functions related to Booking Availability
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Class BookingAvailability
 */
class BookingAvailability extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'booking_availability';


    /**
     * Confirmed Availablity Data
     *
     * @param integer $booking_request_id Booking Request Id.
     * @param integer $host_id            Host Id.
     *
     * @return object
     */
    public static function confirmAvailability(int $booking_request_id, int $host_id)
    {
        $availability = new self;
        // Booking status 1 for Booking Available.
        $availability->status             = 1;
        $availability->booking_request_id = $booking_request_id;
        $availability->marked_by_host_id  = $host_id;
        $availability->save();
        return $availability;

    }//end confirmAvailability()


    /**
     * Check Availability Already Marked or not
     *
     * @param integer $booking_request_id Booking Request Id.
     * @param integer $host_id            Host Id.
     *
     * @return integer
     */
    public static function checkMarkedStatus(int $booking_request_id, int $host_id)
    {
        return self::where('booking_request_id', $booking_request_id)->where('marked_by_host_id', $host_id)->count();

    }//end checkMarkedStatus()


}//end class
