<?php
/**
 * GetBookingRequestDetailsTest Test containing methods related to BookingRequestDetails Test case
 */

use App\Libraries\Helper;
use Illuminate\Http\{Request};

/**
 * Class GetBookingRequestDetailsTest
 *
 * @group Trip
 */
class GetBookingTripDetailsTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Test for Logout User.
     *
     * @return void
     */
    public function testInvalidResponseWithoutAuthentication()
    {
        // Create Trip.
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        $url      = $this->getApiVersion().'/booking/trip/'.$request_hash_id;
        $response = $this->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        $this->seeStatusCode(401);

    }//end testInvalidResponseWithoutAuthentication()


    /**
     * Test for Wrong User whose request not exist.
     *
     * @return void
     */
    public function testResponseWithoutAuthorization()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/booking/trip/'.$request_hash_id;
        $response = $this->actingAs($booking_request_data['host'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check status of response.
        $this->seeStatusCode(404);

    }//end testResponseWithoutAuthorization()


    /**
     * Test for invalid request id .
     *
     * @return void
     */
    public function testBadRequestResponseWithAuthentication()
    {
        // Create Fake User.
        $user = $this->createUsers();

        // Create fake request hash id.
        $booking_hash_id = 'ABCDEF';

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/booking/trip/'.$booking_hash_id;
        $response = $this->actingAs($user[0])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);

        // Check Status of response.
        $this->seeStatusCode(400);

    }//end testBadRequestResponseWithAuthentication()


    /**
     * Test Authorized User response.
     *
     * @return void
     */
    public function testValidResponseWithAuthentication()
    {
        // Create New Booking Request.
        $booking_request_data = $this->createBookingRequests(BOOKED);

        // Encode Request Id.
        $request_hash_id = Helper::encodeBookingRequestId($booking_request_data['booking_request']->id);

        // Execute api using Acting which Autometically create Token data using Gaurd.
        $url      = $this->getApiVersion().'/booking/trip/'.$request_hash_id;
        $response = $this->actingAs($booking_request_data['traveller'])->get($url, ['HTTP_device-unique-id' => $this->getDeviceUniqueId()]);
        // Check status of response.
        $this->seeStatusCode(200);

        // Match Reponse json with defined json.
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
                'property_section'      => [
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
                'booking_info_section'  => [
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
                        'can_review',
                        'can_rate',
                        'can_share_fb'
                    ],
                    'booking_amount_info' => [
                        'currency' => [
                            'webicon',
                            'non-webicon',
                            'iso_code',
                        ],
                        'total_amount',
                        'total_amount_unformatted',
                        'paid_amount_unformatted',
                        'pending_payment',
                        'pending_payment_text',
                        'pending_payment_amount',
                        'pending_payment_url',
                        'payment_gateway_method',
                        'payment_option'
                    ],
                ],
                'invoice_section'       => [
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
                'property_info_section' => [
                    'show_host',
                    'host_name',
                    'host_contact',
                    'host_address',
                ],
                'cancellation_section'  => [
                    'cancellation_policy_info' => [
                        'id',
                        'title',
                        'policy_days',
                        'desc',
                        'popup_text',
                        'cancel_text',

                    ],
                    'cancellable',
                    'cancellation_reasons' => [
                        '*' => [
                            'id',
                            'reason_title',
                        ],
                    ]
                ],
                'refund_section'        => [
                    'amount',
                    'status' => [
                        '*' => [
                            'text',
                            'class',
                            'status',
                        ],
                    ],
                    'show',
                    'current_status',
                    'processing_date'
                ],
            ],
            'error'
        ];

    }//end getApiStructureWithOnlyDefaultValues()


}//end class
