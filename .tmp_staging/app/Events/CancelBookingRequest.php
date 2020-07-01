<?php
/**
 * A simple Booking Cancel event class.
 */

namespace App\Events;

use App\Models\{BookingRequest};


/**
 * Class CancelBookingRequest. An event class which is fired when Booking Cancelled.
 */
class CancelBookingRequest extends Event
{

    /**
     * BookingRequest object.
     *
     * @var BookingRequest $booking_request
     */
    public $booking_request;

    /**
     * Property Title
     *
     * @var string $property_title
     */
    public $property_title;

    /**
     * Host Email
     *
     * @var string $host_email.
     */
    public $host_email;

    /**
     * Host name
     *
     * @var string $host_name.
     */
    public $host_name;

    /**
     * Traveller Email
     *
     * @var string $traveller_email.
     */
    public $traveller_email;

    /**
     * Traveller name
     *
     * @var string $traveller_name.
     */
    public $traveller_name;

    /**
     * Host Dial Code
     *
     * @var string $host_dial_code.
     */
    public $host_dial_code;

    /**
     * Host Contact
     *
     * @var string $host_contact.
     */
    public $host_contact;

    /**
     * Traveller Dial Code
     *
     * @var string $traveller_dial_code.
     */
    public $traveller_dial_code;

    /**
     * Traveller Contact
     *
     * @var string $traveller_contact.
     */
    public $traveller_contact;

    /**
     * Refund Amount
     *
     * @var float $refund_amount.
     */
    public $refund_amount;


    /**
     * Create a new event instance.
     *
     * @param BookingRequest $booking_request     Booking Request.
     * @param string         $property_title      Property Title.
     * @param string         $host_email          Host Email.
     * @param string         $host_name           Host name.
     * @param string         $traveller_email     Traveller Email.
     * @param string         $traveller_name      Traveller name.
     * @param float          $refund_amount       Refund Amount.
     * @param string         $host_dial_code      Host Dial Code.
     * @param string         $host_contact        Host Dial Code.
     * @param string         $traveller_dial_code Traveller Dial Code.
     * @param string         $traveller_contact   Traveller Contact.
     *
     * @return void
     */
    public function __construct(
        BookingRequest $booking_request,
        string $property_title,
        string $host_email,
        string $host_name,
        string $traveller_email,
        string $traveller_name,
        float $refund_amount,
        string $host_dial_code,
        string $host_contact,
        string $traveller_dial_code,
        string $traveller_contact
    ) {
        $this->booking_request     = $booking_request;
        $this->property_title      = $property_title;
        $this->host_email          = $host_email;
        $this->host_name           = $host_name;
        $this->traveller_email     = $traveller_email;
        $this->traveller_name      = $traveller_name;
        $this->refund_amount       = $refund_amount;
        $this->host_dial_code      = $host_dial_code;
        $this->host_contact        = $host_contact;
        $this->traveller_dial_code = $traveller_dial_code;
        $this->traveller_contact   = $traveller_contact;

    }//end __construct()


}//end class
