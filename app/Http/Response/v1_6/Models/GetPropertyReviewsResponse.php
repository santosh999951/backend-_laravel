<?php
/**
 * Response Model for Property Reviews List Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPropertyReviewsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPropertyReviewsResponse",
 * description="Response Model for Property Reviews List Api",
 * )
 * // phpcs:enable
 */
class GetPropertyReviewsResponse extends ApiResponse
{

    /**
     * Reviews Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="reviews",
	 *   type="array",
	 *   default="[]",
	 *   description="Reviews Section",
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
	 *       description="Property Comment given in review"
	 *     ),
	 *     @SWG\Property(
	 *       property="nights",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Nights Stay"
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
	 *           description="Image Url Array of string"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="traveller_image",
	 *       type="string",
	 *       default="",
	 *       description="Traveller Image Url"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $reviews = [];

    /**
     * Updated Offset for pagination
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="updated_offset",
	 *   type="integer",
	 *   default="0",
	 *   description="Updated Offset for pagination"
	 * )
     * // phpcs:enable
     */
    protected $updated_offset = 0;

    /**
     * Total number of data required in each iteration
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="limit",
	 *   type="integer",
	 *   default="0",
	 *   description="Total number of data required in each iteration"
	 * )
     * // phpcs:enable
     */
    protected $limit = 0;


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
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;

    }//end getLimit()


    /**
     * Set Limit
     *
     * @param integer $limit Limit.
     *
     * @return self
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
        return $this;

    }//end setLimit()


}//end class
