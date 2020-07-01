<?php
/**
 * GetHostBookingTest Test containing methods related to Booking Request  List Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class GetHostBookingTest
 *
 * @group Host
 */
class GetHostBookingTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Host.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get Home response data.
        $url      = $this->getApiVersion().'/host/booking';
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $traveller = $this->createUsers();

        // Get Home response data.
        $url      = $this->getApiVersion().'/host/booking';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Response due to Invalid Parameters with authenicated user.
     *
     * @test
     * @return void
     */
    public function testInvalidResponseWithInvalidParametersWithAuthentication()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED);

        // Query Parameters with invalid date (end date should be greater than start_date).
        $params = [
            'start_date' => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 30))->format('d-m-Y'),
            'end_date'   => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 30))->format('d-m-Y'),
            'order_by'   => 2,
            'offset'     => 0,
            'limit'      => 1,
        ];

        // Get host home detail response data.
        $url = $this->getApiVersion().'/host/booking?'.http_build_query($params);

        $response = $this->actingAs($create_booking['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(400);

    }//end testInvalidResponseWithInvalidParametersWithAuthentication()


    /**
     * Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED);

        // Get host home detail response data.
        $url = $this->getApiVersion().'/host/booking';

        $response = $this->actingAs($create_booking['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(200);
        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


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
                'booking_requests' => [
                    '*' => [
                        'request_hash_id',
                        'no_of_nights',
                        'guests',
                        'units',
                        'checkin_checkout',
                        'timeline_status',
                        'amount',
                        'booking_status' => [
                            'text',
                            'class',
                            'color_code',
                            'status',
                            'header_text',
                        ],
                        'checkin',
                        'checkout',
                        'property_hash_id',
                        'location_name',
                        'title',
                        'property_image' => []
                    ],
                ],
                'filter' => [
                    'properties' => [
                        '*' => [
                            'id',
                            'title',
                            'selected',
                        ],
                    ],
                    'status'     => [
                        '*' => [
                            'status',
                            'text',
                            'selected',
                        ],
                    ],
                ],
                'updated_offset',
                'total'
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
