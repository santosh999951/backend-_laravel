<?php
/**
 * Response Model for Dial code Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetDialcodesResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetDialcodesResponse",
 * description="Response Model for Dial code Api",
 * )
 * // phpcs:enable
 */
class GetDialcodesResponse extends ApiResponse
{

    /**
     * Dial Codes data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="dial_codes",
	 *   type="array",
	 *   default="[]",
	 *   description="Dial Codes data",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="path",
	 *       type="string",
	 *       default="",
	 *       description="Path Country Icon Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="dial_code",
	 *       type="string",
	 *       default="",
	 *       description="Dial Code Eg. 91 for India, 93 for Afghanistan"
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
    protected $dial_codes = [];


    /**
     * Get Dial_codes
     *
     * @return array
     */
    public function getDialCodes()
    {
        return $this->dial_codes;

    }//end getDialCodes()


    /**
     * Set Dial codes
     *
     * @param array $dial_codes Dial codes.
     *
     * @return self
     */
    public function setDialCodes(array $dial_codes)
    {
        $this->dial_codes = $dial_codes;
        return $this;

    }//end setDialCodes()


}//end class
