<?php
/**
 * PostPriveRegisterResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPriveRegisterResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPriveRegisterResponse",
 * description="PostPriveRegisterResponse",
 * )
 * // phpcs:enable
 */
class PostPriveRegisterResponse extends ApiResponse
{

    /**
     * User Profile
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_profile",
	 *   type="array",
	 *   default="[]",
	 *   description="User Profile",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="first_name",
	 *       type="string",
	 *       default="",
	 *       description="First Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="last_name",
	 *       type="string",
	 *       default="",
	 *       description="Last Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="member_since",
	 *       type="string",
	 *       default="",
	 *       description="Member Since"
	 *     ),
	 *     @SWG\Property(
	 *       property="dob",
	 *       type="string",
	 *       default="",
	 *       description="Dob"
	 *     ),
	 *     @SWG\Property(
	 *       property="marital_status",
	 *       type="string",
	 *       default="",
	 *       description="Marital Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="gender",
	 *       type="string",
	 *       default="",
	 *       description="Gender"
	 *     ),
	 *     @SWG\Property(
	 *       property="profession",
	 *       type="string",
	 *       default="",
	 *       description="Profession"
	 *     ),
	 *     @SWG\Property(
	 *       property="email",
	 *       type="string",
	 *       default="",
	 *       description="Email"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_email_verified",
	 *       type="integer",
	 *       default="0",
	 *       description="Is Email Verified"
	 *     ),
	 *     @SWG\Property(
	 *       property="dial_code",
	 *       type="string",
	 *       default="",
	 *       description="Dial Code"
	 *     ),
	 *     @SWG\Property(
	 *       property="mobile",
	 *       type="string",
	 *       default="",
	 *       description="Mobile"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_mobile_verified",
	 *       type="integer",
	 *       default="0",
	 *       description="Is Mobile Verified"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_user_referred",
	 *       type="integer",
	 *       default="0",
	 *       description="Is User Referred"
	 *     ),
	 *     @SWG\Property(
	 *       property="profile_image",
	 *       type="string",
	 *       default="",
	 *       description="Profile Image"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_avatar_image",
	 *       type="integer",
	 *       default="0",
	 *       description="Is Avatar Image"
	 *     ),
	 *     @SWG\Property(
	 *       property="spoken_languages",
	 *       type="string",
	 *       default="",
	 *       description="Spoken Languages"
	 *     ),
	 *     @SWG\Property(
	 *       property="travelled_places",
	 *       type="string",
	 *       default="",
	 *       description="Travelled Places"
	 *     ),
	 *     @SWG\Property(
	 *       property="description",
	 *       type="string",
	 *       default="",
	 *       description="Description"
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
	 *       property="active_request_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Active Request Count"
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
	 *       description="Is Host"
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
	 *       description="Fb Id"
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
	 *       description="Wallet",
	 *         @SWG\Property(
	 *           property="balance",
	 *           type="integer",
	 *           default="0",
	 *           description="Balance"
	 *         ),
	 *         @SWG\Property(
	 *           property="currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Currency",
	 *             @SWG\Property(
	 *               property="webicon",
	 *               type="string",
	 *               default="",
	 *               description="Webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="non-webicon",
	 *               type="string",
	 *               default="",
	 *               description="Non-webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="iso_code",
	 *               type="string",
	 *               default="",
	 *               description="Iso Code"
	 *             )
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="add_listing",
	 *       type="string",
	 *       default="",
	 *       description="Add Listing"
	 *     ),
	 *     @SWG\Property(
	 *       property="event",
	 *       type="string",
	 *       default="",
	 *       description="Event"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_rm",
	 *       type="integer",
	 *       default="0",
	 *       description="Is Rm"
	 *     ),
	 *     @SWG\Property(
	 *       property="add_email",
	 *       type="integer",
	 *       default="0",
	 *       description="Add Email"
	 *     ),
	 *     @SWG\Property(
	 *       property="auth_key",
	 *       type="string",
	 *       default="",
	 *       description="Auth Key"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_token",
	 *       type="string",
	 *       default="",
	 *       description="Host Token"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $user_profile = [];

    /**
     * Token Type
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="token_type",
	 *   type="string",
	 *   default="",
	 *   description="Token Type"
	 * )
     * // phpcs:enable
     */
    protected $token_type = '';

    /**
     * Expires In
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="expires_in",
	 *   type="integer",
	 *   default="0",
	 *   description="Expires In"
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
     * Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


    /**
     * Get User_profile
     *
     * @return array
     */
    public function getUserProfile()
    {
        return $this->user_profile;

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


    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;

    }//end getMessage()


    /**
     * Set Message
     *
     * @param string $message Message.
     *
     * @return self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;

    }//end setMessage()


}//end class
