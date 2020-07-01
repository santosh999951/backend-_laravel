<?php
/**
 * This file contains the registered artisan commands for our project and the scheduling logic.
 * To run This cron defined in scheduled function, we must have one cron defined in crontab server
 * like following * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
 */

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Console\Commands\{EventListenerCommand, CommunicationListenerCommand, CronCommand, Swagger\GenerateDocsCommand};

/**
 * Class Kernel.
 */
class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        EventListenerCommand::class,
        CommunicationListenerCommand::class,
        CronCommand::class,
        GenerateDocsCommand::class,

    ];


    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule Scheduler object.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $base_path = CRON_LOGS_BASE_PATH;
        // Sample Cron Run
        // $schedule->command('cron:run test')->everyMinute()->runInBackground()->withoutOverlapping()->appendOutputTo($base_path."test.log"); .
        // Aggregate search score in search_score table.
        $schedule->command('cron:run aggregateSearchScore')->daily()->runInBackground()->withoutOverlapping()->appendOutputTo($base_path.'aggregateSearchScore.log');
        $schedule->command('cron:run addProperlyCheckinTask')->hourly()->runInBackground()->withoutOverlapping()->appendOutputTo($base_path.'properlysystemgeneratedtask.log');
        $schedule->command('cron:run addPropertyDataMonthWise')->cron('0 */5 * * *')->runInBackground()->withoutOverlapping()->appendOutputTo($base_path.'addPropertyDataMonthWise.log');

    }//end schedule()


}//end class
