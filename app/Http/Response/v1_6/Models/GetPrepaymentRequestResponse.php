<?php
/**
 * Response Model for Prepayment Request Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPrepaymentRequestResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPrepaymentRequestResponse",
 * description="Response Model for Prepayment Request Api",
 * )
 * // phpcs:enable
 */
class GetPrepaymentRequestResponse extends ApiResponse
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
	 *               description="Property Location Name"
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
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="start_date",
	 *       type="string",
	 *       default="",
	 *       description="Booking Start Date Eg. 28-11-2018"
	 *     ),
	 *     @SWG\Property(
	 *       property="end_date",
	 *       type="string",
	 *       default="",
	 *       description="Booking End Date Eg. 28-11-2018"
	 *     ),
	 *     @SWG\Property(
	 *       property="required_units",
	 *       type="integer",
	 *       default="0",
	 *       description="Required Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Guests Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="min_nights",
	 *       type="integer",
	 *       default="0",
	 *       description="Min Nights"
	 *     ),
	 *     @SWG\Property(
	 *       property="max_nights",
	 *       type="integer",
	 *       default="0",
	 *       description="Max Nights"
	 *     ),
	 *     @SWG\Property(
	 *       property="available_units",
	 *       type="integer",
	 *       default="0",
	 *       description="Available Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests_per_unit",
	 *       type="integer",
	 *       default="0",
	 *       description="Guests Per Unit"
	 *     ),
	 *     @SWG\Property(
	 *       property="instant_book",
	 *       type="integer",
	 *       default="0",
	 *       description="Instant Bookable status"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_section = [];

    /**
     * Request Invoice
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="invoice",
	 *   type="object",
	 *   default="{}",
	 *   description="Request Invoice",
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
	 *           description="Price Value Eg. ₹1,449"
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
	 *           description="Key Eg. Total amount, Cleaning fee, COA fee"
	 *         ),
	 *         @SWG\Property(
	 *           property="sub_key",
	 *           type="string",
	 *           default="",
	 *           description="Sub Key Eg. (₹1,449 x 2 nights)"
	 *         ),
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Price Value Eg. ₹2,898"
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
	 *           description="Price Value Eg. ₹3,822"
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
	 *           description="Section Bold Status in 0,1"
	 *         ),
	 *         @SWG\Property(
	 *           property="size",
	 *           type="integer",
	 *           default="0",
	 *           description="Section Size Status in 0,1"
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
    protected $invoice = [];

    /**
     * Payment Methods
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="payment_methods",
	 *   type="array",
	 *   default="[]",
	 *   description="Payment Methods",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="key",
	 *       type="string",
	 *       default="",
	 *       description="Key Eg. coa_payment, partial_payment, full_payment"
	 *     ),
	 *     @SWG\Property(
	 *       property="label",
	 *       type="string",
	 *       default="",
	 *       description="Label of payment methods"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Payment Methods Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="description",
	 *       type="string",
	 *       default="",
	 *       description="Payment Methods Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="sub_description",
	 *       type="string",
	 *       default="",
	 *       description="Payment Methods Sub Description"
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
	 *       description="Payable Later Before Date Eg. 28 Nov 2018"
	 *     ),
	 *     @SWG\Property(
	 *       property="icon",
	 *       type="string",
	 *       default="",
	 *       description="Icon Url"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $payment_methods = [];

    /**
     * Discount Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="discount_section",
	 *   type="object",
	 *   default="{}",
	 *   description="Discount Section",
	 *     @SWG\Property(
	 *       property="wallet",
	 *       type="object",
	 *       default="{}",
	 *       description="Wallet Section",
	 *         @SWG\Property(
	 *           property="wallet_money",
	 *           type="integer",
	 *           default="0",
	 *           description="Available Wallet Money"
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
	 *       description="Coupon Section",
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
	 *       description="Discount Section",
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
	 *           description="Discount Value"
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
     * User Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_section",
	 *   type="object",
	 *   default="{}",
	 *   description="User Section",
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
	 *           type="object",
	 *           default="{}",
	 *           description="Cancellation Id Eg. 7,8",
	 *             @SWG\Property(
	 *               property="id",
	 *               type="integer",
	 *               default="0",
	 *               description="Cancellation Policy Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="title",
	 *               type="string",
	 *               default="",
	 *               description="Cancellation Policy Title"
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
	 *               description="Cancellation Policy Desc"
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
     * Request Footer Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="footer_data",
	 *   type="object",
	 *   default="{}",
	 *   description="Request Footer Data",
	 *     @SWG\Property(
	 *       property="footer",
	 *       type="object",
	 *       default="{}",
	 *       description="Request Footer Section",
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Footer Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="sub",
	 *           type="string",
	 *           default="",
	 *           description="Footer Sub Title"
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
	 *           description="Final Amount Value"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="left_div",
	 *       type="object",
	 *       default="{}",
	 *       description="Footer Left Div Section",
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
	 *       description="Footer Right Div Section",
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
