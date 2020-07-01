<?php
/**
 * Response Model for Cancel Booking Request
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostBookingRequestCancelResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostBookingRequestCancelResponse",
 * description="Response Model for Cancel Booking Request",
 * )
 * // phpcs:enable
 */
class PostBookingRequestCancelResponse extends ApiResponse
{

    /**
     * Request Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="request_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Request Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $request_hash_id = '';

    /**
     * Request Status Eg. -5
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="request_status",
	 *   type="integer",
	 *   default="0",
	 *   description="Request Status Eg. -5"
	 * )
     * // phpcs:enable
     */
    protected $request_status = 0;

    /**
     * Message for popup Eg. Your request has been cancelled.
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message for popup Eg. Your request has been cancelled."
	 * )
     * // phpcs:enable
     */
    protected $message = '';


    /**
     * Get Request_hash_id
     *
     * @return string
     */
    public function getRequestHashId()
    {
        return $this->request_hash_id;

    }//end getRequestHashId()


    /**
     * Set Request hash id
     *
     * @param string $request_hash_id Request hash id.
     *
     * @return self
     */
    public function setRequestHashId(string $request_hash_id)
    {
        $this->request_hash_id = $request_hash_id;
        return $this;

    }//end setRequestHashId()


    /**
     * Get Request_status
     *
     * @return integer
     */
    public function getRequestStatus()
    {
        return $this->request_status;

    }//end getRequestStatus()


    /**
     * Set Request status
     *
     * @param integer $request_status Request status.
     *
     * @return self
     */
    public function setRequestStatus(int $request_status)
    {
        $this->request_status = $request_status;
        return $this;

    }//end setRequestStatus()


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
