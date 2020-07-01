<?php
/**
 * Response Model for Update Property enabled status
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutHostPropertiesStatusResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutHostPropertiesStatusResponse",
 * description="Response Model for Update Property enabled status",
 * )
 * // phpcs:enable
 */
class PutHostPropertiesStatusResponse extends ApiResponse
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
     * Property Enabled Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_status",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Enabled Status"
	 * )
     * // phpcs:enable
     */
    protected $property_status = 0;

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
     * Get Property_status
     *
     * @return integer
     */
    public function getPropertyStatus()
    {
        return $this->property_status;

    }//end getPropertyStatus()


    /**
     * Set Property status
     *
     * @param integer $property_status Property status.
     *
     * @return self
     */
    public function setPropertyStatus(int $property_status)
    {
        $this->property_status = $property_status;
        return $this;

    }//end setPropertyStatus()


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
