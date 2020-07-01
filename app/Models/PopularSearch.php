<?php
/**
 * Model containing data regarding popular places
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PopularSearch
 */
class PopularSearch extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'popular_search';


    /**
     * Create a new traffic data request.
     *
     * @param integer $limit Limit param for popular places search.
     *
     * @return object
     */
    public static function getTrendingPlaces(int $limit=5)
    {
        return self::where('active', 1)->limit($limit)->get();

    }//end getTrendingPlaces()


}//end class
