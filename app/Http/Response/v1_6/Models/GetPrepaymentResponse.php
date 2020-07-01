<?php
/**
 * Response Model For Prepayment Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPrepaymentResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPrepaymentResponse",
 * description="Response Model For Prepayment Api",
 * )
 * // phpcs:enable
 */
class GetPrepaymentResponse extends ApiResponse
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
	 *       description="Property Tile Structure data",
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
	 *     ),
	 *     @SWG\Property(
	 *       property="start_date",
	 *       type="string",
	 *       default="",
	 *       description="Booking Start Date Eg. 12-12-2018"
	 *     ),
	 *     @SWG\Property(
	 *       property="end_date",
	 *       type="string",
	 *       default="",
	 *       description="Booking End Date Eg. 12-12-2018"
	 *     ),
	 *     @SWG\Property(
	 *       property="required_units",
	 *       type="integer",
	 *       default="0",
	 *       description="Booking Required Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Booking Guests"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected_units",
	 *       type="string",
	 *       default="",
	 *       description="Booking Selected Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected_guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Booking Selected Guests"
	 *     ),
	 *     @SWG\Property(
	 *       property="min_nights",
	 *       type="integer",
	 *       default="0",
	 *       description="Min Nights Stay"
	 *     ),
	 *     @SWG\Property(
	 *       property="max_nights",
	 *       type="integer",
	 *       default="0",
	 *       description="Max Nights Stay"
	 *     ),
	 *     @SWG\Property(
	 *       property="available_units",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Available Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests_per_unit",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Guests Per Unit"
	 *     ),
	 *     @SWG\Property(
	 *       property="instant_book",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Instant Book status"
	 *     ),
	 *     @SWG\Property(
	 *       property="bookable_as_unit",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Bookable As Unit"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_section = [];

    /**
     * Property Invoice
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="invoice",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Invoice",
	 *     @SWG\Property(
	 *       property="invoice_header",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Invoice Header",
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
	 *           description="Sub Key Eg. (for 1 night, 1 unit, 1 guest)"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Formatted Price Value Eg. ₹6,240"
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
	 *       description="Property Invoice Middle",
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
	 *           description="Sub Key Eg. (₹6,240 x 3 nights)"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Formatted Price Value Eg. ₹18,720"
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
	 *           description="Formatted Price Value Eg. ₹25,838"
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
	 *           description="Section Bold Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="size",
	 *           type="integer",
	 *           default="0",
	 *           description="Section Size Status"
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
	 *       description="Property Currency"
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
    protected $invoice = [];

    /**
     * Property Payment Methods
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="payment_methods",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Payment Methods",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="key",
	 *       type="string",
	 *       default="",
	 *       description="Key Eg. si_payment, partial_payment, full_payment"
	 *     ),
	 *     @SWG\Property(
	 *       property="label",
	 *       type="string",
	 *       default="",
	 *       description="Label of Payment Methods"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Title of Payment Methods"
	 *     ),
	 *     @SWG\Property(
	 *       property="description",
	 *       type="string",
	 *       default="",
	 *       description="Description of Payment Methods"
	 *     ),
	 *     @SWG\Property(
	 *       property="sub_description",
	 *       type="string",
	 *       default="",
	 *       description="Sub Description of Payment Methods"
	 *     ),
	 *     @SWG\Property(
	 *       property="popup_text",
	 *       type="string",
	 *       default="",
	 *       description="Popup Text"
	 *     ),
	 *     @SWG\Property(
	 *       property="payable_amount",
	 *       type="float",
	 *       default="0.0",
	 *       description="Payable Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="payable_now",
	 *       type="integer",
	 *       default="0",
	 *       description="Payable Now Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="payable_later",
	 *       type="float",
	 *       default="0.0",
	 *       description="Payable Later Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="payable_later_before",
	 *       type="string",
	 *       default="",
	 *       description="Payable Later Before Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="icon",
	 *       type="string",
	 *       default="",
	 *       description="Icon of Payment Methods"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $payment_methods = [];

    /**
     * Property Discount Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="discount_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Discount Section",
	 *     @SWG\Property(
	 *       property="wallet",
	 *       type="object",
	 *       default="{}",
	 *       description="Wallet",
	 *         @SWG\Property(
	 *           property="wallet_money",
	 *           type="integer",
	 *           default="0",
	 *           description="Wallet Money"
	 *         ),
	 *         @SWG\Property(
	 *           property="applicable",
	 *           type="integer",
	 *           default="0",
	 *           description="Wallet Applicable Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="wallet_currency_symbol",
	 *           type="string",
	 *           default="",
	 *           description="Wallet Currency Symbol"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="coupon",
	 *       type="object",
	 *       default="{}",
	 *       description="Coupon Applicable Section",
	 *         @SWG\Property(
	 *           property="applicable",
	 *           type="integer",
	 *           default="0",
	 *           description="Coupon Applicable Status"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="discount",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Discount",
	 *         @SWG\Property(
	 *           property="discount_type",
	 *           type="string",
	 *           default="",
	 *           description="Discount Type"
	 *         ),
	 *         @SWG\Property(
	 *           property="discount",
	 *           type="integer",
	 *           default="0",
	 *           description="Discount Amount"
	 *         ),
	 *         @SWG\Property(
	 *           property="discount_code",
	 *           type="string",
	 *           default="",
	 *           description="Discount Code"
	 *         ),
	 *         @SWG\Property(
	 *           property="discount_message",
	 *           type="string",
	 *           default="",
	 *           description="Discount Message"
	 *         ),
	 *         @SWG\Property(
	 *           property="discount_valid",
	 *           type="integer",
	 *           default="0",
	 *           description="Discount Valid Status"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $discount_section = [];

    /**
     * Property Footer Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="footer_data",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Footer Data",
	 *     @SWG\Property(
	 *       property="footer",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Footer Section",
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="sub",
	 *           type="string",
	 *           default="",
	 *           description="Sub Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="button_text",
	 *           type="string",
	 *           default="",
	 *           description="Button Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="final_amount",
	 *           type="float",
	 *           default="0.0",
	 *           description="Final Amount"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="left_div",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Left Div Section",
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Text"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="right_div",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Right Div Section",
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Text"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $footer_data = [];

    /**
     * Property User Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Property User Section",
	 *     @SWG\Property(
	 *       property="is_mobile_verified",
	 *       type="integer",
	 *       default="0",
	 *       description="User Mobile Verified Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_user_referred",
	 *       type="integer",
	 *       default="0",
	 *       description="User Referred Status"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $user_section = [];

    /**
     * Property Cancellation Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="cancellation_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Cancellation Section",
	 *     @SWG\Property(
	 *       property="cancellation_policy_info",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Cancellation Policy Info Section",
	 *         @SWG\Property(
	 *           property="[Integer]",
	 *           type="object",
	 *           default="{}",
	 *           description="Cancellation Id",
	 *             @SWG\Property(
	 *               property="id",
	 *               type="integer",
	 *               default="0",
	 *               description="Cancellation Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="title",
	 *               type="string",
	 *               default="",
	 *               description="Cancellation Title"
	 *             ),
	 *             @SWG\Property(
	 *               property="policy_days",
	 *               type="integer",
	 *               default="0",
	 *               description="Cancellation Policy Days"
	 *             ),
	 *             @SWG\Property(
	 *               property="desc",
	 *               type="string",
	 *               default="",
	 *               description="Cancellation Description"
	 *             ),
	 *             @SWG\Property(
	 *               property="popup_text",
	 *               type="string",
	 *               default="",
	 *               description="Popup Text"
	 *             )
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="url",
	 *       type="string",
	 *       default="",
	 *       description="Property Url"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $cancellation_section = [];

    /**
     * Misconception Error
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="misconception",
	 *   type="string",
	 *   default="",
	 *   description="Misconception Error"
	 * )
     * // phpcs:enable
     */
    protected $misconception = '';

    /**
     * Misconception Error Code
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="misconception_code",
	 *   type="string",
	 *   default="",
	 *   description="Misconception Error Code"
	 * )
     * // phpcs:enable
     */
    protected $misconception_code = '';


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


    /**
     * Get Payment_methods
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        return $this->payment_methods;

    }//end getPaymentMethods()


    /**
     * Set Payment methods
     *
     * @param array $payment_methods Payment methods.
     *
     * @return self
     */
    public function setPaymentMethods(array $payment_methods)
    {
        $this->payment_methods = $payment_methods;
        return $this;

    }//end setPaymentMethods()


    /**
     * Get Discount_section
     *
     * @return object
     */
    public function getDiscountSection()
    {
        return (empty($this->discount_section) === false) ? $this->discount_section : new \stdClass;

    }//end getDiscountSection()


    /**
     * Set Discount section
     *
     * @param array $discount_section Discount section.
     *
     * @return self
     */
    public function setDiscountSection(array $discount_section)
    {
        $this->discount_section = $discount_section;
        return $this;

    }//end setDiscountSection()


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


    /**
     * Get User_section
     *
     * @return object
     */
    public function getUserSection()
    {
        return (empty($this->user_section) === false) ? $this->user_section : new \stdClass;

    }//end getUserSection()


    /**
     * Set User section
     *
     * @param array $user_section User section.
     *
     * @return self
     */
    public function setUserSection(array $user_section)
    {
        $this->user_section = $user_section;
        return $this;

    }//end setUserSection()


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
     * Get Misconception
     *
     * @return string
     */
    public function getMisconception()
    {
        return $this->misconception;

    }//end getMisconception()


    /**
     * Set Misconception
     *
     * @param string $misconception Misconception.
     *
     * @return self
     */
    public function setMisconception(string $misconception)
    {
        $this->misconception = $misconception;
        return $this;

    }//end setMisconception()


    /**
     * Get Misconception_code
     *
     * @return string
     */
    public function getMisconceptionCode()
    {
        return $this->misconception_code;

    }//end getMisconceptionCode()


    /**
     * Set Misconception code
     *
     * @param string $misconception_code Misconception code.
     *
     * @return self
     */
    public function setMisconceptionCode(string $misconception_code)
    {
        $this->misconception_code = $misconception_code;
        return $this;

    }//end setMisconceptionCode()


}//end class
