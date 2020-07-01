<?php
/**
 * GetHostBookingTripDetailTest Test containing methods related to Booking Request  List Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetHostBookingTripDetailTest
 *
 * @group Host
 */
class GetHostBookingTripDetailTest extends TestCase
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
        $create_booking = $this->createBookingRequests(BOOKED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get Home response data.
        $url      = $this->getApiVersion().'/host/booking/trip/'.$request_hash_id;
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
        $create_booking = $this->createBookingRequests(BOOKED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get Home response data.
        $url      = $this->getApiVersion().'/host/booking/trip/'.$request_hash_id;
        $response = $this->actingAs($traveller[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(403);

    }//end testInvalidResponseForTravellerWithAuthentication()


    /**
     * Test Data not found for invalid request id .
     *
     * @return void
     */
    public function testNotFoundResponseWithAuthentication()
    {
        // Create Booking Data.
        $create_booking = $this->createBookingRequests(BOOKED);

        // Create fake request hash id.
        $request_hash_id = 'ABCDEF';

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/host/booking/trip/'.$request_hash_id;
        $response = $this->actingAs($create_booking['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(404);

    }//end testNotFoundResponseWithAuthentication()


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

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($create_booking['booking_request']->id);

        // Get host home detail response data.
        $url = $this->getApiVersion().'/host/booking/trip/'.$request_hash_id;

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
                'property_section'       => [
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
                'booking_info_section'   => [
                    'info'                => [
                        'request_hash_id',
                        'instant',
                        'coupon_code_used',
                        'wallet_money_used',
                        'guests',
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
                        'ask_review'
                    ],
                    'booking_amount_info' => [
                        'currency' => [
                            'webicon',
                            'non-webicon',
                            'iso_code',
                        ],
                        'total_amount',
                        'total_amount_unformatted',
                        'pending_payment',
                        'pending_payment_amount'
                    ],
                ],
                'invoice_section'        => [
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
                'traveller_info_section' => [
                    'show_traveller',
                    'hash_id',
                    'name',
                    'contact',
                    'age',
                    'language',
                    'gender',
                    'verified',
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
