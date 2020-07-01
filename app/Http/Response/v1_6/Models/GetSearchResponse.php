<?php
/**
 * Response Model for Search Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetSearchResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetSearchResponse",
 * description="Response Model for Search Api",
 * )
 * // phpcs:enable
 */
class GetSearchResponse extends ApiResponse
{

    /**
     * Search Filters
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="filters",
	 *   type="object",
	 *   default="{}",
	 *   description="Search Filters",
	 *     @SWG\Property(
	 *       property="min_budget",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Min Budget"
	 *     ),
	 *     @SWG\Property(
	 *       property="max_budget",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Max Budget"
	 *     ),
	 *     @SWG\Property(
	 *       property="slider_min_value",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Slider Min Value"
	 *     ),
	 *     @SWG\Property(
	 *       property="slider_max_value",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Slider Max Value"
	 *     ),
	 *     @SWG\Property(
	 *       property="budget_currency",
	 *       type="object",
	 *       default="{}",
	 *       description="Budget Currency",
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
	 *       property="property_types",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Types",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Types Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Property Types Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="link",
	 *           type="string",
	 *           default="",
	 *           description="Property Types Link"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Types Show status"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Types Selected Sataus"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="location_tags",
	 *       type="array",
	 *       default="[]",
	 *       description="Location Tags",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Location Tags array of string"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="search_location",
	 *       type="array",
	 *       default="[]",
	 *       description="Search Location",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Search Location Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="tag",
	 *           type="string",
	 *           default="",
	 *           description="Search Location Tag"
	 *         ),
	 *         @SWG\Property(
	 *           property="link",
	 *           type="string",
	 *           default="",
	 *           description="Search Location Link"
	 *         ),
	 *         @SWG\Property(
	 *           property="show",
	 *           type="integer",
	 *           default="0",
	 *           description="Search Location Show Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Search Location Selected Status"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="popular_similar_locations",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Popular Similar Locations",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Location Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="tag",
	 *           type="string",
	 *           default="",
	 *           description="Location Tag"
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
	 *           property="id",
	 *           type="integer",
	 *           default="0",
	 *           description="Amenities Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="amenity_name",
	 *           type="string",
	 *           default="",
	 *           description="Amenity Name"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="checkin",
	 *       type="string",
	 *       default="",
	 *       description="Property Checkin Date Eg. 2018-11-28"
	 *     ),
	 *     @SWG\Property(
	 *       property="checkout",
	 *       type="string",
	 *       default="",
	 *       description="Property Checkout Date Eg. 2018-11-28"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Guests Count"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $filters = [];

    /**
     * Properties List Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="properties_list",
	 *   type="array",
	 *   default="[]",
	 *   description="Properties List Section",
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
	 *       description="Property Is Liked By User"
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
	 *           description="Display Discount Value"
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
	 *               description="Header Text of Smart Discount"
	 *             ),
	 *             @SWG\Property(
	 *               property="discount",
	 *               type="integer",
	 *               default="0",
	 *               description="Discount Value"
	 *             ),
	 *             @SWG\Property(
	 *               property="footer",
	 *               type="string",
	 *               default="",
	 *               description="Footer Text of Smart Discount"
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
	 *           description="Instant Bookable Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="cash_on_arrival",
	 *           type="integer",
	 *           default="0",
	 *           description="Cash On Arrival Status"
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
	 *           description="Tag Color Code in Rgb Format Eg. (255,239,140)"
	 *         ),
	 *         @SWG\Property(
	 *           property="colorHex",
	 *           type="string",
	 *           default="",
	 *           description="Tag Color Code in Hex Format Eg. #F3F3F3"
	 *         ),
	 *         @SWG\Property(
	 *           property="textRgb",
	 *           type="string",
	 *           default="",
	 *           description="Tag Text Color Code in Rgb Format"
	 *         ),
	 *         @SWG\Property(
	 *           property="textHex",
	 *           type="string",
	 *           default="",
	 *           description="Tag Text Color Code in Hex Format"
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
    protected $properties_list = [];

    /**
     * Total Properties Count In Search List
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total_properties_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Total Properties Count In Search List"
	 * )
     * // phpcs:enable
     */
    protected $total_properties_count = 0;

    /**
     * Search Address Data
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="search_address_data",
	 *   type="object",
	 *   default="{}",
	 *   description="Search Address Data",
	 *     @SWG\Property(
	 *       property="area",
	 *       type="string",
	 *       default="",
	 *       description="Area"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="City"
	 *     ),
	 *     @SWG\Property(
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="State"
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
	 *       property="lat",
	 *       type="string",
	 *       default="",
	 *       description="Latitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="lng",
	 *       type="string",
	 *       default="",
	 *       description="Longitude"
	 *     ),
	 *     @SWG\Property(
	 *       property="search_keyword",
	 *       type="array",
	 *       default="[]",
	 *       description="Search Keyword",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Array of String of Search Keyword"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="location",
	 *       type="string",
	 *       default="",
	 *       description="Property Location"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $search_address_data = [];

    /**
     * Filter Cards In Properties
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="filter_cards_in_properties",
	 *   type="object",
	 *   default="{}",
	 *   description="Filter Cards In Properties",
	 *     @SWG\Property(
	 *       property="filter_card_type",
	 *       type="string",
	 *       default="",
	 *       description="Filter Card Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="filter_card_repetition",
	 *       type="array",
	 *       default="[]",
	 *       description="Filter Card Repetition",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="integer",
	 *           default="0",
	 *           description="Repetition of Filter Card Array of integer"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $filter_cards_in_properties = [];

    /**
     * Promo Banners
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="promo_banners",
	 *   type="object",
	 *   default="{}",
	 *   description="Promo Banners",
	 *     @SWG\Property(
	 *       property="mobile_url",
	 *       type="string",
	 *       default="",
	 *       description="Promo Banner Mobile Url For Stay Pages"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $promo_banners = [];

    /**
     * Seo Content For Stay Pages
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="seo_content",
	 *   type="object",
	 *   default="{}",
	 *   description="Seo Content For Stay Pages",
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Seo Content Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="description",
	 *       type="string",
	 *       default="",
	 *       description="Seo Content Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="details",
	 *       type="string",
	 *       default="",
	 *       description="Seo Content Details"
	 *     ),
	 *     @SWG\Property(
	 *       property="meta_title",
	 *       type="string",
	 *       default="",
	 *       description="Seo Content Meta Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="meta_description",
	 *       type="string",
	 *       default="",
	 *       description="Seo Content Meta Description"
	 *     ),
	 *     @SWG\Property(
	 *       property="show",
	 *       type="boolean",
	 *       default="false",
	 *       description="Seo Content Show Status"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $seo_content = [];

    /**
     * Search Metadata
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="meta",
	 *   type="object",
	 *   default="{}",
	 *   description="Search Metadata",
	 *     @SWG\Property(
	 *       property="meta_title",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="meta_url",
	 *       type="string",
	 *       default="",
	 *       description="Metadata Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="title_prefix",
	 *       type="string",
	 *       default="",
	 *       description="Title Prefix"
	 *     ),
	 *     @SWG\Property(
	 *       property="keyword",
	 *       type="string",
	 *       default="",
	 *       description="Keyword"
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
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $meta = [];


    /**
     * Get Filters
     *
     * @return object
     */
    public function getFilters()
    {
        return (empty($this->filters) === false) ? $this->filters : new \stdClass;

    }//end getFilters()


    /**
     * Set Filters
     *
     * @param array $filters Filters.
     *
     * @return self
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
        return $this;

    }//end setFilters()


    /**
     * Get Properties_list
     *
     * @return array
     */
    public function getPropertiesList()
    {
        return $this->properties_list;

    }//end getPropertiesList()


    /**
     * Set Properties list
     *
     * @param array $properties_list Properties list.
     *
     * @return self
     */
    public function setPropertiesList(array $properties_list)
    {
        $this->properties_list = $properties_list;
        return $this;

    }//end setPropertiesList()


    /**
     * Get Total_properties_count
     *
     * @return integer
     */
    public function getTotalPropertiesCount()
    {
        return $this->total_properties_count;

    }//end getTotalPropertiesCount()


    /**
     * Set Total properties count
     *
     * @param integer $total_properties_count Total properties count.
     *
     * @return self
     */
    public function setTotalPropertiesCount(int $total_properties_count)
    {
        $this->total_properties_count = $total_properties_count;
        return $this;

    }//end setTotalPropertiesCount()


    /**
     * Get Search_address_data
     *
     * @return object
     */
    public function getSearchAddressData()
    {
        return (empty($this->search_address_data) === false) ? $this->search_address_data : new \stdClass;

    }//end getSearchAddressData()


    /**
     * Set Search address data
     *
     * @param array $search_address_data Search address data.
     *
     * @return self
     */
    public function setSearchAddressData(array $search_address_data)
    {
        $this->search_address_data = $search_address_data;
        return $this;

    }//end setSearchAddressData()


    /**
     * Get Filter_cards_in_properties
     *
     * @return object
     */
    public function getFilterCardsInProperties()
    {
        return (empty($this->filter_cards_in_properties) === false) ? $this->filter_cards_in_properties : new \stdClass;

    }//end getFilterCardsInProperties()


    /**
     * Set Filter cards in properties
     *
     * @param array $filter_cards_in_properties Filter cards in properties.
     *
     * @return self
     */
    public function setFilterCardsInProperties(array $filter_cards_in_properties)
    {
        $this->filter_cards_in_properties = $filter_cards_in_properties;
        return $this;

    }//end setFilterCardsInProperties()


    /**
     * Get Promo_banners
     *
     * @return object
     */
    public function getPromoBanners()
    {
        return (empty($this->promo_banners) === false) ? $this->promo_banners : new \stdClass;

    }//end getPromoBanners()


    /**
     * Set Promo banners
     *
     * @param array $promo_banners Promo banners.
     *
     * @return self
     */
    public function setPromoBanners(array $promo_banners)
    {
        $this->promo_banners = $promo_banners;
        return $this;

    }//end setPromoBanners()


    /**
     * Get Seo_content
     *
     * @return object
     */
    public function getSeoContent()
    {
        return (empty($this->seo_content) === false) ? $this->seo_content : new \stdClass;

    }//end getSeoContent()


    /**
     * Set Seo content
     *
     * @param array $seo_content Seo content.
     *
     * @return self
     */
    public function setSeoContent(array $seo_content)
    {
        $this->seo_content = $seo_content;
        return $this;

    }//end setSeoContent()


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
