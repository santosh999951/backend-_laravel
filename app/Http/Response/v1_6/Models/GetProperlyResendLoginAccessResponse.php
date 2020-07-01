<?php
/**
 * GetProperlyResendLoginAccessResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetProperlyResendLoginAccessResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetProperlyResendLoginAccessResponse",
 * description="GetProperlyResendLoginAccessResponse",
 * )
 * // phpcs:enable
 */
class GetProperlyResendLoginAccessResponse extends ApiResponse
{

    /**
     * Sms Sent
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="sms_sent",
	 *   type="integer",
	 *   default="0",
	 *   description="Sms Sent"
	 * )
     * // phpcs:enable
     */
    protected $sms_sent = 0;

    /**
     * User Contact
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="contact",
	 *   type="string",
	 *   default="",
	 *   description="User Contact"
	 * )
     * // phpcs:enable
     */
    protected $contact = '';

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
     * Get Sms_sent
     *
     * @return integer
     */
    public function getSmsSent()
    {
        return $this->sms_sent;

    }//end getSmsSent()


    /**
     * Set Sms sent
     *
     * @param integer $sms_sent Sms sent.
     *
     * @return self
     */
    public function setSmsSent(int $sms_sent)
    {
        $this->sms_sent = $sms_sent;
        return $this;

    }//end setSmsSent()


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
