<?php
/**
 * Amenity Model contain all functions related to property amenties
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
// phpcs:disable  
/**
 * // phpcs:enable
 * Class Amenity
 */
class Amenity extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'amenities';


    /**
     * Helper function to create scope with active equal one
     *
     * @param EloquentQuery $query Eloquent model query.
     *
     * @return EloquentQuery Active scope query
     */
    public static function scopeActive(EloquentQuery $query)
    {
        return $query->where('active', 1);

    }//end scopeActive()


    /**
     * Helper function to get active amenties.
     *
     * @return array  Array of all amenties
     */
    public static function getActiveAmenities()
    {
        // Get property types linked to active properties.
        return self::active()->select(['id', 'cat_id', 'category_name', 'amenity_name'])->orderby('rank', 'asc')->get()->toArray();

    }//end getActiveAmenities()


    /**
     * Helper function to get active amenties group by category.
     *
     * @param array $selected_amenities Selected Amenities.
     *
     * @return array Array of all amenties category wise.
     */
    public static function getActiveAmenitiesGroupedByCategory(array $selected_amenities=[])
    {
        // Amenities.
        $amenities = [];

        // Get property types linked to active properties.
        $raw_amenities = self::getActiveAmenities();

        // Iterate over amenities and club them.
        foreach ($raw_amenities as $amenity) {
            if (array_key_exists($amenity['cat_id'], $amenities) === false) {
                // Create category if doesn't exist.
                $amenities[$amenity['cat_id']] = [
                    'cat_id'        => $amenity['cat_id'],
                    'category_name' => $amenity['category_name'],
                    'amenities'     => [],
                ];
            }

            // Push new amenity.
            $amenities[$amenity['cat_id']]['amenities'][] = [
                'id'       => $amenity['id'],
                'name'     => $amenity['amenity_name'],
                'selected' => (in_array($amenity['id'], $selected_amenities) === true) ? 1 : 0,
            ];
        }

        return array_values($amenities);

    }//end getActiveAmenitiesGroupedByCategory()


    /**
     * Function to get amenity base url based upon headers.
     *
     * @param array $headers Array of headers from request.
     *
     * @return string Base url.
     */
    public static function getAmenityBaseUrl(array $headers)
    {
        $amenity_base_url = '';
        $height           = (isset($headers['screen_height']) === true) ? (int) $headers['screen_height'] : 0;

        if ($height < 480) {
            $amenity_base_url = AMENITY_ICON_URL.'amenities1x/';
        } else if ($height >= 480 && $height < 960) {
            $amenity_base_url = AMENITY_ICON_URL.'amenities2x/';
        } else if ($height >= 960) {
            $amenity_base_url = AMENITY_ICON_URL.'amenities3x/';
        }

        return $amenity_base_url;

    }//end getAmenityBaseUrl()


    /**
     * Function to get amenity details.
     *
     * @param array $amenities Array of amenties required.
     * @param array $headers   Array of headers from request.
     *
     * @return array Array of amenties details.
     */
    public static function getPropertyAmenityDetails(array $amenities, array $headers)
    {
        $amenity_name = self::select('id', 'amenity_name')->whereIn('id', $amenities)->get()->toArray();

        $amenity_name = array_column($amenity_name, 'amenity_name', 'id');

        $amenity_icon_url = self::getAmenityBaseUrl($headers);

        $output = [];

        foreach ($amenities as $amenity) {
            if (array_key_exists($amenity, $amenity_name) === true) {
                $output[] = [
                    'id'   => $amenity,
                    'name' => $amenity_name[$amenity],
                    'icon' => $amenity_icon_url.$amenity.'.png',
                    // phpcs:ignore
                    'rank' => (array_key_exists($amenity, AMENITY_RANK) === true) ? AMENITY_RANK[$amenity] : 100
                ];
            }
        }

        usort(
            $output,
            function ($amenity1, $amenity2) {
                return ($amenity1['rank'] <=> $amenity2['rank']);
            }
        );

        return $output;

    }//end getPropertyAmenityDetails()


    /**
     * Function to get amenity details.
     *
     * @return object array of amenties details.
     */
    public static function getAmenitiesForSearchPage()
    {
        $selected_amenities = self::select('id', 'amenity_name')->whereIn('id', [7, 3, 5, 41, 42, 12, 2, 4, 13, 23, 53])->get();

        foreach ($selected_amenities as $amenity) {
            $amenity->amenity_name = trim(explode('/', $amenity->amenity_name)[0]);
        }

        return $selected_amenities;

    }//end getAmenitiesForSearchPage()


}//end class
