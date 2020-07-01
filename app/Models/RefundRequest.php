<?php
/**
 * Admin Model containing all functions related to admin table
 */

namespace App\Models;

use \Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
/**
 * Class Admin
 */
class RefundRequest extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'refund_request';


    /**
     * Get cancellation reasons for requests/trips
     *
     * @param array $params Array.
     *
     * @return void
     */
    public static function saveRefundRequest(array $params)
    {
        $refund             = new RefundRequest;
        $refund->booking_id = $params['booking_id'];
        $refund->booking_request_id = $params['booking_request_id'];
        $refund->traveller_id       = $params['user_id'];
        $refund->refund_amount      = $params['refund_amount'];
        $refund->currency           = $params['currency'];
        $refund->status             = 0;
        $refund->save();

    }//end saveRefundRequest()


    /**
     * Get cancellation amount for requests/trips
     *
     * @param array $param Array.
     *
     * @return integer
     */
    public static function getRefundedAmount(array $param)
    {
        if (empty($param['wallet_money_applied']) === false) {
            $total_fee = ($param['total_charged_fee'] - $param['service_fee'] - $param['wallet_money_applied']);
        } else if (empty($param['coa_charges']) === false) {
            $total_fee = ($param['total_charged_fee'] - $param['service_fee'] - $param['coa_charges']);
        } else {
            $total_fee = ($param['total_charged_fee'] - $param['service_fee']);
        }

        $check_in_time = $param['check_in_time'];
        $check_in_date = $param['from_date'];
        $check_in      = new Carbon(date('Y-m-d H:i:s', strtotime($check_in_date.' '.$check_in_time)));
        $now           = Carbon::now('GMT');
        $diff_in_days  = $now->diffInDays($check_in, false);
        $diff_in_hours = $now->diffInHours($check_in, false);

        switch ($param['cancellation_policy']) {
            case 1:
            return ($diff_in_hours >= 0) ? $total_fee : 0;

            case 2:
            return ($diff_in_hours >= 24) ? $total_fee : 0;

            case 3:
            return ($diff_in_days >= 7) ? $total_fee : ( ( $diff_in_days < 7 && $diff_in_days >= 3 ) ? $total_fee / 2 : 0);

            case 4:
            return ($diff_in_days >= 7) ? $total_fee : 0;

            case 6:
            return ($diff_in_hours >= 24) ? $param['total_charged_fee'] : 0;

            case 7:
            return ($diff_in_days >= 3) ? $param['total_charged_fee'] : 0;

            case 8:
            return ($diff_in_days >= 7) ? $param['total_charged_fee'] : 0;

            case 9:
            return ($diff_in_days >= 14) ? $param['total_charged_fee'] : 0;

            default:
            return 0;
        }//end switch

    }//end getRefundedAmount()


    /**
     * Get Refund Request data of booking
     *
     * @param integer $booking_request_id Booking Request ID.
     *
     * @return array
     */
    public static function getRefundRequestOfBooking(int $booking_request_id)
    {
        $refund_request = self::from('booking_requests as br')->join('bookings as b', 'br.id', '=', 'b.booking_request_id')->join('refund_request as rr', 'rr.booking_id', '=', 'b.id')->select(
            'rr.id as refund_id',
            'rr.refund_amount as refund_amount',
            'rr.currency as refund_currency',
            'rr.created_at as processing_date',
            'rr.status'
        )->where('br.id', '=', $booking_request_id)->where('b.total_charged_fee', '>', 0)->first();

        if (empty($refund_request) === true) {
            return [];
        }

        return $refund_request->toArray();

    }//end getRefundRequestOfBooking()


}//end class
