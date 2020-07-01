<?php
/**
 * Booking Serve Details Model contain all functions realted to booking served
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BookingServeDetails
 */
class BookingServeDetails extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'booking_serve_details';

    /**
     * Timestamps Required or not
     *
     * @var boolean
     */
    public $timestamps = false;
}//end class
