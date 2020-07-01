<?php
/**
 * Model containing data regarding Shift Booking
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShiftBooking
 */
class ShiftBooking extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'shift_booking';

}//end class
