<?php
/**
 * PostLoginResponse
 */

namespace App\Http\Response\v1_7\Models;

use App\Http\Response\v1_7\Models\Partial\UserProfileResponse;

/**
 * Class PostLoginResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostLoginResponse",
 * description="PostLoginResponse",
 * )
 * // phpcs:enable
 */
class PostLoginResponse extends ApiResponse
{

    /**
     * Property User Profile
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_profile",
	 *   type="object",
	 *   default="{}",
	 * ref="#/definitions/UserProfileResponse",
	 *   description="Property User Profile"
	 * )
     * // phpcs:enable
     */
    protected $user_profile = [];

    /**
     * Property Token Type
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="token_type",
	 *   type="string",
	 *   default="",
	 *   description="Property Token Type"
	 * )
     * // phpcs:enable
     */
    protected $token_type = '';

    /**
     * Property Expires In
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="expires_in",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Expires In"
	 * )
     * // phpcs:enable
     */
    protected $expires_in = 0;

    /**
     * Property Access Token
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="access_token",
	 *   type="string",
	 *   default="",
	 *   description="Property Access Token"
	 * )
     * // phpcs:enable
     */
    protected $access_token = '';

    /**
     * Property Refresh Token
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="refresh_token",
	 *   type="string",
	 *   default="",
	 *   description="Property Refresh Token"
	 * )
     * // phpcs:enable
     */
    protected $refresh_token = '';


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
        $response            = new UserProfileResponse($user_profile);
         $response           = $response->toArray();
         $this->user_profile = $response;
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


}//end class
