<?php
/**
 * Offline Discovery controller containing methods
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Hash, View};
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use \Event;
use DB;

use App\Libraries\Helper;
use App\Libraries\ApiResponse;

use App\Libraries\v1_6\{LeadService,AwsService};
use App\Events\UserRegistered;

use App\Models\User;
use App\Models\TrafficData;
use App\Models\MobileAppDevice;




use App\Models\HostConversionLead;
use App\Admin;

use App\Models\{Amenity, RoomType, PropertyType, CancellationPolicy,  Property};


use App\Http\Requests\{PostOfflineDiscoveryLoginRequest, GetOfflineDiscoverySearchRequest,
    PostOfflineDiscoveryCreateLeadRequest, PostOfflineDiscoveryUploadLeadImageRequest, GetOfflineDiscoveryLeadFormListRequest};


use App\Http\Requests\PutOfflineDiscoveryUploadImageRequest;

use App\Http\Response\v1_6\Models\{PostOfflineDiscoveryLoginResponse, GetOfflineDiscoverySearchResponse,
    PostOfflineDiscoveryCreateLeadResponse, PostOfflineDiscoveryUploadLeadImageResponse, GetOfflineDiscoveryLeadFormListResponse};
use App\Listeners\CancelBookingRequestListener;


/**
 * Class OfflineDiscoveryController
 */
class OfflineDiscoveryController extends Controller
{
    use \App\Traits\PassportToken;

    /**
     * Lead Service
     *
     * @var object lead_service
     */
    private $lead_service;


    /**
     * Constructor for dependency injection.
     *
     * @param LeadService $lead_service Lead Service Object.
     *
     * @return void
     */
    public function __construct(LeadService $lead_service)
    {
        $this->lead_service = $lead_service;

    }//end __construct()


    /**
     * Login Offline Discovery
     *
     * @param \App\Http\Requests\PostOfflineDiscoveryLoginRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/offlinediscovery/login",
     *     tags={"Offline Discovery"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="offlinediscovery.post.login",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     *  * @SWG\Parameter(ref="#/parameters/access_token_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="You are successfully loggedin.Please contact team to enable your access",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostOfflineDiscoveryLoginResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="You are successfully signedup.Please contact team to enable your access",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostOfflineDiscoveryLoginResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/CreateHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Invalid access token. || Invalid source parameter.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while signing you up. Please try again after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=502,
     *         description="Error while fetching profile image.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postLogin(PostOfflineDiscoveryLoginRequest $request)
    {
        // Get device unique id.
        $device_unique_id = $request->getDeviceUniqueId();

        // Get Device Type.
        $header_device_type = $request->getDeviceSource();

        // Get All Input Param.
        $input_params = $request->input();

        // Access token is used for Google or Facebook Login.
        $access_token = $input_params['access_token'];

        // Randomly generated keys.
        $confirmation_code  = str_random(36);
        $auth_token         = str_random(36);
        $generated_password = str_random(6);

        // Get user ip and location.
        $ip_address    = Helper::getUserIpAddress();
        $user_location = Helper::getLocationByIp($ip_address);
        $user_currency = $user_location['currency'];

        // Is new user.
        $is_new_user = true;

        // User id.
        $user_id = '';

        // User has not passed any currency and location has no currency.
        if (empty($currency) === true && empty($user_currency) === true) {
            $currency = DEFAULT_CURRENCY;
        }

        // IMPORTANT: Setting default currency to INR till we support other currecny Payment.
        $currency = DEFAULT_CURRENCY;

        // Google signup.
        $payload = Helper::getGoogleSignUpProfile($access_token, $header_device_type);

        if (empty($payload) === true) {
            // Invaid access token.
            return ApiResponse::badRequestError(EC_INVALID_ACCESS_TOKEN, 'Invalid access token.');
        }

        // Google fetched data.
        $g_id       = $payload['id'];
        $email      = $payload['email'];
        $g_name     = $payload['name'];
        $g_lastname = $payload['last_name'];
        $g_picture  = $payload['profile_img'];

        $domain = explode('@', $email)[1];
        if ($domain !== 'guesthouser.com') {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Use guesthouser account for login.');
        }

        // Get user data via email.
        $user = User::select('id', 'email', 'profile_img', 'auth_key', 'base_currency', 'wallet_currency', 'deleted_at')->where('email', $email)->withTrashed()->first();

        // If user does not exist then create new else merge both the users.
        if (empty($user) === true) {
            // New user instance.
            $user                    = new User;
            $user->email             = $email;
            $user->password          = Hash::make($generated_password);
            $user->name              = $g_name;
            $user->last_name         = $g_lastname;
            $user->country           = $user_location['country_code'];
            $user->state             = $user_location['state_name'];
            $user->city              = $user_location['city'];
            $user->confirmation_code = $confirmation_code;
            $user->signup_source     = $header_device_type;
            $user->signup_method     = 'google';
            // Email, facebook, google.
            $user->ip_address      = $ip_address;
            $user->profile_img     = '';
            $user->auth_key        = '';
            $user->wallet_currency = '';
            $user->base_currency   = DEFAULT_CURRENCY;
        } else {
            $user->activateIfTrashed();
            $is_new_user = false;
        }//end if

        // Set google id.
        $user->google_id = $g_id;

        // Set email verify for gmail login case.
        if (empty($user->email_verify) === true) {
            $user->email_verify = 1;
        }

        // Set auth key.
        if (empty($user->auth_key) === true) {
            $user->auth_key = $auth_token;
        }

        // Set currency.
        if (empty($user->wallet_currency) === true && empty($currency) === false) {
            $user->wallet_currency = $currency;
        }

        if (empty($user->base_currency) === true && empty($currency) === false) {
            $user->base_currency = $currency;
        }

        if (empty($user->profile_img) === true && empty($g_picture) === false) {
            $user->profile_img = User::uploadImageToS3FromUrl($g_picture);

            if (empty($user->profile_img) === true) {
                return ApiResponse::badGatewayError(EC_BAD_GATEWAY, 'Error while fetching profile image.');
            }
        }

        // Create new user.
        if ($user->save() === true) {
            // Set user id.
            $user_id = $user->id;

            // Set google picture.
            $user_profile_image = Helper::generateProfileImageUrl('Male', $user->profile_img, $user_id);

            // For new user.
            if ($is_new_user === true) {
                // Generate user referral code using user id.
                $generated_referral_code = Helper::generateReferralCode($user_id);
                $user->referral_code     = $generated_referral_code;
                $user->save();

                // Send welcome email.
                $to_email = $user->email;
                // Send user registration email.
                $user_registered_event = new UserRegistered($user, GOOGLE_SOURCE_ID, $generated_password);
                Event::dispatch($user_registered_event);
            }//end if
        } else {
            // User not created successfully - possibly sever error.
            return ApiResponse::serverError(EC_SERVER_ERROR, ERROR_CODE_USER_NOT_CREATED);
        }//end if

        // Merge user browsed properties from device unique id to user id.
        // PropertyView::mergePropertyViews($device_unique_id, $user->id);
        // Set user id corresponding to device id.
        $row               = MobileAppDevice::getDeviceByDeviceUniqueId($device_unique_id);
        $current_timestamp = date('Y:m:d h:i:s');
        $row->user_id      = $user->id;
        $row->last_active  = $current_timestamp;
        $row->last_login   = $current_timestamp;
        $row->status       = 1;
        if ($row->device_type === 'ios') {
            $row->device_type = 'iPhone';
        }

        $row->save();

        // Traffic data.
        $traffic = [];
        $traffic['device_unique_id'] = $device_unique_id;
        $traffic['event']            = ($is_new_user === true) ? 'signup' : 'login';
        $traffic['actor_id']         = $user->id;
        $traffic['referrer']         = '';
        TrafficData::createNew($traffic);

        // Generate Access Token.
        $oauth_response = $this->getBearerTokenByUser($user, '2', false);
        $host_token     = User::loginUser($oauth_response['access_token'], $oauth_response['refresh_token'], $user->id);

        $user_profile               = User::getUserProfile($user->id);
        $user_profile['host_token'] = $host_token;
        $user_profile['event']      = ($is_new_user === true) ? 'signup' : 'login';

        // Response content.
        $content = [
            'user_profile'  => $user_profile,
            'token_type'    => $oauth_response['token_type'],
            'expires_in'    => $oauth_response['expires_in'],
            'access_token'  => $oauth_response['access_token'],
            'refresh_token' => $oauth_response['refresh_token'],
        ];

        if ($is_new_user === true) {
            // New user created.
            $content['message'] = 'You are successfully signedup.';
        } else {
            // User updated.
            $content['message'] = 'You are successfully loggedin.';
        }

        $response = new PostOfflineDiscoveryLoginResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postLogin()


    /**
     * Get search results
     *
     * @param \App\Http\Requests\GetOfflineDiscoverySearchRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/offlinediscovery/search",
     *     tags={"Offline Discovery"},
     *     description="Get offline discovery search results",
     *     operationId="offlinediscovery.get.search",
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_in_query"),
     * @SWG\Parameter(ref="#/parameters/contact_in_query"),
     * @SWG\Parameter(ref="#/parameters/state_in_query"),
     * @SWG\Parameter(ref="#/parameters/city_in_query"),
     * @SWG\Parameter(ref="#/parameters/property_name_in_query"),
     *
     *
     *
     *     produces={"application/json"},
     * @SWG\Response(
     *         response=200,
     *         description="Successfully fetched search data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetOfflineDiscoverySearchResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * )
     */
    public function getSearch(GetOfflineDiscoverySearchRequest $request)
    {
        $input = $request->input();

        $params = [
            'email'         => $input['email'],
            'contact'       => $input['contact'],
            'state'         => addslashes($input['state']),
            'city'          => addslashes($input['city']),
            'property_name' => addslashes($input['property_name']),
        ];

        if ((empty($params['contact']) === true && empty($params['email']) === true ) && (empty($params['property_name']) === true)
            && (empty($params['city']) === true && empty($params['state']) === true)
        ) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Enter either email/contact or property name or city, state for result.');
        }

        $search_result = Property::getODASearchResult($params);

        $content['property_list'] = $search_result['data'];
        $content['message']       = $search_result['message'];
        $response                 = new GetOfflineDiscoverySearchResponse($content);
        $response                 = $response->toArray();
        return ApiResponse::success($response);

    }//end getSearch()


    /**
     * Lead Creation Offline Discovery
     *
     * @param \App\Http\Requests\PostOfflineDiscoveryCreateLeadRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/offlinediscovery/createlead",
     *     tags={"Offline Discovery"},
     *     description="Create host conversion lead.",
     *     operationId="offlinediscovery.post.createlead",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/email_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/country_in_form"),
     * @SWG\Parameter(ref="#/parameters/total_listings_in_form"),           *
     * @SWG\Parameter(ref="#/parameters/state_in_form"),
     * @SWG\Parameter(ref="#/parameters/city_in_form"),
     * @SWG\Parameter(ref="#/parameters/latitude_in_form"),
     * @SWG\Parameter(ref="#/parameters/longitude_in_form"),
     * @SWG\Parameter(ref="#/parameters/address_in_form"),
     * @SWG\Parameter(ref="#/parameters/tariff_in_form"),
     * @SWG\Parameter(ref="#/parameters/cancellation_policy_in_form"),
     * @SWG\Parameter(ref="#/parameters/amenities_in_form"),
     * @SWG\Parameter(ref="#/parameters/property_type_in_form"),
     * @SWG\Parameter(ref="#/parameters/room_type_in_form"),
     * @SWG\Parameter(ref="#/parameters/units_in_form"),
     * @SWG\Parameter(ref="#/parameters/accomodation_in_form"),
     * @SWG\Parameter(ref="#/parameters/extra_guests_in_form"),
     * @SWG\Parameter(ref="#/parameters/bedrooms_in_form"),
     * @SWG\Parameter(ref="#/parameters/beds_in_form"),
     * @SWG\Parameter(ref="#/parameters/checkin_in_form"),
     * @SWG\Parameter(ref="#/parameters/checkout_in_form"),
     * @SWG\Parameter(ref="#/parameters/extra_guest_price_in_form"),
     * @SWG\Parameter(ref="#/parameters/property_notes_in_form"),
     * @SWG\Parameter(ref="#/parameters/website_in_form"),
     * @SWG\Parameter(ref="#/parameters/payment_terms_in_form"),
     * @SWG\Parameter(ref="#/parameters/gst_no_in_form"),
     * @SWG\Parameter(ref="#/parameters/payee_name_not_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/branch_name_not_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/acc_no_in_form"),
     * @SWG\Parameter(ref="#/parameters/ifsc_in_form"),
     * @SWG\Parameter(ref="#/parameters/noc_in_form"),
     *
     * @SWG\Response(
     *         response=201,
     *         description="Host conversion lead successfully created.",
     * @SWG\Schema(
     * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",   ref="#definitions/PostOfflineDiscoveryCreateLeadResponse"),
     * @SWG\Property(property="error",  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Invalid access token. || Invalid source parameter.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while signing you up. Please try again after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=502,
     *         description="Error while creating lead.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postCreateLead(PostOfflineDiscoveryCreateLeadRequest $request)
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

        $check_user = User::withTrashed()->where('contact', '=', $input_params['contact'])->first();

        if ($check_user === true) {
            return ApiResponse::badRequestError(ERROR_CODE_EMAIL_ALREADY_REGISTERED, 'User already exist.');
        }

        $lead_data_keys = [
            'property_name',
            'email',
            'contact',
            'contact_name',
            'country',
            'state',
            'city',
            'latitude',
            'longitude',
            'address',
            'total_listings',
            'tariff',
            'cancellation_policy',
            'amenities',
            'bank_details',
            'payment_terms',
            'property_notes',
            'website',
            'property_type',
            'room_type',
            'units',
            'accomodation',
            'extra_guests',
            'bedrooms',
            'beds',
            'checkin',
            'checkout',
            'price',
            'extra_guest_price',
            'gst_no',
            'payee_name',
            'branch_name',
            'acc_no',
            'ifsc',
            'noc',
        ];

        // Get Property Data.
        $lead_data = Helper::getArrayKeysData($input_params, $lead_data_keys);

        $user_id = 0;
        $host    = User::where('email', '=', $input_params['email'])->first();
        if (isset($host) === true) {
            $user_id = $host->id;
        }

        $lead_created = $this->lead_service->createLead($user_id, $lead_data);

        // Validate Bank Info that already exist or not.
        if (empty($lead_created) === true) {
            return ApiResponse::forbiddenError(EC_SERVER_ERROR, 'Unable to add lead.');
        }

        $response['lead_id'] = $lead_created->id;
        $response['message'] = 'Successfully created host conversion lead.';

        $data = new PostOfflineDiscoveryCreateLeadResponse($response);
        $data = $data->toArray();
        return ApiResponse::success($response);

    }//end postCreateLead()


    /**
     * Post lead image for lead
     *
     * @param \App\Http\Requests\PostOfflineDiscoveryUploadLeadImageRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/offlinediscovery/leaduploadimage",
     *     tags={"Offline Discovery"},
     *     description="post property review image.",
     *     operationId="offlinediscovery.post.leaduploadimage",
     *     produces={"application/json"},
     *     consumes={"multipart/form-data"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/lead_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/lead_image_in_form"),
     *
     * @SWG\Response(
     *         response=200,
     *         description="Image uploaded successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",   ref="#definitions/PostOfflineDiscoveryUploadLeadImageResponse"),
     * @SWG\Property(property="error",  ref="#/definitions/SuccessHttpResponse/properties/error"),
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
    public function postLeadUploadImage(PostOfflineDiscoveryUploadLeadImageRequest $request)
    {
        $input_file     = $request->file();
        $input_params   = $request->input();
        $uploaded_image = $input_file['lead_image'];

        $lead = HostConversionLead::getLead($input_params['lead_id']);

        $images = [];
        // If valid property lead exit.
        if (isset($lead[0]['id']) === true) {
            if (empty($uploaded_image) === false) {
                $image          = $uploaded_image;
                $file_name      = $image->getClientOriginalName();
                $file_path      = $image->getPathName();
                $extension      = Helper::getImageExtension($file_name);
                $new_image_name = rand(100, 9999).'_'.uniqid().'_'.time().'.'.$extension;

                // Check Valid format of image.
                $formats = [
                    'jpg',
                    'png',
                    'gif',
                    'jpeg',
                    'PNG',
                    'JPG',
                    'JPEG',
                    'svg',
                ];
                if (in_array($extension, $formats) === false) {
                    return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Please upload only images.');
                }

                // Put Lead Image in S3 Bucket.
                $put_image = AwsService::putImageInS3Bucket(S3_LEAD_PIC_FOLDER_URL.$new_image_name, $file_path);
                if ($put_image === false) {
                    return ApiResponse::serverError(ERROR_CODE_FILE_UPLOAD_FAILED, 'Error while uploading images.');
                }

                if (empty($lead[0]['images']) === true) {
                    array_push($images, $new_image_name);
                } else {
                    $images = explode(',', $lead[0]['images']);
                    array_push($images, $new_image_name);
                }

                $final_image_str = implode(',', $images);

                $params['images']       = $final_image_str;
                $params['leads_status'] = SUBMITTED_STATUS;
                $params['submitted_at'] = date('y-m-d');

                $update_image = HostConversionLead::updateLeadImage($input_params['lead_id'], $params);

                if ($update_image === false) {
                    return ApiResponse::serverError(EC_SERVER_ERROR, 'Error while updating in database.');
                }
            }//end if
        } else {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property lead does not exist.');
        }//end if

        $content = [
            'picture' => $new_image_name,
            'message' => 'Image uploaded successfully.',
        ];

        $response = new PostOfflineDiscoveryUploadLeadImageResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($content);

    }//end postLeadUploadImage()


    /**
     * Get Property Form Field List.
     *
     * @param \App\Http\Requests\GetOfflineDiscoveryLeadFormListRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/offlinediscovery/leadformlist",
     *     tags={"Offline Discovery"},
     *     description="Get offline discovery search results",
     *     operationId="offlinediscovery.get.leadformlist",
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     *     produces={"application/json"},
     * @SWG\Response(
     *         response=200,
     *         description="Successfully fetched property lead form field list.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetOfflineDiscoveryLeadFormListResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * )
     */
    public function getLeadFormList(GetOfflineDiscoveryLeadFormListRequest $request)
    {
        $property_type_list  = PropertyType::getAllPropertyTypesData();
        $room_type_list      = RoomType::getAllRoomTypes();
        $amenities           = Amenity::getActiveAmenities();
        $cancellation_policy = CancellationPolicy::getAllCancellationPolicies();

        $content['property_list']       = $property_type_list;
        $content['room_type_list']      = $room_type_list;
        $content['amenities']           = $amenities;
        $content['cancellation_policy'] = $cancellation_policy;
        $content['message']             = 'Successfully fetched property lead form field list.';

        $response = new GetOfflineDiscoveryLeadFormListResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getLeadFormList()


}//end class
