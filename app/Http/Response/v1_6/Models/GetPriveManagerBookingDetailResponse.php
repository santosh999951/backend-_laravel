<?php
/**
 * Get prive manager booking details
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPriveManagerBookingDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="Get Prive Manager Booking Detail Response",
 * description="Get prive manager booking details",
 * )
 * // phpcs:enable
 */
class GetPriveManagerBookingDetailResponse extends ApiResponse
{

    /**
     * Property Section
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Section",
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_type",
	 *       type="string",
	 *       default="",
	 *       description="Property Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="room_type",
	 *       type="string",
	 *       default="",
	 *       description="Room Type"
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
	 *           description="Area"
	 *         ),
	 *         @SWG\Property(
	 *           property="city",
	 *           type="string",
	 *           default="",
	 *           description="City"
	 *         ),
	 *         @SWG\Property(
	 *           property="state",
	 *           type="string",
	 *           default="",
	 *           description="State"
	 *         ),
	 *         @SWG\Property(
	 *           property="country",
	 *           type="object",
	 *           default="{}",
	 *           description="Country",
	 *             @SWG\Property(
	 *               property="name",
	 *               type="string",
	 *               default="",
	 *               description="Name"
	 *             ),
	 *             @SWG\Property(
	 *               property="ccode",
	 *               type="string",
	 *               default="",
	 *               description="Ccode"
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
	 *       property="property_title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="image",
	 *           type="string",
	 *           default="",
	 *           description="Image Url"
	 *         ),
	 *         @SWG\Property(
	 *           property="caption",
	 *           type="string",
	 *           default="",
	 *           description="Image Caption"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="url",
	 *       type="string",
	 *       default="",
	 *       description="Property Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="amenities",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Amenities",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Amenities Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Amenities Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="icon",
	 *           type="string",
	 *           default="",
	 *           description="Amenities Icon"
	 *         ),
	 *         @SWG\Property(
	 *           property="rank",
	 *           type="integer",
	 *           default="0",
	 *           description="Amenities Rank"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_section = [];

    /**
     * Booking Info Section
     *
     * @var object
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
	 *           property="request_hash_id",
	 *           type="string",
	 *           default="",
	 *           description="Booking Request Hash Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="guests",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Guests"
	 *         ),
	 *         @SWG\Property(
	 *           property="extra_guest",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Extra Guest"
	 *         ),
	 *         @SWG\Property(
	 *           property="units",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Units"
	 *         ),
	 *         @SWG\Property(
	 *           property="bedroom",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Bedroom"
	 *         ),
	 *         @SWG\Property(
	 *           property="status",
	 *           type="object",
	 *           default="{}",
	 *           description="Checkedin Status",
	 *             @SWG\Property(
	 *               property="text",
	 *               type="string",
	 *               default="",
	 *               description="Text"
	 *             ),
	 *             @SWG\Property(
	 *               property="class",
	 *               type="string",
	 *               default="",
	 *               description="Class"
	 *             ),
	 *             @SWG\Property(
	 *               property="status",
	 *               type="integer",
	 *               default="0",
	 *               description="Status"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="checkin",
	 *           type="string",
	 *           default="",
	 *           description="Checkin"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout",
	 *           type="string",
	 *           default="",
	 *           description="Checkout"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkin_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Checkin Formatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Checkout Formatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="guest_name",
	 *           type="string",
	 *           default="",
	 *           description="Guest Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="can_checkin",
	 *           type="integer",
	 *           default="0",
	 *           description="Can Checkin"
	 *         ),
	 *         @SWG\Property(
	 *           property="can_checkout",
	 *           type="integer",
	 *           default="0",
	 *           description="Can Checkout"
	 *         ),
	 *         @SWG\Property(
	 *           property="source",
	 *           type="string",
	 *           default="",
	 *           description="Booking Source"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkin_data",
	 *           type="object",
	 *           default="{}",
	 *           description="Checkin Data",
	 *             @SWG\Property(
	 *               property="actual_checkin",
	 *               type="string",
	 *               default="",
	 *               description="Actual Checkin"
	 *             ),
	 *             @SWG\Property(
	 *               property="actual_checkout",
	 *               type="string",
	 *               default="",
	 *               description="Actual Checkout"
	 *             ),
	 *             @SWG\Property(
	 *               property="expected_checkin",
	 *               type="string",
	 *               default="",
	 *               description="Expected Checkin"
	 *             ),
	 *             @SWG\Property(
	 *               property="expected_checkout",
	 *               type="string",
	 *               default="",
	 *               description="Expected Checkout"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="notes",
	 *           type="object",
	 *           default="{}",
	 *           description="Notes",
	 *             @SWG\Property(
	 *               property="managerial",
	 *               type="array",
	 *               default="[]",
	 *               description="Managerial",
	 *               @SWG\Items(
	 *                 type="object",
	 *                 @SWG\Property(
	 *                   property="id",
	 *                   type="integer",
	 *                   default="0",
	 *                   description="Property Id"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="remark",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Remark"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="type",
	 *                   type="integer",
	 *                   default="0",
	 *                   description="Property Type"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="last_updated_by",
	 *                   type="string",
	 *                   default="",
	 *                   description="Last Updated By"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="last_updated ",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Last Updated "
	 *                 )
	 *               )
	 *             ),
	 *             @SWG\Property(
	 *               property="operational",
	 *               type="object",
	 *               default="{}",
	 *               description="Operational",
	 *                 @SWG\Property(
	 *                   property="note",
	 *                   type="string",
	 *                   default="",
	 *                   description="Note"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="last_updated",
	 *                   type="string",
	 *                   default="",
	 *                   description="Last Updated"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="last_updated_by",
	 *                   type="string",
	 *                   default="",
	 *                   description="Last Updated By"
	 *                 )
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="traveller_email",
	 *           type="string",
	 *           default="",
	 *           description="Traveller Email"
	 *         ),
	 *         @SWG\Property(
	 *           property="contacts",
	 *           type="object",
	 *           default="{}",
	 *           description="Contacts",
	 *             @SWG\Property(
	 *               property="manager",
	 *               type="object",
	 *               default="{}",
	 *               description="Manager",
	 *                 @SWG\Property(
	 *                   property="primary",
	 *                   type="string",
	 *                   default="",
	 *                   description="Primary"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="secondary",
	 *                   type="string",
	 *                   default="",
	 *                   description="Secondary"
	 *                 )
	 *             ),
	 *             @SWG\Property(
	 *               property="traveller",
	 *               type="object",
	 *               default="{}",
	 *               description="Traveller",
	 *                 @SWG\Property(
	 *                   property="primary",
	 *                   type="string",
	 *                   default="",
	 *                   description="Primary"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="secondary",
	 *                   type="string",
	 *                   default="",
	 *                   description="Secondary"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="contact",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Contact"
	 *                 )
	 *             )
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
	 *         ),
	 *         @SWG\Property(
	 *           property="total_amount_unformatted",
	 *           type="integer",
	 *           default="0",
	 *           description="Total Amount Unformatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="paid_amount",
	 *           type="string",
	 *           default="",
	 *           description="Paid Amount"
	 *         ),
	 *         @SWG\Property(
	 *           property="paid_amount_unformatted",
	 *           type="integer",
	 *           default="0",
	 *           description="Paid Amount Unformatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="pending_payment",
	 *           type="integer",
	 *           default="0",
	 *           description="Pending Payment"
	 *         ),
	 *         @SWG\Property(
	 *           property="pending_payment_amount",
	 *           type="string",
	 *           default="",
	 *           description="Pending Payment Amount"
	 *         ),
	 *         @SWG\Property(
	 *           property="extra_services",
	 *           type="array",
	 *           default="[]",
	 *           description="Extra Services",
	 *           @SWG\Items(
	 *             type="object",
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="payment_option",
	 *           type="string",
	 *           default="",
	 *           description="Payment Option"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $booking_info_section = [];


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


}//end class
