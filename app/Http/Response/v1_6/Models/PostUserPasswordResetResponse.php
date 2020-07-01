<?php
/**
 * Response Model for user password reset
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostUserPasswordResetResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostUserPasswordResetResponse",
 * description="Response Model for user password reset",
 * )
 * // phpcs:enable
 */
class PostUserPasswordResetResponse extends ApiResponse
{

    /**
     * Email Sent Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="email_sent",
	 *   type="integer",
	 *   default="0",
	 *   description="Email Sent Status"
	 * )
     * // phpcs:enable
     */
    protected $email_sent = 0;

    /**
     * Sms Sent Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="sms_sent",
	 *   type="integer",
	 *   default="0",
	 *   description="Sms Sent Status"
	 * )
     * // phpcs:enable
     */
    protected $sms_sent = 0;

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
     * Get Email_sent
     *
     * @return integer
     */
    public function getEmailSent()
    {
        return $this->email_sent;

    }//end getEmailSent()


    /**
     * Set Email sent
     *
     * @param integer $email_sent Email sent.
     *
     * @return self
     */
    public function setEmailSent(int $email_sent)
    {
        $this->email_sent = $email_sent;
        return $this;

    }//end setEmailSent()


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
