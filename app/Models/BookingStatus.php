<?php
/**
 * BookingStatus Model containing all functions related to booking status
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BookingStatus
 */
class BookingStatus extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'booking_status';
}//end class
