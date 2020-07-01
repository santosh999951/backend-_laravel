<?php
/**
 * Response Model for save prive manager booking status
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPriveBookingCheckedinResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPriveBookingCheckedinResponse",
 * description="Response Model for save prive manager booking status",
 * )
 * // phpcs:enable
 */
class PostPriveBookingCheckedinResponse extends ApiResponse
{

    /**
     * Booking Request Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="request_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Booking Request Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $request_hash_id = '';

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
