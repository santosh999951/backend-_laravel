<?php
/**
 * SimilarListingService contains method fetching similar properties to that of input
 */

namespace App\Libraries\v1_6;

use Carbon\Carbon;
use App\Models\CountryCodeMapping;
use App\Models\MyFavourite;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyTagMapping;
use App\Models\PropertyVideo;
use App\Models\User;

use App\Libraries\Helper;

/**
 * Class SimilarListingService
 */
class SimilarListingService
{


    /**
     * Get similar properties.
     *
     * @param array $data Property data.
     *
     * @return array Property data for tile.
     */
    public static function getSimilarProperties(array $data)
    {
        $similar_properties = Property::getSimilarProperties($data);
        // Recently viewed property ids.
        $similar_properties_property_ids = array_column($similar_properties, 'id');

        // Get properties images.
        $property_images = PropertyImage::getPropertiesImagesByIds($similar_properties_property_ids, $data['headers'], 1);

        $user_liked_property_ids = [];

        if ($data['user_id'] !== 0) {
            // Is user liked these properties.
            $liked_properties = MyFavourite::getUserWishlistedPropertiesFromPropertyIds($data['user_id'], $similar_properties_property_ids);

            // User liked properties.
            $user_liked_property_ids = array_unique(array_column($liked_properties, 'property_id'));
        }

        // Get all property tags of property.
        $property_tags = PropertyTagMapping::getPropertyTagsWithColorCodingByPropertyIds($similar_properties_property_ids, 1);

        // Get property videos.
        $property_videos = PropertyVideo::getPropertyVideosByPropertyIds($similar_properties_property_ids);

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        $output = [];

        foreach ($similar_properties as $property) {
            $gender     = (empty($property['host_gender']) === false) ? $property['host_gender'] : 'Male';
            $host_image = (empty($property['host_image']) === false) ? $property['host_image'] : '';
            $host_image = Helper::generateProfileImageUrl($gender, $host_image, $property['host_id']);

            $effective_discount_percentage = round((100 - ($property['final_rate_without_service_fee'] * 100) / $property['actual_rate_without_service_fee']), 0);

            $structured_property_data = PropertyTileService::getPropertytileStructure(
                [
                    'property_id'           => $property['id'],
                    'property_score'        => (float) $property['property_score'],
                    'property_type_name'    => $property['property_type_name'],
                    'room_type'             => $property['room_type'],
                    'room_type_name'        => $property['room_type_name'],
                    'area'                  => $property['area'],
                    'city'                  => $property['city'],
                    'state'                 => $property['state'],
                    'country'               => $property['country'],
                    'country_codes'         => $country_codes,
                    'latitude'              => $property['latitude'],
                    'longitude'             => $property['longitude'],
                    'accomodation'          => $property['accomodation'],
                    'currency'              => $data['currency'],
                    'is_liked_by_user'      => (in_array($property['id'], $user_liked_property_ids) === true) ? 1 : 0,
                    'display_discount'      => $effective_discount_percentage,
                    'price_after_discount'  => $property['final_rate_without_service_fee'],
                    'price_before_discount' => $property['actual_rate_without_service_fee'],
                    'instant_book'          => ($property['instant_book'] === false) ? $property['instant_book'] : 0,
                    'cash_on_arrival'       => 1,
                    'bedrooms'              => $property['bedrooms'],
                    'units_consumed'        => $property['units_consumed'],
                    'title'                 => $property['title'],
                    'properties_images'     => $property_images,
                    'properties_videos'     => $property_videos,
                    'properties_tags'       => $property_tags,
                    'host_name'             => $property['host_name'],
                    'host_image'            => $host_image,
                ]
            );

            $output[] = $structured_property_data;
        }//end foreach

        return $output;

    }//end getSimilarProperties()


}//end class
