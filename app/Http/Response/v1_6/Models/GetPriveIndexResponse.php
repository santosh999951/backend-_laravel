<?php
/**
 * GetPriveIndexResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPriveIndexResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPriveIndexResponse",
 * description="GetPriveIndexResponse",
 * )
 * // phpcs:enable
 */
class GetPriveIndexResponse extends ApiResponse
{

    /**
     * Total Booked Nights
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total_booked_nights",
	 *   type="string",
	 *   default="",
	 *   description="Total Booked Nights"
	 * )
     * // phpcs:enable
     */
    protected $total_booked_nights = '';

    /**
     * Total Income
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total_income",
	 *   type="string",
	 *   default="",
	 *   description="Total Income"
	 * )
     * // phpcs:enable
     */
    protected $total_income = '';

    /**
     * Properties Count
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="properties_count",
	 *   type="object",
	 *   default="{}",
	 *   description="Properties Count",
	 *     @SWG\Property(
	 *       property="total",
	 *       type="integer",
	 *       default="0",
	 *       description="Total"
	 *     ),
	 *     @SWG\Property(
	 *       property="active_properties",
	 *       type="integer",
	 *       default="0",
	 *       description="Active Properties"
	 *     ),
	 *     @SWG\Property(
	 *       property="inactive_properties",
	 *       type="integer",
	 *       default="0",
	 *       description="Inactive Properties"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $properties_count = [];

    /**
     * Graph Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="graph_data",
	 *   type="array",
	 *   default="[]",
	 *   description="Graph Data",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="month",
	 *       type="string",
	 *       default="",
	 *       description="Month"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_income",
	 *       type="string",
	 *       default="",
	 *       description="Total Income"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_nights_booked",
	 *       type="integer",
	 *       default="0",
	 *       description="Total Nights Booked"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $graph_data = [];

    /**
     * Upcoming Bookings
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="upcoming_bookings",
	 *   type="array",
	 *   default="[]",
	 *   description="Upcoming Bookings",
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
    protected $upcoming_bookings = [];

    /**
     * Properties
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="properties",
	 *   type="array",
	 *   default="[]",
	 *   description="Properties",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="properties_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Properties Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="image",
	 *           type="string",
	 *           default="",
	 *           description="Image"
	 *         ),
	 *         @SWG\Property(
	 *           property="caption",
	 *           type="string",
	 *           default="",
	 *           description="Caption"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="property_title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="url",
	 *       type="string",
	 *       default="",
	 *       description="Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="units",
	 *       type="string",
	 *       default="",
	 *       description="Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="City"
	 *     ),
	 *     @SWG\Property(
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="State"
	 *     ),
	 *     @SWG\Property(
	 *       property="per_night_price",
	 *       type="string",
	 *       default="",
	 *       description="Per Night Price"
	 *     ),
	 *     @SWG\Property(
	 *       property="status",
	 *       type="integer",
	 *       default="0",
	 *       description="Status"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $properties = [];

    /**
     * Property List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_list",
	 *   type="array",
	 *   default="[]",
	 *   description="Property List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="status",
	 *       type="integer",
	 *       default="0",
	 *       description="Status"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $property_list = [];

    /**
     * Property Capital Investment
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="capital_investment",
	 *   type="string",
	 *   default="",
	 *   description="Property Capital Investment"
	 * )
     * // phpcs:enable
     */
    protected $capital_investment = '';


    /**
     * Get Total_booked_nights
     *
     * @return string
     */
    public function getTotalBookedNights()
    {
        return $this->total_booked_nights;

    }//end getTotalBookedNights()


    /**
     * Set Total booked nights
     *
     * @param string $total_booked_nights Total booked nights.
     *
     * @return self
     */
    public function setTotalBookedNights(string $total_booked_nights)
    {
        $this->total_booked_nights = $total_booked_nights;
        return $this;

    }//end setTotalBookedNights()


    /**
     * Get Total_income
     *
     * @return string
     */
    public function getTotalIncome()
    {
        return $this->total_income;

    }//end getTotalIncome()


    /**
     * Set Total income
     *
     * @param string $total_income Total income.
     *
     * @return self
     */
    public function setTotalIncome(string $total_income)
    {
        $this->total_income = $total_income;
        return $this;

    }//end setTotalIncome()


    /**
     * Get Properties_count
     *
     * @return object
     */
    public function getPropertiesCount()
    {
        return (empty($this->properties_count) === false) ? $this->properties_count : new \stdClass;

    }//end getPropertiesCount()


    /**
     * Set Properties count
     *
     * @param array $properties_count Properties count.
     *
     * @return self
     */
    public function setPropertiesCount(array $properties_count)
    {
        $this->properties_count = $properties_count;
        return $this;

    }//end setPropertiesCount()


    /**
     * Get Graph_data
     *
     * @return array
     */
    public function getGraphData()
    {
        return $this->graph_data;

    }//end getGraphData()


    /**
     * Set Graph data
     *
     * @param array $graph_data Graph data.
     *
     * @return self
     */
    public function setGraphData(array $graph_data)
    {
        $this->graph_data = $graph_data;
        return $this;

    }//end setGraphData()


    /**
     * Get Upcoming_bookings
     *
     * @return array
     */
    public function getUpcomingBookings()
    {
        return $this->upcoming_bookings;

    }//end getUpcomingBookings()


    /**
     * Set Upcoming bookings
     *
     * @param array $upcoming_bookings Upcoming bookings.
     *
     * @return self
     */
    public function setUpcomingBookings(array $upcoming_bookings)
    {
        $this->upcoming_bookings = $upcoming_bookings;
        return $this;

    }//end setUpcomingBookings()


    /**
     * Get Properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;

    }//end getProperties()


    /**
     * Set Properties
     *
     * @param array $properties Properties.
     *
     * @return self
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
        return $this;

    }//end setProperties()


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
     * Get Capital_investment
     *
     * @return string
     */
    public function getCapitalInvestment()
    {
        return $this->capital_investment;

    }//end getCapitalInvestment()


    /**
     * Set Capital investment
     *
     * @param string $capital_investment Capital investment.
     *
     * @return self
     */
    public function setCapitalInvestment(string $capital_investment)
    {
        $this->capital_investment = $capital_investment;
        return $this;

    }//end setCapitalInvestment()


}//end class
