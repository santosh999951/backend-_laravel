<?php
/**
 * GetPriveBookingDetailResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPriveBookingDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPriveBookingDetailResponse",
 * description="GetPriveBookingDetailResponse",
 * )
 * // phpcs:enable
 */
class GetPriveBookingDetailResponse extends ApiResponse
{

    /**
     * Booking Info
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_info",
	 *   type="object",
	 *   default="{}",
	 *   description="Booking Info",
	 *     @SWG\Property(
	 *       property="property_name",
	 *       type="string",
	 *       default="",
	 *       description="Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Guests"
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
	 *     ),
	 *     @SWG\Property(
	 *       property="guest_name",
	 *       type="string",
	 *       default="",
	 *       description="Guest Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_fee",
	 *       type="string",
	 *       default="",
	 *       description="Host Fee"
	 *     ),
	 *     @SWG\Property(
	 *       property="extra_guest",
	 *       type="integer",
	 *       default="0",
	 *       description="Extra Guest"
	 *     ),
	 *     @SWG\Property(
	 *       property="properties_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Images",
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
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $booking_info = [];

    /**
     * Invoice
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="invoice",
	 *   type="object",
	 *   default="{}",
	 *   description="Invoice",
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
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Value"
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
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Value"
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
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Value"
	 *         ),
	 *         @SWG\Property(
	 *           property="raw_value",
	 *           type="integer",
	 *           default="0",
	 *           description="Raw Value"
	 *         ),
	 *         @SWG\Property(
	 *           property="color",
	 *           type="string",
	 *           default="",
	 *           description="Color"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $invoice = [];


    /**
     * Get Booking_info
     *
     * @return object
     */
    public function getBookingInfo()
    {
        return (empty($this->booking_info) === false) ? $this->booking_info : new \stdClass;

    }//end getBookingInfo()


    /**
     * Set Booking info
     *
     * @param array $booking_info Booking info.
     *
     * @return self
     */
    public function setBookingInfo(array $booking_info)
    {
        $this->booking_info = $booking_info;
        return $this;

    }//end setBookingInfo()


    /**
     * Get Invoice
     *
     * @return object
     */
    public function getInvoice()
    {
        return (empty($this->invoice) === false) ? $this->invoice : new \stdClass;

    }//end getInvoice()


    /**
     * Set Invoice
     *
     * @param array $invoice Invoice.
     *
     * @return self
     */
    public function setInvoice(array $invoice)
    {
        $this->invoice = $invoice;
        return $this;

    }//end setInvoice()


}//end class
