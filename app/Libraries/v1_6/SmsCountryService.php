<?php
/**
 * SmsCountry Service containing methods related to sending sending sms and call via smscountry
 */

namespace App\Libraries\v1_6;

use App;
use App\Libraries\Helper;

/**
 * Class SmsCountryService
 */
class SmsCountryService
{


    /**
     * Send text sms to a user using smscountry service
     *
     * @param string $phone     User phone number.
     * @param string $message   Message to send.
     * @param string $sender_id Sms send id like ('GSTHSR', 'GHVRFY').
     *
     * @return array message status (error message also if error)
     */
    public static function sendText(string $phone, string $message, string $sender_id)
    {
        // User name.
        $user = SMS_COUNTRY_API_USERNAME;
        // Password.
        $password = SMS_COUNTRY_API_PASSWORD;
        // Type Of Message N- Normal message, LNG - Unicode Message.
        $messagetype = 'N';
        // Delivery Reports.
        $delivery_reports = 'Y';
        $message          = urlencode($message);

        $params = 'User='.$user.'&passwd='.$password.'&mobilenumber='.$phone.'&message='.$message.'&sid='.$sender_id.'&mtype='.$messagetype.'&DR='.$delivery_reports;
        // Make curl request to send sms.
        $curl_request = Helper::sendCurlRequest(SMS_COUNTRY_URL, $params, 'POST');

        // Log response here.
        $log_response = SmsService::logSmsResponse(SMS_COUNTRY_URL, $message, $curl_request);

        // Process response here.
        if ($curl_request === false) {
            return [
                'status'  => 0,
                'message' => 'Error in curl request.',
            ];
        } else if (empty($curl_request) === true) {
            return [
                'status'  => 0,
                'message' => 'No response from sms service',
            ];
        } else {
            if (strpos(strtolower($curl_request), 'ok') !== false) {
                return ['status' => 1];
            } else {
                return [
                    'status'  => 0,
                    'message' => 'Error while sending message.',
                ];
            }
        }

    }//end sendText()


    /**
     * Use this function to make a voice call to a user
     *
     * @param string $phone       User phone number.
     * @param string $otp_to_send Otp to send.
     *
     * @return null
     */
    public static function makeCall(string $phone, string $otp_to_send)
    {
        return null;

    }//end makeCall()


}//end class
