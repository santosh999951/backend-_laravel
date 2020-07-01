<?php
/**
 * Listener for the Booking Create event.
 */

namespace App\Listeners;

use App\Events\CreateBooking;

use App\Libraries\v1_6\BookingRequestService;

/**
 * Class CreateBookingListener for handling Request Create event.
 */
class CreateBookingListener extends Listener
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
     * @param CreateBooking $event Event.
     *
     * @return void
     */
    public function handle(CreateBooking $event)
    {
        $this->booking_request_service->sendBookingNotifications($event->booking_request, $event->balance_fee, $event->property, $event->host, $event->traveller, $event->send_invoice_mail_only_traveller, $event->second_payment);

        // Send booking notification to guest.
        $this->booking_request_service->sendBookingPushNotificationsToGuest($event->booking_request);

        // Send booking notification to host.
        $this->booking_request_service->sendBookingPushNotificationsToHost($event->booking_request);

    }//end handle()


}//end class
