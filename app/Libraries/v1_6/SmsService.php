<?php
/**
 * Sms Service containing methods related to sending sms via text or call
 */

namespace App\Libraries\v1_6;

use App;
use App\Libraries\Helper;
use \Carbon\Carbon;
use App\Jobs\SendSms;
use Illuminate\Support\Facades\Queue;

/**
 * Class SmsService
 */
class SmsService
{


    /**
     * Send text sms to a user
     *
     * @param string $phone     User phone number.
     * @param string $message   Message to send.
     * @param string $sender_id Sms send id like ('GSTHSR', 'GHVRFY').
     * @param string $handler   Service using which to send the message.
     *
     * @return array message status (error message also if error)
     */
    public static function send(string $phone, string $message, string $sender_id=DEFAULT_SMS_SENDER_ID, string $handler=SEND_SMS_METHOD)
    {
        $client = self::getTextHandler($handler);
        return $client::sendText($phone, $message, $sender_id);

    }//end send()


    /**
     * Send message to user via call
     *
     * @param string $contact     User phone number.
     * @param string $otp_to_send Otp to send.
     *
     * @return array message status (error message also if error)
     */
    public static function call(string $contact, string $otp_to_send)
    {
        $client = self::getCallHandler();
        return $client::makeCall($contact, $otp_to_send);

    }//end call()


    /**
     * Send otp via text
     *
     * @param string $phone     User phone number.
     * @param string $message   Message to send.
     * @param string $sender_id Sms send id like ('GSTHSR', 'GHVRFY').
     *
     * @return array message status (error message also if error)
     */
    public static function sendOtp(string $phone, string $message, string $sender_id=DEFAULT_SMS_SENDER_ID)
    {
        return self::send($phone, $message, $sender_id, 'Twilio');

    }//end sendOtp()


    /**
     * Get text service handler
     *
     * @param string $handler Service handler name.
     *
     * @return object handler type
     */
    private static function getTextHandler(string $handler)
    {
        if ($handler === 'Twilio') {
            return new TwilioService;
        } else if ($handler === 'SmsCountry') {
            return new SmsCountryService;
        } else {
            return new TextLocalService;
        }

    }//end getTextHandler()


    /**
     * Get call service handler
     *
     * @return object handler type
     */
    private static function getCallHandler()
    {
        return new TwilioService;

    }//end getCallHandler()


    /**
     * Log sms request response
     *
     * @param string $url      Api service url.
     * @param string $message  Message to be sent.
     * @param string $response Api service response.
     *
     * @return null
     */
    public static function logSmsResponse(string $url, string $message, string $response)
    {
        $fname = 'smslog_'.Carbon::now('GMT')->toDateString().'.txt';
        $txt   = Carbon::now('GMT')."\t";
        $txt  .= 'url='.$url."\t";
        $txt  .= 'message='.$message."\t";
        $txt  .= 'response='.$response.PHP_EOL;

        \Storage::disk('sms_log')->append($fname, $txt);
        return null;

    }//end logSmsResponse()


     /**
      * Push sms to queue.
      *
      * @param string $dial_code Dial Code.
      * @param string $to_no     Contact number.
      * @param string $msg       Message.
      * @param string $sender_id Sender Id.
      *
      * @return void
      */
    public function sendSms(string $dial_code, string $to_no, string $msg, string $sender_id=DEFAULT_SMS_SENDER_ID)
    {
        $job = new SendSms($dial_code, $to_no, $msg, $sender_id);
        Queue::pushOn(COMMUNICATION_QUEUE, $job);

    }//end sendSms()


    /**
     * Sms for adding money for trip review.
     *
     * @param string $dial_code       Dial Code.
     * @param string $to_no           Contact number.
     * @param string $amount_currency Amount currency.
     * @param float  $amount_added    Amount added.
     * @param string $added_on_date   Amount added on.
     * @param string $wallet_currency Wallet Currency.
     * @param float  $wallet_balance  Wallet Balance.
     *
     * @return void
     */
    public function sendAddedWalletMoneyForTripReviewSms(
        string $dial_code,
        string $to_no,
        string $amount_currency,
        float $amount_added,
        string $added_on_date,
        string $wallet_currency,
        float $wallet_balance
    ) {
        $params = [
            'amount_added'    => $amount_added,
            'amount_currency' => $amount_currency,
            'added_on_date'   => $added_on_date,
            'wallet_currency' => $wallet_currency,
            'wallet_balance'  => $wallet_balance,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent('add_wallet_money_for_trip_review', $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendAddedWalletMoneyForTripReviewSms()


    /**
     * Sms for New Booking Request Created.
     *
     * @param string  $dial_code           Dial Code.
     * @param string  $to_no               Contact number.
     * @param string  $request_hash_id     Reqest Hash Id.
     * @param string  $property_title      Property Title.
     * @param string  $formatted_check_in  Check In.
     * @param string  $formatted_check_out Check Out.
     * @param integer $guests              Guest Count.
     * @param integer $units               Units Count.
     * @param string  $formatted_amount    Formatted Amount.
     * @param string  $expiry_time         Expiry Time.
     *
     * @return void
     */
    public function sendCreateNewRequestSmsToHost(
        string $dial_code,
        string $to_no,
        string $request_hash_id,
        string $property_title,
        string $formatted_check_in,
        string $formatted_check_out,
        int $guests,
        int $units,
        string $formatted_amount,
        string $expiry_time
    ) {
        $sms_name = 'create_booking_request';

        $params = [
            'request_hash_id'  => $request_hash_id,
            'property_title'   => $property_title,
            'check_in'         => $formatted_check_in,
            'check_out'        => $formatted_check_out,
            'guests'           => $guests,
            'units'            => $units,
            'formatted_amount' => $formatted_amount,
            'expiry_time'      => $expiry_time,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent($sms_name, $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendCreateNewRequestSmsToHost()


    /**
     * Sms for Host to Cancel Booking.
     *
     * @param string $dial_code       Dial Code.
     * @param string $to_no           Contact number.
     * @param string $request_hash_id Reqest Hash Id.
     * @param string $property_title  Property Title.
     *
     * @return void
     */
    public function sendCancelBookingSmsToHost(string $dial_code, string $to_no, string $request_hash_id, string $property_title)
    {
        $sms_name = 'cancel_booking_request_sms_to_host';

        $params = [
            'request_hash_id' => $request_hash_id,
            'property_title'  => $property_title,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent($sms_name, $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendCancelBookingSmsToHost()


    /**
     * Sms for Guest to Cancel Booking.
     *
     * @param string $dial_code       Dial Code.
     * @param string $to_no           Contact number.
     * @param string $request_hash_id Reqest Hash Id.
     * @param string $property_title  Property Title.
     *
     * @return void
     */
    public function sendCancelBookingSmsToGuest(string $dial_code, string $to_no, string $request_hash_id, string $property_title)
    {
        $sms_name = 'cancel_booking_request_sms_to_guest';

        $params = [
            'request_hash_id' => $request_hash_id,
            'property_title'  => $property_title,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent($sms_name, $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendCancelBookingSmsToGuest()


    /**
     * Sms for Booking Request Approved to Guest.
     *
     * @param string $dial_code       Dial Code.
     * @param string $to_no           Contact number.
     * @param string $request_hash_id Reqest Hash Id.
     * @param string $property_title  Property Title.
     * @param string $expiry_time     Expiry Time.
     * @param string $payment_url     Payment Url.
     *
     * @return void
     */
    public function sendBookingRequestApprovedSmsToGuest(string $dial_code, string $to_no, string $request_hash_id, string $property_title, string $expiry_time, string $payment_url)
    {
        $sms_name = 'approved_booking_request_sms_to_guest';

        $params = [
            'request_hash_id' => $request_hash_id,
            'property_title'  => $property_title,
            'expiry_time'     => $expiry_time,
            'payment_url'     => $payment_url,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent($sms_name, $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendBookingRequestApprovedSmsToGuest()


    /**
     * Sms for Booking Request Rejected to Guest.
     *
     * @param string $dial_code       Dial Code.
     * @param string $to_no           Contact number.
     * @param string $request_hash_id Reqest Hash Id.
     * @param string $property_title  Property Title.
     *
     * @return void
     */
    public function sendBookingRequestRejectedSmsToGuest(string $dial_code, string $to_no, string $request_hash_id, string $property_title)
    {
        $sms_name = 'rejected_booking_request_sms_to_guest';

        $params = [
            'request_hash_id' => $request_hash_id,
            'property_title'  => $property_title,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent($sms_name, $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendBookingRequestRejectedSmsToGuest()


    /**
     * Sms for Partial Booking to Guest.
     *
     * @param string $dial_code       Dial Code.
     * @param string $to_no           Contact number.
     * @param string $request_hash_id Reqest Hash Id.
     * @param string $payable_amount  Payable Amount.
     *
     * @return void
     */
    public function sendPartialBookingSmsToGuest(string $dial_code, string $to_no, string $request_hash_id, string $payable_amount)
    {
        $sms_name = 'partial_booking_complete_guest';

        $params = [
            'payable_amount'  => $payable_amount,
            'request_hash_id' => $request_hash_id,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent($sms_name, $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendPartialBookingSmsToGuest()


    /**
     * Sms for Booking to Guest.
     *
     * @param string $dial_code           Dial Code.
     * @param string $to_no               Contact number.
     * @param string $request_hash_id     Reqest Hash Id.
     * @param string $payable_amount      Payable Amount.
     * @param string $traveller_name      Traveller Name.
     * @param string $host_name           Host Name.
     * @param string $property_title      Proeprty Title.
     * @param string $formatted_check_in  Formatted Check In Date.
     * @param string $formatted_check_out Formatted Check out Date.
     * @param string $guests              Guests Count.
     * @param string $units               Units Booked.
     *
     * @return void
     */
    public function sendBookingSmsToGuest(
        string $dial_code,
        string $to_no,
        string $request_hash_id,
        string $payable_amount,
        string $traveller_name,
        string $host_name,
        string $property_title,
        string $formatted_check_in,
        string $formatted_check_out,
        string $guests,
        string $units
    ) {
        $sms_name = 'booking_complete_guest';

        $params = [
            'payable_amount'  => $payable_amount,
            'request_hash_id' => $request_hash_id,
            'traveller_name'  => $traveller_name,
            'host_name'       => $host_name,
            'property_title'  => $property_title,
            'check_in'        => $formatted_check_in,
            'check_out'       => $formatted_check_out,
            'guests'          => $guests,
            'units'           => $units,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent($sms_name, $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendBookingSmsToGuest()


    /**
     * Sms for Property Direction to Guest.
     *
     * @param string $dial_code      Dial Code.
     * @param string $to_no          Contact number.
     * @param string $direction_url  Property Direction Url.
     * @param string $property_title Property title.
     *
     * @return void
     */
    public function sendPropertyDirectionSmsToGuest(string $dial_code, string $to_no, string $direction_url, string $property_title)
    {
        $sms_name = 'property_directions';

        $params = [
            'property_title' => $property_title,
            'direction_url'  => $direction_url,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent($sms_name, $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendPropertyDirectionSmsToGuest()


    /**
     * Sms for Booking to Host.
     *
     * @param string $dial_code           Dial Code.
     * @param string $to_no               Contact number.
     * @param string $request_hash_id     Reqest Hash Id.
     * @param string $formatted_check_in  Formatted Check In Date.
     * @param string $formatted_check_out Formatted Check out Date.
     * @param string $guests              Guests Count.
     * @param string $units               Units Booked.
     *
     * @return void
     */
    public function sendBookingSmsToHost(
        string $dial_code,
        string $to_no,
        string $request_hash_id,
        string $formatted_check_in,
        string $formatted_check_out,
        string $guests,
        string $units
    ) {
        $sms_name = 'booking_complete_host';

        $params = [
            'request_hash_id' => $request_hash_id,
            'check_in'        => $formatted_check_in,
            'check_out'       => $formatted_check_out,
            'guests'          => $guests,
            'units'           => $units,
        ];

        // Create message from view.
        $sms_content = Helper::getSmsContent($sms_name, $params);

        if (empty($sms_content) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $sms_content['msg'], $sms_content['sender_id']);

    }//end sendBookingSmsToHost()


    /**
     * Sms for adding money for Referal Bonus.
     *
     * @param string $dial_code       Dial Code.
     * @param string $to_no           Contact number.
     * @param string $amount_currency Amount currency.
     * @param float  $amount_added    Amount added.
     * @param string $wallet_currency Wallet Currency.
     * @param float  $wallet_balance  Wallet Balance.
     *
     * @return void
     */
    public function sendAddedWalletMoneyForReferalBonusSms(
        string $dial_code,
        string $to_no,
        string $amount_currency,
        float $amount_added,
        string $wallet_currency,
        float $wallet_balance
    ) {
        $params = [
            'amount_added'    => $amount_added,
            'amount_currency' => $amount_currency,
            'wallet_currency' => $wallet_currency,
            'wallet_balance'  => $wallet_balance,
        ];
        $msg    = Helper::getSmsContent('add_wallet_money_for_referal_bonus', $params);

        if (empty($msg) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $msg['msg'], $msg['sender_id']);

    }//end sendAddedWalletMoneyForReferalBonusSms()


    /**
     * Sms for adding money for trip review.
     *
     * @param string $dial_code       Dial Code.
     * @param string $to_no           Contact number.
     * @param string $amount_currency Amount currency.
     * @param float  $amount_added    Amount added.
     * @param string $wallet_currency Wallet Currency.
     * @param float  $wallet_balance  Wallet Balance.
     * @param string $expire_on       Expiry Of Wallet Balance.
     *
     * @return void
     */
    public function sendAddedWalletMoneyForFriendReferalBonusSms(
        string $dial_code,
        string $to_no,
        string $amount_currency,
        float $amount_added,
        string $wallet_currency,
        float $wallet_balance,
        string $expire_on
    ) {
        $params = [
            'amount_added'    => $amount_added,
            'amount_currency' => $amount_currency,
            'wallet_currency' => $wallet_currency,
            'wallet_balance'  => $wallet_balance,
            'expire_on'       => $expire_on,
        ];
        $msg    = Helper::getSmsContent('add_wallet_money_for_friend_referal_bonus', $params);

        if (empty($msg) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $msg['msg'], $msg['sender_id']);

    }//end sendAddedWalletMoneyForFriendReferalBonusSms()


    /**
     * Sms for adding money for First Booking.
     *
     * @param string $dial_code       Dial Code.
     * @param string $to_no           Contact number.
     * @param string $amount_currency Amount currency.
     * @param float  $amount_added    Amount added.
     * @param string $wallet_currency Wallet Currency.
     * @param float  $wallet_balance  Wallet Balance.
     * @param string $expire_on       Expiry Of Wallet Balance.
     *
     * @return void
     */
    public function sendAddedWalletMoneyForFirstBookingBonusSms(
        string $dial_code,
        string $to_no,
        string $amount_currency,
        float $amount_added,
        string $wallet_currency,
        float $wallet_balance,
        string $expire_on
    ) {
        $params = [
            'amount_added'    => $amount_added,
            'amount_currency' => $amount_currency,
            'wallet_currency' => $wallet_currency,
            'wallet_balance'  => $wallet_balance,
            'expire_on'       => $expire_on,
        ];
        $msg    = Helper::getSmsContent('add_wallet_money_for_first_booking_bonus', $params);

        if (empty($msg) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $msg['msg'], $msg['sender_id']);

    }//end sendAddedWalletMoneyForFirstBookingBonusSms()


     /**
      * Sms for Updating Wallet money when Applying Wallet Money .
      *
      * @param string $dial_code          Dial Code.
      * @param string $to_no              Contact number.
      * @param string $amount_currency    Amount currency.
      * @param float  $amount_added       Amount added.
      * @param string $added_on_date      Amount added on.
      * @param string $wallet_currency    Wallet Currency.
      * @param float  $wallet_balance     Wallet Balance.
      * @param string $booking_request_id Booking Request Id.
      *
      * @return void
      */
    public function sendAddedWalletMoneyForApplywalletSms(
        string $dial_code,
        string $to_no,
        string $amount_currency,
        float $amount_added,
        string $added_on_date,
        string $wallet_currency,
        float $wallet_balance,
        string $booking_request_id
    ) {
        $params = [
            'amount_added'       => $amount_added,
            'amount_currency'    => $amount_currency,
            'wallet_currency'    => $wallet_currency,
            'wallet_balance'     => $wallet_balance,
            'added_on_date'      => $added_on_date,
            'booking_request_id' => $booking_request_id,
        ];
        $msg    = Helper::getSmsContent('update_wallet_money_for_apply_wallet_money_booking_bonus', $params);

        if (empty($msg) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $msg['msg'], $msg['sender_id']);

    }//end sendAddedWalletMoneyForApplywalletSms()


    /**
     * Send Payment link sms.
     *
     * @param string  $dial_code        Dial Code.
     * @param string  $to_no            Contact number.
     * @param string  $request_hash_id  Request Hash Id.
     * @param string  $payment_link     Payment Link.
     * @param integer $link_expire_time Link Expire Time.
     *
     * @return void
     */
    public function sendPaymentLinkSms(string $dial_code, string $to_no, string $request_hash_id, string $payment_link, int $link_expire_time)
    {
        $params = [
            'request_hash_id'  => $request_hash_id,
            'payment_link'     => $payment_link,
            'link_expire_time' => $link_expire_time,
        ];

        $msg = Helper::getSmsContent('payment_link_sms', $params);

        if (empty($msg) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $msg['msg'], $msg['sender_id']);

    }//end sendPaymentLinkSms()


    /**
     * Sms for Sending Veridication code .
     *
     * @param string $dial_code         Dial Code.
     * @param string $to_no             Contact number.
     * @param string $verification_code Verification Code.
     *
     * @return void
     */
    public function sendUserResetPasswordOtp(
        string $dial_code,
        string $to_no,
        string $verification_code
    ) {
        $params = ['verification_code' => $verification_code];

        $msg = Helper::getSmsContent('password_reset_otp', $params);

        if (empty($msg) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $msg['msg'], $msg['sender_id']);

    }//end sendUserResetPasswordOtp()


    /**
     * Send login link sms with otp.
     *
     * @param string  $login_url Login Link.
     * @param string  $dial_code Dial Code.
     * @param string  $to_no     Contact number.
     * @param integer $otp       OTP.
     *
     * @return void
     */
    public function sendLoginUrlLinkSms(string $login_url, string $dial_code, string $to_no)
    {
        $params = ['login_url' => $login_url];

        $msg = Helper::getSmsContent('user_loginurl_sms', $params);

        if (empty($msg) === true) {
            return;
        }

        $this->sendSms($dial_code, $to_no, $msg['msg'], $msg['sender_id']);

    }//end sendLoginUrlLinkSms()


}//end class
