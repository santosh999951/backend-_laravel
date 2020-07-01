<?php
/**
 * Response Model for Update User
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutUserResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutUserResponse",
 * description="Response Model for Update User",
 * )
 * // phpcs:enable
 */
class PutUserResponse extends ApiResponse
{

    /**
     * User First Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="first_name",
	 *   type="string",
	 *   default="",
	 *   description="User First Name"
	 * )
     * // phpcs:enable
     */
    protected $first_name = '';

    /**
     * User Last Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="last_name",
	 *   type="string",
	 *   default="",
	 *   description="User Last Name"
	 * )
     * // phpcs:enable
     */
    protected $last_name = '';

    /**
     * User Membership time in Guesthouser
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="member_since",
	 *   type="string",
	 *   default="",
	 *   description="User Membership time in Guesthouser"
	 * )
     * // phpcs:enable
     */
    protected $member_since = '';

    /**
     * User Date of Birth
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="dob",
	 *   type="string",
	 *   default="",
	 *   description="User Date of Birth"
	 * )
     * // phpcs:enable
     */
    protected $dob = '';

    /**
     * User Marital Status
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="marital_status",
	 *   type="string",
	 *   default="",
	 *   description="User Marital Status"
	 * )
     * // phpcs:enable
     */
    protected $marital_status = '';

    /**
     * User Gender
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="gender",
	 *   type="string",
	 *   default="",
	 *   description="User Gender"
	 * )
     * // phpcs:enable
     */
    protected $gender = '';

    /**
     * User Profession
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="profession",
	 *   type="string",
	 *   default="",
	 *   description="User Profession"
	 * )
     * // phpcs:enable
     */
    protected $profession = '';

    /**
     * User Email
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="email",
	 *   type="string",
	 *   default="",
	 *   description="User Email"
	 * )
     * // phpcs:enable
     */
    protected $email = '';

    /**
     * User Email Verified Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_email_verified",
	 *   type="integer",
	 *   default="0",
	 *   description="User Email Verified Status"
	 * )
     * // phpcs:enable
     */
    protected $is_email_verified = 0;

    /**
     * Contact Dial Code Eg. 91 for India
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="dial_code",
	 *   type="string",
	 *   default="",
	 *   description="Contact Dial Code Eg. 91 for India"
	 * )
     * // phpcs:enable
     */
    protected $dial_code = '';

    /**
     * User Contact
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="mobile",
	 *   type="string",
	 *   default="",
	 *   description="User Contact"
	 * )
     * // phpcs:enable
     */
    protected $mobile = '';

    /**
     * User Contact Verified Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_mobile_verified",
	 *   type="integer",
	 *   default="0",
	 *   description="User Contact Verified Status"
	 * )
     * // phpcs:enable
     */
    protected $is_mobile_verified = 0;

    /**
     * User Is Referred By Other User Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_user_referred",
	 *   type="integer",
	 *   default="0",
	 *   description="User Is Referred By Other User Status"
	 * )
     * // phpcs:enable
     */
    protected $is_user_referred = 0;

    /**
     * User Profile Image URL
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="profile_image",
	 *   type="string",
	 *   default="",
	 *   description="User Profile Image URL"
	 * )
     * // phpcs:enable
     */
    protected $profile_image = '';

    /**
     * User Profile Image Is Avatar Image When User not save Profile Picture
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_avatar_image",
	 *   type="integer",
	 *   default="0",
	 *   description="User Profile Image Is Avatar Image When User not save Profile Picture"
	 * )
     * // phpcs:enable
     */
    protected $is_avatar_image = 0;

    /**
     * User Spoken Languages
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="spoken_languages",
	 *   type="string",
	 *   default="",
	 *   description="User Spoken Languages"
	 * )
     * // phpcs:enable
     */
    protected $spoken_languages = '';

    /**
     * Travelled Places
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="travelled_places",
	 *   type="string",
	 *   default="",
	 *   description="Travelled Places"
	 * )
     * // phpcs:enable
     */
    protected $travelled_places = '';

    /**
     * Description
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="description",
	 *   type="string",
	 *   default="",
	 *   description="Description"
	 * )
     * // phpcs:enable
     */
    protected $description = '';

    /**
     * Guests Served Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="guests_served_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Guests Served Count"
	 * )
     * // phpcs:enable
     */
    protected $guests_served_count = 0;

    /**
     * Trips Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="trips_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Trips Count"
	 * )
     * // phpcs:enable
     */
    protected $trips_count = 0;

    /**
     * User Currency
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_currency",
	 *   type="string",
	 *   default="",
	 *   description="User Currency"
	 * )
     * // phpcs:enable
     */
    protected $user_currency = '';

    /**
     * User Is Host Status for Showing Host Section
     *
     * @var boolean
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_host",
	 *   type="boolean",
	 *   default="false",
	 *   description="User Is Host Status for Showing Host Section"
	 * )
     * // phpcs:enable
     */
    protected $is_host = false;

    /**
     * User Id
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_id",
	 *   type="integer",
	 *   default="0",
	 *   description="User Id"
	 * )
     * // phpcs:enable
     */
    protected $user_id = 0;

    /**
     * User Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="User Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $user_hash_id = '';

    /**
     * Facebook Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="fb_id",
	 *   type="string",
	 *   default="",
	 *   description="Facebook Id"
	 * )
     * // phpcs:enable
     */
    protected $fb_id = '';

    /**
     * Google Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="google_id",
	 *   type="string",
	 *   default="",
	 *   description="Google Id"
	 * )
     * // phpcs:enable
     */
    protected $google_id = '';

    /**
     * Wallet Section
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="wallet",
	 *   type="object",
	 *   default="{}",
	 *   description="Wallet Section",
	 *     @SWG\Property(
	 *       property="balance",
	 *       type="integer",
	 *       default="0",
	 *       description="Wallet Balance"
	 *     ),
	 *     @SWG\Property(
	 *       property="currency",
	 *       type="object",
	 *       default="{}",
	 *       description="Wallet Currency",
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
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $wallet = [];

    /**
     * Add Listing Url for Adding Property
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="add_listing",
	 *   type="string",
	 *   default="",
	 *   description="Add Listing Url for Adding Property"
	 * )
     * // phpcs:enable
     */
    protected $add_listing = '';

    /**
     * Event Eg. login
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="event",
	 *   type="string",
	 *   default="",
	 *   description="Event Eg. login"
	 * )
     * // phpcs:enable
     */
    protected $event = '';

    /**
     * User Is Relationship manager Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_rm",
	 *   type="integer",
	 *   default="0",
	 *   description="User Is Relationship manager Status"
	 * )
     * // phpcs:enable
     */
    protected $is_rm = 0;

    /**
     * Add Email Status for adding Email when not exist in Database
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="add_email",
	 *   type="integer",
	 *   default="0",
	 *   description="Add Email Status for adding Email when not exist in Database"
	 * )
     * // phpcs:enable
     */
    protected $add_email = 0;

    /**
     * Property Listed by User
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_listings",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Listed by User",
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
	 *       description="Accomodation"
	 *     ),
	 *     @SWG\Property(
	 *       property="min_units_required",
	 *       type="integer",
	 *       default="0",
	 *       description="Min Units Required"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_accomodation",
	 *       type="integer",
	 *       default="0",
	 *       description="Total Accomodation"
	 *     ),
	 *     @SWG\Property(
	 *       property="is_liked_by_user",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Is Liked By User Status"
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
	 *               description="Smart Discount Header Text"
	 *             ),
	 *             @SWG\Property(
	 *               property="discount",
	 *               type="integer",
	 *               default="0",
	 *               description="Smart Discount Value"
	 *             ),
	 *             @SWG\Property(
	 *               property="footer",
	 *               type="string",
	 *               default="",
	 *               description="Smart Discount Footer Text"
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
	 *       description="Property Images Section",
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
	 *           description="Tag Color Code in Rgb Format Eg. (210, 133, 134)"
	 *         ),
	 *         @SWG\Property(
	 *           property="colorHex",
	 *           type="string",
	 *           default="",
	 *           description="Tag Color Code in Hex Format Eg. #f3f3f3"
	 *         ),
	 *         @SWG\Property(
	 *           property="textRgb",
	 *           type="string",
	 *           default="",
	 *           description="Tag Text Coloe Code in Rgb Format Eg. (218, 345, 134)"
	 *         ),
	 *         @SWG\Property(
	 *           property="textHex",
	 *           type="string",
	 *           default="",
	 *           description="Tag Text Color Code in Hex Format Eg. #f3f3f3"
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
    protected $property_listings = [];

    /**
     * Message of Profile Update Status
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message of Profile Update Status"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


    /**
     * Get First_name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;

    }//end getFirstName()


    /**
     * Set First name
     *
     * @param string $first_name First name.
     *
     * @return self
     */
    public function setFirstName(string $first_name)
    {
        $this->first_name = $first_name;
        return $this;

    }//end setFirstName()


    /**
     * Get Last_name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;

    }//end getLastName()


    /**
     * Set Last name
     *
     * @param string $last_name Last name.
     *
     * @return self
     */
    public function setLastName(string $last_name)
    {
        $this->last_name = $last_name;
        return $this;

    }//end setLastName()


    /**
     * Get Member_since
     *
     * @return string
     */
    public function getMemberSince()
    {
        return $this->member_since;

    }//end getMemberSince()


    /**
     * Set Member since
     *
     * @param string $member_since Member since.
     *
     * @return self
     */
    public function setMemberSince(string $member_since)
    {
        $this->member_since = $member_since;
        return $this;

    }//end setMemberSince()


    /**
     * Get Dob
     *
     * @return string
     */
    public function getDob()
    {
        return $this->dob;

    }//end getDob()


    /**
     * Set Dob
     *
     * @param string $dob Dob.
     *
     * @return self
     */
    public function setDob(string $dob)
    {
        $this->dob = $dob;
        return $this;

    }//end setDob()


    /**
     * Get Marital_status
     *
     * @return string
     */
    public function getMaritalStatus()
    {
        return $this->marital_status;

    }//end getMaritalStatus()


    /**
     * Set Marital status
     *
     * @param string $marital_status Marital status.
     *
     * @return self
     */
    public function setMaritalStatus(string $marital_status)
    {
        $this->marital_status = $marital_status;
        return $this;

    }//end setMaritalStatus()


    /**
     * Get Gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;

    }//end getGender()


    /**
     * Set Gender
     *
     * @param string $gender Gender.
     *
     * @return self
     */
    public function setGender(string $gender)
    {
        $this->gender = $gender;
        return $this;

    }//end setGender()


    /**
     * Get Profession
     *
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;

    }//end getProfession()


    /**
     * Set Profession
     *
     * @param string $profession Profession.
     *
     * @return self
     */
    public function setProfession(string $profession)
    {
        $this->profession = $profession;
        return $this;

    }//end setProfession()


    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;

    }//end getEmail()


    /**
     * Set Email
     *
     * @param string $email Email.
     *
     * @return self
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;

    }//end setEmail()


    /**
     * Get Is_email_verified
     *
     * @return integer
     */
    public function getIsEmailVerified()
    {
        return $this->is_email_verified;

    }//end getIsEmailVerified()


    /**
     * Set Is email verified
     *
     * @param integer $is_email_verified Is email verified.
     *
     * @return self
     */
    public function setIsEmailVerified(int $is_email_verified)
    {
        $this->is_email_verified = $is_email_verified;
        return $this;

    }//end setIsEmailVerified()


    /**
     * Get Dial_code
     *
     * @return string
     */
    public function getDialCode()
    {
        return $this->dial_code;

    }//end getDialCode()


    /**
     * Set Dial code
     *
     * @param string $dial_code Dial code.
     *
     * @return self
     */
    public function setDialCode(string $dial_code)
    {
        $this->dial_code = $dial_code;
        return $this;

    }//end setDialCode()


    /**
     * Get Mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;

    }//end getMobile()


    /**
     * Set Mobile
     *
     * @param string $mobile Mobile.
     *
     * @return self
     */
    public function setMobile(string $mobile)
    {
        $this->mobile = $mobile;
        return $this;

    }//end setMobile()


    /**
     * Get Is_mobile_verified
     *
     * @return integer
     */
    public function getIsMobileVerified()
    {
        return $this->is_mobile_verified;

    }//end getIsMobileVerified()


    /**
     * Set Is mobile verified
     *
     * @param integer $is_mobile_verified Is mobile verified.
     *
     * @return self
     */
    public function setIsMobileVerified(int $is_mobile_verified)
    {
        $this->is_mobile_verified = $is_mobile_verified;
        return $this;

    }//end setIsMobileVerified()


    /**
     * Get Is_user_referred
     *
     * @return integer
     */
    public function getIsUserReferred()
    {
        return $this->is_user_referred;

    }//end getIsUserReferred()


    /**
     * Set Is user referred
     *
     * @param integer $is_user_referred Is user referred.
     *
     * @return self
     */
    public function setIsUserReferred(int $is_user_referred)
    {
        $this->is_user_referred = $is_user_referred;
        return $this;

    }//end setIsUserReferred()


    /**
     * Get Profile_image
     *
     * @return string
     */
    public function getProfileImage()
    {
        return $this->profile_image;

    }//end getProfileImage()


    /**
     * Set Profile image
     *
     * @param string $profile_image Profile image.
     *
     * @return self
     */
    public function setProfileImage(string $profile_image)
    {
        $this->profile_image = $profile_image;
        return $this;

    }//end setProfileImage()


    /**
     * Get Is_avatar_image
     *
     * @return integer
     */
    public function getIsAvatarImage()
    {
        return $this->is_avatar_image;

    }//end getIsAvatarImage()


    /**
     * Set Is avatar image
     *
     * @param integer $is_avatar_image Is avatar image.
     *
     * @return self
     */
    public function setIsAvatarImage(int $is_avatar_image)
    {
        $this->is_avatar_image = $is_avatar_image;
        return $this;

    }//end setIsAvatarImage()


    /**
     * Get Spoken_languages
     *
     * @return string
     */
    public function getSpokenLanguages()
    {
        return $this->spoken_languages;

    }//end getSpokenLanguages()


    /**
     * Set Spoken languages
     *
     * @param string $spoken_languages Spoken languages.
     *
     * @return self
     */
    public function setSpokenLanguages(string $spoken_languages)
    {
        $this->spoken_languages = $spoken_languages;
        return $this;

    }//end setSpokenLanguages()


    /**
     * Get Travelled_places
     *
     * @return string
     */
    public function getTravelledPlaces()
    {
        return $this->travelled_places;

    }//end getTravelledPlaces()


    /**
     * Set Travelled places
     *
     * @param string $travelled_places Travelled places.
     *
     * @return self
     */
    public function setTravelledPlaces(string $travelled_places)
    {
        $this->travelled_places = $travelled_places;
        return $this;

    }//end setTravelledPlaces()


    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;

    }//end getDescription()


    /**
     * Set Description
     *
     * @param string $description Description.
     *
     * @return self
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;

    }//end setDescription()


    /**
     * Get Guests_served_count
     *
     * @return integer
     */
    public function getGuestsServedCount()
    {
        return $this->guests_served_count;

    }//end getGuestsServedCount()


    /**
     * Set Guests served count
     *
     * @param integer $guests_served_count Guests served count.
     *
     * @return self
     */
    public function setGuestsServedCount(int $guests_served_count)
    {
        $this->guests_served_count = $guests_served_count;
        return $this;

    }//end setGuestsServedCount()


    /**
     * Get Trips_count
     *
     * @return integer
     */
    public function getTripsCount()
    {
        return $this->trips_count;

    }//end getTripsCount()


    /**
     * Set Trips count
     *
     * @param integer $trips_count Trips count.
     *
     * @return self
     */
    public function setTripsCount(int $trips_count)
    {
        $this->trips_count = $trips_count;
        return $this;

    }//end setTripsCount()


    /**
     * Get User_currency
     *
     * @return string
     */
    public function getUserCurrency()
    {
        return $this->user_currency;

    }//end getUserCurrency()


    /**
     * Set User currency
     *
     * @param string $user_currency User currency.
     *
     * @return self
     */
    public function setUserCurrency(string $user_currency)
    {
        $this->user_currency = $user_currency;
        return $this;

    }//end setUserCurrency()


    /**
     * Get Is_host
     *
     * @return boolean
     */
    public function getIsHost()
    {
        return $this->is_host;

    }//end getIsHost()


    /**
     * Set Is host
     *
     * @param boolean $is_host Is host.
     *
     * @return self
     */
    public function setIsHost(bool $is_host)
    {
        $this->is_host = $is_host;
        return $this;

    }//end setIsHost()


    /**
     * Get User_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;

    }//end getUserId()


    /**
     * Set User id
     *
     * @param integer $user_id User id.
     *
     * @return self
     */
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
        return $this;

    }//end setUserId()


    /**
     * Get User_hash_id
     *
     * @return string
     */
    public function getUserHashId()
    {
        return $this->user_hash_id;

    }//end getUserHashId()


    /**
     * Set User hash id
     *
     * @param string $user_hash_id User hash id.
     *
     * @return self
     */
    public function setUserHashId(string $user_hash_id)
    {
        $this->user_hash_id = $user_hash_id;
        return $this;

    }//end setUserHashId()


    /**
     * Get Fb_id
     *
     * @return string
     */
    public function getFbId()
    {
        return $this->fb_id;

    }//end getFbId()


    /**
     * Set Fb id
     *
     * @param string $fb_id Fb id.
     *
     * @return self
     */
    public function setFbId(string $fb_id)
    {
        $this->fb_id = $fb_id;
        return $this;

    }//end setFbId()


    /**
     * Get Google_id
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->google_id;

    }//end getGoogleId()


    /**
     * Set Google id
     *
     * @param string $google_id Google id.
     *
     * @return self
     */
    public function setGoogleId(string $google_id)
    {
        $this->google_id = $google_id;
        return $this;

    }//end setGoogleId()


    /**
     * Get Wallet
     *
     * @return object
     */
    public function getWallet()
    {
        return (empty($this->wallet) === false) ? $this->wallet : new \stdClass;

    }//end getWallet()


    /**
     * Set Wallet
     *
     * @param array $wallet Wallet.
     *
     * @return self
     */
    public function setWallet(array $wallet)
    {
        $this->wallet = $wallet;
        return $this;

    }//end setWallet()


    /**
     * Get Add_listing
     *
     * @return string
     */
    public function getAddListing()
    {
        return $this->add_listing;

    }//end getAddListing()


    /**
     * Set Add listing
     *
     * @param string $add_listing Add listing.
     *
     * @return self
     */
    public function setAddListing(string $add_listing)
    {
        $this->add_listing = $add_listing;
        return $this;

    }//end setAddListing()


    /**
     * Get Event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;

    }//end getEvent()


    /**
     * Set Event
     *
     * @param string $event Event.
     *
     * @return self
     */
    public function setEvent(string $event)
    {
        $this->event = $event;
        return $this;

    }//end setEvent()


    /**
     * Get Is_rm
     *
     * @return integer
     */
    public function getIsRm()
    {
        return $this->is_rm;

    }//end getIsRm()


    /**
     * Set Is rm
     *
     * @param integer $is_rm Is rm.
     *
     * @return self
     */
    public function setIsRm(int $is_rm)
    {
        $this->is_rm = $is_rm;
        return $this;

    }//end setIsRm()


    /**
     * Get Add_email
     *
     * @return integer
     */
    public function getAddEmail()
    {
        return $this->add_email;

    }//end getAddEmail()


    /**
     * Set Add email
     *
     * @param integer $add_email Add email.
     *
     * @return self
     */
    public function setAddEmail(int $add_email)
    {
        $this->add_email = $add_email;
        return $this;

    }//end setAddEmail()


    /**
     * Get Property_listings
     *
     * @return array
     */
    public function getPropertyListings()
    {
        return $this->property_listings;

    }//end getPropertyListings()


    /**
     * Set Property listings
     *
     * @param array $property_listings Property listings.
     *
     * @return self
     */
    public function setPropertyListings(array $property_listings)
    {
        $this->property_listings = $property_listings;
        return $this;

    }//end setPropertyListings()


    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;

    }//end getMessage()


    /**
     * Set Message
     *
     * @param string $message Message.
     *
     * @return self
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;

    }//end setMessage()


}//end class
