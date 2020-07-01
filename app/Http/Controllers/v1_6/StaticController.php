<?php
/**
 * Static Controller containing methods to get static data from server.
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\{Request, Response};
use Elasticsearch\ClientBuilder as EsClientBuilder;
use App\Models\CountryCodeMapping;
use App\Libraries\{ApiResponse, Helper};
use App\Libraries\ApiResponseModelsGenerator;
use App\Models\{BookingRequest,AppExplore , PropertyType, PropertyImage};

use App\Http\Response\v1_6\Models\{GetDialcodesResponse, GetCountrycodesResponse , GetCommonDataResponse, GetNotificationsResponse};
use App\Http\Response\v1_6\Models\GetCountryDetailsResponse;
use App\Http\Requests\{GetCommonDataRequest, GetNotificationRequest};
use Carbon\Carbon;
/**
 * Class StaticController
 */
class StaticController extends Controller
{


    /**
     * Get an associative array of countries and their dial codes
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\GET(
     *     path="/v1.6/dialcodes",
     *     tags={"Static Data"},
     *     description="Returns an array containing countries and their respective dialcodes.",
     *     operationId="user.get.dialcodes",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="List of dialcodes of countries.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetDialcodesResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * )
     */
    public function getDialCodes()
    {
        $response = new GetDialcodesResponse(['dial_codes' => CountryCodeMapping::getDialCodes()]);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getDialCodes()


    /**
     * Get Status Code (Temp Api).
     *
     * @param integer $status_code Status Code.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatuscode(int $status_code)
    {
        return ApiResponse::error(
            [
                [
                    'code'    => 'status_code',
                    'key'     => 'status_code',
                    'message' => $status_code,
                ],
            ],
            $status_code
        );

    }//end getStatuscode()


    /**
     * Get an associative array of countries and their country codes
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\GET(
     *     path="/v1.6/countrycodes",
     *     tags={"Static Data"},
     *     description="Returns an array containing countries and their respective country codes.",
     *     operationId="user.get.countrycodes",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="List of country codes of countries.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetCountrycodesResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * )
     */
    public function getCountryCodes()
    {
        $response = new GetCountrycodesResponse(['country_codes' => CountryCodeMapping::getCountryCodes()]);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getCountryCodes()


    /**
     * Generate Api respinse mapping file
     *
     * @param \Illuminate\Http\Request $request Http request object.
     * Post params      response_json   Json of api response.
     * Post params      class_name      Class name of Model Ex. GetPropertyResponse.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postGenerateApiResponseMappings(Request $request)
    {
        // Validate params.
        $params_valid = $this->areParamsValid(
            $request,
            [
                'response_json' => 'required',
                'class_name'    => 'required',
                'response_type' => 'in:http_response,data',
                'version'       => 'required',
                'type'          => 'in:partial',
            // Api Version like v1.6 or v1.7 etc.
            ]
        );

        // Send failed response if validation fails.
        if ($params_valid !== true) {
            return ApiResponse::validationFailed($params_valid);
        }

        $response_json = $request->post('response_json');
        $class_name    = $request->post('class_name');
        $response_type = $request->post('response_type');
        $version       = $request->post('version');
        $type          = $request->post('type', '');

        $decode_data = json_decode($response_json);

        if (empty($decode_data) === true) {
            return ApiResponse::validationFailed(['response_json' => 'Not Valid Json.']);
        }

        if (isset($decode_data->data) === false && $response_type !== 'http_response') {
            return ApiResponse::errorMessage('data key not found in json');
        }

        if ($response_type === 'http_response') {
            $decode_data = $decode_data;
        } else {
            $decode_data = $decode_data->data;
        }

        $response = ApiResponseModelsGenerator::saveMappingsFromResponse($class_name, $decode_data, $version, $type);

        return ApiResponse::successMessage($response);

    }//end postGenerateApiResponseMappings()


    /**
     * Generate Api respinse file
     *
     * @param \Illuminate\Http\Request $request Http request object.
     * Post params      class_name      Class name of Model Ex. GetPropertyResponse.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postGenerateApiResponseModels(Request $request)
    {
        // Validate params.
        $params_valid = $this->areParamsValid(
            $request,
            [
                'class_name' => 'required',
                'version'    => 'required',
                'type'       => 'in:partial',
            ]
        );

        // Send failed response if validation fails.
        if ($params_valid !== true) {
            return ApiResponse::validationFailed($params_valid);
        }

        $class_name = $request->post('class_name');
        $version    = $request->post('version');
        $type       = $request->post('type', '');
        // phpcs:disable
        // For Generating all models.
        // $files = glob(base_path().'/app/Http/Response/v1_6/Mappings/*.json');
        // foreach ($files as $value) {
        // $class_name = str_replace(".json", '', array_values(array_slice(explode("/", $value), -1))[0]);
        // $response = ApiResponseModelsGenerator::getModelsFromMappings($class_name);
        // }
        // phpcs:enable
        $response = ApiResponseModelsGenerator::getModelsFromMappings($class_name, $version, $type);

        return ApiResponse::successMessage($response);

    }//end postGenerateApiResponseModels()


    /**
     * Generate Api respinse mapping file for existing mappings
     *
     * @param \Illuminate\Http\Request $request Http request object.
     * Post params      response_json   Json of api response.
     * Post params      class_name      Class name of Model Ex. GetPropertyResponse.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postGenerateApiResponseMappingsForExistingMappings(Request $request)
    {
        // Validate params.
        $params_valid = $this->areParamsValid(
            $request,
            [
                'response_json' => 'required',
                'class_name'    => 'required',
                'version'       => 'required',
                'response_type' => 'in:http_response,data',
                'type'          => 'in:partial',
            ]
        );

        // Send failed response if validation fails.
        if ($params_valid !== true) {
            return ApiResponse::validationFailed($params_valid);
        }

        $response_json = $request->post('response_json');
        $class_name    = $request->post('class_name');
        $version       = $request->post('version');
        $response_type = $request->post('response_type');
        $type          = $request->post('type', '');

        $decode_data = json_decode($response_json);

        if (empty($decode_data) === true) {
            return ApiResponse::validationFailed(['response_json' => 'Not Valid Json.']);
        }

        if (isset($decode_data->data) === false && $response_type !== 'http_response') {
            return ApiResponse::errorMessage('data key not found in json');
        }

        if ($response_type === 'http_response') {
            $decode_data = $decode_data;
        } else {
            $decode_data = $decode_data->data;
        }

        $response = ApiResponseModelsGenerator::getMappingsFromExistingMappings($class_name, $decode_data, $version, $type);

        return ApiResponse::successMessage($response);

    }//end postGenerateApiResponseMappingsForExistingMappings()


    /**
     * Get Common data
     *
     * @param \App\Http\Requests\GetCommonDataRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\GET(
     *     path="/v1.6/common-data",
     *     tags={"Static Data"},
     *     description="Returns an array containing common data.",
     *     operationId="user.get.commondata",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/device_type_in_header"),
     * @SWG\Parameter(ref="#/parameters/app_version_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="List of common data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetCommonDataResponse"),
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
    public function getCommonData(GetCommonDataRequest $request)
    {
        // Check app version.
        $app_version_check = $request->getAppExpiryCheck();

        $content['app_version_check'] = $app_version_check;
        $content['new_rating_days']   = NEW_RATING_DAYS;
        $content['old_rating_days']   = OLD_RATING_DAYS;

        $chat_available = 0;
        $current_time   = Carbon::now('Asia/Calcutta')->format('H.i');
        if ($current_time >= CHAT_TIME_MORN && $current_time < CHAT_TIME_EVE) {
            $chat_available = 1;
        }

        $content['chat_available'] = $chat_available;
        // Is chat available now.
        $content['chat_call_text'] = CHAT_CALL_TEXT;

        $content['chat']['from_time'] = Carbon::parse(CHAT_TIME_MORN)->format('H:i:s');
        $content['chat']['to_time']   = Carbon::parse(CHAT_TIME_EVE)->format('H:i:s');

         $device_type = $request->getDeviceSource();
        if ($device_type === 'ios') {
            $content['spotlight_list'] = AppExplore::getSpotLightList();
        }

        $content['gh_contact']    = GH_CONTACT_NUMBER;
        $content['property_type'] = PropertyType::getAllPropertyTypes();

        $content['autocomplete_api'] = AUTOCOMPLETE_APIS[DEFAULT_AUTOCOMPLETE_API];
        $response                    = new GetCommonDataResponse($content);
        $response                    = $response->toArray();

        return ApiResponse::success($response);

    }//end getCommonData()


    /**
     * Get Notification data
     *
     * @param \App\Http\Requests\GetNotificationRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\GET(
     *     path="/v1.6/notifications",
     *     tags={"Static Data"},
     *     description="Returns an array containing notifications.",
     *     operationId="user.get.notifications",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="List of notification data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetNotificationsResponse"),
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
    public function getNotificationData(GetNotificationRequest $request)
    { 
        $user = $request->getLoggedInUser();

        // Get All Headers Data.
        $headers = $request->getAllHeaders();

        $guest_active_request_count = BookingRequest::getNewAndApprovedRequestsCount($user->id);
        $guest_upcoming_trip_count  = BookingRequest::getTripCountsByUserId($user->id)['upcoming'];

        // Get New Request Count.
        $host_new_request_count = BookingRequest::getNewRequestsCountForHost($user->id);

        $upcoming_checkin_count = BookingRequest::getUpcomingCheckinCountTraveller($user->id);

        // New Booking Awaiting Confirmation Count.
        $new_booking_awaiting_confirmation = BookingRequest::getNewBookedAwaitingConfirmationOfHost($user->id);

        $request_approval_data = BookingRequest::getBookingRequestNotifications($user->id);

        $request_approval_popup_data = [];

        if (empty($request_approval_data) === false) {
            if ($request_approval_data['booking_status'] === EXPIRED) {
                $today              = Carbon::now('GMT')->toDateTimeString();
                $time_since_booking = (12 * 60 * 60 - (strtotime($today) - strtotime($request_approval_data['updated_at'])));
                $resend_in          = ($time_since_booking > 0 && $request_approval_data['from_date'] >= $today) ? 1 : 0;
            }

            if (in_array($request_approval_data['booking_status'], [REQUEST_APPROVED, REQUEST_REJECTED]) === true || ($request_approval_data['booking_status'] === EXPIRED && $resend_in > 0)) {
                // Get first property image to display.
                $properties_images = PropertyImage::getPropertiesImagesByIds([$request_approval_data['property_id']], $headers, 1);

                $country_codes = CountryCodeMapping::getCountries();

                $checkin_date           = $request_approval_data['from_date'];
                $checkin_date_obj       = Carbon::parse($checkin_date);
                $checkin_date_formatted = $checkin_date_obj->format('d M Y');

                $checkout_date           = $request_approval_data['to_date'];
                $checkout_date_obj       = Carbon::parse($checkout_date);
                $checkout_date_formatted = $checkout_date_obj->format('d M Y');

                $request_approval_popup_data[] = [
                    'request_hash_id'    => Helper::encodeBookingRequestId($request_approval_data['id']),
                    'property_id'        => $request_approval_data['property_id'],
                    'property_hash_id'   => Helper::encodePropertyId($request_approval_data['property_id']),
                    'property_image'     => (array_key_exists($request_approval_data['property_id'], $properties_images) === true) ? $properties_images[$request_approval_data['property_id']] : [],
                    'property_title'     => ucfirst($request_approval_data['title']),
                    'location'           => [
                        'area'          => ucfirst($request_approval_data['area']),
                        'city'          => ucfirst($request_approval_data['city']),
                        'state'         => ucfirst($request_approval_data['state']),
                        // Country name from code.
                        'country'       => $country_codes[$request_approval_data['country']],
                        'location_name' => Helper::formatLocation($request_approval_data['area'], $request_approval_data['city'], $request_approval_data['state']),
                    ],
                    'units'              => $request_approval_data['units'],
                    'guests'             => $request_approval_data['guests'],
                    'checkin_formatted'  => $checkin_date_formatted,
                    'checkout_formatted' => $checkout_date_formatted,
                    'booking_status'     => $request_approval_data['booking_status'],
                    'checkin'            => $checkin_date_obj->format('Y-m-d'),
                    'checkout'           => $checkout_date_obj->format('Y-m-d'),
                ];
            }//end if
        }//end if

        $response = [
            'traveller' => [
                'active_request_count' => $guest_active_request_count,
                'upcoming_trip_count'  => $guest_upcoming_trip_count,
                'status_changed'       => $request_approval_popup_data,
            ],
            'host'      => [
                'new_request_count'           => $host_new_request_count,
                'upcoming_booking_count'      => $upcoming_checkin_count,
                'awaiting_confirmation_count' => $new_booking_awaiting_confirmation,
            ],
        ];

        $response = new GetNotificationsResponse($response);
        $response = $response->toArray();

        return ApiResponse::success($response);

    }//end getNotificationData()


    // phpcs:ignore
    public static function getLocationSuggestions(Request $request)
    {
        $index              = config('gh.es_location_autocomplete.location_index');
        $autocomplete_field = config('gh.es_location_autocomplete.autocomplete_field');

        $response = [
            'options' => [],
        ];
        $query    = $request->input('q');
        $length   = $request->input('l');

        $hosts = [
            [
                'host'   => config('gh.es_location_autocomplete.server.host'),
                'port'   => config('gh.es_location_autocomplete.server.port'),
                'scheme' => config('gh.es_location_autocomplete.server.scheme'),
            ],
        ];

        try {
            $client = EsClientBuilder::create()->setHosts($hosts)->setRetries(0)->build();
            // phpcs:ignore
            $searchParams = [
                'index' => $index,
                'body'  => [
                    'suggest' => [
                        'address-suggest' => [
                            'prefix'     => $query,
                            'completion' => [
                                'field' => $autocomplete_field,
                                'size'  => $length,
                            ],
                        ],
                    ],
                ],
            ];
            // phpcs:ignore
            $search = $client->search($searchParams);
            if (empty($search['suggest']) === false && empty($search['suggest']['address-suggest']) === false && empty($search['suggest']['address-suggest'][0]['options']) === false) {
                $options = $search['suggest']['address-suggest'][0]['options'];
                foreach ($options as $key => $option) {
                      $source                = $option['_source'];
                      $response['options'][] = [
                          'text'     => $option['text'],
                          'city'     => $source['city'],
                          'state'    => $source['state'],
                          'country'  => 'IN',
                          'lat'      => '',
                          'lng'      => '',
                          'area'     => $source['area'],
                          'location' => $option['text'],
                      ];
                }
            }
        } catch (\Exception $e) {
            \Log::Error('Error in fetching from elasticsearch and error is '.$e->getMessage()." and query was $query");
            $response['error'] = $e->getMessage();
        }//end try

        return ApiResponse::success($response);

    }//end getLocationSuggestions()


    /**
     * Get an associative array of countries details and their dial codes
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\GET(
     *     path="/v1.6/countrydetails",
     *     tags={"Static Data"},
     *     description="Returns an array containing countries details and their respective dialcodes.",
     *     operationId="user.get.countrydetails",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Detail List of countries.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetCountryDetailsResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * )
     * @SWG\GET(
     *     path="/v1.7/countrydetails",
     *     tags={"Static Data"},
     *     description="Returns an array containing countries details and their respective dialcodes.",
     *     operationId="user.get.countrydetails",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Detail List of countries.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetCountryDetailsResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * )
     */
    public function getCountryDetails()
    {
        $response = new GetCountryDetailsResponse(['country_details' => CountryCodeMapping::getCountryDetails()]);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getCountryDetails()


}//end class
