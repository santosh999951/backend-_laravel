<?php
/**
 * Booking Request Service containing methods related to creating booking request
 */

namespace App\Libraries\v1_6;

use Carbon\Carbon;
use App\Libraries\{Helper, CommonQueue};

use App\Models\{BookingRequest, Booking , ProperlyTask};
use App\Models\Admin;
use App\Models\BookingServeDetails;
use App\Models\TrafficData;
use App\Models\CouponUsage;
use App\Models\Property;
use App\Models\PaymentRefund;
use App\Models\RefundRequest;
use App\Models\{User, PropertyImage, CountryCodeMapping, UserBillingInfo, RelationshipManager, CancellationPolicy, RequestRejections, PriveOperations, IvrContactMapping, VirtualAccount, RmBookingRemark};
use Barryvdh\DomPDF\PDF;
use Aws\Exception\AwsException;

/**
 * Class BookingRequestService
 */
class BookingRequestService
{

    /**
     * Email service object for sending emails.
     *
     * @var object
     */
    protected $email_service;

    /**
     * Sms service object for sending sms.
     *
     * @var $sms_service
     */
    protected $sms_service;

    /**
     * Push Notification service object for sending Push Notifications.
     *
     * @var $push_notification
     */
    protected $push_notification;


    /**
     * Email service object for sending emails.
     *
     * @param EmailService            $email_service     Object.
     * @param SmsService              $sms_service       Object.
     * @param PushNotificationService $push_notification Object.
     */
    public function __construct(EmailService $email_service=null, SmsService $sms_service=null, PushNotificationService $push_notification=null)
    {
        $this->email_service     = $email_service;
        $this->sms_service       = $sms_service;
        $this->push_notification = $push_notification;

    }//end __construct()


    /**
     * Create booking request.
     *
     * @param array $params Array of data required to create booking.
     * @param User  $user   User Model Object.
     *
     * @return object
     */
    public static function createBookingRequest(array $params, User $user)
    {
        $booking_request = new BookingRequest;

        $booking_request->pid          = $params['property_id'];
        $booking_request->host_id      = $params['host_id'];
        $booking_request->traveller_id = $params['user_id'];
        // Remove this in future.
        $booking_request->booking_request_code = substr(hash('sha256', $booking_request->pid.$booking_request->host_id.$booking_request->traveller_id.mt_rand().microtime()), 0, 25);

        $booking_request->units                = $params['units'];
        $booking_request->bedroom              = $params['bedrooms'];
        $booking_request->from_date            = $params['start_date'];
        $booking_request->to_date              = $params['end_date'];
        $booking_request->guests               = $params['guests'];
        $booking_request->price_details        = json_encode($params['price_details']);
        $booking_request->currency             = $params['currency'];
        $booking_request->payable_amount       = $params['payable_amount'];
        $booking_request->commission_from_host = $params['gh_commission_percent'];
        $booking_request->gst                  = $params['gst_percent'];
        $booking_request->cancellation_policy  = $params['cancelation_policy'];
        $booking_request->coa_available        = $params['cash_on_arrival'];
        $booking_request->prive                = $params['prive'];
        $booking_request->source               = $params['source'];
        $booking_request->device_type          = $params['device_type'];

        $booking_request->booking_status = $params['booking_status'];
        $booking_request->instant_book   = $params['instant_book'];
        $booking_request->valid_till     = $params['valid_till'];
        $booking_request->approve_till   = $params['approve_till'];
        // New params for prive owner bookings.
        $booking_request->ota_source              = (isset($params['ota_source']) === true) ? $params['ota_source'] : '';
        $booking_request->offline_booking_request = (isset($params['offline_booking_request']) === true) ? $params['offline_booking_request'] : '';
        $booking_request->prive_owner_id          = (isset($params['prive_owner_id']) === true) ? $params['prive_owner_id'] : '';
        $booking_request->offline_source          = (isset($params['offline_source']) === true) ? $params['offline_source'] : '';
        $booking_request->properly_commission     = $params['properly_commission'];

        $booking_request->save();

        // Entering coupon usage in coupon usage table.
        if (array_key_exists('coupon_applied', $params['price_details']) === true) {
            // phpcs:ignore
            $coupon_usage_id = self::saveCouponUsage($params['price_details']['coupon_id'], $params['user_id'], $booking_request->id, $params['price_details']['currency_code'], $params['price_details']['coupon_amount'], $params['price_details']['gh_coupon_amount'], $params['price_details']['host_coupon_amount']);

            $price_details                    = json_decode($booking_request->price_details, true);
            $price_details['coupon_usage_id'] = $coupon_usage_id;

            $booking_request->price_details = json_encode($price_details);
        }

        // Deduction usable wallet balance.
        if (array_key_exists('wallet_money_applied', $params['price_details']) === true) {
            $user->usable_wallet_balance = ($user->usable_wallet_balance - $params['price_details']['wallet_money_applied']);
        }

        // Deducting booking credits.
        if (array_key_exists('prevous_booking_credits', $params['price_details']) === true) {
            $user->booking_credits = ($user->booking_credits - $params['price_details']['prevous_booking_credits']);
        }

        $user->save();

        // Save traffic source log.
        TrafficData::createNew(['event' => 'booking_request', 'actor_id' => $booking_request->id, 'device_unique_id' => $params['device_unique_id'], 'referrer' => '']);

        // Assign to same cc team person whom users earlier request was assinged.
        $old_assigned_request = BookingRequest::where('traveller_id', '=', $params['user_id'])->where('assigned_to', '!=', '0')->orderBy(\DB::raw("CONVERT_TZ(assigned_at,'+00:00','+05:30')"), 'desc')->first();
        if (empty($old_assigned_request) === false) {
            $admin = Admin::where('id', '=', $old_assigned_request->assigned_to)->first();

            if (empty($admin) === false && $admin->is_enabled === 1) {
                $date = Carbon::now('GMT');
                $booking_request->assigned_to = $old_assigned_request->assigned_to;
                $booking_request->assigned_at = $date;
            }
        }

        // Set request to served if instant bookable.
        if ($params['instant_book'] === 1) {
            $serve_details             = new BookingServeDetails;
            $serve_details->request_id = $booking_request->id;
            $serve_details->status     = 2;
            $serve_details->served_by  = 'Instant Booking';
            $serve_details->save();
        }

        // Hash_id.
        $booking_request->hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $booking_request->save();
        return $booking_request;

    }//end createBookingRequest()


    /**
     * Accept booking request.
     *
     * @param integer $request_id Booking request id.
     *
     * @return object
     */
    public static function acceptBookingRequest(int $request_id)
    {
        $booking_request                 = BookingRequest::find($request_id);
        $booking_request->booking_status = REQUEST_APPROVED;
        $booking_request->valid_till     = self::getValidTillTime(Carbon::now('GMT')->toDateTimeString(), $booking_request->from_date);
        $booking_request->host_response_time = (strtotime(Carbon::now('GMT')->toDateTimeString()) - strtotime($booking_request->created_at));
        $booking_request->save();
        // Update average response time.
        self::updateAverageResponseTime($booking_request->pid);

        return $booking_request;

    }//end acceptBookingRequest()


    /**
     * Reject booking request.
     *
     * @param integer $request_id    Booking request id.
     * @param integer $reason_id     Reason id.
     * @param string  $reason_detail Reason Detail Message.
     *
     * @return object
     */
    public static function rejectBookingRequest(int $request_id, int $reason_id=0, string $reason_detail='')
    {
        $booking_request                 = BookingRequest::find($request_id);
        $booking_request->booking_status = REQUEST_REJECTED;
        $booking_request->host_response_time = (strtotime(Carbon::now('GMT')->toDateTimeString()) - strtotime($booking_request->created_at));
        $booking_request->save();

        // Remove Coupon and applied wallet money from booking request.
        self::removeCouponWalletCreditsFromRequest($booking_request, User::find($booking_request->traveller_id));

        // Update average response time.
        self::updateAverageResponseTime($booking_request->pid);

        // Save Rejection Reasons.
        $request_rejection = new RequestRejections;
        $request_rejection->saveRejectionReason($request_id, $reason_id, $reason_detail);

        return $booking_request;

    }//end rejectBookingRequest()


    /**
     * Update average response time of accepting booking request.
     *
     * @param integer $property_id Property id.
     *
     * @return boolean true/false
     */
    public static function updateAverageResponseTime(int $property_id)
    {
        $avg_time = BookingRequest::where('pid', '=', $property_id)->where('host_response_time', '!=', '')->avg('host_response_time');

        if (empty($avg_time) === false) {
            $property                    = Property::find($property_id);
            $property->avg_response_time = $avg_time;
            $property->save();
        }

        return false;

    }//end updateAverageResponseTime()


    /**
     * Save Coupon Usage data.
     *
     * @param integer $coupon_id          Coupon id.
     * @param integer $user_id            User id.
     * @param integer $request_id         Booking Request id.
     * @param string  $currency_code      Currency Code.
     * @param string  $total_discount     Discount Amount.
     * @param string  $gh_discount_amount Guesthouser Discount.
     * @param string  $host_disount       Host Discount.
     *
     * @return object Coupon Usage
     */
    private static function saveCouponUsage(int $coupon_id, int $user_id, int $request_id, string $currency_code, string $total_discount, string $gh_discount_amount, string $host_disount)
    {
        $coupon_usage            = new CouponUsage;
        $coupon_usage->coupon_id = $coupon_id;
        $coupon_usage->used_by   = $user_id;
        $coupon_usage->booking_request_id   = $request_id;
        $coupon_usage->currency             = $currency_code;
        $coupon_usage->discount             = $total_discount;
        $coupon_usage->gh_discount_amount   = $gh_discount_amount;
        $coupon_usage->host_discount_amount = $host_disount;
        $coupon_usage->save();
        return $coupon_usage->id;

    }//end saveCouponUsage()


    /**
     * Save Coupon Usage data.
     *
     * @param integer $request_id Booking Request id.
     *
     * @return void
     */
    private static function removeCouponUsage(int $request_id)
    {
        $coupon_usage = CouponUsage::where('booking_request_id', $request_id);

        if (empty($coupon_usage) === false) {
            $coupon_usage->delete();
        }

    }//end removeCouponUsage()


     /**
      * Save Coupon Usage data.
      *
      * @param BookingRequest $booking_request Booking Request Object.
      * @param User           $user            User Object.
      *
      * @return void
      */
    public static function removeCouponWalletCreditsFromRequest(BookingRequest $booking_request, User $user)
    {
        $price_details = json_decode($booking_request->price_details, true);

        $applied_wallet_money = 0;
        if (array_key_exists('wallet_money_applied', $price_details) === true) {
            $applied_wallet_money = $price_details['wallet_money_applied'];
        }

        $applied_released_payment = 0;
        if (array_key_exists('used_released_payment_amount', $price_details) === true) {
            $applied_released_payment = $price_details['used_released_payment_amount'];
        } else if (array_key_exists('prevous_booking_credits', $price_details) === true) {
            $applied_released_payment = $price_details['prevous_booking_credits'];
        }

        self::removeCouponUsage($booking_request->id);
        if ($booking_request->booking_status >= BOOKED) {
            $user->wallet_balance = ($user->wallet_balance + $applied_wallet_money);
        }

        $user->usable_wallet_balance = ($user->usable_wallet_balance + $applied_wallet_money);
        $user->booking_credits       = ($user->booking_credits + $applied_released_payment);
        $user->save();

    }//end removeCouponWalletCreditsFromRequest()


    /**
     * Update booking request.
     *
     * @param array $where        Array of data required to check in where before updating.
     * @param array $params       Array of data required to update booking.
     * @param array $extra_params Array of Extra data.
     * @param User  $user         User object.
     *
     * @return integer No of rows updated.
     */
    public static function updateBookingRequest(array $where, array $params, array $extra_params=[], User $user=null)
    {
        if (count($extra_params) > 0 && empty($user) === false) {
            $discount_data              = $extra_params['discount_data'];
            $prev_applied_wallet_amount = $extra_params['applied_wallet_money'];

            $prev_applied_released_payment = $extra_params['applied_released_amount'];
            $new_released_payment          = $extra_params['prevous_booking_credits'];

            $coupon_usage_id = 0;

            // Removing old coupon usage , entering new coupon usage.
            if ($extra_params['coupon_change'] === true) {
                self::removeCouponUsage($where['id']);

                if (array_key_exists('coupon_applied', $discount_data) === true) {
                    // phpcs:ignore
                    $coupon_usage_id = self::saveCouponUsage($discount_data['coupon_id'], $discount_data['user_id'], $discount_data['request_id'], $discount_data['currency_code'], $discount_data['coupon_amount'], $discount_data['gh_coupon_amount'], $discount_data['host_coupon_amount']);
                }
            } else {
                $coupon_usage_id = $extra_params['applied_coupon_usage_id'];
            }

            if (empty($coupon_usage_id) === false) {
                $price_details                    = json_decode($params['price_details'], true);
                $price_details['coupon_usage_id'] = $coupon_usage_id;
                $params['price_details']          = json_encode($price_details);
            }

            $new_wallet_amount_used = 0;
            if (array_key_exists('wallet_money_applied', $discount_data) === true) {
                $new_wallet_amount_used = $discount_data['wallet_money_applied'];
            }

            // Decuting booking credit son updation of request.
            $user->booking_credits = ($user->booking_credits + $prev_applied_released_payment - $new_released_payment);
            // Deduction usable wallet balance on upatation.
            $user->usable_wallet_balance = ($user->usable_wallet_balance + $prev_applied_wallet_amount - $new_wallet_amount_used);
            $user->save();
        }//end if

        return BookingRequest::where($where)->update($params);

    }//end updateBookingRequest()


    /**
     * Calculate time till booking request is valid for approval. (write better code)
     *
     * @param string $created_at   Time of booking request.
     * @param string $checkin_date Date of checkin.
     *
     * @return string
     */
    public static function getValidTillTime(string $created_at, string $checkin_date)
    {
        $created_at   = Carbon::parse($created_at);
        $checkin_date = Carbon::parse($checkin_date);

        // If difference between checkin_date and created_at is more than 48 hours.
        // Allow valid till 48 Hours.
        // Else set validity for 8 Hours.
        if ($created_at->diffInHours($checkin_date) > 48) {
            $valid_till = $created_at->addHours(48);
        } else {
            $valid_till = $created_at->addHours(8);
        }

        return gmdate('Y-m-d H:i:s', strtotime($valid_till));

    }//end getValidTillTime()


    /**
     * Get Refund Data
     *
     * @param integer $booking_request_id   Booking Request Id.
     * @param string  $booking_status_class Booking Status Class.
     *
     * @return array
     */
    public static function getRefundData(int $booking_request_id, string $booking_status_class)
    {
        $refund_amount = '';
        $refund_status = Helper::getRefundStatus(-2);
        $refund_show   = 0;
        $date          = '';
        $message       = '';

        // Return empty refund data for non-cancelled Request.
        if (in_array($booking_status_class, [CANCELLATION_CLASS]) === false) {
            return [
                'amount'           => $refund_amount,
                'status'           => $refund_status['refund_status'],
                'show'             => $refund_show,
                'request_accepted' => $refund_status['request_accepted'],
                'current_status'   => $refund_status['current_status'],
                'processing_date'  => $date,
                'message'          => $message,
            ];
        }

        $refund_request = RefundRequest::getRefundRequestOfBooking($booking_request_id);

        if (empty($refund_request) === false) {
            $refund_show   = (empty($refund_request['refund_amount']) === false) ? 1 : 0;
            $refund_amount = Helper::getFormattedMoney($refund_request['refund_amount'], $refund_request['refund_currency']);
            $refund_status = Helper::getRefundStatus($refund_request['status']);
            $date          = Carbon::parse($refund_request['processing_date'])->format('Y-m-d');
            $message       = $refund_status['message'];
        }

        return [
            'amount'           => $refund_amount,
            'status'           => $refund_status['refund_status'],
            'show'             => $refund_show,
            'request_accepted' => $refund_status['request_accepted'],
            'current_status'   => $refund_status['current_status'],
            'processing_date'  => $date,
            'message'          => $message,
        ];

    }//end getRefundData()


    /**
     * Calculate time till booking request can be approved. (write better code)
     *
     * @param string $created_at   Time of booking request.
     * @param string $checkin_date Date of checkin.
     *
     * @return string
     */
    public static function getApproveTillTime(string $created_at, string $checkin_date)
    {
        $created_at   = Carbon::parse($created_at);
        $checkin_date = Carbon::parse($checkin_date);

        // If difference between checkin_date and created_at is more than 48 hours.
        // Allow valid till 48 Hours.
        // Else set validity for 8 Hours.
        if ($created_at->diffInHours($checkin_date) > 48) {
            $approve_till = $created_at->addHours(48);
        } else {
            $approve_till = $created_at->addHours(8);
        }

        return gmdate('Y-m-d H:i:s', strtotime($approve_till));

    }//end getApproveTillTime()


    /**
     * Check if booking request cancellable.
     *
     * @param integer $booking_status Booking status.
     *
     * @return boolean If request cancellable.
     */
    public static function getBookingRequestCancellationStatus(int $booking_status)
    {
        return (in_array($booking_status, [NEW_REQUEST, REQUEST_APPROVED]) === true) ? REQUEST_CANCELLED : ((in_array($booking_status, [BOOKED]) === true) ? CANCELLED_AFTER_PAYMENT : false);

    }//end getBookingRequestCancellationStatus()


     /**
      * Get Timeline status for a booking
      *
      * @param \Carbon\Carbon $start_date_obj Booking checkin date object.
      * @param \Carbon\Carbon $end_date_obj   Booking checkout date object.
      *
      * @return string
      */
    public static function getTimelineStatusForBookingList(Carbon $start_date_obj, Carbon $end_date_obj)
    {
        $today             = Carbon::now('Asia/Kolkata');
        $no_of_days        = $today->diffInDays($start_date_obj, false);
        $past_no_of_days   = $today->diffInDays($end_date_obj, false);
        $no_of_months      = $today->diffInMonths($start_date_obj, false);
        $past_no_of_months = $today->diffInMonths($end_date_obj, false);
        $no_of_years       = $today->diffInYears($start_date_obj, false);
        $past_no_of_years  = $today->diffInYears($end_date_obj, false);

            // Text to display according to no of days to/from trip.
        if ($no_of_years > 0) {
            $timeline_string = ($no_of_years > 1) ? ' years ' : ' year ';
            $timeline_status = $no_of_years.$timeline_string.'to go';
        } else if ($no_of_months > 0) {
            $timeline_string = ($no_of_months > 1) ? ' months ' : ' month ';
            $timeline_status = $no_of_months.$timeline_string.'to go';
        } else if ($no_of_days > 0) {
            $timeline_string = ($no_of_days > 1) ? ' days ' : ' day ';
            $timeline_status = $no_of_days.$timeline_string.'to go';
        } else if ($no_of_days <= 0 && $past_no_of_days >= 0) {
            $timeline_status = 'Ongoing';
        } else if ($past_no_of_years < 0) {
            $timeline_string = (abs($past_no_of_years) > 1) ? ' years ' : ' year ';
            $timeline_status = abs($past_no_of_years).$timeline_string.'ago';
        } else if ($past_no_of_months < 0) {
            $timeline_string = (abs($past_no_of_months) > 1) ? ' months ' : ' month ';
            $timeline_status = abs($past_no_of_months).$timeline_string.'ago';
        } else {
            $timeline_string = (abs($past_no_of_days) > 1) ? ' days ' : ' day ';
            $timeline_status = abs($past_no_of_days).$timeline_string.'ago';
        }//end if

        return $timeline_status;

    }//end getTimelineStatusForBookingList()


    /**
     * Get SI Payment Auto Recurring from guest account Date
     *
     * @param integer $cancelation_policy_id Cancellation Policy Id.
     * @param string  $checkin_date          Checkin Date.
     *
     * @return string
     */
    public function getSIAutoRecurringDate(int $cancelation_policy_id, string $checkin_date)
    {
        $cancellation_policy = CancellationPolicy::getCancellationPoliciesByIds([$cancelation_policy_id])[$cancelation_policy_id];
        $now                 = Carbon::now('GMT');
        $checkin_date        = Carbon::parse($checkin_date);

        if ($checkin_date->gte($now) === true) {
            return $checkin_date->subDays(($cancellation_policy['policy_days'] + 2))->format('d M Y');
        } else {
            return '';
        }

    }//end getSIAutoRecurringDate()


     /**
      * Create Invoice
      *
      * @param BookingRequest $request Booking Request Object.
      *
      * @return array
      */
    public function createInvoice(BookingRequest $request)
    {
        $booking  = Booking::getBookingForRequestId($request->id);
        $property = Property::getPropertyById($request->pid);

        if (empty($booking) === true || empty($property) === true) {
            return [];
        }

        $traveller_name = User::getUserFullNameByIds([$request->traveller_id])[$request->traveller_id];

        $traveller_invoice = $this->createTravellerInvoice($request, $booking, $property);
        $host_invoice      = $this->createHostInvoice($request, $booking, $property, $traveller_name);

        return [
            'traveller' => $traveller_invoice,
            'host'      => $host_invoice,
        ];

    }//end createInvoice()


    /**
     * Create Traveller Invoice
     *
     * @param BookingRequest $request  Booking Request Object.
     * @param Booking        $booking  Booking Object.
     * @param Property       $property Property Object.
     *
     * @return array
     */
    public function createTravellerInvoice(BookingRequest $request, Booking $booking, Property $property)
    {
        $price_details = json_decode($request->price_details);

        $extra_guest_cost_per_night = ($price_details->extra_guest_cost / $price_details->total_nights);

        $per_night_all_unit_with_extra_guest_cost = (($price_details->per_night_price * $request->units) + $extra_guest_cost_per_night);

        $all_night_all_unit_with_extra_guest_cost = ((($price_details->per_night_price * $request->units) * $price_details->total_nights) + $price_details->extra_guest_cost);

        // Calculate Formatted Paid Amount.
        $formatted_paid_amount = ($booking->total_charged_fee > 0) ? Helper::getFormattedMoney($booking->total_charged_fee, $price_details->currency_code, true, false) : '';

        // Calculate Formatted Balance Amount.
        $formatted_balance_fee = ($booking->balance_fee > 0) ? Helper::getFormattedMoney($booking->balance_fee, $price_details->currency_code, true, false) : '';

        // Calculate GST Amount.
        $formatted_gst_amount = (property_exists($price_details, 'gst_amount') === true && $price_details->gst_amount > 0) ? Helper::getFormattedMoney($price_details->gst_amount, $price_details->currency_code, true, false) : '';

        // Calculate COA Amount.
        $formatted_coa_amount = ((int) $booking->payment_option === CASH_ON_ARRIVAL && empty($price_details->coa_charges) === false) ? Helper::getFormattedMoney($price_details->coa_charges, $price_details->currency_code, true, false) : '';

        // Calculate Convenience Fee.
        $formatted_convenience_fee = (property_exists($price_details, 'convenience_fee') === true && $price_details->convenience_fee > 0) ? Helper::getFormattedMoney($price_details->convenience_fee, $price_details->currency_code, true, false) : '';

        // Calculate Paylater Payment Date.
        $paylater_payment_date = (empty($formatted_balance_fee) === false) ? $this->getSIAutoRecurringDate($property->cancelation_policy, $request->from_date) : '';

        $all_discounts = $this->getAllAppliedDiscount($request);

        $view_data = [
            'request_hash_id'       => Helper::encodeBookingRequestId($request->id),
            'property_title'        => $property->title,
            'formatted_from_date'   => Carbon::parse($request->from_date)->format('d M Y'),
            'formatted_to_date'     => Carbon::parse($request->to_date)->format('d M Y'),
            'total_nights'          => $price_details->total_nights,
            'guests'                => $request->guests,
            'extra_guests'          => $price_details->extra_guests,
            'room_type'             => $property->room_type,
            'units'                 => $request->units,
            'paylater_payment_date' => $paylater_payment_date,
            'booking_status'        => $request->booking_status,
            'price'                 => [

                'extra_guest_cost'                                   => $price_details->extra_guest_cost,
                'formatted_extra_guest_cost_per_night'               => Helper::getFormattedMoney($extra_guest_cost_per_night, $price_details->currency_code, true, false),

                'formatted_per_night_price'                          => Helper::getFormattedMoney($price_details->per_night_price, $price_details->currency_code, true, false),
                'formatted_per_night_all_unit_with_extra_guest_cost' => Helper::getFormattedMoney($per_night_all_unit_with_extra_guest_cost, $price_details->currency_code, true, false),
                'formatted_all_night_all_unit_with_extra_guest_cost' => Helper::getFormattedMoney($all_night_all_unit_with_extra_guest_cost, $price_details->currency_code, true, false),

                'formatted_payable_amount'                           => Helper::getFormattedMoney($price_details->payable_amount, $price_details->currency_code, true, false),
                'formatted_paid_amount'                              => $formatted_paid_amount,
                'formatted_balance_fee'                              => $formatted_balance_fee,

                'formatted_gst_amount'                               => $formatted_gst_amount,
                'formatted_coa_amount'                               => $formatted_coa_amount,
                'formatted_convenience_fee'                          => $formatted_convenience_fee,

            ],
            'all_discounts'         => [
                'formatted_discount'         => $all_discounts['formatted_discount'],
                'formatted_wallet_discount'  => $all_discounts['formatted_wallet_discount'],
                'formatted_miles_discount'   => $all_discounts['formatted_miles_discount'],
                'formatted_coupon_discount'  => $all_discounts['formatted_coupon_discount'],
                'formatted_agent_meal_price' => $all_discounts['formatted_agent_meal_price'],
            ],
        ];

        $pdf_url = base_path().'/public'.PDF_TMP_DIR.'/'.str_random(4).time().'.pdf';
        $pdf     = \App::make('dompdf.wrapper');
        $pdf->loadView('invoice.invoice_guest_pdf', ['view_data' => $view_data])->save($pdf_url);

        $s3_path = 'T_'.Helper::encodeBookingRequestId($request->id).'.pdf';

        try {
            // If using s3, move image to s3 bucket and remove from local directory.
            AwsService::putObjectInS3Bucket(
                S3_BUCKET,
                S3_INVOICE_PDF_DIR.$s3_path,
                $pdf_url,
                'public-read',
                DEFAULT_S3_REGION,
                '0'
            );
            unlink($pdf_url);
        } catch (\ErrorException $e) {
            Helper::logError('Unable to upload invoice on S3 ('.$s3_path.')');
        } catch (AwsException $e) {
            Helper::logError('Unable to upload invoice on S3 ('.$s3_path.')');
        }

        $pdf_url = INVOICE_PDF_DIR.$s3_path;

        return [
            'name' => $s3_path,
            'url'  => $pdf_url,
        ];

    }//end createTravellerInvoice()


    /**
     * Create Host Invoice
     *
     * @param BookingRequest $request        Booking Request Object.
     * @param Booking        $booking        Booking Object.
     * @param Property       $property       Property Object.
     * @param string         $traveller_name Traveller Name.
     *
     * @return array
     */
    public function createHostInvoice(BookingRequest $request, Booking $booking, Property $property, string $traveller_name)
    {
        $price_details = json_decode($request->price_details);
        $all_discounts = $this->getAllAppliedDiscount($request);

        $per_night_markup = 0;

        if (property_exists($price_details, 'per_night_price_without_markup') === true) {
            $per_night_markup = ($price_details->per_night_price - $price_details->per_night_price_without_markup);
        }

        $host_per_night_price = (($price_details->per_night_price - $per_night_markup) * (100 - $price_details->service_percentage) / 100);

        $markup_service_fee_percent = 0;

        if (property_exists($price_details, 'markup_service_fee_percent') === true) {
            $markup_service_fee_percent = (int) $price_details->markup_service_fee_percent;
        }

        $extra_guest_cost_per_night = ($price_details->extra_guest_cost / ($price_details->total_nights * ((100 / (100 - $price_details->service_percentage)) + ($markup_service_fee_percent / 100))));

        $host_total_price_per_night = ($host_per_night_price * $request->units + $extra_guest_cost_per_night);

        $gh_commission = 0;
        if (empty($request->commission_from_host) === false && $request->commission_from_host > 0) {
            $gh_commission = (($price_details->host_fee * $request->commission_from_host) / 100);
        }

        // Formatted Gst Amount.
        $formatted_gh_commission_amount = (empty($gh_commission) === false) ? Helper::getFormattedMoney($gh_commission, $price_details->currency_code, true, false) : '';

        $host_gst_component = (empty($price_details->host_gst_component) === false) ? $price_details->host_gst_component : 0;

        $formatted_host_gst_amount = (empty($host_gst_component) === false) ? Helper::getFormattedMoney($host_gst_component, $price_details->currency_code, true, false) : '';

        // Calculate COA Amount.
        $formatted_coa_amount = ((int) $booking->payment_option === CASH_ON_ARRIVAL && empty($price_details->coa_charges) === false) ? Helper::getFormattedMoney($price_details->coa_charges, $price_details->currency_code, true, false) : '';

        $view_data = [
            'request_hash_id'      => Helper::encodeBookingRequestId($request->id),
            'traveller_name'       => $traveller_name,
            'formatted_created_at' => Carbon::parse($request->created_at)->setTimezone('Asia/Kolkata')->format('d M Y'),
            'total_nights'         => $price_details->total_nights,
            'room_type'            => $property->room_type,
            'guests'               => $request->guests,
            'units'                => $request->units,
            'extra_guests'         => $price_details->extra_guests,
            'price'                => [
                'extra_guest_cost'                     => $price_details->extra_guest_cost,
                'formatted_extra_guest_cost_per_night' => Helper::getFormattedMoney($extra_guest_cost_per_night, $price_details->currency_code, true, false),

                'formatted_host_per_night_price'       => Helper::getFormattedMoney($host_per_night_price, $price_details->currency_code, true, false),
                'formatted_host_total_price_per_night' => Helper::getFormattedMoney($host_total_price_per_night, $price_details->currency_code, true, false),
                'formatted_host_total_price'           => Helper::getFormattedMoney(($host_total_price_per_night * $price_details->total_nights), $price_details->currency_code, true, false),
                'formatted_host_amount'                => Helper::getFormattedMoney(($price_details->host_fee - $gh_commission), $price_details->currency_code, true, false),
                'formatted_host_amount_with_gst'       => Helper::getFormattedMoney(($price_details->host_fee - $gh_commission + $host_gst_component), $price_details->currency_code, true, false),

                'formatted_host_gst_amount'            => $formatted_host_gst_amount,
                'formatted_coa_amount'                 => $formatted_coa_amount,
                'formatted_gh_commission_amount'       => $formatted_gh_commission_amount,
            ],
            'all_discounts'        => [
                'formatted_discount'         => $all_discounts['formatted_discount'],
                'formatted_miles_discount'   => $all_discounts['formatted_miles_discount'],
                'formatted_coupon_discount'  => $all_discounts['formatted_host_coupon_discount'],
                'formatted_agent_meal_price' => $all_discounts['formatted_agent_meal_price'],
            ],

        ];

        $pdf_url = base_path().'/public'.PDF_TMP_DIR.'/'.str_random(4).time().'.pdf';
        $pdf     = \App::make('dompdf.wrapper');
        $pdf->loadView('invoice.invoice_host_pdf', ['view_data' => $view_data])->save($pdf_url);

        $s3_path = 'H_'.Helper::encodeBookingRequestId($request->id).'.pdf';

        try {
            // If using s3, move image to s3 bucket and remove from local directory.
            AwsService::putObjectInS3Bucket(
                S3_BUCKET,
                S3_INVOICE_PDF_DIR.$s3_path,
                $pdf_url,
                'public-read',
                DEFAULT_S3_REGION,
                '0'
            );
            unlink($pdf_url);
        } catch (\ErrorException $e) {
            Helper::logError('Unable to upload invoice on S3 ('.$s3_path.')');
        } catch (AwsException $e) {
            Helper::logError('Unable to upload invoice on S3 ('.$s3_path.')');
        }

        $pdf_url = INVOICE_PDF_DIR.$s3_path;

        return [
            'name' => $s3_path,
            'url'  => $pdf_url,
        ];

    }//end createHostInvoice()


    /**
     * Calculate host booking amount
     *
     * @param BookingRequest $booking_request Booking Request Object.
     *
     * @return float
     */
    public function getHostAmount(BookingRequest $booking_request)
    {
        $price_details = json_decode($booking_request->price_details);

        // Calculate GH Commission from Host.
        $gh_commission_from_host = 0;

        if (empty($booking_request->commission_from_host) === false && $booking_request->commission_from_host > 0) {
            $gh_commission_from_host = (($price_details->host_fee * $booking_request->commission_from_host) / 100);
        }

        return ($price_details->host_fee - $gh_commission_from_host);

    }//end getHostAmount()


    /**
     * Calculate host booking amount
     *
     * @param array $request_data Request data.
     *
     * @return float
     */
    public static function calculateHostAmount(array $request_data)
    {
        $price_details = json_decode($request_data['price_details']);

        $host_coupon_discount = (isset($price_details->host_coupon_discount) === true) ? $price_details->host_coupon_discount : 0;

        // Calculate GH Commission from Host.
        $gh_commission_from_host = 0;

        if (empty($request_data['commission_from_host']) === false && $request_data['commission_from_host'] > 0) {
            $gh_commission_from_host = (($price_details->host_fee * $request_data['commission_from_host']) / 100);
        }

        return ($price_details->host_fee - $gh_commission_from_host - $host_coupon_discount);

    }//end calculateHostAmount()


    /**
     * Calculate Booking all Discounts amount
     *
     * @param BookingRequest $booking_request Booking Request Object.
     *
     * @return array
     */
    public function getAllAppliedDiscount(BookingRequest $booking_request)
    {
        $price_details = json_decode($booking_request->price_details);

        $price_details = json_decode($booking_request->price_details);

        // Discount.
        $formatted_discount = (empty($price_details->discount) === false) ? Helper::getFormattedMoney($price_details->discount, $price_details->currency_code, true, false) : '';

        // Wallet Discount.
        $formatted_wallet_discount = (empty($price_details->wallet_money_applied) === false) ? Helper::getFormattedMoney($price_details->wallet_money_applied, $price_details->currency_code, true, false) : '';

        // Feathers Discount.
        $formatted_miles_discount = (empty($price_details->miles_applied) === false) ? Helper::getFormattedMoney(($price_details->miles_applied * $price_details->mile_conversion_rate), $price_details->currency_code, true, false) : '';

        // Coupon Discount.
        $formatted_coupon_discount = (empty($price_details->coupon_applied) === false) ? Helper::getFormattedMoney((float) $price_details->coupon_amount, $price_details->currency_code, true, false) : '';

        // Host Coupon Discount.
        // phpcs:ignore
        $formatted_host_coupon_discount = (empty($price_details->coupon_applied) === false && empty($price_details->host_coupon_amount) === false) ? Helper::getFormattedMoney((float) $price_details->host_coupon_amount, $price_details->currency_code, true, false) : '';

        // Meal Cost.
        $formatted_agent_meal_price = (empty($price_details->have_agent_meal) === false) ? Helper::getFormattedMoney((float) $price_details->agent_meal_price, $price_details->currency_code, true, false) : '';

        return [
            'formatted_discount'             => $formatted_discount,
            'formatted_wallet_discount'      => $formatted_wallet_discount,
            'formatted_miles_discount'       => $formatted_miles_discount,
            'formatted_coupon_discount'      => $formatted_coupon_discount,
            'formatted_host_coupon_discount' => $formatted_host_coupon_discount,
            'formatted_agent_meal_price'     => $formatted_agent_meal_price,
        ];

    }//end getAllAppliedDiscount()


    /**
     * Send New Request Emails
     *
     * @param BookingRequest $booking_request Booking Request Object.
     * @param string         $property_title  Property Title.
     * @param string         $host_email      Host Email.
     * @param string         $host_name       Host Full Name.
     * @param string         $traveller_name  Traveller Full Name.
     *
     * @return void
     */
    public function sendNewRequestEmailToHost(BookingRequest $booking_request, string $property_title, string $host_email, string $host_name, string $traveller_name)
    {
        $to_email         = $host_email;
        $host_name        = $host_name;
        $traveller_name   = $traveller_name;
        $property_hash_id = Helper::encodePropertyId($booking_request->pid);
        $property_title   = $property_title;
        // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$booking_request->pid], [], 1);

        $property_image = (array_key_exists($booking_request->pid, $property_images) === true) ? $property_images[$booking_request->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $price_details = json_decode($booking_request->price_details);
        // Calculate Amount.
        $formatted_amount = Helper::getFormattedMoney($this->getHostAmount($booking_request), $price_details->currency_code);

        $guests              = $booking_request->guests;
        $formatted_check_in  = Carbon::parse($booking_request->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($booking_request->to_date)->format('d-M-Y');

        // Accept String for Accept Url request_id,property_id,traveller id,action = 1 for accept request.
        $accept_hash = Helper::encodeArray([$booking_request->id, $booking_request->pid, $booking_request->traveller_id, 1]);

        // Accept String for Accept Url request_id,property_id,traveller id,action = 0 for reject request.
        $reject_hash = Helper::encodeArray([$booking_request->id, $booking_request->pid, $booking_request->traveller_id, 0]);

        $expiry_time = Helper::stringTimeFormattedString(strtotime($booking_request->approve_till) - strtotime($booking_request->created_at));

        $this->email_service->sendNewRequestEmailToHost(
            $to_email,
            $host_name,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $formatted_amount,
            $guests,
            $formatted_check_in,
            $formatted_check_out,
            $accept_hash,
            $reject_hash,
            $expiry_time
        );

    }//end sendNewRequestEmailToHost()


    /**
     * Send New Request Emails
     *
     * @param BookingRequest $booking_request Booking Request Object.
     * @param string         $property_title  Property Title.
     * @param string         $host_dial_code  Host Dial Code.
     * @param string         $host_contact    Host Contact Number.
     *
     * @return void
     */
    public function sendNewRequestSmsToHost(BookingRequest $booking_request, string $property_title, string $host_dial_code, string $host_contact)
    {
        if (empty($host_dial_code) === true || empty($host_contact) === true) {
            return;
        }

        $request_hash_id     = Helper::encodeBookingRequestId($booking_request->id);
        $property_title      = Helper::shortPropertyTitle($property_title);
        $guests              = $booking_request->guests;
        $units               = $booking_request->units;
        $formatted_check_in  = Carbon::parse($booking_request->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($booking_request->to_date)->format('d-M-Y');

        $price_details = json_decode($booking_request->price_details);
        // Calculate Amount.
        $formatted_amount = Helper::getFormattedMoney($this->getHostAmount($booking_request), $price_details->currency_code, true, false);
        $expiry_time      = Helper::stringTimeFormattedString(strtotime($booking_request->approve_till) - strtotime($booking_request->created_at));

        $this->sms_service->sendCreateNewRequestSmsToHost($host_dial_code, $host_contact, $request_hash_id, $property_title, $formatted_check_in, $formatted_check_out, $guests, $units, $formatted_amount, $expiry_time);

    }//end sendNewRequestSmsToHost()


    /**
     * Send New Booking Request Push Notifications
     *
     * @param BookingRequest $booking_request Booking Request Object.
     *
     * @return void
     */
    public function sendNewRequestPushNotifications(BookingRequest $booking_request)
    {
        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $this->push_notification->sendNewRequestPushNotificationsToHost($request_hash_id, $booking_request->host_id);

    }//end sendNewRequestPushNotifications()


    /**
     * Send Booking Cancel Emails.
     *
     * @param BookingRequest $booking_request Booking Request Object.
     * @param string         $property_title  Property Title.
     * @param string         $host_email      Host Email.
     * @param string         $host_name       Host Full Name.
     * @param string         $traveller_email Traveller Email.
     * @param string         $traveller_name  Traveller Full Name.
     * @param float          $refund_amount   Refund Amount.
     *
     * @return void
     */
    public function sendCancelBookingRequestEmails(BookingRequest $booking_request, string $property_title, string $host_email, string $host_name, string $traveller_email, string $traveller_name, float $refund_amount)
    {
        $host_name        = $host_name;
        $traveller_name   = $traveller_name;
        $property_hash_id = Helper::encodePropertyId($booking_request->pid);
        $property_title   = $property_title;
        // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$booking_request->pid], [], 1);

        $property_image = (array_key_exists($booking_request->pid, $property_images) === true) ? $property_images[$booking_request->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $guests              = $booking_request->guests;
        $formatted_check_in  = Carbon::parse($booking_request->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($booking_request->to_date)->format('d-M-Y');

        $this->email_service->sendCancelBookingRequestEmailToHost(
            $host_email,
            $host_name,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $guests,
            $formatted_check_in,
            $formatted_check_out
        );

        $this->email_service->sendCancelBookingRequestEmailToGuest(
            $traveller_email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $guests,
            $formatted_check_in,
            $formatted_check_out
        );

        if ($refund_amount > 0) {
            $price_details           = json_decode($booking_request->price_details);
            $formatted_refund_amount = Helper::getFormattedMoney($refund_amount, $price_details->currency_code);
            $this->email_service->sendCancelBookingRequestEmailToCustomerSupport(ADMIN_EMAILS_FOR_NOTIFICATIONS, $request_hash_id, $formatted_refund_amount);
        }

    }//end sendCancelBookingRequestEmails()


    /**
     * Send Booking Cancel Sms
     *
     * @param BookingRequest $booking_request     Booking Request Object.
     * @param string         $property_title      Property Title.
     * @param string         $host_dial_code      Host Dial Code.
     * @param string         $host_contact        Host Contact Number.
     * @param string         $traveller_dial_code Traveller Dial Code.
     * @param string         $traveller_contact   Traveller Contact Number.
     *
     * @return void
     */
    public function sendCancelBookingRequestSms(BookingRequest $booking_request, string $property_title, string $host_dial_code, string $host_contact, string $traveller_dial_code, string $traveller_contact)
    {
        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);
        $property_title  = Helper::shortPropertyTitle($property_title);

        // Send Sms to Host.
        if (empty($host_dial_code) === false && empty($host_contact) === false) {
            $this->sms_service->sendCancelBookingSmsToHost($host_dial_code, $host_contact, $request_hash_id, $property_title);
        }

        // Send Sms to Guest.
        if (empty($traveller_dial_code) === false && empty($traveller_contact) === false) {
            $this->sms_service->sendCancelBookingSmsToGuest($traveller_dial_code, $traveller_contact, $request_hash_id, $property_title);
        }

    }//end sendCancelBookingRequestSms()


    /**
     * Send Booking Cancel Push Notifications
     *
     * @param BookingRequest $booking_request Booking Request Object.
     *
     * @return void
     */
    public function sendCancelBookingRequestPushNotifications(BookingRequest $booking_request)
    {
        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        if ($booking_request->booking_status > BOOKED) {
            $this->push_notification->sendCancelBookingPushNotificationsToHost($request_hash_id, $booking_request->host_id);
        } else {
            $this->push_notification->sendCancelRequestPushNotificationsToHost($request_hash_id, $booking_request->host_id);
        }

    }//end sendCancelBookingRequestPushNotifications()


    /**
     * Send Approved Request Emails to Guest
     *
     * @param BookingRequest $booking_request Booking Request Object.
     * @param string         $property_title  Property Title.
     * @param string         $traveller_email Traveller Email.
     * @param string         $traveller_name  Traveller Full Name.
     *
     * @return void
     */
    public function sendBookingRequestApprovedEmailToGuest(BookingRequest $booking_request, string $property_title, string $traveller_email, string $traveller_name)
    {
        $to_email         = $traveller_email;
        $traveller_name   = $traveller_name;
        $property_hash_id = Helper::encodePropertyId($booking_request->pid);
        $property_title   = $property_title;
        // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$booking_request->pid], [], 1);

        $property_image = (array_key_exists($booking_request->pid, $property_images) === true) ? $property_images[$booking_request->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $price_details = json_decode($booking_request->price_details);

        // Calculate Amount.
        $formatted_amount = Helper::getFormattedMoney($price_details->payable_amount, $price_details->currency_code);

        $guests              = $booking_request->guests;
        $units               = $booking_request->units;
        $formatted_check_in  = Carbon::parse($booking_request->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($booking_request->to_date)->format('d-M-Y');

        // Cancel String for Cancel Url request_id,property_id,traveller id,action = 0 for cancel request.
        $cancel_hash = Helper::encodeArray([$booking_request->id, $booking_request->pid, $booking_request->traveller_id, 0]);

        // Calculate Hour for check day time or night time.
        $hours = Carbon::parse($booking_request->created_at)->setTimezone('Asia/Kolkata')->format('H');

        $expiry_time_in_seconds = ($hours >= 0 && $hours < 8) ? REQUEST_APPROVAL_NIGHT_TIMER : REQUEST_APPROVAL_DAY_TIMER;

        $expiry_time = Helper::stringTimeFormattedString($expiry_time_in_seconds);

        $this->email_service->sendBookingRequestApprovedEmailToGuest(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $property_image,
            $request_hash_id,
            $formatted_amount,
            $guests,
            $units,
            $formatted_check_in,
            $formatted_check_out,
            $cancel_hash,
            $expiry_time
        );

    }//end sendBookingRequestApprovedEmailToGuest()


    /**
     * Send  Approved Booking Request Notification to Guest
     *
     * @param BookingRequest $booking_request Booking Request Object.
     *
     * @return void
     */
    public function sendBookingRequestApprovedNotificationToGuest(BookingRequest $booking_request)
    {
        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $this->push_notification->sendApprovedRequestPushNotificationsToGuest($request_hash_id, $booking_request->traveller_id);

    }//end sendBookingRequestApprovedNotificationToGuest()


    /**
     * Send Approved Booking Request Sms to Guest
     *
     * @param BookingRequest $booking_request     Booking Request Object.
     * @param string         $property_title      Property Title.
     * @param string         $traveller_dial_code Traveller Dial Code.
     * @param string         $traveller_contact   Traveller Contact Number.
     *
     * @return void
     */
    public function sendBookingRequestApprovedSmsToGuest(BookingRequest $booking_request, string $property_title, string $traveller_dial_code, string $traveller_contact)
    {
        if (empty($traveller_dial_code) === true || empty($traveller_contact) === true) {
            return;
        }

        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);
        $property_title  = Helper::shortPropertyTitle($property_title);

        // Calculate Hour for check day time or night time.
        $hours = Carbon::parse($booking_request->created_at)->setTimezone('Asia/Kolkata')->format('H');

        $expiry_time_in_seconds = ($hours >= 0 && $hours < 8) ? REQUEST_APPROVAL_NIGHT_TIMER : REQUEST_APPROVAL_DAY_TIMER;

        $expiry_time = Helper::stringTimeFormattedString($expiry_time_in_seconds);

        // Generate Payment Url.
        $payment_url = Helper::getFirebaseShortUrl(MAILER_SITE_URL.'/payment/confirm/'.$request_hash_id.'?type=request');

        $this->sms_service->sendBookingRequestApprovedSmsToGuest($traveller_dial_code, $traveller_contact, $request_hash_id, $property_title, $expiry_time, $payment_url);

    }//end sendBookingRequestApprovedSmsToGuest()


    /**
     * Send Rejected Request Emails to Guest
     *
     * @param BookingRequest $booking_request Booking Request Object.
     * @param string         $property_title  Property Title.
     * @param string         $traveller_email Traveller Email.
     * @param string         $traveller_name  Traveller Full Name.
     *
     * @return void
     */
    public function sendBookingRequestRejectedEmailToGuest(BookingRequest $booking_request, string $property_title, string $traveller_email, string $traveller_name)
    {
        $to_email           = $traveller_email;
        $traveller_name     = $traveller_name;
        $property_hash_id   = Helper::encodePropertyId($booking_request->pid);
        $property_title     = $property_title;
        $similar_properties = [];
        $search_url         = '';

        // Get Similar Property Data.
        $start_date = Carbon::parse($booking_request->from_date);
        $end_date   = Carbon::parse($booking_request->to_date);
        $no_of_days = $start_date->diffInDays($end_date, false);

        $check_booking_in_last_one_day = BookingRequest::getLastOneDayBookingsOfTraveller($booking_request->traveller_id);

        if ($check_booking_in_last_one_day === 0) {
            // Get property data.
            $property = Property::getPropertyDetailsForPreviewPageById($booking_request->pid, $booking_request->guests, $booking_request->units, false);

            $price_details = json_decode($booking_request->price_details);

            $similar_properties = SimilarListingService::getSimilarProperties(
                [
                    'property_id'    => $booking_request->pid,
                    'start_date'     => $start_date->toDateString(),
                    'end_date'       => $end_date->toDateString(),
                    'days'           => $no_of_days,
                    'guests'         => $booking_request->guests,
                    'units'          => $booking_request->units,
                    'currency'       => DEFAULT_CURRENCY,
                    'offset'         => 0,
                    'limit'          => 3,
                    'latitude'       => $property['latitude'],
                    'longitude'      => $property['longitude'],
                    'state'          => $property['state'],
                    'country'        => $property['country'],
                    'property_type'  => $property['property_type'],
                    'payable_amount' => $price_details->payable_amount,
                    'headers'        => [],
                    'user_id'        => $booking_request->traveller_id,
                ]
            );

            $min_price = round((85 / 100) * $price_details->payable_amount);
            $max_price = round((115 / 100) * $price_details->payable_amount);

            // phpcs:ignore
            $search_url = MAILER_SITE_URL.'/search/s?location='.$property['state'].', '.CountryCodeMapping::getCountryName($property['country']).'&state='.$property['state'].'&country='.$property['country'].'&minvalue='.$min_price.'&maxvalue='.$max_price.'&checkin='.$start_date->format('Y-m-d').'&checkout='.$end_date->format('Y-m-d').'&guests='.$booking_request->guests.'&property_type='.$property['property_type'].'&utm_source=booking_mailer&utm_medium=email&utm_campaign=search_suggestions';
        }//end if

        $this->email_service->sendBookingRequestRejectedEmailToGuest(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $property_title,
            $similar_properties,
            $search_url
        );

    }//end sendBookingRequestRejectedEmailToGuest()


    /**
     * Send Rejected Booking Request Sms to Guest
     *
     * @param BookingRequest $booking_request     Booking Request Object.
     * @param string         $property_title      Property Title.
     * @param string         $traveller_dial_code Traveller Dial Code.
     * @param string         $traveller_contact   Traveller Contact Number.
     *
     * @return void
     */
    public function sendBookingRequestRejectedSmsToGuest(BookingRequest $booking_request, string $property_title, string $traveller_dial_code, string $traveller_contact)
    {
        if (empty($traveller_dial_code) === true || empty($traveller_contact) === true) {
            return;
        }

        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);
        $property_title  = Helper::shortPropertyTitle($property_title);

        $this->sms_service->sendBookingRequestRejectedSmsToGuest($traveller_dial_code, $traveller_contact, $request_hash_id, $property_title);

    }//end sendBookingRequestRejectedSmsToGuest()


    /**
     * Send  Reject Booking Request Notification to Guest
     *
     * @param BookingRequest $booking_request Booking Request Object.
     *
     * @return void
     */
    public function sendBookingRequestRejectNotificationToGuest(BookingRequest $booking_request)
    {
        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $this->push_notification->sendRejectRequestPushNotificationsToGuest($request_hash_id, $booking_request->traveller_id);

    }//end sendBookingRequestRejectNotificationToGuest()


    /**
     * Send Booking Emails to Traveller
     *
     * @param BookingRequest $booking_request Booking Request Object.
     * @param float          $balance_fee     Balance Fee.
     * @param string         $property_title  Property Title.
     * @param string         $traveller_email Traveller Email.
     * @param string         $traveller_name  Traveller Full Name.
     * @param boolean        $second_payment  Set to true when user makes payment after request become booking/ second payment.
     *
     * @return void
     */
    public function sendBookingEmailToTraveller(BookingRequest $booking_request, float $balance_fee, string $property_title, string $traveller_email, string $traveller_name, bool $second_payment=false)
    {
        $to_email         = $traveller_email;
        $property_hash_id = Helper::encodePropertyId($booking_request->pid);
        $property_title   = $property_title;
        // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$booking_request->pid], [], 1);

        $property_image = (array_key_exists($booking_request->pid, $property_images) === true) ? $property_images[$booking_request->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $guests              = $booking_request->guests;
        $formatted_check_in  = Carbon::parse($booking_request->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($booking_request->to_date)->format('d-M-Y');

        $invoice_name         = 'T_'.$request_hash_id.'.pdf';
        $invoice_url          = INVOICE_PDF_DIR.$invoice_name;
        $check_invoice_exists = Helper::remoteFileExists($invoice_url);

        if ($second_payment === true || $check_invoice_exists === false) {
            $booking  = Booking::getBookingForRequestId($booking_request->id);
            $property = Property::getPropertyById($booking_request->pid);

            if (empty($booking) === false && empty($property) === false) {
                $invoices = $this->createTravellerInvoice($booking_request, $booking, $property);
            }
        }

        // phpcs:ignore
        if ($second_payment === true && empty($balance_fee) === true) {
            $this->email_service->sendPartialBookingEmailToTraveller(
                $to_email,
                $traveller_name,
                $property_hash_id,
                $property_title,
                $property_image,
                $request_hash_id,
                $guests,
                $formatted_check_in,
                $formatted_check_out,
                $invoice_url,
                $invoice_name
            );
        } else {
            $this->email_service->sendFullyBookingEmailToTraveller(
                $to_email,
                $traveller_name,
                $property_hash_id,
                $property_title,
                $property_image,
                $request_hash_id,
                $guests,
                $formatted_check_in,
                $formatted_check_out,
                $invoice_url,
                $invoice_name
            );
        }//end if

    }//end sendBookingEmailToTraveller()


    /**
     * Send Booking Emails to Host
     *
     * @param BookingRequest $booking_request Booking Request Object.
     * @param float          $balance_fee     Balance Fee.
     * @param string         $property_title  Property Title.
     * @param string         $host_email      Host Email.
     * @param string         $host_name       Host Full Name.
     * @param string         $traveller_name  Traveller Full Name.
     * @param boolean        $second_payment  Set to true when user makes payment after request become booking/ second payment.
     *
     * @return void
     */
    public function sendBookingEmailToHost(BookingRequest $booking_request, float $balance_fee, string $property_title, string $host_email, string $host_name, string $traveller_name, bool $second_payment=false)
    {
        $to_email         = $host_email;
        $property_hash_id = Helper::encodePropertyId($booking_request->pid);
        $property_title   = $property_title;
        // Get first property image to display.
        $property_images = PropertyImage::getPropertiesImagesByIds([$booking_request->pid], [], 1);

        $property_image = (array_key_exists($booking_request->pid, $property_images) === true) ? $property_images[$booking_request->pid][0]['image'] : MAILER_ASSETS_URL.'default_property.png';

        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $guests              = $booking_request->guests;
        $formatted_check_in  = Carbon::parse($booking_request->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($booking_request->to_date)->format('d-M-Y');

        $invoice_name         = 'H_'.$request_hash_id.'.pdf';
        $invoice_url          = INVOICE_PDF_DIR.$invoice_name;
        $check_invoice_exists = Helper::remoteFileExists($invoice_url);

        if ($second_payment === true || $check_invoice_exists === false) {
            $booking  = Booking::getBookingForRequestId($booking_request->id);
            $property = Property::getPropertyById($booking_request->pid);

            if (empty($booking) === false && empty($property) === false) {
                $invoices = $this->createHostInvoice($booking_request, $booking, $property, $traveller_name);
            }
        }

        if ($second_payment === true && empty($balance_fee) === true) {
            $this->email_service->sendPartialBookingEmailToHost(
                $to_email,
                $host_name,
                $traveller_name,
                $property_hash_id,
                $property_title,
                $property_image,
                $request_hash_id,
                $guests,
                $formatted_check_in,
                $formatted_check_out,
                $invoice_url,
                $invoice_name
            );
        } else {
            $this->email_service->sendFullyBookingEmailToHost(
                $to_email,
                $host_name,
                $traveller_name,
                $property_hash_id,
                $property_title,
                $property_image,
                $request_hash_id,
                $guests,
                $formatted_check_in,
                $formatted_check_out,
                $invoice_url,
                $invoice_name
            );
        }//end if

    }//end sendBookingEmailToHost()


    /**
     * Send Add Payout Detail Emails to Host
     *
     * @param integer $property_id    Property Id.
     * @param string  $property_title Property Title.
     * @param integer $host_id        Host Id.
     * @param string  $host_email     Host Email.
     * @param string  $host_name      Host Full Name.
     *
     * @return void
     */
    public function sendAddPayoutDetailEmailToHost(int $property_id, string $property_title, int $host_id, string $host_email, string $host_name)
    {
        $to_email         = $host_email;
        $property_hash_id = Helper::encodePropertyId($property_id);
        $property_title   = $property_title;

        $user_bank_detail = UserBillingInfo::getUserBankDetail($host_id);

        if (empty($user_bank_detail) === false) {
            return;
        }

        $this->email_service->sendBookedPayoutDetailToHost(
            $to_email,
            $host_name,
            $property_hash_id,
            $property_title
        );

    }//end sendAddPayoutDetailEmailToHost()


    /**
     * Send Booking Emails to Admin
     *
     * @param BookingRequest $booking_request Booking Request Object.
     * @param string         $traveller_name  Traveller Full Name.
     *
     * @return void
     */
    public function sendBookingEmailToAdmin(BookingRequest $booking_request, string $traveller_name)
    {
        $to_email          = ADMIN_EMAILS_FOR_NOTIFICATIONS;
        $property_hash_id  = Helper::encodePropertyId($booking_request->pid);
        $request_hash_id   = Helper::encodeBookingRequestId($booking_request->id);
        $traveller_hash_id = Helper::encodeUserId($booking_request->traveller_id);

        $price_details = json_decode($booking_request->price_details);

        // Calculate Amount.
        $formatted_amount = Helper::getFormattedMoney($price_details->payable_amount, $price_details->currency_code);

        $units = $booking_request->units;

        $formatted_check_in  = Carbon::parse($booking_request->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($booking_request->to_date)->format('d-M-Y');

        $rm_email = RelationshipManager::getRMEmail($booking_request->pid);

        if (empty($rm_email) === false) {
            $to_email = array_merge($to_email, [$rm_email->email]);
        }

        $this->email_service->sendBookingEmailToAdmin(
            $to_email,
            $traveller_name,
            $property_hash_id,
            $request_hash_id,
            $traveller_hash_id,
            $formatted_amount,
            $units,
            $formatted_check_in,
            $formatted_check_out
        );

    }//end sendBookingEmailToAdmin()


    /**
     * Send Booking Sms to Traveller
     *
     * @param BookingRequest $booking_request     Booking Request Object.
     * @param float          $balance_fee         Balance Fee.
     * @param string         $property_title      Property Title.
     * @param string         $traveller_dial_code Traveller Dial Code.
     * @param string         $traveller_contact   Traveller Contact.
     * @param string         $traveller_name      Traveller Full Name.
     * @param string         $host_name           Host Full Name.
     * @param boolean        $second_payment      Set to true when user makes payment after request become booking/ second payment.
     *
     * @return void
     */
    public function sendBookingSmsToTraveller(BookingRequest $booking_request, float $balance_fee, string $property_title, string $traveller_dial_code, string $traveller_contact, string $traveller_name, string $host_name, bool $second_payment=false)
    {
        if (empty($traveller_dial_code) === true || empty($traveller_contact) === true) {
            return;
        }

        $dial_code       = $traveller_dial_code;
        $to_no           = $traveller_contact;
        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);
        $property_title  = Helper::shortPropertyTitle($property_title);

        $price_details = json_decode($booking_request->price_details);

        // Calculate Amount.
        $payable_amount = Helper::getFormattedMoney($price_details->payable_amount, $price_details->currency_code, true, false);

        $guests              = $booking_request->guests;
        $units               = $booking_request->units;
        $formatted_check_in  = Carbon::parse($booking_request->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($booking_request->to_date)->format('d-M-Y');

        if ($second_payment === true && empty($balance_fee) === true) {
            $this->sms_service->sendPartialBookingSmsToGuest(
                $dial_code,
                $to_no,
                $request_hash_id,
                $payable_amount
            );
        } else {
            $this->sms_service->sendBookingSmsToGuest(
                $dial_code,
                $to_no,
                $request_hash_id,
                $payable_amount,
                $traveller_name,
                $host_name,
                $property_title,
                $formatted_check_in,
                $formatted_check_out,
                $guests,
                $units
            );
        }//end if

    }//end sendBookingSmsToTraveller()


    /**
     * Send Booking Sms to Host
     *
     * @param BookingRequest $booking_request Booking Request Object.
     * @param string         $host_dial_code  Host Dial Code.
     * @param string         $host_contact    Host Contact.
     *
     * @return void
     */
    public function sendBookingSmsToHost(BookingRequest $booking_request, string $host_dial_code, string $host_contact)
    {
        if (empty($host_dial_code) === true || empty($host_contact) === true) {
            return;
        }

        $dial_code           = $host_dial_code;
        $to_no               = $host_contact;
        $request_hash_id     = Helper::encodeBookingRequestId($booking_request->id);
        $guests              = $booking_request->guests;
        $units               = $booking_request->units;
        $formatted_check_in  = Carbon::parse($booking_request->from_date)->format('d-M-Y');
        $formatted_check_out = Carbon::parse($booking_request->to_date)->format('d-M-Y');

        $this->sms_service->sendBookingSmsToHost(
            $dial_code,
            $to_no,
            $request_hash_id,
            $formatted_check_in,
            $formatted_check_out,
            $guests,
            $units
        );

    }//end sendBookingSmsToHost()


    /**
     * Send Property Direction Sms to Guest
     *
     * @param string $property_title      Property Title.
     * @param float  $latitude            Property Latitude.
     * @param float  $longitude           Proeprty Longitude.
     * @param string $traveller_dial_code Traveller Dial Code.
     * @param string $traveller_contact   Traveller Contact.
     *
     * @return void
     */
    public function sendPropertyDirectionSmsToGuest(string $property_title, float $latitude, float $longitude, string $traveller_dial_code, string $traveller_contact)
    {
        if (empty($traveller_dial_code) === true || empty($traveller_contact) === true) {
            return;
        }

        $dial_code      = $traveller_dial_code;
        $to_no          = $traveller_contact;
        $property_title = Helper::shortPropertyTitle($property_title);
        $direction_url  = GOOGLE_DIRECTION_URL.$latitude.','.$longitude;

        // Generate Property Direction Short Url.
        $direction_url = Helper::getFirebaseShortUrl($direction_url);

        $this->sms_service->sendPropertyDirectionSmsToGuest(
            $dial_code,
            $to_no,
            $direction_url,
            $property_title
        );

    }//end sendPropertyDirectionSmsToGuest()


    /**
     * Booking Communication
     *
     * @param BookingRequest $booking_request                  Booking Request.
     * @param float          $balance_fee                      Balance fee.
     * @param Property       $property                         Proeprty.
     * @param User           $host                             Host.
     * @param User           $traveller                        Traveller.
     * @param boolean        $send_invoice_mail_only_traveller Send invoice mail only traveller flag.
     * @param boolean        $second_payment                   Set to true when user makes payment after request become booking/ second payment.
     *
     * @return void
     */
    public function sendBookingNotifications(BookingRequest $booking_request, float $balance_fee, Property $property, User $host, User $traveller, bool $send_invoice_mail_only_traveller=false, bool $second_payment=false)
    {
        $this->sendBookingEmailToTraveller($booking_request, $balance_fee, $property->title, $traveller->email, $traveller->getUserFullName(), $second_payment);

        if ($send_invoice_mail_only_traveller === false) {
            $this->sendBookingEmailToHost($booking_request, $balance_fee, $property->title, $host->email, $host->getUserFullName(), $traveller->getUserFullName(), $second_payment);

            $this->sendAddPayoutDetailEmailToHost($property->id, $property->title, $host->id, $host->email, $host->getUserFullName());

            $this->sendBookingEmailToAdmin($booking_request, $traveller->getUserFullName());

            $this->sendBookingSmsToTraveller($booking_request, $balance_fee, $property->title, $traveller->dial_code, $traveller->contact, $traveller->getUserFullName(), $host->getUserFullName(), $second_payment);

            $this->sendBookingSmsToHost($booking_request, $host->dial_code, $host->contact);

            $this->sendPropertyDirectionSmsToGuest($property->title, $property->latitude, $property->longitude, $traveller->dial_code, $traveller->contact);
        }

    }//end sendBookingNotifications()


    /**
     * Send  Booking  Notification to Guest
     *
     * @param BookingRequest $booking_request Booking Request Object.
     *
     * @return void
     */
    public function sendBookingPushNotificationsToGuest(BookingRequest $booking_request)
    {
        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $this->push_notification->sendBookingNotificationToGuest($request_hash_id, $booking_request->traveller_id);

    }//end sendBookingPushNotificationsToGuest()


    /**
     * Send  Booking  Notification to Host
     *
     * @param BookingRequest $booking_request Booking Request Object.
     *
     * @return void
     */
    public function sendBookingPushNotificationsToHost(BookingRequest $booking_request)
    {
        $request_hash_id = Helper::encodeBookingRequestId($booking_request->id);

        $this->push_notification->sendBookingNotificationToHost($request_hash_id, $booking_request->host_id);

    }//end sendBookingPushNotificationsToHost()


    /**
     * Function to get prive booking list.
     *
     * @param integer $prive_owner_id Prive Owner id.
     * @param integer $property_id    Property id.
     * @param string  $start_date     Start date for date range.
     * @param string  $end_date       End date for date range.
     * @param integer $offset         Offset.
     * @param integer $total          Total.
     * @param integer $sort           Sort Order.
     * @param string  $sort_order     Sorting Order.
     * @param integer $booking_status Booking status  for filter.
     *
     * @return array.
     */
    public function getPriveBookings(int $prive_owner_id, int $property_id, string $start_date, string $end_date, int $offset, int $total, int $sort, string $sort_order, int $booking_status) : array
    {
        $booking_requests = [];

        // Today's date.
        $today = Carbon::now('Asia/Kolkata');

        $booking_request = new BookingRequest;
        $booking_list    = $booking_request->getPriveBookings($prive_owner_id, $property_id, $offset, $total, $sort, $sort_order, $start_date, $end_date, $booking_status);

        foreach ($booking_list as $booking) {
            // Get timeline diff from today to from_date and to_date.
            $start_date_obj                 = Carbon::parse($booking['from_date']);
            $end_date_obj                   = Carbon::parse($booking['to_date']);
            $host_amount                    = $booking['host_fee'];
            $properly_commission_percentage = $booking['properly_commission'];
            $host_amount_after_properly_commision = round($host_amount - (($host_amount * $properly_commission_percentage) / 100));

            // Get booking status to display (along with class).
            if (in_array($booking['booking_status'], [BOOKED, OVERBOOKED]) === true) {
                $booking_status = [
                    'text'   => 'Booked',
                    'class'  => 'booked',
                    'status' => 1,
                ];
            } else {
                $booking_status = [
                    'text'   => 'Cancelled',
                    'class'  => 'cancelled',
                    'status' => 2,
                ];
            }

            // Get tile structure for trip page.
            $booking_tile = PropertyTileService::priveBookingListStructure(
                [
                    'title'          => $booking['title'],
                    'request_id'     => $booking['id'],
                    'guest_name'     => $booking['guest_name'],
                    'guests'         => $booking['guests'],
                    'checkin'        => $start_date_obj->format('dS M Y'),
                    'checkout'       => $end_date_obj->format('dS M Y'),
                    'booking_status' => $booking_status,
                    'currency'       => $booking['currency'],
                    'host_amount'    => $host_amount_after_properly_commision,
                    'units'          => $booking['units'],
                    'bedroom'        => $booking['bedroom'],
                ]
            );

            array_push($booking_requests, $booking_tile);
        }//end foreach

        return $booking_requests;

    }//end getPriveBookings()


    /**
     * Function to get prive manager booking list.
     *
     * @param integer $prive_manager_id Prive Manager id.
     * @param integer $offset           Offset.
     * @param integer $total            Total.
     * @param array   $sort             Sort Order.
     * @param array   $filter           All Filters array.
     * @param string  $search           Search query.
     *
     * @return array.
     */
    public function getPriveManagerBookings(int $prive_manager_id, int $offset=0, int $total=150, array $sort=[], array $filter=[], string $search='') : array
    {
        $booking_requests = [];

        // Today's date.
        $today = Carbon::now('Asia/Kolkata');

        if (empty($search) === false) {
            if (strlen($search) === HASH_LENGTH_FOR_BOOKING_REQUEST_ID) {
                $request_id = Helper::decodeBookingRequestId(strtoupper($search));

                if (empty($request_id) === false) {
                    $search = $request_id;
                }
            }
        }

        $booking_request = new BookingRequest;
        $booking_info    = $booking_request->getPriveManagerBookings($prive_manager_id, $offset, $total, $sort, $filter, $search);

        foreach ($booking_info['data'] as $booking) {
            // Get timeline diff from today to from_date and to_date.
            $start_date_obj          = Carbon::parse($booking['from_date']);
            $checkin_date_formatted  = $start_date_obj->format('jS F Y');
            $end_date_obj            = Carbon::parse($booking['to_date']);
            $checkout_date_formatted = $end_date_obj->format('jS F Y');

            $no_of_days_from_checkout = $today->diffInDays($end_date_obj, false);

            // Get booking status to display (along with class).
            if (in_array($booking['booking_status'], [BOOKED, OVERBOOKED]) === true) {
                $booking_status = [
                    'text'   => 'Confirmed',
                    'class'  => 'confirmed',
                    'status' => 1,
                ];
            } else {
                $booking_status = [
                    'text'   => 'Cancelled',
                    'class'  => 'cancelled',
                    'status' => 2,
                ];
            }

            // Get Checked-in status.
            if ($booking['checkedin_status'] === PRIVE_MANAGER_CANCELLED) {
                $checked_status = [
                    'text'   => 'Cancelled',
                    'class'  => 'cancelled',
                    'status' => PRIVE_MANAGER_CANCELLED,
                ];
            } else if ($booking['checkedin_status'] === PRIVE_MANAGER_UPCOMING) {
                $checked_status = [
                    'text'   => 'Upcoming',
                    'class'  => 'upcoming',
                    'status' => PRIVE_MANAGER_UPCOMING,
                ];
            } else if ($booking['checkedin_status'] === PRIVE_MANAGER_CHECKEDIN) {
                $checked_status = [
                    'text'   => 'Ongoing',
                    'class'  => 'ongoing',
                    'status' => PRIVE_MANAGER_CHECKEDIN,
                ];
            } else if ($booking['checkedin_status'] === PRIVE_MANAGER_CHECKEDOUT) {
                $checked_status = [
                    'text'   => 'Due out',
                    'class'  => 'due_out',
                    'status' => PRIVE_MANAGER_CHECKEDOUT,
                ];
            } else if ($booking['checkedin_status'] === PRIVE_MANAGER_NO_SHOW) {
                $checked_status = [
                    'text'   => 'No show',
                    'class'  => 'no_show',
                    'status' => PRIVE_MANAGER_NO_SHOW,
                ];
            } else {
                $checked_status = [
                    'text'   => 'Completed',
                    'class'  => 'completed',
                    'status' => PRIVE_MANAGER_COMPLETED,
                ];
            }//end if

            $payment_option_text = (empty(PAYMENT_OPTION_TEXT[$booking['payment_option']]) === false && empty(PAYMENT_OPTION_TEXT[$booking['payment_option']]['text']) === false) ? PAYMENT_OPTION_TEXT[$booking['payment_option']]['text'] : '';

            $price_detail_data = json_decode($booking['price_details'], true);

            $properly_title = $booking['title'];

            if (empty($booking['properly_title']) === false) {
                $properly_title = $booking['properly_title'];
            }

            // Get tile structure for trip page.
            $booking_tile = PropertyTileService::priveManagerBookingListStructure(
                [
                    'request_id'                  => $booking['id'],
                    'guests'                      => $booking['guests'],
                    'payable_amount'              => $price_detail_data['payable_amount'],
                    'currency'                    => $booking['currency'],
                    'booking_status'              => $booking_status,
                    'checkedin_status'            => $checked_status,
                    'checkin'                     => $start_date_obj->format('d-m-Y'),
                    'checkout'                    => $end_date_obj->format('d-m-Y'),
                    'checkin_formatted'           => $checkin_date_formatted,
                    'checkout_formatted'          => $checkout_date_formatted,
                    'property_id'                 => $booking['property_id'],
                    'title'                       => $properly_title,
                    'guest_id'                    => $booking['guest_id'],
                    'guest_name'                  => $booking['guest_name'],
                    'guest_last_name'             => $booking['guest_last_name'],
                    'guest_verified'              => $booking['guest_verified'],
                    'country'                     => $booking['country'],
                    'city'                        => $booking['city'],
                    'state'                       => $booking['state'],
                    'area'                        => $booking['area'],
                    'property_type_name'          => $booking['property_type_name'],
                    'room_type_name'              => $booking['room_type_name'],
                    'checkin_status'              => $booking['checkin_status'],
                    'checkout_status'             => $booking['checkout_status'],
                    'no_show'                     => $booking['no_show'],
                    'payment_option'              => $payment_option_text,
                    'pending_payment'             => ($booking['balance_fee'] > 0) ? 1 : 0,
                    'manager_primary_contact'     => $booking['manager_primary_contact'],
                    'manager_secondary_contact'   => $booking['manager_secondary_contact'],
                    'traveller_primary_contact'   => $booking['traveller_primary_contact'],
                    'traveller_secondary_contact' => $booking['traveller_secondary_contact'],
                    'traveller_email'             => $booking['traveller_email'],
                    'expected_checkin_datetime'   => $booking['expected_checkin_datetime'],
                    'expected_checkout_datetime'  => $booking['expected_checkout_datetime'],
                    'no_show_reason_id'           => $booking['no_show_reason_id'],

                ]
            );

            array_push($booking_requests, $booking_tile);
        }//end foreach

        return [
            'data'  => $booking_requests,
            'count' => $booking_info['count'],
        ];

    }//end getPriveManagerBookings()


    /**
     * Function to get prive manager booking detail.
     *
     * @param integer $prive_manager_id      Prive Manager id.
     * @param integer $request_id            Request Id.
     * @param boolean $ongoing_upcoming_trip Ongoing Upcoming Trip.
     *
     * @return array.
     */
    public function getPriveManagerBookingDetail(int $prive_manager_id, int $request_id, bool $ongoing_upcoming_trip=false) : array
    {
        $booking_request = new BookingRequest;
        $booking_detail  = $booking_request->getPriveManagerBookingDetail($prive_manager_id, $request_id, $ongoing_upcoming_trip);

        return $booking_detail;

    }//end getPriveManagerBookingDetail()


    /**
     * Function to get prive manager booking detail.
     *
     * @param integer $request_id Request Id.
     * @param array   $params     Params.
     *
     * @return boolean
     */
    public function savePriveManagerCheckedInStatus(int $request_id, array $params) : bool
    {
        $booking = new Booking;

        $save_status = false;

        switch ($params['status']) {
            case PRIVE_MANAGER_CHECKEDIN:
                $save_status = $booking->saveCheckinStatus($request_id);
            break;

            case PRIVE_MANAGER_CHECKEDOUT:
                $save_status = $booking->saveCheckoutStatus($request_id);
            break;

            case PRIVE_MANAGER_NO_SHOW:
                $save_status = $booking->saveNoshowStatus($request_id);

                // Save Data in prive operations.
                $prive_operation        = new PriveOperations;
                $prive_operation_object = $prive_operation->saveNoShow($request_id, $params['reason_id'], (isset($params['comment']) === true) ? $params['comment'] : null);
            break;

            default:
                // No code.
            break;
        }//end switch

        return $save_status;

    }//end savePriveManagerCheckedInStatus()


    /**
     * Function to save prive booking operation data.
     *
     * @param integer $prive_manager_id Prive Manager Id.
     * @param integer $request_id       Request Id.
     * @param array   $params           Params.
     *
     * @return boolean
     */
    public function savePriveBookingOperation(int $prive_manager_id, int $request_id, array $params) : bool
    {
        $prive_operation = new PriveOperations;

        if (empty($params['operational_note']) === false) {
            RmBookingRemark::saveBookingNotes($request_id, $prive_manager_id, $params['operational_note'], 2);
        }

        if (empty($params['managerial_note']) === false) {
            RmBookingRemark::saveBookingNotes($request_id, $prive_manager_id, $params['managerial_note'], 1);
        }

        if (empty($params['expected_checkin']) === false) {
            $expected_checkin = Carbon::parse(trim($params['from_date']).' '.$params['expected_checkin'])->toDateTimeString();
            $prive_operation->saveExpectedCheckinDatetime($request_id, $expected_checkin);
            $update_task = ProperlyTask::updateTaskByEntityIdAndType($request_id, TASK_TYPE_CHECKIN, ['run_at' => $expected_checkin]);
        }

        if (empty($params['expected_checkout']) === false) {
            $expected_checkout = Carbon::parse(trim($params['to_date']).' '.$params['expected_checkout'])->toDateTimeString();
            $prive_operation->saveExpectedCheckoutDatetime($request_id, $expected_checkout);
            $update_task = ProperlyTask::updateTaskByEntityIdAndType($request_id, TASK_TYPE_CHECKOUT, ['run_at' => $expected_checkout]);
        }

        return true;

    }//end savePriveBookingOperation()


    /**
     * Function to map manager and travller contact for calling.
     *
     * @param integer $request_id   Request Id.
     * @param string  $contact_from Contact From.
     * @param string  $contact_to   Contact To.
     *
     * @return string
     */
    public function getBookingContactForCalling(int $request_id, string $contact_from, string $contact_to) : string
    {
        $ivr_contact_mapping = new IvrContactMapping;

        $did_number = IVR_DID_NUMBER_LIST_FOR_TRAVELLER_CONTACT[mt_rand(0, (count(IVR_DID_NUMBER_LIST_FOR_TRAVELLER_CONTACT) - 1))];

        $mappings = $ivr_contact_mapping->saveContactMapping($did_number, $request_id, $contact_from, $contact_to);

        // Define contact.
        $contact  = '';
        $std_code = '0124';

        if (empty($mappings) === false) {
            $contact = $std_code.$mappings->did_number;
        }

        return $contact;

    }//end getBookingContactForCalling()


    /**
     * Function to get Account detail for Cash collection of booking.
     *
     * @param integer $request_id     Request Id.
     * @param integer $property_id    Property Id.
     * @param integer $traveller_id   Traveller Id.
     * @param string  $traveller_name Traveller Name.
     *
     * @return array
     */
    public function getBookingSmartCashCollectionData(int $request_id, int $property_id, int $traveller_id, string $traveller_name) : array
    {
        $virtual_account = new VirtualAccount;

        $account_details = $virtual_account->getBookingVirtualAccountDetails($request_id);

        if (empty($account_details) === true) {
            $account_details = $this->createBookingSmartCashCollectionData($request_id, $property_id, $traveller_id, $traveller_name);

            if (empty($account_details) === true) {
                return [];
            }
        }

        $account_data = json_decode($account_details->json_details, true);

        return [
            'name'           => $account_data['receivers'][0]['name'],
            'account_number' => $account_data['receivers'][0]['account_number'],
            'ifsc'           => $account_data['receivers'][0]['ifsc'],
        ];

    }//end getBookingSmartCashCollectionData()


    /**
     * Function to create Account detail for Cash collection of booking.
     *
     * @param integer $request_id     Request Id.
     * @param integer $property_id    Property Id.
     * @param integer $traveller_id   Traveller Id.
     * @param string  $traveller_name Traveller Name.
     *
     * @return object
     */
    private function createBookingSmartCashCollectionData(int $request_id, int $property_id, int $traveller_id, string $traveller_name)
    {
        $virtual_account_detail = Booking::createRazorPayVirtualAccount(
            $request_id,
            [
                'traveller_name' => $traveller_name,
                'traveller_id'   => $traveller_id,
                'property_id'    => $property_id,
            ]
        );

        if (empty($virtual_account_detail) === true) {
            return (object) [];
        }

        $virtual_account = new VirtualAccount;

        $account_details = $virtual_account->saveVirtualAccount($request_id, $virtual_account_detail->id, $virtual_account_detail->toArray());

        if (empty($account_details) === true) {
            return (object) [];
        }

        return $account_details;

    }//end createBookingSmartCashCollectionData()


    /**
     * Function to get prive invoice list.
     *
     * @param integer $prive_owner_id Prive Owner id.
     * @param string  $month_year     Month_year.
     * @param integer $offset         Offset.
     * @param integer $total          Total.
     * @param array   $property_ids   Property id.
     *
     * @return array.
     */
    public function getInvoice(int $prive_owner_id, string $month_year, int $offset, int $total, array $property_ids)
    {
         $invoice_tile    = [];
         $booking_request = new BookingRequest;
         $sum             = 0;

        $date_month_year = explode('-', $month_year);
        $month           = $date_month_year[0];
        $year            = '20'.$date_month_year[1];

        $month_start_date = Carbon::createFromDate($year, $month, 1);

        $bookings_start_date             = $month_start_date->toDateString();
        $bookings_end_date               = $month_start_date->endOfMonth()->toDateString();
        $invoices_on_checkout_start_date = config('gh.properly.host_payout_based_on_checkout_start_date');

        if ($year.'-'.$month >= '2019-11') {
            $invoices = $booking_request->getInvoiceListByCheckOut($prive_owner_id, $bookings_start_date, $bookings_end_date, $invoices_on_checkout_start_date, $offset, $total, $property_ids);
        } else {
            $invoices = $booking_request->getInvoiceListByCheckIn($prive_owner_id, $bookings_start_date, $bookings_end_date, $offset, $total, $property_ids);
        }

        $currency = 'INR';

        foreach ($invoices['data_pagination'] as $invoice) {
            $invoice_date                   = Carbon::parse($invoice['invoice_date']);
            $price_details                  = json_decode($invoice['price_details']);
            $properly_commission_percentage = $invoice['properly_commission'];
            $host_amount_after_properly_commision = round(($invoice['host_amount'] - (($invoice['host_amount'] * $properly_commission_percentage) / 100)));

            $invoice_tile[] = [
                'request_id'   => Helper::encodeBookingRequestId($invoice['id']),
                'guest_name'   => ucfirst($invoice['guest_name']),
                'guests'       => $invoice['guests'],
                'invoice_date' => $invoice_date->format('dS M Y'),
                'currency'     => $invoice['currency'],
                'host_amount'  => Helper::getFormattedMoney($host_amount_after_properly_commision, $invoice['currency']),
                'title'        => $invoice['title'],
            ];
        }//end foreach

        foreach ($invoices['all_data'] as $invoice) {
            $properly_commission_percentage       = $invoice['properly_commission'];
            $host_amount_after_properly_commision = round(($invoice['host_amount'] - (($invoice['host_amount'] * $properly_commission_percentage) / 100)));

            $sum += $host_amount_after_properly_commision;
        }

            return [
                'total_amount' => Helper::getFormattedMoney($sum, $currency),
                'invoice'      => array_values($invoice_tile),
            ];

    }//end getInvoice()


    /**
     * Function to get Graph home data.
     *
     * @param integer $prive_owner_id Prive Owner id.
     * @param string  $start_date     Start Date.
     * @param string  $end_date       End date.
     *
     * @return array.
     */
    public function getGraphData(int $prive_owner_id, string $start_date, string $end_date)
    {
        $booking_request  = [];
        $booking_list     = [];
        $booking_requests = new BookingRequest;

        $invoices_on_checkout_start_date = config('gh.properly.host_payout_based_on_checkout_start_date');

        // If start date is lesser then 01-06-2019 and end date is greater then 01-06-2019 then reset start date to 01-06-2019.
        if ($start_date <= PROPERLY_BOOOKING_START_DATE && $end_date >= PROPERLY_BOOOKING_START_DATE) {
            $query_start_date = PROPERLY_BOOOKING_START_DATE;
        } else {
            $query_start_date = $start_date;
        }

        if ($query_start_date >= PROPERLY_BOOOKING_START_DATE && $end_date >= PROPERLY_BOOOKING_START_DATE) {
            if ($start_date >= $invoices_on_checkout_start_date && $end_date >= $invoices_on_checkout_start_date) {
                $booking_request = $booking_requests->getGraphDataOnCheckout($prive_owner_id, $query_start_date, $end_date, $invoices_on_checkout_start_date);
            } else if ($start_date < $invoices_on_checkout_start_date && $end_date < $invoices_on_checkout_start_date) {
                $booking_request = $booking_requests->getGraphDataOnCheckin($prive_owner_id, $query_start_date, $end_date);
            } else if ($start_date <= $invoices_on_checkout_start_date && $end_date >= $invoices_on_checkout_start_date) {
                $booking_request_before_invoice_date = $booking_requests->getGraphDataOnCheckin($prive_owner_id, $query_start_date, Carbon::parse($invoices_on_checkout_start_date)->subDays(1));

                $booking_request_after_invoice_date = $booking_requests->getGraphDataOnCheckout($prive_owner_id, $invoices_on_checkout_start_date, $end_date, $invoices_on_checkout_start_date);

                $booking_request = array_merge($booking_request_before_invoice_date, $booking_request_after_invoice_date);
            }
        }

        $from = Carbon::parse($start_date);
        $to   = Carbon::parse($end_date);

        $all_months = [];

        for ($month = $from; $month->lte($to) === true; $month->addMonth()) {
            $all_months[$month->format('m-Y')] = [
                'month'               => $month->format('m-Y'),
                'total_income'        => Helper::getFormattedMoney(0, DEFAULT_CURRENCY),
                'total_nights_booked' => 0,
            ];
        }

        if (empty($booking_request) === true) {
            return array_values($all_months);
        }

        foreach ($booking_request as $booking) {
                $all_months[$booking['month']]['total_income']        = Helper::getFormattedMoney($booking['host_actual_amount'], $booking['currency']);
                $all_months[$booking['month']]['total_nights_booked'] = $booking['total_nights'];
        }

        return array_values($all_months);

    }//end getGraphData()


    /**
     * Send Booking Payment Link Email
     *
     * @param integer $request_id     Booking Request Id.
     * @param string  $traveller_name Traveller Full Name.
     * @param string  $to_mail        To mail.
     *
     * @return void
     */
    public function sendBookingPaymentLinkEmail(int $request_id, string $traveller_name, string $to_mail)
    {
        if (empty($to_mail) === true) {
            return;
        }

        $request_hash_id = Helper::encodeBookingRequestId($request_id);

        $payment_link = SITE_URL.'/v1.6/booking/payment/'.$request_hash_id.'?source=prive&origin='.MSITE_URL;

        // Link Expire time in hours.
        $link_expire_time = 24;

        $this->email_service->sendPaymentLinkEmail($to_mail, $request_hash_id, $traveller_name, $link_expire_time, $payment_link);

    }//end sendBookingPaymentLinkEmail()


    /**
     * Send Booking Payment Link Sms
     *
     * @param integer $request_id Booking Request Id.
     * @param string  $dial_code  Dial Code.
     * @param string  $contact    Contact.
     *
     * @return void
     */
    public function sendBookingPaymentLinkSms(int $request_id, string $dial_code, string $contact)
    {
        if (empty($dial_code) === true || empty($contact) === true) {
            return;
        }

        $request_hash_id = Helper::encodeBookingRequestId($request_id);

        $payment_link = SITE_URL.'/v1.6/booking/payment/'.$request_hash_id.'?source=prive&origin='.MSITE_URL;

        // Generate Property Direction Short Url.
        $payment_link = Helper::getFirebaseShortUrl(urlencode($payment_link));

        if (empty($payment_link) === true) {
            return;
        }

        // Link Expire time in hours.
        $link_expire_time = 24;

        $this->sms_service->sendPaymentLinkSms($dial_code, $contact, $request_hash_id, $payment_link, $link_expire_time);

    }//end sendBookingPaymentLinkSms()


     /**
      * Get Properly Task List
      *
      * @param integer $logged_in_user_id  Logged In  Id.
      * @param array   $filter             Filter parameter.
      * @param integer $booking_request_id Request Id.
      * @param boolean $grouping           Grouping Id.
      *
      * @return array
      */
    public function getProperlyTaskList(int $logged_in_user_id, array $filter=[], int $booking_request_id=0, bool $grouping=false)
    {
        $prive_task = [];

        $prive_task_list = BookingRequest::getProperlyTaskList($logged_in_user_id, $filter, $booking_request_id);

        foreach ($prive_task_list as $key => $task_list) {
            // Encode task Id.
            $tasks['task_hash_id'] = Helper::encodeTaskId($task_list['task_id']);

            $tasks['entity_hash_id'] = Helper::encodeBookingRequestId($task_list['booking_request_id']);
            // Get Prive Status which we Show.
            $tasks['task_status'] = Helper::getPriveTaskShowStatus($task_list['task_status']);

            $tasks['title'] = $task_list['id'].' • '.ucfirst($task_list['title']);

            // Fetch task type which we show.
            $tasks['task_type']                = Helper::getPriveTaskShowType($task_list['task_type']);
            $tasks['task_date']                = Carbon::parse($task_list['task_date_time'])->format('Y-m-d');
            $tasks['task_time']                = Carbon::parse($task_list['task_date_time'])->format('H:i:s');
            $tasks['task_date_time_formatted'] = carbon::parse($task_list['task_date_time'])->format('dS M Y h:i A');
            $tasks['traveller_name']           = ucfirst($task_list['traveller_name']);
            $tasks['assigned_to']              = ucfirst($task_list['assigned_to']);
            $tasks['guests']                   = $task_list['guests'];
            $tasks['reccuring_type']           = $task_list['reccuring_type'];
            $tasks['can_update']               = $task_list['can_update'];
            $tasks['description']              = $task_list['description'];

            if ($grouping === false) {
                $prive_task[] = $tasks;
            } else {
                // Set Task Grouping by Day.
                if ($tasks['task_date'] === Carbon::now('GMT')->format('Y-m-d')) {
                    $tasks['task_date_time_formatted'] = 'Today, '.carbon::parse($task_list['task_date_time'])->format('h:i A');
                    $prive_task['today']['list'][]     = $tasks;
                } else if ($tasks['task_date'] < Carbon::now('GMT')->format('Y-m-d')) {
                     $tasks['task_date_time_formatted'] = 'Yesterday, '.carbon::parse($task_list['task_date_time'])->format('h:i A');
                     $prive_task['yesterday']['list'][] = $tasks;
                } else if ($tasks['task_date'] > Carbon::now('GMT')->format('Y-m-d')) {
                     $tasks['task_date_time_formatted'] = 'Tomorrow, '.carbon::parse($task_list['task_date_time'])->format('h:i A');
                     $prive_task['tomorrow']['list'][]  = $tasks;
                }
            }
        }//end foreach

        // Total Count.
        if ($grouping === true) {
            $prive_task['today']['total']     = (isset($prive_task['today']['list']) === true) ? count($prive_task['today']['list']) : 0;
            $prive_task['yesterday']['total'] = (isset($prive_task['yesterday']['list']) === true) ? count($prive_task['yesterday']['list']) : 0;
            $prive_task['tomorrow']['total']  = (isset($prive_task['tomorrow']['list']) === true) ? count($prive_task['tomorrow']['list']) : 0;
        }

        return $prive_task;

    }//end getProperlyTaskList()


    /**
     * Create Reccuring Task.
     *
     * @param integer $entity_id        Entity_id booking request id or property_id.
     * @param integer $task_type        Type of Task.
     * @param string  $run_at           Task excution Time.
     * @param string  $from_date        Booking From date.
     * @param string  $to_date          Booking to date.
     * @param integer $prive_manager_id Prive Manager Id.
     *
     * @return boolean
     */
    public function createReccuringTask(int $entity_id, int $task_type, string $run_at, string $from_date='', string $to_date='', int $prive_manager_id=0)
    {
        $params = [
            'status'         => PRIVE_TASK_OPEN,
            'assigned_by'    => 0,
            'reccuring_type' => RECCURING,
            'created_by'     => 'system',
            'entity_type'    => ENTITY_TYPE_BOOKING,
        ];

        $from_date_obj = Carbon::parse($from_date);
        $to_date_obj   = Carbon::parse($to_date)->subDay();

        if ($task_type === TASK_TYPE_CHECKOUT || $task_type === TASK_TYPE_DEPARTURE_SERVICE || $task_type === TASK_TYPE_CHECKIN) {
            ProperlyTask::saveTask(array_merge($params, ['entity_id' => $entity_id, 'run_at' => $run_at, 'type' => $task_type]));
        } else if ($task_type === TASK_TYPE_OCCUPIED_SERVICE || $task_type === TASK_TYPE_TURN_DOWN_SERVICE) {
            $checkin_time = self::getCheckedInTimeByBookingId($prive_manager_id, $entity_id);
            if ($checkin_time > $from_date.' '.$run_at) {
                $from_date_obj = $from_date_obj->addDay();
            } else {
                $from_date_obj = $from_date_obj;
            }

            // Create service task equals to no of booking days.
            for ($day = $from_date_obj; $day->lte($to_date_obj) === true; $day->addDay()) {
                $time = $day->format('Y-m-d').' '.$run_at;
                ProperlyTask::saveTask(array_merge($params, ['entity_id' => $entity_id, 'run_at' => $time, 'type' => $task_type]));
            }
        }

        return true;

    }//end createReccuringTask()


     /**
      * Get Checked in time of a booking.
      *
      * @param integer $prive_manager_id Prive manager Id.
      * @param integer $booking_id       Request Id.
      *
      * @return string
      */
    public function getCheckedInTimeByBookingId(int $prive_manager_id, int $booking_id)
    {
        $booking_detail = self::getPriveManagerBookingDetail($prive_manager_id, $booking_id);

        $property_checkin_time = @Carbon::createFromFormat('Y-m-d H:i:s', $booking_detail['from_date'].' '.$booking_detail['property_checkin_time'])->format('Y-m-d H:i:s');

        $checkin_run_at = (empty($booking_detail['expected_checkin_datetime']) === false) ? Carbon::parse($booking_detail['expected_checkin_datetime'])->format('Y-m-d H:i:s') : $property_checkin_time;
        return $checkin_run_at;

    }//end getCheckedInTimeByBookingId()


    /**
     * Get Checked out time of a booking.
     *
     * @param integer $prive_manager_id Prive manager Id.
     * @param integer $booking_id       Request Id.
     *
     * @return string
     */
    public function getCheckedoutTimeByBookingId(int $prive_manager_id, int $booking_id)
    {
        $booking_detail = self::getPriveManagerBookingDetail($prive_manager_id, $booking_id);

        if (empty($booking_detail) === true) {
            return false;
        }

        $property_checkout_time = @Carbon::createFromFormat('Y-m-d H:i:s', $booking_detail['to_date'].' '.$booking_detail['property_checkout_time'])->format('Y-m-d H:i:s');

        $checkout_run_at = (empty($booking_detail['expected_checkout_datetime']) === false) ? Carbon::parse($booking_detail['expected_checkout_datetime'])->format('Y-m-d H:i:s') : $property_checkout_time;
        return $checkout_run_at;

    }//end getCheckedoutTimeByBookingId()


}//end class
