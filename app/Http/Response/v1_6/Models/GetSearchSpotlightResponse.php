<?php
/**
 * Response Model for Spotlight Api Used in iOS
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetSearchSpotlightResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetSearchSpotlightResponse",
 * description="Response Model for Spotlight Api Used in iOS",
 * )
 * // phpcs:enable
 */
class GetSearchSpotlightResponse extends ApiResponse
{

    /**
     * Spotlight List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="spotlight_list",
	 *   type="array",
	 *   default="[]",
	 *   description="Spotlight List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="property_type_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Type Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_type",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="Property State"
	 *     ),
	 *     @SWG\Property(
	 *       property="tag",
	 *       type="string",
	 *       default="",
	 *       description="Tag"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="Property City"
	 *     ),
	 *     @SWG\Property(
	 *       property="country",
	 *       type="string",
	 *       default="",
	 *       description="Property Country"
	 *     ),
	 *     @SWG\Property(
	 *       property="country_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Country Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="v_lat",
	 *       type="string",
	 *       default="",
	 *       description="Property Virtual Latitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="v_lng",
	 *       type="string",
	 *       default="",
	 *       description="Property Virtual Longitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="image_url",
	 *       type="string",
	 *       default="",
	 *       description="Property Image Url"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $spotlight_list = [];


    /**
     * Get Spotlight_list
     *
     * @return array
     */
    public function getSpotlightList()
    {
        return $this->spotlight_list;

    }//end getSpotlightList()


    /**
     * Set Spotlight list
     *
     * @param array $spotlight_list Spotlight list.
     *
     * @return self
     */
    public function setSpotlightList(array $spotlight_list)
    {
        $this->spotlight_list = $spotlight_list;
        return $this;

    }//end setSpotlightList()


}//end class
