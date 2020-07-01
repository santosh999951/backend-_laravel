<?php
/**
 * RecentlyViewedService containing all recently views service releated functions
 */

namespace App\Libraries\v1_6;

use Carbon\Carbon;
use App\Models\CancellationPolicy;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyTagMapping;
use App\Models\PropertyVideo;
use App\Models\User;
use App\Models\MyFavourite;

use App\Libraries\Helper;

/**
 * Class RecentlyViewedService
 */
class RecentlyViewedService
{


    /**
     * Get Recently viewed properties.
     *
     * @param array $data Recently Property data.
     *
     * @return array Property data for tile.
     */
    public static function getRecentlyViewedProperties(array $data)
    {
        // Input params.
        $user_id       = $data['user_id'];
        $start_date    = $data['start_date'];
        $end_date      = $data['end_date'];
        $days          = $data['days'];
        $guests        = $data['guests'];
        $currency      = $data['currency'];
        $units         = $data['bedroom'];
        $headers       = $data['headers'];
        $country_codes = $data['country_codes'];
        $offset        = $data['offset'];
        $limit         = $data['limit'];

        $user = User::find($user_id);

        // User's recently viewed properties.
        $recently_viewed_properties = Property::getRecentlySearchedProperties($user_id, $start_date, $end_date, $units, '', $guests, $days, $offset, $limit);

        // Recently viewed properties.
        $combined_recently_viewed_properties = [];

        // Recently viewed property ids.
        $recently_viewed_property_ids = array_column($recently_viewed_properties, 'id');

        // Get properties images.
        $properties_images = PropertyImage::getPropertiesImagesByIds($recently_viewed_property_ids, $headers, 1);

        // Get videos.
        $properties_videos = PropertyVideo::getPropertyVideosByPropertyIds($recently_viewed_property_ids);

        // Get tags.
        $properties_tags = PropertyTagMapping::getPropertyTagsWithColorCodingByPropertyIds($recently_viewed_property_ids, 1);

        // Cancellation policies.
        $cancellation_policy_ids = array_unique(array_column($recently_viewed_properties, 'cancelation_policy'));

        // Get cancellation policy data.
        $cancellation_policy_data = CancellationPolicy::getCancellationPoliciesByIds($cancellation_policy_ids);

        // Properties liked by user.
        $user_liked_property_ids = [];

        // Is user liked these properties.
        $liked_collection_properties = MyFavourite::getUserWishlistedPropertiesFromPropertyIds($user_id, $recently_viewed_property_ids);

        // User liked properties.
        $user_liked_property_ids = array_unique(array_column($liked_collection_properties, 'property_id'));

        // Iterate over each property.
        foreach ($recently_viewed_properties as $one_property) {
            $property_id = $one_property->id;

            // Array of data to process and get property pricing.
            $property_pricing_data = [
                'property_id'            => $one_property->id,
                'start_date'             => $start_date,
                'end_date'               => $end_date,
                'units'                  => $units,
                'guests'                 => $guests,
                'user_currency'          => $currency,
                'property_currency'      => $one_property->currency,
                'per_night_price'        => $one_property->per_night_price,
                'additional_guest_fee'   => $one_property->additional_guest_fee,
                'cleaning_fee'           => $one_property->cleaning_fee,
                'cleaning_mode'          => $one_property->cleaning_mode,
                'service_fee'            => $one_property->service_fee,
                'custom_discount'        => $one_property->custom_discount,
                'fake_discount'          => $one_property->fake_discount,
                'accomodation'           => (int) $one_property->accomodation,
                'additional_guest_count' => $one_property->additional_guest_count,
                'property_units'         => $one_property->units,
                'instant_book'           => $one_property->instant_book,
                'gh_commission'          => (int) $one_property->gh_commission,
                'markup_service_fee'     => (int) $one_property->markup_service_fee,
                'min_nights'             => $one_property->min_nights,
                'max_nights'             => $one_property->max_nights,
                'prive'                  => $one_property->prive,
                'room_type'              => $one_property->room_type,
                'bedrooms'               => $one_property->bedrooms,
                'user'                   => $user,
                'error'                  => [],
            ];

            // Get property pricing details.
            $property_pricing = PropertyPricingService::getPropertyPrice($property_pricing_data);

             // As No coupon here.
            $discount                = 0;
            $gh_commission_from_host = (($property_pricing['total_host_fee'] * $property_pricing['gh_commission_percent']) / 100);

            $host_amount = ($property_pricing['total_host_fee'] - $gh_commission_from_host);

            $gst = helper::calculateGstAmount(
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
                'cash_on_arrival'                => $one_property->cash_on_arrival,
                'booking_amount'                 => $property_pricing['total_price_all_nights'],
                'released_payment_refund_amount' => 0,
                'payable_amount'                 => $property_pricing['total_price_all_nights_with_cleaning_price_gst'],
                'prive'                          => $one_property->prive,
                'cancelation_policy'             => $one_property->cancelation_policy,
                'payment_gateway_enabled'        => 1,
                'checkin'                        => $start_date,
                'policy_days'                    => $cancellation_policy_data[$one_property->cancelation_policy]['policy_days'],
                'user_currency'                  => $currency,
                'prive_property_coa_max_amount'  => Helper::convertPriceToCurrentCurrency('INR', PRIVE_PROPERTY_COA_MAX_AMOUNT, $currency),
                'partial_payment_coa_max_amount' => Helper::convertPriceToCurrentCurrency('INR', PARTIAL_PAYMENT_COA_MAX_AMOUNT, $currency),
                'checkin_formatted'              => Carbon::parse($start_date)->format('d M Y'),
                'markup_service_fee'             => $property_pricing['total_markup_fee'],
                'total_host_fee'                 => $property_pricing['total_host_fee'],
            ];

            // Get payment methods and label to display.
            $payment_methods = PaymentMethodService::getPaymentMethods($payment_methods_params);

            $host_image = (empty($one_property->host_image) === false) ? $one_property->host_image : '';
            $gender     = (empty($one_property->host_gender) === false) ? $one_property->host_gender : 'Male';
            $host_image = Helper::generateProfileImageUrl($gender, $host_image, $one_property->host_id);

            // Get desired structure.
            $organized_one_property = PropertyTileService::getPropertytileStructure(
                [
                    'property_id'           => $property_id,
                    'property_score'        => (float) $one_property->property_score,
                    'property_type_name'    => $one_property->property_type_name,
                    'room_type'             => $one_property->room_type,
                    'room_type_name'        => $one_property->room_type_name,
                    'area'                  => $one_property->area,
                    'city'                  => $one_property->city,
                    'state'                 => $one_property->state,
                    'country'               => $one_property->country,
                    'country_codes'         => $country_codes,
                    'latitude'              => $one_property->latitude,
                    'longitude'             => $one_property->longitude,
                    'accomodation'          => $one_property->accomodation,
                    'currency'              => $property_pricing['currency'],
                    'is_liked_by_user'      => (in_array($property_id, $user_liked_property_ids) === true) ? 1 : 0,
                    'display_discount'      => $property_pricing['effective_discount_percentage'],
                    'price_after_discount'  => $property_pricing['per_night_per_unit_price_without_service_fee'],
                    'price_before_discount' => (($property_pricing['per_night_per_unit_price_without_service_fee'] * 100) / (100 - $property_pricing['effective_discount_percentage'])),
                    'instant_book'          => $property_pricing['is_instant_bookable'],
                    'cash_on_arrival'       => (isset($payment_methods['coa_payment']) === true) ? 1 : 0,
                    'bedrooms'              => $one_property->bedrooms,
                    'units_consumed'        => $one_property->units_consumed,
                    'title'                 => $one_property->title,
                    'properties_images'     => $properties_images,
                    'properties_videos'     => $properties_videos,
                    'properties_tags'       => $properties_tags,
                    'host_name'             => $one_property->host_name,
                    'host_image'            => $host_image,
                ]
            );

            // Push new recently viewed property.
            $combined_recently_viewed_properties[$property_id] = $organized_one_property;
        }//end foreach

        return $combined_recently_viewed_properties;

    }//end getRecentlyViewedProperties()


}//end class
