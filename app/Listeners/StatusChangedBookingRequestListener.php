<?php
/**
 * Listener for the Request Status Change event.
 */

namespace App\Listeners;

use App\Events\StatusChangedBookingRequest;

use App\Libraries\v1_6\BookingRequestService;

/**
 * Class StatusChangedBookingRequestListener for handling Request Status Change event.
 */
class StatusChangedBookingRequestListener extends Listener
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
     * @param StatusChangedBookingRequest $event Event.
     *
     * @return void
     */
    public function handle(StatusChangedBookingRequest $event)
    {
        if ($event->booking_request->booking_status === REQUEST_APPROVED) {
            // Send Request Approved mails to guest.
            $this->booking_request_service->sendBookingRequestApprovedEmailToGuest($event->booking_request, $event->property_title, $event->traveller_email, $event->traveller_name);

            // Send  Request Approved sms to guest.
            $this->booking_request_service->sendBookingRequestApprovedSmsToGuest($event->booking_request, $event->property_title, $event->traveller_dial_code, $event->traveller_contact);

            // Send Request approved Notification to guest.
            $this->booking_request_service->sendBookingRequestApprovedNotificationToGuest($event->booking_request);
        } else if ($event->booking_request->booking_status === REQUEST_REJECTED) {
            // Send Request Reject mails to guest.
            $this->booking_request_service->sendBookingRequestRejectedEmailToGuest($event->booking_request, $event->property_title, $event->traveller_email, $event->traveller_name);

            // Send  Request Reject sms to guest.
            $this->booking_request_service->sendBookingRequestRejectedSmsToGuest($event->booking_request, $event->property_title, $event->traveller_dial_code, $event->traveller_contact);

             // Send Request reject Notification to guest.
            $this->booking_request_service->sendBookingRequestRejectNotificationToGuest($event->booking_request);
        }

    }//end handle()


}//end class
