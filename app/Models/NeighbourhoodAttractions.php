<?php
/**
 * Model containing data regarding Neighbourhood Attractions
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;

/**
 * Class NeighbourhoodAttractions
 */
class NeighbourhoodAttractions extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'neighbourhood_attractions';


    /**
     * Get Attraction Images.
     *
     * @param string $property_vlat Property Virtual Latitude.
     * @param string $property_vlng Property Virtual Longitude.
     *
     * @return array
     */
    public function getNeighbourhoodAttractionImages(string $property_vlat, string $property_vlng)
    {
        $attraction_images = self::select(
            'pi.image as image',
            'pi.caption as caption'
        )->from('neighbourhood_attractions as na')->join('properties_images as pi', 'na.id', '=', 'pi.neighbourhood_id');

        $attraction_images->where('pi.is_deleted', 0)->where('pi.is_proccessed', 1);
        $attraction_images->whereRaw('(3959 * acos (cos ( radians('.$property_vlat.') ) * cos( radians(na.v_lat) ) * cos( radians(na.v_lng) - radians( '.$property_vlng.') ) + sin ( radians( '.$property_vlat.') ) * sin( radians(na.v_lat) )))  < 5');
        $attraction_images->groupBy('na.id')->orderBy('pi.order_by');

        $attraction_images = $attraction_images->get();

        if (empty($attraction_images) === true) {
            return [];
        }

        return $attraction_images->toArray();

    }//end getNeighbourhoodAttractionImages()


}//end class
