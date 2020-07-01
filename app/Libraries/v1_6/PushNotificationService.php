<?php
/**
 * Push Notification Service use to send Push notifications to devices
 */

namespace App\Libraries\v1_6;

use App\Libraries\Helper;
use Illuminate\Support\Facades\Queue;

use App\Models\MobileAppDevice;

use App\Jobs\SendPushNotification;

/**
 * Class PushNotificationService. All notification template code is housed in here.
 */
class PushNotificationService
{


    /**
     * Dispatches push notification on communication queue.
     *
     * @param array $push Push Notification Data.
     *
     * @return void
     */
    public function send(array $push)
    {
        // Push Notification job.
        $job = new SendPushNotification($push);
        Queue::pushOn(COMMUNICATION_QUEUE, $job);

    }//end send()


    /**
     * Send Push notification to Android Devices.
     *
     * @param array  $device_ids      Device Ids.
     * @param array  $content         Push notification Contents.
     * @param string $property_id     Property Is=d.
     * @param string $request_hash_id Request Hash Id.
     * @param array  $cta_content     CTA content.
     * @param string $medium          Push Notification send medium.
     *
     * @return void
     */
    public function sendPushNotificationToAndroid(array $device_ids, array $content, string $property_id=null, string $request_hash_id=null, array $cta_content=[], string $medium='fcm')
    {
        // Create Chunk of max 500 devices.
        $device_ids = array_chunk($device_ids, 500);

        $headers = [
            'Authorization' => 'key='.ANDROID_API_ACCESS_KEYS[$medium],
            'Content-Type'  => 'application/json',
        ];

        $data_params = [
            'message'           => $content['message'],
            'title'             => $content['title'],
            'sub_title'         => '',
            'notification_type' => $content['notification_type'],
            'status'            => $content['status'],
            'for'               => $content['for'],
        ];

        // Add Image in Notification.
        if (empty($content['notification_url']) === false) {
            $data_params['notification_url'] = $content['notification_url'];
        }

        // Add Property Ids.
        if (empty($property_id) === false) {
            $data_params['pid'] = $property_id;
        }

        // Add Booking Request Hash Id.
        if (empty($request_hash_id) === false) {
            $data_params['request_id'] = $request_hash_id;
        }

        // Add CTA data.
        if (empty($cta_content) === false) {
            $data_params['cta_response_key'] = $cta_content['response_key'];
            $data_params['cta_url']          = $cta_content['url'];
        }

        // Uninstall Devices List.
        $uninstall_device_ids = [];

        // Iterate Chunk of devices.
        foreach ($device_ids as $registration_ids) {
            $post_params = [
                'registration_ids' => $registration_ids,
                'data'             => $data_params,
            ];

            $send_request = Helper::sendCurlRequest(ANDROID_PUSH_NOTIFICATION_URL[$medium], json_encode($post_params), 'POST', null, $headers);

            $response = json_decode($send_request, true);

            // Get Uninstall device ids.
            foreach ($response['results'] as $key => $value) {
                if (empty($value['error']) === false && $value['error'] === 'NotRegistered') {
                    $uninstall_device_ids[] = $registration_ids[$key];
                }
            }
        }

        // Update Uninstall Devices status.
        if (empty($uninstall_device_ids) === false) {
            $uninstall_device_ids = array_unique($uninstall_device_ids);
            MobileAppDevice::updateDeviceStatus($uninstall_device_ids);
        }

    }//end sendPushNotificationToAndroid()


    /**
     * Send Push notification to Ios devices.
     *
     * @param array  $device_ids      Device Ids.
     * @param array  $content         Push notification Contents.
     * @param string $property_id     Property Is=d.
     * @param string $request_hash_id Request Hash Id.
     * @param array  $cta_content     CTA content.
     *
     * @return void
     */
    public function sendPushNotificationToIos(array $device_ids, array $content, string $property_id=null, string $request_hash_id=null, array $cta_content=[])
    {
        // Create Chunk of max 500 devices.
        $device_ids = array_chunk($device_ids, 500);

        $data_params = [
            'aps'         => [
                'alert' => $content['message'],

            ],
            'custom data' => [
                'notification_type' => $content['notification_type'],
                'for'               => $content['for'],
            ],

        ];

        if ($content['notification_type'] === CTA_NOTIFICATION) {
            $data_params['aps']['category'] = 'CTA_notification';
        } else if ($content['notification_type'] === PAY_REMANING_AMOUNT) {
            $data_params['aps']['category'] = 'pay_remaning';
        }

        // Add Property Ids.
        if (empty($property_id) === false) {
            $data_params['custom data']['pid'] = $property_id;
        }

        // Add Booking Request Hash Id.
        if (empty($request_hash_id) === false) {
            $data_params['custom data']['request_id'] = $request_hash_id;
        }

        // Make Payload.
        $payload = json_encode($data_params);

        // Iterate Chunk of devices.
        foreach ($device_ids as $registration_ids) {
            $apns_connection = $this->sendRequestToAPNS(IOS_APNS_URL, 1);

            if (empty($apns_connection) === false) {
                // Send Push Notification to Current Chunk of device.
                foreach ($registration_ids as $registration_id) {
                    $message = chr(0).pack('n', 32).pack('H*', $registration_id).pack('n', strlen($payload)).$payload;

                    // Write Payload Content.
                    if (fwrite($apns_connection, $message, strlen($message)) === false) {
                        fclose($apns_connection);
                        $apns_connection = $this->sendRequestToAPNS(IOS_APNS_URL, 1);
                        continue;
                    }
                }

                // Close Current Apns Connection.
                fclose($apns_connection);
            } else {
                Helper::logInfo('Failed to Connect Apns for following Devices', $registration_ids);
            }//end if
        }//end foreach

        // Update Uninstall Devices status.
        $uninstall_device_ids = $this->getIosUninstallDevices();

        if (empty($uninstall_device_ids) === false) {
            $uninstall_device_ids = array_unique($uninstall_device_ids);
            MobileAppDevice::updateDeviceStatus($uninstall_device_ids);
        }

    }//end sendPushNotificationToIos()


    /**
     * Make APNS Connection.
     *
     * @param string  $url   APNS Url.
     * @param integer $block Blocking Status.
     *
     * @return object
     */
    public function sendRequestToAPNS(string $url, int $block=0)
    {
        // Create Context.
        $context = stream_context_create();

        // Set APNS Context Data.
        stream_context_set_option($context, 'ssl', 'local_cert', IOS_CERTIFICATE);

        $connection = stream_socket_client($url, $error, $error_string, 600, (STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT), $context);

        // Set Blocking Status.
        if ($block === 1) {
            stream_set_blocking($connection, 0);
        }

        return $connection;

    }//end sendRequestToAPNS()


    /**
     * Get APNS Uninstall Ids.
     *
     * @return array
     */
    public function getIosUninstallDevices()
    {
        $apns_connection = $this->sendRequestToAPNS(IOS_FEEDBACK_URL);

        if (empty($apns_connection) === true) {
            return [];
        }

        $feedbacks = [];

        while (feof($apns_connection) === false) {
            $data = fread($apns_connection, 38);

            if (strlen($data) > 0) {
                $feedbacks[] = unpack('N1timestamp/n1length/H*devtoken', $data);
            }
        }

        // Close APNS Connection.
        fclose($apns_connection);

        return array_column($feedbacks, 'devtoken');

    }//end getIosUninstallDevices()


    /**
     * Send Push notification.
     *
     * @param array $device_id_mapped_with_device_type Device Ids.
     * @param array $content                           Push notification Contents.
     *
     * @return void
     */
    public function sendPushNotification(array $device_id_mapped_with_device_type, array $content)
    {
        $view = [
            'message'           => $content['message'],
            'title'             => $content['title'],
            'sub_title'         => $content['sub_title'],
            'notification_type' => $content['notification_type'],
            'status'            => $content['status'],
            'for'               => $content['for'],
            'type'              => $content['type'],
        ];

        $push = [
            'device_id_mapped_with_device_type' => $device_id_mapped_with_device_type,
            'view'                              => $view,
            'property_id'                       => (empty($content['property_id']) === false) ? $content['property_id'] : null,
            'request_hash_id'                   => (empty($content['request_hash_id']) === false) ? $content['request_hash_id'] : null,
            'cta_content'                       => (empty($content['cta_content']) === false) ? $content['cta_content'] : [],
        ];

        $this->send($push);

    }//end sendPushNotification()


    /**
     * Push Notification for Host to Cancel Booking.
     *
     * @param string  $request_hash_id Reqest Hash Id.
     * @param integer $user_id         User id.
     *
     * @return void
     */
    public function sendCancelBookingPushNotificationsToHost(string $request_hash_id, int $user_id)
    {
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

        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($user_id);

        if (empty($user_devices_mapped_with_type) === true) {
            return;
        }

        $this->sendPushNotification($user_devices_mapped_with_type, $content);

    }//end sendCancelBookingPushNotificationsToHost()


    /**
     * Push Notification for Host to Cancel Booking Request.
     *
     * @param string  $request_hash_id Reqest Hash Id.
     * @param integer $user_id         User id.
     *
     * @return void
     */
    public function sendCancelRequestPushNotificationsToHost(string $request_hash_id, int $user_id)
    {
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

        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($user_id);

        if (empty($user_devices_mapped_with_type) === true) {
            return;
        }

        $this->sendPushNotification($user_devices_mapped_with_type, $content);

    }//end sendCancelRequestPushNotificationsToHost()


     /**
      * Push Notification for Host to New Request.
      *
      * @param string  $request_hash_id Reqest Hash Id.
      * @param integer $user_id         User id.
      *
      * @return void
      */
    public function sendNewRequestPushNotificationsToHost(string $request_hash_id, int $user_id)
    {
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

        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($user_id);

        if (empty($user_devices_mapped_with_type) === true) {
            return;
        }

        $this->sendPushNotification($user_devices_mapped_with_type, $content);

    }//end sendNewRequestPushNotificationsToHost()


    /**
     * Push Notification for Guest to Approved Request.
     *
     * @param string  $request_hash_id Reqest Hash Id.
     * @param integer $user_id         User id.
     *
     * @return void
     */
    public function sendApprovedRequestPushNotificationsToGuest(string $request_hash_id, int $user_id)
    {
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

        if (empty($user_devices_mapped_with_type) === true) {
            return;
        }

        $this->sendPushNotification($user_devices_mapped_with_type, $content);

    }//end sendApprovedRequestPushNotificationsToGuest()


    /**
     * Push Notification for Guest to Reject Request.
     *
     * @param string  $request_hash_id Reqest Hash Id.
     * @param integer $user_id         User id.
     *
     * @return void
     */
    public function sendRejectRequestPushNotificationsToGuest(string $request_hash_id, int $user_id)
    {
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

        if (empty($user_devices_mapped_with_type) === true) {
            return;
        }

        $this->sendPushNotification($user_devices_mapped_with_type, $content);

    }//end sendRejectRequestPushNotificationsToGuest()


    /**
     * Push Notification for Guest for a booking.
     *
     * @param string  $request_hash_id Reqest Hash Id.
     * @param integer $user_id         User id.
     *
     * @return void
     */
    public function sendBookingNotificationToGuest(string $request_hash_id, int $user_id)
    {
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

        if (empty($user_devices_mapped_with_type) === true) {
            return;
        }

        $this->sendPushNotification($user_devices_mapped_with_type, $content);

    }//end sendBookingNotificationToGuest()


    /**
     * Push Notification for host for a booking.
     *
     * @param string  $request_hash_id Reqest Hash Id.
     * @param integer $user_id         User id.
     *
     * @return void
     */
    public function sendBookingNotificationToHost(string $request_hash_id, int $user_id)
    {
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

        $user_devices_mapped_with_type = MobileAppDevice::getUserDevice($user_id);

        if (empty($user_devices_mapped_with_type) === true) {
            return;
        }

        $this->sendPushNotification($user_devices_mapped_with_type, $content);

    }//end sendBookingNotificationToHost()


}//end class
