<?php
/**
 * A simple Booking Payment Link event class.
 */

namespace App\Events;


/**
 * Class SendBookingPaymentLink. An event class which is fired when Booking Created.
 */
class SendBookingPaymentLink extends Event
{

    /**
     * BookingRequest id.
     *
     * @var integer $request_id
     */
    public $request_id;

    /**
     * Traveller Name
     *
     * @var string $traveller_name
     */
    public $traveller_name;

    /**
     * To mail
     *
     * @var string $to_mail
     */
    public $to_mail;

    /**
     * Dial Code
     *
     * @var string $dial_code.
     */
    public $dial_code;

    /**
     * Contact number
     *
     * @var string $contact.
     */
    public $contact;


    /**
     * Create a new event instance.
     *
     * @param integer $request_id     Booking Request id.
     * @param string  $traveller_name Traveller Name.
     * @param string  $to_mail        To mail.
     * @param string  $dial_code      Dial Code.
     * @param string  $contact        Contact number.
     *
     * @return void
     */
    public function __construct(int $request_id, string $traveller_name, string $to_mail, string $dial_code, string $contact)
    {
        $this->request_id     = $request_id;
        $this->traveller_name = $traveller_name;
        $this->to_mail        = $to_mail;
        $this->dial_code      = $dial_code;
        $this->contact        = $contact;

    }//end __construct()


}//end class
