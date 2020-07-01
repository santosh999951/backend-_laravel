<?php
/**
 * Response Models for Request List
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetRequestResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetRequestResponse",
 * description="Response Models for Request List",
 * )
 * // phpcs:enable
 */
class GetRequestResponse extends ApiResponse
{

    /**
     * Requests Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="requests",
	 *   type="array",
	 *   default="[]",
	 *   description="Requests Section",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="request_id",
	 *       type="integer",
	 *       default="0",
	 *       description="Request Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="request_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Request Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_tile",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Tile",
	 *         @SWG\Property(
	 *           property="property_id",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_type",
	 *           type="string",
	 *           default="",
	 *           description="Property Type"
	 *         ),
	 *         @SWG\Property(
	 *           property="room_type",
	 *           type="string",
	 *           default="",
	 *           description="Property Room Type"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_score",
	 *           type="string",
	 *           default="",
	 *           description="Property Score"
	 *         ),
	 *         @SWG\Property(
	 *           property="host_name",
	 *           type="string",
	 *           default="",
	 *           description="Host Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="host_image",
	 *           type="string",
	 *           default="",
	 *           description="Host Image"
	 *         ),
	 *         @SWG\Property(
	 *           property="location",
	 *           type="object",
	 *           default="{}",
	 *           description="Location",
	 *             @SWG\Property(
	 *               property="area",
	 *               type="string",
	 *               default="",
	 *               description="Location Area"
	 *             ),
	 *             @SWG\Property(
	 *               property="city",
	 *               type="string",
	 *               default="",
	 *               description="Location City"
	 *             ),
	 *             @SWG\Property(
	 *               property="state",
	 *               type="string",
	 *               default="",
	 *               description="Location State"
	 *             ),
	 *             @SWG\Property(
	 *               property="country",
	 *               type="object",
	 *               default="{}",
	 *               description="Location Country",
	 *                 @SWG\Property(
	 *                   property="name",
	 *                   type="string",
	 *                   default="",
	 *                   description="Country Name"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="ccode",
	 *                   type="string",
	 *                   default="",
	 *                   description="Country Ccode"
	 *                 )
	 *             ),
	 *             @SWG\Property(
	 *               property="location_name",
	 *               type="string",
	 *               default="",
	 *               description="Location Name"
	 *             ),
	 *             @SWG\Property(
	 *               property="latitude",
	 *               type="string",
	 *               default="",
	 *               description="Latitude"
	 *             ),
	 *             @SWG\Property(
	 *               property="longitude",
	 *               type="string",
	 *               default="",
	 *               description="Longitude"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Property Generated Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_title",
	 *           type="string",
	 *           default="",
	 *           description="Property Original Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_images",
	 *           type="array",
	 *           default="[]",
	 *           description="Property Images",
	 *           @SWG\Items(
	 *             type="object",
	 *             @SWG\Property(
	 *               property="image",
	 *               type="string",
	 *               default="",
	 *               description="Image Url"
	 *             ),
	 *             @SWG\Property(
	 *               property="caption",
	 *               type="string",
	 *               default="",
	 *               description="Image Caption"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="url",
	 *           type="string",
	 *           default="",
	 *           description="Property Url"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_amount",
	 *       type="string",
	 *       default="",
	 *       description="Booking Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_amount_unformatted",
	 *       type="float",
	 *       default="0.0",
	 *       description="Booking Amount Unformatted"
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_status",
	 *       type="object",
	 *       default="{}",
	 *       description="Booking Status Section",
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Booking Status Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="class",
	 *           type="string",
	 *           default="",
	 *           description="Booking Status Class"
	 *         ),
	 *         @SWG\Property(
	 *           property="color_code",
	 *           type="string",
	 *           default="",
	 *           description="Booking Status Color Code"
	 *         ),
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Status Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="header_text",
	 *           type="string",
	 *           default="",
	 *           description="Booking Request Header Text"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="created_at",
	 *       type="string",
	 *       default="",
	 *       description="Request Created Date Eg. 19 Nov 18"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $requests = [];

    /**
     * Archived Request Count (Past Request and Cancelled Request)
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="archived_request_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Archived Request Count (Past Request and Cancelled Request)"
	 * )
     * // phpcs:enable
     */
    protected $archived_request_count = 0;

    /**
     * Active Request Count (Awating Approval Request and Awating Payment Request)
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="active_request_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Active Request Count (Awating Approval Request and Awating Payment Request)"
	 * )
     * // phpcs:enable
     */
    protected $active_request_count = 0;

    /**
     * Updated Offset (New Offset of pagination)
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="updated_offset",
	 *   type="integer",
	 *   default="0",
	 *   description="Updated Offset (New Offset of pagination)"
	 * )
     * // phpcs:enable
     */
    protected $updated_offset = 0;

    /**
     * Limit (Total Number of data)
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="limit",
	 *   type="integer",
	 *   default="0",
	 *   description="Limit (Total Number of data)"
	 * )
     * // phpcs:enable
     */
    protected $limit = 0;


    /**
     * Get Requests
     *
     * @return array
     */
    public function getRequests()
    {
        return $this->requests;

    }//end getRequests()


    /**
     * Set Requests
     *
     * @param array $requests Requests.
     *
     * @return self
     */
    public function setRequests(array $requests)
    {
        $this->requests = $requests;
        return $this;

    }//end setRequests()


    /**
     * Get Archived_request_count
     *
     * @return integer
     */
    public function getArchivedRequestCount()
    {
        return $this->archived_request_count;

    }//end getArchivedRequestCount()


    /**
     * Set Archived request count
     *
     * @param integer $archived_request_count Archived request count.
     *
     * @return self
     */
    public function setArchivedRequestCount(int $archived_request_count)
    {
        $this->archived_request_count = $archived_request_count;
        return $this;

    }//end setArchivedRequestCount()


    /**
     * Get Active_request_count
     *
     * @return integer
     */
    public function getActiveRequestCount()
    {
        return $this->active_request_count;

    }//end getActiveRequestCount()


    /**
     * Set Active request count
     *
     * @param integer $active_request_count Active request count.
     *
     * @return self
     */
    public function setActiveRequestCount(int $active_request_count)
    {
        $this->active_request_count = $active_request_count;
        return $this;

    }//end setActiveRequestCount()


    /**
     * Get Updated_offset
     *
     * @return integer
     */
    public function getUpdatedOffset()
    {
        return $this->updated_offset;

    }//end getUpdatedOffset()


    /**
     * Set Updated offset
     *
     * @param integer $updated_offset Updated offset.
     *
     * @return self
     */
    public function setUpdatedOffset(int $updated_offset)
    {
        $this->updated_offset = $updated_offset;
        return $this;

    }//end setUpdatedOffset()


    /**
     * Get Limit
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;

    }//end getLimit()


    /**
     * Set Limit
     *
     * @param integer $limit Limit.
     *
     * @return self
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
        return $this;

    }//end setLimit()


}//end class
