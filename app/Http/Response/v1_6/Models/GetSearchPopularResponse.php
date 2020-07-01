<?php
/**
 * Response Model for Popular Search Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetSearchPopularResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetSearchPopularResponse",
 * description="Response Model for Popular Search Api",
 * )
 * // phpcs:enable
 */
class GetSearchPopularResponse extends ApiResponse
{

    /**
     * Popular Search Location List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="list",
	 *   type="array",
	 *   default="[]",
	 *   description="Popular Search Location List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="location",
	 *       type="string",
	 *       default="",
	 *       description="Popular Location"
	 *     ),
	 *     @SWG\Property(
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="Location State"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="Location City"
	 *     ),
	 *     @SWG\Property(
	 *       property="area",
	 *       type="string",
	 *       default="",
	 *       description="Location Area"
	 *     ),
	 *     @SWG\Property(
	 *       property="country",
	 *       type="object",
	 *       default="{}",
	 *       description="Location Country",
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Country Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="ccode",
	 *           type="string",
	 *           default="",
	 *           description="Country Ccode"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="latitude",
	 *       type="string",
	 *       default="",
	 *       description="Latitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="longitude",
	 *       type="string",
	 *       default="",
	 *       description="Longitude"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $list = [];


    /**
     * Get List
     *
     * @return array
     */
    public function getList()
    {
        return $this->list;

    }//end getList()


    /**
     * Set List
     *
     * @param array $list List.
     *
     * @return self
     */
    public function setList(array $list)
    {
        $this->list = $list;
        return $this;

    }//end setList()


}//end class
