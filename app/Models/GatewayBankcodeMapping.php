<?php
/**
 * Gateway Bankcode Mapping Model.
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;


/**
 * Class GatewayBankcodeMapping
 */
class GatewayBankcodeMapping extends Model
{

    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'gateway_bankcode_mapping';


    /**
     * Gateway payment options.
     *
     * @param integer $gateway_id Gateway Id.
     *
     * @return array
     */
    public static function getPaymentOptions(int $gateway_id)
    {
        $result = [];

        $data = self::select('payment_bankcode.code', 'payment_bankcode.name', 'gateway_bankcode_mapping.type')->leftjoin('payment_bankcode', 'payment_bankcode.id', '=', 'gateway_bankcode_mapping.payment_bankcode_id')->where('gateway_bankcode_mapping.gateway_id', $gateway_id)->get();
        foreach ($data as $bank_code) {
            $result[$bank_code->type][] = [
                'name' => $bank_code->name,
                'code' => $bank_code->code,
            ];
        }

        return $result;

    }//end getPaymentOptions()


    /**
     * Get Bankcode based on gateway.
     *
     * @param string  $bankcode   Bankcode.
     * @param integer $gateway_id Gateway Id.
     *
     * @return array
     */
    public static function getPaymentOptionCode(string $bankcode, int $gateway_id)
    {
        $data = self::select('gateway_bankcode_mapping.code', 'gateway_bankcode_mapping.type')->leftjoin('payment_bankcode', 'payment_bankcode.id', '=', 'gateway_bankcode_mapping.payment_bankcode_id')->where('gateway_bankcode_mapping.gateway_id', $gateway_id)->where('payment_bankcode.code', $bankcode)->where('gateway_bankcode_mapping.status', '1')->get();

        $code = (isset($data[0]->code) === true) ? $data[0]->code : 'CC';
        $type = (isset($data[0]->type) === true) ? $data[0]->type : null;
        return [
            'code' => $code,
            'type' => $type,
        ];

    }//end getPaymentOptionCode()


}//end class
