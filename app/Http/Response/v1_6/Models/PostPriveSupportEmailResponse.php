<?php
/**
 * PostPriveSupportEmailResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPriveSupportEmailResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPriveSupportEmailResponse",
 * description="PostPriveSupportEmailResponse",
 * )
 * // phpcs:enable
 */
class PostPriveSupportEmailResponse extends ApiResponse
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
