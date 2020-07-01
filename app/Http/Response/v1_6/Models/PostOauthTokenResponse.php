<?php
/**
 * Response Model for Login Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostOauthTokenResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostOauthTokenResponse",
 * description="Response Model for Login Api",
 * )
 * // phpcs:enable
 */
class PostOauthTokenResponse extends ApiResponse
{

    /**
     * Token Type Eg. Bearer
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="token_type",
	 *   type="string",
	 *   default="",
	 *   description="Token Type Eg. Bearer"
	 * )
     * // phpcs:enable
     */
    protected $token_type = '';

    /**
     * Token Expire Time
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="expires_in",
	 *   type="integer",
	 *   default="0",
	 *   description="Token Expire Time"
	 * )
     * // phpcs:enable
     */
    protected $expires_in = 0;

    /**
     * Access Token
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="access_token",
	 *   type="string",
	 *   default="",
	 *   description="Access Token"
	 * )
     * // phpcs:enable
     */
    protected $access_token = '';

    /**
     * User Profile Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_profile",
	 *   type="object",
	 *   default="{}",
	 *   description="User Profile Section",
	 *     @SWG\Property(
	 *       property="first_name",
	 *       type="string",
	 *       default="",
	 *       description="User First Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="last_name",
	 *       type="string",
	 *       default="",
	 *       description="User Last Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="member_since",
	 *       type="string",
	 *       default="",
	 *       description="User Membership in Guesthouser"
	 *     ),
	 *     @SWG\Property(
	 *       property="dob",
	 *       type="string",
	 *       default="",
	 *       description="User Date of birth"
	 *     ),
	 *     @SWG\Property(
	 *       property="marital_status",
	 *       type="string",
	 *       default="",
	 *       description="User Marital Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="gender",
	 *       type="string",
	 *       default="",
	 *       description="User Gender"
	 *     ),
	 *     @SWG\Property(
	 *       property="profession",
	 *       type="string",
	 *       default="",
	 *       description="User Profession"
	 *     ),
	 *     @SWG\Property(
	 *       property="email",
	 *       type="string",
	 *       default="",
	 *       description="User Email"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_email_verified",
	 *       type="integer",
	 *       default="0",
	 *       description="User Email Verified Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="dial_code",
	 *       type="string",
	 *       default="",
	 *       description="Conatct Dial Code Eg. 91 for India"
	 *     ),
	 *     @SWG\Property(
	 *       property="mobile",
	 *       type="string",
	 *       default="",
	 *       description="User Contact Number"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_mobile_verified",
	 *       type="integer",
	 *       default="0",
	 *       description="User Mobile Verified Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_user_referred",
	 *       type="integer",
	 *       default="0",
	 *       description="User Referred By Other User status"
	 *     ),
	 *     @SWG\Property(
	 *       property="profile_image",
	 *       type="string",
	 *       default="",
	 *       description="User Profile Image Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_avatar_image",
	 *       type="integer",
	 *       default="0",
	 *       description="User Avatar Image Status when user profile picture not stored"
	 *     ),
	 *     @SWG\Property(
	 *       property="spoken_languages",
	 *       type="string",
	 *       default="",
	 *       description="User Spoken Languages"
	 *     ),
	 *     @SWG\Property(
	 *       property="travelled_places",
	 *       type="string",
	 *       default="",
	 *       description="User Travelled Places"
	 *     ),
	 *     @SWG\Property(
	 *       property="description",
	 *       type="string",
	 *       default="",
	 *       description="User Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests_served_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Guests Served Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="trips_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Trips Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="user_currency",
	 *       type="string",
	 *       default="",
	 *       description="User Currency"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_host",
	 *       type="boolean",
	 *       default="false",
	 *       description="User Is Host Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="user_id",
	 *       type="integer",
	 *       default="0",
	 *       description="User Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="user_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="User Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="fb_id",
	 *       type="string",
	 *       default="",
	 *       description="Facebook Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="google_id",
	 *       type="string",
	 *       default="",
	 *       description="Google Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="wallet",
	 *       type="object",
	 *       default="{}",
	 *       description="User Wallet",
	 *         @SWG\Property(
	 *           property="balance",
	 *           type="integer",
	 *           default="0",
	 *           description="Wallet Balance"
	 *         ),
	 *         @SWG\Property(
	 *           property="currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Wallet Currency",
	 *             @SWG\Property(
	 *               property="webicon",
	 *               type="string",
	 *               default="",
	 *               description="Currency Webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="non-webicon",
	 *               type="string",
	 *               default="",
	 *               description="Currency Non-webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="iso_code",
	 *               type="string",
	 *               default="",
	 *               description="Currency Iso Code"
	 *             )
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="add_listing",
	 *       type="string",
	 *       default="",
	 *       description="Add Listing Url for Adding Property via web"
	 *     ),
	 *     @SWG\Property(
	 *       property="event",
	 *       type="string",
	 *       default="",
	 *       description="Event status Eg. login"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_rm",
	 *       type="integer",
	 *       default="0",
	 *       description="User Is Relationship manager status"
	 *     ),
	 *     @SWG\Property(
	 *       property="add_email",
	 *       type="integer",
	 *       default="0",
	 *       description="Add Email Status when Email of user not exist"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_token",
	 *       type="string",
	 *       default="",
	 *       description="Host Token for host login in v1.5 Api"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $user_profile = [];

    /**
     * Refresh Token
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="refresh_token",
	 *   type="string",
	 *   default="",
	 *   description="Refresh Token"
	 * )
     * // phpcs:enable
     */
    protected $refresh_token = '';


    /**
     * Get Token_type
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->token_type;

    }//end getTokenType()


    /**
     * Set Token type
     *
     * @param string $token_type Token type.
     *
     * @return self
     */
    public function setTokenType(string $token_type)
    {
        $this->token_type = $token_type;
        return $this;

    }//end setTokenType()


    /**
     * Get Expires_in
     *
     * @return integer
     */
    public function getExpiresIn()
    {
        return $this->expires_in;

    }//end getExpiresIn()


    /**
     * Set Expires in
     *
     * @param integer $expires_in Expires in.
     *
     * @return self
     */
    public function setExpiresIn(int $expires_in)
    {
        $this->expires_in = $expires_in;
        return $this;

    }//end setExpiresIn()


    /**
     * Get Access_token
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;

    }//end getAccessToken()


    /**
     * Set Access token
     *
     * @param string $access_token Access token.
     *
     * @return self
     */
    public function setAccessToken(string $access_token)
    {
        $this->access_token = $access_token;
        return $this;

    }//end setAccessToken()


    /**
     * Get User_profile
     *
     * @return object
     */
    public function getUserProfile()
    {
        return (empty($this->user_profile) === false) ? $this->user_profile : new \stdClass;

    }//end getUserProfile()


    /**
     * Set User profile
     *
     * @param array $user_profile User profile.
     *
     * @return self
     */
    public function setUserProfile(array $user_profile)
    {
        $this->user_profile = $user_profile;
        return $this;

    }//end setUserProfile()


    /**
     * Get Refresh_token
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;

    }//end getRefreshToken()


    /**
     * Set Refresh token
     *
     * @param string $refresh_token Refresh token.
     *
     * @return self
     */
    public function setRefreshToken(string $refresh_token)
    {
        $this->refresh_token = $refresh_token;
        return $this;

    }//end setRefreshToken()


}//end class
