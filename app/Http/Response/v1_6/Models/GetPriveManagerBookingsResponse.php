<?php
/**
 * GetPriveManagerBookingsResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPriveManagerBookingsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPriveManagerBookingsResponse",
 * description="GetPriveManagerBookingsResponse",
 * )
 * // phpcs:enable
 */
class GetPriveManagerBookingsResponse extends ApiResponse
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
	 *           description="Booking Status Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="class",
	 *           type="string",
	 *           default="",
	 *           description="Booking Status Class"
	 *         ),
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Status"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="status",
	 *       type="object",
	 *       default="{}",
	 *       description="Checkedin Status",
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Checkedin Status Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="class",
	 *           type="string",
	 *           default="",
	 *           description="Property Class"
	 *         ),
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Checkedin Status"
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
	 *       property="checkin_formatted",
	 *       type="string",
	 *       default="",
	 *       description="Property Checkin Formatted"
	 *     ),
	 *     @SWG\Property(
	 *       property="checkout_formatted",
	 *       type="string",
	 *       default="",
	 *       description="Property Checkout Formatted"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
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
	 *       description="Property Room Type Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_name",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="verified",
	 *       type="integer",
	 *       default="0",
	 *       description="Traveller Verified"
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
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_email",
	 *       type="string",
	 *       default="",
	 *       description="Property Traveller Email"
	 *     ),
	 *     @SWG\Property(
	 *       property="contacts",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Contacts",
	 *         @SWG\Property(
	 *           property="manager",
	 *           type="object",
	 *           default="{}",
	 *           description="Property Manager",
	 *             @SWG\Property(
	 *               property="primary",
	 *               type="string",
	 *               default="",
	 *               description="Property Primary"
	 *             ),
	 *             @SWG\Property(
	 *               property="secondary",
	 *               type="string",
	 *               default="",
	 *               description="Property Secondary"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="traveller",
	 *           type="object",
	 *           default="{}",
	 *           description="Property Traveller",
	 *             @SWG\Property(
	 *               property="primary",
	 *               type="string",
	 *               default="",
	 *               description="Property Primary"
	 *             ),
	 *             @SWG\Property(
	 *               property="secondary",
	 *               type="string",
	 *               default="",
	 *               description="Property Secondary"
	 *             )
	 *         )
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $booking_requests = [];

    /**
     * Booking Filter
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="filter",
	 *   type="object",
	 *   default="{}",
	 *   description="Booking Filter",
	 *     @SWG\Property(
	 *       property="start_date",
	 *       type="string",
	 *       default="",
	 *       description="Booking Start Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="end_date",
	 *       type="string",
	 *       default="",
	 *       description="Booking End Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="properties",
	 *       type="array",
	 *       default="[]",
	 *       description="Properties",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="property_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Hash Id"
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
	 *           description="Property Selected"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="status",
	 *       type="array",
	 *       default="[]",
	 *       description="Status",
	 *       @SWG\Items(
	 *         type="object",
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
	 *           description="Property Class"
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
	 *     ),
	 *     @SWG\Property(
	 *       property="search",
	 *       type="string",
	 *       default="",
	 *       description="Property Search"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $filter = [];

    /**
     * Sort
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="sort",
	 *   type="object",
	 *   default="{}",
	 *   description="Sort",
	 *     @SWG\Property(
	 *       property="sort_by",
	 *       type="integer",
	 *       default="0",
	 *       description="Sort By"
	 *     ),
	 *     @SWG\Property(
	 *       property="order",
	 *       type="string",
	 *       default="",
	 *       description="Order"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $sort = [];

    /**
     * Property Booking Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Booking Count"
	 * )
     * // phpcs:enable
     */
    protected $booking_count = 0;


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
     * Get Sort
     *
     * @return object
     */
    public function getSort()
    {
        return (empty($this->sort) === false) ? $this->sort : new \stdClass;

    }//end getSort()


    /**
     * Set Sort
     *
     * @param array $sort Sort.
     *
     * @return self
     */
    public function setSort(array $sort)
    {
        $this->sort = $sort;
        return $this;

    }//end setSort()


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


}//end class
