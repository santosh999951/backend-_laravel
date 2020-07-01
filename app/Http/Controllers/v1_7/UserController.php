<?php
/**
 * User controller containing methods
 */

namespace App\Http\Controllers\v1_7;

use App\Events\UserResetPassword;
use App\Models\SmsOtp;
use Illuminate\Http\{Request, Response};
use \Event;
use App\Events\UserRegistered;
use App\Models\User;
use App\Libraries\{Helper};
use App\Libraries\v1_6\{UserService};
use Illuminate\Support\Facades\{Hash};
use App\Libraries\v1_7\{ApiResponse};


use App\Http\Requests\v1_7\{PostRegisterRequest,PutVerifyOtpRequest,PostLoginRequest, GetUserStatusRequest, PutUserResetPasswordRequest};
use App\Http\Requests\v1_7\{PostGenerateOtpRequest, PutVerifyForgotOtpRequest, PostUserPasswordResetRequest};
use App\Http\Response\v1_7\Models\{PutVerifyOtpResponse,PostRegisterResponse,PostLoginResponse, GetUserStatusResponse, PutUserResetPasswordResponse};
use App\Http\Response\v1_7\Models\{PostGenerateOtpResponse, PostUserPasswordResetResponse, PutVerifyForgotOtpResponse};

use App\Models\{MobileAppDevice,TrafficData};

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
     * @param \App\Http\Requests\v1_7\PostRegisterRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.7/user",
     *     tags={"User"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="user.post.register",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/source_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_value_in_form"),
     * @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_first_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_last_name_optional_in_form"),
     * @SWG\Response(
     *      response=200,
     *      description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=201,
     *      description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/CreateHttpResponse/properties/error"),
     *      )
     * ),
     * @SWG\Response(
     *      response=400,
     *      description="Missing or invalid parameters. || Invalid source parameter.|| Invalid source type",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     * ),
     * @SWG\Response(
     *      response=403,
     *      description="We already have an account associated with this email or contact number.",
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
     *     path="/v1.7/user#phone",
     *     tags={"User"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="user.post.register.phone",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/source_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_value_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_first_name_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_last_name_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/email_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_code_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/CreateHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters. || Invalid access token. || Invalid source parameter. || inavalid source type",
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
     * ),
     * @SWG\Post(
     *     path="/v1.7/user#google",
     *     tags={"User"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="user.post.register.google",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/source_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_value_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostRegisterResponse"),
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
     *     path="/v1.7/user#facebook",
     *     tags={"User"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="user.post.register.facebook",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/source_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_value_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="User updated successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostRegisterResponse"),
     * @SWG\Property(property="error",                                     ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=201,
     *         description="User created successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                    ref="#/definitions/CreateHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                      ref="#definitions/PostRegisterResponse"),
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
     *     path="/v1.7/user#apple",
     *     tags={"User"},
     *     description="Returns oauth tokens when user registers successfully.",
     *     operationId="user.post.register.apple",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/source_in_form"),
     * @SWG\Parameter(ref="#/parameters/source_value_in_form"),
     * @SWG\Parameter(ref="#/parameters/user_first_name_in_form"),
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

        // Source (email, phone, fb, google , apple).
        $source = $input_params['source'];

        $email        = $input_params['email'];
        $password     = $input_params['password'];
        $name         = $input_params['first_name'];
        $lastname     = $input_params['last_name'];
        $dial_code    = $input_params['dial_code'];
        $source_value = $input_params['source_value'];
        $otp_code     = $input_params['otp_code'];

        // Is new user.
        $is_new_user = true;

        // Set default Currency.
        $currency = DEFAULT_CURRENCY;

        $profile_img = '';

        $birthday = '';

        $gender = '';

        $contact = '';

        // Social Id (Google or Facebook, Apple Id).
        $social_id = '';

        $send_email = 1;

        $email_verify = 0;

        // Make Password auto generate for google and facebook sign-up.
        $generated_password = str_random(6);

        // Get Payload Data from Access Token.
        if ($source === GOOGLE_SOURCE_ID) {
            $payload = Helper::getGoogleSignUpProfile($source_value, $header_device_type);

            if (empty($payload) === true) {
                // Invaid access token.
                return ApiResponse::badRequestError(EC_INVALID_ACCESS_TOKEN, 'Invalid access token.');
            }

            // Google fetched data.
            $social_id      = $payload['id'];
            $email          = $payload['email'];
            $name           = $payload['name'];
            $lastname       = $payload['last_name'];
            $profile_img    = $payload['profile_img'];
            $email_or_phone = $email;
        } else if ($source === FACEBOOK_SOURCE_ID) {
            $payload = Helper::getFacebookSignUpProfile($source_value);

            if (empty($payload) === true) {
                // Invaid access token.
                return ApiResponse::badRequestError(EC_INVALID_ACCESS_TOKEN, 'Invalid access token.');
            }

            // Facebook fetched data.
            $social_id      = $payload['id'];
            $email          = (empty($payload['email']) === false) ? $payload['email'] : $social_id.'@facebook.com';
            $email_or_phone = $email;
            $name           = $payload['name'];
            $lastname       = $payload['last_name'];
            $birthday       = $payload['birthday'];
            $currency       = (empty($payload['currency']) === false) ? $payload['currency'] : $currency;
            $profile_img    = $payload['profile_img'];
            $gender         = (empty($payload['gender']) === false) ? $payload['gender'] : $gender;
        } else if ($source === APPLE_SOURCE_ID) {
            $payload = Helper::getAppleSignUpProfile($source_value);

            if (empty($payload) === true) {
                // Invaid access token.
                return ApiResponse::badRequestError(EC_INVALID_ACCESS_TOKEN, 'Invalid access token.');
            }

            // Facebook fetched data.
            $social_id      = $payload['id'];
            $email          = $payload['email'];
            $email_or_phone = $email;
            $email_verify   = $payload['email_verified'];
        } else if ($source === EMAIL_SOURCE_ID) {
            $email_or_phone = $source_value;
            $email          = $source_value;
        } else if ($source === PHONE_SOURCE_ID) {
            $contact       = $dial_code.$source_value;
            $otp_existance = $this->user_service->checkIfVerifyLoginOtpExists($contact, $otp_code, $device_unique_id, 0, 1);
            if (empty($otp_existance) === true) {
                return ApiResponse::badRequestError(EC_INVALID_OTP, 'Invalid OTP CODE.');
            }

            $email_or_phone = $source_value;
            if (empty($email) === true) {
                $email      = $source_value.'@'.GH_DEFAULT_EMAIL_DOMAIN;
                $send_email = 0;
            } else {
                $email = $email;
            }

            $contact = $source_value;
        }//end if

        // Check if user exist on the basis of phone or email.
        $existing_user = $this->user_service->exists($email_or_phone, $dial_code, $source, $social_id);

        // If user already exist.
        if (empty($existing_user) === false) {
            $is_new_user = false;

            if (in_array($source, [GOOGLE_SOURCE_ID, FACEBOOK_SOURCE_ID, APPLE_SOURCE_ID]) === true) {
                $existing_user = $this->user_service->activateIfTrashed($existing_user);
                // Update User data.
                $user = $this->user_service->update(
                    $existing_user,
                    [
                        'email_verify'    => (empty($existing_user->email_verify) === true || $source === GOOGLE_SOURCE_ID) ? 1 : 0,
                        'dob'             => $birthday,
                        'wallet_currency' => $currency,
                        'base_currency'   => $currency,
                        'profile_img'     => $profile_img,
                        'google_id'       => ($source === GOOGLE_SOURCE_ID) ? $social_id : $existing_user->google_id,
                        'fb_id'           => ($source === FACEBOOK_SOURCE_ID) ? $social_id : $existing_user->facebook_id,
                        'apple_id'        => ($source === APPLE_SOURCE_ID) ? $social_id : $existing_user->apple_id,
                    ]
                );

                if (empty($user) === true) {
                    return ApiResponse::serverError(EC_SERVER_ERROR, ERROR_CODE_USER_NOT_CREATED);
                }
            } else {
                return ApiResponse::forbiddenError(EC_DUPLICATE_USER, ERROR_CODE_EMAIL_CONTACT_ALREADY_REGISTERED);
            }//end if
        } else {
            // New user instance.
            $register = $this->user_service->register(
                [
                    'email'         => $email,
                    'password'      => $password,
                    'name'          => $name,
                    'last_name'     => $lastname,
                    'contact'       => $contact,
                    'dial_code'     => $dial_code,
                    'dob'           => $birthday,
                    'gender'        => $gender,
                    'base_currency' => $currency,
                    'signup_source' => $header_device_type,
                    'profile_img'   => $profile_img,
                    'email_verify'  => $email_verify,
                ],
                $source,
                $social_id,
            );

            if ($register['status'] === false) {
                return ApiResponse::serverError(EC_SERVER_ERROR, ERROR_CODE_USER_NOT_CREATED);
            }

            // Get User Instance.
            $user = $register['data'];
        }//end if

        // Send user registration email.
        if ($send_email === 1) {
            $user_registered_event = new UserRegistered($user, $source, $generated_password);
            Event::dispatch($user_registered_event);
        }

        // Set user id corresponding to device id.
        $this->user_service->addUserDevice($user->id, $device_unique_id);

        // Traffic data.
        $traffic = [];
        $traffic['device_unique_id'] = $device_unique_id;
        $traffic['event']            = ($is_new_user === true) ? 'signup' : 'login';
        $traffic['actor_id']         = $user->id;
        $traffic['referrer']         = '';
        TrafficData::createNew($traffic);

        $user_profile          = $this->user_service->getUserProfile($user->id);
        $user_profile['event'] = ($is_new_user === true) ? 'signup' : 'login';
        // Generate Access Token.
        $oauth_response = $this->getBearerTokenByUser($user, '2', false);

         $content = [
             'user_profile'  => $user_profile,
             'token_type'    => $oauth_response['token_type'],
             'expires_in'    => $oauth_response['expires_in'],
             'access_token'  => $oauth_response['access_token'],
             'refresh_token' => $oauth_response['refresh_token'],
         ];

         // Response content.
         if ($is_new_user === true) {
             $message  = 'User created successfully.';
             $response = new PostRegisterResponse($content);
             $response = $response->toArray();
             return ApiResponse::create($response, $message);
         }

         // User updated.
         $message = 'User updated successfully.';

         $response = new PostRegisterResponse($content);
         $response = $response->toArray();
         return ApiResponse::success($response, OK_HTTP_STATUS_CODE, $message);

    }//end postRegister()


    /**
     * Verify otp for mobile verification or login purpose.
     *
     * @param \App\Http\Requests\v1_7\PutVerifyOtpRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.7/user/verify/otp",
     *     tags={"User"},
     *     description="Returns success/error message based on otp verification parameter.",
     *     operationId="verify.put.otp",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/contact_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_code_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Success response containing mode of login and other details.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutVerifyOtpResponse"),
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
     *         response=503,
     *         description="There was some error while sending OTP. Please try after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function putVerifyOtp(PutVerifyOtpRequest $request)
    {
        $input_params = $request->input();

        $contact   = $input_params['contact'];
        $otp       = $input_params['otp_code'];
        $otp_type  = config('gh')['login_otp_type'];
        $dial_code = $input_params['dial_code'];

        $phone            = $dial_code.$contact;
        $device_unique_id = $request->getDeviceUniqueId();

        $verify_otp = $this->user_service->verifyOtp($otp, $phone, $device_unique_id, $otp_type);

        // Error case.
        if ($verify_otp !== true) {
            return ApiResponse::forbiddenError(EC_INVALID_OTP, $verify_otp['message']);
        }

        $this->user_service->verifyContact($contact, $dial_code);

        $message = 'Otp verified successfully.';
        return ApiResponse::success([], OK_HTTP_STATUS_CODE, $message);

    }//end putVerifyOtp()


    /**
     * Verify forgot password Otp via api for user.
     *
     * @param \App\Http\Requests\v1_7\PutVerifyForgotOtpRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.7/user/verify/forgotpassword/otp",
     *     tags={"User"},
     *     description="Returns success/error message based on otp verification parameter.",
     *     operationId="verify.forgotpassword.put.otp",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_or_phone_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_optional_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_code_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Success response containing mode of login and other details.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PutVerifyForgotOtpResponse"),
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
     *         response=503,
     *         description="There was some error while sending OTP. Please try after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function putVerifyForgotOtp(PutVerifyForgotOtpRequest $request)
    {
        $input_params = $request->input();

        $verify_otp_via = $input_params['email_or_phone'];
        $otp            = $input_params['otp_code'];
        $otp_type       = config('gh')['forgot_password_otp_type'];
        $dial_code      = (false === empty($input_params['dial_code'])) ? $input_params['dial_code'] : '91';

        $user_id = 0;
        if (is_numeric($verify_otp_via) === true) {
            $phone = $dial_code.$verify_otp_via;
        } else {
            $user    = User::getUserByEmail($verify_otp_via);
            $phone   = $dial_code.$user->contact;
            $user_id = $user->id;
        }

        $device_unique_id = $request->getDeviceUniqueId();
        $verify_otp       = $this->user_service->verifyOtp($otp, $phone, $device_unique_id, $otp_type, $user_id);

        // Error case.
        if ($verify_otp !== true) {
            return ApiResponse::forbiddenError(EC_INVALID_OTP, $verify_otp['message']);
        }

        $message = 'Otp verified successfully.';
        return ApiResponse::success([], OK_HTTP_STATUS_CODE, $message);

    }//end putVerifyForgotOtp()


     /**
      * Verify otp when user login
      *
      * @param \App\Http\Requests\v1_7\PostLoginRequest $request Http request object.
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\Post(
      *     path="/v1.7/user/login",
      *     tags={"User"},
      *     description="verify otp code added by user for its validity and make user contact verified.",
      *     operationId="user.post.user.login.mobile",
      *     consumes={"application/x-www-form-urlencoded"},
      *     produces={"application/json"},
      * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
      * @SWG\Parameter(ref="#/parameters/grant_type_in_form"),
      * @SWG\Parameter(ref="#/parameters/email_optional_in_form"),
      * @SWG\Parameter(ref="#/parameters/dial_code_optional_in_form"),
      * @SWG\Parameter(ref="#/parameters/contact_number_optional_in_form"),
      * @SWG\Parameter(ref="#/parameters/otp_code_optional_in_form"),
      * @SWG\Parameter(ref="#/parameters/base64encode_password_optional_in_form"),
      * @SWG\Response(
      *         response=200,
      *         description="User Profile with acess token and refresh token.",
      * @SWG\Schema(
      * @SWG\Property(property="status",                                           ref="#/definitions/SuccessHttpResponse/properties/status"),
      * @SWG\Property(property="data",                                             ref="#definitions/PostLoginResponse"),
      * @SWG\Property(property="error",                                            ref="#/definitions/SuccessHttpResponse/properties/error"),
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
    public function postLogin(PostLoginRequest $request)
    {
        $input_params = $request->input();

        $grant_type = $input_params['grant_type'];

        $device_unique_id = $request->getDeviceUniqueId();

        if (empty($input_params['email']) === false) {
            $user = $this->user_service->getUserByEmail($input_params['email']);
        } else {
            $user = $this->user_service->getUserByMobileNumber($input_params['contact'], $input_params['dial_code']);
        }

        if (empty($user) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'Contact not found.');
        }

        $user                = (isset($user[0]) === true) ? $user[0] : $user;
        $verification_status = false;

        if ($grant_type === 'password') {
            $verification_status = $this->user_service->validatePassword($user, $input_params['password']);
        } else if ($grant_type === 'otp') {
            $verification_status = $this->user_service->checkOtpStatus($user, $input_params['otp_code'], $input_params['contact'], $input_params['dial_code'], $device_unique_id);
        }

        if ($verification_status === true) {
              // Generate Access Token.
            $oauth_response = $this->getBearerTokenByUser($user, '2', false);

            $user_profile = $this->user_service->getUserProfile($user->id);
            // Response content.
            $content  = [
                'user_profile'  => $user_profile,
                'token_type'    => $oauth_response['token_type'],
                'expires_in'    => $oauth_response['expires_in'],
                'access_token'  => $oauth_response['access_token'],
                'refresh_token' => $oauth_response['refresh_token'],
            ];
            $response = new PostLoginResponse($content);
            $response = $response->toArray();
            return ApiResponse::success($response);
        } else {
            $msg = ($grant_type === 'password') ? 'Invalid password.' : 'Otp not verified.';
            return ApiResponse::forbiddenError(EC_INVALID_OTP, $msg);
        }//end if

         return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while verifying the OTP. Please try after some time.');

    }//end postLogin()


    /**
     * User status via api.
     *
     * @param \App\Http\Requests\v1_7\GetUserStatusRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/v1.7/user/status",
     *     tags={"User"},
     *     description="Returns success/error message based on statusvia parameter.",
     *     operationId="user.get.status",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/login_id_in_query"),
     * @SWG\Parameter(ref="#/parameters/dial_code_optional_in_query"),
     * @SWG\Response(
     *         response=200,
     *         description="Success response containing mode of login and other details.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                  ref="#definitions/GetUserStatusResponse"),
     * @SWG\Property(property="error",                                 ref="#/definitions/SuccessHttpResponse/properties/error"),
     *      )
     *     ),
     * @SWG\Response(
     *         response=400,
     *         description="Missing or invalid parameters.",
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
    public function getUserStatus(GetUserStatusRequest $request)
    {
        $input_params = $request->input();
        $config       = config('gh');

        $login_id  = $input_params['login_id'];
        $dial_code = $input_params['dial_code'];

        $user = $this->user_service->exists($login_id, $dial_code);

        $status_col = '';

        // If entered value is mobile number.
        if (ctype_digit($login_id) === true) {
            $status_col = 'mobile_verify';
        } else {
            $status_col = 'email_verify';
        }

        if (empty($user) === true) {
            $content['status'] = $config['user_status']['not_found'];
        } else {
            // Check if email verfied or not.
            if ($user[$status_col] === 1) {
                $content['status'] = $config['user_status']['verified'];
            } else {
                $content['status'] = $config['user_status']['not_verified'];
            }
        }

        $response = new GetUserStatusResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end getUserStatus()


    /**
     * Generate phone otp via api for verifying phone of user.
     *
     * @param \App\Http\Requests\v1_7\PostGenerateOtpRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.7/user/generate/otp",
     *     tags={"User"},
     *     description="Returns success/error message based on otp condition parameter.",
     *     operationId="user.generate.post.otp",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/contact_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_method_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Success response containing mode of login and other details.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                               ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                 ref="#definitions/PostGenerateOtpResponse"),
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
     *         response=503,
     *         description="There was some error while sending OTP. Please try after some time.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postGenerateOtp(PostGenerateOtpRequest $request)
    {
        $input_params     = $request->input();
        $device_unique_id = $request->getDeviceUniqueId();

        $contact  = $input_params['contact'];
        $dialcode = $input_params['dial_code'];

        // 1 means sms 2 means call.
        $otp_method = $input_params['otp_method'];
        $otp_type   = config('gh')['login_otp_type'];

        $user = [
            'contact' => $dialcode.$contact,
        ];

        $send_otp = $this->user_service->generateOtp($user, $otp_method, $device_unique_id, $otp_type);
        if ($send_otp['status'] !== 1) {
            return ApiResponse::serviceUnavailableError(EC_SERVICE_UNAVIALABLE, 'There was some error while sending OTP. Please try after some time.');
        }

        $content = [
            'dial_code'      => $dialcode,
            'contact'        => $contact,
            'sms_sender_ids' => SMS_SENDER_IDS,
        ];

        $response = new PostGenerateOtpResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response, OK_HTTP_STATUS_CODE, $send_otp['message']);

    }//end postGenerateOtp()


    /**
     * Post Reset Password Handler, verify given email or phone and send OTP to reset the password.
     *
     * @param \App\Http\Requests\v1_7\PostUserPasswordResetRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     path="/v1.7/user/reset/password",
     *     tags={"User"},
     *     description="Returns an array containing flags to check if email and sms are sent or not. Also returns error/success message.",
     *     operationId="user.post.reset.password",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_or_phone_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Otp sent to your registered number or email, to reset the password.",
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
     *         description="Error while generating otp.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * @SWG\Response(
     *         response=502,
     *         description="Error while sending the reset password otp email. || Error while sending the reset password otp sms.",
     * @SWG\Schema(
     *   ref="#/definitions/ErrorHttpResponse"),
     *     ),
     * )
     */
    public function postResetPasswordOtp(PostUserPasswordResetRequest $request)
    {
        $input_params = $request->input();

        $reset_password_via = $input_params['email_or_phone'];
        $dial_code          = (false === empty($input_params['dial_code'])) ? $input_params['dial_code'] : '91';
        $otp_type           = config('gh')['forgot_password_otp_type'];

        // Check if entered value is a number (i.e. contact number).
        if (true === is_numeric($reset_password_via)) {
            $mobile = $reset_password_via;

            $check_if_user_exists_by_mobile = User::getUserByMobileNumber($mobile, $dial_code);

            // If no user exists with given number.
            if (0 === count($check_if_user_exists_by_mobile)) {
                // Return ApiResponse::error(array('email_sent' => 0, 'sms_sent' => 0, 'message' => ERROR_CODE_INVALID_MOBILE));.
                return ApiResponse::notFoundError(EC_NOT_FOUND, ERROR_CODE_CONTACT_NOT_FOUND);
            } else {
                $user = $check_if_user_exists_by_mobile[0];

                // Dial Code is needed, to identify the specific user, in case, more than 1 user, have same mobile number.
                if (1 < count($check_if_user_exists_by_mobile) && true === empty($dial_code)) {
                    return ApiResponse::validationFailed(['dial_code' => 'The dial_code field is required.']);
                }
            }
        } else {
            // Entered value is not numeric (i.e. email).
            $email = $reset_password_via;
            // Get user by email id.
            $user = User::getUserByEmail($email);

            if (true === empty($user)) {
                return ApiResponse::notFoundError(EC_NOT_FOUND, ERROR_CODE_EMAIL_NOT_REGISTERED);
            }
        }//end if

        // Check if daily and hourly otp send limit is reached.
        $is_otp_limit_reached = SmsOtp::isOtpSendLimitReachedToResetPassword($user->id);
        if (true === $is_otp_limit_reached) {
            return ApiResponse::forbiddenError(EC_RESET_PASSWORD_OTP_LIMIT_REACHED, ERROR_CODE_RESET_PASSWORD_OTP_LIMIT_REACHED);
        }

        // Generate a new otp code.
        $device_unique_id = $request->getDeviceUniqueId();

        $check_if_valid_otp_exists = SmsOtp::checkIfValidOtpExists($dial_code.$user->contact, $device_unique_id, $otp_type, 0, $user->id);

        // Get valid otp for user.
        if (empty($check_if_valid_otp_exists) === false) {
            $otp_code = SmsOtp::getExistingOtpForUser($dial_code.$user->contact, $otp_type, $user->id);
        } else {
            $otp_code = SmsOtp::generateValidOtpForContact($dial_code.$user->contact, $device_unique_id, $otp_type, $user->id);
        }

        if (true === empty($otp_code)) {
            return ApiResponse::serverError(EC_SERVER_ERROR, ERROR_CODE_OTP_GENERATION_FAILED);
        }

        $content = [
            'email_sent'     => 0,
            'sms_sent'       => 0,
            'contact'        => $user->contact,
            'sms_sender_ids' => '',
            'message'        => '',
        ];

        // If user has contact number.
        if (false === empty($user->contact)) {
            $content['sms_sent']       = 1;
            $content['sms_sender_ids'] = SMS_SENDER_IDS;
            $content['message']        = 'OTP sent on your registered mobile number.';
        }

        // If user has an email.
        if (false === empty($user->email)) {
            $content['email_sent'] = 1;
            $content['message']    = 'OTP sent on registered email id';
        }

        if (false === empty($user->contact) && false === empty($user->email)) {
            $content['message'] = 'OTP sent on your both registered email id and mobile number.';
        }

        // Send user Reset password email and sms.
        $user_reset_password_event = new UserResetPassword($user->email, '', $user->dial_code, $user->contact, $otp_code);
        Event::dispatch($user_reset_password_event);

        $response = new PostUserPasswordResetResponse($content);
        $response = $response->toArray();
        return ApiResponse::success($response);

    }//end postResetPasswordOtp()


    /**
     * Put Reset Password Handler, change password for given email or phone.
     *
     * @param \App\Http\v1_7\PutUserResetPasswordRequest $request Http request object.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Put(
     *     path="/v1.7/user/reset/password",
     *     tags={"User"},
     *     description="Returns success message when password updated successfully.",
     *     operationId="user.put.password",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     * @SWG\Parameter(ref="#/parameters/device_unique_id_in_header"),
     * @SWG\Parameter(ref="#/parameters/email_or_phone_in_form"),
     * @SWG\Parameter(ref="#/parameters/otp_code_in_form"),
     * @SWG\Parameter(ref="#/parameters/base64encode_password_in_form"),
     * @SWG\Parameter(ref="#/parameters/dial_code_optional_in_form"),
     * @SWG\Response(
     *         response=200,
     *         description="Password changed successfully.",
     * @SWG\Schema(
     * @SWG\Property(property="status",                                  ref="#/definitions/SuccessHttpResponse/properties/status"),
     * @SWG\Property(property="data",                                    ref="#definitions/PutUserResetPasswordResponse"),
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
    public function putResetPassword(PutUserResetPasswordRequest $request)
    {
        $input_params     = $request->input();
        $device_unique_id = $request->getDeviceUniqueId();

        // Check If user exist or not.
        if (ctype_digit($input_params['email_or_phone']) === true) {
            $user = $this->user_service->getUserByMobileNumber($input_params['email_or_phone'], $input_params['dial_code']);
            $input_params['email_or_phone'] = $input_params['dial_code'].$input_params['email_or_phone'];
        } else {
            $user = $this->user_service->getUserByEmail($input_params['email_or_phone']);
        }

        if (empty($user) === true) {
            return ApiResponse::notFoundError(EC_NOT_FOUND, 'User not found.');
        }

        $user = (isset($user[0]) === true) ? $user[0] : $user;

        // Get last sent otp to user.
        $otp_status = SmsOtp::checkForOtpMatch($input_params['otp_code'], $input_params['email_or_phone'], $device_unique_id, OTP_TYPES['reset_password'], 1, $user->id);

        // User has different code assigned to him.
        if ($otp_status['status'] === 0) {
            return ApiResponse::forbiddenError(EC_INVALID_OTP, $otp_status['message']);
        }

        // Update user password.
        $is_password_updated = User::updateUserDetails($user->id, ['password' => Hash::make($input_params['password'])]);

        // If password not updated.
        if ($is_password_updated === false) {
            return ApiResponse::serverError(EC_SERVER_ERROR, 'There was some error while updating password. Please try again.');
        }

        return ApiResponse::success([], OK_HTTP_STATUS_CODE, 'Password changed successfully.');

    }//end putResetPassword()


}//end class
