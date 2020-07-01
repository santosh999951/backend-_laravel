<?php
/**
 * Coupon Model contain all functions to coupon
 */

namespace App\Models;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

use Helper;
/**
 * Class CouponUsage
 */
class Coupon extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'coupons';



}//end class
