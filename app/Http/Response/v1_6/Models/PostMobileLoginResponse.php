<?php
/**
 * PostMobileLoginResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostMobileLoginResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostMobileLoginResponse",
 * description="PostMobileLoginResponse",
 * )
 * // phpcs:enable
 */
class PostMobileLoginResponse extends ApiResponse
{

    /**
     * Dial Code
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="dial_code",
	 *   type="integer",
	 *   default="0",
	 *   description="Dial Code"
	 * )
     * // phpcs:enable
     */
    protected $dial_code = 0;

    /**
     * Contact
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="contact",
	 *   type="integer",
	 *   default="0",
	 *   description="Contact"
	 * )
     * // phpcs:enable
     */
    protected $contact = 0;

    /**
     * Sms Sender Ids
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="sms_sender_ids",
	 *   type="string",
	 *   default="",
	 *   description="Sms Sender Ids"
	 * )
     * // phpcs:enable
     */
    protected $sms_sender_ids = '';

    /**
     * Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


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
     * @return integer
     */
    public function getContact()
    {
        return $this->contact;

    }//end getContact()


    /**
     * Set Contact
     *
     * @param integer $contact Contact.
     *
     * @return self
     */
    public function setContact(int $contact)
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
