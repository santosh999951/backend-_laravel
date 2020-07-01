<?php
/**
 * Property Dashboard Response Model
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostPropertiesDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostPropertiesDetailResponse",
 * description="Property Dashboard Response Model",
 * )
 * // phpcs:enable
 */
class GetHostPropertiesDetailResponse extends ApiResponse
{

    /**
     * Property Tile
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_tile",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Tile",
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_type_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Type Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="room_type_name",
	 *       type="string",
	 *       default="",
	 *       description="Room Type Name"
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
	 *       description="Property Location",
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
	 *       property="prices",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Prices",
	 *         @SWG\Property(
	 *           property="currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Property Currency",
	 *             @SWG\Property(
	 *               property="webicon",
	 *               type="string",
	 *               default="",
	 *               description="Currency Webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="non-webicon",
	 *               type="string",
	 *               default="",
	 *               description="Currency Non-webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="iso_code",
	 *               type="string",
	 *               default="",
	 *               description="Currency Iso Code"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="per_night_price",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Per Night Price"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Generated Title"
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
	 *           description="Image Url"
	 *         ),
	 *         @SWG\Property(
	 *           property="caption",
	 *           type="string",
	 *           default="",
	 *           description="Image Caption"
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
	 *       description="Property Last Updated Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="calendar_last_updated",
	 *       type="string",
	 *       default="",
	 *       description="Property Calendar Last Updated Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="show_manage_calender",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Edit mode status"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_enable",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Enabled status"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_status",
	 *       type="string",
	 *       default="",
	 *       description="Property Listing Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Booking Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="avg_response_time",
	 *       type="string",
	 *       default="",
	 *       description="Average Response Time"
	 *     ),
	 *     @SWG\Property(
	 *       property="edit_listing",
	 *       type="string",
	 *       default="",
	 *       description="Property Edit Listing"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_tile = [];

    /**
     * Review Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="review_data",
	 *   type="object",
	 *   default="{}",
	 *   description="Review Data",
	 *     @SWG\Property(
	 *       property="review",
	 *       type="array",
	 *       default="[]",
	 *       description="Review Detail",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="guests",
	 *           type="integer",
	 *           default="0",
	 *           description="Guests"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_rating",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Rating"
	 *         ),
	 *         @SWG\Property(
	 *           property="traveller_id",
	 *           type="string",
	 *           default="",
	 *           description="Traveller Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="traveller_name",
	 *           type="string",
	 *           default="",
	 *           description="Traveller Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="review_date",
	 *           type="string",
	 *           default="",
	 *           description="Review Date"
	 *         ),
	 *         @SWG\Property(
	 *           property="comment",
	 *           type="string",
	 *           default="",
	 *           description="Comment"
	 *         ),
	 *         @SWG\Property(
	 *           property="nights",
	 *           type="integer",
	 *           default="0",
	 *           description="Nights"
	 *         ),
	 *         @SWG\Property(
	 *           property="review_images",
	 *           type="array",
	 *           default="[]",
	 *           description="Review Images",
	 *           @SWG\Items(
	 *             type="object",
	 *             @SWG\Property(
	 *               property="value",
	 *               type="string",
	 *               default="",
	 *               description="Image Url"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="traveller_image",
	 *           type="string",
	 *           default="",
	 *           description="Traveller Image"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_score",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Score"
	 *     ),
	 *     @SWG\Property(
	 *       property="new_count",
	 *       type="integer",
	 *       default="0",
	 *       description="New Reviews Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Total Reviews Count"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $review_data = [];


    /**
     * Get Property_tile
     *
     * @return object
     */
    public function getPropertyTile()
    {
        return (empty($this->property_tile) === false) ? $this->property_tile : new \stdClass;

    }//end getPropertyTile()


    /**
     * Set Property tile
     *
     * @param array $property_tile Property tile.
     *
     * @return self
     */
    public function setPropertyTile(array $property_tile)
    {
        $this->property_tile = $property_tile;
        return $this;

    }//end setPropertyTile()


    /**
     * Get Review_data
     *
     * @return object
     */
    public function getReviewData()
    {
        return (empty($this->review_data) === false) ? $this->review_data : new \stdClass;

    }//end getReviewData()


    /**
     * Set Review data
     *
     * @param array $review_data Review data.
     *
     * @return self
     */
    public function setReviewData(array $review_data)
    {
        $this->review_data = $review_data;
        return $this;

    }//end setReviewData()


}//end class
