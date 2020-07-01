<?php
/**
 * GetPriveManagerPropertyResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPriveManagerPropertyResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPriveManagerPropertyResponse",
 * description="GetPriveManagerPropertyResponse",
 * )
 * // phpcs:enable
 */
class GetPriveManagerPropertyResponse extends ApiResponse
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
	 *       property="property_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Property Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="image",
	 *           type="string",
	 *           default="",
	 *           description="Property Image"
	 *         ),
	 *         @SWG\Property(
	 *           property="caption",
	 *           type="string",
	 *           default="",
	 *           description="Property Caption"
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
	 *       property="location",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Location",
	 *         @SWG\Property(
	 *           property="area",
	 *           type="string",
	 *           default="",
	 *           description="Property Area"
	 *         ),
	 *         @SWG\Property(
	 *           property="city",
	 *           type="string",
	 *           default="",
	 *           description="Property City"
	 *         ),
	 *         @SWG\Property(
	 *           property="state",
	 *           type="string",
	 *           default="",
	 *           description="Property State"
	 *         ),
	 *         @SWG\Property(
	 *           property="country",
	 *           type="object",
	 *           default="{}",
	 *           description="Property Country",
	 *             @SWG\Property(
	 *               property="name",
	 *               type="string",
	 *               default="",
	 *               description="Property Name"
	 *             ),
	 *             @SWG\Property(
	 *               property="ccode",
	 *               type="string",
	 *               default="",
	 *               description="Property Ccode"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="location_name",
	 *           type="string",
	 *           default="",
	 *           description="Property Location Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="latitude",
	 *           type="string",
	 *           default="",
	 *           description="Property Latitude"
	 *         ),
	 *         @SWG\Property(
	 *           property="longitude",
	 *           type="string",
	 *           default="",
	 *           description="Property Longitude"
	 *         )
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
	 *       property="per_night_price",
	 *       type="string",
	 *       default="",
	 *       description="Per Night Price"
	 *     ),
	 *     @SWG\Property(
	 *       property="occupancy_rate",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Occupancy Rate"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $properties = [];

    /**
     * Property Total
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Total"
	 * )
     * // phpcs:enable
     */
    protected $total = 0;


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
     * Get Total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;

    }//end getTotal()


    /**
     * Set Total
     *
     * @param integer $total Total.
     *
     * @return self
     */
    public function setTotal(int $total)
    {
        $this->total = $total;
        return $this;

    }//end setTotal()


}//end class
