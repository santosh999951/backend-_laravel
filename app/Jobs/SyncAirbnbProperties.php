<?php
/**
 * SendEmailJob contain all functions releated to create email job.
 */

namespace App\Jobs;

/**
 * Class SendEmailViaJob
 */
class SyncAirbnbProperties extends Job
{

    /**
     * The data for the job.
     *
     * @var integer $pid
     */
    private $pid;

    /**
     * The data for the job.
     *
     * @var integer $cmp_id
     */
    private $cmp_id;

    /**
     * The data for the job.
     *
     * @var integer $account_id
     */
    private $account_id;


    /**
     * Create a new job instance.
     *
     * @param array $data Channel manager data.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->pid        = $data['pid'];
        $this->cmp_id     = $data['cmp_id'];
        $this->account_id = $data['account_id'];

    }//end __construct()


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

    }//end handle()


}//end class
