<?php
/**
 * GetHostPayoutsResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetHostPayoutsResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetHostPayoutsResponse",
 * description="GetHostPayoutsResponse",
 * )
 * // phpcs:enable
 */
class GetHostPayoutsResponse extends ApiResponse
{

    /**
     * Host Payout History
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="payout_history",
	 *   type="array",
	 *   default="[]",
	 *   description="Host Payout History",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="booking_requests_id",
	 *       type="string",
	 *       default="",
	 *       description="Booking Requests Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_amount",
	 *       type="string",
	 *       default="",
	 *       description="Booking Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="settled_amount",
	 *       type="string",
	 *       default="",
	 *       description="Settled Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="pending_amount",
	 *       type="string",
	 *       default="",
	 *       description="Pending Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="settlement_history",
	 *       type="array",
	 *       default="[]",
	 *       description="Property Settlement History",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="date",
	 *           type="string",
	 *           default="",
	 *           description="Property Date"
	 *         ),
	 *         @SWG\Property(
	 *           property="utr",
	 *           type="string",
	 *           default="",
	 *           description="Property Utr"
	 *         ),
	 *         @SWG\Property(
	 *           property="amount",
	 *           type="string",
	 *           default="",
	 *           description="Property Amount"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_date",
	 *       type="string",
	 *       default="",
	 *       description="Property Booking Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="checkin_date",
	 *       type="string",
	 *       default="",
	 *       description="Checkin Date"
	 *     ),
	 *     @SWG\Property(
	 *       property="checkin_formatted",
	 *       type="string",
	 *       default="",
	 *       description="Checkin Formatted"
	 *     ),
	 *     @SWG\Property(
	 *       property="booking_status",
	 *       type="object",
	 *       default="{}",
	 *       description="Booking Status",
	 *         @SWG\Property(
	 *           property="text",
	 *           type="string",
	 *           default="",
	 *           description="Booking Status Text"
	 *         ),
	 *         @SWG\Property(
	 *           property="class",
	 *           type="string",
	 *           default="",
	 *           description="Booking Status Class"
	 *         ),
	 *         @SWG\Property(
	 *           property="color_code",
	 *           type="string",
	 *           default="",
	 *           description="Booking Status Color Code"
	 *         ),
	 *         @SWG\Property(
	 *           property="status",
	 *           type="integer",
	 *           default="0",
	 *           description="Booking Status Id"
	 *         ),
	 *         @SWG\Property(
	 *           property="header_text",
	 *           type="string",
	 *           default="",
	 *           description="Booking Request Header Text"
	 *         )
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $payout_history = [];

    /**
     * Total Due Amount
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="due_amount",
	 *   type="string",
	 *   default="",
	 *   description="Total Due Amount"
	 * )
     * // phpcs:enable
     */
    protected $due_amount = '';

    /**
     * Property Total Count
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total_count",
	 *   type="integer",
	 *   default="0",
	 *   description="Property Total Count"
	 * )
     * // phpcs:enable
     */
    protected $total_count = 0;


    /**
     * Get Payout_history
     *
     * @return array
     */
    public function getPayoutHistory()
    {
        return $this->payout_history;

    }//end getPayoutHistory()


    /**
     * Set Payout history
     *
     * @param array $payout_history Payout history.
     *
     * @return self
     */
    public function setPayoutHistory(array $payout_history)
    {
        $this->payout_history = $payout_history;
        return $this;

    }//end setPayoutHistory()


    /**
     * Get Due_amount
     *
     * @return string
     */
    public function getDueAmount()
    {
        return $this->due_amount;

    }//end getDueAmount()


    /**
     * Set Due amount
     *
     * @param string $due_amount Due amount.
     *
     * @return self
     */
    public function setDueAmount(string $due_amount)
    {
        $this->due_amount = $due_amount;
        return $this;

    }//end setDueAmount()


    /**
     * Get Total_count
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->total_count;

    }//end getTotalCount()


    /**
     * Set Total count
     *
     * @param integer $total_count Total count.
     *
     * @return self
     */
    public function setTotalCount(int $total_count)
    {
        $this->total_count = $total_count;
        return $this;

    }//end setTotalCount()


}//end class
