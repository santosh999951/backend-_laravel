<?php
/**
 * Response Model for match OTP for Update Contact Number
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutUserVerifyMobileResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutUserVerifyMobileResponse",
 * description="Response Model for match OTP for Update Contact Number",
 * )
 * // phpcs:enable
 */
class PutUserVerifyMobileResponse extends ApiResponse
{

    /**
     * Dial Code
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="dial_code",
	 *   type="string",
	 *   default="",
	 *   description="Dial Code"
	 * )
     * // phpcs:enable
     */
    protected $dial_code = '';

    /**
     * Contact Number
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="contact_number",
	 *   type="string",
	 *   default="",
	 *   description="Contact Number"
	 * )
     * // phpcs:enable
     */
    protected $contact_number = '';

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
     * Get Dial_code
     *
     * @return string
     */
    public function getDialCode()
    {
        return $this->dial_code;

    }//end getDialCode()


    /**
     * Set Dial code
     *
     * @param string $dial_code Dial code.
     *
     * @return self
     */
    public function setDialCode(string $dial_code)
    {
        $this->dial_code = $dial_code;
        return $this;

    }//end setDialCode()


    /**
     * Get Contact_number
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contact_number;

    }//end getContactNumber()


    /**
     * Set Contact number
     *
     * @param string $contact_number Contact number.
     *
     * @return self
     */
    public function setContactNumber(string $contact_number)
    {
        $this->contact_number = $contact_number;
        return $this;

    }//end setContactNumber()


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
