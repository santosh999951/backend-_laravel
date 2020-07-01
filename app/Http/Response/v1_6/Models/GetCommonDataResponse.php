<?php
/**
 * GetCommonDataResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetCommonDataResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetCommonDataResponse",
 * description="GetCommonDataResponse",
 * )
 * // phpcs:enable
 */
class GetCommonDataResponse extends ApiResponse
{

    /**
     * Property App Version Check
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="app_version_check",
	 *   type="object",
	 *   default="{}",
	 *   description="Property App Version Check",
	 *     @SWG\Property(
	 *       property="required",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Required"
	 *     ),
	 *     @SWG\Property(
	 *       property="min_version",
	 *       type="string",
	 *       default="",
	 *       description="Property Min Version"
	 *     ),
	 *     @SWG\Property(
	 *       property="latest_version",
	 *       type="string",
	 *       default="",
	 *       description="Property Latest Version"
	 *     ),
	 *     @SWG\Property(
	 *       property="text",
	 *       type="string",
	 *       default="",
	 *       description="Property Text"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $app_version_check = [];

    /**
     * Property New Rating Days
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="new_rating_days",
	 *   type="integer",
	 *   default="0",
	 *   description="Property New Rating Days"
	 * )
     * // phpcs:enable
     */
    protected $new_rating_days = 0;

    /**
     * Property Old Rating Days
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="old_rating_days",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Old Rating Days"
	 * )
     * // phpcs:enable
     */
    protected $old_rating_days = 0;

    /**
     * Property Chat Available
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="chat_available",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Chat Available"
	 * )
     * // phpcs:enable
     */
    protected $chat_available = 0;

    /**
     * Property Chat Call Text
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="chat_call_text",
	 *   type="string",
	 *   default="",
	 *   description="Property Chat Call Text"
	 * )
     * // phpcs:enable
     */
    protected $chat_call_text = '';

    /**
     * Property Chat
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="chat",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Chat",
	 *     @SWG\Property(
	 *       property="from_time",
	 *       type="string",
	 *       default="",
	 *       description="Property From Time"
	 *     ),
	 *     @SWG\Property(
	 *       property="to_time",
	 *       type="string",
	 *       default="",
	 *       description="Property To Time"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $chat = [];

    /**
     * Property Spotlight List
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="spotlight_list",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Spotlight List",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="property_type_name",
	 *       type="none",
	 *       default="00",
	 *       description="Property Property Type Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="property_type",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Property Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="state",
	 *       type="string",
	 *       default="",
	 *       description="Property State"
	 *     ),
	 *     @SWG\Property(
	 *       property="tag",
	 *       type="string",
	 *       default="",
	 *       description="Property Tag"
	 *     ),
	 *     @SWG\Property(
	 *       property="title",
	 *       type="string",
	 *       default="",
	 *       description="Property Title"
	 *     ),
	 *     @SWG\Property(
	 *       property="city",
	 *       type="string",
	 *       default="",
	 *       description="Property City"
	 *     ),
	 *     @SWG\Property(
	 *       property="country",
	 *       type="string",
	 *       default="",
	 *       description="Property Country"
	 *     ),
	 *     @SWG\Property(
	 *       property="country_name",
	 *       type="string",
	 *       default="",
	 *       description="Property Country Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="v_lat",
	 *       type="string",
	 *       default="",
	 *       description="Property V Lat"
	 *     ),
	 *     @SWG\Property(
	 *       property="v_lng",
	 *       type="string",
	 *       default="",
	 *       description="Property V Lng"
	 *     ),
	 *     @SWG\Property(
	 *       property="image_url",
	 *       type="string",
	 *       default="",
	 *       description="Property Image Url"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $spotlight_list = [];

    /**
     * Gh Contact
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="gh_contact",
	 *   type="string",
	 *   default="",
	 *   description="Gh Contact"
	 * )
     * // phpcs:enable
     */
    protected $gh_contact = '';

    /**
     * Property Property Type
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_type",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Property Type",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="id",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Property Name"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $property_type = [];

    /**
     * Property Autocomplete Api
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="autocomplete_api",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Autocomplete Api"
	 * )
     * // phpcs:enable
     */
    protected $autocomplete_api = 0;


    /**
     * Get App_version_check
     *
     * @return object
     */
    public function getAppVersionCheck()
    {
        return (empty($this->app_version_check) === false) ? $this->app_version_check : new \stdClass;

    }//end getAppVersionCheck()


    /**
     * Set App version check
     *
     * @param array $app_version_check App version check.
     *
     * @return self
     */
    public function setAppVersionCheck(array $app_version_check)
    {
        $this->app_version_check = $app_version_check;
        return $this;

    }//end setAppVersionCheck()


    /**
     * Get New_rating_days
     *
     * @return integer
     */
    public function getNewRatingDays()
    {
        return $this->new_rating_days;

    }//end getNewRatingDays()


    /**
     * Set New rating days
     *
     * @param integer $new_rating_days New rating days.
     *
     * @return self
     */
    public function setNewRatingDays(int $new_rating_days)
    {
        $this->new_rating_days = $new_rating_days;
        return $this;

    }//end setNewRatingDays()


    /**
     * Get Old_rating_days
     *
     * @return integer
     */
    public function getOldRatingDays()
    {
        return $this->old_rating_days;

    }//end getOldRatingDays()


    /**
     * Set Old rating days
     *
     * @param integer $old_rating_days Old rating days.
     *
     * @return self
     */
    public function setOldRatingDays(int $old_rating_days)
    {
        $this->old_rating_days = $old_rating_days;
        return $this;

    }//end setOldRatingDays()


    /**
     * Get Chat_available
     *
     * @return integer
     */
    public function getChatAvailable()
    {
        return $this->chat_available;

    }//end getChatAvailable()


    /**
     * Set Chat available
     *
     * @param integer $chat_available Chat available.
     *
     * @return self
     */
    public function setChatAvailable(int $chat_available)
    {
        $this->chat_available = $chat_available;
        return $this;

    }//end setChatAvailable()


    /**
     * Get Chat_call_text
     *
     * @return string
     */
    public function getChatCallText()
    {
        return $this->chat_call_text;

    }//end getChatCallText()


    /**
     * Set Chat call text
     *
     * @param string $chat_call_text Chat call text.
     *
     * @return self
     */
    public function setChatCallText(string $chat_call_text)
    {
        $this->chat_call_text = $chat_call_text;
        return $this;

    }//end setChatCallText()


    /**
     * Get Chat
     *
     * @return object
     */
    public function getChat()
    {
        return (empty($this->chat) === false) ? $this->chat : new \stdClass;

    }//end getChat()


    /**
     * Set Chat
     *
     * @param array $chat Chat.
     *
     * @return self
     */
    public function setChat(array $chat)
    {
        $this->chat = $chat;
        return $this;

    }//end setChat()


    /**
     * Get Spotlight_list
     *
     * @return array
     */
    public function getSpotlightList()
    {
        return $this->spotlight_list;

    }//end getSpotlightList()


    /**
     * Set Spotlight list
     *
     * @param array $spotlight_list Spotlight list.
     *
     * @return self
     */
    public function setSpotlightList(array $spotlight_list)
    {
        $this->spotlight_list = $spotlight_list;
        return $this;

    }//end setSpotlightList()


    /**
     * Get Gh_contact
     *
     * @return string
     */
    public function getGhContact()
    {
        return $this->gh_contact;

    }//end getGhContact()


    /**
     * Set Gh contact
     *
     * @param string $gh_contact Gh contact.
     *
     * @return self
     */
    public function setGhContact(string $gh_contact)
    {
        $this->gh_contact = $gh_contact;
        return $this;

    }//end setGhContact()


    /**
     * Get Property_type
     *
     * @return array
     */
    public function getPropertyType()
    {
        return $this->property_type;

    }//end getPropertyType()


    /**
     * Set Property type
     *
     * @param array $property_type Property type.
     *
     * @return self
     */
    public function setPropertyType(array $property_type)
    {
        $this->property_type = $property_type;
        return $this;

    }//end setPropertyType()


    /**
     * Get Autocomplete_api
     *
     * @return integer
     */
    public function getAutocompleteApi()
    {
        return $this->autocomplete_api;

    }//end getAutocompleteApi()


    /**
     * Set Autocomplete api
     *
     * @param integer $autocomplete_api Autocomplete api.
     *
     * @return self
     */
    public function setAutocompleteApi(int $autocomplete_api)
    {
        $this->autocomplete_api = $autocomplete_api;
        return $this;

    }//end setAutocompleteApi()


}//end class
