<?php
/**
 * GetPriveBookingTest Test containing methods related to Booking Request  List Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};
use \Carbon\Carbon;

/**
 * Class GetPriveBookingTest
 *
 * @group Owner
 */
class GetPriveBookingTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout Prive User.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Get Prive booking list response data.
        $url      = $this->getApiVersion().'/prive/bookings';
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

        // Get Prive booking list response data.
        $url      = $this->getApiVersion().'/prive/bookings';
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(401);

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
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 3, 1);

        // Query Parameters with invalid date.
        $params = [
            'property_hash_id' => '',
            'start_date'       => Carbon::createFromTimestamp(time() + (60 * 60 * 24 * 30))->format('d-m-Y'),
            'end_date'         => Carbon::createFromTimestamp(time() - (60 * 60 * 24 * 30))->format('d-m-Y'),
            'offset'           => 0,
            'total'            => 1,
        ];

        $url = $this->getApiVersion().'/prive/bookings?'.http_build_query($params);

        $response = $this->actingAs($create_booking['prive_owner'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

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
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 3, 1);

        // Query Parameters.
        $params = [
            'property_hash_id' => Helper::encodePropertyId($create_booking['properties']->id),
            'filter_type'      => 1,
            'start_date'       => '',
            'end_date'         => '',
            'offset'           => 0,
            'total'            => 1,
        ];

        // Get prive booking listing  response data.
        $url = $this->getApiVersion().'/prive/bookings?'.http_build_query($params);

        $response = $this->actingAs($create_booking['prive_owner'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

    }//end testValidResponseWithAuthentication()


    /**
     * Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithValuesAuthentication()
    {
        // Create 2 Booking data with 2 properties.
        $create_booking = $this->createBookingRequests(BOOKED, ['prive' => 1], null, null, [], 2, 1);
        $booking        = [];
        $property_id    = 0;
        $booking_amount = 5543;
        $properties     = $create_booking['property_list'];
        foreach ($properties as $key => $value) {
            $properties_data['selected']         = 0;
            $property_hash_id                    = Helper::encodePropertyId($value['id']);
            $properties_data['property_hash_id'] = $property_hash_id;
            $properties_data['property_title']   = 'Vila';
            $property_list[] = $properties_data;
        }

        foreach ($create_booking['booking_request_list'] as $key => $value) {
            $booking['request_hash_id'] = Helper::encodeBookingRequestId($value['id']);
            //phpcs:ignore
            $booking['guest_name']      = 'Unit Testing'.' '.'Api';
            $booking['guests']         = 1;
            $booking['amount']         = Helper::getFormattedMoney($booking_amount, 'INR');
            $booking['booking_status'] = [
                'text'   => 'Booked',
                'class'  => 'booked',
                'status' => 1,
            ];
            $booking['checkin']        = carbon::parse($value['from_date'])->format('dS M Y');
            $booking['checkout']       = carbon::parse($value['to_date'])->format('dS M Y');
            $booking['title']          = 'Vila';
            $booking['units']          = 1;
            $booking['room']           = 1;
            $booking_data[]            = $booking;
        }

        $filter        = [
            'start_date'     => Carbon::now()->format('d-m-Y'),
            'end_date'       => Carbon::now()->addMonth(1)->format('d-m-Y'),
            'properties'     => $property_list,
            'booking_status' => [
                [
                    'text'     => 'Booked',
                    'status'   => 1,
                    'selected' => 0,
                ],
                [
                    'text'     => 'Cancelled',
                    'status'   => 2,
                    'selected' => 0,
                ],
            ],
        ];
        $response_data = [
            'booking_requests' => $booking_data,
            'filter'           => $filter,
            'booking_count'    => 2,
            'property_count'   => 1,
            //phpcs:ignore
            'user_name'        => 'Unit Testing'.' '.'Api',
        ];

        // Get prive booking listing  response data.
        $url = $this->getApiVersion().'/prive/bookings';

        $response = $this->actingAs($create_booking['prive_owner'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);

        $this->seeJsonStructure($this->getApiStructureWithOnlyDefaultValues());

        // Check Json Exact match.
        $this->seeJsonEquals($this->getApiValues($response_data));

    }//end testValidResponseWithValuesAuthentication()


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
                        'guest_name',
                        'guests',
                        'amount',
                        'booking_status' => [
                            'text',
                            'class',
                            'status',
                        ],
                        'checkin',
                        'checkout',
                        'units',
                        'title',
                        'room',
                    ],
                ],
                'filter' => [
                    'start_date',
                    'end_date',
                    'properties' => [
                        '*' => [
                            'selected',
                            'property_hash_id',
                            'property_title',
                        ],
                    ],
                    'booking_status' => [
                        '*' => [
                            'text',
                            'status',
                            'selected',
                        ],
                    ],
                ],
                'booking_count',
                'property_count',
                'user_name',
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


     /**
      * Helper function to api response
      *
      * @param array $response_data Response Data.
      *
      * @return array
      */
    private function getApiValues(array $response_data)
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.KeySpecified,Squiz.Arrays.ArrayDeclaration.NoKeySpecified
        return [
            'status' => true,
            'data'   => $response_data,
            'error'  => [],
        ];

    }//end getApiValues()


}//end class
