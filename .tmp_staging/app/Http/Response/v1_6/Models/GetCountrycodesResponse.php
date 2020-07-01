<?php
/**
 * Response Model for Country codes List Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetCountrycodesResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetCountrycodesResponse",
 * description="Response Model for Country codes List Api",
 * )
 * // phpcs:enable
 */
class GetCountrycodesResponse extends ApiResponse
{

    /**
     * Country Codes Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="country_codes",
	 *   type="array",
	 *   default="[]",
	 *   description="Country Codes Data",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="country_code",
	 *       type="string",
	 *       default="",
	 *       description="Country Code"
	 *     ),
	 *     @SWG\Property(
	 *       property="country_name",
	 *       type="string",
	 *       default="",
	 *       description="Country Name"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $country_codes = [];


    /**
     * Get Country_codes
     *
     * @return array
     */
    public function getCountryCodes()
    {
        return $this->country_codes;

    }//end getCountryCodes()


    /**
     * Set Country codes
     *
     * @param array $country_codes Country codes.
     *
     * @return self
     */
    public function setCountryCodes(array $country_codes)
    {
        $this->country_codes = $country_codes;
        return $this;

    }//end setCountryCodes()


}//end class
