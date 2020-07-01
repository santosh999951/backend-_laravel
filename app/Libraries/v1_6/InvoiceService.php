<?php
/**
 * Invoice Service containing method defining invoice structure
 */

namespace App\Libraries\v1_6;

use Carbon\Carbon;
use App\Libraries\Helper;

/**
 * Class InvoiceService
 */
class InvoiceService
{


    /**
     * Get booking invoice.
     *
     * @param array $booking_request Booking request data.
     *
     * @return array Invoice.
     */
    public static function requestDetailsInvoice(array $booking_request)
    {
        $price_details = json_decode($booking_request['price_details'], true);

        $service_fee_per_unit_on_price = (isset($price_details['service_fee_on_price_with_discount_per_unit_per_night']) === true) ? $price_details['service_fee_on_price_with_discount_per_unit_per_night'] : 0;

        // phpcs:disable
        $service_fee_all_extra_guest = (((isset($price_details['service_fee_on_extra_guest_price_with_discount_per_guest_per_night']) === true) ? $price_details['service_fee_on_extra_guest_price_with_discount_per_guest_per_night'] : 0) * $price_details['extra_guests'])*$price_details['total_nights'];

        $markup_service_fee_percent = (isset($price_details['markup_service_fee_percent']) === true)? (float) $price_details['markup_service_fee_percent']:0;

        $per_night_price_without_service = Helper::getPriceWithoutServiceFee($price_details['per_night_price'], $price_details['service_percentage'], $markup_service_fee_percent);
        $extra_guest_without_service     = Helper::getPriceWithoutServiceFee($price_details['extra_guest_cost'], $price_details['service_percentage'], $markup_service_fee_percent);

        $gh_discount_applied             = (isset($price_details['gh_coupon_amount']) === true) ? $price_details['gh_coupon_amount'] : ((isset($price_details['wallet_money_applied']) === true) ? $price_details['wallet_money_applied'] : 0);
        $service_fee_without_gh_discount = ($price_details['service_fee'] + $gh_discount_applied);

        $price_details['gst_amount'] = (isset($price_details['gst_amount']) === true) ? $price_details['gst_amount'] : 0;

        $service_fee_taxes = (empty(PRICE_WITHOUT_SERVICE_FEE) === false && isset($price_details['service_fee']) === true) ? ($price_details['gst_amount'] + $service_fee_without_gh_discount) : $price_details['gst_amount'];

        $invoice_data = [
            'per_night_per_unit_price'                         => $per_night_price_without_service,
            'per_unit_guests'                                  => round((($booking_request['guests'] - $price_details['extra_guests']) / $booking_request['units_consumed'])),
            'per_night_all_units_extra_guest_price'            => $extra_guest_without_service/$price_details['total_nights'],
            'all_units_extra_guests'                           => $price_details['extra_guests'],
            'per_night_all_units_price_with_extra_guest_price' => (($per_night_price_without_service * $price_details['units_occupied']) + $extra_guest_without_service),

            'required_units'                                   => $price_details['units_occupied'],
            'no_of_nights'                                     => $price_details['total_nights'],
            'all_night_all_units_price_with_extra_guest_price' => ((($per_night_price_without_service * $price_details['units_occupied']) + $extra_guest_without_service) * $price_details['total_nights']),
           // As of now cleaning price is not being stored anywhere in past requests so zero, will change.
            'cleaning_price'                                   => 0,
            'coa_fee'                                          => (isset($price_details['coa_charges']) === true) ? $price_details['coa_charges'] : 0,
            'discount'                                         => (isset($price_details['wallet_money_applied']) === true) ? ($price_details['wallet_money_applied']) : ((isset($price_details['coupon_amount']) === true) ? $price_details['coupon_amount'] : 0),
            'gst_amount'                                       => (isset($price_details['gst_amount']) === true) ? $price_details['gst_amount'] : 0,
            'service_fee_and_taxes'                            => $service_fee_taxes,
            'payable_amount'                                   => $price_details['payable_amount'],
            'released_payment'                                 => (isset($price_details['prevous_booking_credits']) === true) ? $price_details['prevous_booking_credits'] : ((empty($price_details['used_released_payment_amount']) === false) ? $price_details['used_released_payment_amount'] : 0),
            'gst_percent'                                      => (isset($price_details['gst_percent']) === true) ? $price_details['gst_percent'] : 0,
            'payment_method'                                   => (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment',
            'payable_now'                                      => (isset($price_details['payable_now']) === true) ? $price_details['payable_now'] : $price_details['payable_amount'],
            'payable_later'                                    => (isset($price_details['payable_later']) === true) ? $price_details['payable_later'] : 0,
            'payable_later_before'                             => (isset($price_details['payable_later_before']) === true) ? $price_details['payable_later_before'] : '',
            'early_bird_cashback_percentage'                   => (isset($price_details['early_bird_cashback_percentage']) === true) ? $price_details['early_bird_cashback_percentage'] : 0,
            'early_bird_cashback_amount'                       => (isset($price_details['early_bird_cashback_percentage']) === true) ? round(($price_details['early_bird_cashback_percentage'] * $price_details['payable_amount'] / 100)) : 0,
            'early_bird_cashback_text'                         => (isset($price_details['early_bird_cashback_percentage']) === true) ? 'You are eligible for a early bird cashback of '.$price_details['early_bird_cashback_percentage'].'% after checkout.' : '',
            'early_bird_cashback_applicable'                   => (isset($price_details['early_bird_cashback_applicable']) === true) ? $price_details['early_bird_cashback_applicable'] : false,
            'currency_symbol'                                  => $price_details['currency'],
            'currency_code'                                    => $price_details['currency_code'],
            'room_type'                                        => $booking_request['room_type'],
            'paid_amount'                                      => (isset($price_details['paid_amount']) === true) ? $price_details['paid_amount'] : 0,
            'balance_fee'                                      => (isset($booking_request['balance_fee']) === true) ? $booking_request['balance_fee'] : 0,
        ];

        return self::getFormattedInvoiceWithDetails($invoice_data);

    }//end requestDetailsInvoice()


    /**
     * Get formatted invoice with keys and data.
     *
     * @param array $invoice_data Invoice data.
     *
     * @return array.
     */
    public static function getFormattedInvoiceWithDetails(array $invoice_data)
    {
   // phpcs:disable
        return [
            'invoice_header' => [
                [
                    'key'       => 'Base price',
                    'sub_key'   => 'for 1 night, 1 '.(($invoice_data['room_type'] === 1) ? 'unit' : 'room').', '.$invoice_data['per_unit_guests'].(($invoice_data['per_unit_guests'] > 1) ? ' guests' : ' guest'),
                    'value'     => Helper::getFormattedMoney($invoice_data['per_night_per_unit_price'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['per_night_per_unit_price'] > 0) ? 1 : 0,
                     //Will remove later.
                    'raw_value' => round($invoice_data['per_night_per_unit_price']),
                ],
                [
                    'key'       => 'Extra guest cost',
                    'sub_key'   => 'for 1 night, '.$invoice_data['all_units_extra_guests'].(($invoice_data['all_units_extra_guests'] > 1) ? ' guests' : ' guest'),
                    'value'     => Helper::getFormattedMoney($invoice_data['per_night_all_units_extra_guest_price'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['per_night_all_units_extra_guest_price'] > 0) ? 1 : 0,
                     //Will remove later.
                    'raw_value' => round($invoice_data['per_night_all_units_extra_guest_price']),
                ],
                [
                    'key'       => 'Price for 1 night',
                    // phpcs:ignore
                    'sub_key'   => ''.Helper::getFormattedMoney($invoice_data['per_night_per_unit_price'], $invoice_data['currency_code']).' x '.$invoice_data['required_units'].(($invoice_data['required_units'] > 1) ? (($invoice_data['room_type'] === 1) ? ' units' : ' rooms') : (($invoice_data['room_type'] === 1) ? ' unit' : ' room')).(($invoice_data['all_units_extra_guests'] > 0) ? ' + '.Helper::getFormattedMoney($invoice_data['per_night_all_units_extra_guest_price'], $invoice_data['currency_code']) : ''),
                    'value'     => Helper::getFormattedMoney((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']), $invoice_data['currency_code']),
                    'show'      => ((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']) > 0) ? 1 : 0,
                     //Will remove later.
                    'raw_value'     => round((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price'])),

                ],
            ],
            'invoice_middle' => [
                [
                    'key'       => 'Total amount',
                    // phpcs:ignore
                    'sub_key'   => ''.Helper::getFormattedMoney((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']), $invoice_data['currency_code']).' x '.$invoice_data['no_of_nights'].(($invoice_data['no_of_nights'] > 1) ? ' nights' : ' night'),
                    'value'     => Helper::getFormattedMoney(((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']) * $invoice_data['no_of_nights']), $invoice_data['currency_code']),
                    'show'      => (((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']) * $invoice_data['no_of_nights']) > 0) ? 1 : 0,
                    'raw_value' =>  round(((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']) * $invoice_data['no_of_nights']))
                ],
                [
                    'key'       => 'Cleaning fee',
                    'sub_key'   => '',
                    'value'     => '+'.Helper::getFormattedMoney($invoice_data['cleaning_price'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['cleaning_price'] > 0) ? 1 : 0,
                    'raw_value' => round($invoice_data['cleaning_price']),
                ],
                [
                    'key'       => 'COA fee',
                    'sub_key'   => '',
                    'value'     => '+'.Helper::getFormattedMoney($invoice_data['coa_fee'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['coa_fee'] > 0) ? 1 : 0,
                    'raw_value' => round($invoice_data['coa_fee']),
                ],
                [
                    'key'       => 'Discount',
                    'sub_key'   => '',
                    'value'     => '-'.Helper::getFormattedMoney($invoice_data['discount'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['discount'] > 0) ? 1 : 0,
                    'color'     => PROPERTY_LABEL_COLOR_LATER_HEX,
                    'raw_value'     => round($invoice_data['discount']),
                ],
                [
                    'key'       => 'Service Fees & Taxes',
                    'sub_key'   => '',
                    'value'     => Helper::getFormattedMoney($invoice_data['service_fee_and_taxes'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['service_fee_and_taxes']  > 0) ? 1 : 0,
                    'raw_value' => round($invoice_data['service_fee_and_taxes']),

                ],
                [
                    'key'       => 'Previous Booking Credits',
                    'sub_key'   => '',
                    'value'     => '-'.Helper::getFormattedMoney($invoice_data['released_payment'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['released_payment'] > 0) ? 1 : 0,
                    'color'     => PROPERTY_LABEL_COLOR_LATER_HEX,
                    'raw_value' => round($invoice_data['released_payment']),
                ],
            ],
            'invoice_footer' => [
                [
                    'key'       => 'Booking Amount',
                    'sub_key'   => '',
                    'value'     => Helper::getFormattedMoney($invoice_data['payable_amount'], $invoice_data['currency_code']),
                    'show'      => 1,
                    'bold'      => 1,
                    'size'      => 1,
                    'raw_value' => round($invoice_data['payable_amount']),
                ],
                [
                    'key'       => 'Payable now',
                    'sub_key'   => '',
                    'value'     => Helper::getFormattedMoney($invoice_data['payable_now'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['payable_later'] > 0) ? 1 : 0,
                    'raw_value' => round($invoice_data['payable_now']),
                ],
                [
                    'key'       => (($invoice_data['payment_method'] === 'coa_payment') ? 'Payable at check in' : (($invoice_data['payment_method'] === 'partial_payment') ? 'Payable before '.$invoice_data['payable_later_before'] : (($invoice_data['payment_method'] === 'si_payment') ? 'To be charged on '.$invoice_data['payable_later_before'] : 'Payable Later'))),
                    'sub_key'   => '',
                    'value'     => Helper::getFormattedMoney($invoice_data['payable_later'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['payable_later'] > 0) ? 1 : 0,
                    'raw_value' => round($invoice_data['payable_later']),
                ],
                [
                    'key'       => 'Paid amount',
                    'sub_key'   => '',
                    'value'     => (isset($invoice_data['paid_amount']) === true) ? Helper::getFormattedMoney($invoice_data['paid_amount'], $invoice_data['currency_code']) : 0,
                    'show'      => (isset($invoice_data['balance_fee']) === true && $invoice_data['balance_fee'] > 0) ? 1 : 0,
                    'raw_value' => (isset($invoice_data['paid_amount']) === true) ? round($invoice_data['paid_amount']) : 0,
                ],
                [
                    'key'       => 'Remaining amount',
                    'sub_key'   => '',
                    'value'     => (isset($invoice_data['balance_fee']) === true) ? Helper::getFormattedMoney($invoice_data['balance_fee'], $invoice_data['currency_code']) : 0,
                    'show'      => (isset($invoice_data['balance_fee']) === true && $invoice_data['balance_fee'] > 0) ? 1 : 0,
                    'raw_value' => (isset($invoice_data['balance_fee']) === true) ? round($invoice_data['balance_fee']) : 0,
                ],
                [
                    'key'       => 'Earlybird cashback',
                    'sub_key'   => $invoice_data['early_bird_cashback_text'],
                    'value'     => Helper::getFormattedMoney($invoice_data['early_bird_cashback_amount'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['early_bird_cashback_applicable'] === true  && $invoice_data['early_bird_cashback_amount'] > 0) ? 1 : 0,
                    'color'     => PROPERTY_LABEL_COLOR_LATER_HEX,
                    'raw_value' => round($invoice_data['early_bird_cashback_amount']),
                ],
            ],
            'selected_payment_method' => $invoice_data['payment_method'],
            'selected_payment_method_text' => (isset($invoice_data['payment_method_text']) === true) ?  $invoice_data['payment_method_text'] : '',
            'currency'                => $invoice_data['currency_symbol'],
            'currency_code'           => $invoice_data['currency_code'],
        ];

    }//end getFormattedInvoiceWithDetails()

    /**
     * Get booking invoice for host.
     *
     * @param array $booking_request Booking request data.
     *
     * @return array Invoice.
     */
    public static function requestDetailsInvoiceForHost(array $booking_request)
    {
        $price_details = json_decode($booking_request['price_details'], true);

        // Per night Price markup. 
        $per_night_price_markup = (isset($price_details['per_night_price_without_markup']) === true) ? $price_details['per_night_price'] - $price_details['per_night_price_without_markup'] : 0;

        // Extra Guest Cost markup.
        $extra_guest_cost_markup = (isset($price_details['extra_guest_cost_without_markup']) === true) ? $price_details['extra_guest_cost'] - $price_details['extra_guest_cost_without_markup'] : 0;
        // To Ignore Divide by zero exception.
        if($price_details['service_percentage'] > 99){
            $price_details['service_percentage'] = 99;
        }

        $markup_service_fee_percent = (isset($price_details['markup_service_fee_percent']) === true)? (float) $price_details['markup_service_fee_percent']:0;

        $per_night_price_without_service_and_markup = round(($price_details['per_night_price']-$per_night_price_markup)*(100-$price_details['service_percentage'])/100);

        $extra_guest_without_service_and_markup = (($price_details['extra_guest_cost'])/((100/(100-$price_details['service_percentage']))+($markup_service_fee_percent/100)))/($price_details['total_nights']);

        // Calculate Amount. 
        $host_amount = round(Helper::convertPriceToCurrentCurrency($price_details['currency_code'], $price_details['host_fee'], $price_details['property_currency_code']));
            
        // Calculate GH Commission from Host.
        $gh_commission_from_host = 0;

        if(empty($booking_request['commission_from_host']) === false && $booking_request['commission_from_host'] > 0){
            $gh_commission_from_host =round(Helper::convertPriceToCurrentCurrency($price_details['currency_code'], ($price_details['host_fee']*$booking_request['commission_from_host'])/100, $price_details['property_currency_code']));
        }

        $host_coupon_discount = 0;
        if(isset($price_details['host_coupon_discount']) === true) {
            $host_coupon_discount = $price_details['host_coupon_discount'];
        } elseif (isset($price_details['host_coupon_amount']) === true) {
            $host_coupon_discount = $price_details['host_coupon_amount'];
        }
        $host_gst_component = (isset($price_details['host_gst_component']) === true && $price_details['host_gst_component'] > 0) ? $price_details['host_gst_component'] : 0;

        $cleaning_price = 0;

        $invoice_data = [
            'room_type'                                        => $booking_request['room_type'],
            'per_unit_guests'                                  => round((($booking_request['guests'] - $price_details['extra_guests']) / $booking_request['units_consumed'])),
            'per_night_per_unit_price'                         => $per_night_price_without_service_and_markup,
            'all_units_extra_guests'                           => $price_details['extra_guests'],
            'per_night_all_units_extra_guest_price'            => $extra_guest_without_service_and_markup,
            'currency_code'                                    => $price_details['currency_code'],
            'required_units'                                   => $price_details['units_occupied'],
            'no_of_nights'                                     => $price_details['total_nights'],
            'cleaning_price'                                   => $cleaning_price,
            'discount'                                         => $host_coupon_discount,
            'gh_commission_from_host'                          => $gh_commission_from_host,
            'host_amount'                                      => $host_amount   - $gh_commission_from_host,
            'payment_method'                                   => (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment',
            'currency_symbol'                                  => $price_details['currency'],
        ];

        return self::getFormattedInvoiceWithDetailsForHost($invoice_data);

    }//end requestDetailsInvoice()

    /**
     * Get formatted invoice for host with keys and data.
     *
     * @param array $invoice_data Invoice data.
     *
     * @return array.
     */
    public static function getFormattedInvoiceWithDetailsForHost(array $invoice_data)
    {
   // phpcs:disable
        return [
            'invoice_header' => [
                [
                    'key'       => 'Base price',
                    'sub_key'   => 'for 1 night, 1 '.(($invoice_data['room_type'] === 1) ? 'unit' : 'room').', '.$invoice_data['per_unit_guests'].(($invoice_data['per_unit_guests'] > 1) ? ' guests' : ' guest'),
                    'value'     => Helper::getFormattedMoney($invoice_data['per_night_per_unit_price'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['per_night_per_unit_price'] > 0) ? 1 : 0
                ],
                [
                    'key'       => 'Extra guest cost',
                    'sub_key'   => 'for 1 night, '.$invoice_data['all_units_extra_guests'].(($invoice_data['all_units_extra_guests'] > 1) ? ' guests' : ' guest'),
                    'value'     => Helper::getFormattedMoney($invoice_data['per_night_all_units_extra_guest_price'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['per_night_all_units_extra_guest_price'] > 0) ? 1 : 0
                ],
                [
                    'key'       => 'Price for 1 night',
                    // phpcs:ignore
                    'sub_key'   => ''.Helper::getFormattedMoney($invoice_data['per_night_per_unit_price'], $invoice_data['currency_code']).' x '.$invoice_data['required_units'].(($invoice_data['required_units'] > 1) ? (($invoice_data['room_type'] === 1) ? ' units' : ' rooms') : (($invoice_data['room_type'] === 1) ? ' unit' : ' room')).(($invoice_data['all_units_extra_guests'] > 0) ? ' + '.Helper::getFormattedMoney($invoice_data['per_night_all_units_extra_guest_price'], $invoice_data['currency_code']) : ''),
                    'value'     => Helper::getFormattedMoney((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']), $invoice_data['currency_code']),
                    'show'      => ((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']) > 0) ? 1 : 0
                ],
            ],
            'invoice_middle' => [
                [
                    'key'       => 'Total amount',
                    // phpcs:ignore
                    'sub_key'   => ''.Helper::getFormattedMoney((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']), $invoice_data['currency_code']).' x '.$invoice_data['no_of_nights'].(($invoice_data['no_of_nights'] > 1) ? ' nights' : ' night'),
                    'value'     => Helper::getFormattedMoney(((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']) * $invoice_data['no_of_nights']), $invoice_data['currency_code']),
                    'show'      => (((($invoice_data['per_night_per_unit_price'] * $invoice_data['required_units']) + $invoice_data['per_night_all_units_extra_guest_price']) * $invoice_data['no_of_nights']) > 0) ? 1 : 0
                ],
                [
                    'key'       => 'Cleaning fee',
                    'sub_key'   => '',
                    'value'     => '+'.Helper::getFormattedMoney($invoice_data['cleaning_price'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['cleaning_price'] > 0) ? 1 : 0
                ],
                [
                    'key'       => 'Promotional Discount',
                    'sub_key'   => '',
                    'value'     => '-'.Helper::getFormattedMoney($invoice_data['discount'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['discount'] > 0) ? 1 : 0,
                    'color'     => PROPERTY_LABEL_COLOR_LATER_HEX
                ],
                [
                    'key'       => 'Commission',
                    'sub_key'   => '',
                    'value'     => '-'.Helper::getFormattedMoney($invoice_data['gh_commission_from_host'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['gh_commission_from_host'] > 0) ? 1 : 0,
                    'color'     => PROPERTY_LABEL_COLOR_LATER_HEX
                ],
            ],
            'invoice_footer' => [
                [
                    'key'       => 'Booking Amount',
                    'sub_key'   => '',
                    'value'     => Helper::getFormattedMoney($invoice_data['host_amount'], $invoice_data['currency_code']),
                    'raw_value'     => $invoice_data['host_amount'],
                    'show'      => 1,
                    'bold'      => 1,
                    'size'      => 1,
                ],
            ],
            'selected_payment_method' => $invoice_data['payment_method'],
            'selected_payment_method_text' => '',
            'currency'                => $invoice_data['currency_symbol'],
            'currency_code'           => $invoice_data['currency_code'],
        ];

    }//end getFormattedInvoiceWithDetailsForHost()

    /**
     * Get booking invoice for prive.
     *
     * @param array $booking_request Booking request data.
     *
     * @return array Invoice.
     */
      public static function requestDetailsInvoiceForPrive(array $booking_request)
    {
        $gh_coupon_amount = 0;
        $price_details = json_decode($booking_request['price_details'], true);
        $properly_commission_percentage = $booking_request['properly_commission'];
        $gh_discount = (isset($price_details['gh_coupon_amount']) === true) ? $price_details['gh_coupon_amount'] : 0;

        $markup_service_fee = (isset($price_details['markup_service_fee']) === true)? $price_details['markup_service_fee']:0;


        $service_fee = (isset($price_details['service_fee']) === true)? $price_details['service_fee']:0;

         // Calculate GH Commission from Host.
        $gh_commission_from_host = 0;

        if(empty($booking_request['commission_from_host']) === false && $booking_request['commission_from_host'] > 0){
            $gh_commission_from_host =round(Helper::convertPriceToCurrentCurrency($price_details['currency_code'], ($price_details['host_fee']*$booking_request['commission_from_host'])/100, $price_details['property_currency_code']));
        }

        $ota_fees = $markup_service_fee + $service_fee + $gh_commission_from_host ;

        

        $effective_discount =0;
        $effective_discount = (isset($price_details['wallet_money_applied']) === true) ? ($price_details['wallet_money_applied']) : ((isset($price_details['host_coupon_amount']) === true) ? $price_details['host_coupon_amount'] : 0);
        $total_discount = $gh_discount + $effective_discount;

        // Calculate Amount. 
        $host_amount = $booking_request['host_fee'];
        $properly_commission = round(($host_amount * $properly_commission_percentage)/100);

        $gst_component = (isset($price_details['gst_amount']) === true && $price_details['gst_amount'] > 0) ? $price_details['gst_amount'] : 0;
        $invoice_data = [
            'currency_code'                                    => $price_details['currency_code'],
            'discount'                                         => $total_discount,
            'properly_commission'                              => $properly_commission,
            'host_amount'                                      => $host_amount,
            'payment_method'                                   => (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment',
            'currency_symbol'                                  => $price_details['currency'],
            'gst_component' => $gst_component,
            'net_prive_amount' => $host_amount - $properly_commission,
            'ota_fees' => $ota_fees
        ];

        return self::getFormattedInvoiceWithDetailsForPrive($invoice_data);

    }//end requestDetailsInvoiceForPrive()

    /**
     * Get formatted invoice for prive with keys and data.
     *
     * @param array $invoice_data Invoice data.
     *
     * @return array.
     */
       public static function getFormattedInvoiceWithDetailsForPrive(array $invoice_data)
    {
   // phpcs:disable
        return [
            'invoice_header' => [
                [
                    'key'       => 'Booking Amount',
                    'value'     => Helper::getFormattedMoney($invoice_data['host_amount'], $invoice_data['currency_code']),
                    'show'      => 1
                ],
            ],
            'invoice_middle' => [
                 [
                    'key'       => 'Platform Fees',
                    'value'     => '+'.Helper::getFormattedMoney($invoice_data['ota_fees'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['ota_fees'] > 0) ? 1 : 0
                ],
                [
                    'key'       => 'GST',
                    'value'     => '+'.Helper::getFormattedMoney($invoice_data['gst_component'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['gst_component'] > 0) ? 1 : 0
                ],
                [
                    'key'       => 'Effective Discount',
                    'value'     => '-'.Helper::getFormattedMoney($invoice_data['discount'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['discount'] > 0) ? 1 : 0
                ],
                [
                    'key'       => 'Properly Commission',
                    'value'     => '-'.Helper::getFormattedMoney($invoice_data['properly_commission'], $invoice_data['currency_code']),
                    'show'      => ($invoice_data['properly_commission'] > 0) ? 1 : 0
                ],
                 
            ],
            'invoice_footer' => [
                [
                    'key'       => 'Net Amount Paid',
                    'value'     => Helper::getFormattedMoney($invoice_data['net_prive_amount'], $invoice_data['currency_code']),
                    'raw_value' => $invoice_data['net_prive_amount'],
                    'color'     => '#2a4469',
                    'show'      => 1
                ],
            ],
        ];

    }//end getFormattedInvoiceWithDetailsForPrive()


}//end class
