<?php
/**
 * GetProperlyExpenseResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetProperlyExpenseResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetProperlyExpenseResponse",
 * description="GetProperlyExpenseResponse",
 * )
 * // phpcs:enable
 */
class GetProperlyExpenseResponse extends ApiResponse
{

    /**
     * Property Expense
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="expense",
	 *   type="array",
	 *   default="[]",
	 *   description="Property Expense",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="expense_hash_id",
	 *       type="integer",
	 *       default="0",
	 *       description="Expense Hash Id"
	 *     ),
	 *     @SWG\Property(
	 *       property="expense_name",
	 *       type="string",
	 *       default="",
	 *       description="Expense Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="type",
	 *       type="string",
	 *       default="",
	 *       description="Type"
	 *     ),
	 *     @SWG\Property(
	 *       property="added_on",
	 *       type="string",
	 *       default="",
	 *       description="Added On"
	 *     ),
	 *     @SWG\Property(
	 *       property="basic_amount",
	 *       type="number",
	 *       default="",
	 *       description="Basic Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="nights_booked",
	 *       type="number",
	 *       default="0",
	 *       description="Nights Booked"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_amount",
	 *       type="string",
	 *       default="",
	 *       description="Total Amount"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $expense = [];


    /**
     * Get Expense
     *
     * @return array
     */
    public function getExpense()
    {
        return $this->expense;

    }//end getExpense()


    /**
     * Set Expense
     *
     * @param array $expense Expense.
     *
     * @return self
     */
    public function setExpense(array $expense)
    {
        $this->expense = $expense;
        return $this;

    }//end setExpense()


}//end class
