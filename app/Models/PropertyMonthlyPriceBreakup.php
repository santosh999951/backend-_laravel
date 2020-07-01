<?php
/**
 * Model containing data regarding property PropertyMonthlyPriceBreakup
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PropertyMonthlyPriceBreakup
 */
class PropertyMonthlyPriceBreakup extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'property_monthly_price_breakup';


    /**
     * Save property booking Breakup.
     *
     * @param array   $params Params.
     * @param integer $pid    Property Id.
     * @param string  $year   Year.
     * @param string  $month  Month.
     *
     * @return object property_monthly_price_breakup object
     */
    public static function savePropertyPriceBreakup(array $params, int $pid, string $year, string $month)
    {
        $price_breakup                 = new self;
        $price_breakup->booking_amount = (isset($params['payable_amount']) === true) ? $params['payable_amount'] : 0;
        $price_breakup->gst_amount     = (isset($params['gst_amount']) === true ) ? $params['gst_amount'] : 0;
        $price_breakup->markup_fee     = (isset($params['markup_fee']) === true ) ? $params['markup_fee'] : 0;
        $price_breakup->service_fee    = (isset($params['service_fee']) === true) ? $params['service_fee'] : 0;
        $price_breakup->gh_commission  = (isset($params['gh_commission_from_host']) === true) ? $params['gh_commission_from_host'] : 0;
        $price_breakup->properly_share = (isset($params['properly_share']) === true ) ? $params['properly_share'] : 0;
        $price_breakup->host_fee       = (isset($params['host_fee']) === true ) ? $params['host_fee'] : 0;
        $price_breakup->year           = $year;
        $price_breakup->month          = $month;
        $price_breakup->pid            = $pid;

        $price_breakup->save();
        return $price_breakup;

    }//end savePropertyPriceBreakup()


    /**
     * Get property Breakup by month and year.
     *
     * @param integer $pid   Property Id.
     * @param string  $month Month.
     * @param string  $year  Year.
     *
     * @return array
     */
    public static function getPriceBreakup(int $pid, string $month, string $year)
    {
        $query = self::where('pid', $pid)->where('month', $month)->where('year', $year)->first();
        if (empty($query) === true) {
            return [];
        }

        return $query->toArray();

    }//end getPriceBreakup()


    /**
     * Save property booking Breakup.
     *
     * @param integer $price_breakup_id Price breakup id.
     * @param array   $params           Params.
     * @param integer $pid              Property Id.
     * @param string  $year             Year.
     * @param string  $month            Month.
     *
     * @return object
     */
    public static function updatePropertyPriceBreakup(int $price_breakup_id, array $params, int $pid, string $year, string $month)
    {
        $price_breakup                 = self::find($price_breakup_id);
        $price_breakup->booking_amount = (isset($params['payable_amount']) === true) ? $params['payable_amount'] : 0;
        $price_breakup->gst_amount     = (isset($params['gst_amount']) === true ) ? $params['gst_amount'] : 0;
        $price_breakup->markup_fee     = (isset($params['markup_fee']) === true ) ? $params['markup_fee'] : 0;
        $price_breakup->service_fee    = (isset($params['service_fee']) === true) ? $params['service_fee'] : 0;
        $price_breakup->gh_commission  = (isset($params['gh_commission_from_host']) === true) ? $params['gh_commission_from_host'] : 0;
        $price_breakup->properly_share = (isset($params['properly_share']) === true ) ? $params['properly_share'] : 0;
        $price_breakup->host_fee       = (isset($params['host_fee']) === true ) ? $params['host_fee'] : 0;
        $price_breakup->year           = $year;
        $price_breakup->month          = $month;
        $price_breakup->pid            = $pid;

        $price_breakup->save();
        return $price_breakup;

    }//end updatePropertyPriceBreakup()


     /**
      * Get property booking Breakup.
      *
      * @param integer $pid         Property Id.
      * @param string  $start_month Start Month.
      * @param string  $start_year  Start Year.
      * @param string  $end_month   End Month.
      * @param string  $end_year    End Year.
      *
      * @return array
      */
    public static function getPriceBreakupMonthwise(int $pid, string $start_month, string $start_year, string $end_month, string $end_year)
    {
        $query = self::where('pid', $pid)->where(\DB::raw("CONCAT(year, '-', month) "), '>=', $start_year.'-'.$start_month)->where(\DB::raw("CONCAT(year, '-', month) "), '<=', $end_year.'-'.$end_month)->groupBy('year')->groupBy('month')->get();
        if (empty($query) === true) {
            return [];
        }

        return $query->toArray();

    }//end getPriceBreakupMonthwise()


}//end class
