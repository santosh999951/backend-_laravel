<?php
/**
 * Model containing data regarding send payment link
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

use \Carbon\Carbon;

/**
 * Class PaymentLink
 */
class PaymentLink extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'payment_link';


    /**
     * Get Default Rating Params.
     *
     * @param array $params Params.
     *
     * @return object Payment link.
     */
    public static function savePaymentLink(array $params)
    {
        $payment_link                = new self;
        $payment_link->user_id       = $params['user_id'];
        $payment_link->valid_till    = Carbon::now('GMT')->addHours(24)->toDateTimeString();
        $payment_link->payment_token = '';
        $payment_link->status        = 0;
        $payment_link->amount        = $params['amount'];
        $payment_link->booking_request_id = $params['request_id'];
        $payment_link->assigned_to        = $params['assigned_to'];
        $payment_link->payment_gateway_id = $params['payment_gateways'];
        $payment_link->admin_id           = 0;

        if ($payment_link->save() === false) {
            return (object) [];
        }

        return $payment_link;

    }//end savePaymentLink()


}//end class
