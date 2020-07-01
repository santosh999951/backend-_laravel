<?php
/**
 * Device Controller containing method to register device id
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;

use App\Models\{MobileAppDevice, TrafficSource};
use App\Libraries\ApiResponse;
use App\Libraries\Helper;
use App\Http\Response\v1_6\Models\{PostDeviceResponse, PostDeviceTrafficsourceResponse};
use App\Http\Requests\{PostDeviceRegisterRequest,PostTrafficSourceRequest};

/**
 * Class DashboardController
 */
class DeviceController extends Controller
{


    /**
     * Create a new device.
     *
     * @param \App\Http\Requests\PostDeviceRegisterRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/device",
     *     tags={"Device"},
     *     description="Registers a new device",
     *     operationId="device.post.register",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/device_notification_token_in_form"),
     * @SWG\Parameter(ref="#/parameters/app_version_in_form"),
     * @SWG\Parameter(ref="#/parameters/device_model_in_form"),
     * @SWG\Parameter(ref="#/parameters/device_make_in_form"),
     * @SWG\Parameter(ref="#/parameters/brand_in_form"),
     * @SWG\Parameter(ref="#/parameters/os_version_in_form"),
     * @SWG\Parameter(ref="#/parameters/resolution_in_form"),
     * @SWG\Parameter(ref="#/parameters/country_in_form"),
     * @SWG\Parameter(ref="#/parameters/screen_width_in_form"),
     * @SWG\Parameter(ref="#/parameters/screen_height_in_form"),
     * @SWG\Parameter(ref="#/parameters/ram_in_form"),
     * @SWG\Parameter(ref="#/parameters/dpi_in_form"),
     * @SWG\Parameter(ref="#/parameters/app_version_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/fcm_token_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Device successfully updated",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                      ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                        ref="#definitions/PostDeviceResponse"),
     * @SWG\Property(property="error",                                       ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="Device successfully registered",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function postRegister(PostDeviceRegisterRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        // Get Logged In user id.
        $logged_user_id = $request->getLoginUserId();

        // Collect params.
        $device_unique_id          = $input_params['device_unique_id'];
        $device_notification_token = $input_params['device_notification_token'];
        $app_version               = $input_params['app_version'];
        $device_model              = $input_params['device_model'];
        $device_make               = $input_params['device_make'];
        $brand            = $input_params['brand'];
        $os_version       = $input_params['os_version'];
        $resolution       = $input_params['resolution'];
        $country          = $input_params['country'];
        $screen_width     = $input_params['screen_width'];
        $screen_height    = $input_params['screen_height'];
        $ram              = $input_params['ram'];
        $dpi              = $input_params['dpi'];
        $app_version_code = $input_params['app_version_code'];
        $fcm_token        = $input_params['fcm_token'];

        // Ip address.
        $ip_address = Helper::getUserIpAddress();

        // User agent.
        $agent   = new Agent();
        $browser = $agent->browser();
        // Chrome, IE, Safari, Firefox.
        $browser_version = $agent->version($browser);
        $device          = $agent->device();
        // Macintosh, iPhone, Nexus, AsusTablet.
        $platform = $agent->platform();
        // Ubuntu, Windows, OS X.
        $platform_version = $agent->version($platform);

        // Fetch row with same device unique id.
        $row    = MobileAppDevice::getDeviceByDeviceUniqueId($device_unique_id);
        $exists = 1;

        // If device unique id is not in system then create one.
        if ($row === null) {
            $row    = new MobileAppDevice();
            $exists = 0;
        } else if (empty($logged_user_id) === true && $row->user_id > 0) {
            $row->last_user_id = $row->user_id;
            $row->user_id      = 0;
        }

        if (empty($request->headers->get('device-type')) === false && in_array($request->headers->get('device-type'), ['android', 'ios']) === true) {
            $device_type = ($request->headers->get('device-type') === 'ios') ? 'iPhone' : $request->headers->get('device-type');
        } else {
            $device_type = $device;
        }

        // Update device data.
        $row->device_unique_id = $device_unique_id;
        $row->device_id        = $device_notification_token;
        $row->app_version      = $app_version;
        $row->device_type      = $device_type;
        $row->device_model     = $device_model;
        $row->device_make      = $device_make;
        $row->brand            = $brand;
        $row->os_version       = $os_version;
        $row->resolution       = $resolution;
        $row->country          = $country;
        $row->screen_width     = $screen_width;
        $row->screen_height    = $screen_height;
        $row->ram              = $ram;
        $row->dpi              = $dpi;
        $row->app_version_code = $app_version_code;

        // New params.
        $row->ip_address       = $ip_address;
        $row->browser          = $browser;
        $row->browser_version  = $browser_version;
        $row->platform         = $platform;
        $row->platform_version = $platform_version;
        $row->fcm_token        = $fcm_token;

        // Save status 1.
        $row->status = 1;

        $row->last_login  = Carbon::now()->toDateTimeString();
        $row->last_active = Carbon::now()->toDateTimeString();
        $row->save();

        if ($exists === 1) {
            // Device updated.
            $response = [
                'basic_info' => [
                    'properties_count' => TOTAL_PROPERTIES_COUNT.'+',
                    'cities_count'     => TOTAL_CITIES_COUNT.'+',
                ],
                'message'    => 'Device successfully updated.',
            ];

            $response = new PostDeviceResponse($response);
            $response = $response->toArray();
            return ApiResponse::success($response);
        }

        $response = [
            'basic_info' => [
                'properties_count' => TOTAL_PROPERTIES_COUNT.'+',
                'cities_count'     => TOTAL_CITIES_COUNT.'+',
            ],
            'message'    => 'Device successfully registered.',
        ];

        $response = new PostDeviceResponse($response);
        $response = $response->toArray();
        return ApiResponse::create($response);

    }//end postRegister()


    /**
     * Create a new traffic source
     *
     * @param \App\Http\Requests\PostTrafficSourceRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/device/trafficsource",
     *     tags={"Device"},
     *     description="store traffic source data",
     *     operationId="device.post.trafficsource",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/traffic_source_in_form"),
     * @SWG\Parameter(ref="#/parameters/medium_in_form"),
     * @SWG\Parameter(ref="#/parameters/campaign_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Data Saved successfully",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostDeviceTrafficsourceResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function postTrafficSource(PostTrafficSourceRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        TrafficSource::saveTrafficData(
            [
                'device_id'      => $request->getDeviceUniqueId(),
                'source'         => $input_params['source'],
                'medium'         => $input_params['medium'],
                'campaign'       => $input_params['campaign'],
                'all_parameters' => json_encode($request->all()),
            ]
        );

        $response = new PostDeviceTrafficsourceResponse(['message' => 'Data Saved successfully.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postTrafficSource()


}//end class
