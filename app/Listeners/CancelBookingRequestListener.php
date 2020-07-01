<?php
/**
 * Listener for the Request Cancel event.
 */

namespace App\Listeners;

use App\Events\CancelBookingRequest;

use App\Libraries\v1_6\BookingRequestService;

/**
 * Class CancelBookingRequestListener for handling Request Cancel event.
 */
class CancelBookingRequestListener extends Listener
{

    /**
     * Booking Request service.
     *
     * @var BookingRequestService $booking_request_service Booking Request Service.
     */
    protected $booking_request_service;


    /**
     * Initialize the object.
     *
     * @param BookingRequestService $booking_request_service Booking Request Service.
     */
    public function __construct(BookingRequestService $booking_request_service)
    {
        $this->booking_request_service = $booking_request_service;

    }//end __construct()


    /**
     * Handle the event.
     *
     * @param CancelBookingRequest $event Event.
     *
     * @return void
     */
    public function handle(CancelBookingRequest $event)
    {
        if ($event->booking_request->booking_status >= BOOKED) {
            // Send cancel request mails.
            $this->booking_request_service->sendCancelBookingRequestEmails($event->booking_request, $event->property_title, $event->host_email, $event->host_name, $event->traveller_email, $event->traveller_name, $event->refund_amount);

            // Send Cancel request sms.
            $this->booking_request_service->sendCancelBookingRequestSms($event->booking_request, $event->property_title, $event->host_dial_code, $event->host_contact, $event->traveller_dial_code, $event->traveller_contact);
        }

        // Send Booking Push Notifications.
        $this->booking_request_service->sendCancelBookingRequestPushNotifications($event->booking_request);

    }//end handle()


}//end class
