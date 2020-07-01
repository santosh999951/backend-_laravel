<?php
/**
 * PostPriveBookingResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPriveBookingResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPriveBookingResponse",
 * description="PostPriveBookingResponse",
 * )
 * // phpcs:enable
 */
class PostPriveBookingResponse extends ApiResponse
{

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
     * Request Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="request_id",
	 *   type="string",
	 *   default="",
	 *   description="Request Id"
	 * )
     * // phpcs:enable
     */
    protected $request_id = '';


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


}//end class
