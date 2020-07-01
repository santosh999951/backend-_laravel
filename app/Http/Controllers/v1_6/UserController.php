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

use Queue;
use \Google_Client;

use App\Events\UserRegistered;
use App\Events\{UserEmailVerified, UserResetPassword};

use App\Jobs\PushToQueueJob;
use App\Jobs\SendEmailViaJob;

use App\Models\BookingRequest;
use App\Models\CancellationPolicy;
use App\Models\CountryCodeMapping;
use App\Models\CurrencyConversion;
use App\Models\MobileAppDevice;
use App\Models\MyFavourite;
use App\Models\OtpContact;
use App\Models\PasswordReminder;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyTagMapping;
use App\Models\PropertyVideo;
use App\Models\PropertyView;
use App\Models\TrafficData;
use App\Models\User;
use App\Models\{UpdateEmail, WalletTransaction, OnetimeAccessToken};

use App\Libraries\ApiResponse;
use App\Libraries\Email;
use App\Libraries\CommonQueue;
use App\Libraries\Helper;

use App\Libraries\v1_6\AwsService;
use App\Libraries\v1_6\PaymentMethodService;
use App\Libraries\v1_6\PropertyTileService;
use App\Libraries\v1_6\{SmsService,PropertyPricingService, UserService};

use App\Http\Response\v1_6\Models\{GetUserResponse, GetUserPropertiesResponse, GetUserGetrefererdetailsResponse, GetUserWishlistResponse};
use App\Http\Response\v1_6\Models\{GetUserRefertoearnResponse, GetUserPasswordResetCheckResponse, PutUserResponse, PostUserLogoutResponse};
use App\Http\Response\v1_6\Models\{PostUserPictureResponse, PutUserCurrencyResponse, PostUserResponse, PutUserDeleteResponse};
use App\Http\Response\v1_6\Models\{PostUserAppfeedbackResponse, PostUserLoginoldappuserResponse, PostUserVerifyMobileResponse};
use App\Http\Response\v1_6\Models\{PutUserVerifyMobileResponse, PostUserWishlistResponse, DeleteUserWishlistResponse, PostUserVerifyEmailResponse};
use App\Http\Response\v1_6\Models\{PutUserPasswordResetResponse, PutUserPasswordResponse, PutUserPasswordUpdateResponse};
use App\Http\Response\v1_6\Models\{PutUserLastactiveUpdateResponse, PutUserVerifyEmailResponse, PostUserPasswordResetResponse, PostUserLoginViaTokenResponse , PostMobileLoginResponse, PutMobileLoginResponse};

use App\Http\Requests\{PostRegisterRequest, PostUserPictureRequest , PutUserCurrencyRequest , PutUserLastactiveUpdateRequest , PostUserAppfeedbackRequest};
use App\Http\Requests\{GetReferDetailsRequest , PostVerificationEmailRequest, PutVerificationEmailRequest ,PostVerifiyMobileRequest , PutVerifyMobileRequest};
use App\Http\Requests\{GetUserProfileRequest , PutUpdateUserRequest , PutDeleteUserRequest , GetUserPropertiesRequest , PostUserLoginoldappuserRequest};
use App\Http\Requests\{GetUserWishlistRequest , PostAddWishlistRequest, DeleteUserWishlistRequest};
use App\Http\Requests\{PostUserPasswordResetRequest , PutUserPasswordResetRequest , PutUserPasswordRequest , PutUserPasswordUpdateRequest, PostUserLoginViaTokenRequest , PostLogoutRequest, PostMobileLoginRequest , PutMobileLoginRequest};

/**
 * Class UserController
 */
class UserController extends Controller
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
     * @param UserService $user_service User Service Object.
     *
     * @return void
     */
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;

    }//end __construct()


    /**
     * Create a new user
     *
     * @param \App\Http\Requests\PostRegisterRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/user",
     *     tags={"User"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="user.post.register",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_in_form"),
     * @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_first_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_last_name_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_gender_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_in_form"),
     * @SWG\Parameter(ref="#/parameters/currency_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/referral_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/device_type_in_form"),
     * @SWG\Parameter(ref="#/parameters/base_currency_in_form"),
     * @SWG\Response(
     *      response=200,
     *      description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostUserResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=201,
     *      description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostUserResponse"),
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
     *     path="/v1.6/user#google",
     *     tags={"User"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="user.post.register.google",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/social_access_token_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_in_form"),
     * @SWG\Parameter(ref="#/parameters/currency_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/referral_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/device_type_in_form"),
     * @SWG\Parameter(ref="#/parameters/base_currency_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostUserResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostUserResponse"),
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
     * @SWG\Post(
     *     path="/v1.6/user#facebook",
     *     tags={"User"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="user.post.register.facebook",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/social_access_token_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_in_form"),
     * @SWG\Parameter(ref="#/parameters/currency_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/referral_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/device_type_in_form"),
     * @SWG\Parameter(ref="#/parameters/base_currency_in_form"),
     * @SWG\Parameter(ref="#/parameters/email_in_form_not_required"),
     * @SWG\Response(
     *         response=200,
     *         description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostUserResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostUserResponse"),
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
     *
     * @SWG\Post(
     *     path="/v1.6/user#apple",
     *     tags={"User"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="user.post.register.apple",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/social_access_token_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_first_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_last_name_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_gender_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_in_form"),
     * @SWG\Parameter(ref="#/parameters/currency_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/referral_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/device_type_in_form"),
     * @SWG\Parameter(ref="#/parameters/base_currency_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostUserResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostUserResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/CreateHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Invalid access token or id token. || Invalid source parameter.",
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
    public function postRegister(PostRegisterRequest $request)
    {
        // Get device unique id.
        $device_unique_id = $request->getDeviceUniqueId();

        // Get Device Type.
        $header_device_type = $request->getDeviceSource();

        // Get All Input Param.
        $input_params = $request->input();

        // Source (website, fb, google, apple).
        $source = $input_params['source'];

        $currency = $input_params['currency'];

        $referral_code = $input_params['referral_code'];
        $device_type   = $input_params['device_type'];
        $email         = $input_params['email'];
        $password      = $input_params['password'];
        $name          = $input_params['first_name'];
        $lastname      = $input_params['last_name'];
        $gender        = $input_params['gender'];

        // Access token (Id token, in case of Apple Login) is used for Google, Facebook or Apple Login.
        $access_token = $input_params['access_token'];

        // Randomly generated keys.
        $confirmation_code  = str_random(36);
        $auth_token         = str_random(36);
        $generated_password = str_random(6);
        $random_password    = '';

        // Get user ip and location.
        $ip_address    = Helper::getUserIpAddress();
        $user_location = Helper::getLocationByIp($ip_address);
        $user_currency = $user_location['currency'];

        // Is new user.
        $is_new_user = true;

        // User has not passed any currency and location has no currency.
        if (empty($currency) === true && empty($user_currency) === true) {
            $currency = DEFAULT_CURRENCY;
        }

        // IMPORTANT: Setting default currency to INR till we support other currecny Payment.
        $currency = DEFAULT_CURRENCY;

        // Check source type (website, facebook or google).
        switch ($source) {
            case WEBSITE_SOURCE_ID:
                // Website signup.
                // Get user data.
                $user = User::where('email', $email)->withTrashed()->first();

                // User email already exist.
                if (false === empty($user)) {
                    return ApiResponse::forbiddenError(EC_DUPLICATE_USER, ERROR_CODE_EMAIL_ALREADY_REGISTERED);
                }

                // New user instance.
                $user = new User;

                $user->password        = Hash::make($password);
                $user->name            = $name;
                $user->last_name       = $lastname;
                $user->gender          = $gender;
                $user->wallet_currency = $user_currency;
                $user->signup_method   = 'email';
                // Email, facebook, google.
                $user->profile_img = '';

                // Set base currency if passed.
                if (false === empty($currency)) {
                    $user->base_currency = $currency;
                }

                $source_id = WEBSITE_SOURCE_ID;
            break;

            case GOOGLE_SOURCE_ID:
                // Google signup.
                $payload = Helper::getGoogleSignUpProfile($access_token, $header_device_type);

                if (true === empty($payload)) {
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
                if (true === empty($user)) {
                    // New user instance.
                    $user = new User;

                    $user->password      = Hash::make($generated_password);
                    $user->name          = $g_name;
                    $user->last_name     = $g_lastname;
                    $user->signup_method = 'google';
                    // Email, facebook, google.
                    $user->profile_img     = '';
                    $user->wallet_currency = '';
                    $user->base_currency   = '';
                } else {
                    $user->activateIfTrashed();
                    $is_new_user = false;
                }//end if

                // Set google id.
                $user->google_id = $g_id;

                // Set email verify for gmail login case.
                if (true === empty($user->email_verify)) {
                    $user->email_verify = 1;
                }

                // Set currency.
                if (true === empty($user->wallet_currency) && false === empty($currency)) {
                    $user->wallet_currency = $currency;
                }

                if (true === empty($user->base_currency) && false === empty($currency)) {
                    $user->base_currency = $currency;
                }

                if (true === empty($user->profile_img) && false === empty($g_picture)) {
                    $user->profile_img = User::uploadImageToS3FromUrl($g_picture);

                    if (true === empty($user->profile_img)) {
                        return ApiResponse::badGatewayError(EC_BAD_GATEWAY, 'Error while fetching profile image.');
                    }
                }

                $source_id       = GOOGLE_SOURCE_ID;
                $random_password = $generated_password;
            break;

            case FACEBOOK_SOURCE_ID:
                // Facebook signup.
                $payload = Helper::getFacebookSignUpProfile($access_token);

                if (true === empty($payload)) {
                    // Invaid access token.
                    return ApiResponse::badRequestError(EC_INVALID_ACCESS_TOKEN, 'Invalid access token.');
                }

                // Facebook fetched data.
                $fb_id         = $payload['id'];
                $fb_email      = $payload['email'];
                $fb_first_name = $payload['name'];
                $fb_last_name  = $payload['last_name'];
                $fb_birthday   = $payload['birthday'];
                $fb_currency   = (false === empty($payload['currency'])) ? $payload['currency'] : $currency;
                $fb_picture    = $payload['profile_img'];
                $fb_gender     = (false === empty($payload['gender'])) ? $payload['gender'] : $gender;

                // User email is there.
                if (false === empty($fb_email)) {
                    $email = $fb_email;
                } else if (true === empty($email)) {
                    $email = $fb_id.'@facebook.com';
                }

                // User currency is not given but facebook currency is available.
                if (true === empty($currency) && false === empty($fb_currency)) {
                    $currency = $fb_currency;
                }

                // Get user data via fb_id.
                $user = User::select('id', 'auth_key', 'base_currency', 'wallet_currency', 'profile_img', 'gender', 'deleted_at')->where('fb_id', $fb_id)->withTrashed()->first();

                if (true === empty($user)) {
                    // Get user data through email.
                    $user = User::select('id', 'email', 'profile_img', 'auth_key', 'base_currency', 'wallet_currency')->where('email', $email)->withTrashed()->first();

                    if (true === empty($user)) {
                        // Create new user.
                        $user = new User;

                        $user->password      = Hash::make($generated_password);
                        $user->name          = $fb_first_name;
                        $user->last_name     = $fb_last_name;
                        $user->dob           = $fb_birthday;
                        $user->signup_method = 'facebook';
                        // Email, facebook, google, apple.
                        $user->profile_img     = '';
                        $user->wallet_currency = '';
                        $user->base_currency   = '';
                    } else {
                        // Not a new user.
                        $user->activateIfTrashed();
                        $is_new_user = false;
                    }
                } else {
                    // Not a new user.
                    $user->activateIfTrashed();
                    $is_new_user = false;
                }//end if

                $user->fb_id  = $fb_id;
                $user->gender = $fb_gender;

                // Set currency.
                if (true === empty($user->wallet_currency) && false === empty($currency)) {
                    $user->wallet_currency = $currency;
                }

                if (true === empty($user->base_currency) && false === empty($currency)) {
                    $user->base_currency = $currency;
                }

                if (true === empty($user->profile_img) && false === empty($fb_picture)) {
                    $user->profile_img = User::uploadImageToS3FromUrl($fb_picture);

                    if (true === empty($user->profile_img)) {
                        return ApiResponse::badGatewayError(EC_BAD_GATEWAY, 'Error while fetching profile image.');
                    }
                }

                $source_id       = FACEBOOK_SOURCE_ID;
                $random_password = $generated_password;
            break;

            case APPLE_SOURCE_ID:
                // Get user data via apple id token.
                $payload = Helper::getAppleSignUpProfile($access_token);
                if (true === empty($payload)) {
                    // Invalid id token.
                    return ApiResponse::badRequestError(EC_INVALID_ID_TOKEN, 'Invalid id token.');
                }

                $apple_id = $payload['id'];
                $email    = $payload['email'];

                $user = User::select('id', 'auth_key', 'base_currency', 'wallet_currency', 'profile_img', 'gender', 'deleted_at')->where('apple_id', $apple_id)->withTrashed()->first();

                if (true === empty($user)) {
                    // Create new user.
                    $user = new User;

                    $user->password      = Hash::make($generated_password);
                    $user->name          = $name;
                    $user->last_name     = $lastname;
                    $user->signup_method = 'apple';
                    // Email, facebook, google, apple.
                    $user->profile_img     = '';
                    $user->wallet_currency = '';
                    $user->base_currency   = '';
                    $user->gender          = $gender;
                    $user->apple_id        = $apple_id;
                } else {
                    // Not a new user.
                    $user->activateIfTrashed();
                    $is_new_user = false;
                }//end if

                if (true === empty($user->wallet_currency) && false === empty($currency)) {
                    $user->wallet_currency = $currency;
                }

                if (true === empty($user->base_currency) && false === empty($currency)) {
                    $user->base_currency = $currency;
                }

                $source_id       = APPLE_SOURCE_ID;
                $random_password = $generated_password;
            break;

            default:
                // Invalid source code.
            return ApiResponse::validationFailed(['source' => 'The source field is invalid.']);
            // phpcs:ignore
            break;
        }//end switch

        // For new user only.
        if (true === $is_new_user) {
            $user->email = $email;
        }

        // Create/update user.
        if (true === $user->save()) {
            // Set auth key if empty.
            if (true === empty($user->auth_key)) {
                $user->auth_key = $auth_token;
            }

            // For new user only.
            if (true === $is_new_user) {
                $user->country           = $user_location['country_code'];
                $user->state             = $user_location['state_name'];
                $user->city              = $user_location['city'];
                $user->confirmation_code = $confirmation_code;
                $user->signup_source     = $device_type;
                $user->ip_address        = $ip_address;

                // If user has entered referral code.
                if (false === empty($referral_code)) {
                    // Get friend by referral code.
                    $friend = User::getUserByReferralCode($referral_code);
                    if (false === empty($friend)) {
                        $user->referral_by = $friend->id;
                    }
                }

                // Generate user referral code using user id.
                $generated_referral_code = Helper::generateReferralCode($user->id);
                $user->referral_code     = $generated_referral_code;
                $user->save();

                // Send user registration email.
                $user_registered_event = new UserRegistered($user, $source_id, $random_password);
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

        if (true === $is_new_user) {
            // New user created.
            $content['message'] = 'User created successfully.';

            $response = new PostUserResponse($content);
            $response = $response->toArray();
            return ApiResponse::create($response);
        }

        // User updated.
        $content['message'] = 'User updated successfully.';

        $response = new PostUserResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postRegister()


    /**
     * Send Otp to user
     *
     * @param \App\Http\PostVerifiyMobileRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/user/verify/mobile",
     *     tags={"User"},
     *     description="Returns success/error message based on otp being sent or not.",
     *     operationId="user.post.verify.mobile",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_number_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_method_in_form"),
     * @SWG\Parameter(ref="#/parameters/referral_code_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Sucess Message of otp sent and popup for verify otp.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                   ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                     ref="#definitions/PostUserVerifyMobileResponse"),
     * @SWG\Property(property="error",                                    ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     *         description="Your previous contact is same as this contact. || Sorry! The number is already registered with us.Please try a different number.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while generating otp. Please try after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=502,
     *         description="There was some error while sending otp. Please try after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postSendOtp(PostVerifiyMobileRequest $request)
    {
        $input_params = $request->input();

        $dialcode = $input_params['dial_code'];
        $phone    = $input_params['contact_number'];
        // 0 means sms 1 means call.
        $otp_method = $input_params['otp_method'];

        $check_if_number_exists = false;
        // Logged in user.
        $user_id        = $request->getLoginUserId();
        $contact_detail = User::getUserDataById($user_id, ['dial_code', 'contact', 'mobile_verify', 'referral_by']);

        // phpcs:ignore Squiz.Operators.ComparisonOperatorUsage.NotAllowed
        if (empty($input_params['referral_code']) === false) {
            $referral_code = $input_params['referral_code'];

            $friend = User::getUserByReferralCode($referral_code);

            if (empty($friend) === true || $friend->id === $user_id) {
                return ApiResponse::validationFailed(['referral_code' => 'Invalid Referral Code']);
            }

            // phpcs:ignore
            if ($contact_detail->referral_by !== '' && $contact_detail->referral_by != $friend->id) {
                return ApiResponse::validationFailed(['referral_code' => 'User is already Refered by other user']);
            }

            User::updateUserDetails($user_id, ['referral_by' => $friend->id ]);
        }

        // If user is trying to update mobile number but adding same as the previous one.
        if ($contact_detail->contact === (string) $phone && (int) $contact_detail->mobile_verify === 1) {
            return ApiResponse::forbiddenError(EC_CONTACT_UNCHANGED, 'Your previous contact is same as this contact');
        }

        // Check if any other user has the same mobile number and number is verified by that user.
        $check_if_number_exists = User::checkIfAnyOtherUserHasSameNumber($user_id, $phone);

        // If contact number does exist in db.
        if ($check_if_number_exists === true) {
            return ApiResponse::forbiddenError(EC_DUPLICATE_CONTACT, 'Sorry! The number is already registered with us. Please try a different number.');
        }

        // Save contact detail provided by user.
        if (empty($contact_detail->contact) === true || (int) $contact_detail->mobile_verify === 0) {
            $update_user_detail = User::updateUserContactDetail($user_id, $dialcode, $phone);
        }

        // Check if a valid otp exists for user.
        $check_if_valid_otp_exists = OtpContact::checkIfValidOtpExists($user_id, $dialcode, $phone);

        // Get valid otp for user.
        if (empty($check_if_valid_otp_exists) === false) {
            $otp_to_send = OtpContact::getExistingOtpForUser($user_id, $dialcode, $phone);
        } else {
            $otp_to_send = OtpContact::generateValidOtpForUser($user_id, $dialcode, $phone);
        }

        if (empty($otp_to_send) === true) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while generating otp. Please try after some time.');
        }

        $view = view('sms.mobile_verification', ['verification_code' => $otp_to_send]);
        $msg  = $view->render();

        $contact      = $dialcode.$phone;
        $response_msg = 'Verification code has been successfully sent to '.$phone;

        switch ($otp_method) {
            case 1:
                // Via sms.
                $result = SmsService::sendOtp($contact, $msg, VERIFY_SMS_SENDER_ID);
                Helper::logInfo('PROCESS SMS : Send otp sms via SmsService. Contact <<<<<<<<<<< '.$contact.' >>>>>>>>>>>>');
            break;

            case 2:
                // Via call.
                $response_msg = 'You will receive a call shortly on '.$phone;
                $result       = SmsService::call($contact, $otp_to_send);
                 Helper::logInfo('PROCESS CALL : Otp via call in SmsService. Contact <<<<<<<<<<< '.$contact.' >>>>>>>>>>>>');
            break;

            default:
            return ApiResponse::badRequestError(EC_INVALID_OTP_METHOD, 'Please select an otp method.');
        }

        if ($result['status'] === 1) {
            $content = [
                'dial_code'      => $dialcode,
                'contact'        => $phone,
                'sms_sender_ids' => SMS_SENDER_IDS,
                'message'        => $response_msg,
            ];

            $response = new PostUserVerifyMobileResponse($content);
            $response = $response->toArray();
            return ApiResponse::success($response);
        } else {
            Helper::logError($result['message']);
            return ApiResponse::serviceUnavailableError(EC_SERVICE_UNAVIALABLE, 'There was some error while sending otp. Please try after some time.');
        }

    }//end postSendOtp()


    /**
     * Verify otp sent to user
     *
     * @param \App\Http\PutVerifyMobileRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user/verify/mobile#",
     *     tags={"User"},
     *     description="verify otp code added by user for its validity and make user contact verified.",
     *     operationId="user.put.verify.mobile",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_number_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_code_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Contact verified successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutUserVerifyMobileResponse"),
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
     *         description="Your OTP has expired. || Invalid OTP entered.",
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
    public function putVerifyOtp(PutVerifyMobileRequest $request)
    {
        $input_params = $request->input();

        $otp                    = $input_params['otp_code'];
        $phone                  = $input_params['contact_number'];
        $dialcode               = $input_params['dial_code'];
        $check_if_number_exists = false;

        $user_id = $request->getLoginUserId();

        // Check if any other user has the same mobile number and number is verified by that user.
        $check_if_number_exists = User::checkIfAnyOtherUserHasSameNumber($user_id, $phone);

        // If contact number does exist in db.
        if ($check_if_number_exists === true) {
            return ApiResponse::forbiddenError(EC_DUPLICATE_CONTACT, 'Sorry! The number is already registered with us. Please try a different number.');
        }

        // Validate if provided otp matches one in db.
        $check_if_otp_matches_user = OtpContact::checkForOtpMatch($user_id, $otp, $dialcode, $phone);

        if ($check_if_otp_matches_user['status'] === 1) {
            $mobile_verify      = 1;
            $update_user_detail = User::updateUserContactDetail($user_id, $dialcode, $phone, $mobile_verify);

            if ($update_user_detail === false) {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while verifying the otp. Please try after some time.');
            }

            // Delete otp details if user verified successfully.
            OtpContact::deletePreviousOtps($dialcode, $phone);

            // GIVE WALLET MONEY.
            $user = User::find($user_id);
            if (empty($user->referral_by) === false) {
                WalletTransaction::creditWalletMoneyOnReferralSignUp($user);
            }

            $content = [
                'dial_code'      => $dialcode,
                'contact_number' => $phone,
                'message'        => $check_if_otp_matches_user['message'],
            ];

            $response = new PutUserVerifyMobileResponse($content);
            $response = $response->toArray();
            return ApiResponse::success($response);
        } else {
            return ApiResponse::forbiddenError(EC_INVALID_OTP, $check_if_otp_matches_user['message']);
        }//end if

    }//end putVerifyOtp()


    /**
     * Add Property to wishlist
     *
     * @param \App\Http\PostAddWishlistRequest $request          Http request object.
     * @param string                           $property_hash_id Property hash id.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/user/wishlist/{property_hash_id}",
     *     tags={"Property"},
     *     description="Returns success code and message if property successfully added to wishlist.",
     *     operationId="user.post.wishlist",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
     * @SWG\Response(
     *         response=200,
     *         description="Property added to wishlist.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostUserWishlistResponse"),
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
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while adding property to wishlist.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function postAddToWishlist(PostAddWishlistRequest $request, string $property_hash_id)
    {
        $user_id = $request->getLoginUserId();

        // Decode property_id from the hash id visible in url.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        // Check if property exists in database.
        $check_property_exists = Property::checkIfPropertyExistsById($property_id);

        if ($check_property_exists === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Check if property already in wishlist.
        $check_if_property_is_in_wishlist = MyFavourite::getUserWishlistedPropertiesFromPropertyIds($user_id, [$property_id]);

        if (count($check_if_property_is_in_wishlist) === 0) {
            $add_to_user_wishlist = MyFavourite::addPropertyToUserWishlist($user_id, $property_id);
            if ($add_to_user_wishlist === false) {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while adding property to wishlist.');
            }

            $already_in_wishlist = 0;
            $message             = 'Property added to wishlist';
        } else {
            $already_in_wishlist = 1;
            $message             = 'Property already in wishlist';
        }

        // Standard response.
        $data = [
            'property_hash_id'       => $property_hash_id,
            'is_already_in_wishlist' => $already_in_wishlist,
            'message'                => $message,
        ];

        $response = new PostUserWishlistResponse($data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postAddToWishlist()


    /**
     * Delete Property from wishlist
     *
     * @param \App\Http\DeleteUserWishlistRequest $request          Http request object.
     * @param string                              $property_hash_id Property hash id.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Delete(
     *     path="/v1.6/user/wishlist/{property_hash_id}",
     *     tags={"Property"},
     *     description="Returns success code and message if property successfully removed from wishlist.",
     *     operationId="user.delete.wishlist",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/property_hash_id_in_path"),
     * @SWG\Response(
     *         response=200,
     *         description="Property successfully removed from wishlist.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/DeleteUserWishlistResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters."
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Property is not present in wishlist.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Property not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Error removing property from wishlist.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function deleteFromWishlist(DeleteUserWishlistRequest $request, string $property_hash_id)
    {
        $user_id = $request->getLoginUserId();

        // Decode property_id from the hash id visible in url.
        $property_id = $request->decodePropertyIdOrFail($property_hash_id);

        // Check if property exists in database.
        $check_property_exists = Property::checkIfPropertyExistsById($property_id);

        if ($check_property_exists === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Property not found.');
        }

        // Check if property exists in wishlist.
        $check_if_property_is_in_wishlist = MyFavourite::getUserWishlistedPropertiesFromPropertyIds($user_id, [$property_id]);

        if (count($check_if_property_is_in_wishlist) === 0) {
            return ApiResponse::forbiddenError(EC_PROPERTY_NOT_IN_WISHLIST, 'Property is not present in wishlist.');
        } else {
            if (MyFavourite::removePropertyFromUserWishlist($user_id, $property_id) > 0) {
                $data = [
                    'property_hash_id' => $property_hash_id,
                    'message'          => 'Property successfully removed from wishlist.',
                ];

                $response = new DeleteUserWishlistResponse($data);
                $response = $response->toArray();
                return ApiResponse::success($response);
            } else {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'Error removing property from wishlist.');
            }
        }

    }//end deleteFromWishlist()


    /**
     * Get all properties in wishlist
     *
     * @param \App\Http\GetUserWishlistRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\GET(
     *     path="/v1.6/user/wishlist",
     *     tags={"Property"},
     *     description="Returns an array containing wishlisted properties and their details. If no wishlisted property, returns blank array.",
     *     operationId="user.get.wishlist",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="List of wishlisted properties.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetUserWishlistResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Unauthorized action.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function getWishlist(GetUserWishlistRequest $request)
    {
        // Validate params.
        $input_params = $request->input();

        $offset = $input_params['offset'];
        $total  = $input_params['total'];

        $user_id = $request->getLoginUserId();

        // Paramters to fetch from user table.
        $get_params = ['base_currency'];

        // Fetch user data.
        $user = User::getUserDataById($user_id, $get_params);

        // Base currency.
        $currency = User::getCommonCurrency($user['base_currency']);

        // Output array.
        $properties = ['wishlist' => []];

        // Guests and bedrooms.
        $guests   = DEFAULT_NUMBER_OF_GUESTS;
        $bedrooms = DEFAULT_NUMBER_OF_UNITS;

        // Get all headers.
        $headers = $request->getAllHeaders();

        // Wishlisted properties of a user.
        $favourite_list = MyFavourite::getWishlistedPropertiesOfUser($user_id, $guests, $bedrooms, $offset, $total);

        // No property in wishlist.
        if (count($favourite_list) === 0) {
            return ApiResponse::success(['wishlist' => []]);
        }

        // Get wishlisted property ids array.
        $wishlisted_property_ids = array_unique(array_column($favourite_list, 'id'));

        // Get user's default selected currency.
        $default_currency_code = User::getUserBaseCurrency($user_id);

        // Get first property image to display.
        $properties_images = PropertyImage::getPropertiesImagesByIds($wishlisted_property_ids, $headers, 1);

        // Get property videos.
        $properties_videos = PropertyVideo::getPropertyVideosByPropertyIds($wishlisted_property_ids);

        // Get first property tag associated with property (if any).
        $properties_tags = PropertyTagMapping::getPropertyTagsWithColorCodingByPropertyIds($wishlisted_property_ids, 1);

        // Get associative array of all currency conversions.
        $currency_rates = CurrencyConversion::getAllCurrencyDetails();

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        // Cancellation policies.
        $cancellation_policy_ids = array_unique(array_column($favourite_list, 'cancelation_policy'));

        // Get cancellation policy data.
        $cancellation_policy_data = CancellationPolicy::getCancellationPoliciesByIds($cancellation_policy_ids);

        // Loop through wishlisted properties.
        foreach ($favourite_list as $favourite) {
            // Array of data to process and get property pricing.
            $property_pricing_data = [
                'property_id'            => $favourite['id'],
                'start_date'             => '',
                'end_date'               => '',
                'units'                  => $bedrooms,
                'guests'                 => $guests,
                'user_currency'          => $default_currency_code,
                'property_currency'      => $favourite['currency'],
                'per_night_price'        => $favourite['per_night_price'],
                'additional_guest_fee'   => $favourite['additional_guest_fee'],
                'cleaning_fee'           => $favourite['cleaning_fee'],
                'cleaning_mode'          => $favourite['cleaning_mode'],
                'service_fee'            => $favourite['service_fee'],
                'custom_discount'        => $favourite['custom_discount'],
                'fake_discount'          => $favourite['fake_discount'],
                'accomodation'           => $favourite['accomodation'],
                'additional_guest_count' => $favourite['additional_guest_count'],
                'property_units'         => $favourite['units'],
                'instant_book'           => $favourite['instant_book'],
                'gh_commission'          => (int) $favourite['gh_commission'],
                'markup_service_fee'     => (int) $favourite['markup_service_fee'],
                'min_nights'             => $favourite['min_nights'],
                'max_nights'             => $favourite['max_nights'],
                'prive'                  => $favourite['prive'],
                'room_type'              => $favourite['room_type'],
                'bedrooms'               => $favourite['bedrooms'],
                'user'                   => $user,
                'error'                  => [],
            ];

            // Get property pricing details.
            $property_pricing = PropertyPricingService::getPropertyPrice($property_pricing_data);

            $gender     = (empty($favourite['host_gender']) === false) ? $favourite['host_gender'] : 'Male';
            $host_image = (empty($favourite['host_image']) === false) ? $favourite['host_image'] : '';
            $host_image = Helper::generateProfileImageUrl($gender, $host_image, $favourite['host_id']);

            // Get desired structure.
            $one_organized_property_data = PropertyTileService::getPropertytileStructure(
                [
                    'property_id'           => $favourite['id'],
                    'property_score'        => round($favourite['property_score'], 1),
                    'property_type_name'    => $favourite['property_type_name'],
                    'room_type'             => $favourite['room_type'],
                    'room_type_name'        => $favourite['room_type_name'],
                    'area'                  => $favourite['area'],
                    'city'                  => $favourite['city'],
                    'state'                 => $favourite['state'],
                    'country'               => $favourite['country'],
                    'country_codes'         => $country_codes,
                    'latitude'              => $favourite['latitude'],
                    'longitude'             => $favourite['longitude'],
                    'accomodation'          => $favourite['accomodation'],
                    'currency'              => $favourite['currency'],
                    'is_liked_by_user'      => 1,
                    'display_discount'      => $property_pricing['effective_discount_percentage'],
                    'price_after_discount'  => $property_pricing['per_night_per_unit_price_without_service_fee'],
                    'price_before_discount' => (($property_pricing['per_night_per_unit_price_without_service_fee'] * 100) / (100 - $property_pricing['effective_discount_percentage'])),
                    'instant_book'          => 1,
                    'cash_on_arrival'       => 1,
                    'bedrooms'              => $favourite['bedrooms'],
                    'units_consumed'        => $favourite['units_consumed'],
                    'title'                 => $favourite['title'],
                    'properties_images'     => $properties_images,
                    'properties_videos'     => $properties_videos,
                    'properties_tags'       => $properties_tags,
                    'host_name'             => $favourite['host_name'],
                    'host_image'            => $host_image,
                ]
            );

            $properties['wishlist'][] = $one_organized_property_data;
        }//end foreach

        $response = new GetUserWishlistResponse($properties);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getWishlist()


    /**
     * Reset password
     *
     * @param \App\Http\PostUserPasswordResetRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/user/password/reset",
     *     tags={"User"},
     *     description="Returns an array containing flags to check if email and sms are sent or not. Also returns error/success message.",
     *     operationId="user.post.password.reset",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/reset_password_via_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Link Sent on registered Email id. || Otp sent to your registered number as well as reset password link to your email id.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostUserPasswordResetResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Invalid contact number length. || Invalid contact number. || Mobile number not verified. Please login using email! || Invalid email.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="You have reached maximum otp limit. Please reset through link send in email!",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Contact not found. || Email not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="Error while generating reset password token. || A reset password link has been sent to registered email. But there was some error while generating otp for mobile.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=502,
     *         description="Error while sending the reset password mail. || Error while sending the reset password otp sms.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postResetPassword(PostUserPasswordResetRequest $request)
    {
        $input_params = $request->input();

        $reset_password_via = $input_params['reset_password_via'];

        // Check if entered value is a number (i.e. contact number).
        if (is_numeric($reset_password_via) === true) {
            $mobile = $reset_password_via;

            $check_if_user_exists_by_mobile = User::getUserByMobileNumber($mobile);

            // If no user exists with given number.
            if (count($check_if_user_exists_by_mobile) === 0) {
                // Return ApiResponse::error(array('email_sent' => 0, 'sms_sent' => 0, 'message' => ERROR_CODE_INVALID_MOBILE));.
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Contact not found.');
            } else {
                $user = $check_if_user_exists_by_mobile[0];

                // If more than 1 user has same mobile number and no user is verified.
                if (count($check_if_user_exists_by_mobile) > 1 && (int) $user->mobile_verify === 0) {
                    // Return ApiResponse::error(array('email_sent' => 0, 'sms_sent' => 0, 'message' => ERROR_CODE_MOBILE_NOT_VERIFIED));.
                    return ApiResponse::forbiddenError(EC_CONTACT_NOT_VERIFIED, 'Mobile number is not verified.');
                }
            }
        } else {
            // Entered value is not numeric (i.e. email).
            $email = $reset_password_via;
            // Get user by email id.
            $user = User::getUserByEmail($email);

            if (empty($user) === true) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Email is not registered with us. Kindly use the registered account.');
            }
        }//end if

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

        // Reset password link.
        $reset_link = RESET_PASSWORD_LINK.$reset_password_token;

        $content = [];

        // If user doesn't have any contact number.
        if (empty($user->contact) === true) {
            $content = [
                'email_sent'     => 1,
                'sms_sent'       => 0,
                'contact'        => '',
                'sms_sender_ids' => '',
                'message'        => 'Link sent on registered email id.',
            ];
        }

        // Check if daily and hourly otp limit has been reached.
        $check_if_otp_limit_reached = OtpContact::isOtpLimitReached($user->contact, $user->id);

        if ($check_if_otp_limit_reached === true) {
            $content = [
                'email_sent'     => 1,
                'sms_sent'       => 0,
                'contact'        => '',
                'sms_sender_ids' => '',
                'message'        => ERROR_CODE_OTP_LIMIT,
            ];
        }

        // Generate a new otp code.
        $otp_code = OtpContact::generateValidOtpForUser($user->id, $user->dial_code, $user->contact, 0);
        if (empty($otp_code) === true) {
            $content = [
                'email_sent'     => 1,
                'sms_sent'       => 0,
                'contact'        => '',
                'sms_sender_ids' => '',
                'message'        => 'Link sent on registered email id. But there was some error while generating otp for mobile.',
            ];
        }

        if (empty($content) === false) {
            // Send user Reset password email.
            $user_reset_password_event = new UserResetPassword($user->email, $reset_password_token);
            Event::dispatch($user_reset_password_event);

            $response = new PostUserPasswordResetResponse($content);
            $response = $response->toArray();
            return ApiResponse::success($response);
        }

        // Send user Reset password email.
        $user_reset_password_event = new UserResetPassword($user->email, $reset_password_token, $user->dial_code, $user->contact, $otp_code);
        Event::dispatch($user_reset_password_event);

        $content = [
            'email_sent'     => 1,
            'sms_sent'       => 1,
            'contact'        => $user->contact,
            'sms_sender_ids' => SMS_SENDER_IDS,
            'message'        => 'Otp sent to your registered number as well as reset password link to your email id.',
        ];

        $response = new PostUserPasswordResetResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postResetPassword()


    /**
     * Update new password via sms
     *
     * @param \App\Http\PutUserPasswordRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user/password",
     *     tags={"User"},
     *     description="Returns success message when password updated successfully.",
     *     operationId="user.put.password",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/reset_password_via_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Password changed successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                  ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                    ref="#definitions/PutUserPasswordResponse"),
     * @SWG\Property(property="error",                                   ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || New password should be of minimum 6 characters. || Invalid verification code.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Invalid contact number.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while updating password. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putUpdatePasswordViaOtp(PutUserPasswordRequest $request)
    {
        $input_params = $request->input();

        $reset_password_via = $input_params['reset_password_via'];
        $otp_code           = $input_params['otp_code'];
        $password           = $input_params['password'];

        // Check if entered value is a number (i.e. contact number).
        if (is_numeric($reset_password_via) === true) {
            $mobile = $reset_password_via;

            $check_if_user_exists_by_mobile = User::getUserByMobileNumber($mobile);

            // If no user exists with given number.
            if (count($check_if_user_exists_by_mobile) === 0) {
                // Return ApiResponse::error(array('email_sent' => 0, 'sms_sent' => 0, 'message' => ERROR_CODE_INVALID_MOBILE));.
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Contact not found.');
            } else {
                $user = $check_if_user_exists_by_mobile[0];

                // If more than 1 user has same mobile number and no user is verified.
                if (count($check_if_user_exists_by_mobile) > 1 && (int) $user->mobile_verify === 0) {
                    // Return ApiResponse::error(array('email_sent' => 0, 'sms_sent' => 0, 'message' => ERROR_CODE_MOBILE_NOT_VERIFIED));.
                    return ApiResponse::forbiddenError(EC_CONTACT_NOT_VERIFIED, 'Mobile number is not verified.');
                }
            }
        } else {
            // Entered value is not numeric (i.e. email).
            $email = $reset_password_via;

            // Get user by email id.
            $user = User::getUserByEmail($email);

            if (empty($user) === true) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, 'Email not found.');
            }
        }//end if

        // Get last sent otp to user.
        $user_otp = OtpContact::getExistingOtpForUser($user->id, $user->dial_code, $user->contact);

        // User has different code assigned to him.
        if ((int) $user_otp !== $otp_code) {
            return ApiResponse::forbiddenError(EC_INVALID_OTP, ERROR_CODE_INVALID_OTP);
        }

        // Update user password.
        $is_password_updated = User::updateUserDetails($user->id, ['password' => Hash::make($password)]);

        // If password not updated.
        if ($is_password_updated === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while updating password. Please try again.');
        }

        if ((int) $user->mobile_verify !== 1) {
            // Save user contact as verified.
            $verify_contact = User::updateUserContactDetail($user->id, $user->dial_code, $user->contact, 1);
        }

        // Delete reset password tokens (sent on email).
        $delete_reset_password_token = PasswordReminder::deletePasswordToken($user->email);

        $response = new PutUserPasswordResponse(['message' => 'Password changed successfully.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putUpdatePasswordViaOtp()


    /**
     * Check if reset password token is valid or not
     *
     * @param \Illuminate\Http\Request $request    Http request object.
     * @param string                   $token_hash Token hash.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/user/password/reset/{token_hash}",
     *     tags={"User"},
     *     description="Returns a boolean to represent if a valid token exists or not.",
     *     operationId="user.get.password",
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/reset_password_token_hash_in_path"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns true if reset password token exists, false if it doesn't.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                      ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                        ref="#definitions/GetUserPasswordResetCheckResponse"),
     * @SWG\Property(property="error",                                       ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * )
     */
    public function getCheckIfValidResetPasswordTokenExists(Request $request, string $token_hash)
    {
        // Check if token entered is valid.
        $check_if_token_exists = PasswordReminder::getEmailByToken($token_hash);

        $return_data = ['is_valid' => 0];
        if (empty($check_if_token_exists) === false) {
            $return_data = ['is_valid' => 1];
        }

        $response = new GetUserPasswordResetCheckResponse($return_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getCheckIfValidResetPasswordTokenExists()


    /**
     * Update new password via email
     *
     * @param \App\Http\PutUserPasswordResetRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user/password/reset",
     *     tags={"User"},
     *     description="Returns success message when password updated successfully.",
     *     operationId="user.put.password.reset",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/reset_password_token_hash_in_form"),
     * @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Password changed successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                      ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                        ref="#definitions/PutUserPasswordResetResponse"),
     * @SWG\Property(property="error",                                       ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || New password should be of minimum 6 characters.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Invalid reset token! || Email id not registered.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while updating password. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putUpdatePasswordViaEmail(PutUserPasswordResetRequest $request)
    {
        $input_params = $request->input();

        $token    = $input_params['token_hash'];
        $password = $input_params['password'];

        // Get user email for given token.
        $user_email = PasswordReminder::getEmailByToken($token);

        // No email for given token.
        if (empty($user_email) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, ERROR_CODE_INVALID_RESET_PASSWORD_TOKEN);
        }

        $user = User::getUserByEmail($user_email);

        // No user exists for given token.
        if (empty($user) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, ERROR_CODE_EMAIL_NOT_REGISTERED);
        }

        // Update user password.
        $update_password = User::updateUserDetails($user->id, ['password' => Hash::make($password)]);

        // If password not updated.
        if ($update_password === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while updating password. Please try again.');
        }

        // Delete reset password tokens (sent on email).
        $delete_reset_password_token = PasswordReminder::deletePasswordToken($user->email);

        $response = new PutUserPasswordResetResponse(['message' => 'Password changed successfully.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putUpdatePasswordViaEmail()


    /**
     * Get User profile data
     *
     * @param \App\Http\GetUserProfileRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/user",
     *     tags={"User"},
     *     description="get user's profile data.",
     *     operationId="user.get.data",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/user_id_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing user profile data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetUserResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Auth token/User id missing.",
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
     *         description="User not found.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function getUser(GetUserProfileRequest $request)
    {
        $input_params = $request->input();
        // For sending auth key only own profile.
        $send_auth_key = false;
        $id            = $request->getLoginUserId();

        // If not user id is passed in query.
        if (isset($input_params['user_id']) === false) {
            if ($this->isUserLoggedIn() !== false) {
                // If some user is logged in.
                $user_id = $id;
                // User whose profile to fetch.
                $traveller_id = $id;
                // User who is looking at profile.
                $send_auth_key = true;
            } else {
                return ApiResponse::badRequestError(EC_AUTH_TOKEN_MISSING, 'Auth token/User id missing.');
            }
        } else {
            $user_hash_id = $input_params['user_id'];

            $user_id = $request->decodeUserIdOrFail($user_hash_id);

            if ($this->isUserLoggedIn() !== false) {
                $traveller_id = $id;

                if ((int) $user_id === $traveller_id) {
                    $send_auth_key = true;
                }
            } else {
                $traveller_id = 0;
            }
        }//end if

        // Get all headers.
        $headers = $request->getAllHeaders();

        $user_id      = (int) $user_id;
        $traveller_id = $traveller_id;
        // User profile data.
        $profile_data = User::getUserProfile($user_id, $send_auth_key);

        if (empty($profile_data) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'User not found.');
        }

        if ($user_id !== $traveller_id) {
            $profile_data['last_name'] = '';
        }

        // Paramters to fetch from user table.
        $get_params = ['base_currency'];
        if ($traveller_id !== 0) {
            // Fetch user data.
            $traveller_currency = User::getUserDataById($traveller_id, $get_params);
            // Traveller currency.
            $traveller_currency = ($traveller_currency->base_currency === '') ? DEFAULT_CURRENCY : User::getCommonCurrency($traveller_currency->base_currency);
        } else {
            $traveller_currency = $profile_data['user_currency'];
        }

        if (empty($profile_data) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'User not found.');
        }

         // Get user property listings.
        $profile_data['property_listings'] = $this->getUserListings($user_id, $traveller_currency, $headers, $traveller_id);

        $response = new GetUserResponse($profile_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getUser()


    /**
     * Update User profile data
     *
     * @param \App\Http\PutUpdateUserRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user",
     *     tags={"User"},
     *     description="update user's profile data.",
     *     operationId="user.put.data",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/user_first_name_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_last_name_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_gender_in_form"),
     * @SWG\Parameter(ref="#/parameters/dob_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/marital_status_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/profession_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/spoken_languages_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/travelled_places_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/description_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/fb_access_token_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User details updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                      ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                        ref="#definitions/PutUserResponse"),
     * @SWG\Property(property="error",                                       ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     *         description="There was some error while updating user details. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putUpdateUser(PutUpdateUserRequest $request)
    {
        $user_id      = $request->getLoginUserId();
        $input_params = $request->input();

        // User paramter keys that can be updated.
        $key_params = [
            'first_name',
            'last_name',
            'dob',
            'marital_status',
            'gender',
            'profession',
            'spoken_languages',
            'travelled_places',
            'description',
            'fb_access_token',
        ];
         // Get all headers.
        $headers = $request->getAllHeaders();

        // Fetch data to be updated as associative array.
        $user_data = User::getInputParamsAsAssociativeArray($input_params, $key_params);

        if (empty($user_data['fb_access_token']) === false) {
            $access_token = $user_data['fb_access_token'];
            // Fetch user data using access token.
            $fb_data = Helper::sendCurlRequest('https://graph.facebook.com/me?access_token='.$access_token.'&fields=id');

            $fb_data = json_decode($fb_data, true);
            // Fetched fb id.
            if (isset($fb_data['id']) === false) {
                // Invaid access token.
                 return ApiResponse::validationFailed(['fb_access_token' => 'Invalid Access Token']);
            }

            $fb_id = $fb_data['id'];
            // Get user data via fb_id.
            $user = User::select('id')->where('fb_id', $fb_id)->withTrashed()->first();

            if (empty($user) === false && $user->id !== $user_id) {
                return ApiResponse::forbiddenError(EC_UNAUTHORIZED, 'Facebook Account already attached to some other user');
            }

            $user_data['fb_id'] = $fb_id;
        }//end if

        unset($user_data['fb_access_token']);

        // Parse dob in correct format.
        if (isset($user_data['dob']) === true) {
            $user_data['dob'] = Carbon::parse($user_data['dob'])->format('Y-m-d');
        }

        if (count($user_data) > 0) {
            // Update user details.
            $update_user_details = User::updateUserDetails($user_id, $user_data);

            if ($update_user_details === false) {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while updating user details. Please try again.');
            }
        }

        $profile_data = User::getUserProfile($user_id);

        // Paramters to fetch from user table.
        $get_params = ['base_currency'];
        if ($user_id !== 0) {
            // Fetch user data.
            $traveller_currency = User::getUserDataById($user_id, $get_params);
            // Traveller currency.
            $traveller_currency = ($traveller_currency->base_currency === '') ? DEFAULT_CURRENCY : User::getCommonCurrency($traveller_currency->base_currency);
        } else {
            $traveller_currency = $profile_data['user_currency'];
        }

        if (empty($profile_data) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'User not found.');
        }

         // Get user property listings.
        $profile_data['property_listings'] = $this->getUserListings($user_id, $traveller_currency, $headers, $user_id);

        $profile_data['message'] = 'User details updated successfully.';

        $response = new PutUserResponse($profile_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putUpdateUser()


    /**
     * Send email verification link to user
     *
     * @param \App\Http\PostVerificationEmailRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/user/verify/email",
     *     tags={"User"},
     *     description="Returns success/error message based on email being sent or not.",
     *     operationId="user.post.verify.email",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Verification link has been sent on your email address.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostUserVerifyEmailResponse"),
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
     *         description="We already have an account associated with this email.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while generating email verification link for the email you entered. Please try again. || There was some error while generating email verification link. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=502,
     *         description="Error while sending the verification mail.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postSendVerificationEmail(PostVerificationEmailRequest $request)
    {
        $user         = $this->getAuthUser();
        $user_id      = (int) $user->id;
        $input_params = $request->input();

        // If no new email entered by user.
        if (isset($input_params['email']) === true && empty($input_params['email']) === false) {
            $email = $input_params['email'];
        } else {
            $email = $user->email;
        }

        // Check if any other user has the newly entered email.
        $check_other_users_for_email = User::checkIfAnyOtherUserHasSameEmail($user_id, $email);

        // If any other user has registered with the newly entered email.
        if ($check_other_users_for_email === true) {
            return ApiResponse::forbiddenError(EC_DUPLICATE_USER, ERROR_CODE_EMAIL_ALREADY_REGISTERED);
        }

        // If user's email is not verified.
        if ((int) $user->email_verify !== 1) {
            // Generate a new confirmation code.
            $confirmation_code = User::generateConfirmationCodeForEmailVerification($user_id);
            // Update user email to new email.
            if ((string) $email !== $user->email) {
                $update_user_email = User::updateUserDetails($user_id, ['email' => $email]);
                if ($update_user_email === false) {
                    return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while generating email verification link for the email you entered. Please try again.');
                }
            }

            $to_email          = $email;
            $confirmation_code = $confirmation_code;
            $verification_link = MAILER_SITE_URL.'/user/mailverify/?ucode='.$confirmation_code;
        } else {
            // If user's email is verified (i.e. user trying to update his email).
            // If new email added is empty or same as user's verified email.
            if ((string) $email === $user->email) {
                return ApiResponse::validationFailed(['email' => 'The email field is invalid.']);
            }

            // Generate a new confirmation code.
            $confirmation_code = UpdateEmail::generateConfirmationCodeForEmailVerification($user_id, $email);
            $to_email          = $email;
            $confirmation_code = $confirmation_code;
            $verification_link = MAILER_SITE_URL.'/user/verifynewmail/?ucode='.$confirmation_code;
        }//end if

        $user_name = $user->name.' '.$user->last_name;

        // If confirmation code not generated.
        if (empty($confirmation_code) === true) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while generating email verification link. Please try again.');
        }

        // Send User Verification Email.
        $user_verification_event = new UserEmailVerified($to_email, $user_name, $confirmation_code, $verification_link);
        Event::dispatch($user_verification_event);

        $response = new PostUserVerifyEmailResponse(['message' => 'Verification link has been sent on your email address.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postSendVerificationEmail()


    /**
     * Verify email address of user with confirmation token
     *
     * @param \App\Http\PutVerificationEmailRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user/verify/email",
     *     tags={"User"},
     *     description="Returns success/error message based on email being verified or not.",
     *     operationId="user.put.verify.email",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_verification_code_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Your email has been verified.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PutUserVerifyEmailResponse"),
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
     *         response=500,
     *         description="There was some error while verifying email. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putVerifyEmail(PutVerificationEmailRequest $request)
    {
        $input_params = $request->input();

        $confirmation_code = $input_params['confirmation_code'];

        // Fetch user data by confirmation code.
        $user = User::getDataByConfirmationCode($confirmation_code);

        // When user his verifying his current email address.
        if (count($user) === 1) {
            $user                = $user[0];
            $mark_email_verified = User::updateUserDetails($user->id, ['email_verify' => 1, 'confirmation_code' => '']);

            // If email not marked verified.
            if ($mark_email_verified === false) {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while verifying email. Please try again.');
            }
        } else {
            // When user is trying to update his email address.
            $check_in_update_email = UpdateEmail::getDataByConfirmationCode($confirmation_code);

            // No confirmation code in update email.
            if (count($check_in_update_email) < 1) {
                return ApiResponse::validationFailed(['confirmation_code' => 'The confirmation code field is invalid.']);
            }

            $update_email_data = $check_in_update_email[0];

            // Updae user's email and mark it verified.
            $mark_email_verified = User::updateUserDetails($update_email_data->user_id, ['email_verify' => 1, 'confirmation_code' => '', 'email' => $update_email_data->email]);

            // If email not marked verified.
            if ($mark_email_verified === false) {
                return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while verifying email. Please try again.');
            }

            // Delete update email confirmation codes.
            UpdateEmail::deleteEmailVerificationToken($update_email_data->user_id);
        }//end if

        $response = new PutUserVerifyEmailResponse(['message' => 'Your email has been verified.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putVerifyEmail()


    /**
     * Logouts the user by revoking their access token
     *
     * @param \App\Http\Requests\PostLogoutRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/user/logout",
     *     tags={"User"},
     *     description="Logouts the user by revoking their access token",
     *     operationId="user.post.logout",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="You have been logged out.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostUserLogoutResponse"),
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
    public function postLogout(PostLogoutRequest $request)
    {
        // Get the user from the request, fetch their token and revoke it.
        if (empty($this->getAuthUser()) === false && method_exists($this->getAuthUser(), 'token') === true
            && method_exists($this->getAuthUser()->token(), 'revoke') === true
        ) {
            // Get device unique id.
            $device_unique_id = $request->getDeviceUniqueId();

            $this->getAuthUser()->token()->revoke();

            // Update Device user.
            $mobile_device = MobileAppDevice::getDeviceByDeviceUniqueId($device_unique_id);

            if (empty($mobile_device) === false) {
                $mobile_device->last_user_id = $mobile_device->user_id;
                $mobile_device->user_id      = 0;
                $mobile_device->save();
            }
        }

        $response = new PostUserLogoutResponse(['message' => 'You have been logged out.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postLogout()


    /**
     * Get User property listings
     *
     * @param \App\Http\GetUserPropertiesRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/user/properties",
     *     tags={"Property"},
     *     description="get user's properties.",
     *     operationId="user.get.properties",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Parameter(ref="#/parameters/user_id_in_query"),
     * @SWG\Parameter(ref="#/parameters/offset_optional_in_query"),
     * @SWG\Parameter(ref="#/parameters/total_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns array containing user property listings.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetUserPropertiesResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Auth token/User id missing.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function getUserProperties(GetUserPropertiesRequest $request)
    {
         $input_params = $request->input();
         $id           = $request->getLoginUserId();

        // If not user id is passed in query.
        if (isset($input_params['user_id']) === false) {
            if ($this->isUserLoggedIn() !== false) {
                // If some user is logged in.
                $user_id = $id;
                // User whose profile to fetch.
                $traveller_id = $id;
                // User who is looking at profile.
            } else {
                return ApiResponse::badRequestError(EC_AUTH_TOKEN_MISSING, 'Auth token/User id missing.');
            }
        } else {
            $user_hash_id = $input_params['user_id'];

            $user_id = $request->decodeUserIdOrFail($user_hash_id);

            if ($this->isUserLoggedIn() !== false) {
                $traveller_id = $id;
            } else {
                $traveller_id = 0;
            }
        }//end if

        $offset = $input_params['offset'];

        $limit = $input_params['limit'];

        // Paramters to fetch from user table.
        $get_params = ['base_currency'];

        // Fetch user data.
        $user = User::getUserDataById($user_id, $get_params);

        if (empty($user) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'User not found.');
        }

        // Get all headers.
        $headers = $request->getAllHeaders();

        // User currency.
        $currency = ($user->base_currency === '') ? DEFAULT_CURRENCY : User::getCommonCurrency($user->base_currency);

        // Get user property listings.
        $property_listings = $this->getUserListings($user_id, $currency, $headers, $traveller_id, $offset, $limit);

        $response = new GetUserPropertiesResponse(['properties' => $property_listings]);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getUserProperties()


    /**
     * Get User property listings
     *
     * @param integer $user_id      Users id.
     * @param string  $currency     Curency.
     * @param array   $headers      Request headers.
     * @param integer $traveller_id Traveller id.
     * @param integer $offset       Offset.
     * @param integer $limit        Limit.
     *
     * @return array.
     */
    private function getUserListings(int $user_id, string $currency, array $headers, int $traveller_id, int $offset=0, int $limit=DEFAULT_NUMBER_OF_PROPERTY_LISTED_BY_HOST)
    {
        // Output array.
        $properties = [];

        // Guests and bedrooms.
        $guests   = DEFAULT_NUMBER_OF_GUESTS;
        $bedrooms = DEFAULT_NUMBER_OF_UNITS;

        // Properties listed by a user.
        $listings = Property::getUserProperties($user_id, $guests, $bedrooms, $offset, $limit);

        // No property in listings.
        if (count($listings) === 0) {
            return [];
        }

        $user = User::find($user_id);

        // Get property ids array.
        $property_ids = array_unique(array_column($listings, 'id'));

        // Get first property image to display.
        $properties_images = PropertyImage::getPropertiesImagesByIds($property_ids, $headers, 1);

        // Get property videos.
        $properties_videos = PropertyVideo::getPropertyVideosByPropertyIds($property_ids);

        // Get first property tag associated with property (if any).
        $properties_tags = PropertyTagMapping::getPropertyTagsWithColorCodingByPropertyIds($property_ids, 1);

        // Get associative array of all currency conversions.
        $currency_rates = CurrencyConversion::getAllCurrencyDetails();

        // Get all countries by their codes.
        $country_codes = CountryCodeMapping::getCountries();

        // Cancellation policies.
        $cancellation_policy_ids = array_unique(array_column($listings, 'cancelation_policy'));

        // Get cancellation policy data.
        $cancellation_policy_data = CancellationPolicy::getCancellationPoliciesByIds($cancellation_policy_ids);

        // If traveller id is not zero.
        if ($traveller_id !== 0) {
            // Is user liked these properties.
            $liked_collection_properties = MyFavourite::getUserWishlistedPropertiesFromPropertyIds($traveller_id, $property_ids);

            // User liked properties.
            $user_liked_property_ids = array_unique(array_column($liked_collection_properties, 'property_id'));
        } else {
            $user_liked_property_ids = [];
        }

        // Loop through properties.
        foreach ($listings as $property) {
             // Array of data to process and get property pricing.
            $property_pricing_data = [
                'property_id'            => $property['id'],
                // 'start_date'            => $start_date,
                // 'end_date'              => $end_date,
                'units'                  => $bedrooms,
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
                'gh_commission'          => (int) $property['gh_commission'],
                'markup_service_fee'     => (int) $property['markup_service_fee'],
                'min_nights'             => $property['min_nights'],
                'max_nights'             => $property['max_nights'],
                'prive'                  => $property['prive'],
                'room_type'              => $property['room_type'],
                'bedrooms'               => $property['bedrooms'],
                'user'                   => $user,
                'error'                  => [],
            ];

            // Get property pricing details.
            $property_pricing = PropertyPricingService::getPropertyPrice($property_pricing_data);

            $host_image = (empty($property['host_image']) === false) ? $property['host_image'] : '';
            $gender     = (empty($property['host_gender']) === false) ? $property['host_gender'] : 'Male';
            $host_image = Helper::generateProfileImageUrl($gender, $host_image, $property['host_id']);

            $is_user_liked = (in_array($property['id'], $user_liked_property_ids) === true) ? 1 : 0;

            // Get desired structure.
            $one_organized_property_data = PropertyTileService::getPropertytileStructure(
                [
                    'property_id'           => $property['id'],
                    'property_score'        => number_format($property['property_score'], 1),
                    'property_type_name'    => $property['property_type_name'],
                    'room_type'             => $property['room_type'],
                    'room_type_name'        => $property['room_type_name'],
                    'area'                  => $property['area'],
                    'city'                  => $property['city'],
                    'state'                 => $property['state'],
                    'country'               => $property['country'],
                    'country_codes'         => $country_codes,
                    'latitude'              => $property['latitude'],
                    'longitude'             => $property['longitude'],
                    'accomodation'          => $property['accomodation'],
                    'currency'              => $currency,
                    'is_liked_by_user'      => $is_user_liked,
                    'display_discount'      => $property_pricing['effective_discount_percentage'],
                    'price_after_discount'  => $property_pricing['per_night_per_unit_price_without_service_fee'],
                    'price_before_discount' => (($property_pricing['per_night_per_unit_price_without_service_fee'] * 100) / (100 - $property_pricing['effective_discount_percentage'])),
                    'instant_book'          => 1,
                    'cash_on_arrival'       => 1,
                    'bedrooms'              => $property['bedrooms'],
                    'units_consumed'        => $property['units_consumed'],
                    'title'                 => $property['title'],
                    'properties_images'     => $properties_images,
                    'properties_videos'     => $properties_videos,
                    'properties_tags'       => $properties_tags,
                    'host_name'             => $property['host_name'],
                    'host_image'            => $host_image,
                ]
            );

            array_push($properties, $one_organized_property_data);
        }//end foreach

        return $properties;

    }//end getUserListings()


    /**
     *  Function to users profile picture
     *
     * @param \App\Http\PostUserPictureRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse response Response
     *
     * @SWG\Post(
     *     path="/v1.6/user/picture",
     *     tags={"User"},
     *     description="Update user's profile picture",
     *     operationId="user.post.picture",
     *     consumes={"multipart/form-data"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/picture_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Profile picture has been updated.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostUserPictureResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || There was some error while file upload. Please try again after some time.",
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
    public function postUpdateUserPicture(PostUserPictureRequest $request)
    {
        $user_id      = $request->getLoginUserId();
        $input_params = $request->file();

        $picture = $input_params['picture'];

        // If file has been uploaded OK.
        if ($picture->isValid() === false) {
            return ApiResponse::badRequestError(EC_FILE_NOT_UPLOADED, ERROR_CODE_FILE_UPLOAD_FAILED);
        }

        // Get the user from the request, and update the uploaded picture.
        $picture_url = User::find($user_id)->updateUploadedProfilePicture($picture);

        $response = [
            'message' => 'Profile picture has been updated.',
            'picture' => $picture_url,
        ];

        $response = new PostUserPictureResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postUpdateUserPicture()


    /**
     * Get an array of acceptable currencies
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\GET(
     *     path="/v1.6/currencycodes",
     *     tags={"Static Data"},
     *     description="Returns an array containing currencies.",
     *     operationId="user.get.currencycodes",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_optional_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="List of acceptable currency codes along with user selected currency.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/GetCurrencycodesResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )

     *     ),
     * )
     */
    public function getCurrencyCodes(Request $request)
    {
        $user              = null;
        $is_user_logged_in = $this->isUserLoggedIn();

        if ($is_user_logged_in === true) {
            $user = $this->getAuthUser();
        }

        $currency_codes = [];

        $selected_currency = (empty($user) === false) ? User::getCommonCurrency($user->base_currency) : DEFAULT_CURRENCY;

        // phpcs:ignore
        $supported_currencies = [
            // 'EUR',
            'INR'
            // ,
            // 'GBP',
            // 'USD',
        ];
        foreach ($supported_currencies as $value) {
            $currency_codes[] = [
                'code'   => $value,
                'symbol' => CURRENCY_SYMBOLS[$value]['webicon'],
            ];
        }

        return ApiResponse::success(['selected_currency' => $selected_currency, 'currency_codes' => $currency_codes]);

    }//end getCurrencyCodes()


    /**
     * Update user's base currency
     *
     * @param \App\Http\PutUserCurrencyRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user/currency",
     *     tags={"User"},
     *     description="Update user's base currency",
     *     operationId="user.put.currency",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/currency_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Currency has been updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutUserCurrencyResponse"),
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
    public function putUpdateUserCurrency(PutUserCurrencyRequest $request)
    {
        $input_params = $request->input();

        // Get the user from the request and update their currency.
        $user                = $this->getAuthUser();
        $user->base_currency = $input_params['currency'];
        $user->save();

        $response = [
            'currency' => $input_params['currency'],
            'message'  => 'Currency has been updated successfully.',
        ];

        $response = new PutUserCurrencyResponse($response);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putUpdateUserCurrency()


    /**
     * Deactivate user account
     *
     * @param \App\Http\PutDeleteUserRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user/delete",
     *     tags={"User"},
     *     description="Deactivate user account",
     *     operationId="user.put.delete",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Your account has been deactivated.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                  ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                    ref="#definitions/PutUserDeleteResponse"),
     * @SWG\Property(property="error",                                   ref="#/definitions/SuccessHttpResponse/properties/error"),
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
     *         description="Unauthorized action. || Incorrect password entered. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function putDeleteUser(PutDeleteUserRequest $request)
    {
        $input_params = $request->input();

        $user     = $this->getAuthUser();
        $password = $input_params['password'];

        // Check if password entered is valid.
        if (Hash::check(base64_decode($password), $user->password) === false) {
            return ApiResponse::forbiddenError(EC_CONTACT_NOT_VERIFIED, 'Incorrect password entered. Please try again.');
        }

        $is_host = User::isUserHost($user->id);
        if ($is_host === false) {
            $trip = BookingRequest::getUpcomingTrips($user->id, 0, 1, [BOOKED, OVERBOOKED, BOOKING_SWITCHED]);
        } else {
            $trip = BookingRequest::getUpcomingCheckinTraveller($user->id, true);
        }

        if (empty($trip) === false) {
            return ApiResponse::forbiddenError(EC_FORBIDDEN, 'You can not deactivate your account due to Active trips.');
        }

        $user->properties()->delete();

        // This is not working out as of now, need to fix permantently.
        // $user->token()->revoke(); .
        $user->sendAccountDeactivationEmail();
        $user->delete();

        $response = new PutUserDeleteResponse(['message' => 'Your account has been deactivated.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putDeleteUser()


    /**
     * Get wallet details of the user
     *
     * @param \Illuminate\Http\Request $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/user/wallet",
     *     tags={"User"},
     *     description="Returns wallet transactions of the user",
     *     operationId="user.get.wallet",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Returns wallet details.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetUserWalletResponse"),
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
    public function getWallet(Request $request)
    {
        $user = $this->getAuthUser();

        $wallet = [
            'balance'  => Helper::getFormattedMoney($user->getUsableWalletBalance(), Helper::getCurrencySymbol($user->getWalletCurrency())),
            'currency' => CURRENCY_SYMBOLS[$user->getWalletCurrency()],
            'headline' => 'Earn cash by referring friends and reviewing your trips. Use the cash to get booking discounts',
        ];

        $earn_more = [
            [
                'image'       => WALLET_REFERRAL_IMAGE_URL,
                'title'       => 'Referral on App',
                // phpcs:ignore
                'description' => 'Invite a friend and get '.Helper::getFormattedMoney(round(Helper::convertPriceToCurrentCurrency('INR', MAX_TOTAL_MONEY_FOR_REFERRAL, $user->getWalletCurrency()), 2), Helper::getCurrencySymbol($user->getWalletCurrency())).' in your wallet for every referral! Get '.Helper::getFormattedMoney(round(Helper::convertPriceToCurrentCurrency('INR', MAX_MONEY_FOR_FRIEND_BONUS, $user->getWalletCurrency()), 2), Helper::getCurrencySymbol($user->getWalletCurrency())).' when your friend signs up and '.Helper::getFormattedMoney(round(Helper::convertPriceToCurrentCurrency('INR', MAX_MONEY_FOR_FIRST_BOOKING, $user->getWalletCurrency()), 2), Helper::getCurrencySymbol($user->getWalletCurrency())).' on their first booking.',
                'button'      => 'Share Now',
                'event'       => WALLET_EVENT_SHARE_CONTACT,
            ],
            [
                'image'       => WALLET_REVIEW_IMAGE_URL,
                'title'       => 'Trips and Reviews',
                // phpcs:ignore
                'description' => 'Write a review and get a '.REVIEW_CASHBACK_PERCENTAGE.'% cashback (upto '.Helper::getFormattedMoney(round(Helper::convertPriceToCurrentCurrency('INR', MAX_CASHBACK_FOR_REVIEW, $user->getWalletCurrency()), 2), Helper::getCurrencySymbol($user->getWalletCurrency())).') on your booking amount.',
                'button'      => 'Write Reviews',
                'event'       => WALLET_EVENT_WRITE_REVIEW,
            ],
        ];

        return ApiResponse::success(
            [
                'wallet'       => $wallet,
                'earn_more'    => $earn_more,
                'can_invite'   => true,
                'transactions' => $user->getWalletTransactions(),
            ]
        );

    }//end getWallet()


    /**
     * Change user password
     *
     * @param \App\Http\PutUserPasswordUpdateRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user/password/update",
     *     tags={"User"},
     *     description="Returns success message when password updated successfully.",
     *     operationId="user.put.password.update",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Parameter(ref="#/parameters/base64encode_current_password_in_form"),
     * @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Password changed successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                          ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                            ref="#definitions/PutUserPasswordUpdateResponse"),
     * @SWG\Property(property="error",                                           ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || New password should be of minimum 6 characters.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=403,
     *         description="Unauthorized action. || Incorrect current password entered. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=500,
     *         description="There was some error while updating password. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putUpdatePassword(PutUserPasswordUpdateRequest $request)
    {
        $input_params = $request->input();

        // Input params.
        $current_password = $input_params['current_password'];
        $password         = $input_params['password'];
        $user             = $this->getAuthUser();

        // Check if old password entered is correct.
        if (Hash::check($current_password, $user->password) === false) {
            return ApiResponse::forbiddenError(EC_CONTACT_NOT_VERIFIED, 'Current password is incorrect. Please try again.');
        }

        // Update user password.
        $update_password = User::updateUserDetails($user->id, ['password' => Hash::make($password)]);

        // If password not updated.
        if ($update_password === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while updating password. Please try again.');
        }

        $response = new PutUserPasswordUpdateResponse(['message' => 'Your password has been updated successfully.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putUpdatePassword()


     /**
      * Get refer and earn data
      *
      * @param \Illuminate\Http\Request $request Http request object.
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\GET(
      *     path="/v1.6/user/refertoearn",
      *     tags={"User"},
      *     description="Returns an array containing refer code and refer earn headline and description .",
      *     operationId="user.get.refertoearn",
      *     consumes={"application/x-www-form-urlencoded"},
      *     produces={"application/json"},
      * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
      * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
      * @SWG\Response(
      *         response=200,
      *         description="referal code and refer earn headline and desc.",
      * @SWG\Schema(
      * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
      * @SWG\Property(property="data",                                 ref="#definitions/GetUserRefertoearnResponse"),
      * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
      *      )
      *     ),
      * @SWG\Response(
      *         response=401,
      *         description="Unauthorized action.",
      * @SWG\Schema(
      *   ref="#/definitions/ErrorHttpResponse"),
      *     )
      * )
      */
    public function getReferToEarnDetails(Request $request)
    {
        $user = $this->getAuthUser();
        if (empty($user) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'User not found.');
        }

        $reward_currency = WALLET_MONEY_REWARD_CURRENCY;
        // phpcs:disable Generic.Files.LineLength.TooLong
        $wallet_currency = (empty($user->wallet_currency) === false ) ? $user->wallet_currency : DEFAULT_CURRENCY;

        $total_reward_money = Helper::getFormattedMoney(round(helper::convertPriceToCurrentCurrency(WALLET_MONEY_REWARD_CURRENCY, (MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER + MAXIMUM_WALLET_MONEY_EARNED_FOR_REFERRER_BOOKING), $wallet_currency)), $wallet_currency);
        $reward_for_signup  = Helper::getFormattedMoney(round(helper::convertPriceToCurrentCurrency(WALLET_MONEY_REWARD_CURRENCY, (MAX_MONEY_FOR_FRIEND_BONUS), $wallet_currency)), $wallet_currency);
        $reward_for_booking = Helper::getFormattedMoney(round(helper::convertPriceToCurrentCurrency(WALLET_MONEY_REWARD_CURRENCY, (MAX_MONEY_FOR_FIRST_BOOKING), $wallet_currency)), $wallet_currency);
        // phpcs:enable

        $url = WEBSITE_URL.'/app?referral_code='.$user->referral_code;

        // phpcs:disable
        $refer_to_earn_data = [
                'headline' => "Spread the word & earn up to $total_reward_money !",
                'description' => "Invite your friends to sign up for GuestHouser. Earn $reward_for_signup for every sign up & $reward_for_booking on their first booking. Get inviting. NOW.",
                'referral_code' => "$user->referral_code",
                'refer_url' => $url
            ];

        $response = new GetUserRefertoearnResponse($refer_to_earn_data);
        $response = $response->toArray();
        return ApiResponse::success($response);
        // phpcs:enable

    }//end getReferToEarnDetails()


    /**
     * Update Last Active
     *
     * @param \App\Http\PutUserLastactiveUpdateRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user/lastactive/update",
     *     tags={"User"},
     *     description="Returns success message.",
     *     operationId="user.put.lastactive.update",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
     * @SWG\Response(
     *         response=200,
     *         description="Updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutUserLastactiveUpdateResponse"),
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
     *         response=500,
     *         description="There was some error while updating. Please try again.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putUpdateLastactive(PutUserLastactiveUpdateRequest $request)
    {
        MobileAppDevice::updateLastActive($request->getDeviceUniqueId());

        $response = new PutUserLastactiveUpdateResponse(['message' => 'Updated successfully.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end putUpdateLastactive()


    /**
     * Get Referer user details
     *
     * @param \App\Http\GetReferDetailsRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.6/user/getrefererdetails",
     *     tags={"User"},
     *     description="Returns success message.",
     *     operationId="user.get.getrefererdetails",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/referral_code_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Array of user data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/GetUserGetrefererdetailsResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     )
     * )
     */
    public function getRefererDetails(GetReferDetailsRequest $request)
    {
        $input_params = $request->input();

        $referral_code = $input_params['referral_code'];

        $user = User::where('referral_code', $referral_code)->first();

        if (empty($user) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Invalid referral code.');
        }

        $user = $user->toArray();

        // Process user profile image(get default avatar if image not added, get image full url).
        $gender        = (empty($user['gender']) === false) ? $user['gender'] : 'Male';
        $profile_image = (empty($user['profile_img']) === false) ? $user['profile_img'] : '';

        $profile_image = Helper::generateProfileImageUrl($gender, $profile_image, $user['id']);

        $referer_data = [
            'user_name'     => $user['name'],
            'user_image'    => $profile_image,
            'referral_code' => $referral_code,
            'brief'         => 'You have '.Helper::getCurrencySymbol(WALLET_MONEY_REWARD_CURRENCY).MAX_TOTAL_MONEY_FOR_REFERRAL.' from '.\ucwords($user['name'])."'s referral on our App. Sign up to claim ",
            'detail'        => 'Since '.\ucwords($user['name']).' has referred you on this App. You have '.Helper::getCurrencySymbol(WALLET_MONEY_REWARD_CURRENCY).MAX_TOTAL_MONEY_FOR_REFERRAL.' to get you started.',
        ];

        $response = new GetUserGetrefererdetailsResponse($referer_data);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getRefererDetails()


        /**
         * App Feedback
         *
         * @param \Illuminate\Http\PostUserAppfeedbackRequest $request Http request object.
         *
         * @return \Illuminate\Http\JsonResponse
         *
         * @SWG\Post(
         *     path="/v1.6/user/appfeedback",
         *     tags={"User"},
         *     description="App Feedback",
         *     operationId="user.post.appfeedback",
         *     consumes={"application/x-www-form-urlencoded"},
         *     produces={"application/json"},
         * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
         * @SWG\Parameter(ref="#/parameters/access_token_in_header"),
         * @SWG\Response(
         *         response=200,
         *         description="Your feedback has been submitted successfully.",
         * @SWG\Schema(
         * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
         * @SWG\Property(property="data",                                 ref="#definitions/PostUserAppfeedbackResponse"),
         * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
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
    public function postAppFeedback(PostUserAppfeedbackRequest $request)
    {
        $input_params = $request->input();

        // Mailer Integration here.
        $response = new PostUserAppfeedbackResponse(['message' => 'Your feedback has been submitted successfully.']);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postAppFeedback()


    /**
     * Get user auth tokens
     *
     * @param \Illuminate\Http\Request $request Http request object.
     * @param integer                  $user_id User id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOAuthToken(Request $request, int $user_id)
    {
        // Logged in user.
        $logged_in_user       = $this->getAuthUser();
        $logged_in_user_id    = $logged_in_user->id;
        $logged_in_user_email = $logged_in_user->email;

        // Check only guesthouser.com users can login to the admin panel.
        $email_parts = explode('@', $logged_in_user_email);
        if ($email_parts[1] !== 'guesthouser.com') {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Not a valid login');
        }

        $anonymous_user = User::find($user_id);

        if (empty($anonymous_user) === true || $anonymous_user->deleted_at !== null) {
            return ApiResponse::badRequestError(EC_INVALID_ACCESS_TOKEN, 'Invalid User');
        }

        $oauth_response = $this->getBearerTokenByUser($anonymous_user, '2', false);
        $host_token     = User::loginUser($oauth_response['access_token'], $oauth_response['refresh_token'], $anonymous_user->id);

        // Response content.
        $content = [
            'access_token' => $oauth_response['access_token'],
        ];

        return ApiResponse::success($content);

    }//end getUserOAuthToken()


     /**
      * Login For older app user
      *
      * @param \App\Http\PostUserLoginoldappuserRequest $request Http request object.
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\Post(
      *     path="/v1.6/user/loginoldappuser",
      *     tags={"User"},
      *     description="Returns success message.",
      *     operationId="user.post.loginoldappuser",
      *     consumes={"application/x-www-form-urlencoded"},
      *     produces={"application/json"},
      * @SWG\Parameter(ref="#/parameters/access_token_in_form"),
      * @SWG\Parameter(ref="#/parameters/refresh_token_in_form"),
      * @SWG\Parameter(ref="#/parameters/device_unique_id_in_form"),
      * @SWG\Response(
      *         response=200,
      *         description="Array of user data.",
      * @SWG\Schema(
      * @SWG\Property(property="status",                             ref="#/definitions/SuccessHttpResponse/properties/status"),
      * @SWG\Property(property="data",                               ref="#definitions/PostUserLoginoldappuserResponse"),
      * @SWG\Property(property="error",                              ref="#/definitions/SuccessHttpResponse/properties/error"),
      *      )
      *     )
      * )
      */
    public function postLoginOldAppUser(PostUserLoginoldappuserRequest $request)
    {
        $input_params = $request->input();

        $user = User::findOldAppLoggedInUser($input_params['access_token'], $input_params['refresh_token'], $input_params['device_unique_id']);

        if (empty($user) === false) {
            $device_unique_id = $input_params['device_unique_id'];

            $row               = MobileAppDevice::getDeviceByDeviceUniqueId($device_unique_id);
            $current_timestamp = date('Y:m:d h:i:s');
            $row->last_active  = $current_timestamp;
            $row->last_login   = $current_timestamp;
            $row->status       = 1;
            $row->save();

            // Traffic data.
            $traffic = [];
            $traffic['device_unique_id'] = $device_unique_id;
            $traffic['event']            = 'login';
            $traffic['actor_id']         = $user->id;
            $traffic['referrer']         = '';
            TrafficData::createNew($traffic);

            // Generate Access Token.
            $oauth_response = $this->getBearerTokenByUser($user, '2', false);
            $host_token     = User::loginUser($oauth_response['access_token'], $oauth_response['refresh_token'], $user->id);

            $user_profile               = User::getUserProfile($user->id);
            $user_profile['host_token'] = $host_token;
            $user_profile['event']      = 'login';

            // Response content.
            $content = [
                'user_profile'  => $user_profile,
                'token_type'    => $oauth_response['token_type'],
                'expires_in'    => $oauth_response['expires_in'],
                'access_token'  => $oauth_response['access_token'],
                'refresh_token' => $oauth_response['refresh_token'],
            ];

            $response = new PostUserLoginoldappuserResponse($content);
            $response = $response->toArray();
            return ApiResponse::success($response);
        }//end if

        return ApiResponse::badRequestError(EC_INVALID_ACCESS_TOKEN, 'Invalid User');

    }//end postLoginOldAppUser()


    /**
     * Login via one time access token
     *
     * @param \App\Http\PostUserLoginViaTokenRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/user/loginviatoken",
     *     tags={"User"},
     *     description="Returns success message.",
     *     operationId="user.post.loginviatoken",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/onetime_token_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Array of user data.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostUserLoginViaTokenResponse"),
     * @SWG\Property(property="error",                                ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     )
     * )
     */
    public function postLoginViaToken(PostUserLoginViaTokenRequest $request)
    {
        // Collect all input params.
        $input_params = $request->input();

        // Make service object.
        $user_service = new UserService;

        if (isset($input_params['onetime_token']) === true) {
            $token_data = $user_service->validateOnetimeAccessToken($input_params['onetime_token']);

            // Get User Object.
            $user = (empty($token_data['user_id']) === false) ? $user_service->getUser($token_data['user_id']) : null;
        } else {
            // Get User Object for Auth Key.
            $user = $user_service->validateAuthKeyToken($input_params['auth_key']);
        }

        if (empty($user) === true) {
            return ApiResponse::badRequestError(EC_INVALID_ACCESS_TOKEN, 'Invalid Token or Token expired');
        }

        if (empty($user) === false) {
            // Generate Access Token.
            $oauth_response = $this->getBearerTokenByUser($user, '2', false);

            $user_profile          = User::getUserProfile($user->id);
            $user_profile['event'] = 'login';

            // Response content.
            $content = [
                'user_profile'  => $user_profile,
                'token_type'    => $oauth_response['token_type'],
                'expires_in'    => $oauth_response['expires_in'],
                'access_token'  => $oauth_response['access_token'],
                'refresh_token' => $oauth_response['refresh_token'],
            ];

            $response = new PostUserLoginViaTokenResponse($content);
            $response = $response->toArray();
            return ApiResponse::success($response);
        }//end if

        return ApiResponse::badRequestError(EC_INVALID_ACCESS_TOKEN, 'Invalid User');

    }//end postLoginViaToken()


    /**
     * Send Otp to user while Login Using Mobile number
     *
     * @param \App\Http\PostMobileLoginRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.6/user/login/mobile",
     *     tags={"User"},
     *     description="Returns success/error message based on otp being sent or not.",
     *     operationId="user.post.login.mobile",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_number_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_method_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Sucess Message of otp sent.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostMobileLoginResponse"),
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
     *         description="Mobile number is not verified.",
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
    public function postMobileLogin(PostMobileLoginRequest $request)
    {
        // Collect all input params.
        $input_params = $request->input();

        $contact  = $input_params['contact_number'];
        $dialcode = $input_params['dial_code'];

        // 0 means sms 1 means call.
        $otp_method = $input_params['otp_method'];

        $check_user_existance = User::getUserByMobileNumber($contact, $dialcode)->toArray();

        // If no user exists with given number.
        if (empty($check_user_existance) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Contact not found.');
        } else {
                $user = $check_user_existance[0];
                // If more than 1 user has same mobile number and no user is verified.
            if ($user['mobile_verify'] === 0) {
                return ApiResponse::forbiddenError(EC_CONTACT_NOT_VERIFIED, 'Mobile number is not verified.');
            }
        }

        $user = $check_user_existance[0];

        $send_otp = $this->user_service->generateAndSendOtp($user, $otp_method);
        if ($send_otp['status'] !== 1) {
            return ApiResponse::serviceUnavailableError(EC_SERVICE_UNAVIALABLE, 'There was some error while sending OTP. Please try after some time.');
        }

        $content  = [
            'dial_code'      => $dialcode,
            'contact'        => $contact,
            'sms_sender_ids' => SMS_SENDER_IDS,
            'message'        => $send_otp['message'],
        ];
        $response = new PostMobileLoginResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postMobileLogin()


    /**
     * Verify otp when user login
     *
     * @param \App\Http\PutMobileLoginRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.6/user/login/mobile",
     *     tags={"User"},
     *     description="verify otp code added by user for its validity and make user contact verified.",
     *     operationId="user.put.user.login.mobile",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/contact_number_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_code_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User Profile with acess token and refresh token.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutMobileLoginResponse"),
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
     *         response=500,
     *         description="There was some error while verifying the OTP. Please try after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     )
     * )
     */
    public function putMobileLogin(PutMobileLoginRequest $request)
    {
        $input_params = $request->input();

        $otp      = $input_params['otp_code'];
        $contact  = $input_params['contact_number'];
        $dialcode = $input_params['dial_code'];

        $check_user_existance = User::getUserByMobileNumber($contact, $dialcode);
        // If no user exists with given number.
        if (count($check_user_existance) === 0) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Contact not found.');
        } else {
                $user = $check_user_existance[0];
                // If more than 1 user has same mobile number and no user is verified.
            if (count($check_user_existance) >= 1 && $user->mobile_verify === 0) {
                return ApiResponse::forbiddenError(EC_CONTACT_NOT_VERIFIED, 'Mobile number is not verified.');
            }
        }

        $user    = $check_user_existance[0];
        $user_id = $user->id;

        $verify_otp = $this->user_service->verifyUserOtp($user_id, $otp, $contact, $dialcode);
        if ($verify_otp === true) {
              // Generate Access Token.
            $oauth_response = $this->getBearerTokenByUser($user, '2', false);
            $host_token     = User::loginUser($oauth_response['access_token'], $oauth_response['refresh_token'], $user_id);
            $user_profile   = User::getUserProfile($user_id);

            $user_profile['host_token'] = $host_token;

            $content  = [
                'user_profile'  => $user_profile,
                'token_type'    => $oauth_response['token_type'],
                'expires_in'    => $oauth_response['expires_in'],
                'access_token'  => $oauth_response['access_token'],
                'refresh_token' => $oauth_response['refresh_token'],
            ];
            $response = new PutMobileLoginResponse($content);
            $response = $response->toArray();
            return ApiResponse::success($response);
        } else {
             return ApiResponse::forbiddenError(EC_INVALID_OTP, $verify_otp['message']);
        }//end if

         return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while verifying the OTP. Please try after some time.');

    }//end putMobileLogin()


}//end class
