<?php
/**
 * Prive Controller containing methods to get Prive Owner data.
 */

namespace App\Http\Controllers\v1_6;

use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{Hash};
use \Event;
use \Auth;
use \Carbon\Carbon;


use App\Events\{UserRegistered, SendBookingPaymentLink , UserResetPassword, UserLoginUrlSms, ContactUs};
use App\Models\{PasswordReminder, ProperlyTask};

use App\Libraries\CommonQueue;
use DB;

use App\Http\Requests\{PostPriveLoginRequest, GetPrivePropertyRequest , PostPriveRegisterRequest , GetPriveBookingsRequest,
    PostPriveResetPasswordRequest , GetPriveUserRequest , PutPriveUserRequest , GetPriveInvoiceRequest,
    GetPriveIndexRequest , GetPriveHomeGraphRequest, GetPriveManagerPropertyRequest, GetPriveManagerBookingsRequest, GetPriveManagerBookingDetailRequest, GetPriveBookingDetailRequest,
    PostPriveBookingRequest, PostPriveBookingCheckedinRequest , PostPriveManagerOperationRequest, PostPriveManagerContactTravellerRequest,
      PostPriveManagerBookingCashCollectRequest, PostPriveManagerSendPaymentLinkRequest, PostProperlyCreateMemberRequest, DeleteProperlyRemoveMemberRequest, GetProperlyFilterMemberRequest,
      PutProperlySuspendMemberRequest, GetProperlyResendLoginAccessRequest, GetTravellerPrivePropertiesRequest,PostProperlyTaskRequest ,
      GetProperlyTaskRequest , PutProperlyTaskRequest , PutProperlyTaskStatusRequest , GetProperlyScheduledTaskRequest, PostPriveSupportEmailRequest, PostProperlyExpenseRequest ,
      GetProperlyExpenseIndexRequest ,GetProperlyExpenseRequest , GetProperlyExpenseAccordanceRequest, BaseFormRequest, PutProperlyExpenseRequest,
      PutPriveMobileLoginRequest , PostPriveLoginViaRequest, GetProperlyTaskDetailRequest };


use App\Http\Response\v1_6\Models\{PostPriveLoginResponse, GetPrivePropertyResponse ,PostPriveRegisterResponse ,
    GetPriveBookingsResponse , PostPriveResetPasswordResponse , GetPriveUserResponse ,
    PutPriveUserResponse , GetPriveInvoiceResponse,  GetPriveIndexResponse , GetPriveHomeGraphResponse , GetPriveBookingDetailResponse ,
    PostPriveBookingResponse, GetPriveManagerPropertyResponse, GetPriveManagerBookingsResponse, GetPriveManagerBookingDetailResponse, PostPriveBookingCheckedinResponse, PostPriveManagerOperationResponse,
    PostPriveManagerContactTravellerResponse, PostPriveManagerBookingCashCollectResponse, PostPriveManagerSendPaymentLinkResponse, PostProperlyCreateMemberResponse,
    GetProperlyFilterMemberResponse, DeleteProperlyRemoveMemberResponse, PutProperlySuspendMemberResponse, GetProperlyResendLoginAccessResponse, GetTravellerPrivePropertiesResponse,
    PostProperlyTaskResponse , PutProperlyTaskResponse , PutProperlyTaskStatusResponse , GetProperlyTaskResponse , GetProperlyScheduledTaskResponse, PostPriveSupportEmailResponse, PostProperlyExpenseResponse , GetProperlyExpenseIndexResponse ,
    GetProperlyExpenseResponse , GetProperlyExpenseAccordanceResponse, DeleteProperlyExpenseResponse, PutProperlyExpenseResponse,PostPriveLoginViaResponse, GetProperlyTaskDetailResponse };


use App\Models\{Property , PropertyImage , User , MobileAppDevice, TrafficData, BookingRequest , CancellationPolicy , Booking, CountryCodeMapping, Amenity , RmBookingRemark, NoShowReasons, PaymentLink,
                ProperlyTeam, ProperlyTeamType, OtpContact, PriveManager, ProperlyExpense , ProperlyExpenseType, PriveManagerTagging};

use App\Libraries\v1_6\{UserService, BookingRequestService, PropertyTileService , PropertyService, InvoiceService , PropertyPricingService , CouponService, ProperlyService};

use App\Libraries\{ApiResponse, Helper};
use Illuminate\Support\Facades\Log;

/**
 * Class PriveController
 */
class PriveController extends Controller
{

    use \App\Traits\PassportToken;

    /**
     * User Service object.
     *
     * @var UserService
     */
    protected $user_service;

    /**
     * Properly Service object
     *
     * @var ProperlyService
     */
    private $properly_service;

    /**
     * PropertyService Service object.
     *
     * @var PropertyService
     */
    protected $property_service;


    /**
     * Constructor for dependency injection.
     *
     * @param UserService     $user_service     User Service Object.
     * @param ProperlyService $properly_service Properly Service Object.
     * @param PropertyService $property_service Property Service Object.
     *
     * @return void
     */
    public function __construct(UserService $user_service, ProperlyService $properly_service, PropertyService $property_service)
    {
        $this->user_service     = $user_service;
        $this->properly_service = $properly_service;
        $this->property_service = $property_service;

    }//end __construct()


    /**
     * Login Prive Owner/Manager Tokens
     *
     * @param \App\Http\Requests\PostPriveLoginRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/prive/login",
     *     tags={"Prive"},
     *     description="Returns an array containing Prive Login Data.",
     *     operationId="prive.post.login",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_in_form"),
     * @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_prive_login_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="List of dialcodes of countries.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                  ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                    ref="#definitions/PostPriveLoginResponse"),
     * @SWG\Property(property="error",                                   ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Email is not registerd as Prive Owner.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Unauthorized action. || Incorrect password entered. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postLogin(PostPriveLoginRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $email = $input_params['email'];
        $password_in_base64_encoded = $input_params['password'];

        // Set User is owner or not.
        $is_owner = ($input_params['source'] === 1) ? true : false;

        if ($is_owner === true) {
            // Get Prive Owner user data by email.
            $user = $this->user_service->getPriveOwner($email);
        } else {
            // Get Prive Manager User data by email.
            $user = $this->user_service->getPriveManager($email);
        }

        // Validate user is prive owner or not.
        if (empty($user) === true && $is_owner === true) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Email is not registerd or Not Activated as Prive Owner.', 'email');
        } else if (empty($user) === true && $is_owner === false) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Email is not registerd or Not Activated as Prive Manager.', 'email');
        }

        // Check if member exist in properly team.
        $in_team = ProperlyTeam::checkMemberStatus(['user_id' => $user->id]);

        if ($in_team === true) {
            $status = ProperlyTeam::getMemberStatus($user->id);

            if (isset($status['status']) === true && $status['status'] === PROPERLY_TEAM_MEMBER_INVITE) {
                $update_status = ProperlyTeam::updateStatusOnLogin(['user_id' => $user->id, 'status' => PROPERLY_TEAM_MEMBER_ACTIVE]);
                $user->assignRole('housekeeping#operations');
            }
        }

        // Validate Password.
        if (empty($this->user_service->validatePassword($user, $password_in_base64_encoded)) === true) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Password is incorrect. Please try again.', 'password');
        }

        // Generate Access Token.
        $oauth_response = $this->getBearerTokenByUser($user, '2', false);

        $user_profile = $this->user_service->getProfile($user);

        $user_roles_and_permissions = $this->user_service->getUserRolesPermissions($user);

        // Response content.
        $content = [
            'user_profile'  => [
                'name'          => $user_profile['first_name'],
                'profile_image' => $user_profile['profile_image'],
                'user_hash_id'  => $user_profile['user_hash_id'],
            ],
            'token_type'    => $oauth_response['token_type'],
            'expires_in'    => $oauth_response['expires_in'],
            'access_token'  => $oauth_response['access_token'],
            'refresh_token' => $oauth_response['refresh_token'],
            'permissions'   => $user_roles_and_permissions['permissions'],
            'roles'         => $user_roles_and_permissions['roles'],
        ];

        $response = new PostPriveLoginResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postLogin()


    /**
     * Get prive bookings list data
     *
     * @param \App\Http\Requests\GetPriveBookingsRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/prive/bookings",
     *     tags={"Prive"},
     *     description="Get prive bookings list",
     *     operationId="prive.get.bookings",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_start_date_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_end_date_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_status_in_query"),
     * @SWG\Parameter(ref="#/parameters/sort_for_bookings"),
     * @SWG\Parameter(ref="#/parameters/sort_order_for_bookings"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Booking list data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetPriveBookingsResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     * )
     * )
     */
    public function getBookings(GetPriveBookingsRequest $request)
    {
        $property_id    = 0;
        $property_list  = [];
        $booking_status = 0;
        $booking_list   = [];

        // Get All Input Param.
        $input_params = $request->input();

        if (isset($input_params['property_hash_id']) === true) {
            $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);
        }

        $start_date = $input_params['start_date'];
        $end_date   = $input_params['end_date'];
        $offset     = $input_params['offset'];
        $total      = $input_params['total'];
        $sort       = $input_params['sort'];

        // Set Booking Status Filter.
        if (isset($input_params['status']) === true) {
            $booking_status = $input_params['status'];
        }

        $sort_order = $input_params['sort_order'];

        $prive          = $request->getLoggedInUser();
        $prive_owner_id = $prive->id;
        $prive_name     = ucfirst($prive->name).' '.ucfirst($prive->last_name);

        // Total Property Count.
        $property_count = Property::getPrivePropertiescount($prive_owner_id, false);
        if ($start_date <= PROPERLY_BOOOKING_START_DATE && $end_date >= PROPERLY_BOOOKING_START_DATE) {
            $start_date = PROPERLY_BOOOKING_START_DATE;
        }

        // Get Booking List Data.
        $booking_request_service = new BookingRequestService;
        // Show booking list data after 01-06-2019.
        if ($start_date >= PROPERLY_BOOOKING_START_DATE && $end_date >= PROPERLY_BOOOKING_START_DATE) {
            $booking_list = $booking_request_service->getPriveBookings($prive_owner_id, $property_id, $start_date, $end_date, $offset, $total, $sort, $sort_order, $booking_status);
        }

        // Get prive property listings.
        $property_listings = Property::getPriveProperties($prive_owner_id, false);

        // Get property ids (unique) listed by prive.
        $property_ids = array_unique(array_column($property_listings, 'id'));
        foreach ($property_listings as $key => $one_property) {
            $properties_data['selected'] = ($one_property['id'] === $property_id) ? 1 : 0;
            $property_hash_id            = Helper::encodePropertyId($one_property['id']);

            $properties_data['property_hash_id'] = $property_hash_id;
            $properties_data['property_title']   = ucfirst($one_property['title']);
            $property_list[] = $properties_data;
        }

        $response = [
            'booking_requests' => $booking_list,
            'filter'           => [
                'start_date'     => carbon::parse($input_params['start_date'])->format('d-m-Y'),
                'end_date'       => carbon::parse($end_date)->format('d-m-Y'),
                'properties'     => $property_list,
                'booking_status' => [
                    [
                        'text'     => 'Booked',
                        'status'   => BOOKED,
                        'selected' => (empty($booking_status) === false && $booking_status === BOOKED) ? 1 : 0,
                    ],
                    [
                        'text'     => 'Cancelled',
                        'status'   => REQUEST_TO_CANCEL_AFTER_PAYMENT,
                        'selected' => (empty($booking_status) === false && $booking_status === REQUEST_TO_CANCEL_AFTER_PAYMENT) ? 1 : 0,
                    ],
                ],
            ],
            'booking_count'    => BookingRequest::getPriveBookingCount($prive_owner_id, $start_date, $end_date, $property_id, $booking_status),
            'property_count'   => $property_count,
            'user_name'        => $prive_name,
        ];

        $response = new GetPriveBookingsResponse($response);
        $response = $response->toArray();

        return ApiResponse::success($response);

    }//end getBookings()


    /**
     * Get Prive property listings
     *
     * @param \App\Http\Requests\GetPrivePropertyRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/prive/property",
     *     tags={"Prive"},
     *     description="get prives's properties.",
     *     operationId="prive.get.properties",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns array containing prive property listings.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetPrivePropertyResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",

     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * )
     */
    public function getProperties(GetPrivePropertyRequest $request)
    {
        $input_params = $request->input();

        $headers = $request->getAllHeaders();

        // Get prive User id.
        $prive_owner_id = $request->getLoginUserId();

        $offset = $input_params['offset'];
        $limit  = $input_params['total'];

        // Get prive property listings.
        $property_listings = $this->property_service->getPriveProperties($prive_owner_id, $headers, $offset, $limit, false);

        $content = [
            'properties'  => $property_listings,
            'total_count' => Property::getPrivePropertiescount($prive_owner_id, false),
        ];

        $response = new GetPrivePropertyResponse($content);
        $response = $response->toArray();

        return ApiResponse::success($response);

    }//end getProperties()


    /**
     * Create a new prive user
     *
     * @param \App\Http\Requests\PostPriveRegisterRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/prive/register",
     *     tags={"Prive"},
     *     description="Returns oauth tokens when prive registers successfully.",
     *     operationId="prive.post.register",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_in_form"),
     * @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_first_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_last_name_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_number_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_prive_in_form"),
     * @SWG\Parameter(ref="#/parameters/device_type_in_form"),
     * @SWG\Response(
     *      response=200,
     *      description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostPriveRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=201,
     *      description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostPriveRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/CreateHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters. || Invalid source parameter.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=403,
     *      description="We already have an account associated with this email.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=500,
     *      description="There was some error while signing you up. Please try again after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     * @SWG\Post(
     *     path="/v1.6/prive/register#google",
     *     tags={"Prive"},
     *     description="Returns oauth tokens when prive registers successfully.",
     *     operationId="prive.post.register.google",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/social_access_token_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_prive_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostPriveRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostPriveRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/CreateHttpResponse/properties/error"),
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
    public function postRegister(PostPriveRegisterRequest $request)
    {
        // Get device unique id.
        $device_unique_id = $request->getDeviceUniqueId();

        // Get Device Type.
        $header_device_type = $request->getDeviceType();

        // Get All Input Param.
        $input_params = $request->input();

        // Source (website, google).
        $source = $input_params['source'];
        if (isset($input_params['email']) === true) {
            $email = $input_params['email'];
        }

        if (isset($input_params['password']) === true) {
            $password = $input_params['password'];
        }

        if (isset($input_params['first_name']) === true) {
            $name = $input_params['first_name'];
        }

        $lastname  = $input_params['last_name'];
        $contact   = $input_params['contact'];
        $dial_code = $input_params['dial_code'];

        // Access token is used for Google Login.
        if (isset($input_params['access_token']) === true) {
            $access_token = $input_params['access_token'];
        }

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

        // IMPORTANT: Setting default currency to INR till we support other currecny Payment.
        $currency = DEFAULT_CURRENCY;

        // Check source type (website, facebook or google).
        switch ($source) {
            case WEBSITE_SOURCE_ID:
                // Website signup.
                // Get user data.
                $user = User::where('email', $email)->withTrashed()->first();

                // User email already exist.
                if (empty($user) === false) {
                    $user->activateIfTrashed();
                    return ApiResponse::forbiddenError(EC_DUPLICATE_USER, ERROR_CODE_EMAIL_ALREADY_REGISTERED);
                }

                // New user instance.
                $user                    = new User;
                $user->email             = $email;
                $user->password          = Hash::make($password);
                $user->name              = $name;
                $user->last_name         = $lastname;
                $user->contact           = $contact;
                $user->dial_code         = $dial_code;
                $user->ip_address        = $ip_address;
                $user->country           = $user_location['country_code'];
                $user->state             = $user_location['state_name'];
                $user->city              = $user_location['city'];
                $user->wallet_currency   = $user_currency;
                $user->confirmation_code = $confirmation_code;
                $user->auth_key          = $auth_token;
                $user->signup_source     = 'website';
                $user->signup_method     = 'email';
                $user->profile_img       = '';
                $user->base_currency     = $currency;

                // Create new user.
                if ($user->save() === true) {
                    // Send user registration email.
                    $user_registered_event = new UserRegistered($user, WEBSITE_SOURCE_ID);
                    Event::dispatch($user_registered_event);
                } else {
                    // User not created successfully - possibly sever error.
                    return ApiResponse::serverError(EC_SERVER_ERROR, ERROR_CODE_USER_NOT_CREATED);
                }//end if
            break;

            case GOOGLE_SOURCE_ID:
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
                    $user->signup_source     = 'website';
                    $user->signup_method     = 'google';
                    $user->ip_address        = $ip_address;
                } else {
                    $user->activateIfTrashed();
                    $is_new_user = false;
                }//end if

                // Set google id.
                if (empty($user->google_id) === true) {
                    $user->google_id = $g_id;
                }

                // Set auth key.
                if (empty($user->auth_key) === true) {
                    $user->auth_key = $auth_token;
                }

                // Set base currency.
                if (empty($user->base_currency) === true && empty($currency) === false) {
                    $user->base_currency = $currency;
                }

                // Set email verify for gmail login case.
                if (empty($user->email_verify) === true) {
                    $user->email_verify = 1;
                }

                if (empty($user->profile_img) === true && empty($g_picture) === false) {
                    $user->profile_img = User::uploadImageToS3FromUrl($g_picture);

                    if (empty($user->profile_img) === true) {
                        \Log::Error('Error while fetching profile image.');
                    }
                }

                // Create new user.
                if ($user->save() === true) {
                    // For new user.
                    if ($is_new_user === true) {
                        // Send user registration email.
                        $user_registered_event = new UserRegistered($user, GOOGLE_SOURCE_ID, $generated_password);
                        Event::dispatch($user_registered_event);
                    }//end if
                } else {
                    // User not created successfully - possibly sever error.
                    return ApiResponse::serverError(EC_SERVER_ERROR, ERROR_CODE_USER_NOT_CREATED);
                }//end if
            break;

            default:
                // Invalid source code.
            return ApiResponse::validationFailed(['source' => 'The source field is invalid.']);
                // phpcs:ignore
                break;
        }//end switch

        // Set user id corresponding to device id.
        $row               = MobileAppDevice::getDeviceByDeviceUniqueId($device_unique_id);
        $current_timestamp = date('Y:m:d h:i:s');
        $row->user_id      = $user->id;
        $row->last_active  = $current_timestamp;
        $row->last_login   = $current_timestamp;
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
        $oauth_response        = $this->getBearerTokenByUser($user, '2', false);
        $user_profile          = User::getUserProfile($user->id);
        $user_profile['event'] = ($is_new_user === true) ? 'signup' : 'login';

        // Response content.
        $content            = [
            'user_profile'  => $user_profile,
            'token_type'    => $oauth_response['token_type'],
            'expires_in'    => $oauth_response['expires_in'],
            'access_token'  => $oauth_response['access_token'],
            'refresh_token' => $oauth_response['refresh_token'],
        ];
        $content['message'] = ($is_new_user === true) ? 'User created successfully.' : 'User updated successfully.';
        $response           = new PostPriveRegisterResponse($content);
        $response           = $response->toArray();

        return ($is_new_user === true) ? ApiResponse::create($response) : ApiResponse::success($response);

    }//end postRegister()


    /**
     * Reset password
     *
     * @param \App\Http\Requests\PostPriveResetPasswordRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/prive/password/reset",
     *     tags={"Prive"},
     *     description="Returns an array containing flags to check if email  sent or not. Also returns error/success message.",
     *     operationId="prive.post.password.reset",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Link Sent on registered Email id. || reset password link sent to your email id.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostPriveResetPasswordResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.  Invalid email.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.||Email is not registerd or Not Activated as Prive Owner.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Error while generating reset password token.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postResetPassword(PostPriveResetPasswordRequest $request)
    {
        $input_params = $request->input();

        $email = $input_params['email'];

        // Get User data by email.
        $user = $this->user_service->getPriveOwner($email);

        // Validate user is prive owner or not.
        if (empty($user) === true) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Email is not registerd or Not Activated as Prive Owner.', 'email');
        }

        // Get reset password token.
        $reset_password_token = PasswordReminder::getResetPasswordTokenByEmail($user->email);

        // If reset password token doesn't exist.
        if (empty($reset_password_token) === true) {
            // Generate password token.
            $reset_password_token = PasswordReminder::generateResetPasswordTokenForEmail($user->email);

            if (empty($reset_password_token) === true) {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'Error while generating the reset password token.');
            }
        }

        $user_reset_password_event = new UserResetPassword($email, $reset_password_token, '', '', '', 'prive');
        Event::dispatch($user_reset_password_event);

        $content  = [
            'email_sent' => 1,
            'message'    => 'Link sent on registered email id.',
        ];
        $response = new PostPriveResetPasswordResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postResetPassword()


    /**
     * Get Prive profile data
     *
     * @param \Illuminate\Http\Requests\GetPriveUserRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/prive",
     *     tags={"Prive"},
     *     description="get prive's profile data.",
     *     operationId="prive.get.data",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing prive profile data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetPriveUserResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Auth token missing.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     *   )
     */
    public function getUser(GetPriveUserRequest $request)
    {
        $prive_id = $request->getLoginUserId();

        // User profile data.
        $profile_data = User::getUserProfile($prive_id, true);
        $user         = [
            'first_name'     => ucfirst($profile_data['first_name']),
            'last_name'      => ucfirst($profile_data['last_name']),
            'email'          => $profile_data['email'],
            'dial_code'      => $profile_data['dial_code'],
            'contact'        => $profile_data['mobile'],
            'profile_image'  => $profile_data['profile_image'],
            'property_count' => count(Property::getPriveProperties($prive_id)),
            'aadhar_card_no' => '',
            'pan_card_no'    => '',
            'account_no'     => '',
            'ifsc_code'      => '',

        ];
        $response = new GetPriveUserResponse($user);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getUser()


    /**
     * Update Prive profile data
     *
     * @param \Illuminate\Http\Requests\PutPriveUserRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Put(
     *     path="/v1.6/prive",
     *     tags={"Prive"},
     *     description="update prive's profile data.",
     *     operationId="prive.put.data",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/contact_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User details updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutPriveUserResponse"),
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
     *         description="Sorry! The number is already registered with us. Please try a different number.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while updating user details. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putUser(PutPriveUserRequest $request)
    {
        $input_params           = $request->input();
        $prive_id               = $request->getLoginUserId();
        $data_array             = [];
        $contact                = $input_params['contact'];
        $check_if_number_exists = User::checkIfAnyOtherUserHasSameNumber($prive_id, $contact);

        // If contact number does exist in db.
        if ($check_if_number_exists === true) {
            return ApiResponse::forbiddenError(EC_DUPLICATE_CONTACT, 'Sorry! The number is already registered with us. Please try a different number.');
        }

        $data_array['contact'] = $contact;

        $update_user_details = User::updateUserDetails($prive_id, $data_array);

        // If contact not updated.
        if ($update_user_details === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while updating details. Please try again.');
        }

        $profile_data['message'] = 'User details updated successfully.';
        $response                = new PutPriveUserResponse($profile_data);
        $response                = $response->toArray();
        return ApiResponse::success($response);

    }//end putUser()


    /**
     * Get prive inovice Monthly data
     *
     * @param App\Http\Requests\GetPriveInvoiceRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/prive/invoice",
     *     tags={"Prive"},
     *     description="Get prive bookings list",
     *     operationId="prive.get.invoice",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/month_year_in_query"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_query"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="invoice list data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetPriveInvoiceResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     * )
     * )
     */
    public function getInvoice(GetPriveInvoiceRequest $request)
    {
        $input_params   = $request->input();
        $month_year     = $input_params['month_year'];
        $offset         = $input_params['offset'];
        $total          = $input_params['total'];
        $prive_owner_id = Auth::user()->id;
        $invoice_list   = [];

        if (empty($input_params['property_hash_id']) === false) {
            $property_ids = array_unique($request->decodeAllPropertyIdOrFail($input_params['property_hash_id']));
        }

        $property_service = new PropertyService;
        $properties       = $property_service->getPriveOwnerPropertyIds($prive_owner_id, (isset($property_ids) === true) ? $property_ids : []);

        $booking_request_service = new BookingRequestService();
        $date_month_year         = explode('-', $month_year);
        $year_month              = $date_month_year[1].'-'.$date_month_year[0];
        // Show Invoice data after june 2019.
        if ($year_month >= carbon::parse(PROPERLY_BOOOKING_START_DATE)->format('y-m')) {
            $invoice_list = $booking_request_service->getInvoice($prive_owner_id, $month_year, $offset, $total, (isset($property_ids) === true) ? $property_ids : []);
        }

        $month_year_list = $this->properly_service->getInvoiceMonthYear($prive_owner_id);

        $response = [
            'invoice'         => $invoice_list,
            'filter'          => [
                'month_year' => $month_year,
                'properties' => $properties,
            ],
            'total'           => (isset($invoice_list['invoice']) === true) ? count($invoice_list['invoice']) : 0,
            'month_year_list' => $month_year_list,
        ];

         $response = new GetPriveInvoiceResponse($response);
         $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getInvoice()


    /**
     * Get Prive dashboard data
     *
     * @param \App\Http\Requests\GetPriveIndexRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/prive/home",
     *     tags={"Prive"},
     *     description="Get prive dashboard data",
     *     operationId="prive.get.home",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *      response=200,
     *      description="prive Dashboard data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetpriveIndexResponse"),
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
    public function getIndex(GetPriveIndexRequest $request)
    {
        // Fetch user data.
        $prive_owner_id = Auth::user()->id;

        $property_id             = 0;
        $property_listing        = [];
        $graph_data_current_year = [];
        $booking_list            = [];

        $property_list = [];

        $sort       = 1;
        $sort_order = 'ASC';

        // Show only booked property.
        $booking_status = 1;

        // Show only 5 property ad booking for home page.
        $total_count_show = 5;

        $start_date = Carbon::now()->toDateString();
        $end_date   = Carbon::now()->addMonth(1)->toDateString();

        $start_of_year = Carbon::now()->startOfYear()->format('Y-m-d');
        $end_of_year   = Carbon::now()->endOfYear()->format('Y-m-d');

        $start_of_month = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_of_month   = Carbon::now()->endOfMonth()->format('Y-m-d');

        // Get All headers.
        $headers = $request->headers->all();

        $booking_request_service = new BookingRequestService();

        // Calculate graph data of current year.
        $graph_data_current_year = $booking_request_service->getGraphData($prive_owner_id, $start_of_year, $end_of_year);

        // Calculate graph data of current month.
        $graph_data_current_month = $booking_request_service->getGraphData($prive_owner_id, $start_of_month, $end_of_month);

        // Total income.
        $total_income = (empty($graph_data_current_month) === false) ? $graph_data_current_month[0]['total_income'] : Helper::getFormattedMoney(0, DEFAULT_CURRENCY);

        // Calculate total no of nights booked.
        $no_of_nights_booked = (empty($graph_data_current_month) === false) ? $graph_data_current_month[0]['total_nights_booked'] : 0;

         // Get prive property listings.
        $property_service = new PropertyService;
        $properties       = $property_service->getPriveProperties($prive_owner_id, $headers, 0, 100, false);
        $property_listing = array_slice($properties, 0, 5, true);

        // Properties count.
        $total_properties = Property::getPrivePropertiescount($prive_owner_id, false);

        $active_properties   = Property::getPrivePropertiescount($prive_owner_id);
        $inactive_properties = ($total_properties - $active_properties);

        // Upcoming  Bookings list.
        $booking_list = $booking_request_service->getPriveBookings($prive_owner_id, $property_id, $start_date, $end_date, 0, $total_count_show, $sort, $sort_order, $booking_status);

        // Removing for now.
        // phpcs:ignore
        // $live_properties = $property_service->getLivePropertiesByOwnerId($prive_owner_id);
        foreach ($properties as $key => $one_property) {
            $properties_data['property_hash_id'] = $one_property['property_hash_id'];
            $properties_data['id']               = $one_property['id'];
            $properties_data['property_title']   = ucfirst($one_property['property_title']);
            $properties_data['status']           = $one_property['status'];
            $property_list[] = $properties_data;
        }

        $pids = array_column($property_list, 'id');

        $capital_investment = $this->properly_service->getCapitalInvestmentByPropertyIds($pids);

        $capital_investment = (empty($capital_investment['capital_investment']) === false) ? $capital_investment['capital_investment'] : 0;

        $content = [
            'total_booked_nights' => $no_of_nights_booked,
            'total_income'        => $total_income,
            'properties_count'    => [
                'total'               => $total_properties,
                'active_properties'   => $active_properties,
                'inactive_properties' => $inactive_properties,

            ],
            'graph_data'          => $graph_data_current_year,
            'upcoming_bookings'   => $booking_list,
            'properties'          => $property_listing,
            'property_list'       => $property_list,
            'capital_investment'  => Helper::getFormattedMoney($capital_investment, DEFAULT_CURRENCY),
        ];
        $response = new GetPriveIndexResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getIndex()


    /**
     * Get Prive Home Graph data
     *
     * @param \App\Http\Requests\GetPriveHomeGraphRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/prive/homegraph",
     *     tags={"Prive"},
     *     description="Get prive home graph data",
     *     operationId="prive.get.home",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/month_year_from_in_query"),
     * @SWG\Parameter(ref="#/parameters/month_year_to_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="prive Home graph data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetPriveHomeGraphResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * )
     */
    public function getHomeGraph(GetPriveHomeGraphRequest $request)
    {
        // Get Prive Owner Id.
        $prive_owner_id = Auth::user()->id;

        // Fetch Input Params.
        $input_params = $request->input();
        $start_date   = Carbon::createFromFormat('m-Y', $input_params['month_year_from'])->startOfMonth()->toDateString();
        $end_date     = Carbon::createFromFormat('m-Y', $input_params['month_year_to'])->endOfMonth()->toDateString();

        // Fetch Graph data.
        $booking_request_service = new BookingRequestService;
        $graph_data              = $booking_request_service->getGraphData($prive_owner_id, $start_date, $end_date);

        $content = [
            'graph_data' => $graph_data,
            'filter'     => [
                'month_year_from' => $input_params['month_year_from'],
                'month_year_to'   => $input_params['month_year_to'],

            ],
        ];

        $response = new GetPriveHomeGraphResponse($content);
        $response = $response->toArray();

        return ApiResponse::Success($response);

    }//end getHomeGraph()


    /**
     * Get prive booking details
     *
     * @param \App\Http\Requests\GetPriveBookingDetailRequest $request         Http request object.
     * @param string                                          $request_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\Response containing prive booking details
     *
     * @SWG\Get(
     *     path="/v1.6/prive/booking/{request_hash_id}",
     *     tags={"Prive"},
     *     description="get details of booking",
     *     operationId="prive.get.booking",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns json containing booking details",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetPriveBookingDetailResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * )
     */
    public function getBookingDetails(GetPriveBookingDetailRequest $request, string $request_hash_id)
    {
        $prive_owner_id = Auth::user()->id;

        $booking_request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);
        $headers            = $request->getAllHeaders();

        $booking = BookingRequest::gePriveBookingByRequestId($prive_owner_id, $booking_request_id);

        if (empty($booking) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $price_details = json_decode($booking['price_details'], true);

        // Get Booking Info.
        $booking_info = [
            'property_name' => ucfirst($booking['title']),
            'guests'        => $booking['guests'],
            'checkin'       => Carbon::parse($booking['from_date'])->format('dS M Y'),
            'checkout'      => Carbon::parse($booking['to_date'])->format('dS M Y'),
            'units'         => $booking['units_consumed'],
            'room'          => $booking['room'],
            'guest_name'    => ucfirst($booking['guest_name']),
            'host_fee'      => $booking['host_fee'],
            'extra_guest'   => (isset($price_details['extra_guest']) === true) ? $price_details['extra_guest'] : 0,

        ];

        // Get property images.
        $properties_images = PropertyImage::getPropertiesImagesByIds([$booking['pid']], $headers, 1);

        // Adding extra services.
        $extra_services = (empty($price_details['extra_services']) === false) ? $price_details['extra_services'] : [];

        $extra_services_data = [];

        foreach ($extra_services as $value) {
            $extra_services_name     = (isset($value['name']) === true) ? $value['name'] : '';
            $extra_services_quantity = (isset($value['quantity']) === true) ? $value['quantity'] : 1;
            $extra_services_cost     = (isset($value['cost']) === true) ? $value['cost'] : 0;
            $extra_services_rate     = (isset($value['rate']) === true) ? $value['rate'] : 0;

            $extra_services_data[] = [
                'name'     => $extra_services_name,
                'quantity' => $extra_services_quantity,
                'price'    => Helper::getFormattedMoney($extra_services_rate, $booking['currency'], true).' x '.$extra_services_quantity.' = '.Helper::getFormattedMoney($extra_services_cost, $booking['currency'], true),
            ];
        }

        $booking_info['properties_images'] = $properties_images[$booking['pid']];
        $booking_info['extra_services']    = $extra_services_data;

        // GetPrice Breakup.
        $invoice = InvoiceService::requestDetailsInvoiceForPrive($booking);

        $content = [
            'booking_info' => $booking_info,
            'invoice'      => $invoice,

        ];
        $response = new GetPriveBookingDetailResponse($content);
        $response = $response->toArray();

        return ApiResponse::success($response);

    }//end getBookingDetails()


    /**
     * Create Booking
     *
     * @param \App\Http\Requests\PostPriveBookingRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/prive/booking",
     *     tags={"Prive"},
     *     description="Create booking",
     *     operationId="prive.post.booking",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/checkin_in_form"),
     * @SWG\Parameter(ref="#/parameters/checkout_in_form"),
     * @SWG\Parameter(ref="#/parameters/guests_in_form"),
     * @SWG\Parameter(ref="#/parameters/units_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_first_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_last_name_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/email_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_in_form"),
     *
     * @SWG\Response(
     *         response=200,
     *         description="Returns json containing request id and message",
     * @SWG\Schema(
     * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",   ref="#definitions/PostPriveBookingResponse"),
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
     *         response=404,
     *         description="Property not found. || Property unavailable on selected dates. ",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function postBooking(PostPriveBookingRequest $request)
    {
        $input_params = $request->input();

        $device_source = $request->getDeviceType();

        $property_hash_id = $input_params['property_hash_id'];

        // Decode property_id from the hash id visible in url.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        $guests       = $input_params['guests'];
        $extra_guests = $input_params['extra_guests'];

        $units = $input_params['units'];

        $start_date = $input_params['checkin'];
        $end_date   = $input_params['checkout'];

        // User Details.
        $first_name = $input_params['first_name'];
        $last_name  = $input_params['last_name'];
        $email      = $input_params['email'];
        $contact    = $input_params['contact'];
        $dial_code  = $input_params['dial_code'];

        $prive          = $request->getLoggedInUser();
        $prive_owner_id = $prive->id;

        $is_bookable = PriveManagerTagging::validateBooking($property_id, $prive_owner_id, $start_date, $end_date);

        if ($is_bookable === false) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, "You don't have sufficient complementary nights for the selected dates.");
        }

        $traveller = User::getUserByEmail($email);

        if (empty($traveller) === false) {
            $travellar_id = $traveller->id;
            if (($traveller->mobile_verify === 1) && ($contact !== (int) $traveller->contact)) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'There is different contact number associated with this email.please enter the associated number or use different email.');
            } else if (($traveller->mobile_verify !== 1) && ($contact !== $traveller->contact)) {
                User::updateUserPrimaryContactDetail($travellar_id, $contact, $traveller->contact);
            }
        } else {
            $traveller               = new User;
            $traveller->name         = $first_name;
            $traveller->last_name    = $last_name;
            $traveller->email        = $email;
            $traveller->contact      = $contact;
            $traveller->dial_code    = $dial_code;
            $traveller->password     = Hash::make(123456);
            $traveller->offline_user = 1;
            $traveller->save();
            $travellar_id = $traveller->id;
        }

        $total_guests = ($extra_guests + $guests);

        $user             = $request->getLoggedInUser();
        $currency         = (empty($traveller->base_currency) === false) ? User::getCommonCurrency($traveller->base_currency) : DEFAULT_CURRENCY;
        $device_unique_id = $request->getDeviceUniqueId();

        $property = Property::getPrivePropertyDetailsForPreviewPageById($user->id, $property_id, $total_guests, $units);

        if (count($property) === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Array of data to process and get property pricing.
        $property_pricing_data = [
            'property_id'             => $property_id,
            'start_date'              => $start_date,
            'end_date'                => $end_date,
            'units'                   => $units,
            'guests'                  => $total_guests,
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
            'user'                    => $traveller,
            'pc_properly_commission'  => $property['pc_properly_commission'],
            'pmt_properly_commission' => $property['pmt_properly_commission'],
        ];

        // Get property pricing details.
        $property_pricing = PropertyPricingService::getPropertyPrice($property_pricing_data);

        // phpcs:ignore
        if ($property_pricing['error'] != '') {

            return ApiResponse::notFoundError(EC_NOT_FOUND, $property_pricing['error']);
        }

        $booking_amount = $property_pricing['total_price_all_nights'];

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

        $booking_amount_with_cleaning = ($booking_amount + $property_pricing['cleaning_price']);

        $property_pricing['total_price_all_nights_with_cleaning_price_gst'] = ($booking_amount_with_cleaning + $property_pricing['gst_amount']);

        // Discount.
        $payable_amount = $property_pricing['total_price_all_nights_with_cleaning_price_gst'];

        // Write better version for Get coupon or wallet and match.
        $released_payment = CouponService::getReleasedPayment($traveller);

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

        $choose_payment = Helper::getOldPaymentMethodName('coa_payment');

        // Fix this later.
        $source      = $device_source;
        $device_type = (empty($device_source) === false) ? $device_source : 'desktop';

        $booking_status = REQUEST_APPROVED;

        // Per night per unit price  + cleaning price per night per unit ) this include service fee.
        $price_per_night                = ($property_pricing['per_night_per_unit_price'] + $property_pricing['cleaning_price_per_unit']);
        $price_per_night_without_markup = ($property_pricing['per_night_per_unit_price_without_markup'] + $property_pricing['cleaning_price_per_unit']);

        // This is set to 0 by default by mistake but all other calculation are.
        // property inclucing service fee.
        // checking invetory princg line no 72 (guesthouser5).
        $service_fee_per_unit = 0;

        $price_details = [
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
            'chosen_payment_method'                                              => 'coa_payment',
            'currency_code'                                                      => $property_pricing['currency'],
            'currency_conversion_rate'                                           => Helper::getCurrencyExchanegRate($currency),
            'property_currency_code'                                             => $property['currency'],
            'property_currency_conversion_rate'                                  => Helper::getCurrencyExchanegRate($property['currency']),
        ];

        $params = [
            'currency'                => $property_pricing['currency'],
            'property_id'             => $property_id,
            'host_id'                 => $property['user_id'],
            'user_id'                 => $traveller->id,
            'units'                   => $property_pricing['required_units'],
            'bedrooms'                => $property_pricing['required_bedrooms'],
            'start_date'              => $start_date,
            'end_date'                => $end_date,
            // Write better version read from property pricing.
            'guests'                  => $total_guests,
            'price_details'           => $price_details,
            'payable_amount'          => $payable_amount,
            'gh_commission_percent'   => $property_pricing['gh_commission_percent'],
            'gst_percent'             => $property_pricing['gst_percent'],
            'cancelation_policy'      => $property['cancelation_policy'],
            'cash_on_arrival'         => $property['cash_on_arrival'],
            'prive'                   => $property['prive'],
            'source'                  => $source,
            'device_type'             => $device_type,
            'booking_status'          => $booking_status,
            'instant_book'            => $property_pricing['is_instant_bookable'],
            'device_unique_id'        => $device_unique_id,
            'valid_till'              => BookingRequestService::getValidTillTime(Carbon::now('GMT')->toDateTimeString(), $start_date),
            'approve_till'            => BookingRequestService::getApproveTillTime(Carbon::now('GMT')->toDateTimeString(), $start_date),

            // New Params for prive offline booking.
            'ota_source'              => 'New',
            'offline_source'          => 'prive_owner',
            'offline_booking_request' => 1,
            'prive_owner_id'          => $user->id,
            'properly_commission'     => $property_pricing['properly_commission'],
        ];

        $booking_request = BookingRequestService::createBookingRequest($params, $traveller);

        $booking_status = Booking::processCashlessPayment($booking_request);

        $response_data = [
            'message'    => 'booking Created',
            'request_id' => Helper::encodeBookingRequestId($booking_request->id),
        ];

        $response = new PostPriveBookingResponse($response_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postBooking()


    /**
     * Get Prive Manager property listings
     *
     * @param \Illuminate\Http\GetPriveManagerPropertyRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/prive/manager/property",
     *     tags={"Prive"},
     *     description="get prive manager's properties.",
     *     operationId="prive.manager.get.properties",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns array containing prive managers property listings.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetPriveManagerPropertyResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * )
     */
    public function getManagersProperties(GetPriveManagerPropertyRequest $request)
    {
        $input_params = $request->input();

        $headers          = $request->headers->all();
        $prive_manager_id = $request->getLoginUserId();
        $offset           = $input_params['offset'];
        $limit            = $input_params['total'];

        // Get prive property listings.
        $property_data = $this->property_service->getPriveManagerProperties($prive_manager_id, $headers, $offset, $limit);

        $response = [
            'properties' => $property_data['property_list'],
            'total'      => $property_data['total_count'],
        ];

        $response = new GetPriveManagerPropertyResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getManagersProperties()


    /**
     * Get prive manager bookings list data
     *
     * @param App\Http\Requests\GetPriveManagerBookingsRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/prive/manager/bookings",
     *     tags={"Prive"},
     *     description="Get prive manager bookings list",
     *     operationId="prive.manager.get.bookings",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_start_date_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_end_date_in_query"),
     * @SWG\Parameter(ref="#/parameters/sort_by_for_prive_bookings"),
     * @SWG\Parameter(ref="#/parameters/sort_order_for_prive_bookings"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/status_for_prive_manager_bookings"),
     * @SWG\Parameter(ref="#/parameters/enable_count_in_quest"),
     * @SWG\Parameter(ref="#/parameters/search_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Booking list data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                      ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                        ref="#definitions/GetPriveManagerBookingsResponse"),
     * @SWG\Property(property="error",                                       ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     * )
     * )
     */
    public function getManagerBookings(GetPriveManagerBookingsRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $headers          = $request->headers->all();
        $prive_manager_id = $request->getLoginUserId();
        $offset           = $input_params['offset'];
        $total            = $input_params['total'];
        $enable_count     = $input_params['enable_count'];
        $search           = (isset($input_params['search']) === true) ? $input_params['search'] : '';

        // Set Default Filter and sort type.
        $filter = [];
        $sort   = [];

        // Get Dates.
        $today    = Carbon::now();
        $tomorrow = $today->copy()->addDay();

        // Set Date Filter.
        if (empty($input_params['start_date']) === false && empty($input_params['end_date']) === false) {
            $filter['start_date'] = $input_params['start_date'];
            $filter['end_date']   = $input_params['end_date'];
        }

        // Set Property Filter.
        if (empty($input_params['property_hash_id']) === false) {
            $filter['property_ids'] = array_unique($request->decodeAllPropertyIdOrFail($input_params['property_hash_id']));
        }

        // Set Checkedin Status Filter.
        if (empty($input_params['status']) === false) {
            $selected_checkedin_status = array_map('intval', explode(',', $input_params['status']));
            $filter['status']          = array_intersect($selected_checkedin_status, [PRIVE_MANAGER_UPCOMING, PRIVE_MANAGER_CHECKEDIN, PRIVE_MANAGER_CHECKEDOUT, PRIVE_MANAGER_NO_SHOW, PRIVE_MANAGER_COMPLETED, PRIVE_MANAGER_CANCELLED]);
        } else {
            // Default filters.
            $filter['status'] = [
                PRIVE_MANAGER_UPCOMING,
                PRIVE_MANAGER_CHECKEDIN,
                PRIVE_MANAGER_CHECKEDOUT,
                PRIVE_MANAGER_NO_SHOW,
            ];
        }

        // Set Sort data.
        if (empty($input_params['sort_by']) === false && empty($input_params['sort_order']) === false) {
            $sort['sort_by'] = $input_params['sort_by'];
            $sort['order']   = $input_params['sort_order'];
        }

        // Get Booking List Data.
        $booking_request_service = new BookingRequestService;
        $booking_info            = $booking_request_service->getPriveManagerBookings($prive_manager_id, $offset, $total, $sort, $filter, $search);

        // Get prive property listings.
        $properties = $this->property_service->getPriveManagerPropertyIds($prive_manager_id, (isset($filter['property_ids']) === true) ? $filter['property_ids'] : []);

        $filter_start_date = (isset($filter['start_date']) === true) ? $filter['start_date'] : '';
        $filter_end_date   = (isset($filter['end_date']) === true) ? $filter['end_date'] : '';

        $response = [
            'booking_requests' => $booking_info['data'],
            'filter'           => [
                'start_date' => (isset($filter['start_date']) === true) ? $filter['start_date'] : '',
                'end_date'   => (isset($filter['end_date']) === true) ? $filter['end_date'] : '',
                'properties' => $properties,
                'status'     => [
                    [
                        'text'     => 'Upcoming',
                        'class'    => 'upcoming',
                        'status'   => PRIVE_MANAGER_UPCOMING,
                        'selected' => (empty($filter['status']) === false && in_array(PRIVE_MANAGER_UPCOMING, $filter['status']) === true) ? 1 : 0,
                    ],
                    [
                        'text'     => 'Ongoing',
                        'class'    => 'ongoing',
                        'status'   => PRIVE_MANAGER_CHECKEDIN,
                        'selected' => (empty($filter['status']) === false && in_array(PRIVE_MANAGER_CHECKEDIN, $filter['status']) === true) ? 1 : 0,
                    ],
                    [
                        'text'     => 'Due out',
                        'class'    => 'due_out',
                        'status'   => PRIVE_MANAGER_CHECKEDOUT,
                        'selected' => (empty($filter['status']) === false && in_array(PRIVE_MANAGER_CHECKEDOUT, $filter['status']) === true) ? 1 : 0,
                    ],
                    [
                        'text'     => 'No Show',
                        'class'    => 'no_show',
                        'status'   => PRIVE_MANAGER_NO_SHOW,
                        'selected' => (empty($filter['status']) === false && in_array(PRIVE_MANAGER_NO_SHOW, $filter['status']) === true) ? 1 : 0,
                    ],
                    [
                        'text'     => 'Completed',
                        'class'    => 'completed',
                        'status'   => PRIVE_MANAGER_COMPLETED,
                        'selected' => (empty($filter['status']) === false && in_array(PRIVE_MANAGER_COMPLETED, $filter['status']) === true) ? 1 : 0,
                    ],
                    [
                        'text'     => 'Cancelled',
                        'class'    => 'cancelled',
                        'status'   => PRIVE_MANAGER_CANCELLED,
                        'selected' => (empty($filter['status']) === false && in_array(PRIVE_MANAGER_CANCELLED, $filter['status']) === true) ? 1 : 0,

                    ],
                ],
                'search'     => $search,
            ],
            'sort'             => (empty($sort) === false) ? $sort : [],
            'booking_count'    => $booking_info['count'],
        ];

        $response = new GetPriveManagerBookingsResponse($response);
        $response = $response->toArray();

        return ApiResponse::success($response);

    }//end getManagerBookings()


    /**
     * Get prive manager booking detail data
     *
     * @param App\Http\Requests\GetPriveManagerBookingDetailRequest $request         Http request object.
     * @param string                                                $request_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/prive/manager/booking/{request_hash_id}",
     *     tags={"Prive"},
     *     description="Get prive manager bookings detail",
     *     operationId="prive.manager.get.booking",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Response(
     *      response=200,
     *      description="Booking list data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetPriveManagerBookingDetailResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     * )
     * )
     */
    public function getManagerBookingDetail(GetPriveManagerBookingDetailRequest $request, string $request_hash_id)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $headers = $request->headers->all();

        $user = $request->getLoggedInUser();

        $prive_manager_id = $user->id;

        // Get Booking List Data.
        $booking_request_service = new BookingRequestService;
        $booking_detail          = $booking_request_service->getPriveManagerBookingDetail($prive_manager_id, $request_id);

        if (empty($booking_detail) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No details available.');
        }

        $currency = (empty($booking_detail['currency']) === true ) ? DEFAULT_CURRENCY : $booking_detail['currency'];

        $country_codes = CountryCodeMapping::getCountries();

        $balance_fee    = round($booking_detail['balance_fee']);
        $payment_option = (int) $booking_detail['payment_option'];

        $property_hash_id = Helper::encodePropertyId($booking_detail['property_id']);

        $request_hash_id = Helper::encodeBookingRequestId($request_id);

        $properties_images = PropertyImage::getPropertiesImagesByIds([$booking_detail['property_id']], $headers, 1);

        // Get all property amenities.
        $property_amenities = ($booking_detail['amenities'] === '') ? [] : array_map('intval', explode(',', $booking_detail['amenities']));

        if (count($property_amenities) > 0) {
            $property_amenities = Amenity::getPropertyAmenityDetails($property_amenities, $headers);
        }

        $price_detail_data = json_decode($booking_detail['price_details'], true);

        $extra_services = (empty($price_detail_data['extra_services']) === false) ? $price_detail_data['extra_services'] : [];

        $extra_services_data = [];

        foreach ($extra_services as $value) {
            $extra_services_name     = (isset($value['name']) === true) ? $value['name'] : '';
            $extra_services_quantity = (isset($value['quantity']) === true) ? $value['quantity'] : 1;
            $extra_services_cost     = (isset($value['cost']) === true) ? $value['cost'] : 0;
            $extra_services_rate     = (isset($value['rate']) === true) ? $value['rate'] : 0;

            $extra_services_data[] = [
                'name'     => $extra_services_name,
                'quantity' => $extra_services_quantity,
                'price'    => Helper::getFormattedMoney($extra_services_rate, $currency, true).' x '.$extra_services_quantity.' = '.Helper::getFormattedMoney($extra_services_cost, $currency, true),
            ];
        }

        $checkin_date_obj       = Carbon::parse($booking_detail['from_date']);
        $checkin_date_formatted = $checkin_date_obj->format('jS F Y');

        $checkout_date_obj       = Carbon::parse($booking_detail['to_date']);
        $checkout_date_formatted = $checkout_date_obj->format('jS F Y');

        $today = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        $can_checkin  = 0;
        $can_checkout = 0;

        // Get Checked-in status.
        if ($booking_detail['checkedin_status'] === PRIVE_MANAGER_CANCELLED) {
            $checked_status = [
                'text'   => 'Cancelled',
                'class'  => 'cancelled',
                'status' => PRIVE_MANAGER_CANCELLED,
            ];
        } else if ($booking_detail['checkedin_status'] === PRIVE_MANAGER_UPCOMING) {
            $checked_status = [
                'text'   => 'Upcoming',
                'class'  => 'upcoming',
                'status' => PRIVE_MANAGER_UPCOMING,
            ];

            if ($booking_detail['from_date'] <= $today) {
                $can_checkin = 1;
            }
        } else if ($booking_detail['checkedin_status'] === PRIVE_MANAGER_CHECKEDIN) {
            $checked_status = [
                'text'   => 'Ongoing',
                'class'  => 'ongoing',
                'status' => PRIVE_MANAGER_CHECKEDIN,
            ];

            $can_checkout = 1;
        } else if ($booking_detail['checkedin_status'] === PRIVE_MANAGER_CHECKEDOUT) {
            $checked_status = [
                'text'   => 'Due out',
                'class'  => 'due_out',
                'status' => PRIVE_MANAGER_CHECKEDOUT,
            ];

            $can_checkout = 1;
        } else if ($booking_detail['checkedin_status'] === PRIVE_MANAGER_NO_SHOW) {
            $checked_status = [
                'text'   => 'No show',
                'class'  => 'no_show',
                'status' => PRIVE_MANAGER_NO_SHOW,
            ];

            $can_checkin = 1;
        } else {
            $checked_status = [
                'text'   => 'Completed',
                'class'  => 'completed',
                'status' => PRIVE_MANAGER_COMPLETED,
            ];
        }//end if

        $pending_payment = 0;

        if ($balance_fee > 0 && in_array($booking_detail['booking_status'], [BOOKED]) === true) {
            $pending_payment = 1;
        }

        $property_checkin_time  = @Carbon::createFromFormat('Y-m-d H:i:s', $booking_detail['from_date'].' '.$booking_detail['property_checkin_time'])->format('Y-m-d H:i:s');
        $property_checkout_time = @Carbon::createFromFormat('Y-m-d H:i:s', $booking_detail['to_date'].' '.$booking_detail['property_checkout_time'])->format('Y-m-d H:i:s');

        // phpcs:ignore
        $payment_option_text = (empty(PAYMENT_OPTION_TEXT[$booking_detail['payment_option']]) === false && empty(PAYMENT_OPTION_TEXT[$booking_detail['payment_option']]['text']) === false) ? PAYMENT_OPTION_TEXT[$booking_detail['payment_option']]['text'] : '';

        $booking_notes = ($user->hasPermissionTo('notes-view#operations') === true) ? RmBookingRemark::getBookingNotes($request_id) : [];

        $properly_title = $booking_detail['property_id'].' • '.ucfirst($booking_detail['title']);

        if (empty($booking_detail['properly_title']) === false) {
            $properly_title = $booking_detail['property_id'].' • '.ucfirst($booking_detail['properly_title']);
        }

        $response = [
            'property_section'     => [
                'property_hash_id' => $property_hash_id,
                'property_type'    => $booking_detail['property_type_name'],
                'room_type'        => $booking_detail['room_type_name'],
                'location'         => [
                    'area'          => ucfirst($booking_detail['area']),
                    'city'          => ucfirst($booking_detail['city']),
                    'state'         => ucfirst($booking_detail['state']),
                    // Country name from code.
                    'country'       => $country_codes[$booking_detail['country']],
                    'location_name' => Helper::formatLocation($booking_detail['area'], $booking_detail['city'], $booking_detail['state']),
                    'latitude'      => $booking_detail['latitude'],
                    'longitude'     => $booking_detail['longitude'],
                ],
                'property_title'   => $properly_title,
                'property_images'  => (array_key_exists($booking_detail['property_id'], $properties_images) === true) ? $properties_images[$booking_detail['property_id']] : [],
                'url'              => VERSION_PREFIX.'/property/'.$property_hash_id,
                'amenities'        => $property_amenities,
            ],
            'booking_info_section' => [
                'info'                => [
                    'request_hash_id'    => $request_hash_id,
                    'guests'             => $booking_detail['guests'],
                    'extra_guest'        => (isset($price_detail_data['extra_guest']) === true) ? $price_detail_data['extra_guest'] : 0,
                    'units'              => $booking_detail['units'],
                    'bedroom'            => $booking_detail['bedroom'],
                    'status'             => $checked_status,
                    'checkin'            => $checkin_date_obj->format('d-m-Y'),
                    'checkout'           => $checkout_date_obj->format('d-m-Y'),
                    'checkin_formatted'  => $checkin_date_formatted,
                    'checkout_formatted' => $checkout_date_formatted,
                    'guest_name'         => ucwords(trim($booking_detail['guest_name'].' '.$booking_detail['guest_last_name'])),
                    'can_checkin'        => $can_checkin,
                    'can_checkout'       => $can_checkout,
                    'source'             => $booking_detail['source'],
                    'checkin_data'       => [
                        'actual_checkin'    => (empty($booking_detail['actual_checkin_datetime']) === false) ? Carbon::parse($booking_detail['actual_checkin_datetime'])->format('Y-m-d H:i:s') : '',
                        'actual_checkout'   => (empty($booking_detail['actual_checkout_datetime']) === false) ? Carbon::parse($booking_detail['actual_checkout_datetime'])->format('Y-m-d H:i:s') : '',
                        'expected_checkin'  => (empty($booking_detail['expected_checkin_datetime']) === false) ? Carbon::parse($booking_detail['expected_checkin_datetime'])->format('Y-m-d H:i:s') : '',
                        'expected_checkout' => (empty($booking_detail['expected_checkout_datetime']) === false) ? Carbon::parse($booking_detail['expected_checkout_datetime'])->format('Y-m-d H:i:s') : '',
                    ],
                    'notes'              => $booking_notes,
                    'traveller_email'    => Helper::getModifiedEmail($booking_detail['traveller_email']),
                    'contacts'           => [
                        'manager'   => [
                            'primary'   => Helper::getFormattedContact($booking_detail['manager_primary_contact']),
                            'secondary' => Helper::getFormattedContact($booking_detail['manager_secondary_contact']),
                        ],
                        'traveller' => [
                            'primary'   => Helper::getFormattedContact($booking_detail['traveller_primary_contact']),
                            'secondary' => Helper::getFormattedContact($booking_detail['traveller_secondary_contact']),
                            'contact'   => $booking_detail['traveller_primary_contact'],
                        ],
                    ],
                ],
                'booking_amount_info' => [
                    'currency'                 => CURRENCY_SYMBOLS[$currency],
                    'total_amount'             => Helper::getFormattedMoney($price_detail_data['payable_amount'], $currency, true),
                    'total_amount_unformatted' => $price_detail_data['payable_amount'],
                    'paid_amount'              => Helper::getFormattedMoney(($price_detail_data['payable_amount'] - $balance_fee), $currency, true),
                    'paid_amount_unformatted'  => ($price_detail_data['payable_amount'] - $balance_fee),
                    'pending_payment'          => $pending_payment,
                    'pending_payment_amount'   => Helper::getFormattedMoney($balance_fee, $currency, true),
                    'extra_services'           => $extra_services_data,
                    'payment_option'           => $payment_option_text,

                ],
            ],
        ];

        $response = new GetPriveManagerBookingDetailResponse($response);
        $response = $response->toArray();

        return ApiResponse::success($response);

    }//end getManagerBookingDetail()


    /**
     * Save Booking Checkedin Status
     *
     * @param App\Http\Requests\PostPriveBookingCheckedinRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/prive/manager/booking/status",
     *     tags={"Prive"},
     *     description="Returns success message when reply save successfully.",
     *     operationId="prive.post.booking.status",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/status_for_prive_manager_bookings_required_in_form"),
     * @SWG\Parameter(ref="#/parameters/reason_id_for_prive_manager_bookings_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/comment_for_prive_manager_bookings_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if reply submitted successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                                          ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                                            ref="#definitions/PostPriveBookingCheckedinResponse"),
     * @SWG\Property(property="error",                                                           ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     *         description="Status already submitted / Unable to connect server.",
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
    public function postBookingCheckedInStatus(PostPriveBookingCheckedinRequest $request)
    {
        // Fetch All Input Params.
        $input_params = $request->input();

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);

        // Get logged in User Id.
        $prive_manager_id = $request->getLoginUserId();

        // Get Booking List Data.
        $booking_request_service = new BookingRequestService;
        $booking_detail          = $booking_request_service->getPriveManagerBookingDetail($prive_manager_id, $request_id);

        if (empty($booking_detail) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }

        if (in_array($booking_detail['booking_status'], [BOOKED]) === false) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Cannot mark check-in / check-out on a Cancelled Booking.');
        }

        $previous_checkin  = $booking_detail['checkin_status'];
        $previous_checkout = $booking_detail['checkout_status'];
        $previous_no_show  = $booking_detail['no_show'];
        $from_date         = $booking_detail['from_date'];
        $to_date           = $booking_detail['to_date'];
        $today             = Carbon::now('GMT')->format('Y-m-d');

        if ($input_params['status'] === PRIVE_MANAGER_NO_SHOW && ($previous_checkin === 1 || $previous_checkout === 1)) {
            return ApiResponse::forbiddenError(EC_SERVER_ERROR, 'Already Checked-In/Checked-Out.');
        } else if ($input_params['status'] === PRIVE_MANAGER_CHECKEDOUT && $previous_checkin === 0) {
            return ApiResponse::forbiddenError(EC_SERVER_ERROR, 'Not Checked-In or Already Marked as No show.');
        } else if ($input_params['status'] === PRIVE_MANAGER_CHECKEDIN && ($previous_checkout === 1 || $to_date < $today || $from_date > $today)) {
            return ApiResponse::forbiddenError(EC_SERVER_ERROR, 'Today is not the Check-In date.');
        } else if ($input_params['status'] === PRIVE_MANAGER_CHECKEDIN && $previous_checkin === 1) {
            return ApiResponse::forbiddenError(EC_SERVER_ERROR, 'Already Checked-In');
        }

        // Save Booking status.
        $change_status = $booking_request_service->savePriveManagerCheckedInStatus($request_id, $input_params);

        // If no booking exists for provided request id.
        if ($change_status === false) {
            return ApiResponse::forbiddenError(EC_SERVER_ERROR, 'Unable to connect server.');
        }

        if ($input_params['status'] === PRIVE_MANAGER_CHECKEDOUT) {
            $check_existance_task_type = ProperlyTask::checkTaskTypeByEntityId($request_id, TASK_TYPE_DEPARTURE_SERVICE);
            if (empty($check_existance_task_type) === true) {
                // Create Departure Task.
                $run_at                = Carbon::now('Asia/Kolkata')->addMinutes(15)->format('Y-m-d H:i:s');
                $create_departure_task = $booking_request_service->createReccuringTask($request_id, TASK_TYPE_DEPARTURE_SERVICE, $run_at);
            }
        } else {
            $property_checkout_time = @Carbon::createFromFormat('Y-m-d H:i:s', $booking_detail['to_date'].' '.$booking_detail['property_checkout_time'])->format('Y-m-d H:i:s');

            $checkout_run_at = (empty($booking_detail['expected_checkout_datetime']) === false) ? Carbon::parse($booking_detail['expected_checkout_datetime'])->format('Y-m-d H:i:s') : $property_checkout_time;

            // Create Checkout Task.
            $checkout_task = $booking_request_service->createReccuringTask($request_id, TASK_TYPE_CHECKOUT, $checkout_run_at);

            // Create Occupied Task.
            $occupied_service_task = $booking_request_service->createReccuringTask($request_id, TASK_TYPE_OCCUPIED_SERVICE, OCCUPIED_SERVICE_TIME, $from_date, $to_date, $prive_manager_id);

            // Turn Down Task.
            $turndown_service_task = $booking_request_service->createReccuringTask($request_id, TASK_TYPE_TURN_DOWN_SERVICE, TURN_DOWN_SERVICE_TIME, $from_date, $to_date, $prive_manager_id);
        }//end if

        $response = [
            'request_hash_id' => $input_params['request_hash_id'],
            'message'         => 'The changes have been saved successfully.',

        ];

        $response = new PostPriveBookingCheckedinResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postBookingCheckedInStatus()


    /**
     * Save Prive Booking Operational Data
     *
     * @param App\Http\Requests\PostPriveManagerOperationRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/prive/manager/booking/operation",
     *     tags={"Prive"},
     *     description="Returns success message when reply save successfully.",
     *     operationId="prive.post.booking.operation",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/operational_note_for_prive_manager_bookings_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/managerial_note_for_prive_manager_bookings_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/expected_checkin_for_prive_manager_bookings_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/expected_checkout_for_prive_manager_bookings_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if reply submitted successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                                                  ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                                                    ref="#definitions/PostPriveManagerOperationResponse"),
     * @SWG\Property(property="error",                                                                   ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     *         description="Data already submitted / Unable to connect server.",
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
    public function postManagerOperation(PostPriveManagerOperationRequest $request)
    {
        // Get Input Params.
        $input_params = $request->input();

        // Get Logged in user.
        $user = $request->getLoggedInUser();

        $prive_manager_id = $user->id;

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);

        // Get Booking List Data.
        $booking_request_service = new BookingRequestService;
        $booking_detail          = $booking_request_service->getPriveManagerBookingDetail($prive_manager_id, $request_id);

        if (empty($booking_detail) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }

        if (in_array($booking_detail['booking_status'], [BOOKED]) === false && (isset($input_params['expected_checkin']) === true || isset($input_params['expected_checkout']) === true)) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Operations cannot be perform on Cancelled Booking.');
        }

        if ((empty($input_params['operational_note']) === false || empty($input_params['managerial_note']) === false) && $user->hasPermissionTo('notes-edit#operations') === false) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'You do not have the permission to perform this task.', 'permission-denied');
        }

        if ((empty($input_params['expected_checkin']) === false || empty($input_params['expected_checkout']) === false) && $user->hasPermissionTo('checkedin-time-edit#operations') === false) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'You do not have the permission to perform this task.', 'permission-denied');
        }

        $input_params['from_date'] = $booking_detail['from_date'];
        $input_params['to_date']   = $booking_detail['to_date'];

        // Save Prive Booking Operation Data.
        $prive_operation = $booking_request_service->savePriveBookingOperation($prive_manager_id, $request_id, $input_params, $user);

        // If no booking exists for provided request id.
        if ($prive_operation === false) {
            return ApiResponse::forbiddenError(EC_SERVER_ERROR, 'Unable to connect server.');
        }

        $response = [
            'request_hash_id' => $input_params['request_hash_id'],
            'message'         => 'The changes have been saved successfully.',

        ];

        $response = new PostPriveManagerOperationResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postManagerOperation()


    /**
     * Call Traveller Mapping
     *
     * @param App\Http\Requests\PostPriveManagerContactTravellerRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/prive/manager/booking/contact-traveller",
     *     tags={"Prive"},
     *     description="Returns success message when reply save successfully.",
     *     operationId="prive.post.booking.contact-traveller",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/is_manager_primary_contact_for_prive_manager_bookings_in_form"),
     * @SWG\Parameter(ref="#/parameters/is_traveller_primary_contact_for_prive_manager_bookings_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if contact mapped successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                                                      ref="#definitions/PostPriveManagerContactTravellerResponse"),
     * @SWG\Property(property="error",                                                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     *         description="Data already submitted / Unable to connect server.",
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
    public function postManagerContactTraveller(PostPriveManagerContactTravellerRequest $request)
    {
        // Get Input Params.
        $input_params = $request->input();

        // Get Logged in user.
        $prive_manager_id = $request->getLoginUserId();

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);

        // Get Booking List Data.
        $booking_request_service = new BookingRequestService;
        $booking_detail          = $booking_request_service->getPriveManagerBookingDetail($prive_manager_id, $request_id);

        if (empty($booking_detail) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }

        if (in_array($booking_detail['booking_status'], [BOOKED]) === false) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Calling not allowed on Cancelled Booking.');
        }

        // Traveller Contacts.
        $traveller_contact = [
            (empty($booking_detail['traveller_secondary_contact']) === false && ctype_digit($booking_detail['traveller_secondary_contact']) === true) ? $booking_detail['traveller_secondary_contact'] : '',
            (empty($booking_detail['traveller_primary_contact']) === false && ctype_digit($booking_detail['traveller_primary_contact']) === true) ? $booking_detail['traveller_primary_contact'] : '',

        ];

        // Manager Contact.
        $manager_contact = [
            (empty($booking_detail['manager_secondary_contact']) === false && ctype_digit($booking_detail['manager_secondary_contact']) === true) ? $booking_detail['manager_secondary_contact'] : '',
            (empty($booking_detail['manager_primary_contact']) === false && ctype_digit($booking_detail['manager_primary_contact']) === true) ? $booking_detail['manager_primary_contact'] : '',

        ];

        // Return Not Found error when manager contact not found.
        if (empty($manager_contact[$input_params['is_manager_primary_contact']]) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Manager Contact not found or Invalid Contact.');
        }

        // Return Not Found error when traveller contact not found.
        if (empty($traveller_contact[$input_params['is_traveller_primary_contact']]) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Traveller Contact not found or Invalid Contact.');
        }

        // Save Contact Mapping Data Data.
        $contact = $booking_request_service->getBookingContactForCalling($request_id, $manager_contact[$input_params['is_manager_primary_contact']], $traveller_contact[$input_params['is_traveller_primary_contact']]);

        $response = [
            'request_hash_id' => $input_params['request_hash_id'],
            'contact'         => $contact,
            'message'         => 'Contacts successfully saved for calling.',

        ];

        $response = new PostPriveManagerContactTravellerResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postManagerContactTraveller()


    /**
     * Booking Cash Collection Account Detail
     *
     * @param App\Http\Requests\PostPriveManagerBookingCashCollectRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/prive/manager/booking/cash-collect",
     *     tags={"Prive"},
     *     description="Returns success message when reply save successfully.",
     *     operationId="prive.post.booking.cash-collect",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if contact mapped successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostPriveManagerContactTravellerResponse"),
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
     *         description="Data already submitted / Unable to connect server.",
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
    public function postManagerBookingCashCollect(PostPriveManagerBookingCashCollectRequest $request)
    {
        // Get Input Params.
        $input_params = $request->input();

        // Get Logged in user.
        $prive_manager_id = $request->getLoginUserId();

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);

        // Get Booking List Data.
        $booking_request_service = new BookingRequestService;
        $booking_detail          = $booking_request_service->getPriveManagerBookingDetail($prive_manager_id, $request_id);

        if (empty($booking_detail) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }

        if (in_array($booking_detail['booking_status'], [BOOKED]) === false) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'This action cannot be performed on Cancelled Booking.');
        }

        if (((int) $booking_detail['balance_fee']) === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No Pending payment found on this Booking.');
        }

        // Get Virtual Account Data.
        $account_data = $booking_request_service->getBookingSmartCashCollectionData($request_id, $booking_detail['property_id'], $booking_detail['guest_id'], ucfirst($booking_detail['guest_name'].' '.$booking_detail['guest_last_name']));

        // If no booking exists for provided request id.
        if (empty($account_data) === true) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Server not responding.');
        }

        $response = [
            'request_hash_id' => $input_params['request_hash_id'],
            'bank_name'       => $account_data['name'],
            'account_number'  => $account_data['account_number'],
            'ifsc_code'       => $account_data['ifsc'],
        ];

        $response = new PostPriveManagerBookingCashCollectResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postManagerBookingCashCollect()


    /**
     * Booking Send Payment Link
     *
     * @param App\Http\Requests\PostPriveManagerSendPaymentLinkRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/prive/manager/booking/send-payment-link",
     *     tags={"Prive"},
     *     description="Returns success message when payment link send.",
     *     operationId="prive.post.booking.send-payment-link",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_number_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/email_in_form_not_required"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if contact mapped successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostPriveManagerSendPaymentLinkResponse"),
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
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Data already submitted / Unable to connect server.",
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
    public function postManagerSendPaymentLink(PostPriveManagerSendPaymentLinkRequest $request)
    {
        // Get Input Params.
        $input_params = $request->input();

        // Get Logged in user.
        $prive_manager_id = $request->getLoginUserId();

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($input_params['request_hash_id']);

        // Get Booking List Data.
        $booking_request_service = new BookingRequestService;
        $booking_detail          = $booking_request_service->getPriveManagerBookingDetail($prive_manager_id, $request_id);

        if (empty($booking_detail) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Booking request not found.');
        }

        if (in_array($booking_detail['booking_status'], [BOOKED]) === false) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'This action cannot be performed on Cancelled Booking.');
        }

        if (((int) $booking_detail['balance_fee']) === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'No Pending payment found on this Booking.');
        }

        PaymentLink::savePaymentLink(
            [
                'user_id'          => $booking_detail['guest_id'],
                'amount'           => (int) $booking_detail['balance_fee'],
                'request_id'       => $request_id,
                'assigned_to'      => $booking_detail['assigned_to'],
                'payment_gateways' => 26,
            ]
        );

        $contact_number = (empty($input_params['contact_number']) === false) ? $input_params['contact_number'] : $booking_detail['traveller_primary_contact'];
        $dial_code      = (empty($input_params['dial_code']) === false) ? $input_params['dial_code'] : $booking_detail['traveller_dial_code'];
        $email          = (empty($input_params['email']) === false) ? $input_params['email'] : $booking_detail['traveller_email'];

        $send_payment_link_event = new SendBookingPaymentLink($request_id, ucfirst($booking_detail['guest_name'].' '.$booking_detail['guest_last_name']), $email, $dial_code, $contact_number);
        Event::dispatch($send_payment_link_event);

        $response = [
            'request_hash_id' => $input_params['request_hash_id'],
            'message'         => 'Payment link send successfully.',
        ];

        $response = new PostPriveManagerSendPaymentLinkResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postManagerSendPaymentLink()


    /**
     * Create Team Member
     *
     * @param \App\Http\Requests\PostProperlyCreateMemberRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *     path="/v1.6/properly/member",
     *     tags={"Properly"},
     *     description="create team member properly",
     *     operationId="properly.post.create",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/first_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/last_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/phone_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/team_type_id_in_form"),
     *
     * * @SWG\Response(
     *         response=200,
     *         description="Successfully create member.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostProperlyCreateMemberResponse"),
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
    public function postCreateMember(PostProperlyCreateMemberRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $user_params['contact']   = $input_params['phone'];
        $user_params['name']      = $input_params['first_name'];
        $user_params['last_name'] = $input_params['last_name'];
        $user_params['dial_code'] = $input_params['dial_code'];

        $loggedin_user = $request->getLoggedInUser();

        // Check user exist or not.
        $get_user = new User;
        $is_user  = $get_user->getUserByMobileNumber($input_params['phone']);

        $team_params                 = [];
        $team_params['manager_id']   = $loggedin_user['id'];
        $team_params['team_type_id'] = $input_params['team_type_id'];

        $teams   = ProperlyTeamType::getTeamDetails();
        $team_id = [];

        foreach ($teams as $id) {
            array_push($team_id, $id['id']);
        }

        // Check for invalid role id.
        if ((in_array($input_params['team_type_id'], $team_id)) === false) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Invalid role id.');
        }

        if (isset($is_user[0]) === true) {
            $team_params['user_id'] = $is_user[0]['id'];

            // Check if member with same userid and role exist.
            $is_already_added = ProperlyTeam::checkMemberStatus($team_params);

            if ($is_already_added === true) {
                return ApiResponse::forbiddenError(EC_DUPLICATE_USER, 'User already exists in the team.');
            }

            $is_added = ProperlyTeam::getAddedMember(['user_id' => $is_user[0]['id']]);
            // Check if previous status of member is in invite state.
            if (count($is_added) !== 0 && $is_added[0]['status'] === PROPERLY_TEAM_MEMBER_INVITE) {
                $team_params['status'] = PROPERLY_TEAM_MEMBER_INVITE;
            } else {
                $team_params['status'] = PROPERLY_TEAM_MEMBER_ACTIVE;
            }

            $save_member = ProperlyTeam::saveTeam($team_params);

            if ($save_member === false) {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'Member not created.');
            }

            User::updateUserDetails($is_user[0]['id'], ['prive_manager' => 1]);
            $is_user[0]->assignRole('housekeeping#operations');
        } else {
            $user_data_keys = [
                'contact',
                'name',
                'last_name',
                'dial_code',
                'prive_manager',
            ];

            $user_params['prive_manager'] = 1;
            $user_data                    = Helper::getArrayKeysData($user_params, $user_data_keys);

            $user_created = $this->user_service->createUser($user_data);

            // Create new user.
            if (isset($user_created['id']) === true) {
                // Send otp to registered mobile number pending.
                $team_params['user_id'] = $user_created['id'];
                $team_params['status']  = PROPERLY_TEAM_MEMBER_INVITE;

                $save_member = ProperlyTeam::saveTeam($team_params);

                if ($save_member === false) {
                    return ApiResponse::serverError(EC_SERVER_ERROR, 'Member not created.');
                }
            } else {
                // User not created successfully - possibly sever error.
                return ApiResponse::serverError(EC_SERVER_ERROR, ERROR_CODE_USER_NOT_CREATED);
            }//end if
        }//end if

        $login_url = Helper::getShortUrl(PROPERLY_URL.'?phone='.$input_params['phone']);

        // Event to send sms with login url.
        $send_login_url = new UserLoginUrlSms($login_url, $input_params['dial_code'], $input_params['phone']);
        Event::dispatch($send_login_url);

        $content['message'] = (isset($is_user[0]) === true) ? 'User added to team successfully.' : 'Invitation sent successfully.';
        $response           = new PostProperlyCreateMemberResponse($content);
        $response           = $response->toArray();

        return (isset($is_user) === true) ? ApiResponse::success($response) : ApiResponse::create($response);

    }//end postCreateMember()


    /**
     * Create Booking Task
     *
     * @param App\Http\Requests\PostProperlyTaskRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Post(
     *  path="/v1.6/properly/task",
     *  tags={"Properly"},
     *  description="Returns success message when  task created.",
     *  operationId="properly.post.task",
     *  consumes={"application/x-www-form-urlencoded"},
     *  produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/entity_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/type_in_form"),
     * @SWG\Parameter(ref="#/parameters/assigned_to_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/run_at_date_in_form"),
     * @SWG\Parameter(ref="#/parameters/run_at_time_in_form"),
     * @SWG\Parameter(ref="#/parameters/task_description_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if task create successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                 ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                   ref="#definitions/PostProperlyTaskResponse"),
     * @SWG\Property(property="error",                                  ref="#/definitions/SuccessHttpResponse/properties/error"),
     *  )
     * ),
     * @SWG\Response(
     *         response=404,
     *         description="Either booking not found or booking is expired",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"
     *      ),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Either booking not found or booking has been completed|Task time can be between booking period",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Assigned to user Id not Found",

     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="You have successfully created Task..",
     *
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postProperlytask(PostProperlyTaskRequest $request)
    {
        // Default Params.
        $params                   = [];
        $params['status']         = PRIVE_TASK_OPEN;
        $params['assigned_by']    = '';
        $ongoing_upcoming_booking = true;

        // Collect Input params.
        $input_params = $request->input();

        $run_at = $input_params['run_at_date'].' '.$input_params['run_at_time'];

        if ($run_at <= Carbon::now('Asia/Kolkata')->toDateTimeString()) {
            return ApiResponse::validationFailed(['run_at_time' => 'The task date time must be after or equal to current date time']);
        }

         // Get Logged In user Id.
        $logged_in_user_id    = $request->getLoginUserId();
        $params['created_by'] = $logged_in_user_id;

        if (empty($input_params['assigned_to']) === false) {
            $user_id = $request->decodeUserIdOrFail($input_params['assigned_to']);
            $team    = ProperlyTeam::getMemberFilterResult($logged_in_user_id, ['team_user_id' => $user_id, 'status' => PROPERLY_TEAM_MEMBER_ACTIVE]);
            if (empty($team) === false) {
                $params['assigned_to'] = $user_id;
                $params['status']      = PRIVE_TASK_TODO;
                $params['assigned_by'] = $logged_in_user_id;
            } else {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Assigned to user Id not Found');
            }
        }

        $params['type']   = $input_params['type'];
        $params['run_at'] = $input_params['run_at_date'].' '.$input_params['run_at_time'];
        if (empty($input_params['description']) === false) {
            $params['description'] = $input_params['description'];
        }

        $params['entity_type']    = ENTITY_TYPE_BOOKING;
        $params['reccuring_type'] = NOT_RECCURING;

        // Decode request_id from the hash id.
        $request_id = $request->decodeBookingRequestIdOrFail($input_params['entity_id']);

        // Get Booking  Data.
        $booking_request_service = new BookingRequestService;
        $booking_detail          = $booking_request_service->getPriveManagerBookingDetail($logged_in_user_id, $request_id, $ongoing_upcoming_booking);

        if (empty($booking_detail) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Either booking not found or booking has been completed');
        }

        $property_checkout_time = @Carbon::createFromFormat('Y-m-d H:i:s', $booking_detail['to_date'].' '.$booking_detail['property_checkout_time'])->format('Y-m-d H:i:s');

        $property_checkin_time = @Carbon::createFromFormat('Y-m-d H:i:s', $booking_detail['from_date'].' '.$booking_detail['property_checkin_time'])->format('Y-m-d H:i:s');

        $checkout_run_at = (empty($booking_detail['expected_checkout_datetime']) === false) ? Carbon::parse($booking_detail['expected_checkout_datetime'])->format('Y-m-d H:i:s') : $property_checkout_time;

        $checkin_run_at = (empty($booking_detail['expected_checkin_datetime']) === false) ? Carbon::parse($booking_detail['expected_checkin_datetime'])->format('Y-m-d H:i:s') : $property_checkin_time;

        if ($params['run_at'] > $checkout_run_at || $params['run_at'] < $checkin_run_at) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Task time can be between booking period');
        }

        $params['entity_id'] = $request_id;

        // Save Task.
        $save_task = ProperlyTask::saveTask($params);

        if ($save_task === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Task not created.');
        }

        $message  = ['message' => 'You have successfully created the Task.'];
        $response = new PostProperlyTaskResponse($message);
        $response = $response->toArray();

        return ApiResponse::success($response);

    }//end postProperlytask()


    /**
     * Get Properly Member filter result
     *
     * @param \Illuminate\Http\GetProperlyFilterMemberRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/properly/member/filter",
     *     tags={"Properly"},
     *     description="get properly team  member.",
     *     operationId="properly.member.get.filter",

     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/filter_id_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns array containing properly members listings.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetProperlyFilterMemberyResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *   )
     *  ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getFilterMember(GetProperlyFilterMemberRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $loggedin_user = $request->getLoggedInUser();
        $id            = $loggedin_user['id'];

        // Get Filter result based on filter request.
        $data = ProperlyTeam::getMemberFilterResult($id, $input_params);

        $member_data = $data;
        $i           = 0;
        foreach ($data as $d) {
            $member_data[$i]->id = Helper::encodeUserId($d->id);
            $i++;
        }

        // Get all role details.
        $department = ProperlyTeamType::getTeamDetails();

        // Check for invalid filter id.
        if (isset($input_params['filter_id']) === true) {
            $dep_id = [];
            foreach ($department as $d_id) {
                array_push($dep_id, $d_id['id']);
            }

            if ((in_array($input_params['filter_id'], $dep_id)) === false) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid filter applied. Id should be '.implode(',', $dep_id));
            }
        }

        $content['team_count'] = count($member_data);
        $content['message']    = (count($member_data) > 0) ? 'Team member data fetched successfully.' : 'No team member found.';
        $content['members']    = $member_data;

        $i = 0;
        foreach ($department as $dp) {
            $content['filter']['team'][$i]['id']       = $dp['id'];
            $content['filter']['team'][$i]['name']     = ucfirst($dp['team_name']);
            $content['filter']['team'][$i]['selected'] = (isset($input_params['filter_id']) === true) ? (in_array($dp['id'], explode(',', $input_params['filter_id'])) === true) ? 1 : 0 : 0;
            $i++;
        }

        $content['filter']['team_filter_count'] = $i;

        $response = new GetProperlyFilterMemberResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getFilterMember()


    /**
     * Get Booking Task
     *
     * @param App\Http\Requests\GetProperlyTaskRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *  path="/v1.6/properly/task",
     *  tags={"Properly"},
     *  description="Returns task list.",
     *  operationId="properly.get.task",
     *  consumes={"application/x-www-form-urlencoded"},
     *  produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/filter_property_hash_string_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_task_status_string_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_task_type_string_in_query"),
     * @SWG\Parameter(ref="#/parameters/filter_assigned_to_string_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array of task listings.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                         ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                           ref="#definitions/GetProperlyTaskResponse"),
     * @SWG\Property(property="error",                                          ref="#/definitions/SuccessHttpResponse/properties/error"),
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
    public function getProperlytask(GetProperlyTaskRequest $request)
    {
        // Default Params.
        $filter = [];

        $input_params = $request->input();

        // Set Property Filter.
        if (empty($input_params['property_hash_ids']) === false) {
            $filter['property_ids'] = array_unique($request->decodeAllPropertyIdOrFail($input_params['property_hash_ids']));
        }

        // Set Task Status Filter.
        if (empty($input_params['status']) === false) {
            $filter['status'] = array_unique(explode(',', $input_params['status']));
        }

        // Set Task Allocated_to Filter.
        if (empty($input_params['assigned_to']) === false) {
            $filter['assigned_to'] = array_unique($request->decodeAllUserIdOrFail($input_params['assigned_to']));
        }

        // Set Task Type Filter.
        if (empty($input_params['type']) === false) {
            $filter['type'] = array_unique(explode(',', $input_params['type']));
        }

        // Get Logged In user Id.
        $logged_in_user_id = $request->getLoginUserId();

        // Get Booking Task Listing of today tomorrow and yesterday.
        $booking_request_service = new BookingRequestService;
        $task_list               = $booking_request_service->getProperlyTaskList($logged_in_user_id, $filter, 0, true);

        // Get all Task status for filter.
        $task_status = Helper::getPriveTaskStatus((isset($filter['status']) === true) ? $filter['status'] : []);

        // Get All Prive task type For filter.
        $task_type = Helper::getPriveTaskType((isset($filter['type']) === true) ? $filter['type'] : []);

        // Get prive property listings of current User for filter.
        $properties = $this->property_service->getPriveManagerPropertyIds($logged_in_user_id, (isset($filter['property_ids']) === true) ? $filter['property_ids'] : []);

        // Prive Manager Team.
        $properly_team = $this->property_service->getPriveManagerTeam($logged_in_user_id, (isset($filter['assigned_to']) === true) ? $filter['assigned_to'] : []);

        // Response Data.
        $response = [
            'tasks'  => $task_list,
            'filter' => [
                'status'      => $task_status,
                'type'        => $task_type,
                'properties'  => $properties,
                'assigned_to' => $properly_team,
            ],
        ];

        $response = new GetProperlyTaskResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getProperlytask()


    /**
     * Put Prive Manager Member Suspension
     *
     * @param \Illuminate\Http\PutProperlySuspendMemberRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Put(
     *     path="/v1.6/properly/member/suspend",
     *     tags={"Properly"},
     *     description="Properly member suspend.",
     *     operationId="properly.member.put.suspend",

     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/user_hash_id_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns successfully member suspension.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutProperlySuspendMemberResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * )
     */
    public function putSuspendMember(PutProperlySuspendMemberRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $loggedin_user = $request->getLoggedInUser();
        $manager_id    = $loggedin_user['id'];

        $user_id = Helper::decodeUserId($input_params['user_hash_id']);

        if (empty($user_id) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid user hash id.');
        }

        $time = Carbon::now()->toDateTimeString();

        $is_valid_user = User::getUserDataById($user_id);

        if (empty($is_valid_user) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid user id.');
        }

        $is_user_suspended = ProperlyTeam::getSuspendedMember($manager_id, $user_id);

        if (isset($is_user_suspended[0]['id']) === true) {
            $team_params['manager_id'] = $manager_id;
            $team_params['user_id']    = $user_id;
            $team_params['status']     = PROPERLY_TEAM_MEMBER_ACTIVE;
            $update_team               = ProperlyTeam::updateStatus($team_params);

            if ($update_team === false) {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'Error while unsuspending team member.');
            } else {
                $content['message'] = 'Successfully unsuspended team member.';
            }

            $is_valid_user->assignRole('housekeeping#operations');
        } else {
            $user = ProperlyTeam::getTeamMemberToSuspend($manager_id, $user_id);

            if (isset($user[0]['id']) === true) {
                $status = [
                    PRIVE_TASK_OPEN,
                    PRIVE_TASK_TODO,
                    PRIVE_TASK_PENDING,
                ];

                $team_params['manager_id'] = $manager_id;
                $team_params['user_id']    = $user_id;
                $team_params['status']     = PROPERLY_TEAM_MEMBER_SUSPEND;
                $update_team               = ProperlyTeam::updateStatus($team_params);

                if ($update_team === false) {
                    return ApiResponse::serverError(EC_SERVER_ERROR, 'Error while suspending team member.');
                } else {
                    $get_tasks = ProperlyTask::updateSuspendMemberTask($user_id, $manager_id, $status);

                    if ($get_tasks === false) {
                        return ApiResponse::serverError(EC_SERVER_ERROR, 'Error while free task assigned to this team member.');
                    }
                }

                $is_valid_user->removeRole('housekeeping#operations');
                // Message that updated succesfully.
                $content['message'] = 'Successfully suspended team member.';
            } else {
                // Something went wrong while suspending this member.
                $content['message'] = 'Something went wrong while supsending this member.';
            }//end if
        }//end if

        $response = new PutProperlySuspendMemberResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putSuspendMember()


    /**
     * Put Booking Task
     *
     * @param App\Http\Requests\PutProperlyTaskRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Put(
     *     path="/v1.6/properly/task",
     *     tags={"Properly"},
     *     description="Returns success message when task updated.",
     *     operationId="properly.put.task",
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/task_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/run_at_date_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/run_at_time_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/task_description_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/assigned_to_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/task_type_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                      ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                        ref="#definitions/PutProperlyTaskResponse"),
     * @SWG\Property(property="error",                                       ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.|Assigned to user Id not Found",
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
     *         description="Either booking not found or booking has been completed|Task time can be between booking period",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Error while updating task.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function putProperlyTask(PutProperlyTaskRequest $request)
    {
        // Fetch All Input Params.
        $input_params = $request->input();

        $params  = [];
        $task_id = $request->decodeTaskIdOrFail($input_params['task_hash_id']);

        $params['id'] = $task_id;

        $logged_in_user_id = $request->getLoginUserId();

         // Get Task Details.
        $task_details = ProperlyTask::getTaskDetails($task_id);

        $task_details['run_at'] = explode(' ', $task_details['run_at']);

        $run_at_date = $task_details['run_at'][0];
        $run_at_time = $task_details['run_at'][1];

        // Completed task not updatable.
        if ($task_details['status'] === PRIVE_TASK_COMPLETED) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Completed task can-not be updated.');
        }

        // First Time Assigned a task and change status to TO-DO.
        if (isset($input_params['assigned_to']) === true) {
            if ($input_params['assigned_to'] !== 'unassigned') {
                $user_id = $request->decodeUserIdOrFail($input_params['assigned_to']);

                 // Get team details.
                $team = ProperlyTeam::getMemberFilterResult($logged_in_user_id, ['team_user_id' => $user_id, 'status' => PROPERLY_TEAM_MEMBER_ACTIVE]);
                    // If no user find then dont update assignedto coloumn.
                if (empty($team) === false) {
                    $params['assigned_to'] = $user_id;
                    if ($task_details['status'] === PRIVE_TASK_OPEN) {
                        $params['status'] = PRIVE_TASK_TODO;
                    }
                } else {
                    return ApiResponse::notFoundError(EC_NOT_FOUND, 'Assigned to user Id not Found');
                }
            } else {
                $params['status']      = PRIVE_TASK_OPEN;
                $params['assigned_to'] = null;
            }
        }

        if (isset($input_params['description']) === true) {
            $params['description'] = $input_params['description'];
        }

        if (isset($input_params['type']) === true) {
            if ($task_details['reccuring_type'] === RECCURING) {
                return ApiResponse::forbiddenError(EC_FORBIDDEN, 'System generated task type can-not be updated.');
            } else {
                 $params['type'] = $input_params['type'];
            }
        }

        if (isset($input_params['run_at_date']) === true && isset($input_params['run_at_time']) === true) {
            $run_at = $input_params['run_at_date'].' '.$input_params['run_at_time'];
        } else if (isset($input_params['run_at_date']) === true) {
            $run_at = $input_params['run_at_date'].' '.$run_at_time;
        } else if (isset($input_params['run_at_time']) === true) {
             $run_at = $run_at_date.' '.$input_params['run_at_time'];
        }

        if (isset($run_at) === true) {
            if ($run_at <= Carbon::now('Asia/Kolkata')->toDateTimeString()) {
                return ApiResponse::validationFailed(['run_at_time' => 'The task date time must be after or equal to current date time']);
            }

            // Get Checkout Time.
            $booking_request_service = new BookingRequestService;
            $checkout_run_at         = $booking_request_service->getCheckedOutTimeByBookingId($logged_in_user_id, $task_details['entity_id']);

            $checkin_run_at = $booking_request_service->getCheckedInTimeByBookingId($logged_in_user_id, $task_details['entity_id']);

            if (empty($checkout_run_at) === true) {
                 return ApiResponse::notFoundError(EC_NOT_FOUND, 'Either booking not found or booking is expired');
            }

            if ($run_at > $checkout_run_at || $run_at < $checkin_run_at) {
                return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Task time can be between booking period');
            }

            $params['run_at'] = $run_at;
        }//end if

        $update_task = ProperlyTask::updateTask($params);

        if ($update_task === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Error while updating task');
        }

        $message  = ['message' => 'You have successfully updated this task.'];
        $response = new PutProperlyTaskResponse($message);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putProperlyTask()


    /**
     * Delete Properly Manager Member remove
     *
     * @param \Illuminate\Http\DeleteProperlyRemoveMemberRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Delete(
     *     path="/v1.6/properly/member",
     *     tags={"Properly"},
     *     description="remove properly manager's team member.",
     *     operationId="properly.delete.member",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/user_hash_id_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns response of successfully remove member.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/DeleteProperlyRemoveMemberResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * )
     */
    public function deleteMember(DeleteProperlyRemoveMemberRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $loggedin_user = $request->getLoggedInUser();
        $manager_id    = $loggedin_user['id'];

        $user_id = Helper::decodeUserId($input_params['user_hash_id']);

        if (empty($user_id) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid user hash id.');
        }

        $time = Carbon::now()->toDateTimeString();

        $is_valid_user = User::getUserDataById($user_id);

        if (empty($is_valid_user) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid user id.');
        }

        $user = ProperlyTeam::getSuspendedMember($manager_id, $user_id);

        if (isset($user[0]['id']) === true) {
            $params['manager_id'] = $manager_id;
            $params['user_id']    = $user_id;
            $params['status']     = PROPERLY_TEAM_MEMBER_DELETE;
            $delete_member        = ProperlyTeam::updateStatus($params);

            if ($delete_member === false) {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'Error while deleting team member.');
            }

            $is_valid_user->removeRole('housekeeping#operations');

            // Message that updated succesfully.
            $content['message'] = 'Successfully removed team member.';
        } else {
            // Something went wrong while removing this member.
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Please check if the member has been suspended or removed.');
        }

        $response = new DeleteProperlyRemoveMemberResponse($content);
        $response = $response->toArray();
        return ($user !== 0) ? ApiResponse::success($response) : ApiResponse::forbiddenError(EC_FORBIDDEN, $content['message']);

    }//end deleteMember()


    /**
     * Put Booking Task status
     *
     * @param App\Http\Requests\PutProperlyTaskStatusRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Put(
     *     path="/v1.6/properly/task/status",
     *     tags={"Properly"},
     *     description="Returns success message when task status updated.",
     *     operationId="properly.put.task.status",
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/task_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/task_status_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutProperlyTaskStatusResponse"),
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
     *         description="Task can Only move one step forward.|| Pending Task can only go to to-do state. || You can-not update a task to its backward state.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Error while updating task.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function putProperlyTaskStatus(PutProperlyTaskStatusRequest $request)
    {
        // Default Params.
         $params = [];

        // Fetch All Input Params.
        $input_params = $request->input();

        // Decode task_hash_id.
        $task_id = $request->decodeTaskIdOrFail($input_params['task_hash_id']);

        $params['id'] = $task_id;

        $params['status'] = $input_params['status'];

        $current_status = ProperlyTask::getTaskDetails($task_id);
        if ($current_status['status'] === PRIVE_TASK_OPEN && $params['status'] !== PRIVE_TASK_TODO) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Task can Only move one step forward.');
        }

        if ($current_status['status'] === PRIVE_TASK_PENDING && $params['status'] !== PRIVE_TASK_TODO) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Pending Task can only go to todo state.');
        }

        if ($current_status['status'] > $params['status'] && $current_status['status'] !== PRIVE_TASK_PENDING) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'You can-not update a task to its backward state.');
        }

        $update_task = ProperlyTask::updateTask($params);

        if ($update_task === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Error while updating task status.');
        }

        $message = ['message' => 'You have successfully updated this tasks status.'];

        $response = new PutProperlyTaskStatusResponse($message);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putProperlyTaskStatus()


    /**
     * Get Properly Send Login Access Message.
     *
     * @param \Illuminate\Http\GetProperlyResendLoginAccessRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/properly/loginaccess",
     *     tags={"Properly"},
     *     description="get login access token on phone number.",
     *     operationId="properly.get.resendloginaccess",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/contact_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns successfully send login url to register number.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetProperlyResendLoginAccessResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * )
     */
    public function getResendLoginAccess(GetProperlyResendLoginAccessRequest $request)
    {
        $input_params = $request->input();
        $phone        = $input_params['contact'];

        $get_user = new User;
        $user     = $get_user->getUserByMobileNumber($phone)->toArray();

        // Check if user with that number exist or not.
        if (isset($user[0]) === true) {
            $url       = PROPERLY_URL.'/managerlogin?contact='.$user[0]['contact'];
            $login_url = Helper::getShortUrl($url);
            $dial_code = (isset($user[0]['dial_code']) === true) ? $user[0]['dial_code'] : '91';
            $contact   = $user[0]['contact'];

            // Check if daily and hourly otp limit has been reached.
            $check_if_otp_limit_reached = OtpContact::isOtpLimitReached($contact, $user[0]['id']);

            if ($check_if_otp_limit_reached === true) {
                $content = [
                    'sms_sent' => 0,
                    'contact'  => '',
                    'message'  => ERROR_CODE_OTP_LIMIT,
                ];
            }

            $send_otp = $this->user_service->generateAndSendOtp($user[0], 3);
            if ($send_otp['status'] !== 1) {
                return ApiResponse::serviceUnavailableError(EC_SERVICE_UNAVIALABLE, 'There was some error while sending OTP. Please try after some time.');
            }
        } else {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'The phone number is not registered with us.');
        }//end if

        $content = [
            'sms_sent' => 1,
            'contact'  => $user[0]['contact'],
            'message'  => 'Otp sent to your registered number along with login url link.',
        ];

        $response = new GetProperlyResendLoginAccessResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($content);

    }//end getResendLoginAccess()


    /**
     * Get Booking Scheduled Task status By Request Id
     *
     * @param App\Http\Requests\GetProperlyScheduledTaskRequest $request         Http request object.
     * @param string                                            $request_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/properly/booking/task/{request_hash_id}",
     *     tags={"Properly"},
     *     description="Returns List of tasks",
     *     operationId="properly.get.task.request_hash_id",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/request_hash_id_in_path"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array of task list.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetProperlyScheduledTaskResponse"),
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
    public function getProperlyScheduledTask(GetProperlyScheduledTaskRequest $request, string $request_hash_id)
    {
        $team               = [];
        $booking_request_id = $request->decodeBookingRequestIdOrFail($request_hash_id);

        $booking_request_service = new BookingRequestService;

        $logged_in_user_id = $request->getLoginUserId();

        $task_list = $booking_request_service->getProperlyTaskList($logged_in_user_id, [], $booking_request_id);

        $property_service = new PropertyService;

        // Prive Manager Team.
        $properly_team = $property_service->getPriveManagerTeam($logged_in_user_id, []);

        foreach ($properly_team as $value) {
            unset($value['selected']);
            $team[] = $value;
        }

        $content  = [
            'tasks'       => $task_list,
            'assigned_to' => $team,
        ];
        $response = new GetProperlyScheduledTaskResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getProperlyScheduledTask()


    /**
     * Get Scheduled Task detail
     *
     * @param App\Http\Requests\GetProperlyTaskDetailRequest $request      Http request object.
     * @param string                                         $task_hash_id Request id in hash.
     *
     * @return \Illuminate\Http\Response
     *
     * @SWG\Get(
     *     path="/v1.6/properly/task/{task_hash_id}",
     *     tags={"Properly"},
     *     description="Task Detail",
     *     operationId="properly.get.task.detail",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/task_hash_id_in_path"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array of task list.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetProperlyScheduledTaskResponse"),
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
    public function getProperlyTaskDetail(GetProperlyTaskDetailRequest $request, string $task_hash_id)
    {
        // Default team data.
        $team = [];

        $task_id = $request->decodeTaskIdOrFail($task_hash_id);

        $user = $request->getLoggedInUser();

        $task_detail = BookingRequest::getProperlyTaskDetail($task_id);

        if (empty($task_detail) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Task not found.');
        }

        $property_service = new PropertyService;

        // Prive Manager Team.
        $properly_team = $property_service->getPriveManagerTeam($user->id);

        foreach ($properly_team as $value) {
            unset($value['selected']);
            $team[] = $value;
        }

        $can_status_edit = 0;
        $can_desc_edit   = 0;
        $can_allocate    = 0;
        $can_time_edit   = 0;

        if (($user->hasPermissionTo('task-status-edit#operations') === true && $user->id === $task_detail['assigned_to_id']) || $user->hasPermissionTo('task-edit#operations') === true) {
            $can_status_edit = 1;
            $can_desc_edit   = 1;
        }

        if ($user->hasPermissionTo('task-edit#operations') === true) {
            $can_allocate  = 1;
            $can_time_edit = 1;
        }

        $response = [
            'task_section' => [
                'task_hash_id'             => Helper::encodeTaskId($task_detail['task_id']),
                'entity_hash_id'           => Helper::encodeBookingRequestId($task_detail['booking_request_id']),
                'task_status'              => Helper::getPriveTaskShowStatus($task_detail['task_status']),
                'title'                    => $task_detail['id'].' • '.ucfirst($task_detail['title']),
                'task_type'                => Helper::getPriveTaskShowType($task_detail['task_type']),
                'task_date'                => @Carbon::parse($task_detail['task_date_time'])->format('Y-m-d'),
                'task_time'                => @Carbon::parse($task_detail['task_date_time'])->format('H:i:s'),
                'task_date_time_formatted' => @Carbon::parse($task_detail['task_date_time'])->format('dS M Y h:i A'),
                'traveller_name'           => ucfirst($task_detail['traveller_name']),
                'assigned_to'              => ucfirst($task_detail['assigned_to']),
                'guests'                   => $task_detail['guests'],
                'reccuring_type'           => $task_detail['reccuring_type'],
                'can_update'               => $task_detail['can_update'],
                'description'              => $task_detail['description'],
                'can_status_edit'          => $can_status_edit,
                'can_desc_edit'            => $can_desc_edit,
                'can_allocate'             => $can_allocate,
                'can_time_edit'            => $can_time_edit,
            ],
            'assigned_to'  => $team,
        ];

        $response = new GetProperlyTaskDetailResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getProperlyTaskDetail()


    /**
     * Post prive support emails
     *
     * @param \App\Http\Requests\PostPriveSupportEmailRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response containing prive booking details
     *
     * @SWG\Post(
     *     path="/v1.6/prive/contactus",
     *     tags={"Prive"},
     *     description="send email to prive support",
     *     operationId="prive.post.contactus",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/subject_in_form"),
     * @SWG\Parameter(ref="#/parameters/message_in_form"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns json containing message",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostPriveSupportEmailResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * )
     */
    public function postSupportEmail(PostPriveSupportEmailRequest $request)
    {
        $input_params = $request->input();
        $subject      = $input_params['subject'];
        $message      = $input_params['message'];
        // Send  email to support.
        $contact_us_event = new ContactUs($subject, $message);
        Event::dispatch($contact_us_event);
        $response = ['message' => 'Email successfully sent'];
        $response = new PostPriveSupportEmailResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postSupportEmail()


    /**
     * Add Properly Expense
     *
     * @param App\Http\Requests\PostProperlyExpenseRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *  path="/v1.6/prive/expense",
     *  tags={"Prive"},
     *  description="Returns success message when  task created.",
     *  operationId="prive.post.expense",
     *  consumes={"application/x-www-form-urlencoded"},
     *  produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_form"),
     * @SWG\Parameter(ref="#/parameters/month_year_in_form"),
     * @SWG\Parameter(ref="#/parameters/expense_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/basic_amount_in_form"),
     * @SWG\Parameter(ref="#/parameters/nights_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success message if expense creates successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostProperlyExpenseResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     * )
     * ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.|Expense Name Not Found",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Quantity can not be empty.|Expenses month year can be after or equal to property live month year",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Property was live after month_year.You cannot add expense",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Expense not added.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postProperlyExpense(PostProperlyExpenseRequest $request)
    {
        $params = [];

        // Get All Input Param.
        $input_params = $request->input();

        // Validate Property Hash id.
        $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);

        // Get Prive Owner Id.
        $prive_owner_id = $request->getLoginUserId();

        // Get prive owner property by id.
        $property = $this->properly_service->getPriveOwnerPropertyById($prive_owner_id, $property_id);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        $params['pid']          = $property_id;
        $params['month_year']   = $input_params['month_year'];
        $params['basic_amount'] = $input_params['basic_amount'];

        $property_live_date       = $this->properly_service->getPropertyLiveDate($property_id);
        $property_live_month_year = Carbon::parse($property_live_date)->format('Y-m');

        if (empty($property_live_date) === true) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Property was live after '.$property_live_month_year.'.You cannot add expense');
        }

        // Given month year can be after or equal to property live date.
        if ($property_live_month_year > $params['month_year']) {
            return ApiResponse::validationFailed(['month_year' => 'Expenses month year can be after or equal to property live month year '.Carbon::parse($property_live_date)->format('Y-m')]);
        }

        // Get Expense type by Name.
        $expense = ProperlyExpenseType::getExpenseByName($input_params['name']);

        if (empty($expense) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Expense Name Not Found.');
        }

        $expense_type = $expense['type'];

        if ($expense_type === EXPENSE_VARIABLE) {
            if (empty($input_params['nights']) === true) {
                return ApiResponse::validationFailed(['nights' => 'Quantity can not be empty.']);
            }

            $params['nights'] = $input_params['nights'];
        }

        $params['expense_type_id'] = $expense['id'];

        $save_expense = ProperlyExpense::saveExpenses($params);

        if ($save_expense === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'Expense not added.');
        }

         $message  = ['message' => 'You have successfully added Expense.'];
         $response = new PostProperlyExpenseResponse($message);
         $response = $response->toArray();

         return ApiResponse::success($response);

    }//end postProperlyExpense()


    /**
     * Get Prive owner expense dashboard data
     *
     * @param \App\Http\Requests\GetProperlyExpenseIndexRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/prive/expense/index",
     *     tags={"Prive"},
     *     description="Get prive owner expensedashboard data",
     *     operationId="prive.get.expense.home",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_required_in_query"),
     * @SWG\Parameter(ref="#/parameters/month_year_expense_optional_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="prive owner expense dashboard data",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                         ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                           ref="#definitions/GetProperlyExpenseIndexResponse"),
     * @SWG\Property(property="error",                                          ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.||Property was live after",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getProperlyExpenseIndex(GetProperlyExpenseIndexRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $month_year = $input_params['month_year'];

        // Get prive_owner id.
        $prive_owner_id = $request->getLoginUserId();

        // Validate Property Hash id.
        $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);

        // Check If property is logged in prive owner property or not.
        $property = $this->properly_service->getPriveOwnerPropertyById($prive_owner_id, $property_id);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Get Property Live date.
        $property_live_date = $this->properly_service->getPropertyLiveDate($property_id);

        if (empty($property_live_date) === false) {
            $property_live_month_year = Carbon::parse($property_live_date)->format('Y-m');
        }

        if (empty($property_live_date) === true || $property_live_month_year > $month_year) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property was live after '.$property_live_month_year);
        }

        // Expense list.
        $expenses = $this->properly_service->getMonthlyExpenses($property_id, $prive_owner_id, $month_year);

        // Total count of expenses.
        $total_expense_list = ProperlyExpense::getTotalMonthlyExpenses($property_id, $prive_owner_id, $month_year);

        // Get selected month pl and pl comparison to last month.
        $expense_distribution = $this->properly_service->getExpenseDistribution($prive_owner_id, $property_id, $month_year, $property_live_date);

        // Get all unique expense list data, as suggestions.
        $expenses_as_suggestions = $this->properly_service->getExpenseListAsSuggestions($property_id, $prive_owner_id, $month_year);

        $content = [
            'expense'              => $expenses,
            'expense_distrubution' => $expense_distribution,
            'expense_type'         => ProperlyExpenseType::getAllExpenseType(),
            'suggestions'          => $expenses_as_suggestions,
            'property_title'       => $property_id.' • '.$property['title'],
            'property_live_date'   => $property_live_date,
            'total'                => $total_expense_list,

        ];

        $response = new GetProperlyExpenseIndexResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getProperlyExpenseIndex()


    /**
     * Get Prive owner expense list
     *
     * @param \App\Http\Requests\GetProperlyExpenseRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/prive/expense",
     *     tags={"Prive"},
     *     description="Get prive owner expense list",
     *     operationId="prive.get.list",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_required_in_query"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/month_year_expense_optional_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="prive owner expense list",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                         ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                           ref="#definitions/GetProperlyExpenseResponse"),
     * @SWG\Property(property="error",                                          ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getProperlyExpense(GetProperlyExpenseRequest $request)
    {
        $input_params = $request->input();

        $limit      = $input_params['total'];
        $offset     = $input_params['offset'];
        $month_year = $input_params['month_year'];

        // Validate Property Hash id.
        $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);

        $prive_owner_id = $request->getLoginUserId();

        $property = $this->properly_service->getPriveOwnerPropertyById($prive_owner_id, $property_id);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Expense list.
        $expense_list = $this->properly_service->getMonthlyExpenses($property_id, $prive_owner_id, $month_year, $limit, $offset);

        $content  = ['expense' => $expense_list];
        $response = new GetProperlyExpenseResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getProperlyExpense()


    /**
     * Login via api.
     *
     * @param \App\Http\PostPriveLoginViaRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/prive/loginvia",
     *     tags={"Prive"},
     *     description="Returns success/error message based on login_via parameter.",
     *     operationId="prive.post.login_via",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/source_prive_login_in_form"),
     * @SWG\Parameter(ref="#/parameters/login_via_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Sucess response containing type and other details.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostPriveLoginViaResponse"),
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
     *         description="Email or Contact number is not registerd or Activated as Prive Manager",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=503,
     *         description="There was some error while sending OTP. Please try after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postLoginVia(PostPriveLoginViaRequest $request)
    {
        $type           = 'email';
        $message        = '';
        $sms_sender_ids = '';
        $dial_code      = '91';

        $input_params = $request->input();

        $login_via = $input_params['login_via'];

        if (is_numeric($login_via) === true) {
            $dial_code = $input_params['dial_code'];
        }

         // Set User is owner or not.
        $is_owner = ($input_params['source'] === 1) ? true : false;

        if ($is_owner === true) {
            // Get Prive Owner user data by contact.
            $user = $this->user_service->getPriveOwner($login_via, $dial_code);
        } else {
            // Get Prive Manager User data by contact.
            $user = $this->user_service->getPriveManager($login_via, $dial_code);
        }

        // Validate user is prive owner or not.
        if (empty($user) === true && $is_owner === true) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Email or Contact number is not registerd or Activated as Prive Owner.', 'login_via');
        } else if (empty($user) === true && $is_owner === false) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Email or Contact number is not registerd or Activated as Prive Manager.', 'login_via');
        }

        $user = $user->toArray();

        if (is_numeric($login_via) === true) {
            // Send Otp.
            $send_otp = $this->user_service->generateAndSendOtp($user, 1);

            if ($send_otp['status'] !== 1) {
                return ApiResponse::serviceUnavailableError(EC_SERVICE_UNAVIALABLE, 'There was some error while sending OTP. Please try after some time.');
            }

            $type           = 'phone';
            $sms_sender_ids = SMS_SENDER_IDS;
            $message        = $send_otp['message'];
        }

        $content  = [
            'type'           => $type,
            'message'        => $message,
            'sms_sender_ids' => $sms_sender_ids,
        ];
        $response = new PostPriveLoginViaResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postLoginVia()


    /**
     * Get Prive owner expense accordance list for given month
     *
     * @param \App\Http\Requests\GetProperlyExpenseAccordanceRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/prive/expense/accordance",
     *     tags={"Prive"},
     *     description="Get prive owner expense accordance list",
     *     operationId="prive.get.expanse.accordance",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_required_in_query"),
     * @SWG\Parameter(ref="#/parameters/month_year_expense_optional_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="prive owner expense accordance list",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                         ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                           ref="#definitions/GetProperlyExpenseAccordanceResponse"),
     * @SWG\Property(property="error",                                          ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     * ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getProperlyExpenseAccordance(GetProperlyExpenseAccordanceRequest $request)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $month_year = $input_params['month_year'];

        // Validate Property Hash id.
        $property_id = $request->decodePropertyIdOrFail($input_params['property_hash_id']);

        $prive_owner_id = $request->getLoginUserId();

        $property = $this->properly_service->getPriveOwnerPropertyById($prive_owner_id, $property_id);

        if (empty($property) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Get Property Live date.
        $property_live_date = $this->properly_service->getPropertyLiveDate($property_id);
        if (empty($property_live_date) === false) {
            $property_live_month_year = Carbon::parse($property_live_date)->format('Y-m');
        }

        if (empty($property_live_date) === true || $property_live_month_year > $month_year) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property was live after '.$property_live_month_year);
        }

        $expense_accordance = $this->properly_service->getExpenseAccordance($property_id, $prive_owner_id, $month_year, $property_live_date);

        $content = [
            'accordance'       => $expense_accordance,
            'accordance_month' => Carbon::parse($month_year)->format('F'),
            'property_title'   => $property_id.' •  '.$property['title'],
        ];

        $response = new GetProperlyExpenseAccordanceResponse($content);
        $response = $response->toArray();

        return ApiResponse::success($response);

    }//end getProperlyExpenseAccordance()


    /**
     * Update Properly Expense
     *
     * @param PutProperlyExpenseRequest $request                  HTTP request object.
     * @param string                    $properly_expense_hash_id Properly expense hash id.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException Validation Exception.
     *
     * @SWG\Put(
     *     path="/v1.6/prive/expense/{properly_expense_hash_id}",
     *     tags={"Prive"},
     *     description="Update properly expense",
     *     operationId="prive.put.expense",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/properly_expense_hash_id_in_path"),
     * @SWG\Parameter(ref="#/parameters/basic_amount_in_form"),
     * @SWG\Parameter(ref="#/parameters/nights_in_form"),
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns success message if updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",   ref="#definitions/PutProperlyExpenseResponse"),
     * @SWG\Property(property="error",  ref="#/definitions/SuccessHttpResponse/properties/error"),
     * )
     * ),
     *
     * @SWG\Response(
     *         response=400,
     *         description="The properly expense hash id is invalid.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Invalid Property",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Invalid expense update request.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function putProperlyExpense(PutProperlyExpenseRequest $request, string $properly_expense_hash_id)
    {
        // Get All Input Param.
        $input_params = $request->input();

        $properly_expense_id = $request->decodeProperlyExpenseIdOrFail($properly_expense_hash_id);
        $data['id']          = $properly_expense_id;

        $properly_expense = $this->properly_service->getExpense($properly_expense_id);
        if (true === empty($properly_expense)) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid expense update request.');
        }

        // Check if expense property id owned by requested user.
        // Get Prive Owner Id.
        $prive_owner_id = $request->getLoginUserId();

        // Get prive owner property by id.
        $property = $this->properly_service->getPriveOwnerPropertyById($prive_owner_id, $properly_expense->pid);
        if (empty($property) === true) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Property not found.');
        }

        $data['basic_amount'] = $input_params['basic_amount'];

        // Get Expense type by expense type id.
        $expense_type = $this->properly_service->getExpenseType($properly_expense->expense_type_id);
        if (true === empty($expense_type)) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Expense Name not found.');
        }

        if ($expense_type->type === EXPENSE_VARIABLE) {
            if (true === empty($input_params['nights'])) {
                return ApiResponse::validationFailed(['nights' => 'Quantity  cannot be empty.']);
            }

            $data['nights_booked'] = $input_params['nights'];
        }

        $this->properly_service->updateExpense($data);

        $content = [
            'properly_expense_hash_id' => Helper::encodeProperlyExpenseId($properly_expense_id),
            'message'                  => 'You have successfully updated Expense data',
        ];

        $response = new PutProperlyExpenseResponse($content);

        return ApiResponse::success($response->toArray());

    }//end putProperlyExpense()


    /**
     * Delete Properly Expense
     *
     * @param string $properly_expense_hash_id Properly expense hash id.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException Validation Exception.
     *
     * @SWG\Delete(
     *     path="/v1.6/prive/expense/{properly_expense_hash_id}",
     *     tags={"Prive"},
     *     description="Delete properly expense",
     *     operationId="prive.delete.expense",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/properly_expense_hash_id_in_path"),
     *
     * @SWG\Response(
     *     response=204,
     *     description="Returns success message if deleted successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status", ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",   ref="#definitions/DeleteProperlyExpenseResponse"),
     * @SWG\Property(property="error",  ref="#/definitions/SuccessHttpResponse/properties/error"),
     * )
     * ),
     *
     * @SWG\Response(
     *         response=400,
     *         description="The properly expense hash id is invalid.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse")
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Invalid Property",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Invalid expense delete request",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function deleteProperlyExpense(string $properly_expense_hash_id)
    {
        $request = new BaseFormRequest;

        $properly_expense_id = $request->decodeProperlyExpenseIdOrFail($properly_expense_hash_id);

        $properly_expense = $this->properly_service->getExpense($properly_expense_id);
        if (true === empty($properly_expense)) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid expense delete request.');
        }

        // Check if expense property id owned by requested user.
        // Get Prive Owner Id.
        $prive_owner_id = $request->getLoginUserId();

        // Get prive owner property by id.
        $property = $this->properly_service->getPriveOwnerPropertyById($prive_owner_id, $properly_expense->pid);
        if (empty($property) === true) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'Property not found.');
        }

        $this->properly_service->deleteExpense($properly_expense_id);

        $content = [
            'properly_expense_hash_id' => Helper::encodeProperlyExpenseId($properly_expense_id),
            'message'                  => 'Expense successfully deleted from Properly expense list.',
        ];

        $response = new DeleteProperlyExpenseResponse($content);

        return ApiResponse::success($response->toArray());

    }//end deleteProperlyExpense()


    /**
     * Verify otp when user login
     *
     * @param \App\Http\PutPriveMobileLoginRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/prive/login/mobile",
     *     tags={"Prive"},
     *     description="verify otp code added by prive for its validity and make prive user contact verified.",
     *     operationId="prive.put.user.login.mobile",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/source_prive_login_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_number_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_code_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User Profile with acess token and refresh token.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostPriveLoginResponse"),
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
     *         response=403,
     *         description="Mobile number is not verified.| Your OTP has been expired. || Invalid OTP entered.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Contact number is not registerd or Not Activated as Prive Owner|Contact number is not registerd or Not Activated as Prive manager",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while verifying the OTP. Please try after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putMobileLogin(PutPriveMobileLoginRequest $request)
    {
        $input_params = $request->input();

        $otp      = $input_params['otp_code'];
        $contact  = $input_params['contact_number'];
        $dialcode = $input_params['dial_code'];

         // Set User is owner or not.
        $is_owner = ($input_params['source'] === 1) ? true : false;

        if ($is_owner === true) {
            // Get Prive Owner user data by contact.
            $user = $this->user_service->getPriveOwner($contact);
        } else {
            // Get Prive Manager User data by contact.
            $user = $this->user_service->getPriveManager($contact);
        }

        // Validate user is prive owner or not.
        if (empty($user) === true && $is_owner === true) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Contact number is not registerd or Not Activated as Prive Owner.', 'contact_number');
        } else if (empty($user) === true && $is_owner === false) {
            return ApiResponse::unauthorizedError(EC_UNAUTHORIZED, 'Contact number is not registerd or Not Activated as Prive Manager.', 'contact_number');
        }

        // Check if member exist in properly team.
        $in_team = ProperlyTeam::checkMemberStatus(['user_id' => $user->id]);

        if ($in_team === true) {
            $status = ProperlyTeam::getMemberStatus($user->id);

            if (isset($status['status']) === true && $status['status'] === PROPERLY_TEAM_MEMBER_INVITE) {
                $update_status = ProperlyTeam::updateStatusOnLogin(['user_id' => $user->id, 'status' => PROPERLY_TEAM_MEMBER_ACTIVE]);
                $user->assignRole('housekeeping#operations');
            }
        }

        $user_id = $user->id;

        $verify_otp = $this->user_service->verifyUserOtp($user_id, $otp, $contact, $dialcode);

        if ($verify_otp === true) {
              // Generate Access Token.
            $oauth_response = $this->getBearerTokenByUser($user, '2', false);

            $user_profile = User::getUserProfile($user_id);

            $user_roles_and_permissions = $this->user_service->getUserRolesPermissions($user);

            // Response content.
            $content  = [
                'user_profile'  => [
                    'name'          => $user_profile['first_name'],
                    'profile_image' => $user_profile['profile_image'],
                    'user_hash_id'  => $user_profile['user_hash_id'],
                ],
                'token_type'    => $oauth_response['token_type'],
                'expires_in'    => $oauth_response['expires_in'],
                'access_token'  => $oauth_response['access_token'],
                'refresh_token' => $oauth_response['refresh_token'],
                'permissions'   => $user_roles_and_permissions['permissions'],
                'roles'         => $user_roles_and_permissions['roles'],
            ];
            $response = new PostPriveLoginResponse($content);
            $response = $response->toArray();
            return ApiResponse::success($response);
        } else {
             return ApiResponse::forbiddenError(EC_INVALID_OTP, $verify_otp['message']);
        }//end if

         return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while verifying the OTP. Please try after some time.');

    }//end putMobileLogin()


    /**
     * Get Prive Properties listings for traveller
     *
     * @param \App\Http\Requests\GetTravellerPrivePropertiesRequest $request Http request object.
     *
     * @return \Illuminate\Http\Response containing prive Property listing for traveller
     *
     * @SWG\Get(
     *     path="/v1.6/prive/listings",
     *     tags={"Prive"},
     *     description="get  property listing for traveller",
     *     operationId="prive.get.listings",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/city_optional_in_query"),
     * @SWG\Response(
     *      response=200,
     *      description="Returns json containing property listing",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetTravellerPrivePropertiesResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="The device unique id header field is required",
     * @SWG\Schema(ref="#/definitions/ErrorHttpResponse"),
     * ),
     * )
     */
    public function getTravellerPriveProperties(GetTravellerPrivePropertiesRequest $request)
    {
        $city = [];

        // Get All headers.
        $headers = $request->getAllHeaders();

        $input_params = $request->input();
        $offset       = $input_params['offset'];
        $total        = $input_params['total'];

        if (empty($input_params['city']) === false) {
            $prive_city = $input_params['city'];
            $city       = explode(',', $prive_city);
        }

        // Get prive Traveller property listings.
        $property_listings = $this->property_service->getPriveTravellerProperties($headers, $offset, $total, $city);

        $content = [
            'properties'  => $property_listings['property_list'],
            'total_count' => $property_listings['total_count'],
            'city'        => $property_listings['cities'],
        ];

        $response = new GetTravellerPrivePropertiesResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getTravellerPriveProperties()


}//end class
