<?php
/**
 * Response Model of User Logout Api
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostUserLogoutResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostUserLogoutResponse",
 * description="Response Model of User Logout Api",
 * )
 * // phpcs:enable
 */
class PostUserLogoutResponse extends ApiResponse
{

    /**
     * Logout Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Logout Message"
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
