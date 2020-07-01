<?php
/**
 * Prive No Show Reasons Model contain all functions to coupon
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

use Helper;

/**
 * Class Admin
 */
class NoShowReasons extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'no_show_reasons';


    /**
     * Get no show reasons data
     *
     * @return array
     */
    public static function getReasons()
    {
        $reasons = self::select('id', 'description as reason')->get();

        if (empty($reasons) === true) {
            return [];
        }

        return $reasons->toArray();

    }//end getReasons()


}//end class
