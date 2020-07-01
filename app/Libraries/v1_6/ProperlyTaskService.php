<?php
/**
 * Properly task Service containing methods related to Properly task.
 */

namespace App\Libraries\v1_6;
use App\Libraries\v1_6\PushNotificationService;
use App\Models\ProperlyTask;

/**
 * Class ProperlyService
 */
class ProperlyTaskService
{

	  /**
     * Push Notification service object for sending Push Notifications.
     *
     * @var $push_notification
     */
    protected $push_notification;


    /**
     * Email service object for sending emails.
     *
     * @param EmailService            $email_service     Object.
     * @param SmsService              $sms_service       Object.
     * @param PushNotificationService $push_notification Object.
     */
    public function __construct( PushNotificationService $push_notification=null)
    {
        $this->push_notification = $push_notification;
    }

	/**
     * Send  Booking  Notification to Guest
     *
     * @param ProperlyTask $properly_task Booking Request Object.
     *
     * @return void
     */
    public function sendCreateTaskNotification(ProperlyTask $properly_task, array $recipient_id)
    {

        $this->push_notification->sendCreateTaskNotification($properly_task , $recipient_id);

    }//end sendBookingPushNotificationsToGuest()

}