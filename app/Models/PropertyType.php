<?php
/**
 * Model containing data regarding property types
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use DB;

/**
 * Class PropertyType
 */
class PropertyType extends Model
{
    //phpcs:disable

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'property_type';


    /**
     * Get active property types.
     *
     * @param EloquentQuery $query Query to be passed.
     *
     * @return EloquentQuery
     */
    public static function scopeActive(EloquentQuery $query)
    {
        return $query->where('active', 1);

    }//end scopeActive()


    /**
     * Get available property types.
     *
     * @param string  $country              Country of property.
     * @param string  $state                State of property.
     * @param string  $city                 City of proeprty.
     * @param integer $selected_property_id Selected Property Id.
     *
     * @return array
     */
    public static function getAvailablePropertyTypes(string $country='', string $state='', string $city='', int $selected_property_id=0)
    {
        // Propety types.
        $property_types = [];

        // Get property types linked to active properties.
        // phpcs:ignore
        $property_types_in_property_query = self::active()->where('P.enabled', 1)->where('P.status', 1)->where('P.admin_score', '!=', 0)->where('P.v_lat', '!=', 0)->where('P.v_lng', '!=', 0)->join('properties as P', 'P.property_type', 'property_type.id')->select(
            [
                'property_type.id as id',
                DB::raw('count(*) as property_types_count'),
                'property_type.name as name',
            ]
        )->groupby('property_type.id')->orderby('property_types_count', 'desc');

        // Country is provided.
        if (empty($country) === false) {
             $location = $country;
            $property_types_in_property_query->where('country', $country);
        }

        // State is provided.
        if (empty($state) === false) {
            $location = $state;
            $property_types_in_property_query->where('state', $state);
        }

        // City is provided.
        if (empty($city) === false) {
            $location = $city;
            $property_types_in_property_query->where('city', $city);
        }

        if (empty($location) === true) {
            return [];
        }

        // Get property types linked.
        $linked_property_types = $property_types_in_property_query->get()->toArray();

        // Set property types.
        $property_types = $linked_property_types;

        // If no active linked properties found then.
        if (count($linked_property_types) < 1) {
            $property_types = self::active()->select(
                [
                    'id as id',
                    'name as name',
                ]
            )->orderby('rank', 'asc')->orderby('id', 'asc')->get()->toArray();
        }

        // Property types.
        $response_property_types = [];
        foreach ($property_types as $property_type) {
            $response_property_types[] = [
                'id'       => $property_type['id'],
                'name'     => $property_type['name'],
                'link'     => rtrim(self::getSearchLink($location, $property_type['name']), '-'),
                'show'     => 1,
                'selected' => ($property_type['id'] === $selected_property_id) ? 1 : 0,
            ];
        }

        $response_property_types = array_merge(
            [[
                'id'       => 0,
                'name'     => 'All',
                'link'     => rtrim(self::getSearchLink($location, $property_type['name']), '-'),
                'show'     => 0,
                'selected' => (0 === $selected_property_id) ? 1 : 0,
            ],
            ],
            $response_property_types
        );

        return $response_property_types;

    }//end getAvailablePropertyTypes()

    /**
     * Get all property types.
     *
     * @param integer $selected_property_type Selected Property Type Id.
     *
     * @return array
     */
    public static function getAllPropertyTypesData(int $selected_property_type=0)
    {
        // Propety types.
        $property_types = [];

        // Get property types linked to active properties.
        $property_types_in_property_query = self::select(
            'id',
            'name'
        )->where('active', 1)->orderby('rank', 'asc')->orderby('id', 'asc');

        // Get property types.
        $property_types = $property_types_in_property_query->get()->toArray();

        // Property types.
        $response_property_types = [];
        foreach ($property_types as $property_type) {
            $response_property_types[] = [
                'id'       => $property_type['id'],
                'name'     => $property_type['name'],
                'selected' => ($property_type['id'] == $selected_property_type) ? 1 : 0,
            ];
        }

        return $response_property_types;

    }//end getAllPropertyTypesData()


    /**
     * Get Search Link.
     *
     * @param string $city    Property City.
     * @param string $amenity Property Amenity.
     *
     * @return string
     */
    private static function getSearchLink(string $city, string $amenity)
    {
        $city    = strtolower(str_replace(' ', '-', $city));
        $amenity = (empty($amenity) === true) ? 'stay' : strtolower(str_replace(' ', '-', str_replace('&', 'and', $amenity))).'s';
        return $amenity.'-in-'.$city;

    }//end getSearchLink()


    /**
     * Get property type by pid.
     *
     * @param integer $property_id Property id.
     *
     * @return string
     */
    public static function getPropertyTypeByPid(int $property_id)
    {
        $type = self::from('property_type as pt')->join('properties as p', 'p.property_type', '=', 'pt.id')->where('p.id', $property_id)->first();

        return $type->name;

    }//end getPropertyTypeByPid()


    /**
     * Get All property type.
     *
     * @return array
     */
    public static function getAllPropertyTypes()
    {
        return self::select('id', 'name')->active()->orderBy('name', 'ASC')->get()->toArray();

    }//end getAllPropertyTypes()


}//end class
