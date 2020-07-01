<?php
/**
 * Swagger controller contains all defined params description for swagger documentation
 */

namespace App\Http\Controllers\v1_7;

/**
 * Swagger Documentation
 *
 * @SWG\Swagger(
 *     schemes={"http","https"},
 *     host=API_HOST,
 *     basePath="/",
 * @SWG\Info(
 *         version=1.7,
 *         title="Guesthouser APIs",
 *         description="Api description...",
 *         termsOfService="",
 * @SWG\Contact(
 *             email=DEVELOPER_EMAIL
 *         ),
 * @SWG\License(
 *             name="Private License",
 *             url=API_HOST
 *         )
 *     ),
 * @SWG\ExternalDocumentation(
 *         description="Find out more about my website",
 *         url=API_HOST
 *     ),
 * @SWG\Tag(
 *       name="User"
 *     ),
 * )
 *
 * Security Definition
 *
 * @SWG\SecurityScheme(
 *   securityDefinition="Authorization",
 *   type="oauth2",
 *   in="header",
 *   description="oauth2 access token",
 *   name="Authorization",
 *   flow="password",
 *   tokenUrl="/oauth/token",
 *   scopes={},
 * )
 */

/**
 * Swagger controller contains all defined params description for swagger documentation
 */
class SwaggerController extends Controller
{





    // phpcs:ignore
    /**
     *     @SWG\Parameter(
     *         parameter="device_unique_id_in_header",
     *         in="header",
     *         name="device-unique-id",
     *         type="string",
     *         description="device unique id",
     *         required=true,
     *         default="8hfjdhf84r84kdhdh",
     *     ),
     *     @SWG\Parameter(
     *         parameter="base64encode_password_in_form",
     *         in="formData",
     *         name="password",
     *         description="password as base64 encoded string",
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         parameter="base64encode_password_optional_in_form",
     *         in="formData",
     *         name="password",
     *         description="password as base64 encoded string",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="user_first_name_in_form",
     *         in="formData",
     *         name="first_name",
     *         description="first name",
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         parameter="user_last_name_in_form",
     *         in="formData",
     *         name="last_name",
     *         description="last name",
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         parameter="user_last_name_optional_in_form",
     *         in="formData",
     *         name="last_name",
     *         description="last name",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="dial_code_in_form",
     *         in="formData",
     *         name="dial_code",
     *         description="dial code",
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         parameter="email_optional_in_form",
     *         in="formData",
     *         name="email",
     *         description="email",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="dial_code_optional_in_form",
     *         in="formData",
     *         name="dial_code",
     *         description="dial code",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="contact_number_optional_in_form",
     *         in="formData",
     *         name="contact",
     *         description="user contact number",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="otp_code_optional_in_form",
     *         in="formData",
     *         name="otp_code",
     *         description="otp code",
     *         type="integer",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="loginsignupvia_in_form",
     *         in="formData",
     *         name="loginsignupvia",
     *         description="loginsignupvia Either email or phone",
     *         type="string",
     *         required=true,
     *         default=""
     *     ),
     *     @SWG\Parameter(
     *         parameter="source_in_form",
     *         in="formData",
     *         name="source",
     *         type="integer",
     *         description="source (1 - google, 2 - facebook, 3 - email , 4-phone , 5-Apple)",
     *         required=true,
     *         enum={ 1, 2,3,4,5},
     *         default="1",
     *     ),
     *     @SWG\Parameter(
     *         parameter="source_value_in_form",
     *         in="formData",
     *         name="source_value",
     *         type="string",
     *         description="source value can be email of user ,phone number of user or accesstoken received from oauth",
     *         required=true,
     *         default="",
     *     ),
     *     @SWG\Parameter(
     *         parameter="grant_type_in_form",
     *         in="formData",
     *         name="grant_type",
     *         type="string",
     *         description="grant type : password or otp",
     *         enum={"password", "otp"},
     *         required=true,
     *         default="otp",
     *     ),
     *     @SWG\Parameter(
     *         parameter="login_id_in_query",
     *         in="query",
     *         name="login_id",
     *         type="string",
     *         description="Either email or phone number.",
     *         required=true,
     *         default="",
     *     ),
     *     @SWG\Parameter(
     *         parameter="dial_code_optional_in_query",
     *         in="query",
     *         name="dial_code",
     *         description="dial code",
     *         type="string",
     *         required=false,
     *     ),
     *    @SWG\Parameter(
     *         parameter="contact_in_form",
     *         in="formData",
     *         name="contact",
     *         description="Contact Number.",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         parameter="otp_method_in_form",
     *         in="formData",
     *         name="otp_method",
     *         description="Otp method 1 for sms and 2 for call.",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         parameter="otp_type_in_form",
     *         in="formData",
     *         name="otp_type",
     *         description="Otp type 0 for login and 1 for forget password.",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         parameter="reset_password_via_in_form",
     *         in="formData",
     *         name="reset_password_via",
     *         description="reset password param containing either email or verified mobile",
     *         type="string",
     *         required=true,
     *     ),
     *    @SWG\Parameter(
     *         parameter="otp_code_in_form",
     *         in="formData",
     *         name="otp_code",
     *         description="Otp send to user.",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         parameter="device_unique_id_in_form",
     *         in="formData",
     *         name="device_unique_id",
     *         description="device unique id",
     *         type="string",
     *         required=true,
     *         default="8hfjdhf84r84kdhdh",
     *     ),
     *     @SWG\Parameter(
     *         parameter="device_notification_token_in_form",
     *         in="formData",
     *         name="device_notification_token",
     *         description="device push notification token - GCM tken, APNs, token, ..",
     *         type="string",
     *         required=false,
     *         default="wCcFqBgyz9T9PUUpf8qiMU88GuOMaCo489NrBlhI",
     *     ),
     *     @SWG\Parameter(
     *         parameter="app_version_in_form",
     *         in="formData",
     *         name="app_version",
     *         description="app version (different from api version)",
     *         type="string",
     *         required=false,
     *     ),
     *    @SWG\Parameter(
     *         parameter="device_model_in_form",
     *         in="formData",
     *         name="device_model",
     *         description="device modal",
     *         type="string",
     *         required=false,
     *     ),
     *   @SWG\Parameter(
     *         parameter="device_make_in_form",
     *         in="formData",
     *         name="device_make",
     *         description="maker of device. Eg - apple, motorola,...",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="brand_in_form",
     *         in="formData",
     *         name="brand",
     *         description="device brand",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="os_version_in_form",
     *         in="formData",
     *         name="os_version",
     *         description="os version. Eg - 10.0.1, 11.2.1",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="resolution_in_form",
     *         in="formData",
     *         name="resolution",
     *         description="device resolution",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="country_in_form",
     *         in="formData",
     *         name="country",
     *         description="user country",
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         parameter="screen_width_in_form",
     *         in="formData",
     *         name="screen_width",
     *         description="device screen width",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="screen_height_in_form",
     *         in="formData",
     *         name="screen_height",
     *         description="device screen height",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="ram_in_form",
     *         in="formData",
     *         name="ram",
     *         description="device RAM",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="dpi_in_form",
     *         in="formData",
     *         name="dpi",
     *         description="device DPI",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="app_version_code_in_form",
     *         in="formData",
     *         name="app_version_code",
     *         description="app version code",
     *         type="integer",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="fcm_token_in_form",
     *         in="formData",
     *         name="fcm_token",
     *         description="fcm token",
     *         type="string",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         parameter="email_or_phone_in_form",
     *         in="formData",
     *         name="email_or_phone",
     *         description="Email id or phone number of user.",
     *         type="string",
     *         required=true,
     *     ),
    */

}//end class
