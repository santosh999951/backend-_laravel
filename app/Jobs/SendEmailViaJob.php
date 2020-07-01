<?php
/**
 * SendEmailJob contain all functions releated to create email job.
 */

namespace App\Jobs;

/**
 * Class SendEmailViaJob
 */
class SendEmailViaJob extends Job
{

    /**
     * The data for the job.
     *
     * @var array instance
     */
    protected $data;

    /**
     * Intantiate the job.
     *
     * @return void
     */


    /**
     * Intantiate the job.
     *
     * @param array $data Email job data.
     *
     * @return void.
     */
    public function __construct(array $data)
    {
        $this->data = $data;

    }//end __construct()


    /**
     * Execute the email job.
     *
     * @return void.
     */
    public function handle()
    {

    }//end handle()


}//end class
