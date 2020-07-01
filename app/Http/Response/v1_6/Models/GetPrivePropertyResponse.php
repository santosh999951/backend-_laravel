<?php
/**
 * GetPrivePropertyResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPrivePropertyResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPrivePropertyResponse",
 * description="GetPrivePropertyResponse",
 * )
 * // phpcs:enable
 */
class GetPrivePropertyResponse extends ApiResponse
{

    /**
     * Properties
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="properties",
	 *   type="array",
	 *   default="[]",
	 *   description="Properties",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="properties_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Properties Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="image",
	 *           type="string",
	 *           default="",
	 *           description="Image"
	 *         ),
	 *         @SWG\Property(
	 *           property="caption",
	 *           type="string",
	 *           default="",
	 *           description="Caption"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="property_title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="url",
	 *       type="string",
	 *       default="",
	 *       description="Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="units",
	 *       type="string",
	 *       default="",
	 *       description="Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="City"
	 *     ),
	 *     @SWG\Property(
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="State"
	 *     ),
	 *     @SWG\Property(
	 *       property="per_night_price",
	 *       type="string",
	 *       default="",
	 *       description="Per Night Price"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $properties = [];

    /**
     * Total Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Total Count"
	 * )
     * // phpcs:enable
     */
    protected $total_count = 0;


    /**
     * Get Properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;

    }//end getProperties()


    /**
     * Set Properties
     *
     * @param array $properties Properties.
     *
     * @return self
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
        return $this;

    }//end setProperties()


    /**
     * Get Total_count
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->total_count;

    }//end getTotalCount()


    /**
     * Set Total count
     *
     * @param integer $total_count Total count.
     *
     * @return self
     */
    public function setTotalCount(int $total_count)
    {
        $this->total_count = $total_count;
        return $this;

    }//end setTotalCount()


}//end class
