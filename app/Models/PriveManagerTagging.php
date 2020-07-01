<?php
/**
 * PriveManagerTagging Model containing all functions related to Prive manager tagging
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

/**
 * Class PriveManagerTagging
 */
class PriveManagerTagging extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'prive_manager_taggings';


     /**
      * Get Complimentry_night per year
      *
      * @param integer $pid        Property id.
      * @param string  $start_date Start Date.
      * @param string  $end_date   End Date.
      *
      * @return array
      */
    public static function getComplementryNight(int $pid, string $start_date, string $end_date)
    {
        $prive_taggings = self::select('complimentary_nights_per_year', 'contract_start_date', 'contract_end_date')->where('pid', '=', $pid)->where('contract_start_date', '<=', $start_date)->where('contract_end_date', '>=', $end_date)->first();

        if (empty($prive_taggings) === false) {
            return $prive_taggings->toArray();
        }

        return [];

    }//end getComplementryNight()


    /**
     * Get Property Live date
     *
     * @param integer $pid Property id.
     *
     * @return array
     */
    public static function getPropertyLiveDate(int $pid)
    {
        $prive_taggings = self::select('contract_start_date')->where('pid', '=', $pid)->where('status', 'ACTIVE')->first();
        if (empty($prive_taggings) === false) {
            return $prive_taggings->toArray();
        }

        return [];

    }//end getPropertyLiveDate()


    /**
     * Validate Booking with Contract
     *
     * @param integer $property_id    Property id.
     * @param integer $prive_owner_id Prive Owner Id.
     * @param string  $start_date     Start Date.
     * @param string  $end_date       End Date.
     *
     * @return boolean
     */
    public static function validateBooking(int $property_id, int $prive_owner_id, string $start_date, string $end_date)
    {
        $prive_tagging = self::getComplementryNight($property_id, $start_date, $end_date);

        if (empty($prive_tagging) === true || empty($prive_tagging['complimentary_nights_per_year']) === true) {
            return false;
        }

        $from_date           = Carbon::parse($start_date);
        $to_date             = Carbon::parse($end_date);
        $contract_start_date = Carbon::parse($prive_tagging['contract_start_date']);
        $contract_end_date   = Carbon::parse($prive_tagging['contract_end_date']);
        $complimentary_nights_per_year = $prive_tagging['complimentary_nights_per_year'];
        $total_nights                  = $from_date->diffInDays($to_date);

        $total_nights_booked = BookingRequest::getPriveBookedNights($contract_start_date, $contract_end_date, $prive_owner_id);

        if (($total_nights_booked + $total_nights) > $complimentary_nights_per_year) {
            return false;
        } else {
            return true;
        }

        return false;

    }//end validateBooking()


    /**
     * Get Capital Investament
     *
     * @param array $pids Property id.
     *
     * @return array
     */
    public static function getCapitalInvestmentByPropertyIds(array $pids)
    {
        $prive_taggings = self::selectRaw('sum(capital_investment) as capital_investment')->whereIn('pid', $pids)->first();

        if (empty($prive_taggings) === false) {
            return $prive_taggings->toArray();
        }

        return [];

    }//end getCapitalInvestmentByPropertyIds()


    /**
     * Get Invoice Start Month year
     *
     * @param integer $prive_owner_id Prive Owner id.
     *
     * @return array
     */
    public static function getInvoiceStartMonthYear(int $prive_owner_id)
    {
        $query = self::select(
            \DB::raw("DATE_FORMAT(pmt.contract_start_date,'%Y-%m') as start_month_year"),
        )->from('prive_owner as po')->join(
            'properties as p',
            function ($join) use ($prive_owner_id) {
                    $join->on('po.pid', '=', 'p.id')->where('po.user_id', '=', $prive_owner_id)->whereNull('po.deleted_at');
            }
        )->join('prive_manager_taggings as pmt', 'pmt.pid', '=', 'p.id')->where('pmt.status', 'ACTIVE');
        $query = $query->orderBy('pmt.contract_start_date', 'asc')->first();
        if (empty($query) === false) {
            return $query->toArray();
        }

        return [];

    }//end getInvoiceStartMonthYear()


}//end class
