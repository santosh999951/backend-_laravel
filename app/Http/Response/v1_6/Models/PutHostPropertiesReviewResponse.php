<?php
/**
 * Response Model for save host reply on traveller review
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutHostPropertiesReviewResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutHostPropertiesReviewResponse",
 * description="Response Model for save host reply on traveller review",
 * )
 * // phpcs:enable
 */
class PutHostPropertiesReviewResponse extends ApiResponse
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
