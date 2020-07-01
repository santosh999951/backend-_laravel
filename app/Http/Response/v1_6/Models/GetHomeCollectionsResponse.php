<?php
/**
 * Response Model for Home Collections List Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHomeCollectionsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHomeCollectionsResponse",
 * description="Response Model for Home Collections List Api",
 * )
 * // phpcs:enable
 */
class GetHomeCollectionsResponse extends ApiResponse
{

    /**
     * Collections Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="collections",
	 *   type="array",
	 *   default="[]",
	 *   description="Collections Section",
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
	 *       description="Collection Image"
	 *     ),
	 *     @SWG\Property(
	 *       property="properties",
	 *       type="array",
	 *       default="[]",
	 *       description="Collection Properties",
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
	 *           type="string",
	 *           default="",
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
	 *               description="Property State"
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
	 *           description="Property Is Wishlisted By User"
	 *         ),
	 *         @SWG\Property(
	 *           property="prices",
	 *           type="object",
	 *           default="{}",
	 *           description="Prices",
	 *             @SWG\Property(
	 *               property="display_discount",
	 *               type="integer",
	 *               default="0",
	 *               description="Display Discount Value"
	 *             ),
	 *             @SWG\Property(
	 *               property="smart_discount",
	 *               type="object",
	 *               default="{}",
	 *               description="Smart Discount Section",
	 *                 @SWG\Property(
	 *                   property="header",
	 *                   type="string",
	 *                   default="",
	 *                   description="Header Text"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="discount",
	 *                   type="integer",
	 *                   default="0",
	 *                   description="Discount Value"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="footer",
	 *                   type="string",
	 *                   default="",
	 *                   description="Footer Text"
	 *                 )
	 *             ),
	 *             @SWG\Property(
	 *               property="final_currency",
	 *               type="object",
	 *               default="{}",
	 *               description="Final Currency",
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
	 *               description="Instant Bookable Status"
	 *             ),
	 *             @SWG\Property(
	 *               property="cash_on_arrival",
	 *               type="integer",
	 *               default="0",
	 *               description="Cash On Arrival Status"
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
	 *           property="property_videos_available",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Videos Available Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_tags",
	 *           type="array",
	 *           default="[]",
	 *           description="Property Tags",
	 *           @SWG\Items(
	 *             type="object",
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
     * Home Explore Content
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="home_explore_content",
	 *   type="object",
	 *   default="{}",
	 *   description="Home Explore Content",
	 *     @SWG\Property(
	 *       property="heading",
	 *       type="string",
	 *       default="",
	 *       description="Heading"
	 *     ),
	 *     @SWG\Property(
	 *       property="cities_sub_heading",
	 *       type="string",
	 *       default="",
	 *       description="Cities Sub Heading"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $home_explore_content = [];

    /**
     * Collection Metadata
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="meta",
	 *   type="object",
	 *   default="{}",
	 *   description="Collection Metadata",
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
	 *       description="Metadata Description"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $meta = [];


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
