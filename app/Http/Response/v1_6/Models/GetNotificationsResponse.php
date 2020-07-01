<?php
/**
 * GetNotificationsResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetNotificationsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetNotificationsResponse",
 * description="GetNotificationsResponse",
 * )
 * // phpcs:enable
 */
class GetNotificationsResponse extends ApiResponse
{

    /**
     * Property Traveller
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="traveller",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Traveller",
	 *     @SWG\Property(
	 *       property="active_request_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Active Request Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="upcoming_trip_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Upcoming Trip Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="status_changed",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Status Changed",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="request_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Request Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_id",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Property Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Property Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_image",
	 *           type="array",
	 *           default="[]",
	 *           description="Property Property Image",
	 *           @SWG\Items(
	 *             type="object",
	 *             @SWG\Property(
	 *               property="image",
	 *               type="string",
	 *               default="",
	 *               description="Property Image"
	 *             ),
	 *             @SWG\Property(
	 *               property="caption",
	 *               type="string",
	 *               default="",
	 *               description="Property Caption"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="property_title",
	 *           type="string",
	 *           default="",
	 *           description="Property Property Title"
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
	 *               description="Property Area"
	 *             ),
	 *             @SWG\Property(
	 *               property="city",
	 *               type="string",
	 *               default="",
	 *               description="Property City"
	 *             ),
	 *             @SWG\Property(
	 *               property="state",
	 *               type="string",
	 *               default="",
	 *               description="Property State"
	 *             ),
	 *             @SWG\Property(
	 *               property="country",
	 *               type="object",
	 *               default="{}",
	 *               description="Property Country",
	 *                 @SWG\Property(
	 *                   property="name",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Name"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="ccode",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Ccode"
	 *                 )
	 *             ),
	 *             @SWG\Property(
	 *               property="location_name",
	 *               type="string",
	 *               default="",
	 *               description="Property Location Name"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="units",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Units"
	 *         ),
	 *         @SWG\Property(
	 *           property="guests",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Guests"
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
	 *           type="integer",
	 *           default="0",
	 *           description="Property Booking Status"
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
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $traveller = [];

    /**
     * Property Host
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="host",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Host",
	 *     @SWG\Property(
	 *       property="new_request_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Property New Request Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="upcoming_booking_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Upcoming Booking Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="awaiting_confirmation_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Awaiting Confirmation Count"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $host = [];


    /**
     * Get Traveller
     *
     * @return object
     */
    public function getTraveller()
    {
        return (empty($this->traveller) === false) ? $this->traveller : new \stdClass;

    }//end getTraveller()


    /**
     * Set Traveller
     *
     * @param array $traveller Traveller.
     *
     * @return self
     */
    public function setTraveller(array $traveller)
    {
        $this->traveller = $traveller;
        return $this;

    }//end setTraveller()


    /**
     * Get Host
     *
     * @return object
     */
    public function getHost()
    {
        return (empty($this->host) === false) ? $this->host : new \stdClass;

    }//end getHost()


    /**
     * Set Host
     *
     * @param array $host Host.
     *
     * @return self
     */
    public function setHost(array $host)
    {
        $this->host = $host;
        return $this;

    }//end setHost()


}//end class
