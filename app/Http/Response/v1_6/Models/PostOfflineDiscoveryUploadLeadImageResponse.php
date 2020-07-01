<?php
/**
 * PostOfflineDiscoveryUploadLeadImageResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostOfflineDiscoveryUploadLeadImageResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostOfflineDiscoveryUploadLeadImageResponse",
 * description="PostOfflineDiscoveryUploadLeadImageResponse",
 * )
 * // phpcs:enable
 */
class PostOfflineDiscoveryUploadLeadImageResponse extends ApiResponse
{

    /**
     * Lead Property Picture
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="picture",
	 *   type="string",
	 *   default="",
	 *   description="Lead Property Picture"
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
