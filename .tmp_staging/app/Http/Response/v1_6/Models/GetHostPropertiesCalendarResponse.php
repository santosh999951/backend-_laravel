<?php
/**
 * Get Host Properties Price Calendar Response Model
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostPropertiesCalendarResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostPropertiesCalendarResponse",
 * description="Get Host Properties Price Calendar Response Model",
 * )
 * // phpcs:enable
 */
class GetHostPropertiesCalendarResponse extends ApiResponse
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
	 *       property="accomodation",
	 *       type="integer",
	 *       default="0",
	 *       description="Accomodation"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="url",
	 *       type="string",
	 *       default="",
	 *       description="Property Url"
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
	 *       property="calendar_last_updated",
	 *       type="string",
	 *       default="",
	 *       description="Property Calendar Last Updated"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_tile = [];

    /**
     * Property Booking Stats
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_stats",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Booking Stats",
	 *     @SWG\Property(
	 *       property="inquiry",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Inquiry"
	 *     ),
	 *     @SWG\Property(
	 *       property="bookings",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Bookings"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $booking_stats = [];

    /**
     * Property Default
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="default",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Default",
	 *     @SWG\Property(
	 *       property="price",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Default Price"
	 *     ),
	 *     @SWG\Property(
	 *       property="extra_guest_cost",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Extra Guest Cost"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_available",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Is Available Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_units",
	 *       type="string",
	 *       default="",
	 *       description="Total Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="available_units",
	 *       type="string",
	 *       default="",
	 *       description="Available Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="booked_units",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Booked Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="blocked_units",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Blocked Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="open_units",
	 *       type="string",
	 *       default="",
	 *       description="Property Open Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="instant_book",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Instant Book Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="gh_commission",
	 *       type="float",
	 *       default="0.0",
	 *       description="Property Gh Commission"
	 *     ),
	 *     @SWG\Property(
	 *       property="service_fee",
	 *       type="string",
	 *       default="",
	 *       description="Property Service Fee"
	 *     ),
	 *     @SWG\Property(
	 *       property="markup_service_fee",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Markup Service Fee"
	 *     ),
	 *     @SWG\Property(
	 *       property="x_plus_5",
	 *       type="integer",
	 *       default="0",
	 *       description="Property X Plus 5"
	 *     ),
	 *     @SWG\Property(
	 *       property="discount",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Discount"
	 *     ),
	 *     @SWG\Property(
	 *       property="smart_discount",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Smart Discount"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $default = [];

    /**
     * Exception Property pricing
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="exception",
	 *   type="object",
	 *   default="{}",
	 *   description="Exception Property pricing",
	 *     @SWG\Property(
	 *       property="date",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Date",
	 *         @SWG\Property(
	 *           property="price",
	 *           type="string",
	 *           default="",
	 *           description="Property Price"
	 *         ),
	 *         @SWG\Property(
	 *           property="extra_guest_cost",
	 *           type="string",
	 *           default="",
	 *           description="Property Extra Guest Cost"
	 *         ),
	 *         @SWG\Property(
	 *           property="is_available",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Is Available"
	 *         ),
	 *         @SWG\Property(
	 *           property="total_units",
	 *           type="string",
	 *           default="",
	 *           description="Property Total Units"
	 *         ),
	 *         @SWG\Property(
	 *           property="available_units",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Available Units"
	 *         ),
	 *         @SWG\Property(
	 *           property="booked_units",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Booked Units"
	 *         ),
	 *         @SWG\Property(
	 *           property="blocked_units",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Blocked Units"
	 *         ),
	 *         @SWG\Property(
	 *           property="open_units",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Open Units"
	 *         ),
	 *         @SWG\Property(
	 *           property="instant_book",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Instant Book"
	 *         ),
	 *         @SWG\Property(
	 *           property="gh_commission",
	 *           type="string",
	 *           default="",
	 *           description="Property Gh Commission"
	 *         ),
	 *         @SWG\Property(
	 *           property="service_fee",
	 *           type="string",
	 *           default="",
	 *           description="Property Service Fee"
	 *         ),
	 *         @SWG\Property(
	 *           property="markup_service_fee",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Markup Service Fee"
	 *         ),
	 *         @SWG\Property(
	 *           property="x_plus_5",
	 *           type="integer",
	 *           default="0",
	 *           description="Property X Plus 5"
	 *         ),
	 *         @SWG\Property(
	 *           property="discount",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Discount"
	 *         ),
	 *         @SWG\Property(
	 *           property="smart_discount",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Smart Discount"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $exception = [];

    /**
     * Property Smart Discounts
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="smart_discounts",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Smart Discounts",
	 *     @SWG\Property(
	 *       property="date",
	 *       type="array",
	 *       default="[]",
	 *       description="Inventory date",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="discount",
	 *           type="integer",
	 *           default="0",
	 *           description="Discount Percentage"
	 *         ),
	 *         @SWG\Property(
	 *           property="days",
	 *           type="integer",
	 *           default="0",
	 *           description="Days of discount"
	 *         ),
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Discount Status"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $smart_discounts = [];


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


    /**
     * Get Default
     *
     * @return object
     */
    public function getDefault()
    {
        return (empty($this->default) === false) ? $this->default : new \stdClass;

    }//end getDefault()


    /**
     * Set Default
     *
     * @param array $default Default.
     *
     * @return self
     */
    public function setDefault(array $default)
    {
        $this->default = $default;
        return $this;

    }//end setDefault()


    /**
     * Get Exception
     *
     * @return object
     */
    public function getException()
    {
        return (empty($this->exception) === false) ? $this->exception : new \stdClass;

    }//end getException()


    /**
     * Set Exception
     *
     * @param array $exception Exception.
     *
     * @return self
     */
    public function setException(array $exception)
    {
        $this->exception = $exception;
        return $this;

    }//end setException()


    /**
     * Get Smart_discounts
     *
     * @return object
     */
    public function getSmartDiscounts()
    {
        return (empty($this->smart_discounts) === false) ? $this->smart_discounts : new \stdClass;

    }//end getSmartDiscounts()


    /**
     * Set Smart discounts
     *
     * @param array $smart_discounts Smart discounts.
     *
     * @return self
     */
    public function setSmartDiscounts(array $smart_discounts)
    {
        $this->smart_discounts = $smart_discounts;
        return $this;

    }//end setSmartDiscounts()


}//end class
