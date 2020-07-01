<?php
/**
 * Search Controller containing methods related to search query
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use \Auth;
use Carbon\Carbon;

use App\Models\Amenity;
use App\Models\CancellationPolicy;
use App\Models\CountryCodeMapping;
use App\Models\CurrencyConversion;
use App\Models\MyFavourite;
use App\Models\PaymentGateway;
use App\Models\PopularSearch;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyTagMapping;
use App\Models\PropertyType;
use App\Models\PropertyVideo;
use App\Models\User;
use App\Models\AppExplore;


use App\Libraries\ApiResponse;
use App\Libraries\Helper;

use App\Libraries\v1_6\FilterService;
use App\Libraries\v1_6\PaymentMethodService;
use App\Libraries\v1_6\PropertyService;
use App\Libraries\v1_6\PropertyTileService;
use App\Libraries\v1_6\RecentlyViewedService;
use App\Libraries\v1_6\SearchService;

use App\Http\Response\v1_6\Models\{GetSearchRecentResponse, GetSearchResponse, GetSearchPopularResponse, GetSearchSpotlightResponse};
use App\Libraries\v1_6\OfferService;
use App\Http\Requests\{GetSearchRecentRequest};


/**
 * Class SearchController
 */
class SearchController extends Controller
{


    /**
     * Get search results
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/search",
     *     tags={"Search"},
     *     description="Get search results",
     *     operationId="search.get.index",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/lat_in_query"),
     * @SWG\Parameter(ref="#/parameters/lng_in_query"),
     * @SWG\Parameter(ref="#/parameters/per_page_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/item_count_offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/country_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/location_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/state_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/city_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/exclude_city_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/area_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/exclude_area_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/search_keyword_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/checkin_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/checkout_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/units_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/guests_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/roomtype_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/view_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/cancellation_policy_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/property_type_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/amenities_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/instant_book_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/cash_on_arrival_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/min_budget_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/max_budget_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/have_images_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/within_distance_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/prive_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/extra_params_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/sort_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/is_promotional_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/north_east_boundary_lat_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/north_east_boundary_lng_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/south_west_boundary_lat_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/south_west_boundary_lng_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/promo_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/slug_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Search page data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                              ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                                ref="#definitions/GetSearchResponse"),
     * @SWG\Property(property="error",                                               ref="#/definitions/SuccessHttpResponse/properties/error"),
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
    public function getIndex(Request $request)
    {
        // Input params.
        // Validate params.
        // $params_valid = $this->areParamsValid(
        // $request,
        // [
        // 'lat' => 'required',
        // 'lng' => 'required',
        // ]
        // );
        // // Send failed response if validation fails.
        // if ($params_valid !== true) {
        // return ApiResponse::validationFailed($params_valid);
        // }
        // Response data content.
        $content = [
            'lat'            => [],
            'lng'            => [],
            'popular_cities' => [],
        ];

        $seo_content   = [];
        $property_type = '';
        $selected_property_type_id_for_stay = 0;
        $selected_city_for_stay             = '';
        $meta = [];

        // Get all headers.
        $headers = $request->headers->all();

        // Collect params.
        $params             = [];
        $params['lat']      = $request->input('lat');
        $params['lng']      = $request->input('lng');
        $params['per_page'] = (int) $request->input('per_page', '12');
        $params['item_count_offset']   = (int) $request->input('item_count_offset', '0');
        $params['country']             = $request->input('country', 'IN');
        $params['location']            = addslashes($request->input('location', ''));
        $params['state']               = addslashes($request->input('state', ''));
        $params['city']                = addslashes($request->input('city', ''));
        $params['exclude_city']        = $request->input('exclude_city', '');
        $params['area']                = addslashes($request->input('area', ''));
        $params['exclude_area']        = addslashes($request->input('exclude_area', ''));
        $params['search_keyword']      = addslashes($request->input('search_keyword', ''));
        $params['checkin']             = $request->input('checkin', '');
        $params['checkout']            = $request->input('checkout', '');
        $params['bedroom']             = (int) $request->input('bedroom', '0');
        $params['guests']              = (int) $request->input('guests', (string) DEFAULT_NUMBER_OF_GUESTS);
        $params['roomtype']            = $request->input('roomtype', '');
        $params['view']                = $request->input('view', '');
        $params['cancellation_policy'] = $request->input('cancellation_policy', '');
        $params['property_type']       = $request->input('property_type', '');
        $params['amenities']           = $request->input('amenities', '');
        $params['instant_book']        = (int) $request->input('instant_book', '0');
        $params['cash_on_arrival']     = (int) $request->input('cash_on_arrival', '0');
        $params['min_budget']          = (int) $request->input('min_budget', '0');
        $params['max_budget']          = (int) $request->input('max_budget', '0');
        $params['have_images']         = (int) $request->input('have_images', '0');
        $params['within_distance']     = (int) $request->input('within_distance', '0');
        $params['prive']               = (int) $request->input('prive', '0');
        $params['extra_params']        = $request->input('extra_params', '');
        $params['sort']                = $request->input('sort', 'popularity');
        // Popularity, low_to_high, high_to_low, ratings.
        $params['is_promotional'] = $request->input('is_promotional', '0');
        $params['bounds_nelat']   = $request->input('bounds_nelat', '');
        $params['bounds_nelng']   = $request->input('bounds_nelng', '');
        $params['bounds_swlat']   = $request->input('bounds_swlat', '');
        $params['bounds_swlng']   = $request->input('bounds_swlng', '');
        $params['share']          = $request->input('share', '');

        // Decode Share Property Id.
        if (empty($params['share']) === false) {
            $params['share'] = Helper::decodePropertyHashId($params['share']);
        }

        // Extra params as key value pair.
        parse_str($params['extra_params'], $params['extra_params']);

        if (empty($params['search_keyword']) === false && $params['search_keyword'] === $params['exclude_area']) {
            $params['search_keyword'] = '';
        }

        // Set default params.
        $params['per_page']            = ($params['per_page'] < 1) ? NUMBER_OF_PROPERTIES_PER_PAGE : $params['per_page'];
        $params['per_page']            = ($params['per_page'] > MAX_NUMBER_OF_PROPERTIES_PER_PAGE) ? MAX_NUMBER_OF_PROPERTIES_PER_PAGE : $params['per_page'];
        $params['search_keyword']      = (empty($params['search_keyword']) === false) ? explode(',', $params['search_keyword']) : [];
        $params['roomtype']            = (empty($params['roomtype']) === false) ? explode(',', $params['roomtype']) : [];
        $params['cancellation_policy'] = (empty($params['cancellation_policy']) === false) ? explode(',', $params['cancellation_policy']) : [];
        $params['property_type']       = (empty($params['property_type']) === false) ? explode(',', $params['property_type']) : [];
        $params['amenities']           = (empty($params['amenities']) === false) ? explode(',', $params['amenities']) : [];
        $params['country']             = ($params['country'] === 'India') ? 'IN' : $params['country'];
        $params['params_checkin']      = $params['checkin'];
        $params['checkin']             = (empty($params['checkin']) === false) ? date('Y-m-d', strtotime($params['checkin'])) : Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS)->toDateString();
        $params['params_checkout']     = $params['checkout'];
        $params['checkout']            = (empty($params['checkout']) === false) ? date('Y-m-d', strtotime($params['checkout'])) : Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS)->toDateString();
        $params['params_bedroom']      = $params['bedroom'];
        $params['bedroom']             = ($params['bedroom'] > 0) ? $params['bedroom'] : 0;
        $params['param_guests']        = $request->input('guests');
        $params['guests']              = ($params['guests'] > 0) ? $params['guests'] : DEFAULT_NUMBER_OF_GUESTS;
        // phpcs:ignore
        $params['days']                = (empty($params['checkin']) === false && empty($params['checkout']) === false) ? round(abs(strtotime($params['checkout']) - strtotime($params['checkin'])) / 86400) : DEFAULT_NUMBER_OF_DAYS;
        $params['current_page_number'] = (int) floor((($params['item_count_offset'] + 1) / $params['per_page']));

        $promo = $request->input('promo', '0');

        $promo = (empty($promo) === false) ? $promo : 0;

        $slug = $request->input('slug', '');

        if (empty($slug) === false) {
            $slug          = self::getSlugExceptions($slug);
            $location_info = self::getLocationInfoFromSlug($slug);

            if ($location_info['is_valid'] === true) {
                $params['city']     = $location_info['adrs']['city'];
                $params['state']    = $location_info['adrs']['state'];
                $params['country']  = $location_info['adrs']['country'];
                $params['location'] = $location_info['location'];

                if (empty($params['current_page_number']) === true) {
                    $url_info = self::_getPtypeInfo($slug);

                    if ($url_info['is_valid'] === false) {
                        return ApiResponse::notFoundError(EC_NOT_FOUND, 'Page Not Found');
                    }

                    $property_type = $url_info['property_type_name'];

                    $selected_property_type_id_for_stay = (int) $url_info['property_type_id'];

                    $selected_city_for_stay = $url_info['url_location'];

                    if ($url_info['is_valid'] === true) {
                        $seo_content = self::_getLocationContent($url_info['url_location'], $url_info['property_type_name']);
                    }

                    $meta = self::_getMetaTagsData($params['property_type'], $params, $slug, $params['current_page_number'], $url_info);
                }//end if
            } else {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Page Not Found');
            }//end if
        } else {
            if ($params['view'] === 'map' && (empty($params['country']) === true && empty($params['lat']) === false && empty($params['lng']) === false)) {
                $location_data     = Helper::getLocationFromLatLong($params['lat'], $params['lng']);
                $params['city']    = $location_data['city'];
                $params['state']   = $location_data['state'];
                $params['country'] = $location_data['country'];
            } else if (empty($params['location']) === true) {
                if (empty($params['lat']) === true && empty($params['lng']) === true
                    && !( empty($params['country']) === false && empty($params['state']) === false)
                ) {
                    $user_ip       = Helper::getUserIpAddress();
                    $user_location = Helper::getLocationByIp($user_ip);

                    $params['city']    = $user_location['city'];
                    $params['state']   = $user_location['state'];
                    $params['country'] = $user_location['country_code'];
                    $params['lat']     = $user_location['lat'];
                    $params['lng']     = $user_location['lon'];
                }
            } else {
                if ((empty($params['lat']) === true && empty($params['lng']) === true) || strpos($params['location'], 'Jim Corbett National Park') !== false && !( empty($params['country']) === false && empty($params['state']) === false)) {
                    $location_data     = Helper::getLocationFromGoogleApi($params['location'], AUTOCOMPLETE_APIS[DEFAULT_AUTOCOMPLETE_API]);
                    $params['city']    = $location_data['city'];
                    $params['state']   = $location_data['state'];
                    $params['country'] = $location_data['country'];
                    $params['lat']     = $location_data['lat'];
                    $params['lng']     = $location_data['long'];
                }
            }//end if
        }//end if

        // Set default service fee.
        $service_fee = SERVICE_FEE;

        // Set default currency.
        $currency = DEFAULT_CURRENCY;

        // User id.
        $is_user_logged_in = $this->isUserLoggedIn();
        $user_id           = 0;

        // Set user currency if user is logged in.
        if ($is_user_logged_in === true) {
            // Get user id from access token.
            $user_data = $this->getAuthUser();
            $user_id   = $user_data->id;

            // Fetch user data through id.
            $user = User::getUserDataById($user_id, ['base_currency']);

            // Set user currency.
            $currency = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        }

        /*
            Get currency conversion rates
        */

        // Get countries currency value.
        $countries_currency_exchange_rates = CurrencyConversion::getAllCurrencyDetails();

        // Else case added just because currency_conversion table truncated by cron which gives error sometimes.
        if (isset($countries_currency_exchange_rates[$currency]) === true) {
            // Get user currency exchange rate.
            $user_currency_details = $countries_currency_exchange_rates[$currency];
            $user_currency_factor  = $user_currency_details['exchange_rate'];
        } else if (isset($countries_currency_exchange_rates[DEFAULT_CURRENCY]) === true) {
            // Get user currency exchange rate.
            $currency              = DEFAULT_CURRENCY;
            $user_currency_details = $countries_currency_exchange_rates[$currency];
            $user_currency_factor  = $user_currency_details['exchange_rate'];
        } else {
            $currency             = DEFAULT_CURRENCY;
            $user_currency_factor = 1;
        }

        /*
            Amounts till coa is applicable
        */

        // Change this.
        $max_coa_amount = Helper::convertPriceToCurrentCurrency(COA_CURRENCY, COA_MAX_AMOUNT, $currency);

        // Exceptional conditions for conflicting names.
        $updated_city_data  = SearchService::mapExceptionalLocationsToArea(
            [
                'city'     => $params['city'],
                'state'    => $params['state'],
                'country'  => $params['country'],
                'location' => $params['location'],
            ]
        );
        $params['city']     = $updated_city_data['city'];
        $params['state']    = $updated_city_data['state'];
        $params['country']  = $updated_city_data['country'];
        $params['location'] = $updated_city_data['location'];

        // Base currency details.
        $currency_details['base_currency']  = 'INR';
        $currency_details['final_currency'] = $currency;
        $currency_details['final_factor']   = $user_currency_factor;

        // Params required for making search query params.
        $search_query_input_params                   = $params;
        $search_query_input_params['currency']       = $currency;
        $search_query_input_params['max_coa_amount'] = $max_coa_amount;
        $search_query_input_params['user_currency_factor']              = $user_currency_factor;
        $search_query_input_params['countries_currency_exchange_rates'] = $countries_currency_exchange_rates;

        // Get results from search query.
        $raw_searched_properties = SearchService::getPropertiesFromParams($search_query_input_params);

        // Property ids.
        $property_ids = array_column($raw_searched_properties['properties_sorted_list'], 'id');

        // Default Property Images Count.
        $property_images_count = 1;

        // Property Tags Count.
        $property_tag_count = 1;

        if ($this->getDeviceType($request) === 'website') {
            // Property Images Count (0 indicate that send all images).
            $property_images_count = 0;

            // For 2x Image on website.
            $headers['image_optimized'] = 1;

            // Send All Property Tags.
            $property_tag_count = 4;
        }

        // Get properties images // pending - this should only get one image.
        $properties_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, $property_images_count);

        // Get videos.
        $properties_videos = PropertyVideo::getPropertyVideosByPropertyIds($property_ids);

        // Get tags.
        $properties_tags = PropertyTagMapping::getPropertyTagsWithColorCodingByPropertyIds($property_ids, $property_tag_count);

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        // Is payment gateway enabled for currency.
        $payment_gateway_enabled = PaymentGateway::isPaymentGateEnabledForCurrency($currency);

        // Cancellation policies.
        $cancellation_policy_ids = array_column($raw_searched_properties['properties_sorted_list'], 'cancelation_policy');

        // Get cancellation policy data.
        $cancellation_policy_data = CancellationPolicy::getCancellationPoliciesByIds($cancellation_policy_ids);

        // Properties added to wishlist out of fetched properties.
        $liked_property_ids = [];

        // User logged in.
        if ($is_user_logged_in === true) {
            // Check if user has liked any of these properties or not.
            $user_liked_properties = MyFavourite::getUserWishlistedPropertiesFromPropertyIds($user_id, $property_ids);

            // User liked properties.
            $liked_property_ids = array_column($user_liked_properties, 'property_id');
        }

        // Searched properties created from raw searched properties.
        $searched_properties = [];

        // Total properties found.
        $total_properties_count = $raw_searched_properties['total_properties_count'];

        // Iterate over each property and set property images data and other data.
        foreach ($raw_searched_properties['properties_sorted_list'] as $one_property) {
            // Property id.
            $property_id = $one_property->id;

             $effective_discount_percentage = round((100 - ($one_property->final_rate_without_service_fee * 100) / $one_property->actual_rate_without_service_fee), 0);

            // Capitalize first alphabet.
            $one_property->search_keyword = ucwords($one_property->search_keyword);

            // Get formatted location.
            $one_property->location_name = Helper::formatLocation($one_property->area, $one_property->city, $one_property->state);

            // Is property liked by user.
            $one_property->is_liked_by_user = (in_array($one_property->id, $liked_property_ids) === true) ? 1 : 0;

            // Get property description and details.
            $property_description = PropertyService::getPropertyDescription($one_property->id, $one_property->check_in, $one_property->checkout);

            // Params for calculating payment methods.
            $payment_methods_params = [
                'is_instant_bookable'            => $one_property->instant_book,
                'service_fee'                    => 0,
                'gh_commission'                  => 0,
                'coa_fee'                        => 0,
                'gst'                            => 0,
                'cash_on_arrival'                => $one_property->cash_on_arrival_without_service_fee,
                'booking_amount'                 => $one_property->final_rate_without_service_fee,
                'released_payment_refund_amount' => 0,
                'payable_amount'                 => $one_property->final_rate_without_service_fee,
                'prive'                          => $one_property->prive,
                'cancelation_policy'             => $one_property->cancelation_policy,
                'payment_gateway_enabled'        => 1,
                'checkin'                        => $params['checkin'],
                'policy_days'                    => $cancellation_policy_data[$one_property->cancelation_policy]['policy_days'],
                'user_currency'                  => $currency,
                'prive_property_coa_max_amount'  => Helper::convertPriceToCurrentCurrency('INR', PRIVE_PROPERTY_COA_MAX_AMOUNT, $currency),
                'partial_payment_coa_max_amount' => Helper::convertPriceToCurrentCurrency('INR', PARTIAL_PAYMENT_COA_MAX_AMOUNT, $currency),
                'checkin_formatted'              => Carbon::parse($params['checkin'])->format('d M Y'),
                'markup_service_fee'             => 0,
                'total_host_fee'                 => $one_property->final_rate_without_service_fee,
            ];

            // Get payment methods and label to display.
            $payment_methods = PaymentMethodService::getPaymentMethods($payment_methods_params);

            $host_gender = (empty($one_property->host_gender) === false) ? $one_property->host_gender : 'Male';
            $host_image  = (empty($one_property->host_image) === false) ? $one_property->host_image : '';
            $host_image  = Helper::generateProfileImageUrl($host_gender, $host_image, $one_property->host_id);

            // Get desired structure and push property into response property array.
            $one_property_tile = PropertyTileService::getPropertytileStructure(
                [
                    'property_id'           => $property_id,
                    'property_score'        => number_format((float) $one_property->property_score, 1),
                    'property_type_name'    => $one_property->property_type_name,
                    'room_type'             => $one_property->room_type,
                    'room_type_name'        => $one_property->room_type_name,
                    'search_keyword'        => $one_property->search_keyword,
                    'area'                  => $one_property->area,
                    'city'                  => $one_property->city,
                    'state'                 => $one_property->state,
                    'country'               => $one_property->country,
                    'country_codes'         => $country_codes,
                    'latitude'              => $one_property->latitude,
                    'longitude'             => $one_property->longitude,
                    'accomodation'          => $one_property->accomodation,
                    'currency'              => $one_property->final_currency,
                    'is_liked_by_user'      => $one_property->is_liked_by_user,
                    'display_discount'      => $effective_discount_percentage,
                    'smart_discount'        => $one_property->smart_discount,
                    'price_after_discount'  => $one_property->final_rate_without_service_fee,
                    'price_before_discount' => $one_property->actual_rate_without_service_fee,
                    'instant_book'          => $one_property->instant_book,
                    'cash_on_arrival'       => $one_property->cash_on_arrival_without_service_fee,
                    'bedrooms'              => $one_property->bedrooms,
                    'units_consumed'        => $one_property->units_consumed,
                    'title'                 => $one_property->title,
                    'properties_images'     => $properties_images,
                    'properties_videos'     => $properties_videos,
                    'properties_tags'       => $properties_tags,
                    'host_name'             => $one_property->host_name,
                    'host_image'            => $host_image,
                    'property_description'  => $property_description,
                ]
            );

            $one_property_tile['nearby_started'] = (isset($one_property->show_nearby_text) === true) ? $one_property->show_nearby_text : 0;

            $searched_properties[] = $one_property_tile;
        }//end foreach

        // Temp. solution form fixing 5xx on map search.
        if ($params['view'] === 'map' && (empty($params['country']) === true && empty($params['state']) === true && empty($params['city']) === true && empty($raw_searched_properties['properties_sorted_list']) === false)) {
            $params['country'] = $raw_searched_properties['properties_sorted_list'][0]->country;
            $params['state']   = $raw_searched_properties['properties_sorted_list'][0]->state;
            $params['city']    = $raw_searched_properties['properties_sorted_list'][0]->city;
        }

        // Fetch amenities grouped by category.
        $available_amenities = Amenity::getAmenitiesForSearchPage();

        // Nearby locations tags and keywords.
        $nearby_location_tags_keywords = PropertyService::getNearbyLocationTagsAndKeywords(
            [
                'city'                   => $params['city'],
                'state'                  => $params['state'],
                'country'                => $params['country'],
                'area'                   => $params['area'],
                'property_type'          => $property_type,
                'selected_city_for_stay' => $selected_city_for_stay,
            ]
        );

        $nearby_location_for_stays = [];

        if (empty($slug) === false) {
            // Nearby locations tags and keywords.
            $nearby_location_for_stays = PropertyService::getNearbyLocationsForStays(
                [
                    'city'                   => $params['city'],
                    'state'                  => $params['state'],
                    'country'                => $params['country'],
                    'area'                   => $params['area'],
                    'property_type'          => $property_type,
                    'selected_city_for_stay' => $selected_city_for_stay,
                ]
            );
        }

        // Get available property types.
        $available_property_types = PropertyType::getAvailablePropertyTypes($params['country'], $params['state'], $params['city'], $selected_property_type_id_for_stay);

        /*
            Get filter data
        */

        // Slider min and max in user currency.
        $slider_min_value = Helper::convertPriceToCurrentCurrency($currency_details['base_currency'], MIN_FILTER_BUDGET_VALUE_IN_INR, $currency_details['final_currency']);
        $slider_max_value = Helper::convertPriceToCurrentCurrency($currency_details['base_currency'], MAX_FILTER_BUDGET_VALUE_IN_INR, $currency_details['final_currency']);

        // Round of min and max values.
        $slider_min_value = (int) Helper::roundOfSliderMinValue(ceil($slider_min_value));
        $slider_max_value = (int) Helper::roundOfSliderMaxValue(ceil($slider_max_value));

        // Pending. // Fetch nearby properties.
        // Filter type - which card to show after property tiles.
        // Filter cards in properties.
        $filter_cards_in_properties = FilterService::getFilterCardAndRepetition(
            [
                'checkin'                 => $params['params_checkin'],
                'checkout'                => $params['params_checkout'],
                'guests'                  => $params['param_guests'],
                'min_budget'              => $params['min_budget'],
                'max_budget'              => $params['max_budget'],
                'slider_min_value'        => $slider_min_value,
                'slider_max_value'        => $slider_max_value,
                'instant_book'            => $params['instant_book'],
                'bedroom'                 => $params['params_bedroom'],
                'property_type'           => $params['property_type'],
                'roomtype'                => $params['roomtype'],
                'amenities'               => $params['amenities'],
                'search_keyword'          => $params['search_keyword'],
                'cash_on_arrival'         => $params['cash_on_arrival'],
                'current_page_number'     => $params['current_page_number'],
                'per_page'                => $params['per_page'],
                'total_properties_count'  => $total_properties_count,
                'nearby_properties_count' => count($raw_searched_properties['properties_sorted_list']),
            ]
        );

        // User search address data.
        $search_address_data = [
            'area'           => $params['area'],
            'city'           => $params['city'],
            'state'          => $params['state'],
            'country'        => $params['country'],

            'country_name'   => (array_key_exists($params['country'], $country_codes) === true) ? $country_codes[$params['country']]['name'] : $params['country'],
            'lat'            => $params['lat'],
            'lng'            => $params['lng'],
            'search_keyword' => $params['search_keyword'],
            'location'       => $params['location'],
        ];

        // Filter data.
        $filters = [
            'min_budget'                => ($params['min_budget'] > 0) ? $params['min_budget'] : $slider_min_value,
            'max_budget'                => ($params['max_budget'] > 0) ? $params['max_budget'] : $slider_max_value,
            'slider_min_value'          => $slider_min_value,
            'slider_max_value'          => $slider_max_value,
            'budget_currency'           => CURRENCY_SYMBOLS[$currency_details['final_currency']],
            'property_types'            => $available_property_types,
            'location_tags'             => $nearby_location_tags_keywords['location_search_tags'],
            'search_location'           => $nearby_location_tags_keywords['keyword_locations'],
            'popular_similar_locations' => $nearby_location_tags_keywords['popular_similar_locations'],
            'nearby_location'           => $nearby_location_for_stays,
            'amenities'                 => $available_amenities,
            'checkin'                   => $params['checkin'],
            'checkout'                  => $params['checkout'],
            'guests'                    => $params['guests'],
        ];

        if (empty($raw_searched_properties['properties_sorted_list']) === true && empty($seo_content) === false) {
            $seo_content = [];
        }

        if (empty($slug) === true) {
            $meta = self::searchMetaData($params['property_type'], $params, $total_properties_count);
        }

        $search_data = [
            'filters'                    => $filters,
            'properties_list'            => $searched_properties,
            'total_properties_count'     => $total_properties_count,
            'search_address_data'        => $search_address_data,
            'filter_cards_in_properties' => $filter_cards_in_properties,
            'promo_banners'              => self::getPromoBanner($promo),
            'seo_content'                => $seo_content,
            'meta'                       => $meta,
        ];

        $response = new GetSearchResponse($search_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getIndex()


    /**
     * Get search results
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/search/popular",
     *     tags={"Search"},
     *     description="Get popular search results",
     *     operationId="search.get.popular",
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     *     produces={"application/json"},
     * @SWG\Response(
     *         response=200,
     *         description="Trending search data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetSearchPopularResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * )
     */
    public function getPopularSearch()
    {
        // Get top 5 trending places.
        $popular_searches = PopularSearch::getTrendingPlaces(TRENDING_PLACES_LIMIT);

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        $location_list = ['list' => []];

        foreach ($popular_searches as $popular_search) {
            $temp_array              = [];
            $temp_array['location']  = $popular_search->location;
            $temp_array['state']     = $popular_search->state;
            $temp_array['city']      = $popular_search->city;
            $temp_array['area']      = $popular_search->area;
            $temp_array['country']   = $country_codes[$popular_search->country];
            $temp_array['latitude']  = $popular_search->lat;
            $temp_array['longitude'] = $popular_search->lng;

            $location_list['list'][] = $temp_array;
        }

        $response = new GetSearchPopularResponse($location_list);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getPopularSearch()


    /**
     * Get recently viewed properties by user
     *
     * @param \App\Http\GetSearchRecentRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/search/recent",
     *     tags={"Search"},
     *     description="get recently viewed properties by user.",
     *     operationId="property.get.recent",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing recently viewed properties data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetSearchRecentResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getRecentlyViewedProperties(GetSearchRecentRequest $request)
    {
        // Validate params.
        $input_params = $request->input();

        // Input optional params.
        $offset  = $input_params['offset'];
        $limit   = $input_params['total'];
        $user    = $request->getLoggedInUser();
        $user_id = (int) $user->id;

        // Default params.
        $start_date = Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS)->toDateString();
        $end_date   = Carbon::createFromTimestamp(time() + BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS)->toDateString();
        $no_of_days = ceil((BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS - BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS) / (24 * 60 * 60));
        $guests     = DEFAULT_NUMBER_OF_GUESTS;
        $bedrooms   = DEFAULT_NUMBER_OF_UNITS;

        $headers = $request->getAllHeaders();

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        // Set user currency.
        $currency = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;

        // Get recently viewed properties.
        $combined_recently_viewed_properties = RecentlyViewedService::getRecentlyViewedProperties(
            [
                'user_id'       => $user_id,
                'start_date'    => $start_date,
                'end_date'      => $end_date,
                'days'          => $no_of_days,
                'guests'        => $guests,
                'bedroom'       => $bedrooms,
                'currency'      => $currency,
                'country_codes' => $country_codes,
                'headers'       => $headers,
                'limit'         => $limit,
                'offset'        => $offset,
            ]
        );

        $content = ['recently_viewed_properties' => array_values($combined_recently_viewed_properties)];

        $response = new GetSearchRecentResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getRecentlyViewedProperties()


    /**
     * Get Spot light details
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/search/spotlight",
     *     tags={"Search"},
     *     description="get spot light details.",
     *     operationId="property.get.spotlight",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing spot light data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetSearchSpotlightResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     )
     * )
     */
    public function getSpotlightDetails()
    {
        // phpcs:ignore
        $result = AppExplore::join('property_type as pt', 'app_explore.p_type', '=', 'pt.id', 'left')->select('pt.name as property_type_name', 'p_type as property_type', 'place_state as state', 'tag', 'place_title as title', 'place_city as city', 'place_country as country', 'place_country_name as country_name', 'lat as v_lat', 'lng as v_lng', 'place_image as image_url')->get();
        foreach ($result as $val) {
            $val->image_url = SPOT_LIGHT_IMAGES_PATH.$val->image_url;
        }

        $spotlight_list = [];
        if (empty($result) === false) {
            $spotlight_list = $result->toArray();
        }

        // phpcs:disable
        //$response = new GetSearchSpotlightResponse($spotlight_list);
        //$response = $response->toArray();
        // phpcs:enable
        return ApiResponse::success($spotlight_list);

    }//end getSpotlightDetails()


     /**
      * Get promo banner urls
      *
      * @param integer $promo Promo type.
      *
      * @return array Banner Url.
      */
    public static function getPromoBanner(int $promo)
    {
        if (empty($promo) === false) {
            $promobanner = OfferService::getPromoBanners($promo);
            if (empty($promobanner) === true) {
                  return [];
            } else {
                $mobile_url = CDN_URL.S3_PROMOBANNER_DIR_MOBILE.$promobanner[0]['mobile_image'];
                return ['mobile_url' => $mobile_url];
            }
        } else {
            return [];
        }

    }//end getPromoBanner()


    /**
     * Get Exceptional slug
     *
     * @param string $slug Slug string.
     *
     * @return string Slug data.
     */
    public static function getSlugExceptions(string $slug)
    {
        if (strpos($slug, 'chikmagalur') !== false) {
                $slug = str_replace('chikmagalur', 'chikkamagaluru', $slug);
        }

        if ($slug === 'service-apartments-in-goa') {
            $slug = 'apartments-in-goa';
        }

        if (strpos($slug, 'coorg') !== false) {
            $slug = str_replace('coorg', 'kodagu', $slug);
        }

        if (strpos($slug, 'alleppey') !== false) {
            $slug = str_replace('alleppey', 'alappuzha', $slug);
        }

        if (strpos($slug, 'pondicherry') !== false) {
            $slug = str_replace('pondicherry', 'puducherry', $slug);
        }

            return $slug;

    }//end getSlugExceptions()


    /**
     * Get Location Information from slug
     *
     * @param string $slug Slug string.
     *
     * @return array Response data.
     */
    public static function getLocationInfoFromSlug(string $slug)
    {
        $response             = [];
        $response['is_valid'] = true;

        $parts = explode('-in-', strtolower($slug));

        if (count($parts) < 2) {
            $response['is_valid'] = false;
            return $response;
        }

        $location      = str_replace('-', ' ', $parts[1]);
        $query         = "select country,state,city from properties where (city='{$location}' or state ='$location') and enabled = 1 and status = 1 and deleted_at is null and admin_score > 0 limit 1";
        $location_data = \DB::select($query);

        if (count($location_data) > 0) {
            $adrs['country'] = $location_data[0]->country;

            if (strtolower($location) === strtolower($location_data[0]->state)) {
                $adrs['state'] = $location_data[0]->state;
                $adrs['city']  = '';
            } else {
                $adrs['state'] = $location_data[0]->state;
                $adrs['city']  = $location_data[0]->city;
            }

            $adrs['area']     = '';
            $adrs['lat']      = '';
            $adrs['long']     = '';
            $adrs['long']     = '';
            $adrs['postal']   = '';
            $response['adrs'] = $adrs;
            // $response['adrs'] = Common::getLocationFromGoogle($location);
            if (empty($response['adrs']['country']) === false) {
                $response['adrs']['country_name'] = self::countryname($response['adrs']['country']);
            } else {
                $response['adrs']['country_name'] = '';
            }

            $location_arr = [];
            if (empty($response['adrs']['city']) === false) {
                $location_arr[] = $response['adrs']['city'];
            }

            if (empty($response['adrs']['state']) === false) {
                $location_arr[] = $response['adrs']['state'];
            }

            if (empty($response['adrs']['country_name']) === false) {
                $location_arr[] = $response['adrs']['country_name'];
            }

            $response['location'] = implode(', ', $location_arr);
        } else {
            $response['is_valid'] = false;
        }//end if

        return $response;

    }//end getLocationInfoFromSlug()


    /**
     * Get Country Name from country code
     *
     * @param string $coun_code Country code.
     *
     * @return string Country name.
     */
    public static function countryname(string $coun_code)
    {
        $country_name = \DB::table('ccode_currency_mapping')->select('name')->where('ccode', $coun_code)->first();
        $c_name       = 'i';
        if (empty($country_name) === false) {
            $c_name = $country_name->name;
        }

        return ucfirst($c_name);

    }//end countryname()


    /**
     * Get content of a location from the database
     *
     * @param string $location Location.
     * @param string $ptype    Property Type.
     *
     * @return string Country name.
     */
    // phpcs:ignore
    private static function _getLocationContent(string $location, string $ptype)
    {
        $ptype    = strtolower(str_replace(' ', '-', $ptype));
        $location = strtolower(str_replace(' ', '-', $location));

        $content = \DB::table('seo_locations_content')->select('title', 'description', 'details', 'meta_title', 'meta_description')->where('property_type', '=', $ptype)->where('location', '=', $location)->where('active', '=', 1)->first();

        if (empty($content) === false) {
            $content->show          = true;
            $content                = (array) $content;
            $content['description'] = str_replace('>read more<', '><', $content['description']);
            $content['details']     = str_replace('>Back to top<', '><', $content['details']);

            return $content;
        } else {
            return [
                'show'        => false,
                'title'       => '',
                'description' => '',
            ];
        }

    }//end _getLocationContent()


    /**
     * Get property type info from slug
     *
     * @param string $slug Slug.
     *
     * @return array Response data.
     */
    // phpcs:ignore
    private function _getPtypeInfo(string $slug)
    {
        $parts         = explode('-in-', strtolower($slug));
        $property_type = str_replace('-', ' ', $parts[0]);
        $location      = str_replace('-', ' ', $parts[1]);

        $response                 = [];
        $response['is_valid']     = true;
        $response['url_location'] = $location;

        // Determine property type.
        if ($property_type === 'stay') {
            $response['property_type_name'] = '';
            $response['property_type_id']   = '';
        } else {
            $property_type = substr($property_type, 0, -1);
            $property_type = str_replace(' and ', ' & ', $property_type);

            $p_type = PropertyType::where('name', '=', $property_type)->where('active', 1)->first();
            if (empty($p_type) === false) {
                $response['property_type_name'] = $p_type->name;
                $response['property_type_id']   = $p_type->id;
            } else {
                $response['is_valid'] = false;
            }
        }

        return $response;

    }//end _getPtypeInfo()


    /**
     * Get Meta tags
     *
     * @param array   $property_type_input Property type array.
     * @param array   $params              Parameter array.
     * @param string  $slug                Slug.
     * @param integer $page_no             Page number.
     * @param array   $url_info            Url Info.
     *
     * @return array Meta data.
     */
    // phpcs:ignore
    private function _getMetaTagsData(array $property_type_input, array $params, string $slug, int $page_no, array $url_info)
    {
        $meta_info           = [];
        $property_type       = \DB::table('property_type')->where('active', 1)->orderby('rank', 'asc')->orderby('id', 'asc')->get();
            $p_type          = [];
            $p_type_keywords = [];
        foreach ($property_type as $property) {
            $p_type[$property->id] = $property->name;
            $p_type_keywords[$property->id]['keywords'] = $property->description;
        }

            // Seo changes.
            $county_title_row       = \DB::table('ccode_currency_mapping')->where('ccode', '=', $params['country'])->select(['name'])->first();
            $params['country_name'] = (empty($county_title_row) === false) ? $county_title_row->name : '';

            $property_type_title          = '';
            $property_type_title_original = '';
            $property_type_keywords       = '';

        $search_slug = 'stays/'.$slug;

        $p_type_index = '';
        if (is_array($property_type_input) === true && count($property_type_input) > 0) {
            $url_property_type_arr = $property_type_input;
            $p_type_index          = $url_property_type_arr[0];
        } else if (count($property_type_input) > 0) {
            $p_type_index = $property_type_input;
        }

        if (empty($p_type_index) === false) {
            if (isset($p_type[$p_type_index]) === true && $p_type[$p_type_index] === 'Home') {
                $property_type_title_original = 'Home';
                $property_type_title          = 'Homestays';
            } else {
                $property_type_title_original = (isset($p_type[$p_type_index]) === true && strtolower($p_type[$p_type_index]) !== 'other') ? $p_type[$p_type_index] : '';
                $property_type_title          = (isset($p_type[$p_type_index]) === true && strtolower($p_type[$p_type_index]) !== 'other') ? $p_type[$p_type_index].'s' : '';
            }

            $property_type_keywords = (isset($p_type_keywords[$p_type_index]['keywords']) === true && strtolower($p_type_keywords[$p_type_index]['keywords']) !== 'other') ? $p_type_keywords[$p_type_index]['keywords'] : '';
        }

        $query_string = (isset($_SERVER['QUERY_STRING']) === true) ? $_SERVER['QUERY_STRING'] : '';
        parse_str($query_string, $query_string);
        unset($query_string['slug']);
        $query_string = http_build_query($query_string);

        $meta_info['meta_url'] = WEBSITE_URL.'/'.$search_slug.'?'.$query_string;

            $loc_str = [];
        if (empty($params['city']) === false) {
            $loc_str[] = $params['city'];
        }

        if (empty($params['state']) === false) {
            $loc_str[] = $params['state'];
        }

        if (empty($params['country_name']) === false) {
            $loc_str[] = $params['country_name'];
        }

        if (empty($params['city']) === false) {
            $meta_info['title_prefix'] = ucwords($params['city']);
        } else if (empty($params['state']) === false) {
            $meta_info['title_prefix'] = ucwords($params['state']);
        } else if (empty($params['country_name']) === false) {
                $meta_info['title_prefix'] = ucwords($params['country_name']);
        } else {
            $meta_info['title_prefix'] = '';
        }

        if (strtolower($property_type_title) === 'apartments') {
            $property_type_title = 'Service Apartments';
        }

            // Dynamic page title.
        if (empty($meta_info['title_prefix']) === false && empty($property_type_title) === false) {
            // Keywords.
            $keyword_postfix      = '--ptype-- in --loc--, --loc-- --ptype--, Pool --ptype-- --loc--, Private --ptype-- in --loc--, Luxury --ptype-- in --loc--, --ptype-- for Rent in --loc--, --ptype-- in --loc-- for Rent';
            $meta_info['keyword'] = str_replace('--ptype--', $property_type_title, $keyword_postfix);
            $meta_info['keyword'] = str_replace('--loc--', $meta_info['title_prefix'], $meta_info['keyword']);
        } else if (empty($meta_info['title_prefix']) === false) {
            // phpcs:disable
            $keyword_postfix      = '--loc-- Accommodation, --loc-- Stay, --loc-- Rooms, --loc-- Holiday Homes, --loc-- Vacation Rentals, Accommodation in --loc--, Stay in --loc--, Rooms in --loc--, Holiday Homes in --loc--, Vacation Rentals in --loc--';
            $meta_info['keyword'] = str_replace('--loc--', $meta_info['title_prefix'], $keyword_postfix);
        }

            $meta_info['canonical_url'] = WEBSITE_URL.'/'.$search_slug;

            // Canonical url fix.
            // For stays/puducherry/stay-in-puducherry.
            // And stays/stay-in-puducherry.
            // Set stays/stay-in-puducherry as canonical url.
        if (strpos($slug, 'puducherry/') === 0 && strpos($slug, 'in-puducherry') !== false) {
            $meta_info['canonical_url'] = str_replace('puducherry/', '', $meta_info['canonical_url']);
        }

        if (empty($meta_info['title_prefix']) === false && empty($property_type_title) === false) {
            // Keywords.
            $keyword_postfix      = '--ptype-- in --loc--, --loc-- --ptype--, Pool --ptype-- --loc--, Private --ptype-- in --loc--, Luxury --ptype-- in --loc--, --ptype-- for Rent in --loc--, --ptype-- in --loc-- for Rent';
            $meta_info['keyword'] = str_replace('--ptype--', $property_type_title, $keyword_postfix);
            $meta_info['keyword'] = str_replace('--loc--', $meta_info['title_prefix'], $meta_info['keyword']);
        } else if (empty($meta_info['title_prefix']) === false) {
            $keyword_postfix      = '--loc-- Accommodation, --loc-- Stay, --loc-- Rooms, --loc-- Holiday Homes, --loc-- Vacation Rentals, Accommodation in --loc--, Stay in --loc--, Rooms in --loc--, Holiday Homes in --loc--, Vacation Rentals in --loc--';
            $meta_info['keyword'] = str_replace('--loc--', $meta_info['title_prefix'], $keyword_postfix);
        }

        $meta_info['canonical_url'] = WEBSITE_URL.'/'.$search_slug;

        if ($slug === 'apartments-in-goa') {
            $meta_info['canonical_url'] = WEBSITE_URL.'/stays/service-apartments-in-goa';
        }

        $meta                    = self::getSeoPageMeta($params, $url_info);
        $meta_info['meta_title'] = $meta['meta_title'];
        $meta_info['meta_desc']  = $meta['meta_desc'];

        if ($page_no > 1) {
            $meta_info['canonical_url']  .= '?page='.$page_no;
            $meta_info['no_index_follow'] = 1;
        }

        return $meta_info;

    }//end _getMetaTagsData()


    /**
     * Get Meta tags
     *
     * @param array $adrs     Address Info array.
     * @param array $url_info Url Info.
     *
     * @return array Seo meta data.
     */
    public static function getSeoPageMeta(array $adrs, array $url_info)
    {
        $op['meta_title']    = '';
        $op['meta_desc']     = '';
        $property_type_title = '';
        $property_type_desc  = '';
        $location            = (empty($adrs['city']) === false) ? $adrs['city'] : $adrs['state'];
        if (empty($url_info['property_type_id']) === false) {
            $p_type = (int) $url_info['property_type_id'];
            switch ($p_type) {
                case 1:
                    $property_type_title = "Book Service Apartments in {$location} at Best Prices - GuestHouser";
                    $property_type_desc  = "Book a service apartment based on genuine reviews nearby famous attractions in {$location}. Get instant booking confirmation and free luxury amenities.";
                break;

                case 2:
                    $property_type_title = "Book Guest Houses in {$location} at Best Prices - GuestHouser";
                    $property_type_desc  = "Enjoy an authentic trip experience by booking a guest house in {$location}. Get instant discount and booking confirmation with free amenities.";
                break;

                case 3:
                    $property_type_title = "Book B&B Accommodation in {$location} at Best Prices - GuestHouser";
                    $property_type_desc  = "Book best bed & breakfast accommodations in {$location} at affordable prices. Check genuine reviews and images for booking.";
                break;

                case 6:
                    $property_type_title = "Book Best Villas in {$location} at Lowest Prices - GuestHouser";
                    $property_type_desc  = "Get instant booking of beautiful villas with pool in {$location} for families and groups. Enjoy your trip with fully furnished villas near famous attractions.";
                break;

                case 17:
                    $property_type_title = "Book Cottages in {$location} at Best Prices - GuestHouser";
                    $property_type_desc  = "Book holiday cottages with luxury amenities in {$location} at affordable rates. Get instant booking confirmation of your accommodation and enjoy your trip.";
                break;

                case 19:
                    $property_type_title = "Book Holiday Bungalows in {$location} at Best Prices - GuestHouser";
                    $property_type_desc  = "Save money by booking a bungalow instantly for your trip in {$location} at lower rates. Get free amenities like pool, Wi-Fi, etc. with free cancellation facility.";
                break;

                case 19:
                    $property_type_title = "Book Homestays in {$location} at Lowest Prices - GuestHouser";
                    $property_type_desc  = "Book a homestay in {$location} on discounted rates with free amenities like pool, Wi-Fi, etc. Instant booking and free cancellation facility available.";
                break;

                case 23:
                    $property_type_title = "Book Heritage Hotels in {$location} at Best Prices - GuestHouser";
                    $property_type_desc  = "Book a luxurious heritage stay in {$location} with amenities like pool. Instant booking available to enjoy astonishing heritage ambience and interiors.";
                break;

                case 28:
                    $property_type_title = "Book Boutique Stays in {$location} at Best Prices - GuestHouser";
                    $property_type_desc  = "Save money by booking unique boutique stay in {$location} with instant booking confirmation. Enjoy free amenities with fully furnished accommodation.";
                break;

                default:
                    $property_type_title = 'Book '.$url_info['property_type_name']."s in {$location} at Best Prices – GuestHouser";
                    $property_type_desc  = 'Book your '.$url_info['property_type_name']."s in {$location} and get flat discount on your first booking. Enjoy instant booking, free amenities & cancellation facilities";
                break;
            }//end switch
        } else {
            $property_type_title = "Book Holiday Homes & Accommodations in {$location} - GuestHouser";
            $property_type_desc  = " Find perfect short term vacation rental properties at amazing discounted prices with additional luxury amenities benefit in {$location}";
        }//end if

        $op['meta_title'] = $property_type_title;
        $op['meta_desc']  = $property_type_desc;

        return $op;

    }//end getSeoPageMeta()


    /**
     * Get Meta data for search page
     *
     * @param array   $property_type_input Property type inputs.
     * @param array   $params              Parameter Info.
     * @param integer $count               Total Property count.
     *
     * @return array search meta data.
     */
    public static function searchMetaData(array $property_type_input, array $params, int $count)
    {
        $search_slug     = 'search/s';
        $property_type   = \DB::table('property_type')->where('active', 1)->orderby('rank', 'asc')->orderby('id', 'asc')->get();
        $p_type          = [];
        $p_type_keywords = [];
        foreach ($property_type as $property) {
            $p_type[$property->id] = $property->name;
            $p_type_keywords[$property->id]['keywords'] = $property->description;
        }

        // Seo changes.
        $county_title_row       = \DB::table('ccode_currency_mapping')->where('ccode', '=', $params['country'])->select(['name'])->first();
        $params['country_name'] = (empty($county_title_row) === false) ? $county_title_row->name : '';

        $property_type_title          = '';
        $property_type_title_original = '';
        $property_type_keywords       = '';

        // Seo stuff start.
        $p_type_index = '';
        $p_type_name = '';
        if (is_array($property_type_input) === true && count($property_type_input) > 0) {
            $url_property_type_arr = $property_type_input;
            $p_type_index          = $url_property_type_arr[0];
        } else if (empty($property_type_input) === false) {
            $p_type_index = $property_type_input;
        }

        if (empty($p_type_index) === false) {
            if (isset($p_type[$p_type_index]) === true && $p_type[$p_type_index] === 'Home') {
                $property_type_title_original = 'Home';
                $property_type_title          = 'Homestays';
            } else {
                $property_type_title_original = (isset($p_type[$p_type_index]) === true && strtolower($p_type[$p_type_index]) !== 'other') ? $p_type[$p_type_index] : '';
                $property_type_title          = (isset($p_type[$p_type_index]) === true && strtolower($p_type[$p_type_index]) !== 'other') ? $p_type[$p_type_index].'s' : '';
            }

            $property_type_keywords = (isset($p_type_keywords[$p_type_index]['keywords']) === true && strtolower($p_type_keywords[$p_type_index]['keywords']) !== 'other') ? $p_type_keywords[$p_type_index]['keywords'] : '';
        }

        $meta_info['meta_title'] = 'Vacation Rentals | Guesthouser';
        $meta_info['meta_url']   = WEBSITE_URL.'/'.$search_slug.'?'.((isset($_SERVER['QUERY_STRING']) === true) ? $_SERVER['QUERY_STRING'] : '');

        $loc_str = [];
        if (empty($params['city']) === false) {
            $loc_str[] = $params['city'];
        }

        if (empty($params['state']) === false) {
            $loc_str[] = $params['state'];
        }

        if (empty($params['country_name']) === false) {
            $loc_str[] = $params['country_name'];
        }

        if (empty($params['city']) === false) {
            $meta_info['title_prefix'] = ucwords($params['city']);
        } else if (empty($params['state']) === false) {
            $meta_info['title_prefix'] = ucwords($params['state']);
        } else if (empty($params['country_name']) === false) {
            $meta_info['title_prefix'] = ucwords($params['country_name']);
        } else {
            $meta_info['title_prefix'] = '';
        }

        if (strtolower($property_type_title) === 'apartments') {
            $property_type_title = 'Service Apartments';
        }

        // Dynamic page title.
        if (empty($meta_info['title_prefix']) === false && empty($property_type_title) === false) {
            $meta_info['meta_title'] = $property_type_title.' in '.$meta_info['title_prefix'].' | GuestHouser';

            // Keywords.
            $keyword_postfix      = '--ptype-- in --loc--, --loc-- --ptype--, Pool --ptype-- --loc--, Private --ptype-- in --loc--, Luxury --ptype-- in --loc--, --ptype-- for Rent in --loc--, --ptype-- in --loc-- for Rent';
            $meta_info['keyword'] = str_replace('--ptype--', $property_type_title, $keyword_postfix);
            $meta_info['keyword'] = str_replace('--loc--', $meta_info['title_prefix'], $meta_info['keyword']);
        } else if (empty($meta_info['title_prefix']) === false) {
            $meta_info['meta_title'] = 'Accommodations & Stays in --loc-- | --loc-- Vacation Rentals | Guesthouser';
            $meta_info['meta_title'] = str_replace('--loc--', $meta_info['title_prefix'], $meta_info['meta_title']);

            $keyword_postfix      = '--loc-- Accommodation, --loc-- Stay, --loc-- Rooms, --loc-- Holiday Homes, --loc-- Vacation Rentals, Accommodation in --loc--, Stay in --loc--, Rooms in --loc--, Holiday Homes in --loc--, Vacation Rentals in --loc--';
            $meta_info['keyword'] = str_replace('--loc--', $meta_info['title_prefix'], $keyword_postfix);
        }

        $city = (empty($params['city']) === false) ? $params['city'] : $params['state'];

        $city_result = strtolower(
            str_replace(
                [
                    ' ',
                    '&',
                ],
                [
                    '-',
                    'and',
                ],
                $city
            )
        );

        $meta_info['canonical_url'] = '';
        if (empty($p_type_index) === false && isset($p_type[$p_type_index]) === true) {
            $meta_info['canonical_url'] = WEBSITE_URL.'/'.$search_slug.'?location='.(implode(',', $loc_str)).'&property_type='.$p_type_index;
            $p_type_name                = strtolower(
                str_replace(
                    [
                        ' ',
                        '&',
                    ],
                    [
                        '-',
                        'and',
                    ],
                    $p_type[$p_type_index]
                )
            );
        } else {
            $meta_info['canonical_url'] = WEBSITE_URL.'/'.$search_slug.'?location='.(implode(',', $loc_str));
        }

        if (empty($params['state']) === true && empty($params['city']) === true) {
            if (empty($p_type_index) === false && isset($p_type[$p_type_index]) === true) {
                $meta_info['canonical_url'] = ($params['state'] === 'Goa') ? WEBSITE_URL.'/'.$p_type_name.'s-in-'.$city_result : WEBSITE_URL.'/'.$search_slug.'?location='.(implode(',', $loc_str));
            } else {
                $meta_info['canonical_url'] = ($params['state'] === 'Goa') ? WEBSITE_URL.'/stay-in-'.$city_result : WEBSITE_URL.'/'.$search_slug.'?location='.(implode(',', $loc_str));
            }
        }

        // Dynamic meta description.
        if (empty($meta_info['title_prefix']) === false && empty($property_type_title) === false) {
            $meta_info['meta_desc'] = "Book {$count} ".$property_type_title.' in '.$meta_info['title_prefix'].' on GuestHouser';
        } else {
            $meta_info['meta_desc'] = 'Book vacation rentals, villas, service apartments, accommodations, homestays, cottages, bungalows in '.$meta_info['title_prefix'].' on GuestHouser';
        }

        // Seo stuff end.
        return $meta_info;

    }//end searchMetaData()


}//end class
