<?php
/**
 * PostRequestCancelTest Test containing methods related to Booking Request and trip cancel Test case
 */

use App\Libraries\Helper;
use App\Events\CancelBookingRequest;

/**
 * Class PostRequestCancelTest
 *
 * @group Request
 */
class PostRequestCancelTest extends TestCase
{
    use App\Traits\FactoryHelper;

    /**
     * Default reason id for cancellation
     *
     * @var integer
     */
    protected $reason_id = 3;

    /**
     * Default Reason for cancellation
     *
     * @var integer
     */
    protected $reason_detail = 'Got a better deal';


    /**
     * Setup.
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();

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
     * Test with logout user.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create Booking.
        $booking_request_data = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $param    = [
            'request_hash_id' => $request_hash_id,
            'request_status'  => $booking_request_data['booking_request']->booking_status,
            'reason_id'       => $this->reason_id,
            'reason_detail'   => $this->reason_detail,
        ];
        $url      = $this->getApiVersion().'/booking/request/cancel';
        $response = $this->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test Login User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

        // Create Booking.
        $booking_request_data = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);
        $param           = [
            'request_hash_id' => $request_hash_id,
            'request_status'  => $booking_request_data['booking_request']->booking_status,
            'reason_id'       => $this->reason_id,
            'reason_detail'   => $this->reason_detail,
        ];

        $url      = $this->getApiVersion().'/booking/request/cancel';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Decode Json Response.
        $content = json_decode($this->response->getContent());

        Event::assertDispatched(
            CancelBookingRequest::class,
            function ($event) use ($content) {
                    return ($event->booking_request->id === Helper::decodeBookingRequestId($content->data->request_hash_id));
            }
        );

    }//end testValidResponseWithAuthentication()


    /**
     * Test Response For trip.
     *
     * @return void
     */
    public function testValidResponseForTrip()
    {
        // Check event is fired.
        Event::fake();

        // Create Booking.
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);
        $param           = [
            'request_hash_id' => $request_hash_id,
            'request_status'  => $booking_request_data['booking_request']->booking_status,
            'reason_id'       => $this->reason_id,
            'reason_detail'   => $this->reason_detail,
        ];

        $url      = $this->getApiVersion().'/booking/request/cancel';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Decode Json Response.
        $content = json_decode($this->response->getContent());

        Event::assertDispatched(
            CancelBookingRequest::class,
            function ($event) use ($content) {
                    return ($event->booking_request->id === Helper::decodeBookingRequestId($content->data->request_hash_id));
            }
        );

    }//end testValidResponseForTrip()


    /**
     * Test Invalid Hash Id.
     *
     * @return void
     */
    public function testInvalidHashId()
    {
        // Create Booking.
        $booking_request_data = $this->createBookingRequests();

          // Encode Request Id.
        $request_hash_id = 'ABCD';
        $param           = [
            'request_hash_id' => $request_hash_id,
            'request_status'  => $booking_request_data['booking_request']->booking_status,
            'reason_id'       => $this->reason_id,
            'reason_detail'   => $this->reason_detail,
        ];

        $url      = $this->getApiVersion().'/booking/request/cancel';
        $response = $this->actingAs($booking_request_data['traveller'])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status code.
        $this->seeStatusCode(400);

    }//end testInvalidHashId()


    /**
     * Test Parameters Not passed.
     *
     * @return void
     */
    public function testInvalidParameters()
    {
         // Create Booking.
        $booking_request_data = $this->createBookingRequests();
        $url                  = $this->getApiVersion().'/booking/request/cancel';
        $response             = $this->actingAs($booking_request_data['traveller'])->post($url, [], ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status code.
        $this->seeStatusCode(400);

    }//end testInvalidParameters()


    /**
     * Test Expired Booking.
     *
     * @return void
     */
    public function testExpiredBooking()
    {
         // Create Booking.
        $booking_request_data = $this->createBookingRequests(EXPIRED);

         // Encode Request Id.
         $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

           $param = [
               'request_hash_id' => $request_hash_id,
               'request_status'  => $booking_request_data['booking_request']->booking_status,
               'reason_id'       => $this->reason_id,
               'reason_detail'   => $this->reason_detail,
           ];

           $url      = $this->getApiVersion().'/booking/request/cancel';
           $response = $this->actingAs($booking_request_data['traveller'])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

           // Check status code.
           $this->seeStatusCode(404);

    }//end testExpiredBooking()


     /**
      * Test Whether request cancallable or not.
      *
      * @return void
      */
    public function testRequestNotCancellable()
    {
         // Create Booking.
        $booking_request_data = $this->createBookingRequests(EXPIRED);

         // Encode Request Id.
         $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

           $param = [
               'request_hash_id' => $request_hash_id,
               'request_status'  => -4,
               'reason_id'       => $this->reason_id,
               'reason_detail'   => $this->reason_detail,
           ];

           $url      = $this->getApiVersion().'/booking/request/cancel';
           $response = $this->actingAs($booking_request_data['traveller'])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

           // Check status code.
           $this->seeStatusCode(404);

    }//end testRequestNotCancellable()


    /**
     * Helper function to api structure
     *
     * @return array
     */
    private function getApiStructureWithOnlyDefaultValues()
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status',
            'data' => [
                'request_hash_id',
                'request_status',
                'message',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
