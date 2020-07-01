<?php
/**
 * GetHostBookingRequestDetailResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostBookingRequestDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostBookingRequestDetailResponse",
 * description="GetHostBookingRequestDetailResponse",
 * )
 * // phpcs:enable
 */
class GetHostBookingRequestDetailResponse extends ApiResponse
{

    /**
     * Property Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Section",
	 *     @SWG\Property(
	 *       property="tile",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Tile data",
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
	 *           description="Room Type"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_score",
	 *           type="integer",
	 *           default="0",
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
	 *           description="Property Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_title",
	 *           type="string",
	 *           default="",
	 *           description="Property Property Title"
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
	 *         ),
	 *         @SWG\Property(
	 *           property="tags",
	 *           type="array",
	 *           default="[]",
	 *           description="Property Tags",
	 *           @SWG\Items(
	 *             type="object",
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="amenities",
	 *           type="array",
	 *           default="[]",
	 *           description="Property Amenities",
	 *           @SWG\Items(
	 *             type="object",
	 *             @SWG\Property(
	 *               property="id",
	 *               type="integer",
	 *               default="0",
	 *               description="Property Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="name",
	 *               type="string",
	 *               default="",
	 *               description="Property Name"
	 *             ),
	 *             @SWG\Property(
	 *               property="icon",
	 *               type="string",
	 *               default="",
	 *               description="Property Icon"
	 *             ),
	 *             @SWG\Property(
	 *               property="rank",
	 *               type="integer",
	 *               default="0",
	 *               description="Property Rank"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="usp",
	 *           type="string",
	 *           default="",
	 *           description="Property Usp"
	 *         ),
	 *         @SWG\Property(
	 *           property="description",
	 *           type="array",
	 *           default="[]",
	 *           description="Property Description",
	 *           @SWG\Items(
	 *             type="object",
	 *             @SWG\Property(
	 *               property="key",
	 *               type="string",
	 *               default="",
	 *               description="Property Key"
	 *             ),
	 *             @SWG\Property(
	 *               property="title",
	 *               type="string",
	 *               default="",
	 *               description="Property Title"
	 *             ),
	 *             @SWG\Property(
	 *               property="value",
	 *               type="string",
	 *               default="",
	 *               description="Property Value"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="how_to_reach",
	 *           type="array",
	 *           default="[]",
	 *           description="Property How To Reach",
	 *           @SWG\Items(
	 *             type="object",
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="units_consumed",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Units Consumed"
	 *         ),
	 *         @SWG\Property(
	 *           property="zipcode",
	 *           type="string",
	 *           default="",
	 *           description="Property Zipcode"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_section = [];

    /**
     * Booking Info Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_info_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Booking Info Section",
	 *     @SWG\Property(
	 *       property="info",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Info",
	 *         @SWG\Property(
	 *           property="request_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Request Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="guests",
	 *           type="integer",
	 *           default="0",
	 *           description="Guests"
	 *         ),
	 *         @SWG\Property(
	 *           property="units",
	 *           type="integer",
	 *           default="0",
	 *           description="Units"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkin_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Checkin Formatted Eg. 12 Dec 2018"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Checkout Formatted Eg. 12 Dec 2018"
	 *         ),
	 *         @SWG\Property(
	 *           property="booking_status",
	 *           type="object",
	 *           default="{}",
	 *           description="Booking Status",
	 *             @SWG\Property(
	 *               property="text",
	 *               type="string",
	 *               default="",
	 *               description="Booking Status Text"
	 *             ),
	 *             @SWG\Property(
	 *               property="class",
	 *               type="string",
	 *               default="",
	 *               description="Booking Status Class"
	 *             ),
	 *             @SWG\Property(
	 *               property="color_code",
	 *               type="string",
	 *               default="",
	 *               description="Booking Status Color Code"
	 *             ),
	 *             @SWG\Property(
	 *               property="status",
	 *               type="integer",
	 *               default="0",
	 *               description="Booking Status Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="header_text",
	 *               type="string",
	 *               default="",
	 *               description="Booking Request Header Text"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="checkin",
	 *           type="string",
	 *           default="",
	 *           description="Checkin Eg. 2018-12-12"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout",
	 *           type="string",
	 *           default="",
	 *           description="Checkout Eg. 2018-12-12"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_type",
	 *           type="string",
	 *           default="",
	 *           description="Property Type"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Property Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="expires_in",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Expires In Time"
	 *         ),
	 *         @SWG\Property(
	 *           property="confirm_text",
	 *           type="string",
	 *           default="",
	 *           description="Property Confirm Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="can_confirm_booking",
	 *           type="integer",
	 *           default="0",
	 *           description="Can Confirm Booking"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_amount_info",
	 *       type="object",
	 *       default="{}",
	 *       description="Booking Amount Info",
	 *         @SWG\Property(
	 *           property="total_amount_unformatted",
	 *           type="integer",
	 *           default="0",
	 *           description="Total Amount Unformatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="payment_option",
	 *           type="string",
	 *           default="",
	 *           description="Payment Option"
	 *         ),
	 *         @SWG\Property(
	 *           property="currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Currency",
	 *             @SWG\Property(
	 *               property="webicon",
	 *               type="string",
	 *               default="",
	 *               description="Webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="non-webicon",
	 *               type="string",
	 *               default="",
	 *               description="Non-webicon"
	 *             ),
	 *             @SWG\Property(
	 *               property="iso_code",
	 *               type="string",
	 *               default="",
	 *               description="Iso Code"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="total_amount",
	 *           type="string",
	 *           default="",
	 *           description="Total Amount"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $booking_info_section = [];

    /**
     * Invoice Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="invoice_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Invoice Section",
	 *     @SWG\Property(
	 *       property="invoice_header",
	 *       type="array",
	 *       default="[]",
	 *       description="Invoice Header",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="key",
	 *           type="string",
	 *           default="",
	 *           description="Key"
	 *         ),
	 *         @SWG\Property(
	 *           property="sub_key",
	 *           type="string",
	 *           default="",
	 *           description="Sub Key"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Value"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Show"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="invoice_middle",
	 *       type="array",
	 *       default="[]",
	 *       description="Invoice Middle",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="key",
	 *           type="string",
	 *           default="",
	 *           description="Key"
	 *         ),
	 *         @SWG\Property(
	 *           property="sub_key",
	 *           type="string",
	 *           default="",
	 *           description="Sub Key"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Value"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Show"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="invoice_footer",
	 *       type="array",
	 *       default="[]",
	 *       description="Invoice Footer",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="key",
	 *           type="string",
	 *           default="",
	 *           description="Key"
	 *         ),
	 *         @SWG\Property(
	 *           property="sub_key",
	 *           type="string",
	 *           default="",
	 *           description="Sub Key"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Value"
	 *         ),
	 *         @SWG\Property(
	 *           property="raw_value",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Raw Value"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Show"
	 *         ),
	 *         @SWG\Property(
	 *           property="bold",
	 *           type="integer",
	 *           default="0",
	 *           description="Bold"
	 *         ),
	 *         @SWG\Property(
	 *           property="size",
	 *           type="integer",
	 *           default="0",
	 *           description="Size"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="selected_payment_method",
	 *       type="string",
	 *       default="",
	 *       description="Selected Payment Method"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected_payment_method_text",
	 *       type="string",
	 *       default="",
	 *       description="Selected Payment Method Text"
	 *     ),
	 *     @SWG\Property(
	 *       property="currency",
	 *       type="string",
	 *       default="",
	 *       description="Currency"
	 *     ),
	 *     @SWG\Property(
	 *       property="currency_code",
	 *       type="string",
	 *       default="",
	 *       description="Currency Code"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $invoice_section = [];

    /**
     * Traveller Info Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="traveller_info_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Traveller Info Section",
	 *     @SWG\Property(
	 *       property="show_traveller",
	 *       type="integer",
	 *       default="0",
	 *       description="Show Traveller"
	 *     ),
	 *     @SWG\Property(
	 *       property="hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="contact",
	 *       type="string",
	 *       default="",
	 *       description="Contact"
	 *     ),
	 *     @SWG\Property(
	 *       property="age",
	 *       type="integer",
	 *       default="0",
	 *       description="Age"
	 *     ),
	 *     @SWG\Property(
	 *       property="language",
	 *       type="string",
	 *       default="",
	 *       description="Language"
	 *     ),
	 *     @SWG\Property(
	 *       property="gender",
	 *       type="string",
	 *       default="",
	 *       description="Gender"
	 *     ),
	 *     @SWG\Property(
	 *       property="verified",
	 *       type="integer",
	 *       default="0",
	 *       description="Verified"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $traveller_info_section = [];

    /**
     * Rejection Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="rejection_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Rejection Section",
	 *     @SWG\Property(
	 *       property="can_reject",
	 *       type="integer",
	 *       default="0",
	 *       description="Can Reject Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="reasons",
	 *       type="array",
	 *       default="[]",
	 *       description="Reasons",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Rejection Reasons Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="reason",
	 *           type="string",
	 *           default="",
	 *           description="Reason Title"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $rejection_section = [];

    /**
     * Cancellation Section
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="cancellation_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Cancellation Section",
	 *     @SWG\Property(
	 *       property="cancellation_policy_info",
	 *       type="object",
	 *       default="{}",
	 *       description="Cancellation Policy Info",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="policy_days",
	 *           type="integer",
	 *           default="0",
	 *           description="Policy Days"
	 *         ),
	 *         @SWG\Property(
	 *           property="desc",
	 *           type="string",
	 *           default="",
	 *           description="Desc"
	 *         ),
	 *         @SWG\Property(
	 *           property="popup_text",
	 *           type="string",
	 *           default="",
	 *           description="Popup Text"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $cancellation_section = [];


    /**
     * Get Property_section
     *
     * @return object
     */
    public function getPropertySection()
    {
        return (empty($this->property_section) === false) ? $this->property_section : new \stdClass;

    }//end getPropertySection()


    /**
     * Set Property section
     *
     * @param array $property_section Property section.
     *
     * @return self
     */
    public function setPropertySection(array $property_section)
    {
        $this->property_section = $property_section;
        return $this;

    }//end setPropertySection()


    /**
     * Get Booking_info_section
     *
     * @return object
     */
    public function getBookingInfoSection()
    {
        return (empty($this->booking_info_section) === false) ? $this->booking_info_section : new \stdClass;

    }//end getBookingInfoSection()


    /**
     * Set Booking info section
     *
     * @param array $booking_info_section Booking info section.
     *
     * @return self
     */
    public function setBookingInfoSection(array $booking_info_section)
    {
        $this->booking_info_section = $booking_info_section;
        return $this;

    }//end setBookingInfoSection()


    /**
     * Get Invoice_section
     *
     * @return object
     */
    public function getInvoiceSection()
    {
        return (empty($this->invoice_section) === false) ? $this->invoice_section : new \stdClass;

    }//end getInvoiceSection()


    /**
     * Set Invoice section
     *
     * @param array $invoice_section Invoice section.
     *
     * @return self
     */
    public function setInvoiceSection(array $invoice_section)
    {
        $this->invoice_section = $invoice_section;
        return $this;

    }//end setInvoiceSection()


    /**
     * Get Traveller_info_section
     *
     * @return object
     */
    public function getTravellerInfoSection()
    {
        return (empty($this->traveller_info_section) === false) ? $this->traveller_info_section : new \stdClass;

    }//end getTravellerInfoSection()


    /**
     * Set Traveller info section
     *
     * @param array $traveller_info_section Traveller info section.
     *
     * @return self
     */
    public function setTravellerInfoSection(array $traveller_info_section)
    {
        $this->traveller_info_section = $traveller_info_section;
        return $this;

    }//end setTravellerInfoSection()


    /**
     * Get Rejection_section
     *
     * @return object
     */
    public function getRejectionSection()
    {
        return (empty($this->rejection_section) === false) ? $this->rejection_section : new \stdClass;

    }//end getRejectionSection()


    /**
     * Set Rejection section
     *
     * @param array $rejection_section Rejection section.
     *
     * @return self
     */
    public function setRejectionSection(array $rejection_section)
    {
        $this->rejection_section = $rejection_section;
        return $this;

    }//end setRejectionSection()


    /**
     * Get Cancellation_section
     *
     * @return object
     */
    public function getCancellationSection()
    {
        return (empty($this->cancellation_section) === false) ? $this->cancellation_section : new \stdClass;

    }//end getCancellationSection()


    /**
     * Set Cancellation section
     *
     * @param array $cancellation_section Cancellation section.
     *
     * @return self
     */
    public function setCancellationSection(array $cancellation_section)
    {
        $this->cancellation_section = $cancellation_section;
        return $this;

    }//end setCancellationSection()


}//end class
