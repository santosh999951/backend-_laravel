<?php
/**
 * PostPriveLoginViaResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPriveLoginViaResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPriveLoginViaResponse",
 * description="PostPriveLoginViaResponse",
 * )
 * // phpcs:enable
 */
class PostPriveLoginViaResponse extends ApiResponse
{

    /**
     * Type
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="type",
	 *   type="string",
	 *   default="",
	 *   description="Type"
	 * )
     * // phpcs:enable
     */
    protected $type = '';

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
     * Sms Sender Ids
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="sms_sender_ids",
	 *   type="string",
	 *   default="",
	 *   description="Sms Sender Ids"
	 * )
     * // phpcs:enable
     */
    protected $sms_sender_ids = '';


    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;

    }//end getType()


    /**
     * Set Type
     *
     * @param string $type Type.
     *
     * @return self
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;

    }//end setType()


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


    /**
     * Get Sms_sender_ids
     *
     * @return string
     */
    public function getSmsSenderIds()
    {
        return $this->sms_sender_ids;

    }//end getSmsSenderIds()


    /**
     * Set Sms sender ids
     *
     * @param string $sms_sender_ids Sms sender ids.
     *
     * @return self
     */
    public function setSmsSenderIds(string $sms_sender_ids)
    {
        $this->sms_sender_ids = $sms_sender_ids;
        return $this;

    }//end setSmsSenderIds()


}//end class
