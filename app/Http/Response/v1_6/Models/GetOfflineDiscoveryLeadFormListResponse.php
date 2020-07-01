<?php
/**
 * GetOfflineDiscoveryLeadFormListResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetOfflineDiscoveryLeadFormListResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetOfflineDiscoveryLeadFormListResponse",
 * description="GetOfflineDiscoveryLeadFormListResponse",
 * )
 * // phpcs:enable
 */
class GetOfflineDiscoveryLeadFormListResponse extends ApiResponse
{

    /**
     * Lead Property Form Field List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_list",
	 *   type="array",
	 *   default="[]",
	 *   description="Lead Property Form Field List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Type List Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Property Type Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Type Selected"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $property_list = [];

    /**
     * Room Type List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="room_type_list",
	 *   type="array",
	 *   default="[]",
	 *   description="Room Type List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Room Type Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Room Type Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected",
	 *       type="integer",
	 *       default="0",
	 *       description="Room Type Selected"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $room_type_list = [];

    /**
     * Amenities List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="amenities",
	 *   type="array",
	 *   default="[]",
	 *   description="Amenities List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Amenities Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="cat_id",
	 *       type="integer",
	 *       default="0",
	 *       description="Category Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="category_name",
	 *       type="string",
	 *       default="",
	 *       description="Category Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="amenity_name",
	 *       type="string",
	 *       default="",
	 *       description="Amenity Name"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $amenities = [];

    /**
     * Cancellation Policy List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="cancellation_policy",
	 *   type="array",
	 *   default="[]",
	 *   description="Cancellation Policy List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Cancellation Policy Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Cancellation Policy Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected",
	 *       type="integer",
	 *       default="0",
	 *       description="Cancellation Policy Selected"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $cancellation_policy = [];

    /**
     * Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


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


    /**
     * Get Room_type_list
     *
     * @return array
     */
    public function getRoomTypeList()
    {
        return $this->room_type_list;

    }//end getRoomTypeList()


    /**
     * Set Room type list
     *
     * @param array $room_type_list Room type list.
     *
     * @return self
     */
    public function setRoomTypeList(array $room_type_list)
    {
        $this->room_type_list = $room_type_list;
        return $this;

    }//end setRoomTypeList()


    /**
     * Get Amenities
     *
     * @return array
     */
    public function getAmenities()
    {
        return $this->amenities;

    }//end getAmenities()


    /**
     * Set Amenities
     *
     * @param array $amenities Amenities.
     *
     * @return self
     */
    public function setAmenities(array $amenities)
    {
        $this->amenities = $amenities;
        return $this;

    }//end setAmenities()


    /**
     * Get Cancellation_policy
     *
     * @return array
     */
    public function getCancellationPolicy()
    {
        return $this->cancellation_policy;

    }//end getCancellationPolicy()


    /**
     * Set Cancellation policy
     *
     * @param array $cancellation_policy Cancellation policy.
     *
     * @return self
     */
    public function setCancellationPolicy(array $cancellation_policy)
    {
        $this->cancellation_policy = $cancellation_policy;
        return $this;

    }//end setCancellationPolicy()


    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;

    }//end getMessage()


    /**
     * Set Message
     *
     * @param string $message Message.
     *
     * @return self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;

    }//end setMessage()


}//end class
