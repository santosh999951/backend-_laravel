<?php
/**
 * CouponUsage Model contain all functions related to Coupon usage
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CouponUsage
 */
class CouponUsage extends Model
{

    use SoftDeletes;

    /**
     * Fillable id
     *
     * @var array
     */
    protected $fillable = ['id'];

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'coupon_usage';

    /**
     * Date to mutate
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}//end class
