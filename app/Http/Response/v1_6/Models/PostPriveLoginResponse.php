<?php
/**
 * Response Model for Prive Owner Login
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPriveLoginResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPriveLoginResponse",
 * description="Response Model for Prive Owner Login",
 * )
 * // phpcs:enable
 */
class PostPriveLoginResponse extends ApiResponse
{

    /**
     * User Profile Data
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_profile",
	 *   type="object",
	 *   default="{}",
	 *   description="User Profile Data",
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Property Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="profile_image",
	 *       type="string",
	 *       default="",
	 *       description="User Profile Image Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="user_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="User Hash Id"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $user_profile = [];

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
     * Permissions
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="permissions",
	 *   type="object",
	 *   default="{}",
	 *   description="Permissions",
	 *     @SWG\Property(
	 *       property="product",
	 *       type="array",
	 *       default="[]",
	 *       description="Product",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Permisssion Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Permisssion Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="permisssion_via",
	 *           type="string",
	 *           default="",
	 *           description="Permisssion Via"
	 *         ),
	 *         @SWG\Property(
	 *           property="role_id",
	 *           type="integer",
	 *           default="0",
	 *           description="Role Id"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $permissions = [];

    /**
     * User Roles
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="roles",
	 *   type="object",
	 *   default="{}",
	 *   description="User Roles",
	 *     @SWG\Property(
	 *       property="product",
	 *       type="array",
	 *       default="[]",
	 *       description="Role product",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Role Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Role Name"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $roles = [];


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
     * Get Permissions
     *
     * @return object
     */
    public function getPermissions()
    {
        return (empty($this->permissions) === false) ? $this->permissions : new \stdClass;

    }//end getPermissions()


    /**
     * Set Permissions
     *
     * @param array $permissions Permissions.
     *
     * @return self
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
        return $this;

    }//end setPermissions()


    /**
     * Get Roles
     *
     * @return object
     */
    public function getRoles()
    {
        return (empty($this->roles) === false) ? $this->roles : new \stdClass;

    }//end getRoles()


    /**
     * Set Roles
     *
     * @param array $roles Roles.
     *
     * @return self
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;

    }//end setRoles()


}//end class
