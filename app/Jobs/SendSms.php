<?php
/**
 * Single point of creating sms jobs.
 */

namespace App\Jobs;


use App\Mail\GenericMailable;
use Mail;

use App\Libraries\v1_6\SmsService;
use App\Libraries\Helper;

/**
 * Class SendSms
 */
class SendSms extends Job
{

    /**
     * Dial code of contact.
     *
     * @var string Dial code.
     */
    public $dial_code;

     /**
      * Contact no.
      *
      * @var string Contact no.
      */
    public $to_no;

     /**
      * Message to send.
      *
      * @var string Message
      */
    public $msg;

     /**
      * Sender id
      *
      * @var string Sender Id.
      */
    public $sender_id;


    /**
     * Intantiate the job.
     *
     * @param string $dial_code Dial Code.
     * @param string $to_no     Contact No. to send.
     * @param string $msg       Message to Send.
     * @param string $sender_id Sender Id.
     *
     * @return void.
     */
    public function __construct(string $dial_code, string $to_no, string $msg, string $sender_id)
    {
        $this->dial_code = $dial_code;
        $this->to_no     = $to_no;
        $this->msg       = $msg;
        $this->sender_id = $sender_id;

    }//end __construct()


    /**
     * Execute the Sms job.
     *
     * @return void
     */
    public function handle()
    {
         SmsService::sendOtp($this->dial_code.''.$this->to_no, $this->msg, $this->sender_id);

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
