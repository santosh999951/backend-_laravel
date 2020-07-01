<?php
/**
 * Response Model for Trip Detail Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetTripDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetTripDetailResponse",
 * description="Response Model for Trip Detail Api",
 * )
 * // phpcs:enable
 */
class GetTripDetailResponse extends ApiResponse
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
	 *       description="Property Tile Section",
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
	 *           description="Property Location",
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
	 *             @SWG\Property(
	 *               property="id",
	 *               type="integer",
	 *               default="0",
	 *               description="Tags Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="class",
	 *               type="string",
	 *               default="",
	 *               description="Tags Class"
	 *             ),
	 *             @SWG\Property(
	 *               property="text",
	 *               type="string",
	 *               default="",
	 *               description="Tags Text"
	 *             ),
	 *             @SWG\Property(
	 *               property="colorRgb",
	 *               type="string",
	 *               default="",
	 *               description="Tags ColorRgb"
	 *             ),
	 *             @SWG\Property(
	 *               property="colorHex",
	 *               type="string",
	 *               default="",
	 *               description="Tags ColorHex"
	 *             ),
	 *             @SWG\Property(
	 *               property="textRgb",
	 *               type="string",
	 *               default="",
	 *               description="Tags TextRgb"
	 *             ),
	 *             @SWG\Property(
	 *               property="textHex",
	 *               type="string",
	 *               default="",
	 *               description="Tags TextHex"
	 *             )
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
	 *               description="Amenities Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="name",
	 *               type="string",
	 *               default="",
	 *               description="Amenities Name"
	 *             ),
	 *             @SWG\Property(
	 *               property="icon",
	 *               type="string",
	 *               default="",
	 *               description="Amenities Icon"
	 *             ),
	 *             @SWG\Property(
	 *               property="rank",
	 *               type="integer",
	 *               default="0",
	 *               description="Amenities Rank"
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
	 *               description="Description Key"
	 *             ),
	 *             @SWG\Property(
	 *               property="title",
	 *               type="string",
	 *               default="",
	 *               description="Description Title"
	 *             ),
	 *             @SWG\Property(
	 *               property="value",
	 *               type="string",
	 *               default="",
	 *               description="Description Value"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="how_to_reach",
	 *           type="array",
	 *           default="[]",
	 *           description="How To Reach Property Info",
	 *           @SWG\Items(
	 *             type="object",
	 *             @SWG\Property(
	 *               property="key",
	 *               type="string",
	 *               default="",
	 *               description="Key"
	 *             ),
	 *             @SWG\Property(
	 *               property="value",
	 *               type="string",
	 *               default="",
	 *               description="Value"
	 *             ),
	 *             @SWG\Property(
	 *               property="icon",
	 *               type="string",
	 *               default="",
	 *               description="Icon"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="units_consumed",
	 *           type="integer",
	 *           default="0",
	 *           description="Units Consumed"
	 *         ),
	 *         @SWG\Property(
	 *           property="zipcode",
	 *           type="string",
	 *           default="",
	 *           description="Zipcode"
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
	 *           property="instant",
	 *           type="integer",
	 *           default="0",
	 *           description="Instant Bookable status"
	 *         ),
	 *         @SWG\Property(
	 *           property="coupon_code_used",
	 *           type="string",
	 *           default="",
	 *           description="Coupon Code Used in Booking Request"
	 *         ),
	 *         @SWG\Property(
	 *           property="wallet_money_used",
	 *           type="integer",
	 *           default="0",
	 *           description="Wallet Money Used in Booking Request"
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
	 *           description="Checkin Formatted Date Eg. 19 Nov 2018"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Checkout Formatted Date Eg. 19 Nov 2018"
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
	 *           description="Checkin Date Eg. 2018-11-19"
	 *         ),
	 *         @SWG\Property(
	 *           property="checkout",
	 *           type="string",
	 *           default="",
	 *           description="Checkout Date Eg. 2018-11-19"
	 *         ),
	 *         @SWG\Property(
	 *           property="can_review",
	 *           type="integer",
	 *           default="0",
	 *           description="Can Review in completed trip status such that user can review"
	 *         ),
	 *         @SWG\Property(
	 *           property="can_rate",
	 *           type="integer",
	 *           default="0",
	 *           description="Can Rate in completed trip status such that user can rate"
	 *         ),
	 *         @SWG\Property(
	 *           property="can_share_fb",
	 *           type="integer",
	 *           default="0",
	 *           description="Can Share Fb status for share Trip to Facebook"
	 *         ),
	 *         @SWG\Property(
	 *           property="units",
	 *           type="integer",
	 *           default="0",
	 *           description="Units"
	 *         ),
	 *         @SWG\Property(
	 *           property="resend_request",
	 *           type="integer",
	 *           default="0",
	 *           description="Resend Request"
	 *         ),
	 *         @SWG\Property(
	 *           property="check_other_date",
	 *           type="integer",
	 *           default="0",
	 *           description="Check Other Date"
	 *         ),
	 *         @SWG\Property(
	 *           property="expires_in",
	 *           type="integer",
	 *           default="0",
	 *           description="Expires In"
	 *         ),
	 *         @SWG\Property(
	 *           property="payment_url",
	 *           type="string",
	 *           default="",
	 *           description="Payment Url"
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
	 *           description="Currency Section",
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
	 *           description="Total Amount"
	 *         ),
	 *         @SWG\Property(
	 *           property="total_amount_unformatted",
	 *           type="float",
	 *           default="0.0",
	 *           description="Total Amount Unformatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="paid_amount_unformatted",
	 *           type="float",
	 *           default="0.0",
	 *           description="Paid Amount Unformatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="pending_payment",
	 *           type="integer",
	 *           default="0",
	 *           description="Pending Payment"
	 *         ),
	 *         @SWG\Property(
	 *           property="pending_payment_text",
	 *           type="string",
	 *           default="",
	 *           description="Pending Payment Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="pending_payment_amount",
	 *           type="string",
	 *           default="",
	 *           description="Pending Payment Amount"
	 *         ),
	 *         @SWG\Property(
	 *           property="pending_payment_url",
	 *           type="string",
	 *           default="",
	 *           description="Pending Payment Url"
	 *         ),
	 *         @SWG\Property(
	 *           property="payment_gateway_method",
	 *           type="string",
	 *           default="",
	 *           description="Payment Gateway Method"
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
	 *           description="Sub Key Eg. (for 1 night, 1 room, 1 guest)"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Price Value Eg. ₹2,353"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Section Show Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="raw_value",
	 *           type="integer",
	 *           default="0",
	 *           description="Raw Value"
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
	 *           description="Sub Key Eg. (₹2,353 x 3 nights)"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Price Value"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Section Show Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="raw_value",
	 *           type="integer",
	 *           default="0",
	 *           description="Raw Value"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="invoice_footer",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Invoice Footer",
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
	 *           description="Price Value"
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
	 *           description="Text Bold Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="size",
	 *           type="integer",
	 *           default="0",
	 *           description="Text Size Status either large or medium"
	 *         ),
	 *         @SWG\Property(
	 *           property="raw_value",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Raw Value"
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
     * Property Info Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_info_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Info Section",
	 *     @SWG\Property(
	 *       property="show_host",
	 *       type="integer",
	 *       default="0",
	 *       description="Show Host status to show the host information"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_name",
	 *       type="string",
	 *       default="",
	 *       description="Host Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_contact",
	 *       type="string",
	 *       default="",
	 *       description="Host Contact"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_address",
	 *       type="string",
	 *       default="",
	 *       description="Host Address"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_first_name",
	 *       type="string",
	 *       default="",
	 *       description="Host First Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_dob",
	 *       type="string",
	 *       default="",
	 *       description="Host Dob"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_created",
	 *       type="string",
	 *       default="",
	 *       description="Host Created"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_language",
	 *       type="string",
	 *       default="",
	 *       description="Host Language"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_work",
	 *       type="string",
	 *       default="",
	 *       description="Host Work"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_gender",
	 *       type="string",
	 *       default="",
	 *       description="Host Gender"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_info_section = [];

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
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Cancellation Policy Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Cancellation Policy Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="policy_days",
	 *           type="integer",
	 *           default="0",
	 *           description="Cancellation Policy Days"
	 *         ),
	 *         @SWG\Property(
	 *           property="desc",
	 *           type="string",
	 *           default="",
	 *           description="Cancellation Policy Description"
	 *         ),
	 *         @SWG\Property(
	 *           property="popup_text",
	 *           type="string",
	 *           default="",
	 *           description="Popup Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="cancel_text",
	 *           type="string",
	 *           default="",
	 *           description="Cancel Text"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="cancellable",
	 *       type="integer",
	 *       default="0",
	 *       description="Cancellable"
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
	 *           description="Cancellation Reasons Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="reason_title",
	 *           type="string",
	 *           default="",
	 *           description="Cancellation Reasons Title"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $cancellation_section = [];

    /**
     * Property Refund Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="refund_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Refund Section",
	 *     @SWG\Property(
	 *       property="amount",
	 *       type="string",
	 *       default="",
	 *       description="Property Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="status",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Status",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Property Text"
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
	 *           description="Property Status"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="show",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Show"
	 *     ),
	 *     @SWG\Property(
	 *       property="current_status",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Current Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="processing_date",
	 *       type="string",
	 *       default="",
	 *       description="Property Processing Date"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $refund_section = [];

    /**
     * Footer Data
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="footer_data",
	 *   type="object",
	 *   default="{}",
	 *   description="Footer Data",
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="sub",
	 *       type="string",
	 *       default="",
	 *       description="Sub Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="button_text",
	 *       type="string",
	 *       default="",
	 *       description="Button Text"
	 *     ),
	 *     @SWG\Property(
	 *       property="final_amount",
	 *       type="integer",
	 *       default="0",
	 *       description="Final Amount"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $footer_data = [];


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
     * Get Property_info_section
     *
     * @return object
     */
    public function getPropertyInfoSection()
    {
        return (empty($this->property_info_section) === false) ? $this->property_info_section : new \stdClass;

    }//end getPropertyInfoSection()


    /**
     * Set Property info section
     *
     * @param array $property_info_section Property info section.
     *
     * @return self
     */
    public function setPropertyInfoSection(array $property_info_section)
    {
        $this->property_info_section = $property_info_section;
        return $this;

    }//end setPropertyInfoSection()


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
     * Get Refund_section
     *
     * @return object
     */
    public function getRefundSection()
    {
        return (empty($this->refund_section) === false) ? $this->refund_section : new \stdClass;

    }//end getRefundSection()


    /**
     * Set Refund section
     *
     * @param array $refund_section Refund section.
     *
     * @return self
     */
    public function setRefundSection(array $refund_section)
    {
        $this->refund_section = $refund_section;
        return $this;

    }//end setRefundSection()


    /**
     * Get Footer_data
     *
     * @return object
     */
    public function getFooterData()
    {
        return (empty($this->footer_data) === false) ? $this->footer_data : new \stdClass;

    }//end getFooterData()


    /**
     * Set Footer data
     *
     * @param array $footer_data Footer data.
     *
     * @return self
     */
    public function setFooterData(array $footer_data)
    {
        $this->footer_data = $footer_data;
        return $this;

    }//end setFooterData()


}//end class
