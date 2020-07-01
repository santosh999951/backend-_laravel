<?php
/**
 * AppExplore Model contain all functions realted to app explore
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BookingServeDetails
 */
class AppExplore extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'app_explore';


    /**
     * Get SpotLight List
     *
     * @return array
     */
    public static function getSpotLightList()
    {
        $result = self::join('property_type as pt', 'app_explore.p_type', '=', 'pt.id', 'left')->select(
            'pt.name as property_type_name',
            'p_type as property_type',
            'place_state as state',
            'tag',
            'place_title as title',
            'place_city as city',
            'place_country as country',
            'place_country_name as country_name',
            'lat as v_lat',
            'lng as v_lng',
            'place_image as image_url'
        )->get();
        foreach ($result as $val) {
            $val->image_url = SPOT_LIGHT_IMAGES_PATH.$val->image_url;
        }

        $spotlight_list = [];
        if (empty($result) === false) {
            $spotlight_list = $result->toArray();
            return $spotlight_list;
        }

        return [];

    }//end getSpotLightList()


}//end class
