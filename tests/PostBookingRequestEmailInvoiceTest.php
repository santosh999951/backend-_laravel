<?php
/**
 * PostBookingRequestEmailInvoiceTest Test containing methods related to sending email invoice of request.
 */

use App\Libraries\Helper;
use App\Events\CreateBooking;

/**
 * Class PostBookingRequestEmailInvoiceTest
 *
 * @group Request
 */
class PostBookingRequestEmailInvoiceTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Setup.
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();
        // Mocking common queue not stop not live sending email.
        $this->mocked_service = $this->mock('alias:App\Libraries\CommonQueue');

    }//end setup()


    /**
     * Tear down.
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->clearmock();

    }//end tearDown()


    /**
     * Test Unauthorized User response.
     *
     * @return void
     */
    public function testUnauthroizedResponseWithoutAuthentication()
    {
        // Execute api using without authentication.
        $url      = $this->getApiVersion().'/booking/request/emailinvoice';
        $response = $this->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testUnauthroizedResponseWithoutAuthentication()


     /**
      * Test Bad Request Response.
      *
      * @return void
      */
    public function testBadRequestResponseWithAuthentication()
    {
        $traveller = $this->createUser();
        // Execute api using with authentication.
        $url = $this->getApiVersion().'/booking/request/emailinvoice';

        $response = $this->actingAs($traveller)->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Valid response with authentication.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

        $booking_request_details = $this->createBookingRequests(BOOKED);
        $traveller               = $booking_request_details['traveller'];
        $request_hash_id         = Helper::encodeBookingRequestId($booking_request_details['booking_request']->id);
        // Execute api using without authentication.
        $url = $this->getApiVersion().'/booking/request/emailinvoice';

        $this->actingAs($traveller)->post($url, ['request_hash_id' => $request_hash_id], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);

        Event::assertDispatched(
            CreateBooking::class,
            function ($event) use ($booking_request_details) {
                return ($event->booking_request->id === $booking_request_details['booking_request']->id);
            }
        );

    }//end testValidResponseWithAuthentication()


     /**
      * Test Invalid User response when trying any random request id.
      *
      * @return void
      */
    public function testInvalidResponseWithAuthentication()
    {
        // Mocking push email function ,and should not be called.
        $this->mocked_service->shouldNotReceive('pushEmail');

        $booking_request_details = $this->createBookingRequests(BOOKED);
        $traveller               = $booking_request_details['traveller'];
        $request_hash_id         = Helper::encodeBookingRequestId(11111);
        // Execute api using without authentication.
        $url = $this->getApiVersion().'/booking/request/emailinvoice';

        // Created a valid request id ,but requested invoice by some other user or booking request id not found.
        $this->actingAs($traveller)->post($url, ['request_hash_id' => $request_hash_id], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidResponseWithAuthentication()


}//end class
