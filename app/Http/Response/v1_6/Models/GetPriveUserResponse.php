<?php
/**
 * GetPriveUserResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetPriveUserResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetPriveUserResponse",
 * description="GetPriveUserResponse",
 * )
 * // phpcs:enable
 */
class GetPriveUserResponse extends ApiResponse
{

    /**
     * First Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="first_name",
	 *   type="string",
	 *   default="",
	 *   description="First Name"
	 * )
     * // phpcs:enable
     */
    protected $first_name = '';

    /**
     * Last Name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="last_name",
	 *   type="string",
	 *   default="",
	 *   description="Last Name"
	 * )
     * // phpcs:enable
     */
    protected $last_name = '';

    /**
     * Email
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="email",
	 *   type="string",
	 *   default="",
	 *   description="Email"
	 * )
     * // phpcs:enable
     */
    protected $email = '';

    /**
     * Dial Code
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="dial_code",
	 *   type="string",
	 *   default="",
	 *   description="Dial Code"
	 * )
     * // phpcs:enable
     */
    protected $dial_code = '';

    /**
     * Contact
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="contact",
	 *   type="string",
	 *   default="",
	 *   description="Contact"
	 * )
     * // phpcs:enable
     */
    protected $contact = '';

    /**
     * Profile Image
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="profile_image",
	 *   type="string",
	 *   default="",
	 *   description="Profile Image"
	 * )
     * // phpcs:enable
     */
    protected $profile_image = '';

    /**
     * Property Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Count"
	 * )
     * // phpcs:enable
     */
    protected $property_count = 0;

    /**
     * Aadhar Card No
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="aadhar_card_no",
	 *   type="string",
	 *   default="",
	 *   description="Aadhar Card No"
	 * )
     * // phpcs:enable
     */
    protected $aadhar_card_no = '';

    /**
     * Pan Card No
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="pan_card_no",
	 *   type="string",
	 *   default="",
	 *   description="Pan Card No"
	 * )
     * // phpcs:enable
     */
    protected $pan_card_no = '';

    /**
     * Account No
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="account_no",
	 *   type="string",
	 *   default="",
	 *   description="Account No"
	 * )
     * // phpcs:enable
     */
    protected $account_no = '';

    /**
     * Ifsc Code
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="ifsc_code",
	 *   type="string",
	 *   default="",
	 *   description="Ifsc Code"
	 * )
     * // phpcs:enable
     */
    protected $ifsc_code = '';


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
     * Get Contact
     *
     * @return string
     */
    public function getContact()
    {
        return $this->contact;

    }//end getContact()


    /**
     * Set Contact
     *
     * @param string $contact Contact.
     *
     * @return self
     */
    public function setContact(string $contact)
    {
        $this->contact = $contact;
        return $this;

    }//end setContact()


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
     * Get Property_count
     *
     * @return integer
     */
    public function getPropertyCount()
    {
        return $this->property_count;

    }//end getPropertyCount()


    /**
     * Set Property count
     *
     * @param integer $property_count Property count.
     *
     * @return self
     */
    public function setPropertyCount(int $property_count)
    {
        $this->property_count = $property_count;
        return $this;

    }//end setPropertyCount()


    /**
     * Get Aadhar_card_no
     *
     * @return string
     */
    public function getAadharCardNo()
    {
        return $this->aadhar_card_no;

    }//end getAadharCardNo()


    /**
     * Set Aadhar card no
     *
     * @param string $aadhar_card_no Aadhar card no.
     *
     * @return self
     */
    public function setAadharCardNo(string $aadhar_card_no)
    {
        $this->aadhar_card_no = $aadhar_card_no;
        return $this;

    }//end setAadharCardNo()


    /**
     * Get Pan_card_no
     *
     * @return string
     */
    public function getPanCardNo()
    {
        return $this->pan_card_no;

    }//end getPanCardNo()


    /**
     * Set Pan card no
     *
     * @param string $pan_card_no Pan card no.
     *
     * @return self
     */
    public function setPanCardNo(string $pan_card_no)
    {
        $this->pan_card_no = $pan_card_no;
        return $this;

    }//end setPanCardNo()


    /**
     * Get Account_no
     *
     * @return string
     */
    public function getAccountNo()
    {
        return $this->account_no;

    }//end getAccountNo()


    /**
     * Set Account no
     *
     * @param string $account_no Account no.
     *
     * @return self
     */
    public function setAccountNo(string $account_no)
    {
        $this->account_no = $account_no;
        return $this;

    }//end setAccountNo()


    /**
     * Get Ifsc_code
     *
     * @return string
     */
    public function getIfscCode()
    {
        return $this->ifsc_code;

    }//end getIfscCode()


    /**
     * Set Ifsc code
     *
     * @param string $ifsc_code Ifsc code.
     *
     * @return self
     */
    public function setIfscCode(string $ifsc_code)
    {
        $this->ifsc_code = $ifsc_code;
        return $this;

    }//end setIfscCode()


}//end class
