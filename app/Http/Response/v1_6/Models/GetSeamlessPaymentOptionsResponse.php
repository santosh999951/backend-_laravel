<?php
/**
 * GetSeamlessPaymentOptionsResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetSeamlessPaymentOptionsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetSeamlessPaymentOptionsResponse",
 * description="GetSeamlessPaymentOptionsResponse",
 * )
 * // phpcs:enable
 */
class GetSeamlessPaymentOptionsResponse extends ApiResponse
{

    /**
     * Payment Action
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="action",
	 *   type="string",
	 *   default="",
	 *   description="Payment Action"
	 * )
     * // phpcs:enable
     */
    protected $action = '';

    /**
     * Reason
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="reason",
	 *   type="string",
	 *   default="",
	 *   description="Reason"
	 * )
     * // phpcs:enable
     */
    protected $reason = '';

    /**
     * Booking Status
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_status",
	 *   type="integer",
	 *   default="0",
	 *   description="Booking Status"
	 * )
     * // phpcs:enable
     */
    protected $booking_status = 0;

    /**
     * Payable Amount
     *
     * @var float
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="amount",
	 *   type="float",
	 *   default="0.0",
	 *   description="Payable Amount"
	 * )
     * // phpcs:enable
     */
    protected $amount = 0.0;

    /**
     * Payment Currency
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="currency",
	 *   type="object",
	 *   default="{}",
	 *   description="Payment Currency",
	 *     @SWG\Property(
	 *       property="webicon",
	 *       type="string",
	 *       default="",
	 *       description="Webicon"
	 *     ),
	 *     @SWG\Property(
	 *       property="non-webicon",
	 *       type="string",
	 *       default="",
	 *       description="Non-webicon"
	 *     ),
	 *     @SWG\Property(
	 *       property="iso_code",
	 *       type="string",
	 *       default="",
	 *       description="Iso Code"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $currency = [];

    /**
     * Payment Method
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="payment_method",
	 *   type="string",
	 *   default="",
	 *   description="Payment Method"
	 * )
     * // phpcs:enable
     */
    protected $payment_method = '';

    /**
     * Payment Options
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="options",
	 *   type="object",
	 *   default="{}",
	 *   description="Payment Options",
	 *     @SWG\Property(
	 *       property="debit_card",
	 *       type="array",
	 *       default="[]",
	 *       description="Debit Card",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Card Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="code",
	 *           type="string",
	 *           default="",
	 *           description="Card Code"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="credit_card",
	 *       type="array",
	 *       default="[]",
	 *       description="Credit Card",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Card Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="code",
	 *           type="string",
	 *           default="",
	 *           description="Card Code"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="netbanking",
	 *       type="array",
	 *       default="[]",
	 *       description="Netbanking",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="name",
	 *           type="string",
	 *           default="",
	 *           description="Netbanking Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="code",
	 *           type="string",
	 *           default="",
	 *           description="Netbanking Code"
	 *         )
	 *       )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $options = [];


    /**
     * Get Action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;

    }//end getAction()


    /**
     * Set Action
     *
     * @param string $action Action.
     *
     * @return self
     */
    public function setAction(string $action)
    {
        $this->action = $action;
        return $this;

    }//end setAction()


    /**
     * Get Reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;

    }//end getReason()


    /**
     * Set Reason
     *
     * @param string $reason Reason.
     *
     * @return self
     */
    public function setReason(string $reason)
    {
        $this->reason = $reason;
        return $this;

    }//end setReason()


    /**
     * Get Booking_status
     *
     * @return integer
     */
    public function getBookingStatus()
    {
        return $this->booking_status;

    }//end getBookingStatus()


    /**
     * Set Booking status
     *
     * @param integer $booking_status Booking status.
     *
     * @return self
     */
    public function setBookingStatus(int $booking_status)
    {
        $this->booking_status = $booking_status;
        return $this;

    }//end setBookingStatus()


    /**
     * Get Amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;

    }//end getAmount()


    /**
     * Set Amount
     *
     * @param float $amount Amount.
     *
     * @return self
     */
    public function setAmount(float $amount)
    {
        $this->amount = $amount;
        return $this;

    }//end setAmount()


    /**
     * Get Currency
     *
     * @return object
     */
    public function getCurrency()
    {
        return (empty($this->currency) === false) ? $this->currency : new \stdClass;

    }//end getCurrency()


    /**
     * Set Currency
     *
     * @param array $currency Currency.
     *
     * @return self
     */
    public function setCurrency(array $currency)
    {
        $this->currency = $currency;
        return $this;

    }//end setCurrency()


    /**
     * Get Payment_method
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;

    }//end getPaymentMethod()


    /**
     * Set Payment method
     *
     * @param string $payment_method Payment method.
     *
     * @return self
     */
    public function setPaymentMethod(string $payment_method)
    {
        $this->payment_method = $payment_method;
        return $this;

    }//end setPaymentMethod()


    /**
     * Get Options
     *
     * @return object
     */
    public function getOptions()
    {
        return (empty($this->options) === false) ? $this->options : new \stdClass;

    }//end getOptions()


    /**
     * Set Options
     *
     * @param array $options Options.
     *
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;

    }//end setOptions()


}//end class
