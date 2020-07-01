<?php
/**
 * Response Model for Resend Booking Request
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostBookingRequestResendResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostBookingRequestResendResponse",
 * description="Response Model for Resend Booking Request",
 * )
 * // phpcs:enable
 */
class PostBookingRequestResendResponse extends ApiResponse
{

    /**
     * Status for Resend Booking Request
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="valid",
	 *   type="integer",
	 *   default="0",
	 *   description="Status for Resend Booking Request"
	 * )
     * // phpcs:enable
     */
    protected $valid = 0;

    /**
     * Message Eg. Request Created
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message Eg. Request Created"
	 * )
     * // phpcs:enable
     */
    protected $message = '';

    /**
     * Message Code Eg. request_created
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="msg_code",
	 *   type="string",
	 *   default="",
	 *   description="Message Code Eg. request_created"
	 * )
     * // phpcs:enable
     */
    protected $msg_code = '';

    /**
     * Booking Status Id
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_status",
	 *   type="integer",
	 *   default="0",
	 *   description="Booking Status Id"
	 * )
     * // phpcs:enable
     */
    protected $booking_status = 0;

    /**
     * Request Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="request_id",
	 *   type="string",
	 *   default="",
	 *   description="Request Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $request_id = '';

    /**
     * Instant Bookable status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="instant_book",
	 *   type="integer",
	 *   default="0",
	 *   description="Instant Bookable status"
	 * )
     * // phpcs:enable
     */
    protected $instant_book = 0;

    /**
     * Payment Url
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="payment_url",
	 *   type="string",
	 *   default="",
	 *   description="Payment Url"
	 * )
     * // phpcs:enable
     */
    protected $payment_url = '';

    /**
     * Payment Gateway Method Eg. web, sdk
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="payment_gateway_method",
	 *   type="string",
	 *   default="",
	 *   description="Payment Gateway Method Eg. web, sdk"
	 * )
     * // phpcs:enable
     */
    protected $payment_gateway_method = '';


    /**
     * Get Valid
     *
     * @return integer
     */
    public function getValid()
    {
        return $this->valid;

    }//end getValid()


    /**
     * Set Valid
     *
     * @param integer $valid Valid.
     *
     * @return self
     */
    public function setValid(int $valid)
    {
        $this->valid = $valid;
        return $this;

    }//end setValid()


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
     * Get Msg_code
     *
     * @return string
     */
    public function getMsgCode()
    {
        return $this->msg_code;

    }//end getMsgCode()


    /**
     * Set Msg code
     *
     * @param string $msg_code Msg code.
     *
     * @return self
     */
    public function setMsgCode(string $msg_code)
    {
        $this->msg_code = $msg_code;
        return $this;

    }//end setMsgCode()


    /**
     * Get Booking_status
     *
     * @return integer
     */
    public function getBookingStatus()
    {
        return $this->booking_status;

    }//end getBookingStatus()


    /**
     * Set Booking status
     *
     * @param integer $booking_status Booking status.
     *
     * @return self
     */
    public function setBookingStatus(int $booking_status)
    {
        $this->booking_status = $booking_status;
        return $this;

    }//end setBookingStatus()


    /**
     * Get Request_id
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->request_id;

    }//end getRequestId()


    /**
     * Set Request id
     *
     * @param string $request_id Request id.
     *
     * @return self
     */
    public function setRequestId(string $request_id)
    {
        $this->request_id = $request_id;
        return $this;

    }//end setRequestId()


    /**
     * Get Instant_book
     *
     * @return integer
     */
    public function getInstantBook()
    {
        return $this->instant_book;

    }//end getInstantBook()


    /**
     * Set Instant book
     *
     * @param integer $instant_book Instant book.
     *
     * @return self
     */
    public function setInstantBook(int $instant_book)
    {
        $this->instant_book = $instant_book;
        return $this;

    }//end setInstantBook()


    /**
     * Get Payment_url
     *
     * @return string
     */
    public function getPaymentUrl()
    {
        return $this->payment_url;

    }//end getPaymentUrl()


    /**
     * Set Payment url
     *
     * @param string $payment_url Payment url.
     *
     * @return self
     */
    public function setPaymentUrl(string $payment_url)
    {
        $this->payment_url = $payment_url;
        return $this;

    }//end setPaymentUrl()


    /**
     * Get Payment_gateway_method
     *
     * @return string
     */
    public function getPaymentGatewayMethod()
    {
        return $this->payment_gateway_method;

    }//end getPaymentGatewayMethod()


    /**
     * Set Payment gateway method
     *
     * @param string $payment_gateway_method Payment gateway method.
     *
     * @return self
     */
    public function setPaymentGatewayMethod(string $payment_gateway_method)
    {
        $this->payment_gateway_method = $payment_gateway_method;
        return $this;

    }//end setPaymentGatewayMethod()


}//end class
