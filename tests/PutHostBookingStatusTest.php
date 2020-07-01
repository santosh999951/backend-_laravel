<?php
/**
 * PutHostBookingStatusTest Test containing methods related to Host Booking Status Test case
 */

use App\Libraries\Helper;
use App\Events\StatusChangedBookingRequest;

/**
 * Class PutHostBookingStatusTest
 *
 * @group Host
 */
class PutHostBookingStatusTest extends TestCase
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
        // Mocking common queue not stop not live sending email,notification,sms.
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
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Form data parameters.
        $put_param = [
            'request_hash_id' => $request_hash_id,
            'status'          => 1,
        ];

        // Get Home response data.
        $url = $this->getApiVersion().'/host/booking/status';
        $this->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test for Traveller.
     *
     * @return void
     */
    public function testInvalidResponseForTravellerWithAuthentication()
    {
         // Create Booking Data.
        $create_booking = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Form data parameters.
        $put_param = [
            'request_hash_id' => $request_hash_id,
            'status'          => 1,
        ];

        // Get Home response data.
        $url = $this->getApiVersion().'/host/booking/status';

        $response = $this->actingAs($create_booking['traveller'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Test Data not found for invalid request id .
     *
     * @return void
     */
    public function testInvalidRequestHashIdWithAuthentication()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Form data parameters.
        $put_param = [
            'request_hash_id' => 'ABCDEF',
            'status'          => 1,
        ];

        // Get Home response data.
        $url = $this->getApiVersion().'/host/booking/status';

        $response = $this->actingAs($create_booking['host'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testInvalidRequestHashIdWithAuthentication()


    /**
     * Response due to Invalid Parameters with authenicated user.
     *
     * @test
     * @return void
     */
    public function testInvalidResponseWithInvalidParametersWithAuthentication()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Form data parameters.
        $put_param = [
            'request_hash_id' => $request_hash_id,
            'status'          => 2,
        ];

        // Get Home response data.
        $url = $this->getApiVersion().'/host/booking/status';

        $response = $this->actingAs($create_booking['host'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidResponseWithInvalidParametersWithAuthentication()


    /**
     * Accept request response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testAcceptRequestResponseWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

        // Mocking push notification function ,and should return blank.
        $this->mocked_service->shouldReceive('pushNotification')->andReturn('');

        // Create Booking Data.
        $create_booking = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Form data parameters.
        $put_param = [
            'request_hash_id' => $request_hash_id,
            'status'          => 1,
        ];

        // Get Home response data.
        $url = $this->getApiVersion().'/host/booking/status';

        $response = $this->actingAs($create_booking['host'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        Event::assertDispatched(
            StatusChangedBookingRequest::class
        );

    }//end testAcceptRequestResponseWithAuthentication()


    /**
     * Reject request response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testRejectRequestResponseWithAuthentication()
    {
        // Check event is fired.
        Event::fake();

        // Mocking push notification function ,and should return blank.
        $this->mocked_service->shouldReceive('pushNotification')->andReturn('');

        // Create Booking Data.
        $create_booking = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Form data parameters.
        $put_param = [
            'request_hash_id' => $request_hash_id,
            'status'          => 0,
            'reason_id'       => 3,
            'reason_detail'   => 'Unit testing',
        ];

        // Get Home response data.
        $url = $this->getApiVersion().'/host/booking/status';

        $response = $this->actingAs($create_booking['host'])->put($url, $put_param, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        Event::assertDispatched(
            StatusChangedBookingRequest::class
        );

    }//end testRejectRequestResponseWithAuthentication()


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
                'booking_status' => [
                    'text',
                    'class',
                    'color_code',
                    'status',
                    'header_text',
                ],
                'message'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
