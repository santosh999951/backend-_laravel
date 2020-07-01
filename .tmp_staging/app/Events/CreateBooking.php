<?php
/**
 * A simple Booking Created event class.
 */

namespace App\Events;

use App\Models\{BookingRequest, Property, User};


/**
 * Class CreateBooking. An event class which is fired when Booking Created.
 */
class CreateBooking extends Event
{

    /**
     * BookingRequest object.
     *
     * @var BookingRequest $booking_request
     */
    public $booking_request;

    /**
     * Booking Balance Fee Object
     *
     * @var float $balance_fee
     */
    public $balance_fee;

    /**
     * Property Object
     *
     * @var Property $property
     */
    public $property;

    /**
     * User Object
     *
     * @var User $host.
     */
    public $host;

    /**
     * Traveller Object
     *
     * @var User $traveller.
     */
    public $traveller;

    /**
     * Send Invoice Mail only Traveller Flag
     *
     * @var boolean $send_invoice_mail_only_traveller.
     */
    public $send_invoice_mail_only_traveller;

    /**
     * Is this second payment.
     *
     * @var boolean $second_payment.
     */
    public $second_payment;


    /**
     * Create a new event instance.
     *
     * @param BookingRequest $booking_request                  Booking Request.
     * @param float          $balance_fee                      Balance Amount.
     * @param Property       $property                         Property.
     * @param User           $host                             Host.
     * @param User           $traveller                        Traveller.
     * @param boolean        $send_invoice_mail_only_traveller Send invoice mail only traveller flag.
     * @param boolean        $second_payment                   Set to true when user makes payment after request become booking/second payment.
     *
     * @return void
     */
    public function __construct(BookingRequest $booking_request, float $balance_fee, Property $property, User $host, User $traveller, bool $send_invoice_mail_only_traveller=false, bool $second_payment=false)
    {
        $this->booking_request = $booking_request;
        $this->balance_fee     = $balance_fee;
        $this->property        = $property;
        $this->host            = $host;
        $this->traveller       = $traveller;
        $this->send_invoice_mail_only_traveller = $send_invoice_mail_only_traveller;
        $this->second_payment                   = $second_payment;

    }//end __construct()


}//end class
