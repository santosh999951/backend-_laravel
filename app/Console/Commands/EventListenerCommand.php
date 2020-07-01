<?php
/**
 * This file contains the command for listening events queue.
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class EventListenerCommand.
 */
class EventListenerCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to events on sqs';


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
                '--queue' => EVENT_QUEUE,
                '--tries' => 3,
                '--sleep' => 5,
                '--env'   => \App::environment(),
            ]
        );

    }//end handle()


}//end class
