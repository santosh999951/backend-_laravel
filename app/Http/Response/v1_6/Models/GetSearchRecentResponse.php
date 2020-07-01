<?php
/**
 * Response Model for Recent Search Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetSearchRecentResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetSearchRecentResponse",
 * description="Response Model for Recent Search Api",
 * )
 * // phpcs:enable
 */
class GetSearchRecentResponse extends ApiResponse
{

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
	 *           description="Display Discount Value"
	 *         ),
	 *         @SWG\Property(
	 *           property="smart_discount",
	 *           type="object",
	 *           default="{}",
	 *           description="Smart Discount Section",
	 *             @SWG\Property(
	 *               property="header",
	 *               type="string",
	 *               default="",
	 *               description="Header Value"
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
	 *               description="Footer Value"
	 *             )
	 *         ),
	 *         @SWG\Property(
	 *           property="final_currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Final Currency Section for Property",
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
	 *           description="Cash On Arrival Applicable status"
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
	 *       description="Property Videos Available status"
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
	 *           description="Tag Color code in RGB Format"
	 *         ),
	 *         @SWG\Property(
	 *           property="colorHex",
	 *           type="string",
	 *           default="",
	 *           description="Tag Color Code in Hex Format"
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
    protected $recently_viewed_properties = [];


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


}//end class
