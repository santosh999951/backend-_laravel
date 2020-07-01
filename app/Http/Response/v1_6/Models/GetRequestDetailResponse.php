<?php
/**
 * Response Model for Request Detail Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetRequestDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetRequestDetailResponse",
 * description="Response Model for Request Detail Api",
 * )
 * // phpcs:enable
 */
class GetRequestDetailResponse extends ApiResponse
{

    /**
     * Request Invoice Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="invoice_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Request Invoice Section",
	 *     @SWG\Property(
	 *       property="invoice_header",
	 *       type="array",
	 *       default="[]",
	 *       description="Request Invoice Header",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="key",
	 *           type="string",
	 *           default="",
	 *           description="Key Eg. Base price, Extra guest cost"
	 *         ),
	 *         @SWG\Property(
	 *           property="sub_key",
	 *           type="string",
	 *           default="",
	 *           description="Sub Key Eg. for 1 night, 1 unit, 1 guest"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Value Eg. ₹4,122"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Show Status in 0,1"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="invoice_middle",
	 *       type="array",
	 *       default="[]",
	 *       description="Request Invoice Middle",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="key",
	 *           type="string",
	 *           default="",
	 *           description="Key Eg. Total amount, Cleaning fee"
	 *         ),
	 *         @SWG\Property(
	 *           property="sub_key",
	 *           type="string",
	 *           default="",
	 *           description="Sub Key Eg. ₹4,122 x 3 nights"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Value Eg. ₹4,122"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Show Status in 0,1"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="invoice_footer",
	 *       type="array",
	 *       default="[]",
	 *       description="Request Invoice Footer",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="key",
	 *           type="string",
	 *           default="",
	 *           description="Key Eg. Booking Amount, Payable now"
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
	 *           description="Value Eg. ₹4,122"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Show Status in 0,1"
	 *         ),
	 *         @SWG\Property(
	 *           property="bold",
	 *           type="integer",
	 *           default="0",
	 *           description="Bold Status in 0,1"
	 *         ),
	 *         @SWG\Property(
	 *           property="size",
	 *           type="integer",
	 *           default="0",
	 *           description="Size status in 0,1"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="selected_payment_method",
	 *       type="string",
	 *       default="",
	 *       description="Selected Payment Method Section"
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
	 *       description="Booking Info",
	 *         @SWG\Property(
	 *           property="checkin_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Booking Checkin Formatted Eg. 12 Dec 2018"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Booking Checkout Formatted Eg. 12 Dec 2018"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkin",
	 *           type="string",
	 *           default="",
	 *           description="Booking Checkin Eg. 12-12-2018"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout",
	 *           type="string",
	 *           default="",
	 *           description="Booking Checkout Eg. 12-12-2018"
	 *         ),
	 *         @SWG\Property(
	 *           property="guests",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Guests"
	 *         ),
	 *         @SWG\Property(
	 *           property="units",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Units"
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
	 *           property="request_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Request Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="booking_status",
	 *           type="object",
	 *           default="{}",
	 *           description="Booking Status Section",
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
	 *           property="resend_request",
	 *           type="integer",
	 *           default="0",
	 *           description="Resend Request Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="check_other_date",
	 *           type="integer",
	 *           default="0",
	 *           description="Request Check Other Date Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="expires_in",
	 *           type="integer",
	 *           default="0",
	 *           description="Request Expires In"
	 *         ),
	 *         @SWG\Property(
	 *           property="payment_url",
	 *           type="string",
	 *           default="",
	 *           description="Request Payment Url"
	 *         ),
	 *         @SWG\Property(
	 *           property="payment_gateway_method",
	 *           type="string",
	 *           default="",
	 *           description="Payment Gateway Method"
	 *         ),
	 *         @SWG\Property(
	 *           property="instant",
	 *           type="integer",
	 *           default="0",
	 *           description="Instant Bookable status"
	 *         ),
	 *         @SWG\Property(
	 *           property="coupon_code_used",
	 *           type="string",
	 *           default="",
	 *           description="Coupon Code Used Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="wallet_money_used",
	 *           type="integer",
	 *           default="0",
	 *           description="Wallet Money Used Status"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_amount_info",
	 *       type="object",
	 *       default="{}",
	 *       description="Booking Amount Info",
	 *         @SWG\Property(
	 *           property="total_amount_unformatted",
	 *           type="float",
	 *           default="0.0",
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
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $booking_info_section = [];

    /**
     * Cancellation Section
     *
     * @var array
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
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Cancellation Policy Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="sub",
	 *           type="string",
	 *           default="",
	 *           description="Cancellation Policy Sub"
	 *         ),
	 *         @SWG\Property(
	 *           property="button_text",
	 *           type="string",
	 *           default="",
	 *           description="Cancellation Button Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="final_amount",
	 *           type="float",
	 *           default="0.0",
	 *           description="Cancellation Final Amount"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="cancellable",
	 *       type="integer",
	 *       default="0",
	 *       description="Cancellable Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="cancellation_reasons",
	 *       type="array",
	 *       default="[]",
	 *       description="Cancellation Reasons",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Cancellation Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="reason_title",
	 *           type="string",
	 *           default="",
	 *           description="Cancellation Reason Title"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $cancellation_section = [];

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
	 *       description="Property Tile",
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
	 *           description="Property Host Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="host_image",
	 *           type="string",
	 *           default="",
	 *           description="Property Host Image"
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


}//end class
