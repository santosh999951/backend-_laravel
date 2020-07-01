<?php
/**
 * Response Model for save prive booking contact for calling.
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostPriveManagerContactTravellerResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostPriveManagerContactTravellerResponse",
 * description="Response Model for save prive booking contact for calling.",
 * )
 * // phpcs:enable
 */
class PostPriveManagerContactTravellerResponse extends ApiResponse
{

    /**
     * Booking Request Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="request_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Booking Request Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $request_hash_id = '';

    /**
     * Response contact
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="contact",
	 *   type="string",
	 *   default="",
	 *   description="Response contact"
	 * )
     * // phpcs:enable
     */
    protected $contact = '';

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
     * Get Request_hash_id
     *
     * @return string
     */
    public function getRequestHashId()
    {
        return $this->request_hash_id;

    }//end getRequestHashId()


    /**
     * Set Request hash id
     *
     * @param string $request_hash_id Request hash id.
     *
     * @return self
     */
    public function setRequestHashId(string $request_hash_id)
    {
        $this->request_hash_id = $request_hash_id;
        return $this;

    }//end setRequestHashId()


    /**
     * Get Contact
     *
     * @return string
     */
    public function getContact()
    {
        return $this->contact;

    }//end getContact()


    /**
     * Set Contact
     *
     * @param string $contact Contact.
     *
     * @return self
     */
    public function setContact(string $contact)
    {
        $this->contact = $contact;
        return $this;

    }//end setContact()


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
