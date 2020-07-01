<?php
/**
 * PutProperlySuspendMemberResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutProperlySuspendMemberResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutProperlySuspendMemberResponse",
 * description="PutProperlySuspendMemberResponse",
 * )
 * // phpcs:enable
 */
class PutProperlySuspendMemberResponse extends ApiResponse
{

    /**
     * Properly Suspend Team Member Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Properly Suspend Team Member Message"
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
