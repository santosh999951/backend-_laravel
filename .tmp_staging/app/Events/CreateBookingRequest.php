<?php
/**
 * A simple Request Create event class.
 */

namespace App\Events;

use App\Models\{BookingRequest};


/**
 * Class CreateBookingRequest. An event class which is fired when Booking Request Created.
 */
class CreateBookingRequest extends Event
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
     * Host Contact Number
     *
     * @var string $host_contact.
     */
    public $host_contact;


    /**
     * Create a new event instance.
     *
     * @param BookingRequest $booking_request Booking Request.
     * @param string         $property_title  Property Title.
     * @param string         $host_email      Host Email.
     * @param string         $host_name       Host name.
     * @param string         $host_dial_code  Host Dial Code.
     * @param string         $host_contact    Host Contact.
     * @param string         $traveller_name  Traveller name.
     *
     * @return void
     */
    public function __construct(BookingRequest $booking_request, string $property_title, string $host_email, string $host_name, string $host_dial_code, string $host_contact, string $traveller_name)
    {
        $this->booking_request = $booking_request;
        $this->property_title  = $property_title;
        $this->host_email      = $host_email;
        $this->host_name       = $host_name;
        $this->traveller_name  = $traveller_name;
        $this->host_dial_code  = $host_dial_code;
        $this->host_contact    = $host_contact;

    }//end __construct()


}//end class
