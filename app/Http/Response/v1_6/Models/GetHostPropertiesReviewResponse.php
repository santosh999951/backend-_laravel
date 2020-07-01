<?php
/**
 * GetHostPropertiesReviewResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostPropertiesReviewResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostPropertiesReviewResponse",
 * description="GetHostPropertiesReviewResponse",
 * )
 * // phpcs:enable
 */
class GetHostPropertiesReviewResponse extends ApiResponse
{

    /**
     * Reviews List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="reviews",
	 *   type="array",
	 *   default="[]",
	 *   description="Reviews List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="request_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Request Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="guests",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Guests"
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
	 *       property="property_rating",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Rating"
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_id",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_name",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="review_date",
	 *       type="string",
	 *       default="",
	 *       description="Review Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="comment",
	 *       type="string",
	 *       default="",
	 *       description="Comment by Traveller"
	 *     ),
	 *     @SWG\Property(
	 *       property="reply",
	 *       type="string",
	 *       default="",
	 *       description="Reply by Host"
	 *     ),
	 *     @SWG\Property(
	 *       property="nights",
	 *       type="integer",
	 *       default="0",
	 *       description="Nights stay"
	 *     ),
	 *     @SWG\Property(
	 *       property="review_images",
	 *       type="array",
	 *       default="[]",
	 *       description="Review Images",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="value",
	 *           type="string",
	 *           default="",
	 *           description="Image Url"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_image",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Image"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_tile",
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
	 *           description="Room Type"
	 *         ),
	 *         @SWG\Property(
	 *           property="property_score",
	 *           type="integer",
	 *           default="0",
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
	 *           description="Property Images Data",
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
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $reviews = [];

    /**
     * Updated Offset
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="updated_offset",
	 *   type="integer",
	 *   default="0",
	 *   description="Updated Offset"
	 * )
     * // phpcs:enable
     */
    protected $updated_offset = 0;

    /**
     * Limit
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="limit",
	 *   type="string",
	 *   default="",
	 *   description="Limit"
	 * )
     * // phpcs:enable
     */
    protected $limit = '';


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
     * Get Updated_offset
     *
     * @return integer
     */
    public function getUpdatedOffset()
    {
        return $this->updated_offset;

    }//end getUpdatedOffset()


    /**
     * Set Updated offset
     *
     * @param integer $updated_offset Updated offset.
     *
     * @return self
     */
    public function setUpdatedOffset(int $updated_offset)
    {
        $this->updated_offset = $updated_offset;
        return $this;

    }//end setUpdatedOffset()


    /**
     * Get Limit
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->limit;

    }//end getLimit()


    /**
     * Set Limit
     *
     * @param string $limit Limit.
     *
     * @return self
     */
    public function setLimit(string $limit)
    {
        $this->limit = $limit;
        return $this;

    }//end setLimit()


}//end class
