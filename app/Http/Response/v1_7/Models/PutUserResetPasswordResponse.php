<?php
/**
 * Response Model for Update user password via OTP
 */

namespace App\Http\Response\v1_7\Models;

/**
 * Class PutUserResetPasswordResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutUserResetPasswordResponse",
 * description="Response Model for Update user password via OTP",
 * )
 * // phpcs:enable
 */
class PutUserResetPasswordResponse extends ApiResponse
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
