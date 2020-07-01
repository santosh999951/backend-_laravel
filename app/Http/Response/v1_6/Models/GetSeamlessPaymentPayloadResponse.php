<?php
/**
 * GetSeamlessPaymentPayloadResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetSeamlessPaymentPayloadResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetSeamlessPaymentPayloadResponse",
 * description="GetSeamlessPaymentPayloadResponse",
 * )
 * // phpcs:enable
 */
class GetSeamlessPaymentPayloadResponse extends ApiResponse
{

    /**
     * Payment Payload
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="payload",
	 *   type="object",
	 *   default="{}",
	 *   description="Payment Payload",
	 *     @SWG\Property(
	 *       property="action",
	 *       type="string",
	 *       default="",
	 *       description="Payment Action"
	 *     ),
	 *     @SWG\Property(
	 *       property="key",
	 *       type="string",
	 *       default="",
	 *       description="Payload Key"
	 *     ),
	 *     @SWG\Property(
	 *       property="hash",
	 *       type="string",
	 *       default="",
	 *       description="Hash"
	 *     ),
	 *     @SWG\Property(
	 *       property="txnid",
	 *       type="string",
	 *       default="",
	 *       description="Txnid of payment"
	 *     ),
	 *     @SWG\Property(
	 *       property="amount",
	 *       type="float",
	 *       default="0.0",
	 *       description="Payment Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="firstname",
	 *       type="string",
	 *       default="",
	 *       description="Firstname"
	 *     ),
	 *     @SWG\Property(
	 *       property="lastname",
	 *       type="string",
	 *       default="",
	 *       description="Lastname"
	 *     ),
	 *     @SWG\Property(
	 *       property="email",
	 *       type="string",
	 *       default="",
	 *       description="Email"
	 *     ),
	 *     @SWG\Property(
	 *       property="phone",
	 *       type="string",
	 *       default="",
	 *       description="Phone"
	 *     ),
	 *     @SWG\Property(
	 *       property="productinfo",
	 *       type="integer",
	 *       default="0",
	 *       description="Productinfo"
	 *     ),
	 *     @SWG\Property(
	 *       property="surl",
	 *       type="string",
	 *       default="",
	 *       description="Surl"
	 *     ),
	 *     @SWG\Property(
	 *       property="furl",
	 *       type="string",
	 *       default="",
	 *       description="Furl"
	 *     ),
	 *     @SWG\Property(
	 *       property="drop_category",
	 *       type="string",
	 *       default="",
	 *       description="Drop Category"
	 *     ),
	 *     @SWG\Property(
	 *       property="bankcode",
	 *       type="string",
	 *       default="",
	 *       description="Bankcode"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $payload = [];

    /**
     * Extra Payload
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="extra_payload",
	 *   type="array",
	 *   default="[]",
	 *   description="Extra Payload",
	 *   @SWG\Items(
	 *     type="object",
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $extra_payload = [];

    /**
     * Property Booking Info
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="booking_info",
	 *   type="object",
	 *   default="{}",
	 *   description="Property Booking Info",
	 *     @SWG\Property(
	 *       property="booking_status",
	 *       type="integer",
	 *       default="0",
	 *       description="Property Booking Status"
	 *     ),
	 *     @SWG\Property(
	 *       property="amount",
	 *       type="float",
	 *       default="0.0",
	 *       description="Property Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="currency",
	 *       type="string",
	 *       default="",
	 *       description="Property Currency"
	 *     ),
	 *     @SWG\Property(
	 *       property="gateway",
	 *       type="string",
	 *       default="",
	 *       description="Property Gateway"
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $booking_info = [];


    /**
     * Get Payload
     *
     * @return object
     */
    public function getPayload()
    {
        return (empty($this->payload) === false) ? $this->payload : new \stdClass;

    }//end getPayload()


    /**
     * Set Payload
     *
     * @param array $payload Payload.
     *
     * @return self
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
        return $this;

    }//end setPayload()


    /**
     * Get Extra_payload
     *
     * @return array
     */
    public function getExtraPayload()
    {
        return $this->extra_payload;

    }//end getExtraPayload()


    /**
     * Set Extra payload
     *
     * @param array $extra_payload Extra payload.
     *
     * @return self
     */
    public function setExtraPayload(array $extra_payload)
    {
        $this->extra_payload = $extra_payload;
        return $this;

    }//end setExtraPayload()


    /**
     * Get Booking_info
     *
     * @return object
     */
    public function getBookingInfo()
    {
        return (empty($this->booking_info) === false) ? $this->booking_info : new \stdClass;

    }//end getBookingInfo()


    /**
     * Set Booking info
     *
     * @param array $booking_info Booking info.
     *
     * @return self
     */
    public function setBookingInfo(array $booking_info)
    {
        $this->booking_info = $booking_info;
        return $this;

    }//end setBookingInfo()


}//end class
