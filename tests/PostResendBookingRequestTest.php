<?php
/**
 * PostResendBookingRequestTest Test containing methods related to Post resend Booking Request Test case
 */

use App\Libraries\Helper;

/**
 * Class PostResendBookingRequestTest
 *
 * @group Request
 */
class PostResendBookingRequestTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test with logout user.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create Booking.
        $booking_request_data = $this->createBookingRequests(EXPIRED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $param    = ['request_hash_id' => $request_hash_id];
        $url      = $this->getApiVersion().'/booking/request/resend';
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
        // Create Booking.
         $booking_request_data = $this->createBookingRequests(EXPIRED);

          // Encode Request Id.
         $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

         $param = ['request_hash_id' => $request_hash_id];

         $url      = $this->getApiVersion().'/booking/request/resend';
         $response = $this->actingAs($booking_request_data['traveller'])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

         // Check status code.
         $this->seeStatusCode(200);

         // Match Reponse json with defined json.
         $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


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
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);
        $param           = ['request_hash_id' => $request_hash_id];

        $url      = $this->getApiVersion().'/booking/request/resend';
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
        $booking_request_data = $this->createBookingRequests(EXPIRED);
        $url                  = $this->getApiVersion().'/booking/request/resend';
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
        $booking_request_data = $this->createBookingRequests(EXPIRED, ['updated_at' => '2018-12-20 03:27:54']);

         // Encode Request Id.
         $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

            $param = ['request_hash_id' => $request_hash_id];

            $url      = $this->getApiVersion().'/booking/request/resend';
            $response = $this->actingAs($booking_request_data['traveller'])->post($url, $param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

            // Check status code.
            $this->seeStatusCode(400);

    }//end testExpiredBooking()


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
                'valid',
                'message',
                'msg_code',
                'booking_status',
                'request_id',
                'instant_book',
                'payment_url',
                'payment_gateway_method',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
