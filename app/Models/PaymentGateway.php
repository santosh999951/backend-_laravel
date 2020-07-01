<?php
/**
 * Model containing data regarding payment gateways
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use App\Libraries\v1_6\PaymentService;
use App\Libraries\Helper;
use Exception;

/**
 * Class PaymentGateway
 */
class PaymentGateway extends Model
{
    //phpcs:disable

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'payment_gateways';

    /**
     * @var array Cofiguration details of all payment gateways
     */
    public static $payment_gateways = [
                                        // payu
        '1'  => [
            'id'                     => '1',
            'name'                   => 'PAYU_INR',
            'currency'               => 'INR',
            'payment_url'            => PAYU_BASE_URL,
            'api_url'                => PAYU_API_ENDPOINT,
            'merchant_id'            => INR_MERCHANT_ID,
            'salt'                   => INR_SALT,
            'success_url'            => PAYU_SUCCESS_URL,
            'failure_url'            => PAYU_FAILURE_URL
        ],
        '8'  => [
            'id'       => '8',
            'name'     => 'CASHLESS_INR',
            'currency' => 'INR',
        ],
        '9'  => [
            'id'       => '9',
            'name'     => 'CASHLESS_USD',
            'currency' => 'USD',
        ],
        '10' => [
            'id'       => '10',
            'name'     => 'CASHLESS_EUR',
            'currency' => 'EUR',
        ],
        '11' => [
            'id'       => '11',
            'name'     => 'CASHLESS_GBP',
            'currency' => 'GBP',
        ],
        // Razorpay details
        '26' => [
            'id'                     => '26',
            'name'                   => 'RAZORPAY_INR',
            'currency'               => 'INR',
            // 'payment_url' => RAZORPAY_PAYMENT_URL,
            // 'api_url' => RAZORPAY_API_ENDPOINT,
            'merchant_id'            => RAZORPAY_MERCHANT_ID,
            'secret'                 => RAZORPAY_SECRET,
            'success_url'            => RAZORPAY_SUCCESS_URL,
            'failure_url'            => RAZORPAY_FAILURE_URL
        ],
    ];

    /**
     * @var array Cofiguration details of all netbanking banks
     */
    public static $netbanking_banks = [
        'AXIB' => [
            'code' => 'AXIB',
            'name' => 'AXIS Bank NetBanking'
        ],
        'BOIB' => [
            'code' => 'BOIB',
            'name' => 'Bank of India'
        ],
        'BOMB' => [
            'code' => 'BOMB',
            'name' => 'Bank of Maharashtra'
        ],
        'CBIB' => [
            'code' => 'CBIB',
            'name' => 'Central Bank Of India'
        ],
        'CRPB' => [
            'code' => 'CRPB',
            'name' => 'Corporation Bank'
        ],
        'DCBB' => [
            'code' => 'DCBB',
            'name' => 'Development Credit Bank'
        ],
        'FEDB' => [
            'code' => 'FEDB',
            'name' => 'Federal Bank'
        ],
        'HDFB' => [
            'code' => 'HDFB',
            'name' => 'HDFC Bank'
        ],
        'ICIB' => [
            'code' => 'ICIB',
            'name' => 'ICICI Netbanking'
        ],
        'IDBB' => [
            'code' => 'IDBB',
            'name' => 'Industrial Development Bank of India'
        ],
        'INDB' => [
            'code' => 'INDB',
            'name' => 'Indian Bank'
        ],
        'INIB' => [
            'code' => 'INIB',
            'name' => 'IndusInd Bank'
        ],
        'INOB' => [
            'code' => 'INOB',
            'name' => 'Indian Overseas Bank'
        ],
        'JAKB' => [
            'code' => 'JAKB',
            'name' => 'Jammu and Kashmir Bank'
        ],
        'KRKB' => [
            'code' => 'KRKB',
            'name' => 'Karnataka Bank'
        ],
        'KRVB' => [
            'code' => 'KRVB',
            'name' => 'Karur Vysya - Retail Netbanking'
        ],
        'KRVBC' => [
            'code' => 'KRVBC',
            'name' => 'Karur Vysya - Corporate Netbanking'
        ],
        'SBBJB' => [
            'code' => 'SBBJB',
            'name' => 'State Bank of Bikaner and Jaipur'
        ],
        'SBHB' => [
            'code' => 'SBHB',
            'name' => 'State Bank of Hyderabad'
        ],
        'SBIB' => [
            'code' => 'SBIB',
            'name' => 'State Bank of India'
        ],
        'SBMB' => [
            'code' => 'SBMB',
            'name' => 'State Bank of Mysore'
        ],
        'SBTB' => [
            'code' => 'SBTB',
            'name' => 'State Bank of Travancore'
        ],
        'SOIB' => [
            'code' => 'SOIB',
            'name' => 'South Indian Bank'
        ],
        'UBIB' => [
            'code' => 'UBIB',
            'name' => 'Union Bank - Retail Netbanking'
        ],
        'UBIBC' => [
            'code' => 'UBIBC',
            'name' => 'Union Bank - Corporate Netbanking',
        ],
        'UNIB' => [
            'code' => 'UNIB',
            'name' => 'United Bank Of India',
        ],
        'VJYB' => [
            'code' => 'VJYB',
            'name' => 'Vijaya Bank',
        ],
        'YESB' => [
            'code' => 'YESB',
            'name' => 'Yes Bank',
        ],
        'CUBB' => [
            'code' => 'CUBB',
            'name' => 'CityUnion',
        ],
        'CABB' => [
            'code' => 'CABB',
            'name' => 'Canara Bank',
        ],
        'SBPB' => [
            'code' => 'SBPB',
            'name' => 'State Bank of Patiala',
        ],
        'DSHB' => [
            'code' => 'DSHB',
            'name' => 'Deutsche bank Netbanking',
        ],
        '162B' => [
            'code' => '162B',
            'name' => 'kotak bank Netbanking',
        ],
        'DLSB' => [
            'code' => 'DLSB',
            'name' => 'Dhanlaxmi Netbanking',
        ],
        'INGB' => [
            'code' => 'INGB',
            'name' => 'ING Vysya Netbanking',
        ],
        'CSBN' => [
            'code' => 'CSBN',
            'name' => 'Catholic Syrian Bank',
        ],
        'PNBB' => [
            'code' => 'PNBB',
            'name' => 'Punjab Nation Bank - Retail Netbanking',
        ],
        'CPNB' => [
            'code' => 'CPNB',
            'name' => 'Punjab Nation Bank - Corporate Netbanking',
        ]
    ];

    /**
     * @var array Cofiguration details of all netbanking banks for razorpay
    */
    public static $razorpay_netbanking_banks = [
        'AXIB' => [
            'code' => 'AXIB',
            'name' => 'AXIS Bank NetBanking'
        ],
        'AIRP'=>[
            'code' =>'AIRP',
            'name' => 'Airtel Payments Bank'
        ],
        'ABPB'=>[
            'code' =>'ABPB',
            'name' => 'Aditya Birla Idea Payments Bank'
        ],
        'ALLA'=>[
            'code' =>'ALLA',
            'name' => 'Allahabad Bank'
        ],
        'ANDB'=>[
            'code' =>'ANDB',
            'name' => 'Andhra Bank'
        ],
        'AUBL'=>[
            'code' =>'AUBL',
            'name' => 'AU Small Finance Bank'
        ],
        'BACB'=>[
            'code'=>'BACB',
            'name' => 'Bassein Catholic Co-operative Bank'
        ],
        'BARB'=>[
            'code' =>'BARB_R',
            'name' => 'Bank of Baroda - Retail Banking',
        ],
        'BBKM'=>[
            'code' =>'BBKM',
            'name' => 'Bank of Bahrein and Kuwait'
        ],
        'BDBL'=>[
            'code' =>'BDBL',
            'name' => 'Bandhan Bank'
        ],
        'BKDN'=>[
            'code' =>'BKDN',
            'name' => 'Dena Bank'
        ],
        'BKID'=>[
            'code' =>'BKID',
            'name' => 'Bank of India'
        ],
        'CBIN'=>[
            'code' =>'CBIN',
            'name' => 'Central Bank of India'
        ],
        'CIUB'=>[
            'code' =>'CIUB',
            'name' => 'City Union Bank'
        ],
        'CNRB'=>[
            'code' =>'CNRB',
            'name' => 'Canara Bank'
        ],
        'CORP'=>[
            'code' =>'CORP',
            'name' => 'Corporation Bank'
        ],
        'COSB'=>[
            'code' =>'COSB',
            'name' => 'Cosmos Co-operative Bank'
        ],
        'CSBK'=>[
            'code' =>'CSBK',
            'name' => 'Catholic Syrian Bank'
        ],
        'DBSS'=>[
            'code' =>'DBSS',
            'name' => 'Development Bank of Singapore'
        ],
        'DCBL'=>[
            'code'=>'DCBL',
            'name' => 'DCB Bank'
        ],
        'DEUT'=>[
            'code'=>'DEUT',
            'name' => 'Deutsche Bank'
        ],
        'DLXB'=>[
            'code'=>'DLXB',
            'name' => 'Dhanlaxmi Bank'
        ],
        'DLXB_C'=>[
            'code'=>'DLXB_C',
            'name' => 'Dhanlaxmi Bank - Corporate Banking'
        ],
        'ESAF'=>[
            'code'=>'ESAF',
            'name' => 'ESAF Small Finance Bank'
        ],
        'ESFB'=>[
            'code'=>'ESFB',
            'name' => 'Equitas Small Finance Bank'
        ],
        'FDRL'=>[
            'code'=>'FDRL',
            'name' => 'Federal Bank'
        ],
        'HDFC'=>[
            'code'=>'HDFC',
            'name' => 'HDFC Bank'
        ],
        'IBKL'=>[
            'code'=>'IBKL',
            'name' => 'IDBI'
        ],
        'IBKL_C'=>[
            'code'=>'IBKL_C',
            'name' => 'IDBI - Corporate Banking'
        ],
        'ICIC'=>[
            'code'=>'ICIC',
            'name' => 'ICICI Bank'
        ],
        'IDFB'=>[
            'code'=>'IDFB',
            'name' => 'IDFC FIRST Bank'
        ],
        'IDIB'=>[
            'code'=>'IDIB',
            'name' => 'Indian Bank'
        ],
        'INDB'=>[
            'code'=>'INDB',
            'name' => 'Indusind Bank'
        ],
        'IOBA'=>[
            'code'=>'IOBA',
            'name' => 'Indian Overseas Bank'
        ],
        'JAKA'=>[
            'code'=>'JAKA',
            'name' => 'Jammu and Kashmir Bank'
        ],
        'JSBP'=>[
            'code'=>'JSBP',
            'name' => 'Janata Sahakari Bank (Pune)'
        ],
        'KARB'=>[
            'code'=>'KARB',
            'name' => 'Karnataka Bank'
        ],
        'KCCB'=>[
            'code'=>'KCCB',
            'name' => 'Kalupur Commercial Co-operative Bank'
        ],
        'KJSB'=>[
            'code'=>'KJSB',
            'name' => 'Kalyan Janata Sahakari Bank'
        ],
        'KKBK'=>[
            'code'=>'KKBK',
            'name' => 'Kotak Mahindra Bank'
        ],
        'KVBL'=>[
            'code'=>'KVBL',
            'name' => 'Karur Vysya Ba"k'
       ],
       'LAVB_C'=>[
            'code'=>'LAVB_C',
            'name' => 'Lakshmi Vilas Bank - Corporate Banking'
        ],
        'LAVB_R'=>[
            'code'=>'LAVB_R',
            'name' => 'Lakshmi Vilas Bank - Retail Banking'
        ],
        'MAHB'=>[
            'code'=>'MAHB',
            'name' => 'Bank of Maharashtra'
        ],
        'MSNU'=>[
            'code'=>'MSNU',
            'name' => 'Mehsana Urban Co-operative Bank'
        ],
        'NESF'=>[
            'code'=>'NESF',
            'name' => 'North East Small Finance Bank'
        ],
        'NKGS'=>[
            'code' =>'NKGS',
            'name' => 'NKGSB Co-operative Bank'
        ],
        'ORBC'=>[
            'code'=>'ORBC',
            'name' => 'Oriental Bank of Commerce'
        ],
        'PMCB'=>[
            'code'=>'PMCB',
            'name' => 'Punjab & Maharashtra Co-operative Bank'
        ],
        'PSIB'=>[
            'code'=>'PSIB',
            'name' => 'Punjab & Sind Bank'
        ],
        'PUNB_R'=>[
            'code'=>'PUNB_R',
            'name' => 'Punjab National Bank - Retail Banking'
        ],
        'RATN'=>[
            'code'=>'RATN',
            'name' => 'RBL Bank'
        ],
        'RATN_C'=>[
            'code'=>'RATN_C',
            'name' => 'RBL Bank - Corporate Banking'
        ],
        'SBBJ'=>[
            'code'=>'SBBJ',
            'name' => 'State Bank of Bikaner and Jaipur'
        ],
        'SBHY'=>[
            'code'=>'SBHY',
            'name' => 'State Bank of Hyderabad'
        ],
        'SBIN'=>[
            'code'=>'SBIN',
            'name' => 'State Bank of India'
        ],
        'SBMY'=>[
            'code'=>'SBMY',
            'name' => 'State Bank of Mysore'
        ],
        'SBTR'=>[
            'code'=>'SBTR',
            'name' => 'State Bank of Travancore'
        ],
        'SCBL'=>[
            'code'=>'SCBL',
            'name' => 'Standard Chartered Bank'
        ],
        'SIBL'=>[
            'code'=>'SIBL',
            'name' => 'South Indian Bank'
        ],
        'SRCB'=>[
            'code'=>'SRCB',
            'name' => 'Saraswat Co-operative Bank'
        ],
        'STBP'=>[
            'code'=>'STBP',
            'name' => 'State Bank of Patiala'
        ],
        'SURY'=>[
            'code'=>'SURY',
            'name' => 'Suryoday Small Finance Bank'
        ],
        'SVCB'=>[
            'code'=>'SVCB',
            'name' => 'Shamrao Vithal Co-operative Bank'
        ],
        'SVCB_C'=>[
            'code'=>'SVCB_C',
            'name' => 'Shamrao Vithal Bank - Corporate Banking'
        ],
        'SYNB'=>[
            'code'=>'SYNB',
            'name' => 'Syndicate Bank'
        ],
        'TBSB'=>[
            'code'=>'TBSB',
            'name' => 'Thane Bharat Sahakari Bank'
        ],
        'TJSB'=>[
            'code'=>'TJSB',
            'name' => 'Thane Janata Sahakari Bank'
        ],
        'TMBL'=>[
            'code'=>'TMBL',
            'name' => 'Tamilnadu Mercantile Bank'
        ],
        'TNSC'=>[
            'code'=>'TNSC',
            'name' => 'Tamilnadu State Apex Co-operative Bank'
        ],
        'UBIN'=>[
            'code'=>'UBIN',
            'name' => 'Union Bank of India'
        ],
        'UCBA'=>[
            'code'=>'UCBA',
            'name' => 'UCO Bank'
        ],
        'UTBI'=>[
            'code'=>'UTBI',
            'name' => 'United Bank of India'
        ],
        'UTIB'=>[
            'code'=>'UTIB',
            'name' => 'Axis Bank'
        ],
        'VARA'=>[
            'code'=>'VARA',
            'name' => 'Varachha Co-operative Bank'
        ],
        'VIJB'=>[
            'code'=>'VIJB',
            'name' => 'Vijaya Bank'
        ],
        'YESB'=>[
            'code'=>'YESB',
            'name' => 'Yes Bank'
        ],
        'YESB_C'=>[
            'code'=>'YESB_C',
            'name' => 'Yes Bank - Corporate Banking'
        ],
        'ZCBL'=>[
            'code'=>'ZCBL',
            'name' => 'Zoroastrian Co-operative Bank'
        ],
    ];    

    /**
     * @var array Cofiguration details of all Debit Card types
     */
    public static $debit_card_types = [
        'VISA' => [  
            'code' => 'VISA',
            'name' => 'Visa Cards',
        ],
        'MAST' => [  
            'code' => 'MAST',
            'name' => 'MasterCard',
        ],
        'SMAE' => [  
            'code' => 'SMAE',
            'name' => 'SBI Maestro',
        ],
        'MAES' => [  
            'code' => 'MAES',
            'name' => 'Other Maestro',
        ],
        'RUPAY' => [  
            'code' => 'RUPAY',
            'name' => 'Rupay ',
        ],
    ];

    /**
     * @var array Cofiguration details of all Credit Card Types
     */
    public static $credit_card_types = [
        'CC' => [
            'code' => 'CC',
            'name' => 'Visa/Master',
        ],
        'AMEX' => [
            'code' => 'AMEX',
            'name' => 'AMEX',
        ],
        'DINR' => [
            'code' => 'DINR',
            'name' => 'Diners',
        ]
    ];

    /**
     * @var array Cofiguration details of all Upi Types
     */
    public static $upi_types = [
        
        'okhdfcbank' =>[
            'code' => 'okhdfcbank',
            'name' => 'HDFC',
        ],
        'okicici' =>[
            'code' => 'okicici',
            'name' => 'ICICI'
        ],
        'oksbi' =>[
            'code' => 'oksbi',
            'name' => 'SBI'
        ],
        'okaxis' =>[
            'code' => 'okaxis',
            'name' => 'AXIS'
        ],
      
    ];


    /**
     * Get active payment gateways.
     *
     * @param EloquentQuery $query Query to be passed.
     *
     * @return EloquentQuery
     */
    public static function scopeActive(EloquentQuery $query)
    {
        return $query->where('active', 1);

    }//end scopeActive()


    /**
     * Get available payment gateways.
     *
     * @param string $currency Currency code.
     *
     * @return boolean
     */
    public static function isPaymentGateEnabledForCurrency(string $currency)
    {
        return (self::where('currency', $currency)->active()->count() > 0) ? true : false;

    }//end isPaymentGateEnabledForCurrency()


    /**
     * Returns active payment gateway details according to currency
     *
     * @param  string $currency
     * @return array
     */
    public static function getActiveGateway($currency, $type='')
    {
        try{
            $gateway = self::where('currency', $currency);

            if (empty($type) === true) {
                $gateway->where('active', 1);
            } else {
                $gateway->where('type', $type);
            }

            $gateway         = $gateway->first();
            $details         = self::getDetails($gateway->id);
            $details['type'] = $gateway->type;
            
            return $details;
        }catch(Exception $e){
            return null;
        }
    }//end getActiveGateway()


    /**
     * Returns active payment gateway details according to payment_gateway_id
     *
     * @param  integer $payment_gateway_id
     * @return array
     */
    public static function getDetails($payment_gateway_id)
    {
        return self::$payment_gateways[$payment_gateway_id];

    }//end getDetails()


    public static function getPaymentProcessingDetails($params, $payment_gateway)
    {
        $function = 'get'.ucfirst($payment_gateway['type']).ucfirst($params['payment_method']).'PaymentProcessingDetails';

        return self::$function($params, $payment_gateway);

    }//end getPaymentProcessingDetails()


    public static function getRazorpaySdkPaymentProcessingDetails($params, $payment_gateway)
    {
        $request_id    = $params['booking_request_id'];
        $amount        = $params['amount'];
        $currency_code = $params['currency_code'];
        $surl          = $payment_gateway['success_url'];
        $furl          = $payment_gateway['failure_url'];
        $txnid         = Booking::createRazorPayOrder($request_id, $amount, $currency_code);
        // HANDLE SI PAYMENT DETAILS
        return [
            'txnid'           => $txnid,
            'surl'            => $surl,
            'furl'            => $furl,
            'method'          => 'sdk',
            'gateway'         => 'razorpay',
            'title'           => '',
            'amount'          => $amount,
            'amount_in_paisa' => (round($amount, 2) * 100),
        ];

    }//end getRazorpaySdkPaymentProcessingDetails()


    public static function getRazorpayWebPaymentProcessingDetails($params, $payment_gateway)
    {
        $request_id        = $params['booking_request_id'];
        $amount            = $params['amount'];
        $currency_code     = $params['currency_code'];
        $surl              = $payment_gateway['success_url'];
        $furl              = $payment_gateway['failure_url'];
        $txnid             = Booking::createRazorPayOrder($request_id, $amount, $currency_code);
        $traveller_email   = $params['traveller_email'];
        $traveller_contact = $params['traveller_contact'];
        $traveller_name    = $params['traveller_name'];
        $source            = $params['source'];
        $origin            = $params['origin'];

        $url_append = '?source='.$source.'&origin='.$origin.'&razorpay_order_id='.$txnid;

        return [
            'merchant_key'    => RAZORPAY_MERCHANT_ID,
            'logo'            => WEBSITE_URL.'/images/images_m/siteMobLogo.png',
            'txnid'           => $txnid,
            'surl'            => $surl.$url_append,
            'furl'            => $furl.$url_append,
            'method'          => 'web',
            'gateway'         => 'razorpay',
            'title'           => '',
            'amount'          => $amount,
            'amount_in_paisa' => (int)(round($amount, 2) * 100),
            'firstname'       => $traveller_name,
            'email'           => $traveller_email,
            'phone'           => $traveller_contact,
        ];

    }//end getRazorpayWebPaymentProcessingDetails()


    public static function getPayuWebPaymentProcessingDetails($params, $payment_gateway)
    {
        $request_id        = $params['booking_request_id'];
        $pid               = $params['pid'];
        $traveller_name    = $params['traveller_name'];
        $traveller_email   = $params['traveller_email'];
        $traveller_contact = $params['traveller_contact'];
        $traveller_user_id = $params['traveller_user_id'];
        $amount            = $params['amount'];
        $source            = $params['source'];
        $origin            = $params['origin'];
        $payment_option    = $params['payment_option'];

        $txnid                  = md5('pre'.time().str_random(5).$request_id);
        $payment_gateway_params = [
            'merchant_key' => $payment_gateway['merchant_id'],
            'salt'         => $payment_gateway['salt'],
            'productinfo'  => $pid,
            'txnid'        => $txnid,
            'firstname'    => $traveller_name,
            'email'        => $traveller_email,
            'amount'       => $amount,
        ];

        $hash               = PaymentService::create_payment_hash($payment_gateway_params);
        $pg_params['phone'] = $traveller_contact;

        $url_append = '?source='.$source.'&origin='.$origin;

        $surl = $payment_gateway['success_url'];
        $furl = $payment_gateway['failure_url'];

        // HANDLE SI PAYMENT DETAILS.
        $payment_processing_details = [
            'action'        => $payment_gateway['payment_url'].'/_payment.php',
            'merchant_key'  => $payment_gateway['merchant_id'],
            'hash'          => $hash,
            'txnid'         => $txnid,
            'amount'        => $amount,
            'firstname'     => $traveller_name,
            'email'         => $traveller_email,
            'phone'         => $traveller_contact,
            'productinfo'   => $pid,
            'surl'          => $surl.$url_append,
            'furl'          => $furl.$url_append,
            'drop_category' => 'EMI,CASH,COD',
            'pg_name'       => $payment_gateway['name'],
            'si_payment'    => 0,
            'method'        => 'web',
            'gateway'       => 'payu',
        ];

        if (PAYMENT_NO[$payment_option] === SI_PAYMENT) {
            $payment_processing_details['user_credentials'] = Helper::encodeUserId($traveller_user_id).':guesthouser';
            $payment_processing_details['si']               = 1;
            $payment_processing_details['si_payment']       = 1;
        }

        return $payment_processing_details;

    }//end getPayuWebPaymentProcessingDetails()


}//end class
