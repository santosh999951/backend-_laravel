<?php
/**
 * Response Model for Completed Trip Pending Review Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetTripReviewResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetTripReviewResponse",
 * description="Response Model for Completed Trip Pending Review Api",
 * )
 * // phpcs:enable
 */
class GetTripReviewResponse extends ApiResponse
{

    /**
     * Review Text
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="review_text",
	 *   type="string",
	 *   default="",
	 *   description="Review Text"
	 * )
     * // phpcs:enable
     */
    protected $review_text = '';

    /**
     * Rating Params
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="rating_params",
	 *   type="array",
	 *   default="[]",
	 *   description="Rating Params",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Rating Id Eg. 1, 2"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Rating Title Eg. Location accuracy, Comfort & cleanliness"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $rating_params = [];

    /**
     * Pending Reviews Bookings List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="bookings",
	 *   type="array",
	 *   default="[]",
	 *   description="Pending Reviews Bookings List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="booking_request_id",
	 *       type="string",
	 *       default="",
	 *       description="Booking Request Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_section",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Section",
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
	 *       property="review_pending",
	 *       type="integer",
	 *       default="0",
	 *       description="Review Pending Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="rating_pending",
	 *       type="integer",
	 *       default="0",
	 *       description="Rating Pending Status"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $bookings = [];

    /**
     * Updated Offset For Pagination
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="updated_offset",
	 *   type="integer",
	 *   default="0",
	 *   description="Updated Offset For Pagination"
	 * )
     * // phpcs:enable
     */
    protected $updated_offset = 0;

    /**
     * Number of data fetch in api
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="limit",
	 *   type="integer",
	 *   default="0",
	 *   description="Number of data fetch in api"
	 * )
     * // phpcs:enable
     */
    protected $limit = 0;


    /**
     * Get Review_text
     *
     * @return string
     */
    public function getReviewText()
    {
        return $this->review_text;

    }//end getReviewText()


    /**
     * Set Review text
     *
     * @param string $review_text Review text.
     *
     * @return self
     */
    public function setReviewText(string $review_text)
    {
        $this->review_text = $review_text;
        return $this;

    }//end setReviewText()


    /**
     * Get Rating_params
     *
     * @return array
     */
    public function getRatingParams()
    {
        return $this->rating_params;

    }//end getRatingParams()


    /**
     * Set Rating params
     *
     * @param array $rating_params Rating params.
     *
     * @return self
     */
    public function setRatingParams(array $rating_params)
    {
        $this->rating_params = $rating_params;
        return $this;

    }//end setRatingParams()


    /**
     * Get Bookings
     *
     * @return array
     */
    public function getBookings()
    {
        return $this->bookings;

    }//end getBookings()


    /**
     * Set Bookings
     *
     * @param array $bookings Bookings.
     *
     * @return self
     */
    public function setBookings(array $bookings)
    {
        $this->bookings = $bookings;
        return $this;

    }//end setBookings()


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
