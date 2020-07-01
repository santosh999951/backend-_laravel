<?php
/**
 * Send Push notifications Job
 */

namespace App\Jobs;

use App\Libraries\v1_6\PushNotificationService;
use App\Mail\GenericMailable;
use Mail;

use App\Libraries\Helper;

/**
 * Class SendPushNotification
 */
class SendPushNotification extends Job
{

    /**
     * The data for the job.
     *
     * @var $push Push Notification Data.
     */
    public $push;


    /**
     * Intantiate the job.
     *
     * @param array $push Push Notification job data.
     *
     * @return void.
     */
    public function __construct(array $push)
    {
        $this->push = $push;

    }//end __construct()


    /**
     * Execute the push notification job.
     *
     * @return void
     */
    public function handle()
    {
        $android_device_ids = [];
        $ios_device_ids     = [];

        foreach ($this->push['device_id_mapped_with_device_type'] as $device_id => $type) {
            if ($type === 'android') {
                $android_device_ids[] = $device_id;
            } else {
                $ios_device_ids[] = $device_id;
            }
        }

        $push_notification = new PushNotificationService;

        if (empty($android_device_ids) === false) {
            $push_notification->sendPushNotificationToAndroid($android_device_ids, $this->push['view'], $this->push['property_id'], $this->push['request_hash_id'], $this->push['cta_content']);
        }

        if (empty($ios_device_ids) === false) {
            $push_notification->sendPushNotificationToIos($ios_device_ids, $this->push['view'], $this->push['property_id'], $this->push['request_hash_id'], $this->push['cta_content']);
        }

    }//end handle()


    /**
     * The job failed to process.
     *
     * @param \Exception $exception The exception object.
     *
     * @return void
     */
    public function failed(\Exception $exception)
    {
        $view_data   = [
            'job_exception'   => $exception,
            'tracking_params' => '',
        ];
        $mailer_name = 'failed_job';

        // Mail template params.
        $mailer = Helper::getMailer($mailer_name);
        if (count($mailer) === 0) {
            \Log::Error('Mailer Not found: '.$mailer_name);
        }

        $mailable = new GenericMailable(
            $mailer['subject'],
            $mailer['view'],
            $view_data
        );

        Mail::to(FAILED_JOB_NOTIFICATION_EMAILS)->send($mailable);

    }//end failed()


}//end class
