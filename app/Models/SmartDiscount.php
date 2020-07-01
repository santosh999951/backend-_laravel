<?php
/**
 * Model containing data regarding Smart Discount
 */

namespace App\Models;

use \Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SmartDiscount
 */
class SmartDiscount extends Model
{

    /**
     * Variable definition.
     *
     * @var $table
     */
    protected $table = 'smart_discount';


    /**
     * Helper function to get Smart Discount of property
     *
     * @param integer $pid        Property id.
     * @param string  $start_date Start date.
     *
     * @return array Smart Discount data.
     */
    public function getPropertySmartDiscounts(int $pid, string $start_date)
    {
        $smart_discount = self::where('pid', $pid)->where('start_date', '>=', $start_date)->get();

        if (empty($smart_discount) === true) {
            return [];
        }

        return $smart_discount->toArray();

    }//end getPropertySmartDiscounts()


    /**
     * Helper function to get Common Smart Discount between selected date(())
     *
     * @param integer $pid        Property id.
     * @param string  $start_date Start date.
     * @param string  $end_date   End date.
     *
     * @return array Smart Discount data.
     */
    public function getCommonSmartDiscounts(int $pid, string $start_date, string $end_date)
    {
        $smart_discount = self::select(
            'id',
            'start_date',
            'end_date',
            'discount',
            'discount_days',
            'status'
        )->where('pid', $pid)->where(
            function ($query) use ($start_date, $end_date) {
                $query->where(
                    function ($inner_query) use ($start_date, $end_date) {
                        $inner_query->where('start_date', '<=', $start_date)->where('end_date', '>=', $start_date);
                    }
                )->orWhere(
                    function ($inner_query) use ($start_date, $end_date) {
                            $inner_query->where('start_date', '<=', $end_date)->where('end_date', '>=', $end_date);
                    }
                )->orWhere(
                    function ($inner_query) use ($start_date, $end_date) {
                            $inner_query->where('start_date', '>', $start_date)->where('end_date', '<', $end_date);
                    }
                );
            }
        )->get()->keyBy('id');

        if (empty($smart_discount) === true) {
            return [];
        }

        return $smart_discount->toArray();

    }//end getCommonSmartDiscounts()


    /**
     * Helper function to get Smart Discount array
     *
     * @param array  $smart_discounts        Smart Discounts.
     * @param Carbon $start_date_obj         Start date Object.
     * @param Carbon $end_date_obj           End date Object.
     * @param array  $extra_keys_in_response Extra keys data required in Response.
     *
     * @return array Smart Discount data.
     */
    public function getSmartDiscountsOfDate(array $smart_discounts, Carbon $start_date_obj, Carbon $end_date_obj, array $extra_keys_in_response=[])
    {
        $smart_discount_exception = [];

        if (empty($smart_discounts) === true) {
            return $smart_discount_exception;
        }

        while ($start_date_obj->format('Y-m-d') <= $end_date_obj->format('Y-m-d')) {
            $all_smart_discounts = [];
            foreach ($smart_discounts as $discount) {
                $key = $discount['discount'].'_'.$discount['discount_days'];
                if ($start_date_obj->format('Y-m-d') >= $discount['start_date'] && $start_date_obj->format('Y-m-d') <= $discount['end_date'] && array_key_exists($key, $all_smart_discounts) === false) {
                    // Add Extra Data in response.
                    $extra_data = [];

                    if (empty($extra_keys_in_response) === false) {
                        foreach ($extra_keys_in_response as $value) {
                            if (isset($discount[$value]) === true) {
                                $extra_data[$value] = $discount[$value];
                            }
                        }
                    }

                    $all_smart_discounts[$key] = array_merge(
                        [
                            'id'       => $discount['id'],
                            'discount' => $discount['discount'],
                            'days'     => $discount['discount_days'],
                            'status'   => $discount['status'],
                        ],
                        $extra_data
                    );
                }//end if
            }//end foreach

            $all_smart_discounts = array_values($all_smart_discounts);

            if (empty($all_smart_discounts) === false) {
                $smart_discount_exception[$start_date_obj->format('Y-m-d')] = $all_smart_discounts;
            }

            $start_date_obj->addDay();
        }//end while

        return $smart_discount_exception;

    }//end getSmartDiscountsOfDate()


    /**
     * Helper function to create/update Smart Discount
     *
     * @param array $param Create/Update Smart Discount Parameters.
     *
     * @return object Smart Discount.
     */
    public static function changeSmartDiscounts(array $param)
    {
        $pid           = $param['property_id'];
        $start_date    = $param['start_date'];
        $end_date      = $param['end_date'];
        $discount      = $param['discount'];
        $discount_days = $param['discount_days'];
        $status        = $param['status'];

        if (empty($param['id']) === false) {
            $smart_discount = self::find($param['id']);
        } else {
            $smart_discount = new self;
        }

        $smart_discount->pid           = $pid;
        $smart_discount->start_date    = $start_date;
        $smart_discount->end_date      = $end_date;
        $smart_discount->discount      = $discount;
        $smart_discount->discount_days = $discount_days;
        $smart_discount->status        = $status;
        $smart_discount->save();

        return $smart_discount;

    }//end changeSmartDiscounts()


}//end class
