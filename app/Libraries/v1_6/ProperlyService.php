<?php
/**
 * Properly Service containing methods related to Properly Dashboard.
 */

namespace App\Libraries\v1_6;

use App\Models\ProperlyExpense;
use App\Models\{BookingRequest,Property,PriveManagerTagging, ProperlyDesignationUserMapping};
use App\Libraries\Helper;
use App\Models\{ProperlyExpenseType,PropertyMonthlyPriceBreakup};
use Carbon\Carbon;

/**
 * Class ProperlyService
 */
class ProperlyService
{


    /**
     * Get Property current month pl and last month pl percentage.
     *
     * @param integer $prive_owner_id     Prive Owner id to fetch expenses for.
     * @param integer $property_id        Property id to fetch property description for.
     * @param string  $month_year         Month year of expenses.
     * @param string  $property_live_date Live Date of Property.
     *
     * @return array expense distribution and pl.
     */
    public function getExpenseDistribution(int $prive_owner_id, int $property_id, string $month_year, string $property_live_date='')
    {
        $comparion_month_pl_percentage = [];
        $property_live_date_month_year = Carbon::parse($property_live_date)->format('Y-m');

        // If property is lived in current month then only show data from property live date.
        if ($property_live_date_month_year === $month_year) {
            $start_date_obj = Carbon::parse($property_live_date);
        } else {
            $start_date_obj = Carbon::parse($month_year)->subMonth();
        }

        $start_month_year = $start_date_obj->format('Y-m');
        $start_month      = $start_date_obj->format('m');
        $start_year       = $start_date_obj->format('Y');

        $end_date_obj = Carbon::createFromFormat('Y-m', $month_year)->endOfMonth();
        $end_month    = $end_date_obj->format('m');
        $end_year     = $end_date_obj->format('Y');

        // Get current month expense distribution namewise.
        $monthly_expense_distribution_name_wise = $this->getExpenseDistributionNamewise($prive_owner_id, $property_id, $month_year);

        // Get expense of current and last month.
        $monthly_expenses = $this->getMontwiseProperlyExpense($property_id, $prive_owner_id, $start_month_year, $month_year, 1);

        $monthly_payable_amount = $this->getPropertyPriceBreakupMonthwise($property_id, $start_month, $start_year, $end_month, $end_year);

        $current_month_payable_amount = (isset($monthly_payable_amount[$month_year]['payable_amount']) === true) ? $monthly_payable_amount[$month_year]['payable_amount'] : 0;

        $current_month_ota_fees = (isset($monthly_payable_amount[$month_year]['ota_fees']) === true) ? $monthly_payable_amount[$month_year]['ota_fees'] : 0;

        $current_month_host_fee = (isset($monthly_payable_amount[$month_year]['host_fee']) === true) ? $monthly_payable_amount[$month_year]['host_fee'] : 0;

        $current_month_expense_amount = (isset($monthly_expenses[$month_year]) === true) ? $monthly_expenses[$month_year] : 0;

        $current_month_pl = round(($current_month_payable_amount - $current_month_ota_fees - $current_month_host_fee - $current_month_expense_amount), 2);

        // If property lived before current month then only we comapre current month pl to last month pl.
        if ($property_live_date_month_year !== $month_year) {
            $last_month_payable_amount = (isset($monthly_payable_amount[$start_month_year]['payable_amount']) === true) ? $monthly_payable_amount[$start_month_year]['payable_amount'] : 0;

            $last_month_ota_fees = (isset($monthly_payable_amount[$start_month_year]['ota_fees']) === true) ? $monthly_payable_amount[$start_month_year]['ota_fees'] : 0;

            $last_month_host_fee = (isset($monthly_payable_amount[$start_month_year]['host_fee']) === true) ? $monthly_payable_amount[$start_month_year]['host_fee'] : 0;

            $last_month_expense = (isset($monthly_expenses[$start_month_year]) === true) ? $monthly_expenses[$start_month_year] : 0;

            $last_month_pl = round(($last_month_payable_amount - $last_month_ota_fees - $last_month_host_fee - $last_month_expense), 2);

            $comparion_month_pl_percentage = $this->calculateComparisaonMonthPlPercentage($current_month_pl, $last_month_pl);
        }//end if

         // Current Month Pl distribution.
        $monthly_pl = [
            'payable_amount'        => Helper::getFormattedMoney(round($current_month_payable_amount, 2), DEFAULT_CURRENCY),
            'ota_fees'              => Helper::getFormattedMoney(round($current_month_ota_fees, 2), DEFAULT_CURRENCY),
            'host_fee'              => Helper::getFormattedMoney(round($current_month_host_fee, 2), DEFAULT_CURRENCY),
            'expenses'              => Helper::getFormattedMoney($current_month_expense_amount, DEFAULT_CURRENCY),
          // 'properly_share'        => (isset($monthly_payable_amount[$month_year]['properly_share']) === true) ? Helper::getFormattedMoney(round($monthly_payable_amount[$month_year]['properly_share'], 2), DEFAULT_CURRENCY) : 0,
            'pl_formatted'          => Helper::getFormattedMoney(abs($current_month_pl), DEFAULT_CURRENCY),
            'type'                  => ($current_month_pl >= 0) ? 'profit' : 'loss',
            'accordance_to_last_pl' => $comparion_month_pl_percentage,
        ];
        $expense_distributions['distribution'] = $monthly_expense_distribution_name_wise;

        $expense_distributions['pl'] = $monthly_pl;

        return $expense_distributions;

    }//end getExpenseDistribution()


     /**
      * Get Property expense list by given month year
      *
      * @param integer $property_id    Property id to fetch property description for.
      * @param integer $prive_owner_id Prive Owner id to fetch expenses for.
      * @param string  $month_year     Month year of expenses.
      * @param integer $limit          Total number of expenses .
      * @param integer $offset         Offset.
      *
      * @return array expense list.
      */
    public function getMonthlyExpenses(int $property_id, int $prive_owner_id, string $month_year, int $limit=20, int $offset=0)
    {
        $expense_list    = [];
        $monthly_expense = ProperlyExpense::getMonthlyExpenses($property_id, $prive_owner_id, $month_year, $limit, $offset);

        foreach ($monthly_expense as $key => $value) {
            $expense['expense_hash_id'] = Helper::encodeProperlyExpenseId($value['id']);
            $expense['expense_name']    = $value['expense_name'];
            $expense['type']            = $value['type'];
            $expense['added_on']        = Carbon::parse($value['created_at'])->format('dS M Y');
            $expense['basic_amount']    = Helper::getFormattedMoney($value['basic_amount'], DEFAULT_CURRENCY, false);
            $expense['nights_booked']   = $value['nights_booked'];
            //phpcs:ignore
            $expense['total_amount']    = (($value['nights_booked']) === null) ? Helper::getFormattedMoney($value['basic_amount'], DEFAULT_CURRENCY, false) : Helper::getFormattedMoney(($value['basic_amount'] * $value['nights_booked']), DEFAULT_CURRENCY, false);
            $expense_list[]          = $expense;
        }

        return $expense_list;

    }//end getMonthlyExpenses()


     /**
      * Get Property expense accordance by given month_year
      *
      * @param integer $property_id        Property id to fetch property description for.
      * @param integer $prive_owner_id     Prive Owner id to fetch expenses for.
      * @param string  $month_year         Month year of expenses.
      * @param string  $property_live_date MLive date of property.
      *
      * @return array expense list.
      */
    public function getExpenseAccordance(int $property_id, int $prive_owner_id, string $month_year, string $property_live_date)
    {
        $monthly_booking_amounts = [];

        $property_live_date_obj        = Carbon::parse($property_live_date);
        $property_live_date_month_year = $property_live_date_obj->format('Y-m');

        $start_date_obj = Carbon::createFromFormat('Y-m', $month_year)->subYear()->startOfMonth();
        $end_date_obj   = Carbon::createFromFormat('Y-m', $month_year);

        $end_date_last_date_obj = $end_date_obj->endOfMonth();

        $start_date = $start_date_obj->toDateString();

        $start_month_year = $start_date_obj->format('Y-m');
        $end_month_year   = $end_date_obj->format('Y-m');

        // If property live date is greater then 1 year data then we only provide data of last one year.
        if ($property_live_date > $start_date) {
            $start_date_obj   = $property_live_date_obj;
            $start_month_year = $property_live_date_month_year;
            $start_date       = $start_date_obj->toDateString();
        }

        $start_month = $start_date_obj->format('m');
        $start_year  = $start_date_obj->format('Y');
        $end_month   = $end_date_obj->format('m');
        $end_year    = $end_date_obj->format('Y');

        // Get monthwise total booking amount.
        $monthwise_booking_amount = $this->getPropertyPriceBreakupMonthwise($property_id, $start_month, $start_year, $end_month, $end_year);

        // Get Monthwise properly expense.
        $monthwise_properly_expense = $this->getMontwiseProperlyExpense($property_id, $prive_owner_id, $start_month_year, $end_month_year, 1);

        // Create a common array.
        for ($month = $start_date_obj; $month->lte($end_date_last_date_obj) === true; $month->addMonth()) {
            $all_months[$month->format('Y-m')] = [
                'month_year'     => $month->format('Y-m'),
                'total_expense'  => 0,
                'payable_amount' => 0,
                'ota_fees'       => 0,
                'host_fee'       => 0,
            ];
        }

        // Push payable amount in common array.
        foreach ($monthwise_booking_amount as $key => $monthly_data) {
            $all_months[$key]['payable_amount'] = $monthly_data['payable_amount'];
            $all_months[$key]['ota_fees']       = $monthly_data['ota_fees'];
            $all_months[$key]['host_fee']       = $monthly_data['host_fee'];
        }

        // Push expense in common array.
        foreach ($monthwise_properly_expense as $key => $expense) {
            $all_months[$key]['total_expense'] = $expense;
        }

        // Get selected month profit loss.
        $current_month_pl = ($all_months[$month_year]['payable_amount'] - $all_months[$month_year]['ota_fees'] - $all_months[$month_year]['host_fee'] - $all_months[$month_year]['total_expense']);

        foreach ($all_months as $month => $value) {
            $accordance[$month]['month']           = $month;
            $accordance[$month]['formatted_month'] = Carbon::parse($month)->format('F Y');
            $accordance[$month]['pl']              = round(($value['payable_amount'] - $value['ota_fees'] - $value['host_fee'] - $value['total_expense']), 2);
            $accordance[$month]['pl_formatted']    = Helper::getFormattedMoney($accordance[$month]['pl'], DEFAULT_CURRENCY);

            // Get Pl percentage of all months in comaprison of selected month.
            $comparion_month_pl_percentage = $this->calculateComparisaonMonthPlPercentage($accordance[$month]['pl'], $current_month_pl);

            $accordance[$month]['percentage'] = $comparion_month_pl_percentage;
        }

        return array_values($accordance);

    }//end getExpenseAccordance()


    /**
     * Get Prive owner property by id
     *
     * @param integer $prive_owner_id Prive Owner id to fetch expenses for.
     * @param integer $property_id    Property id to fetch property description for.
     *
     * @return array.
     */
    public function getPriveOwnerPropertyById(int $prive_owner_id, int $property_id)
    {
        $property = Property::getPriveOwnerPropertyById($prive_owner_id, $property_id);

        return $property;

    }//end getPriveOwnerPropertyById()


    /**
     * Get Properly expense object based on given expense id
     *
     * @param integer $expense_id Properly expense id.
     *
     * @return mixed
     */
    public function getExpense(int $expense_id)
    {
        return ProperlyExpense::find($expense_id);

    }//end getExpense()


    /**
     * Get Properly expense type object based on given expense type id
     *
     * @param integer $expense_type_id Properly expense type id.
     *
     * @return mixed
     */
    public function getExpenseType(int $expense_type_id)
    {
        return ProperlyExpenseType::find($expense_type_id);

    }//end getExpenseType()


    /**
     * Update Properly expense of given id
     *
     * @param array $data Data to be updated.
     *
     * @return mixed
     */
    public function updateExpense(array $data)
    {
        return ProperlyExpense::updateExpense($data);

    }//end updateExpense()


    /**
     * Delete (soft) Properly expense of given id
     *
     * @param integer $id Properly expense id.
     *
     * @return mixed
     */
    public function deleteExpense(int $id)
    {
        return ProperlyExpense::find($id)->delete();

    }//end deleteExpense()


    /**
     * Get Live date of given property id.
     *
     * @param integer $pid Property  id.
     *
     * @return array
     */
    public function getPropertyLiveDate(int $pid)
    {
        $live_date = PriveManagerTagging::getPropertyLiveDate($pid);
        if (empty($live_date) === true || ($live_date['contract_start_date'] === '0000-00-00')) {
            return PROPERTY_LIVE_DATE;
        } else {
            return $live_date['contract_start_date'];
        }

    }//end getPropertyLiveDate()


    /**
     * Calculate Pl percentage.
     *
     * @param float $selected_month_pl   Selected Month Pl.
     * @param float $comparison_month_pl Comaprison month  pl.
     *
     * @return array
     */
    public function calculateComparisaonMonthPlPercentage(float $selected_month_pl, float $comparison_month_pl)
    {
        $comparison_month_pl_distribution = [];

        if (empty($selected_month_pl) === true && empty($comparison_month_pl) === true) {
            $comparison_month_pl_percentage = 0;
        } else if (empty($selected_month_pl) === false && empty($comparison_month_pl) === true) {
            $comparison_month_pl_percentage = $selected_month_pl;
        } else {
            $comparison_month_pl_percentage = (round((($selected_month_pl - $comparison_month_pl) / abs($comparison_month_pl) * 100)));
        }

        if ($comparison_month_pl_percentage >= 0) {
            $type = 'profit';
        } else if ($comparison_month_pl_percentage < 0) {
            $type = 'loss';
        }

        $comparison_month_pl_distribution['type']       = $type;
        $comparison_month_pl_distribution['percentage'] = $comparison_month_pl_percentage;

        return $comparison_month_pl_distribution;

    }//end calculateComparisaonMonthPlPercentage()


    /**
     * Get namewise expense distribution by month year
     *
     * @param integer $prive_owner_id Prive Owner id to fetch expenses for.
     * @param integer $property_id    Property id to fetch property description for.
     * @param string  $month_year     Month year of expenses.
     *
     * @return array expense distribution.
     */
    public function getExpenseDistributionNamewise(int $prive_owner_id, int $property_id, string $month_year)
    {
        $expense_distribution = [];

        // Get Expense name wise of current month.
        $monthly_expense_distribution_name_wise = ProperlyExpense::getExpenseDistribution($property_id, $prive_owner_id, $month_year, $month_year);

        // Current Month total Expense.
        $current_month_expense_amount = array_sum(array_column($monthly_expense_distribution_name_wise, 'total_amount'));

        // Get formatted expense distribution by namewise.
        foreach ($monthly_expense_distribution_name_wise as $key => $value) {
            $total_amount                  = (empty($value['total_amount']) === false) ? $value['total_amount'] : 0.0;
            $expense['percentage']         = (floor(round(($value['total_amount'] / $current_month_expense_amount * 100), 2) * 10) / 10).'%';
            $expense['expense_name']       = $value['expense_name'];
            $expense['unformatted_amount'] = $total_amount;
            $expense['amount']             = Helper::getFormattedMoney($total_amount, DEFAULT_CURRENCY);
            $expense_distribution[]        = $expense;
        }

        $percentage = array_column($expense_distribution, 'percentage');

        array_multisort($percentage, SORT_DESC, SORT_NUMERIC, $expense_distribution);

        return $expense_distribution;

    }//end getExpenseDistributionNamewise()


    /**
     * Get Property expense distribution by month year
     *
     * @param integer $property_id      Property id to fetch property description for.
     * @param integer $prive_owner_id   Prive Owner id to fetch expenses for.
     * @param string  $start_month_year Start Month year of expenses.
     * @param string  $end_month_year   End Month year of expenses.
     *
     * @return array expense distribution.
     */
    public function getMontwiseProperlyExpense(int $property_id, int $prive_owner_id, string $start_month_year, string $end_month_year)
    {
        $monthly_expense_amounts = [];

        $monthwise_properly_expense = ProperlyExpense::getExpenseDistribution($property_id, $prive_owner_id, $start_month_year, $end_month_year, 1);

        foreach ($monthwise_properly_expense as $monthly_expenses) {
            $monthly_expense_amounts[$monthly_expenses['month_year']] = $monthly_expenses['total_amount'];
        }

        return $monthly_expense_amounts;

    }//end getMontwiseProperlyExpense()


    /**
     * Function to get property booking data monthwise.
     *
     * @param integer $property_id Property id.
     * @param string  $start_month Start Month.
     * @param string  $start_year  Start Year.
     * @param string  $end_month   End Month.
     * @param string  $end_year    End Year.
     *
     * @return array total monthly booking .
     */
    public function getPropertyPriceBreakupMonthwise(int $property_id, string $start_month, string $start_year, string $end_month, string $end_year)
    {
        $monthwise_price_breakup = [];
        $booking_price_breakup   = PropertyMonthlyPriceBreakup::getPriceBreakupMonthwise($property_id, $start_month, $start_year, $end_month, $end_year);

        foreach ($booking_price_breakup as $price_breakup) {
            $monthwise_price_breakup[$price_breakup['year'].'-'.$price_breakup['month']]['payable_amount'] = ($price_breakup['booking_amount'] - $price_breakup['gst_amount']);
              $monthwise_price_breakup[$price_breakup['year'].'-'.$price_breakup['month']]['ota_fees']     = ($price_breakup['markup_fee'] + $price_breakup['service_fee'] + $price_breakup['gh_commission']);
              $monthwise_price_breakup[$price_breakup['year'].'-'.$price_breakup['month']]['host_fee']     = ($price_breakup['host_fee'] - $price_breakup['properly_share']);
        }

        return $monthwise_price_breakup;

    }//end getPropertyPriceBreakupMonthwise()


    /**
     * Get all unique Properly Expense list of specific property and owner, as suggestions.
     *
     * @param integer $property_id    Property id .
     * @param integer $prive_owner_id Prive Owner id.
     * @param string  $month_year     Month year.
     *
     * @return array unique expense list.
     */
    public function getExpenseListAsSuggestions(int $property_id, int $prive_owner_id, string $month_year)
    {
        $expenses = ProperlyExpense::getUniqueExpenses($property_id, $prive_owner_id, $month_year);
        foreach ($expenses as &$expense) {
            $expense->added_on      = Carbon::parse($expense->added_on)->format('dS M Y');
            $expense->basic_amount  = Helper::getFormattedMoney($expense->basic_amount, DEFAULT_CURRENCY, false);
            $expense->nights_booked = ($expense->type === 'Fixed') ? null : 0;
            $expense->total_amount  = 0;
        }

        return $expenses->toArray();

    }//end getExpenseListAsSuggestions()


     /**
      * Get Capital Investament
      *
      * @param array $pids Property id.
      *
      * @return array
      */
    public static function getCapitalInvestmentByPropertyIds(array $pids)
    {
        return PriveManagerTagging::getCapitalInvestmentByPropertyIds($pids);

    }//end getCapitalInvestmentByPropertyIds()


    /**
     * Get Properly user property
     *
     * @param integer $user_id Properly user id.
     *
     * @return array
     */
    public function getUserProperties(int $user_id)
    {
        $properly_designation_user_mapping = new ProperlyDesignationUserMapping;
        $user_properties                   = $properly_designation_user_mapping->getUserProperties($user_id);

        return $user_properties;

    }//end getUserProperties()


    /**
     * Get Invoice Month year
     *
     * @param integer $prive_owner_id Prive Owner id.
     *
     * @return array
     */
    public static function getInvoiceMonthYear(int $prive_owner_id)
    {
        $month_year = [];

        $start_month_year = PriveManagerTagging::getInvoiceStartMonthYear($prive_owner_id);

        $month_year['start_month_year'] = (empty($start_month_year) === true ) ? '2019-01' : $start_month_year['start_month_year'];

        $end_month_year = BookingRequest::getInvoiceEndMonthYear($prive_owner_id);

        $month_year['end_month_year'] = (empty($end_month_year) === true ) ? Carbon::now()->format('Y-m') : $end_month_year['end_month_year'];

        return $month_year;

    }//end getInvoiceMonthYear()


}//end class
