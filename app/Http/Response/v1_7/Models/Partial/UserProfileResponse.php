<?php
/**
 * UserProfileResponse
 */

namespace App\Http\Response\v1_7\Models\Partial;

use App\Http\Response\v1_7\Models\ApiResponse;

/**
 * Class UserProfileResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="UserProfileResponse",
 * description="UserProfileResponse",
 * )
 * // phpcs:enable
 */
class UserProfileResponse extends ApiResponse
{

    /**
     * Property First Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="first_name",
	 *   type="string",
	 *   default="",
	 *   description="Property First Name"
	 * )
     * // phpcs:enable
     */
    protected $first_name = '';

    /**
     * Property Last Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="last_name",
	 *   type="string",
	 *   default="",
	 *   description="Property Last Name"
	 * )
     * // phpcs:enable
     */
    protected $last_name = '';

    /**
     * Property Member Since
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="member_since",
	 *   type="string",
	 *   default="",
	 *   description="Property Member Since"
	 * )
     * // phpcs:enable
     */
    protected $member_since = '';

    /**
     * Property Dob
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="dob",
	 *   type="string",
	 *   default="",
	 *   description="Property Dob"
	 * )
     * // phpcs:enable
     */
    protected $dob = '';

    /**
     * Property Marital Status
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="marital_status",
	 *   type="string",
	 *   default="",
	 *   description="Property Marital Status"
	 * )
     * // phpcs:enable
     */
    protected $marital_status = '';

    /**
     * Property Gender
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="gender",
	 *   type="string",
	 *   default="",
	 *   description="Property Gender"
	 * )
     * // phpcs:enable
     */
    protected $gender = '';

    /**
     * Property Profession
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="profession",
	 *   type="string",
	 *   default="",
	 *   description="Property Profession"
	 * )
     * // phpcs:enable
     */
    protected $profession = '';

    /**
     * Property Email
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="email",
	 *   type="string",
	 *   default="",
	 *   description="Property Email"
	 * )
     * // phpcs:enable
     */
    protected $email = '';

    /**
     * Property Is Email Verified
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_email_verified",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Is Email Verified"
	 * )
     * // phpcs:enable
     */
    protected $is_email_verified = 0;

    /**
     * Property Dial Code
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="dial_code",
	 *   type="string",
	 *   default="",
	 *   description="Property Dial Code"
	 * )
     * // phpcs:enable
     */
    protected $dial_code = '';

    /**
     * Property Mobile
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="mobile",
	 *   type="string",
	 *   default="",
	 *   description="Property Mobile"
	 * )
     * // phpcs:enable
     */
    protected $mobile = '';

    /**
     * Property Is Mobile Verified
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_mobile_verified",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Is Mobile Verified"
	 * )
     * // phpcs:enable
     */
    protected $is_mobile_verified = 0;

    /**
     * Property Is User Referred
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_user_referred",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Is User Referred"
	 * )
     * // phpcs:enable
     */
    protected $is_user_referred = 0;

    /**
     * Property Profile Image
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="profile_image",
	 *   type="string",
	 *   default="",
	 *   description="Property Profile Image"
	 * )
     * // phpcs:enable
     */
    protected $profile_image = '';

    /**
     * Property Is Avatar Image
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_avatar_image",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Is Avatar Image"
	 * )
     * // phpcs:enable
     */
    protected $is_avatar_image = 0;

    /**
     * Property Spoken Languages
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="spoken_languages",
	 *   type="string",
	 *   default="",
	 *   description="Property Spoken Languages"
	 * )
     * // phpcs:enable
     */
    protected $spoken_languages = '';

    /**
     * Property Travelled Places
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="travelled_places",
	 *   type="string",
	 *   default="",
	 *   description="Property Travelled Places"
	 * )
     * // phpcs:enable
     */
    protected $travelled_places = '';

    /**
     * Property Description
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="description",
	 *   type="string",
	 *   default="",
	 *   description="Property Description"
	 * )
     * // phpcs:enable
     */
    protected $description = '';

    /**
     * Property Guests Served Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="guests_served_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Guests Served Count"
	 * )
     * // phpcs:enable
     */
    protected $guests_served_count = 0;

    /**
     * Property Trips Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="trips_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Trips Count"
	 * )
     * // phpcs:enable
     */
    protected $trips_count = 0;

    /**
     * Property Active Request Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="active_request_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Active Request Count"
	 * )
     * // phpcs:enable
     */
    protected $active_request_count = 0;

    /**
     * Property User Currency
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_currency",
	 *   type="string",
	 *   default="",
	 *   description="Property User Currency"
	 * )
     * // phpcs:enable
     */
    protected $user_currency = '';

    /**
     * Property Is Host
     *
     * @var boolean
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_host",
	 *   type="boolean",
	 *   default="false",
	 *   description="Property Is Host"
	 * )
     * // phpcs:enable
     */
    protected $is_host = false;

    /**
     * Property User Id
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_id",
	 *   type="integer",
	 *   default="0",
	 *   description="Property User Id"
	 * )
     * // phpcs:enable
     */
    protected $user_id = 0;

    /**
     * Property User Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="user_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Property User Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $user_hash_id = '';

    /**
     * Property Fb Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="fb_id",
	 *   type="string",
	 *   default="",
	 *   description="Property Fb Id"
	 * )
     * // phpcs:enable
     */
    protected $fb_id = '';

    /**
     * Property Google Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="google_id",
	 *   type="string",
	 *   default="",
	 *   description="Property Google Id"
	 * )
     * // phpcs:enable
     */
    protected $google_id = '';

    /**
     * Property Apple Id
     *
     * @var null
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="apple_id",
	 *   type="null",
	 *   default="",
	 *   description="Property Apple Id"
	 * )
     * // phpcs:enable
     */
    protected $apple_id = '';

    /**
     * Property Wallet
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="wallet",
	 *   type="object",
	 *   default="{}",
	 * ref="#/definitions/WalletResponse",
	 *   description="Property Wallet"
	 * )
     * // phpcs:enable
     */
    protected $wallet = [];

    /**
     * Property Add Listing
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="add_listing",
	 *   type="string",
	 *   default="",
	 *   description="Property Add Listing"
	 * )
     * // phpcs:enable
     */
    protected $add_listing = '';

    /**
     * Property Event
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="event",
	 *   type="string",
	 *   default="",
	 *   description="Property Event"
	 * )
     * // phpcs:enable
     */
    protected $event = '';

    /**
     * Property Is Rm
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_rm",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Is Rm"
	 * )
     * // phpcs:enable
     */
    protected $is_rm = 0;

    /**
     * Property Add Email
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="add_email",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Add Email"
	 * )
     * // phpcs:enable
     */
    protected $add_email = 0;

    /**
     * Property Auth Key
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="auth_key",
	 *   type="string",
	 *   default="",
	 *   description="Property Auth Key"
	 * )
     * // phpcs:enable
     */
    protected $auth_key = '';

    /**
     * Property Is Prive Manager
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="is_prive_manager",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Is Prive Manager"
	 * )
     * // phpcs:enable
     */
    protected $is_prive_manager = 0;


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


//phpcs:disable
    /**
     * Get Apple_id
     *
     * @return null
     */
    public function getAppleId()
    {
        return $this->apple_id;

    }//end getAppleId()


    /**
     * Set Apple id
     *
     * @param $apple_id Apple id.
     *
     * @return self
     */
    public function setAppleId($apple_id)
    {
        $this->apple_id = $apple_id;
        return $this;

    }//end setAppleId()


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
        $response      = new WalletResponse($wallet);
         $response     = $response->toArray();
         $this->wallet = $response;
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
     * Get Auth_key
     *
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;

    }//end getAuthKey()


    /**
     * Set Auth key
     *
     * @param string $auth_key Auth key.
     *
     * @return self
     */
    public function setAuthKey(string $auth_key)
    {
        $this->auth_key = $auth_key;
        return $this;

    }//end setAuthKey()


    /**
     * Get Is_prive_manager
     *
     * @return integer
     */
    public function getIsPriveManager()
    {
        return $this->is_prive_manager;

    }//end getIsPriveManager()


    /**
     * Set Is prive manager
     *
     * @param integer $is_prive_manager Is prive manager.
     *
     * @return self
     */
    public function setIsPriveManager(int $is_prive_manager)
    {
        $this->is_prive_manager = $is_prive_manager;
        return $this;

    }//end setIsPriveManager()


}//end class
