<?php

/**
 * GetCountryDetailsResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetCountryDetailsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetCountryDetailsResponse",
 * description="GetCountryDetailsResponse",
 * )
 * // phpcs:enable
 */
class GetCountryDetailsResponse extends ApiResponse
{

    /**
     * Country Details
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="country_details",
	 *   type="array",
	 *   default="[]",
	 *   description="Country Details",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Country Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="ccode",
	 *       type="string",
	 *       default="",
	 *       description="Country code"
	 *     ),
	 *     @SWG\Property(
	 *       property="dial_code",
	 *       type="string",
	 *       default="",
	 *       description="Country Dial Code"
	 *     ),
	 *     @SWG\Property(
	 *       property="image",
	 *       type="string",
	 *       default="",
	 *       description="Country flag Image"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $country_details = [];


    /**
     * Get Country_details
     *
     * @return array
     */
    public function getCountryDetails()
    {
        return $this->country_details;

    }//end getCountryDetails()


    /**
     * Set Country details
     *
     * @param array $country_details Country details.
     *
     * @return self
     */
    public function setCountryDetails(array $country_details)
    {
        $this->country_details = $country_details;
        return $this;

    }//end setCountryDetails()


}//end class
