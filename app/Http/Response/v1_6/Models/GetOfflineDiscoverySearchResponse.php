<?php
/**
 * GetOfflineDiscoverySearchResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetOfflineDiscoverySearchResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetOfflineDiscoverySearchResponse",
 * description="GetOfflineDiscoverySearchResponse",
 * )
 * // phpcs:enable
 */
class GetOfflineDiscoverySearchResponse extends ApiResponse
{

    /**
     * Property Property List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_list",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Property List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="status",
	 *       type="string",
	 *       default="",
	 *       description="Property Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="image",
	 *       type="none",
	 *       default="00",
	 *       description="Property Image"
	 *     ),
	 *     @SWG\Property(
	 *       property="contact",
	 *       type="string",
	 *       default="",
	 *       description="Property Owner Contact"
	 *     ),
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Property Owner Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="area",
	 *       type="string",
	 *       default="",
	 *       description="Property Area"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="Property City"
	 *     ),
	 *     @SWG\Property(
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="Property State"
	 *     ),
	 *     @SWG\Property(
	 *       property="country",
	 *       type="string",
	 *       default="",
	 *       description="Property Country"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $property_list = [];


    /**
     * Get Property_list
     *
     * @return array
     */
    public function getPropertyList()
    {
        return $this->property_list;

    }//end getPropertyList()


    /**
     * Set Property list
     *
     * @param array $property_list Property list.
     *
     * @return self
     */
    public function setPropertyList(array $property_list)
    {
        $this->property_list = $property_list;
        return $this;

    }//end setPropertyList()


}//end class
