<?php
/**
 * PostPropertyCloneResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPropertyCloneResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPropertyCloneResponse",
 * description="PostPropertyCloneResponse",
 * )
 * // phpcs:enable
 */
class PostPropertyCloneResponse extends ApiResponse
{

    /**
     * Cloned Property Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="cloned_property_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Cloned Property Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $cloned_property_hash_id = '';

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
     * Get Cloned_property_hash_id
     *
     * @return string
     */
    public function getClonedPropertyHashId()
    {
        return $this->cloned_property_hash_id;

    }//end getClonedPropertyHashId()


    /**
     * Set Cloned property hash id
     *
     * @param string $cloned_property_hash_id Cloned property hash id.
     *
     * @return self
     */
    public function setClonedPropertyHashId(string $cloned_property_hash_id)
    {
        $this->cloned_property_hash_id = $cloned_property_hash_id;
        return $this;

    }//end setClonedPropertyHashId()


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
