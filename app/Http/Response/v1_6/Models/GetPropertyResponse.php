<?php
/**
 * Response Model For Property Detail Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPropertyResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPropertyResponse",
 * description="Response Model For Property Detail Api",
 * )
 * // phpcs:enable
 */
class GetPropertyResponse extends ApiResponse
{

    /**
     * Property Id
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="id",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Id"
	 * )
     * // phpcs:enable
     */
    protected $id = 0;

    /**
     * Property Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Property Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $property_hash_id = '';

    /**
     * Property Original Title
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_title",
	 *   type="string",
	 *   default="",
	 *   description="Property Original Title"
	 * )
     * // phpcs:enable
     */
    protected $property_title = '';

    /**
     * Property Generated Title
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="title",
	 *   type="string",
	 *   default="",
	 *   description="Property Generated Title"
	 * )
     * // phpcs:enable
     */
    protected $title = '';

    /**
     * Property Score
     *
     * @var float
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_score",
	 *   type="float",
	 *   default="0.0",
	 *   description="Property Score"
	 * )
     * // phpcs:enable
     */
    protected $property_score = 0.0;

    /**
     * Property Images
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_images",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Images",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="image",
	 *       type="string",
	 *       default="",
	 *       description="Image Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="caption",
	 *       type="string",
	 *       default="",
	 *       description="Image Caption"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $property_images = [];

    /**
     * Property Image Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_image_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Image Count"
	 * )
     * // phpcs:enable
     */
    protected $property_image_count = 0;

    /**
     * Property Video
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_video",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Video",
	 *   @SWG\Items(
	 *     type="object",
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $property_video = [];

    /**
     * Property Min Nights
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="min_nights",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Min Nights"
	 * )
     * // phpcs:enable
     */
    protected $min_nights = 0;

    /**
     * Property Max Nights
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="max_nights",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Max Nights"
	 * )
     * // phpcs:enable
     */
    protected $max_nights = 0;

    /**
     * Property Location
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="location",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Location",
	 *     @SWG\Property(
	 *       property="area",
	 *       type="string",
	 *       default="",
	 *       description="Location Area"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="Location City"
	 *     ),
	 *     @SWG\Property(
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="Location State"
	 *     ),
	 *     @SWG\Property(
	 *       property="country",
	 *       type="object",
	 *       default="{}",
	 *       description="Location Country",
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Country Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="ccode",
	 *           type="string",
	 *           default="",
	 *           description="Country Ccode"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="location_name",
	 *       type="string",
	 *       default="",
	 *       description="Location Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="latitude",
	 *       type="string",
	 *       default="",
	 *       description="Latitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="longitude",
	 *       type="string",
	 *       default="",
	 *       description="Longitude"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $location = [];

    /**
     * Property Bookable Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="bookable",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Bookable Status"
	 * )
     * // phpcs:enable
     */
    protected $bookable = 0;

    /**
     * Property Host Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="host_id",
	 *   type="string",
	 *   default="",
	 *   description="Property Host Id"
	 * )
     * // phpcs:enable
     */
    protected $host_id = '';

    /**
     * Property Host Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="host_name",
	 *   type="string",
	 *   default="",
	 *   description="Property Host Name"
	 * )
     * // phpcs:enable
     */
    protected $host_name = '';

    /**
     * Property Host Image
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="host_image",
	 *   type="string",
	 *   default="",
	 *   description="Property Host Image"
	 * )
     * // phpcs:enable
     */
    protected $host_image = '';

    /**
     * Property Review Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="review_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Review Count"
	 * )
     * // phpcs:enable
     */
    protected $review_count = 0;

    /**
     * Property Reviews
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="reviews",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Reviews",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Guests"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_rating",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Property Rating"
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Traveller Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Traveller Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="review_date",
	 *       type="string",
	 *       default="",
	 *       description="Property Review Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="comment",
	 *       type="string",
	 *       default="",
	 *       description="Property Comment"
	 *     ),
	 *     @SWG\Property(
	 *       property="nights",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Nights"
	 *     ),
	 *     @SWG\Property(
	 *       property="review_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Review Images",
	 *       @SWG\Items(
	 *         type="object",
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_image",
	 *       type="string",
	 *       default="",
	 *       description="Property Traveller Image"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $reviews = [];

    /**
     * Property Tags
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="tags",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Tags",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Tags Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="class",
	 *       type="string",
	 *       default="",
	 *       description="Tags Class"
	 *     ),
	 *     @SWG\Property(
	 *       property="text",
	 *       type="string",
	 *       default="",
	 *       description="Tags Text"
	 *     ),
	 *     @SWG\Property(
	 *       property="colorRgb",
	 *       type="string",
	 *       default="",
	 *       description="Tags ColorRgb Eg. (255,239,140)"
	 *     ),
	 *     @SWG\Property(
	 *       property="colorHex",
	 *       type="string",
	 *       default="",
	 *       description="Tags ColorHex Eg. #ffef8c"
	 *     ),
	 *     @SWG\Property(
	 *       property="textRgb",
	 *       type="string",
	 *       default="",
	 *       description="Tags TextRgb Eg. (183,172,39)"
	 *     ),
	 *     @SWG\Property(
	 *       property="textHex",
	 *       type="string",
	 *       default="",
	 *       description="Tags TextHex Eg. #b7ac27"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $tags = [];

    /**
     * Property Cancellation Policy
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="cancellation_policy",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Cancellation Policy",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Cancellation Policy Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Cancellation Policy Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="policy_days",
	 *       type="integer",
	 *       default="0",
	 *       description="Cancellation Policy Days"
	 *     ),
	 *     @SWG\Property(
	 *       property="desc",
	 *       type="string",
	 *       default="",
	 *       description="Cancellation Policy Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="popup_text",
	 *       type="string",
	 *       default="",
	 *       description="Cancellation Popup Text"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $cancellation_policy = [];

    /**
     * Property About
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="about",
	 *   type="string",
	 *   default="",
	 *   description="Property About"
	 * )
     * // phpcs:enable
     */
    protected $about = '';

    /**
     * Property Usp
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="usp",
	 *   type="string",
	 *   default="",
	 *   description="Property Usp"
	 * )
     * // phpcs:enable
     */
    protected $usp = '';

    /**
     * Property Description
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="description",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Description",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="key",
	 *       type="string",
	 *       default="",
	 *       description="Description Key Eg. space, extra_details, local_experience"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Description Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="value",
	 *       type="string",
	 *       default="",
	 *       description="Description Value"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $description = [];

    /**
     * Property How To Reach
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="how_to_reach",
	 *   type="array",
	 *   default="[]",
	 *   description="Property How To Reach",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="key",
	 *       type="string",
	 *       default="",
	 *       description="How to reach Key Eg. airport, train_station"
	 *     ),
	 *     @SWG\Property(
	 *       property="value",
	 *       type="string",
	 *       default="",
	 *       description="How to reach Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="icon",
	 *       type="string",
	 *       default="",
	 *       description="How to reach Icon"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $how_to_reach = [];

    /**
     * Property Space Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="space",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Space Section",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="key",
	 *       type="string",
	 *       default="",
	 *       description="Property Space Key Eg. property_type, room_type"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Property Space Name Eg. Villa, Entire Home"
	 *     ),
	 *     @SWG\Property(
	 *       property="icon_id",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Space Icon Id"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $space = [];

    /**
     * Property Amenities
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="amenities",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Amenities",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Amenities Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Amenities Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="icon",
	 *       type="string",
	 *       default="",
	 *       description="Amenities Icon"
	 *     ),
	 *     @SWG\Property(
	 *       property="rank",
	 *       type="integer",
	 *       default="0",
	 *       description="Amenities Rank"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $amenities = [];

    /**
     * Property Is Wishlisted Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_wishlisted",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Is Wishlisted Status"
	 * )
     * // phpcs:enable
     */
    protected $is_wishlisted = 0;

    /**
     * Property Checkin Date Eg. 2018-12-12
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="checkin",
	 *   type="string",
	 *   default="",
	 *   description="Property Checkin Date Eg. 2018-12-12"
	 * )
     * // phpcs:enable
     */
    protected $checkin = '';

    /**
     * Property Checkout Date Eg. 2018-12-12
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="checkout",
	 *   type="string",
	 *   default="",
	 *   description="Property Checkout Date Eg. 2018-12-12"
	 * )
     * // phpcs:enable
     */
    protected $checkout = '';

    /**
     * Property Selected Checkin
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="selected_checkin",
	 *   type="string",
	 *   default="",
	 *   description="Property Selected Checkin"
	 * )
     * // phpcs:enable
     */
    protected $selected_checkin = '';

    /**
     * Property Selected Checkout
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="selected_checkout",
	 *   type="string",
	 *   default="",
	 *   description="Property Selected Checkout"
	 * )
     * // phpcs:enable
     */
    protected $selected_checkout = '';

    /**
     * Property Selected Guests
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="selected_guests",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Selected Guests"
	 * )
     * // phpcs:enable
     */
    protected $selected_guests = 0;

    /**
     * Property Selected Units
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="selected_units",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Selected Units"
	 * )
     * // phpcs:enable
     */
    protected $selected_units = 0;

    /**
     * Property Required Units
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="required_units",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Required Units"
	 * )
     * // phpcs:enable
     */
    protected $required_units = 0;

    /**
     * Property Available Units
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="available_units",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Available Units"
	 * )
     * // phpcs:enable
     */
    protected $available_units = 0;

    /**
     * Property Guests Per Unit
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="guests_per_unit",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Guests Per Unit"
	 * )
     * // phpcs:enable
     */
    protected $guests_per_unit = 0;

    /**
     * Property Pricing Section
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_pricing",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Pricing Section",
	 *     @SWG\Property(
	 *       property="currency",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Pricing Currency",
	 *         @SWG\Property(
	 *           property="webicon",
	 *           type="string",
	 *           default="",
	 *           description="Currency Webicon"
	 *         ),
	 *         @SWG\Property(
	 *           property="non-webicon",
	 *           type="string",
	 *           default="",
	 *           description="Currency Non-webicon"
	 *         ),
	 *         @SWG\Property(
	 *           property="iso_code",
	 *           type="string",
	 *           default="",
	 *           description="Currency Iso Code"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="cleaning_price",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Cleaning Price"
	 *     ),
	 *     @SWG\Property(
	 *       property="per_night_price",
	 *       type="string",
	 *       default="",
	 *       description="Property Per Night Price"
	 *     ),
	 *     @SWG\Property(
	 *       property="per_night_price_unformatted",
	 *       type="float",
	 *       default="0.0",
	 *       description="Property Per Night Price Unformatted"
	 *     ),
	 *     @SWG\Property(
	 *       property="discount",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Discount"
	 *     ),
	 *     @SWG\Property(
	 *       property="original_price",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Original Price"
	 *     ),
	 *     @SWG\Property(
	 *       property="per_night_per_guest_extra_guest_price",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Per Night Per Guest Extra Guest Price"
	 *     ),
	 *     @SWG\Property(
	 *       property="per_night_all_guest_extra_guest_price",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Per Night All Guest Extra Guest Price"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_instant_bookable",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Is Instant Bookable"
	 *     ),
	 *     @SWG\Property(
	 *       property="per_unit_guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Per Unit Guests"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_pricing = [];

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
	 *       description="Payment Method Key Eg. si_payment, partial_payment, full_payment"
	 *     ),
	 *     @SWG\Property(
	 *       property="value",
	 *       type="string",
	 *       default="",
	 *       description="Payment Method Value Eg. Charge later, Partial payment, Full payment"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $payment_methods = [];

    /**
     * Selected Payment Method
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="selected_payment_method",
	 *   type="string",
	 *   default="",
	 *   description="Selected Payment Method"
	 * )
     * // phpcs:enable
     */
    protected $selected_payment_method = '';

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
	 *       property="footer",
	 *       type="object",
	 *       default="{}",
	 *       description="Footer Main Section",
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
	 *           description="Footer Sub Title Eg. Reserve with ₹1"
	 *         ),
	 *         @SWG\Property(
	 *           property="button_text",
	 *           type="string",
	 *           default="",
	 *           description="Footer Button Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="final_amount",
	 *           type="float",
	 *           default="0.0",
	 *           description="Footer Final Amount"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="left_div",
	 *       type="object",
	 *       default="{}",
	 *       description="Left Div Section",
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Left Div Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Left Div Text"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="right_div",
	 *       type="object",
	 *       default="{}",
	 *       description="Right Div Section",
	 *         @SWG\Property(
	 *           property="title",
	 *           type="string",
	 *           default="",
	 *           description="Right Div Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Right Div Text"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $footer_data = [];

    /**
     * Similar Properties
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="similar_properties",
	 *   type="array",
	 *   default="[]",
	 *   description="Similar Properties",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="property_id",
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
	 *       property="property_score",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Score"
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
	 *       description="Property Room Type Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_name",
	 *       type="string",
	 *       default="",
	 *       description="Host Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="host_image",
	 *       type="string",
	 *       default="",
	 *       description="Host Image"
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
	 *       description="Property Accomodation"
	 *     ),
	 *     @SWG\Property(
	 *       property="min_units_required",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Min Units Required"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_accomodation",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Total Accomodation"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_liked_by_user",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Wishlisted Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="prices",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Prices",
	 *         @SWG\Property(
	 *           property="display_discount",
	 *           type="integer",
	 *           default="0",
	 *           description="Display Discount"
	 *         ),
	 *         @SWG\Property(
	 *           property="smart_discount",
	 *           type="object",
	 *           default="{}",
	 *           description="Smart Discount",
	 *             @SWG\Property(
	 *               property="header",
	 *               type="string",
	 *               default="",
	 *               description="Header"
	 *             ),
	 *             @SWG\Property(
	 *               property="discount",
	 *               type="integer",
	 *               default="0",
	 *               description="Discount"
	 *             ),
	 *             @SWG\Property(
	 *               property="footer",
	 *               type="string",
	 *               default="",
	 *               description="Footer"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="final_currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Property Final Currency",
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
	 *           property="price_after_discount",
	 *           type="string",
	 *           default="",
	 *           description="Property Price After Discount"
	 *         ),
	 *         @SWG\Property(
	 *           property="price_after_discount_unformatted",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Price After Discount Unformatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="price_before_discount",
	 *           type="string",
	 *           default="",
	 *           description="Property Price Before Discount"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="payment_methods",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Payment Methods",
	 *         @SWG\Property(
	 *           property="instant_book",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Instant Book Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="cash_on_arrival",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Cash On Arrival Status"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Generated Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_title",
	 *       type="string",
	 *       default="",
	 *       description="Property Original Title"
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
	 *           description="Images Caption"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="property_videos_available",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Videos Available"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_tags",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Tags",
	 *       @SWG\Items(
	 *         type="object",
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="url",
	 *       type="string",
	 *       default="",
	 *       description="Property Url"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $similar_properties = [];

    /**
     * Property Metadata
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="meta",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Metadata",
	 *     @SWG\Property(
	 *       property="meta_title",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="canonical_url",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Canonical Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="meta_desc",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="meta_image",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Image"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $meta = [];

    /**
     * Property enabled Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="enabled",
	 *   type="integer",
	 *   default="0",
	 *   description="Property enabled Status"
	 * )
     * // phpcs:enable
     */
    protected $enabled = 0;

    /**
     * Property Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="status",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Status"
	 * )
     * // phpcs:enable
     */
    protected $status = 0;

    /**
     * Property prive Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="prive",
	 *   type="integer",
	 *   default="0",
	 *   description="Property prive Status"
	 * )
     * // phpcs:enable
     */
    protected $prive = 0;

    /**
     * Property Attraction Images
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="attraction_images",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Attraction Images",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="image",
	 *       type="string",
	 *       default="",
	 *       description="Property Image"
	 *     ),
	 *     @SWG\Property(
	 *       property="caption",
	 *       type="string",
	 *       default="",
	 *       description="Property Caption"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $attraction_images = [];

    /**
     * Property Misconception
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="misconception",
	 *   type="string",
	 *   default="",
	 *   description="Property Misconception"
	 * )
     * // phpcs:enable
     */
    protected $misconception = '';

    /**
     * Property Misconception Code
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="misconception_code",
	 *   type="string",
	 *   default="",
	 *   description="Property Misconception Code"
	 * )
     * // phpcs:enable
     */
    protected $misconception_code = '';


    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;

    }//end getId()


    /**
     * Set Id
     *
     * @param integer $id Id.
     *
     * @return self
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;

    }//end setId()


    /**
     * Get Property_hash_id
     *
     * @return string
     */
    public function getPropertyHashId()
    {
        return $this->property_hash_id;

    }//end getPropertyHashId()


    /**
     * Set Property hash id
     *
     * @param string $property_hash_id Property hash id.
     *
     * @return self
     */
    public function setPropertyHashId(string $property_hash_id)
    {
        $this->property_hash_id = $property_hash_id;
        return $this;

    }//end setPropertyHashId()


    /**
     * Get Property_title
     *
     * @return string
     */
    public function getPropertyTitle()
    {
        return $this->property_title;

    }//end getPropertyTitle()


    /**
     * Set Property title
     *
     * @param string $property_title Property title.
     *
     * @return self
     */
    public function setPropertyTitle(string $property_title)
    {
        $this->property_title = $property_title;
        return $this;

    }//end setPropertyTitle()


    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;

    }//end getTitle()


    /**
     * Set Title
     *
     * @param string $title Title.
     *
     * @return self
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;

    }//end setTitle()


    /**
     * Get Property_score
     *
     * @return float
     */
    public function getPropertyScore()
    {
        return $this->property_score;

    }//end getPropertyScore()


    /**
     * Set Property score
     *
     * @param float $property_score Property score.
     *
     * @return self
     */
    public function setPropertyScore(float $property_score)
    {
        $this->property_score = $property_score;
        return $this;

    }//end setPropertyScore()


    /**
     * Get Property_images
     *
     * @return array
     */
    public function getPropertyImages()
    {
        return $this->property_images;

    }//end getPropertyImages()


    /**
     * Set Property images
     *
     * @param array $property_images Property images.
     *
     * @return self
     */
    public function setPropertyImages(array $property_images)
    {
        $this->property_images = $property_images;
        return $this;

    }//end setPropertyImages()


    /**
     * Get Property_image_count
     *
     * @return integer
     */
    public function getPropertyImageCount()
    {
        return $this->property_image_count;

    }//end getPropertyImageCount()


    /**
     * Set Property image count
     *
     * @param integer $property_image_count Property image count.
     *
     * @return self
     */
    public function setPropertyImageCount(int $property_image_count)
    {
        $this->property_image_count = $property_image_count;
        return $this;

    }//end setPropertyImageCount()


    /**
     * Get Property_video
     *
     * @return array
     */
    public function getPropertyVideo()
    {
        return $this->property_video;

    }//end getPropertyVideo()


    /**
     * Set Property video
     *
     * @param array $property_video Property video.
     *
     * @return self
     */
    public function setPropertyVideo(array $property_video)
    {
        $this->property_video = $property_video;
        return $this;

    }//end setPropertyVideo()


    /**
     * Get Min_nights
     *
     * @return integer
     */
    public function getMinNights()
    {
        return $this->min_nights;

    }//end getMinNights()


    /**
     * Set Min nights
     *
     * @param integer $min_nights Min nights.
     *
     * @return self
     */
    public function setMinNights(int $min_nights)
    {
        $this->min_nights = $min_nights;
        return $this;

    }//end setMinNights()


    /**
     * Get Max_nights
     *
     * @return integer
     */
    public function getMaxNights()
    {
        return $this->max_nights;

    }//end getMaxNights()


    /**
     * Set Max nights
     *
     * @param integer $max_nights Max nights.
     *
     * @return self
     */
    public function setMaxNights(int $max_nights)
    {
        $this->max_nights = $max_nights;
        return $this;

    }//end setMaxNights()


    /**
     * Get Location
     *
     * @return object
     */
    public function getLocation()
    {
        return (empty($this->location) === false) ? $this->location : new \stdClass;

    }//end getLocation()


    /**
     * Set Location
     *
     * @param array $location Location.
     *
     * @return self
     */
    public function setLocation(array $location)
    {
        $this->location = $location;
        return $this;

    }//end setLocation()


    /**
     * Get Bookable
     *
     * @return integer
     */
    public function getBookable()
    {
        return $this->bookable;

    }//end getBookable()


    /**
     * Set Bookable
     *
     * @param integer $bookable Bookable.
     *
     * @return self
     */
    public function setBookable(int $bookable)
    {
        $this->bookable = $bookable;
        return $this;

    }//end setBookable()


    /**
     * Get Host_id
     *
     * @return string
     */
    public function getHostId()
    {
        return $this->host_id;

    }//end getHostId()


    /**
     * Set Host id
     *
     * @param string $host_id Host id.
     *
     * @return self
     */
    public function setHostId(string $host_id)
    {
        $this->host_id = $host_id;
        return $this;

    }//end setHostId()


    /**
     * Get Host_name
     *
     * @return string
     */
    public function getHostName()
    {
        return $this->host_name;

    }//end getHostName()


    /**
     * Set Host name
     *
     * @param string $host_name Host name.
     *
     * @return self
     */
    public function setHostName(string $host_name)
    {
        $this->host_name = $host_name;
        return $this;

    }//end setHostName()


    /**
     * Get Host_image
     *
     * @return string
     */
    public function getHostImage()
    {
        return $this->host_image;

    }//end getHostImage()


    /**
     * Set Host image
     *
     * @param string $host_image Host image.
     *
     * @return self
     */
    public function setHostImage(string $host_image)
    {
        $this->host_image = $host_image;
        return $this;

    }//end setHostImage()


    /**
     * Get Review_count
     *
     * @return integer
     */
    public function getReviewCount()
    {
        return $this->review_count;

    }//end getReviewCount()


    /**
     * Set Review count
     *
     * @param integer $review_count Review count.
     *
     * @return self
     */
    public function setReviewCount(int $review_count)
    {
        $this->review_count = $review_count;
        return $this;

    }//end setReviewCount()


    /**
     * Get Reviews
     *
     * @return array
     */
    public function getReviews()
    {
        return $this->reviews;

    }//end getReviews()


    /**
     * Set Reviews
     *
     * @param array $reviews Reviews.
     *
     * @return self
     */
    public function setReviews(array $reviews)
    {
        $this->reviews = $reviews;
        return $this;

    }//end setReviews()


    /**
     * Get Tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;

    }//end getTags()


    /**
     * Set Tags
     *
     * @param array $tags Tags.
     *
     * @return self
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;

    }//end setTags()


    /**
     * Get Cancellation_policy
     *
     * @return object
     */
    public function getCancellationPolicy()
    {
        return (empty($this->cancellation_policy) === false) ? $this->cancellation_policy : new \stdClass;

    }//end getCancellationPolicy()


    /**
     * Set Cancellation policy
     *
     * @param array $cancellation_policy Cancellation policy.
     *
     * @return self
     */
    public function setCancellationPolicy(array $cancellation_policy)
    {
        $this->cancellation_policy = $cancellation_policy;
        return $this;

    }//end setCancellationPolicy()


    /**
     * Get About
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;

    }//end getAbout()


    /**
     * Set About
     *
     * @param string $about About.
     *
     * @return self
     */
    public function setAbout(string $about)
    {
        $this->about = $about;
        return $this;

    }//end setAbout()


    /**
     * Get Usp
     *
     * @return string
     */
    public function getUsp()
    {
        return $this->usp;

    }//end getUsp()


    /**
     * Set Usp
     *
     * @param string $usp Usp.
     *
     * @return self
     */
    public function setUsp(string $usp)
    {
        $this->usp = $usp;
        return $this;

    }//end setUsp()


    /**
     * Get Description
     *
     * @return array
     */
    public function getDescription()
    {
        return $this->description;

    }//end getDescription()


    /**
     * Set Description
     *
     * @param array $description Description.
     *
     * @return self
     */
    public function setDescription(array $description)
    {
        $this->description = $description;
        return $this;

    }//end setDescription()


    /**
     * Get How_to_reach
     *
     * @return array
     */
    public function getHowToReach()
    {
        return $this->how_to_reach;

    }//end getHowToReach()


    /**
     * Set How to reach
     *
     * @param array $how_to_reach How to reach.
     *
     * @return self
     */
    public function setHowToReach(array $how_to_reach)
    {
        $this->how_to_reach = $how_to_reach;
        return $this;

    }//end setHowToReach()


    /**
     * Get Space
     *
     * @return array
     */
    public function getSpace()
    {
        return $this->space;

    }//end getSpace()


    /**
     * Set Space
     *
     * @param array $space Space.
     *
     * @return self
     */
    public function setSpace(array $space)
    {
        $this->space = $space;
        return $this;

    }//end setSpace()


    /**
     * Get Amenities
     *
     * @return array
     */
    public function getAmenities()
    {
        return $this->amenities;

    }//end getAmenities()


    /**
     * Set Amenities
     *
     * @param array $amenities Amenities.
     *
     * @return self
     */
    public function setAmenities(array $amenities)
    {
        $this->amenities = $amenities;
        return $this;

    }//end setAmenities()


    /**
     * Get Is_wishlisted
     *
     * @return integer
     */
    public function getIsWishlisted()
    {
        return $this->is_wishlisted;

    }//end getIsWishlisted()


    /**
     * Set Is wishlisted
     *
     * @param integer $is_wishlisted Is wishlisted.
     *
     * @return self
     */
    public function setIsWishlisted(int $is_wishlisted)
    {
        $this->is_wishlisted = $is_wishlisted;
        return $this;

    }//end setIsWishlisted()


    /**
     * Get Checkin
     *
     * @return string
     */
    public function getCheckin()
    {
        return $this->checkin;

    }//end getCheckin()


    /**
     * Set Checkin
     *
     * @param string $checkin Checkin.
     *
     * @return self
     */
    public function setCheckin(string $checkin)
    {
        $this->checkin = $checkin;
        return $this;

    }//end setCheckin()


    /**
     * Get Checkout
     *
     * @return string
     */
    public function getCheckout()
    {
        return $this->checkout;

    }//end getCheckout()


    /**
     * Set Checkout
     *
     * @param string $checkout Checkout.
     *
     * @return self
     */
    public function setCheckout(string $checkout)
    {
        $this->checkout = $checkout;
        return $this;

    }//end setCheckout()


    /**
     * Get Selected_checkin
     *
     * @return string
     */
    public function getSelectedCheckin()
    {
        return $this->selected_checkin;

    }//end getSelectedCheckin()


    /**
     * Set Selected checkin
     *
     * @param string $selected_checkin Selected checkin.
     *
     * @return self
     */
    public function setSelectedCheckin(string $selected_checkin)
    {
        $this->selected_checkin = $selected_checkin;
        return $this;

    }//end setSelectedCheckin()


    /**
     * Get Selected_checkout
     *
     * @return string
     */
    public function getSelectedCheckout()
    {
        return $this->selected_checkout;

    }//end getSelectedCheckout()


    /**
     * Set Selected checkout
     *
     * @param string $selected_checkout Selected checkout.
     *
     * @return self
     */
    public function setSelectedCheckout(string $selected_checkout)
    {
        $this->selected_checkout = $selected_checkout;
        return $this;

    }//end setSelectedCheckout()


    /**
     * Get Selected_guests
     *
     * @return integer
     */
    public function getSelectedGuests()
    {
        return $this->selected_guests;

    }//end getSelectedGuests()


    /**
     * Set Selected guests
     *
     * @param integer $selected_guests Selected guests.
     *
     * @return self
     */
    public function setSelectedGuests(int $selected_guests)
    {
        $this->selected_guests = $selected_guests;
        return $this;

    }//end setSelectedGuests()


    /**
     * Get Selected_units
     *
     * @return integer
     */
    public function getSelectedUnits()
    {
        return $this->selected_units;

    }//end getSelectedUnits()


    /**
     * Set Selected units
     *
     * @param integer $selected_units Selected units.
     *
     * @return self
     */
    public function setSelectedUnits(int $selected_units)
    {
        $this->selected_units = $selected_units;
        return $this;

    }//end setSelectedUnits()


    /**
     * Get Required_units
     *
     * @return integer
     */
    public function getRequiredUnits()
    {
        return $this->required_units;

    }//end getRequiredUnits()


    /**
     * Set Required units
     *
     * @param integer $required_units Required units.
     *
     * @return self
     */
    public function setRequiredUnits(int $required_units)
    {
        $this->required_units = $required_units;
        return $this;

    }//end setRequiredUnits()


    /**
     * Get Available_units
     *
     * @return integer
     */
    public function getAvailableUnits()
    {
        return $this->available_units;

    }//end getAvailableUnits()


    /**
     * Set Available units
     *
     * @param integer $available_units Available units.
     *
     * @return self
     */
    public function setAvailableUnits(int $available_units)
    {
        $this->available_units = $available_units;
        return $this;

    }//end setAvailableUnits()


    /**
     * Get Guests_per_unit
     *
     * @return integer
     */
    public function getGuestsPerUnit()
    {
        return $this->guests_per_unit;

    }//end getGuestsPerUnit()


    /**
     * Set Guests per unit
     *
     * @param integer $guests_per_unit Guests per unit.
     *
     * @return self
     */
    public function setGuestsPerUnit(int $guests_per_unit)
    {
        $this->guests_per_unit = $guests_per_unit;
        return $this;

    }//end setGuestsPerUnit()


    /**
     * Get Property_pricing
     *
     * @return object
     */
    public function getPropertyPricing()
    {
        return (empty($this->property_pricing) === false) ? $this->property_pricing : new \stdClass;

    }//end getPropertyPricing()


    /**
     * Set Property pricing
     *
     * @param array $property_pricing Property pricing.
     *
     * @return self
     */
    public function setPropertyPricing(array $property_pricing)
    {
        $this->property_pricing = $property_pricing;
        return $this;

    }//end setPropertyPricing()


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
     * Get Selected_payment_method
     *
     * @return string
     */
    public function getSelectedPaymentMethod()
    {
        return $this->selected_payment_method;

    }//end getSelectedPaymentMethod()


    /**
     * Set Selected payment method
     *
     * @param string $selected_payment_method Selected payment method.
     *
     * @return self
     */
    public function setSelectedPaymentMethod(string $selected_payment_method)
    {
        $this->selected_payment_method = $selected_payment_method;
        return $this;

    }//end setSelectedPaymentMethod()


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
     * Get Similar_properties
     *
     * @return array
     */
    public function getSimilarProperties()
    {
        return $this->similar_properties;

    }//end getSimilarProperties()


    /**
     * Set Similar properties
     *
     * @param array $similar_properties Similar properties.
     *
     * @return self
     */
    public function setSimilarProperties(array $similar_properties)
    {
        $this->similar_properties = $similar_properties;
        return $this;

    }//end setSimilarProperties()


    /**
     * Get Meta
     *
     * @return object
     */
    public function getMeta()
    {
        return (empty($this->meta) === false) ? $this->meta : new \stdClass;

    }//end getMeta()


    /**
     * Set Meta
     *
     * @param array $meta Meta.
     *
     * @return self
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
        return $this;

    }//end setMeta()


    /**
     * Get Enabled
     *
     * @return integer
     */
    public function getEnabled()
    {
        return $this->enabled;

    }//end getEnabled()


    /**
     * Set Enabled
     *
     * @param integer $enabled Enabled.
     *
     * @return self
     */
    public function setEnabled(int $enabled)
    {
        $this->enabled = $enabled;
        return $this;

    }//end setEnabled()


    /**
     * Get Status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;

    }//end getStatus()


    /**
     * Set Status
     *
     * @param integer $status Status.
     *
     * @return self
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
        return $this;

    }//end setStatus()


    /**
     * Get Prive
     *
     * @return integer
     */
    public function getPrive()
    {
        return $this->prive;

    }//end getPrive()


    /**
     * Set Prive
     *
     * @param integer $prive Prive.
     *
     * @return self
     */
    public function setPrive(int $prive)
    {
        $this->prive = $prive;
        return $this;

    }//end setPrive()


    /**
     * Get Attraction_images
     *
     * @return array
     */
    public function getAttractionImages()
    {
        return $this->attraction_images;

    }//end getAttractionImages()


    /**
     * Set Attraction images
     *
     * @param array $attraction_images Attraction images.
     *
     * @return self
     */
    public function setAttractionImages(array $attraction_images)
    {
        $this->attraction_images = $attraction_images;
        return $this;

    }//end setAttractionImages()


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
