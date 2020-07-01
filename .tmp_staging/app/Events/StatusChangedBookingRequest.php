<?php
/**
 * A simple Request Status Change event class.
 */

namespace App\Events;

use App\Models\{BookingRequest};


/**
 * Class StatusChangedBookingRequest. An event class which is fired when Booking Request Status Change.
 */
class StatusChangedBookingRequest extends Event
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
     * Traveller Dial Code
     *
     * @var string $traveller_dial_code.
     */
    public $traveller_dial_code;

    /**
     * Traveller Contact Number
     *
     * @var string $traveller_contact.
     */
    public $traveller_contact;


    /**
     * Create a new event instance.
     *
     * @param BookingRequest $booking_request     Booking Request.
     * @param string         $property_title      Property Title.
     * @param string         $traveller_email     Traveller Email.
     * @param string         $traveller_name      Traveller name.
     * @param string         $traveller_dial_code Traveller Dial Code.
     * @param string         $traveller_contact   Traveller Contact.
     *
     * @return void
     */
    public function __construct(BookingRequest $booking_request, string $property_title, string $traveller_email, string $traveller_name, string $traveller_dial_code, string $traveller_contact)
    {
        $this->booking_request     = $booking_request;
        $this->property_title      = $property_title;
        $this->traveller_email     = $traveller_email;
        $this->traveller_name      = $traveller_name;
        $this->traveller_dial_code = $traveller_dial_code;
        $this->traveller_contact   = $traveller_contact;

    }//end __construct()


}//end class
