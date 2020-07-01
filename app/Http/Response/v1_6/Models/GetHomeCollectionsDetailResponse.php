<?php
/**
 * Response Model for Home Collections Detail Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHomeCollectionsDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHomeCollectionsDetailResponse",
 * description="Response Model for Home Collections Detail Api",
 * )
 * // phpcs:enable
 */
class GetHomeCollectionsDetailResponse extends ApiResponse
{

    /**
     * Collection Data with Collection Properties
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="collection",
	 *   type="object",
	 *   default="{}",
	 *   description="Collection Data with Collection Properties",
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
	 *           type="integer",
	 *           default="0",
	 *           description="Property Property Score"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_type_name",
	 *           type="string",
	 *           default="",
	 *           description="Property Property Type Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="room_type_name",
	 *           type="string",
	 *           default="",
	 *           description="Property Room Type Name"
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
	 *                   description="Property Name"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="ccode",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Ccode"
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
	 *               description="Property Latitude"
	 *             ),
	 *             @SWG\Property(
	 *               property="longitude",
	 *               type="string",
	 *               default="",
	 *               description="Property Longitude"
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
	 *           description="Property Is Liked By User"
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
	 *               description="Property Display Discount"
	 *             ),
	 *             @SWG\Property(
	 *               property="smart_discount",
	 *               type="object",
	 *               default="{}",
	 *               description="Property Smart Discount",
	 *                 @SWG\Property(
	 *                   property="header",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Header"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="discount",
	 *                   type="integer",
	 *                   default="0",
	 *                   description="Property Discount"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="footer",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Footer"
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
	 *                   description="Property Webicon"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="non-webicon",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Non-webicon"
	 *                 ),
	 *                 @SWG\Property(
	 *                   property="iso_code",
	 *                   type="string",
	 *                   default="",
	 *                   description="Property Iso Code"
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
	 *           description="Property Videos Available"
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
	 *               description="Tag Id"
	 *             ),
	 *             @SWG\Property(
	 *               property="class",
	 *               type="string",
	 *               default="",
	 *               description="Tag Class"
	 *             ),
	 *             @SWG\Property(
	 *               property="text",
	 *               type="string",
	 *               default="",
	 *               description="Tag Text"
	 *             ),
	 *             @SWG\Property(
	 *               property="colorRgb",
	 *               type="string",
	 *               default="",
	 *               description="Tag Color Code in Rgb"
	 *             ),
	 *             @SWG\Property(
	 *               property="colorHex",
	 *               type="string",
	 *               default="",
	 *               description="Tag Color Code in Hex"
	 *             ),
	 *             @SWG\Property(
	 *               property="textRgb",
	 *               type="string",
	 *               default="",
	 *               description="Tag Text Color Code in Rgb"
	 *             ),
	 *             @SWG\Property(
	 *               property="textHex",
	 *               type="string",
	 *               default="",
	 *               description="Tag Text Color Code in Hex"
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
	 * )
     * // phpcs:enable
     */
    protected $collection = [];

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
     * Get Collection
     *
     * @return object
     */
    public function getCollection()
    {
        return (empty($this->collection) === false) ? $this->collection : new \stdClass;

    }//end getCollection()


    /**
     * Set Collection
     *
     * @param array $collection Collection.
     *
     * @return self
     */
    public function setCollection(array $collection)
    {
        $this->collection = $collection;
        return $this;

    }//end setCollection()


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
