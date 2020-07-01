<?php
/**
 * Response Model for send invoice of Booking Request
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostBookingRequestEmailinvoiceResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostBookingRequestEmailinvoiceResponse",
 * description="Response Model for send invoice of Booking Request",
 * )
 * // phpcs:enable
 */
class PostBookingRequestEmailinvoiceResponse extends ApiResponse
{

    /**
     * Message Eg. Email Sent Successfully
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message Eg. Email Sent Successfully"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


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
