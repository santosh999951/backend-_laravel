<?php
/**
 * Response Model for save Property Rating
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPropertyRatingResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPropertyRatingResponse",
 * description="Response Model for save Property Rating",
 * )
 * // phpcs:enable
 */
class PostPropertyRatingResponse extends ApiResponse
{

    /**
     * Message of Saved status
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Message of Saved status"
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
