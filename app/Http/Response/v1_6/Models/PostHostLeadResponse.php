<?php
/**
 * PostHostLeadResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostHostLeadResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostHostLeadResponse",
 * description="PostHostLeadResponse",
 * )
 * // phpcs:enable
 */
class PostHostLeadResponse extends ApiResponse
{

    /**
     * Property Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Property Message"
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
