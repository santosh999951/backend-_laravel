<?php
/**
 * Response Models for Trips List api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetTripsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetTripsResponse",
 * description="Response Models for Trips List api",
 * )
 * // phpcs:enable
 */
class GetTripsResponse extends ApiResponse
{

    /**
     * Trip List Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="trips",
	 *   type="array",
	 *   default="[]",
	 *   description="Trip List Section",
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
	 *       description="Property Tile Section",
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
	 *           description="Property Location",
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
	 *       property="timeline_status",
	 *       type="string",
	 *       default="",
	 *       description="Timeline Status Eg. Ongoing, 2 months to go"
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_status",
	 *       type="object",
	 *       default="{}",
	 *       description="Booking Status",
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
	 *           description="Color code of status in HEX format Eg. #f2a419"
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
	 *           description="Booking Status Header Text"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="trip_status",
	 *       type="integer",
	 *       default="0",
	 *       description="Trip Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="checkin_checkout",
	 *       type="string",
	 *       default="",
	 *       description="Checkin Checkout Date Eg. 18 Nov - 22 Nov 2018"
	 *     ),
	 *     @SWG\Property(
	 *       property="checkin",
	 *       type="string",
	 *       default="",
	 *       description="Checkin Date Eg. 2018-11-18"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Guests Count"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $trips = [];

    /**
     * Past Trip Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="past_trip_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Past Trip Count"
	 * )
     * // phpcs:enable
     */
    protected $past_trip_count = 0;

    /**
     * Total Trip Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total_trip_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Total Trip Count"
	 * )
     * // phpcs:enable
     */
    protected $total_trip_count = 0;

    /**
     * Updated Offset on pagination
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="updated_offset",
	 *   type="integer",
	 *   default="0",
	 *   description="Updated Offset on pagination"
	 * )
     * // phpcs:enable
     */
    protected $updated_offset = 0;

    /**
     * Total number of data required
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="limit",
	 *   type="integer",
	 *   default="0",
	 *   description="Total number of data required"
	 * )
     * // phpcs:enable
     */
    protected $limit = 0;


    /**
     * Get Trips
     *
     * @return array
     */
    public function getTrips()
    {
        return $this->trips;

    }//end getTrips()


    /**
     * Set Trips
     *
     * @param array $trips Trips.
     *
     * @return self
     */
    public function setTrips(array $trips)
    {
        $this->trips = $trips;
        return $this;

    }//end setTrips()


    /**
     * Get Past_trip_count
     *
     * @return integer
     */
    public function getPastTripCount()
    {
        return $this->past_trip_count;

    }//end getPastTripCount()


    /**
     * Set Past trip count
     *
     * @param integer $past_trip_count Past trip count.
     *
     * @return self
     */
    public function setPastTripCount(int $past_trip_count)
    {
        $this->past_trip_count = $past_trip_count;
        return $this;

    }//end setPastTripCount()


    /**
     * Get Total_trip_count
     *
     * @return integer
     */
    public function getTotalTripCount()
    {
        return $this->total_trip_count;

    }//end getTotalTripCount()


    /**
     * Set Total trip count
     *
     * @param integer $total_trip_count Total trip count.
     *
     * @return self
     */
    public function setTotalTripCount(int $total_trip_count)
    {
        $this->total_trip_count = $total_trip_count;
        return $this;

    }//end setTotalTripCount()


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
