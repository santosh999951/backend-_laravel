<?php
/**
 * Properties list of host response
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostPropertiesResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostPropertiesResponse",
 * description="Properties list of host response",
 * )
 * // phpcs:enable
 */
class GetHostPropertiesResponse extends ApiResponse
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
	 *       property="property_id",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Property Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_type",
	 *       type="string",
	 *       default="",
	 *       description="Property Property Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="room_type",
	 *       type="string",
	 *       default="",
	 *       description="Property Room Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_score",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Score"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Host Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_image",
	 *       type="string",
	 *       default="",
	 *       description="Property Host Image"
	 *     ),
	 *     @SWG\Property(
	 *       property="location",
	 *       type="object",
	 *       default="{}",
	 *       description="Location",
	 *         @SWG\Property(
	 *           property="area",
	 *           type="string",
	 *           default="",
	 *           description="Location Area"
	 *         ),
	 *         @SWG\Property(
	 *           property="city",
	 *           type="string",
	 *           default="",
	 *           description="Location City"
	 *         ),
	 *         @SWG\Property(
	 *           property="state",
	 *           type="string",
	 *           default="",
	 *           description="Location State"
	 *         ),
	 *         @SWG\Property(
	 *           property="country",
	 *           type="object",
	 *           default="{}",
	 *           description="Location Country",
	 *             @SWG\Property(
	 *               property="name",
	 *               type="string",
	 *               default="",
	 *               description="Country Name"
	 *             ),
	 *             @SWG\Property(
	 *               property="ccode",
	 *               type="string",
	 *               default="",
	 *               description="Country Ccode"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="location_name",
	 *           type="string",
	 *           default="",
	 *           description="Location Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="latitude",
	 *           type="string",
	 *           default="",
	 *           description="Latitude"
	 *         ),
	 *         @SWG\Property(
	 *           property="longitude",
	 *           type="string",
	 *           default="",
	 *           description="Longitude"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property modified Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_title",
	 *       type="string",
	 *       default="",
	 *       description="Property Original Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="image",
	 *           type="string",
	 *           default="",
	 *           description="Image url"
	 *         ),
	 *         @SWG\Property(
	 *           property="caption",
	 *           type="string",
	 *           default="",
	 *           description="Images Caption"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="url",
	 *       type="string",
	 *       default="",
	 *       description="Property Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="last_updated",
	 *       type="string",
	 *       default="",
	 *       description="Last Updated"
	 *     ),
	 *     @SWG\Property(
	 *       property="show_manage_calender",
	 *       type="integer",
	 *       default="0",
	 *       description="Show Manage Calender Button Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_status",
	 *       type="string",
	 *       default="",
	 *       description="Property Status Text"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_enable",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Enable Status Eg. 0 for Offline, 1 for Online"
	 *     ),
	 *     @SWG\Property(
	 *       property="prices",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Prices",
	 *         @SWG\Property(
	 *           property="currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Currency",
	 *             @SWG\Property(
	 *               property="webicon",
	 *               type="string",
	 *               default="",
	 *               description="Webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="non-webicon",
	 *               type="string",
	 *               default="",
	 *               description="Non-webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="iso_code",
	 *               type="string",
	 *               default="",
	 *               description="Iso Code"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="per_night_price",
	 *           type="integer",
	 *           default="0",
	 *           description="Per Night Price"
	 *         )
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $properties = [];

    /**
     * Cities
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="cities",
	 *   type="array",
	 *   default="[]",
	 *   description="Cities",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="City"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $cities = [];

    /**
     * Status List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="status_list",
	 *   type="array",
	 *   default="[]",
	 *   description="Status List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="status",
	 *       type="integer",
	 *       default="0",
	 *       description="Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="text",
	 *       type="string",
	 *       default="",
	 *       description="Text"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $status_list = [];

    /**
     * Property Types
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_types",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Types",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Name"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $property_types = [];


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
     * Get Cities
     *
     * @return array
     */
    public function getCities()
    {
        return $this->cities;

    }//end getCities()


    /**
     * Set Cities
     *
     * @param array $cities Cities.
     *
     * @return self
     */
    public function setCities(array $cities)
    {
        $this->cities = $cities;
        return $this;

    }//end setCities()


    /**
     * Get Status_list
     *
     * @return array
     */
    public function getStatusList()
    {
        return $this->status_list;

    }//end getStatusList()


    /**
     * Set Status list
     *
     * @param array $status_list Status list.
     *
     * @return self
     */
    public function setStatusList(array $status_list)
    {
        $this->status_list = $status_list;
        return $this;

    }//end setStatusList()


    /**
     * Get Property_types
     *
     * @return array
     */
    public function getPropertyTypes()
    {
        return $this->property_types;

    }//end getPropertyTypes()


    /**
     * Set Property types
     *
     * @param array $property_types Property types.
     *
     * @return self
     */
    public function setPropertyTypes(array $property_types)
    {
        $this->property_types = $property_types;
        return $this;

    }//end setPropertyTypes()


}//end class
