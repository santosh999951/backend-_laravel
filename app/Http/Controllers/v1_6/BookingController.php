<?php
/**
 * Booking controller containing methods
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Hash, View};

use \Auth;
use \Event;
use \Carbon\Carbon;

use App\Libraries\ApiResponse;
use App\Libraries\Helper;
use App\Libraries\v1_6\PropertyTileService;
use App\Libraries\v1_6\PaymentMethodService;
use App\Libraries\v1_6\PropertyPricingService;
use App\Libraries\v1_6\BookingRequestService;
use App\Libraries\v1_6\CouponService;
use App\Libraries\v1_6\{InvoiceService,PaymentService, PropertyService};
use App\Libraries\v1_6\FbShare;

use App\Models\Amenity;
use App\Models\BookingRequest;
use App\Models\CountryCodeMapping;
use App\Models\CancellationReasonDetails;
use App\Models\PropertyImage;
use App\Models\CancellationPolicy;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\RequestStatusTracking;
use App\Models\User;
use App\Models\{Booking, PaymentTracking, BookingCancellationReason,RefundRequest, InventoryPricing};
use App\Libraries\CommonQueue;
use App\Models\PropertyTagMapping;
use App\Models\GatewayBankcodeMapping;


use App\Http\Response\v1_6\Models\{GetRequestResponse, GetRequestDetailResponse, GetTripsResponse, GetTripDetailResponse,
                            GetBookingShareResponse, PostBookingRequestResponse, PutBookingRequestResponse,
                            PostBookingRequestResendResponse, PostBookingRequestCancelResponse, PostBookingRequestEmailinvoiceResponse};
use App\Http\Requests\{GetBookingRequest,GetBookingRequestDetailsRequest,GetTripRequest,GetTripDetailRequest , GetBookingShareRequest , PostBookingRequestEmailinvoiceRequest};

use App\Http\Requests\{PostBookingRequest,PutBookingRequest, PostBookingRequestResendRequest , PostBookingRequestCancelRequest, PutTravellerConfirmationOnArrivalRequest};
use App\Http\Requests\{GetSeamlessPaymentOptionsRequest, GetSeamlessPaymentPayloadRequest};


use App\Events\{CreateBookingRequest, CancelBookingRequest, CreateBooking};
use App\Models\PaymentGateway;


/**
 * Class BookingController
 */
class BookingController extends Controller
{


    /**
     * Get user trips
     *
     * @param \App\Http\Requests\GetTripRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse containing trip data
     *
     * @SWG\Get(
     *     path="/v1.6/booking/trip",
     *     tags={"Trip"},
     *     description="get all of user's trips.",
     *     operationId="booking.get.trips",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/for_in_query"),
     * @SWG\Parameter(ref="#/parameters/past_in_query"),
     * @SWG\Parameter(ref="#/parameters/status_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing list of user trips with pagination. ",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetTripsResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getTrips(GetTripRequest $request)
    {
        $input_params = $request->input();

        // Input optional params.
        $for              = $input_params['for'];
        $offset           = $input_params['offset'];
        $limit            = $input_params['total'];
        $past             = $input_params['past'];
        $status           = $input_params['status'];
        $user_id          = Auth::user()->id;
        $output           = [];
        $status_array     = [];
        $past_trip_count  = 0;
        $total_trip_count = 0;

        // Get all headers.
        $headers = $request->getAllHeaders();

        if (empty($status) === false) {
            $status_array = Helper::getTripStatusViaStatusClass($status);
        }

        // Get user's trips data.
        $trips_data = BookingRequest::getTripsByUserId($user_id, $offset, $limit, $for, $status_array, $past);

        if (empty($for) === false && $for === 'web') {
            $trip_counts      = BookingRequest::getTripCountsByUserId($user_id);
            $past_trip_count  = $trip_counts['past'];
            $total_trip_count = $trip_counts['total'];
        }

        if (count($trips_data) < 1) {
            return ApiResponse::success(
                [
                    'trips'            => [],
                    'past_trip_count'  => $past_trip_count,
                    'total_trip_count' => $total_trip_count,
                    'updated_offset'   => $offset,
                    'limit'            => $limit,
                ]
            );
        }

        // Get property ids (unique) visited by user.
        $property_ids = array_unique(array_column($trips_data, 'id'));

        // Get first property image to display.
        $properties_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, 1);

        // Today's date.
        $today = Carbon::today('Asia/Kolkata');

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        // Iterate through trip data.
        foreach ($trips_data as $trip) {
            // Get timeline diff from today to from_date and to_date.
            $start_date_obj    = Carbon::createFromTimestamp(strtotime($trip['from_date']));
            $end_date_obj      = Carbon::createFromTimestamp(strtotime($trip['to_date']));
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
                $trip_status     = UPCOMING_TRIP;
            } else if ($no_of_months > 0) {
                $timeline_string = ($no_of_months > 1) ? ' months ' : ' month ';
                $timeline_status = $no_of_months.$timeline_string.'to go';
                $trip_status     = UPCOMING_TRIP;
            } else if ($no_of_days > 0) {
                $timeline_string = ($no_of_days > 1) ? ' days ' : ' day ';
                $timeline_status = $no_of_days.$timeline_string.'to go';
                $trip_status     = UPCOMING_TRIP;
            } else if ($no_of_days <= 0 && $past_no_of_days >= 0) {
                $timeline_status = 'Ongoing';
                $trip_status     = ONGOING_TRIP;
            } else if ($past_no_of_years < 0) {
                $timeline_string = (abs($past_no_of_years) > 1) ? ' years ' : ' year ';
                $timeline_status = abs($past_no_of_years).$timeline_string.'ago';
                $trip_status     = PAST_TRIP;
            } else if ($past_no_of_months < 0) {
                $timeline_string = (abs($past_no_of_months) > 1) ? ' months ' : ' month ';
                $timeline_status = abs($past_no_of_months).$timeline_string.'ago';
                $trip_status     = PAST_TRIP;
            } else {
                $timeline_string = (abs($past_no_of_days) > 1) ? ' days ' : ' day ';
                $timeline_status = abs($past_no_of_days).$timeline_string.'ago';
                $trip_status     = PAST_TRIP;
            }//end if

            // Get booking status to display (along with class).
            $booking_status_by_date = Helper::getTripStatusTextAndClassForMsite($trip['booking_status'], $trip['from_date'], $trip['to_date']);
            if (empty($for) === false && $for === 'web') {
                $booking_status_text = $booking_status_by_date;
            } else {
                $booking_status_text = Helper::getBookingStatusTextAndClass($trip['booking_status']);
            }

            // Get booking amount with currency.
            $price_details = json_decode($trip['price_details']);
            $amount        = $price_details->payable_amount;
            $currency      = $price_details->currency_code;

            $trip['country_codes']     = $country_codes;
            $trip['properties_images'] = $properties_images;
            $trip['original_title']    = true;

            // Get tile structure for trip page.
            $property_tile = PropertyTileService::minPropertyTileStructure($trip);

            // Checkin - checkout format.
            $checkin_checkout = $start_date_obj->format('d M').' - '.$end_date_obj->format('d M Y');

            $can_review = 0;
            $can_rate   = 0;
            if (in_array($booking_status_by_date['class'], [COMPLETED_CLASS]) === true) {
                $can_review = (empty($trip['review_id']) === true) ? 1 : 0;
                $can_rate   = (empty($trip['rating_id']) === true) ? 1 : 0;
            }

            // Output array.
            $output[] = [
                'request_id'                 => $trip['request_id'],
                'request_hash_id'            => Helper::encodeBookingRequestId($trip['request_id']),
                'property_tile'              => $property_tile,
                'timeline_status'            => $timeline_status,
                'booking_amount'             => Helper::getFormattedMoney($amount, $currency),
                'booking_amount_unformatted' => $amount,
                'booking_status'             => $booking_status_text,
                'trip_status'                => $trip_status,
                'checkin_checkout'           => $checkin_checkout,
                'checkin'                    => $start_date_obj->toDateString(),
                'checkout'                   => $end_date_obj->toDateString(),
                'guests'                     => $trip['guests'],
                'can_review'                 => $can_review,
                'can_rate'                   => $can_rate,
            ];
        }//end foreach

        $return_array = [
            'trips'            => $output,
            'past_trip_count'  => $past_trip_count,
            'total_trip_count' => $total_trip_count,
            'updated_offset'   => ($offset + $limit),
            'limit'            => $limit,
        ];

        $response = new GetTripsResponse($return_array);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getTrips()


    /**
     * Get user booking requests
     *
     * @param \App\Http\Requests\GetBookingRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse containing booking requests
     *
     * @SWG\Get(
     *     path="/v1.6/booking/request",
     *     tags={"Request"},
     *     description="get all of user's booking requests.",
     *     operationId="booking.get.requests",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/archived_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing list of user booking requests with pagination.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetRequestResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getBookingRequests(GetBookingRequest $request)
    {
        $input_params = $request->input();

        // Input optional params.
        $offset   = $input_params['offset'];
        $limit    = $input_params['total'];
        $archived = $input_params['archived'];
        $user_id  = Auth::user()->id;
        $output   = [];

        // Get all headers.
        $headers = $request->getAllHeaders();

        // Get user's booking request data.
        $requests_data = BookingRequest::getBookingRequestsByUserId($user_id, $offset, $limit, (bool) $archived);

        // Get total archived request count.
        $archived_request_count = BookingRequest::getArchivedBookingRequestsCount($user_id);

        $active_request_count = BookingRequest::getNewAndApprovedRequestsCount($user_id);

        // If no booking request found.
        if (count($requests_data) < 1) {
            return ApiResponse::success(
                [
                    'requests'               => [],
                    'active_request_count'   => $active_request_count,
                    'archived_request_count' => $archived_request_count,
                    'updated_offset'         => $offset,
                    'limit'                  => $limit,
                ]
            );
        }

        // Get property ids (unique) visited by user.
        $property_ids = array_unique(array_column($requests_data, 'id'));

        // Get first property image to display.
        $properties_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, 1);

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        // Iterate through bookings.
        foreach ($requests_data as $request) {
            $start_date_obj = Carbon::createFromTimestamp(strtotime($request['from_date']));
            $end_date_obj   = Carbon::createFromTimestamp(strtotime($request['to_date']));
            // Get booking create date.
            $created_at = Carbon::createFromTimestamp((strtotime($request['created_at']) + (int) (5.5 * 60 * 60)))->format('d M y');

            // Get booking status to display (along with class).
            $booking_status = Helper::getBookingStatusTextAndClass($request['booking_status']);

            if ($booking_status['status'] === REQUEST_APPROVED) {
                $booking_expiry_time = (strtotime($request['valid_till']) - strtotime(Carbon::now('GMT')->format('Y-m-d H:i:s')));
            } else if ($booking_status['status'] === NEW_REQUEST) {
                $booking_expiry_time = (strtotime($request['approve_till']) - strtotime(Carbon::now('GMT')->format('Y-m-d H:i:s')));
            } else {
                $booking_expiry_time = 0;
            }

            // Get booking amount with currency.
            $price_details = json_decode($request['price_details']);
            $amount        = $price_details->payable_amount;
            $currency      = $price_details->currency_code;

            $request['country_codes']     = $country_codes;
            $request['properties_images'] = $properties_images;
            $request['original_title']    = true;

            // Get tile structure for trip page.
            $property_tile = PropertyTileService::minPropertyTileStructure($request);

            // Output array.
            $output[] = [
                'request_id'                 => $request['request_id'],
                'request_hash_id'            => Helper::encodeBookingRequestId($request['request_id']),
                'property_tile'              => $property_tile,
                'booking_amount'             => Helper::getFormattedMoney($amount, $currency),
                'booking_amount_unformatted' => $amount,
                'booking_status'             => $booking_status,
                'created_at'                 => $created_at,
                'checkin'                    => $start_date_obj->toDateString(),
                'checkout'                   => $end_date_obj->toDateString(),
                'expires_in'                 => ($booking_expiry_time > 0) ? $booking_expiry_time : 0,
                'guests'                     => $request['guests'],
            ];
        }//end foreach

        $return_array = [
            'requests'               => $output,
            'archived_request_count' => $archived_request_count,
            'active_request_count'   => $active_request_count,
            'updated_offset'         => ($offset + $limit),
            'limit'                  => $limit,
        ];

        $response = new GetRequestResponse($return_array);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getBookingRequests()


    /**
     * Get trip details
     *
     * @param \App\Http\Requests\GetTripDetailRequest $request         Http request object.
     * @param string                                  $request_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\JsonResponse containing trip details
     *
     * @SWG\Get(
     *     path="/v1.6/booking/trip/{request_hash_id}",
     *     tags={"Trip"},
     *     description="get details of trip",
     *     operationId="booking.get.tripdetails",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing trip details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetTripDetailResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="No details available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function getTripDetails(GetTripDetailRequest $request, string $request_hash_id)
    {
        $request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $headers = $request->getAllHeaders();

        $device_source = $request->getDeviceType();

        $user_id = $request->getLoginUserId();

        $trip = BookingRequest::getBookingRequest($request_id, $user_id);

        if (empty($trip) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $trip_invoice = InvoiceService::requestDetailsInvoice($trip);

        $trip['address'] = Helper::decodePropertyAddress($trip['address']);
        $booking_status  = (int) $trip['booking_status'];

        $is_trip = ($booking_status > REQUEST_APPROVED) ? 1 : 0;

        $payment_option = (int) $trip['payment_option'];

        $balance_fee = round($trip['balance_fee']);

        // Checkin date Parse.
        $checkin_date           = $trip['from_date'];
        $checkin_date_obj       = Carbon::parse($checkin_date);
        $checkin_date_formatted = $checkin_date_obj->format('d M Y');

        // Checkout date Parse.
        $checkout_date           = $trip['to_date'];
        $checkout_date_obj       = Carbon::parse($checkout_date);
        $checkout_date_formatted = $checkout_date_obj->format('d M Y');
        $currency                = (empty($trip['currency']) === true ) ? DEFAULT_CURRENCY : $trip['currency'];

        $check_in_time = $trip['property_check_in'];
        $check_in      = new Carbon(date('Y-m-d H:i:s', strtotime($trip['from_date'].' '.$check_in_time)));
        $now           = Carbon::now('GMT');
        $diff_in_days  = $now->diffInDays($check_in, false);

        $updated                = new Carbon($trip['updated_at']);
        $diff_from_updated_time = $updated->diffInMinutes($now, false);

        $price_detail_data = json_decode($trip['price_details'], true);
        $coa_fee           = (isset($price_detail_data['coa_charges']) === true) ? $price_detail_data['coa_charges'] : 0;

        $payable_amount = $price_detail_data['payable_amount'];

        $booking_amount = (($price_detail_data['per_night_price'] * $trip['units_consumed'] * $price_detail_data['total_nights']) + $price_detail_data['extra_guest_cost']);

        // Booking status by date.
        $booking_status_by_date = Helper::getTripStatusTextAndClassForMsite($booking_status, $checkin_date, $checkout_date);

        // Cancellation policy.
        $cancellation_policy = CancellationPolicy::getCancellationPoliciesByIds([$trip['cancellation_policy']])[$trip['cancellation_policy']];

        // Set Default Cancel Text.
        $cancellation_policy['cancel_text'] = '';

        $request_footer_data = [];

        $footer_message = '';

        // Host Info Section.
        $host_info = [
            'show_host'       => ((int) $balance_fee === 0 && $is_trip === 1) ? 1 : 0,
            'host_name'       => '',
            'host_contact'    => '',
            'host_address'    => '',
            'host_first_name' => '',
            'host_dob'        => '',
            'host_created'    => '',
            'host_language'   => '',
            'host_work'       => '',
            'host_gender'     => '',
            'host_image'      => '',
        ];

        if ($is_trip === 0) {
            // Privous Credit Used or not.
            $released_payment_refund_amount = (isset($price_detail_data['used_released_payment_amount']) === true) ? $price_detail_data['used_released_payment_amount'] : 0;

            $new_payment_method = (isset($price_detail_data['choose_payment']) === true ) ? Helper::getNewPaymentMethodName($price_detail_data['choose_payment']) : 'full_payment';

            $available_method = (isset($price_detail_data['chosen_payment_method']) === true) ? $price_detail_data['chosen_payment_method'] : $new_payment_method;

            // Get payment methods and label to display.
            $payment_methods = PaymentMethodService::getPaymentMethods(
                [
                    'is_instant_bookable'            => $trip['instant_book'],
                    'service_fee'                    => (isset($price_detail_data['service_fee']) === true) ? $price_detail_data['service_fee'] : 0,
                    'gh_commission'                  => $trip['commission_from_host'],
                    'coa_fee'                        => $coa_fee,
                    'gst'                            => (isset($price_detail_data['gst_amount']) === true) ? $price_detail_data['gst_amount'] : 0,
                    'cash_on_arrival'                => $trip['coa_available'],
                    'booking_amount'                 => $booking_amount,
                    'released_payment_refund_amount' => $released_payment_refund_amount,

                    'payable_amount'                 => $price_detail_data['payable_amount'],
                    'prive'                          => $trip['prive'],
                    'cancelation_policy'             => $trip['cancellation_policy'],
                    'payment_gateway_enabled'        => 1,
                    'checkin'                        => $checkin_date,
                    'policy_days'                    => $cancellation_policy['policy_days'],
                    'user_currency'                  => $currency,
                    'prive_property_coa_max_amount'  => Helper::convertPriceToCurrentCurrency('INR', PRIVE_PROPERTY_COA_MAX_AMOUNT, $currency),
                    'partial_payment_coa_max_amount' => Helper::convertPriceToCurrentCurrency('INR', PARTIAL_PAYMENT_COA_MAX_AMOUNT, $currency),
                    'checkin_formatted'              => Carbon::parse($checkin_date)->format('d M Y'),
                    'markup_service_fee'             => (isset($price_detail_data['markup_service_fee']) === true) ? $price_detail_data['markup_service_fee'] : 0,
                    'total_host_fee'                 => $price_detail_data['host_fee'],
                ]
            );

            $request_footer_data = PropertyService::getFooterAndCancellationPolicyDivData(
                [
                    'service_fee'             => $price_detail_data['service_fee'],
                    'cancellation_policy'     => $cancellation_policy,
                    'prive'                   => $trip['prive'],
                    'coa'                     => $trip['coa_available'],
                    'coa_fee'                 => $coa_fee,
                    'gh_commission'           => $trip['commission_from_host'],
                    'start_date'              => $checkin_date,
                    'payment_methods'         => $payment_methods,
                    'currency'                => $currency,
                    'selected_payment_method' => $available_method,
                ]
            )['footer'];
        } else {
            $refund_amount   = RefundRequest::getRefundedAmount(
                [
                    'total_charged_fee'    => $trip['total_charged_fee'],
                    'service_fee'          => $trip['service_fee'],
                    'from_date'            => $trip['from_date'],
                    'wallet_money_applied' => (empty($price_detail_data['wallet_money_applied']) === false) ? $price_detail_data['wallet_money_applied'] : 0,
                    'coa_charges'          => (empty($price_detail_data['coa_charges']) === false) ? $price_detail_data['coa_charges'] : 0,
                    'check_in_time'        => $check_in_time,
                    'cancellation_policy'  => $cancellation_policy['id'],
                ]
            );
            $refunded_amount = Helper::getFormattedMoney($refund_amount, $price_detail_data['currency_code']);
            // phpcs:ignore
            $cancellation_policy['cancel_text'] = ($payment_option !== SI_PAYMENT) ? 'As per your current refund policy, the total refund amount is : '.$refunded_amount.'. Please see our terms and conditions for more information regarding our cancellation policy.' : 'Are you sure you want to cancel this booking.';
        }//end if

        // Cancellation Data.
        $cancellation_data = [
            'cancellation_policy_info' => $cancellation_policy,
            'cancellable'              => 0,
            'cancellation_reasons'     => CancellationReasonDetails::getCancellationReasons($booking_status),
        ];

        if (Carbon::now('Asia/Kolkata')->format('Y-m-d') <= $trip['from_date']) {
             $cancel = BookingRequestService::getBookingRequestCancellationStatus($booking_status);

            if ($cancel !== false) {
                $cancellation_data['cancellable'] = 1;
            }
        }

        // Get Property Tile Structure.
        $property_tile = PropertyTileService::minPropertyTileStructureWithExtraInfo($trip);

        // Fetch Refund data.
        $refund_data = BookingRequestService::getRefundData($request_id, $booking_status_by_date['class']);

        // Resend Request Status.
        $resend_request = 0;
        if ($booking_status === EXPIRED && $checkin_date >= Carbon::now('Asia/Kolkata')->format('Y-m-d') && $diff_from_updated_time <= 30 && empty($trip['resend_request_status']) === true) {
            $resend_request = 1;
        }

        // Check Other Date Status.
        // This is enable when Request Rejected by host then is show upto 24 hrs.
        $check_other_date = 0;
        if ($booking_status === REQUEST_REJECTED && $checkin_date >= Carbon::now('Asia/Kolkata')->format('Y-m-d') && $diff_from_updated_time >= 0 && $diff_from_updated_time <= 1440) {
            $check_other_date = 1;
        }

        // Booking Expiry Time used for Request Detail Page Timer.
        if ($booking_status === REQUEST_APPROVED) {
            $booking_expiry_time = (strtotime($trip['valid_till']) - strtotime(Carbon::now('GMT')->format('Y-m-d H:i:s')));
            if ((Carbon::parse($trip['created_at']))->diffInHours(Carbon::parse($trip['valid_till'])) === 48) {
                $footer_message = "This home is available on the chosen dates. \nPlease make the payment within the next 48 hours to confirm the booking";
            } else {
                $footer_message = "This home is available on the chosen dates. \nPlease make the payment within the next 8 hours to confirm the booking";
            }
        } else if ($booking_status === NEW_REQUEST) {
            $booking_expiry_time = (strtotime($trip['approve_till']) - strtotime(Carbon::now('GMT')->format('Y-m-d H:i:s')));
            $footer_message      = "This home is not instantly bookable. \nAvailability confirmation usually takes about 8-48 hours";
        } else {
            $booking_expiry_time = 0;
        }

        // Si payment date.
        $si_payment_date = PaymentMethodService::siPaymentDate($checkin_date_obj, $cancellation_policy['policy_days']);

        $show_fb_post = (in_array($booking_status_by_date['class'], [ONGOING_CLASS, UPCOMING_CLASS]) === true && $booking_status === BOOKED) ? 1 : 0;

        $pending_payment = 0;
        $payment_url     = '';
        $payment_text    = '';
        // Write Better version.
        if ($balance_fee > 0 && Carbon::parse($trip['to_date'])->diffInDays(Carbon::now('GMT'), false) <= 2) {
            // Coa , partial payment.
            if ($payment_option === 2) {
                $host_info['show_host'] = 1;
                $payment_text           = 'Payable at checkin';
                // Si payment.
            } else if ($payment_option === 4) {
                $payment_text = 'To be charged on '.$si_payment_date;
            } else if (in_array($payment_option, [6, 7]) === true) {
                $payment_text = 'Pay by '.$si_payment_date;
            }

            $payment_status  = PaymentTracking::getIsRequestPaymentInitated($request_id, false);
            $pending_payment = 1;
            $payment_url     = SITE_URL.'/v1.6/booking/payment/'.$request_hash_id.'?source='.$device_source;
            ;
        } else if ($is_trip === 0 && $booking_status === REQUEST_APPROVED) {
            $payment_url = SITE_URL.'/v1.6/booking/payment/'.$request_hash_id.'?source='.$device_source;
        }

        if (in_array($booking_status_by_date['class'], [COMPLETED_CLASS, CANCELLATION_CLASS]) === true) {
            $host_info['show_host'] = 0;
            $cancellable            = 0;
            if (Carbon::parse($trip['to_date'])->diffInDays(Carbon::now('GMT'), false) > 2 || $booking_status_by_date['class'] === CANCELLATION_CLASS) {
                $pending_payment = 0;
            }
        }

        $can_review = 0;
        $can_rate   = 0;
        if (in_array($booking_status_by_date['class'], [COMPLETED_CLASS]) === true) {
            $can_review = (empty($trip['review_id']) === true) ? 1 : 0;
            $can_rate   = (empty($trip['rating_id']) === true) ? 1 : 0;
        }

        if ($host_info['show_host'] === 1 && $booking_status === BOOKED) {
            if ($diff_in_days <= 3 && $diff_in_days > -1) {
                $host_info['host_name']    = $trip['host_name'];
                $host_info['host_address'] = trim($trip['address'].','.$trip['area'].','.$trip['city'].','.$trip['state'].'-'.$trip['zipcode'], ',');
                $host_info['host_contact'] = (empty($trip['contact']) === false) ? $trip['contact'] : GH_CONTACT_NUMBER;

                // Change location by actutal lat long.
                $property_tile['location']['latitude']  = $trip['original_latitude'];
                $property_tile['location']['longitude'] = $trip['original_longitude'];
            } else if ($diff_in_days > $cancellation_policy['policy_days']) {
                $host_info['host_name']    = 'Guesthouser Support';
                $host_info['host_address'] = '';
                $host_info['host_contact'] = GH_CONTACT_NUMBER;
            } else if ($trip['total_charged_fee'] >= round(($balance_fee + $trip['total_charged_fee']) * 30 / 100)) {
                $host_info['host_name']    = $trip['host_name'];
                $host_info['host_address'] = trim($trip['address'].','.$trip['area'].','.$trip['city'].','.$trip['state'].'-'.$trip['zipcode'], ',');
                $host_info['host_contact'] = (empty($trip['contact']) === false) ? $trip['contact'] : GH_CONTACT_NUMBER;

                // Change location by actutal lat long.
                $property_tile['location']['latitude']  = $trip['original_latitude'];
                $property_tile['location']['longitude'] = $trip['original_longitude'];
            } else {
                $host_info['host_name']    = 'Guesthouser Support';
                $host_info['host_address'] = '';
                $host_info['host_contact'] = GH_CONTACT_NUMBER;
            }//end if
        }//end if

        // Host Extra Details For Website.
        $host_info['host_first_name'] = ucfirst($trip['host_first_name']);
        $host_info['host_dob']        = (empty($trip['host_dob']) === false && $trip['host_dob'] !== '0000-00-00') ? Carbon::parse($trip['host_dob'])->format('d M Y') : '';
        $host_info['host_created']    = (empty($trip['host_created_date']) === false) ? Carbon::parse($trip['host_created_date'])->format('M Y') : '';
        $host_info['host_language']   = (empty($trip['host_language']) === false) ? $trip['host_language'] : '';
        $host_info['host_work']       = (empty($trip['host_work']) === false) ? $trip['host_work'] : '';
        $host_info['host_gender']     = (empty($trip['host_gender']) === false) ? ucfirst($trip['host_gender']) : 'Male';
        $host_info['host_image']      = (empty($trip['host_image']) === false) ? Helper::generateProfileImageUrl($host_info['host_gender'], $trip['host_image'], $trip['host_id']) : '';
        $host_info['host_contact']    = (empty($host_info['host_contact']) === false) ? $host_info['host_contact'] : GH_CONTACT_NUMBER;

        // Get booking status to display (along with class).
        $booking_status = Helper::getBookingStatusTextAndClass($booking_status);

        $booking_info = [
            'request_hash_id'    => $request_hash_id,
            'instant'            => $trip['instant_book'],
            'coupon_code_used'   => (empty($price_detail_data['coupon_applied']) === false) ? $price_detail_data['coupon_applied'] : '',
            'wallet_money_used'  => (empty($price_detail_data['wallet_money_applied']) === false) ? $price_detail_data['wallet_money_applied'] : 0,
            'guests'             => $trip['guests'],
            'checkin_formatted'  => $checkin_date_formatted,
            'checkout_formatted' => $checkout_date_formatted,
            'booking_status'     => $booking_status,
            'checkin'            => $checkin_date_obj->format('Y-m-d'),
            'checkout'           => $checkout_date_obj->format('Y-m-d'),
            'can_review'         => $can_review,
            'can_rate'           => $can_rate,
            'can_share_fb'       => $show_fb_post,
            'units'              => $trip['units_consumed'],
            'resend_request'     => $resend_request,
            'check_other_date'   => $check_other_date,
            'expires_in'         => $booking_expiry_time,
            'payment_url'        => $payment_url,
            'footer_text'        => $footer_message,

        ];

        $payment_method = 'web';

        // Here would be second payment of user.
        // So it does nt matter user did it si payment or not.
        // So any gateway can be used.
        if ($device_source === 'app') {
            $payment_method = APP_PAYMENT_METHOD['method'];
        }

        $payment_option_text = (empty(PAYMENT_OPTION_TEXT[$payment_option]) === false && empty(PAYMENT_OPTION_TEXT[$payment_option]['text']) === false) ? PAYMENT_OPTION_TEXT[$payment_option]['text'] : '';

        $return_array = [
            'property_section'      => ['tile' => $property_tile],
            'booking_info_section'  => [
                'info'                => $booking_info,
                'booking_amount_info' => [
                    'currency'                 => CURRENCY_SYMBOLS[$currency],
                    'total_amount'             => Helper::getFormattedMoney($payable_amount, $currency, true),
                    'total_amount_unformatted' => $payable_amount,
                    'paid_amount_unformatted'  => ($payable_amount - $balance_fee),
                    'pending_payment'          => $pending_payment,
                    'pending_payment_text'     => $payment_text,
                    'pending_payment_amount'   => Helper::getFormattedMoney($balance_fee, $currency, true),
                    'pending_payment_url'      => $payment_url,
                    'payment_gateway_method'   => $payment_method,
                    'payment_option'           => $payment_option_text,
                ],
            ],
            'invoice_section'       => $trip_invoice,
            'property_info_section' => $host_info,
            'cancellation_section'  => $cancellation_data,
            'refund_section'        => $refund_data,
            'footer_data'           => $request_footer_data,
        ];

        $response = new GetTripDetailResponse($return_array);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getTripDetails()


    /**
     * Create Booking Request
     *
     * @param \App\Http\PostBookingRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/booking/request",
     *     tags={"Request"},
     *     description="Create booking request",
     *     operationId="booking.post.bookingrequest",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/checkin_in_form"),
     * @SWG\Parameter(ref="#/parameters/checkout_in_form"),
     * @SWG\Parameter(ref="#/parameters/guests_in_form"),
     * @SWG\Parameter(ref="#/parameters/units_in_form"),
     * @SWG\Parameter(ref="#/parameters/payment_method_in_form"),
     * @SWG\Parameter(ref="#/parameters/payable_amount_in_form"),
     * @SWG\Parameter(ref="#/parameters/coupon_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/apply_wallet_in_form"),
     * @SWG\Parameter(ref="#/parameters/force_create_in_form"),
     *
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing request id and instant book",
     * @SWG\Schema(
     * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",   ref="#definitions/PostBookingRequestResponse"),
     * @SWG\Property(property="error",  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Chosen payment method is not available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Mobile number not verified. Please login using email!",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function postBookingRequest(PostBookingRequest $request)
    {
        $input_params = $request->input();

        $device_source = $request->getDeviceType();

        $property_hash_id = $input_params['property_hash_id'];
         // Get request parameters (if not entered, take default values).
        $guests = $input_params['guests'];

        $units = $input_params['units'];

        $start_date = $input_params['checkin'];
        $end_date   = $input_params['checkout'];

        $coupon_code  = $input_params['coupon_code'];
        $apply_wallet = $input_params['apply_wallet'];

        $selected_payment_method = $input_params['payment_method'];
        $selected_payable_amount = $input_params['payable_amount'];

        $force_create = $input_params['force_create'];

        $user             = $request->getLoggedInUser();
        $currency         = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        $device_unique_id = $request->getDeviceUniqueId();

         // Decode property_id from the hash id visible in url.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        if ((string) $user->contact === '' || (int) $user->mobile_verify === 0) {
            return ApiResponse::forbiddenError(EC_CONTACT_NOT_VERIFIED, 'Mobile number is not verified.');
        }

        if ($force_create !== 1) {
            // Old request.
            // phpcs:ignore
            $old_request = BookingRequest::where('pid', '=', $property_id)->where('from_date', '=', $start_date)->where('to_date', '=', $end_date)->where('units', '=', $units)->where('guests', '=', $guests)->where('traveller_id', '=', $user->id)->whereIn('booking_status', [NEW_REQUEST, REQUEST_APPROVED, BOOKED])->select('id', 'booking_status')->first();

            if (empty($old_request) === false) {
                $request_text   = "You've already placed a request for these dates on this property. Are you sure you want to submit another request?";
                $booking_text   = "You've already booked this place for these dates. Are you sure you want to create another booking request?";
                $request_header = 'Duplicate request';
                $booking_header = 'Duplicate booking';

                $response_data = [
                    'valid'                  => 1,
                    'message'                => ($old_request->booking_status === BOOKED) ? $booking_text : $request_text,
                    'msg_code'               => ($old_request->booking_status === BOOKED) ? 'trip_exists' : 'request_exists',
                    'booking_status'         => $old_request->booking_status,
                    'request_id'             => Helper::encodeBookingRequestId($old_request->id),
                    'instant_book'           => 0,
                    'user_id'                => 0,
                    'payment_url'            => '',
                    'payment_gateway_method' => APP_PAYMENT_METHOD['method'],
                    'header'                 => ($old_request->booking_status === BOOKED) ? $booking_header : $request_header,
                ];

                $response = new PostBookingRequestResponse($response_data);
                $response = $response->toArray();
                return ApiResponse::success($response);
            }//end if
        }//end if

        $property = Property::getPropertyDetailsForPreviewPageById($property_id, $guests, $units);

        if (count($property) === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Array of data to process and get property pricing.
        $property_pricing_data = [
            'property_id'             => $property_id,
            'start_date'              => $start_date,
            'end_date'                => $end_date,
            'units'                   => $units,
            'guests'                  => $guests,
            'user_currency'           => $currency,
            'property_currency'       => $property['currency'],
            'per_night_price'         => $property['per_night_price'],
            'per_week_price'          => $property['per_week_price'],
            'per_month_price'         => $property['per_month_price'],
            'gh_commission'           => (int) $property['gh_commission'],
            'markup_service_fee'      => (int) $property['markup_service_fee'],
            'additional_guest_fee'    => $property['additional_guest_fee'],
            'cleaning_fee'            => $property['cleaning_fee'],
            'cleaning_mode'           => $property['cleaning_mode'],
            'service_fee'             => $property['service_fee'],
            'custom_discount'         => $property['custom_discount'],
            'fake_discount'           => $property['fake_discount'],
            'accomodation'            => $property['accomodation'],
            'additional_guest_count'  => $property['additional_guest_count'],
            'property_units'          => $property['units'],
            'instant_book'            => $property['instant_book'],
            'min_nights'              => $property['min_nights'],
            'max_nights'              => $property['max_nights'],
            'room_type'               => $property['room_type'],
            'bedrooms'                => $property['bedrooms'],
            'user'                    => $user,
            'pc_properly_commission'  => $property['pc_properly_commission'],
            'pmt_properly_commission' => $property['pmt_properly_commission'],
        ];

        // Get property pricing details.
        $property_pricing = PropertyPricingService::getPropertyPrice($property_pricing_data);

        // phpcs:ignore
        if ($property_pricing['error'] != '') {
            $response_data = [
                'valid'                  => 0,
                'message'                => $property_pricing['error'],
                'msg_code'               => $property_pricing['error_code'],
                'booking_status'         => 0,
                'request_id'             => '',
                'instant_book'           => $property_pricing['is_instant_bookable'],
                'user_id'                => 0,
                'payment_url'            => '',
                'payment_gateway_method' => APP_PAYMENT_METHOD['method'],
                'header'                 => '',
            ];

            $response = new PostBookingRequestResponse($response_data);
            $response = $response->toArray();
            return ApiResponse::success($response);
        }

        $discount = 0;
        // Fix this discount data.
        $discount_data             = [];
        $discounted_gh_service_fee = 0;
        $discounted_host_fee       = 0;

        $booking_amount = $property_pricing['total_price_all_nights'];

        // Fix only one can apply.
        if ($coupon_code !== '') {
            $coupon_data = [
                'coupon_code'      => $coupon_code,
                'property_city'    => $property['city'],
                'property_state'   => $property['state'],
                'booking_currency' => $currency,
                'booking_amount'   => $booking_amount,
                'host_fee'         => $property_pricing['total_host_fee'],
                'user_id'          => ($user !== null) ? $user->id : 0,
                'gh_commission'    => (int) $property['gh_commission'],
                'is_mobile_app'    => ($device_source === 'app') ? true : false,
                'property_type'    => $property['property_type'],
                'from_date'        => $start_date,
                'to_date'          => $end_date,
            ];

            $coupon = CouponService::checkCouponValidity($coupon_data);

            // Add data to coupon usage.
            if ($coupon['status'] === 1) {
                $discount      = $coupon['total_discount'];
                $discount_data = [
                    'coupon_applied'     => $coupon_code,
                    'coupon_id'          => $coupon['coupon_id'],
                    'coupon_amount'      => $coupon['total_discount'],
                    'gh_coupon_amount'   => $coupon['gh_discount_amount'],
                    'host_coupon_amount' => $coupon['host_discount_amount'],
                ];

                $discounted_gh_service_fee = $coupon['gh_discount_amount'];
                $discounted_host_fee       = $coupon['host_discount_amount'];
            }
        }//end if

        if (empty($apply_wallet) === false && $user !== null) {
                $wallet = CouponService::checkWalletDiscount($booking_amount, $currency, $user->id, $user->usable_wallet_balance, $user->wallet_currency);

            if ($wallet['status'] === 1) {
                $discount      = $wallet['amount'];
                $discount_data = [
                    'wallet_money_applied' => $wallet['amount'],
                ];

                $discounted_gh_service_fee = $wallet['amount'];
            }
        }

         // Reduce service fee after discount.
        $property_pricing['total_service_fee'] = ($property_pricing['total_service_fee'] - $discounted_gh_service_fee);
        $property_pricing['total_host_fee']    = ($property_pricing['total_host_fee'] - $discounted_host_fee);

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

        $booking_amount_with_coupon_wallet_discount = ($booking_amount - $discount);

        $booking_amount_with_coupon_wallet_discount_with_cleaning = ($booking_amount_with_coupon_wallet_discount + $property_pricing['cleaning_price']);

        $property_pricing['total_price_all_nights_with_cleaning_price_gst_with_wallet_coupon_discount'] = ($booking_amount_with_coupon_wallet_discount_with_cleaning + $property_pricing['gst_amount']);

        // Discount.
        $payable_amount = $property_pricing['total_price_all_nights_with_cleaning_price_gst_with_wallet_coupon_discount'];

        // Write better version for Get coupon or wallet and match.
        $released_payment = CouponService::getReleasedPayment($user);

        $released_payment_refund_amount = 0;

        // THIS released_payment_refund_amount is pending amount after deducting payabel amount
        // Need to refund this.
        if ($released_payment > $payable_amount) {
            $released_payment_refund_amount = ($released_payment - $payable_amount);
            $used_released_payment_amount   = $payable_amount;

            $payable_amount = 0;
        } else {
            $payable_amount                 = ($payable_amount - $released_payment);
            $released_payment_refund_amount = 0;
            $used_released_payment_amount   = $released_payment;
        }

        // Round Off payable amount.
        $payable_amount = round($payable_amount, 2);

            $cancellation_policy = CancellationPolicy::getCancellationPoliciesByIds([$property['cancelation_policy']]);
            // Parameters to fetch payment methods and label to display.
            $payment_methods_params = [
                'is_instant_bookable'            => $property_pricing['is_instant_bookable'],
                'service_fee'                    => $property_pricing['total_service_fee'],
                'gh_commission'                  => $property_pricing['gh_commission_percent'],
                'coa_fee'                        => $property_pricing['coa_fee'],
                'gst'                            => $property_pricing['gst_amount'],
                'cash_on_arrival'                => $property['cash_on_arrival'],
                'booking_amount'                 => $booking_amount,
                'released_payment_refund_amount' => $released_payment_refund_amount,
                'payable_amount'                 => $payable_amount,
                'prive'                          => $property['prive'],
                'cancelation_policy'             => $property['cancelation_policy'],
                'payment_gateway_enabled'        => 1,
                'checkin'                        => $start_date,
                'policy_days'                    => $cancellation_policy[$property['cancelation_policy']]['policy_days'],
                'user_currency'                  => $currency,
                'prive_property_coa_max_amount'  => Helper::convertPriceToCurrentCurrency('INR', PRIVE_PROPERTY_COA_MAX_AMOUNT, $currency),
                'partial_payment_coa_max_amount' => Helper::convertPriceToCurrentCurrency('INR', PARTIAL_PAYMENT_COA_MAX_AMOUNT, $currency),
                'checkin_formatted'              => Carbon::parse($start_date)->format('d M Y'),
                'markup_service_fee'             => $property_pricing['total_markup_fee'],
                'total_host_fee'                 => $property_pricing['total_host_fee'],
            ];

            // Get payment methods and label to display.
            $payment_methods = PaymentMethodService::getPaymentMethods($payment_methods_params);

            // Selected method must be availble while creating request.
            if (in_array($selected_payment_method, array_keys($payment_methods)) === false) {
                return ApiResponse::badRequestError(EC_PAYMENT_METHOD_NOT_AVAILABLE, 'Chosen payment method is not available.');
            }

            $choose_payment = Helper::getOldPaymentMethodName($selected_payment_method);

            // One of various conditons to be checked.
            if ((int) $payable_amount !== (int) $selected_payable_amount) {
                return ApiResponse::validationFailed(['payable_amount' => 'The payable amount field is invalid.']);
            }

            // Fix this later.
            $source      = $device_source;
            $device_type = (empty($device_source) === false) ? $device_source : 'desktop';

            $booking_status = NEW_REQUEST;
            if ((int) $property_pricing['is_instant_bookable'] === 1) {
                $booking_status = REQUEST_APPROVED;
            }

            // Per night per unit price  + cleaning price per night per unit ) this include service fee.
            $price_per_night                = ($property_pricing['per_night_per_unit_price'] + $property_pricing['cleaning_price_per_unit']);
            $price_per_night_without_markup = ($property_pricing['per_night_per_unit_price_without_markup'] + $property_pricing['cleaning_price_per_unit']);

            // This is set to 0 by default by mistake but all other calculation are.
            // property inclucing service fee.
            // checking invetory princg line no 72 (guesthouser5).
            $service_fee_per_unit = 0;

            $price_details = array_merge(
                $discount_data,
                [
                    'currency'                                                           => Helper::getCurrencySymbol($property_pricing['currency']),
                    'per_night_price'                                                    => $price_per_night,
                    'per_night_price_without_markup'                                     => $price_per_night_without_markup,
                    // Not using this anywhere.
                    'per_night_price_with_guests'                                        => ($price_per_night + $property_pricing['per_night_all_guest_extra_guest_price']),
                    'total_nights'                                                       => $property_pricing['no_of_nights'],
                    'service_fee_per_unit'                                               => $service_fee_per_unit,
                    'cleaning_price_per_unit'                                            => $property_pricing['cleaning_price_per_unit'],
                    'per_unit_cost'                                                      => ($price_per_night * $property_pricing['no_of_nights']),
                    'units_occupied'                                                     => $property_pricing['required_units'],
                    'sub_total'                                                          => $property_pricing['total_price_all_nights'],
                    'extra_guests'                                                       => $property_pricing['total_extra_guests'],
                    'extra_guest_cost'                                                   => ($property_pricing['per_night_all_guest_extra_guest_price'] * $property_pricing['no_of_nights']),
                    'extra_guest_cost_without_markup'                                    => ($property_pricing['per_night_all_guest_extra_guest_price_without_markup'] * $property_pricing['no_of_nights']),
                    // This is discount comes from  inventory pricing fix this.
                    'discount'                                                           => 0,
                    'prevous_booking_credits'                                            => $used_released_payment_amount,
                    'released_payment_refund_amount'                                     => $released_payment_refund_amount,
                    'payable_amount'                                                     => $payable_amount,
                    'service_percentage'                                                 => $property_pricing['service_fee_percentage'],
                    'coa_charges'                                                        => $property_pricing['coa_fee'],
                    'coa_fee_percentage_slab'                                            => $property_pricing['coa_fee_percentage_slab'],
                    'custom_discount'                                                    => $property['custom_discount'],
                    'host_fee'                                                           => $property_pricing['total_host_fee'],
                    'service_fee'                                                        => $property_pricing['total_service_fee'],
                    'service_fee_on_price_with_discount_per_unit_per_night'              => $property_pricing['service_fee_on_price_with_discount_per_unit_per_night'],
                     // Multiply this with all extra guests to get all extra guests service fee.
                    'service_fee_on_extra_guest_price_with_discount_per_guest_per_night' => $property_pricing['service_fee_on_extra_guest_price_with_discount_per_guest_per_night'],
                    'markup_service_fee'                                                 => $property_pricing['total_markup_fee'],
                    'markup_service_fee_percent'                                         => $property_pricing['gh_markup_percentage'],
                    'gst_percentage'                                                     => $property_pricing['gst_percent'],
                    'gst_amount'                                                         => $property_pricing['gst_amount'],
                    'gh_gst_percentage'                                                  => $gst['gh_gst_percentage'],
                    'gh_gst_component'                                                   => $gst['gh_gst'],
                    'host_gst_percentage'                                                => $gst['host_gst_percentage'],
                    'host_gst_component'                                                 => $gst['host_gst'],
                    'discount_data'                                                      => $property_pricing['discount_percentage_per_date'],
                    'overall_discount'                                                   => $property_pricing['effective_discount_percentage'],
                    'choose_payment'                                                     => $choose_payment,
                    'chosen_payment_method'                                              => $selected_payment_method,
                    'currency_code'                                                      => $property_pricing['currency'],
                    'currency_conversion_rate'                                           => Helper::getCurrencyExchanegRate($currency),
                    'property_currency_code'                                             => $property['currency'],
                    'property_currency_conversion_rate'                                  => Helper::getCurrencyExchanegRate($property['currency']),
                ]
            );

            $params = [
                'currency'              => $property_pricing['currency'],
                'property_id'           => $property_id,
                'host_id'               => $property['user_id'],
                'user_id'               => $user->id,
                'units'                 => $property_pricing['required_units'],
                'bedrooms'              => $property_pricing['required_bedrooms'],
                'start_date'            => $start_date,
                'end_date'              => $end_date,
                // Write better version read from property pricing.
                'guests'                => $guests,
                'price_details'         => $price_details,
                'payable_amount'        => $payable_amount,
                'gh_commission_percent' => $property_pricing['gh_commission_percent'],
                'gst_percent'           => $property_pricing['gst_percent'],
                'cancelation_policy'    => $property['cancelation_policy'],
                'cash_on_arrival'       => $property['cash_on_arrival'],
                'prive'                 => $property['prive'],
                'source'                => $source,
                'device_type'           => $device_type,
                'booking_status'        => $booking_status,
                'instant_book'          => $property_pricing['is_instant_bookable'],
                'device_unique_id'      => $device_unique_id,
                'valid_till'            => BookingRequestService::getValidTillTime(Carbon::now('GMT')->toDateTimeString(), $start_date),
                'approve_till'          => BookingRequestService::getApproveTillTime(Carbon::now('GMT')->toDateTimeString(), $start_date),
                'properly_commission'   => $property_pricing['properly_commission'],
            ];

            $booking_request = BookingRequestService::createBookingRequest($params, $user);

            if ($booking_status === NEW_REQUEST) {
                // Send new request email to host.
                $new_request_event = new CreateBookingRequest($booking_request, $property['title'], $property['host_email'], $property['host_fullname'], $property['host_dial_code'], $property['host_contact'], $user->getUserFullName());
                Event::dispatch($new_request_event);
            }//end if

            $url = '';
            if ($property_pricing['is_instant_bookable'] === 1) {
                $url = SITE_URL.'/v1.6/booking/payment/'.Helper::encodeBookingRequestId($booking_request->id).'?source='.$device_source;
            }

            $payment_method = 'web';

            if ($device_source === 'app' && $selected_payment_method !== 'si_payment') {
                $payment_method = APP_PAYMENT_METHOD['method'];
            }

            $response_data = [
                'valid'                  => 1,
                'message'                => 'Request Created',
                'msg_code'               => 'request_created',
                'booking_status'         => $booking_status,
                'request_id'             => Helper::encodeBookingRequestId($booking_request->id),
                'instant_book'           => $property_pricing['is_instant_bookable'],
                'user_id'                => $user->id,
                'payment_url'            => $url,
                'payment_gateway_method' => $payment_method,
                'header'                 => '',
            ];

            $response = new PostBookingRequestResponse($response_data);
            $response = $response->toArray();
            return ApiResponse::success($response);

    }//end postBookingRequest()


    /**
     *  Update Booking Request.
     *
     * @param \App\Http\PutBookingRequest $request         Http request object.
     * @param string                      $request_hash_id Request hash id.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/booking/request/{{request_hash_id}}",
     *     tags={"Request"},
     *     description="Update booking request",
     *     operationId="booking.put.bookingrequest",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},

     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/payment_method_in_form"),
     * @SWG\Parameter(ref="#/parameters/payable_amount_in_form"),
     * @SWG\Parameter(ref="#/parameters/coupon_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/apply_wallet_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing request id and instant book",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutBookingRequestResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Chosen payment method is not available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Mobile number not verified. Please login using email!",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Booking Request not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putBookingRequest(PutBookingRequest $request, string $request_hash_id)
    {
        $applied_discounted_host_fee       = 0;
        $applied_discounted_gh_service_fee = 0;
        $discounted_gh_service_fee         = 0;
        $discounted_host_fee               = 0;
        $applied_wallet                    = 0;
        $applied_coupon_code               = '';
        $applied_discount_amount           = 0;
        $applied_released_amount           = 0;
        $applied_coupon_usage_id           = 0;
        $coupon_change = true;
        $coupon_data   = [];

        $discount_valid            = 0;
        $discount                  = 0;
        $discounted_gh_service_fee = 0;
        $discounted_host_fee       = 0;

        $discount_data = [];
        // Validate params.
        $input_params  = $request->input();
        $device_source = $request->getDeviceType();

        $user             = $request->getLoggedInUser();
        $currency         = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        $device_unique_id = $request->getDeviceUniqueId();

         // Decode property_id from the hash id visible in url.
        $booking_request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $booking_request = BookingRequest::getBookingRequestForPreviewPageByUserId($booking_request_id, $user->id);
        if (empty($booking_request) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid Booking Request.');
        }

        // $payment_status = PaymentTracking::getIsRequestPaymentInitated($booking_request_id);
        // if ($payment_status === true) {
        // return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid Booking Request3.');
        // }
        $property_id        = $booking_request['pid'];
        $guests             = $booking_request['guests'];
        $units              = $booking_request['units'];
        $start_date         = $booking_request['from_date'];
        $end_date           = $booking_request['to_date'];
        $price_details      = json_decode($booking_request['price_details']);
        $no_of_nights       = $price_details->total_nights;
        $currency           = $booking_request['currency'];
        $applied_gst_amount = $price_details->gst_amount;
        $applied_total_service_fee = $price_details->service_fee;
        $applied_total_host_fee    = $price_details->host_fee;
        $applied_wallet_money      = 0;

        $per_unit_guests = ceil(($guests - $price_details->extra_guests) / $no_of_nights);

        $cleaning_price_per_unit = (isset($price_details->cleaning_price_per_unit) === true) ? $price_details->cleaning_price_per_unit : 0;

        $coa_fee = (isset($price_details->coa_charges) === true) ? $price_details->coa_charges : 0;

        $booking_amount = ((($price_details->per_night_price * $units) + $price_details->extra_guest_cost ) * $no_of_nights);
        $payable_amount = $price_details->payable_amount;

        $new_payment_method = (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment';

        $applied_payment_method = (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : $new_payment_method;

        $gh_coupon_amount = (isset($price_details->gh_coupon_amount) === true) ? $price_details->gh_coupon_amount : '';

        $coupon_amount = (isset($price_details->coupon_amount) === true) ? $price_details->coupon_amount : '';

        $host_coupon_amount = (isset($price_details->host_coupon_amount) === true) ? $price_details->host_coupon_amount : '';

        $coupon_usage_id = (isset($price_details->coupon_usage_id) === true) ? $price_details->coupon_usage_id : '';

         // If Booking requests already has coupon.
        if (property_exists($price_details, 'coupon_applied') === true) {
            $applied_coupon_code               = $price_details->coupon_applied;
            $applied_discount_amount           = $coupon_amount;
            $applied_discounted_gh_service_fee = $gh_coupon_amount;
            $applied_discounted_host_fee       = $host_coupon_amount;
            $applied_coupon_usage_id           = $coupon_usage_id;
        }

        if (property_exists($price_details, 'wallet_money_applied') === true) {
            $applied_wallet_money              = $price_details->wallet_money_applied;
            $applied_discount_amount           = $price_details->wallet_money_applied;
            $applied_wallet                    = 1;
            $applied_discounted_gh_service_fee = $price_details->wallet_money_applied;
        }

        if (property_exists($price_details, 'used_released_payment_amount') === true) {
            $applied_released_amount = $price_details->used_released_payment_amount;
        } else if (property_exists($price_details, 'prevous_booking_credits') === true) {
            $applied_released_amount = $price_details->prevous_booking_credits;
        }

        $apply_wallet            = (isset($input_params['apply_wallet']) === true) ? $input_params['apply_wallet'] : $applied_wallet;
        $coupon_code             = (isset($input_params['coupon_code']) === true) ? $input_params['coupon_code'] : $applied_coupon_code;
        $selected_payment_method = (empty($input_params['payment_method']) === false) ? $input_params['payment_method'] : $applied_payment_method;

        $selected_payable_amount = $input_params['payable_amount'];

        $property = Property::getPropertyDetailsForPreviewPageById($property_id, $guests, $units);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid Booking Request.');
        }

        $cancellation_policy = CancellationPolicy::getCancellationPoliciesByIds([$booking_request['cancellation_policy']]);

        // If coupon invalid send result back.
        if ((isset($input_params['apply_wallet']) === false || empty($input_params['apply_wallet']) === true) && empty($coupon_code) === false) {
            // Removing earlier applied any values.
            $coupon_data = [
                'coupon_code'        => $coupon_code,
                'property_city'      => $property['city'],
                'property_state'     => $property['state'],
                'booking_currency'   => $currency,
                'booking_amount'     => $booking_amount,
                'host_fee'           => ($applied_total_host_fee + $applied_discounted_host_fee),
                'user_id'            => $user->id,
                'gh_commission'      => (int) $booking_request['commission_from_host'],
                'is_mobile_app'      => ($device_source === 'app') ? true : false,
                'property_type'      => $property['property_type'],
                'from_date'          => $start_date,
                'to_date'            => $end_date,
                'booking_request_id' => $booking_request_id,
            ];

            $coupon         = CouponService::checkCouponValidity($coupon_data);
            $discount_valid = $coupon['status'];

            if ($discount_valid === 1) {
                $discount                  = $coupon['total_discount'];
                $discounted_gh_service_fee = $coupon['gh_discount_amount'];
                $discounted_host_fee       = $coupon['host_discount_amount'];

                $discount_data = [
                    'coupon_applied'     => $coupon_code,
                    'coupon_amount'      => $discount,
                    'gh_coupon_amount'   => $discounted_gh_service_fee,
                    'host_coupon_amount' => $discounted_host_fee,
                    'coupon_id'          => $coupon['coupon_id'],
                ];

                if ($applied_coupon_code === $coupon_code) {
                    $coupon_change = false;
                }
            }
        }//end if

        // Adding applied wallet money as after creating booking request we deduct usable balance.
        $wallet = CouponService::checkWalletDiscount($booking_amount, $currency, $user->id, ($user->usable_wallet_balance + $applied_wallet_money), $user->wallet_currency);

        $wallet_money           = $wallet['amount'];
        $wallet_currency_symbol = $wallet['currency_symbol'];
        $wallet_applicable      = $wallet['status'];

        if (isset($input_params['coupon_code']) === false && empty($apply_wallet) === false) {
            $discount_valid            = $wallet['status'];
            $discount                  = $wallet['amount'];
            $discount_message          = $wallet['message'];
            $discount_type             = 'wallet';
            $discounted_gh_service_fee = $wallet_money;

            if ($wallet['status'] === 1) {
                $discount_data = ['wallet_money_applied' => $discount];
            }
        }

         // Reduce service fee after discount.
        $total_service_fee = ($applied_total_service_fee + $applied_discounted_gh_service_fee - $discounted_gh_service_fee);
        $total_host_fee    = ($applied_total_host_fee + $applied_discounted_host_fee - $discounted_host_fee);

        $payable_amount = ($payable_amount + $applied_discount_amount - $discount);

        $gh_commission_from_host = (($total_host_fee * $booking_request['commission_from_host']) / 100);

        $host_amount = ($total_host_fee - $gh_commission_from_host);

        $markup_service_fee = (isset($price_details->markup_service_fee) === true) ? $price_details->markup_service_fee : 0;

        $gst = helper::calculateGstAmount($host_amount, $property['room_type'], $booking_request['bedroom'], $currency, $no_of_nights, $units, $total_service_fee, $markup_service_fee, $gh_commission_from_host);

        $gst_percent = $gst['host_gst_percentage'];

        $gst_amount = $gst['total_gst'];

        $payable_amount = ($payable_amount - $applied_gst_amount + $gst_amount);

        $released_payment_refund_amount = 0;
        $used_released_payment_amount   = 0;

        // Adding previous subtracted released payment amount if any.
        $payable_amount = ($payable_amount + $applied_released_amount);

        // Adding applied released payment as this was dedeucted from boking credits while creating.
        $released_payment = ($user->booking_credits + $applied_released_amount > 0) ? ($user->booking_credits + $applied_released_amount) : 0;

        if ($released_payment > $payable_amount) {
            $released_payment_refund_amount = ($released_payment - $payable_amount);
            $used_released_payment_amount   = $payable_amount;
            $payable_amount                 = 0;
        } else {
            $payable_amount                 = ($payable_amount - $released_payment);
            $released_payment_refund_amount = 0;
            $used_released_payment_amount   = $released_payment;
        }

        // CHECK ALL OTHER PROPERTIS AS WELL THAT CHANGE.
        $payment_methods_params = [
            'is_instant_bookable'            => $booking_request['instant_book'],
            'service_fee'                    => $total_service_fee,
            'gh_commission'                  => $booking_request['commission_from_host'],
            'coa_fee'                        => $coa_fee,
            'gst'                            => $gst_amount,
            'cash_on_arrival'                => $booking_request['coa_available'],
            'booking_amount'                 => $booking_amount,
            'released_payment_refund_amount' => $released_payment_refund_amount,

            'payable_amount'                 => $payable_amount,
            'prive'                          => $booking_request['prive'],
            'cancelation_policy'             => $property['cancelation_policy'],
            'payment_gateway_enabled'        => 1,
            'checkin'                        => $start_date,
            'policy_days'                    => $cancellation_policy[$booking_request['cancellation_policy']]['policy_days'],
            'user_currency'                  => $currency,
            'prive_property_coa_max_amount'  => Helper::convertPriceToCurrentCurrency('INR', PRIVE_PROPERTY_COA_MAX_AMOUNT, $currency),
            'partial_payment_coa_max_amount' => Helper::convertPriceToCurrentCurrency('INR', PARTIAL_PAYMENT_COA_MAX_AMOUNT, $currency),
            'checkin_formatted'              => Carbon::parse($start_date)->format('d M Y'),
            'markup_service_fee'             => (isset($price_details->markup_service_fee) === true ) ? $price_details->markup_service_fee : 0,
            'total_host_fee'                 => $total_host_fee,
        ];

        // Get payment methods and label to display.
        $payment_methods = PaymentMethodService::getPaymentMethods($payment_methods_params);

        // Selected method must be availble while creating request.
        if (in_array($selected_payment_method, array_keys($payment_methods)) === false) {
            return ApiResponse::badRequestError(EC_PAYMENT_METHOD_NOT_AVAILABLE, 'Chosen payment method is not available.');
        }

        $choose_payment = Helper::getOldPaymentMethodName($selected_payment_method);

         // One of various conditons to be checked.
        // Selected_payable_amount already tpecast in request model.
        if ((int) $payable_amount !== $selected_payable_amount) {
            return ApiResponse::validationFailed(['payable_amount' => 'The payable amount field is invalid.']);
        }

        // Round payable amount.
        $payable_amount = round($payable_amount, 2);

        // Remove old keys.
        $keys_to_remove = [
            'coupon_applied',
            'coupon_amount',
            'coupon_id',
            'coupon_usage_id',
            'gh_coupon_amount',
            'host_coupon_amount',
            'wallet_money_applied',
            'used_released_payment_amount',
            'prevous_booking_credits',
            'released_payment_refund_amount',
        ];

        // Temp solution.
        $price_details = (array) $price_details;

        $price_details = array_diff_key($price_details, array_flip($keys_to_remove));

        $price_details = array_merge(
            $price_details,
            $discount_data,
            [
                'payable_amount'                 => $payable_amount,
                'prevous_booking_credits'        => $used_released_payment_amount,
                'released_payment_refund_amount' => $released_payment_refund_amount,
                'host_fee'                       => $total_host_fee,
                'service_fee'                    => $total_service_fee,
                'gst_percent'                    => $gst_percent,
                'gst_amount'                     => $gst_amount,
                'gh_gst_percentage'              => $gst['gh_gst_percentage'],
                'gh_gst_component'               => $gst['gh_gst'],
                'host_gst_percentage'            => $gst['host_gst_percentage'],
                'host_gst_component'             => $gst['host_gst'],
                'chosen_payment_method'          => $selected_payment_method,
                'choose_payment'                 => $choose_payment,
            ]
        );

        $params = [
            'price_details'  => json_encode($price_details),
            'payable_amount' => $payable_amount,
            'GST'            => $gst_percent,
        ];

        $where = [
            'id'             => $booking_request_id,
            'booking_status' => REQUEST_APPROVED,
        ];

        $extra_params = [
            'coupon_change'           => $coupon_change,
            'discount_data'           => array_merge(
                $discount_data,
                [
                    'currency_code' => $currency,
                    'user_id'       => $user->id,
                    'request_id'    => $booking_request['id'],
                ]
            ),
            'applied_coupon_usage_id' => $applied_coupon_usage_id,
            'applied_wallet_money'    => $applied_wallet_money,
            'applied_released_amount' => $applied_released_amount,
            'prevous_booking_credits' => $used_released_payment_amount,
        ];

        $booking_request_updated = BookingRequestService::updateBookingRequest($where, $params, $extra_params, $user);

        if ($booking_request_updated === 1) {
            $url = SITE_URL.'/v1.6/booking/payment/'.Helper::encodeBookingRequestId($booking_request_id).'?source='.$device_source;

            $payment_method = 'web';

            if ($device_source === 'app' && $selected_payment_method !== 'si_payment') {
                $payment_method = APP_PAYMENT_METHOD['method'];
            }

            $response_data = [
                'valid'                  => 1,
                'message'                => 'request_updated',
                'request_id'             => Helper::encodeBookingRequestId($booking_request_id),
                'instant_book'           => 1,
                'payment_url'            => $url,
                'payment_gateway_method' => $payment_method,
            ];

            $response = new PutBookingRequestResponse($response_data);
            $response = $response->toArray();
            return ApiResponse::success($response);
        }//end if

        return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid Booking Request.');

    }//end putBookingRequest()


    /**
     * Resend Booking Request.
     *
     * @param \App\Http\PostBookingRequestResendRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/booking/request/resend",
     *     tags={"Request"},
     *     description="Resend booking request",
     *     operationId="booking.post.resendbookingrequest",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     *
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing request id and instant book",
     * @SWG\Schema(
     * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",   ref="#definitions/PostBookingRequestResendResponse"),
     * @SWG\Property(property="error",  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Chosen payment method is not available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Mobile number not verified. Please login using email!",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function postResendBookingRequest(PostBookingRequestResendRequest $request)
    {
        // Validate params.
        $input_params = $request->input();

        $device_source = $request->getDeviceType();

        $request_hash_id  = $input_params['request_hash_id'];
        $user             = $request->getLoggedInUser();
        $currency         = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        $device_unique_id = $request->getDeviceUniqueId();

         // Decode request_id from the hash id visible in parameter.
        $request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        if ((string) $user->contact === '' || (int) $user->mobile_verify === 0) {
            return ApiResponse::forbiddenError(EC_CONTACT_NOT_VERIFIED, 'Mobile number is not verified.');
        }

        // Old request.
        // phpcs:ignore
        $old_request = BookingRequest::where('id', '=', $request_id)->where('from_date', '>=', Carbon::now('Asia/Kolkata')->format('Y-m-d'))->where('traveller_id', '=', $user->id)->whereIn('booking_status', [EXPIRED])->first();

        if (empty($old_request) === true) {
             return ApiResponse::errorMessage('Invalid Booking Request');
        }

        $updated = new Carbon($old_request->updated_at);
        $now     = Carbon::now('GMT');
        $diff    = $updated->diffInMinutes($now, false);

        if ($diff > 30) {
             return ApiResponse::errorMessage('Booking expired');
        }

        $property_id        = $old_request->pid;
        $guests             = $old_request->guests;
        $bedrooms           = $old_request->bedroom;
        $units              = $old_request->units;
        $start_date         = $old_request->from_date;
        $end_date           = $old_request->to_date;
        $price_details      = json_decode($old_request->price_details, true);
        $no_of_nights       = $price_details['total_nights'];
        $currency           = $old_request->currency;
        $applied_gst_amount = $price_details['gst_amount'];
        $applied_total_service_fee = $price_details['service_fee'];
        $applied_total_host_fee    = $price_details['host_fee'];
        $payable_amount            = $price_details['payable_amount'];

        $new_payment_method = (isset($price_details['choose_payment']) === true ) ? Helper::getNewPaymentMethodName($price_details['choose_payment']) : 'full_payment';
        // Just for website compatibility.
        $chosen_payment_method                  = (isset($price_details['chosen_payment_method']) === true) ? $price_details['chosen_payment_method'] : $new_payment_method;
        $price_details['choose_payment']        = Helper::getOldPaymentMethodName($chosen_payment_method);
        $price_details['chosen_payment_method'] = $chosen_payment_method;

        // Fix this later.
        $source         = $device_source;
        $device_type    = (empty($device_source) === false) ? $device_source : 'desktop';
        $booking_status = REQUEST_APPROVED;

        $is_available = PropertyPricingService::isRequiredUnitsAvailableInProperty($property_id, $start_date, $end_date, $units);
        if ($is_available === false) {
            return ApiResponse::errorMessage('Booking not available');
        }

        $params = [
            'currency'              => $currency,
            'property_id'           => $property_id,
            'host_id'               => $old_request->host_id,
            'user_id'               => $user->id,
            'units'                 => $units,
            'bedrooms'              => $bedrooms,
            'start_date'            => $start_date,
            'end_date'              => $end_date,
            // Write better version read from property pricing.
            'guests'                => $guests,
            'price_details'         => $price_details,
            'payable_amount'        => $payable_amount,
            'gh_commission_percent' => $old_request->commission_from_host,
            // phpcs:ignore
            'gst_percent' => $old_request->GST,
            'cancelation_policy'    => $old_request->cancellation_policy,
            'cash_on_arrival'       => $old_request->coa_available,
            'prive'                 => $old_request->prive,
            // Change this.
            'source'                => $source,
            'device_type'           => $device_type,
            'booking_status'        => $booking_status,
            'instant_book'          => $old_request->instant_book,
            'device_unique_id'      => $device_unique_id,
            'valid_till'            => BookingRequestService::getValidTillTime(Carbon::now('GMT')->toDateTimeString(), $start_date),
            'approve_till'          => BookingRequestService::getApproveTillTime(Carbon::now('GMT')->toDateTimeString(), $start_date),
            'properly_commission'   => $old_request->properly_commission,
        ];

        $new_booking_request = BookingRequestService::createBookingRequest($params, $user);

        // Update resend status in booking request.
        $params_for_update = ['resend_reqest_sent' => 1];

        $where = ['id' => $request_id];

        $booking_request_updated = BookingRequestService::updateBookingRequest($where, $params_for_update);

        $url = '';
        if ($old_request->instant_book === 1) {
            $url = SITE_URL.'/v1.6/booking/payment/'.Helper::encodeBookingRequestId($new_booking_request->id).'?source='.$device_source;
        }

        $payment_method = 'web';

        if ($device_source === 'app' && $chosen_payment_method !== 'si_payment') {
            $payment_method = APP_PAYMENT_METHOD['method'];
        }

        $response_data = [
            'valid'                  => 1,
            'message'                => 'Request Created',
            'msg_code'               => 'request_created',
            'booking_status'         => $booking_status,
            'request_id'             => Helper::encodeBookingRequestId($new_booking_request->id),
            'instant_book'           => $old_request->instant_book,
            'payment_url'            => $url,
            'payment_gateway_method' => $payment_method,
        ];

        $response = new PostBookingRequestResendResponse($response_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postResendBookingRequest()


    /**
     * Get user booking request details
     *
     * @param \App\Http\Requests\GetBookingRequestDetailsRequest $request         Http request object.
     * @param string                                             $request_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/booking/request/{request_hash_id}",
     *     tags={"Request"},
     *     description="get details of user's booking request.",
     *     operationId="booking.get.request.id",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing request details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetRequestDetailResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="No details available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getBookingRequest(GetBookingRequestDetailsRequest $request, string $request_hash_id)
    {
        $booking_request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);
        $headers            = $request->getAllHeaders();

        $user_data = $this->getAuthUser();
        $user_id   = (int) $user_data->id;

        $booking_requests = BookingRequest::getBookingRequestByRequestIdAndUserId($booking_request_id, $user_id);
        if (count($booking_requests) === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $device_source = $request->getDeviceType();

        $booking_request = $booking_requests[0];

        $price_details = json_decode($booking_request['price_details']);

        $property_type = PropertyType::getPropertyTypeByPid($booking_request['property_id']);

        $property_tile = PropertyTileService::minPropertyTileStructure(
            [
                'id'                 => $booking_request['property_id'],
                'property_type_name' => $booking_request['property_type_name'],
                'room_type_name'     => $booking_request['room_type_name'],
                'area'               => $booking_request['area'],
                'city'               => $booking_request['city'],
                'state'              => $booking_request['state'],
                'country'            => $booking_request['country'],
                'latitude'           => $booking_request['latitude'],
                'longitude'          => $booking_request['longitude'],
                'title'              => $booking_request['title'],
                'properties_images'  => PropertyImage::getPropertiesImagesByIds([$booking_request['property_id']], $headers, 1),
                'original_title'     => true,
                'property_score'     => $booking_request['property_score'],
                'host_name'          => ucfirst($booking_request['host_name']),
                'host_image'         => $booking_request['host_image'],

            ]
        );
        $booking_request_invoice = InvoiceService::requestDetailsInvoice($booking_request);
        $from_date               = $booking_request['from_date'];
        $to_date                 = $booking_request['to_date'];
        $booking_request_status  = $booking_request['booking_status'];
        $resend_reqest_status    = $booking_request['resend_reqest_status'];

        $footer_message = '';

        if ($booking_request_status === REQUEST_APPROVED) {
            $booking_expiry_time = (strtotime($booking_request['valid_till']) - strtotime(Carbon::now('GMT')->format('Y-m-d H:i:s')));

            if ((Carbon::parse($booking_request['created_at']))->diffInHours(Carbon::parse($booking_request['valid_till'])) === 48) {
                $footer_message = "This home is available on the chosen dates. \nPlease make the payment within the next 48 hours to confirm the booking";
            } else {
                $footer_message = "This home is available on the chosen dates. \nPlease make the payment within the next 8 hours to confirm the booking";
            }
        } else if ($booking_request_status === NEW_REQUEST) {
            $booking_expiry_time = (strtotime($booking_request['approve_till']) - strtotime(Carbon::now('GMT')->format('Y-m-d H:i:s')));
            $footer_message      = "This home is not instantly bookable. \nAvailability confirmation usually takes about 8-48 hours";
        } else {
            $booking_expiry_time = 0;
        }

        $updated = new Carbon($booking_request['updated_at']);
        $now     = Carbon::now('GMT');
        $diff    = $updated->diffInMinutes($now, false);

        $resend = 0;
        if ((int) $booking_request_status === EXPIRED && $from_date >= Carbon::now('Asia/Kolkata')->format('Y-m-d') && $diff <= 30 && empty($resend_reqest_status) === true) {
            $resend = 1;
        }

        $check_other_date = 0;
        if ((int) $booking_request_status === REQUEST_REJECTED && $from_date >= Carbon::now('Asia/Kolkata')->format('Y-m-d') && $diff >= 0 && $diff <= 1440) {
            $check_other_date = 1;
        }

        $can_cancel = 0;
        if (Carbon::now('Asia/Kolkata')->format('Y-m-d') <= $from_date) {
             $cancel = BookingRequestService::getBookingRequestCancellationStatus($booking_request_status);

            if ($cancel !== false) {
                $can_cancel = 1;
            }
        }

        // Get booking status to display (along with class).
        $booking_status = Helper::getBookingStatusTextAndClass($booking_request_status);

        $resend_request = $resend;

        // Cancellation policy.
        $cancellation_policy = CancellationPolicy::getCancellationPoliciesByIds([$booking_request['cancellation_policy']])[$booking_request['cancellation_policy']];

        $coa_fee = (isset($price_details->coa_charges) === true) ? $price_details->coa_charges : 0;

        $booking_amount = ((($price_details->per_night_price * $booking_request['units']) + $price_details->extra_guest_cost ) * $price_details->total_nights);

        $released_payment_refund_amount = (isset($price_details->used_released_payment_amount) === true) ? $price_details->used_released_payment_amount : 0;

        $payable_amount = $price_details->payable_amount;
        $currency       = (empty($booking_request['currency']) === true ) ? DEFAULT_CURRENCY : $booking_request['currency'];

        $payment_methods_params = [
            'is_instant_bookable'            => $booking_request['instant_book'],
            'service_fee'                    => (isset($price_details->service_fee) === true) ? $price_details->service_fee : 0,
            'gh_commission'                  => $booking_request['commission_from_host'],
            'coa_fee'                        => $coa_fee,
            'gst'                            => (isset($price_details->gst_amount) === true) ? $price_details->gst_amount : 0,
            'cash_on_arrival'                => $booking_request['coa_available'],
            'booking_amount'                 => $booking_amount,
            'released_payment_refund_amount' => $released_payment_refund_amount,

            'payable_amount'                 => $payable_amount,
            'prive'                          => $booking_request['prive'],
            'cancelation_policy'             => $booking_request['cancellation_policy'],
            'payment_gateway_enabled'        => 1,
            'checkin'                        => $from_date,
            'policy_days'                    => $cancellation_policy['policy_days'],
            'user_currency'                  => $currency,
            'prive_property_coa_max_amount'  => Helper::convertPriceToCurrentCurrency('INR', PRIVE_PROPERTY_COA_MAX_AMOUNT, $currency),
            'partial_payment_coa_max_amount' => Helper::convertPriceToCurrentCurrency('INR', PARTIAL_PAYMENT_COA_MAX_AMOUNT, $currency),
            'checkin_formatted'              => Carbon::parse($from_date)->format('d M Y'),
            'markup_service_fee'             => (isset($price_details->markup_service_fee) === true) ? $price_details->markup_service_fee : 0,
            'total_host_fee'                 => $price_details->host_fee,
        ];

            // Get payment methods and label to display.
        $payment_methods = PaymentMethodService::getPaymentMethods($payment_methods_params);

        $new_payment_method = (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment';

        $available_method = (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : $new_payment_method;

        $display_data = [
            'service_fee'             => $price_details->service_fee,
            'cancellation_policy'     => $cancellation_policy,
            'prive'                   => $booking_request['prive'],
            'coa'                     => $booking_request['coa_available'],
            'coa_fee'                 => $coa_fee,
            'gh_commission'           => $booking_request['commission_from_host'],
            'start_date'              => $from_date,
            'payment_methods'         => $payment_methods,
            'currency'                => $currency,
            'selected_payment_method' => $available_method,
        ];

        // Get footer data and 2 divs displaying refund policy and best payment method.
        $footer_cancellation_data = PropertyService::getFooterAndCancellationPolicyDivData($display_data);

        $cancellation_reasons = CancellationReasonDetails::getCancellationReasons($booking_request_status);

        $payment_url = '';
        if ($booking_request_status === REQUEST_APPROVED) {
            $payment_url = SITE_URL.'/v1.6/booking/payment/'.$request_hash_id.'?source='.$device_source;
        }

        $payment_method = 'web';

        if ($device_source === 'app' && $available_method !== 'si_payment') {
            $payment_method = APP_PAYMENT_METHOD['method'];
        }

        $payment_option_text = PAYMENT_OPTION_TEXT[PAYMENT_NO[$available_method]]['text'];

        $booking_info_section = [
            'checkin_formatted'      => Carbon::parse($from_date)->format('d M Y'),
            'checkout_formatted'     => Carbon::parse($to_date)->format('d M Y'),
            'checkin'                => Carbon::parse($from_date)->format('d-m-Y'),
            'checkout'               => Carbon::parse($to_date)->format('d-m-Y'),
            'guests'                 => $booking_request['guests'],
            'units'                  => $booking_request['units_consumed'],
            'property_hash_id'       => Helper::encodePropertyId($booking_request['property_id']),
            'property_type'          => $property_type,
            'request_hash_id'        => $request_hash_id,
            'booking_status'         => $booking_status,
            'resend_request'         => $resend_request,
            'check_other_date'       => $check_other_date,
            'expires_in'             => ($booking_expiry_time > 0) ? $booking_expiry_time : 0,
            'payment_url'            => $payment_url,
            'payment_gateway_method' => $payment_method,
            'instant'                => $booking_request['instant_book'],
            'coupon_code_used'       => (isset($price_details->coupon_applied) === true) ? $price_details->coupon_applied : '',
            'wallet_money_used'      => (isset($price_details->wallet_money_applied) === true) ? $price_details->wallet_money_applied : 0,
            'footer_text'            => $footer_message,
        ];

        $return_array = [
            'invoice_section'      => $booking_request_invoice,
            'booking_info_section' => [
                'info'                => $booking_info_section,
                'booking_amount_info' => [
                    'total_amount_unformatted' => $payable_amount,
                    'payment_option'           => $payment_option_text,
                    'currency'                 => CURRENCY_SYMBOLS[$currency],
                ],
            ],
            'cancellation_section' => [
                'cancellation_policy_info' => $footer_cancellation_data['footer'],
                'cancellable'              => $can_cancel,
                'cancellation_reasons'     => $cancellation_reasons,
            ],
            'property_section'     => ['tile' => $property_tile],
        ];

        $response = new GetRequestDetailResponse($return_array);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getBookingRequest()


    /**
     * Get seamless payment options details
     *
     * @param \App\Http\Requests\GetSeamlessPaymentOptionsRequest $request         Http request object.
     * @param string                                              $request_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/booking/payment/options/{request_hash_id}",
     *     tags={"Seamless Payment"},
     *     description="seamless paytion options details.",
     *     operationId="booking.payment.options.get.request.id",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Response(
     *         response=200,
     *         description="Successfull fetch payment options",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetSeamlessPaymentOptionsResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="No details available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getSeamlessPaymentOptions(Request $request, string $request_hash_id)
    {
        $request_id = Helper::decodeBookingRequestId($request_hash_id);

        if (empty($request_id) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid booking request hash id.');
        }

        $payment_options = PaymentService::getSeamlessPaymentOptions($request_id);

        if ($payment_options['status'] === false && $payment_options['reason'] === EC_NOT_FOUND) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        } else if ($payment_options['status'] === false && $payment_options['reason'] === 'inventory_not_available') {
            return ApiResponse::notFoundError('inventory_not_available', 'Inventory not available.');
        } else if ($payment_options['status'] === false && $payment_options['reason'] === EC_PAYMENT_GATEWAY_NOT_SUPPORTED) {
            return ApiResponse::notFoundError(EC_PAYMENT_GATEWAY_NOT_SUPPORTED, 'This payment is not supported by gateway.');
        } else if ($payment_options['status'] === false && $payment_options['reason'] === EC_PAYMENT_GATEWAY_NOT_AVAILABLE) {
            return ApiResponse::notFoundError(EC_PAYMENT_GATEWAY_NOT_AVAILABLE, 'No payment gateway available for this payment.');
        }

        if ($payment_options['status'] === true && $payment_options['action'] === 'non_payment') {
            // Will Remove soom.
            $host      = User::getUserDataById($payment_options['booking_request']->host_id);
            $traveller = User::getUserDataById($payment_options['booking_request']->traveller_id);

            // Dispatch Event for Cashless Booking Created.
            $create_booking_event = new CreateBooking($payment_options['booking_request'], $payment_options['booking']->balance_fee, $payment_options['property'], $host, $traveller, false, false);
            Event::dispatch($create_booking_event);
        }

        return ApiResponse::success(
            [
                'action'             => $payment_options['action'],
                'reason'             => $payment_options['reason'],
                'booking_status'     => (isset($payment_options['booking_status']) === true) ? $payment_options['booking_status'] : '',
                'amount'             => (isset($payment_options['amount']) === true) ? $payment_options['amount'] : 0,
                'is_partial_payment' => $payment_options['is_partial_payment'],
                'currency'           => (isset($payment_options['currency']) === true) ? $payment_options['currency'] : [],
                'payment_method'     => $payment_options['payment_method'],
                'options'            => $payment_options['data'],
            ]
        );

    }//end getSeamlessPaymentOptions()


    /**
     * Get seamless payment payload details
     *
     * @param \App\Http\Requests\GetSeamlessPaymentPayloadRequest $request         Http request object.
     * @param string                                              $request_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/booking/payment/payload/{request_hash_id}",
     *     tags={"Seamless Payment"},
     *     description="get details of payment payload.",
     *     operationId="booking.payment.payalod.get.request.id",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/code_in_query"),
     * @SWG\Parameter(ref="#/parameters/source_in_query"),
     * @SWG\Parameter(ref="#/parameters/origin_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Successfull fetch payment payload details.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetSeamlessPaymentPayloadResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="No details available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getSeamlessPayment(Request $request, string $request_hash_id)
    {
        $source              = (empty($request->get('source')) === false) ? $request->get('source') : 'web';
        $origin              = (empty($request->get('origin')) === false) ? $request->get('origin') : '';
        $payment_option_code = (empty($request->get('code')) === false) ? $request->get('code') : 'GH_DEFAULT';

        $request_id = Helper::decodeBookingRequestId($request_hash_id);

        if (empty($request_id) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid booking request hash id.');
        }

        $request = BookingRequest::getBookingRequestById($request_id, ['*']);

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
  
        $payment_bankcode = GatewayBankCodeMapping::getPaymentOptionCode($payment_option_code, $gateway['id']);

        $payment_data = PaymentService::getSeamlessPayment($request_id, $payment_bankcode['code'], $source, $origin);

        if ($payment_data['status'] === false) {
            return ApiResponse::notFoundError($payment_data['reason'], $payment_data['message']);
        }

        $payload = [
            'action'        => (isset($payment_data['data']['action']) === true) ? $payment_data['data']['action'] : $payment_data['data']['surl'],
        // In case of Razorpay surl is action.
            'key'           => $payment_data['data']['merchant_key'],
            'hash'          => (isset($payment_data['data']['hash']) === true) ? $payment_data['data']['hash'] : '',
            'txnid'         => $payment_data['data']['txnid'],
            'amount'        => ($payment_data['data']['gateway'] !== 'razorpay') ? $payment_data['data']['amount'] : $payment_data['data']['amount_in_paisa'],
            'firstname'     => $payment_data['data']['firstname'],
            'lastname'      => (empty($payment_data['data']['lastname']) === false) ? $payment_data['data']['lastname'] : '',
            'email'         => $payment_data['data']['email'],
            'phone'         => $payment_data['data']['phone'],
            'productinfo'   => (isset($payment_data['data']['productinfo']) === true) ? $payment_data['data']['productinfo'] : '',
            'surl'          => $payment_data['data']['surl'],
            'furl'          => $payment_data['data']['furl'],
            'drop_category' => (isset($payment_data['data']['drop_category']) === true) ? $payment_data['data']['drop_category'] : '',
            'bankcode'      => $payment_data['data']['bankcode'],
        ];
     
        if (isset($payment_data['data']['si_payment']) === true && $payment_data['data']['si_payment'] === 1) {
            if ($payment_bankcode['type'] === 'debit_card' || $payment_bankcode['type'] === 'credit_card') {
                $payload['user_credentials'] = $payment_data['data']['user_credentials'];
                $payload['si']               = $payment_data['data']['si'];
            } else {
                return ApiResponse::notFoundError($payment_data['reason'], 'Payment method for si payment is invalid. Please select different payment method.');
            }
        }

        return ApiResponse::success(
            [
                'payload'       => $payload,
                'extra_payload' => $payment_data['extra_payload'],
                'booking_info'  => [
                    'booking_status' => $payment_data['booking_status'],
                    'amount'         => $payment_data['amount'],
                    'currency'       => $payment_data['currency'],
                    'gateway'        => $payment_data['gateway'],
                ],
            ]
        );

    }//end getSeamlessPayment()


     /**
      * Cancel Booking Request.
      *
      * @param \Illuminate\Http\PostBookingRequestCancelRequest $request Http request object.
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\Post(
      *     path="/v1.6/booking/request/cancel",
      *     tags={"Request"},
      *     description="Cancel booking request",
      *     operationId="booking.post.cancelbookingrequest",
      *     consumes={"application/x-www-form-urlencoded"},
      *     produces={"application/json"},
      * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
      * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
      * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
      * @SWG\Parameter(ref="#/parameters/request_status_in_form"),
      * @SWG\Parameter(ref="#/parameters/reason_id_in_form"),
      * @SWG\Parameter(ref="#/parameters/reason_detail_in_form"),
      *
      * @SWG\Response(
      *         response=200,
      *         description="Returns json containing request id and request status",
      * @SWG\Schema(
      * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
      * @SWG\Property(property="data",   ref="#definitions/PostBookingRequestCancelResponse"),
      * @SWG\Property(property="error",  ref="#/definitions/SuccessHttpResponse/properties/error"),
      *      )
      *     ),
      * @SWG\Response(
      *         response=404,
      *         description="No details available.",
      *     )
      * )
      */
    public function postCancelBookingRequest(PostBookingRequestCancelRequest $request)
    {
        $input_params = $request->input();

        $user            = $request->getLoggedInUser();
        $traveller_id    = (int) $user->id;
        $request_hash_id = $input_params['request_hash_id'];
        $selected_booking_request_status = $input_params['request_status'];

        // Check if this is valid id.
        $cancellation_reason_id      = $input_params['reason_id'];
        $cancellation_reason_details = $input_params['reason_detail'];

        $booking_request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $booking_request = BookingRequest::getBookingRequestForCancellation($booking_request_id, $traveller_id);

        if (empty($booking_request) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $booking_status = $booking_request->booking_status;

        if ((int) $booking_status !== (int) $selected_booking_request_status) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $booking_request_cancellation_status = BookingRequestService::getBookingRequestCancellationStatus($booking_status);

        if ($booking_request_cancellation_status === false) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $booking_request->booking_status = $booking_request_cancellation_status;

        $cancellation_params = [
            'user_id'            => $traveller_id,
            'booking_request_id' => $booking_request_id,
            'reason_id'          => $cancellation_reason_id,
            'reason_details'     => $cancellation_reason_details,
        ];

        BookingCancellationReason::saveBookingCancellationReason($cancellation_params);

        $refund_amount = 0;

        if (in_array($booking_status, [BOOKED]) === true) {
            InventoryPricing::increaseInventory($booking_request->pid, $booking_request->from_date, $booking_request->to_date, $booking_request->units);

            $refund_amount_params = [
                'total_charged_fee'    => $booking_request->total_charged_fee,
                'service_fee'          => $booking_request->service_fee,
                'from_date'            => $booking_request->from_date,
                'wallet_money_applied' => (empty($booking_request->price_details->wallet_money_applied) === false) ? $booking_request->price_details->wallet_money_applied : 0,
                'coa_charges'          => (empty($booking_request->price_details->coa_charges) === false) ? $booking_request->price_details->coa_charges : 0,
                'check_in_time'        => $booking_request->check_in_time,
                'cancellation_policy'  => $booking_request->cancellation_policy_id,
            ];
            $refund_amount        = RefundRequest::getRefundedAmount($refund_amount_params);

            $refund_params = [
                'user_id'            => $traveller_id,
                'booking_request_id' => $booking_request_id,
                'refund_amount'      => $refund_amount,
                'currency'           => Helper::getCurrencySymbol($booking_request->currency),
                'booking_id'         => $booking_request->booking_id,
            ];

            RefundRequest::saveRefundRequest($refund_params);

            // Optimize this.
            if (json_decode($booking_request->last_edited_by) !== null) {
                $booking_log = [
                    'id'             => $traveller_id,
                    'name'           => $booking_request->traveller_name,
                    'booking_status' => $booking_request_cancellation_status,
                    'amount_refund'  => 'Yes',
                    'date'           => date('Y-m-d h:i:sa'),
                ];
                $old_data    = json_decode($booking_request->last_edited_by);
                array_push($old_data, $booking_log);
                $booking_request->last_edited_by = json_encode($old_data);
            } else {
                $booking_log = [
                    '0' => [
                        'id'             => $traveller_id,
                        'name'           => $booking_request->traveller_name,
                        'booking_status' => $booking_request_cancellation_status,
                        'amount_refund'  => 'Yes',
                        'date'           => date('Y-m-d h:i:sa'),
                    ],
                ];
                $booking_request->last_edited_by = json_encode($booking_log);
            }//end if
        }//end if

        // Dispatch Event For Mails, Sms.
        $cancel_booking_request_event = new CancelBookingRequest(
            $booking_request,
            $booking_request->property_title,
            $booking_request->host_email,
            $booking_request->host_name,
            $user->email,
            $user->getUserFullName(),
            $refund_amount,
            $booking_request->host_dial_code,
            $booking_request->host_contact,
            $booking_request->traveller_dial_code,
            $booking_request->traveller_contact
        );

        Event::dispatch($cancel_booking_request_event);

        $booking_request->save();

        BookingRequestService::removeCouponWalletCreditsFromRequest($booking_request, $user);

        $response_message = ( $booking_status < BOOKED ) ? 'Your request has been cancelled.' : 'Your trip has been cancelled.';
        $response_data    = [
            'request_hash_id' => $request_hash_id,
            'request_status'  => $booking_request_cancellation_status,
            'message'         => $response_message,
        ];

        $response = new PostBookingRequestCancelResponse($response_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postCancelBookingRequest()


    /**
     * Send Invoice for  Booking Request.
     *
     * @param \App\Http\PostBookingRequestEmailinvoiceRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/booking/request/emailinvoice",
     *     tags={"Request"},
     *     description="Email Invoice booking request",
     *     operationId="booking.post.emailinvoice",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     *
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing request id and request status",
     * @SWG\Schema(
     * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",   ref="#definitions/PostBookingRequestEmailinvoiceResponse"),
     * @SWG\Property(property="error",  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="No details available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function postEmailInvoiceForBookingRequest(PostBookingRequestEmailinvoiceRequest $request)
    {
        $input_params = $request->input();

        $request_hash_id = $input_params['request_hash_id'];

        $booking_request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $traveller    = $request->getLoggedInUser();
        $traveller_id = (int) $traveller->id;
        $booking      = Booking::getBookingForRequestAndTravellerId($booking_request_id, $traveller_id);

        if (empty($booking) === false) {
            $property = Property::getPropertyById($booking['pid']);

            $booking_request = BookingRequest::getBookingRequestById($booking_request_id, ['*']);

            $host = User::getUserDataById($booking_request->host_id);

            // Dispatch Event for Booking Created.
            $create_booking_event = new CreateBooking($booking_request, $booking['balance_fee'], $property, $host, $traveller, true, false);
            Event::dispatch($create_booking_event);

            $response_data = ['message' => 'Email Sent Successfully'];

            $response = new PostBookingRequestEmailinvoiceResponse($response_data);
            $response = $response->toArray();
            return ApiResponse::success($response);
        } else {
            return ApiResponse::errorMessage('Incomplete Parameters');
        }//end if

    }//end postEmailInvoiceForBookingRequest()


    /**
     * Save Traveller arrival confirmation by Guest.
     *
     * @param \App\Http\PutTravellerConfirmationOnArrivalRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/booking/confirm-arrival",
     *     tags={"Request"},
     *     description="Traveller Arrival Confirmation",
     *     operationId="booking.put.confirm.arrival",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/request_status_in_form"),
     *
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing request id and request status",
     * @SWG\Schema(
     * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",   ref="#definitions/PostBookingRequestEmailinvoiceResponse"),
     * @SWG\Property(property="error",  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="No details available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putTravellerConfirmationOnArrival(PutTravellerConfirmationOnArrivalRequest $request)
    {
        $input_params = $request->input();

        $request_hash_id = $input_params['request_hash_id'];

        $status = $input_params['status'];

        $booking_request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $traveller = $request->getLoggedInUser();

        $traveller_id    = (int) $traveller->id;
        $booking_request = BookingRequest::getTripByRequestIdAndUserId($booking_request_id, $traveller_id);

        if (empty($booking_request) === false && $booking_request[0]['booking_status'] === BOOKED && $booking_request[0]['from_date'] === Carbon::now('Asia/Kolkata')->format('Y-m-d')) {
            // Need to Code here.
            // Need to save data for tracking.
            // Will write here after migrating mongo db.
            return ApiResponse::successMessage('Your response has been recorded.');
        } else {
            return ApiResponse::errorMessage('The booking id is invalid.');
        }//end if

    }//end putTravellerConfirmationOnArrival()


    /**
     * Get Facebook share property.
     *
     * @param \App\Http\GetBookingShareRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse containing trip data
     *
     * @SWG\Get(
     *     path="/v1.6/booking/share",
     *     tags={"Trip"},
     *     description="get facebook share content for property",
     *     operationId="booking.get.share",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing sharable content of property ",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetBookingShareResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getFbShareProperties(GetBookingShareRequest $request)
    {
        $input_params = $request->input();

        // Input required params.
        $request_hash_id    = $input_params['request_hash_id'];
        $booking_request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $user_id = $request->getLoginUserId();
        // Get all headers.
        $headers = $request->getAllHeaders();
        $output  = [];

        $content = BookingRequest::getFbShareDataByTravellerId($booking_request_id, $user_id);

        if (empty($content) === false) {
            $property_images = PropertyImage::getPropertiesImagesByIds([$content['property_id']], $headers, 1);
            if (empty($property_images) === false) {
                $fb_share_img = $property_images[$content['property_id']][0]['image'];
            } else {
                $fb_share_img = WEBSITE_URL.'/images/no_property.png';
            }

            $fb_content = FbShare::fbShareProperty($content['property_type']);
            $fb_content = str_replace('<Name>', ucfirst($content['username']), $fb_content);
            $fb_content = str_replace('<property_name>', ucfirst($content['title']), $fb_content);
            $fb_content = str_replace('<destination/city>', ucfirst($content['city']), $fb_content);
            $fb_content = str_replace('<property type>', ucfirst($content['title']), $fb_content);
            $link       = WEBSITE_URL.'/properties/rooms/'.$content['property_id'];
            $link      .= '?utm_source=facebook&utm_medium=wall_post&utm_campaign=Share_Trip_Wall&_$ja=tsid:86769%7Ccgn:Share_Trip_Wall&ra_em=0';

            return ApiResponse::success(
                [
                    'content'        => $fb_content,
                    'link'           => $link,
                    'share_image'    => $fb_share_img,
                    'property_title' => $content['title'],
                    'property_desc'  => $content['description'],
                ]
            );
        } else {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }//end if

    }//end getFbShareProperties()


    /**
     * Api to call to make payment Merged Function.
     *
     * @param \Illuminate\Http\Request $request         Http request object.
     * @param string                   $request_hash_id Request id in hash.
     *
     * @return view|\Illuminate\Http\JsonResponse
     */
    public function getPaymentData(Request $request, string $request_hash_id)
    {
        $payment_link = '';
        $url          = 'http://www.guesthouser.com';
        $final_source = 'web';

        $source = $request->get('source');
        $origin = (empty($request->get('origin')) === false) ? $request->get('origin') : '';

        $request_id   = Helper::decodeBookingRequestId($request_hash_id);
        $payment_data = PaymentService::getPaymentPageData($request_id, $source, $origin);

        $booking_status   = (isset($payment_data['booking_status']) === true) ? $payment_data['booking_status'] : '';
        $booking_amount   = (isset($payment_data['amount']) === true) ? $payment_data['amount'] : '';
        $booking_currency = (isset($payment_data['currency']) === true) ? $payment_data['currency'] : '';

        if ($payment_data['status'] === true && $payment_data['action'] === 'non_payment') {
            // Will Remove soom.
            $host      = User::getUserDataById($payment_data['booking_request']->host_id);
            $traveller = User::getUserDataById($payment_data['booking_request']->traveller_id);

            // Dispatch Event for Cashless Booking Created.
            $create_booking_event = new CreateBooking($payment_data['booking_request'], $payment_data['booking']->balance_fee, $payment_data['property'], $host, $traveller, false, false);
            Event::dispatch($create_booking_event);
        }

        // Request needs json resposne.
        if (0 === strpos($request->headers->get('Accept'), 'application/json')) {
            if ($payment_data['status'] === true) {
                $response = [
                    'action'  => $payment_data['action'],
                    'details' => $payment_data['data'],
                ];
                return ApiResponse::success($response);
            }

            return ApiResponse::notFoundError($payment_data['reason'], 'No Details available');
        }

        if ($payment_data['status'] === true && $payment_data['action'] === 'payment') {
            return view::make($payment_data['data']['gateway'].'_payment', $payment_data['data']);
        } else {
            if (empty($origin) === false && $source !== 'app') {
                     $url = $origin.'/requests';
                if ($request_hash_id !== '') {
                    if ($booking_status <= REQUEST_APPROVED) {
                        $url = $origin.'/request/'.$request_hash_id;
                    } else {
                        $url = $origin.'/trip/'.$request_hash_id;
                    }
                }

                if ($payment_data['status'] === true) {
                    $url = $origin.'/trips';
                    if ($request_hash_id !== '') {
                        $url = $origin.'/trip/'.$request_hash_id;
                    }

                    if ($payment_data['action'] === 'non_payment') {
                        $url = $url.'?payment=true';
                    }
                }
            } else if ($source === 'app') {
                $final_source = 'app';
            }//end if

            return view::make(
                'post_payment',
                [
                    'request_hash_id' => $request_hash_id,
                    'url'             => $url,
                    'source'          => $final_source,
                    'status'          => $payment_data['status'],
                    'booking_status'  => $booking_status,
                    'amount'          => $booking_amount,
                    'currency'        => $booking_currency,
                ]
            );
        }//end if

    }//end getPaymentData()


      /**
       * Api called by payu on success Merged Function
       *
       * @param \Illuminate\Http\Request $request Http request object.
       *
       * @return view
       */
    public function anyPaymentSuccess(Request $request)
    {
        // $txnid = "d2169bbf79956d7cbae89888eeae0697";
        $input = $request->all();

        $response = Booking::processPayment($input);

        $booking_status   = (isset($response['booking_status']) === true) ? $response['booking_status'] : '';
        $booking_amount   = (isset($response['amount']) === true) ? $response['amount'] : '';
        $booking_currency = (isset($response['currency']) === true) ? $response['currency'] : '';

        $request_hash_id = (empty($response['booking_request_id']) === false) ? Helper::encodeBookingRequestId($response['booking_request_id']) : '';

        if ($response['status'] === true) {
            // Will Remove soom.
            $host      = User::getUserDataById($response['booking_request']->host_id);
            $traveller = User::getUserDataById($response['booking_request']->traveller_id);

            // Dispatch Event for Booking Created.
            $create_booking_event = new CreateBooking($response['booking_request'], $response['booking']->balance_fee, $response['property'], $host, $traveller, false, $response['second_payment']);
            Event::dispatch($create_booking_event);
        }

        // Request needs json resposne.
        if (0 === strpos($request->headers->get('Accept'), 'application/json')) {
            if ($response['status'] === true) {
                return ApiResponse::success(
                    [
                        'message'        => 'Payment Completed',
                        'booking_status' => $booking_status,
                        'amount'         => $booking_amount,
                        'currency'       => $booking_currency,
                    ]
                );
            }

            return ApiResponse::errorMessage('Payment Failed');
        }

        $source = $request->get('source');
        $origin = $request->get('origin');

        $url          = 'http://www.guesthouser.com';
        $final_source = 'web';

        if (empty($origin) === false && $source !== 'app') {
            // Inventory not availbel sending to trip as booking is created.
            if ($response['status'] === false && $response['second_payment'] === false) {
                $url = $origin.'/';
                if ($request_hash_id !== '') {
                    $url = $origin.'/request/'.$request_hash_id;
                }
            } else {
                $url = $origin.'/trips';
                if ($request_hash_id !== '') {
                    $url = $origin.'/trip/'.$request_hash_id.'?payment=true';
                }
            }
        } else if ($source === 'app') {
            $final_source = 'app';
        }

        return view::make(
            'post_payment',
            [
                'request_hash_id' => $request_hash_id,
                'url'             => $url,
                'source'          => $final_source,
                'status'          => $response['status'],
                'booking_status'  => $booking_status,
                'amount'          => $booking_amount,
                'currency'        => $booking_currency,
            ]
        );

    }//end anyPaymentSuccess()


    /**
     * Api called by payu on failure Merged Function
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return view
     */
    public function postPaymentFail(Request $request)
    {
        $request_hash_id        = '';
        $url                    = 'http://www.guesthouser.com';
        $final_source           = 'web';
        $booking_request_status = '';

        $source = $request->get('source');
        $origin = $request->get('origin');
        $input  = $request->all();

        if ($request->has('txnid') === true || $request->has('razorpay_order_id') === true) {
            $txn_id = (isset($input['txnid']) === true) ? $input['txnid'] : ((isset($input['razorpay_order_id']) === true) ? $input['razorpay_order_id'] : '');

             $payment = PaymentTracking::where('txnid', '=', $txn_id)->where('status', '=', PAYMENT_INITIATED)->first();

            if (empty($payment) === false) {
                $request_hash_id = Helper::encodeBookingRequestId($payment->booking_request_id);

                PaymentTracking::where('txnid', '=', $request->get('txnid'))->update(['status' => PAYMENT_FAILED, 'payment_failure_logs' => json_encode($request->all())]);

                $booking_request = BookingRequest::select('booking_status')->find($payment->booking_request_id);
                if (empty($booking_request) === false) {
                    $booking_request_status = $booking_request->booking_status;
                }
            }
        }

        // Request needs json resposne.
        if (0 === strpos($request->headers->get('Accept'), 'application/json')) {
            return ApiResponse::success(
                [
                    'message'        => 'Payment Failed',
                    'booking_status' => $booking_request_status,
                ]
            );
        }

        if (empty($origin) === false && $source !== 'app') {
            $url = $origin.'/';
            if ($request_hash_id !== '') {
                if ($booking_request_status < BOOKED) {
                    $url = $origin.'/request/'.$request_hash_id;
                } else {
                    $url = $origin.'/trip/'.$request_hash_id;
                }
            }
        } else if ($source === 'app') {
            $final_source = 'app';
        }

        return view::make(
            'post_payment',
            [
                'request_hash_id' => $request_hash_id,
                'url'             => $url,
                'source'          => $final_source,
                'status'          => false,
                'booking_status'  => $booking_request_status,
                'amount'          => 0,
                'currency'        => DEFAULT_CURRENCY,
            ]
        );

    }//end postPaymentFail()


}//end class
