<?php
/**
 * Property service containing all property releated functions
 */

namespace App\Libraries\v1_6;

use Carbon\Carbon;
use App\Models\{Property, CountryCodeMapping, PropertyTagMapping, Amenity, PropertyImage};
use App\Libraries\{Helper};

/**
 * Class PropertyService
 */
class PropertyTileService
{


    /**
     * Get Property tile structure.
     *
     * @param array $data Property data.
     *
     * @return array Property data for tile.
     */
    public static function getPropertytileStructure(array $data)
    {
        $property_id      = $data['property_id'];
        $property_hash_id = Helper::encodePropertyId($property_id);

        if (isset($data['smart_discount']) === true && $data['smart_discount'] > 0) {
            $smart_discount = [
                'header'   => 'Extra '.(int) $data['smart_discount'].'% Off',
                'discount' => (int) $data['smart_discount'],
                'footer'   => 'TODAY ONLY',
            ];
        } else {
            $smart_discount = [
                'header'   => '',
                'discount' => 0,
                'footer'   => '',
            ];
        }

        //phpcs:disable Squiz.ControlStructures.InlineIfDeclaration.NotSingleLine
        return [
            'property_id'               => (int) $property_id,
            'property_hash_id'          => $property_hash_id,
            'property_score'            => (empty($data['property_score']) === true) ? 0 : number_format($data['property_score'], 1),
            'property_type_name'        => $data['property_type_name'],
            'room_type_name'            => $data['room_type_name'],
            'host_name'                 => ucfirst($data['host_name']),
            'host_image'                => $data['host_image'],
            'location'                  => [
                'area'          => ucfirst($data['area']),
                'city'          => ucfirst($data['city']),
                'state'         => ucfirst($data['state']),
                'country'       => $data['country_codes'][$data['country']],
            // Country name from code.
                'location_name' => Helper::formatLocation($data['area'], $data['city'], $data['state'], ((isset($data['search_keyword']) === true) ? $data['search_keyword'] : '')),
                'latitude'      => $data['latitude'],
                'longitude'     => $data['longitude'],
            ],
            'accomodation'              => (int) $data['accomodation'],
            'min_units_required'        => (int) $data['units_consumed'],
            'total_accomodation'        => (((int) $data['accomodation']) * ((int) $data['units_consumed'])),
            'is_liked_by_user'          => (int) $data['is_liked_by_user'],
            'prices'                    => [
                'display_discount'                 => (int) $data['display_discount'],
                'smart_discount'                   => $smart_discount,
                'final_currency'                   => CURRENCY_SYMBOLS[$data['currency']],
                'price_after_discount'             => Helper::getFormattedMoney($data['price_after_discount'], $data['currency']),
                'price_after_discount_unformatted' => $data['price_after_discount'],
                'price_before_discount'            => Helper::getFormattedMoney($data['price_before_discount'], $data['currency']),
            ],
            'payment_methods'           => [
                'instant_book'    => (int) $data['instant_book'],
                'cash_on_arrival' => (int) $data['cash_on_arrival'],
            ],
            'title'                     => ucfirst(
                Property::propertyTitle(
                    [
                        'room_type'      => $data['room_type'],
                        'room_type_name' => $data['room_type_name'],
                        'bedrooms'       => $data['bedrooms'],
                        'property_type'  => $data['property_type_name'],
                        'units_consumed' => $data['units_consumed'],
                        'city'           => $data['city'],
                        'title'          => $data['title'],
                    ]
                )
            ),
            'property_title'            => ucfirst($data['title']),
            'property_images'           => (array_key_exists($property_id, $data['properties_images']) === true) ? $data['properties_images'][$property_id] : [],
            'property_videos_available' => (array_key_exists($property_id, $data['properties_videos']) === true) ? 1 : 0,
            'property_tags'             => (array_key_exists($property_id, $data['properties_tags']) === true) ? $data['properties_tags'][$property_id] : [],
            'url'                       => VERSION_PREFIX.'/property/'.$property_hash_id,
            'usp'                       => (empty($data['property_description']) === false) ? $data['property_description']['usp'] : '',
        ];
        //phpcs:enable

    }//end getPropertytileStructure()


    /**
     * Get Property min tile structure.
     *
     * @param array $data Property data.
     *
     * @return array Property data for min tile.
     */
    public static function minPropertyTileStructure(array $data)
    {
        $property_id      = $data['id'];
        $property_hash_id = Helper::encodePropertyId($property_id);
        $country_codes    = CountryCodeMapping::getCountries();
        $title            = [];

        if ($data['original_title'] === false) {
            $title = Property::propertyTitle(
                [
                    'room_type'      => $data['room_type'],
                    'room_type_name' => $data['room_type_name'],
                    'bedrooms'       => $data['bedrooms'],
                    'property_type'  => $data['property_type_name'],
                    'units_consumed' => $data['units_consumed'],
                    'city'           => $data['city'],
                    'title'          => $data['title'],
                ]
            );
        }

        $data['host_gender'] = (isset($data['host_gender']) === true) ? $data['host_gender'] : ((isset($data['gender']) === true) ? $data['gender'] : 'Male');

        return [
            'property_id'      => (int) $property_id,
            'property_hash_id' => $property_hash_id,
            'property_type'    => $data['property_type_name'],
            'room_type'        => $data['room_type_name'],
            'property_score'   => (empty($data['property_score']) === true) ? 0 : number_format($data['property_score'], 1),
            'host_name'        => ucfirst($data['host_name']),
            'host_image'       => Helper::generateProfileImageUrl($data['host_gender'], $data['host_image']),
            'location'         => [
                'area'          => ucfirst($data['area']),
                'city'          => ucfirst($data['city']),
                'state'         => ucfirst($data['state']),
                // Country name from code.
                'country'       => $country_codes[$data['country']],
                'location_name' => Helper::formatLocation($data['area'], $data['city'], $data['state']),
                'latitude'      => $data['latitude'],
                'longitude'     => $data['longitude'],
            ],
            'title'            => ($data['original_title'] === false) ? ucfirst($title) : ucfirst($data['title']),
            'property_title'   => ucfirst($data['title']),
            'property_images'  => (array_key_exists($property_id, $data['properties_images']) === true) ? $data['properties_images'][$property_id] : [],
            'url'              => VERSION_PREFIX.'/property/'.$property_hash_id,
        ];

    }//end minPropertyTileStructure()


    /**
     * Get Property tile structure for Booking Request.
     *
     * @param array $data    Property data.
     * @param array $headers Headers Data.
     *
     * @return array Property data for min tile.
     */
    public static function minPropertyTileStructureWithExtraInfo(array $data, array $headers=[])
    {
        // Get property images.
        $data['properties_images'] = PropertyImage::getPropertiesImagesByIds([$data['id']], $headers, 1);
        $data['original_title']    = true;

         // Get all property tags of property.
        $property_tags = PropertyTagMapping::getPropertyTagsWithColorCodingByPropertyIds([$data['id']]);

        // Get all property amenities.
        $property_amenities = ($data['amenities'] === '') ? [] : array_map('intval', explode(',', $data['amenities']));

        // Get property description and details.
        $property_description = PropertyService::getPropertyDescription($data['id'], $data['property_check_in'], $data['property_checkout']);

        if (count($property_amenities) > 0) {
            $property_amenities = Amenity::getPropertyAmenityDetails($property_amenities, $headers);
        }

        $property_tile = self::minPropertyTileStructure($data);

        // Adding Extra Data in Property min Tile.
        $property_tile['tags']      = (array_key_exists($data['id'], $property_tags) === true) ? $property_tags[$data['id']] : [];
        $property_tile['amenities'] = $property_amenities;

        $property_tile['usp']          = $property_description['usp'];
        $property_tile['description']  = $property_description['description'];
        $property_tile['how_to_reach'] = $property_description['how_to_reach'];

        $property_tile['units_consumed'] = $data['units_consumed'];
        $property_tile['zipcode']        = $data['property_zipcode'];

        return $property_tile;

    }//end minPropertyTileStructureWithExtraInfo()


    /**
     * Get Property tile pricing array
     *
     * @param array $pricing Property pricing  data.
     *
     * @return array Property pricing data for min tile.
     */
    public static function propertyTilePricingArray(array $pricing)
    {
        return [
            'currency'                              => CURRENCY_SYMBOLS[$pricing['currency']],
            'cleaning_price'                        => $pricing['cleaning_price'],
            'per_night_price'                       => Helper::getFormattedMoney(($pricing['total_price_per_night'] - ($pricing['total_service_fee'] / $pricing['no_of_nights'])), $pricing['currency']),
            'per_night_price_unformatted'           => ($pricing['total_price_per_night'] - ($pricing['total_service_fee'] / $pricing['no_of_nights'])),
            'discount'                              => $pricing['effective_discount_percentage'],
            'original_price'                        => round(
                (($pricing['total_price_per_night'] - ($pricing['total_service_fee'] / $pricing['no_of_nights'])) * 100 ) / (100 - $pricing['effective_discount_percentage'])
            ),
            'per_night_per_guest_extra_guest_price' => ($pricing['per_night_per_guest_extra_guest_price_without_service_fee']),
            'per_night_all_guest_extra_guest_price' => ($pricing['per_night_all_guest_extra_guest_price_without_service_fee']),
            'is_instant_bookable'                   => $pricing['is_instant_bookable'],
            'per_unit_guests'                       => $pricing['per_unit_guests'],
        ];

    }//end propertyTilePricingArray()


    /**
     * Get tile structure for host booking list.
     *
     * @param array $data Booking data.
     *
     * @return array
     */
    public static function hostBookingListStructure(array $data)
    {
        return [
            'request_hash_id'  => Helper::encodeBookingRequestId($data['request_id']),
            'no_of_nights'     => (int) $data['no_of_nights'],
            'guests'           => (int) $data['guests'],
            'units'            => (int) $data['units'],
            'checkin_checkout' => $data['checkin_checkout'],
            'timeline_status'  => $data['timeline_status'],
            'amount'           => Helper::getFormattedMoney($data['host_amount'], $data['currency']),
            'booking_status'   => $data['booking_status'],
            'checkin'          => $data['checkin'],
            'checkout'         => $data['checkout'],
            'property_hash_id' => Helper::encodePropertyId($data['pid']),
            'location_name'    => Helper::formatLocation($data['area'], $data['city'], $data['state']),
            'title'            => ucfirst($data['title']),
            'property_image'   => (array_key_exists($data['pid'], $data['properties_images']) === true) ? $data['properties_images'][$data['pid']] : [],
            'expires_in'       => ($data['expires_in'] > 0) ? $data['expires_in'] : 0,
        ];

    }//end hostBookingListStructure()


     /**
      * Get tile structure for prive booking list.
      *
      * @param array $data Booking data.
      *
      * @return array
      */
    public static function priveBookingListStructure(array $data)
    {
        return [
            'request_hash_id' => Helper::encodeBookingRequestId($data['request_id']),
            'guest_name'      => ucfirst($data['guest_name']),
            'guests'          => (int) $data['guests'],
            'amount'          => Helper::getFormattedMoney($data['host_amount'], $data['currency']),
            'booking_status'  => $data['booking_status'],
            'checkin'         => $data['checkin'],
            'checkout'        => $data['checkout'],
            'title'           => ucfirst($data['title']),
            'units'           => $data['units'],
            'room'            => $data['bedroom'],
        ];

    }//end priveBookingListStructure()


    /**
     * Get tile structure for prive manager booking list.
     *
     * @param array $data Booking data.
     *
     * @return array
     */
    public static function priveManagerBookingListStructure(array $data)
    {
        $country_codes = CountryCodeMapping::getCountries();

        return [
            'request_hash_id'    => Helper::encodeBookingRequestId($data['request_id']),
            'guests'             => $data['guests'],
            'amount'             => Helper::getFormattedMoney($data['payable_amount'], $data['currency']),
            'booking_status'     => $data['booking_status'],
            'status'             => $data['checkedin_status'],
            'checkin'            => $data['checkin'],
            'checkout'           => $data['checkout'],
            'checkin_formatted'  => $data['checkin_formatted'],
            'checkout_formatted' => $data['checkout_formatted'],
            'property_hash_id'   => Helper::encodePropertyId($data['property_id']),
            'property_title'     => $data['property_id'].' • '.ucfirst($data['title']),
            'property_type_name' => $data['property_type_name'],
            'room_type_name'     => $data['room_type_name'],
            'traveller_hash_id'  => Helper::encodeUserId($data['guest_id']),
            'traveller_name'     => ucfirst(trim($data['guest_name'].' '.$data['guest_last_name'])),
            'verified'           => $data['guest_verified'],
            'location'           => [
                'area'          => ucfirst($data['area']),
                'city'          => ucfirst($data['city']),
                'state'         => ucfirst($data['state']),
                'country'       => $country_codes[$data['country']],
                // Country name from code.
                'location_name' => Helper::formatLocation($data['area'], $data['city'], $data['state']),
            ],
            'traveller_email'    => Helper::getModifiedEmail($data['traveller_email']),
            'contacts'           => [
                'manager'   => [
                    'primary'   => Helper::getFormattedContact($data['manager_primary_contact']),
                    'secondary' => Helper::getFormattedContact($data['manager_secondary_contact']),
                ],
                'traveller' => [
                    'primary'   => Helper::getFormattedContact($data['traveller_primary_contact']),
                    'secondary' => Helper::getFormattedContact($data['traveller_secondary_contact']),
                    'contact'   => $data['traveller_primary_contact'],
                ],
            ],
        ];

    }//end priveManagerBookingListStructure()


    /**
     * Get Host Property tile structure.
     *
     * @param array $data Property data.
     *
     * @return array Property data for tile.
     */
    public static function getHostPropertytileStructure(array $data)
    {
        $property_id      = $data['id'];
        $property_hash_id = Helper::encodePropertyId($property_id);
        $country_codes    = CountryCodeMapping::getCountries();

        $data['host_gender'] = (isset($data['host_gender']) === true) ? $data['host_gender'] : 'Male';

        return [
            'property_hash_id'      => $property_hash_id,
            'property_type_name'    => $data['property_type_name'],
            'room_type_name'        => $data['room_type_name'],
            'host_name'             => ucfirst($data['host_name']),
            'host_image'            => Helper::generateProfileImageUrl($data['host_gender'], $data['host_image']),
            'location'              => [
                'area'          => ucfirst($data['area']),
                'city'          => ucfirst($data['city']),
                'state'         => ucfirst($data['state']),
                'country'       => $country_codes[$data['country']],
            // Country name from code.
                'location_name' => Helper::formatLocation($data['area'], $data['city'], $data['state']),
                'latitude'      => $data['latitude'],
                'longitude'     => $data['longitude'],
            ],
            'prices'                => [
                'currency'        => CURRENCY_SYMBOLS[$data['currency']],
                'per_night_price' => $data['per_night_price'],
            ],
            'title'                 => ucfirst(
                Property::propertyTitle(
                    [
                        'room_type'      => $data['room_type'],
                        'room_type_name' => $data['room_type_name'],
                        'bedrooms'       => $data['bedrooms'],
                        'property_type'  => $data['property_type_name'],
                        'units_consumed' => $data['units_consumed'],
                        'city'           => $data['city'],
                        'title'          => $data['title'],
                    ]
                )
            ),
            'property_title'        => ucfirst($data['title']),
            'property_images'       => (array_key_exists($property_id, $data['properties_images']) === true) ? $data['properties_images'][$property_id] : [],
            'url'                   => VERSION_PREFIX.'/property/'.$property_hash_id,
            'last_updated'          => $data['last_updated'],
            'calendar_last_updated' => $data['calendar_last_updated'],
            'show_manage_calender'  => $data['show_manage_calender'],
            'property_enable'       => $data['enabled'],
            'property_status'       => $data['property_status_text'],
            'booking_count'         => (empty($data['booking_count']) === false) ? (int) $data['booking_count'] : 0,
            'avg_response_time'     => Helper::stringTimeFormattedString($data['avg_response_time']),
            'edit_listing'          => WEBSITE_URL.'/user/applogin?auth_key='.$data['auth_key'].'&app=1&next_url=/properties/edit/'.$property_hash_id.'?app=1',
        ];

    }//end getHostPropertytileStructure()


    /**
     * Get Host Listing Property tile structure.
     *
     * @param array $data Property data.
     *
     * @return array Property data for tile.
     */
    public static function getHostListingPropertyTile(array $data)
    {
        $property_id      = $data['id'];
        $property_hash_id = Helper::encodePropertyId($property_id);
        $country_codes    = CountryCodeMapping::getCountries();

        $check_in         = @Carbon::createFromFormat('H:i:s', $data['check_in'])->format('g:i a');
        $check_out        = @Carbon::createFromFormat('H:i:s', $data['checkout'])->format('g:i a');
        $property_address = Helper::decodePropertyAddress($data['address']);

        if (empty($property_address) === true) {
            $property_address = '';
        }

        return [
            'property_hash_id'       => $property_hash_id,
            'units'                  => $data['units'],
            'accomodation'           => $data['accomodation'],
            'additional_guest_count' => $data['additional_guest_count'],
            'bedrooms'               => $data['bedrooms'],
            'beds'                   => $data['beds'],
            'bathrooms'              => $data['bathrooms'],
            'property_title'         => ucfirst($data['title']),
            'noc_status'             => $data['noc_status'],
            'min_nights'             => $data['min_nights'],
            'max_nights'             => $data['max_nights'],
            'check_in'               => $check_in,
            'check_out'              => $check_out,
            'cleaning_mode'          => $data['cleaning_mode'],
            'location'               => [
                'address'   => $property_address,
                'area'      => $data['area'],
                'city'      => $data['city'],
                'state'     => $data['state'],
                'country'   => $country_codes[$data['country']],
                'zipcode'   => $data['zipcode'],
                'latitude'  => $data['latitude'],
                'longitude' => $data['longitude'],
            ],
            'prices'                 => [
                'currency'             => CURRENCY_SYMBOLS[$data['currency']],
                'per_night_price'      => $data['per_night_price'],
                'per_week_price'       => $data['per_week_price'],
                'per_month_price'      => $data['per_month_price'],
                'per_week_price'       => $data['per_week_price'],
                'per_week_price'       => $data['per_week_price'],
                'additional_guest_fee' => $data['additional_guest_fee'],
                'gh_commission'        => $data['gh_commission'],
                'markup_service_fee'   => $data['markup_service_fee'],
                'cleaning_fee'         => $data['cleaning_fee'],
            ],
            // phpcs:ignore
            'last_update'            => (new Carbon($data['last_updated']))->formatLocalized('%d %B %Y'),
            'enabled'                => $data['enabled'],
            'status'                 => $data['status'],
            'gstin'                  => $data['gstin'],
            'video_link'             => $data['video_link'],
            'property_images'        => $data['properties_images'],
            'properties_videos'      => $data['properties_videos'],
            'url'                    => VERSION_PREFIX.'/property/'.$property_hash_id,
            'details'                => [
                'policy_services'        => $data['policy_services'],
                'your_space'             => $data['your_space'],
                'house_rule'             => $data['house_rule'],
                'guest_brief'            => $data['guest_brief'],
                'interaction_with_guest' => $data['interaction_with_guest'],
                'local_experience'       => $data['local_experience'],
                'from_airport'           => $data['from_airport'],
                'train_station'          => $data['train_station'],
                'bus_station'            => $data['bus_station'],
                'extra_detail'           => $data['extra_detail'],
                'usp'                    => $data['usp'],
            ],
        ];

    }//end getHostListingPropertyTile()


}//end class
