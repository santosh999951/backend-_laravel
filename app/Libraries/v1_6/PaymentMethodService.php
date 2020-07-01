<?php
/**
 * PaymentMethodService containing all payment releated services
 */

namespace App\Libraries\v1_6;

use Carbon\Carbon;
use App\Libraries\Helper;

/**
 * Class PaymentMethodService
 */
class PaymentMethodService
{


    /**
     * Sends Available payment methods.
     *
     * @param array $payment_methods_params Payment method params.
     *
     * @return array Array of array of all payemnt methods.
     */
    public static function getPaymentMethods(array $payment_methods_params)
    {
        foreach (ALL_PAYMENT_METHODS as $payment_method) {
            $payment_method_function                    = self::_getPaymentMethodFunction($payment_method);
            $available_payment_methods[$payment_method] = self::$payment_method_function($payment_methods_params);
        }

        return self::_onlyAvailablePaymentMethods($available_payment_methods);

    }//end getPaymentMethods()


    /**
     * Sends full payment data.
     *
     * @param array $params Payment method params.
     *
     * @return array Full payment data.
     */
    // phpcs:ignore
    private static function _getFullPaymentMethodData(array $params)
    {
        $label                   = 'Full Payment';
        $released_payment_refund = 0;
        if ((int) $params['is_instant_bookable'] === 1) {
            $label = 'Book Instantly';
        }

        return [
            'lable'                => $label,
            'title'                => 'Full payment',
            'description'          => 'Book the property now via netbanking, credit or debit card',
            'sub_description'      => '',
            'popup_text'           => '',
            'payable_amount'       => round($params['payable_amount'], 2),
            'payable_now'          => round($params['payable_amount'], 2),
            'payable_later'        => 0,
            'payable_later_before' => $params['checkin_formatted'],
            'icon'                 => FULL_PAYMENT_ICON_2X,
        ];

    }//end _getFullPaymentMethodData()


    /**
     * Sends coa payment data.
     *
     * @param array $params Payment method params.
     *
     * @return array Coa payment data.
     */
    // phpcs:ignore
    private static function _getCoaPaymentMethodData(array $params)
    {
        // Write better checkin date should be less than 3 days.
        // phpcs:disable
        if ($params['payable_amount'] > 0 && (int) $params['cash_on_arrival'] === 1 && (int) $params['prive'] === 1 && (int) $params['booking_amount'] <= (int) $params['prive_property_coa_max_amount'] && (int) $params['released_payment_refund_amount'] === 0) {
            return [
                'label'                => 'Book Now, Pay Later',
                'title'                => 'Pay at check-in',
                'description'          => 'Book the property now and pay the full amount at check-in. You may also complete the payment before check-in over the app/website',
                'sub_description'      => '',
                'popup_text'           => '',
                'payable_amount'       => round($params['payable_amount'], 2),
                'payable_now'          => 0,
                'payable_later'        => round($params['payable_amount'], 2),
                'payable_later_before' => $params['checkin_formatted'],
                'icon'                  => COA_PAYMENT_ICON_2X
            ];
        }

        return [];

    }//end _getCoaPaymentMethodData()


    /**
     * Sends partial payment data.
     *
     * @param array $params Payment method params.
     *
     * @return array Partial payment data.
     */
    private static function _getPartialPaymentMethodData(array $params)
    {
        if ($params['payable_amount'] > 0 && (int) $params['cash_on_arrival'] === 1 && (int) $params['booking_amount'] <= (int) $params['partial_payment_coa_max_amount'] && (int) $params['released_payment_refund_amount'] === 0 && $params['service_fee'] > 0) {
            $coa = Helper::calculateCoaUpfrontAmount(
                $params['payable_amount'],
                $params['service_fee'],
                $params['gst'],
                $params['prive'],
                $params['gh_commission'],
                $params['coa_fee'],
                $params['markup_service_fee'],
                $params['total_host_fee']
            );

            // Cases where host fee is zero by discount.
            if ($coa['remaining_amount'] < 1) {
                return [];
            }

            $sub_description = '';
            $description     = 'Book the property now at just ';
            $description     = $description.Helper::getFormattedMoney($coa['coa_upfront_amount'], CURRENCY_SYMBOLS[$params['user_currency']]['webicon']).' and pay ';
            $description     = $description.Helper::getFormattedMoney($coa['remaining_amount'], CURRENCY_SYMBOLS[$params['user_currency']]['webicon']).' at check-in';

            if ($params['coa_fee'] > 0) {
                $sub_description = 'Note: To avail the COA facility, a non refundable amount of '.Helper::getFormattedMoney($params['coa_fee'], CURRENCY_SYMBOLS[$params['user_currency']]['webicon']).' would be charged.';
            }

            return [
                'label'                => 'Cash On Arrival',
                'title'                => 'Partial payment',
                'description'          => $description,
                'sub_description'      => $sub_description,
                'popup_text'           => '',
                'payable_amount'       => round(($params['payable_amount'] + $params['coa_fee']), 2),
                'payable_now'          => round($coa['coa_upfront_amount'], 2),
                'payable_later'        => round($coa['remaining_amount'], 2),
                'payable_later_before' => $params['checkin_formatted'],
                'icon'                 => PARTIAL_PAYMENT_ICON_2X
            ];
        }//end if

        return [];

    }//end _getPartialPaymentMethodData()


    /**
     * Sends si payment data.
     *
     * @param array $params Payment method params.
     *
     * @return array Si payment data array.
     */
    private static function _getSiPaymentMethodData(array $params)
    {
        // Checkin date available.
        $si_payment_date = self::_isSIPaymentAvailable($params['cancelation_policy'], $params['payment_gateway_enabled'], $params['policy_days'], $params['checkin']);

        if ($params['payable_amount'] > 0 && $si_payment_date !== false && (int) $params['released_payment_refund_amount'] === 0) {
            return [
                'label'                => 'Book Now, Pay Later',
                'title'                => 'Charge later',
                // phpcs:ignore
                'description' => 'Book the property at '.Helper::getFormattedMoney(1, CURRENCY_SYMBOLS[$params['user_currency']]['webicon'])." now and the remaining amount will be deducted from you card on ".$si_payment_date.'.',
                'sub_description'      => 'This facility is only available on VISA & Mastercard credit cards.',
                'popup_text'           => 'We charge '.Helper::getFormattedMoney(1, CURRENCY_SYMBOLS[$params['user_currency']]['webicon']).' for card authorisation, and the remaining amount will be deducted from your card on '.$si_payment_date.'.',
                'payable_amount'       => round($params['payable_amount'], 2),
                'payable_now'          => 1,
                'payable_later'        => round(($params['payable_amount'] - 1), 2),
                'payable_later_before' => $si_payment_date,
                'icon'                 => PAY_LATER_PAYMENT_ICON_2X
            ];
        }

        return [];

    }//end _getSiPaymentMethodData()


    /**
     * Sends paylater payment data.
     *
     * @param array $params Payment method params.
     *
     * @return array Paylater payment data.
     */
    private static function _getPayLaterPaymentMethodData(array $params)
    {
        $si_payment_date = self::_isSIPaymentAvailable($params['cancelation_policy'], $params['payment_gateway_enabled'], $params['policy_days'], $params['checkin']);

        if ($params['payable_amount'] > 0 && $si_payment_date !== false && (int) $params['released_payment_refund_amount'] === 0) {
            return [
                'label'                => 'Pay later',
                'title'                => 'Pay later',
                'description'          => 'Book the property now and pay the full amount before '.$si_payment_date.' to confirm your booking',
                'sub_description'      => '',
                'popup_text'           => '',
                'payable_amount'       => round($params['payable_amount'], 2),
                'payable_now'          => 0,
                'payable_later'        => round($params['payable_amount'], 2),
                'payable_later_before' => $si_payment_date,
                'icon'                 => PAY_LATER_PAYMENT_ICON_2X
            ];
        }

        return [];

    }//end _getPayLaterPaymentMethodData()


    /**
     * Checks if si payment method available
     *
     * @param integer $cancelation_policy Cancellation policy id.
     * @param integer $gateway            Gateway id.
     * @param integer $policy_days        Policy dates.
     * @param string  $checkin_date       Checking date.
     *
     * @return mixed date/false
     */
    private static function _isSIPaymentAvailable(int $cancelation_policy, int $gateway, int $policy_days, string $checkin_date)
    {
        if ($gateway === 1 && in_array($cancelation_policy, FULL_REFUND_CANCELLATION_POLICY) === true) {
            // Current date.
            $now = Carbon::now('GMT');
            // Checkin date.
            $checkin_date = Carbon::parse($checkin_date);

            // Difference between checking and todays date.
            $diff = $checkin_date->diffInDays($now);

            // Checkin date should be ahead of 2 days + cancellation policy days.
            if ($diff >= ($policy_days + 2)) {
                return self::siPaymentDate($checkin_date, $policy_days);
            }
        }

        return false;

    }//end _isSIPaymentAvailable()


     /**
      * Send si payment method date
      *
      * @param Carbon  $checkin_date Checkin date.
      * @param integer $policy_days  Policy days.
      *
      * @return string Date.
      */
    public static function siPaymentDate(Carbon $checkin_date, int $policy_days)
    {
        // Checkin date should be ahead of 2 days + cancellation policy days.
        return (clone $checkin_date)->subDays($policy_days + 2)->format('d M Y');

    }//end siPaymentDate()


     /**
      * Resolve payment method function.
      *
      * @param string $payment_method Payment method name.
      *
      * @return string Function name corresponding to payment method.
      */
    private static function _getPaymentMethodFunction(string $payment_method)
    {
        return '_get'.str_replace(' ', '', ucwords(str_replace('_', ' ', $payment_method))).'MethodData';

    }//end _getPaymentMethodFunction()


     /**
      * Checks if its a valid payment method.
      *
      * @param array  $params         Payment method params.
      * @param string $payment_method Payment method name.
      *
      * @return boolean true/false.
      */
    private static function _checkIfValidPaymentMethod(array $params, string $payment_method)
    {
        if ($params['payable_amount_without_released_payment'] > $params['released_payment'] || $payment_method === 'full_payment') {
            return true;
        }

        return false;

    }//end _checkIfValidPaymentMethod()


    /**
     * Get only available payment method.
     *
     * @param array $payment_methods Payment methods.
     *
     * @return array
     */
    private static function _onlyAvailablePaymentMethods(array &$payment_methods)
    {
        foreach ($payment_methods as $key => $method) {
            if (count($method) <= 0) {
                unset($payment_methods[$key]);
            }
        }

        return $payment_methods;

    }//end _onlyAvailablePaymentMethods()


}//end class
