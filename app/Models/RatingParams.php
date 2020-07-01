<?php
/**
 * Model containing data regarding property room_type
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomType
 */
class RatingParams extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'rating_params';


      /**
       * Get Default Rating Params.
       *
       * @return array Rating parms.
       */
    public static function getDefaultRatingParams()
    {
        return self::where('rating_for', '=', ENTITY_PROPERTY)->where('rating_by', '=', ENTITY_TRAVELLER)->select('id', 'title')->get()->toArray();

    }//end getDefaultRatingParams()


}//end class
