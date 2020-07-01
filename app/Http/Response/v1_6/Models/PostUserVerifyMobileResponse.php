<?php
/**
 * Response model for User Mobile Vaerification
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostUserVerifyMobileResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostUserVerifyMobileResponse",
 * description="Response model for User Mobile Vaerification",
 * )
 * // phpcs:enable
 */
class PostUserVerifyMobileResponse extends ApiResponse
{

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
     * Contact Number
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="contact",
	 *   type="string",
	 *   default="",
	 *   description="Contact Number"
	 * )
     * // phpcs:enable
     */
    protected $contact = '';

    /**
     * Sms Sender Ids Eg. GHBOOK,GHHELP,GHVRFY,GSTHSR
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="sms_sender_ids",
	 *   type="string",
	 *   default="",
	 *   description="Sms Sender Ids Eg. GHBOOK,GHHELP,GHVRFY,GSTHSR"
	 * )
     * // phpcs:enable
     */
    protected $sms_sender_ids = '';

    /**
     * Response Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Response Message"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


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
