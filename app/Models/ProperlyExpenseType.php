<?php
/**
 * ProperlyExpenseType contain all functions related to properly_expense_type
 */

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProperlyExpense
 */
class ProperlyExpenseType extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'properly_expense_type';

    /**
     * Disable timestamps columns
     *
     * @var boolean
     */
    public $timestamps = false;


    /**
     * Function to get expense type.
     *
     * @param string $name Name of Expenses.
     *
     * @return array.
     */
    public static function getExpenseByName(string $name)
    {
        $expense_id = self::select('id', 'type')->where('name', $name)->first();

        if (empty($expense_id) === false) {
            return $expense_id->toArray();
        }

        return [];

    }//end getExpenseByName()


    /**
     * Function to get all expense type.
     *
     * @return array.
     */
    public static function getAllExpenseType()
    {
        $expense_type = self::select(
            'name',
            \DB::raw('(CASE WHEN type = 1  THEN "Fixed" else "Variable" end) as type')
        )->get();
        if (empty($expense_type) === false) {
            return $expense_type->toArray();
        }

        return [];

    }//end getAllExpenseType()


}//end class
