<?php
/**
 * Model containing data regarding payment tracking
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentTracking
 */
class PaymentTracking extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'payment_tracking';


    /**
     * Checks if payment verification is pending for a booking request
     *
     * @param integer $request_id   Booking request id.
     * @param string  $payment_type Text containing payment type.
     *
     * @return boolean True/false
     */
    public static function isPaymentStatusPending(int $request_id, string $payment_type='pre_booking')
    {
        // Set all payment with same booking request id to fail.
        $count = self::where('booking_request_id', '=', $request_id)->where('payment_type', $payment_type)->whereIn('status', [PAYMENT_VERIFICATION_PENDING])->count();

        if ($count > 0) {
            return true;
        }

        return false;

    }//end isPaymentStatusPending()


    /**
     * Record a payment initiated on our end.
     *
     * @param array $tracking_details Tracking details.
     *
     * @return boolean
     */
    public static function initiate(array $tracking_details)
    {
        // Set all payment with same booking request id to fail.
        self::where('booking_request_id', '=', $tracking_details['request_id'])->where('txnid', '=', $tracking_details['txn_id'])->whereNotIn('status', [PAYMENT_SUCCESS, PAYMENT_VERIFICATION_PENDING])->update(['status' => PAYMENT_FAILED]);

        // Make an entry into payment table for payment initiated.
        $tracking = new PaymentTracking;
        $tracking->booking_request_id = $tracking_details['request_id'];
        $tracking->payment_gateway_id = $tracking_details['payment_gateway_id'];
        $tracking->txnid              = $tracking_details['txn_id'];
        $tracking->status             = PAYMENT_INITIATED;
        $tracking->payment_option     = $tracking_details['payment_option'];
        $tracking->amount             = $tracking_details['amount'];
        $tracking->source             = (isset($tracking_details['source']) === true) ? $tracking_details['source'] : '';

        // DOESNOT MATCH WEBSITE.
        if (isset($tracking_details['payment_type']) === true) {
            $tracking->payment_type = $tracking_details['payment_type'];
        }

        return $tracking->save();

    }//end initiate()


    /**
     * Get Payment tracking by txnid.
     *
     * @param string $txnid Txn id.
     *
     * @return object.
     */
    public static function getPaymentTrackingByTxnId(string $txnid)
    {
        return self::where('txnid', '=', $txnid)->whereIn('status', [PAYMENT_INITIATED, PAYMENT_VERIFICATION_PENDING])->first();

    }//end getPaymentTrackingByTxnId()


    /**
     * Check if any payment is intiated ever for given booking request id.
     *
     * @param integer $booking_request_id Booking Request id.
     * @param boolean $include_success    Include success status.
     *
     * @return boolean.
     */
    public static function getIsRequestPaymentInitated(int $booking_request_id, bool $include_success=true)
    {
        $status = [
            PAYMENT_INITIATED,
            PAYMENT_VERIFICATION_PENDING,
        ];
        if ($include_success === true) {
            array_push($status, PAYMENT_SUCCESS);
        }

        $count = self::where('booking_request_id', '=', $booking_request_id)->whereIn('status', $status)->count();

        if ($count === 0) {
            return false;
        }

        return true;

    }//end getIsRequestPaymentInitated()


}//end class
