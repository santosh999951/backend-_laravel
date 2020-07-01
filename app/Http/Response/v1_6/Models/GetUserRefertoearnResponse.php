<?php
/**
 * Response Model for User Refer to earn API
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetUserRefertoearnResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetUserRefertoearnResponse",
 * description="Response Model for User Refer to earn API",
 * )
 * // phpcs:enable
 */
class GetUserRefertoearnResponse extends ApiResponse
{

    /**
     * Headline of Refer to earn
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="headline",
	 *   type="string",
	 *   default="",
	 *   description="Headline of Refer to earn"
	 * )
     * // phpcs:enable
     */
    protected $headline = '';

    /**
     * Refer to earn Description
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="description",
	 *   type="string",
	 *   default="",
	 *   description="Refer to earn Description"
	 * )
     * // phpcs:enable
     */
    protected $description = '';

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
     * Refer Url
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="refer_url",
	 *   type="string",
	 *   default="",
	 *   description="Refer Url"
	 * )
     * // phpcs:enable
     */
    protected $refer_url = '';


    /**
     * Get Headline
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;

    }//end getHeadline()


    /**
     * Set Headline
     *
     * @param string $headline Headline.
     *
     * @return self
     */
    public function setHeadline(string $headline)
    {
        $this->headline = $headline;
        return $this;

    }//end setHeadline()


    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;

    }//end getDescription()


    /**
     * Set Description
     *
     * @param string $description Description.
     *
     * @return self
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;

    }//end setDescription()


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
     * Get Refer_url
     *
     * @return string
     */
    public function getReferUrl()
    {
        return $this->refer_url;

    }//end getReferUrl()


    /**
     * Set Refer url
     *
     * @param string $refer_url Refer url.
     *
     * @return self
     */
    public function setReferUrl(string $refer_url)
    {
        $this->refer_url = $refer_url;
        return $this;

    }//end setReferUrl()


}//end class
