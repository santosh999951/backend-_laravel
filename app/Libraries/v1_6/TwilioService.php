<?php
/**
 * Twilio Service containing methods related to sending sending sms and call via twilio
 */

namespace App\Libraries\v1_6;

use App;
use \Twilio\Rest\Client;
use Twilio\Exceptions\RestException;

/**
 * Class TwilioService
 */
class TwilioService
{

    /**
     * Variable definition.
     *
     * @var string
     */
    private static $client = null;

    /**
     * Variable definition.
     *
     * @var array
     */
    private static $twilio_numbers = TWILIO_NUMBERS;


    /**
     * Get a twilio connection
     *
     * @param string $sid   Twilio client id.
     * @param string $token Twilio access token.
     *
     * @return object Return twilio connection object
     */
    private static function connectToTwilioClient(string $sid, string $token)
    {
        if (self::$client === null) {
            self::$client = new Client($sid, $token);
        }

        return self::$client;

    }//end connectToTwilioClient()


    /**
     * Send a text message using twilio service
     *
     * @param string $phone   User phone number.
     * @param string $message Message to send.
     *
     * @return array message status (error message also if error)
     */
    public static function sendText(string $phone, string $message)
    {
        $phone  = '+'.$phone;
        $client = self::connectToTwilioClient(TWILIO_SID, TWILIO_TOKEN);
        $result = [];

        try {
            $res = $client->messages->create(
                $phone,
                [
                    'from' => self::$twilio_numbers,
                    'body' => $message,
                ]
            );
            // phpcs:ignore
            if ($res->errorCode === null) {
                $result['status'] = 1;
            }
        } catch (RestException $e) {
            $result['message'] = $e->getMessage();
            $result['status']  = 0;
        }

        return $result;

    }//end sendText()


    /**
     * Make a voice call to send otp using twilio service
     *
     * @param string $phone       User phone number.
     * @param string $otp_to_send Otp to send.
     *
     * @return array message status (error message also if error)
     */
    public static function makeCall(string $phone, string $otp_to_send)
    {
        $str     = $otp_to_send;
        $new_otp = implode(', ', str_split($str));

        $speech = self::getOtpSpeechArrayForPolly($new_otp);

        $generated_audio_file = AwsService::generatePollyAudioAndSaveInS3Bucket($speech);

        if ($generated_audio_file['status'] === 0) {
            return $generated_audio_file;
        }

        $client = self::connectToTwilioClient(TWILIO_SID, TWILIO_TOKEN);

        $voice_url = TWIMLET_URL.$generated_audio_file['message'];

        try {
            $call             = $client->calls->create($phone, self::$twilio_numbers, [ 'url' => $voice_url]);
            $result['status'] = 1;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            $result['status']  = 0;
        }

        return $result;

    }//end makeCall()


    /**
     * Function to generate speech array to use in Amazon Polly service
     *
     * @param string $new_otp Otp string.
     *
     * @return array speech array for new otp
     */
    private static function getOtpSpeechArrayForPolly(string $new_otp)
    {
        $speech = [
            // phpcs:ignore
            'Text' => '<speak>Welcome to Guest Houser. <break time="300ms" /> Your mobile verification code is: <break time="300ms" />'.$new_otp.'.<break time="500ms" />Once-again, your mobile verification code is: <break time="300ms" />'.$new_otp.'. <break time="300ms" />Thank You.</speak>',
            'OutputFormat' => 'mp3',
            'TextType'     => 'ssml',
            'VoiceId'      => 'Raveena',
        ];

        return $speech;

    }//end getOtpSpeechArrayForPolly()


}//end class
