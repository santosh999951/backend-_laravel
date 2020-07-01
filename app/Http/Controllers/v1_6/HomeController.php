<?php
/**
 * Home Controller containing methods related to home page
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use \Auth;

use App\Models\BookingRequest;
use App\Models\Collection;
use App\Models\CountryCodeMapping;
use App\Models\CurrencyConversion;
use App\Models\User;
use App\Libraries\{ApiResponse};
use App\Libraries\v1_6\{CollectionService, HomeService, RecentlyViewedService};

use App\Http\Response\v1_6\Models\{GetHomeResponse, GetHomeCollectionsDetailResponse, GetHomeCollectionsResponse};
use App\Libraries\v1_6\OfferService;
use App\Http\Requests\{GetHomeRequest,GetCollectionRequest,GetCollectionDetailRequest};

/**
 * Class HomeController
 */
class HomeController extends Controller
{


    /**
     * Get home page data
     *
     * @param \App\Http\Requests\GetHomeRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/home",
     *     tags={"Home"},
     *     description="Get home page data",
     *     operationId="home.get.index",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/collections_offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/collections_total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/collections_property_total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/offer_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Home page data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                                 ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                                   ref="#definitions/GetHomeResponse"),
     * @SWG\Property(property="error",                                                  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )

     *     ),
     * )
     */
    public function getIndex(GetHomeRequest $request)
    {
        // Collect params.
        $start_date = date('Y-m-d', (time() + BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS));
        $end_date   = date('Y-m-d', (time() + BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS));
         // Recently searched properties params.
        $days   = round(abs(strtotime($end_date) - strtotime($start_date)) / 86400);
        $guests = DEFAULT_NUMBER_OF_GUESTS;
         // Set currency.
        $currency = DEFAULT_CURRENCY;
        $bedroom  = 0;
        // Get All Input Param.
        $input_params = $request->input();

        $collections_offset         = $input_params['collections_offset'];
        $collections_total          = $input_params['collections_total'];
        $collections_property_total = $input_params['collections_property_total'];
        $offer = $input_params['offer'];

        // Response data content.
        $content = [
            'user'                                    => [],
            'popular_cities'                          => [],
            'property_types'                          => [],
            'home_videos'                             => [],
            'collections'                             => [],
            'recently_viewed_properties'              => [],
            'new_and_approved_booking_requests_count' => 0,
            'home_explore_content'                    => [],
        ];

        // Get all headers.
        $headers = $request->getAllHeaders();

        // Check app version.
        $device_type       = $request->getDeviceType();
        $app_version_check = $request->getAppExpiryCheck();

        $content['app_version_check'] = $app_version_check;
        $content['new_rating_days']   = NEW_RATING_DAYS;
        $content['old_rating_days']   = OLD_RATING_DAYS;

        // Check if user logged in.
        $is_user_logged_in = $this->isUserLoggedIn();
        $user_id           = 0;
        if ($is_user_logged_in === true) {
            // Get user id from access token.
            $user    = $this->getAuthUser();
            $user_id = $user->id;

            // Set wallet data.
            $content['user']           = [];
            $content['user']['wallet'] = [
                'wallet_balance'         => $user->wallet_balance,
                'wallet_currency_symbol' => CURRENCY_SYMBOLS[$user->wallet_currency],
            ];

            // Set user currency.
            $currency = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        }//end if

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        // Get mobile new home banners.
        $content['home_banner'] = HomeService::getHomeBanner(
            [
                'country_codes' => $country_codes,
                'currency'      => $currency,
                'device_type'   => $device_type,
            ]
        );

        // ---- Home widgets and cities ----
        $home_widgets_and_cities_data = HomeService::getHomeWidgetsAndCities(
            [
                'country_codes' => $country_codes,
                'device_type'   => $device_type,
            ]
        );

        // Home widgets and cities add to response.
        $content['popular_cities'] = $home_widgets_and_cities_data['organized_city_data'];
        $content['property_types'] = $home_widgets_and_cities_data['organized_home_widgets'];

        /*
            ---- Video data ----
        */

        // Video array.
        $content['home_videos'] = HomeService::getHomeVideos();

        /*
            ---- Collection data ----
        */

        // Get collection data.
        $organized_collection_data = CollectionService::getCollectionData(
            [
                'is_user_logged_in' => $is_user_logged_in,
                'user_id'           => $user_id,
                'currency'          => $currency,
                'offset'            => $collections_offset,
                'total'             => $collections_total,
                'property_total'    => $collections_property_total,
                'headers'           => $headers,
                'start_date'        => $start_date,
                'end_date'          => $end_date,
            ]
        );

        // Set collections.
        $content['collections'] = $organized_collection_data;

        $chat_available = 0;
        $current_time   = Carbon::now('Asia/Calcutta')->format('H.i');
        if ($current_time >= CHAT_TIME_MORN && $current_time < CHAT_TIME_EVE) {
            $chat_available = 1;
        }

        $content['chat_available'] = $chat_available;
        // Is chat available now.
        $content['chat_call_text'] = CHAT_CALL_TEXT;

        // If user logged in.
        if ($is_user_logged_in === true) {
            // Get new request and approved requests count.
            $content['active_request_count'] = BookingRequest::getNewAndApprovedRequestsCount($user_id);
            // Get recently viewed properties.
            $combined_recently_viewed_properties = RecentlyViewedService::getRecentlyViewedProperties(
                [
                    'user_id'       => $user_id,
                    'start_date'    => $start_date,
                    'end_date'      => $end_date,
                    'days'          => $days,
                    'guests'        => $guests,
                    'currency'      => $currency,
                    'bedroom'       => $bedroom,
                    'country_codes' => $country_codes,
                    'headers'       => $headers,
                    'limit'         => ($device_type === 'website') ? 6 : 5,
                    'offset'        => 0,
                ]
            );

            // Recently viewed properties.
            $content['recently_viewed_properties'] = array_values($combined_recently_viewed_properties);
        }//end if

        $content['offer'] = self::getOfferContent($device_type, $offer);

        $meta_info['canonical_url'] = WEBSITE_URL;
        $meta_info['meta_title']    = HOME_PAGE_TITLE;
        $meta_info['keyword']       = HOME_PAGE_KEYWORD;
        $meta_info['meta_desc']     = HOME_PAGE_DESC;

        $content['meta'] = $meta_info;

        $content['home_explore_content'] = [
            'heading'            => ($device_type === 'website') ? 'India’s largest network of holiday homes' : 'Travel the local way!',
            'cities_sub_heading' => ($device_type === 'website') ? TOTAL_PROPERTIES_COUNT.' holiday homes in '.TOTAL_CITIES_COUNT.' destinations' : 'Choose from '.TOTAL_PROPERTIES_COUNT.'+ vacation rentals in '.TOTAL_CITIES_COUNT.'+ cities.',
        ];

        $response = new GetHomeResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getIndex()


    /**
     * Get offer content
     *
     * @param string $device_type Device Type.
     * @param string $offer_name  Offer name.
     *
     * @return array Offer details.
     */
    public static function getOfferContent(string $device_type, string $offer_name='default')
    {
        $offers = OfferService::getOfferByName($offer_name);

        if (empty($offers) === true) {
            return [];
        }

        $m_base_url       = CDN_URL.S3_OFFER_DIR_MOBILE;
        $website_base_url = CDN_URL.S3_OFFER_DIR_WEB;
        $offer['title']   = $offers[0]['title'];
        $offer['desc']    = json_decode($offers[0]['description'], true);

        $offer['img_url'] = ($device_type === 'website') ? $website_base_url.$offers[0]['offer_image'][0]['web_image'] : $m_base_url.$offers[0]['offer_image'][0]['mobile_image'];

        if (empty($offers[0]['destination']) === false) {
            $offer['destination'] = json_decode($offers[0]['destination'], true);
        }

        $meta_info['canonical_url'] = WEBSITE_URL.'/offers';
        $meta_info['meta_title']    = HOME_PAGE_TITLE;
        $meta_info['keyword']       = HOME_PAGE_KEYWORD;
        $meta_info['meta_desc']     = HOME_PAGE_DESC;

        $offer['meta'] = $meta_info;

        return $offer;

    }//end getOfferContent()


    /**
     * Get all collections data
     *
     * @param \App\Http\Requests\GetCollectionRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/home/collections",
     *     tags={"Home"},
     *     description="Get all collections data",
     *     operationId="home.get.collections",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/property_total_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="All collections data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                     ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                       ref="#definitions/GetHomeCollectionsResponse"),
     * @SWG\Property(property="error",                                      ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * )
     */
    public function getCollections(GetCollectionRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();
        // Get Device Type.
        $device_type = $this->getDeviceType($request);

        // Collect params.
        $offset         = $input_params['offset'];
        $total          = $input_params['total'];
        $property_total = $input_params['property_total'];

        // Response data content.
        $content = [
            'collections' => [],
        ];

        // Set currency.
        $currency = DEFAULT_CURRENCY;

        // Get all headers.
        $headers = $request->getAllHeaders();

        // User id.
        $is_user_logged_in = $this->isUserLoggedIn();
        $user_id           = 0;
        if ($is_user_logged_in === true) {
            // Get user id from access token.
            $user    = $this->getAuthUser();
            $user_id = $user->id;

            // Set user currency.
            $currency = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        }

        // Get collection data.
        $organized_collection_data = CollectionService::getCollectionData(
            [
                'is_user_logged_in' => $is_user_logged_in,
                'user_id'           => $user_id,
                'currency'          => $currency,
                'offset'            => $offset,
                'total'             => $total,
                'property_total'    => $property_total,
                'headers'           => $headers,
                'start_date'        => date('Y-m-d', (time() + BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS)),
                'end_date'          => date('Y-m-d', (time() + BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS)),
            ]
        );

        // Set fetched collection data.
        $content['collections'] = $organized_collection_data;

        $meta_info['canonical_url'] = WEBSITE_URL.'/collections';
        $meta_info['meta_title']    = COMMON_META_TITLE;
        $meta_info['keyword']       = '';
        $meta_info['meta_desc']     = COMMON_META_DESC;

        $content['home_explore_content'] = [
            'heading'            => ($device_type === 'website') ? 'India’s largest network of holiday homes' : 'Travel the local way!',
            'cities_sub_heading' => ($device_type === 'website') ? TOTAL_PROPERTIES_COUNT.' holiday homes in '.TOTAL_CITIES_COUNT.' destinations' : 'Choose from '.TOTAL_PROPERTIES_COUNT.'+ vacation rentals in '.TOTAL_CITIES_COUNT.'+ cities.',
        ];
        $content['meta']                 = $meta_info;

        $response = new GetHomeCollectionsResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getCollections()


    /**
     * Get particular collection data
     *
     * @param \App\Http\Requests\GetCollectionDetailRequest $request            Http request object.
     * @param string                                        $collection_hash_id Collection hash id.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/home/collections/{collection_hash_id}",
     *     tags={"Home"},
     *     description="Get particular collection data",
     *     operationId="home.get.particular.collection",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/collection_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Get particular collection data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetHomeCollectionsDetailResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="No details available.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getParticularCollection(GetCollectionDetailRequest $request, string $collection_hash_id)
    {
        // Get All Input Param.
        $input_params = $request->input();

        // Collect params.
        $offset = $input_params['offset'];
        $total  = $input_params['total'];

        // Decode hash id.
        $collection_id = $request->decodeCollectionIdOrFail($collection_hash_id);

        // Does collection exist corresponding to collection id.
        if (empty(Collection::doesCollectionExist($collection_id)) === true) {
            // Collection doesn't exist.
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        // Response data content.
        $content = [
            'collection' => [],
        ];

        // Set currency.
        $currency = DEFAULT_CURRENCY;

        // Get all headers.
        $headers = $request->getAllHeaders();

        // Get Device Type.
        $device_type = $request->getDeviceType();

        // User id.
        $is_user_logged_in = $this->isUserLoggedIn();
        $user_id           = 0;
        if ($is_user_logged_in === true) {
            // Get user id from access token.
            $user    = $this->getAuthUser();
            $user_id = $user->id;

            // Set user currency.
            $currency = (empty($user->base_currency) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;
        }

        // Get collection data.
        $organized_collection_data = CollectionService::getCollectionData(
            [
                'is_user_logged_in' => $is_user_logged_in,
                'user_id'           => $user_id,
                'currency'          => $currency,
                'collection_id'     => $collection_id,
                'offset'            => 0,
                'total'             => 1,
                'property_offset'   => $offset,
                'property_total'    => $total,
                'headers'           => $headers,
                'start_date'        => date('Y-m-d', (time() + BOOKING_DEFAULT_START_DAYS_FROM_TODAY_IN_SECONDS)),
                'end_date'          => date('Y-m-d', (time() + BOOKING_DEFAULT_END_DAYS_FROM_TODAY_IN_SECONDS)),
                'is_particular'     => 1,
                // Image Count 0 defines that send all images and other defines their count.
                'images_count'      => ($device_type === 'website') ? 0 : 1,
            ]
        );

        // Set fetched collection data.
        $content['collection'] = (count($organized_collection_data) !== 0) ? $organized_collection_data[0] : [];

        $meta_info['canonical_url'] = (count($organized_collection_data) !== 0) ? WEBSITE_URL.'/collection/'.$organized_collection_data[0]['collection_hash_id'] : '';
        $meta_info['meta_title']    = (count($organized_collection_data) !== 0) ? $organized_collection_data[0]['collection_title'] : COMMON_META_TITLE;
        $meta_info['keyword']       = '';
        $meta_info['meta_desc']     = COMMON_META_DESC;

        $content['meta'] = $meta_info;

        $response = new GetHomeCollectionsDetailResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getParticularCollection()


}//end class
