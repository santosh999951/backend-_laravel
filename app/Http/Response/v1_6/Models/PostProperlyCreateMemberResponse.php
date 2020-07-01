<?php
/**
 * PostProperlyCreateMemberResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostProperlyCreateMemberResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostProperlyCreateMemberResponse",
 * description="PostProperlyCreateMemberResponse",
 * )
 * // phpcs:enable
 */
class PostProperlyCreateMemberResponse extends ApiResponse
{

    /**
     * Create team member message.
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="create team member message"
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
