<?php
/**
 * PaymentRefund Model containing all functions related to admin table
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Libraries\CommonQueue;

use App\Models\{Booking, BookingRequest, User, Admin};

use Carbon\Carbon;

/**
 * Class PaymentRefund
 */
class PaymentRefund extends Model
{

    /**
     * Gaurd variable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'payment_refund';

    /**
     * Timestamp status.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Refund amount rule pattern.
     *
     * @var array
     */
    public static $rules_refund = ['refund_amount' => 'numeric'];


    /**
     * Initiate payment refund.
     *
     * @param array $details Data Array.
     *
     * @return object refund_request
     */
    public static function initiate(array $details)
    {
        // Unused.
        // Method not in use any where.
        $refund_request               = new PaymentRefund;
        $refund_request->refund_token = $details['refund_token'];
        $refund_request->booking_id   = $details['booking_id'];
        $refund_request->payment_gateway_id = $details['payment_gateway_id'];
        $refund_request->mihpayid           = $details['mihpayid'];
        $refund_request->refund_amount      = $details['refund_amount'];
        $refund_request->refund_currency    = $details['currency'];
        $refund_request->refund_request_id  = $details['refund_request_id'];
        $refund_request->refund_status      = (isset($details['refund_status']) === true) ? $details['refund_status'] : REFUND_INITIATED;
        $refund_request->initiated_on       = $details['initiated_on'];
        $refund_request->save();

        // Payment refund log store in Booking Request table.
        $booking_data = Booking::find($details['booking_id']);
        $booking      = BookingRequest::find($booking_data->booking_request_id);
        $traveller    = User::find($booking->traveller_id);
        $admin        = Admin::find($details['admin_id']);
        if (empty($admin) === false) {
            $booking->booking_status = CANCELLED_BY_HOST_AFTER_PAYMENT;
            if (empty($booking->last_edited_by) === false && empty(json_decode($booking->last_edited_by)) === false) {
                $booking_log = [
                    'id'             => $admin->id,
                    'name'           => $admin->name,
                    'booking_status' => $booking->booking_status,
                    'amount_refund'  => 'Yes',
                    'date'           => Carbon::now('GMT'),
                ];
                $old_data    = json_decode($booking->last_edited_by);
                array_push($old_data, $booking_log);
                $booking->last_edited_by = json_encode($old_data);
            } else {
                $booking_log             = [
                    '0' => [
                        'id'             => $admin->id,
                        'name'           => $admin->name,
                        'booking_status' => $booking->booking_status,
                        'amount_refund'  => 'Yes',
                        'date'           => Carbon::now('GMT'),
                    ],
                ];
                $booking->last_edited_by = json_encode($booking_log);
            }//end if

            $booking->save();
        }//end if

            // Send mail to user.
            CommonQueue::pushEmail(
                [
                    'dir'      => 'mailers',
                    'template' => 'refund_initiated',
                    'content'  => [
                        'booking_request_id' => $booking->id,
                    ],
                ]
            );
        return $refund_request;

    }//end initiate()


    /**
     * Get Payment Refund data of booking
     *
     * @param integer $booking_request_id Booking Request ID.
     *
     * @return array
     */
    public static function getPaymentRefundOfBooking(int $booking_request_id)
    {
        $payment_refund = self::from('booking_requests as br')->join('bookings as b', 'br.id', '=', 'b.booking_request_id')->join('payment_refund as pr', 'pr.booking_id', '=', 'b.id')->select(
            'pr.id as refund_id',
            'pr.refund_status as refund_status',
            'pr.refund_amount as refund_amount',
            'pr.refund_currency as refund_currency',
            \DB::raw('IF(pr.refund_status = 0 , pr.initiated_on, pr.completed_on) as processing_date')
        )->where('br.id', '=', $booking_request_id)->where('b.total_charged_fee', '>', 0)->first();

        if (empty($payment_refund) === true) {
            return [];
        }

        return $payment_refund->toArray();

    }//end getPaymentRefundOfBooking()


}//end class
