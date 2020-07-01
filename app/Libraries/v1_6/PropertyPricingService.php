<?php
/**
 * Property pricing containing all pricing releated functions
 */

namespace App\Libraries\v1_6;

use \Carbon\Carbon;
use App\Models\CancellationPolicy;
use App\Models\InventoryPricing;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyPricing;
use App\Models\PropertyTagMapping;
use App\Models\PropertyVideo;
use App\Models\Booking;
use App\Models\{User, SmartDiscount};
use App\Libraries\Helper;

/**
 * Class PropertyPricingService
 */
class PropertyPricingService
{


    /**
     * Get property price.
     *
     * @param array $data Property pricing data.
     *
     * @return array Property prices.
     */
    public static function getPropertyPrice(array $data)
    {
        $early_bird_cashback_percentage = 0;
        $early_bird_cashback_text       = '';
        $early_bird_cashback_applicable = false;
        $error      = '';
        $error_code = '';

        $selected_units  = (int) $data['units'];
        $selected_guests = (int) $data['guests'];
        $user            = $data['user'];

        $property_additional_guest_count = (int) $data['additional_guest_count'];
        $property_accomodation           = (int) $data['accomodation'];
        $property_bedroom                = (int) $data['bedrooms'];
        $room_type = $data['room_type'];

        $from_currency = $data['property_currency'];
        $to_currency   = $data['user_currency'];
        // Check allowed guest per unit.
        $allowed_guest_per_unit = Property::getBaseAndAdditionalGuestCount($property_additional_guest_count, $property_accomodation);

        $property_guest_count_per_unit       = $allowed_guest_per_unit['base_guest_count'];
        $property_extra_guest_count_per_unit = $allowed_guest_per_unit['extra_guest_count'];
        $property_total_guest_count_per_unit = $allowed_guest_per_unit['total_guest_count'];

        // If no of guests more than guests to be accomodated by 1 unit.
         $total_all_units_extra_guests = 0;
        if ($selected_guests >= ($property_total_guest_count_per_unit * $selected_units)) {
            $min_reqiured_units = ( ceil($selected_guests / $property_total_guest_count_per_unit) > $selected_units) ? ceil($selected_guests / $property_total_guest_count_per_unit) : $selected_units;
        } else {
            $min_reqiured_units = $selected_units;
        }

        if (($min_reqiured_units * $property_guest_count_per_unit) < $selected_guests) {
            $total_all_units_extra_guests = ($selected_guests - ($min_reqiured_units * $property_guest_count_per_unit));
        }

        $data['total_all_units_extra_guests'] = $total_all_units_extra_guests;
        $data['min_reqiured_units']           = $min_reqiured_units;
        // Write Better version: check if seleccted guests can fit or not.
        // Check if start date and end date are not selected (if not, take default values).
        if (empty($data['start_date']) === true || empty($data['end_date']) === true) {
            $start_date_obj = Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS);
            $end_date_obj   = Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS);
        } else {
            $start_date_obj = Carbon::parse($data['start_date']);
            $end_date_obj   = Carbon::parse($data['end_date']);
        }

        $today_obj = Carbon::now('GMT');

        $no_of_days               = $start_date_obj->diffInDays($end_date_obj, false);
        $checkin_no_days_from_now = $today_obj->diffInDays($start_date_obj, false);

        // Check if user's no. of nights stay is within allowed range for property (if not, take default values).
        if ($no_of_days < $data['min_nights'] || $no_of_days > $data['max_nights']) {
            $error          = 'Minimum and maximum nights for which property can be booked is '.$data['min_nights'].' and '.$data['max_nights'].' respectively';
            $error_code     = 'min_max_night_invalid';
            $start_date_obj = Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS);
            $end_date_obj   = Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS);

            $no_of_days = ceil((BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS - BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS) / (24 * 60 * 60));
        }

        $data['start_date_obj'] = $start_date_obj;
        $data['end_date_obj']   = $end_date_obj;
        $data['no_of_days']     = $no_of_days;

        // Calculate 1 unit price of property.
        $pricing = self::calculatePerUnitPrice($data);

        $cleaning_price_per_unit = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['cleaning_price_per_unit'], $to_currency));
        // This is all days per units.
        $service_fee_on_cleaning_price_per_unit = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['service_fee_on_cleaning_price'], $to_currency));
        $service_fee_on_cleaning_price_all_unit = ($service_fee_on_cleaning_price_per_unit * $min_reqiured_units);

        $cleaning_price_all_unit = ($cleaning_price_per_unit * $min_reqiured_units);

        $is_available    = $pricing['is_available'];
        $available_units = $pricing['available_units'];

        $per_night_price_with_discount_service_fee = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['per_night_price_with_discount_service_fee'], $to_currency));

        $per_night_price_with_discount_service_fee_without_markup = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['per_night_price_with_discount_service_fee_without_markup'], $to_currency));

        $per_night_price_with_discount_without_service_fee_with_markup = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['per_night_price_with_discount_without_service_fee_with_markup'], $to_currency));

        $extra_guest_price_with_discount_and_service_fee = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['extra_guest_price_with_discount_and_service_fee'], $to_currency));

        $per_night_price_without_discount_service_fee       = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['per_night_price_without_discount_service_fee'], $to_currency));
        $extra_guest_price_without_discount_and_service_fee = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['extra_guest_price_without_discount_and_service_fee'], $to_currency));

         $extra_guest_price_with_discount_without_service_fee_with_markup = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['extra_guest_price_with_discount_without_service_fee_with_markup'], $to_currency));

         $extra_guest_price_with_discount_and_service_fee_without_markup = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['extra_guest_price_with_discount_and_service_fee_without_markup'], $to_currency));

        // This is for all nights.
        $total_service_fee_on_price_with_discount_per_unit    = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['total_service_fee_on_price_with_discount'], $to_currency));
        $total_service_fee_on_extra_guest_price_with_discount = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['total_service_fee_on_extra_guest_price_with_discount'], $to_currency));

        // This is for one nights.
        $service_fee_on_price_with_discount_per_unit_per_night              = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['per_night_service_fee'], $to_currency));
        $service_fee_on_extra_guest_price_with_discount_per_guest_per_night = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['per_night_service_fee_on_extra_guest_price'], $to_currency));

        $total_nights_all_units_gh_markup_on_price_plus_extra_guests = ceil(Helper::convertPriceToCurrentCurrency($from_currency, $pricing['total_nights_all_units_gh_markup_on_price_plus_extra_guests'], $to_currency));

        $total_price_per_night_with_discount    = (($per_night_price_with_discount_service_fee * $min_reqiured_units) + ($extra_guest_price_with_discount_and_service_fee * $total_all_units_extra_guests));
        $total_price_per_night_without_discount = (($per_night_price_without_discount_service_fee * $min_reqiured_units) + ($extra_guest_price_without_discount_and_service_fee * $total_all_units_extra_guests));

        // phpcs:disable
        $total_price_per_night_with_discount_without_service_fee = (($per_night_price_with_discount_without_service_fee_with_markup * $min_reqiured_units) + ($extra_guest_price_with_discount_without_service_fee_with_markup * $total_all_units_extra_guests));

        $total_price_all_nights = ($total_price_per_night_with_discount * $no_of_days);

        $total_price_all_nights_without_markup = ($total_price_all_nights - $total_nights_all_units_gh_markup_on_price_plus_extra_guests);

        // All nights all units.
        $total_service_fee = ceil(Helper::convertPriceToCurrentCurrency($from_currency, (($pricing['total_service_fee_on_price_with_discount'] * $min_reqiured_units) + ($pricing['total_service_fee_on_extra_guest_price_with_discount'] * $total_all_units_extra_guests)), $to_currency) + $service_fee_on_cleaning_price_all_unit);

        // Comment total_price_all_nights_without_markup as total service fee is without markup.
        $service_fee_percentage = round((($total_service_fee * 100) / $total_price_all_nights_without_markup), 1);

        $total_price_all_nights_without_service_fee = ($total_price_per_night_with_discount_without_service_fee * $no_of_days);

        $coa_fee_data            = Helper::coaChargesForGuesthouser($total_price_all_nights, $to_currency);
        $coa_fee                 = $coa_fee_data['coa_fee'];
        $coa_fee_percentage_slab = $coa_fee_data['coa_fee_percentage_slab'];

        if (empty($user) === false) {
            $early_bird_cashback = self::_earlyBirdCashbackEligiblity($checkin_no_days_from_now, $user);

            if ($early_bird_cashback['is_applicable'] === true) {
                $early_bird_cashback_percentage = $early_bird_cashback['percentage'];
                $early_bird_cashback_text       = $early_bird_cashback['display_text'];
                $early_bird_cashback_applicable = true;
            }
        }

        $total_price_all_nights_with_cleaning_price = ($total_price_all_nights + $cleaning_price_all_unit);

        // Availablity check.
        if ($is_available !== 1 || $available_units < $min_reqiured_units) {
            $error      = 'Property unavailable on selected dates';
            $error_code = 'inventory_not_available';
        }


        $properly_commission = 0;
        if(isset($data['pc_properly_commission']) === true && isset($data['pmt_properly_commission']) === true){
            $properly_commission = ($data['pmt_properly_commission'] !== null) ? $data['pmt_properly_commission'] :
                                    (($data['pc_properly_commission'] !== null) ? $data['pc_properly_commission'] : 0);
        }

        return [
            'currency'                                                           => $data['user_currency'],
            'cleaning_price'                                                     => $cleaning_price_all_unit,
            'cleaning_price_per_unit'                                            => $cleaning_price_per_unit,

            'per_night_per_unit_price'                                           => $per_night_price_with_discount_service_fee,
            'per_night_per_unit_price_without_service_fee'                       => $per_night_price_with_discount_without_service_fee_with_markup,

            'per_night_per_unit_price_without_markup'                            => $per_night_price_with_discount_service_fee_without_markup,
            'per_night_all_units_price'                                          => ($per_night_price_with_discount_service_fee * $min_reqiured_units),

            'per_night_per_guest_extra_guest_price'                              => $extra_guest_price_with_discount_and_service_fee,
            'per_night_per_guest_extra_guest_price_without_service_fee'          => $extra_guest_price_with_discount_without_service_fee_with_markup,

            'per_night_all_guest_extra_guest_price'                              => ($extra_guest_price_with_discount_and_service_fee * $total_all_units_extra_guests),
            'per_night_all_guest_extra_guest_price_without_service_fee'          => ($extra_guest_price_with_discount_without_service_fee_with_markup * $total_all_units_extra_guests),
            'per_night_all_guest_extra_guest_price_without_markup'               => ($extra_guest_price_with_discount_and_service_fee_without_markup * $total_all_units_extra_guests),
            'total_price_per_night'                                              => $total_price_per_night_with_discount,

            'total_price_per_night_without_service_fee'                          => $total_price_per_night_with_discount_without_service_fee,
             // This is without gst cleaning cost and user discount.
            'total_price_all_nights'                                             => $total_price_all_nights,
            'total_price_all_nights_with_cleaning_price'                         => $total_price_all_nights_with_cleaning_price,
            'total_price_all_nights_without_service_fee'                         => $total_price_all_nights_without_service_fee,

            'total_price_per_night_without_discount'                             => $total_price_per_night_without_discount,

            'total_service_fee'                                                  => $total_service_fee,
            'service_fee_on_price_with_discount_per_unit_per_night'              => $service_fee_on_price_with_discount_per_unit_per_night,
             // Multiply this with all extra guests to get all extra guests service fee.
            'service_fee_on_extra_guest_price_with_discount_per_guest_per_night' => $service_fee_on_extra_guest_price_with_discount_per_guest_per_night,
            'total_markup_fee'                                                   => round($total_nights_all_units_gh_markup_on_price_plus_extra_guests, 2),
            'service_fee_percentage'                                             => $service_fee_percentage,
            'effective_discount_percentage'                                      => $pricing['effective_discount_percentage'],
            'coa_fee'                                                            => $coa_fee,
            'coa_percentage'                                                     => $coa_fee_percentage_slab,
            'coa_fee_percentage_slab'                                            => $coa_fee_percentage_slab,
            'gh_commission_percent'                                              => $pricing['gh_commission_percentage'],
            'gh_markup_percentage'                                               => $pricing['gh_markup_percentage'],
            'total_host_fee'                                                     => $pricing['total_host_fee'],
            'discount_percentage_per_date'                                       => $pricing['discount_percentage_per_date'],
            'max_host_discount'                                                  => $pricing['max_host_discount'],
            'max_smart_discount'                                                 => $pricing['max_smart_discount'],

            'early_bird_cashback_percentage'                                     => $early_bird_cashback_percentage,
            'early_bird_cashback_text'                                           => $early_bird_cashback_text,
            'early_bird_cashback_applicable'                                     => $early_bird_cashback_applicable,

            'start_date'                                                         => $start_date_obj->toDateString(),
            'end_date'                                                           => $end_date_obj->toDateString(),
            'no_of_nights'                                                       => $no_of_days,
            'selected_guests'                                                    => $selected_guests,
            'per_unit_guests'                                                    => $property_guest_count_per_unit,
            'selected_units'                                                     => $selected_units,
            'required_units'                                                     => $min_reqiured_units,
            'required_bedrooms'                                                  => ($min_reqiured_units * $property_bedroom),
            'total_extra_guests'                                                 => $total_all_units_extra_guests,

            'available_units'                                                    => $pricing['available_units'],
            'is_available'                                                       => $pricing['is_available'],
            'is_instant_bookable'                                                => (int) $pricing['is_instant_bookable'],
            'guests_per_unit'                                                    => $allowed_guest_per_unit['total_guest_count'],
            'per_day_price_array'                                                => $pricing['per_day_price_array'],
            'properly_commission'                                                => $properly_commission,
            'error'                                                              => $error,
            'error_code'                                                         => $error_code,

        ];

    }//end getPropertyPrice()


    /**
     * Get single property prices.
     *
     * @param array $base_pricing Property pricing data.
     *
     * @return array single unit prices.
     */
    public static function calculatePerUnitPrice(array $base_pricing)
    {
        $total_price_with_discount_and_service_fee                = 0;
        $total_price_without_discount_and_service_fee             = 0;
        $total_service_fee_on_price_without_discount              = 0;
        $total_extra_guest_price_with_discount_and_service_fee    = 0;
        $total_extra_guest_price_without_discount_and_service_fee = 0;
        $total_discount_on_price                  = 0;
        $total_discount_on_extra_guest_price      = 0;
        $total_service_fee_on_price_with_discount = 0;
        $total_service_fee_on_extra_guest_price_with_discount    = 0;
        $total_service_fee_on_extra_guest_price_without_discount = 0;

        $pid                 = $base_pricing['property_id'];
        $start_date_obj      = $base_pricing['start_date_obj'];
        $end_date_obj        = $base_pricing['end_date_obj'];
        $no_of_days          = $base_pricing['no_of_days'];
        $property_min_nights = $base_pricing['min_nights'];
        $property_max_nights = $base_pricing['max_nights'];
        $property_additional_guest_count      = $base_pricing['additional_guest_count'];
        $property_accomodation                = $base_pricing['accomodation'];
        $property_base_per_night_price        = $base_pricing['per_night_price'];
        $property_base_service_fee_percantage = $base_pricing['service_fee'];
        $property_base_additional_guest_fee   = $base_pricing['additional_guest_fee'];
        $property_instant_bookable            = $base_pricing['instant_book'];
        $property_units               = $base_pricing['property_units'];
        $property_base_gh_commission  = $base_pricing['gh_commission'];
        $property_base_gh_markup_fee  = $base_pricing['markup_service_fee'];
        $from_currency                = $base_pricing['property_currency'];
        $to_currency                  = $base_pricing['user_currency'];
        $cleaning_mode                = $base_pricing['cleaning_mode'];
        $cleaning_fee                 = $base_pricing['cleaning_fee'];
        $min_reqiured_units           = $base_pricing['min_reqiured_units'];
        $total_all_units_extra_guests = $base_pricing['total_all_units_extra_guests'];

        // Setting default 2 to get property value.
        // Why not setting it to proerty instant value?.
        // Answer Case . property default 0, and all dates have 1 then result.
        // Would have been 0 which is wrong so setting it 2 make it right.
        $is_instant_bookable_on_all_days = 2;
        $is_available_on_all_days        = 1;
        $min_units_available_on_all_days = $property_units;
        $no_of_days_with_base_price      = 0;
        $error = '';

        // Get inventory pricing for property (from start date to end date)=.
        $inventory_pricing = InventoryPricing::getPropertyInventoryDetails($pid, $start_date_obj->format('Y-m-d'), $end_date_obj->format('Y-m-d'));

        $default_gh_markup_percentage = ($property_base_gh_markup_fee - $property_base_gh_commission > 0) ? ($property_base_gh_markup_fee - $property_base_gh_commission) : 0;

        // Original property per night price with discount = 0 and service fee.
        $original_price_with_discount_and_service_fee = ((($property_base_per_night_price * 100) / (100 - $property_base_service_fee_percantage)) + (($property_base_per_night_price * $default_gh_markup_percentage) / 100));

        $original_price_with_discount_and_without_service_fee = ($property_base_per_night_price + (($property_base_per_night_price * $default_gh_markup_percentage) / 100));

        // Cleaning.
        $cleaning_cost_per_unit_without_service_fee = ((int) $cleaning_mode === -1) ? ($cleaning_fee * $no_of_days) : $cleaning_fee;
        $service_fee_on_cleaning_cost               = (($property_base_service_fee_percantage * $cleaning_cost_per_unit_without_service_fee) / 100);
        $total_cleaning_cost_per_unit               = ((($cleaning_cost_per_unit_without_service_fee * 100) / (100 - $property_base_service_fee_percantage)) + (($cleaning_cost_per_unit_without_service_fee * $default_gh_markup_percentage) / 100));

        $cleaning_cost_per_unit_per_day = ($total_cleaning_cost_per_unit / $no_of_days);
        $cleaning_cost_per_unit_per_day_without_service_fee = ($cleaning_cost_per_unit_without_service_fee / $no_of_days);

        $discount_percentage_per_date = [];
        $max_host_discount            = 0;
        $max_smart_discount           = 0;
        $total_price_with_discount_without_service_with_cleaning = 0;
        $total_gh_commission_amount = 0;
        $total_host_fee             = 0;
        $per_day_price_array        = [];
        $total_gh_markup_on_price_with_discount             = 0;
        $total_gh_markup_on_extra_guest_price_with_discount = 0;

        $default_pricing_and_availability = [
            'currency'        => Helper::getCurrencySymbol($to_currency),
            'price'           => number_format(ceil(Helper::convertPriceToCurrentCurrency($from_currency, $original_price_with_discount_and_without_service_fee, $to_currency))),
            'is_available'    => 1,
            'available_units' => $property_units,
        ];

        $per_day_price_array['default']   = $default_pricing_and_availability;
        $per_day_price_array['exception'] = [];

        for ($i = 0; $i < $no_of_days; $i++) {
            $date_obj = $start_date_obj->copy()->addDays($i);
            $date     = $date_obj->toDateString();

            $is_instant_bookable = (isset($inventory_pricing[$date]) === true) ? $inventory_pricing[$date]['instant_booking'] : $property_instant_bookable;

            $available_units = (isset($inventory_pricing[$date]) === true) ? $inventory_pricing[$date]['available_units'] : $property_units;

            // phpcs:ignore
            $is_available = (isset($inventory_pricing[$date]) === true) ? ( $inventory_pricing[$date]['is_available'] && $inventory_pricing[$date]['available_units'] > 0 ? 1 : 0 ) : 1;

            // Calculate all prices with a check that if they exist for given date in inventory, take inventory value otherwise take default property value.
            // phpcs:ignore
            $price = (int) (isset($inventory_pricing[$date]) === true ? ($inventory_pricing[$date]['price'] > 0) ? $inventory_pricing[$date]['price'] : $property_base_per_night_price : $property_base_per_night_price);

            // phpcs:ignore
            $extra_guest_price = (int) (isset($inventory_pricing[$date]) === true ? ($inventory_pricing[$date]['extra_guest_cost'] > 0) ? $inventory_pricing[$date]['extra_guest_cost'] : $property_base_additional_guest_fee : $property_base_additional_guest_fee);

            $discount_percentage = (isset($inventory_pricing[$date]) === true) ? $inventory_pricing[$date]['discount'] : 0;
            // phpcs:ignore
            $smart_discount_percentage = (isset($inventory_pricing[$date]) === true && empty($inventory_pricing[$date]['smart_discount']) === false) ? $inventory_pricing[$date]['smart_discount'] : 0;

            $final_discount_percentage = ($discount_percentage > $smart_discount_percentage) ? $discount_percentage : $smart_discount_percentage;
            // phpcs:ignore
            $service_fee_percentage = (float) ((isset($inventory_pricing[$date]) === true ) ? ($inventory_pricing[$date]['service_fee'] > 0 ? $inventory_pricing[$date]['service_fee'] : $property_base_service_fee_percantage) : $property_base_service_fee_percantage);
             // phpcs:ignore
            $gh_commission_percentage = (float) ((isset($inventory_pricing[$date]) === true ) ? ($inventory_pricing[$date]['gh_commission'] >= 0 && is_null($inventory_pricing[$date]['gh_commission']) === false ? $inventory_pricing[$date]['gh_commission'] : $property_base_gh_commission) : $property_base_gh_commission);
             // phpcs:ignore
            $gh_markup_percentage = ((isset($inventory_pricing[$date]) === true ) ? $inventory_pricing[$date]['markup_service_fee'] : $property_base_gh_markup_fee);

            $discount_on_price             = ($price * $final_discount_percentage / 100);
            $discount_on_extra_guest_price = ($extra_guest_price * $final_discount_percentage / 100);

            $price_with_discount             = ($price - $discount_on_price);
            $extra_guest_price_with_discount = ($extra_guest_price - $discount_on_extra_guest_price);

            // $smart_discount_on_discounted_price             = ($price_with_discount * $smart_discount_percentage / 100);
            // $smart_discount_on_discounted_extra_guest_price = ($extra_guest_price_with_discount * $smart_discount_percentage / 100);
            // $price_with_discount             = ($price_with_discount - $smart_discount_on_discounted_price);
            // $extra_guest_price_with_discount = ($extra_guest_price_with_discount - $smart_discount_on_discounted_extra_guest_price);
            $service_fee_on_price_with_discount    = ((($price_with_discount * 100) / ( 100 - $service_fee_percentage)) - $price_with_discount);
            $service_fee_on_price_without_discount = ($price * $service_fee_percentage / ( 100 - $service_fee_percentage));

            $service_fee_on_extra_guest_price_with_discount    = ((($extra_guest_price_with_discount * 100) / ( 100 - $service_fee_percentage)) - $extra_guest_price_with_discount);
            $service_fee_on_extra_guest_price_without_discount = ((($extra_guest_price * 100) / ( 100 - $service_fee_percentage)) - $extra_guest_price);

            $gh_markup_on_price_with_discount = (($price_with_discount * $gh_markup_percentage) / 100);
            $gh_markup_on_price               = (($price * $gh_markup_percentage) / 100);

            $gh_markup_on_extra_guest_price_with_discount = (($extra_guest_price_with_discount * $gh_markup_percentage) / 100);
            $gh_markup_on_extra_guest_price               = (($extra_guest_price * $gh_markup_percentage) / 100);

            $price_with_discount_and_service_fee         = ($price_with_discount + $service_fee_on_price_with_discount + $gh_markup_on_price_with_discount );
            $price_with_discount_and_without_service_fee = ($price_with_discount + $gh_markup_on_price_with_discount );

            $price_without_discount_and_service_fee = ($price + $service_fee_on_price_without_discount + $gh_markup_on_price);

            $extra_guest_price_with_discount_and_service_fee    = ($extra_guest_price_with_discount + $service_fee_on_extra_guest_price_with_discount + $gh_markup_on_extra_guest_price_with_discount);
            $extra_guest_price_without_discount_and_service_fee = ($extra_guest_price + $service_fee_on_extra_guest_price_without_discount + $gh_markup_on_extra_guest_price);

            // Write better version: FIXED CLEANIG ISSUE.
            // $price_with_discount_without_service_with_cleaning = (($price_with_discount_and_service_fee -$service_fee_on_price_with_discount) * $base_pricing['min_reqiured_units'] ).
            // + (($extra_guest_price_with_discount_and_service_fee - $service_fee_on_extra_guest_price_with_discount) * $base_pricing['total_all_units_extra_guests'] ).
            // + $cleaning_cost_per_unit_per_day * $base_pricing['min_reqiured_units'] ;.
            // $gh_commission_amount = $price_with_discount_without_service_with_cleaning * $gh_commission_percentage;.
            // $host_fee_amount = $price_with_discount_without_service_with_cleaning - $gh_commission_amount;.
            $price_with_discount_without_service_with_cleaning = (($price_with_discount + $cleaning_cost_per_unit_per_day_without_service_fee) * $min_reqiured_units + ($extra_guest_price_with_discount * $total_all_units_extra_guests));

            $gh_commission_amount = (($price_with_discount_without_service_with_cleaning * $gh_commission_percentage) / 100);
            $host_fee_amount      = ($price_with_discount_without_service_with_cleaning);

            // Add up each day value to get overall value.
            $total_price_with_discount_and_service_fee    += $price_with_discount_and_service_fee;
            $total_price_without_discount_and_service_fee += $price_without_discount_and_service_fee;

            $total_extra_guest_price_with_discount_and_service_fee    += $extra_guest_price_with_discount_and_service_fee;
            $total_extra_guest_price_without_discount_and_service_fee += $extra_guest_price_without_discount_and_service_fee;

            $total_discount_on_price             += $discount_on_price;
            $total_discount_on_extra_guest_price += $discount_on_extra_guest_price;

            $total_service_fee_on_price_with_discount    += $service_fee_on_price_with_discount;
            $total_service_fee_on_price_without_discount += $service_fee_on_price_without_discount;

            $total_service_fee_on_extra_guest_price_with_discount    += $service_fee_on_extra_guest_price_with_discount;
            $total_service_fee_on_extra_guest_price_without_discount += $service_fee_on_extra_guest_price_without_discount;

            $total_price_with_discount_without_service_with_cleaning += $price_with_discount_without_service_with_cleaning;
            $total_gh_commission_amount += $gh_commission_amount;
            $total_host_fee             += $host_fee_amount;

            $total_gh_markup_on_price_with_discount             += $gh_markup_on_price_with_discount;
            $total_gh_markup_on_extra_guest_price_with_discount += $gh_markup_on_extra_guest_price_with_discount;

            $is_instant_bookable_on_all_days = min($is_instant_bookable_on_all_days, $is_instant_bookable);
            $min_units_available_on_all_days = min($min_units_available_on_all_days, $available_units);
            $is_available_on_all_days        = min($is_available_on_all_days, $is_available);

            // Flag to check if price in inventory is same as price defined in property table.
            if (($original_price_with_discount_and_service_fee === $price_with_discount_and_without_service_fee) && $is_available !== 0) {
                $no_of_days_with_base_price++;
            } else {
                $pricing_and_availablity                 = [];
                $pricing_and_availablity                 = [
                    'price'           => number_format(ceil(Helper::convertPriceToCurrentCurrency($from_currency, $price_with_discount_and_without_service_fee, $to_currency))),
                    'is_available'    =>  $is_available,
                    'available_units' => (int) $available_units,
                ];
                $per_day_price_array['exception'][$date] = $pricing_and_availablity;
            }

            $discount_date_formatted = $date_obj->format('d-m-Y');
            if ($discount_percentage > 0 || $smart_discount_percentage > 0) {
                $discount_percentage_per_date[$discount_date_formatted] = [
                    'smart_discount' => $smart_discount_percentage,
                    'host_discount'  => $discount_percentage,
                ];
            }

            $max_host_discount  = max($max_host_discount, $discount_percentage);
            $max_smart_discount = max($max_smart_discount, $smart_discount_percentage);
        }//end for

        $per_night_price_with_discount_service_fee    = ($total_price_with_discount_and_service_fee / $no_of_days);
        $per_night_price_without_discount_service_fee = ($total_price_without_discount_and_service_fee / $no_of_days);

        $per_night_extra_guest_price_with_discount_and_service_fee    = ($total_extra_guest_price_with_discount_and_service_fee / $no_of_days);
        $per_night_extra_guest_price_without_discount_and_service_fee = ($total_extra_guest_price_without_discount_and_service_fee / $no_of_days);

        $gh_commission_percentage = round((($total_gh_commission_amount / $total_price_with_discount_without_service_with_cleaning) * 100), 2);

        $per_night_service_fee = ($total_service_fee_on_price_with_discount / $no_of_days);
        $per_night_service_fee_on_extra_guest_price = ($total_service_fee_on_extra_guest_price_with_discount / $no_of_days);

        $total_nights_all_units_gh_markup_on_price_plus_extra_guests = (($total_gh_markup_on_price_with_discount * $min_reqiured_units ) + ($total_gh_markup_on_extra_guest_price_with_discount * $total_all_units_extra_guests));

        $per_night_per_unit_gh_markup_on_price_with_discount             = ($total_gh_markup_on_price_with_discount / $no_of_days);
        $per_night_per_unit_gh_markup_on_extra_guest_price_with_discount = ($total_gh_markup_on_extra_guest_price_with_discount / $no_of_days);

        $per_night_price_with_discount_service_fee_without_markup = ($per_night_price_with_discount_service_fee - $per_night_per_unit_gh_markup_on_price_with_discount);

        // Without service fee per night price but with markup.
        $per_night_price_with_discount_without_service_fee_with_markup = ($per_night_price_with_discount_service_fee - $per_night_service_fee);

        $extra_guest_price_with_discount_and_service_fee_without_markup = ($per_night_extra_guest_price_with_discount_and_service_fee - $per_night_per_unit_gh_markup_on_extra_guest_price_with_discount);

        $extra_guest_price_with_discount_without_service_fee_with_markup = ($per_night_extra_guest_price_with_discount_and_service_fee - $per_night_service_fee_on_extra_guest_price);

        $avg_gh_markup_percentage = (($per_night_per_unit_gh_markup_on_price_with_discount / ($per_night_price_with_discount_service_fee - $per_night_service_fee - $per_night_per_unit_gh_markup_on_price_with_discount)) * 100);

        $total_discount_on_price_per_day             = ($total_discount_on_price / $no_of_days);
        $total_discount_on_extra_guest_price_per_day = ($total_discount_on_extra_guest_price / $no_of_days);

        $total_discount_all_nights_all_units_on_price_plus_extra_guest_all = (($total_discount_on_price * $min_reqiured_units) + ($total_discount_on_extra_guest_price * $total_all_units_extra_guests));

        $effective_discount_percentage = round((($total_price_without_discount_and_service_fee - $total_price_with_discount_and_service_fee) * 100 / $total_price_without_discount_and_service_fee), 0);

        /*
            Per_week_price and per_month_price are not considered here (live on web and older apis) because we have to change the logic from week_price and month price to week and month discounts
        */

         // Return pricing array.
        return [
            'is_available'                                                      => $is_available_on_all_days,
            'available_units'                                                   => $min_units_available_on_all_days,
            'is_instant_bookable'                                               => $is_instant_bookable_on_all_days,
            'cleaning_price_per_unit'                                           => $total_cleaning_cost_per_unit,
            'service_fee_on_cleaning_price'                                     => $service_fee_on_cleaning_cost,

            'per_night_price_with_discount_service_fee'                         => $per_night_price_with_discount_service_fee,
            'per_night_price_without_discount_service_fee'                      => $per_night_price_without_discount_service_fee,
            'per_night_price_with_discount_service_fee_without_markup'          => $per_night_price_with_discount_service_fee_without_markup,
            'per_night_price_with_discount_without_service_fee_with_markup'     => $per_night_price_with_discount_without_service_fee_with_markup,

            'extra_guest_price_with_discount_and_service_fee'                   => $per_night_extra_guest_price_with_discount_and_service_fee,
            'extra_guest_price_without_discount_and_service_fee'                => $per_night_extra_guest_price_without_discount_and_service_fee,
            'extra_guest_price_with_discount_and_service_fee_without_markup'    => $extra_guest_price_with_discount_and_service_fee_without_markup,
            'extra_guest_price_with_discount_without_service_fee_with_markup'   => $extra_guest_price_with_discount_without_service_fee_with_markup,

            'per_night_per_unit_gh_markup_on_price_with_discount'               => $per_night_per_unit_gh_markup_on_price_with_discount,
            'per_night_per_unit_gh_markup_on_extra_guest_price_with_discount'   => $per_night_per_unit_gh_markup_on_extra_guest_price_with_discount,
            'gh_markup_percentage'                                              => round($avg_gh_markup_percentage, 2),
            'total_nights_all_units_gh_markup_on_price_plus_extra_guests'       => $total_nights_all_units_gh_markup_on_price_plus_extra_guests,

            'total_service_fee_on_price_with_discount'                          => $total_service_fee_on_price_with_discount,
            'total_service_fee_on_extra_guest_price_with_discount'              => $total_service_fee_on_extra_guest_price_with_discount,
            'per_night_service_fee'                                             => $per_night_service_fee,
            'per_night_service_fee_on_extra_guest_price'                        => $per_night_service_fee_on_extra_guest_price,
            'total_discount_on_price_per_day'                                   => $total_discount_on_price_per_day,
            'total_discount_on_extra_guest_price_per_day'                       => $total_discount_on_extra_guest_price_per_day,
            'total_discount_all_nights_all_units_on_price_plus_extra_guest_all' => $total_discount_all_nights_all_units_on_price_plus_extra_guest_all,
            'gh_commission_percentage'                                          => $gh_commission_percentage,
            'total_host_fee'                                                    => $total_host_fee,
            'discount_percentage_per_date'                                      => $discount_percentage_per_date,
            'effective_discount_percentage'                                     => $effective_discount_percentage,
            'max_host_discount'                                                 => $max_host_discount,
            'max_smart_discount'                                                => $max_smart_discount,
            'per_day_price_array'                                               => $per_day_price_array,
            'error'                                                             => $error,
        ];

    }//end calculatePerUnitPrice()


    /**
     * Get Early bird cashback eligibility.
     *
     * @param integer $days Property pricing data.
     * @param User    $user User object.
     *
     * @return array Early bird cashback data.
     */
    private static function _earlyBirdCashbackEligiblity(int $days, User $user)
    {
        $is_applicable = false;
        $percentage    = 0;
        $text          = '';
        $response      = ['is_applicable' => $is_applicable];

        if (empty($user) === false) {
            $user_bookings = Booking::where('traveller_id', $user->id)->count();
            if ($user_bookings > 0) {
                return $response;
            }
        }

        if ($days >= 90) {
            $is_applicable = true;
            $percentage    = EARLYBIRD_CB_90_DAYS;
        } else if ($days >= 45) {
            $is_applicable = true;
            $percentage    = EARLYBIRD_CB_45_DAYS;
        }

        $response['is_applicable'] = $is_applicable;
        $response['percentage']    = $percentage;
        $response['display_text']  = 'You are eligible for an early bird cashback of '.$percentage.'% after checkout.';

        return $response;

    }//end _earlyBirdCashbackEligiblity()


     /**
      * Get if required no of units available bw dates
      *
      * @param integer $pid            Property Property id.
      * @param string  $checkin        Checkin date.
      * @param string  $checkout       Checkout date.
      * @param integer $required_units Required units.
      *
      * @return boolean if required units available or not.
      */
    public static function isRequiredUnitsAvailableInProperty(int $pid, string $checkin, string $checkout, int $required_units)
    {
        // Get inventory pricing for property (from start date to end date)=.
        $inventory_pricing = InventoryPricing::getPropertyInventoryDetails($pid, $checkin, $checkout);
        $property          = Property::find($pid);
        $property_units    = $property->units;

        $start_date_obj = Carbon::parse($checkin);
        $end_date_obj   = Carbon::parse($checkout);

        $no_of_days = $start_date_obj->diffInDays($end_date_obj, false);

        $is_available_on_all_days        = 1;
        $min_units_available_on_all_days = $property_units;
        for ($i = 0; $i < $no_of_days; $i++) {
            $date_obj = $start_date_obj->copy()->addDays($i);
            $date     = $date_obj->toDateString();

            $available_units = (isset($inventory_pricing[$date]) === true) ? $inventory_pricing[$date]['available_units'] : $property_units;

            // phpcs:ignore
            $is_available = (isset($inventory_pricing[$date]) === true) ? ( $inventory_pricing[$date]['is_available'] && $inventory_pricing[$date]['available_units'] > 0 ? 1 : 0 ) : 1;

            $min_units_available_on_all_days = min($min_units_available_on_all_days, $available_units);
            $is_available_on_all_days        = min($is_available_on_all_days, $is_available);
        }

        if ($is_available_on_all_days > 0 && $min_units_available_on_all_days >= $required_units) {
            return true;
        }

        return false;

    }//end isRequiredUnitsAvailableInProperty()


    /**
     * Get host properties price calender.
     *
     * @param array $param Array data.
     *
     * @return array
     */
    public static function getPropertyPriceCalenderById(array $param)
    {
        $pid            = $param['property_id'];

        $start_date_obj = Carbon::parse($param['start_date']);
        $end_date_obj   = Carbon::parse($param['end_date']);

        // Add one to Fetch end_date data also.
        $end_date_obj->addDay();

        $property_units = $param['property_units'];


        $exception = [];
        $smart_discount_exception = [];

        // Get Smart Discount.
        $smart_discount_object = new SmartDiscount;
        $smart_discount = $smart_discount_object->getPropertySmartDiscounts($pid, $start_date_obj->format('Y-m-d'));

         // Get inventory pricing for property (from start date to end date)=.
        $inventory_pricing = InventoryPricing::getPropertyInventoryDetails($pid, $start_date_obj->format('Y-m-d'), $end_date_obj->format('Y-m-d'));


        foreach ($inventory_pricing as $one_inventory) {
            // Calculate Open Units and Blocked Units for is_available false.
            $open_units = $one_inventory['available_units'];
            $blocked_units = ($property_units - ($one_inventory['available_units'] + $one_inventory['booked_units']));

            if($one_inventory['is_available'] === 0){
                $open_units = 0;
                $blocked_units = $property_units - $one_inventory['booked_units'];
            }

            $exception[$one_inventory['date']] = [
                'price'             => (float) $one_inventory['price'],
                'extra_guest_cost'  => (float) $one_inventory['extra_guest_cost'],
                'is_available'      => $one_inventory['is_available'],
                'total_units'       => $property_units,
                'available_units'   => $one_inventory['available_units'],
                'booked_units'      => $one_inventory['booked_units'],
                'blocked_units'     => $blocked_units,
                'open_units'        => $open_units,
                'instant_book'      => $one_inventory['instant_booking'],
                'gh_commission'     => (float) $one_inventory['gh_commission'],
                'service_fee'       => (float) $one_inventory['service_fee'],
                'markup_service_fee' => $one_inventory['markup_service_fee'],
                'x_plus_5'          => $one_inventory['x_plus_5'],
                'discount'          => $one_inventory['discount'],
                'discount_type'     => $one_inventory['discount_type'],
                'discount_days'     => $one_inventory['discount_days'],
                'smart_discount'    => $one_inventory['smart_discount'],
            ];
        }


        $start_date_replica = $start_date_obj->copy();

        $smart_discount_exception = $smart_discount_object->getSmartDiscountsOfDate($smart_discount, $start_date_obj->copy(), $end_date_obj->copy());

        $content['inventory_pricing'] = $exception;
        $content['smart_discounts'] = $smart_discount_exception;

        return $content;

    }//end getPropertyPriceCalenderById()

    /**
     * Update host properties price calender.
     *
     * @param array $param Array data.
     *
     * @return boolean
     */
    public static function updatePropertyPriceCalender(array $param)
    {
        $pid            = $param['property_id'];
        $property       = Property::where('id', '=', $pid)->select('service_fee', 'units', 'instant_book')->first();
        $service_fee    = $property->service_fee;
        $property_units = $property->units;
        $property_instant_bookable = $property->instant_book;

        $property_price           = PropertyPricing::where('pid', '=', $pid)->select('per_night_price', 'additional_guest_fee')->first();
        $old_per_night_price      = $property_price->per_night_price;
        $old_additional_guest_fee = $property_price->additional_guest_fee;
        $old_gh_commission            = $property_price->gh_commission;

        $host_id            = $param['user_id'];

        $start_date_obj     = Carbon::parse($param['start_date']);
        $end_date_obj       = Carbon::parse($param['end_date']);

        $markup_service_fee = 0;
        $unix_start_date    = $start_date_obj->timestamp;
        $unix_end_date      = $end_date_obj->timestamp;

        while ($unix_start_date <= $unix_end_date) {
            $date          = Carbon::createFromTimestamp($unix_start_date)->format('Y-m-d');
            $one_inventory = InventoryPricing::where('pid', '=', $pid)->where('date', '=', $date)->first();
            $updated_by    = $host_id;

            $inventory_change_data = [];
            if (empty($one_inventory) === true) {
                $inventory_change_data = [
                    'property_id'       => $pid,
                    'available_units'   => (isset($param['available_units']) === true && $param['available_units'] <= $property_units) ? $param['available_units'] : $property_units,
                    'service_fee'       => $service_fee,
                    'per_night_price'   => (isset($param['per_night_price']) === true) ? $param['per_night_price'] : $old_per_night_price,
                    'extra_guest_cost'  => (isset($param['extra_guest_cost']) === true) ? $param['extra_guest_cost'] : $old_additional_guest_fee,
                    'markup_service_fee' => $markup_service_fee,
                    'date'              => $date,
                    'is_available'      => (isset($param['is_available']) === true) ? $param['is_available'] : 1,
                    'instant_book'      => (isset($param['instant_book']) === true) ? $param['instant_book'] : $property_instant_bookable,
                    'x_plus_5'          => (isset($param['x_plus_5']) === true) ? $param['x_plus_5'] : 1,
                    'gh_commission'     => (isset($param['gh_commission']) === true) ? $param['gh_commission'] : $old_gh_commission,
                    'discount_type'     => (isset($param['discount_type']) === true) ? $param['discount_type'] : 0,
                    'discount_days'     => (isset($param['discount_days']) === true) ? $param['discount_days'] : '',
                    'discount'          => (isset($param['discount']) === true) ? $param['discount'] : 0,
                    'admin_id'          => $param['admin_id']
                ];
            } else {
                if(isset($param['gh_commission']) === true && $one_inventory->gh_commission > $param['gh_commission'] && $param['admin_id'] === 0){
                    $param['gh_commission'] = $one_inventory->gh_commission;
                }

                $inventory_change_data = [
                    'property_id'       => $pid,
                    'available_units'   => (isset($param['available_units']) === true && ($param['available_units'] + $one_inventory->booked_units) <= $property_units) ? $param['available_units'] : $one_inventory->available_units,
                    'service_fee'       => (empty($one_inventory->service_fee) === false) ? $one_inventory->service_fee : $service_fee,
                    'per_night_price'   => (isset($param['per_night_price']) === true) ? $param['per_night_price'] : $one_inventory->price,
                    'extra_guest_cost'  => (isset($param['extra_guest_cost']) === true) ? $param['extra_guest_cost'] : $one_inventory->extra_guest_cost,
                    'markup_service_fee' => (empty($one_inventory->markup_service_fee) === false ) ? $one_inventory->markup_service_fee : $markup_service_fee,
                    'date' => $date,
                    'is_available'      => (isset($param['is_available']) === true) ? $param['is_available'] : $one_inventory->is_available,
                    'instant_book'      => (isset($param['instant_book']) === true) ? $param['instant_book'] : $one_inventory->instant_booking,
                    'x_plus_5'          => (isset($param['x_plus_5']) === true) ? $param['x_plus_5'] : $one_inventory->x_plus_5,
                    'gh_commission'     => (isset($param['gh_commission']) === true) ? $param['gh_commission'] : $one_inventory->gh_commission,
                    'discount_type'     => (isset($param['discount_type']) === true) ? $param['discount_type'] : $one_inventory->discount_type,
                    'discount_days'     => (isset($param['discount_days']) === true) ? $param['discount_days'] : $one_inventory->discount_days,
                    'discount'          => (isset($param['discount']) === true) ? $param['discount'] :$one_inventory->discount,
                    'admin_id'          => $param['admin_id']
                ];
            }//end if

            InventoryPricing::changePropertyInventory($inventory_change_data);

            $unix_start_date = ($unix_start_date + 60 * 60 * 24);
        }//end while

        return true;

    }//end updatePropertyPriceCalender()

    /**
     * Save Smart Discount.
     *
     * @param array $param Array data.
     *
     * @return array
     */
    public static function saveSmartDiscount(array $param)
    {
        $pid            = $param['property_id'];
        $start_date_obj = Carbon::parse($param['start_date']);
        $end_date_obj   = Carbon::parse($param['end_date']);
        $status         = $param['status'];

        // Make Object of Model For Access Non Static Methods
        $smart_discount_object = new SmartDiscount;

        // Get All Smart Discounts.
        $smart_discount = $smart_discount_object->getCommonSmartDiscounts($pid, $start_date_obj->format('Y-m-d'), $end_date_obj->format('Y-m-d'));

        // Data Change Status.
        $change_status = false;

        foreach ($param['discounts'] as $key => $discount_data) {

            // For No Smart Discount yet save as it is data.
            if(empty($smart_discount) === true){

                $smart_discount_object->changeSmartDiscounts([
                    'property_id' => $pid,
                    'start_date'  => $param['start_date'],
                    'end_date'    => $param['end_date'],
                    'discount'    => $discount_data['value'],
                    'discount_days' => $discount_data['days'],
                    'status'        => $status
                ]);
                $change_status = true;
            }
            else{
                if(empty($discount_data['id']) === false && isset($smart_discount[$discount_data['id']]) === true){

                    // Case 1 : When Start date and End Date between Existing Dates.
                    if($smart_discount[$discount_data['id']]['start_date'] < $param['start_date'] && $smart_discount[$discount_data['id']]['end_date'] > $param['end_date']){
                        // Change When data Changed.
                        if($smart_discount[$discount_data['id']]['discount'] !== $discount_data['value'] || $smart_discount[$discount_data['id']]['discount_days'] !== $discount_data['days'] || $smart_discount[$discount_data['id']]['status'] !== $status){

                            // Make Outer Date Range as Seperate Discount.
                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => $smart_discount[$discount_data['id']]['start_date'],
                                'end_date'    => Carbon::parse($param['start_date'])->subDay()->format('Y-m-d'),
                                'discount'    => $smart_discount[$discount_data['id']]['discount'],
                                'discount_days' => $smart_discount[$discount_data['id']]['discount_days'],
                                'status'        => $smart_discount[$discount_data['id']]['status'],
                                'id'            => $discount_data['id']
                            ]);

                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => $param['start_date'],
                                'end_date'    => $param['end_date'],
                                'discount'    => $discount_data['value'],
                                'discount_days' => $discount_data['days'],
                                'status'        => $status
                            ]);

                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => Carbon::parse($param['end_date'])->addDay()->format('Y-m-d'),
                                'end_date'    => $smart_discount[$discount_data['id']]['end_date'],
                                'discount'    => $smart_discount[$discount_data['id']]['discount'],
                                'discount_days' => $smart_discount[$discount_data['id']]['discount_days'],
                                'status'        => $smart_discount[$discount_data['id']]['status'],
                                'id'            => $discount_data['id']
                            ]);
                            $change_status = true;
                        }
                        // Else No Changes
                    }
                    elseif ($smart_discount[$discount_data['id']]['start_date'] < $param['start_date'] && $smart_discount[$discount_data['id']]['end_date'] < $param['end_date']) {
                        // Case 2 : When Only Start date lies between Existing Dates
                        if($smart_discount[$discount_data['id']]['discount'] !== $discount_data['value'] || $smart_discount[$discount_data['id']]['discount_days'] !== $discount_data['days'] || $smart_discount[$discount_data['id']]['status'] !== $status){

                            // Make Outer Date Range as Seperate Discount.
                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => $smart_discount[$discount_data['id']]['start_date'],
                                'end_date'    => Carbon::parse($param['start_date'])->subDay()->format('Y-m-d'),
                                'discount'    => $smart_discount[$discount_data['id']]['discount'],
                                'discount_days' => $smart_discount[$discount_data['id']]['discount_days'],
                                'status'        => $smart_discount[$discount_data['id']]['status'],
                                'id'            => $discount_data['id']
                            ]);

                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => $param['start_date'],
                                'end_date'    => $param['end_date'],
                                'discount'    => $discount_data['value'],
                                'discount_days' => $discount_data['days'],
                                'status'        => $status
                            ]);
                            $change_status = true;
                        }
                        else{
                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => $smart_discount[$discount_data['id']]['start_date'],
                                'end_date'    => $param['end_date'],
                                'discount'    => $smart_discount[$discount_data['id']]['discount'],
                                'discount_days' => $smart_discount[$discount_data['id']]['discount_days'],
                                'status'        => $smart_discount[$discount_data['id']]['status'],
                                'id'            => $discount_data['id']
                            ]);
                            $change_status = true;
                        }
                    }
                    elseif ($smart_discount[$discount_data['id']]['start_date'] > $param['start_date'] && $smart_discount[$discount_data['id']]['end_date'] > $param['end_date']) {
                        // Case 3 :
                        if($smart_discount[$discount_data['id']]['discount'] !== $discount_data['value'] || $smart_discount[$discount_data['id']]['discount_days'] !== $discount_data['days'] || $smart_discount[$discount_data['id']]['status'] !== $status){

                            // Make Outer Date Range as Seperate Discount.
                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => $param['start_date'],
                                'end_date'    => $param['end_date'],
                                'discount'    => $discount_data['value'],
                                'discount_days' => $discount_data['days'],
                                'status'        => $status
                            ]);

                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => Carbon::parse($param['end_date'])->addDay()->format('Y-m-d'),
                                'end_date'    => $smart_discount[$discount_data['id']]['end_date'],
                                'discount'    => $smart_discount[$discount_data['id']]['discount'],
                                'discount_days' => $smart_discount[$discount_data['id']]['discount_days'],
                                'status'        => $smart_discount[$discount_data['id']]['status'],
                                'id'            => $discount_data['id']
                            ]);
                            $change_status = true;
                        }
                        else{

                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => $param['start_date'],
                                'end_date'    => $smart_discount[$discount_data['id']]['end_date'],
                                'discount'    => $smart_discount[$discount_data['id']]['discount'],
                                'discount_days' => $smart_discount[$discount_data['id']]['discount_days'],
                                'status'        => $smart_discount[$discount_data['id']]['status'],
                                'id'            => $discount_data['id']
                            ]);
                            $change_status = true;
                        }
                    }
                    elseif ($smart_discount[$discount_data['id']]['start_date'] > $param['start_date'] && $smart_discount[$discount_data['id']]['end_date'] < $param['end_date']) {
                        // Case 4 :
                         if($smart_discount[$discount_data['id']]['discount'] !== $discount_data['value'] || $smart_discount[$discount_data['id']]['discount_days'] !== $discount_data['days'] || $smart_discount[$discount_data['id']]['status'] !== $status){

                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => $param['start_date'],
                                'end_date'    => $param['end_date'],
                                'discount'    => $discount_data['value'],
                                'discount_days' => $discount_data['days'],
                                'status'        => $status,
                                'id'            => $discount_data['id']
                            ]);
                            $change_status = true;
                        }
                        else{

                            $smart_discount_object->changeSmartDiscounts([
                                'property_id' => $pid,
                                'start_date'  => $param['start_date'],
                                'end_date'    => $param['end_date'],
                                'discount'    => $smart_discount[$discount_data['id']]['discount'],
                                'discount_days' => $smart_discount[$discount_data['id']]['discount_days'],
                                'status'        => $smart_discount[$discount_data['id']]['status'],
                                'id'            => $discount_data['id']
                            ]);
                            $change_status = true;
                        }
                    }
                    elseif ($smart_discount[$discount_data['id']]['start_date'] === $param['start_date'] && $smart_discount[$discount_data['id']]['end_date'] === $param['end_date']) {

                        $smart_discount_object->changeSmartDiscounts([
                            'property_id' => $pid,
                            'start_date'  => $param['start_date'],
                            'end_date'    => $param['end_date'],
                            'discount'    => $discount_data['value'],
                            'discount_days' => $discount_data['days'],
                            'status'        => $status,
                            'id'            => $discount_data['id']
                        ]);
                        $change_status = true;
                    }
                }
                else{
                    $duplicate_smart_discount = [];

                    foreach ($smart_discount as $value) {
                        if($value['discount'] === $discount_data['value'] && $value['discount_days'] === $discount_data['days']){
                            $duplicate_smart_discount = $value;
                            break;
                        }
                    }

                    if(empty($duplicate_smart_discount) === false){
                        $min_start_date = $start_date_obj->min(Carbon::parse($duplicate_smart_discount['start_date']))->format('Y-m-d');
                        $max_end_date = $end_date_obj->max(Carbon::parse($duplicate_smart_discount['end_date']))->format('Y-m-d');

                        $smart_discount_object->changeSmartDiscounts([
                            'property_id' => $pid,
                            'start_date'  => $min_start_date,
                            'end_date'    => $max_end_date,
                            'discount'    => $discount_data['value'],
                            'discount_days' => $discount_data['days'],
                            'status'        => $status,
                            'id'            => $duplicate_smart_discount['id']
                        ]);
                        $change_status = true;
                    }
                    else{

                        $smart_discount_object->changeSmartDiscounts([
                            'property_id' => $pid,
                            'start_date'  => $param['start_date'],
                            'end_date'    => $param['end_date'],
                            'discount'    => $discount_data['value'],
                            'discount_days' => $discount_data['days'],
                            'status'        => $status,
                        ]);
                        $change_status = true;
                    }
                }
            }
        }
        return $change_status;
    }//end saveSmartDiscount()




}//end class
