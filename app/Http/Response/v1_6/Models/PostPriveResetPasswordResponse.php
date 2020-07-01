<?php
/**
 * PostPriveResetPasswordResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPriveResetPasswordResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPriveResetPasswordResponse",
 * description="PostPriveResetPasswordResponse",
 * )
 * // phpcs:enable
 */
class PostPriveResetPasswordResponse extends ApiResponse
{

    /**
     * Email Sent
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="email_sent",
	 *   type="integer",
	 *   default="0",
	 *   description="Email Sent"
	 * )
     * // phpcs:enable
     */
    protected $email_sent = 0;

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
