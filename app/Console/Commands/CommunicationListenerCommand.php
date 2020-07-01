<?php
/**
 * This file contains the command for listening communicaiton queue.
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class CommunicationListenerCommand.
 */
class CommunicationListenerCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'communication-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to communication jobs on sqs';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call(
            'queue:work',
            [
                '--queue' => COMMUNICATION_QUEUE,
                '--tries' => 3,
                '--sleep' => 5,
                '--env'   => \App::environment(),
            ]
        );

    }//end handle()


}//end class
