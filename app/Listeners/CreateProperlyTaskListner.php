<?php
/**
 * Listener for the properly task Create event.
 */

namespace App\Listeners;

use  App\Libraries\v1_6\ProperlyTaskService;
use App\Events\CreateProperlyTask;


/**
 * Class CreateProperlyTaskListener for handling Request Create event.
 */
class CreateProperlyTaskListner extends Listener
{

    /**
     * Properly Task service.
     *
     * @var ProperlyTaskService $properly_task_service Booking Request Service.
     */
    protected $properly_task_service;


    /**
     * Initialize the object.
     *
     * @param ProperlyTaskService $properly_task_service Properly Task Service.
     */
    public function __construct(ProperlyTaskService $properly_task_service)
    {
        $this->properly_task_service = $properly_task_service;

    }//end __construct()


    /**
     * Handle the event.
     *
     * @param CreateBooking $event Event.
     *
     * @return void
     */
    public function handle(CreateProperlyTask $event)
    {
        $this->properly_task_service->sendCreateTaskNotification($event->properly_task, $event->recipient_id );

    }//end handle()


}//end class
