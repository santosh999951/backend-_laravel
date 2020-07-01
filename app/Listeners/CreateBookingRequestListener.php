<?php
/**
 * Listener for the Request Create event.
 */

namespace App\Listeners;

use App\Events\CreateBookingRequest;

use App\Libraries\v1_6\BookingRequestService;

/**
 * Class CreateBookingRequestListener for handling Request Create event.
 */
class CreateBookingRequestListener extends Listener
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
     * @param CreateBookingRequest $event Event.
     *
     * @return void
     */
    public function handle(CreateBookingRequest $event)
    {
        // Send new request mails.
        $this->booking_request_service->sendNewRequestEmailToHost($event->booking_request, $event->property_title, $event->host_email, $event->host_name, $event->traveller_name);

        // Send new request sms to host.
        $this->booking_request_service->sendNewRequestSmsToHost($event->booking_request, $event->property_title, $event->host_dial_code, $event->host_contact);

        // Send new request notification to host.
        $this->booking_request_service->sendNewRequestPushNotifications($event->booking_request);

    }//end handle()


}//end class
