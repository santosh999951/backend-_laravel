<?php
/**
 * Listener for the Send Payment Link.
 */

namespace App\Listeners;

use App\Events\SendBookingPaymentLink;

use App\Libraries\v1_6\BookingRequestService;

/**
 * Class SendBookingPaymentLinkListener for handling Request Create event.
 */
class SendBookingPaymentLinkListener extends Listener
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
     * @param SendBookingPaymentLink $event Event.
     *
     * @return void
     */
    public function handle(SendBookingPaymentLink $event)
    {
        $this->booking_request_service->sendBookingPaymentLinkEmail($event->request_id, $event->traveller_name, $event->to_mail);

        $this->booking_request_service->sendBookingPaymentLinkSms($event->request_id, $event->dial_code, $event->contact);

    }//end handle()


}//end class
