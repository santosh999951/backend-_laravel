<?php
/**
 * Booking and Request List Response Model
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostBookingResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostBookingResponse",
 * description="Booking and Request List Response Model",
 * )
 * // phpcs:enable
 */
class GetHostBookingResponse extends ApiResponse
{

    /**
     * Booking Requests data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_requests",
	 *   type="array",
	 *   default="[]",
	 *   description="Booking Requests data",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="request_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Request Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="no_of_nights",
	 *       type="integer",
	 *       default="0",
	 *       description="No Of Nights"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Guests"
	 *     ),
	 *     @SWG\Property(
	 *       property="units",
	 *       type="integer",
	 *       default="0",
	 *       description="Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="checkin_checkout",
	 *       type="string",
	 *       default="",
	 *       description="Checkin Checkout"
	 *     ),
	 *     @SWG\Property(
	 *       property="timeline_status",
	 *       type="string",
	 *       default="",
	 *       description="Timeline Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="amount",
	 *       type="string",
	 *       default="",
	 *       description="Booking Amount"
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
	 *       property="checkin",
	 *       type="string",
	 *       default="",
	 *       description="Checkin"
	 *     ),
	 *     @SWG\Property(
	 *       property="checkout",
	 *       type="string",
	 *       default="",
	 *       description="Checkout"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="location_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Location Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_image",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Image",
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
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $booking_requests = [];

    /**
     * Booking List Filters
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="filter",
	 *   type="object",
	 *   default="{}",
	 *   description="Booking List Filters",
	 *     @SWG\Property(
	 *       property="properties",
	 *       type="object",
	 *       default="{}",
	 *       description="All Properties listed by host",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="string",
	 *           default="",
	 *           description="Property Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Property Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Selected status"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="status",
	 *       type="object",
	 *       default="{}",
	 *       description="Booking Status Filter",
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Status id"
	 *         ),
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Status Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Selected status"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="order_by",
	 *       type="integer",
	 *       default="0",
	 *       description="Sort Order By"
	 *     ),
	 *     @SWG\Property(
	 *       property="start_date",
	 *       type="string",
	 *       default="",
	 *       description="Selected Start Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="end_date",
	 *       type="string",
	 *       default="",
	 *       description="Selected End Date"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $filter = [];

    /**
     * Updated Offset
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="updated_offset",
	 *   type="integer",
	 *   default="0",
	 *   description="Updated Offset"
	 * )
     * // phpcs:enable
     */
    protected $updated_offset = 0;

    /**
     * Count of data to fetch
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total",
	 *   type="integer",
	 *   default="0",
	 *   description="Count of data to fetch"
	 * )
     * // phpcs:enable
     */
    protected $total = 0;


    /**
     * Get Booking_requests
     *
     * @return array
     */
    public function getBookingRequests()
    {
        return $this->booking_requests;

    }//end getBookingRequests()


    /**
     * Set Booking requests
     *
     * @param array $booking_requests Booking requests.
     *
     * @return self
     */
    public function setBookingRequests(array $booking_requests)
    {
        $this->booking_requests = $booking_requests;
        return $this;

    }//end setBookingRequests()


    /**
     * Get Filter
     *
     * @return object
     */
    public function getFilter()
    {
        return (empty($this->filter) === false) ? $this->filter : new \stdClass;

    }//end getFilter()


    /**
     * Set Filter
     *
     * @param array $filter Filter.
     *
     * @return self
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;
        return $this;

    }//end setFilter()


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
