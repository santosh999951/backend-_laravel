<?php
/**
    Payment Service containing methods related to Payment
 */

namespace App\Libraries\v1_6;

use Carbon\Carbon;
use App\Libraries\Helper;
use App\Libraries\v1_6\{BookingRequestService};

use App\Models\BookingRequest;
use App\Models\Admin;
use App\Models\User;
use App\Models\BookingServeDetails;
use App\Models\TrafficData;
use App\Models\PaymentGateway;
use App\Models\GatewayBankcodeMapping;
use App\Models\{PaymentTracking, Booking};
use Illuminate\Support\Facades\View as View;
use App\Models\CancellationPolicy;
use \Auth;

/**
 * Class PaymentService
 */
class PaymentService
{

    /**
     * Create Payment hash.
     *
     * @param array $arr Array data.
     *
     * @return string Hash
     */
    // phpcs:ignore
    public static function create_payment_hash(array $arr)
    {
        $hash_sequence = 'merchant_key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10';
        $hash_vars_seq = explode('|', $hash_sequence);
        $hash_string   = '';
        foreach ($hash_vars_seq as $hash_var) {
            $hash_string .= (isset($arr[$hash_var]) === true) ? $arr[$hash_var] : '';
            $hash_string .= '|';
        }

        $hash_string .= $arr['salt'];
        $hash         = strtolower(hash('sha512', $hash_string));
        return $hash;

    }//end create_payment_hash()


    /**
     * Get Payable Amount.
     *
     * @param BookingRequest $request       Booking Request Object.
     * @param object         $price_details Price Detail Object.
     *
     * @return integer Payable amount
     */
    // phpcs:ignore
    private static function getPayableAmount(BookingRequest $request, $price_details)
    {
        $currency_code         = (isset($price_details->currency_code) === true) ? $price_details->currency_code : DEFAULT_PAYMENT_CURRENCY;
        $gst_amount            = (isset($price_details->gst_amount) === true) ? $price_details->gst_amount : 0;
        $cleaning_fee_per_unit = (isset($price_details->cleaning_price_per_unit) === true) ? $price_details->cleaning_price_per_unit : 0;

        $cleaning_fee_all_units_all_nights = ($cleaning_fee_per_unit * $price_details->units_occupied * $price_details->total_nights);

        $total_price_all_nights = ($price_details->payable_amount - $gst_amount - $cleaning_fee_all_units_all_nights);

        $coa_fee = Helper::coaChargesForGuesthouser($total_price_all_nights, $currency_code);

         $cancellation_policy = CancellationPolicy::getCancellationPoliciesByIds([$request->cancellation_policy]);
        // $request->cancelation_policy
        $payment_methods_params = [
            'is_instant_bookable'            => $request->instant_book,
            'service_fee'                    => $price_details->service_fee,
            'gh_commission'                  => $request->commission_from_host,
            'coa_fee'                        => $coa_fee['coa_fee'],
            'gst'                            => $gst_amount,
            'cash_on_arrival'                => $request->coa_available,
            'booking_amount'                 => $total_price_all_nights,
            'released_payment_refund_amount' => 0,
            'payable_amount'                 => $price_details->payable_amount,
            'prive'                          => $request->prive,
            'cancelation_policy'             => $request->cancellation_policy,
            'payment_gateway_enabled'        => 1,
            'checkin'                        => $request->from_date,
            'policy_days'                    => $cancellation_policy[$request->cancellation_policy]['policy_days'],
            'user_currency'                  => $currency_code,
            'prive_property_coa_max_amount'  => Helper::convertPriceToCurrentCurrency('INR', PRIVE_PROPERTY_COA_MAX_AMOUNT, $currency_code),
            'partial_payment_coa_max_amount' => Helper::convertPriceToCurrentCurrency('INR', PARTIAL_PAYMENT_COA_MAX_AMOUNT, $currency_code),
            'checkin_formatted'              => Carbon::parse($request->start_date)->format('d M Y'),
            'markup_service_fee'             => (isset($price_details->markup_service_fee) === true) ? $price_details->markup_service_fee : 0,
            'total_host_fee'                 => $price_details->host_fee,
        ];

            // Get payment methods and label to display.
            $payment_methods = PaymentMethodService::getPaymentMethods($payment_methods_params);

            // phpcs:ignore
            $payment_choosen = (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment';

        if (isset($payment_methods[$payment_choosen]) === true && count($payment_methods[$payment_choosen]) > 0) {
            $payable_now = $payment_methods[$payment_choosen]['payable_now'];
            return $payable_now;
        }

            return $price_details->payable_amount;

    }//end getPayableAmount()


    /**
     * Get Seamless Payment Option data.
     *
     * @param integer $request_id Booking Request Id.
     *
     * @return array
     */
    public static function getSeamlessPaymentOptions(int $request_id)
    {
        $request = BookingRequest::getBookingRequestById($request_id, ['*']);

        if (empty($request) === true || in_array($request->booking_status, [REQUEST_APPROVED, BOOKED]) === false) {
            return [
                'status' => false,
                'reason' => EC_NOT_FOUND,
                'data'   => [],
            ];
        }

        // Get gateway based on currency.
        $currency = ($request->currency !== null) ? $request->currency : DEFAULT_PAYMENT_CURRENCY;
        
        //Condition to add in case of si payment only payu should consider.
        $price_details = json_decode($request->price_details);

        // phpcs:ignore
        $payment_option = (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment';

        $type = '';
        if ($payment_option === 'si_payment') {
            $type = 'payu';
        }

        $gateway  = PaymentGateway::getActiveGateway($currency,$type);
       
        if (empty($gateway) === true) {
            return [
                'status' => false,
                'reason' => EC_PAYMENT_GATEWAY_NOT_AVAILABLE,
                'data'   => [],
            ];
        }

        $data = GatewayBankcodeMapping::getPaymentOptions($gateway['id']);
     
        // CHECK IF PAYMENT INITiTAILED.
        $is_inventory_available = PropertyPricingService::isRequiredUnitsAvailableInProperty($request->pid, $request->from_date, $request->to_date, $request->units);

        $traveller = User::find($request->traveller_id);

        if (empty($is_inventory_available) === true) {
            $request->booking_status = EXPIRED;
            $request->save();

             // Returning wallet decducted, coupon usage, booking credits.
            BookingRequestService::removeCouponWalletCreditsFromRequest($request, $traveller);
            return [
                'status' => false,
                'reason' => 'inventory_not_available',
                'data'   => [],
            ];
        }

        if ($payment_option === 'si_payment' && $gateway['type'] !== 'payu') {
            return [
                'status' => false,
                'reason' => EC_PAYMENT_GATEWAY_NOT_SUPPORTED,
                'data'   => [],
            ];
        }

        // Handle full coa.
        $currency_code = (isset($price_details->currency_code) === true) ? $price_details->currency_code : DEFAULT_PAYMENT_CURRENCY;

        $payment_status = Helper::getPaymentStatus($price_details, $request->booking_status);
        if ($payment_status === 1) {
            $amount = round($price_details->balance_amount, 2);
        } else {
            $amount = self::getPayableAmount($request, $price_details);
        }

        if (empty($amount) === true) {
            $booking_status = Booking::processCashlessPayment($request);
            return [
                'status'          => $booking_status['status'],
                'action'          => 'non_payment',
                'reason'          => $booking_status['reason'],
                'data'            => [],
                'booking'         => $booking_status['booking'],
                'property'        => $booking_status['property'],
                'booking_request' => $booking_status['booking_request'],
            ];
        }

        return [
            'status'             => true,
            'action'             => 'payment',
            'reason'             => '',
            'data'               => $data,
            'booking_status'     => $request->booking_status,
            'amount'             => $amount,
            'currency'           => CURRENCY_SYMBOLS[$currency_code],
            'payment_method'     => $payment_option,
            'is_partial_payment' => $payment_status,
        ];

    }//end getSeamlessPaymentOptions()


    /**
     * Get Seamless Payment Payload data.
     *
     * @param integer $request_id          Booking Request Id.
     * @param string  $payment_option_code Payment Option Code.
     * @param string  $source              Source.
     * @param string  $origin              Origin.
     *
     * @return array
     */
    public static function getSeamlessPayment(int $request_id, string $payment_option_code, string $source, string $origin)
    {
        $request = BookingRequest::getBookingRequestById($request_id, ['*']);

        if (empty($request) === true || in_array($request->booking_status, [REQUEST_APPROVED, BOOKED]) === false) {
            return [
                'status'  => false,
                'reason'  => EC_NOT_FOUND,
                'message' => 'Booking request id is invalid.',
                'data'    => [],
            ];
        }

        // CHECK IF PAYMENT INITiTAILED.
        $is_inventory_available = PropertyPricingService::isRequiredUnitsAvailableInProperty($request->pid, $request->from_date, $request->to_date, $request->units);

        $traveller = User::find($request->traveller_id);

        if (empty($is_inventory_available) === true) {
            $request->booking_status = EXPIRED;
            $request->save();

             // Returning wallet decducted, coupon usage, booking credits.
            BookingRequestService::removeCouponWalletCreditsFromRequest($request, $traveller);
            return [
                'status'  => false,
                'reason'  => 'inventory_not_available',
                'message' => 'Inventory not available',
                'data'    => [],
            ];
        }

        $price_details = json_decode($request->price_details);

        // phpcs:ignore
        $payment_option = (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment';

        // Handle full coa.
        $currency_code = (isset($price_details->currency_code) === true) ? $price_details->currency_code : DEFAULT_PAYMENT_CURRENCY;

        $payment_status = Helper::getPaymentStatus($price_details, $request->booking_status);
        if ($payment_status === 1) {
            $amount = round($price_details->balance_amount, 2);
        } else {
            $amount = self::getPayableAmount($request, $price_details);
        }

        if (empty($amount) === true) {
            return [
                'status'  => false,
                'reason'  => 'non_payment',
                'message' => 'Cash On Arrival Booking Payment Not Allowed',
                'data'    => [],
            ];
        }

        $gateway        = '';
        $payment_method = 'web';

        if ($source === 'app' && $payment_option !== 'si_payment') {
            $gateway        = APP_PAYMENT_METHOD['gateway'];
            $payment_method = APP_PAYMENT_METHOD['method'];
            // For web , for si payment , use payu only.
        } else if ($payment_option === 'si_payment') {
            $gateway = DEFAULT_SI_PAYMENT_METHOD_GATEWAY['GATEWAY_NAME'];
        }

        $payment_gateway = PaymentGateway::getActiveGateway($currency_code, $gateway);

        // Check partial payment.
        $params = [
            'booking_request_id' => $request_id,
            'pid'                => $request->pid,
            'traveller_name'     => $traveller->name,
            'traveller_email'    => $traveller->email,
            'traveller_contact'  => $traveller->contact,
            'traveller_user_id'  => $traveller->id,
            'amount'             => $amount,
            'source'             => $source,
            'origin'             => $origin,
            'payment_method'     => $payment_method,
            'payment_option'     => $payment_option,
            'currency_code'      => $currency_code,
        ];

        $payment_processing_details = PaymentGateway::getPaymentProcessingDetails($params, $payment_gateway);

        if (empty($payment_processing_details['txnid']) === true) {
            return [
                'status'  => false,
                'reason'  => EC_NOT_FOUND,
                'message' => 'Unable to process payment. Server Not responding.',
                'data'    => [],
            ];
        }

        $payment_tracking_details = [
            'request_id'         => $request->id,
            'txn_id'             => $payment_processing_details['txnid'],
            'payment_option'     => PAYMENT_NO[$payment_option],
            'amount'             => $amount,
            'payment_gateway_id' => $payment_gateway['id'],
            'payment_type'       => 'pre_booking',
        ];

        PaymentTracking::initiate($payment_tracking_details);

        $extra_payload = [];
        $payment_processing_details['bankcode'] = $payment_option_code;

        return [
            'status'         => true,
            'action'         => 'payment',
            'reason'         => '',
            'data'           => $payment_processing_details,
            'extra_payload'  => $extra_payload,
            'booking_status' => $request->booking_status,
            'amount'         => $amount,
            'currency'       => $currency_code,
            'gateway'        => $payment_gateway['type'],
        ];

    }//end getSeamlessPayment()


    /**
     * Get Payment data Merged Function.
     *
     * @param integer $request_id Booking Request Id.
     * @param string  $source     Source.
     * @param string  $origin     Origin.
     *
     * @return array
     */
    public static function getPaymentPageData(int $request_id, string $source, string $origin)
    {
        $request = BookingRequest::find($request_id);
        if (empty($request) === true || in_array($request->booking_status, [BOOKED, REQUEST_APPROVED]) === false) {
            return [
                'status' => false,
                'reason' => EC_NOT_FOUND,
                'data'   => [],
            ];
        }

        $price_details = json_decode($request->price_details);
        $traveller     = User::find($request->traveller_id);
        if ($request->booking_status === REQUEST_APPROVED) {
            // CHECK IF PAYMENT INITiTAILED.
            $is_inventory_available = PropertyPricingService::isRequiredUnitsAvailableInProperty($request->pid, $request->from_date, $request->to_date, $request->units);

            if (empty($is_inventory_available) === true) {
                $request->booking_status = EXPIRED;
                $request->save();

                 // Returning wallet decducted, coupon usage, booking credits.
                BookingRequestService::removeCouponWalletCreditsFromRequest($request, $traveller);
                return [
                    'status'         => false,
                    'reason'         => 'inventory_not_available',
                    'booking_status' => REQUEST_APPROVED,
                    'data'           => [],
                ];
            }

            $amount = self::getPayableAmount($request, $price_details);

            if (empty($amount) === true) {
                $booking_status = Booking::processCashlessPayment($request);
                return [
                    'status'          => $booking_status['status'],
                    'action'          => 'non_payment',
                    'reason'          => $booking_status['reason'],
                    'data'            => new \stdClass,
                    'booking'         => $booking_status['booking'],
                    'property'        => $booking_status['property'],
                    'booking_request' => $booking_status['booking_request'],
                ];
            }

            $payment_type = 'pre_booking';
        } else {
            if ($source === 'prive') {
                $booking = Booking::getBookingForRequestId($request->id);

                if (($booking->coa_to_be_collected - $booking->coa_received) > 0) {
                    $amount = round(($booking->coa_to_be_collected - $booking->coa_received), 2);
                } else {
                    return [
                        'status'         => false,
                        'reason'         => 'already_paid',
                        'booking_status' => BOOKED,
                        'data'           => [],
                    ];
                }
            } else {
                $amount = round($price_details->balance_amount, 2);
            }

            $gateway      = '';
            $payment_type = 'post_booking';
        }//end if

        // phpcs:ignore
        $payment_option = (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment';

        // Handle full coa.
        $currency_code = (isset($price_details->currency_code) === true) ? $price_details->currency_code : DEFAULT_PAYMENT_CURRENCY;

        $gateway        = '';
        $payment_method = 'web';

        if ($payment_option === 'si_payment' && $request->booking_status === REQUEST_APPROVED) {
            $gateway = DEFAULT_SI_PAYMENT_METHOD_GATEWAY['GATEWAY_NAME'];
        } else if ($source === 'app') {
            $gateway        = APP_PAYMENT_METHOD['gateway'];
            $payment_method = APP_PAYMENT_METHOD['method'];
        }

        $payment_gateway = PaymentGateway::getActiveGateway($currency_code, $gateway);

        $params = [
            'booking_request_id' => $request_id,
            'pid'                => $request->pid,
            'traveller_name'     => $traveller->name,
            'traveller_email'    => $traveller->email,
            'traveller_contact'  => $traveller->contact,
            'traveller_user_id'  => $traveller->id,
            'amount'             => $amount,
            'source'             => $source,
            'origin'             => $origin,
            'payment_method'     => $payment_method,
            'payment_option'     => $payment_option,
            'currency_code'      => $currency_code,

        ];

        $payment_processing_details = PaymentGateway::getPaymentProcessingDetails($params, $payment_gateway);

        if (empty($payment_processing_details['txnid']) === true) {
            return [
                'status' => false,
                'reason' => EC_NOT_FOUND,
                'data'   => new \stdClass,
            ];
        }

        $payment_tracking_details = [
            'request_id'         => $request->id,
            'txn_id'             => $payment_processing_details['txnid'],
            'payment_option'     => PAYMENT_NO[$payment_option],
            'amount'             => $amount,
            'payment_gateway_id' => $payment_gateway['id'],
            'payment_type'       => $payment_type,
            'source'             => $source,
        ];

        PaymentTracking::initiate($payment_tracking_details);

        return [
            'status'         => true,
            'action'         => 'payment',
            'reason'         => '',
            'data'           => $payment_processing_details,
            'booking_status' => $request->booking_status,
            'amount'         => $amount,
            'currency'       => $currency_code,
        ];

    }//end getPaymentPageData()


}//end class
