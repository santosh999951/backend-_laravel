<?php
/**
 * PayoutTransactions Model containing all functions related to payment settelment to host
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PayoutTransactions
 */
class PayoutTransactions extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'payout_transactions';


    /**
     * Get Payout detail of host.
     *
     * @param integer $user       User id.
     * @param string  $start_date From_date of booking history.
     * @param string  $end_date   To_date of booking history.
     * @param integer $status     Status of settlement.
     * @param integer $offset     Offset of payout details.
     * @param integer $total      Total payout details.
     *
     * @return array
     */
    public static function getPayoutDetails(int $user, string $start_date='', string $end_date='', int $status=0, int $offset=0, int $total=50)
    {
        $payout_detail = self::select(
            \DB::raw('SQL_CALC_FOUND_ROWS b.created_at'),
            'br.to_date',
            'br.from_date',
            'br.to_date',
            'b.extra_amount',
            'pt.currency as currencyCode',
            \DB::raw('sum(if(pt.payout_status = 1,pt.amount,0)) as settled_amount'),
            'b.booking_request_id',
            'bs.status_title',
            'bs.status_id as booking_status',
            'b.no_show',
            'br.price_details',
            \DB::raw(
                '(CASE WHEN b.token_amount+b.settlement_amount !="" THEN b.token_amount+b.settlement_amount 
                                              WHEN b.token_amount+b.settlement_amount > 0 THEN b.token_amount+b.settlement_amount 
                                              WHEN b.token_amount+b.settlement_amount !="" THEN 0 
                                              ELSE b.host_fee END) as host_fee'
            )
            // phpcs:ignore
        )->from('bookings as b')->join('booking_requests as br', 'br.id', '=', 'b.booking_request_id')->join('booking_status as bs', 'bs.status_id', '=', 'br.booking_status')->leftJoin('payout_transactions as pt', 'pt.booking_request_id', '=', 'br.id')->where('b.host_id', '=', $user)->where(\DB::raw("DATE(CONVERT_TZ(b.created_at,'+00:00','+05:30'))"), '>=', PAYOUT_DATA_SHOW_DATE)->groupBy('br.id')->orderBy('br.from_date', 'desc');

        if (empty($start_date) === false && empty($end_date) === false) {
            $payout_detail->whereRaw("b.created_at BETWEEN '".$start_date."' and '".$end_date."'");
        }

        if ($status === 1) {
            $payout_detail->having('settled_amount', '<', \DB::raw('host_fee'));
        } else if ($status === 2) {
            $payout_detail->having('settled_amount', '>=', \DB::raw('host_fee'));
        }

        if ($offset > 0) {
            $payout_detail->offset($offset);
        }

        $payout_detail->limit($total);

        $payout_detail = $payout_detail->get();

        $total_rows = \DB::select('SELECT FOUND_ROWS() as total');

        if (empty($payout_detail) === true) {
            return [
                'total_count' => 0,
                'payouts'     => [],
            ];
        }

        return [
            'total_count' => $total_rows[0]->total,
            'payouts'     => $payout_detail->toArray(),
        ];

    }//end getPayoutDetails()


    /**
     * Get settlement history of booking requests
     *
     * @param array $booking_requests Array of booking request ids.
     *
     * @return array
     */
    public static function getSettlementHistoryOfBookingRequests(array $booking_requests)
    {
        return self::select('booking_request_id', 'utr_number', 'amount', 'created_at', 'currency')->where('utr_number', '!=', '')->where('payout_status', '=', 1)->whereIn('booking_request_id', $booking_requests)->get()->all();

    }//end getSettlementHistoryOfBookingRequests()


    /**
     * Get Payout due amount of host.
     *
     * @param integer $user User id.
     *
     * @return array
     */
    public static function getPayoutDueAmount(int $user)
    {
        $payout_detail = self::select(
            'b.extra_amount',
            'pt.currency as currencyCode',
            \DB::raw('sum(if(pt.payout_status = 1,pt.amount,0)) as settled_amount'),
            'br.price_details',
            \DB::raw(
                '(CASE WHEN b.token_amount+b.settlement_amount !="" THEN b.token_amount+b.settlement_amount 
                                              WHEN b.token_amount+b.settlement_amount > 0 THEN b.token_amount+b.settlement_amount 
                                              WHEN b.token_amount+b.settlement_amount !="" THEN 0 
                                              ELSE b.host_fee END) as host_fee'
            )
        )->from('bookings as b')->join('booking_requests as br', 'br.id', '=', 'b.booking_request_id')->leftJoin(
            'payout_transactions as pt',
            function ($join) {
                                    $join->on('pt.booking_request_id', '=', 'br.id')->where('pt.utr_number', '!=', ' ')->where('pt.payout_status', '=', 1);
            }
        )->where('b.host_id', '=', $user)->where(\DB::raw("DATE(CONVERT_TZ(b.created_at,'+00:00','+05:30'))"), '>=', PAYOUT_DATA_SHOW_DATE)->groupBy('br.id')->get();

        if (empty($payout_detail) === true) {
            return [];
        }

        return $payout_detail->toArray();

    }//end getPayoutDueAmount()


    /**
     * Get host paid amount of Booking Request.
     *
     * @param integer $request_id Request id.
     *
     * @return array
     */
    public static function getHostPaidAmountOfRequest(int $request_id)
    {
        $payout_amount = [];
        $payout_amount = self::sum('amount')->where('booking_request_id', '=', $request_id)->where('payout_status', '=', PAYMENT_TRANSFER_DONE)->get();

        if (empty($payout_detail) === true || $payout_amount < 0) {
            return [];
        }

        return $payout_amount;

    }//end getHostPaidAmountOfRequest()


}//end class
