<?php
/**
 * Textlocal Service containing methods related to sending sending sms and call via texlocal
 */

namespace App\Libraries\v1_6;

use App;
use App\Libraries\Helper;

/**
 * Class TextLocalService
 */
class TextLocalService
{


    /**
     * Send text sms to a user using textlocal service
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
        $user = TLOCAL_SMS_API_USERNAME;
        // Hash password.
        $hash = TLOCAL_SMS_API_HASH;

        $message = urlencode($message);
        // True if testing (no message sent while testing, only response).
        $test   = false;
        $params = 'username='.$user.'&hash='.$hash.'&message='.$message.'&sender='.$sender_id.'&numbers='.$phone.'&test='.$test;
        // Make curl request to send sms.
        $curl_request = Helper::sendCurlRequest(TLOCAL_URL, $params, 'POST');
        // Log response here.
        $log_response = SmsService::logSmsResponse(TLOCAL_URL, $message, $curl_request);
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
            if (json_decode($curl_request, false)->status === 'success') {
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
