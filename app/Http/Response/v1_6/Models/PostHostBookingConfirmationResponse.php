<?php
/**
 * Response Model for save host booking Availability
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostHostBookingConfirmationResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostHostBookingConfirmationResponse",
 * description="Response Model for save host booking Availability",
 * )
 * // phpcs:enable
 */
class PostHostBookingConfirmationResponse extends ApiResponse
{

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
     * Confirm Text
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="confirm_text",
	 *   type="string",
	 *   default="",
	 *   description="Confirm Text"
	 * )
     * // phpcs:enable
     */
    protected $confirm_text = '';


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


    /**
     * Get Confirm_text
     *
     * @return string
     */
    public function getConfirmText()
    {
        return $this->confirm_text;

    }//end getConfirmText()


    /**
     * Set Confirm text
     *
     * @param string $confirm_text Confirm text.
     *
     * @return self
     */
    public function setConfirmText(string $confirm_text)
    {
        $this->confirm_text = $confirm_text;
        return $this;

    }//end setConfirmText()


}//end class
