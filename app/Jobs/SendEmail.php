<?php
/**
 * Single point of sending emails.
 */

namespace App\Jobs;

use App\Libraries\Email;
use App\Mail\GenericMailable;
use Mail;

use App\Libraries\v1_6\EmailService;
use App\Libraries\Helper;

/**
 * Class SendEmail
 */
class SendEmail extends Job
{

    /**
     * The data for the job.
     *
     * @var array mail data
     */
    public $mail;


    /**
     * Intantiate the job.
     *
     * @param array $mail Email job data.
     *
     * @return void.
     */
    public function __construct(array $mail)
    {
        $this->mail = $mail;

    }//end __construct()


    /**
     * Execute the email job.
     *
     * @return void
     */
    public function handle()
    {
        $mailable = new GenericMailable(
            $this->mail['subject'],
            $this->mail['view'],
            $this->mail['view_data'],
            ($this->mail['attachment'] ?? [])
        );

        Mail::to($this->mail['to_email'])->send($mailable);

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
