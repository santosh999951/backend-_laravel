<?php
/**
 * DeleteProperlyRemoveMemberResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class DeleteProperlyRemoveMemberResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="DeleteProperlyRemoveMemberResponse",
 * description="DeleteProperlyRemoveMemberResponse",
 * )
 * // phpcs:enable
 */
class DeleteProperlyRemoveMemberResponse extends ApiResponse
{

    /**
     * Remove Team Member Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Remove Team Member Message"
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
