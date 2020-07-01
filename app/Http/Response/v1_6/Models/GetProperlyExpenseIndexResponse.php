<?php
/**
 * GetProperlyExpenseIndexResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetProperlyExpenseIndexResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetProperlyExpenseIndexResponse",
 * description="GetProperlyExpenseIndexResponse",
 * )
 * // phpcs:enable
 */
class GetProperlyExpenseIndexResponse extends ApiResponse
{

    /**
     * Properly Expense
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="expense",
	 *   type="array",
	 *   default="[]",
	 *   description="Expense",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="expense_hash_id",
	 *       type="string",
	 *       default="",
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
	 *       type="string",
	 *       default="",
	 *       description="Basic Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="nights_booked",
	 *       type="string",
	 *       default="",
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
     * Properly Expense Distrubution
     *
     * @var object
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="expense_distrubution",
	 *   type="object",
	 *   default="{}",
	 *   description="Expense Distrubution",
	 *     @SWG\Property(
	 *       property="distribution",
	 *       type="array",
	 *       default="[]",
	 *       description="Distribution",
	 *       @SWG\Items(
	 *         type="object",
	 *         @SWG\Property(
	 *           property="percentage",
	 *           type="string",
	 *           default="",
	 *           description="Percentage"
	 *         ),
	 *         @SWG\Property(
	 *           property="expense_name",
	 *           type="string",
	 *           default="",
	 *           description="Expense Name"
	 *         ),
	 *         @SWG\Property(
	 *           property="unformatted_amount",
	 *           type="string",
	 *           default="",
	 *           description="Unformatted Amount"
	 *         ),
	 *         @SWG\Property(
	 *           property="amount",
	 *           type="string",
	 *           default="",
	 *           description="Amount"
	 *         )
	 *       )
	 *     ),
	 *     @SWG\Property(
	 *       property="pl",
	 *       type="object",
	 *       default="{}",
	 *       description="Pl",
	 *         @SWG\Property(
	 *           property="payable_amount",
	 *           type="string",
	 *           default="",
	 *           description="Payable Amount"
	 *         ),
	 *         @SWG\Property(
	 *           property="ota_fees",
	 *           type="string",
	 *           default="",
	 *           description="Ota Fees"
	 *         ),
	 *         @SWG\Property(
	 *           property="host_fee",
	 *           type="string",
	 *           default="",
	 *           description="Host Fee"
	 *         ),
	 *         @SWG\Property(
	 *           property="expenses",
	 *           type="string",
	 *           default="",
	 *           description="Expenses"
	 *         ),
	 *         @SWG\Property(
	 *           property="pl_formatted",
	 *           type="string",
	 *           default="",
	 *           description="Pl Formatted"
	 *         ),
	 *         @SWG\Property(
	 *           property="accordance_to_last_pl",
	 *           type="object",
	 *           default="{}",
	 *           description="Accordance To Last Pl",
	 *             @SWG\Property(
	 *               property="percentage",
	 *               type="float",
	 *               default="0.0",
	 *               description="Percentage"
	 *             ),
	 *             @SWG\Property(
	 *               property="type",
	 *               type="string",
	 *               default="",
	 *               description="Type"
	 *             )
	 *         )
	 *     )
	 * )
     * // phpcs:enable
     */
    protected $expense_distrubution = [];

    /**
     * Properly Expense Type
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="expense_type",
	 *   type="array",
	 *   default="[]",
	 *   description="Expense Type",
	 *   @SWG\Items(
	 *     type="object",
	 *     @SWG\Property(
	 *       property="name",
	 *       type="string",
	 *       default="",
	 *       description="Name"
	 *     ),
	 *     @SWG\Property(
	 *       property="type",
	 *       type="string",
	 *       default="",
	 *       description="Type"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $expense_type = [];

    /**
     * Properly Suggestions
     *
     * @var array
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="suggestions",
	 *   type="array",
	 *   default="[]",
	 *   description="Suggestions",
	 *   @SWG\Items(
	 *     type="object",
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
	 *       type="string",
	 *       default="",
	 *       description="Basic Amount"
	 *     ),
	 *     @SWG\Property(
	 *       property="nights_booked",
	 *       type="integer",
	 *       default="0",
	 *       description="Nights Booked"
	 *     ),
	 *     @SWG\Property(
	 *       property="total_amount",
	 *       type="integer",
	 *       default="0",
	 *       description="Total Amount"
	 *     )
	 *   )
	 * )
     * // phpcs:enable
     */
    protected $suggestions = [];

    /**
     * Property Title
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_title",
	 *   type="string",
	 *   default="",
	 *   description="Property Title"
	 * )
     * // phpcs:enable
     */
    protected $property_title = '';

    /**
     * Property Live Date
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_live_date",
	 *   type="string",
	 *   default="",
	 *   description="Property Live Date"
	 * )
     * // phpcs:enable
     */
    protected $property_live_date = '';

    /**
     * Property Total
     *
     * @var integer
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="total",
	 *   type="integer",
	 *   default="0",
	 *   description="Total"
	 * )
     * // phpcs:enable
     */
    protected $total = 0;


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


    /**
     * Get Expense_distrubution
     *
     * @return object
     */
    public function getExpenseDistrubution()
    {
        return (empty($this->expense_distrubution) === false) ? $this->expense_distrubution : new \stdClass;

    }//end getExpenseDistrubution()


    /**
     * Set Expense distrubution
     *
     * @param array $expense_distrubution Expense distrubution.
     *
     * @return self
     */
    public function setExpenseDistrubution(array $expense_distrubution)
    {
        $this->expense_distrubution = $expense_distrubution;
        return $this;

    }//end setExpenseDistrubution()


    /**
     * Get Expense_type
     *
     * @return array
     */
    public function getExpenseType()
    {
        return $this->expense_type;

    }//end getExpenseType()


    /**
     * Set Expense type
     *
     * @param array $expense_type Expense type.
     *
     * @return self
     */
    public function setExpenseType(array $expense_type)
    {
        $this->expense_type = $expense_type;
        return $this;

    }//end setExpenseType()


    /**
     * Get Suggestions
     *
     * @return array
     */
    public function getSuggestions()
    {
        return $this->suggestions;

    }//end getSuggestions()


    /**
     * Set Suggestions
     *
     * @param array $suggestions Suggestions.
     *
     * @return self
     */
    public function setSuggestions(array $suggestions)
    {
        $this->suggestions = $suggestions;
        return $this;

    }//end setSuggestions()


    /**
     * Get Property_title
     *
     * @return string
     */
    public function getPropertyTitle()
    {
        return $this->property_title;

    }//end getPropertyTitle()


    /**
     * Set Property title
     *
     * @param string $property_title Property title.
     *
     * @return self
     */
    public function setPropertyTitle(string $property_title)
    {
        $this->property_title = $property_title;
        return $this;

    }//end setPropertyTitle()


    /**
     * Get Property_live_date
     *
     * @return string
     */
    public function getPropertyLiveDate()
    {
        return $this->property_live_date;

    }//end getPropertyLiveDate()


    /**
     * Set Property live date
     *
     * @param string $property_live_date Property live date.
     *
     * @return self
     */
    public function setPropertyLiveDate(string $property_live_date)
    {
        $this->property_live_date = $property_live_date;
        return $this;

    }//end setPropertyLiveDate()


    /**
     * Get Total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;

    }//end getTotal()


    /**
     * Set Total
     *
     * @param integer $total Total.
     *
     * @return self
     */
    public function setTotal(int $total)
    {
        $this->total = $total;
        return $this;

    }//end setTotal()


}//end class
