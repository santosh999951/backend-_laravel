<?php
/**
 * PushNotificationServiceTest containing tests for PushNotificationTest.
 */

use App\Libraries\Helper;
use App\Libraries\v1_6\PushNotificationService;
use App\Jobs\SendPushNotification;
use Illuminate\Support\Facades\Queue;
use App\Models\MobileAppDevice;

/**
 * Class PushNotificationServiceTest
 *
 * @group Services
 */
class PushNotificationServiceTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Setup.
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();
        // Push service mock, Use this to call functions.
        $this->mocked_push_service = $this->mock(PushNotificationService::class);
        Queue::fake();

    }//end setup()


    /**
     * Test booking push notification with proper msg.
     *
     * @return void
     */
    public function test_function_send_cancel_booking_push_notifications_to_host()
    {
        $create_booking_request = $this->createBookingRequests(CANCELLED_AFTER_PAYMENT);
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $host_id                = $create_booking_request['host']->id;

        // Register device for push notifictaion.
        $register_device = $this->registerDevice(['device_unique_id' => $this->device_unique_id, 'device_type' => 'android', 'user_id' => $host_id, 'device_id' => 'sdcvbnmlkjhgcgvvjjvjbvj']);

        $content = [
            'message'           => 'Your booking '.$request_hash_id.' with GuestHouser has been cancelled. Your property is now free to receive bookings for these dates.',
            'title'             => 'Booking Cancellation '.$request_hash_id,
            'sub_title'         => '',
            'notification_type' => BOOKINGS_PUSH_NOTIFICATION,
            'status'            => CANCELLED_AFTER_PAYMENT,
            'for'               => 'host',
            'type'              => 'trip',
            'request_hash_id'   => $request_hash_id,
        ];

        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($host_id);

        $this->mocked_push_service->shouldReceive('sendPushNotification')->with($user_devices_mapped_with_type, $content)->once()->andReturn('');
        $this->mocked_push_service->sendCancelBookingPushNotificationsToHost($request_hash_id, $host_id);

    }//end test_function_send_cancel_booking_push_notifications_to_host()


    /**
     * Test request push notifications
     *
     * @return void
     */
    public function test_function_send_cancel_request_push_notifications_to_host()
    {
        $create_booking_request = $this->createBookingRequests(REQUEST_CANCELLED);
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $host_id                = $create_booking_request['host']->id;

        // Register device for push notifictaion.
        $register_device = $this->registerDevice(['device_unique_id' => $this->device_unique_id, 'device_type' => 'android', 'user_id' => $host_id, 'device_id' => 'sdcvbnmlkjhgcgvvjjvjbvj']);

        $content = [
            'message'           => 'Sorry! We regret to inform you that the explorer has cancelled the booking request.',
            'title'             => 'GuestHouser ',
            'sub_title'         => '',
            'notification_type' => BOOKINGS_PUSH_NOTIFICATION,
            'status'            => NEW_REQUEST,
            'for'               => 'host',
            'type'              => 'request',
            'request_hash_id'   => $request_hash_id,
        ];

        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($host_id);

        $this->mocked_push_service->shouldReceive('sendPushNotification')->with($user_devices_mapped_with_type, $content)->once()->andReturn('');
        $this->mocked_push_service->sendCancelRequestPushNotificationsToHost($request_hash_id, $host_id);

    }//end test_function_send_cancel_request_push_notifications_to_host()


    /**
     * Test new request push notification to host.
     *
     * @return void
     */
    public function test_function_send_new_request_push_notification_to_host()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $host_id                = $create_booking_request['host']->id;

         // Register device for push notifictaion.
        $register_device = $this->registerDevice(['device_unique_id' => $this->device_unique_id, 'device_type' => 'android', 'user_id' => $host_id, 'device_id' => 'sdcvbnmlkjhgcgvvjjvjbvj']);

        $content = [
            'message'           => 'You have a new booking request. Please respond to confirm the booking now.',
            'title'             => 'GuestHouser',
            'sub_title'         => '',
            'notification_type' => BOOKINGS_PUSH_NOTIFICATION,
            'status'            => NEW_REQUEST,
            'for'               => 'host',
            'type'              => 'request',
            'request_hash_id'   => $request_hash_id,
        ];
        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($host_id);

        $this->mocked_push_service->shouldReceive('sendPushNotification')->with($user_devices_mapped_with_type, $content)->once()->andReturn('');
         $this->mocked_push_service->sendNewRequestPushNotificationsToHost($request_hash_id, $host_id);

    }//end test_function_send_new_request_push_notification_to_host()


     /**
      * Test approved request push notification to guset.
      *
      * @return void
      */
    public function test_function_send_approved_request_push_notification_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $user_id                = $create_booking_request['traveller']->id;

         // Register device for push notifictaion.
        $register_device = $this->registerDevice(['device_unique_id' => $this->device_unique_id, 'device_type' => 'android', 'user_id' => $user_id, 'device_id' => 'sdcvbnmlkjhgcgvvjjvjbvj']);

        $content = [
            'message'           => 'Your booking request has been approved.',
            'title'             => 'GuestHouser',
            'sub_title'         => '',
            'notification_type' => BOOKINGS_PUSH_NOTIFICATION,
            'status'            => NEW_REQUEST,
            'for'               => 'traveller',
            'type'              => 'request',
            'request_hash_id'   => $request_hash_id,
        ];

        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($user_id);

        $this->mocked_push_service->shouldReceive('sendPushNotification')->with($user_devices_mapped_with_type, $content)->once()->andReturn('');
         $this->mocked_push_service->sendApprovedRequestPushNotificationsToGuest($request_hash_id, $user_id);

    }//end test_function_send_approved_request_push_notification_to_guest()


     /**
      * Test reject request push notification to guest.
      *
      * @return void
      */
    public function test_function_send_reject_request_push_notification_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $user_id                = $create_booking_request['traveller']->id;

         // Register device for push notifictaion.
        $register_device = $this->registerDevice(['device_unique_id' => $this->device_unique_id, 'device_type' => 'android', 'user_id' => $user_id, 'device_id' => 'sdcvbnmlkjhgcgvvjjvjbvj']);

        $content = [
            'message'           => 'Sorry! Your booking request has been rejected.',
            'title'             => 'GuestHouser',
            'sub_title'         => '',
            'notification_type' => BOOKINGS_PUSH_NOTIFICATION,
            'status'            => NEW_REQUEST,
            'for'               => 'traveller',
            'type'              => 'request',
            'request_hash_id'   => $request_hash_id,
        ];

        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($user_id);

        $this->mocked_push_service->shouldReceive('sendPushNotification')->with($user_devices_mapped_with_type, $content)->once()->andReturn('');
         $this->mocked_push_service->sendRejectRequestPushNotificationsToGuest($request_hash_id, $user_id);

    }//end test_function_send_reject_request_push_notification_to_guest()


    /**
     * Test booking push notification to guest.
     *
     * @return void
     */
    public function test_function_send_booking_push_notification_to_guest()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $user_id                = $create_booking_request['traveller']->id;

         // Register device for push notifictaion.
        $register_device = $this->registerDevice(['device_unique_id' => $this->device_unique_id, 'device_type' => 'android', 'user_id' => $user_id, 'device_id' => 'sdcvbnmlkjhgcgvvjjvjbvj']);

        $content = [
            'message'           => 'Let’s go packing! You’ve successfully booked with us!',
            'title'             => 'Let’s Go Packing!',
            'sub_title'         => '',
            'notification_type' => BOOKINGS_PUSH_NOTIFICATION,
            'status'            => BOOKED,
            'for'               => 'guest',
            'type'              => 'trip',
            'request_hash_id'   => $request_hash_id,
        ];

        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($user_id);

        $this->mocked_push_service->shouldReceive('sendPushNotification')->with($user_devices_mapped_with_type, $content)->once()->andReturn('');
         $this->mocked_push_service->sendBookingNotificationToGuest($request_hash_id, $user_id);

    }//end test_function_send_booking_push_notification_to_guest()


    /**
     * Test booking push notification to host.
     *
     * @return void
     */
    public function test_function_send_booking_push_notification_to_host()
    {
        $create_booking_request = $this->createBookingRequests();
        $request_hash_id        = Helper::encodeBookingRequestId($create_booking_request['booking_request']->id);
        $host_id                = $create_booking_request['host']->id;

         // Register device for push notifictaion.
        $register_device = $this->registerDevice(['device_unique_id' => $this->device_unique_id, 'device_type' => 'android', 'user_id' => $host_id, 'device_id' => 'sdcvbnmlkjhgcgvvjjvjbvj']);

         $content = [
             'message'           => 'Ready to welcome new explorers? Your property has been successfully booked.',
             'title'             => 'Property Booked',
             'sub_title'         => '',
             'notification_type' => BOOKINGS_PUSH_NOTIFICATION,
             'status'            => BOOKED,
             'for'               => 'host',
             'type'              => 'trip',
             'request_hash_id'   => $request_hash_id,
         ];

         $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($host_id);

         $this->mocked_push_service->shouldReceive('sendPushNotification')->with($user_devices_mapped_with_type, $content)->once()->andReturn('');
         $this->mocked_push_service->sendBookingNotificationToHost($request_hash_id, $host_id);

    }//end test_function_send_booking_push_notification_to_host()


}//end class
