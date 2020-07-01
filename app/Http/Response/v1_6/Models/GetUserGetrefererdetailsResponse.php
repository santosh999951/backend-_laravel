<?php
/**
 * Response for User referer details Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetUserGetrefererdetailsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetUserGetrefererdetailsResponse",
 * description="Response for User referer details Api",
 * )
 * // phpcs:enable
 */
class GetUserGetrefererdetailsResponse extends ApiResponse
{

    /**
     * User Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_name",
	 *   type="string",
	 *   default="",
	 *   description="User Name"
	 * )
     * // phpcs:enable
     */
    protected $user_name = '';

    /**
     * User Image
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_image",
	 *   type="string",
	 *   default="",
	 *   description="User Image"
	 * )
     * // phpcs:enable
     */
    protected $user_image = '';

    /**
     * Referral Code
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="referral_code",
	 *   type="string",
	 *   default="",
	 *   description="Referral Code"
	 * )
     * // phpcs:enable
     */
    protected $referral_code = '';

    /**
     * Brief description
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="brief",
	 *   type="string",
	 *   default="",
	 *   description="Brief description"
	 * )
     * // phpcs:enable
     */
    protected $brief = '';

    /**
     * Detail
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="detail",
	 *   type="string",
	 *   default="",
	 *   description="Detail"
	 * )
     * // phpcs:enable
     */
    protected $detail = '';


    /**
     * Get User_name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;

    }//end getUserName()


    /**
     * Set User name
     *
     * @param string $user_name User name.
     *
     * @return self
     */
    public function setUserName(string $user_name)
    {
        $this->user_name = $user_name;
        return $this;

    }//end setUserName()


    /**
     * Get User_image
     *
     * @return string
     */
    public function getUserImage()
    {
        return $this->user_image;

    }//end getUserImage()


    /**
     * Set User image
     *
     * @param string $user_image User image.
     *
     * @return self
     */
    public function setUserImage(string $user_image)
    {
        $this->user_image = $user_image;
        return $this;

    }//end setUserImage()


    /**
     * Get Referral_code
     *
     * @return string
     */
    public function getReferralCode()
    {
        return $this->referral_code;

    }//end getReferralCode()


    /**
     * Set Referral code
     *
     * @param string $referral_code Referral code.
     *
     * @return self
     */
    public function setReferralCode(string $referral_code)
    {
        $this->referral_code = $referral_code;
        return $this;

    }//end setReferralCode()


    /**
     * Get Brief
     *
     * @return string
     */
    public function getBrief()
    {
        return $this->brief;

    }//end getBrief()


    /**
     * Set Brief
     *
     * @param string $brief Brief.
     *
     * @return self
     */
    public function setBrief(string $brief)
    {
        $this->brief = $brief;
        return $this;

    }//end setBrief()


    /**
     * Get Detail
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;

    }//end getDetail()


    /**
     * Set Detail
     *
     * @param string $detail Detail.
     *
     * @return self
     */
    public function setDetail(string $detail)
    {
        $this->detail = $detail;
        return $this;

    }//end setDetail()


}//end class
