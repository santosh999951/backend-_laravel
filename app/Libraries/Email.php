<?php
/**
 * Email service to send mailers
 */

namespace App\Libraries;

use Illuminate\Support\Facades\Mail;

use App\Libraries\v1_6\AwsService;

use App\Libraries\Helper;

/**
 * Class Email
 */
class Email
{


    /**
     * Check if the access token has been revoked.
     *
     * @param array $data Data array containing array params.
     *
     * @return boolean Return true if this token has been revoked
     */
    public static function send(array $data)
    {
        // Required params.
        $template           = $data['template'];
        $to                 = $data['to'];
        $email_content_vars = $data['email_content_vars'];
        $subject            = $data['subject'];

        // Collect optional params.
        $cc  = (isset($data['cc']) === true) ?: [];
        $bcc = (isset($data['bcc']) === true) ?: [];

        // Send email.
        Mail::send(
            $template,
            $email_content_vars,
            function ($message) use ($subject, $to, $cc, $bcc) {
                $message->subject($subject)->to($to)->cc($cc)->bcc($bcc);
            }
        );
        return true;

    }//end send()


    /**
     * Push data in queue for send mail.
     *
     * @param string $data   Data json containing mail params.
     * @param string $region Region of queue.
     *
     * @return boolean Return true if push in queue has been processed
     */
    // phpcs:ignore
    public static function push_in_queue(string $data, string $region=S3_OTP_AUDIO_REGION)
    {
        try {
            $sqs = AwsService::getSqsClient($region);
            $sqs->sendMessage(
                [
                    'QueueUrl'    => NEW_API_MAIL_QUEUE,
                    'MessageBody' => $data,
                ]
            );
             return true;
        } catch (\Exception $e) {
             Helper::logError('Error in creating job to process invoice '.$e->getMessage());
        }

        return false;

    }//end push_in_queue()


}//end class
