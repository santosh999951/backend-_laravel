<?php
/**
 * Host controller containing methods
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\{Request, Response};

use \Auth;
use \Event;
use \Carbon\Carbon;

use App\Libraries\{ApiResponse, Helper, CommonQueue};

use App\Libraries\v1_6\{PropertyTileService, PropertyPricingService, BookingRequestService, InvoiceService, UserService , PropertyService};

use App\Models\{BookingRequest, PropertyReview, TravellerRating, CountryCodeMapping, PropertyImage, Property,
                User, Booking, PaymentTracking, PropertyType, UserBillingInfo, PayoutTransactions, CancellationReasonDetails,
                BookingAvailability, RequestRejections, CancellationPolicy, HostConversionLead, RoomType, PropertyTagMapping, Amenity, PropertyVideo, Admin, ChannelManagerProperties};

use App\Http\Response\v1_6\Models\{GetHostHomeResponse, GetHostBookingResponse, GetHostBookingTripDetailResponse,
                                    GetHostBookingRequestDetailResponse, GetHostPropertiesResponse,
                                    GetHostPropertiesDetailResponse, GetHostPropertiesCalendarResponse,
                                    GetHostPropertiesReviewResponse, GetHostPayoutsResponse, GetHostPaymentPreferencesResponse,
                                    PutHostBookingStatusResponse, PutHostPropertiesCalendarResponse,
                                    PutHostPropertiesStatusResponse, PutHostPropertiesReviewResponse,PutHostPaymentPreferencesResponse,
                                    PostHostBookingConfirmationResponse,GetHostSearchBookings, GetRmAsHostLoginResponse, GetRmHostListingResponse,
                                    PostHostPaymentPreferencesResponse , PostHostLeadResponse , PostSmartDiscountsResponse, DeleteHostPropertyResponse, PostPropertyCloneResponse, PutPropertyResponse, GetHostPropertyListingDetailResponse};

use App\Http\Requests\{GetRmHostRequest, GetRmAsHostLoginRequest, PostPaymentPreferencesRequest, GetHostPropertiesRequest, GetHostPropertyRequest,
                        GetHostPropertyPriceCalenderRequest, PutHostPropertyPriceCalenderRequest, GetPaymentPreferencesRequest, PutPaymentPreferencesRequest,
                        GetHostPayoutHistoryRequest, GetHostHomeRequest, GetHostBookingListRequest, GetHostRequestDetailRequest,
                        PutHostRequestStatusRequest, GetHostPropertyReviewsRequest, PutHostPropertyStatusRequest, PutHostReviewReplyRequest,
                        PostHostBookingConfirmationRequest , PostLeadRequest, PostHostSmartDiscountRequest, DeletePropertyRequest, PostPropertyCloneRequest, PutPropertyRequest, GetHostListingPropertyRequest,PutHostConfirmTravellerArrivalRequest};

use App\Events\{StatusChangedBookingRequest, PropertyListing};

use App\Jobs\SyncAirbnbProperties;

/**
 * Class HostController
 */
class HostController extends Controller
{
    use \App\Traits\PassportToken;

    /**
     * User Service object.
     *
     * @var UserService
     */
    protected $user_service;


    /**
     * Constructor for dependency injection.
     *
     * @param UserService     $user_service     User Service Object.
     * @param PropertyService $property_service Property Service Object.
     *
     * @return void
     */
    public function __construct(UserService $user_service, PropertyService $property_service)
    {
        $this->user_service     = $user_service;
        $this->property_service = $property_service;

    }//end __construct()


    /**
     * Get host dashboard data
     *
     * @param App\Http\Requests\GetHostHomeRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/host/home",
     *     tags={"Host"},
     *     description="Get host dashboard data",
     *     operationId="host.get.home",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *      response=200,
     *      description="Host Dashboard data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetHostHomeResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * )
     */
    public function getIndex(GetHostHomeRequest $request)
    {
        // Fetch user data.
        $user_id = $request->getLoginUserId();

        // Get New Request Count.
        $new_request_count = BookingRequest::getNewRequestsCountForHost($user_id);

        // Get Traveller count whose checkin date is today.
        $traveller_checkin_today_count = BookingRequest::getTravellerCheckinTodayCount($user_id);

        // Get Traveller Review count on Awating reply by host.
        $traveller_review_awating_response_count = PropertyReview::getAwatingReplyCountByHost($user_id);

        // Get Upcoming checkin's count.
        $upcoming_checking_traveller = BookingRequest::getUpcomingCheckinTraveller($user_id);

        $upcoming_checkin_count = BookingRequest::getUpcomingCheckinCountTraveller($user_id);

        // New Booking Awaiting Confirmation Count.
        $new_booking_awaiting_confirmation = BookingRequest::getNewBookedAwaitingConfirmationOfHost($user_id);

        $start_of_week      = Carbon::now('GMT')->startOfWeek();
        $end_of_week        = Carbon::now('GMT')->endOfWeek();
        $start_of_last_week = $start_of_week->copy()->addDay(-7);
        $end_of_last_week   = $end_of_week->copy()->addDay(-7);

        // Last week Booking Stats.
        $last_week_booking_count = BookingRequest::getBookingCountSpecificDate($user_id, $start_of_last_week, $end_of_last_week);

        // Last week Booking Stats.
        $this_week_booking_count = BookingRequest::getBookingCountSpecificDate($user_id, $start_of_week, $end_of_week);

        // Weekly Booking Stats.
        $booking_stats = ($last_week_booking_count > 0) ? (( ( $this_week_booking_count - $last_week_booking_count ) / $last_week_booking_count ) * 100) : ($this_week_booking_count * 100);

        // Upcoming Checkins.
        $upcoming_checkin = [];

        foreach ($upcoming_checking_traveller as $one_traveller) {
            $checkin_date      = $one_traveller['from_date'];
            $checkin_date_obj  = Carbon::parse($checkin_date);
            $checkin_formatted = $checkin_date_obj->format('d M');

            $checkout_date      = $one_traveller['to_date'];
            $checkout_date_obj  = Carbon::parse($checkout_date);
            $checkout_formatted = $checkout_date_obj->format('d M');

            // Get booking status to display (along with class).
            $booking_status = Helper::getHostBookingStatusTextAndClass($one_traveller['booking_status'], $checkout_date);

            $text = ucwords($one_traveller['traveller_name']).' is going to check In on '.$checkin_formatted;

            // Create Upcoming Checkin data.
            $upcoming_checkin[] = [
                'request_hash_id'    => Helper::encodeBookingRequestId($one_traveller['request_id']),
                'property_hash_id'   => Helper::encodePropertyId($one_traveller['id']),
                'guest'              => $one_traveller['guests'],
                'units_consumed'     => $one_traveller['units_consumed'],
                'traveller_name'     => $one_traveller['traveller_name'],
                'checkin'            => $checkin_date_obj->format('Y-m-d'),
                'checkout'           => $checkout_date_obj->format('Y-m-d'),
                'checkin_formatted'  => $checkin_formatted,
                'checkout_formatted' => $checkout_formatted,
                'booking_status'     => $booking_status,
                'text'               => $text,
            ];
        }//end foreach

        $response = [
            'notification'     => [
                [
                    'type'  => HOST_FILTER_NEW_REQUEST,
                    'count' => $new_request_count,
                    'text'  => 'Requests awaiting your approval',
                ],
                [
                    'type'  => HOST_FILTER_AWAITING_CONFIRMATION_BOOKING,
                    'count' => $new_booking_awaiting_confirmation,
                    'text'  => 'New Bookings awaiting confirmation',
                ],
                [
                    'type'  => HOST_FILTER_CHECK_IN_TODAY,
                    'count' => $traveller_checkin_today_count,
                    'text'  => 'Traveller check In’s today',
                ],
                [
                    'type'  => HOST_FILTER_NEW_GUEST_REVIEW,
                    'count' => $traveller_review_awating_response_count,
                    'text'  => 'New guest reviews awaiting your response',
                ],
            ],
            'booking_stats'    => [
                'status' => ($booking_stats < 0) ? 'down' : 'up',
                'value'  => round(abs($booking_stats), 1).'%',
            ],
            'upcoming_checkin' => [
                'type'  => HOST_FILTER_UPCOMING_CHECKIN,
                'list'  => $upcoming_checkin,
                'count' => $upcoming_checkin_count,
            ],

        ];

        $response = new GetHostHomeResponse($response);
        $response = $response->toArray();
         return ApiResponse::success($response);

    }//end getIndex()


    /**
     * Get host booking/request list data
     *
     * @param App\Http\Requests\GetHostBookingListRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/host/booking",
     *     tags={"Host"},
     *     description="Get host booking/request list",
     *     operationId="host.get.booking",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/order_by_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_start_date_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_end_date_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_property_hash_string_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_host_status_string_in_query"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_type_for_bookings"),
     * @SWG\Response(
     *      response=200,
     *      description="Booking list data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                         ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                           ref="#definitions/GetHostBookingResponse"),
     * @SWG\Property(property="error",                                          ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *      response=404,
     *      description="No details available.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * )
     * )
     */
    public function getBookingList(GetHostBookingListRequest $request)
    {
        // Fetch Input Params.
        $input_params = $request->input();

        $start_date          = $input_params['start_date'];
        $end_date            = $input_params['end_date'];
        $order_by            = $input_params['order_by'];
        $filter_type         = $input_params['filter_type'];
        $limit               = $input_params['total'];
        $offset              = $input_params['offset'];
        $host_booking_status = $input_params['status'];

        // Get Logged In User Id.
        $host_id = $request->getLoginUserId();

        // Decode all property ids if exist.
        $property_ids = $request->decodeAllPropertyIdOrFail($input_params['property_hash_ids']);

        // Get all headers.
        $headers = $request->getAllHeaders();

        $awaiting_confirmation = false;

        // Today's date.
        $today = Carbon::now('Asia/Kolkata');

        // Sort Type. ASC, DESC.
        $sort_type = 'DESC';

        if ($filter_type > 0) {
            switch ($filter_type) {
                case HOST_FILTER_NEW_REQUEST:
                        $host_booking_status = HOST_NEW_REQUEST;
                break;

                case HOST_FILTER_CHECK_IN_TODAY:
                        $host_booking_status = HOST_BOOKING_CONFIRMED;
                        $start_date          = $today->toDateString();
                        $end_date            = $today->toDateString();
                        $order_by            = 2;
                break;

                case HOST_FILTER_UPCOMING_CHECKIN:
                        $host_booking_status = HOST_BOOKING_CONFIRMED;
                        $start_date          = $today->copy()->addDay(1)->toDateString();
                        $end_date            = $today->copy()->addYear()->toDateString();
                        $order_by            = 2;
                        $sort_type           = 'ASC';
                break;

                case HOST_FILTER_AWAITING_CONFIRMATION_BOOKING:
                        $host_booking_status   = HOST_BOOKING_CONFIRMED;
                        $start_date            = $today->toDateString();
                        $end_date              = $today->copy()->addYear()->toDateString();
                        $order_by              = 2;
                        $awaiting_confirmation = true;
                default:
                        $host_booking_status = '';
                break;
            }//end switch
        }//end if

        $host_status_array = [];
        $db_status_array   = [];
        $booking_requests  = [];

        if (empty($host_booking_status) === false) {
            $host_status_array = explode(',', $host_booking_status);

            foreach ($host_status_array as $status) {
                $status = Helper::getDBStatusForHostStatus((int) $status);

                if (count($status) > 0) {
                    $db_status_array = array_merge($db_status_array, $status);
                }
            }
        }

        // Get Booking List Data.
        $list = BookingRequest::getHostBookings($host_id, $start_date, $end_date, $property_ids, $db_status_array, $offset, $limit, $order_by, $host_status_array, '', $awaiting_confirmation, $sort_type);

        // Get property ids (unique) visited by user.
        $available_property_ids = array_unique(array_column($list, 'pid'));

        // Get first property image to display.
        $properties_images = PropertyImage::getPropertiesImagesByIds($available_property_ids, $headers, 1);

        foreach ($list as $booking) {
            // Get timeline diff from today to from_date and to_date.
            $start_date_obj = Carbon::parse($booking['from_date']);
            $end_date_obj   = Carbon::parse($booking['to_date']);
            $no_of_nights   = $start_date_obj->diffInDays($end_date_obj, false);

            $no_of_days_from_checkout = $today->diffInDays($end_date_obj, false);

            $timeline_status = BookingRequestService::getTimelineStatusForBookingList($start_date_obj, $end_date_obj);

            if ($booking['booking_status'] === NEW_REQUEST) {
                $booking_expiry_time = (strtotime($booking['approve_till']) - strtotime(Carbon::now('GMT')->format('Y-m-d H:i:s')));
            } else {
                $booking_expiry_time = 0;
            }

            // Checkin - checkout format.
            $checkin_checkout = $start_date_obj->format('d M').' - '.$end_date_obj->format('d M Y');

            $price_details          = json_decode($booking['price_details']);
            $property_currency_code = (empty($price_details->property_currency_code) === true) ? DEFAULT_CURRENCY : $price_details->property_currency_code;
            $payable_amount         = (isset($price_details->payable_amount) === true) ? $price_details->payable_amount : 0.00;

            // Calculate Amount.
            $host_amount = round(Helper::convertPriceToCurrentCurrency($price_details->currency_code, $price_details->host_fee, $property_currency_code), 2);

            // Calculate GH Commission from Host.
            $gh_commission_from_host = 0;

            if (empty($booking['commission_from_host']) === false && $booking['commission_from_host'] > 0) {
                $gh_commission_from_host = round(Helper::convertPriceToCurrentCurrency($price_details->currency_code, (($price_details->host_fee * $booking['commission_from_host']) / 100), $property_currency_code), 2);
            }

            // Get booking status to display (along with class).
            $booking_status = Helper::getHostBookingStatusTextAndClass($booking['booking_status'], $booking['to_date']);

            $tile_data = [
                'pid'               => $booking['pid'],
                'area'              => $booking['area'],
                'city'              => $booking['city'],
                'state'             => $booking['state'],
                'properties_images' => $properties_images,
                'title'             => $booking['title'],
                'request_id'        => $booking['id'],
                'no_of_nights'      => $no_of_nights,
                'guests'            => $booking['guests'],
                'units'             => $booking['units'],
                'checkin_checkout'  => $checkin_checkout,
                'timeline_status'   => $timeline_status,
                'checkin'           => $start_date_obj->format('d M Y'),
                'checkout'          => $end_date_obj->format('d M Y'),
                'booking_status'    => $booking_status,
                'currency'          => $property_currency_code,
                'payable_amount'    => $payable_amount,
                'host_amount'       => ($host_amount - $gh_commission_from_host),
                'expires_in'        => $booking_expiry_time,
            ];

            // Get tile structure for trip page.
            $property_tile = PropertyTileService::hostBookingListStructure($tile_data);

            array_push($booking_requests, $property_tile);
        }//end foreach

        $all_properties = Property::select('id', 'title')->where('user_id', $host_id)->orderBy('title', 'ASC')->get()->toArray();

        foreach ($all_properties as $key => $value) {
            $all_properties[$key]['selected'] = (in_array($all_properties[$key]['id'], $property_ids) === true) ? 1 : 0;
            $all_properties[$key]['id']       = Helper::encodePropertyId($all_properties[$key]['id']);
            $all_properties[$key]['title']    = ucfirst($all_properties[$key]['title']);
        }

        $response = [
            'booking_requests' => $booking_requests,
            'filter'           => [
                'properties' => $all_properties,
                'status'     => Helper::getBookingStatusOfHost($host_status_array),
                'order_by'   => $order_by,
                'start_date' => $start_date,
                'end_date'   => $end_date,
            ],
        ];

        $response = new GetHostBookingResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getBookingList()


    /**
     * Get host trip details
     *
     * @param \Illuminate\Http\Request $request         Http request object.
     * @param string                   $request_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\JsonResponse containing host trip details
     *
     * @SWG\Get(
     *     path="/v1.6/host/booking/trip/{request_hash_id}",
     *     tags={"Host"},
     *     description="get details of trip",
     *     operationId="host.get.booking.trip",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns json containing trip details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetHostBookingTripDetailResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=404,
     *      description="No details available.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * )
     * )
     */
    public function getTripDetails(Request $request, string $request_hash_id)
    {
        $request_id = Helper::decodeBookingRequestId($request_hash_id);

        if (empty($request_id) === true) {
             return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $headers = $request->headers->all();

        $trip = BookingRequest::getHostTripByRequestId($request_id);

        if (empty($trip) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $device_source = $this->getDeviceType($request);

        // Get property images.
        $trip['properties_images'] = PropertyImage::getPropertiesImagesByIds([$trip['id']], $headers, 1);
        $trip['original_title']    = true;

        // Get property tile data.
        $property_tile = PropertyTileService::minPropertyTileStructure($trip);

        // Get Booking Invoice.
        $trip_invoice = InvoiceService::requestDetailsInvoiceForHost($trip);

        $booking_status         = (int) $trip['booking_status'];
        $checkin_date           = $trip['from_date'];
        $checkin_date_obj       = Carbon::parse($checkin_date);
        $checkin_date_formatted = $checkin_date_obj->format('d M Y');

        $checkout_date           = $trip['to_date'];
        $checkout_date_obj       = Carbon::parse($checkout_date);
        $checkout_date_formatted = $checkout_date_obj->format('d M Y');

        $balance_fee       = (int) $trip['balance_fee'];
        $price_detail_data = json_decode($trip['price_details'], true);
        $booking_amount    = $price_detail_data['payable_amount'];
        $currency          = (empty($trip['currency']) === true ) ? DEFAULT_CURRENCY : $trip['currency'];

        if (isset($price_detail_data['currency_code']) === false) {
            $price_detail_data['currency_code'] = DEFAULT_CURRENCY;
        }

        $pending_payment  = 0;
        $show_traveller   = 1;
        $traveller_detail = [
            'show_traveller' => 0,
            'hash_id'        => '',
            'name'           => '',
            'contact'        => '',
            'age'            => 0,
            'language'       => '',
            'gender'         => '',
            'verified'       => 0,
        ];
        $ask_review       = 0;

        // Booking status by date.
        $booking_status_by_date = Helper::getTripStatusTextAndClassForMsite($booking_status, $checkin_date, $checkout_date);

        if ($balance_fee > 0) {
            $payment_status  = PaymentTracking::getIsRequestPaymentInitated($request_id, false);
            $pending_payment = 1;
        }

        if (in_array($booking_status_by_date['class'], [COMPLETED_CLASS, CANCELLATION_CLASS]) === true) {
            $pending_payment = 0;
            $show_traveller  = 0;
        }

        if (in_array($booking_status_by_date['class'], [COMPLETED_CLASS]) === true) {
            $ask_review = (empty($trip['review_id']) === true || empty($trip['rating_id']) === true) ? 1 : 0;
        }

        if ($show_traveller === 1) {
            $traveller_detail = [
                'show_traveller' => $show_traveller,
                'hash_id'        => Helper::encodeUserId($trip['traveller_id']),
                'name'           => ucfirst($trip['traveller_name']),
                'contact'        => $trip['contact'],
                'age'            => (empty($trip['dob']) === false && $trip['dob'] !== '0000-00-00' && $trip['display_age'] === 'Yes') ? Carbon::now('GMT')->diffInYears(Carbon::parse($trip['dob'])) : 0,
                'language'       => (empty($trip['language']) === false) ? ucfirst($trip['language']) : 'English, Hindi',
                'gender'         => (empty($trip['gender']) === false) ? ucfirst($trip['gender']) : 'Male',
                'verified'       => (empty($trip['mobile_verify']) === false) ? (int) $trip['mobile_verify'] : 0,
            ];
        }

        // Get booking status to display (along with class).
        $booking_status = Helper::getHostBookingStatusTextAndClass($booking_status, $trip['to_date']);

        $booking_info = [
            'request_hash_id'     => $request_hash_id,
            'instant'             => $trip['instant_book'],
            'coupon_code_used'    => (empty($price_detail_data['coupon_applied']) === false) ? $price_detail_data['coupon_applied'] : '',
            'wallet_money_used'   => (empty($price_detail_data['wallet_money_applied']) === false) ? $price_detail_data['wallet_money_applied'] : 0,
            'guests'              => $trip['guests'],
            'checkin_formatted'   => $checkin_date_formatted,
            'checkout_formatted'  => $checkout_date_formatted,
            'booking_status'      => $booking_status,
            'checkin'             => $checkin_date_obj->format('Y-m-d'),
            'checkout'            => $checkout_date_obj->format('Y-m-d'),
            'ask_review'          => $ask_review,
            'can_confirm_booking' => $trip['can_confirm_booking'],
        ];

        $trip_data = [
            'property_section'       => ['tile' => $property_tile],
            'booking_info_section'   => [
                'info'                => $booking_info,
                'booking_amount_info' => [
                    'currency'                 => CURRENCY_SYMBOLS[$currency],
                    'total_amount'             => Helper::getFormattedMoney($booking_amount, $currency, true),
                    'total_amount_unformatted' => $booking_amount,
                    'pending_payment'          => $pending_payment,
                    'pending_payment_amount'   => Helper::getFormattedMoney($balance_fee, $currency, true),
                ],
            ],
            'invoice_section'        => $trip_invoice,
            'traveller_info_section' => $traveller_detail,
        ];

        $response = new GetHostBookingTripDetailResponse($trip_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getTripDetails()


    /**
     * Get host request details
     *
     * @param App\Http\Requests\GetHostRequestDetailRequest $request         Http request object.
     * @param string                                        $request_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\JsonResponse containing host trip details
     *
     * @SWG\Get(
     *     path="/v1.6/host/booking/request/{request_hash_id}",
     *     tags={"Host"},
     *     description="get details of request",
     *     operationId="host.get.booking.request",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns json containing request details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetHostBookingRequestDetailResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Invalid Request Hash Id",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *      response=404,
     *      description="No details available.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * )
     * )
     */
    public function getRequestDetails(GetHostRequestDetailRequest $request, string $request_hash_id)
    {
        // Decode Booking Request Id Or Fail.
        $request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        // Fetch Logged In User Id.
        $host_id = $request->getLoginUserId();

        // Get All Headers Data.
        $headers = $request->getAllHeaders();

        // Fetch Request Detail Data.
        $request_data = BookingRequest::getBookingRequestByRequestId($request_id, $host_id);

        if (empty($request_data) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $request_data = $request_data[0];

        $price_details = json_decode($request_data['price_details']);
        $host_amount   = BookingRequestService::calculateHostAmount($request_data);

        // Get property images.
        $request_data['properties_images'] = PropertyImage::getPropertiesImagesByIds([$request_data['property_id']], $headers, 1);
        $request_data['original_title']    = true;

        $property_type = PropertyType::getPropertyTypeByPid($request_data['property_id']);

        // Get property tile data.
        $request_data['id'] = $request_data['property_id'];
        $property_tile      = PropertyTileService::minPropertyTileStructureWithExtraInfo($request_data);

        // Get Booking Invoice.
        $booking_invoice = InvoiceService::requestDetailsInvoiceForHost($request_data);

        $booking_status         = (int) $request_data['booking_status'];
        $resend_request_status  = $request_data['resend_reqest_status'];
        $checkin_date           = $request_data['from_date'];
        $checkin_date_obj       = Carbon::parse($checkin_date);
        $checkin_date_formatted = $checkin_date_obj->format('d M Y');

        $checkout_date           = $request_data['to_date'];
        $checkout_date_obj       = Carbon::parse($checkout_date);
        $checkout_date_formatted = $checkout_date_obj->format('d M Y');
        $currency                = $request_data['currency'];
        $payable_amount          = $price_details->payable_amount;

        $new_payment_method = (isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment';

        $available_method    = (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : $new_payment_method;
        $payment_option_text = PAYMENT_OPTION_TEXT[PAYMENT_NO[$available_method]]['text'];

        if ($booking_status === NEW_REQUEST) {
            $booking_expiry_time = (strtotime($request_data['approve_till']) - strtotime(Carbon::now('GMT')->format('Y-m-d H:i:s')));
        } else {
            $booking_expiry_time = 0;
        }

        $show_traveller   = 1;
        $traveller_detail = [
            'show_traveller' => 0,
            'hash_id'        => '',
            'name'           => '',
            'contact'        => '',
            'age'            => 0,
            'language'       => '',
            'gender'         => '',
            'verified'       => 0,
        ];

        // Booking status by date.
        $booking_status_by_date = Helper::getTripStatusTextAndClassForMsite($booking_status, $checkin_date, $checkout_date);

        if (in_array($booking_status_by_date['class'], [COMPLETED_CLASS, CANCELLATION_CLASS]) === true) {
            $show_traveller = 0;
        }

        if ($show_traveller === 1) {
            $traveller_detail = [
                'show_traveller' => $show_traveller,
                'hash_id'        => Helper::encodeUserId($request_data['traveller_id']),
                'name'           => ucfirst($request_data['traveller_name']),
                'contact'        => $request_data['contact'],
                'age'            => (empty($request_data['dob']) === false && $request_data['dob'] !== '0000-00-00' && $request_data['display_age'] === 'Yes') ? Carbon::now('GMT')->diffInYears(Carbon::parse($request_data['dob'])) : 0,
                'language'       => (empty($request_data['language']) === false) ? ucfirst($request_data['language']) : 'English, Hindi',
                'gender'         => (empty($request_data['gender']) === false) ? ucfirst($request_data['gender']) : '',
                'verified'       => (empty($request_data['mobile_verify']) === false) ? (int) $request_data['mobile_verify'] : 0,
                'image'          => (empty($request_data['traveller_image']) === false) ? Helper::generateProfileImageUrl($request_data['gender'], $request_data['traveller_image'], $request_data['traveller_id']) : '',
            ];
        }

        // Get booking status to display (along with class).
        $booking_status = Helper::getHostBookingStatusTextAndClass($booking_status, $checkout_date);

        // Get Rejection Reasons.
        $request_rejection = new RequestRejections;
        $rejection_reasons = $request_rejection->getRejectionReasons();

        $booking_info = [
            'request_hash_id'     => $request_hash_id,
            'guests'              => $request_data['guests'],
            'units'               => $request_data['units_consumed'],
            'checkin_formatted'   => $checkin_date_formatted,
            'checkout_formatted'  => $checkout_date_formatted,
            'booking_status'      => $booking_status,
            'checkin'             => $checkin_date_obj->format('Y-m-d'),
            'checkout'            => $checkout_date_obj->format('Y-m-d'),
            'property_type'       => $property_type,
            'property_hash_id'    => Helper::encodePropertyId($request_data['property_id']),
            'expires_in'          => ($booking_expiry_time > 0) ? $booking_expiry_time : 0,
            'confirm_text'        => ($request_data['can_confirm_booking'] === 0 && empty($request_data['confirm_date']) === false) ? 'You have confirmed this booking on '.Carbon::parse($request_data['confirm_date'])->format('d M Y') : '',
            'can_confirm_booking' => $request_data['can_confirm_booking'],
        ];

        $cancellation_policy_info = CancellationPolicy::getCancellationPoliciesByIds([$request_data['cancellation_policy']])[$request_data['cancellation_policy']];

        $request_response_data = [
            'property_section'       => ['tile' => $property_tile],
            'booking_info_section'   => [
                'info'                => $booking_info,
                'booking_amount_info' => [
                    'total_amount_unformatted' => $booking_invoice['invoice_footer'][0]['raw_value'],
                    'payment_option'           => $payment_option_text,
                    'currency'                 => CURRENCY_SYMBOLS[$currency],
                    'total_amount'             => Helper::getFormattedMoney($booking_invoice['invoice_footer'][0]['raw_value'], $currency, true),
                ],
            ],
            'invoice_section'        => $booking_invoice,
            'traveller_info_section' => $traveller_detail,
            'cancellation_section'   => ['cancellation_policy_info' => $cancellation_policy_info],
            'rejection_section'      => [
                'can_reject' => (($booking_status['status'] === NEW_REQUEST) ? 1 : 0),
                'reasons'    => $rejection_reasons,
            ],
        ];

        $response = new GetHostBookingRequestDetailResponse($request_response_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getRequestDetails()


        /**
         * Accept/Reject booking request
         *
         * @param App\Http\Requests\PutHostRequestStatusRequest $request Http request object.
         *
         * @return \Illuminate\Http\JsonResponse containing booking request accept/reject data.
         *
         * @SWG\Put(
         *     path="/v1.6/host/booking/status",
         *     tags={"Host"},
         *     description="accept/reject booking request",
         *     operationId="host.put.booking.status",
         *     produces={"application/json"},
         * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
         * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
         * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
         * @SWG\Parameter(ref="#/parameters/accept_status_in_form"),
         * @SWG\Parameter(ref="#/parameters/host_request_rejection_reason_id_in_form"),
         * @SWG\Parameter(ref="#/parameters/host_request_rejection_reason_detail_in_form"),
         * @SWG\Response(
         *      response=200,
         *      description="Returns json containing status of accpet booking request",
         * @SWG\Schema(
         * @SWG\Property(property="status",                                                 ref="#/definitions/SuccessHttpResponse/properties/status"),
         * @SWG\Property(property="data",                                                   ref="#definitions/PutHostBookingStatusResponse"),
         * @SWG\Property(property="error",                                                  ref="#/definitions/SuccessHttpResponse/properties/error"),
         *      )
         * ),
         * @SWG\Response(
         *      response=400,
         *      description="Invalid Operation On request",
         * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
         * ),
         * @SWG\Response(
         *      response=401,
         *      description="Unauthorized action.",
         * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
         * ),
         * @SWG\Response(
         *      response=404,
         *      description="No details available.",
         * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
         * )
         * )
         */
    public function putRequestStatus(PutHostRequestStatusRequest $request)
    {
        // Fetch All Input Params.
        $input_params = $request->input();

        // Decode Request Hash Id Or fail.
        $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);

        // Get Logged In user id.
        $user_id = $request->getLoginUserId();

        $booking_requests = BookingRequest::find($request_id);

        if (empty($booking_requests) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        if ($booking_requests->host_id !== $user_id) {
             return ApiResponse::forbiddenError(EC_NOT_FOUND, 'You are not Authorised to perform this action.');
        }

        $traveller = User::find($booking_requests->traveller_id);
        $property  = Property::find($booking_requests->pid);

        // Status 1 for Request Accept else Request Reject.
        if ($input_params['status'] === 1) {
            if ($booking_requests->booking_status === NEW_REQUEST) {
                $booking_requests = BookingRequestService::acceptBookingRequest($request_id);

                // Send new request email to host.
                $approved_request_event = new StatusChangedBookingRequest($booking_requests, $property->title, $traveller->email, $traveller->getUserFullName(), $traveller->dial_code, $traveller->contact);
                Event::dispatch($approved_request_event);

                $response = [
                    'booking_status' => Helper::getHostBookingStatusTextAndClass($booking_requests->booking_status, $booking_requests->to_date),
                    'message'        => 'Thanks for your quick response, we will confirm you for the booking once we receive a response from guest.',
                ];

                $response = new PutHostBookingStatusResponse($response);
                $response = $response->toArray();
                return ApiResponse::success($response);
            } else {
                return ApiResponse::forbiddenError(EC_NOT_FOUND, 'Booking Request Already Approved.');
            }
        } else {
            $payment_status = PaymentTracking::getIsRequestPaymentInitated($request_id);
            if ($payment_status === true) {
                 return ApiResponse::forbiddenError(EC_NOT_FOUND, 'You are not Authorised to perform this action due to payment initiated.');
            }

            if (in_array($booking_requests->booking_status, [NEW_REQUEST, REQUEST_APPROVED]) === true) {
                $booking_requests = BookingRequestService::rejectBookingRequest($request_id, $input_params['reason_id'], $input_params['reason_detail']);

                // Send new request email to host.
                $rejected_request_event = new StatusChangedBookingRequest($booking_requests, $property->title, $traveller->email, $traveller->getUserFullName(), $traveller->dial_code, $traveller->contact);
                Event::dispatch($rejected_request_event);

                $response = [
                    'booking_status' => Helper::getHostBookingStatusTextAndClass($booking_requests->booking_status, $booking_requests->to_date),
                    'message'        => 'Booking Request Rejected',
                ];

                $response = new PutHostBookingStatusResponse($response);
                $response = $response->toArray();
                return ApiResponse::success($response);
            }
        }//end if

        return ApiResponse::errorMessage('Can not perform this operation.');

    }//end putRequestStatus()


    /**
     * Get Host property listings
     *
     * @param App\Http\Requests\GetHostPropertiesRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/host/property",
     *     tags={"Host"},
     *     description="get host's properties.",
     *     operationId="host.get.properties",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/property_id_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/city_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/property_type_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/property_status_optional_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns array containing host property listings.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                      ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                        ref="#definitions/GetHostPropertiesResponse"),
     * @SWG\Property(property="error",                                       ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters. || Auth token/User id missing.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * )
     */
    public function getProperties(GetHostPropertiesRequest $request)
    {
        // Fetch All Input Params.
        $input_params    = $request->input();
        $property_id     = 0;
        $property_type   = [];
        $property_city   = [];
        $property_status = [];

        // Property_id can be string or numeric both.we can pass property_id or property_hash_id in property_id.
        if (isset($input_params['property_id']) === true && empty($input_params['property_id']) === false) {
            $property_id = strtoupper($input_params['property_id']);
        }

        if (isset($input_params['property_type']) === true && empty($input_params['property_type']) === false) {
             $property_type = explode(',', $input_params['property_type']);
        }

        if (isset($input_params['city']) === true && empty($input_params['city']) === false) {
             $property_city = explode(',', $input_params['city']);
        }

        if (isset($input_params['property_status']) === true) {
             $property_status = explode(',', $input_params['property_status']);
        }

         // Decode property_id from the hash id visible in url.
        if (empty($property_id) === false) {
            $property_id = Helper::decodePropertyHashId($property_id);
        }

        // Get Logged in user Id.
        $user_id = $request->getLoginUserId();

        // Get all headers.
        $headers = $request->getAllHeaders();

        $properties_data = [];
        $city            = [];
        $status          = [];
        $type            = [];

        $city   = Property::getAllCity($user_id);
        $status = Helper::getPropertyStatusOfHost();
        $type   = PropertyType::getAllPropertyTypes();
        if (isset($input_params['property_id']) === false || empty($property_id) === false) {
            // Get host property listings.
            $property_listings = Property::getHostProperties($user_id, $input_params['offset'], $input_params['total'], $property_id, $property_type, $property_city, $property_status);

            // Get property ids listed by host.
            $property_ids = array_column($property_listings, 'id');

            // Get first property image to display.
            $properties_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, 1);

            foreach ($property_listings as $key => $one_property) {
                $last_update = new Carbon($one_property['last_updated']);

                $one_property['original_title']    = false;
                $one_property['properties_images'] = $properties_images;

                $property_min_tile_data = PropertyTileService::minPropertyTileStructure($one_property);

                $property_status_text = '';
                $show_manage_calender = 1;

                if (in_array($one_property['status'], [NEW_REVIEW, EDITED_REVIEW, REJECTED_REVIEW]) === true) {
                    $property_status_text = $one_property['status_text'];
                    $show_manage_calender = 0;
                } else if ($one_property['status'] === APPROVED_REVIEW && $one_property['enabled'] === 0) {
                    $property_status_text = 'Offline';
                }

                // Merge Extra Data.
                $property_min_tile_data = array_merge(
                    $property_min_tile_data,
                    [
                        'last_updated'         => $last_update->formatLocalized('%d %B %Y'),
                        'show_manage_calender' => $show_manage_calender,
                        'property_status'      => $property_status_text,
                        'property_status_id'   => $one_property['status'],
                        'property_enable'      => $one_property['enabled'],
                        'prices'               => [
                            'currency'        => CURRENCY_SYMBOLS[$one_property['currency']],
                            'per_night_price' => $one_property['per_night_price'],
                        ],
                    ]
                );

                $properties_data[] = $property_min_tile_data;
            }//end foreach
        }//end if

        $response = [
            'properties'     => $properties_data,
            'cities'         => $city,
            'status_list'    => $status,
            'property_types' => $type,
        ];

        $response = new GetHostPropertiesResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getProperties()


    /**
     * Get Host property Detail
     *
     * @param App\Http\Requests\GetHostPropertyRequest $request          Http request object.
     * @param string                                   $property_hash_id Property id in hash.
     *
     * @return \Illuminate\Http\JsonResponse containg host property details
     *
     * @SWG\Get(
     *     path="/v1.6/host/property/{property_hash_id}",
     *     tags={"Host"},
     *     description="get host's property detail.",
     *     operationId="host.get.property",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns array containing host property detail.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetHostPropertiesDetailResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters. || Auth token/User id missing.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getProperty(GetHostPropertyRequest $request, string $property_hash_id)
    {
        // Validate Property Hash Id and fetch property Id.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        // Get Logged In User Id.
        $user_id = $request->getLoginUserId();

        // Get all headers.
        $headers = $request->getAllHeaders();

        // Get host property listings.
        $property = Property::getHostPropertyById($property_id, $user_id);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Get first property image to display.
        $property['properties_images'] = PropertyImage::getPropertiesImagesByIds([$property_id], $headers, 1);

        // Last Updated Listing date.
        $calendar_last_updated             = Property::getProeprtyCalendarLastUpdated($property_id);
        $last_updated_date                 = new Carbon($property['last_updated']);
        $calendar_last_updated             = (empty($calendar_last_updated) === false) ? new Carbon($calendar_last_updated) : $last_updated_date;
        $property['last_updated']          = $last_updated_date->formatLocalized('%d %B %Y');
        $property['calendar_last_updated'] = $calendar_last_updated->formatLocalized('%d %B %Y');

        $property['property_status_text'] = '';
        $property['show_manage_calender'] = 1;

        if (in_array($property['status'], [NEW_REVIEW, EDITED_REVIEW, REJECTED_REVIEW]) === true) {
            $property['property_status_text'] = $property['status_text'];
            $property['show_manage_calender'] = 0;
        } else if ($property['status'] === APPROVED_REVIEW && $property['enabled'] === 0) {
            $property['property_status_text'] = 'Offline';
        }

        // Get Property Tile Structure.
        $property_section = PropertyTileService::getHostPropertytileStructure($property);

        // Get Property Review Given by traveller.
        $property_review        = PropertyReview::getHostPropertyReviews($user_id, ['property_id' => $property_id], 0, 1);
        $property_reviews       = PropertyController::processPropertyReviewData($property_review['reviews']);
        $property_review_counts = PropertyReview::getPropertyReviewCount($property_id);
        $traveller_score        = TravellerRating::getPropertyReviewByTraveller($property_id);

        // Make Response Data.
        $response = [
            'property_tile' => $property_section,
            'review_data'   => [
                'review'          => $property_reviews,
                'traveller_score' => (empty($traveller_score) === false) ? number_format($traveller_score[0]['property_score'], 1) : 0,
                'new_count'       => $property_review_counts['new_review_count'],
                'total_count'     => $property_review_counts['total_review_count'],
            ],
        ];

        // Pass Response data to Response Model.
        $response = new GetHostPropertiesDetailResponse($response);
        $response = $response->toArray();

        return ApiResponse::success($response);

    }//end getProperty()


    /**
     * Get Host property price calendar
     *
     * @param App\Http\Requests\GetHostPropertyPriceCalenderRequest $request          Http request object.
     * @param string                                                $property_hash_id Property id in hash.
     *
     * @return \Illuminate\Http\Response containing price calendar
     *
     * @SWG\Get(
     *     path="/v1.6/host/property/calendar/{property_hash_id}",
     *     tags={"Host"},
     *     description="get host's property price calendar.",
     *     operationId="host.get.property.calender",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/start_date_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/end_date_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing host property listings.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                 ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                   ref="#definitions/GetHostPropertiesCalendarResponse"),
     * @SWG\Property(property="error",                                  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Auth token/User id missing.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getPropertyPriceCalendar(GetHostPropertyPriceCalenderRequest $request, string $property_hash_id)
    {
        // Get All Input Params.
        $input_params = $request->input();

        // Decode property_id from the hash id visible in url.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        // Fetch Logged In user Id.
        $user_id = $request->getLoginUserId();

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        // Get host property listings.
        $property = Property::getHostPropertyById($property_id, $user_id);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Get property data.
        $property_calender_data = PropertyPricingService::getPropertyPriceCalenderById(
            [
                'property_id'    => $property_id,
                'start_date'     => $input_params['start_date'],
                'end_date'       => $input_params['end_date'],
                'property_units' => $property['units'],
            ]
        );

        $calendar_last_updated = '';

        $calendar_last_updated_string = Property::getProeprtyCalendarLastUpdated($property_id);

        if (empty($calendar_last_updated_string) === false) {
            $calendar_last_updated = new Carbon($calendar_last_updated);
            $calendar_last_updated = $calendar_last_updated->formatLocalized('%d %B %Y');
        }

        $response                  = [];
        $response['property_tile'] = [
            'property_hash_id'      => $property_hash_id,
            'property_type_name'    => $property['property_type_name'],
            'room_type_name'        => $property['room_type_name'],
            'location'              => [
                'area'          => ucfirst($property['area']),
                'city'          => ucfirst($property['city']),
                'state'         => ucfirst($property['state']),
                'country'       => $country_codes[$property['country']],
                    // Country name from code.
                'location_name' => Helper::formatLocation($property['area'], $property['city'], $property['state']),
                'latitude'      => $property['latitude'],
                'longitude'     => $property['longitude'],
            ],
            'accomodation'          => (int) $property['accomodation'],
            'extra_guest_count'     => $property['additional_guest_count'],
            'title'                 => $property['title'],
            'url'                   => Helper::getPropertyUrl($property_hash_id),
            'prices'                => [
                'currency'        => Helper::getCurrencyObject($property['currency']),
                'per_night_price' => $property['per_night_price'],
            ],
            'calendar_last_updated' => $calendar_last_updated,
        ];

        $response['booking_stats'] = [
            'inquiry'  => BookingRequest::getAllBookingRequestCountOfHostProperty($property_id),
            'bookings' => BookingRequest::getAllBookingCountOfHostProperty($property_id),
        ];

        $response['default']         = [
            'price'              => $property['per_night_price'],
            'extra_guest_cost'   => $property['additional_guest_fee'],
            'is_available'       => ($property['units'] > 0) ? 1 : 0,
            'total_units'        => $property['units'],
            'available_units'    => $property['units'],
            'booked_units'       => 0,
            'blocked_units'      => 0,
            'open_units'         => $property['units'],
            'instant_book'       => $property['instant_book'],
            'gh_commission'      => $property['gh_commission'],
            'service_fee'        => $property['service_fee'],
            'markup_service_fee' => $property['markup_service_fee'],
            'x_plus_5'           => 0,
            'discount'           => 0,
            'discount_type'      => 0,
            'discount_days'      => '',
            'smart_discount'     => 0,
        ];
        $response['exception']       = $property_calender_data['inventory_pricing'];
        $response['smart_discounts'] = $property_calender_data['smart_discounts'];

        $response = new GetHostPropertiesCalendarResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getPropertyPriceCalendar()


    /**
     * Update Host property price calendar
     *
     * @param App\Http\Requests\PutHostPropertyPriceCalenderRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse containing price calendar
     *
     * @SWG\Put(
     *     path="/v1.6/host/property/calendar",
     *     tags={"Host"},
     *     description="update host's property price calendar.",
     *     operationId="host.put.property.calendar",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/start_date_in_form"),
     * @SWG\Parameter(ref="#/parameters/end_date_in_form"),
     * @SWG\Parameter(ref="#/parameters/is_available_in_query"),
     * @SWG\Parameter(ref="#/parameters/per_night_price_in_query"),
     * @SWG\Parameter(ref="#/parameters/extra_guest_cost_in_query"),
     * @SWG\Parameter(ref="#/parameters/available_units_in_query"),
     * @SWG\Parameter(ref="#/parameters/instant_book_in_query"),
     * @SWG\Parameter(ref="#/parameters/admin_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/discount_days_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing host property listings.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutHostPropertiesCalendarResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Update Fail.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function putPropertyCalendar(PutHostPropertyPriceCalenderRequest $request)
    {
        // Fetch all input params.
        $input_params = $request->input();

        // Validate and Decode property_hash_id.
        $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);

        // Fetch Logged In user data.
        $user_id = $request->getLoginUserId();

        // Validate Discount Days.
        if (isset($input_params['discount_days']) === true) {
            $all_days = [
                'sun',
                'mon',
                'tue',
                'wed',
                'thu',
                'fri',
                'sat',
            ];

            $discount_days = array_unique(array_map('strtolower', array_map('trim', explode(',', $input_params['discount_days']))));

            $discount_days = array_map('ucfirst', array_intersect($discount_days, $all_days));

            $input_params['discount_days'] = implode(',', $discount_days);
        }

        // Get Property Data By Property Id and host Id.
        $property = Property::getHostPropertyById($property_id, $user_id);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Get property data.
        $property_calender_data = PropertyPricingService::updatePropertyPriceCalender(
            array_merge(
                $input_params,
                [
                    'property_id' => $property_id,
                    'user_id'     => $user_id,
                ]
            )
        );

        if ($property_calender_data === true) {
            $airbnb_channel_manager_info = ChannelManagerProperties::getAirbnbDataByProperty($property_id);

            foreach ($airbnb_channel_manager_info as $values) {
                $job = new SyncAirbnbProperties($values);
                dispatch($job)->onQueue(API_BNB_QUEUE);
            }

            $response = [
                'property_hash_id' => $input_params['property_hash_id'],
                'message'          => 'Changes successfully updated and reflected on your calendar.',
            ];

            $response = new PutHostPropertiesCalendarResponse($response);
            $response = $response->toArray();
            return ApiResponse::success($response);
        }

        return ApiResponse::errorMessage('Unable to change Property Inventory.');

    }//end putPropertyCalendar()


    /**
     * Property status change
     *
     * @param App\Http\Requests\PutHostPropertyStatusRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse containing property status data.
     *
     * @SWG\Put(
     *     path="/v1.6/host/property/status",
     *     tags={"Host"},
     *     description="property status change",
     *     operationId="host.put.property.status",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/property_status_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing status of property status change",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutHostPropertiesStatusResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )

     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Already updated.",
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
     *         response=404,
     *         description="No details available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putPropertyStatus(PutHostPropertyStatusRequest $request)
    {
        // Fetch All Input Params.
        $input_params = $request->input();

        // Decode and Validate Property Hash Id.
        $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);

        // Fetch Logged In User Id.
        $user_id = $request->getLoginUserId();

        // Get host property listings.
        $property = Property::getHostPropertyById($property_id, $user_id);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not exist.');
        }

        $update_property = Property::updateHostPropertyStatus($property_id, $user_id, $input_params['property_status']);

        if (empty($update_property) === true) {
            return ApiResponse::errorMessage('Property status already updated.');
        }

        $response = [
            'property_hash_id' => $input_params['property_hash_id'],
            'property_status'  => $update_property->enabled,
            'message'          => 'Property status changed',
        ];

        $response = new PutHostPropertiesStatusResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putPropertyStatus()


    /**
     * Get host property reviews
     *
     * @param App\Http\Requests\GetHostPropertyReviewsRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/host/property/review",
     *     tags={"Host"},
     *     description="get host property reviews.",
     *     operationId="host.get.property.review",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_type_for_reviews"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing host property reviews.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetHostPropertiesReviewResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getPropertyReviews(GetHostPropertyReviewsRequest $request)
    {
        // Fetch Validated Input Params.
        $input_params = $request->input();

        $property_id = 0;

        // Decode and Validate Propert Hash Id.
        if (empty($input_params['property_hash_id']) === false) {
            $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);
        }

        // Fetch Host Id.
        $user_id = $request->getLoginUserId();

        // Get all headers.
        $headers = $request->getAllHeaders();

        $filter_param = [];

        if (empty($property_id) === false) {
            $filter_param['property_id'] = $property_id;
        }

        if (empty($input_params['filter_type']) === false) {
            switch ($input_params['filter_type']) {
                case HOST_FILTER_NEW_GUEST_REVIEW:
                        $filter_param['reply'] = 0;
                break;

                default:
                        $filter_param['reply'] = 0;
                break;
            }
        }

        // Get property reviews with limit and offset.
        $property_review_data = PropertyReview::getHostPropertyReviews($user_id, $filter_param, $input_params['offset'], $input_params['total']);
        $property_reviews     = self::processHostPropertyReviewData($property_review_data['reviews'], $headers);

        $output = ['reviews' => $property_reviews];

        $response = new GetHostPropertiesReviewResponse($output);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getPropertyReviews()


    /**
     * Save Host Reply
     *
     * @param App\Http\Requests\PutHostReviewReplyRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/host/property/review",
     *     tags={"Host"},
     *     description="Returns success message when reply save successfully.",
     *     operationId="host.put.property.review",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/reply_in_form"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if reply submitted successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutHostPropertiesReviewResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Reply data empty. || Reply not saved.",
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
     *         description="Reply already submitted.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Booking request not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Reply not saved.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function putReviewReply(PutHostReviewReplyRequest $request)
    {
        // Fetch All Input Params.
        $input_params = $request->input();

        // Get Logged In User Id.
        $user_id = $request->getLoginUserId();

        $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);

        // Get booking details for provided request in database.
        $booking = Booking::getBookingForRequestAndHostId($request_id, $user_id);

        // If no booking exists for provided request id.
        if (empty($booking) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }

        // If booking review already exist.
        $reply_old_data = PropertyReview::getReviewForBooking($booking['id']);

        if (empty($reply_old_data) === true) {
            return ApiResponse::forbiddenError(EC_NOT_FOUND, 'Review not found.');
        }

        if (empty($reply_old_data) === false && empty($reply_old_data->reply) === false) {
            return ApiResponse::forbiddenError(EC_REVIEW_ALREADY_SUBMITTED, 'Reply already submitted.');
        }

        $save_reply = PropertyReview::addReviewReply(
            [
                'reply'      => $input_params['reply'],
                'booking_id' => $booking['id'],
                'host_id'    => $user_id,
            ]
        );

        if ($save_reply === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Reply not saved.');
        }

        $response = new PutHostPropertiesReviewResponse(['message' => 'Reply successfully submitted.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putReviewReply()


    /**
     * Save Booking Acknowledgement
     *
     * @param App\Http\Requests\PostHostBookingConfirmationRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/host/booking/confirm",
     *     tags={"Host"},
     *     description="Returns success message when reply save successfully.",
     *     operationId="host.post.booking.confirm",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if reply submitted successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostHostBookingConfirmationResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Reply data empty. || Reply not saved.",
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
     *         description="Acknowledgement already submitted.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Booking request not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postBookingConfirmation(PostHostBookingConfirmationRequest $request)
    {
        // Fetch All Input Params.
        $input_params = $request->input();

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);

        // Get logged in User Id.
        $user_id = $request->getLoginUserId();

        // Get booking count for provided request in database.
        $booking_count = BookingRequest::getBookedRequestsOfHost($user_id, $request_id);

        // If no booking exists for provided request id.
        if ($booking_count === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }

        // Check Already Marked or not.
        $check_marked_status = BookingAvailability::checkMarkedStatus($request_id, $user_id);

        if ($check_marked_status > 0) {
            return ApiResponse::forbiddenError(EC_REVIEW_ALREADY_SUBMITTED, 'Acknowledgement already submitted.');
        }

        $save_availability = BookingAvailability::confirmAvailability($request_id, $user_id);

        $response = [
            'message'      => 'You have successfully confirmed this booking.',
            'confirm_text' => 'You have confirmed this booking on '.Carbon::parse($save_availability->created_at)->format('d M Y'),
        ];

        $response = new PostHostBookingConfirmationResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postBookingConfirmation()


    /**
     * Save Traveller Arrival Confirmation
     *
     * @param App\Http\Requests\PutHostConfirmTravellerArrivalRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/host/booking/confirm-guest-arrival",
     *     tags={"Host"},
     *     description="Returns success message when data save successfully.",
     *     operationId="host.post.booking.confirm.guest.arrival",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/request_status_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if data submitted successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostHostBookingConfirmationResponse"),
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
     * @SWG\Response(
     *         response=403,
     *         description="Already checkin.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Booking request not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function putConfirmTravellerArrival(PutHostConfirmTravellerArrivalRequest $request)
    {
        // Fetch All Input Params.
        $input_params = $request->input();

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);

        // Get logged in User Id.
        $user_id = $request->getLoginUserId();

        // Get booking count for provided request in database.
        $booking_count = BookingRequest::getBookedRequestsOfHost($user_id, $request_id);

        // If no booking exists for provided request id.
        if ($booking_count === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'The booking id is invalid.');
        }

        $booking_request_service = new BookingRequestService;

        if ($input_params['status'] === 1) {
            $mark_checkin_status = $booking_request_service->savePriveManagerCheckedInStatus($request_id, ['status' => PRIVE_MANAGER_CHECKEDIN]);
        } else {
            $mark_checkin_status = $booking_request_service->savePriveManagerCheckedInStatus(
                $request_id,
                [
                    'status'    => PRIVE_MANAGER_NO_SHOW,
                    'reason_id' => 3,
                    'comment'   => '',
                ]
            );
        }

        if ($mark_checkin_status === false) {
            return ApiResponse::errorMessage('Our Server are busy. Please try after sometime.');
        }

        return ApiResponse::successMessage('Your response has been recorded.');

    }//end putConfirmTravellerArrival()


    /**
     * Get host payout settelment list of bookings
     *
     * @param App\Http\Requests\GetHostPayoutHistoryRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse containing host bank details
     *
     * @SWG\Get(
     *     path="/v1.6/host/payouts",
     *     tags={"Host"},
     *     description="get payout history of host",
     *     operationId="host.get.payouts",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/filter_start_date_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_end_date_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_settlement_status_in_query"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing bank details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                      ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                        ref="#definitions/GetHostPayoutsResponse"),
     * @SWG\Property(property="error",                                       ref="#/definitions/SuccessHttpResponse/properties/error"),
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
    public function getPayoutHistory(GetHostPayoutHistoryRequest $request)
    {
        // Fetch All Input params.
        $input_params = $request->input();

        $status = (empty($input_params['status']) === false) ? $input_params['status'] : 0;

        // Fetch Logged In User Id.
        $user_id = $request->getLoginUserId();

        // Get Payment Transaction history of host bookings.
        $payout_details = PayoutTransactions::getPayoutDetails($user_id, $input_params['start_date'], $input_params['end_date'], $status, $input_params['offset'], $input_params['total']);

        $payout_history = $payout_details['payouts'];
        $payout_count   = $payout_details['total_count'];

        $payout_data          = [];
        $due_amount           = 0;
        $booking_requests_ids = [];

        foreach ($payout_history as $one_transaction) {
            $host_fee       = $one_transaction['host_fee'];
            $extra_amount   = $one_transaction['extra_amount'];
            $price_details  = json_decode($one_transaction['price_details'], true);
            $settled_amount = $one_transaction['settled_amount'];

            if (empty($extra_amount) === false) {
                $host_fee = ($host_fee - $extra_amount);
            }

            if (isset($price_details['property_currency_code']) === true && $price_details['property_currency_code'] !== $price_details['currency_code']) {
                $host_fee = Helper::convertPriceToCurrentCurrency($price_details['property_currency_code'], $host_fee, DEFAULT_CURRENCY);
            }

            if (empty($one_transaction['currencyCode']) === false) {
                $settled_amount = Helper::convertPriceToCurrentCurrency($one_transaction['currencyCode'], $settled_amount, DEFAULT_CURRENCY);
            }

            $pending_amount   = ($host_fee - $settled_amount);
            $from_date_obj    = Carbon::parse($one_transaction['from_date']);
            $booking_date_obj = Carbon::parse($one_transaction['created_at']);

            $booking_requests_ids[] = $one_transaction['booking_request_id'];

            $payout_data[] = [
                'booking_requests_id' => Helper::encodeBookingRequestId($one_transaction['booking_request_id']),
                'booking_amount'      => Helper::getFormattedMoney($host_fee, DEFAULT_CURRENCY),
                'settled_amount'      => Helper::getFormattedMoney($settled_amount, DEFAULT_CURRENCY),
                'pending_amount'      => Helper::getFormattedMoney($pending_amount, DEFAULT_CURRENCY),
                'settlement_history'  => [],
                'booking_date'        => $booking_date_obj->format('d M Y'),
                'checkin_date'        => $from_date_obj->format('Y-m-d'),
                'checkin_formatted'   => $from_date_obj->format('d M Y'),
                'booking_status'      => Helper::getHostBookingStatusTextAndClass($one_transaction['booking_status'], $one_transaction['to_date']),
            ];
        }//end foreach

        // Attach settlement history of booking requests.
        if (empty($booking_requests_ids) === false) {
            $settlement_history = PayoutTransactions::getSettlementHistoryOfBookingRequests($booking_requests_ids);

            foreach ($settlement_history as $settlement) {
                foreach ($payout_data as $key => $data) {
                    if (Helper::encodeBookingRequestId($settlement->booking_request_id) === $data['booking_requests_id']) {
                        $payout_data[$key]['settlement_history'][] = [
                            'date'   => Carbon::parse($settlement->created_at)->format('d M Y'),
                            'utr'    => $settlement->utr_number,
                            'amount' => Helper::getFormattedMoney($settlement->amount, $settlement->currency),
                        ];
                    }
                }
            }
        }

        // Get Payment Transaction due amount of host bookings.
        $due_amount_data = PayoutTransactions::getPayoutDueAmount($user_id);

        foreach ($due_amount_data as $key => $one_due_amount) {
            $host_fee       = $one_due_amount['host_fee'];
            $extra_amount   = $one_due_amount['extra_amount'];
            $price_details  = json_decode($one_due_amount['price_details'], true);
            $settled_amount = $one_due_amount['settled_amount'];

            if (isset($price_details['property_currency_code']) === true && $price_details['property_currency_code'] !== $price_details['currency_code']) {
                $host_fee = Helper::convertPriceToCurrentCurrency($price_details['property_currency_code'], $host_fee, DEFAULT_CURRENCY);
            }

            if (empty($one_due_amount['currencyCode']) === false) {
                $settled_amount = Helper::convertPriceToCurrentCurrency($one_due_amount['currencyCode'], $settled_amount, DEFAULT_CURRENCY);
            }

            $due_amount = ($due_amount + ($host_fee - $settled_amount));
        }

        $response = [
            'payout_history' => $payout_data,
            'due_amount'     => Helper::getFormattedMoney($due_amount, DEFAULT_CURRENCY),
            'total_count'    => $payout_count,
        ];

        $response = new GetHostPayoutsResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getPayoutHistory()


    /**
     * Get host bank details
     *
     * @param App\Http\Requests\GetPaymentPreferencesRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse containing host bank details
     *
     * @SWG\Get(
     *     path="/v1.6/host/payment/preferences",
     *     tags={"Host"},
     *     description="get bank detail of host",
     *     operationId="host.get.payment.preferences",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing bank details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetHostPaymentPreferencesResponse"),
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
    public function getPaymentPreferences(GetPaymentPreferencesRequest $request)
    {
        // Fetch Logged in user Id.
        $user_id = $request->getLoginUserId();

        $bank_details = UserBillingInfo::getUserBankDetail($user_id);

        $response = ['bank_details' => $bank_details];

        $response = new GetHostPaymentPreferencesResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getPaymentPreferences()


    /**
     * Add host bank details
     *
     * @param \App\Http\Requests\PostPaymentPreferencesRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse containing host bank details
     *
     * @SWG\Post(
     *     path="/v1.6/host/payment/preferences",
     *     tags={"Host"},
     *     description="Add bank detail of host",
     *     operationId="host.post.payment.preferences",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/payee_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/payee_address_line_1_in_form"),
     * @SWG\Parameter(ref="#/parameters/payee_address_line_2_in_form"),
     * @SWG\Parameter(ref="#/parameters/payee_country_in_form"),
     * @SWG\Parameter(ref="#/parameters/payee_state_in_form"),
     * @SWG\Parameter(ref="#/parameters/bank_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/branch_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/account_number_in_form"),
     * @SWG\Parameter(ref="#/parameters/ifsc_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/routing_number_in_form"),
     * @SWG\Parameter(ref="#/parameters/gstin_in_form"),
     * @SWG\Response(
     *         response=201,
     *         description="Returns json containing bank details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                 ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                   ref="#definitions/PostHostPaymentPreferencesResponse"),
     * @SWG\Property(property="error",                                  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters. || Invalid source parameter.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     * )
     * )
     */
    public function postPaymentPreferences(PostPaymentPreferencesRequest $request)
    {
        // Get All Input Parameters.
        $input_params = $request->input();

        // Get Login User Id.
        $user_id = $request->getLoginUserId();
        // Get Admin id if exist.
        $admin_id = $request->getValidatedAdminIdOrFail();

        // Validate admin id.
        if (empty($admin_id) === false) {
            if (Admin::isAdmin($admin_id) === false) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Admin not found.');
            }
        }

        // Get Admin id if exist.
        $admin_id = $request->getValidatedAdminIdOrFail();

        // Validate admin id.
        if (empty($admin_id) === false) {
            if (Admin::isAdmin($admin_id) === false) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Admin not found.');
            }
        }

        // Save Bank Detail.
        $billing_info = $this->user_service->saveUserBankDetails(
            [
                'user_id'        => $user_id,
                'payee_name'     => $input_params['payee_name'],
                'address_line_1' => $input_params['address_line_1'],
                'address_line_2' => $input_params['address_line_2'],
                'country'        => $input_params['country'],
                'state'          => $input_params['state'],
                'bank_name'      => $input_params['bank_name'],
                'branch_name'    => $input_params['branch_name'],
                'account_number' => $input_params['account_number'],
                'ifsc_code'      => $input_params['ifsc_code'],
                'routing_number' => $input_params['routing_number'],
                'gstin'          => $input_params['gstin'],
                'admin_id'       => $admin_id,
            ]
        );

        // Validate Bank Info that already exust or not.
        if (empty($billing_info) === true) {
            return ApiResponse::errorMessage('Bank Details already linked.');
        }

        // Make Response Data.
        $response_data = [
            'bank_details' => $billing_info,
            'message'      => 'Bank Details added successfully.',
        ];

        // Send Data to Response Model.
        $response = new PostHostPaymentPreferencesResponse($response_data);
        $response = $response->toArray();
        return ApiResponse::create($response);

    }//end postPaymentPreferences()


    /**
     * Update host bank details
     *
     * @param App\Http\Requests\PutPaymentPreferencesRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse containing host bank details
     *
     * @SWG\Put(
     *     path="/v1.6/host/payment/preferences",
     *     tags={"Host"},
     *     description="update bank detail of host",
     *     operationId="host.put.payment.preferences",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/payee_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/bank_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/branch_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/account_number_in_form"),
     * @SWG\Parameter(ref="#/parameters/ifsc_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/payee_address_line_1_in_form"),
     * @SWG\Parameter(ref="#/parameters/payee_address_line_2_in_form"),
     * @SWG\Parameter(ref="#/parameters/payee_country_in_form"),
     * @SWG\Parameter(ref="#/parameters/payee_state_in_form"),
     * @SWG\Parameter(ref="#/parameters/routing_number_in_form"),
     * @SWG\Parameter(ref="#/parameters/gstin_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing bank details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                 ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                   ref="#definitions/PutHostPaymentPreferencesResponse"),
     * @SWG\Property(property="error",                                  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters. || Invalid source parameter. || Update Fail.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     * ),
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
    public function putPaymentPreferences(PutPaymentPreferencesRequest $request)
    {
        // Fetch Input Params.
        $input_params = $request->input();
        // Fetch Logged in user id.
        $user_id = $request->getLoginUserId();

        // Get Admin id if exist.
        $admin_id = $request->getValidatedAdminIdOrFail();

        // Validate admin id.
        if (empty($admin_id) === false) {
            if (Admin::isAdmin($admin_id) === false) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Admin not found.');
            }
        }

        // Update User Bank Details.
        $details_update_status = UserBillingInfo::updateBankDetail($user_id, $input_params, $admin_id);

        // Validate Update Status.
        if ($details_update_status === true) {
            $response = new PutHostPaymentPreferencesResponse(['message' => 'Bank Detail updated successfully.']);
            $response = $response->toArray();
            return ApiResponse::success($response);
        }

        return ApiResponse::errorMessage('Bank not linked with login user.');

    }//end putPaymentPreferences()


    /**
     * Output all reviews in proper formatting and detail.
     *
     * @param array $reviews Review data from db as array.
     * @param array $headers Header data as array.
     *
     * @return array formatted reviews
     */
    private static function processHostPropertyReviewData(array $reviews, array $headers)
    {
        $output = [];
        // Get property ids (unique) visited by user.
        $property_ids = array_unique(array_column($reviews, 'id'));

        // Get first property image to display.
        $properties_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, 1);

        foreach ($reviews as $review) {
            $temp                    = [];
            $temp['request_hash_id'] = Helper::encodeBookingRequestId($review['request_id']);
            $temp['guests']          = (int) $review['guests'];
            $temp['host_name']       = ucfirst($review['host_name']);
            $temp['host_image']      = $review['host_image'];
            $temp['property_rating'] = (float) $review['property_rating'];
            $temp['traveller_id']    = Helper::encodeUserId((int) $review['traveller_id']);
            $temp['traveller_name']  = ucfirst($review['traveller_name']);
            $temp['review_date']     = Carbon::parse($review['created_at'])->format('j F Y');
            $temp['comment']         = $review['comments'];
            $temp['reply']           = (empty($review['reply']) === false) ? $review['reply'] : '';

            if ($review['from_date'] === '0000-00-00' || $review['to_date'] === '0000-00-00') {
                $temp['nights'] = 0;
            } else {
                $from_date      = Carbon::parse($review['from_date']);
                $to_date        = Carbon::parse($review['to_date']);
                $temp['nights'] = ($to_date->diffInDays($from_date) > 0) ? $to_date->diffInDays($from_date) : 0;
            }

            if (USING_S3 === true) {
                $review_image_url = S3_REVIEW_ORIGINAL_DIR_URL;
            } else {
                $review_image_url = PROPERTY_REVIEW_IMAGE_BASE_URL;
            }

            $review_images = [];
            if (json_decode($review['images']) !== null) {
                foreach (json_decode($review['images']) as $image) {
                    $review_images[] = $review_image_url.$image;
                }
            }

            $temp['review_images'] = $review_images;
            $review['gender']      = (empty($review['gender']) === false) ? $review['gender'] : 'Male';

            $temp['traveller_image'] = Helper::generateProfileImageUrl($review['gender'], $review['profile_img'], (int) $review['traveller_id']);
            // Get tile structure for trip page.
            $review['original_title']    = true;
            $review['properties_images'] = $properties_images;
            $property_tile               = PropertyTileService::minPropertyTileStructure($review);
            $temp['property_tile']       = $property_tile;

            array_push($output, $temp);
        }//end foreach

        return $output;

    }//end processHostPropertyReviewData()


     /**
      * Get Rm host list
      *
      * @param \App\Http\Requests\GetRmHostRequest $request Http request object.
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\Get(
      *     path="/v1.6/host/rmhostlisting",
      *     tags={"Host"},
      *     description="Get host list",
      *     operationId="host.get.",
      *     consumes={"application/x-www-form-urlencoded"},
      *     produces={"application/json"},
      * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
      * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
      * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
      * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
      * @SWG\Response(
      *      response=200,
      *      description="host list data",
      * @SWG\Schema(
      * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
      * @SWG\Property(property="data",                                 ref="#definitions/GetRmHostListingResponse"),
      * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
      *      )
      * ),
      * @SWG\Response(
      *      response=401,
      *      description="Unauthorized action.",
      * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
      * ),
      * @SWG\Response(
      *      response=400,
      *      description="Not a Rm user.",
      * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
      * ),
      * )
      */
    public function getRmHostListing(GetRmHostRequest $request)
    {
        $input_params = $request->input();

        $offset = $input_params['offset'];
        $limit  = $input_params['total'];
        $user   = $this->getAuthUser();
        $email  = $user->email;

        // Check if email is valid guesthouser id.
        $is_rm = User::isUserRmByEmail($email);
        if ($is_rm === false) {
            return ApiResponse::forbiddenError(EC_NOT_FOUND, 'User should be a RM');
        }

        $hostlist = UserService::getRmHostList($email, $offset, $limit);
        $host     = [];
        foreach ($hostlist as $key => $hostdata) {
            $host[$key]['host_id']        = Helper::encodeUserId($hostdata['id']);
            $host[$key]['name']           = ucfirst($hostdata['name']).' '.ucfirst($hostdata['last_name']);
            $host[$key]['email']          = $hostdata['email'];
            $host[$key]['property_count'] = $hostdata['prop_count'];
        }

        $response = ['host_list' => $host];
        $response = new GetRmHostListingResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getRmHostListing()


    /**
     * Rm login as a host
     *
     * @param \App\Http\Requests\GetRmAsHostLoginRequest $request      Http request object.
     * @param string                                     $host_hash_id Host id in hash.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/host/rmashostlogin/{host_hash_id}",
     *     tags={"Host"},
     *     description="Login Rm as a host",
     *     operationId="host.get.rmhostlogin",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/host_hash_id_in_path"),
     * @SWG\Response(
     *      response=200,
     *      description="Access token host",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetRmAsHostLoginResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Invalid Host Id.||Not a Rm user||The user hash id field is invalid.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *      response=404,
     *      description="This host is not mapped with current logged in RM",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * )
     * )
     */
    public function getRmAsHostLogin(GetRmAsHostLoginRequest $request, string $host_hash_id)
    {
        $user  = $this->getAuthUser();
        $email = $user->email;
        $is_rm = User::isUserRmByEmail($email);

        if ($is_rm === false) {
            return ApiResponse::forbiddenError(EC_NOT_FOUND, 'User should be a RM');
        }

        $host_id = $request->decodeUserIdOrFail($host_hash_id);

        $is_host = User::isUserHost($host_id);
        if ($is_host === false) {
            return ApiResponse::validationFailed(['host_id' => 'Invalid Host Id.']);
        }

        $rm_host_list = UserService::getRmHostList($email, 0, 1, $host_id);

        if (count($rm_host_list) > 0) {
            $host           = User::find($host_id);
            $oauth_response = $this->getBearerTokenByUser($host, '2', false);

            // Response content.
            $content   = [
                'user_profile'  => User::getUserProfile($host_id, false),
                'token_type'    => $oauth_response['token_type'],
                'expires_in'    => $oauth_response['expires_in'],
                'access_token'  => $oauth_response['access_token'],
                'refresh_token' => $oauth_response['refresh_token'],
            ];
             $response = new GetRmAsHostLoginResponse($content);
             $response = $response->toArray();
            return ApiResponse::success($response);
        } else {
             return ApiResponse::notFoundError(EC_NOT_FOUND, 'This host is not mapped with current logged in RM');
        }

    }//end getRmAsHostLogin()


    /**
     * Save Host Lead
     *
     * @param \App\Http\PostLeadRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/host/lead",
     *     tags={"Host"},
     *     description="Returns success message when reply save successfully.",
     *     operationId="host.post.lead",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/name_lead_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_in_form"),
     * @SWG\Parameter(ref="#/parameters/email_in_form_not_required"),
     * @SWG\Parameter(ref="#/parameters/property_type_in_form"),
     * @SWG\Parameter(ref="#/parameters/city_in_form"),
     * @SWG\Parameter(ref="#/parameters/address_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if lead submitted successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostHostLeadResponse"),
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
     *    )
     */
    public function postLead(PostLeadRequest $request)
    {
        $input_params = $request->input();

        $params = [];

        $params['user_id']       = $request->getLoginUserId();
        $params['name']          = $input_params['name'];
        $params['contact']       = $input_params['contact'];
        $params['city']          = $input_params['city'];
        $params['email']         = $input_params['email'];
        $params['property_type'] = $input_params['property_type'];
        $params['address']       = $input_params['address'];
        HostConversionLead::saveLead($params);

        $response = ['message' => 'You have successfully saved this Lead.'];

        $response = new PostHostLeadResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postLead()


    /**
     * Clone Property
     *
     * @param \App\Http\Requests\PostPropertyCloneRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/host/property/clone",
     *     tags={"Host"},
     *     description="Returns success message when property cloned successfully.",
     *     operationId="host.post.property.clone",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/converted_by_in_form"),
     * @SWG\Parameter(ref="#/parameters/property_title_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if property cloned successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostPropertyCloneResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.|| The property hash id field is invalid.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while cloning. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     * )
     * )
     */
    public function postPropertyClone(PostPropertyCloneRequest $request)
    {
        $input_params     = $request->input();
        $property_hash_id = $input_params['property_hash_id'];
        $property_title   = $input_params['property_title'];
        $converted_by     = $input_params['converted_by'];

        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        $property = Property::getPropertyById($property_id);

        $clone_property = PropertyService::cloneProperty($property, $property_title, $converted_by);
        if (empty($clone_property) === true) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while cloning. Please try again.');
        }

        $clone_property_hash_id = Helper::encodePropertyId($clone_property);

          // Make Response Data.
        $response_data = [
            'cloned_property_hash_id' => $clone_property_hash_id,
            'message'                 => 'Property cloned succesffully',
        ];
        $response      = new PostPropertyCloneResponse($response_data);
        $response      = $response->toArray();
        return ApiResponse::success($response);

    }//end postPropertyClone()


    /**
     * Add Smart Discounts
     *
     * @param App\Http\Requests\PostHostSmartDiscountRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response containing price calendar
     *
     * @SWG\Post(
     *     path="/v1.6/host/smartdiscounts",
     *     tags={"Host"},
     *     description="change property Smart Discounts.",
     *     operationId="host.post.smartdiscounts",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/start_date_in_form"),
     * @SWG\Parameter(ref="#/parameters/end_date_in_form"),
     * @SWG\Parameter(ref="#/parameters/smart_discount_status_in_form"),
     * @SWG\Parameter(ref="#/parameters/smart_discounts_json_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing host property listings.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                  ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                    ref="#definitions/PostSmartDiscountsResponse"),
     * @SWG\Property(property="error",                                   ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Unable to Change Smart Discounts",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *      response=404,
     *      description="This host is not mapped with current logged in RM",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postSmartDiscounts(PostHostSmartDiscountRequest $request)
    {
        // IMPORTANT : This api is not in use any where yet.
        // As anyone use this please remove above comment.
        // Fetch all input params.
        $input_params = $request->input();

        // Validate and Decode property_hash_id.
        $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);

        // Validate Discounts Json Data.
        $discounts = json_decode($input_params['discounts'], true);

        foreach ($discounts as $discount) {
            $request->customValidation(
                $discount,
                [
                    'value' => 'required|integer|min:1',
                    'days'  => 'required|integer|min:1',
                    'id'    => 'integer',
                ],
                [
                    'value.required' => 'Invalid Discounts',
                    'days.required'  => 'Invalid Discounts',
                    'value.integer'  => 'Value in Discounts should be number',
                    'days.integer'   => 'Days in Discounts should be number',
                ]
            );
        }

        // Fetch Logged In user data.
        $user_id = $request->getLoginUserId();

        // Check Host Property Exist.
        $property = Property::checkHostPropertyExist($property_id, $user_id);

        if ($property === false) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Save Smart Discount.
        $change_smart_discount = PropertyPricingService::saveSmartDiscount(
            [
                'property_id' => $property_id,
                'user_id'     => $user_id,
                'start_date'  => $input_params['start_date'],
                'end_date'    => $input_params['end_date'],
                'status'      => $input_params['status'],
                'discounts'   => $discounts,
            ]
        );

        if ($change_smart_discount === true) {
            $response = [
                'property_hash_id' => $input_params['property_hash_id'],
                'message'          => 'Smart Discount changed successfully.',
            ];

            $response = new PostSmartDiscountsResponse($response);
            $response = $response->toArray();
            return ApiResponse::success($response);
        }

        return ApiResponse::errorMessage('Please add valid discounts.');

    }//end postSmartDiscounts()


    /**
     * Get Host listing property Detail
     *
     * @param App\Http\Requests\GetHostListingPropertyRequest $request          Http request object.
     * @param string                                          $property_hash_id Property id in hash.
     *
     * @return \Illuminate\Http\JsonResponse containg host property details
     *
     * @SWG\Get(
     *     path="/v1.6/host/listing/property/{property_hash_id}",
     *     tags={"Host"},
     *     description="get host's listing property detail.",
     *     operationId="host.get.listing.property",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns array containing host property detail.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetHostPropertyListingDetailResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters. || Auth token/User id missing.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getListingProperty(GetHostListingPropertyRequest $request, string $property_hash_id)
    {
        // Validate Property Hash Id and fetch property Id.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        // Get Logged In User Id.
        $user_id = $request->getLoginUserId();

        // Get all headers.
        $headers = $request->getAllHeaders();

        // Get host property listings.
        $property_details = $this->property_service->getListingPropertyData($user_id, $property_id);

        if (empty($property_details) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Get all property types.
        $all_property_types = PropertyType::getAllPropertyTypesData($property_details['property_type']);

         // Get all room types.
        $all_room_types = RoomType::getAllRoomTypes($property_details['room_type']);

        // Fetch Property Tags.
        $selected_property_tags = (empty($property_details['tag']) === false) ? explode(',', $property_details['tag']) : [];

        // Fetch Property Amenities.
        $selected_property_amenities = (empty($property_details['amenities']) === false) ? explode(',', $property_details['amenities']) : [];

        // Get All Property Tags.
        $all_property_tags = PropertyTagMapping::getAllPropertyTags($selected_property_tags);

        // Get All Active Amenities.
        $all_active_amenity = Amenity::getActiveAmenitiesGroupedByCategory($selected_property_amenities);

        // Get All Cancellation Policies.
        $all_cancellation_policy = CancellationPolicy::getAllCancellationPolicies($property_details['cancelation_policy']);

        // Get first property image to display.
        $property_details['properties_images'] = PropertyImage::getAllPropertiesImagesDetails($property_id);

        // Get first property image to display.
        $property_details['properties_videos'] = PropertyVideo::getAllPropertiesVideosDetails($property_id);

        // Get Property Tile Structure.
        $property_section = PropertyTileService::getHostListingPropertyTile($property_details);

        // Make Response Data.
        $response = [
            'property_details'   => $property_section,
            'property_types'     => $all_property_types,
            'room_types'         => $all_room_types,
            'tags'               => $all_property_tags,
            'amenities'          => $all_active_amenity,
            'cancelation_policy' => $all_cancellation_policy,
        ];

        // Pass Response data to Response Model.
        $response = new GetHostPropertyListingDetailResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getListingProperty()


    /**
     * Update Property by Host
     *
     * @param App\Http\Requests\PutPropertyRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response containing Property update status
     *
     * @SWG\Put(
     *     path="/v1.6/property",
     *     tags={"Host"},
     *     description="Update Property",
     *     operationId="host.put.property.edit",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/property_type_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/room_type_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/units_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/accomodation_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/per_unit_extra_guests_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/bedrooms_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/beds_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/bathrooms_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/title_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/currency_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/per_night_price_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/gh_commission_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/noc_status_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/address_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/area_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/city_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/state_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/country_code_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/zipcode_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/cancelation_policy_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/description_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/policy_services_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/house_rule_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/your_space_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/guest_brief_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/interaction_with_guest_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/local_experience_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/from_airport_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/train_station_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/bus_station_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/extra_detail_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/extra_guest_price_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/per_week_price_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/per_month_price_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/cleaning_fee_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/cleaning_mode_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/min_nights_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/max_nights_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/check_in_time_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/check_out_time_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/video_link_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/property_tags_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/usp_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/converted_by_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/latitude_by_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/longitude_by_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/search_keyword_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/gstin_in_form"),
     * @SWG\Parameter(ref="#/parameters/amenities_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/admin_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/image_caption_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/image_data_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/video_data_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/properly_title_in_form"),
     * @SWG\Response(
     *         response=201,
     *         description="Returns json containing bank details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                            ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                              ref="#definitions/PutPropertyResponse"),
     * @SWG\Property(property="error",                                             ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters. || Invalid source parameter.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     * )
     * )
     */
    public function putProperty(PutPropertyRequest $request)
    {
        // Get All Input Parameters.
        $input_params = $request->input();

        // Validate and Decode property_hash_id.
        $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);

        // Get Login User.
        $user = $request->getLoggedInUser();

        // Get Admin id if exist.
        $admin_id = $request->getValidatedAdminIdOrFail();

        // Validate admin id.
        if (empty($admin_id) === false) {
            if (Admin::isAdmin($admin_id) === false) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Admin not found.');
            }
        }

        // Get host property listings.
        $property = Property::getHostPropertyById($property_id, $user->id);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // User Verified Status.
        $input_params['user_verfied'] = $request->checkUserHasVerifiedContactAndEmail();

        // Decode Image Caption If exist.
        if (isset($input_params['image_caption']) === true) {
            $input_params['image_caption'] = json_decode($input_params['image_caption'], true);
        }

        // Property Data Keys.
        $property_data_keys = [
            // Required data.
            'property_type',
            'room_type',
            'units',
            'accomodation',
            'bedrooms',
            'beds',
            'bathrooms',
            'title',
            'properly_title',
            'currency',
            'cancelation_policy',
            'area',
            'city',
            'state',
            'country_code',
            'zipcode',
            'address',
            'description',
            'property_tags',
            'amenities',
            'latitude',
            'longitude',
            'noc_status',
            'min_nights',
            'max_nights',
            'check_in_time',
            'check_out_time',
            'search_keyword',
            'admin_id',
            'converted_by',
            'gstin',
            'image_caption',
            'video_link',
            'user_verfied',
        ];

        // Get Property Data.
        $property_data = Helper::getArrayKeysData($input_params, $property_data_keys);

        // Property Detail Keys.
        $property_details_keys = [
            'policy_services',
            'your_space',
            'house_rule',
            'guest_brief',
            'interaction_with_guest',
            'local_experience',
            'from_airport',
            'train_station',
            'bus_station',
            'extra_detail',
            'usp',
        ];

        // Get Property Detail Data.
        $property_detail_data = Helper::getArrayKeysData($input_params, $property_details_keys);

        // Property Pricing Keys.
        $property_pricing_keys = [
            'per_night_price',
            'gh_commission',
            'per_unit_extra_guests',
            'extra_guest_price',
            'per_week_price',
            'per_month_price',
            'cleaning_mode',
            'cleaning_fee',
            // Exceptional for save extra guest.
            'accomodation',
        ];

        // Get Property Pricing Data.
        $property_pricing_data = Helper::getArrayKeysData($input_params, $property_pricing_keys);

        // Image Data.
        $image_data_keys = [
            'image',
            'caption',
            'is_hide',
            'order',
        ];

        $image_data = [];

        if (isset($input_params['image_data']) === true) {
            // Validate Image Json Data.
            $image_data = json_decode($input_params['image_data'], true);

            foreach ($image_data as $image) {
                $request->customValidation(
                    $image,
                    [
                        'image'   => 'required',
                        'caption' => 'present',
                        'is_hide' => 'required|integer|in:0,1',
                        'order'   => 'required|integer',
                        'unlink'  => 'required|integer|in:0,1',
                    ],
                    [
                        'image.required'   => 'Image Required in Image data',
                        'caption.present'  => 'Caption Required in Image data',
                        'is_hide.required' => 'is_hide Required in Image data',
                        'order.required'   => 'Order Required in Image data',
                        'unlink.required'  => 'Unlink status required in Image data',
                    ]
                );
            }
        }//end if

        $video_data = [];

        if (isset($input_params['video_data']) === true) {
            // Validate Image Json Data.
            $video_data = json_decode($input_params['video_data'], true);

            foreach ($video_data as $video) {
                $request->customValidation(
                    $video,
                    [
                        'video'     => 'required',
                        'thumbnail' => 'required_with:video',
                        'unlink'    => 'required|integer|in:0,1',
                    ],
                    [
                        'video.required'          => 'Video Required in Video data',
                        'thumbnail.required_with' => 'thumbnail Required in Video data',
                        'unlink.required'         => 'Video Unlink status Required in Video data',
                    ]
                );
            }
        }//end if

        $property_tags = [];

        if (isset($input_params['property_tags']) === true) {
            $property_tags = array_map('intval', explode(',', $input_params['property_tags']));
        }

        $property_updated = $this->property_service->updateProperty($user->id, $property_id, $property_data, $property_detail_data, $property_pricing_data, $image_data, $property_tags, $video_data, $admin_id);

        // Validate Bank Info that already exust or not.
        if (empty($property_updated) === true) {
            return ApiResponse::errorMessage('Unable to update Property.');
        }

        // Dispatch Event for Notifications.
        $new_property_event = new PropertyListing($property_updated['property'], $user->email, false, $property_updated['updated_data'], ((empty($admin_id) === false) ? true : false));
        Event::dispatch($new_property_event);

        // Make Response Data.
        $response_data = [
            'property_hash_id' => Helper::encodePropertyId($property_updated['property']->id),
            'message'          => 'Property updated successfully.',
        ];

        // Send Data to Response Model.
        $response = new PutPropertyResponse($response_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putProperty()


    /**
     * Delete Host Listed Property
     *
     * @param App\Http\Requests\DeletePropertyRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Delete(
     *     path="/v1.6/host/property/remove",
     *     tags={"Host"},
     *     description="Delete Host Listed Property.",
     *     operationId="host.delete.property",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/admin_id_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Property successfully removed.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/DeleteHostPropertyResponse"),
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
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function deleteProperty(DeletePropertyRequest $request)
    {
        // Get All Input params.
        $input_params = $request->post();

        $admin_id = (isset($input_params['admin_id']) === true) ? $input_params['admin_id'] : 0;

         // Validate and Decode property_hash_id.
        $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);

        // Fetch Logged In user data.
        $user_id = $request->getLoginUserId();

        // Check Host Property Exist.
        $property_exist_status = Property::checkHostPropertyExist($property_id, $user_id);

        if ($property_exist_status === false) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        $property = Property::deleteProperty($property_id, $admin_id);

        if (empty($property) === true) {
            return ApiResponse::errorMessage('Unable to delete property.');
        }

        if (empty($property->lead_id) === false) {
            $update_host_listing_count = HostConversionLead::updateListingCount($property->lead_id);
        }

        $response = [
            'property_hash_id' => $input_params['property_hash_id'],
            'message'          => 'Property Deleted Successfully.',
        ];

        $response = new DeleteHostPropertyResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end deleteProperty()


}//end class
