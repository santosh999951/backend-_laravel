<?php
/**
 * Property Controller containing methods related to properties
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Hash, View};
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use \Auth;
use \Carbon\Carbon;
use \Event;

use App\Libraries\ApiResponse;
use App\Libraries\Helper;
use App\Libraries\v1_6\AwsService;
use App\Libraries\v1_6\PropertyService;
use App\Libraries\v1_6\PaymentMethodService;
use App\Libraries\v1_6\PropertyPricingService;
use App\Libraries\v1_6\PropertyTileService;
use App\Libraries\v1_6\SimilarListingService;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\CancellationPolicy;
use App\Models\CountryCodeMapping;
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
use App\Models\{TravellerRating,RatingParams, PropertyType, WalletTransaction, BookingReview};
use App\Models\{User, Admin};

use Aws\Exception\AwsException;

use App\Http\Response\v1_6\Models\{GetPropertyResponse, GetPropertyPriceCalendarResponse, GetPropertyReviewsResponse,
                                    GetTripReviewResponse, GetPropertySimilarResponse, PostPropertyRatingResponse,
                                    PostPropertyReviewResponse, PostPropertyReviewImageResponse, PostPropertyResponse};

use App\Http\Requests\{GetPropertyDetailRequest,GetPropertyPriceCalenderRequest,GetSimilarPropertyListingRequest,
    GetPropertyReviewRequest,GetTripReviewRequest , PostPropertyRatingRequest , PostPropertyReviewRequest,PostPropertyReviewImageRequest, PostProperty};

use App\Events\PropertyListing;


/**
 * Class PropertyController
 */
class PropertyController extends Controller
{

    /**
     * Property Service
     *
     * @var object property_service
     */
    private $property_service;


    /**
     * Constructor for dependency injection.
     *
     * @param PropertyService $property_service Property Service Object.
     *
     * @return void
     */
    public function __construct(PropertyService $property_service)
    {
        $this->property_service = $property_service;

    }//end __construct()


    /**
     * Get property preview data
     *
     * @param \App\Http\Requests\GetPropertyDetailRequest $request          Http request object.
     * @param string                                      $property_hash_id Property hash code.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/property/{property_hash_id}",
     *     tags={"Property"},
     *     description="get property data for preview page.",
     *     operationId="property.get.property",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/checkin_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/checkout_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/units_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/guests_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing data pertaining to property.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetPropertyResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
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
    public function getProperty(GetPropertyDetailRequest $request, string $property_hash_id)
    {
        // Get All Input Param.
         $input_params = $request->input();
         $property_id  = $request->decodePropertyIdOrFail($property_hash_id);

        // Check if property exists in database.
        $check_property_exists = Property::checkIfPropertyExistsById($property_id, false);

        if ($check_property_exists === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Get request parameters (if not entered, take default values).
        $selected_guests = $input_params['guests'];
        $selected_units  = $input_params['units'];

        $guests = ($selected_guests > 0) ? $selected_guests : DEFAULT_NUMBER_OF_GUESTS;
        $units  = ($selected_units > 0 ) ? $selected_units : DEFAULT_NUMBER_OF_UNITS;

        $start_date       = $input_params['checkin'];
        $is_date_selected = $start_date;
        $end_date         = $input_params['checkout'];
        $device_unique_id = $request->getDeviceUniqueId();
        $headers          = $request->getAllHeaders();

        $start_date       = (empty($start_date) === false && Carbon::parse(Carbon::today())->lessThanOrEqualTo(Carbon::parse($start_date)) === true) ? Carbon::parse($start_date)->toDateString() : '';
        $is_date_selected = $start_date;
        $end_date         = (empty($end_date) === false && empty($start_date) === false && Carbon::parse($end_date)->greaterThan(Carbon::parse($start_date)) === true) ? Carbon::parse($end_date)->toDateString() : '';

        if (empty($end_date) === true) {
            $start_date = '';
        }

        $selected_checkin  = $start_date;
        $selected_checkout = $end_date;

        // Set currency.
        $currency = DEFAULT_CURRENCY;

        // Check if user logged in.
        $is_user_logged_in = $this->isUserLoggedIn();
        $user_id           = 0;
        $user              = null;

        if ($is_user_logged_in === true) {
            // Get user id from access token.
            $user_data = $this->getAuthUserData();
            $user_id   = $user_data['user_id'];

            // Paramters to fetch from user table.
            $get_params = [
                'id',
                'base_currency',
            ];

            // Fetch user data.
            $user = User::getUserDataById($user_id, $get_params);

            // Set user currency.
            $currency = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        }

        // Get property data.
        $property = Property::getPropertyDetailsForPreviewPageById($property_id, $guests, $units, false, false);

        // Paramters to fetch for host.
        $host_params = [
            'name',
            'last_name',
            'country',
            'state',
            'city',
        ];

        // Fetch host data.
        $host       = User::getUserDataById($property['user_id'], $host_params);
        $host_image = User::getUserProfileImageByIds([$property['user_id']]);
        $host_image = (array_key_exists($property['user_id'], $host_image) === true) ? $host_image[$property['user_id']] : '';

        $host_gender = (empty($property['gender']) === false) ? $property['gender'] : 'Male';

        if (empty($host_image) === true) {
            $host_image = Helper::generateProfileImageUrl($host_gender, '', $property['user_id']);
        }

        // Update property preview page view counter.
        PropertyView::updatePropertyViewsCounter($device_unique_id, $is_user_logged_in, $user_id, $property_id, $start_date, $end_date, $guests);

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
            'additional_guest_fee'   => $property['additional_guest_fee'],
            'cleaning_fee'           => $property['cleaning_fee'],
            'cleaning_mode'          => $property['cleaning_mode'],
            'service_fee'            => $property['service_fee'],
            'custom_discount'        => $property['custom_discount'],
            'fake_discount'          => $property['fake_discount'],
            'accomodation'           => $property['accomodation'],
            'additional_guest_count' => $property['additional_guest_count'],
            'property_units'         => $property['units'],
            'instant_book'           => $property['instant_book'],
            'markup_service_fee'     => (int) $property['markup_service_fee'],
            'min_nights'             => $property['min_nights'],
            'max_nights'             => $property['max_nights'],
            'prive'                  => $property['prive'],
            'gh_commission'          => $property['gh_commission'],
            'room_type'              => $property['room_type'],
            'bedrooms'               => $property['bedrooms'],
            'user'                   => $user,
            'error'                  => [],
        ];

        // Get property pricing details.
        $property_pricing = PropertyPricingService::getPropertyPrice($property_pricing_data);

        // As No coupon here.
        $discount = 0;

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

        $display_price = PropertyTileService::propertyTilePricingArray($property_pricing);

        // Get property images.
        $property_images = PropertyImage::getPropertiesImagesByIds([$property_id], $headers);

        // Get property video.
        $property_video = PropertyVideo::getPropertyVideosByPropertyIds([$property_id]);

        // Check if property is wishlisted.
        $is_wishlisted = ($is_user_logged_in === true) ? (empty(MyFavourite::checkIfPropertyIsInWishlist($property_id, $user_id)) === false) ? 1 : 0 : 0;

        // Get total review count and top 2 property reviews.
        $property_review_data  = PropertyReview::getPropertyReviewData($property_id, 0, 2);
        $property_review_count = $property_review_data['total_review_count'];
        $property_reviews      = self::processPropertyReviewData($property_review_data['reviews']);

        // All country codes mapped with names.
        $countries = CountryCodeMapping::getCountries();
        $country   = $countries[$property['country']];

        // Get cancellation policy title to display.
        $cancellation_policy       = CancellationPolicy::getCancellationPoliciesByIds([$property['cancelation_policy']]);
        $cancellation_policy_title = (isset($cancellation_policy[$property['cancelation_policy']]) === true) ? $cancellation_policy[$property['cancelation_policy']]['title'] : '';

        // Get location of property.
        $location = [
            'area'          => ucfirst($property['area']),
            'city'          => ucfirst($property['city']),
            'state'         => ucfirst($property['state']),
            'country'       => $country,
        // Country name from code.
            'location_name' => Helper::formatLocation($property['area'], $property['city'], $property['state']),
            'latitude'      => $property['latitude'],
            'longitude'     => $property['longitude'],
        ];

        $payment_methods_params = [
            'is_instant_bookable'            => $property_pricing['is_instant_bookable'],
            'service_fee'                    => $property_pricing['total_service_fee'],
            'gh_commission'                  => $property_pricing['gh_commission_percent'],
            'coa_fee'                        => $property_pricing['coa_fee'],
            'gst'                            => $property_pricing['gst_amount'],
            'cash_on_arrival'                => $property['cash_on_arrival'],
            'booking_amount'                 => $property_pricing['total_price_all_nights'],
            'released_payment_refund_amount' => 0,
            'payable_amount'                 => $property_pricing['total_price_all_nights_with_cleaning_price_gst'],
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
        $payment_methods          = PaymentMethodService::getPaymentMethods($payment_methods_params);
        $available_payment_method = [];

        // Get available payment methods title.
        foreach ($payment_methods as $method => $method_details) {
            $available_payment_method[] = [
                'key'   => $method,
                'value' => $method_details['title'],
            ];
        }

        // Parameters to fetch footer data and 2 divs displaying refund policy and best payment method.
        $display_data = [
            'start_date'          => $start_date,
            'cancellation_policy' => $cancellation_policy[$property['cancelation_policy']],
            'currency'            => $currency,
            'payment_methods'     => $payment_methods,
        ];

        // Get footer data and 2 divs displaying refund policy and best payment method.
        $footer_cancellation_data = PropertyService::getFooterAndCancellationPolicyDivData($display_data, 'preview');
        $selected_payment_method  = $footer_cancellation_data['selected_payment_method'];
        unset($footer_cancellation_data['selected_payment_method']);

        if (empty($is_date_selected) === true) {
            $footer_cancellation_data['footer']['button_text'] = 'Select dates';
        }

        $bookable = 1;
        if ((int) $property['enabled'] !== 1 || (int) $property['status'] !== 1) {
            $footer_cancellation_data['footer']['button_text'] = 'Sold out';
            $bookable = 0;
        }

        // Parameters to fetch space data of property.
        $space_data = [
            'room_type_name'     => $property['room_type_name'],
            'bedrooms'           => $property['bedrooms'],
            'property_type'      => $property['property_type'],
            'property_type_name' => $property['property_type_name'],
            'accomodation'       => $property['accomodation'],
            'beds'               => $property['beds'],
            'bathrooms'          => $property['bathrooms'],
        ];

        // Get all space data of property.
        $property_space = $this->getSpaceDataForProperty($space_data);

        // Get property description and details.
        $property_description = PropertyService::getPropertyDescription($property_id, $property['check_in'], $property['checkout']);

        // Get all property tags of property.
        $property_tags = PropertyTagMapping::getPropertyTagsWithColorCodingByPropertyIds([$property_id]);

        // Get all property amenities.
        $property_amenities = ($property['amenities'] === '') ? [] : array_map('intval', explode(',', $property['amenities']));

        if (count($property_amenities) > 0) {
            $property_amenities = Amenity::getPropertyAmenityDetails($property_amenities, $headers);
        }

        $checkin  = (empty($start_date) === false) ? Carbon::createFromTimestamp(strtotime($start_date)) : Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS);
        $checkout = (empty($end_date) === false) ? Carbon::createFromTimestamp(strtotime($end_date)) : Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS);

        $no_of_days = $checkin->diffInDays($checkout, false);

        // Get recently viewed properties.
        $similar_properties = SimilarListingService::getSimilarProperties(
            [
                'property_id'    => $property_id,
                'start_date'     => $checkin->toDateString(),
                'end_date'       => $checkout->toDateString(),
                'days'           => $no_of_days,
                'guests'         => $guests,
                'units'          => $units,
                'offset'         => 0,
                'limit'          => 5,
                'latitude'       => $property['latitude'],
                'longitude'      => $property['longitude'],
                'state'          => $property['state'],
                'country'        => $property['country'],
                'property_type'  => $property['property_type'],
                'payable_amount' => $property_pricing['total_price_per_night'],
                'headers'        => $headers,
                'user_id'        => ($is_user_logged_in === true) ? $user_id : 0,
                'currency'       => $currency,
            ]
        );

        $meta = self::getMetaData($property['description'], $property['id'], (array_key_exists($property_id, $property_images) === true) ? $property_images[$property_id][0]['image'] : '', $property['address'], $property['user_id']);

        // Get Neighbourehood Attraction Images.
        $attraction_images = $this->property_service->getAttractionImages($headers, $property['latitude'], $property['longitude']);

        // Output data to be sent in an array.
        $propertyarray = [
            'id'                      => $property['id'],
            'property_hash_id'        => $property_hash_id,
            'property_title'          => ucfirst($property['title']),
            'title'                   => ucfirst(
                Property::propertyTitle(
                    [
                        'room_type'      => $property['room_type'],
                        'room_type_name' => $property['room_type_name'],
                        'bedrooms'       => $property['bedrooms'],
                        'property_type'  => $property['property_type_name'],
                        'units_consumed' => $property['units_consumed'],
                        'city'           => $property['city'],
                        'title'          => $property['title'],
                    ]
                )
            ),
            'property_score'          => ($property['property_score'] === null) ? 0 : (float) $property['property_score'],
            'property_images'         => (array_key_exists($property_id, $property_images) === true) ? $property_images[$property_id] : [],
            'property_image_count'    => (array_key_exists($property_id, $property_images) === true) ? count($property_images[$property_id]) : 0,
            'property_video'          => (array_key_exists($property_id, $property_video) === true) ? $property_video[$property_id] : [],
            'min_nights'              => $property['min_nights'],
            'max_nights'              => $property['max_nights'],
            'location'                => $location,
            'bookable'                => $bookable,

            'host_id'                 => Helper::encodeUserId($property['user_id']),
            'host_name'               => ucfirst(rtrim($host->name, ' ')),
            'host_image'              => $host_image,

            'review_count'            => $property_review_count,
            'reviews'                 => ($property_review_count > 0) ? $property_reviews : [],
            'tags'                    => (array_key_exists($property_id, $property_tags) === true) ? $property_tags[$property_id] : [],
            'cancellation_policy'     => $cancellation_policy[$property['cancelation_policy']],
            'about'                   => $property['description'],
            'usp'                     => $property_description['usp'],
            'description'             => $property_description['description'],
            'how_to_reach'            => $property_description['how_to_reach'],
            'space'                   => $property_space,
            'amenities'               => $property_amenities,

            'is_wishlisted'           => $is_wishlisted,

            'checkin'                 => $checkin->toDateString(),
            'checkout'                => $checkout->toDateString(),
            'selected_checkin'        => $selected_checkin,
            'selected_checkout'       => $selected_checkout,
            'selected_guests'         => $selected_guests,
            'selected_units'          => $selected_units,
            'required_units'          => $property_pricing['required_units'],
            'available_units'         => (int) $property_pricing['available_units'],
            'guests_per_unit'         => (int) $property_pricing['guests_per_unit'],

            'property_pricing'        => $display_price,

            'payment_methods'         => $available_payment_method,
            'selected_payment_method' => $selected_payment_method,

            'footer_data'             => $footer_cancellation_data,

            'similar_properties'      => $similar_properties,

            'meta'                    => $meta,

            // Extra Data For Website. Will Remove Soon.
            'enabled'                 => $property['enabled'],
            'status'                  => $property['status'],
            'prive'                   => $property['prive'],
            'attraction_images'       => $attraction_images,
            'misconception'           => $property_pricing['error'],
            'misconception_code'      => $property_pricing['error_code'],
        ];

        $response = new GetPropertyResponse($propertyarray);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getProperty()


    /**
     * List Property by User
     *
     * @param App\Http\Requests\PostProperty $request Http request object.
     *
     * @return \Illuminate\Http\Response containing host bank details
     *
     * @SWG\Post(
     *     path="/v1.6/property",
     *     tags={"Host"},
     *     description="Add Property",
     *     operationId="host.post.property.add",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_type_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/room_type_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/units_in_form"),
     * @SWG\Parameter(ref="#/parameters/accomodation_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/per_unit_extra_guests_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/bedrooms_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/beds_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/bathrooms_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/title_in_form"),
     * @SWG\Parameter(ref="#/parameters/currency_in_form"),
     * @SWG\Parameter(ref="#/parameters/per_night_price_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/gh_commission_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/noc_status_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/address_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/area_required_in_query"),
     * @SWG\Parameter(ref="#/parameters/city_in_form"),
     * @SWG\Parameter(ref="#/parameters/state_required_in_query"),
     * @SWG\Parameter(ref="#/parameters/country_code_required_in_query"),
     * @SWG\Parameter(ref="#/parameters/zipcode_required_in_query"),
     * @SWG\Parameter(ref="#/parameters/cancelation_policy_required_in_query"),
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
     * @SWG\Property(property="data",                                              ref="#definitions/PostPropertyResponse"),
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
    public function postProperty(PostProperty $request)
    {
        // Get All Input Parameters.
        $input_params = $request->input();

        // Get Login User.
        $user = $request->getLoggedInUser();

        // Get Admin id if exist.
        $admin_id = $request->getValidatedAdminIdOrFail();

        // Validate admin id.
        if (empty($admin_id) === false) {
            if (Admin::isAdmin($admin_id) === false) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Admin not found.');
            }

            // Custum validation for Admin Listing.
            $request->customValidation(
                $input_params,
                ['converted_by' => 'required|integer'],
                ['converted_by.required' => 'converted_by is required for admin ligin']
            );
        }

        // User Verified Status.
        $input_params['user_verfied'] = $request->checkUserHasVerifiedContactAndEmail();

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
            // Exceptional Condition for Extra guest count.
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
                    ],
                    [
                        'image.required'   => 'image Required in Image data',
                        'caption.present'  => 'caption Required in Image data',
                        'is_hide.required' => 'is_hide Required in Image data',
                        'order.required'   => 'order Required in Image data',
                    ]
                );
            }
        }//end if

        $video_data = [];

        if (empty($input_params['video_data']) === false) {
            // Validate Image Json Data.
            $video_data = json_decode($input_params['video_data'], true);

            if (empty($video_data) === false) {
                $request->customValidation(
                    $video_data,
                    [
                        'video'     => 'required',
                        'thumbnail' => 'required_with:video',
                    ],
                    [
                        'video.required'          => 'Video Required in Video data',
                        'thumbnail.required_with' => 'thumbnail Required in Video data',
                    ]
                );
            }
        }//end if

        $property_tags = [];

        if (isset($input_params['property_tags']) === true) {
            $property_tags = array_map('intval', explode(',', $input_params['property_tags']));
        }

        $property_created = $this->property_service->createProperty($user->id, $property_data, $property_detail_data, $property_pricing_data, $image_data, $property_tags, $video_data, $admin_id);

        // Validate Bank Info that already exust or not.
        if (empty($property_created) === true) {
            return ApiResponse::errorMessage('Unable to add Property.');
        }

        // Dispatch Event for Notifications.
        $new_property_event = new PropertyListing($property_created['property'], $user->email, true, [], ((empty($admin_id) === false) ? true : false));
        Event::dispatch($new_property_event);

        // Make Response Data.
        $response_data = [
            'property_hash_id' => Helper::encodePropertyId($property_created['property']->id),
            'message'          => 'Property added successfully.',
        ];

        // Send Data to Response Model.
        $response = new PostPropertyResponse($response_data);
        $response = $response->toArray();
        return ApiResponse::create($response);

    }//end postProperty()


    /**
     * Get property space data as an array.
     *
     * @param array $data Key value paired data to fetch space params related to property.
     *
     * @return array space data
     */
    private function getSpaceDataForProperty(array $data)
    {
        return [
            [
                'key'     => 'property_type',
                'name'    => $data['property_type_name'],
                'icon_id' => $data['property_type'],
            ],
            [
                'key'     => 'room_type',
                'name'    => $data['room_type_name'],
                'icon_id' => constant(strtoupper(str_replace(' ', '_', $data['room_type_name']))),
            ],
            [
                'key'     => 'bedrooms',
                'name'    => ($data['bedrooms'] > 1) ? $data['bedrooms'].' Bedrooms' : $data['bedrooms'].' Bedroom',
                'icon_id' => BEDROOM_ICON_ID,
            ],
            [
                'key'     => 'guests',
                'name'    => ($data['accomodation'] > 1) ? $data['accomodation'].' Guests' : $data['accomodation'].' Guest',
                'icon_id' => GUEST_ICON_ID,
            ],
            [
                'key'     => 'beds',
                'name'    => ($data['beds'] > 1) ? $data['beds'].' Beds' : $data['beds'].' Bed',
                'icon_id' => BED_ICON_ID,
            ],
            [
                'key'     => 'bathrooms',
                'name'    => ($data['bathrooms'] > 1) ? $data['bathrooms'].' Bathrooms' : $data['bathrooms'].' Bathroom',
                'icon_id' => BATHROOM_ICON_ID,
            ],
        ];

    }//end getSpaceDataForProperty()


    /**
     * Output all reviews in proper formatting and detail.
     *
     * @param array $reviews Review data from db as array.
     *
     * @return array formatted reviews
     */
    public static function processPropertyReviewData(array $reviews)
    {
        $output = [];

        foreach ($reviews as $review) {
            $temp                    = [];
            $temp['guests']          = (int) $review['guests'];
            $temp['property_rating'] = (float) $review['property_rating'];
            $temp['traveller_id']    = Helper::encodeUserId((int) $review['traveller_id']);
            $temp['traveller_name']  = ucfirst($review['traveller_name']);
            $temp['review_date']     = Carbon::parse($review['created_at'])->format('j F Y');
            $temp['comment']         = $review['comments'];

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

            array_push($output, $temp);
        }//end foreach

        return $output;

    }//end processPropertyReviewData()


     /**
      * Get property reviews
      *
      * @param \App\Http\Requests\GetPropertyReviewRequest $request          Http request object.
      * @param string                                      $property_hash_id Property hash code.
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\Get(
      *     path="/v1.6/property/reviews/{property_hash_id}",
      *     tags={"Property"},
      *     description="get property reviews.",
      *     operationId="property.get.property.reviews",
      *     produces={"application/json"},
      * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
      * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
      * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
      * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
      * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
      * @SWG\Response(
      *         response=200,
      *         description="Returns json containing property reviews.",
      * @SWG\Schema(
      * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
      * @SWG\Property(property="data",                                      ref="#definitions/GetPropertyReviewsResponse"),
      * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
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
    public function getPropertyReviews(GetPropertyReviewRequest $request, string $property_hash_id)
    {
        $input_params = $request->input();

        // Decode property_id from the hash id visible in url.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        // Check if property exists in database.
        $check_property_exists = Property::checkIfPropertyExistsById($property_id);

        if ($check_property_exists === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Input optional params.
        $offset = $input_params['offset'];
        $limit  = $input_params['total'];

        // Get property reviews with limit and offset.
        $property_review_data  = PropertyReview::getPropertyReviewData($property_id, $offset, $limit);
        $property_review_count = $property_review_data['total_review_count'];
        $property_reviews      = self::processPropertyReviewData($property_review_data['reviews']);

        $output = [
            'reviews'        => $property_reviews,
            'updated_offset' => ($offset + $limit > $property_review_data['total_review_count']) ? $property_review_data['total_review_count'] : ($offset + $limit),
            'limit'          => $limit,
        ];

        $response = new GetPropertyReviewsResponse($output);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getPropertyReviews()


     /**
      * Get pending rate review bookings and static data
      *
      * @param \App\Http\Requests\GetTripReviewRequest $request Http request object.
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\Get(
      *     path="/v1.6/trip/review",
      *     tags={"Property"},
      *     description="get pending rating review bookings",
      *     operationId="property.get.pendingratingreviews",
      *     produces={"application/json"},
      *     consumes={"application/x-www-form-urlencoded"},
      * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
      * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
      * @SWG\Parameter(ref="#/parameters/request_hash_id_optional_in_query"),
      * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
      * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
      * @SWG\Response(
      *         response=200,
      *         description="Returns array of static data and bookings.",
      * @SWG\Schema(
      * @SWG\Property(property="status",                                      ref="#/definitions/SuccessHttpResponse/properties/status"),
      * @SWG\Property(property="data",                                        ref="#definitions/GetTripReviewResponse"),
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
      *         description="Booking request not found.",
      * @SWG\Schema(
      *   ref="#/definitions/ErrorHttpResponse"),
      *     ),
      * )
      */
    public function getRatingReviewDetails(GetTripReviewRequest $request)
    {
        $input_params = $request->input();
        $user         = $this->getAuthUser();
        $user_id      = (int) $user->id;
        $user_wallet_currency_symbol = Helper::getCurrencySymbol($user->getWalletCurrency());

        $request_id = 0;
        $offset     = $input_params['offset'];
        $limit      = $input_params['total'];

        if (isset($input_params['request_hash_id']) === true && empty($input_params['request_hash_id']) === false) {
            $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);
        }

        $bookings = Booking::getBookingAndPropertyForTravellerId($user_id, $request_id, $offset, $limit);
        $headers  = $request->getAllHeaders();

        $response                  = [];
        $pending                   = [];
        $response['review_text']   = 'Write a review and we’ll credit '.Helper::getFormattedMoney(MAX_CASHBACK_FOR_REVIEW, $user_wallet_currency_symbol).' to your GuestHouser wallet!';
        $response['rating_params'] = RatingParams::getDefaultRatingParams();

        foreach ($bookings as $booking) {
            $property_images              = PropertyImage::getPropertiesImagesByIds([$booking['id']], $headers, 1);
            $booking['properties_images'] = $property_images;
            $booking['original_title']    = true;

            $booking_request_id = Helper::encodeBookingRequestId($booking['booking_request_id']);
            $tile               = PropertyTileService::minPropertyTileStructure($booking);
            $review_pending     = (empty($booking['review_id']) === false ) ? 0 : 1;
            $rating_pending     = (empty($booking['rating_id']) === false ) ? 0 : 1;

            if ($rating_pending === 0 && $review_pending === 0) {
                continue;
            }

            $pending[] = [
                'booking_request_id' => $booking_request_id,
                'property_section'   => $tile,
                'review_pending'     => $review_pending,
                'rating_pending'     => $rating_pending,
            ];
        }//end foreach

        $response['bookings'] = $pending;
        if (empty($limit) === false) {
            $response['updated_offset'] = ($offset + $limit);
            $response['limit']          = $limit;
        }

        $response = new GetTripReviewResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getRatingReviewDetails()


    /**
     * Post traveller rating for booking
     *
     * @param \App\Http\Requests\PostPropertyRatingRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/property/rating",
     *     tags={"Property"},
     *     description="post property rating.",
     *     operationId="property.post.property.rating",
     *     produces={"application/json"},
     *     consumes={"application/x-www-form-urlencoded"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/ratings_in_form"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/booking_experience_in_form"),
     * @SWG\Parameter(ref="#/parameters/property_rating_in_form"),
     * @SWG\Parameter(ref="#/parameters/booking_review_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if submitted successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostPropertyRatingResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Rating data empty. || Rating already submitted. || Rating not saved.",
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
     *         description="Rating already submitted.",
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
     *         description="Rating not saved.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postAddRating(PostPropertyRatingRequest $request)
    {
        $input_params = $request->input();

        // Input params.
        $ratings            = $input_params['ratings'];
        $request_hash_id    = $input_params['request_hash_id'];
        $booking_experience = $input_params['booking_experience'];
        $property_rating    = $input_params['property_rating'];
        $booking_review     = $input_params['booking_review'];
        $user_id            = (int) $this->getAuthUser()->id;
        $rating_value       = [
            '1',
            '2',
            '3',
            '4',
            '5',
        ];
        $rating_key         = [];
        $rating_param       = RatingParams::getDefaultRatingParams();

        foreach ($rating_param as $key => $value) {
            $rating_key[] = $value['id'];
        }

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $ratings = json_decode(mb_convert_encoding($ratings, 'UTF-8'), true);
        if (is_array($ratings) === false) {
            return ApiResponse::badRequestError(EC_VALIDATION_FAILED, 'The rating field is invalid.');
        }

        if (empty($ratings) === true) {
            return ApiResponse::badRequestError(EC_VALIDATION_FAILED, 'The rating field is invalid.');
        }

        $invalid_rating_key = array_diff(array_keys($ratings), $rating_key);
        if (empty($invalid_rating_key) === false) {
            return ApiResponse::badRequestError(EC_VALIDATION_FAILED, 'The rating field is invalid.');
        }

        $invalid_rating_value = array_diff($ratings, $rating_value);
        if (empty($invalid_rating_value) === false) {
            return ApiResponse::badRequestError(EC_VALIDATION_FAILED, 'The rating field is invalid.');
        }

        // Get booking details for provided request in database.
        $booking = Booking::getBookingForRequestAndTravellerId($request_id, $user_id);

        // If no booking exists for provided request id.
        if (empty($booking) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }

        // Check rating done by traveller.
        $rating = TravellerRating::getRatingForRequest($request_id, $user_id);

        if (empty($rating) === false) {
            return ApiResponse::forbiddenError(EC_RATING_ALREADY_SUBMITTED, 'Rating already submitted.');
        }

        $save_booking_review = BookingReview::saveBookingReview(
            [
                'booking_request_id' => $request_id,
                'property_id'        => $booking['pid'],
                'booking_rating'     => $booking_experience,
                'property_rating'    => $property_rating,
                'booking_comment'    => $booking_review,
            ]
        );

        $save_rating = TravellerRating::savePropertyRatingForBooking($ratings, $request_id, $booking['pid'], $user_id);

        if ($save_rating === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Rating not saved.');
        }

        $response = new PostPropertyRatingResponse(['message' => 'Ratings successfully submitted.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postAddRating()


    /**
     * Post traveller review for booking
     *
     * @param \App\Http\Requests\PostPropertyReviewRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/property/review",
     *     tags={"Property"},
     *     description="post property review.",
     *     operationId="property.post.property.review",
     *     produces={"application/json"},
     *     consumes={"application/x-www-form-urlencoded"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/review_in_form"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/review_images_object_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if submitted successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                 ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                   ref="#definitions/PostPropertyReviewResponse"),
     * @SWG\Property(property="error",                                  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Rating data empty. || Review already submitted. || Review not saved.",
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
     *         description="Review already submitted.",
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
     *         description="Review not saved.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postAddReview(PostPropertyReviewRequest $request)
    {
        $input_params = $request->input();

        // Input params.
        $review                   = $input_params['review'];
        $request_hash_id          = $input_params['request_hash_id'];
        $review_images            = $input_params['review_images'];
        $user_id                  = (int) $this->getAuthUser()->id;
        $successful_mapped_images = [];

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        // Get booking details for provided request in database.
        $booking = Booking::getBookingForRequestAndTravellerId($request_id, $user_id);

        // If no booking exists for provided request id.
        if (empty($booking) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }

        // If booking review already exist.
        $review_old_data = PropertyReview::getReviewForBooking($booking['id']);

        if (empty($review_old_data) === false) {
            return ApiResponse::forbiddenError(EC_REVIEW_ALREADY_SUBMITTED, 'Review already submitted.');
        }

        $review_images = json_decode($review_images);

        $review_images = (json_last_error() === JSON_ERROR_NONE) ? $review_images : [];

        if (empty($review_images) === false) {
            foreach ($review_images as $image) {
                try {
                    // If using s3, move image to s3 bucket and remove from local directory.
                    if (USING_S3 === true) {
                        AwsService::putObjectInS3Bucket(
                            S3_BUCKET,
                            S3_REVIEW_ORIGINAL_DIR.$image,
                            PROPERTY_REVIEW_IMAGE_TEMP_URL.$image,
                            'public-read'
                        );
                        unlink(PROPERTY_REVIEW_IMAGE_TEMP_URL.$image);
                    } else {
                        rename(PROPERTY_REVIEW_IMAGE_TEMP_URL.$image, PROPERTY_REVIEW_IMAGE_BASE_URL.$image);
                    }
                } catch (\ErrorException $e) {
                    continue;
                } catch (AwsException $e) {
                    continue;
                }

                array_push($successful_mapped_images, $image);
            }//end foreach
        }//end if

        $save_review = PropertyReview::addPropertyReview(
            [
                'review'        => $review,
                'review_images' => $successful_mapped_images,
                'booking_id'    => $booking['id'],
                'request_id'    => $request_id,
                'property_id'   => $booking['pid'],
                'user_id'       => $user_id,
                'host_id'       => $booking['host_id'],
            ]
        );

        if ($save_review === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Review not saved.');
        }

        $data_for_wallet = [
            'user_id'      => $user_id,
            'property_id'  => $booking['pid'],
            'request_id'   => $request_id,
            'wallet_money' => WALLET_MONEY_FOR_REVIEW,
        ];

        WalletTransaction::addWalletMoney(TRIP_AND_REVIEW, $data_for_wallet);

        $response = new PostPropertyReviewResponse(['message' => 'Review successfully submitted.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postAddReview()


    /**
     * Post property review image for booking
     *
     * @param \App\Http\Requests\PostPropertyReviewImageRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/property/review/image",
     *     tags={"Property"},
     *     description="post property review image.",
     *     operationId="property.post.property.review.image",
     *     produces={"application/json"},
     *     consumes={"multipart/form-data"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/review_image_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Image uploaded successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostPropertyReviewImageResponse"),
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
     *         response=500,
     *         description="Image not uploaded.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postReviewImage(PostPropertyReviewImageRequest $request)
    {
        $input_params = $request->file();

        // Input params.
        $uploaded_image = $input_params['review_image'];
        $user_id        = (int) $this->getAuthUser()->id;

        // Get original file name.
        $file_name = $uploaded_image->getClientOriginalName();

        // Get file extension.
        $extension = Helper::getImageExtension($file_name);

        $image_dir = PROPERTY_REVIEW_IMAGE_TEMP_URL;

        // New image name.
        $new_image_name = rand(100, 999).'_'.$user_id.'_'.time().'.'.$extension;

        // Move image to temp directory.
        try {
            $uploaded_image->move($image_dir, $new_image_name);
        } catch (FileException $e) {
            return ApiResponse::serverError(EC_FILE_NOT_UPLOADED, 'Image not uploaded.');
        }

        $response = [
            'picture' => $new_image_name,
            'message' => 'Image uploaded successfully.',
        ];

        $response = new PostPropertyReviewImageResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postReviewImage()


    /**
     * Get property price for calendar dates
     *
     * @param \App\Http\Requests\GetPropertyPriceCalenderRequest $request          Http request object.
     * @param string                                             $property_hash_id Property hash code.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/property/price/calendar/{property_hash_id}",
     *     tags={"Property"},
     *     description="get property prices w.r.t dates.",
     *     operationId="property.get.property.price",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/checkin_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/checkout_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/units_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/guests_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing property price.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetPropertyPriceCalendarResponse"),
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
    public function getPropertyPriceCalendar(GetPropertyPriceCalenderRequest $request, string $property_hash_id)
    {
        $input_params = $request->input();

        // Fetch property_id from the hash id visible in url.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        // Check if property exists in database.
        $check_property_exists = Property::checkIfPropertyExistsById($property_id, false);

        if ($check_property_exists === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Get request parameters (if not entered, take default values).
        $guests     = $input_params['guests'];
        $units      = $input_params['units'];
        $start_date = Carbon::parse($input_params['checkin']);
        $end_date   = Carbon::parse($input_params['checkout']);
        $no_of_days = $start_date->diffInDays($end_date, false);

        // Set currency.
        $currency = DEFAULT_CURRENCY;
        $user     = null;
        // Check if user logged in.
        $is_user_logged_in = $this->isUserLoggedIn();

        if ($is_user_logged_in === true) {
            // Fetch user data.
            $user    = $this->getAuthUser();
            $user_id = (int) $user->id;

            // Set user currency.
            $currency = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        }

        // Get property data.
        $property = Property::getPropertyDetailsForPreviewPageById($property_id, $guests, $units, false, false);

        if (empty($property) === true) {
            return ApiResponse::validationFailed(['property_hash_id' => 'The property hash id field is invalid.']);
        }

        // Get max base, extra guest a unit can hold.
        $allowed_guest_per_unit = Property::getBaseAndAdditionalGuestCount((int) $property['additional_guest_count'], (int) $property['accomodation']);

        $property_guest_count_per_unit       = $allowed_guest_per_unit['base_guest_count'];
        $property_extra_guest_count_per_unit = $allowed_guest_per_unit['extra_guest_count'];
        $property_total_guest_count_per_unit = $allowed_guest_per_unit['total_guest_count'];

        // If no of guests more than guests to be accomodated by 1 unit.
         $total_all_units_extra_guests = 0;
        if ($guests >= ($property_total_guest_count_per_unit * $units)) {
            $min_reqiured_units = (ceil($guests / $property_total_guest_count_per_unit) > $units) ? ceil($guests / $property_total_guest_count_per_unit) : $units;
        } else {
            $min_reqiured_units = $units;
        }

        if (($min_reqiured_units * $property_guest_count_per_unit) < $guests) {
            $total_all_units_extra_guests = ($guests - ($min_reqiured_units * $property_guest_count_per_unit));
        }

        // Array of data to process and get property pricing.
        $property_pricing_data = [
            'property_id'                  => $property_id,
            'start_date_obj'               => $start_date,
            'end_date_obj'                 => $end_date,
            'no_of_days'                   => $no_of_days,
            'min_nights'                   => $property['min_nights'],
            'max_nights'                   => $property['max_nights'],
            'additional_guest_count'       => $property['additional_guest_count'],
            'accomodation'                 => $property['accomodation'],
            'per_night_price'              => $property['per_night_price'],
            'service_fee'                  => $property['service_fee'],
            'additional_guest_fee'         => $property['additional_guest_fee'],
            'instant_book'                 => $property['instant_book'],
            'property_units'               => $property['units'],
            'gh_commission'                => $property['gh_commission'],
            'markup_service_fee'           => (int) $property['markup_service_fee'],
            'property_currency'            => $property['currency'],
            'user_currency'                => $currency,
            'cleaning_mode'                => $property['cleaning_mode'],
            'cleaning_fee'                 => $property['cleaning_fee'],
            'min_reqiured_units'           => $min_reqiured_units,
            'total_all_units_extra_guests' => $total_all_units_extra_guests,
        ];

        // Get property pricing details.
        $property_pricing = PropertyPricingService::calculatePerUnitPrice($property_pricing_data);

        $extra_details = [
            'min_nights'           => $property['min_nights'],
            'max_nights'           => $property['max_nights'],
            'guests_per_unit'      => (int) $property_total_guest_count_per_unit,
            'currency_plus_symbol' => $currency.'('.Helper::getCurrencySymbol($currency).')',
            'extra_guest_count'    => $property_extra_guest_count_per_unit,
        ];

        $property_pricing['per_day_price_array']['default'] = array_merge($property_pricing['per_day_price_array']['default'], $extra_details);

        $response = new GetPropertyPriceCalendarResponse($property_pricing['per_day_price_array']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getPropertyPriceCalendar()


    /**
     * Get properties similar to given property id.
     *
     * @param \App\Http\Requests\GetSimilarPropertyListingRequest $request          Http request object.
     * @param string                                              $property_hash_id Property hash code.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/property/similar/{property_hash_id}",
     *     tags={"Property"},
     *     description="get similar properties.",
     *     operationId="property.get.property.similar",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/checkin_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/checkout_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/units_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/guests_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing similar properties array.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetPropertySimilarResponse"),
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
    public function getSimilarProperties(GetSimilarPropertyListingRequest $request, string $property_hash_id)
    {
        $input_params = $request->input();

        // Get Property Id from the hash id visible in url.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        // Check if property exists in database.
        $check_property_exists = Property::checkIfPropertyExistsById($property_id);

        if ($check_property_exists === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Get request parameters (if not entered, take default values).
        $guests     = $input_params['guests'];
        $units      = $input_params['units'];
        $start_date = Carbon::parse($input_params['checkin']);
        $end_date   = Carbon::parse($input_params['checkout']);
        $no_of_days = $start_date->diffInDays($end_date, false);

        $offset = $input_params['offset'];
        $limit  = $input_params['total'];

        $headers = $request->headers->all();

        // Set currency.
        $currency = DEFAULT_CURRENCY;
        $user     = null;
        $user_id  = 0;
        // Check if user logged in.
        $is_user_logged_in = $this->isUserLoggedIn();

        if ($is_user_logged_in === true) {
            // Fetch user data.
            $user    = $this->getAuthUser();
            $user_id = (int) $user->id;

            // Set user currency.
            $currency = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        }

        // Get property data.
        $property = Property::getPropertyDetailsForPreviewPageById($property_id, $guests, $units, false);

        // Array of data to process and get property pricing.
        $property_pricing_data = [
            'property_id'            => $property_id,
            'start_date'             => $start_date->toDateString(),
            'end_date'               => $end_date->toDateString(),
            'units'                  => $units,
            'guests'                 => $guests,
            'user_currency'          => $currency,
            'property_currency'      => $property['currency'],
            'per_night_price'        => $property['per_night_price'],
            'additional_guest_fee'   => $property['additional_guest_fee'],
            'cleaning_fee'           => $property['cleaning_fee'],
            'cleaning_mode'          => $property['cleaning_mode'],
            'service_fee'            => $property['service_fee'],
            'custom_discount'        => $property['custom_discount'],
            'fake_discount'          => $property['fake_discount'],
            'accomodation'           => $property['accomodation'],
            'additional_guest_count' => $property['additional_guest_count'],
            'property_units'         => $property['units'],
            'instant_book'           => $property['instant_book'],
            'markup_service_fee'     => (int) $property['markup_service_fee'],
            'min_nights'             => $property['min_nights'],
            'max_nights'             => $property['max_nights'],
            'prive'                  => $property['prive'],
            'gh_commission'          => $property['gh_commission'],
            'room_type'              => $property['room_type'],
            'bedrooms'               => $property['bedrooms'],
            'user'                   => $user,
            'error'                  => [],
        ];

        // Get property pricing details.
        $property_pricing = PropertyPricingService::getPropertyPrice($property_pricing_data);

        $data = [
            'property_id'    => $property_id,
            'start_date'     => $start_date->toDateString(),
            'end_date'       => $end_date->toDateString(),
            'days'           => $no_of_days,
            'guests'         => $guests,
            'units'          => $units,
            'offset'         => $offset,
            'limit'          => $limit,
            'latitude'       => $property['latitude'],
            'longitude'      => $property['longitude'],
            'state'          => $property['state'],
            'country'        => $property['country'],
            'property_type'  => $property['property_type'],
            'payable_amount' => $property_pricing['total_price_per_night'],
            'headers'        => $headers,
            'user_id'        => ($is_user_logged_in === true) ? $user_id : 0,
            'currency'       => $currency,
        ];

        // Get recently viewed properties.
        $similar_properties = SimilarListingService::getSimilarProperties($data);

        $property = ['properties' => $similar_properties];
        $response = new GetPropertySimilarResponse($property);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getSimilarProperties()


    /**
     * Get metadata of property.
     *
     * @param string  $property_description Property description data.
     * @param integer $id                   Property Id.
     * @param string  $image_url            Property Image Url.
     * @param string  $address              Property Address.
     * @param integer $host_id              Property Host id.
     *
     * @return array property metadata.
     */
    public static function getMetaData(string $property_description, int $id, string $image_url, string $address, int $host_id)
    {
        $actual_slug = self::getPropertySlug($id);

        $meta_description = substr($property_description, 0, 150);
        $actual_link      = WEBSITE_URL.'/rooms/'.$actual_slug;
        $meta_information = self::getNewPropertyTitleDesc($id);

        $meta_info = [
            'meta_title'    => $meta_information['title'],
            'canonical_url' => $actual_link,
            'meta_desc'     => $meta_information['description'],
        ];

        $address = addslashes($address);

        // Find out parent property.
        $parent_property = \DB::Select(
            "select p.id from properties p 
                                        inner join property_parents pp on p.id = pp.pid
                                        where address = '{$address}'
                                        and p.user_id=".$host_id.' limit 1'
        );
        if (count($parent_property) > 0) {
            if ($parent_property[0]->id !== $id) {
                $parent_slug                = self::getPropertySlug($parent_property[0]->id, ['property_page' => 1 ]);
                $meta_info['canonical_url'] = WEBSITE_URL.'/rooms/'.$parent_slug;
            }
        }

        // Find out parent property end.
        $meta_info['meta_image'] = $image_url;
        return $meta_info;

    }//end getMetaData()


    /**
     * Get property title and description.
     *
     * @param integer $pid Property Id.
     *
     * @return array property data.
     */
    public static function getNewPropertyTitleDesc(int $pid=0)
    {
        $title = [];
        if (empty($pid) === false) {
            $new_title    = '';
            $search_title = '';
            $property     = Property::where('properties.id', '=', $pid)->join('room_type as rt', 'properties.room_type', '=', 'rt.id', 'left')->join('property_type as pt', 'properties.property_type', '=', 'pt.id', 'left');
            $property     = $property->select('properties.*', 'rt.name as room_type_name', 'pt.name as property_type_name')->first();

            $amenity_arr = explode(',', $property->amenities);
            $room_type   = $property->room_type;

            if ($room_type === 1 && in_array(23, $amenity_arr) === false) {
                $room_type_name = 'entire '.$property->property_type_name;
            } else if ($room_type === 2) {
                $room_type_name = $property->property_type_name.' Rooms';
            } else if ($room_type === 3) {
                $room_type_name = 'shared '.$property->property_type_name.' rooms';
            } else if ($room_type === 4) {
                $room_type_name = 'shared '.$property->property_type_name;
            } else {
                $room_type_name = $property->property_type_name;
            }

            if (in_array(23, $amenity_arr) === true) {
                $pet_friendly = ' pet friendly,';
            } else {
                $pet_friendly = '';
            }

            if (empty($property->city) === false) {
                $citystate = $property->city.', '.$property->state;
            } else if (empty($property->state) === false) {
                $citystate = $property->state;
            } else {
                $citystate = '';
            }

            $guests             = ($property->accomodation < 2) ? $property->accomodation.' number of guest' : $property->accomodation.' number of guests';
            $page_title         = 'Book'.rtrim(ucwords($pet_friendly), ',').' '.ucwords(strtolower($room_type_name)).' in '.$citystate.'-'.$pid;
            $des_room_type_name = ($property->room_type === 1) ? strtolower($property->property_type_name) : $room_type_name;
            $instant_booking    = ($property->instant_book === 1) ? ' and instant booking facility available' : '';
            $room               = ($property->room_type === 1 || $property->room_type === 4) ? 'This '.$property->property_type_name : 'The rooms';
            $page_desc          = 'Book '.$des_room_type_name.' in '.$property->city.' with'.$pet_friendly.' free amenities'.$instant_booking.'. '.$room.' can accomodate '.$guests.'.';

            $title['title']       = $page_title;
            $title['description'] = $page_desc;
            $title['keyword']     = '';
            return $title;
        }//end if

    }//end getNewPropertyTitleDesc()


    /**
     * Get SEO froendly property page URL slug.
     *
     * @param integer $id     Property Id.
     * @param array   $params Property Parameters.
     * @param integer $use_db Use db or not status.
     *
     * @return string property slug.
     */
    private static function getPropertySlug(int $id, array $params=[], int $use_db=1)
    {
        $property = null;
        if ($use_db === 1) {
            $property = Property::getPropertyWithTrashed($id);
        }

        if (isset($params['property_type']) === false) {
            $propertytype            = PropertyType::find($property->property_type);
            $params['property_type'] = $propertytype->name;
        }

        if (isset($params['room_type']) === false) {
            $room_type           = \DB::table('room_type')->where('id', $property->room_type)->first();
            $room_type_name      = $room_type->name;
            $room_type_name      = explode('/', $room_type_name);
            $params['room_type'] = $room_type_name[0];
        }

        if (isset($params['state']) === false) {
            $params['state'] = $property->state;
        }

        if (isset($params['city']) === false) {
            $params['city'] = $property->city;
        }

         $slug = self::createUrlStr($params['property_type']).'-'.self::createUrlStr($params['room_type']).'-'.self::createUrlStr($params['city']).'-'.self::createUrlStr($params['state']).'-'.Helper::encodePropertyId($id);
         return $slug;

    }//end getPropertySlug()


    /**
     * Get parse URL string.
     *
     * @param string $url Url.
     *
     * @return string url.
     */
    public static function createUrlStr(string $url)
    {
        return strtolower(str_replace([' ', '&'], ['-', 'and'], $url));

    }//end createUrlStr()


}//end class
