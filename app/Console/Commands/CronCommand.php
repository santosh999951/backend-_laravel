<?php
/**
 * This file contains CronCommand that call functions in cronMethods function.
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Console\Commands\CronMethods;

/**
 * Class CronCommand.
 */
class CronCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:run {arguments*} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To run differnt cron using this only.Pass space seprated params, first params would be function name and others will be passed as array to function';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument('arguments');

        $function_to_call = $arguments[0];
        // Removing function name.
        array_shift($arguments);

        $cron   = new CronMethods;
        $output = call_user_func_array([$cron, $function_to_call], [$arguments]);
        $this->info($output);

    }//end handle()


}//end class
