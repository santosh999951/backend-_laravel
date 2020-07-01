<?php
/**
 * Response Model for Update new password via email
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutUserPasswordResetResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutUserPasswordResetResponse",
 * description="Response Model for Update new password via email",
 * )
 * // phpcs:enable
 */
class PutUserPasswordResetResponse extends ApiResponse
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
