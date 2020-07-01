<?php
/**
 * Update Property Api Response Models
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutPropertyResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutPropertyResponse",
 * description="Update Property Api Response Models",
 * )
 * // phpcs:enable
 */
class PutPropertyResponse extends ApiResponse
{

    /**
     * Property Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Property Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $property_hash_id = '';

    /**
     * Property Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Property Message"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


    /**
     * Get Property_hash_id
     *
     * @return string
     */
    public function getPropertyHashId()
    {
        return $this->property_hash_id;

    }//end getPropertyHashId()


    /**
     * Set Property hash id
     *
     * @param string $property_hash_id Property hash id.
     *
     * @return self
     */
    public function setPropertyHashId(string $property_hash_id)
    {
        $this->property_hash_id = $property_hash_id;
        return $this;

    }//end setPropertyHashId()


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
