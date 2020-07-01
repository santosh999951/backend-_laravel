<?php
/**
 * ProperlyExpense contain all functions related to properly_expenses
 */

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Class ProperlyExpense
 */
class ProperlyExpense extends Model
{
    use SoftDeletes;

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'properly_expenses';


    /**
     * Function to save properly Expenses.
     *
     * @param array $params Parameters.
     *
     * @return boolean true/false.
     */
    public static function saveExpenses(array $params)
    {
        $properly_expense                  = new self;
        $properly_expense->expense_type_id = $params['expense_type_id'];
        $properly_expense->month_year      = $params['month_year'];
        $properly_expense->pid             = $params['pid'];
        $properly_expense->basic_amount    = $params['basic_amount'];

        if (isset($params['nights']) === true) {
            $properly_expense->nights_booked = $params['nights'];
        }

        try {
            $properly_expense->save();
        } catch (QueryException $e) {
            return false;
        }

        return true;

    }//end saveExpenses()


    /**
     * Get Property expense list by month year
     *
     * @param integer $property_id    Property id to fetch property description for.
     * @param integer $prive_owner_id Prive Owner id to fetch expenses for.
     * @param string  $month_year     Month year of expenses.
     * @param integer $limit          Limit.
     * @param integer $offset         Offset.
     *
     * @return array expense list.
     */
    public static function getMonthlyExpenses(int $property_id, int $prive_owner_id, string $month_year, int $limit, int $offset)
    {
        $query = self::select(
            'pe.id',
            'pet.name as expense_name',
            \DB::raw('(CASE WHEN pet.type = 1  THEN "Fixed" else "Variable" end) as type'),
            'pe.created_at',
            'pe.basic_amount',
            'pe.nights_booked'
        )->from('properly_expenses as pe')->join('properly_expense_type as pet', 'pet.id', '=', 'pe.expense_type_id')->join('properties as p', 'p.id', '=', 'pe.pid')->join(
            'prive_owner as po',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->where('pe.pid', $property_id)->where('month_year', $month_year)->whereNull('pe.deleted_at')->withTrashed()->orderBy('pe.created_at', 'desc')->offset($offset)->limit($limit)->get();

        if (empty($query) === false) {
            return $query->toArray();
        }

        return [];

    }//end getMonthlyExpenses()


    /**
     * Get Property expense distribution by month year
     *
     * @param integer $property_id      Property id to fetch property description for.
     * @param integer $prive_owner_id   Prive Owner id to fetch expenses for.
     * @param string  $start_month_year Start Month year of expenses.
     * @param string  $end_month_year   End Month year of expenses.
     * @param integer $group_by         Group by be default name of properly_expense_type.
     *
     * @return array expense distribution.
     */
    public static function getExpenseDistribution(int $property_id, int $prive_owner_id, string $start_month_year, string $end_month_year, int $group_by=0)
    {
        if (empty($group_by) === false) {
            $group_by = 'pe.month_year';
        } else {
            $group_by = 'pet.name';
        }

        $query = self::select(
            'pet.name as expense_name',
            'pe.month_year',
            \DB::raw('sum(CASE WHEN pet.type = 1  THEN (pe.basic_amount) else (pe.basic_amount * pe.nights_booked) end) as total_amount')
        )->from('properly_expenses as pe')->join('properties as p', 'p.id', '=', 'pe.pid')->join(
            'prive_owner as po',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join(
            'properly_expense_type as pet',
            'pet.id',
            '=',
            'pe.expense_type_id'
        )->where('pe.pid', $property_id)->where('pe.month_year', '>=', $start_month_year)->where('pe.month_year', '<=', $end_month_year)->whereNull('pe.deleted_at')->withTrashed()->groupBy($group_by)->get();

        if (empty($query) === false) {
            return $query->toArray();
        }

        return [];

    }//end getExpenseDistribution()


    /**
     * Update expense by id
     *
     * @param array $data Data to be updated.
     *
     * @return mixed
     */
    public static function updateExpense(array $data)
    {
        return self::where('id', $data['id'])->update($data);

    }//end updateExpense()


    /**
     * Get total Property expense  by month year
     *
     * @param integer $property_id    Property id to fetch property description for.
     * @param integer $prive_owner_id Prive Owner id to fetch expenses for.
     * @param string  $month_year     Month year of expenses.
     *
     * @return array expense list.
     */
    public static function getTotalMonthlyExpenses(int $property_id, int $prive_owner_id, string $month_year)
    {
        $query = self::from('properly_expenses as pe')->join('properly_expense_type as pet', 'pet.id', '=', 'pe.expense_type_id')->join('properties as p', 'p.id', '=', 'pe.pid')->join(
            'prive_owner as po',
            function ($join) use ($prive_owner_id) {
                $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->where('pe.pid', $property_id)->where('month_year', $month_year)->whereNull('pe.deleted_at')->withTrashed()->count();
        return $query;

    }//end getTotalMonthlyExpenses()


    /**
     * Get all unique Properly Expense list of specific property and owner.
     *
     * @param integer $property_id    Property id.
     * @param integer $prive_owner_id Prive Owner.
     * @param string  $month_year     Month Year.
     *
     * @return object unique expense list.
     */
    public static function getUniqueExpenses(int $property_id, int $prive_owner_id, string $month_year)
    {
        $current_month_expenses    = self::select('expense_type_id')->where('pid', $property_id)->where('month_year', $month_year)->whereNull('deleted_at')->groupBy('expense_type_id')->get()->toArray();
        $current_month_expense_ids = [];
        foreach ($current_month_expenses as $id) {
            array_push($current_month_expense_ids, $id['expense_type_id']);
        }

        $current_month_expense_ids = (count($current_month_expense_ids) > 0) ? implode(',', $current_month_expense_ids) : 0;

        return \DB::table('properly_expenses AS pe')->select(
            'pet.name AS expense_name',
            \DB::raw('(CASE WHEN pet.type = 1  THEN "Fixed" ELSE "Variable" END) AS type'),
            'pe.created_at AS added_on',
            'pe.basic_amount',
            'pe.nights_booked'
        )->join('properly_expense_type AS pet', 'pet.id', '=', 'pe.expense_type_id')->join('properties AS p', 'p.id', '=', 'pe.pid')->join(
            'prive_owner AS po',
            function ($join) use ($prive_owner_id) {
                    $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->whereNull('pe.deleted_at')->whereRaw(
            'pe.id IN 
            (SELECT MAX(id) FROM properly_expenses WHERE pid='.$property_id.' AND deleted_at IS NULL AND expense_type_id NOT IN ('.$current_month_expense_ids.') 
            GROUP BY expense_type_id)'
        )->orderBy('pe.created_at', 'DESC')->get();

    }//end getUniqueExpenses()


}//end class
