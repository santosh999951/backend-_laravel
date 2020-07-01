<?php
/**
 * Host Home Page Response
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostHomeResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostHomeResponse",
 * description="Host Home Page Response",
 * )
 * // phpcs:enable
 */
class GetHostHomeResponse extends ApiResponse
{

    /**
     * Host Home page Notification for Bookings and Review by traveller
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="notification",
	 *   type="array",
	 *   default="[]",
	 *   description="Host Home page Notification for Bookings and Review by traveller",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="type",
	 *       type="integer",
	 *       default="0",
	 *       description="Type of notification Eg. new_request, checkin_today, new_guest_review"
	 *     ),
	 *     @SWG\Property(
	 *       property="count",
	 *       type="integer",
	 *       default="0",
	 *       description="Count of notification"
	 *     ),
	 *     @SWG\Property(
	 *       property="text",
	 *       type="string",
	 *       default="",
	 *       description="Text to show Eg. Requests awaiting your approval"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $notification = [];

    /**
     * Upcoming Checkins of traveller
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="upcoming_checkin",
	 *   type="object",
	 *   default="{}",
	 *   description="Upcoming Checkins of traveller",
	 *     @SWG\Property(
	 *       property="type",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="list",
	 *       type="array",
	 *       default="[]",
	 *       description="Property List",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="request_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Request Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Property Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="guest",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Guest"
	 *         ),
	 *         @SWG\Property(
	 *           property="units_consumed",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Units Consumed"
	 *         ),
	 *         @SWG\Property(
	 *           property="traveller_name",
	 *           type="string",
	 *           default="",
	 *           description="Property Traveller Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkin",
	 *           type="string",
	 *           default="",
	 *           description="Property Checkin"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout",
	 *           type="string",
	 *           default="",
	 *           description="Property Checkout"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkin_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Property Checkin Formatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Property Checkout Formatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="booking_status",
	 *           type="object",
	 *           default="{}",
	 *           description="Property Booking Status",
	 *             @SWG\Property(
	 *               property="text",
	 *               type="string",
	 *               default="",
	 *               description="Property Text"
	 *             ),
	 *             @SWG\Property(
	 *               property="class",
	 *               type="string",
	 *               default="",
	 *               description="Property Class"
	 *             ),
	 *             @SWG\Property(
	 *               property="color_code",
	 *               type="string",
	 *               default="",
	 *               description="Property Color Code"
	 *             ),
	 *             @SWG\Property(
	 *               property="status",
	 *               type="integer",
	 *               default="0",
	 *               description="Property Status"
	 *             ),
	 *             @SWG\Property(
	 *               property="header_text",
	 *               type="string",
	 *               default="",
	 *               description="Property Header Text"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Property Text"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $upcoming_checkin = [];

    /**
     * Booking Stats
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_stats",
	 *   type="object",
	 *   default="{}",
	 *   description="Booking Stats",
	 *     @SWG\Property(
	 *       property="status",
	 *       type="string",
	 *       default="",
	 *       description="Booking stats Status Eg. up (when booking increased in current week), down (When booking decrease in current week)"
	 *     ),
	 *     @SWG\Property(
	 *       property="value",
	 *       type="string",
	 *       default="",
	 *       description="Booking stats Value Eg. 70%"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $booking_stats = [];


    /**
     * Get Notification
     *
     * @return array
     */
    public function getNotification()
    {
        return $this->notification;

    }//end getNotification()


    /**
     * Set Notification
     *
     * @param array $notification Notification.
     *
     * @return self
     */
    public function setNotification(array $notification)
    {
        $this->notification = $notification;
        return $this;

    }//end setNotification()


    /**
     * Get Upcoming_checkin
     *
     * @return object
     */
    public function getUpcomingCheckin()
    {
        return (empty($this->upcoming_checkin) === false) ? $this->upcoming_checkin : new \stdClass;

    }//end getUpcomingCheckin()


    /**
     * Set Upcoming checkin
     *
     * @param array $upcoming_checkin Upcoming checkin.
     *
     * @return self
     */
    public function setUpcomingCheckin(array $upcoming_checkin)
    {
        $this->upcoming_checkin = $upcoming_checkin;
        return $this;

    }//end setUpcomingCheckin()


    /**
     * Get Booking_stats
     *
     * @return object
     */
    public function getBookingStats()
    {
        return (empty($this->booking_stats) === false) ? $this->booking_stats : new \stdClass;

    }//end getBookingStats()


    /**
     * Set Booking stats
     *
     * @param array $booking_stats Booking stats.
     *
     * @return self
     */
    public function setBookingStats(array $booking_stats)
    {
        $this->booking_stats = $booking_stats;
        return $this;

    }//end setBookingStats()


}//end class
