<?php
/**
 * PostGenerateOtpResponse
 */

namespace App\Http\Response\v1_7\Models;

/**
 * Class PostGenerateOtpResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostGenerateOtpResponse",
 * description="PostGenerateOtpResponse",
 * )
 * // phpcs:enable
 */
class PostGenerateOtpResponse extends ApiResponse
{

    /**
     * Property Dial Code
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="dial_code",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Dial Code"
	 * )
     * // phpcs:enable
     */
    protected $dial_code = 0;

    /**
     * Property Contact
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="contact",
	 *   type="string",
	 *   default="",
	 *   description="Property Contact"
	 * )
     * // phpcs:enable
     */
    protected $contact = '';

    /**
     * Property Sms Sender Ids
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="sms_sender_ids",
	 *   type="string",
	 *   default="",
	 *   description="Property Sms Sender Ids"
	 * )
     * // phpcs:enable
     */
    protected $sms_sender_ids = '';


    /**
     * Get Dial_code
     *
     * @return integer
     */
    public function getDialCode()
    {
        return $this->dial_code;

    }//end getDialCode()


    /**
     * Set Dial code
     *
     * @param integer $dial_code Dial code.
     *
     * @return self
     */
    public function setDialCode(int $dial_code)
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
     * Get Sms_sender_ids
     *
     * @return string
     */
    public function getSmsSenderIds()
    {
        return $this->sms_sender_ids;

    }//end getSmsSenderIds()


    /**
     * Set Sms sender ids
     *
     * @param string $sms_sender_ids Sms sender ids.
     *
     * @return self
     */
    public function setSmsSenderIds(string $sms_sender_ids)
    {
        $this->sms_sender_ids = $sms_sender_ids;
        return $this;

    }//end setSmsSenderIds()


}//end class
