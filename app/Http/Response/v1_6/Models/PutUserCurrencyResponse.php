<?php
/**
 * Response Model for Update User Currency
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutUserCurrencyResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutUserCurrencyResponse",
 * description="Response Model for Update User Currency",
 * )
 * // phpcs:enable
 */
class PutUserCurrencyResponse extends ApiResponse
{

    /**
     * Currency
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="currency",
	 *   type="string",
	 *   default="",
	 *   description="Currency"
	 * )
     * // phpcs:enable
     */
    protected $currency = '';

    /**
     * Update Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Update Message"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


    /**
     * Get Currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;

    }//end getCurrency()


    /**
     * Set Currency
     *
     * @param string $currency Currency.
     *
     * @return self
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
        return $this;

    }//end setCurrency()


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
