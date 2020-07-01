<?php
/**
 * Response Model for Home Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHomeResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHomeResponse",
 * description="Response Model for Home Api",
 * )
 * // phpcs:enable
 */
class GetHomeResponse extends ApiResponse
{

    /**
     * User Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user",
	 *   type="object",
	 *   default="{}",
	 *   description="User Section",
	 *     @SWG\Property(
	 *       property="wallet",
	 *       type="object",
	 *       default="{}",
	 *       description="Wallet Details",
	 *         @SWG\Property(
	 *           property="wallet_balance",
	 *           type="integer",
	 *           default="0",
	 *           description="User Wallet Balance"
	 *         ),
	 *         @SWG\Property(
	 *           property="wallet_currency_symbol",
	 *           type="object",
	 *           default="{}",
	 *           description="Wallet Currency Symbol",
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
    protected $user = [];

    /**
     * Home Banners Old Structre
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="home_banners",
	 *   type="array",
	 *   default="[]",
	 *   description="Home Banners Old Structre",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="country",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Country",
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
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="Property State"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="Property City"
	 *     ),
	 *     @SWG\Property(
	 *       property="latitude",
	 *       type="string",
	 *       default="",
	 *       description="Property Latitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="longitude",
	 *       type="string",
	 *       default="",
	 *       description="Property Longitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="tag",
	 *       type="string",
	 *       default="",
	 *       description="Property Tag"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="keyword",
	 *       type="string",
	 *       default="",
	 *       description="Keyword"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_type",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Type",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="String Array of Property Type"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="room_type",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Room Type",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="String Array of Room Type"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="amenities",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Amenities",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="String Array of Amenities"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="promo",
	 *       type="string",
	 *       default="",
	 *       description="Promo"
	 *     ),
	 *     @SWG\Property(
	 *       property="utm_source",
	 *       type="string",
	 *       default="",
	 *       description="Utm Source for Tracking"
	 *     ),
	 *     @SWG\Property(
	 *       property="utm_campaign",
	 *       type="string",
	 *       default="",
	 *       description="Utm Campaign for Tracking"
	 *     ),
	 *     @SWG\Property(
	 *       property="utm_medium",
	 *       type="string",
	 *       default="",
	 *       description="Utm Medium for Tracking"
	 *     ),
	 *     @SWG\Property(
	 *       property="heading1",
	 *       type="string",
	 *       default="",
	 *       description="Heading1"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Images Urls Array"
	 *         )
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
    protected $home_banners = [];

    /**
     * Popular Cities for Search
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="popular_cities",
	 *   type="array",
	 *   default="[]",
	 *   description="Popular Cities for Search",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="country",
	 *       type="object",
	 *       default="{}",
	 *       description="Country",
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
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="Popular Cities State"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="City"
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
	 *     ),
	 *     @SWG\Property(
	 *       property="property_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Image Url array"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="tag",
	 *       type="string",
	 *       default="",
	 *       description="Tag"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="home_count",
	 *       type="string",
	 *       default="",
	 *       description="Home Count"
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
    protected $popular_cities = [];

    /**
     * Property Types
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_types",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Types",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="country",
	 *       type="object",
	 *       default="{}",
	 *       description="Country",
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
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="Property State"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="Property City"
	 *     ),
	 *     @SWG\Property(
	 *       property="latitude",
	 *       type="string",
	 *       default="",
	 *       description="Property Latitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="longitude",
	 *       type="string",
	 *       default="",
	 *       description="Property Longitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="heading1",
	 *       type="string",
	 *       default="",
	 *       description="Heading1"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Images Url Array"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="property_type",
	 *       type="string",
	 *       default="",
	 *       description="Property Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_type_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Type Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="tag",
	 *       type="string",
	 *       default="",
	 *       description="Tag"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
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
    protected $property_types = [];

    /**
     * Home Videos
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="home_videos",
	 *   type="array",
	 *   default="[]",
	 *   description="Home Videos",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="url",
	 *       type="string",
	 *       default="",
	 *       description="Video Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="type",
	 *       type="string",
	 *       default="",
	 *       description="Video Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="thumbnail",
	 *       type="string",
	 *       default="",
	 *       description="Video Thumbnail"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $home_videos = [];

    /**
     * Collections Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="collections",
	 *   type="array",
	 *   default="[]",
	 *   description="Collections Data",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="collection_id",
	 *       type="integer",
	 *       default="0",
	 *       description="Collection Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="collection_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Collection Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="collection_title",
	 *       type="string",
	 *       default="",
	 *       description="Collection Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="collection_image",
	 *       type="string",
	 *       default="",
	 *       description="Collection Image Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="properties",
	 *       type="array",
	 *       default="[]",
	 *       description="Properties Section",
	 *       @SWG\Items(
	 *         type="object",
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
	 *           property="property_score",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Score"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_type_name",
	 *           type="string",
	 *           default="",
	 *           description="Property Type Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="room_type_name",
	 *           type="string",
	 *           default="",
	 *           description="Room Type Name"
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
	 *           description="Host Image Url"
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
	 *           property="accomodation",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Accomodation"
	 *         ),
	 *         @SWG\Property(
	 *           property="min_units_required",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Min Units Required"
	 *         ),
	 *         @SWG\Property(
	 *           property="total_accomodation",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Total Accomodation"
	 *         ),
	 *         @SWG\Property(
	 *           property="is_liked_by_user",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Wishlist status of User"
	 *         ),
	 *         @SWG\Property(
	 *           property="prices",
	 *           type="object",
	 *           default="{}",
	 *           description="Property Prices",
	 *             @SWG\Property(
	 *               property="display_discount",
	 *               type="integer",
	 *               default="0",
	 *               description="Display Discount"
	 *             ),
	 *             @SWG\Property(
	 *               property="smart_discount",
	 *               type="object",
	 *               default="{}",
	 *               description="Smart Discount",
	 *                 @SWG\Property(
	 *                   property="header",
	 *                   type="string",
	 *                   default="",
	 *                   description="Header String for show price section"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="discount",
	 *                   type="integer",
	 *                   default="0",
	 *                   description="Discount on Price"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="footer",
	 *                   type="string",
	 *                   default="",
	 *                   description="Footer String for show price section"
	 *                 )
	 *             ),
	 *             @SWG\Property(
	 *               property="final_currency",
	 *               type="object",
	 *               default="{}",
	 *               description="Property Final Currency",
	 *                 @SWG\Property(
	 *                   property="webicon",
	 *                   type="string",
	 *                   default="",
	 *                   description="Currency Webicon"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="non-webicon",
	 *                   type="string",
	 *                   default="",
	 *                   description="Currency Non-webicon"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="iso_code",
	 *                   type="string",
	 *                   default="",
	 *                   description="Currency Iso Code"
	 *                 )
	 *             ),
	 *             @SWG\Property(
	 *               property="price_after_discount",
	 *               type="string",
	 *               default="",
	 *               description="Property Price After Discount"
	 *             ),
	 *             @SWG\Property(
	 *               property="price_after_discount_unformatted",
	 *               type="integer",
	 *               default="0",
	 *               description="Property Price After Discount Unformatted"
	 *             ),
	 *             @SWG\Property(
	 *               property="price_before_discount",
	 *               type="string",
	 *               default="",
	 *               description="Property Price Before Discount"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="payment_methods",
	 *           type="object",
	 *           default="{}",
	 *           description="Property Payment Methods",
	 *             @SWG\Property(
	 *               property="instant_book",
	 *               type="integer",
	 *               default="0",
	 *               description="Instant Bookable status"
	 *             ),
	 *             @SWG\Property(
	 *               property="cash_on_arrival",
	 *               type="integer",
	 *               default="0",
	 *               description="Cash On Arrival applicable status"
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
	 *           property="property_videos_available",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Videos Available status"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_tags",
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
	 *               description="Tags Color in Rgb Format Eg. (210, 123, 134)"
	 *             ),
	 *             @SWG\Property(
	 *               property="colorHex",
	 *               type="string",
	 *               default="",
	 *               description="Tags Color in Hex Format Eg. #FF3F3F"
	 *             ),
	 *             @SWG\Property(
	 *               property="textRgb",
	 *               type="string",
	 *               default="",
	 *               description="Tags Text Color in Rgb Format Eg. (210, 123, 134)"
	 *             ),
	 *             @SWG\Property(
	 *               property="textHex",
	 *               type="string",
	 *               default="",
	 *               description="Tags Text Color in Hex Format Eg. #f3f3f3"
	 *             )
	 *           )
	 *         ),
	 *         @SWG\Property(
	 *           property="url",
	 *           type="string",
	 *           default="",
	 *           description="Property Url"
	 *         )
	 *       )
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $collections = [];

    /**
     * Recently Viewed Properties Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="recently_viewed_properties",
	 *   type="array",
	 *   default="[]",
	 *   description="Recently Viewed Properties Section",
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
	 *       type="string",
	 *       default="",
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
	 *       description="Room Type Name"
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
	 *       description="Property Location",
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
	 *       description="Property Is Wishlisted By User"
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
	 *               description="Header Price"
	 *             ),
	 *             @SWG\Property(
	 *               property="discount",
	 *               type="integer",
	 *               default="0",
	 *               description="Discount Amount"
	 *             ),
	 *             @SWG\Property(
	 *               property="footer",
	 *               type="string",
	 *               default="",
	 *               description="Footer Price"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="final_currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Final Currency",
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
	 *           description="Instant Bookable status"
	 *         ),
	 *         @SWG\Property(
	 *           property="cash_on_arrival",
	 *           type="integer",
	 *           default="0",
	 *           description="Cash On Arrival applicable status"
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
	 *           description="Image Url string"
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
	 *       property="property_videos_available",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Videos Available Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_tags",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Tags",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Tag Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="class",
	 *           type="string",
	 *           default="",
	 *           description="Tag Class"
	 *         ),
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Tag Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="colorRgb",
	 *           type="string",
	 *           default="",
	 *           description="Tag Color Code in Rgb Format Eg. (201, 123, 132)"
	 *         ),
	 *         @SWG\Property(
	 *           property="colorHex",
	 *           type="string",
	 *           default="",
	 *           description="Tag Color Code in Hex Format Eg. #FF3F3F"
	 *         ),
	 *         @SWG\Property(
	 *           property="textRgb",
	 *           type="string",
	 *           default="",
	 *           description="Tag Text Color code in Rgb"
	 *         ),
	 *         @SWG\Property(
	 *           property="textHex",
	 *           type="string",
	 *           default="",
	 *           description="Tag Text Color code in Hex"
	 *         )
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
    protected $recently_viewed_properties = [];

    /**
     * New And Approved Booking Requests Count For Bottom Menu Bar
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="new_and_approved_booking_requests_count",
	 *   type="integer",
	 *   default="0",
	 *   description="New And Approved Booking Requests Count For Bottom Menu Bar"
	 * )
     * // phpcs:enable
     */
    protected $new_and_approved_booking_requests_count = 0;

    /**
     * Home Explore Content Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="home_explore_content",
	 *   type="object",
	 *   default="{}",
	 *   description="Home Explore Content Data",
	 *     @SWG\Property(
	 *       property="heading",
	 *       type="string",
	 *       default="",
	 *       description="Home Explore Content Heading"
	 *     ),
	 *     @SWG\Property(
	 *       property="cities_sub_heading",
	 *       type="string",
	 *       default="",
	 *       description="Home Explore Content Cities Sub Heading"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $home_explore_content = [];

    /**
     * Property App Version Check
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="app_version_check",
	 *   type="object",
	 *   default="{}",
	 *   description="Property App Version Check",
	 *     @SWG\Property(
	 *       property="required",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Required"
	 *     ),
	 *     @SWG\Property(
	 *       property="min_version",
	 *       type="string",
	 *       default="",
	 *       description="Property Min Version"
	 *     ),
	 *     @SWG\Property(
	 *       property="latest_version",
	 *       type="string",
	 *       default="",
	 *       description="Property Latest Version"
	 *     ),
	 *     @SWG\Property(
	 *       property="text",
	 *       type="string",
	 *       default="",
	 *       description="Property Text"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $app_version_check = [];

    /**
     * Property New Rating Days
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="new_rating_days",
	 *   type="integer",
	 *   default="0",
	 *   description="Property New Rating Days"
	 * )
     * // phpcs:enable
     */
    protected $new_rating_days = 0;

    /**
     * Property Old Rating Days
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="old_rating_days",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Old Rating Days"
	 * )
     * // phpcs:enable
     */
    protected $old_rating_days = 0;

    /**
     * Home Banner Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="home_banner",
	 *   type="array",
	 *   default="[]",
	 *   description="Home Banner Section",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Banner Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="notification_type",
	 *       type="integer",
	 *       default="0",
	 *       description="Banner Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="location",
	 *       type="string",
	 *       default="",
	 *       description="Location"
	 *     ),
	 *     @SWG\Property(
	 *       property="country",
	 *       type="string",
	 *       default="",
	 *       description="Country"
	 *     ),
	 *     @SWG\Property(
	 *       property="country_name",
	 *       type="string",
	 *       default="",
	 *       description="Country Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="State"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="City"
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
	 *     ),
	 *     @SWG\Property(
	 *       property="search_keyword",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Search Keyword",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Search Keyword Array of String"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="check_in",
	 *       type="string",
	 *       default="",
	 *       description="Property Check In Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="check_out",
	 *       type="string",
	 *       default="",
	 *       description="Property Check Out Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Guests"
	 *     ),
	 *     @SWG\Property(
	 *       property="min_budget",
	 *       type="string",
	 *       default="",
	 *       description="Property Min Budget"
	 *     ),
	 *     @SWG\Property(
	 *       property="max_budget",
	 *       type="string",
	 *       default="",
	 *       description="Property Max Budget"
	 *     ),
	 *     @SWG\Property(
	 *       property="currency",
	 *       type="object",
	 *       default="{}",
	 *       description="Currency",
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
	 *       property="property_type",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Type",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Property Type Array of String"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="room_type",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Room Type",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Room Type Array Of String"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="amenities",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Amenities",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Amenities Array of String"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="bedroom",
	 *       type="string",
	 *       default="",
	 *       description="Bedroom Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="tag",
	 *       type="string",
	 *       default="",
	 *       description="Tag"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="keyword",
	 *       type="string",
	 *       default="",
	 *       description="Keyword"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="promo",
	 *       type="string",
	 *       default="",
	 *       description="Promo"
	 *     ),
	 *     @SWG\Property(
	 *       property="utm_source",
	 *       type="string",
	 *       default="",
	 *       description="Utm Source for Tracking"
	 *     ),
	 *     @SWG\Property(
	 *       property="utm_campaign",
	 *       type="string",
	 *       default="",
	 *       description="Utm Campaign for Tracking"
	 *     ),
	 *     @SWG\Property(
	 *       property="utm_medium",
	 *       type="string",
	 *       default="",
	 *       description="Utm Medium for Tracking"
	 *     ),
	 *     @SWG\Property(
	 *       property="heading",
	 *       type="string",
	 *       default="",
	 *       description="Heading"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Images Url Array of String"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="content",
	 *       type="string",
	 *       default="",
	 *       description="Banner Content"
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
    protected $home_banner = [];

    /**
     * Chat Available Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="chat_available",
	 *   type="integer",
	 *   default="0",
	 *   description="Chat Available Status"
	 * )
     * // phpcs:enable
     */
    protected $chat_available = 0;

    /**
     * Chat Call Text
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="chat_call_text",
	 *   type="string",
	 *   default="",
	 *   description="Chat Call Text"
	 * )
     * // phpcs:enable
     */
    protected $chat_call_text = '';

    /**
     * Active Request Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="active_request_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Active Request Count"
	 * )
     * // phpcs:enable
     */
    protected $active_request_count = 0;

    /**
     * Offers Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="offer",
	 *   type="object",
	 *   default="{}",
	 *   description="Offers Data",
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Offer Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="desc",
	 *       type="array",
	 *       default="[]",
	 *       description="Offer Desc",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Offer Desc Array"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="img_url",
	 *       type="string",
	 *       default="",
	 *       description="Offer Image Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="meta",
	 *       type="object",
	 *       default="{}",
	 *       description="Offer Metadata",
	 *         @SWG\Property(
	 *           property="canonical_url",
	 *           type="string",
	 *           default="",
	 *           description="Metadata Canonical Url"
	 *         ),
	 *         @SWG\Property(
	 *           property="meta_title",
	 *           type="string",
	 *           default="",
	 *           description="Metadata Title"
	 *         ),
	 *         @SWG\Property(
	 *           property="keyword",
	 *           type="string",
	 *           default="",
	 *           description="Metadata Keyword"
	 *         ),
	 *         @SWG\Property(
	 *           property="meta_desc",
	 *           type="string",
	 *           default="",
	 *           description="Metadata description"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $offer = [];

    /**
     * Home Metadata
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="meta",
	 *   type="object",
	 *   default="{}",
	 *   description="Home Metadata",
	 *     @SWG\Property(
	 *       property="canonical_url",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Canonical Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="meta_title",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="keyword",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Keyword"
	 *     ),
	 *     @SWG\Property(
	 *       property="meta_desc",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Desc"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $meta = [];


    /**
     * Get User
     *
     * @return object
     */
    public function getUser()
    {
        return (empty($this->user) === false) ? $this->user : new \stdClass;

    }//end getUser()


    /**
     * Set User
     *
     * @param array $user User.
     *
     * @return self
     */
    public function setUser(array $user)
    {
        $this->user = $user;
        return $this;

    }//end setUser()


    /**
     * Get Home_banners
     *
     * @return array
     */
    public function getHomeBanners()
    {
        return $this->home_banners;

    }//end getHomeBanners()


    /**
     * Set Home banners
     *
     * @param array $home_banners Home banners.
     *
     * @return self
     */
    public function setHomeBanners(array $home_banners)
    {
        $this->home_banners = $home_banners;
        return $this;

    }//end setHomeBanners()


    /**
     * Get Popular_cities
     *
     * @return array
     */
    public function getPopularCities()
    {
        return $this->popular_cities;

    }//end getPopularCities()


    /**
     * Set Popular cities
     *
     * @param array $popular_cities Popular cities.
     *
     * @return self
     */
    public function setPopularCities(array $popular_cities)
    {
        $this->popular_cities = $popular_cities;
        return $this;

    }//end setPopularCities()


    /**
     * Get Property_types
     *
     * @return array
     */
    public function getPropertyTypes()
    {
        return $this->property_types;

    }//end getPropertyTypes()


    /**
     * Set Property types
     *
     * @param array $property_types Property types.
     *
     * @return self
     */
    public function setPropertyTypes(array $property_types)
    {
        $this->property_types = $property_types;
        return $this;

    }//end setPropertyTypes()


    /**
     * Get Home_videos
     *
     * @return array
     */
    public function getHomeVideos()
    {
        return $this->home_videos;

    }//end getHomeVideos()


    /**
     * Set Home videos
     *
     * @param array $home_videos Home videos.
     *
     * @return self
     */
    public function setHomeVideos(array $home_videos)
    {
        $this->home_videos = $home_videos;
        return $this;

    }//end setHomeVideos()


    /**
     * Get Collections
     *
     * @return array
     */
    public function getCollections()
    {
        return $this->collections;

    }//end getCollections()


    /**
     * Set Collections
     *
     * @param array $collections Collections.
     *
     * @return self
     */
    public function setCollections(array $collections)
    {
        $this->collections = $collections;
        return $this;

    }//end setCollections()


    /**
     * Get Recently_viewed_properties
     *
     * @return array
     */
    public function getRecentlyViewedProperties()
    {
        return $this->recently_viewed_properties;

    }//end getRecentlyViewedProperties()


    /**
     * Set Recently viewed properties
     *
     * @param array $recently_viewed_properties Recently viewed properties.
     *
     * @return self
     */
    public function setRecentlyViewedProperties(array $recently_viewed_properties)
    {
        $this->recently_viewed_properties = $recently_viewed_properties;
        return $this;

    }//end setRecentlyViewedProperties()


    /**
     * Get New_and_approved_booking_requests_count
     *
     * @return integer
     */
    public function getNewAndApprovedBookingRequestsCount()
    {
        return $this->new_and_approved_booking_requests_count;

    }//end getNewAndApprovedBookingRequestsCount()


    /**
     * Set New and approved booking requests count
     *
     * @param integer $new_and_approved_booking_requests_count New and approved booking requests count.
     *
     * @return self
     */
    public function setNewAndApprovedBookingRequestsCount(int $new_and_approved_booking_requests_count)
    {
        $this->new_and_approved_booking_requests_count = $new_and_approved_booking_requests_count;
        return $this;

    }//end setNewAndApprovedBookingRequestsCount()


    /**
     * Get Home_explore_content
     *
     * @return object
     */
    public function getHomeExploreContent()
    {
        return (empty($this->home_explore_content) === false) ? $this->home_explore_content : new \stdClass;

    }//end getHomeExploreContent()


    /**
     * Set Home explore content
     *
     * @param array $home_explore_content Home explore content.
     *
     * @return self
     */
    public function setHomeExploreContent(array $home_explore_content)
    {
        $this->home_explore_content = $home_explore_content;
        return $this;

    }//end setHomeExploreContent()


    /**
     * Get App_version_check
     *
     * @return object
     */
    public function getAppVersionCheck()
    {
        return (empty($this->app_version_check) === false) ? $this->app_version_check : new \stdClass;

    }//end getAppVersionCheck()


    /**
     * Set App version check
     *
     * @param array $app_version_check App version check.
     *
     * @return self
     */
    public function setAppVersionCheck(array $app_version_check)
    {
        $this->app_version_check = $app_version_check;
        return $this;

    }//end setAppVersionCheck()


    /**
     * Get New_rating_days
     *
     * @return integer
     */
    public function getNewRatingDays()
    {
        return $this->new_rating_days;

    }//end getNewRatingDays()


    /**
     * Set New rating days
     *
     * @param integer $new_rating_days New rating days.
     *
     * @return self
     */
    public function setNewRatingDays(int $new_rating_days)
    {
        $this->new_rating_days = $new_rating_days;
        return $this;

    }//end setNewRatingDays()


    /**
     * Get Old_rating_days
     *
     * @return integer
     */
    public function getOldRatingDays()
    {
        return $this->old_rating_days;

    }//end getOldRatingDays()


    /**
     * Set Old rating days
     *
     * @param integer $old_rating_days Old rating days.
     *
     * @return self
     */
    public function setOldRatingDays(int $old_rating_days)
    {
        $this->old_rating_days = $old_rating_days;
        return $this;

    }//end setOldRatingDays()


    /**
     * Get Home_banner
     *
     * @return array
     */
    public function getHomeBanner()
    {
        return $this->home_banner;

    }//end getHomeBanner()


    /**
     * Set Home banner
     *
     * @param array $home_banner Home banner.
     *
     * @return self
     */
    public function setHomeBanner(array $home_banner)
    {
        $this->home_banner = $home_banner;
        return $this;

    }//end setHomeBanner()


    /**
     * Get Chat_available
     *
     * @return integer
     */
    public function getChatAvailable()
    {
        return $this->chat_available;

    }//end getChatAvailable()


    /**
     * Set Chat available
     *
     * @param integer $chat_available Chat available.
     *
     * @return self
     */
    public function setChatAvailable(int $chat_available)
    {
        $this->chat_available = $chat_available;
        return $this;

    }//end setChatAvailable()


    /**
     * Get Chat_call_text
     *
     * @return string
     */
    public function getChatCallText()
    {
        return $this->chat_call_text;

    }//end getChatCallText()


    /**
     * Set Chat call text
     *
     * @param string $chat_call_text Chat call text.
     *
     * @return self
     */
    public function setChatCallText(string $chat_call_text)
    {
        $this->chat_call_text = $chat_call_text;
        return $this;

    }//end setChatCallText()


    /**
     * Get Active_request_count
     *
     * @return integer
     */
    public function getActiveRequestCount()
    {
        return $this->active_request_count;

    }//end getActiveRequestCount()


    /**
     * Set Active request count
     *
     * @param integer $active_request_count Active request count.
     *
     * @return self
     */
    public function setActiveRequestCount(int $active_request_count)
    {
        $this->active_request_count = $active_request_count;
        return $this;

    }//end setActiveRequestCount()


    /**
     * Get Offer
     *
     * @return object
     */
    public function getOffer()
    {
        return (empty($this->offer) === false) ? $this->offer : new \stdClass;

    }//end getOffer()


    /**
     * Set Offer
     *
     * @param array $offer Offer.
     *
     * @return self
     */
    public function setOffer(array $offer)
    {
        $this->offer = $offer;
        return $this;

    }//end setOffer()


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


}//end class
