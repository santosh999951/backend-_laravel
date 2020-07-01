<?php
/**
 * Prepayment Controller containing methods related to prepayment page
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Hash, View};

use \Auth;
use \Carbon\Carbon;

use App\Libraries\ApiResponse;
use App\Libraries\Helper;
use App\Libraries\v1_6\CouponService;
use App\Libraries\v1_6\InvoiceService;
use App\Libraries\v1_6\PropertyService;
use App\Libraries\v1_6\PaymentMethodService;
use App\Libraries\v1_6\PropertyPricingService;
use App\Libraries\v1_6\PropertyTileService;
use App\Libraries\v1_6\UserService;


use App\Models\Amenity;
use App\Models\CancellationPolicy;
use App\Models\CurrencyConversion;
use App\Models\MyFavourite;
use App\Models\Property;
use App\Models\PropertyDetail;
use App\Models\PropertyImage;
use App\Models\PropertyPricing;
use App\Models\PropertyTagMapping;
use App\Models\PropertyVideo;
use App\Models\PropertyView;
use App\Models\PropertyReview;
use App\Models\User;
use App\Models\BookingRequest;

use App\Http\Response\v1_6\Models\{GetPrepaymentResponse, GetPrepaymentRequestResponse};
use App\Http\Requests\{GetPropertyPrepaymentDetailRequest,GetRequestPrepaymentDetailRequest};

/**
 * Class PrepaymentController
 */
class PrepaymentController extends Controller
{


    /**
     * Get property prepayment data
     *
     * @param \App\Http\Requests\GetPropertyPrepaymentDetailRequest $request          Http request object.
     * @param string                                                $property_hash_id Property hash code.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/prepayment/{property_hash_id}",
     *     tags={"Prepayment"},
     *     description="get property data, pricing, and payment data for prepayment page.",
     *     operationId="prepayment.get.details",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/checkin_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/checkout_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/units_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/guests_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/coupon_code_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/apply_wallet_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/payment_method_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing data pertaining to upcoming booking (amount to be paid, payment methods etc).",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                     ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                       ref="#definitions/GetPrepaymentResponse"),
     * @SWG\Property(property="error",                                      ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     *     ),
     * )
     */
    public function getPrepaymentDetails(GetPropertyPrepaymentDetailRequest $request, string $property_hash_id=null)
    {
        // Validate params.
        $input_params = $request->input();

        $is_mobile_verified = 0;
        $is_user_referred   = 0;
        $user               = null;
        $currency           = DEFAULT_CURRENCY;
        $is_user_logged_in  = $this->isUserLoggedIn();
        if ($is_user_logged_in === true) {
            $user     = $this->getAuthUser();
            $currency = ($user->base_currency !== '') ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;

            $is_mobile_verified = UserService::isMobileVerified($user);

            // phpcs:ignore
            $is_user_referred = ($user->referral_by == '') ? 0 : 1;
        }

        $discount               = 0;
        $discount_code          = '';
        $discount_valid         = 0;
        $discount_message       = '';
        $discount_type          = '';
        $wallet_money           = 0;
        $wallet_applicable      = 0;
        $released_payment       = 0;
        $wallet_currency_symbol = '';

        $response                 = [];
        $response_payment_methods = [];

        // Decode property_id from the hash id visible in url.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        // Get request parameters (if not entered, take default values).
        $selected_guests = $input_params['guests'];
        $selected_units  = $input_params['units'];

        $guests = ($selected_guests > 0) ? $selected_guests : DEFAULT_NUMBER_OF_GUESTS;
        $units  = ($selected_units > 0) ? $selected_units : DEFAULT_NUMBER_OF_UNITS;

        $start_date   = (empty($input_params['checkin']) === false) ? Carbon::parse($input_params['checkin'])->format('d-m-Y') : '';
        $end_date     = (empty($input_params['checkout']) === false) ? Carbon::parse($input_params['checkout'])->format('d-m-Y') : '';
        $coupon_code  = $input_params['coupon_code'];
        $apply_wallet = $input_params['apply_wallet'];

        $selected_payment_method = $input_params['payment_method'];

        $headers = $request->getAllHeaders();

        // Get property data.
        $property = Property::getPropertyDetailsForPreviewPageById($property_id, $guests, $units);

        if (count($property) !== 0) {
            if ($property['enabled'] !== 1) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
            }

            // Get all countries by their codes.
            // Get property images.
            $property_images = PropertyImage::getPropertiesImagesByIds([$property_id], $headers, 1);

            $property['properties_images'] = $property_images;
            $property['original_title']    = true;
            $property_section['tile']      = PropertyTileService::minPropertyTileStructure($property);

            $property_section['start_date'] = $start_date;
            $property_section['end_date']   = $end_date;

            // Array of data to process and get property pricing.
            $property_pricing_data = [
                'property_id'            => $property_id,
                'start_date'             => $start_date,
                'end_date'               => $end_date,
                'units'                  => $units,
                'guests'                 => $guests,
                'user_currency'          => $currency,
                'property_currency'      => $property['currency'],
                'per_night_price'        => $property['per_night_price'],
                'per_week_price'         => $property['per_week_price'],
                'per_month_price'        => $property['per_month_price'],
                'additional_guest_fee'   => $property['additional_guest_fee'],
                'cleaning_fee'           => $property['cleaning_fee'],
                'cleaning_mode'          => $property['cleaning_mode'],
                'service_fee'            => $property['service_fee'],
                'gh_commission'          => (int) $property['gh_commission'],
                'markup_service_fee'     => (int) $property['markup_service_fee'],
                'custom_discount'        => $property['custom_discount'],
                'fake_discount'          => $property['fake_discount'],
                'accomodation'           => $property['accomodation'],
                'additional_guest_count' => $property['additional_guest_count'],
                'property_units'         => $property['units'],
                'instant_book'           => $property['instant_book'],
                'min_nights'             => $property['min_nights'],
                'max_nights'             => $property['max_nights'],
                'room_type'              => $property['room_type'],
                'bedrooms'               => $property['bedrooms'],
                'user'                   => $user,
            ];

            // Get property pricing details.
            $property_pricing = PropertyPricingService::getPropertyPrice($property_pricing_data);

            $property_section['required_units']   = $property_pricing['required_units'];
            $property_section['guests']           = (int) $guests;
            $property_section['selected_units']   = $selected_units;
            $property_section['selected_guests']  = (int) $selected_guests;
            $property_section['min_nights']       = $property['min_nights'];
            $property_section['max_nights']       = $property['max_nights'];
            $property_section['available_units']  = (int) $property_pricing['available_units'];
            $property_section['guests_per_unit']  = (int) $property_pricing['guests_per_unit'];
            $property_section['instant_book']     = $property_pricing['is_instant_bookable'];
            $property_section['bookable_as_unit'] = ((int) $property['room_type'] === 1) ? 1 : 0;
            $property_section['property_pricing'] = PropertyTileService::propertyTilePricingArray($property_pricing);

            $booking_amount = $property_pricing['total_price_all_nights'];

            $coupon_applicable         = 1;
            $discounted_gh_service_fee = 0;
            $discounted_host_fee       = 0;

            if (empty($coupon_code) === false) {
                $coupon_data = [
                    'coupon_code'      => $coupon_code,
                    'property_city'    => $property['city'],
                    'property_state'   => $property['state'],
                    'booking_currency' => $currency,
                    'booking_amount'   => $booking_amount,
                    'host_fee'         => $property_pricing['total_host_fee'],
                    'user_id'          => ($user !== null) ? $user->id : 0,
                    'gh_commission'    => (int) $property['gh_commission'],
                    'is_mobile_app'    => ($request->getDeviceType() === 'app') ? true : false,
                    'property_type'    => $property['property_type'],
                    'from_date'        => $start_date,
                    'to_date'          => $end_date,
                ];

                $coupon = CouponService::checkCouponValidity($coupon_data);

                $discount_valid            = $coupon['status'];
                $discount                  = $coupon['total_discount'];
                $discount_message          = $coupon['message'];
                $discount_type             = 'coupon';
                $discount_code             = $coupon_code;
                $discounted_gh_service_fee = $coupon['gh_discount_amount'];
                $discounted_host_fee       = $coupon['host_discount_amount'];
            }//end if

            if (empty($user) === false) {
                $wallet = CouponService::checkWalletDiscount($booking_amount, $currency, $user->id, $user->usable_wallet_balance, $user->wallet_currency);

                $wallet_money           = $wallet['amount'];
                $wallet_applicable      = $wallet['status'];
                $wallet_currency_symbol = $wallet['currency_symbol'];

                if (empty($apply_wallet) === false) {
                    $discount_valid            = $wallet['status'];
                    $discount                  = $wallet['amount'];
                    $discount_message          = $wallet['message'];
                    $discount_type             = 'wallet';
                    $discount_code             = '1';
                    $discounted_gh_service_fee = $wallet_money;
                }
            }

            // Reduce service fee after discount.
            $property_pricing['total_service_fee'] = ($property_pricing['total_service_fee'] - $discounted_gh_service_fee);

            // This is with gh commision ,stored in requests.
            $property_pricing['total_host_fee'] = ($property_pricing['total_host_fee'] - $discounted_host_fee);

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

            $booking_amount_with_coupon_wallet_discount = ($booking_amount - $discount);

            $booking_amount_with_coupon_wallet_discount_with_cleaning = ($booking_amount_with_coupon_wallet_discount + $property_pricing['cleaning_price']);

            $property_pricing['total_price_all_nights_with_cleaning_price_gst_with_wallet_coupon_discount'] = ($booking_amount_with_coupon_wallet_discount_with_cleaning + $property_pricing['gst_amount']);

            // Discount.
            $payable_amount = $property_pricing['total_price_all_nights_with_cleaning_price_gst_with_wallet_coupon_discount'];

            if ($user !== null) {
                $released_payment = ($user->booking_credits > 0) ? $user->booking_credits : 0;
            }

            if ($released_payment > $payable_amount) {
                $released_payment_refund_amount = ($released_payment - $payable_amount);
                $payable_amount                 = 0;
            } else {
                $payable_amount                 = ($payable_amount - $released_payment);
                $released_payment_refund_amount = 0;
            }

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
            foreach ($payment_methods as $key => $method) {
                $response_payment_methods[] = array_merge(['key' => $key], $method);
            }

            $response_payment_methods = array_values($response_payment_methods);

            $available_method = $response_payment_methods[0]['key'];
            if (in_array($selected_payment_method, array_keys($payment_methods)) === true) {
                $available_method = $selected_payment_method;
            }

            // Parameters to fetch footer data and 2 divs displaying refund policy and best payment method.
            $display_data = [
                'price'                   => $payable_amount,
                'cancellation_policy'     => $cancellation_policy[$property['cancelation_policy']],
                'prive'                   => $property['prive'],
                'coa'                     => $property['cash_on_arrival'],
                'coa_fee'                 => $property_pricing['coa_fee'],
                'gh_commission'           => $property['gh_commission'],
                'start_date'              => $start_date,
                'payment_methods'         => $payment_methods,
                'currency'                => $currency,
                'selected_payment_method' => $available_method,
                'is_instant_bookable'     => $property_pricing['is_instant_bookable'],
            ];

            // Get footer data and 2 divs displaying refund policy and best payment method.
            $footer_cancellation_data = PropertyService::getFooterAndCancellationPolicyDivData($display_data);
            unset($footer_cancellation_data['selected_payment_method']);

            $guests_to_shown = ($selected_guests > 0 && $selected_guests < $property_pricing['per_unit_guests']) ? $selected_guests : $property_pricing['per_unit_guests'];

            $service_fee_without_gh_discount = ($property_pricing['total_service_fee'] + $discounted_gh_service_fee);
            if (empty(PRICE_WITHOUT_SERVICE_FEE) === false && isset($property_pricing['service_fee_on_price_with_discount_per_unit_per_night']) === true) {
                $service_fee_taxes = ($property_pricing['gst_amount'] + $service_fee_without_gh_discount);
            } else {
                $service_fee_taxes = $property_pricing['gst_amount'];
            }

            $invoice_data = [
                    // Base price- price for one night one room.              // per_night_per_unit_price .
                'per_night_per_unit_price'                         => $property_pricing['per_night_per_unit_price_without_service_fee'],

                    // No of guests in one units.
                'per_unit_guests'                                  => $guests_to_shown,

                    // Extra guest price - price for one night.
                    // For all extra guests all units.                         // per_night_all_guest_extra_guest_price .
                'per_night_all_units_extra_guest_price'            => $property_pricing['per_night_all_guest_extra_guest_price_without_service_fee'],

                    // All extra guests counts for all units.
                'all_units_extra_guests'                           => $property_pricing['total_extra_guests'],

                    // Price for one night for all units with extra guests price.   // total_price_per_night .
                'per_night_all_units_price_with_extra_guest_price' => $property_pricing['total_price_per_night_without_service_fee'],

                    // No of units required to accomadate all guests.
                'required_units'                                   => $property_pricing['required_units'],

                'no_of_nights'                                     => $property_pricing['no_of_nights'],

                    // All nights all days price with extra guests cost.        // total_price_all_nights .
                'all_night_all_units_price_with_extra_guest_price' => $property_pricing['total_price_all_nights_without_service_fee'],

                    // Cleaning fees.
                'cleaning_price'                                   => $property_pricing['cleaning_price'],

                    // Cash on arrival fees.
                'coa_fee'                                          => $property_pricing['coa_fee'],

                    // Discount as per coupon or wallet.
                'discount'                                         => $discount,

                    // Released payment.
                'released_payment'                                 => ($released_payment - $released_payment_refund_amount),

                    // Gst.
                'gst_amount'                                       => $property_pricing['gst_amount'],
                'gst_percent'                                      => $property_pricing['gst_percent'],
                'service_fee_and_taxes'                            => $service_fee_taxes,
                'payment_method'                                   => $available_method,

                    // Total payable amount.
                'payable_amount'                                   => $payment_methods[$available_method]['payable_amount'],
                'payable_now'                                      => $payment_methods[$available_method]['payable_now'],
                'payable_later'                                    => $payment_methods[$available_method]['payable_later'],

                'payable_later_before'                             => $payment_methods[$available_method]['payable_later_before'],

                'early_bird_cashback_percentage'                   => $property_pricing['early_bird_cashback_percentage'],
                'early_bird_cashback_amount'                       => round(($property_pricing['early_bird_cashback_percentage'] * $payment_methods[$available_method]['payable_amount'] / 100)),
                'early_bird_cashback_text'                         => $property_pricing['early_bird_cashback_text'],
                'early_bird_cashback_applicable'                   => $property_pricing['early_bird_cashback_applicable'],
                'currency_symbol'                                  => CURRENCY_SYMBOLS[$currency]['webicon'],
                'currency_code'                                    => $currency,
                'room_type'                                        => $property['room_type'],
            ];

            $formatted_invoice = InvoiceService::getFormattedInvoiceWithDetails($invoice_data);

            $prepayment_page = [
                'property_section'     => $property_section,
                'invoice'              => $formatted_invoice,

                'payment_methods'      => $response_payment_methods,
                'discount_section'     => [
                    'wallet'   => [
                        'wallet_money'           => $wallet_money,
                        'applicable'             => $wallet_applicable,
                        'wallet_currency_symbol' => $wallet_currency_symbol,
                    ],
                    'coupon'   => ['applicable' => $coupon_applicable],
                    'discount' => [
                        'discount_type'    => $discount_type,
                        'discount'         => $discount,
                        'discount_code'    => $discount_code,
                        'discount_message' => $discount_message,
                        'discount_valid'   => $discount_valid,

                    ],
                ],
                'footer_data'          => $footer_cancellation_data,
                'user_section'         => [
                    'is_mobile_verified' => $is_mobile_verified,
                    'is_user_referred'   => $is_user_referred,
                ],
                'cancellation_section' => [
                    'cancellation_policy_info' => $cancellation_policy,
                    'url'                      => WEBSITE_URL.'/cancellation_policy?app=1',
                ],
                'misconception'        => $property_pricing['error'],
                'misconception_code'   => $property_pricing['error_code'],
            ];

            $response = new GetPrepaymentResponse($prepayment_page);
            $response = $response->toArray();
            return ApiResponse::success($response);
        } else {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }//end if

    }//end getPrepaymentDetails()


    /**
     * Get Request prepayment data
     *
     * @param \App\Http\Requests\GetRequestPrepaymentDetailRequest $request         Http request object.
     * @param string                                               $request_hash_id Request hash code.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/prepayment/request/{request_hash_id}",
     *     tags={"Prepayment"},
     *     description="get property data, pricing, and payment data for request prepayment page.",
     *     operationId="prepayment.get.detailsforrequest",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/coupon_code_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/apply_wallet_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/payment_method_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing data in currenct booking request (amount to be paid, payment methods etc).",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                     ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                       ref="#definitions/GetPrepaymentRequestResponse"),
     * @SWG\Property(property="error",                                      ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     *         description="Property/booking request not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getPrepaymentDetailsForRequest(GetRequestPrepaymentDetailRequest $request, string $request_hash_id=null)
    {
        $input_params = $request->input();

        $discount                    = 0;
        $discount_code               = '';
        $discount_valid              = 0;
        $discount_message            = '';
        $discount_type               = '';
        $wallet_money                = 0;
        $wallet_applicable           = 0;
        $wallet_currency_symbol      = '';
        $released_payment            = 0;
        $applied_discount_amount     = 0;
        $coupon_applicable           = 1;
        $applied_coupon_code         = '';
        $applied_wallet              = 0;
        $applied_discounted_host_fee = 0;
        $applied_discounted_gh_service_fee = 0;
        $applied_released_amount           = 0;
        $discounted_gh_service_fee         = 0;
        $discounted_host_fee               = 0;
        $applied_wallet_amount             = 0;

        // Fix early bird cashback.
        $early_bird_cashback_percentage = 0;
        $early_bird_cashback_text       = '';
        $early_bird_cashback_applicable = false;
        $response_payment_methods       = [];

        $request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $user = $this->getAuthUser();

        $booking_request = BookingRequest::getBookingRequestForPreviewPageByUserId($request_id, $user->id);
        if (empty($booking_request) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid Booking Request.');
        }

        $property_id   = $booking_request['pid'];
        $guests        = $booking_request['guests'];
        $units         = $booking_request['units'];
        $start_date    = Carbon::parse($booking_request['from_date'])->format('d-m-Y');
        $end_date      = Carbon::parse($booking_request['to_date'])->format('d-m-Y');
        $price_details = json_decode($booking_request['price_details']);
        $no_of_nights  = $price_details->total_nights;
        $currency      = $booking_request['currency'];

        $is_mobile_verified = UserService::isMobileVerified($user);

        // phpcs:ignore
        $is_user_referred = ($user->referral_by == '') ? 0 : 1;

        $applied_gst_amount        = $price_details->gst_amount;
        $applied_total_service_fee = $price_details->service_fee;
        $applied_total_host_fee    = $price_details->host_fee;

        $per_unit_guests = ceil(($guests - $price_details->extra_guests) / $no_of_nights);

        $cleaning_price_per_unit = (isset($price_details->cleaning_price_per_unit) === true) ? $price_details->cleaning_price_per_unit : 0;

        $coa_fee = (isset($price_details->coa_charges) === true) ? $price_details->coa_charges : 0;

        // This include cleaning price as in website we are including.
        $booking_amount = ((($price_details->per_night_price * $units) + $price_details->extra_guest_cost ) * $no_of_nights);

        $payable_amount = $price_details->payable_amount;

        $new_payment_method_name = ((isset($price_details->choose_payment) === true ) ? Helper::getNewPaymentMethodName($price_details->choose_payment) : 'full_payment');

        $applied_payment_method = (isset($price_details->chosen_payment_method) === true) ? $price_details->chosen_payment_method : $new_payment_method_name;

        $gh_coupon_amount = (isset($price_details->gh_coupon_amount) === true) ? $price_details->gh_coupon_amount : '';

        $coupon_amount = (isset($price_details->coupon_amount) === true) ? $price_details->coupon_amount : '';

        $host_coupon_amount = (isset($price_details->host_coupon_amount) === true) ? $price_details->host_coupon_amount : '';

         // If Booking requests already has coupon.
        if (property_exists($price_details, 'coupon_applied') === true) {
            $applied_coupon_code               = $price_details->coupon_applied;
            $applied_discount_amount           = $coupon_amount;
            $applied_discounted_gh_service_fee = $gh_coupon_amount;
            $applied_discounted_host_fee       = $host_coupon_amount;
        }

        if (property_exists($price_details, 'wallet_money_applied') === true) {
            $applied_discount_amount           = $price_details->wallet_money_applied;
            $applied_wallet_amount             = $price_details->wallet_money_applied;
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
        $selected_payment_method = (isset($input_params['payment_method']) === true) ? $input_params['payment_method'] : $applied_payment_method;

        // Property Section Start.
        // Get property data.
        $property = Property::getPropertyDetailsForPreviewPageById($property_id, $guests, $units);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid Booking Request.');
        }

        $headers         = $request->getAllHeaders();
        $property_images = PropertyImage::getPropertiesImagesByIds([$property_id], $headers, 1);

        $property['properties_images'] = $property_images;
        $property['original_title']    = false;
        $property_section['tile']      = PropertyTileService::minPropertyTileStructure($property);

        $property_section['start_date']      = $start_date;
        $property_section['end_date']        = $end_date;
        $property_section['required_units']  = $units;
        $property_section['guests']          = $guests;
        $property_section['min_nights']      = $property['min_nights'];
        $property_section['max_nights']      = $property['max_nights'];
        $property_section['available_units'] = $units;
        $property_section['guests_per_unit'] = $per_unit_guests;
        $property_section['instant_book']    = $booking_request['instant_book'];

        // Property section End.
        $cancellation_policy = CancellationPolicy::getCancellationPoliciesByIds([$booking_request['cancellation_policy']]);

        if ((isset($input_params['apply_wallet']) === false || empty($input_params['apply_wallet']) === true) && (empty($coupon_code) === false)) {
            $coupon_data = [
                'coupon_code'        => $coupon_code,
                'property_city'      => $property['city'],
                'property_state'     => $property['state'],
                'booking_currency'   => $currency,
                'booking_amount'     => $booking_amount,
                'host_fee'           => ($applied_total_host_fee + $applied_discounted_host_fee),
                'user_id'            => $user->id,
                'gh_commission'      => (int) $booking_request['commission_from_host'],
                'is_mobile_app'      => ($request->getDeviceType() === 'app') ? true : false,
                'property_type'      => $property['property_type'],
                'from_date'          => $start_date,
                'to_date'            => $end_date,
                'booking_request_id' => $request_id,
            ];

            $coupon = CouponService::checkCouponValidity($coupon_data);

            $discount_valid            = $coupon['status'];
            $discount                  = $coupon['total_discount'];
            $discount_message          = $coupon['message'];
            $discount_type             = 'coupon';
            $discount_code             = $coupon_code;
            $discounted_gh_service_fee = $coupon['gh_discount_amount'];
            $discounted_host_fee       = $coupon['host_discount_amount'];
        }//end if

        $wallet = CouponService::checkWalletDiscount($booking_amount, $currency, $user->id, ($user->usable_wallet_balance + $applied_wallet_amount), $user->wallet_currency);

        $wallet_money           = $wallet['amount'];
        $wallet_currency_symbol = $wallet['currency_symbol'];
        $wallet_applicable      = $wallet['status'];

        if (isset($input_params['coupon_code']) === false && empty($apply_wallet) === false) {
            $discount_valid            = $wallet['status'];
            $discount                  = $wallet['amount'];
            $discount_message          = $wallet['message'];
            $discount_type             = 'wallet';
            $discount_code             = '1';
            $discounted_gh_service_fee = $wallet_money;
        }

        // Reduce service fee after discount.
        $total_service_fee = ($applied_total_service_fee + $applied_discounted_gh_service_fee - $discounted_gh_service_fee);
        $total_host_fee    = ($applied_total_host_fee + $applied_discounted_host_fee - $discounted_host_fee);

        $gh_commission_from_host = (($total_host_fee * $booking_request['commission_from_host']) / 100);

        $host_amount = ($total_host_fee - $gh_commission_from_host);

        $markup_service_fee = (isset($price_details->markup_service_fee) === true) ? $price_details->markup_service_fee : 0;

        $gst = helper::calculateGstAmount($host_amount, $property['room_type'], $booking_request['bedroom'], $currency, $no_of_nights, $units, $total_service_fee, $markup_service_fee, $gh_commission_from_host);

        $gst_percent = $gst['host_gst_percentage'];

        $gst_amount = $gst['total_gst'];

        $payable_amount = ($payable_amount + $applied_discount_amount - $discount);
        $payable_amount = ($payable_amount - $applied_gst_amount + $gst_amount);

        $released_payment_refund_amount = 0;
        $used_released_payment_amount   = 0;

        // Adding previous subtracted released payment amount if any.
        $payable_amount = ($payable_amount + $applied_released_amount);

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

        // SERVICE FEE SHOULD CHANGE ACCC TO DISOUNCT,
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
            'markup_service_fee'             => (isset($price_details->markup_service_fee) === true) ? $price_details->markup_service_fee : 0,
            'total_host_fee'                 => $total_host_fee,
        ];

            // Get payment methods and label to display.
        $payment_methods = PaymentMethodService::getPaymentMethods($payment_methods_params);
        foreach ($payment_methods as $key => $method) {
            $response_payment_methods[] = array_merge(['key' => $key], $method);
        }

            $response_payment_methods = array_values($response_payment_methods);

            $available_method      = 'full_payment';
            $available_method_text = $payment_methods[$available_method]['title'];
        if (in_array($selected_payment_method, array_keys($payment_methods)) === true) {
            $available_method      = $selected_payment_method;
            $available_method_text = $payment_methods[$available_method]['title'];
        }

        $service_fee_per_unit_on_price = (isset($price_details->service_fee_on_price_with_discount_per_unit_per_night) === true) ? $price_details->service_fee_on_price_with_discount_per_unit_per_night : 0;
        $service_fee_no_extra_guest    = (isset($price_details->service_fee_on_extra_guest_price_with_discount_per_guest_per_night) === true) ? $price_details->service_fee_on_extra_guest_price_with_discount_per_guest_per_night : 0;
        $service_fee_all_extra_guest   = ($service_fee_no_extra_guest * $price_details->extra_guests);

        $per_night_per_unit_price_without_service_fee = ($price_details->per_night_price - $service_fee_per_unit_on_price);

        $extra_guest_without_service = (($price_details->extra_guest_cost / (100 / (100 - $price_details->service_percentage))) / $no_of_nights);

        $service_fee_without_gh_discount = ($total_service_fee + $discounted_gh_service_fee);

        if (empty(PRICE_WITHOUT_SERVICE_FEE) === false && isset($price_details->service_fee_on_price_with_discount_per_unit_per_night) === true) {
            $service_fee_taxes = ($gst_amount + $service_fee_without_gh_discount);
        } else {
            $service_fee_taxes = $gst_amount;
        }

        $invoice_data = [
                // Base price- price for one night one room.
            'per_night_per_unit_price'                         => $per_night_per_unit_price_without_service_fee,

                // No of guests in one units.
            'per_unit_guests'                                  => $per_unit_guests,

                // Extra guest price - price for one night.
                // For all extra guests all units.
            'per_night_all_units_extra_guest_price'            => $extra_guest_without_service,

                // All extra guests counts for all units.
            'all_units_extra_guests'                           => $price_details->extra_guests,

                // Price for one night for all units with extra guests price.
            'per_night_all_units_price_with_extra_guest_price' => (($per_night_per_unit_price_without_service_fee * $units) + $extra_guest_without_service),

                // No of units required to accomadate all guests.
            'required_units'                                   => $units,

            'no_of_nights'                                     => $no_of_nights,

                // All nights all days price with extra guests cost.
            'all_night_all_units_price_with_extra_guest_price' => ((($per_night_per_unit_price_without_service_fee * $units) + $extra_guest_without_service) * $no_of_nights),

                // Cleaning fees.
            'cleaning_price'                                   => ($cleaning_price_per_unit * $units),

                // Cash on arrival fees.
            'coa_fee'                                          => $coa_fee,

                // Discount as per coupon or wallet.
            'discount'                                         => $discount,

                // Released payment.
            'released_payment'                                 => $used_released_payment_amount,

                // Gst.
            'gst_amount'                                       => $gst_amount,
            'gst_percent'                                      => $gst_percent,
            'service_fee_and_taxes'                            => $service_fee_taxes,

            'payment_method'                                   => $available_method,
            'payment_method_text'                              => $available_method_text,

                // Total payable amount.
            'payable_amount'                                   => $payment_methods[$available_method]['payable_amount'],
            'payable_now'                                      => $payment_methods[$available_method]['payable_now'],
            'payable_later'                                    => $payment_methods[$available_method]['payable_later'],

            'payable_later_before'                             => $payment_methods[$available_method]['payable_later_before'],

            'early_bird_cashback_percentage'                   => $early_bird_cashback_percentage,
            'early_bird_cashback_amount'                       => round(($early_bird_cashback_percentage * $payment_methods[$available_method]['payable_amount'] / 100)),
            'early_bird_cashback_text'                         => $early_bird_cashback_text,
            'early_bird_cashback_applicable'                   => $early_bird_cashback_applicable,
            'currency_symbol'                                  => CURRENCY_SYMBOLS[$currency]['webicon'],
            'currency_code'                                    => $currency,
            'room_type'                                        => $property['room_type'],
        ];

        $formatted_invoice = InvoiceService::getFormattedInvoiceWithDetails($invoice_data);

        // Parameters to fetch footer data and 2 divs displaying refund policy and best payment method.
        $display_data = [
            'service_fee'             => $total_service_fee,
            'cancellation_policy'     => $cancellation_policy[$booking_request['cancellation_policy']],
            'prive'                   => $booking_request['prive'],
            'coa'                     => $booking_request['coa_available'],
            'coa_fee'                 => $coa_fee,
            'gh_commission'           => $booking_request['commission_from_host'],
            'start_date'              => $start_date,
            'payment_methods'         => $payment_methods,
            'currency'                => $currency,
            'selected_payment_method' => $available_method,
        ];

        // Get footer data and 2 divs displaying refund policy and best payment method.
        $footer_cancellation_data = PropertyService::getFooterAndCancellationPolicyDivData($display_data);

        unset($footer_cancellation_data['selected_payment_method']);
        // Not setting this now. as not required to show error.
        $error      = '';
        $error_code = '';

        $prepayment_page = [
            'property_section'     => $property_section,
            'invoice'              => $formatted_invoice,
            'payment_methods'      => $response_payment_methods,
            'discount_section'     => [
                'wallet'   => [
                    'wallet_money'           => $wallet_money,
                    'applicable'             => $wallet_applicable,
                    'wallet_currency_symbol' => $wallet_currency_symbol,
                ],
                'coupon'   => ['applicable' => $coupon_applicable],
                'discount' => [
                    'discount_type'    => $discount_type,
                    'discount'         => $discount,
                    'discount_code'    => $discount_code,
                    'discount_message' => $discount_message,
                    'discount_valid'   => $discount_valid,

                ],
            ],
            'user_section'         => [
                'is_mobile_verified' => $is_mobile_verified,
                'is_user_referred'   => $is_user_referred,
            ],
            'footer_data'          => $footer_cancellation_data,
            'cancellation_section' => [
                'cancellation_policy_info' => $cancellation_policy,
                'url'                      => WEBSITE_URL.'/cancellation_policy?app=1',
            ],
            'misconception'        => $error,
            'misconception_code'   => $error_code,
        ];

        $response = new GetPrepaymentResponse($prepayment_page);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getPrepaymentDetailsForRequest()


}//end class
