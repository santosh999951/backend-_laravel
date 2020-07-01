<?php
/**
 * Booking Model contain all functions related to Bookings
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

use App\Models\{PaymentTracking, BookingRequest,PaymentGateway, Payments, Coupon, CouponCashback, EarlyBirdCashback, SIPayment,
                PaymentRefund, InventoryPricing, Property, WalletTransaction, User, PaymentFailureLog, ReleasePaymentCredits, ShiftBooking};
use App\Libraries\v1_6\PropertyPricingService;
use App\Libraries\v1_6\{AwsService, BookingRequestService};
use App\Libraries\Helper;
use Razorpay\Api\Api;
use App\Libraries\CommonQueue;
use \Carbon\Carbon as Carbon;
/**
 * Class Booking
 */
class Booking extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'bookings';

    /**
     * Gaurded columns
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * Helper function to create scope with active equal one
     *
     * @param integer $request_id   Request id.
     * @param integer $traveller_id User id.
     *
     * @return array Array of booking requests.
     */
    public static function getBookingForRequestAndTravellerId(int $request_id, int $traveller_id)
    {
        $booking = self::where('booking_request_id', '=', $request_id)->where('traveller_id', '=', $traveller_id)->first();

        if (empty($booking) === false) {
            return $booking->toArray();
        }

        return [];

    }//end getBookingForRequestAndTravellerId()


    /**
     * Helper function to create scope with active equal one
     *
     * @param integer $request_id Request id.
     * @param integer $host_id    User id.
     *
     * @return array Array of booking requests.
     */
    public static function getBookingForRequestAndHostId(int $request_id, int $host_id)
    {
        $booking = self::where('booking_request_id', '=', $request_id)->where('host_id', '=', $host_id)->first();

        if (empty($booking) === false) {
            return $booking->toArray();
        }

        return [];

    }//end getBookingForRequestAndHostId()


    /**
     * Helper function to get Booking
     *
     * @param integer $request_id Request id.
     *
     * @return Booking.
     */
    public static function getBookingForRequestId(int $request_id)
    {
        return self::where('booking_request_id', '=', $request_id)->first();

    }//end getBookingForRequestId()


    /**
     * Processes cashless payment
     *
     * @param BookingRequest $request Booking Request.
     *
     * @return array
     */
    public static function processCashlessPayment(BookingRequest $request)
    {
        $user      = User::find($request->traveller_id);
        $firstname = $user->name;

        $price_details = json_decode($request->price_details);

        $amount = 0;
        //phpcs:ignore
        $chosen_payment_method = (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment';
        $payment_option        = PAYMENT_NO[$chosen_payment_method];

        $currency_code     = $request->currency;
        $si_payment        = ($payment_option === SI_PAYMENT) ? 1 : 0;
        $pay_later_payment = ($payment_option === PAY_LATER_ZERO_PAYMENT) ? 1 : 0;

        $pg_list = [
            'CASHLESS_INR' => 8,
            'CASHLESS_EUR' => 9,
            'CASHLESS_USD' => 10,
            'CASHLESS_GBP' => 11,
        ];

        $pg_name         = 'CASHLESS_'.$currency_code;
        $pg_id           = $pg_list[$pg_name];
        $payment_gateway = PaymentGateway::getDetails($pg_id);

        $is_inventory_available = PropertyPricingService::isRequiredUnitsAvailableInProperty($request->pid, $request->from_date, $request->to_date, $request->units);

        if ($is_inventory_available === false) {
            // Expire request.
            $request->booking_status = EXPIRED;
            $request->save();

            // Returning wallet decducted, coupon usage, booking credits.
            $user = User::find($request->traveller_id);
            BookingRequestService::removeCouponWalletCreditsFromRequest($request, $user);

            return [
                'status'             => false,
                'reason'             => EC_NOT_FOUND,
                'booking_request_id' => '',
                'booking'            => (object) [],
                'property'           => (object) [],
                'booking_request'    => (object) [],
            ];
        }

         // Qnly succuessful payment should be stored in payment table.
        $payment_details = [
            'booking_request_id'           => $request->id,
            'amount'                       => $amount,
            'txnid'                        => '',
            'status'                       => 'success',
            'payment_gateway_id'           => $payment_gateway['id'],
            'payment_gateway_name'         => $payment_gateway['name'],
            'currency'                     => $payment_gateway['currency'],
            'first_name'                   => $firstname,
            'mihpayid'                     => '',
            'mode'                         => 'cashless',
            'bank_ref_num'                 => '',
            'addedon'                      => '',
            'payment_gateway_raw_response' => '',
        ];

        $payment = self::saveRecivedPayment($payment_details);

        $booking_params = [
            'amount'                 => $amount,
            'payment_option'         => $payment_option,
            'payment_id'             => $payment->id,
            'partial_payment_status' => $si_payment,
        ];
        // Create booking.
        $booking = self::createBooking($request, $booking_params);

        $price_details->paid_amount    = $amount;
        $price_details->balance_amount = ($price_details->payable_amount - $amount);
        //phpcs:ignore
        $price_details->SI_PAYMENT         = $si_payment;
        $price_details->pay_later_zero_pay = $pay_later_payment;
        $request->price_details            = json_encode($price_details);
        $request->booking_status           = BOOKED;
        $request->save();

        InventoryPricing::decreaseInventory($request->pid, $request->from_date, $request->to_date, $request->units);

        if (property_exists($price_details, 'coupon_applied') === true) {
                $coupon         = $price_details->coupon_applied;
                $coupon_details = Coupon::where('coupon_code', $coupon)->first();

                $coupon_cashback                  = new CouponCashback;
                $coupon_cashback->coupon_usage_id = $price_details->coupon_usage_id;
                $coupon_cashback->currency        = $coupon_details->currency;

                $coupon_cashback->process_date = $request->to_date;
                $coupon_cashback->status       = 0;
            // phpcs:ignore
            if ($coupon_details->coupon_cashback_type == MONEY_COUPON) {
                $coupon_cashback->amount = $coupon_details->cashback_percentage;
            }

            // phpcs:ignore
            if ($coupon_details->coupon_cashback_type == PERCENTAGE_COUPON) {
                $coupon_cashback_amount = (($price_details->payable_amount) * ($coupon_details->cashback_percentage / 100));
                if ($coupon_details->max_cashback > 0) {
                    $coupon_cashback_amount = ($coupon_details->max_cashback < $coupon_cashback_amount) ? $coupon_details->max_cashback : $coupon_cashback_amount;
                }

                $coupon_cashback->amount = $coupon_cashback_amount;
            }

                $coupon_cashback->save();
        }//end if

        $property = Property::find($request->pid);
        if (property_exists($price_details, 'wallet_money_applied') === true) {
            $wallet['event']      = APPLY_WALLET_MONEY;
            $wallet['amount']     = $price_details->wallet_money_applied;
            $wallet['user_id']    = $request->traveller_id;
            $wallet['request_id'] = $request->id;

            $wallet['property_title'] = $property->title;
            $wallet['property_link']  = 'https://www.guesthouser.com/properties/rooms/'.Helper::encodePropertyId($property->id);
            WalletTransaction::removeWalletMoney($wallet);
        }

        // For early bird cashback.
        $booking_date_diff = floor((strtotime($booking->from_date) - strtotime($booking->created_at)) / (60 * 60 * 24));
        $earlybird_count   = EarlyBirdCashback::where('traveller_id', $booking->traveller_id)->count();
        if ($earlybird_count < 1 && $booking_date_diff > 44) {
            $earlybird_cashback               = new EarlyBirdCashback;
            $earlybird_cashback->traveller_id = $booking->traveller_id;
            $earlybird_cashback->booking_request_id = $booking->booking_request_id;
            $earlybird_cashback->save();
        }

        // Not doing this as add wallet money need to be fixed first.
        WalletTransaction::giveReferralMoney($request->traveller_id, $request->id);

        return [
            'status'             => true,
            'reason'             => '',
            'booking_request_id' => $request->id,
            'booking'            => $booking,
            'property'           => $property,
            'booking_request'    => $request,
        ];

    }//end processCashlessPayment()


    /**
     * Create Bookings
     *
     * @param BookingRequest $request Booking Request.
     * @param array          $params  Other Parameters.
     *
     * @return object Booking Object
     */
    public static function createBooking(BookingRequest $request, array $params)
    {
        $price_details           = json_decode($request->price_details);
        $gh_commission_from_host = round((($price_details->host_fee * $request->commission_from_host) / 100), 2);

        $gst_amount = (property_exists($price_details, 'gst_amount') === true) ? $price_details->gst_amount : 0;

        $partial_payment_status = $params['partial_payment_status'];

        $prevous_booking_credits = (property_exists($price_details, 'prevous_booking_credits') === true) ? $price_details->prevous_booking_credits : 0;

        $markup_service_fee = (property_exists($price_details, 'markup_service_fee') === true) ? $price_details->markup_service_fee : 0.00;

        // Payment tracking amount could be of differnt currency fix that.
        $booking_details = [
            'pid'                     => $request->pid,
            'host_id'                 => $request->host_id,
            'traveller_id'            => $request->traveller_id,
            'booking_request_id'      => $request->id,
            'from_date'               => $request->from_date,
            'to_date'                 => $request->to_date,
            'units'                   => $request->units,
            'recieved_currency'       => $request->currency,
            'service_fee'             => $price_details->service_fee,
            'gh_commission_from_host' => $gh_commission_from_host,
            'host_fee'                => ($price_details->host_fee - $gh_commission_from_host),
            'gst_amount'              => $gst_amount,
            'total_charged_fee'       => ($params['amount'] + $prevous_booking_credits),
            'payment_option'          => $params['payment_option'],
            'balance_fee'             => ($price_details->payable_amount - $params['amount']),
            'partial_payment_status'  => $partial_payment_status,
            'payment_id'              => $params['payment_id'],
            'prevous_booking_credits' => $prevous_booking_credits,
            'markup_fee'              => $markup_service_fee,
        ];

        $booking = new Booking($booking_details);
        $booking->save();

        if (property_exists($price_details, 'prevous_booking_credits') === true && $price_details->prevous_booking_credits > 0) {
            self::prevousCreditUpdatePaymentData($request->id);
        }

        return $booking;

    }//end createBooking()


    /**
     * Save Received Payment
     *
     * @param array $details Parameters.
     *
     * @return object Booking Object
     */
    public static function saveRecivedPayment(array $details)
    {
        // Handle store previous booking swtiched id, now removed.
        $payment = new Payments;

        $payment->booking_request_id   = $details['booking_request_id'];
        $payment->paid_amount          = $details['amount'];
        $payment->txnid                = $details['txnid'];
        $payment->status               = $details['status'];
        $payment->payment_gateway_id   = $details['payment_gateway_id'];
        $payment->payment_gateway_name = $details['payment_gateway_name'];
        $payment->currency             = $details['currency'];
        $payment->firstname            = $details['first_name'];

        $payment->mihpayid     = (isset($details['mihpayid']) === true) ? $details['mihpayid'] : '';
        $payment->mode         = (isset($details['mode']) === true) ? $details['mode'] : '';
        $payment->bank_ref_num = (isset($details['bank_ref_num']) === true) ? $details['bank_ref_num'] : '';
        $payment->addedon      = (isset($details['addedon']) === true) ? $details['addedon'] : '';
        $payment->rrn          = (isset($details['rrn']) === true) ? $details['rrn'] : '';
        $payment->auth_id_code = (isset($details['auth_id_code']) === true) ? $details['auth_id_code'] : '';
        $payment->pg_txn_id    = (isset($details['pg_txn_id']) === true) ? $details['pg_txn_id'] : '';
        $payment->phone        = (isset($details['contact']) === true) ? $details['contact'] : '';

        $payment->details = (isset($details['payment_gateway_raw_response']) === true) ? json_encode($details['payment_gateway_raw_response']) : '';
        $payment->save();

        return $payment;

    }//end saveRecivedPayment()


    // phpcs:ignore
    public static function payu_api($command, $var1, $salt, $key)
    {
        $hash = hash('sha512', $key.'|'.$command.'|'.$var1.'|'.$salt);
        $url  = PAYU_API_ENDPOINT.'?form=2';

        $data = [
            'key'     => $key,
            'command' => $command,
            'hash'    => $hash,
            'var1'    => $var1,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;

    }//end payu_api()


    // phpcs:ignore
    public static function non_availability_refund($details, $payment_gateway)
    {
        $output = self::payu_refund_api($details['key'], 'cancel_refund_transaction', $details['var1'], $details['var2'], $details['var3'], $details['salt'], $payment_gateway);
        return $output;

    }//end non_availability_refund()


    // phpcs:ignore
    public static function payu_refund_api($key, $command, $var1, $var2, $var3, $salt, $pg_details)
    {
        $hash = hash('sha512', $key.'|'.$command.'|'.$var1.'|'.$salt);

        $data = [
            'key'     => $key,
            'command' => $command,
            'hash'    => $hash,
            'var1'    => $var1,
            'var2'    => $var2,
            'var3'    => $var3,
        ];

        $qs  = http_build_query($data);
        $url = $pg_details['api_url'].'?form=2';

            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
            curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
            $output = curl_exec($c);
        // phpcs:ignore
        if (curl_errno($c)) {
            $sad = curl_error($c);
            throw new \Exception($sad);
        }

            curl_close($c);
            return $output;

    }//end payu_refund_api()


    // phpcs:ignore
    private static function checkPayUPaymentStatus($txn_id, $salt, $merchant_id, $pid)
    {
        $output       = self::payu_api('verify_payment', $txn_id, $salt, $merchant_id);
        $output       = json_decode($output);
        $api_response = $output->transaction_details->$txn_id;

        $success = true;
        // phpcs:ignore
        if ($api_response->status != 'success' || $api_response->productinfo != $pid) {
            $success = false;
        }

        return [
            'success'                      => $success,
            'failure_msg'                  => $api_response->field9,
            'mihpayid'                     => $api_response->mihpayid,
            'mode'                         => $api_response->mode,
            'status'                       => $api_response->status,
            'bank_ref_num'                 => $api_response->bank_ref_num,
            'addedon'                      => $api_response->addedon,
            'txnid'                        => $api_response->txnid,
            'firstname'                    => $api_response->firstname,
            'payment_gateway_raw_response' => $api_response,
        ];

    }//end checkPayUPaymentStatus()


    /**
     * Get Booking Data.
     *
     * @param integer $traveller_id       Traveller id.
     * @param integer $booking_request_id Booking Request id.
     * @param integer $offset             Offset.
     * @param integer $limit              Total Data.
     *
     * @return array data
     */
    public static function getBookingAndPropertyForTravellerId(int $traveller_id, int $booking_request_id=0, int $offset=0, int $limit=1)
    {
        $bookings = self::select(
            'br.id as booking_request_id',
            'br.pid as id',
            'br.guests',
            'br.units as units_consumed',
            'br.from_date',
            'br.to_date',
            'br.booking_status',
            'p.title',
            'p.area',
            'p.city',
            'p.state',
            'p.country',
            'p.bedrooms',
            'p.property_type',
            'p.room_type',
            'p.v_lat as latitude',
            'p.v_lng as longitude',
            'pt.name as property_type_name',
            'rt.name as room_type_name',
            'r.id as rating_id',
            'pr.id as review_id',
            'h.gender as host_gender',
            'h.profile_img as host_image',
            \DB::raw('RTRIM(h.name) as host_name')
        // phpcs:ignore
        )->join('booking_requests as br', 'br.id', '=', 'bookings.booking_request_id')->join('users as h', 'br.host_id', '=', 'h.id')->join('properties as p', 'p.id', '=', 'bookings.pid')->join('property_type as pt', 'pt.id', '=', 'p.property_type')->join('room_type as rt', 'rt.id', '=', 'p.room_type')->leftjoin('property_reviews as pr', 'pr.booking_id', '=', 'bookings.id')->leftJoin(
            'traveller_ratings as r',
            function ($join) use ($traveller_id) {
                $join->on('r.booking_requests_id', '=', 'br.id')->where('r.rated_by', '=', $traveller_id);
            }
        )->where('bookings.traveller_id', $traveller_id)->where('br.booking_status', BOOKED)->where('bookings.to_date', '<', date('Y-m-d'))->groupBy('br.id');
        if (empty($booking_request_id) === false) {
            $bookings->where('bookings.booking_request_id', $booking_request_id);
        } else {
            $bookings->whereRaw('(r.id is null or pr.id is null)')->orderBy('br.to_date', 'desc')->offset($offset)->limit($limit);
        }

        return $bookings->get()->toArray();

    }//end getBookingAndPropertyForTravellerId()


    // phpcs:ignore
    public static function createRazorPayOrder($request_id, $amount, $currency_code)
    {
        try {
             $api = new Api(RAZORPAY_MERCHANT_ID, RAZORPAY_SECRET);

             // Amount is multiple by 100 as razor pay asked it in paisa.
            $order = $api->order->create(['receipt' => $request_id, 'amount' => (round($amount, 2) * 100), 'currency' => $currency_code, 'payment_capture' => false]);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return '';
        }

        return $order->id;

    }//end createRazorPayOrder()


    /**
     * Curl for hitting urls
     *
     * @param integer $request_id Request Id.
     * @param array   $params     Params.
     *
     * @return object
     */
    public static function createRazorPayVirtualAccount(int $request_id, array $params)
    {
        try {
            $api = new Api(RAZORPAY_MERCHANT_ID, RAZORPAY_SECRET);

            // phpcs:ignore
            $virtual_account = $api->virtualAccount->create(
                [
                    'receiver_types' => ['bank_account'],
                    'description'    => 'Virtual Account for Booking '.Helper::encodeBookingRequestId($request_id),
                    'notes'          => $params,
                ]
            );
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return (object) [];
        }

        return $virtual_account;

    }//end createRazorPayVirtualAccount()


    // phpcs:ignore
    public static function checkRazorPayPaymentStatus($payment_id, $amount, $order_id, $capture=false, $payment_failed=[])
    {
        $success     = false;
        $status      = 'pending';
        $failure_msg = '';
        $method      = '';
        $created_at  = '';
        $payment     = [];

        if (count($payment_failed) > 0 || empty($payment_id) === true) {
            $failure_msg = 'payment failed send by gateway';
            $success     = false;
            $status      = 'failed';
            $payment     = $payment_failed;
        } else {
            try {
                $api               = new Api(RAZORPAY_MERCHANT_ID, RAZORPAY_SECRET);
                $payment           = $api->payment->fetch($payment_id);
                $received_order_id = $payment->order_id;
                $received_amount   = $payment->amount;
                $status            = $payment->status;

                $amount_in_paisa = ($amount * 100);

                // This means its already captured,so should nt captured again.
                // In auto capture ,capture flag will be false.
                if ($capture === true && $status !== 'captured') {
                    //phpcs:ignore
                    if ($received_order_id == $order_id) {
                        if ($capture === true && $status === 'authorized') {
                            $payment = $payment->capture(['amount' => $received_amount]);
                            // Captures a payment.
                        }

                        // Updated status in case if after trying for capture.
                        $status = $payment->status;

                        if (in_array($status, ['captured']) === true) {
                            // Success.
                            $success = true;
                            $status  = 'captured';
                        } else if (in_array($status, ['created', 'authorized']) === true) {
                            // Pending.
                            $success = false;
                            $status  = 'pending';
                        } else if (in_array($status, ['failed']) === true) {
                            // Failed.
                             $success = false;
                             $status  = 'failed';
                        } else if (in_array($status, ['refunded']) === true) {
                            // Refunded.
                            $success = false;
                             $status = 'refunded';
                        } else {
                            $success = false;
                            $status  = 'pending';
                        }

                        $failure_msg = $payment->error_description;
                        $method      = $payment->method;
                        $created_at  = $payment->created_at;
                    } else {
                        $failure_msg = 'txnid associated with booking doesnt matched with payment id ';
                        $success     = false;
                        $status      = 'failed';
                    }//end if
                } else {
                    $failure_msg = 'Capture is set to true but payment is already captured';
                    $success     = false;
                    $status      = 'failed';
                }//end if
            } catch (\Exception $e) {
                $failure_msg = $e->getMessage();
            }//end try
        }//end if

        return [
            'success'                      => $success,
            'status'                       => $status,
            'failure_msg'                  => $failure_msg,
            'mihpayid'                     => $payment_id,
            'mode'                         => $method,
            'bank_ref_num'                 => '',
            'addedon'                      => $created_at,
            'txnid'                        => $order_id,
            'tracking_id'                  => $payment_id,
            'firstname'                    => '',
            'payment_gateway_raw_response' => $payment,
        ];

    }//end checkRazorPayPaymentStatus()


    /**
     * Prevous credit applied request save in payment tables
     *
     * @param integer $new_request_id Request Id.
     *
     * @return boolean true/false
     */
    public static function prevousCreditUpdatePaymentData(int $new_request_id)
    {
        $new_request              = BookingRequest::find($new_request_id);
        $price_details            = json_decode($new_request->price_details);
        $previous_booking_request = BookingRequest::where('traveller_id', $new_request->traveller_id)->where('booking_status', BOOKING_SWITCHED)->orderBy('id', 'DESC')->first();

        if (empty($previous_booking_request) === false && property_exists($price_details, 'prevous_booking_credits') === true && $price_details->prevous_booking_credits > 0) {
            // Update request id in payment table.
            $previous_payments = Payments::where('booking_request_id', $previous_booking_request->id)->where('paid_amount', '>', 0)->update(['booking_request_id' => $new_request->id]);

            // Update Payment Tracking request Id.
            $previous_payments_tracking = PaymentTracking::where('booking_request_id', $previous_booking_request->id)->where('status', 1)->update(['booking_request_id' => $new_request->id]);

            // Update Release Payment Credit.
            $release_payment_credits = ReleasePaymentCredits::where('booking_request_id', $previous_booking_request->id)->update(['new_booking_request_id' => $new_request->id]);

            User::updateBookingCredits($new_request->traveller_id);

            // Update Shift Booking request.
            $shift_booking = ShiftBooking::where('booking_request_id', $previous_booking_request->id)->first();

            if (empty($shift_booking) === false && empty($shift_booking->new_pid) === true) {
                $shift_booking->new_pid = $new_request->pid;
                $shift_booking->status  = 1;
                $shift_booking->save();
            }

            // phpcs:ignore
            Helper::logInfo('<<<<<<<<< Update [Payments, PaymentTracking, ReleasePaymentCredits, ShiftBooking] tables for prevous booking credit release NEW_REQUEST_ID-'.$new_request->id.' *****  PREVIOUS_REQUST_ID-'.$previous_booking_request->id.' >>>>>>>>>>>');

            return true;
        }//end if

        return false;

    }//end prevousCreditUpdatePaymentData()


    /**
     * Save Booking Checkin status
     *
     * @param integer $request_id Booking Request Id.
     *
     * @return boolean
     */
    public function saveCheckinStatus(int $request_id)
    {
        $status = self::where('booking_request_id', $request_id)->update(['checkin_status' => 1, 'checkin_date' => Carbon::now('Asia/Kolkata')->toDateTimeString()]);

        return $status;

    }//end saveCheckinStatus()


    /**
     * Save Booking Checkout status
     *
     * @param integer $request_id Booking Request Id.
     *
     * @return boolean
     */
    public function saveCheckoutStatus(int $request_id)
    {
        $status = self::where('booking_request_id', $request_id)->update(['checkout_status' => 1, 'checkout_date' => Carbon::now('Asia/Kolkata')->toDateTimeString()]);

        return $status;

    }//end saveCheckoutStatus()


    /**
     * Save Booking No Show status
     *
     * @param integer $request_id Booking Request Id.
     *
     * @return boolean
     */
    public function saveNoshowStatus(int $request_id)
    {
        $status = self::where('booking_request_id', $request_id)->update(['no_show' => 1]);

        return $status;

    }//end saveNoshowStatus()


     /**
      * Processes incoming payu payment Merged function
      *
      * @param array $all_input All Inputs.
      *
      * @return array
      */
    public static function processPayment(array $all_input=[])
    {
        $tracking_id = '';

        $txn_id = (isset($all_input['txnid']) === true) ? $all_input['txnid'] : ((isset($all_input['razorpay_order_id']) === true) ? $all_input['razorpay_order_id'] : '');

        if (empty($txn_id) === true) {
            return [
                'status'             => false,
                'reason'             => EC_NOT_FOUND,
                'booking_request_id' => '',
                'second_payment'     => false,
            ];
        }

        $payment_tracking = PaymentTracking::getPaymentTrackingByTxnId($txn_id);

        if (empty($payment_tracking) === true) {
            return [
                'status'             => false,
                'reason'             => EC_NOT_FOUND,
                'booking_request_id' => '',
                'second_payment'     => false,
            ];
        }

        $request = BookingRequest::find($payment_tracking->booking_request_id);
        if (empty($request) === true && in_array($request->booking_status, [BOOKED, REQUEST_APPROVED]) === false) {
            return [
                'status'             => false,
                'reason'             => EC_NOT_FOUND,
                'booking_request_id' => '',
                'second_payment'     => false,
            ];
        }

        $payment_gateway = PaymentGateway::getDetails($payment_tracking->payment_gateway_id);

        if (empty($payment_gateway) === true) {
            return [
                'status'             => false,
                'reason'             => EC_NOT_FOUND,
                'booking_request_id' => $request->id,
                'booking_status'     => $request->booking_status,
                'second_payment'     => ($request->booking_status === REQUEST_APPROVED) ? false : true,
            ];
        }

        // TMP solution.
        if ($payment_tracking->payment_gateway_id === 1) {
            $payment_status = self::checkPayUPaymentStatus($txn_id, $payment_gateway['salt'], $payment_gateway['merchant_id'], $request->pid);
        } else if ($payment_tracking->payment_gateway_id === 26 && isset($all_input['razorpay_payment_id']) === true || isset($all_input['error']) === true) {
            $error      = [];
            $payment_id = '';
            if (isset($all_input['razorpay_payment_id']) === true) {
                $payment_id = $all_input['razorpay_payment_id'];
            } else if (isset($all_input['error']) === true) {
                $error = $all_input['error'];
            }

            $amount = $payment_tracking->amount;

            $payment_status = self::checkRazorPayPaymentStatus($payment_id, $amount, $txn_id, true, $error);
            $tracking_id    = ($payment_status['success'] === true) ? $payment_id : '';
        } else {
            return [
                'status'             => false,
                'reason'             => EC_NOT_FOUND,
                'booking_request_id' => $request->id,
                'booking_status'     => $request->booking_status,
                'second_payment'     => ($request->booking_status === REQUEST_APPROVED) ? false : true,
            ];
        }//end if

        $price_details = json_decode($request->price_details);

        if ($request->booking_status === REQUEST_APPROVED) {
            // IF USER MAKE THE PAYMENT BY SI PAYU.
            if (empty($all_input) === false && (isset($all_input['payment_source']) === true && trim($all_input['payment_source']) === 'sist')) {
                $si          = new SIPayment;
                $si->user_id = $request->traveller_id;
                $si->booking_request_id   = $request->id;
                $si->total_booking_amount = $price_details->payable_amount;
                $si->mihpayid             = $all_input['mihpayid'];
                $si->txnid                = $txn_id;
                $si->user_credential      = Helper::encodeUserId($request->traveller_id).':guesthouser';
                $si->card_token           = (isset($all_input['cardToken']) === true) ? $all_input['cardToken'] : '';
                $si->card_no              = (isset($all_input['cardnum']) === true) ? $all_input['cardnum'] : '';
                $si->card_type            = (isset($all_input['card_type']) === true) ? $all_input['card_type'] : '';
                $si->payment_source       = (isset($all_input['payment_source']) === true) ? $all_input['payment_source'] : '';
                // phpcs:ignore
                $si->All_Info             = json_encode($all_input);

                $si->save();
            }
        }//end if

        $payment_tracking_status = ($payment_status['success'] === true) ? PAYMENT_SUCCESS : (($payment_status['status'] === 'pending') ? PAYMENT_VERIFICATION_PENDING : PAYMENT_FAILED);

        $payment_failure_logs = ($payment_status['success'] === true) ? '' : json_encode($payment_status['payment_gateway_raw_response']);

        $payment_tracking->tracking_id          = $tracking_id;
        $payment_tracking->status               = $payment_tracking_status;
        $payment_tracking->payment_failure_logs = $payment_failure_logs;
        $payment_tracking->save();

        if ($payment_status['success'] !== true) {
            return [
                'status'             => false,
                'reason'             => 'payment_failed',
                'booking_request_id' => $payment_tracking->booking_request_id,
                'booking_status'     => $request->booking_status,
                'second_payment'     => ($request->booking_status === REQUEST_APPROVED) ? false : true,
            ];
        }

        // Only succuessful payment should be stored in payment table.
        $payment_details = [
            'booking_request_id'           => $request->id,
            'amount'                       => $payment_tracking->amount,
            'txnid'                        => $txn_id,
            'status'                       => $payment_status['status'],
            'payment_gateway_id'           => $payment_gateway['id'],
            'payment_gateway_name'         => $payment_gateway['name'],
            'currency'                     => $payment_gateway['currency'],
            'first_name'                   => $payment_status['firstname'],

            'mihpayid'                     => $payment_status['mihpayid'],
            'mode'                         => $payment_status['mode'],
            'bank_ref_num'                 => $payment_status['bank_ref_num'],
            'addedon'                      => $payment_status['addedon'],
            'payment_gateway_raw_response' => json_encode($payment_status['payment_gateway_raw_response']),
        ];

        $payment = self::saveRecivedPayment($payment_details);

        if ($request->booking_status === REQUEST_APPROVED) {
            $second_payment         = false;
            $is_inventory_available = PropertyPricingService::isRequiredUnitsAvailableInProperty($request->pid, $request->from_date, $request->to_date, $request->units);

            $booking_params = [
                'amount'                 => $payment_tracking->amount,
                'payment_option'         => $payment_tracking->payment_option,
                'payment_id'             => $payment->id,
                'partial_payment_status' => ($payment_tracking->payment_option === SI_PAYMENT) ? 1 : 0,
            ];
            // Create booking.
            $booking                    = self::createBooking($request, $booking_params);
            $price_details->paid_amount = $payment_tracking->amount;
            $price_details->balance_amount = ($price_details->payable_amount - $payment_tracking->amount);
            // phpcs:ignore
            $price_details->SI_PAYMENT         = ($payment_tracking->payment_option === SI_PAYMENT) ? 1 : 0;
            $price_details->pay_later_zero_pay = ($payment_tracking->payment_option === PAY_LATER_ZERO_PAYMENT) ? 1 : 0;
            $request->price_details            = json_encode($price_details);
            $request->booking_status           = ($is_inventory_available === true) ? BOOKED : OVERBOOKED;

            $request->save();

            InventoryPricing::decreaseInventory($request->pid, $request->from_date, $request->to_date, $request->units);
            if (property_exists($price_details, 'coupon_applied') === true) {
                $coupon         = $price_details->coupon_applied;
                $coupon_amount  = $price_details->coupon_amount;
                $coupon_details = Coupon::where('coupon_code', $coupon)->first();

                $coupon_cashback                  = new CouponCashback;
                $coupon_cashback->coupon_usage_id = $price_details->coupon_usage_id;
                $coupon_cashback->currency        = $coupon_details->currency;

                $coupon_cashback->process_date = $request->to_date;
                $coupon_cashback->status       = 0;

                $booking_amount = ($price_details->payable_amount - $price_details->gst_amount + $coupon_amount);

                if ($coupon_details->coupon_cashback_type === MONEY_COUPON) {
                    $coupon_cashback->amount = (($booking_amount - $coupon_amount) > $coupon_details->cashback_percentage) ? $coupon_details->cashback_percentage : ($booking_amount - $coupon_amount);
                }

                if ($coupon_details->coupon_cashback_type === PERCENTAGE_COUPON) {
                    $coupon_cashback_amount = ($booking_amount * ($coupon_details->cashback_percentage / 100));

                    if ($coupon_details->max_cashback > 0) {
                        $coupon_cashback_amount = ($coupon_details->max_cashback < $coupon_cashback_amount) ? $coupon_details->max_cashback : $coupon_cashback_amount;
                    }

                    $netcashback_amount      = (($booking_amount - $coupon_amount) > $coupon_cashback_amount) ? $coupon_cashback_amount : ($booking_amount - $booking_amount - $coupon_amount);
                    $coupon_cashback->amount = $netcashback_amount;
                }

                $coupon_cashback->save();

                if (property_exists($price_details, 'wallet_money_applied') === true) {
                    $wallet['event']      = APPLY_WALLET_MONEY;
                    $wallet['amount']     = $price_details->wallet_money_applied;
                    $wallet['user_id']    = $request->traveller_id;
                    $wallet['request_id'] = $request->id;

                    $wallet['property_title'] = $property->title;
                    $wallet['property_link']  = 'https://www.guesthouser.com/properties/rooms/'.Helper::encodePropertyId($property->id);
                    WalletTransaction::removeWalletMoney($wallet);
                }

                // For early bird cashback.
                $booking_date_diff = floor((strtotime($booking->from_date) - strtotime($booking->created_at)) / (60 * 60 * 24));
                $earlybird_count   = EarlyBirdCashback::where('traveller_id', $booking->traveller_id)->count();
                if ($earlybird_count < 1 && $booking_date_diff > 44) {
                    $earlybird_cashback               = new EarlyBirdCashback;
                    $earlybird_cashback->traveller_id = $booking->traveller_id;
                    $earlybird_cashback->booking_request_id = $booking->booking_request_id;
                    $earlybird_cashback->save();
                }

                // Not doing this as add wallet money need to be fixed first.
                WalletTransaction::giveReferralMoney($request->traveller_id, $request->id);
            }//end if
        } else {
            $booking = self::where('booking_request_id', '=', $payment_tracking->booking_request_id)->first();
            if (empty($booking) === true) {
                return [
                    'status'             => false,
                    'reason'             => EC_NOT_FOUND,
                    'booking_request_id' => $payment_tracking->booking_request_id,
                    'second_payment'     => true,
                ];
            }

            $price_details->paid_amount    = ($price_details->paid_amount + $payment_tracking->amount);
            $price_details->balance_amount = ($price_details->balance_amount - $payment_tracking->amount);
            $request->price_details        = json_encode($price_details);
            $request->save();
            $booking->partial_payment_status = 1;
            $booking->total_charged_fee      = ($booking->total_charged_fee + $payment_tracking->amount);
            $booking->balance_fee            = ($booking->balance_fee - $payment_tracking->amount);
            $booking->coa_received           = ($payment_tracking->source === 'prive') ? ($booking->coa_received + $payment_tracking->amount) : $booking->coa_received;
            $booking->save();
            $second_payment = true;
        }//end if

        $property = Property::find($request->pid);

        return [
            'status'             => true,
            'reason'             => '',
            'booking_request_id' => $payment_tracking->booking_request_id,
            'booking_status'     => $request->booking_status,
            'amount'             => $payment_tracking->amount,
            'currency'           => $payment_gateway['currency'],
            'booking'            => $booking,
            'property'           => $property,
            'booking_request'    => $request,
            'second_payment'     => $second_payment,
        ];

    }//end processPayment()


}//end class
