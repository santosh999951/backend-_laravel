<?php
/**
 * GetHostBookingRequestDetailTest Test containing methods related to Booking Request  List Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetHostBookingRequestDetailTest
 *
 * @group Host
 */
class GetHostBookingRequestDetailTest extends TestCase
{
    use App\Traits\FactoryHelper;


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

        // Get Home response data.
        $url      = $this->getApiVersion().'/host/booking/request/'.$request_hash_id;
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
        // Create User.
        $traveller = $this->createUsers();

        // Create Booking Data.
        $create_booking = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get Home response data.
        $url      = $this->getApiVersion().'/host/booking/request/'.$request_hash_id;
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Test Data invalid request id .
     *
     * @return void
     */
    public function testInvalidRequestIdWithAuthentication()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests();

        // Create fake request hash id.
        $request_hash_id = 'ABCDEF';

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/host/booking/request/'.$request_hash_id;
        $response = $this->actingAs($create_booking['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testInvalidRequestIdWithAuthentication()


    /**
     * Response with authenicated user.
     *
     * @test
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests();

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get host home detail response data.
        $url = $this->getApiVersion().'/host/booking/request/'.$request_hash_id;

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
                'property_section'     => [
                    'tile' => [
                        'property_id',
                        'property_hash_id',
                        'property_type',
                        'room_type',
                        'property_score',
                        'host_name',
                        'host_image',
                        'location' => [
                            'area',
                            'city',
                            'state',
                            'country' => [
                                'name',
                                'ccode',
                            ],
                            'location_name',
                            'latitude',
                            'longitude'
                        ],
                        'title',
                        'property_title',
                        'property_images' => [
                            '*' => [
                                'image',
                                'caption',
                            ],
                        ],
                        'url'
                    ],
                ],
                'booking_info_section' => [
                    'info'                => [
                        'request_hash_id',
                        'guests',
                        'units',
                        'checkin_formatted',
                        'checkout_formatted',
                        'booking_status' => [
                            'text',
                            'class',
                            'color_code',
                            'status',
                            'header_text',
                        ],
                        'checkin',
                        'checkout',
                        'expires_in'
                    ],
                    'booking_amount_info' => [
                        'total_amount_unformatted',
                        'payment_option',
                        'currency' => [
                            'webicon',
                            'non-webicon',
                            'iso_code',
                        ]
                    ],
                ],
                'invoice_section'      => [
                    'invoice_header' => [
                        '*' => [
                            'key',
                            'sub_key',
                            'value',
                            'show',
                        ],
                    ],
                    'invoice_middle' => [
                        '*' => [
                            'key',
                            'sub_key',
                            'value',
                            'show',
                        ],
                    ],
                    'invoice_footer' => [
                        '*' => [
                            'key',
                            'sub_key',
                            'value',
                            'show',
                        ],
                    ],
                    'selected_payment_method',
                    'selected_payment_method_text',
                    'currency',
                    'currency_code'
                ],
                'rejection_section'    => [
                    'can_reject',
                    'reasons' => [
                        '*' => [
                            'id',
                            'reason',
                        ],
                    ]
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
