<?php
/**
 * PutProperlyExpenseResponse
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class PutProperlyExpenseResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="PutProperlyExpenseResponse",
 * description="PutProperlyExpenseResponse",
 * )
 * // phpcs:enable
 */
class PutProperlyExpenseResponse extends ApiResponse
{

    /**
     * Property Properly Expense Hash Id
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="properly_expense_hash_id",
	 *   type="string",
	 *   default="",
	 *   description="Property Properly Expense Hash Id"
	 * )
     * // phpcs:enable
     */
    protected $properly_expense_hash_id = '';

    /**
     * Property Message
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="message",
	 *   type="string",
	 *   default="",
	 *   description="Property Message"
	 * )
     * // phpcs:enable
     */
    protected $message = '';


    /**
     * Get Properly_expense_hash_id
     *
     * @return string
     */
    public function getProperlyExpenseHashId()
    {
        return $this->properly_expense_hash_id;

    }//end getProperlyExpenseHashId()


    /**
     * Set Properly expense hash id
     *
     * @param string $properly_expense_hash_id Properly expense hash id.
     *
     * @return self
     */
    public function setProperlyExpenseHashId(string $properly_expense_hash_id)
    {
        $this->properly_expense_hash_id = $properly_expense_hash_id;
        return $this;

    }//end setProperlyExpenseHashId()


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
