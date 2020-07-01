<?php
/**
 * GetPriveBookingsResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPriveBookingsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPriveBookingsResponse",
 * description="GetPriveBookingsResponse",
 * )
 * // phpcs:enable
 */
class GetPriveBookingsResponse extends ApiResponse
{

    /**
     * Booking Requests
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_requests",
	 *   type="array",
	 *   default="[]",
	 *   description="Booking Requests",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="request_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Request Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="guest_name",
	 *       type="string",
	 *       default="",
	 *       description="Guest Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Guests"
	 *     ),
	 *     @SWG\Property(
	 *       property="amount",
	 *       type="string",
	 *       default="",
	 *       description="Amount"
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
	 *           description="Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="class",
	 *           type="string",
	 *           default="",
	 *           description="Class"
	 *         ),
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Status"
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
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="units",
	 *       type="integer",
	 *       default="0",
	 *       description="Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="room",
	 *       type="integer",
	 *       default="0",
	 *       description="Room"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $booking_requests = [];

    /**
     * Filter
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="filter",
	 *   type="object",
	 *   default="{}",
	 *   description="Filter",
	 *     @SWG\Property(
	 *       property="start_date",
	 *       type="string",
	 *       default="",
	 *       description="Start Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="end_date",
	 *       type="string",
	 *       default="",
	 *       description="End Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="properties",
	 *       type="array",
	 *       default="[]",
	 *       description="Properties",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Selected"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_title",
	 *           type="string",
	 *           default="",
	 *           description="Title"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_status",
	 *       type="array",
	 *       default="[]",
	 *       description="Booking Status",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Selected"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $filter = [];

    /**
     * Booking Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Booking Count"
	 * )
     * // phpcs:enable
     */
    protected $booking_count = 0;

    /**
     * Property Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Count"
	 * )
     * // phpcs:enable
     */
    protected $property_count = 0;

    /**
     * User Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_name",
	 *   type="string",
	 *   default="",
	 *   description="User Name"
	 * )
     * // phpcs:enable
     */
    protected $user_name = '';


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
     * Get Booking_count
     *
     * @return integer
     */
    public function getBookingCount()
    {
        return $this->booking_count;

    }//end getBookingCount()


    /**
     * Set Booking count
     *
     * @param integer $booking_count Booking count.
     *
     * @return self
     */
    public function setBookingCount(int $booking_count)
    {
        $this->booking_count = $booking_count;
        return $this;

    }//end setBookingCount()


    /**
     * Get Property_count
     *
     * @return integer
     */
    public function getPropertyCount()
    {
        return $this->property_count;

    }//end getPropertyCount()


    /**
     * Set Property count
     *
     * @param integer $property_count Property count.
     *
     * @return self
     */
    public function setPropertyCount(int $property_count)
    {
        $this->property_count = $property_count;
        return $this;

    }//end setPropertyCount()


    /**
     * Get User_name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;

    }//end getUserName()


    /**
     * Set User name
     *
     * @param string $user_name User name.
     *
     * @return self
     */
    public function setUserName(string $user_name)
    {
        $this->user_name = $user_name;
        return $this;

    }//end setUserName()


}//end class
