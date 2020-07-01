<?php
/**
 * Sms service to send sms
 */

namespace App\Libraries;

use App\Libraries\v1_6\AwsService;
use App\Jobs\PushToQueueJob;
use App\Jobs\SendEmailViaJob;

/**
 * Class CommonQueue
 */
class CommonQueue
{


    /**
     * Push email in available queue.
     *
     * @param array  $data       Queue params.
     * @param string $connection Queue connection.
     *
     * @return void
     */
    public static function pushEmail(array $data, string $connection=QUEUE_DRIVER)
    {
        self::push($data, 'email', $connection);

    }//end pushEmail()


    /**
     * Push in available queue.
     *
     * @param array  $data       Queue params.
     * @param string $type       Type of queue eg sms, email.
     * @param string $connection Queue connection.
     *
     * @return void
     */
    public static function push(array $data, string $type, string $connection=QUEUE_DRIVER)
    {
        $queue_function = self::getQueueFunction($connection);
        self::$queue_function($data, $type);

    }//end push()


    /**
     * Get name of function to execute.
     *
     * @param string $connection Queue Connection name.
     *
     * @return string Function name corresponding to connection.
     */
    private static function getQueueFunction(string $connection=QUEUE_DRIVER)
    {
        return 'sendVia'.ucfirst($connection);

    }//end getQueueFunction()


    /**
     * Push data in database job for send sms and email.
     *
     * @param array  $data Data array containing mail sms params.
     * @param string $type Type of queue.
     *
     * @return boolean Return true if push in queue has been processed
     */
    public static function sendViaDatabase(array $data, string $type)
    {
        switch ($type) {
            case 'email':
                try {
                    $send_email_via_job = new SendEmailViaJob($data);
                    dispatch($send_email_via_job->onQueue('email_via_new_api')->onConnection('database'));
                    Helper::logInfo('PROCESS EMAIL JOB : Dispatch email in job table. Data <<<<<<<<<<< '.json_encode($data).' >>>>>>>>>>>>');
                    return true;
                } catch (\Exception $e) {
                    $error_msg = $e->getMessage();
                    Helper::logError('Error in creating job to process email in database'.$e->getMessage());
                }
            break;

            default:
            return false;
        }//end switch

        return false;

    }//end sendViaDatabase()


    /**
     * Push data in sqs queue for send sms and email.
     *
     * @param array  $data Data array containing mail sms params.
     * @param string $type Type of queue.
     *
     * @return boolean Return true if push in queue has been processed
     */
    public static function sendViaSqs(array $data, string $type)
    {
        switch ($type) {
            case 'email':
            return self::pushInMailQueue($data);

            default:
            return false;
        }

    }//end sendViaSqs()


    /**
     * Push data in queue for send mail.
     *
     * @param array  $data   Data containing mail params.
     * @param string $region Region of queue.
     *
     * @return boolean Return true if push in queue has been processed
     */
    public static function pushInMailQueue(array $data, string $region=S3_OTP_AUDIO_REGION)
    {
        try {
            $send_email_via_job = new SendEmailViaJob($data);
            dispatch($send_email_via_job->onQueue(NEW_API_MAIL_QUEUE)->onConnection('sqs'));
            Helper::logInfo('PROCESS EMAIL IN QUEUE : Push email in sqs queue. Data <<<<<<<<<<< '.json_encode($data).' >>>>>>>>>>>>');
            return true;
        } catch (\Exception $e) {
            $error_msg = $e->getMessage();
            Helper::logError('Error in creating job to process invoice '.$e->getMessage());
        }

        return false;

    }//end pushInMailQueue()


}//end class
