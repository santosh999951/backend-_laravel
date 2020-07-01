<?php
/**
 * Response Models for Adding host bank details
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PostHostPaymentPreferencesResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PostHostPaymentPreferencesResponse",
 * description="Response Models for Adding host bank details",
 * )
 * // phpcs:enable
 */
class PostHostPaymentPreferencesResponse extends ApiResponse
{

    /**
     * Bank Details
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="bank_details",
	 *   type="object",
	 *   default="{}",
	 *   description="Bank Details",
	 *     @SWG\Property(
	 *       property="payee_name",
	 *       type="string",
	 *       default="",
	 *       description="Payee Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="bank_name",
	 *       type="string",
	 *       default="",
	 *       description="Bank Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="branch_name",
	 *       type="string",
	 *       default="",
	 *       description="Branch Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="account_number",
	 *       type="string",
	 *       default="",
	 *       description="Account Number"
	 *     ),
	 *     @SWG\Property(
	 *       property="ifsc_code",
	 *       type="string",
	 *       default="",
	 *       description="IFSC Code"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $bank_details = [];

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
     * Get Bank_details
     *
     * @return object
     */
    public function getBankDetails()
    {
        return (empty($this->bank_details) === false) ? $this->bank_details : new \stdClass;

    }//end getBankDetails()


    /**
     * Set Bank details
     *
     * @param array $bank_details Bank details.
     *
     * @return self
     */
    public function setBankDetails(array $bank_details)
    {
        $this->bank_details = $bank_details;
        return $this;

    }//end setBankDetails()


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
