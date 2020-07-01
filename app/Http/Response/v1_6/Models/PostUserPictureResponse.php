<?php
/**
 * Response for Update user profile api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostUserPictureResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostUserPictureResponse",
 * description="Response for Update user profile api",
 * )
 * // phpcs:enable
 */
class PostUserPictureResponse extends ApiResponse
{

    /**
     * Uploaded Image Url
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="picture",
	 *   type="string",
	 *   default="",
	 *   description="Uploaded Image Url"
	 * )
     * // phpcs:enable
     */
    protected $picture = '';

    /**
     * Upload Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Upload Message"
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
