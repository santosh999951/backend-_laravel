<?php
/**
 * Response Model for save review Image
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPropertyReviewImageResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPropertyReviewImageResponse",
 * description="Response Model for save review Image",
 * )
 * // phpcs:enable
 */
class PostPropertyReviewImageResponse extends ApiResponse
{

    /**
     * Image name
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="picture",
	 *   type="string",
	 *   default="",
	 *   description="Image name"
	 * )
     * // phpcs:enable
     */
    protected $picture = '';

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
     * Get Picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;

    }//end getPicture()


    /**
     * Set Picture
     *
     * @param string $picture Picture.
     *
     * @return self
     */
    public function setPicture(string $picture)
    {
        $this->picture = $picture;
        return $this;

    }//end setPicture()


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
