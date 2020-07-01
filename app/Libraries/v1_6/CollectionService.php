<?php
/**
 * Collection Service contains methods to return collection data
 */

namespace App\Libraries\v1_6;

use App\Models\CancellationPolicy;
use App\Models\Collection;
use App\Models\CurrencyConversion;
use App\Models\CountryCodeMapping;
use App\Models\MyFavourite;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyVideo;
use App\Models\PropertyTagMapping;
use App\Models\User;

use App\Libraries\Helper;
use Carbon\Carbon;

use App\Libraries\v1_6\PropertyTileService;

/**
 * Class CollectionService
 */
class CollectionService
{


    /**
     * Get all collections.
     *
     * @param array $data Array Containing data to fetch collections.
     *
     * @return array Collection data.
     */
    public static function getCollectionData(array $data)
    {
        // Collect input params.
        $user_id              = $data['user_id'];
        $is_user_logged_in    = $data['is_user_logged_in'];
        $currency             = $data['currency'];
        $collection_id        = (array_key_exists('collection_id', $data) === true) ? $data['collection_id'] : '';
        $offset               = $data['offset'];
        $total                = $data['total'];
        $property_total       = (isset($data['property_total']) === true ) ? $data['property_total'] : 0;
        $property_offset      = (isset($data['property_offset']) === true ) ? $data['property_offset'] : 0;
        $headers              = $data['headers'];
        $start_date           = $data['start_date'];
        $end_date             = $data['end_date'];
        $units                = DEFAULT_NUMBER_OF_UNITS;
        $guests               = DEFAULT_NUMBER_OF_GUESTS;
        $property_image_count = (isset($data['images_count']) === true) ? $data['images_count'] : 1;

        $user = '';

        // Get collection data.
        $collection_data = Collection::getCollectionAndPropertyData(
            [
                'collection_id'   => $collection_id,
                'offset'          => $offset,
                'total'           => $total,
                'property_total'  => $property_total,
                'property_offset' => $property_offset,
            ]
        );

        // Recently viewed property ids.
        $property_ids = array_unique(array_column($collection_data, 'property_id'));

        if (empty($headers['device-type']) === false && $headers['device-type'][0] === 'website') {
            // For 2x Image on website.
            $headers['image_optimized'] = 1;
        }

        // Get properties images.
        $properties_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, $property_image_count);

        // Get videos.
        $properties_videos = PropertyVideo::getPropertyVideosByPropertyIds($property_ids);

        // Get tags.
        $properties_tags = PropertyTagMapping::getPropertyTagsWithColorCodingByPropertyIds($property_ids, 1);

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        // Get associative array of all currency conversions.
        $currency_rates = CurrencyConversion::getAllCurrencyDetails();

        // Cancellation policies.
        $cancellation_policy_ids = array_unique(array_column($collection_data, 'cancelation_policy'));

        // Get cancellation policy data.
        $cancellation_policy_data = CancellationPolicy::getCancellationPoliciesByIds($cancellation_policy_ids);

        // Organized collection data.
        $combined_collection_data = [];
        // Not organized.
        $organized_collection_data = [];

        // Collection in properties.
        $property_collection_mapping = [];

        // Properties liked by user.
        $user_liked_property_ids = [];

        if (isset($data['is_particular']) === true && $data['is_particular'] === 1) {
            $collection_list = Collection::getCollectionData($offset, $total, $collection_id);

            foreach ($collection_list as $one_collection) {
                // Collection hash id.
                $collection_hash_id = Helper::encodeCollectionId($one_collection['collection_id']);
                $base_url           = Property::imageBaseUrlAsPerDeviceSizeAndConnection(array_merge($headers, ['image_type' => 'attraction']));

                // Collection info in case of new collection.
                $combined_collection_data[$one_collection['collection_id']] = [
                    'collection_id'      => $one_collection['collection_id'],
                    'collection_hash_id' => $collection_hash_id,
                    'collection_title'   => $one_collection['collection_title'],
                    'collection_image'   => $base_url['image_base_url'].$one_collection['collection_image'],
                    'properties'         => [],
                ];
            }
        }

        // Is user logged in.
        if ($is_user_logged_in === true) {
            // Is user liked these properties.
            $liked_collection_properties = MyFavourite::getUserWishlistedPropertiesFromPropertyIds($user_id, $property_ids);

            // User liked properties.
            $user_liked_property_ids = array_unique(array_column($liked_collection_properties, 'property_id'));

            $user = User::find($user_id);
        }

        // Collection data.
        foreach ($collection_data as $one_collection) {
            $collection_id = $one_collection->collection_id;
            $property_id   = $one_collection->property_id;

            // Is new collection.
            if (array_key_exists($collection_id, $combined_collection_data) === false) {
                $base_url = Property::imageBaseUrlAsPerDeviceSizeAndConnection(array_merge($headers, ['image_type' => 'attraction']));
                // Collection hash id.
                $collection_hash_id = Helper::encodeCollectionId($collection_id);

                // Collection info in case of new collection.
                $combined_collection_data[$collection_id] = [
                    'collection_id'      => $collection_id,
                    'collection_hash_id' => $collection_hash_id,
                    'collection_title'   => $one_collection->collection_title,
                    'collection_image'   => $base_url['image_base_url'].$one_collection->collection_image,
                    'properties'         => [],
                ];
            }

            // Array of data to process and get property pricing.
            $property_pricing_data = [
                'property_id'            => $one_collection->property_id,
                'start_date'             => $start_date,
                'end_date'               => $end_date,
                'units'                  => $units,
                'guests'                 => $guests,
                'user_currency'          => $currency,
                'property_currency'      => $one_collection->currency,
                'per_night_price'        => $one_collection->per_night_price,
                'additional_guest_fee'   => $one_collection->additional_guest_fee,
                'cleaning_fee'           => $one_collection->cleaning_fee,
                'cleaning_mode'          => $one_collection->cleaning_mode,
                'service_fee'            => $one_collection->service_fee,
                'custom_discount'        => $one_collection->custom_discount,
                'fake_discount'          => $one_collection->fake_discount,
                'accomodation'           => $one_collection->accomodation,
                'additional_guest_count' => $one_collection->additional_guest_count,
                'property_units'         => $one_collection->units,
                'instant_book'           => $one_collection->instant_book,
                'gh_commission'          => (int) $one_collection->gh_commission,
                'markup_service_fee'     => (int) $one_collection->markup_service_fee,
                'min_nights'             => $one_collection->min_nights,
                'max_nights'             => $one_collection->max_nights,
                'prive'                  => $one_collection->prive,
                'room_type'              => $one_collection->room_type,
                'bedrooms'               => $one_collection->bedrooms,
                'user'                   => $user,
                'error'                  => [],
            ];

            // Get property pricing details.
            $property_pricing = PropertyPricingService::getPropertyPrice($property_pricing_data);

            // As No coupon here.
            $discount                = 0;
            $gh_commission_from_host = (($property_pricing['total_host_fee'] * $property_pricing['gh_commission_percent']) / 100);

            $host_amount = ($property_pricing['total_host_fee'] - $gh_commission_from_host);

            $gst = Helper::calculateGstAmount(
                $host_amount,
                $property_pricing_data['room_type'],
                $property_pricing_data['bedrooms'],
                $property_pricing_data['user_currency'],
                $property_pricing['no_of_nights'],
                $property_pricing['required_units'],
                $property_pricing['total_service_fee'],
                $property_pricing['total_markup_fee'],
                $gh_commission_from_host
            );

            $property_pricing['gst_percent'] = $gst['host_gst_percentage'];

            $property_pricing['gst_amount'] = $gst['total_gst'];

            $property_pricing['total_price_all_nights_with_cleaning_price_gst'] = ($property_pricing['total_price_all_nights_with_cleaning_price'] + $property_pricing['gst_amount']);

            // Params for calculating payment methods.
            $payment_methods_params = [
                'is_instant_bookable'            => $property_pricing['is_instant_bookable'],
                'service_fee'                    => $property_pricing['total_service_fee'],
                'gh_commission'                  => $property_pricing['gh_commission_percent'],
                'coa_fee'                        => $property_pricing['coa_fee'],
                'gst'                            => $property_pricing['gst_amount'],
                'cash_on_arrival'                => $one_collection->cash_on_arrival,
                'booking_amount'                 => $property_pricing['total_price_all_nights'],
                'released_payment_refund_amount' => 0,
                'payable_amount'                 => $property_pricing['total_price_all_nights_with_cleaning_price_gst'],
                'prive'                          => $one_collection->prive,
                'cancelation_policy'             => $one_collection->cancelation_policy,
                'payment_gateway_enabled'        => 1,
                'checkin'                        => $start_date,
                'policy_days'                    => $cancellation_policy_data[$one_collection->cancelation_policy]['policy_days'],
                'user_currency'                  => $currency,
                'prive_property_coa_max_amount'  => Helper::convertPriceToCurrentCurrency('INR', PRIVE_PROPERTY_COA_MAX_AMOUNT, $currency),
                'partial_payment_coa_max_amount' => Helper::convertPriceToCurrentCurrency('INR', PARTIAL_PAYMENT_COA_MAX_AMOUNT, $currency),
                'checkin_formatted'              => Carbon::parse($start_date)->format('d M Y'),
                'markup_service_fee'             => $property_pricing['total_markup_fee'],
                'total_host_fee'                 => $property_pricing['total_host_fee'],
            ];

            // Get payment methods and label to display.
            $payment_methods = PaymentMethodService::getPaymentMethods($payment_methods_params);

            $host_image = (empty($one_collection->host_image) === false) ? $one_collection->host_image : '';
            $gender     = (empty($one_collection->host_gender) === false) ? $one_collection->host_gender : 'Male';
            $host_image = Helper::generateProfileImageUrl($gender, $host_image, $one_collection->host_id);

            // One collection data.
            // Get desired structure.
            $one_organized_property_data = PropertyTileService::getPropertytileStructure(
                [
                    'property_id'           => $property_id,
                    'property_score'        => (float) $one_collection->property_score,
                    'property_type_name'    => $one_collection->property_type_name,
                    'room_type'             => $one_collection->room_type,
                    'room_type_name'        => $one_collection->room_type_name,
                    'area'                  => $one_collection->area,
                    'city'                  => $one_collection->city,
                    'state'                 => $one_collection->state,
                    'country'               => $one_collection->country,
                    'country_codes'         => $country_codes,
                    'latitude'              => $one_collection->latitude,
                    'longitude'             => $one_collection->longitude,
                    'accomodation'          => $one_collection->accomodation,
                    'currency'              => $property_pricing['currency'],
                    'is_liked_by_user'      => (in_array($property_id, $user_liked_property_ids) === true) ? 1 : 0,
                    'display_discount'      => $property_pricing['effective_discount_percentage'],
                    'price_after_discount'  => $property_pricing['per_night_per_unit_price_without_service_fee'],
                    'price_before_discount' => (($property_pricing['per_night_per_unit_price_without_service_fee'] * 100) / (100 - $property_pricing['effective_discount_percentage'])),
                    'instant_book'          => $property_pricing['is_instant_bookable'],
                    'cash_on_arrival'       => (isset($payment_methods['coa_payment']) === true) ? 1 : 0,
                    'bedrooms'              => $one_collection->bedrooms,
                    'units_consumed'        => $one_collection->units_consumed,
                    'title'                 => $one_collection->title,
                    'properties_images'     => $properties_images,
                    'properties_videos'     => $properties_videos,
                    'properties_tags'       => $properties_tags,
                    'host_name'             => $one_collection->host_name,
                    'host_image'            => $host_image,
                ]
            );

            // Map property and collection.
            if (array_key_exists($property_id, $property_collection_mapping) === false) {
                $property_collection_mapping[$property_id] = [];
            }

            // Add collection to property array.
            array_push($property_collection_mapping[$property_id], $collection_id);

            // Add properties of collection.
            $combined_collection_data[$collection_id]['properties'][$property_id] = $one_organized_property_data;
        }//end foreach

        // Organize data - remove collection id and property id keys.
        $organized_collection_data = $combined_collection_data;

        // Iterate over collections.
        foreach ($organized_collection_data as $collection_id => $one_collection) {
            // Remove property id as keys.
            $organized_collection_data[$collection_id]['properties'] = array_values($one_collection['properties']);
        }

        // Remove colection ids as keys.
        $organized_collection_data = array_values($organized_collection_data);

        return $organized_collection_data;

    }//end getCollectionData()


}//end class
