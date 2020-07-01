<?php
/**
 * GetHostBookingTripDetailResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostBookingTripDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostBookingTripDetailResponse",
 * description="GetHostBookingTripDetailResponse",
 * )
 * // phpcs:enable
 */
class GetHostBookingTripDetailResponse extends ApiResponse
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
	 *       description="Property Tile Data",
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
	 *               description="Property Country",
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
	 *               description="Images Url"
	 *             ),
	 *             @SWG\Property(
	 *               property="caption",
	 *               type="string",
	 *               default="",
	 *               description="Images Caption"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="url",
	 *           type="string",
	 *           default="",
	 *           description="Property Url"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_section = [];

    /**
     * Booking Details
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_info_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Booking Details",
	 *     @SWG\Property(
	 *       property="info",
	 *       type="object",
	 *       default="{}",
	 *       description="Booking data",
	 *         @SWG\Property(
	 *           property="request_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Request Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="instant",
	 *           type="integer",
	 *           default="0",
	 *           description="Instant bookable status"
	 *         ),
	 *         @SWG\Property(
	 *           property="coupon_code_used",
	 *           type="string",
	 *           default="",
	 *           description="Coupon Code Used"
	 *         ),
	 *         @SWG\Property(
	 *           property="wallet_money_used",
	 *           type="integer",
	 *           default="0",
	 *           description="Wallet Money Used"
	 *         ),
	 *         @SWG\Property(
	 *           property="guests",
	 *           type="integer",
	 *           default="0",
	 *           description="Guests"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkin_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Checkin Formatted Eg. 17 Dec 2018"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Checkout Formatted Eg. 17 Dec 2018"
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
	 *           description="Checkin date Eg. 2018-12-17"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout",
	 *           type="string",
	 *           default="",
	 *           description="Checkout date Eg. 2018-12-17"
	 *         ),
	 *         @SWG\Property(
	 *           property="ask_review",
	 *           type="integer",
	 *           default="0",
	 *           description="Ask Review Status"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_amount_info",
	 *       type="object",
	 *       default="{}",
	 *       description="Booking Amount Info",
	 *         @SWG\Property(
	 *           property="currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Currency",
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
	 *           property="total_amount",
	 *           type="string",
	 *           default="",
	 *           description="Total Booking Amount"
	 *         ),
	 *         @SWG\Property(
	 *           property="total_amount_unformatted",
	 *           type="float",
	 *           default="0.0",
	 *           description="Total Booking Amount Unformatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="pending_payment",
	 *           type="integer",
	 *           default="0",
	 *           description="Pending Payment Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="pending_payment_amount",
	 *           type="string",
	 *           default="",
	 *           description="Pending Payment Amount"
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
	 *           description="Section Show Status"
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
	 *           description="Section Show Status"
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
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Section Show Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="bold",
	 *           type="integer",
	 *           default="0",
	 *           description=" Text Bold Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="size",
	 *           type="integer",
	 *           default="0",
	 *           description="Text Size Status"
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
	 *       description="Show Traveller Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="contact",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Contact"
	 *     ),
	 *     @SWG\Property(
	 *       property="age",
	 *       type="integer",
	 *       default="0",
	 *       description="Traveller Age"
	 *     ),
	 *     @SWG\Property(
	 *       property="language",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Language"
	 *     ),
	 *     @SWG\Property(
	 *       property="gender",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Gender"
	 *     ),
	 *     @SWG\Property(
	 *       property="verified",
	 *       type="integer",
	 *       default="0",
	 *       description="Traveller Verified Status"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $traveller_info_section = [];


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


}//end class
