<?php
/**
 * Get Host Property Listing Details Dats
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostPropertyListingDetailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostPropertyListingDetailResponse",
 * description="Get Host Property Listing Details Dats",
 * )
 * // phpcs:enable
 */
class GetHostPropertyListingDetailResponse extends ApiResponse
{

    /**
     * Property Details
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_details",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Details",
	 *     @SWG\Property(
	 *       property="property_hash_id",
	 *       type="string",
	 *       default="",
	 *       description="Property Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="units",
	 *       type="string",
	 *       default="",
	 *       description="Units"
	 *     ),
	 *     @SWG\Property(
	 *       property="accomodation",
	 *       type="string",
	 *       default="",
	 *       description="Accomodation"
	 *     ),
	 *     @SWG\Property(
	 *       property="additional_guest_count",
	 *       type="integer",
	 *       default="0",
	 *       description="Additional Guest Count"
	 *     ),
	 *     @SWG\Property(
	 *       property="bedrooms",
	 *       type="string",
	 *       default="",
	 *       description="Bedrooms"
	 *     ),
	 *     @SWG\Property(
	 *       property="beds",
	 *       type="string",
	 *       default="",
	 *       description="Beds"
	 *     ),
	 *     @SWG\Property(
	 *       property="bathrooms",
	 *       type="string",
	 *       default="",
	 *       description="Bathrooms"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="noc_status",
	 *       type="integer",
	 *       default="0",
	 *       description="Noc Status"
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
	 *       property="check_in",
	 *       type="string",
	 *       default="",
	 *       description="Check In"
	 *     ),
	 *     @SWG\Property(
	 *       property="check_out",
	 *       type="string",
	 *       default="",
	 *       description="Check Out"
	 *     ),
	 *     @SWG\Property(
	 *       property="cleaning_mode",
	 *       type="integer",
	 *       default="0",
	 *       description="Cleaning Mode"
	 *     ),
	 *     @SWG\Property(
	 *       property="location",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Location",
	 *         @SWG\Property(
	 *           property="address",
	 *           type="string",
	 *           default="",
	 *           description="Address"
	 *         ),
	 *         @SWG\Property(
	 *           property="area",
	 *           type="string",
	 *           default="",
	 *           description="Area"
	 *         ),
	 *         @SWG\Property(
	 *           property="city",
	 *           type="string",
	 *           default="",
	 *           description="City"
	 *         ),
	 *         @SWG\Property(
	 *           property="state",
	 *           type="string",
	 *           default="",
	 *           description="State"
	 *         ),
	 *         @SWG\Property(
	 *           property="country",
	 *           type="object",
	 *           default="{}",
	 *           description="Country",
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
	 *           property="zipcode",
	 *           type="string",
	 *           default="",
	 *           description="Property Zipcode"
	 *         ),
	 *         @SWG\Property(
	 *           property="latitude",
	 *           type="string",
	 *           default="",
	 *           description="Property Latitude"
	 *         ),
	 *         @SWG\Property(
	 *           property="longitude",
	 *           type="string",
	 *           default="",
	 *           description="Property Longitude"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="prices",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Prices",
	 *         @SWG\Property(
	 *           property="currency",
	 *           type="object",
	 *           default="{}",
	 *           description="Pricing Currency",
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
	 *           property="per_night_price",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Per Night Price"
	 *         ),
	 *         @SWG\Property(
	 *           property="per_week_price",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Per Week Price"
	 *         ),
	 *         @SWG\Property(
	 *           property="per_month_price",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Per Month Price"
	 *         ),
	 *         @SWG\Property(
	 *           property="additional_guest_fee",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Additional Guest Fee"
	 *         ),
	 *         @SWG\Property(
	 *           property="gh_commission",
	 *           type="string",
	 *           default="",
	 *           description="Property Gh Commission"
	 *         ),
	 *         @SWG\Property(
	 *           property="markup_service_fee",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Markup Service Fee"
	 *         ),
	 *         @SWG\Property(
	 *           property="cleaning_fee",
	 *           type="integer",
	 *           default="0",
	 *           description="Property Cleaning Fee"
	 *         )
	 *     ),
	 *     @SWG\Property(
	 *       property="last_update",
	 *       type="string",
	 *       default="",
	 *       description="Property Last Updated Time"
	 *     ),
	 *     @SWG\Property(
	 *       property="enabled",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Enabled status"
	 *     ),
	 *     @SWG\Property(
	 *       property="status",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Listing Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="gstin",
	 *       type="string",
	 *       default="",
	 *       description="GST Number"
	 *     ),
	 *     @SWG\Property(
	 *       property="video_link",
	 *       type="string",
	 *       default="",
	 *       description="Property Video Link"
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
	 *           description="Image url"
	 *         ),
	 *         @SWG\Property(
	 *           property="caption",
	 *           type="string",
	 *           default="",
	 *           description="Image Caption"
	 *         ),
	 *         @SWG\Property(
	 *           property="is_hide",
	 *           type="integer",
	 *           default="0",
	 *           description="Image Is Hide Status"
	 *         ),
	 *         @SWG\Property(
	 *           property="unlink",
	 *           type="integer",
	 *           default="0",
	 *           description="Image Unlink status"
	 *         ),
	 *         @SWG\Property(
	 *           property="order",
	 *           type="integer",
	 *           default="0",
	 *           description="Image Order"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="properties_videos",
	 *       type="array",
	 *       default="[]",
	 *       description="Properties Videos",
	 *       @SWG\Items(
	 *         type="object",
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="url",
	 *       type="string",
	 *       default="",
	 *       description="Property Url"
	 *     ),
	 *     @SWG\Property(
	 *       property="details",
	 *       type="object",
	 *       default="{}",
	 *       description="Property Details",
	 *         @SWG\Property(
	 *           property="policy_services",
	 *           type="string",
	 *           default="",
	 *           description="Policy Services"
	 *         ),
	 *         @SWG\Property(
	 *           property="your_space",
	 *           type="string",
	 *           default="",
	 *           description="Your Space"
	 *         ),
	 *         @SWG\Property(
	 *           property="house_rule",
	 *           type="string",
	 *           default="",
	 *           description="House Rule"
	 *         ),
	 *         @SWG\Property(
	 *           property="guest_brief",
	 *           type="string",
	 *           default="",
	 *           description="Guest Brief"
	 *         ),
	 *         @SWG\Property(
	 *           property="interaction_with_guest",
	 *           type="string",
	 *           default="",
	 *           description="Interaction With Guest"
	 *         ),
	 *         @SWG\Property(
	 *           property="local_experience",
	 *           type="string",
	 *           default="",
	 *           description="Local Experience"
	 *         ),
	 *         @SWG\Property(
	 *           property="from_airport",
	 *           type="string",
	 *           default="",
	 *           description="From Airport"
	 *         ),
	 *         @SWG\Property(
	 *           property="train_station",
	 *           type="string",
	 *           default="",
	 *           description="Train Station"
	 *         ),
	 *         @SWG\Property(
	 *           property="bus_station",
	 *           type="string",
	 *           default="",
	 *           description="Bus Station"
	 *         ),
	 *         @SWG\Property(
	 *           property="extra_detail",
	 *           type="string",
	 *           default="",
	 *           description="Extra Detail"
	 *         ),
	 *         @SWG\Property(
	 *           property="usp",
	 *           type="string",
	 *           default="",
	 *           description="Usp"
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $property_details = [];

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
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected",
	 *       type="integer",
	 *       default="0",
	 *       description="Selected"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $property_types = [];

    /**
     * Property Room Types
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="room_types",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Room Types",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected",
	 *       type="integer",
	 *       default="0",
	 *       description="Selected"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $room_types = [];

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
	 *       description="Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected",
	 *       type="integer",
	 *       default="0",
	 *       description="Selected"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $tags = [];

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
	 *       property="cat_id",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Cat Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="category_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Category Name"
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
	 *           description="Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="selected",
	 *           type="integer",
	 *           default="0",
	 *           description="Selected"
	 *         )
	 *       )
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $amenities = [];

    /**
     * Property Cancelation Policy
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="cancelation_policy",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Cancelation Policy",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="selected",
	 *       type="integer",
	 *       default="0",
	 *       description="Selected"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $cancelation_policy = [];


    /**
     * Get Property_details
     *
     * @return object
     */
    public function getPropertyDetails()
    {
        return (empty($this->property_details) === false) ? $this->property_details : new \stdClass;

    }//end getPropertyDetails()


    /**
     * Set Property details
     *
     * @param array $property_details Property details.
     *
     * @return self
     */
    public function setPropertyDetails(array $property_details)
    {
        $this->property_details = $property_details;
        return $this;

    }//end setPropertyDetails()


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
     * Get Room_types
     *
     * @return array
     */
    public function getRoomTypes()
    {
        return $this->room_types;

    }//end getRoomTypes()


    /**
     * Set Room types
     *
     * @param array $room_types Room types.
     *
     * @return self
     */
    public function setRoomTypes(array $room_types)
    {
        $this->room_types = $room_types;
        return $this;

    }//end setRoomTypes()


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
     * Get Cancelation_policy
     *
     * @return array
     */
    public function getCancelationPolicy()
    {
        return $this->cancelation_policy;

    }//end getCancelationPolicy()


    /**
     * Set Cancelation policy
     *
     * @param array $cancelation_policy Cancelation policy.
     *
     * @return self
     */
    public function setCancelationPolicy(array $cancelation_policy)
    {
        $this->cancelation_policy = $cancelation_policy;
        return $this;

    }//end setCancelationPolicy()


}//end class
